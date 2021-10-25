<?php

/*
 * ==========================================================
 * AJAX.PHP
 * ==========================================================
 *
 * Ajax functions. This file must be executed only via ajax. © 2021 board.support. All rights reserved.
 *
 */

if (file_exists('../config.php')) {
    require_once('../config.php');
}
if (defined('SB_CROSS_DOMAIN') && SB_CROSS_DOMAIN) {
    header('Access-Control-Allow-Origin: *');
}
if (!isset($_POST['function'])) {
    die('true');
}
global $SB_LANGUAGE;
$SB_LANGUAGE = sb_post('language');
require_once('functions.php');

if (!defined('SB_API') && sb_security() == false) {
    die(sb_json_response(new SBError('security-error', $_POST['function'])));
}

if (defined('SB_CLOUD') && sb_cloud_ajax_function_forbidden($_POST['function'])) die('cloud_function_forbidden');

switch ($_POST['function']) {
    case 'emoji':
        die(file_get_contents(SB_PATH . '/resources/json/emoji.json'));
    case 'saved-replies':
        die(sb_json_response(sb_get_setting('saved-replies')));
    case 'save-settings':
        die(sb_json_response(sb_save_settings($_POST['settings'], sb_post('external_settings', []), sb_post('external_settings_translations', []))));
    case 'get-settings':
        die(sb_json_response(sb_get_settings()));
    case 'get-all-settings':
        die(sb_json_response(sb_get_all_settings()));
    case 'get-front-settings':
        die(sb_json_response(sb_get_front_settings()));
    case 'get-block-setting':
        die(sb_json_response(sb_get_block_setting($_POST['value'])));
    case 'add-user':
        die(sb_json_response(sb_add_user($_POST['settings'], sb_post('settings_extra', null))));
    case 'add-user-and-login':
        die(sb_json_response(sb_add_user_and_login(sb_post('settings', null), sb_post('settings_extra', null))));
    case 'get-user':
        die(sb_json_response(sb_get_user($_POST['user_id'], sb_post('extra'))));
    case 'get-users':
        die(sb_json_response(sb_get_users(sb_post('sorting', ['creation_time', 'DESC']), sb_post('user_types', []), sb_post('search', ''), sb_post('pagination'))));
    case 'get-new-users':
        die(sb_json_response(sb_get_new_users($_POST['datetime'])));
    case 'get-user-extra':
        die(sb_json_response(sb_get_user_extra($_POST['user_id'], sb_post('slug'), sb_post('default'))));
    case 'get-user-language':
        die(sb_json_response(sb_get_user_language(sb_post('user_id'))));
    case 'get-user-from-conversation':
        die(sb_json_response(get_user_from_conversation($_POST['conversation_id'], sb_post('agent'))));
    case 'get-online-users':
        die(sb_json_response(sb_get_online_users(sb_post('exclude_id', -1), sb_post('sorting', 'creation_time'), sb_post('agents'))));
    case 'search-users':
        die(sb_json_response(sb_search_users($_POST['search'])));
    case 'get-active-user':
        die(sb_json_response(sb_get_active_user(sb_post('login-cookie'), sb_post('db'), sb_post('login_app'), sb_post('user_token'))));
    case 'get-agent':
        die(sb_json_response(sb_get_agent($_POST['agent_id'])));
    case 'delete-user':
        die(sb_json_response(sb_delete_user($_POST['user_id'])));
    case 'delete-users':
        die(sb_json_response(sb_delete_users($_POST['user_ids'])));
    case 'update-user':
        die(sb_json_response(sb_update_user($_POST['user_id'], $_POST['settings'], sb_post('settings_extra', []))));
    case 'count-users':
        die(sb_json_response(sb_count_users()));
    case 'get-users-with-details':
        die(sb_json_response(sb_get_users_with_details($_POST['details'], sb_post('user_ids'))));
    case 'update-user-to-lead':
        die(sb_json_response(sb_update_user_to_lead($_POST['user_id'])));
    case 'get-conversations':
        die(sb_json_response(sb_get_conversations(sb_post('pagination'), sb_post('status_code'), sb_post('routing'), sb_post('routing_unassigned'))));
    case 'get-new-conversations':
        die(sb_json_response(sb_get_new_conversations($_POST['datetime'], sb_post('routing'), sb_post('routing_unassigned'))));
    case 'get-conversation':
        die(sb_json_response(sb_get_conversation(sb_post('user_id'), $_POST['conversation_id'])));
    case 'search-conversations':
        die(sb_json_response(sb_search_conversations($_POST['search'], sb_post('routing'))));
    case 'search-user-conversations':
        die(sb_json_response(sb_search_user_conversations($_POST['search'], sb_post('user_id'))));
    case 'new-conversation':
        die(sb_json_response(sb_new_conversation($_POST['user_id'], sb_post('status_code'), sb_post('title', ''), sb_post('department', -1), sb_post('agent_id', -1), sb_post('routing'))));
    case 'get-user-conversations':
        die(sb_json_response(sb_get_user_conversations($_POST['user_id'], sb_post('exclude_id', -1))));
    case 'get-new-user-conversations':
        die(sb_json_response(sb_get_new_user_conversations($_POST['user_id'], $_POST['datetime'])));
    case 'update-conversation-status':
        die(sb_json_response(sb_update_conversation_status($_POST['conversation_id'], $_POST['status_code'])));
    case 'update-conversation-department':
        die(sb_json_response(sb_update_conversation_department($_POST['conversation_id'], $_POST['department'], sb_post('message'))));
    case 'update-conversation-agent':
        die(sb_json_response(sb_update_conversation_agent($_POST['conversation_id'], $_POST['agent_id'], sb_post('message'))));
    case 'queue':
        die(sb_json_response(sb_queue(sb_post('conversation_id'), sb_post('department'))));
    case 'update-users-last-activity':
        die(sb_json_response(sb_update_users_last_activity($_POST['user_id'], sb_post('return_user_id', -1), sb_post('check_slack'))));
    case 'is-typing':
        die(sb_json_response(sb_is_typing($_POST['user_id'], $_POST['conversation_id'])));
    case 'is-agent-typing':
        die(sb_json_response(sb_is_agent_typing($_POST['conversation_id'])));
    case 'set-typing':
        die(sb_json_response(sb_set_typing($_POST['user_id'], $_POST['conversation_id'])));
    case 'login':
        die(sb_json_response(sb_login(sb_post('email', ''), sb_post('password', ''), sb_post('user_id', ''), sb_post('token', ''))));
    case 'logout':
        die(sb_json_response(sb_logout()));
    case 'update-login':
        die(sb_json_response(sb_update_login(sb_post('profile_image', ''), sb_post('first_name', ''), sb_post('last_name', ''), sb_post('email', ''), sb_post('department', ''), sb_post('user_id', ''))));
    case 'get-new-messages':
        die(sb_json_response(sb_get_new_messages($_POST['user_id'], $_POST['conversation_id'], $_POST['datetime'])));
    case 'send-message':
        die(sb_json_response(sb_send_message($_POST['user_id'], $_POST['conversation_id'], sb_post('message', ''), sb_post('attachments', []), sb_post('conversation_status_code', -1), sb_post('payload'), sb_post('queue'), sb_post('recipient_id'))));
    case 'send-slack-message':
        die(sb_json_response(sb_send_slack_message($_POST['user_id'], $_POST['full_name'], sb_post('profile_image'), sb_post('message', ''), sb_post('attachments', []), sb_post('channel', -1))));
    case 'send-bot-message': // Deprecated
        die(sb_json_response(sb_dialogflow_message($_POST['conversation_id'], $_POST['message'], sb_post('token', -1), sb_post('dialogflow_language', sb_post('language')), sb_post('attachments', []), sb_post('event'))));
    case 'update-message':
        die(sb_json_response(sb_update_message($_POST['message_id'], sb_post('message'), sb_post('attachments'), sb_post('payload'))));
    case 'delete-message':
        die(sb_json_response(sb_delete_message($_POST['message_id'])));
    case 'close-message':
        die(sb_json_response(sb_close_message($_POST['bot_id'], $_POST['conversation_id'])));
    case 'csv-users':
        die(sb_json_response(sb_csv_users()));
    case 'csv-conversations':
        die(sb_json_response(sb_csv_conversations(sb_post('conversation_id', -1))));
    case 'transcript':
        die(sb_json_response(sb_transcript($_POST['conversation_id'], sb_post('type'))));
    case 'update-user-and-message':
        die(sb_json_response(sb_update_user_and_message($_POST['user_id'], sb_post('settings', []), sb_post('settings_extra', []), sb_post('message_id', ''), sb_post('message', ''), sb_post('payload'))));
    case 'get-rich-message':
        die(sb_json_response(sb_get_rich_message($_POST['name'])));
    case 'create-email':
        die(sb_json_response(sb_email_create($_POST['recipient_id'], $_POST['sender_name'], $_POST['sender_profile_image'], $_POST['message'], sb_post('attachments', []), sb_post('department'), sb_post('conversation_id'))));
    case 'send-email':
        die(sb_json_response(sb_email($_POST['recipient_id'], $_POST['message'], sb_post('attachments', []), sb_post('sender_id', -1))));
    case 'send-custom-email':
        die(sb_json_response(sb_email_send($_POST['to'], $_POST['subject'], sb_email_default_parts($_POST['message'], sb_post('recipient_id')))));
    case 'send-test-email':
        die(sb_json_response(sb_email_send_test($_POST['to'], $_POST['email_type'])));
    case 'slack-users':
        die(sb_json_response(sb_slack_users()));
    case 'archive-slack-channels':
        die(sb_json_response(sb_archive_slack_channels()));
    case 'slack-presence':
        die(sb_json_response(sb_slack_presence(sb_post('agent_id'), sb_post('list'))));
    case 'clean-data':
        die(sb_json_response(sb_clean_data()));
    case 'user-autodata':
        die(sb_json_response(sb_user_autodata($_POST['user_id'])));
    case 'current-url':
        die(sb_json_response(sb_current_url(sb_post('user_id'), sb_post('url'))));
    case 'get-translations':
        die(sb_json_response(sb_get_translations()));
    case 'get-translation':
        die(sb_json_response(sb_get_translation($_POST['language_code'])));
    case 'save-translations':
        die(sb_json_response(sb_save_translations($_POST['translations'])));
    case 'dialogflow-message':
        die(sb_json_response(sb_dialogflow_message($_POST['conversation_id'], $_POST['message'], sb_post('token', -1), sb_post('dialogflow_language', sb_post('language')), sb_post('attachments', []), sb_post('event'))));
    case 'dialogflow-create-intent':
        die(sb_json_response(sb_dialogflow_create_intent($_POST['expressions'], $_POST['response'], sb_post('agent_language', ''), sb_post('conversation_id'))));
    case 'dialogflow-update-intent':
        die(sb_json_response(sb_dialogflow_update_intent($_POST['intent_name'], $_POST['expressions'], sb_post('agent_language', ''))));
    case 'dialogflow-entity':
        die(sb_json_response(sb_dialogflow_create_entity($_POST['entity_name'], $_POST['synonyms'], sb_post('agent_language', ''))));
    case 'dialogflow-get-entity':
        die(sb_json_response(sb_dialogflow_get_entity(sb_post('entity_id', 'all'), sb_post('agent_language', ''))));
    case 'dialogflow-get-token':
        die(sb_json_response(sb_dialogflow_get_token()));
    case 'dialogflow-get-agent':
        die(sb_json_response(sb_dialogflow_get_agent()));
    case 'dialogflow-get-intents':
        die(sb_json_response(sb_dialogflow_get_intents(sb_post('intent_name'), sb_post('language'))));
    case 'dialogflow-set-active-context':
        die(sb_json_response(sb_dialogflow_set_active_context($_POST['context_name'], sb_post('parameters', []))));
    case 'dialogflow-intent': // Deprecated
        die(sb_json_response(sb_dialogflow_create_intent($_POST['expressions'], $_POST['response'], sb_post('agent_language', ''))));
    case 'dialogflow-curl':
        die(sb_json_response(sb_dialogflow_curl($_POST['url_part'], $_POST['query'], sb_post('language'), sb_post('type', 'POST'))));
    case 'dialogflow-smart-reply':
        die(sb_json_response(sb_dialogflow_smart_reply($_POST['message'], sb_post('smart_reply_data'), sb_post('dialogflow_language'), sb_post('token'), sb_post('language_detection'))));
    case 'dialogflow-smart-reply-update':
        die(sb_json_response(sb_dialogflow_smart_reply_update($_POST['message'], $_POST['smart_reply_data'], sb_post('dialogflow_language'), sb_post('token'), sb_post('user_type', 'agent'))));
    case 'dialogflow-smart-reply-generate-conversations-data':
        die(sb_json_response(sb_dialogflow_smart_reply_generate_conversations_data()));
    case 'dialogflow-unknow-email':
        die(sb_json_response(sb_dialogflow_unknow_email($_POST['conversation_id'], sb_post('department'))));
    case 'set-rating':
        die(sb_json_response(sb_set_rating($_POST['settings'], sb_post('payload'), sb_post('message_id'), sb_post('message'))));
    case 'get-rating':
        die(sb_json_response(sb_get_rating($_POST['user_id'])));
    case 'save-articles':
        die(sb_json_response(sb_save_articles(sb_post('articles', []), sb_post('categories'), sb_post('translations'))));
    case 'get-articles':
        die(sb_json_response(sb_get_articles(sb_post('id', -1), sb_post('count'), sb_post('full'), sb_post('categories'), sb_post('articles_language'))));
    case 'get-articles-categories':
        die(sb_json_response(sb_get_articles_categories()));
    case 'save-articles-categories':
        die(sb_json_response(sb_save_articles_categories($_POST['categories'])));
    case 'search-articles':
        die(sb_json_response(sb_search_articles($_POST['search'], sb_post('articles_language'))));
    case 'article-ratings':
        die(sb_json_response(sb_article_ratings($_POST['article_id'], sb_post('rating'))));
    case 'installation':
        die(sb_json_response(sb_installation($_POST['details'])));
    case 'get-versions':
        die(sb_json_response(sb_get_versions()));
    case 'update':
        die(sb_json_response(sb_update()));
    case 'app-activation':
        die(sb_json_response(sb_app_activation($_POST['app_name'], $_POST['key'])));
    case 'app-get-key':
        die(sb_json_response(sb_app_get_key($_POST['app_name'])));
    case 'wp-sync':
        die(sb_json_response(sb_wp_synch()));
    case 'webhooks':
        die(sb_json_response(sb_webhooks($_POST['function_name'], $_POST['parameters'])));
    case 'system-requirements':
        die(sb_json_response(sb_system_requirements()));
    case 'get-departments':
        die(sb_json_response(sb_get_departments()));
    case 'push-notification':
        die(sb_json_response(sb_push_notification(sb_post('title'), sb_post('message'), sb_post('icon'), sb_post('interests'), sb_post('conversation_id'))));
    case 'delete-leads':
        die(sb_json_response(sb_delete_leads()));
    case 'cron-jobs':
        die(sb_json_response(sb_cron_jobs()));
    case 'path':
        die(sb_json_response(sb_('The Support Board path is:') . '<br>' . SB_PATH));
    case 'subscribe-email':
        die(sb_json_response(sb_subscribe_email($_POST['email'])));
    case 'agents-online':
        die(sb_json_response(sb_agents_online()));
    case 'woocommerce-get-customer':
        die(sb_json_response(sb_woocommerce_get_customer(sb_post('session_key'))));
    case 'woocommerce-get-user-orders':
        die(sb_json_response(sb_woocommerce_get_user_orders($_POST['user_id'])));
    case 'woocommerce-get-product':
        die(sb_json_response(sb_woocommerce_get_product($_POST['product_id'])));
    case 'woocommerce-get-taxonomies':
        die(sb_json_response(sb_woocommerce_get_taxonomies($_POST['type'], sb_post('language', ''))));
    case 'woocommerce-get-attributes':
        die(sb_json_response(sb_woocommerce_get_attributes(sb_post('type'), sb_post('language', ''))));
    case 'woocommerce-get-product-id-by-name':
        die(sb_json_response(sb_woocommerce_get_product_id_by_name($_POST['name'])));
    case 'woocommerce-get-product-images':
        die(sb_json_response(sb_woocommerce_get_product_images($_POST['product_id'])));
    case 'woocommerce-get-product-taxonomies':
        die(sb_json_response(sb_woocommerce_get_product_taxonomies($_POST['product_id'])));
    case 'woocommerce-get-attribute-by-term':
        die(sb_json_response(sb_woocommerce_get_attribute_by_term($_POST['term_name'])));
    case 'woocommerce-get-attribute-by-name':
        die(sb_json_response(sb_woocommerce_get_attribute_by_name($_POST['name'])));
    case 'woocommerce-is-in-stock':
        die(sb_json_response(sb_woocommerce_is_in_stock($_POST['product_id'])));
    case 'woocommerce-coupon':
        die(sb_json_response(sb_woocommerce_coupon($_POST['discount'], $_POST['expiration'], sb_post('product_id', ''), sb_post('user_id', ''))));
    case 'woocommerce-coupon-check':
        die(sb_json_response(sb_woocommerce_coupon_check($_POST['user_id'])));
    case 'woocommerce-coupon-delete-expired':
        die(sb_json_response(sb_woocommerce_coupon_delete_expired()));
    case 'woocommerce-get-url':
        die(sb_json_response(sb_woocommerce_get_url($_POST['type'], sb_post('name', ''), sb_post('language', ''))));
    case 'woocommerce-get-session':
        die(sb_json_response(sb_woocommerce_get_session(sb_post('session_key'))));
    case 'woocommerce-get-session-key':
        die(sb_json_response(sb_woocommerce_get_session_key(sb_post('user_id'))));
    case 'woocommerce-payment-methods':
        die(sb_json_response(sb_woocommerce_payment_methods()));
    case 'woocommerce-shipping-locations':
        die(sb_json_response(sb_woocommerce_shipping_locations(sb_post('country_code'))));
    case 'woocommerce-get-conversation-details':
        die(sb_json_response(sb_woocommerce_get_conversation_details($_POST['user_id'])));
    case 'woocommerce-returning-visitor':
        die(sb_json_response(sb_woocommerce_returning_visitor()));
    case 'woocommerce-get-products':
        die(sb_json_response(sb_woocommerce_get_products(sb_post('filters'), sb_post('pagination'), sb_post('user_language', ''))));
    case 'woocommerce-search-products':
        die(sb_json_response(sb_woocommerce_search_products($_POST['search'])));
    case 'woocommerce-dialogflow-entities':
        die(sb_json_response(sb_woocommerce_dialogflow_entities(sb_post('entity_id', 'all'))));
    case 'woocommerce-dialogflow-intents':
        die(sb_json_response(sb_woocommerce_dialogflow_intents()));
    case 'woocommerce-products-popup':
        die(sb_json_response([sb_woocommerce_get_products([], 0, sb_post('user_language')), sb_woocommerce_get_taxonomies('category', sb_post('user_language'))]));
    case 'woocommerce-waiting-list':
        die(sb_json_response(sb_woocommerce_waiting_list($_POST['product_id'], sb_post('conversation_id'), $_POST['user_id'], sb_post('action', 'request'), sb_post('token'))));
    case 'woocommerce-get-order':
        die(sb_json_response(sb_woocommerce_get_order($_POST['order_id'])));
    case 'ump-get-conversation-details':
        die(sb_json_response(sb_ump_get_conversation_details($_POST['user_id'])));
    case 'armember-get-conversation-details':
        die(sb_json_response(sb_armember_get_conversation_details($_POST['wp_user_id'])));
    case 'perfex-sync':
        die(sb_json_response(sb_perfex_sync()));
    case 'perfex-articles-sync':
        die(sb_json_response(sb_perfex_articles_sync()));
    case 'whmcs-get-conversation-details':
        die(sb_json_response(sb_whmcs_get_conversation_details($_POST['whmcs_id'])));
    case 'whmcs-articles-sync':
        die(sb_json_response(sb_whmcs_articles_sync()));
    case 'whmcs-sync':
        die(sb_json_response(sb_whmcs_sync()));
    case 'aecommerce-get-conversation-details':
        die(sb_json_response(sb_aecommerce_get_conversation_details($_POST['aecommerce_id'])));
    case 'aecommerce-sync-sellers':
    case 'aecommerce-sync-admins':
    case 'aecommerce-sync':
        die(sb_json_response(sb_aecommerce_sync($_POST['function'] == 'aecommerce-sync-admins' ? 'admin' : ($_POST['function'] == 'aecommerce-sync-sellers' ? 'seller' : 'customer'))));
    case 'aecommerce-cart':
        die(sb_json_response(sb_aecommerce_cart($_POST['cart'])));
    case 'email-piping':
        die(sb_json_response(sb_email_piping(sb_post('force'))));
    case 'reports':
        die(sb_json_response(sb_reports($_POST['name'], sb_post('date_start'), sb_post('date_end'))));
    case 'reports-update':
        die(sb_json_response(sb_reports_update($_POST['name'], sb_post('value'), sb_post('external_id'), sb_post('extra'))));
    case 'pusher-trigger':
        die(sb_json_response(sb_pusher_trigger($_POST['channel'], $_POST['event'], sb_post('data'))));
    case 'is-online':
        die(sb_json_response(sb_is_user_online($_POST['user_id'])));
    case 'get-notes':
        die(sb_json_response(sb_get_notes($_POST['conversation_id'])));
    case 'add-note':
        die(sb_json_response(sb_add_note($_POST['conversation_id'], $_POST['user_id'], $_POST['name'], $_POST['message'])));
    case 'delete-note':
        die(sb_json_response(sb_delete_note($_POST['conversation_id'], $_POST['note_id'])));
    case 'messenger-send-message':
        die(sb_json_response(sb_messenger_send_message($_POST['psid'], $_POST['facebook_page_id'], sb_post('message', ''), sb_post('attachments', []))));
    case 'whatsapp-send-message':
        die(sb_json_response(sb_whatsapp_send_message($_POST['to'], sb_post('message', ''), sb_post('attachments', []))));
    case 'send-sms':
        die(sb_json_response(sb_send_sms($_POST['message'], $_POST['to'], sb_post('template', true), sb_post('conversation_id'))));
    case 'direct-message':
        die(sb_json_response(sb_direct_message($_POST['user_ids'], $_POST['message'], sb_post('subject'))));
    case 'automations-get':
        die(sb_json_response(sb_automations_get()));
    case 'automations-save':
        die(sb_json_response(sb_automations_save(sb_post('automations'), sb_post('translations'))));
    case 'automations-run':
        die(sb_json_response(sb_automations_run($_POST['automation'], sb_post('validate'))));
    case 'automations-run-all':
        die(sb_json_response(sb_automations_run_all()));
    case 'automations-validate':
        die(sb_json_response(sb_automations_validate($_POST['automation'])));
    case 'chat-css':
        die(sb_json_response(sb_css(sb_post('color_1'), sb_post('color_2'), sb_post('color_3'), true)));
    case 'get-avatar':
        die(sb_json_response(sb_get_avatar($_POST['first_name'], sb_post('last_name'))));
    case 'get-agents-ids':
        die(sb_json_response(sb_get_agents_ids(sb_post('admins', true))));
    case 'get-agents-in-conversation':
        die(sb_json_response(sb_get_agents_in_conversation($_POST['conversation_id'])));
    case 'count-conversations':
        die(sb_json_response(sb_count_conversations(sb_post('status_code'))));
    case 'updates-available':
        die(sb_json_response(sb_updates_available()));
    case 'google-translate':
        die(sb_json_response(sb_google_translate($_POST['strings'], $_POST['language_code'])));
    case 'google-language-detection-update-user':
        die(sb_json_response(sb_google_language_detection_update_user($_POST['string'], sb_post('user_id'), sb_post('token'))));
    case 'export-settings':
        die(sb_json_response(sb_export_settings()));
    case 'import-settings':
        die(sb_json_response(sb_import_settings($_POST['file_url'])));
    case 'delete-file':
        die(sb_json_response(sb_file_delete($_POST['path'])));
    case 'check-conversations-assignment':
        die(sb_json_response(sb_check_conversations_assignment($_POST['conversations_ids'], sb_post('agent_id'), sb_post('department'))));
    default:
        die('["error", "Support Board Error [ajax.php]: No functions found with name: ' . $_POST['function'] . '."]');
}

