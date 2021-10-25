<?php

/*
 * ==========================================================
 * SLACK APP
 * ==========================================================
 *
 * Slack app main file. © 2021 board.support. All rights reserved.
 *
 */

define('SB_SLACK', '1.1.5');

/*
 * -----------------------------------------------------------
 * SEND SLACK MESSAGE
 * -----------------------------------------------------------
 *
 * Send a message to Slack
 *
 */

function sb_send_slack_message($user_id, $full_name, $profile_image = SB_URL . '/media/user.png', $message = '', $attachments = [], $channel = -1) {

    //Channel ID
    $token = sb_get_setting('slack-token');
    if ($token == '') {
        return ['status' => 'error', 'response' => 'Slack token not found'];
    }
    $full_name = str_replace('#', '', $full_name);
    if ($channel == -1) {
        $channel = sb_get_slack_channel($user_id, $token);
        if ($channel[0] == -1 || $user_id == -1) {
            $channel = [sb_get_setting('slack-channel'), false];
        }
    } else {
        $channel = [$channel, false];
    }

    // Attachments
    $slack_attachments = '';
    for ($i = 0; $i < count($attachments); $i++) {
        $slack_attachments .= '{ "title": "' . $attachments[$i][0] . '", "title_link": "' .  $attachments[$i][1] . '" },';
    }

    // Send message to Slack
    $data = [ 'token' => $token, 'channel' => $channel[0], 'text' => $message, 'username' => $full_name, 'bot_id' => 'support-board', 'icon_url' => strpos($profile_image, '.svg') ? SB_URL . '/media/user.png' : $profile_image, 'attachments' => ($slack_attachments != '' ? '[' . substr($slack_attachments, 0, -1) . ']' : '')];
    $response = sb_curl('https://slack.com/api/chat.postMessage', $data);

    // Send a message to the main channel if the user's channel is new
    if ($channel[1] && isset($response['ok']) && $response['ok'] == true) {
        $user = sb_get_active_user();
        $user_extra = sb_get_user_extra($user_id);
        $channel_link = 'https://' . sb_get_setting('slack-workspace') . '.slack.com/archives/' . $channel[0];
        $data['channel'] = sb_get_setting('slack-channel');
        $data['text'] = '';

        // Fields
        $fields_include = ['location', 'browser', 'browser_language'];
        $fields = [['title' => sb_('Message'), 'value' => sb_json_escape($message), 'short' => false]];
        if (isset($user['email'])) {
            array_push($fields, ['title' => 'Email', 'value' => $user['email'], 'short' => true]);
        }
        if (is_array($user_extra)) {
            for ($i = 0; $i < count($user_extra); $i++) {
                if (in_array($user_extra[$i]['slug'], $fields_include)) {
                    array_push($fields, ['title' => $user_extra[$i]['name'], 'value' =>$user_extra[$i]['value'], 'short' => true]);
                }
            }
        }
        array_push($fields, ['title' => '', 'value' => '*<' . $channel_link . '|Reply in channel>*', 'short' => false]);
        $data['attachments'] = json_encode([['fallback' => 'A conversation was started by' . ' ' . sb_json_escape($full_name) . '. *<' . $channel_link . '|Reply in channel>*"', 'text' => '*' . sb_('A conversation was started by') . ' ' . sb_db_escape($full_name) . '*', 'color' => '#028be5', 'fields' => $fields]]);

        //Send message to slack
        $response = sb_curl('https://slack.com/api/chat.postMessage', $data);
    }
    if (isset($response['ok'])) {
        if ($response['ok']) {
            return ['success', $channel[0]];
        } else if (isset($response['error'])) {

            // Unarchive or create new channel and send the message again
            if ($response['error'] == 'is_archived') {
                $response = sb_curl('https://slack.com/api/conversations.unarchive', ['token' => sb_get_setting('slack-token-user'), 'channel' => $channel[0]]);
            } else if ($response['error'] == 'channel_not_found') {
                $response = sb_get_slack_channel($user_id, $token, 0, true);
            }
            if ($response['ok']) {
                return sb_send_slack_message($user_id, $full_name, $profile_image, $message, $attachments);
            }
        }
    }
    return $response;
}

/*
 * -----------------------------------------------------------
 * CHANNELS
 * -----------------------------------------------------------
 *
 * 1. Return the correct Slack channel of the user to send the messages to
 * 2. Rename a channel
 * 3. Archive all Slack channels
 *
 */