function sb_json_response($result) {
    if (sb_is_error($result)) {
        return defined('SB_API') ? sb_api_error($result) : json_encode(['error', $result->code(), $result->function_name(), $result->message()]);
    } else {
        return defined('SB_API') ? sb_api_success($result) : json_encode(sb_is_validation_error($result) ? ['validation-error', $result->code()] : ['success', $result]);
    }
}

function sb_post($key, $default = false) {
    global $_POST;
    return isset($_POST[$key]) ? ($_POST[$key] == 'false' ? false : $_POST[$key]) : $default;
}

function sb_security() {
    global $_POST;
    $security = [
        'admin' => ['delete-file', 'import-settings', 'export-settings', 'dialogflow-smart-reply-generate-conversations-data', 'updates-available', 'automations-save','get-articles-categories', 'save-articles-categories', 'path', 'reports', 'aecommerce-sync-admins', 'aecommerce-sync-sellers', 'aecommerce-sync', 'whmcs-sync', 'whmcs-articles-sync', 'perfex-articles-sync', 'perfex-sync', 'woocommerce-get-session', 'woocommerce-get-attributes', 'woocommerce-get-taxonomies', 'woocommerce-dialogflow-intents', 'woocommerce-dialogflow-entities', 'dialogflow-curl', 'delete-leads', 'system-requirements', 'save-settings', 'get-settings', 'get-all-settings', 'add-user', 'delete-user', 'delete-users', 'app-get-key', 'app-activation', 'wp-sync'],
        'agent' => ['check-conversations-assignment', 'dialogflow-smart-reply-update', 'dialogflow-smart-reply', 'dialogflow-update-intent', 'dialogflow-get-intents', 'ump-get-conversation-details', 'armember-get-conversation-details', 'count-conversations', 'reports-update', 'get-agents-ids', 'send-custom-email', 'get-users-with-details', 'direct-message', 'messenger-send-message', 'whatsapp-send-message', 'get-user-language', 'get-notes', 'add-note', 'delete-note', 'user-online', 'get-user-from-conversation', 'aecommerce-get-conversation-details', 'whmcs-get-conversation-details', 'woocommerce-get-order', 'woocommerce-coupon-delete-expired', 'woocommerce-coupon-check', 'woocommerce-coupon', 'woocommerce-is-in-stock', 'woocommerce-get-attribute-by-name', 'woocommerce-get-attribute-by-term', 'woocommerce-get-product-taxonomies', 'woocommerce-get-product-images', 'woocommerce-get-product-id-by-name', 'woocommerce-get-user-orders', 'woocommerce-get-product', 'woocommerce-get-customer', 'dialogflow-get-agent', 'dialogflow-get-entity', 'woocommerce-products-popup', 'woocommerce-search-products', 'woocommerce-get-products', 'woocommerce-get-data', 'is-agent-typing', 'close-message', 'count-users', 'get-users', 'get-new-users', 'get-online-users', 'search-users', 'get-conversations', 'get-new-conversations', 'search-conversations', 'csv-users', 'csv-conversations', 'send-test-email', 'slack-users', 'clean-data', 'save-translations', 'dialogflow-intent', 'dialogflow-create-intent', 'dialogflow-entity', 'get-rating', 'save-articles', 'update', 'archive-slack-channels'],
        'user' => ['dialogflow-unknow-email', 'google-language-detection-update-user', 'google-translate', 'get-agents-in-conversation', 'update-conversation-agent', 'update-conversation-department', 'get-avatar', 'article-ratings', 'slack-presence', 'woocommerce-waiting-list', 'dialogflow-set-active-context', 'search-user-conversations', 'update-login', 'update-user', 'get-user', 'get-user-extra', 'update-user-to-lead', 'new-conversation', 'get-user-conversations', 'get-new-user-conversations', 'send-slack-message', 'slack-unarchive', 'update-message', 'delete-message', 'update-user-and-message', 'get-conversation', 'get-new-messages', 'set-rating', 'create-email', 'send-email'],
        'login' => ['dialogflow-message', 'transcript', 'automations-get', 'send-sms', 'pusher-trigger', 'woocommerce-shipping-locations', 'woocommerce-payment-methods', 'woocommerce-get-url', 'dialogflow-get-token', 'subscribe-email', 'woocommerce-returning-visitor', 'push-notification', 'queue', 'update-conversation-status', 'update-users-last-activity', 'is-typing', 'send-message', 'set-typing', 'user-autodata', 'saved-replies' ]
    ];
    $function = $_POST['function'];
    $user_id = sb_post('user_id', -1);
    $active_user = sb_get_active_user(sb_post('login-cookie'));

    // No check
    $no_check = true;
    foreach ($security as $key => $value) {
        if (in_array($function, $security[$key])) {
            $no_check = false;
            break;
        }
    }
    if ($no_check) {
        return true;
    }

    // Login check
    if (in_array($function, $security['login']) && $active_user) {
        return true;
    }
    if ($active_user && isset($active_user['user_type'])) {
        $user_type = $active_user['user_type'];
        $current_user_id = sb_isset($active_user, 'id', -2);

        // User check
        if (in_array($function, $security['user']) && (sb_is_agent($user_type) || $user_id == $current_user_id)) {
            return true;
        }

        // Agent check
        if (in_array($function, $security['agent']) && sb_is_agent($user_type)) {
            return true;
        }

        // Admin check
        if (in_array($function, $security['admin']) && $user_type == 'admin') {
            return true;
        }
    }
    return false;
}

?>