function sb_get_slack_channel($user_id, $token, $index = 0, $force_creation = false) {
    $channels = sb_get_external_setting('slack-channels');
    if (isset($channels[$user_id]) && !$force_creation) {
        return [$channels[$user_id]['id'], false];
    } else {
        $active_user = sb_get_active_user();
        $username = mb_strtolower(str_replace(['#', ' '], ['', '_'], $active_user['first_name'] . (empty($active_user['last_name']) ? '' : ('_' . $active_user['last_name'])) . ($index > 0 ? ('_' . $index) : '')));
        $response = sb_curl('https://slack.com/api/conversations.create', ['token' => $token, 'name' => $username]);
        if (isset($response['channel'])) {
            $channels[$user_id] = ['id' => $response['channel']['id'], 'name' => $response['channel']['name']];
            $json = sb_db_json_escape($channels);
            sb_db_query('INSERT INTO sb_settings(name, value) VALUES (\'slack-channels\', \'' . $json . '\') ON DUPLICATE KEY UPDATE value = \'' . $json . '\'');
            $slack_users = sb_slack_get_users($token);
            $slack_users_string = '';
            for ($i = 0; $i < count($slack_users); $i++) {
                $slack_users_string .= $slack_users[$i]['id'] . ',';
            }
            if (!sb_get_setting('slack-disable-invitation')) {
                sb_curl('https://slack.com/api/conversations.invite', ['token' => $token, 'channel' => $response['channel']['id'], 'users' => substr($slack_users_string, 0, -1)]);
            }
            return [$response['channel']['id'], true];
        } else if (isset($response['error']) && $response['error'] === 'name_taken') {
            return sb_get_slack_channel($user_id, $token, $index + 1);
        }
    }
    return false;
}

function sb_slack_rename_channel($user_id, $channel_name) {
    $channels = sb_get_external_setting('slack-channels');
    if (isset($channels[$user_id])) {
        $token = sb_get_setting('slack-token');
        if ($token != '') {
            $channel_name = mb_strtolower(str_replace(['#', ' '], ['', '_'], $channel_name));
            $response = sb_curl('https://slack.com/api/conversations.rename', ['token' => $token, 'channel' => $channels[$user_id]['id'], 'name' => $channel_name]);
            if ($response['ok']) {
                $channels[$user_id]['name'] = $channel_name;
                $json = sb_db_json_escape($channels);
                sb_db_query('INSERT INTO sb_settings(name, value) VALUES (\'slack-channels\', \'' . $json . '\') ON DUPLICATE KEY UPDATE value = \'' . $json . '\'');
                return true;
            }
        }
    }
    return false;
}

function sb_archive_slack_channels() {
    $token = sb_get_setting('slack-token');
    $response = sb_curl('https://slack.com/api/conversations.list', ['token' => $token, 'exclude_archived' => true, 'limit' => 999]);
    if ($response['ok'] && isset($response['channels'])) {
        $token = sb_get_setting('slack-token-user');
        set_time_limit(3000);
        for ($i = 0; $i < count($response['channels']); $i++) {
            sb_curl('https://slack.com/api/conversations.archive', ['token' => $token, 'channel' => $response['channels'][$i]['id']]);
        }
    } else {
        return new SBError('Error: ' . json_encode($response), 'sb_get_channels');
    }
    return true;
}

/*
 * -----------------------------------------------------------
 * USERS
 * -----------------------------------------------------------
 *
 * 1. Return the Slack users ID and name
 * 2. Return all slack members
 *
 */

function sb_slack_users() {
    $token = sb_get_setting('slack-token');
    $users = ['slack_users' => [], 'agents' => [], 'saved' => sb_get_setting('slack-agents')];
    if ($token != '') {
        $slack_users = sb_slack_get_users($token);
        for ($i = 0; $i < count($slack_users); $i++) {
            array_push($users['slack_users'], $slack_users[$i]);
        }
        $agents = sb_db_get('SELECT id, first_name, last_name FROM sb_users WHERE user_type = "agent" OR user_type = "admin"', false);
        for ($i = 0; $i < count($agents); $i++) {
            array_push($users['agents'], ['id' => $agents[$i]['id'], 'name' => sb_get_user_name($agents[$i])]);
        }
        return $users;
    } else {
        return new SBValidationError('slack-token-not-found');
    }
}

function sb_slack_get_users($token = false) {
    $response = sb_curl('https://slack.com/api/users.list', ['token' => $token === false ? sb_get_setting('slack-token') : $token]);
    $users = [];
    if ($response['members']) {
        for ($i = 0; $i < count($response['members']); $i++) {
            $id = $response['members'][$i]['id'];
            $name = sb_isset($response['members'][$i], 'real_name');
            if (!empty($name) && $name != 'Slackbot' && $name != 'Support Board') {
                array_push($users, ['id' => $id, 'name' => $name]);
            }
        }
    }
    return $users;
}

/*
 * -----------------------------------------------------------
 * SLACK PRESENCE
 * -----------------------------------------------------------
 *
 * Check if a Slack agent is online, or if at least one agent is online, or returns all the online users
 *
 */

function sb_slack_presence($agent_id = false, $list = false) {
    $online_users = [];
    $token = sb_get_setting('slack-token');
    if (!empty($token)) {
        $slack_agents = sb_get_setting('slack-agents');
        if ($agent_id === false && !$list) {
            $slack_users = sb_slack_get_users();
            $slack_agents = [];
            for ($i = 0; $i < count($slack_users); $i++) {
                $slack_agents[$slack_users[$i]['id']] = false;
            }
        }
        if ($slack_agents != false && !is_string($slack_agents)) {
            foreach ($slack_agents as $slack_id => $id) {
                if ($id == $agent_id || ($list && !empty($id))) {
                    $response = json_decode(sb_download('https://slack.com/api/users.getPresence?token=' . $token . '&user=' . $slack_id), true);
                    $response = (isset($response['ok']) && sb_isset($response, 'presence') == 'active') || sb_isset($response, 'online');
                    if ($list) {
                        if ($response) {
                            array_push($online_users, $id);
                        }
                    } else if ($agent_id !== false || $response) {
                        return $response ? 'online' : 'offline';
                    }
                }
            }
        }
    }
    return $list ? $online_users : 'offline';
}

// [deprecated] This function will be removed soon
function sb_slack_agent_online($agent_id = false) {
    return sb_slack_presence($agent_id);
}

/*
 * -----------------------------------------------------------
 * LISTENER
 * -----------------------------------------------------------
 *
 * Receive and process the Slack messages of the Slack agents forwarded by board.support
 *
 */

function sb_slack_listener($response) {
    if (isset($response['event'])) {
        $response = $response['event'];
        $subtype = isset($response['subtype']) ? $response['subtype'] : '';
        $GLOBALS['SB_FORCE_ADMIN'] = true;

        // Message: Check if the json response is a valid message
        if (isset($response['type']) && $response['type'] == 'message' && $subtype != 'channel_join' && ($subtype == '' || $subtype == 'file_share') && ($response['text'] != '' || (is_array($response['files']) && count($response['files']) > 0))) {

            // Get the user id of the slack message
            $user_id = sb_slack_api_user_id($response['channel']);

            // Elaborate the Slack message
            if ($user_id != -1) {
                $last_message = sb_slack_last_user_message($user_id);
                $message = $response['text'];
                $user = sb_get_user($user_id);

                // Emoji
                $emoji = explode(':', $message);
                if (count($emoji)) {
                    $emoji_slack = json_decode(file_get_contents(SB_PATH . '/resources/json/emoji-slack.json'), true);
                    for ($i = 0; $i < count($emoji); $i++) {
                        if ($emoji[$i] != '') {
                            $emoji_code = ':' . $emoji[$i] . ':';
                            if (isset($emoji_slack[$emoji_code])) {
                                $message = str_replace($emoji_code, $emoji_slack[$emoji_code], $message);
                            }
                        }
                    }
                }

                // Message
                $message = sb_slack_response_message_text($message);

                // Attachments
                $attachments = $subtype == 'file_share' ? sb_slack_response_message_attachments($response['files']) : [];

                // Set the user login
                global $SB_LOGIN;
                $SB_LOGIN = ['id' => $user_id,  'user_type' => 'user'];

                // Get the agent id
                $agent_id = sb_db_escape(sb_slack_api_agent_id($response['user']));

                // Send the message
                $send_response = sb_send_message($agent_id, $last_message['conversation_id'], $message, $attachments, 1, $response);

                // Notifications
                if (!sb_is_error($send_response) && isset($send_response['message-id']) && !sb_is_user_online($user_id)) {
                    if (sb_get_setting('notify-user-email') && $user['email']) {
                        $agent = sb_db_get('SELECT first_name, last_name, profile_image FROM sb_users WHERE id = ' . $agent_id);
                        sb_email_create($user_id, sb_get_user_name($agent), $agent['profile_image'], $message, $attachments);
                    }
                    if (sb_get_setting('sms-active-users')) {
                        $phone = sb_get_user_extra($user_id, 'phone');
                        if ($phone) {
                            sb_send_sms($message, $phone, true, $last_message['conversation_id']);
                        }
                    }
                }

                // Pusher online status
                if (sb_pusher_active()) {
                    sb_pusher_trigger('private-user-' . $user_id, 'add-user-presence', [ 'agent_id' => $agent_id]);
                }

            }
        }

        // Event: message deleted
        if ($subtype == 'message_deleted') {
            $user_id = sb_db_escape(sb_slack_api_user_id($response['channel']));
            $agent_id = sb_slack_api_agent_id($response['previous_message']['user']);
            $last_message = sb_slack_last_user_message($user_id);
            $online = sb_update_users_last_activity(-1, $user_id) === 'online';
            $previous_message = sb_db_escape($response['previous_message']['text']);
            sb_db_query(($online ? 'UPDATE sb_messages SET message = "", attachments = "", payload = "{ \"event\": \"delete-message\" }", creation_time = "' . gmdate('Y-m-d H:i:s') . '"' : 'DELETE FROM sb_messages') . ' WHERE (user_id = ' . $agent_id . ' OR user_id = ' . $user_id . ') AND conversation_id = "' . $last_message['conversation_id'] . '" AND ' . ($previous_message == '' ? 'attachments LIKE "%' . sb_db_escape($response['previous_message']['attachments'][0]['title']) . '%"' : 'message = "' . $previous_message . '"') .' LIMIT 1');
        }

        // Event: message changed
        if ($subtype == 'message_changed') {
            $agent_id = sb_db_escape(sb_slack_api_agent_id($response['previous_message']['user']));
            sb_db_query('UPDATE sb_messages SET message = "' . sb_db_escape(sb_slack_response_message_text($response['message']['text'])) . '", creation_time = "' . gmdate('Y-m-d H:i:s') . '" WHERE user_id = ' . $agent_id . ' AND payload LIKE "%' .  sb_db_escape($response['message']['ts']) . '%" LIMIT 1');
        }
        $GLOBALS['SB_FORCE_ADMIN'] = false;
    }
}

function sb_slack_response_message_text($message) {

    // Links
    $links = [];
    if (preg_match_all('/<https(\s*?.*?)*?\>/', $message, $links)) {
        for ($i = 0; $i < count($links); $i++){
            $link = $links[$i][0];
            if (substr($link, 0, 5) == '<http') {
                $link = ' ' . substr($link, 1, strpos($link, '|') - 1) . ' ';
                $message = str_replace($links[$i][0], $link, $message);
            }
        }
    }

    // Formatting
    $message = preg_replace('/\<([^\<>]+)\>/', ' $1', $message);
    $message = str_replace('_', '__', $message);

    return $message;
}

function sb_slack_response_message_attachments($slack_files) {
    $attachments = [];
    $token = sb_get_setting('slack-token');
    for ($i = 0; $i < count($slack_files); $i++) {
        array_push($attachments, [$slack_files[$i]['name'], sb_curl($slack_files[$i]['url_private'], '', ['Authorization: Bearer ' . $token], 'FILE')]);
    }
    return $attachments;
}

/*
 * -----------------------------------------------------------
 * SLACK.PHP
 * -----------------------------------------------------------
 *
 * 1. Get the user id of the slack message
 * 2. Get the agent id
 * 3. Get the last message sent by the user
 *
 */

function sb_slack_api_user_id($channel_id) {
    $user_id = -1;
    $channels = sb_get_external_setting('slack-channels');
    foreach ($channels as $key => $channel) {
        if ($channel['id'] == $channel_id) {
            $user_id = $key;
            break;
        }
    }
    return $user_id;
}

function sb_slack_api_agent_id($user) {
    $slack_agents = sb_get_setting('slack-agents');
    if (isset($slack_agents[$user])) {
        return $slack_agents[$user];
    } else {
        return sb_db_get('SELECT id FROM sb_users WHERE user_type = "admin" LIMIT 1')['id'];
    }
}

function sb_slack_last_user_message($user_id) {
    $last_message = sb_db_get('SELECT conversation_id, creation_time FROM sb_messages WHERE user_id = ' . sb_db_escape($user_id) . ' ORDER BY creation_time DESC LIMIT 1');
    return [ 'conversation_id' => (isset($last_message['conversation_id']) ? $last_message['conversation_id'] : -1), 'message_time' => (isset($last_message['creation_time']) ? $last_message['creation_time'] : '')];
}

?>