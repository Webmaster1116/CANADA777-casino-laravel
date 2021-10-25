<?php

/*
 * ==========================================================
 * FUNCTIONS.PHP
 * ==========================================================
 *
 * Main PHP functions file. © 2021 board.support. All rights reserved.
 *
 */

define('SB_VERSION', '3.3.3');

if (!defined('SB_PATH')) {
    $path = dirname(__DIR__, 1);
    if ($path == '') {
        $path = dirname(__DIR__);
    }
    define('SB_PATH', $path);
}
if (!defined('JSON_INVALID_UTF8_IGNORE')) {
    define('JSON_INVALID_UTF8_IGNORE', 0);
}

require_once(SB_PATH . '/config.php');

if (!defined('SB_DB_NAME') && defined('SB_CLOUD')) {
    if (isset($GLOBALS['sb-cloud-url'])) require_once($url);
    else if (!empty($_COOKIE['sb-cloud'])) {
        $cookie = json_decode(sb_encryption('decrypt', $_COOKIE['sb-cloud']), true);
        $url = SB_CLOUD_PATH . '/script/config/config_' . $cookie['token'] . '.php';
        require_once($url);
        $GLOBALS['sb-cloud-token'] = $url;
    } else return;
}

global $SB_CONNECTION;
global $SB_SETTINGS;
global $SB_LOGIN;
global $SB_LANGUAGE;
global $SB_TRANSLATIONS;
const  SELECT_FROM_USERS = 'SELECT id, first_name, last_name, email, profile_image, user_type, creation_time, last_activity, department, token';

/*
 * -----------------------------------------------------------
 * # APPS LOADING
 * -----------------------------------------------------------
 *
 * Load the external apps if availables
 *
 */

$sb_apps = ['dialogflow', 'slack', 'wordpress', 'tickets', 'woocommerce', 'ump', 'perfex', 'whmcs', 'aecommerce', 'messenger', 'whatsapp', 'armember'];
for ($i = 0; $i < count($sb_apps); $i++) {
    $file = SB_PATH . '/apps/' . $sb_apps[$i] . '/functions.php';
    if (file_exists($file)) {
        require_once($file);
    }
}

/*
 * -----------------------------------------------------------
 * # DATABASE
 * -----------------------------------------------------------
 *
 * Functions to read, update, delete database records and to connect to the database.
 *
 */

function sb_db_connect() {
    global $SB_CONNECTION;
    if (!defined('SB_DB_NAME')) return false;
    if (SB_DB_NAME != '' && $SB_CONNECTION) {
        sb_db_init_settings();
        return true;
    }
    $SB_CONNECTION = new mysqli(SB_DB_HOST, SB_DB_USER, SB_DB_PASSWORD, SB_DB_NAME, defined('SB_DB_PORT') && SB_DB_PORT != '' ? intval(SB_DB_PORT) : ini_get('mysqli.default_port'));
    if ($SB_CONNECTION->connect_error) {
        echo 'Connection error. Visit the admin area for more details or open the config.php file and check the database information. Message: ' . $SB_CONNECTION->connect_error . '.';
        return false;
    }
    sb_db_init_settings();
    return true;
}

function sb_db_get($query, $single = true) {
    global $SB_CONNECTION;
    $status = sb_db_connect();
    $value = ($single ? '' : []);
    if ($status) {
        $result = $SB_CONNECTION->query($query);
        if ($result) {
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    if ($single) {
                        $value = $row;
                    } else {
                        array_push($value, $row);
                    }
                }
            }
        } else {
            return sb_db_error('sb_db_get');
        }
    } else {
        return $status;
    }
    return $value;
}

function sb_db_query($query, $return = false) {
    global $SB_CONNECTION;
    $status = sb_db_connect();
    if ($status) {
        $result = $SB_CONNECTION->query($query);
        if ($result) {
            if ($return) {
                if (isset($SB_CONNECTION->insert_id) && $SB_CONNECTION->insert_id > 0) {
                    return $SB_CONNECTION->insert_id;
                } else {
                    return sb_db_error('sb_db_query');
                }
            } else {
                return true;
            }
        } else {
            return sb_db_error('sb_db_query');
        }
    } else {
        return $status;
    }
}

function sb_db_encode($string) {
    global $SB_CONNECTION;
    sb_db_connect();
    return $SB_CONNECTION->real_escape_string($string);
}

function sb_db_escape($value, $escape_special_chars = false) {
    $value = str_replace('"', '\"', $value);
    if ($escape_special_chars) $value = sb_db_encode($value);
    return $value;
}

function sb_db_json_enconde($array) {
    return sb_db_encode(str_replace(['"false"', '"true"'], ['false', 'true'], json_encode($array, JSON_INVALID_UTF8_IGNORE)));
}

function sb_db_error($function) {
    global $SB_CONNECTION;
    return new SBError('db-error', $function, $SB_CONNECTION->error);
}

function sb_db_check_connection($name = false, $user = false, $password = false, $host = false, $port = false) {
    global $SB_CONNECTION;
    $response = true;
    if ($name === false && defined('SB_DB_NAME')) {
        $name = SB_DB_NAME;
        $user = SB_DB_USER;
        $password = SB_DB_PASSWORD;
        $host = SB_DB_HOST;
        $port = defined('SB_DB_PORT') && SB_DB_PORT != '' ? intval(SB_DB_PORT) : false;
    }
    if ($name === false || $name == '') {
        return 'installation';
    }
    try {
        set_error_handler(function() {}, E_ALL);
    	$SB_CONNECTION = new mysqli($host, $user, $password, $name, $port === false ? ini_get('mysqli.default_port') : intval($port));
        sb_db_init_settings();
    }
    catch (Exception $e) {
        $response = $e->getMessage();
    }
    if ($SB_CONNECTION->connect_error) {
        $response = $SB_CONNECTION->connect_error;
    }
    restore_error_handler();
    return $response;
}

function sb_db_init_settings() {
    global $SB_CONNECTION;
    $SB_CONNECTION->set_charset('utf8mb4');
    $SB_CONNECTION->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
}

function sb_external_db($action, $name, $query = '', $extra = false) {
    $NAME = strtoupper($name);
    $name = strtolower($name);
    switch ($action) {
    	case 'connect':
            $connection = sb_isset($GLOBALS, 'SB_' . $NAME . '_CONNECTION');
            $defined = defined('SB_' . $NAME . '_DB_NAME');
            if (!empty($connection) && $connection->ping()) {
                return true;
            }
            if (!$defined) {
                $prefix = '';
                $database = sb_get_setting($name . '-db');
                if (empty($database[$name . '-db-name'])) {
                    return new SBError('db-error', 'sb_external_db', 'Missing database details in ' . $name . ' settings area.');
                }
                define('SB_' . $NAME . '_DB_HOST', $database[$name . '-db-host']);
                define('SB_' . $NAME . '_DB_USER', $database[$name . '-db-user']);
                define('SB_' . $NAME . '_DB_PASSWORD', $database[$name . '-db-password']);
                define('SB_' . $NAME . '_DB_NAME', $database[$name . '-db-name']);
                if ($name == 'perfex' || $name == 'whmcs') {
                    define('SB_' . $NAME . '_DB_PREFIX', empty($database[$name . '-db-prefix']) ? 'tbl' : $database[$name . '-db-prefix']);
                    $prefix = PHP_EOL . 'define(\'SB_' . $NAME . '_DB_PREFIX\', \'' . sb_isset($database, $name . '-db-prefix', 'tbl') . '\');';
                }
                sb_write_config_extra('/* '. $NAME .' CRM  */' . PHP_EOL . 'define(\'SB_' . $NAME . '_DB_HOST\', \'' . $database[$name . '-db-host'] . '\');' . PHP_EOL . 'define(\'SB_' . $NAME . '_DB_USER\', \'' . $database[$name . '-db-user'] . '\');' . PHP_EOL . 'define(\'SB_' . $NAME . '_DB_PASSWORD\', \'' . $database[$name . '-db-password'] . '\');' . PHP_EOL . 'define(\'SB_' . $NAME . '_DB_NAME\', \'' . $database[$name . '-db-name'] . '\');' . $prefix);
            }
            $connection = new mysqli(constant('SB_' . $NAME . '_DB_HOST'), constant('SB_' . $NAME . '_DB_USER'), constant('SB_' . $NAME . '_DB_PASSWORD'), constant('SB_' . $NAME . '_DB_NAME'));
            if ($connection->connect_error) {
                if ($defined) {
                    $database = sb_get_setting($name . '-db');
                    if (constant('SB_' . $NAME . '_DB_HOST') != $database[$name . '-db-host'] || constant('SB_' . $NAME . '_DB_USER') != $database[$name . '-db-user'] || constant('SB_' . $NAME . '_DB_PASSWORD') != $database[$name . '-db-password'] || constant('SB_' . $NAME . '_DB_NAME') != $database[$name . '-db-name'] || (defined('SB_' . $NAME . '_DB_PREFIX') && constant('SB_' . $NAME . '_DB_PREFIX') != $database[$name . '-db-prefix'])) {
                        $raw = file_get_contents(SB_PATH . '/config.php');
                        sb_file(SB_PATH . '/config.php', str_replace(['/* Perfex CRM  */', 'define(\'SB_' . $NAME . '_DB_HOST\', \'' . constant('SB_' . $NAME . '_DB_HOST') . '\');', 'define(\'SB_' . $NAME . '_DB_USER\', \'' . constant('SB_' . $NAME . '_DB_USER') . '\');', 'define(\'SB_' . $NAME . '_DB_PASSWORD\', \'' . constant('SB_' . $NAME . '_DB_PASSWORD') . '\');', 'define(\'SB_' . $NAME . '_DB_NAME\', \'' . constant('SB_' . $NAME . '_DB_NAME') . '\');', defined('SB_' . $NAME . '_DB_PREFIX') ? 'define(\'SB_' . $NAME . '_DB_PREFIX\', \'' . constant('SB_' . $NAME . '_DB_PREFIX') . '\');' : ''], '', $raw));
                    }
                }
                die($connection->connect_error);
            }
            $connection->set_charset('utf8mb4');
            $connection->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
            $GLOBALS['SB_' . $NAME . '_CONNECTION'] = $connection;
            return true;
        case 'read':
            $status = sb_external_db('connect', $name);
            $value = $extra ? '' : [];
            if ($status === true) {
                $result = $GLOBALS['SB_' . strtoupper($name) . '_CONNECTION']->query($query);
                if ($result) {
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            if ($extra) {
                                $value = $row;
                            } else {
                                array_push($value, $row);
                            }
                        }
                    }
                } else {
                    return new SBError('db-error', 'sb_external_db', $GLOBALS['SB_' . strtoupper($name) . '_CONNECTION']->error);
                }
            } else {
                return $status;
            }
            return $value;
        case 'write':
            $status = sb_external_db('connect', $name);
            if ($status === true) {
                $connection = $GLOBALS['SB_' . $NAME . '_CONNECTION'];
                $result = $connection->query($query);
                if ($result) {
                    if ($extra) {
                        if (isset($connection->insert_id) && $connection->insert_id > 0) {
                            return $connection->insert_id;
                        } else {
                            return sb_db_error('sb_db_query');
                        }
                    } else {
                        return true;
                    }
                } else {
                    return new SBError('db-error', 'sb_external_db', $connection->error);
                }
            }
            return $status;
    }
    return false;
}

/*
 * -----------------------------------------------------------
 * # ERRORS
 * -----------------------------------------------------------
 *
 * Return a error message as json string
 *
 */

class SBError {
    public $error;

    function __construct($error_code, $function = '', $message = '') {
        $this->error = ['message' => $message, 'function' => $function, 'code' => $error_code];
    }

    public function __toString() {
        return $this->message();
    }

    function message() {
        return $this->error['message'];
    }

    function code() {
        return $this->error['code'];
    }

    function function_name() {
        return $this->error['function'];
    }
}

class SBValidationError {
    public $error;

    function __construct($error_code) {
        $this->error = $error_code;
    }

    public function __toString() {
        return $this->error;
    }

    function code() {
        return $this->error;
    }
}

function sb_is_error($object) {
    return is_a($object, 'SBError');
}

function sb_is_validation_error($object) {
    return is_a($object, 'SBValidationError');
}

/*
 * -----------------------------------------------------------
 * # LOGIN AND ACCOUNT
 * -----------------------------------------------------------
 *
 * Check if the login details are corrects and if yes set the login
 * Logout a user
 * Return the logged in user information
 * Return the agent department
 *
 */

function sb_login($email = '', $password = '', $user_id = '', $user_token = '') {
    global $SB_LOGIN;
    $valid_login = false;
    $result = null;
    if ($email != '' && $password != '') {

        // Login for registered users and agents
        $result = sb_db_get('SELECT id, profile_image, first_name, last_name, email, password, user_type, token, department, password FROM sb_users WHERE email = "' . $email . '" LIMIT 1');
        if (sb_is_error($result)) return $result;
        if (isset($result) && $result != '' && isset($result['password']) && isset($result['user_type']) && sb_password_verify($password, $result['password'])) {
            $valid_login = true;
        }
    } else if ($user_id != '' && $user_token != '') {

        // Login for visitors
        $result = sb_db_get('SELECT id, profile_image, first_name, last_name, email, password, user_type, token FROM sb_users WHERE id = "' . $user_id . '" AND token = "' . $user_token . '" LIMIT 1');
        if (sb_is_error($result)) return $result;
        if (isset($result['user_type']) && isset($result['token'])) {
            $valid_login = true;
        }
    }
    if ($valid_login) {
        $settings =  ['id' => $result['id'], 'profile_image' => $result['profile_image'], 'first_name' => $result['first_name'], 'last_name' => $result['last_name'], 'email' => $result['email'], 'user_type' => $result['user_type'], 'token' => $result['token'], 'url' => SB_URL, 'password' => $result['password']];
        if (isset($result['department'])) {
            $settings['department'] = $result['department'];
        }
        sb_set_cookie_login($settings);
        $SB_LOGIN = $settings;
        return [$settings, sb_encryption('encrypt', json_encode($settings))];
    }
    return false;
}

function sb_update_login($profile_image, $first_name, $last_name, $email, $department = '', $user_type = false, $user_id = false) {
    global $SB_LOGIN;
    $settings = sb_get_cookie_login();
    if (empty($settings)) $settings = [];
    if ($user_id !== false) $settings['id'] = $user_id;
    $settings['profile_image'] = $profile_image;
    $settings['first_name'] = $first_name;
    $settings['last_name'] = $last_name;
    $settings['email'] = $email;
    $settings['department'] = $department == 'NULL' || $department == '' || $department === false ? null : $department;
    if ($user_type != false) {
        $settings['user_type'] = $user_type;
    }
    if (!headers_sent()) {
        sb_set_cookie_login($settings);
    }
    $SB_LOGIN = $settings;
    return [$settings, sb_encryption('encrypt', json_encode($settings))];
}

function sb_logout() {
    global $SB_LOGIN;
    if (!headers_sent()) {
        $time = time() - 3600;
        setcookie('sb-login', '', $time);
        setcookie('sb-cloud', '', $time);
    }
    $SB_LOGIN = null;
    return true;
}

function sb_get_active_user($login_data = false, $database = false, $login_app = false, $user_token = false) {
    global $SB_LOGIN;
    $return = false;
    if (isset($SB_LOGIN)) {
        $return = $SB_LOGIN;
    }
    if ($return === false && !empty($login_data)) {
        $return = json_decode(sb_encryption('decrypt', $login_data), true);
    }
    if ($return === false) {
        $return = sb_get_cookie_login();
    }
    if ($login_app !== false) {
        $app = $login_app[1];
        $login_app_data = $login_app[0];
        if (defined('SB_WP') && $app == 'wp') {
            if ($return === false || !isset($return['email'])) {
                $return = sb_wp_get_active_user($login_app_data);
                if (isset($return[1])) {
                    $return = array_merge($return[0], ['cookie' => $return[1]]);
                }
            } else {
                $wp_user = sb_wp_get_user($login_app_data[0]);
                if (isset($wp_user['email']) && $wp_user['email'] != $return['email']) {
                    $return = sb_wp_get_active_user($login_app_data);
                }
            }
        }
        if (defined('SB_PERFEX') && $app == 'perfex') {
            $return = sb_perfex_get_active_user_function($return, $login_app_data);
        }
        if (defined('SB_WHMCS') && $app == 'whmcs') {
            $return = sb_whmcs_get_active_user_function($return, $login_app_data);
        }
        if (defined('SB_AECOMMERCE') && $app == 'aecommerce') {
            $return = sb_aecommerce_get_active_user_function($return, $login_app_data);
        }
    }
    if (($database && $return && isset($return['id'])) || $user_token) {
        $active_user = sb_db_get('SELECT id, profile_image, first_name, last_name, email, password, user_type FROM sb_users WHERE ' . ($user_token ? ('token = "' . $user_token . '"') : ('id = ' . $return['id'])));
        if ($active_user && (empty($return['password']) || empty($active_user['password']) || $return['password'] == $active_user['password'])) {
            $return['id'] = $active_user['id'];
            $return['profile_image'] = $active_user['profile_image'];
            $return['first_name'] = $active_user['first_name'];
            $return['last_name'] = $active_user['last_name'];
            $return['email'] = $active_user['email'];
            $return['password'] = $active_user['password'];
            $return['user_type'] = $active_user['user_type'];
            $return['phone'] = sb_get_user_extra($return['id'], 'phone');
            $return['cookie'] = sb_encryption('encrypt', json_encode($return));
        } else if ($login_data !== false && $login_app !== false) {
            unset($_COOKIE['sb-login']);
            $SB_LOGIN = false;
            return sb_get_active_user(false, $database, $login_app);
        } else $return = false;
    }
    if ($return !== false && !isset($SB_LOGIN)) {
        $SB_LOGIN = $return;
    }
    return $return;
}

function sb_set_cookie_login($value) {
    if (!headers_sent()) {
        setcookie('sb-login', sb_encryption('encrypt', json_encode($value)), time() + 315569260, '/', sb_get_setting('cookie-domain', ''));
    }
}

function sb_get_cookie_login() {
    if (isset($_COOKIE['sb-login'])) {
        $response = json_decode(sb_encryption('decrypt', $_COOKIE['sb-login']), true);
        return empty($response) ? false : $response;
    }
    return false;
}

function sb_password_verify($password, $hash) {
    $success = password_verify($password, $hash);
    if (!$success && defined('SB_WP')) {
        $wp_hasher = new SBPasswordHash(8, true);
        $success = $wp_hasher->CheckPassword($password, $hash);
    }
    return $success;
}

function sb_is_agent($user = false) {
    if ($user === '') return false;
    $user = $user === false ? sb_get_active_user() : (is_string($user) ? ['user_type' => $user] : $user);
    if ($user == false) return false;
    return $user['user_type'] == 'agent' || $user['user_type'] == 'admin' || $user['user_type'] == 'bot';
}

function sb_get_agent_department() {
    if (sb_is_agent() && !defined('SB_API')) {
        $user = sb_get_active_user();
        return isset($user['department']) && $user['department'] != '' ? $user['department'] : false;
    }
    return false;
}

/*
 * -----------------------------------------------------------
 * # JAVASCRIPT GLOBAL AND ADMIN
 * -----------------------------------------------------------
 *
 * Global Javascripts and admin codes to be printed to the user and admin page.
 *
 */

function sb_js_global() {
    global $SB_LANGUAGE;
    if (!isset($SB_LANGUAGE)) {
        sb_init_translations();
    }
    $ajax_url = str_replace('//include', '/include', SB_URL . '/include/ajax.php');
    $code = '<script data-cfasync="false">';
    $code .= 'var SB_AJAX_URL = "' . $ajax_url . '";';
    $code .= 'var SB_URL = "' . SB_URL . '";';
    $code .= 'var SB_LANG = ' . ($SB_LANGUAGE ? json_encode($SB_LANGUAGE) : 'false') . ';';
    $code .= '</script>';
    echo $code;
}

function sb_js_admin() {
    $active_user = sb_get_active_user();
    $active_user_type = $active_user ? $active_user['user_type'] : false;
    $settings = [
        'bot-id' => sb_get_bot_id(),
        'close-message' => sb_get_multi_setting('close-message', 'close-active'),
        'routing' => (!$active_user || $active_user_type == 'agent') && (sb_get_multi_setting('queue', 'queue-active') || sb_get_setting('routing') || sb_get_multi_setting('agent-hide-conversations', 'agent-hide-conversations-active')),
        'desktop-notifications' => sb_get_setting('desktop-notifications'),
        'push-notifications' => sb_get_multi_setting('push-notifications', 'push-notifications-active'),
        'push-notifications-users' => sb_get_multi_setting('push-notifications', 'push-notifications-users-active'),
        'push-notifications-id' => sb_get_multi_setting('push-notifications', 'push-notifications-id'),
        'flash-notifications' => sb_get_setting('flash-notifications'),
        'notifications-icon' => sb_get_setting('notifications-icon', SB_URL . '/media/icon.png'),
        'auto-updates' => sb_get_setting('auto-updates'),
        'sounds' => sb_get_setting('chat-sound-admin'),
        'pusher' => sb_pusher_active(),
        'bot-unknow-notify' => sb_get_setting('bot-unknow-notify'),
        'notify-user-email' => sb_get_setting('notify-user-email') || sb_get_multi_setting('email-piping', 'email-piping-active'),
        'assign-conversation-to-agent' => $active_user_type == 'agent' && sb_get_multi_setting('agent-hide-conversations', 'agent-hide-conversations-active') && sb_get_multi_setting('agent-hide-conversations', 'agent-hide-conversations-view'),
        'sms-active-users' => sb_get_multi_setting('sms', 'sms-active-users'),
        'sms' => sb_get_multi_setting('sms', 'sms-user'),
        'now-db' => gmdate('Y-m-d H:i:s'),
        'login-time' => time(),
        'smart-reply' => sb_get_multi_setting('dialogflow-smart-reply', 'dialogflow-smart-reply-active'),
        'single-agent' => intval(sb_db_get('SELECT COUNT(*) as count FROM sb_users WHERE user_type = "agent" OR user_type = "admin"')['count']) == 1,
        'slack-active' => sb_get_setting('slack-active'),
        'translation' => sb_get_setting('google-translation'),
        'active-agent-language' => sb_get_user_language()
    ];
    $code = '<script>';
    if (defined('SB_WOOCOMMERCE')) {
        $settings['currency'] = sb_get_setting('wc-currency-symbol');
        $settings['languages'] = json_encode(sb_isset(sb_wp_language_settings(), 'languages', []));
    }
    if (defined('SB_PERFEX')) $settings['perfex-url'] = sb_get_setting('perfex-url');
    if (defined('SB_WHMCS')) $settings['whmcs-url'] = sb_get_setting('whmcs-url');
    if (defined('SB_AECOMMERCE')) $settings['aecommerce-panel-title'] = sb_get_setting('aecommerce-panel-title', 'Active eCommerce');
    if ($settings['pusher']) {
        $settings['pusher-key'] = sb_get_multi_setting('pusher', 'pusher-key');
        $settings['pusher-cluster'] = sb_get_multi_setting('pusher', 'pusher-cluster');
    }
    if ($settings['smart-reply']) {
        $settings['smart-reply-language-detection'] = sb_get_multi_setting('dialogflow-smart-reply', 'dialogflow-smart-reply-language-detection');
        $settings['smart-reply-language-detection-bot'] = sb_get_setting('dialogflow-active') && sb_get_multi_setting('dialogflow-language-detection', 'dialogflow-language-detection-active');
        $settings['smart-reply-agent-assistant'] = !empty(sb_get_multi_setting('dialogflow-smart-reply', 'dialogflow-smart-reply-profile'));
    }
    if ($active_user) {
        if (empty($active_user['url']) || $active_user['url'] == SB_URL) {
            $code .= 'var SB_ACTIVE_AGENT = { id: "' . $active_user['id'] . '", full_name: "' . sb_get_user_name($active_user) . '", user_type: "' . $active_user_type . '", profile_image: "' . $active_user['profile_image'] . '", department: "' . sb_isset($active_user, 'department', '') . '"};';
        } else {
            $code .= 'SBF.reset();';
        }
    } else {
        $code .= 'var SB_ACTIVE_AGENT = { id: "", full_name: "", user_type: "", profile_image: "" };';
    }
    if (defined('SB_WP')) {
        $code .= 'var SB_WP = true;';
    }
    $code .= 'var SB_LANGUAGE_CODES = ' . file_get_contents(SB_PATH . '/resources/languages/language-codes.json') . ';';
    $code .= 'var SB_ADMIN_SETTINGS = ' . json_encode($settings) .';';
    $code .= 'var SB_TRANSLATIONS = ' . json_encode(sb_get_current_translations(), JSON_INVALID_UTF8_IGNORE) . ';';
    $code .= 'var SB_VERSIONS = { sb: "' . SB_VERSION . '", dialogflow: "' . (defined('SB_DIALOGFLOW') ? SB_DIALOGFLOW : -1) . '", slack: "' . (defined('SB_SLACK') ? SB_SLACK : -1) . '", tickets: "' . (defined('SB_TICKETS') ? SB_TICKETS : -1) . '", perfex: "' . (defined('SB_PERFEX') ? SB_PERFEX : -1) . '", whmcs: "' . (defined('SB_WHMCS') ? SB_WHMCS : -1) . '", woocommerce: "' . (defined('SB_WOOCOMMERCE') ? SB_WOOCOMMERCE : -1) . '", ump: "' . (defined('SB_UMP') ? SB_UMP : -1) . '", aecommerce: "' . (defined('SB_AECOMMERCE') ? SB_AECOMMERCE : -1) . '", messenger: "' . (defined('SB_MESSENGER') ? SB_MESSENGER : -1)  . '", whatsapp: "' . (defined('SB_WHATSAPP') ? SB_WHATSAPP : -1) . '", armember: "' . (defined('SB_ARMEMBER') ? SB_ARMEMBER : -1) . '"};';
    $code .= '</script>';
    echo $code;
}

/*
 * -----------------------------------------------------------
 * # USERS
 * -----------------------------------------------------------
 *
 * 1. Add a new user or agent.
 * 2. Add a new user extra details
 * 3. Add a new user and login it
 * 4. Delete a user and all the related information (conversations, messages)
 * 5. Delete multiple users and all the related information (conversations, messages)
 * 6. Delete all leads
 * 7. Update a user or agent.
 * 8. Update a user or agent detail or extra detail.
 * 9. Update a visitor to lead
 * 10. Update the current user and a conversation message
 * 11. Return the user with the given id
 * 12. Return all users, Agents
 * 13. Return the users registered after the given date
 * 14. Search users based on the gived keyword
 * 15. Return the users count grouped by user type
 * 16. Return the user additional details
 * 17. Return the agent or admin with the given ID
 * 18. Set the active admin if any and register if required
 * 19. Return the full name of a user
 * 20. Save a CSV file with all users details
 * 21. Save automatic information from the user: IP, Country, OS, Browser
 * 22. Set and get the current page URL of a user
 * 23. Create or update the bot
 * 24. Return the bot ID
 * 25. Return the user or the last agent of a conversation
 * 26. Return an array with the agents ids
 * 27. Generate the profile picture of the user from its name
 * 28. Return the users who have the requested details
 * 29. Return the ID of the active user
 * 30. Get a user from a detail
 *
 */

function sb_add_user($settings = [], $settings_extra = [], $hash_password = true) {
    $keys = ['profile_image', 'first_name', 'last_name', 'email', 'user_type', 'password', 'department'];
    for ($i = 0; $i < count($keys); $i++) {
        $settings[$keys[$i]] = sb_isset($settings, $keys[$i], '');
        if (!is_string($settings[$keys[$i]])) {
            $settings[$keys[$i]] = $settings[$keys[$i]][0];
        }
    }
    if (!empty($settings['email']) && intval(sb_db_get('SELECT COUNT(*) as count FROM sb_users WHERE email = "' . $settings['email'] . '"')['count']) > 0) {
        if (sb_get_setting('duplicate-emails')) {
            sb_db_query('UPDATE sb_users SET email = NULL WHERE email = "' . $settings['email'] . '"');
        } else return new SBValidationError('duplicate-email');
    }
    if (!empty($settings_extra['phone']) && sb_get_user_by('phone', $settings_extra['phone'][0])) {
        return new SBValidationError('duplicate-phone');
    }
    if (empty($settings['profile_image'])) {
        $settings['profile_image'] = sb_get_avatar($settings['first_name'], $settings['last_name']);
    }
    if (empty($settings['first_name'])) {
        $name = sb_get_setting('visitor-prefix');
        $settings['first_name'] = $name === false || $name == '' ? 'User' : $name;
        $settings['last_name'] = '#' . rand(0, 99999);
    }
    if (empty($settings['user_type'])) {
        $settings['user_type'] = empty($settings['email']) ? 'visitor' : 'user';
    } else if (!in_array($settings['user_type'], ['visitor', 'user', 'lead', 'agent', 'admin', 'bot'])) {
        return new SBValidationError('invalid-user-type');
    }
    if ($settings['user_type'] == 'user') {
        if (!empty($settings['first_name']) && substr($settings['last_name'], 0, 1) == '#') {
            $settings['last_name'] = '';
        }
    }
    if (sb_is_agent($settings['user_type']) && sb_isset(sb_get_active_user(), 'user_type') != 'admin') {
        return new SBError('security-error', 'sb_add_user');
    }
    if (!empty($settings['password']) && $hash_password) {
        $settings['password'] = password_hash($settings['password'], PASSWORD_DEFAULT);
    }
    if (empty($settings['department'])) {
        $settings['department'] = 'NULL';
    }
    $now = gmdate('Y-m-d H:i:s');
    $token = bin2hex(openssl_random_pseudo_bytes(20));
    $query = 'INSERT INTO sb_users(first_name, last_name, password, email, profile_image, user_type, creation_time, token, department, last_activity) VALUES ("' . sb_db_escape(sb_sanatize($settings['first_name'])) . '", "' . sb_db_escape(sb_sanatize($settings['last_name'])) . '", "' . sb_db_escape(sb_sanatize($settings['password'])) . '", ' . ($settings['email'] == '' ? 'NULL' : '"' . sb_db_escape(sb_sanatize($settings['email'])) . '"') . ', "' . sb_db_escape(sb_sanatize($settings['profile_image'])) . '", "' . $settings['user_type'] . '", "' . $now . '", "' . $token . '", ' . $settings['department'] . ', "' . $now . '")';
    $result = sb_db_query($query, true);

    if (!sb_is_error($result) && is_int($result) && $result > 0 && isset($settings_extra)) {
        sb_add_new_user_extra($result, $settings_extra);
    }
    if (!sb_is_error($result) && !sb_is_agent() && ($settings['user_type'] == 'user' || sb_get_setting('visitor-autodata'))) {
        sb_user_autodata($result);
    }
    if ($settings['user_type'] == 'visitor') {
        sb_reports_update('visitors');
    }
    if (isset($_POST['payload']) && isset($_POST['payload']['rich-messages']) && isset($_POST['payload']['rich-messages']['registration'])) {
        sb_reports_update('registrations');
    }
    if ($settings['email'] != '') {
        sb_newsletter($settings['email'], $settings['first_name'], $settings['last_name']);
    }
    return $result;
}

function sb_add_new_user_extra($user_id, $settings) {
    $query = '';
    foreach ($settings as $key => $setting) {
        if (is_array($setting) && $setting[0] != '' && $setting[0] != 'null') {
            $query .= '("' . $user_id . '", "' . $key . '", "' . sb_db_escape(sb_sanatize($setting[1])) . '", "' . sb_db_escape(sb_sanatize($setting[0])) . '"),';
        }
    }
    if ($query != '') {
        $query = 'INSERT IGNORE INTO sb_users_data(user_id, slug, name, value) VALUES ' . substr($query, 0, -1);
        return sb_db_query($query);
    }
    return false;
}

function sb_add_user_and_login($settings, $settings_extra, $hash_password = true) {
    $response = sb_add_user($settings, $settings_extra, $hash_password);
    if (is_int($response)) {
        $token = sb_db_get('SELECT token FROM sb_users WHERE id = "' . $response . '"');
        return sb_login('', '', $response, $token['token']);
    }
    return $response;
}

function sb_delete_user($user_id) {
    return sb_db_query('DELETE FROM sb_users WHERE id = "' . $user_id . '"');
}

function sb_delete_users($user_ids) {
    $query = '';
    for ($i = 0; $i < count($user_ids); $i++) {
        $query .= $user_ids[$i] . ',';
    }
    return sb_db_query('DELETE FROM sb_users WHERE id IN (' . substr($query, 0, -1) . ')');
}

function sb_delete_leads() {
    return sb_db_query('DELETE FROM sb_users WHERE user_type = "lead"');
}

function sb_update_user($user_id, $settings, $settings_extra = [], $hash_password = true) {
    $keys = ['profile_image', 'first_name', 'last_name', 'email', 'user_type', 'password', 'department'];
    for ($i = 0; $i < count($keys); $i++) {
        $settings[$keys[$i]] = sb_isset($settings, $keys[$i], '');
        if (!is_string($settings[$keys[$i]])) {
            $settings[$keys[$i]] = $settings[$keys[$i]][0];
        }
    }

    $profile_image = $settings['profile_image'];
    $first_name = $settings['first_name'];
    $last_name = $settings['last_name'];
    $email = $settings['email'];
    $user_type = $settings['user_type'];
    $password = isset($settings['password']) && $settings['password'] != '********' ? $settings['password'] : '';
    $department = sb_isset($settings, 'department', 'NULL');
    $active_user = sb_get_active_user();
    $query = '';

    if ($email != '' && intval(sb_db_get('SELECT COUNT(*) as count FROM sb_users WHERE email = "' . $email . '" AND id <> ' . $user_id)['count']) > 0) {
        if (sb_get_setting('duplicate-emails')) {
            sb_db_query('UPDATE sb_users SET email = NULL WHERE email = "' . sb_sanatize($settings['email']) . '"');
        } else return new SBValidationError('duplicate-email');
    }
    if (!empty($settings_extra['phone']) && intval(sb_db_get('SELECT COUNT(*) as count FROM sb_users_data WHERE slug = "phone" AND (value = "' . $settings_extra['phone'][0] . '"' . (strpos($settings_extra['phone'][0], '+') !== false ? (' OR value = "' . str_replace('+', '00', $settings_extra['phone'][0]) . '"') : '') . ') AND user_id <> ' . $user_id)['count']) > 0) {
        return new SBValidationError('duplicate-phone');
    }

    if ($profile_image == '') {
        $profile_image = SB_URL . '/media/user.svg';
    }
    if ($profile_image == SB_URL . '/media/user.svg') {
        $profile_image = sb_get_avatar($first_name, $last_name);
    }
    if ($first_name != '') {
        $query .= ', first_name = "' . sb_db_escape($first_name) . '"';
    }
    if ($password != '') {
        if ($hash_password) $password = password_hash($password, PASSWORD_DEFAULT);
        $query .= ', password = "' . sb_db_escape($password) . '"';
    }
    if ($department == '') {
        $department = 'NULL';
    }
    if ($user_type == 'user' && $first_name != '' && $last_name != '' && substr($last_name, 0, 1) == '#') {
        $last_name = '';
    }
    if (!sb_is_agent($user_type)) {
        $user_type = $email != '' ? 'user' : (intval(sb_db_get('SELECT COUNT(*) AS count FROM sb_conversations WHERE user_id = "' . $user_id . '"')['count']) > 0 ? 'lead' : 'visitor');
    }

    $query_final = 'UPDATE sb_users SET profile_image = "' . sb_db_escape(sb_sanatize($profile_image)) . '", last_name = "' . sb_db_escape(sb_sanatize($last_name)) . '", user_type = "' . sb_sanatize($user_type) . '", email = ' . (strlen($email) == 0 ? 'NULL' : '"' . sb_db_escape(sb_sanatize($email)) . '"') . ', department = ' . sb_sanatize($department . $query) . ' WHERE id = "' . $user_id . '"';
    $result = sb_db_query($query_final);

    foreach ($settings_extra as $key => $setting) {
        if (is_array($setting)) {
            sb_db_query('REPLACE INTO sb_users_data SET name = "' . sb_db_escape(sb_sanatize($setting[1])) . '", value = "' . sb_db_escape(sb_sanatize($setting[0])) . '", slug = "' . sb_sanatize($key) . '", user_id = "' . $user_id . '"');
        }
    }
    sb_db_query('DELETE FROM sb_users_data WHERE user_id = "' . $user_id . '" AND value = ""');

    if (defined('SB_SLACK') && $first_name != '' && $last_name != '' && sb_get_setting('slack-active')) {
        sb_slack_rename_channel($user_id, trim($first_name . '_' . $last_name));
    }
    if ($active_user && $active_user['id'] == $user_id) {
        $result = sb_update_login($profile_image, $first_name, $last_name, $email, $department, $user_type, $user_id);
        sb_user_autodata($user_id);
    }
    if ($email) {
        sb_newsletter($email, $first_name, $last_name);
    }
    return $result;
}

function sb_update_user_value($user_id, $slug, $value, $name = '') {
    if (empty($value)) {
        return sb_db_query('DELETE FROM sb_users_data WHERE user_id = "' . $user_id . '" AND slug = "' . sb_db_escape($slug) . '"');
    }
    if (in_array($slug, ['profile_image', 'first_name', 'last_name', 'email', 'password', 'department', 'user_type', 'last_activity', 'typing'])) {
        if ($slug == 'password') $value = password_hash($value, PASSWORD_DEFAULT);
        if ($slug == 'email') {
            sb_newsletter($value);
        }
        return sb_db_query('UPDATE sb_users SET ' . sb_db_escape(sb_sanatize($slug)) . ' = "' . sb_db_escape(sb_sanatize($value)) . '" WHERE id = "' . $user_id . '"');
    }
    return sb_db_query('REPLACE INTO sb_users_data SET name = "' . sb_db_escape(sb_sanatize($name)) . '", value = "' . sb_db_escape(sb_sanatize($value)) . '", slug = "' . sb_db_escape(sb_sanatize($slug)) . '", user_id = "' . $user_id . '"');
}

function sb_update_user_to_lead($user_id) {
    sb_user_autodata($user_id);
    return sb_update_user_value($user_id, 'user_type', 'lead');
}

function sb_update_user_and_message($user_id, $settings, $settings_extra = [], $message_id = '', $message = '', $payload = false) {
    $result = sb_update_user($user_id, $settings, $settings_extra);
    $rich_message = sb_isset($payload, 'rich-messages');
    if (sb_is_validation_error($result) && $result->code() == 'duplicate-email') {
        return $result;
    }
    if ($message_id != '' && $message != '') {
        sb_update_message($message_id, $message, false, $payload);
    }
    if ($rich_message) {
        if (isset($rich_message['sb-follow-up-form'])) {
            sb_reports_update('follow-up');
        }
        if (isset($rich_message['registration'])) {
            sb_reports_update('registrations');
        }
    }
    return $result;
}

function sb_get_user($user_id, $extra = false) {
    $user = sb_db_get(SELECT_FROM_USERS . ', password FROM sb_users WHERE id = ' . $user_id);
    if (isset($user) && is_array($user)) {
        if ($extra) {
            $user['details'] = sb_get_user_extra($user_id);
        }
        return $user;
    } else {
        return false;
    }
}

function sb_get_users($sorting = ['creation_time' , 'DESC'], $user_types = [], $search = '', $pagination = 0) {
    $query = '';
    $query_search = '';
    $count = count($user_types);
    if ($count) {
        for ($i = 0; $i < $count; $i++) {
            $query .= 'user_type = "' . $user_types[$i] . '" OR ';
        }
        $query = '(' . substr($query, 0, strlen($query) - 4) . ')';
    }
    if ($search != '') {
        $searched_users = sb_search_users($search);
        $count_search = count($searched_users);
        if ($count_search > 0) {
            for ($i = 0; $i < $count_search; $i++) {
                $query_search .= $searched_users[$i]['id'] . ',';
            }
            $query .= ($query != '' ? ' AND ' : '') . 'id IN (' . substr($query_search, 0, -1) . ')';
        }
    }
    if ($query != '') {
        $query = ' WHERE user_type <> "bot" AND ' . $query;
    } else {
        $query = ' WHERE user_type <> "bot"';
    }
    $users = sb_db_get(SELECT_FROM_USERS . ' FROM sb_users ' . $query . ' ORDER BY ' . $sorting[0] . ' ' . $sorting[1] . ' LIMIT ' . ($pagination * 100) . ',100', false);
    if (isset($users) && is_array($users)) {
        return $users;
    } else {
        return new SBError('db-error', 'sb_get_users', $users);
    }
}

function sb_get_new_users($datetime) {
    $users = sb_db_get(SELECT_FROM_USERS . ' FROM sb_users WHERE  user_type <> "bot" AND creation_time > "' . $datetime . '" ORDER BY creation_time DESC', false);
    if (isset($users) && is_array($users)) {
        return $users;
    } else {
        return new SBError('db-error', 'sb_get_new_users', $users);
    }
}

function sb_search_users($search) {
    $search = trim(sb_db_escape($search));
    $query = '';
    if (strpos($search, ' ') > 0) {
        $search = explode(' ', $search);
    } else {
        $search = [$search];
    }
    for ($i = 0; $i < count($search); $i++) {
    	$query .= 'first_name LIKE "%' . $search[$i] . '%" OR last_name LIKE "%' . $search[$i] . '%" OR ';
    }
    $result = sb_db_get('SELECT * FROM sb_users WHERE user_type <> "bot" AND ' . $query . ' email LIKE "%' . $search[0] . '%" OR id IN (SELECT user_id FROM sb_users_data WHERE value LIKE "%' . $search[0] . '%") GROUP BY sb_users.id;', false);
    if (isset($result) && is_array($result)) {
        return $result;
    } else {
        return new SBError('db-error', 'sb_search_users', $result);
    }
}

function sb_count_users() {
    return sb_db_get('SELECT SUM(CASE WHEN user_type <> "bot" THEN 1 ELSE 0 END) AS `all`, SUM(CASE WHEN user_type = "lead" THEN 1 ELSE 0 END) AS `lead`, SUM(CASE WHEN user_type = "user" THEN 1 ELSE 0 END) AS `user`, SUM(CASE WHEN user_type = "visitor" THEN 1 ELSE 0 END) AS `visitor` FROM sb_users');
}

function sb_get_user_extra($user_id, $slug = false, $default = false) {
    if (empty($user_id)) return false;
    $response = sb_db_get('SELECT slug, name, value FROM sb_users_data WHERE user_id = ' . $user_id . ($slug ? ' AND slug = "' . $slug . '" LIMIT 1' : ''), $slug ? true : false);
    return $slug ? sb_isset($response, 'value', $default) : $response;
}

function sb_get_agent($agent_id) {
    $user = sb_db_get('SELECT id, first_name, last_name, profile_image, department FROM sb_users WHERE (user_type = "admin" OR user_type = "agent" OR user_type = "bot") AND id = ' . $agent_id);
    if (isset($user) && is_array($user)) {
        $user['details'] = sb_get_user_extra($agent_id);
        for ($i = 0; $i < count($user['details']); $i++) {
            if ($user['details'][$i]['slug'] == 'country') {
                $country = $user['details'][$i]['value'];
                $countries = json_decode(file_get_contents(SB_PATH . '/resources/json/countries.json'), true);
                $user['country_code'] = $countries[$country];
                if (isset($countries[$country]) && file_exists(SB_PATH . '/media/flags/countries/' . strtolower($countries[$country]) . '.png')) {
                    $user['flag'] = strtolower($countries[$country]) . '.png';
                }
                break;
            }
        }
        return $user;
    } else {
        return false;
    }
}

function sb_set_external_active_admin($external_user) {
    $active_user = sb_get_active_user();
    if (!sb_is_agent($active_user) || empty($active_user['url']) || $active_user['url'] != SB_URL || empty($external_user['email']) || $external_user['email'] != $active_user['email']) {
        $settings = false;
        $db_user = sb_db_get('SELECT * FROM sb_users WHERE email = "' . $external_user['email'] . '" LIMIT 1');
        if (!empty($db_user) && isset($db_user['password']) && $external_user['password'] == $db_user['password']) {
            $settings = ['id' => $db_user['id'], 'profile_image' => $db_user['profile_image'], 'first_name' => $db_user['first_name'], 'last_name' => $db_user['last_name'], 'email' => $db_user['email'], 'user_type' => $db_user['user_type'], 'token' => $db_user['token']];
        } else if (empty($db_user)) {
            $settings = ['profile_image' => sb_isset($external_user, 'profile_image', ''), 'first_name' => $external_user['first_name'], 'last_name' => $external_user['last_name'], 'password' => $external_user['password'], 'email' => $external_user['email'], 'user_type' => 'admin'];
            if (!sb_is_agent($active_user)) {
                global $SB_LOGIN;
                $SB_LOGIN = $settings;
            }
            $settings['id'] = sb_add_user($settings, [], false);
        } else {
            sb_logout();
            return 'logout';
        }
        if ($settings) {
            unset($settings['password']);
            global $SB_LOGIN;
            $settings['url'] = SB_URL;
            if (!headers_sent()) {
                sb_set_cookie_login($settings);
                $SB_LOGIN = $settings;
            }
            return true;
        }
        return false;
    }
    return true;
}

function sb_get_user_name($user = false) {
    $user = $user === false ? sb_get_active_user() : $user;
    $name = trim(sb_isset($user, 'first_name', '') . ' ' . sb_isset($user, 'last_name', ''));
    return substr(sb_isset($user, 'last_name', '-'), 0, 1) != '#' ? $name : sb_get_setting('visitor-default-name', $name);
}

function sb_csv_users() {
    $custom_fields = sb_get_setting('user-additional-fields');
    $header = ['Birthdate', 'City', 'Company', 'Country', 'Facebook', 'Language', 'LinkedIn', 'Phone', 'Twitter', 'Website'];
    $users = sb_db_get('SELECT id, first_name, last_name, email, profile_image, user_type, creation_time FROM sb_users WHERE user_type <> "bot" ORDER BY first_name', false);
    if (isset($custom_fields) && is_array($custom_fields)) {
        for ($i = 0; $i < count($custom_fields); $i++) {
            array_push($header, $custom_fields[$i]['extra-field-name']);
        }
    }
    for ($i = 0; $i < count($users); $i++) {
        $user = $users[$i];
        if ($user['user_type'] != 'visitor' && $user['user_type'] != 'lead') {
            $user_extra = sb_db_get('SELECT * FROM sb_users_data WHERE user_id = ' . $user['id'], false);
            for ($j = 0; $j < count($header); $j++) {
                $key = $header[$j];
                $user[$key] = '';
                for ($y = 0; $y < count($user_extra); $y++) {
                    if ($user_extra[$y]['name'] == $key) {
                        $user[$key] = $user_extra[$y]['value'];
                        break;
                    }
                }
            }
        } else {
            for ($j = 0; $j < count($header); $j++) {
                $user[$header[$j]] = '';
            }
        }
        $users[$i] = $user;
    }
    return sb_csv($users, array_merge(['ID', 'First Name', 'Last Name', 'Email', 'Profile Image', 'Type', 'Creation Time'], $header), 'users');
}

function sb_user_autodata($user_id) {
    if (!defined('SB_API') && empty($GLOBALS['SB_FORCE_ADMIN'])) {
        $settings = [];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        // IP and related data
        $ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) && substr_count($_SERVER['HTTP_CF_CONNECTING_IP'], '.') == 3 ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
        if (strlen($ip) > 6) {
            $settings['ip'] = [$ip, 'IP'];
            $ip_data = json_decode(sb_download('http://ip-api.com/json/' . $ip . '?fields=status,country,countryCode,city,timezone,currency'), true);
            if (isset($ip_data['status']) && $ip_data['status'] == 'success') {
                if (isset($ip_data['city']) && isset($ip_data['country'])) {
                    $settings['location'] = [$ip_data['city'] . ', ' . $ip_data['country'], 'Location'];
                }
                if (isset($ip_data['timezone'])) {
                    $settings['timezone'] = [$ip_data['timezone'], 'Timezone'];
                }
                if (isset($ip_data['currency'])) {
                    $settings['currency'] = [$ip_data['currency'], 'Currency'];
                }
                if (isset($ip_data['countryCode'])) {
                    $settings['country_code'] = [$ip_data['countryCode'], 'Country Code'];
                }
            }
        }

        // Browser
        $browser = '';
        $agent = strtolower($user_agent);
        if (strpos($agent, 'safari/') and strpos($agent, 'opr/')) {
            $browser = 'Opera';
        } else if (strpos($agent, 'safari/') and strpos($agent, 'chrome/') and strpos($agent, 'edge/') == false) {
            $browser = 'Chrome';
        } else if (strpos($agent, 'msie')) {
            $browser = 'Internet Explorer';
        } else if (strpos($agent, 'firefox/')) {
            $browser = 'Firefox';
        } else if (strpos($agent, 'edge/')) {
            $browser = 'Microsoft Edge';
        } else if (strpos($agent, 'safari/') and strpos($agent, 'opr/') == false and strpos($agent, 'chrome/') == false) {
            $browser = 'Safari';
        };
        if ($browser != '') {
            $settings['browser'] = [$browser, 'Browser'];
        }

        // Browser language
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $settings['language'] = [strtoupper(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)), 'Language'];
        }

        // OS
        $os = '';
        $os_array = ['/windows nt 10/i' =>  'Windows 10', '/windows nt 6.3/i' => 'Windows 8.1', '/windows nt 6.2/i' => 'Windows 8',  '/windows nt 6.1/i' => 'Windows 7',  '/windows nt 6.0/i' => 'Windows Vista', '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',  '/windows xp/i' => 'Windows XP', '/windows nt 5.0/i' => 'Windows 2000', '/windows me/i' => 'Windows ME', '/macintosh|mac os x/i' => 'Mac OS X', '/mac_powerpc/i' => 'Mac OS 9', '/linux/i' => 'Linux', '/ubuntu/i' => 'Ubuntu', '/iphone/i' => 'iPhone', '/ipod/i' => 'iPod', '/ipad/i' => 'iPad', '/android/i' => 'Android', '/blackberry/i' => 'BlackBerry', '/webos/i' => 'Mobile' ];
        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os = $value;
            }
        }
        if ($os != '') {
            $settings['os'] = [$os, 'OS'];
        }

        // Current url
        if (isset($_POST['current_url'])) {
            $settings['current_url'] = [$_POST['current_url'], 'Current URL'];
        } else if (isset($_SERVER['HTTP_REFERER'])) {
            $settings['current_url'] = [$_SERVER['HTTP_REFERER'], 'Current URL'];
        }

        // Save the data
        return sb_add_new_user_extra($user_id, $settings);
    }
    return false;
}

function sb_current_url($user_id = false, $url = false) {
    if (!empty($user_id)) {
        if ($url === false) {
            $url = sb_db_get('SELECT value FROM sb_users_data WHERE user_id ="' . $user_id . '" and slug = "current_url" LIMIT 1');
            return isset($url['value']) ? $url['value'] : false;
        }
        return sb_update_user_value($user_id, 'current_url', $url, 'Current URL');
    }
    return false;
}

function sb_update_bot($name = '', $profile_image = '') {
    $bot = sb_db_get('SELECT id, profile_image, first_name, last_name FROM sb_users WHERE user_type = "bot" LIMIT 1');
    if ($name == '') {
        $name = 'Bot';
    }
    if ($profile_image == '') {
        $profile_image = SB_URL . '/media/user.svg';
    }
    $settings = ['profile_image' => [$profile_image], 'first_name' => [$name], 'user_type' => ['bot']];
    if ($bot == '') {
        return sb_add_user($settings);
    } else if ($bot['profile_image'] != $profile_image || $bot['first_name'] != $name){
        return sb_update_user($bot['id'], $settings);
    }
    return false;
}

function sb_get_bot_id() {
    $bot_id = sb_isset(sb_db_get('SELECT id FROM sb_users WHERE user_type = "bot" LIMIT 1'), 'id');
    if ($bot_id == false) {
        $bot_id = sb_update_bot();
    }
    return $bot_id;
}

function get_user_from_conversation($conversation_id, $agent = false) {
    if ($agent) return sb_db_get('SELECT A.id, A.email FROM sb_users A, sb_messages B WHERE A.id = B.user_id AND (A.user_type = "admin" OR A.user_type = "agent") AND B.conversation_id = ' . $conversation_id . ' GROUP BY A.id');
    return sb_db_get('SELECT A.id, A.email FROM sb_users A, sb_conversations B WHERE A.id = B.user_id AND B.id = ' . $conversation_id);
}

function sb_get_agents_ids($admins = true) {
    $agents_ids = sb_db_get('SELECT id FROM sb_users WHERE user_type = "agent"' . ($admins ? ' OR user_type = "admin"' : ''), false);
    for ($i = 0; $i < count($agents_ids); $i++) {
        $agents_ids[$i] = intval($agents_ids[$i]['id']);
    }
    return $agents_ids;
}

function sb_get_avatar($first_name, $last_name = '') {
    return !empty($first_name) && substr($last_name, 0, 1) != '#' ? sb_download_file('https://ui-avatars.com/api/?background=random&size=512&font-size=0.35&name=' . $first_name . '+' . $last_name, rand(99, 9999999) . '.png') : SB_URL . '/media/user.svg';
}

function sb_get_users_with_details($details, $user_ids = false) {
    $response = [];
    $primary_details = ['last_name', 'email', 'profile_image', 'department'];
    if ($user_ids == 'all') $user_ids = false;
    if ($user_ids == 'agents') $user_ids = sb_get_agents_ids();
    if ($user_ids) {
        $user_ids = '(' . (is_string($user_ids) ? str_replace(' ', '', $user_ids) : substr(json_encode($user_ids), 1, -1)) . ')';
    }
    for ($i = 0; $i < count($details); $i++) {
        $detail = $details[$i];
        $primary = in_array($detail, $primary_details);
        if ($primary) {
            $query = 'SELECT id, ' . $detail . ' AS `value` FROM sb_users WHERE ' . $detail . ' IS NOT NULL AND ' . $detail . ' != ""' . ($user_ids ? ' AND id IN '. $user_ids : '');
        } else {
            $query = 'SELECT user_id AS `id`, value FROM sb_users_data WHERE slug = "' . $detail . '"' . ($user_ids ? ' AND user_id IN '. $user_ids : '');
        }
        $response[$detail] = sb_db_get($query, false);
    }
    return $response;
}

function sb_get_active_user_ID() {
    $active_user = sb_get_active_user();
    return $active_user ? sb_isset($active_user, 'id') : false;
}

/*
 * -----------------------------------------------------------
 * # ONLINE STATUS
 * -----------------------------------------------------------
 *
 * 1. Update the user last activity date
 * 2. Check if a date is considered online
 * 3. Check if at least one agent or admin is online
 * 4. Return the online users
 * 5. Return an array with the IDs of the online users
 * 6. Check if a user is online
 *
 */

function sb_update_users_last_activity($user_id = -1, $return_user_id = -1, $check_slack = false) {
    $result = $user_id != -1 ? sb_update_user_value($user_id, 'last_activity', gmdate('Y-m-d H:i:s')) : false;
    if ($return_user_id != -1) {
        $last_activity = sb_db_get('SELECT last_activity FROM sb_users WHERE id = "' . $return_user_id . '" LIMIT 1');
        if (!isset($last_activity['last_activity'])) {
            return 'offline';
        }
        if (sb_is_online($last_activity['last_activity'])) {
            return 'online';
        } else {
            return defined('SB_SLACK') && $check_slack ? sb_slack_presence($return_user_id) : 'offline';
        }
    }
    return $result;
}

function sb_is_online($datetime) {
    return strtotime($datetime) > strtotime(gmdate('Y-m-d H:i:s', time() - 15));
}

function sb_agents_online() {
    $online = $online = sb_pusher_active() ? sb_pusher_agents_online() : intval(sb_db_get('SELECT COUNT(*) as count FROM sb_users WHERE (user_type = "agent" OR user_type = "admin") AND last_activity > "' . gmdate('Y-m-d H:i:s', time() - 15) . '"')['count']) > 0;
    return $online ? true : (defined('SB_SLACK') ? sb_slack_presence() == 'online' : false);
}

function sb_get_online_users($exclude_id = -1, $sorting = 'creation_time', $agents = false) {
    $users = false;
    $query_agents = $agents ? ' AND (user_type = "agent" OR user_type = "admin")' : '';
    if (sb_pusher_active()) {
        $ids = '';
        $users = sb_pusher_get_online_users();
        for ($i = 0; $i < count($users); $i++) {
            $ids .= $users[$i]->id . ',';
        }
        if ($exclude_id != -1) {
            $ids = str_replace($exclude_id . ',', '', $ids);
        }
        $users = $ids == '' ? [] : sb_db_get(SELECT_FROM_USERS . ' FROM sb_users WHERE id IN (' . substr($ids, 0, -1) . ')' . $query_agents . ' ORDER BY ' . $sorting . ' DESC', false);
    } else {
        $users = sb_db_get(SELECT_FROM_USERS . ' FROM sb_users WHERE user_type <> "bot"' . $query_agents . ' AND last_activity > "' . gmdate('Y-m-d H:i:s', time() - 15) . '"' . ($exclude_id > 0 ? ' AND id <> "' . $exclude_id . '"' : '') . ' ORDER BY ' . $sorting . ' DESC', false);
    }
    if (isset($users) && is_array($users)) {
        return $users;
    } else {
        return new SBError('db-error', 'sb_get_online_users', $users);
    }
}

function sb_get_online_users_ids($agents = false) {
    $online_users = sb_get_online_users(-1, 'creation_time', $agents);
    $online_users_id = [];
    for ($i = 0; $i < count($online_users); $i++) {
        array_push($online_users_id, $online_users[$i]['id']);
    }
    return $online_users_id;
}

function sb_is_user_online($user_id) {
    $response = false;
    $user = sb_db_get('SELECT last_activity, user_type FROM sb_users WHERE id = "' . $user_id . '" LIMIT 1');
    if (isset($user['last_activity']) && sb_is_online($user['last_activity'])) {
        $response = true;
    }
    if (!$response && defined('SB_SLACK') && isset($user['user_type']) && sb_is_agent($user['user_type'])) {
        $response = sb_slack_presence($user_id) == 'online';
    }
    if (!$response && sb_pusher_active()) {
        $users = sb_pusher_get_online_users();
        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i]->id == $user_id) return true;
        }
    }
    return $response;
}

function sb_get_user_by($by, $value) {
    $query = SELECT_FROM_USERS . ' FROM sb_users A WHERE ';
    if (empty($value)) return false;
    switch ($by) {
    	case 'email':
            return sb_db_get($query . 'email = "' . $value . '" LIMIT 1');
        case 'first_name':
            return sb_db_get($query . 'first_name = "' . $value . '" LIMIT 1');
        case 'last_name':
            return sb_db_get($query . 'last_name = "' . $value . '" LIMIT 1');
        case 'phone':
            return sb_db_get($query . 'id IN (SELECT user_id FROM sb_users_data  WHERE slug = "phone" AND (value = "' . $value . '"' . (strpos($value, '+') !== false ? (' OR value = "' . str_replace('+', '00', $value) . '"') : '') . ')) LIMIT 1');
    }
    return false;
}

/*
 * -----------------------------------------------------------
 * # CONVERSATIONS
 * -----------------------------------------------------------
 *
 * 1. Return the user details of each conversation. This function is used internally by other functions.
 * 2. Return the messages grouped by conversation
 * 3. Return only the conversations or messages older than the given date
 * 4. Return only the messages older than the given date of the conversation with the given ID
 * 5. Return only the conversations older than the given date of the user with the given ID
 * 6. Return the messages of the conversation with the given ID
 * 7. Search conversations by searching user details and messages contents
 * 8. Search conversations of the user with the given ID
 * 9. Create a new user covnersation and return the ID
 * 10. Return all the conversations of a user
 * 11. Return the ID of the last user conversation if any, otherwise create a new conversation and return its ID
 * 12. Update a conversation status with one of the allowed stutus:  live = 0, pending = 1, pending user = 2, archive = 3, trash = 4.
 * 13. Update the conversation department and alert the agents of that department
 * 14. Update the agent assigned to a conversation and alert the agent
 * 15. Save a conversation as a CSV file
 * 16. Internal notes
 * 17. Direct message
 * 18. Return an array with all agents who replied to a conversation
 * 19. Verify if the active user is an agent or if the given conversation is owned by the active user
 * 20. Set or update the conversation opened by the agent in the admin area
 * 21. Check if a conversation is currently open by an agent
 * 22. Count conversations
 * 23. Send all notifications types to all validated agents
 * 24. Check if the given conversation are assigned to a department or agent
 * 25. Return the ID of the last agent of a conversation
 *
 */

function sb_get_conversations_users($conversations) {
    if (count($conversations) > 0) {
        $code = '(';
        for ($i = 0; $i < count($conversations); $i++) {
            $code .= $conversations[$i]['conversation_id'] . ',';
        }
        $code = substr($code, 0, -1) . ')';
        $result = sb_db_get('SELECT sb_users.id, sb_users.first_name, sb_users.last_name, sb_users.profile_image, sb_users.user_type, sb_conversations.id AS `conversation_id`, sb_conversations.status_code, sb_conversations.title, sb_conversations.source FROM sb_users, sb_conversations WHERE sb_users.id = sb_conversations.user_id AND sb_conversations.id IN ' . $code, false);
        for ($i = 0; $i < count($conversations); $i++) {
            $conversation_id = $conversations[$i]['conversation_id'];
            for ($j = 0; $j < count($result); $j++) {
                if ($conversation_id == $result[$j]['conversation_id']) {
                    $conversations[$i]['first_name'] = $result[$j]['first_name'];
                    $conversations[$i]['last_name'] = $result[$j]['last_name'];
                    $conversations[$i]['profile_image'] = $result[$j]['profile_image'];
                    $conversations[$i]['user_id'] = $result[$j]['id'];
                    $conversations[$i]['conversation_status_code'] = $result[$j]['status_code'];
                    $conversations[$i]['conversation_source'] = $result[$j]['source'];
                    $conversations[$i]['user_type'] = $result[$j]['user_type'];
                    break;
                }
            }
        }
    }
    return $conversations;
}

function sb_get_conversations($pagination = 0, $status_code = 0, $routing = false, $routing_unassigned = false) {
    $status_code = $status_code == 0 ? 'AND sb_conversations.status_code <> 3 AND sb_conversations.status_code <> 4' : 'AND sb_conversations.status_code = ' . $status_code;
    $result = sb_db_get('SELECT sb_messages.*, sb_users.user_type as message_user_type FROM sb_messages, sb_users, sb_conversations WHERE sb_users.id = sb_messages.user_id ' . $status_code . ' AND sb_conversations.id = sb_messages.conversation_id' . (sb_get_agent_department() !== false ? ' AND sb_conversations.department = ' . sb_get_agent_department() : '') . sb_routing_db($routing, $routing_unassigned) . ' AND sb_messages.creation_time IN (SELECT max(creation_time) latest_creation_time FROM sb_messages WHERE message != "" OR attachments != "" GROUP BY conversation_id) GROUP BY conversation_id ORDER BY sb_conversations.status_code DESC, sb_messages.creation_time DESC LIMIT ' . ($pagination * 100) . ',100', false);
    if (isset($result) && is_array($result)) {
        return sb_get_conversations_users($result);
    } else {
        return new SBError('db-error', 'versations', $result);
    }
}

function sb_get_new_conversations($datetime, $routing = false, $routing_unassigned = false) {
    $result = sb_db_get('SELECT m.*, u.user_type as message_user_type FROM sb_messages m, sb_users u, sb_conversations c WHERE m.creation_time IN (SELECT max(creation_time) latest_creation_time FROM sb_messages WHERE creation_time > "' . $datetime . '" GROUP BY conversation_id) AND u.id = m.user_id AND c.id = m.conversation_id' . (sb_get_agent_department() !== false ? ' AND c.department = ' . sb_get_agent_department() : '') . sb_routing_db($routing, $routing_unassigned, 'c') . ' GROUP BY conversation_id ORDER BY m.creation_time DESC', false);
    if (isset($result) && is_array($result)) {
        if (count($result) > 0) {
            return sb_get_conversations_users($result);
        } else {
            return [];
        }
    } else {
        return new SBError('db-error', 'sb_get_new_conversations', $result);
    }
}

function sb_get_new_messages($user_id, $conversation_id, $datetime) {
    $result = sb_db_get('SELECT sb_messages.*, sb_users.first_name, sb_users.last_name, sb_users.profile_image, sb_users.user_type FROM sb_messages, sb_users, sb_conversations WHERE sb_messages.creation_time > "' . $datetime . '" AND sb_messages.conversation_id = "' . $conversation_id . '" AND sb_users.id = sb_messages.user_id AND sb_conversations.user_id = "' . $user_id . '" AND sb_messages.conversation_id = sb_conversations.id ORDER BY sb_messages.creation_time ASC', false);
    if (isset($result) && is_array($result)) {
        return $result;
    } else {
        return new SBError('db-error', 'sb_get_new_messages', $result);
    }
}

function sb_get_new_user_conversations($user_id, $datetime) {
    return sb_db_get('SELECT sb_messages.*, sb_users.first_name, sb_users.last_name, sb_users.profile_image, sb_users.user_type, sb_conversations.status_code AS conversation_status_code, sb_conversations.department, sb_conversations.title FROM sb_messages, sb_users, sb_conversations WHERE sb_users.id = sb_messages.user_id AND sb_messages.conversation_id = sb_conversations.id AND sb_messages.creation_time IN (SELECT max(sb_messages.creation_time) AS latest_creation_time FROM sb_messages, sb_conversations WHERE sb_messages.creation_time > "' . $datetime . '" AND sb_messages.conversation_id = sb_conversations.id AND sb_conversations.user_id = "' . $user_id . '" GROUP BY conversation_id) AND sb_messages.conversation_id IN (SELECT id FROM sb_conversations WHERE user_id = ' . $user_id . ') GROUP BY conversation_id ORDER BY creation_time DESC', false);
}

function sb_get_conversation($user_id = false, $conversation_id) {
    $messages = sb_db_get('SELECT sb_messages.*, sb_users.first_name, sb_users.last_name, sb_users.profile_image, sb_users.user_type FROM sb_messages, sb_users, sb_conversations WHERE sb_messages.conversation_id = "' . $conversation_id . '"' . (sb_is_agent() ? '' : ' AND sb_conversations.user_id = "' . $user_id . '"') . ' AND sb_messages.conversation_id = sb_conversations.id AND sb_users.id = sb_messages.user_id ORDER BY sb_messages.creation_time ASC', false);
    if (isset($messages) && is_array($messages)) {
        $details = sb_db_get('SELECT sb_users.id as user_id, sb_users.first_name, sb_users.last_name, sb_users.profile_image, sb_users.user_type, sb_conversations.id, sb_conversations.title, sb_conversations.creation_time, sb_conversations.status_code as conversation_status_code, sb_conversations.department, sb_conversations.agent_id, sb_conversations.source, sb_conversations.extra FROM sb_users, sb_conversations WHERE sb_conversations.id = "' . $conversation_id . '"' . (sb_is_agent() ? '' : ' AND sb_users.id = "' . $user_id . '"') . ' AND sb_users.id = sb_conversations.user_id LIMIT 1');
        $details['busy'] = false;
        if (sb_is_agent()) {
            if (!sb_get_multi_setting('queue', 'queue-active') && !sb_get_setting('routing') && (!sb_get_multi_setting('agent-hide-conversations', 'agent-hide-conversations-active') || sb_get_multi_setting('agent-hide-conversations', 'agent-hide-conversations-view'))) {
                $details['busy'] = sb_is_active_conversation_busy($conversation_id, sb_get_active_user_ID());
                if (!$details['busy']) sb_set_agent_active_conversation($conversation_id);
            }
            if (!sb_get_setting('disable-notes')) $details['notes'] = sb_get_notes($conversation_id);
        }
        return ['messages' => $messages, 'details' => $details];
    } else {
        return new SBError('db-error', 'sb_get_conversation', $messages);
    }
}

function sb_search_conversations($search, $routing) {
    $search = sb_db_escape(mb_strtolower($search));
    $department = sb_get_agent_department();
    $result = sb_db_get('SELECT sb_messages.*, sb_users.user_type as message_user_type FROM sb_messages, sb_users' . ($department !== false || $routing ? ', sb_conversations' : '') . ' WHERE sb_users.id = sb_messages.user_id' . ($department !== false ? ' AND sb_conversations.id = sb_messages.conversation_id AND sb_conversations.department = ' . $department : '') . sb_routing_db($routing) . ($routing && $department === false ? ' AND sb_conversations.id = sb_messages.conversation_id' : '') .' AND (LOWER(sb_messages.message) LIKE "%' . $search . '%" OR LOWER(sb_messages.attachments) LIKE "%' . $search . '%" OR LOWER(sb_users.first_name) LIKE "%' . $search . '%" OR LOWER(sb_users.last_name) LIKE "%' . $search . '%" OR LOWER(sb_users.email) LIKE "%' . $search . '%") GROUP BY sb_messages.conversation_id ORDER BY sb_messages.creation_time DESC', false);
    if (isset($result) && is_array($result)) {
        return sb_get_conversations_users($result);
    } else {
        return new SBError('db-error', 'sb_search_conversations', $result);
    }
}

function sb_search_user_conversations($search, $user_id = false) {
    $search = sb_db_escape(mb_strtolower($search));
    return sb_db_get('SELECT sb_messages.*, sb_users.first_name, sb_users.last_name, sb_users.profile_image, sb_users.user_type, sb_conversations.status_code AS conversation_status_code, sb_conversations.title FROM sb_messages, sb_users, sb_conversations WHERE sb_messages.conversation_id = sb_conversations.id AND sb_users.id = sb_conversations.user_id AND sb_users.id = ' . ($user_id === false ? sb_get_active_user_ID() : $user_id) . ' AND (LOWER(sb_messages.message) LIKE "%' . $search . '%" OR LOWER(sb_messages.attachments) LIKE "%' . $search . '%" OR LOWER(sb_conversations.title) LIKE "%' . $search . '%") GROUP BY sb_messages.conversation_id ORDER BY sb_messages.creation_time DESC', false);
}

function sb_new_conversation($user_id, $status_code = 0, $title = '', $department = -1, $agent_id = -1, $routing = false, $source = false, $extra = false) {
    if (!sb_isset_num($agent_id) && $routing) {
        $agent_id = sb_routing(-1, $department);
    } else if (sb_isset_num($agent_id)) {
        if (defined('SB_AECOMMERCE')) {
            $agent_id = sb_aecommerce_get_agent_id($agent_id);
        }
    }
    $conversation_id = sb_db_query('INSERT INTO sb_conversations(user_id, title, status_code, creation_time, department, agent_id, source, extra) VALUES (' . $user_id . ', "' . sb_db_escape(sb_sanatize(ucfirst($title))) . '", "' . ($status_code == -1 || $status_code === false || $status_code === '' ? 2 : $status_code) . '", "' . gmdate('Y-m-d H:i:s') . '", ' . (sb_isset_num($department) ? $department : 'NULL') . ', ' . (sb_isset_num($agent_id) ? $agent_id : 'NULL') . ', ' . ($source ? '"' . sb_sanatize($source) . '"' : 'NULL') . ', ' . ($extra ? '"' . sb_db_escape(sb_sanatize($extra)) . '"' : 'NULL') . ')', true);
    if (is_int($conversation_id)) {
        if (sb_pusher_active()) sb_pusher_trigger('private-user-' . $user_id, 'new-conversation', ['conversation_user_id' => $user_id, 'conversation_id' => $conversation_id]);
        return sb_get_conversation($user_id, $conversation_id);
    } else if (sb_is_error($conversation_id) && sb_db_get('SELECT count(*) as count FROM sb_users WHERE id = "' . $user_id . '"')['count'] == 0) {
        return new SBValidationError('user-not-found');
    }
    return $conversation_id;
}

function sb_get_user_conversations($user_id, $exclude_id = -1) {
    $exclude = $exclude_id != -1 ? ' AND sb_messages.conversation_id <> ' . $exclude_id : '';
    return sb_db_get('SELECT sb_messages.*, sb_users.first_name, sb_users.last_name, sb_users.profile_image, sb_users.user_type, sb_conversations.status_code AS conversation_status_code, sb_conversations.department, sb_conversations.title FROM sb_messages, sb_users, sb_conversations WHERE sb_users.id = sb_messages.user_id' . (sb_get_agent_department() !== false ? ' AND sb_conversations.department = ' . sb_get_agent_department() : '') . ' AND sb_messages.conversation_id = sb_conversations.id AND sb_messages.creation_time IN (SELECT max(sb_messages.creation_time) AS latest_creation_time FROM sb_messages, sb_conversations WHERE sb_messages.conversation_id = sb_conversations.id AND sb_conversations.user_id = ' . $user_id . $exclude . ' GROUP BY conversation_id) AND sb_messages.conversation_id IN (SELECT id FROM sb_conversations WHERE user_id = ' . $user_id . ') GROUP BY conversation_id ORDER BY creation_time DESC', false);
}

function sb_get_last_conversation_id_or_create($user_id, $conversation_status_code = 1) {
    $conversation_id = sb_isset(sb_db_get('SELECT id FROM sb_conversations WHERE user_id = ' . $user_id . ' ORDER BY creation_time DESC LIMIT 1'), 'id');
    if ($conversation_id) {
        return $conversation_id;
    } else {
        return sb_isset(sb_isset(sb_new_conversation($user_id, $conversation_status_code), 'details'), 'id');
    }
}

function sb_update_conversation_status($conversation_id, $status) {
    $response = false;
    if (in_array($status, [0, 1, 2, 3, 4])) {
        $response = sb_db_query('UPDATE sb_conversations SET status_code = ' . $status . ' WHERE id = '. $conversation_id);
    } else {
        if ($status == 5 && sb_is_agent()) {
            $response = sb_db_query('DELETE FROM sb_conversations WHERE status_code = 4');
        } else {
            $response = new SBValidationError('invalid-status-code');
        }
    }
    if (in_array($status, [3, 4]) && sb_is_agent()) sb_update_conversation_event('conversation-status-update-' . $status, $conversation_id);
    return $response;
}

function sb_update_conversation_department($conversation_id, $department, $message = false) {
    if (sb_conversation_security_error($conversation_id)) return new SBError('security-error', 'sb_update_conversation_department');
    $empty_department = empty($department) || $department == -1;
    $response = sb_db_query('UPDATE sb_conversations SET department = ' . ($empty_department ? 'NULL' : sb_sanatize($department)) . ' WHERE id = '. $conversation_id);
    if ($response) {
        if ($message) {
            $recipients = $empty_department ? 'agents' : ('department-' . $department);
            sb_send_agents_notifications($recipients, $message, 'This message has been sent because {name} assigned this conversation to your department.', $conversation_id, false, false, $department, true);
        }
        sb_update_conversation_event('conversation-department-update-' . $department, $conversation_id);
        return true;
    }
    return new SBError('department-update-error', 'sb_update_conversation_department', $response);
}

function sb_update_conversation_agent($conversation_id, $agent_id, $message = false) {
    if (sb_conversation_security_error($conversation_id)) return new SBError('security-error', 'sb_update_conversation_agent');
    if ($agent_id == 'routing' || $agent_id == 'routing-unassigned') $agent_id = sb_routing(false, sb_isset('SELECT department FROM sb_conversations WHERE id = ' . $conversation_id, 'department'), $agent_id == 'routing-unassigned');
    if (!empty($agent_id) && !in_array(sb_isset(sb_db_get('SELECT user_type FROM sb_users WHERE id = ' . $agent_id), 'user_type'), ['agent', 'admin'])) {
        return new SBError('not-an-agent', 'sb_update_conversation_agent');
    }
    $response = sb_db_query('UPDATE sb_conversations SET agent_id = ' . (empty($agent_id) ? 'NULL' : sb_sanatize($agent_id)) . ' WHERE id = '. $conversation_id);
    if ($response) {
        if ($message) {
            sb_send_agents_notifications($agent_id, $message, 'This message has been sent because {name} assigned this conversation to you.', $conversation_id, false, false, false, true);
        }
        if (!empty($agent_id)) sb_update_conversation_event('conversation-agent-update-' . $agent_id, $conversation_id);
        return true;
    }
    return new SBError('agent-update-error', 'sb_update_conversation_agent', $response);
}

function sb_update_conversation_event($payload_event, $conversation_id) {
    sb_db_query('INSERT INTO sb_messages(user_id, message, creation_time, status_code, attachments, payload, conversation_id) VALUES (' . sb_get_active_user_ID() . ', "", "' . gmdate('Y-m-d H:i:s') . '", 0, "", "{\"event\": \"' . $payload_event . '\"}", ' . $conversation_id . ')');
    if (sb_pusher_active()) sb_pusher_trigger('global', 'update-conversations');
}

// Deprecated from V 3.2.4
function sb_csv_conversations($conversation_id) {
    sb_transcript($conversation_id, 'csv');
}

function sb_transcript($conversation_id, $type = false) {
    $conversation = sb_db_get('SELECT id, user_id, message, creation_time, attachments, payload FROM sb_messages WHERE conversation_id = ' . $conversation_id, false);
    if (sb_conversation_security_error($conversation_id)) return new SBError('security-error', 'sb_transcript');
    if ($type === false) $type = sb_get_setting('transcript-type', 'txt');
    if ($type == 'csv') {
        return sb_csv($conversation, ['ID', 'User ID', 'Message', 'Creation date', 'Attachments', 'Payload'], 'conversation-' . $conversation_id);
    }
    if ($type == 'txt') {
        $code = '';
        $agents_ids = sb_get_agents_ids();
        for ($i = 0; $i < count($conversation); $i++) {
            $message = $conversation[$i];
            $code .= sb_(in_array($message['user_id'], $agents_ids) ? 'Agent' : 'You') . ' ' . $message['creation_time'] . PHP_EOL . $message['message'] . PHP_EOL . PHP_EOL;
        }
        sb_file(sb_upload_path() . '/' . $conversation_id . '.txt', $code);
        return sb_upload_path(true) . '/' . $conversation_id . '.txt';
    }
    return false;
}

function sb_get_notes($conversation_id) {
    return sb_get_external_setting('notes-' . $conversation_id, []);
}

function sb_add_note($conversation_id, $user_id, $name, $message) {
    $notes = sb_get_notes($conversation_id);
    $id = rand(0, 99999);
    array_push($notes, ['id' => $id, 'user_id' => $user_id, 'name' => $name, 'message' => $message]);
    $response = sb_save_external_setting('notes-' . $conversation_id, $notes);
    return $response ? $id : $response;
}

function sb_delete_note($conversation_id, $note_id) {
    $notes = sb_get_notes($conversation_id);
    for ($i = 0; $i < count($notes); $i++) {
    	if ($notes[$i]['id'] == $note_id) {
            array_splice($notes, $i, 1);
            return count($notes) ? sb_save_external_setting('notes-' . $conversation_id, $notes) : sb_db_query('DELETE FROM sb_settings WHERE name = "notes-' . $conversation_id . '" LIMIT 1');
        }
    }
    return false;
}

function sb_direct_message($user_ids, $message, $subject = false) {
    if ($user_ids == 'all') {
        $user_ids = [];
        $items = sb_db_get('SELECT id FROM sb_users WHERE user_type <> "agent" AND user_type <> "admin" AND user_type <> "bot"', false);
        for ($i = 0; $i < count($items); $i++){
            array_push($user_ids, $items[$i]['id']);
        }
    }
    $user_ids = is_string($user_ids) ? explode(',', str_replace(' ', '', $user_ids)) : $user_ids;
    $user_ids_string = substr(json_encode($user_ids), 1, -1);
    $missing = sb_db_get('SELECT id FROM sb_users WHERE id NOT IN (' . $user_ids_string . ') AND id NOT IN (SELECT user_id FROM sb_conversations)', false);
    if (!empty($missing)) {
        $query = 'INSERT INTO sb_conversations(user_id, title, status_code, creation_time) VALUES ';
        for ($i = 0; $i < count($missing); $i++) {
            $query .= '(' . $missing[$i]['id']  . ', "", 1, NOW()),';
        }
        sb_db_query(substr($query, 0, -1));
    }
    $conversation_ids = sb_db_get('SELECT id FROM sb_conversations WHERE user_id IN (' . $user_ids_string . ') GROUP BY user_id', false);
    $message_db = sb_db_escape(sb_sanatize(sb_merge_fields($message)));
    $query = 'INSERT INTO sb_messages(user_id, message, creation_time, status_code, attachments, payload, conversation_id) VALUES ';
    $active_user = sb_get_active_user();
    $active_user_id = $active_user['id'];
    $now = gmdate('Y-m-d H:i:s');
    $count = count($conversation_ids);
    for ($i = 0; $i < $count; $i++) {
        $query .= '(' . $active_user_id  . ', "' . $message_db . '", "' . $now . '", 0, "", "", ' . $conversation_ids[$i]['id'] . '),';
    }
    $response = sb_db_query(substr($query, 0, -1));
    if (sb_is_error($response)) return new SBValidationError($response);

    // Pusher
    if (sb_pusher_active()) {
        $channels = [];
        for ($i = 0; $i < count($user_ids); $i++) {
            array_push($channels, 'private-user-' . $user_ids[$i]);
        }
        sb_pusher_trigger($channels, 'new-message');
        sb_pusher_trigger('global', 'update-conversations');
        sb_update_users_last_activity($active_user_id);
    }

    // Push notifications
    if (sb_get_multi_setting('push-notifications', 'push-notifications-users-active')) {
        sb_push_notification(sb_get_user_name(), $message,  $active_user['profile_image'], $user_ids);
    }

    sb_reports_update('direct-messages', mb_substr($message, 0, 18) . ' | ' . $count);
    return $response;
}

function sb_get_agents_in_conversation($conversation_id) {
    $rows = sb_db_get('SELECT A.id, first_name, last_name, profile_image, B.conversation_id FROM sb_users A, sb_messages B WHERE (A.user_type = "agent" OR A.user_type = "admin") AND A.id = B.user_id AND conversation_id ' . (is_array($conversation_id) ? ('IN (' . implode(',', $conversation_id) . ')') : ('= ' . $conversation_id)) . (sb_is_agent() ? '' : ' AND conversation_id in (SELECT id FROM sb_conversations WHERE user_id = ' .  sb_get_active_user_ID() . ')') . ' GROUP BY A.id, B.conversation_id', false);
    $response = [];
    for ($i = 0; $i < count($rows); $i++) {
        if (isset($response[$rows[$i]['conversation_id']])) array_push($response[$rows[$i]['conversation_id']], $rows[$i]);
        else $response[$rows[$i]['conversation_id']] = [$rows[$i]];
    }
    return $response;
}

function sb_conversation_security_error($conversation_id) {
    return !sb_is_agent() && sb_isset(sb_db_get('SELECT user_id FROM sb_conversations WHERE id = ' . $conversation_id), 'user_id') != sb_get_active_user_ID();
}

function sb_set_agent_active_conversation($conversation_id, $agent_id = false) {
    $active_agents_conversations = sb_get_external_setting('active_agents_conversations', []);
    $active_agents_conversations[$agent_id ? $agent_id : sb_get_active_user_ID()] = [$conversation_id, time()];
    sb_save_external_setting('active_agents_conversations', $active_agents_conversations);
}

function sb_is_active_conversation_busy($conversation_id, $skip = -1) {
    $items = sb_get_external_setting('active_agents_conversations', []);
    $now_less_three_hours = time() - 7200;
    if (empty($items)) return false;
    foreach ($items as $agent_id => $value) {
        if ($agent_id != $skip && $value[0] == $conversation_id && $now_less_three_hours < $value[1]) {
            return true;
        }
    }
    return false;
}

function sb_count_conversations($status_code = false) {
    return sb_isset(sb_db_get('SELECT COUNT(*) AS count FROM sb_conversations' . ($status_code ? ' WHERE status_code = ' . $status_code : '')), 'count');
}

function sb_send_agents_notifications($recipients, $message, $bottom_message = false, $conversation_id = false, $attachments = false, $sender = false, $department = false, $force = false) {
    $sender = $sender ? $sender : sb_get_active_user();
    $sender_name = sb_get_user_name($sender);
    $recipient_id = is_int($recipients) ? $recipients : sb_isset($recipients, 'id');
    if ($recipient_id && sb_is_user_online($recipient_id)) return 'agent-online';
    if (!$recipient_id) $recipients = $department ? ('department-' . $department) : 'agents';
    if ($force || sb_get_setting('notify-agent-email')) sb_email_create($recipients, $sender_name, $sender['profile_image'], $message . ($bottom_message ? ('<br><br><span style="color:#a8a8a8;font-size:12px;">' . sb_(str_replace('{name}', $sender_name, $bottom_message)) . '</span>') : ''), $attachments, $department, $conversation_id);
    if ($force || sb_pusher_active()) {
        sb_pusher_trigger('global', 'update-conversations');
        sb_push_notification($sender_name, $message, $sender['profile_image'], $recipient_id ? ('private-user-' . $recipient_id) : $recipients, $conversation_id);
    }
    if ($force || sb_get_multi_setting('sms', 'sms-active-agents')) {
        sb_send_sms($message, $recipient_id ? $recipient_id : $recipients, true, $conversation_id);
    }
    return true;
}

function sb_check_conversations_assignment($conversations_ids, $agent_id = false, $department = false) {
    if (empty($conversations_ids)) return [];
    $conversations_ids = sb_db_get('SELECT id FROM sb_conversations WHERE id IN (' . implode(',', $conversations_ids) . ') AND ' . ($agent_id ? ('agent_id <> ' . $agent_id) : '') . ($agent_id && $department ? ' AND ' : '') . ($department ? ('department <> ' . $department) : ''), false);
    for ($i = 0; $i < count($conversations_ids); $i++) {
        $conversations_ids[$i] = $conversations_ids[$i]['id'];
    }
    return $conversations_ids;
}

function sb_get_last_agent_in_conversation($conversation_id) {
    $agent = sb_db_get('SELECT B.id, B.first_name, B.last_name, B.email, B.user_type, B.token, B.department  FROM sb_messages A, sb_users B WHERE A.conversation_id = ' . $conversation_id . ' AND A.user_id = B.id AND (B.user_type = "agent" OR B.user_type = "admin") ORDER BY A.creation_time LIMIT 1');
    return isset($agent['id']) ? $agent : false;
}

/*
 * -----------------------------------------------------------
 * # QUEUE AND ROUTING
 * -----------------------------------------------------------
 *
 * Update the queue and return the current queue status
 *
 */

function sb_queue($conversation_id, $department = -1) {
    $position = 0;
    $queue_db = sb_get_external_setting('queue', []);
    $cuncurrent_chats = sb_get_setting('queue');
    $queue = [];
    $index = 0;
    $unix_now = time();
    $unix_min = strtotime('-1 minutes');
    if (!sb_isset_num($department)) $department = -1;
    for ($i = 0; $i < count($queue_db); $i++) {
        if ($unix_min < intval($queue_db[$i][1])) {
            if ($queue_db[$i][0] == $conversation_id) {
                array_push($queue, [$conversation_id, $unix_now, $department]);
                $position = $index + 1;
            } else {
                array_push($queue, $queue_db[$i]);
            }
            if ($department == -1 || $department == $queue_db[$i][2]){
                $index++;
            }
        }
    }
    if (count($queue) == 0 || $position == 1) {
        $counts = sb_db_get('SELECT COUNT(*) AS `count`, agent_id FROM sb_conversations WHERE (status_code = 0 OR status_code = 1 OR status_code = 2) AND agent_id IS NOT NULL' . ($department != -1 ? ' AND department = ' . $department : '' ) . ' GROUP BY agent_id', false);
        $cuncurrent_chats = sb_get_setting('queue');
        $cuncurrent_chats = $cuncurrent_chats == false || $cuncurrent_chats['queue-concurrent-chats'] == '' ? 5 : intval($cuncurrent_chats['queue-concurrent-chats']);
        $smaller = false;
        $pusher = sb_pusher_active();
        for ($i = 0; $i < count($counts); $i++) {
            $count = intval($counts[$i]['count']);
            if ($count < $cuncurrent_chats && ($smaller === false || $count < $smaller['count'])) {
                $smaller = $counts[$i];
            }
        }
        if ($smaller === false) {
            $query = '';
            for ($i = 0; $i < count($counts); $i++) {
                $query .= $counts[$i]['agent_id'] . ',';
            }
            if ($pusher) {
                $agents_ids = sb_get_agents_ids(false);
                $online_agents = sb_pusher_get_online_users();
                for ($i = 0; $i < count($online_agents); $i++) {
                    $online_agents[$i] = $online_agents[$i]->id;
                }
                for ($i = 0; $i < count($agents_ids); $i++) {
                    if (!in_array($agents_ids[$i], $online_agents)) {
                        $query .= $agents_ids[$i] . ',';
                    }
                }
            }
            $smaller = sb_db_get('SELECT id FROM sb_users WHERE user_type = "agent"' . ($query == '' ? '' : ' AND id NOT IN (' . substr($query, 0, -1) . ')') . ($pusher ? '' : ' AND last_activity > "' . gmdate('Y-m-d H:i:s', time() - 15) . '"') . ($department != -1 ? ' AND department = ' . $department : '' ) . ' LIMIT 1');
            if (empty($smaller)) {
                $smaller = false;
            } else {
                $smaller = ['agent_id' => $smaller['id']];
            }
        }
        if ($smaller !== false) {
            sb_routing_assign_conversation($smaller['agent_id'], $conversation_id);
            array_shift($queue);
            $position = 0;
        } else if ($position == 0) {
            array_push($queue, [$conversation_id, $unix_now, $department]);
            $position = $index + 1;
        }
    } else if ($position == 0) {
        array_push($queue, [$conversation_id, $unix_now, $department]);
        $position = $index + 1;
    }
    sb_save_external_setting('queue', $queue);
    return $position;
}

function sb_routing_db($routing, $routing_unassigned = false, $table_name = 'sb_conversations') {
    return $routing && sb_get_active_user()['user_type'] == 'agent' ? (' AND (' . $table_name. '.agent_id = ' . sb_get_active_user_ID() . ($routing_unassigned ? (' OR (' . $table_name. '.agent_id IS NULL OR ' . $table_name. '.agent_id = ""))') : ')')) : '';
}

function sb_routing_assign_conversation($agent_id, $conversation_id) {
    return sb_db_query('UPDATE sb_conversations SET agent_id = ' . (is_null($agent_id) ? 'NULL' : $agent_id) . ' WHERE id = ' . $conversation_id);
}

function sb_routing($conversation_id = false, $department = false, $unassigned = false) {
    $online_agents = sb_get_online_users_ids(true);
    $agents = count($online_agents) ? sb_db_get('SELECT id FROM sb_users WHERE user_type = "agent" AND id IN (' . implode(',', $online_agents) . ')' . (sb_isset_num($department) ? ' AND department = ' . $department : ''), false) : [];
    $count_last = 0;
    $index = 0;
    $count = count($agents);
    if ($count == 0) {
        if ($unassigned ) return $conversation_id ? sb_routing_assign_conversation(null, $conversation_id) : null;
        $agents = sb_db_get('SELECT id FROM sb_users WHERE user_type = "agent"' . (sb_isset_num($department) ? ' AND department = ' . $department : ''), false);
        $count = count($agents);
    }
    if ($count) {
        for ($i = 0; $i < $count; $i++) {
            $count_now = intval(sb_db_get('SELECT COUNT(*) AS `count` FROM sb_conversations WHERE (status_code = 0 OR status_code = 1 OR status_code = 2) AND agent_id = ' . $agents[$i]['id'])['count']);
            if ($count_last > $count_now) {
                $index = $i;
                break;
            }
            $count_last = $count_now;
        }
        return $conversation_id == -1 || !$conversation_id ? $agents[$index]['id'] : sb_routing_assign_conversation($agents[$index]['id'], $conversation_id);
    }
    return false;
}

/*
 * -----------------------------------------------------------
 * # MESSAGES
 * -----------------------------------------------------------
 *
 * 1. Add a message to a conversation
 * 2. Update an existin message
 * 3. Delete a message
 * 4. Send the default close message
 * 5. Convert the merge fields to the final values
 *
 */

function sb_send_message($sender_id = -1, $conversation_id, $message = '', $attachments = [], $conversation_status_code = -1, $payload = false, $queue = false, $recipient_id = false) {
    $pusher = sb_pusher_active();
    $user_id = $sender_id;
    if ($sender_id == -1) {
        $sender_id = sb_get_active_user_ID();
    }
    if ($sender_id != -1) {
        $attachments_json = '';
        $security = sb_is_agent();
        $attachments = sb_json_array($attachments);
        $sender = sb_get_user($sender_id);
        $is_sender_agent = sb_is_agent($sender);
        $push_notifications = defined('SB_API') && ((!$is_sender_agent && sb_get_multi_setting('push-notifications', 'push-notifications-active')) || ($is_sender_agent && sb_get_multi_setting('push-notifications', 'push-notifications-users-active')));
        if (count($attachments) > 0) {
            $attachments_json = '[';
            for ($i = 0; $i < count($attachments); $i++) {
            	$attachments_json .= '[\"' . sb_db_escape(sb_sanatize($attachments[$i][0])) . '\", \"' . sb_db_escape(sb_sanatize($attachments[$i][1])) . '\"],';
            }
            $attachments_json = substr($attachments_json, 0, -1) . ']';
        }
        if (!$security || $pusher || $push_notifications) {
            $user_id = sb_isset(sb_db_get('SELECT user_id FROM sb_conversations WHERE id = "' . $conversation_id . '" LIMIT 1'), 'user_id');
            if ($user_id == sb_get_active_user_ID()) {
                $security = true;
            }
        }
        if ($security || !empty($GLOBALS['SB_FORCE_ADMIN'])) {
            if ($recipient_id) {
                global $SB_LANGUAGE;
                $SB_LANGUAGE = [sb_get_user_language($recipient_id), 'front'];
            }
            if (!$pusher) sb_set_typing($sender_id, -1);
            if ($payload !== false) $payload = sb_json_array($payload);
            $message = sb_merge_fields(sb_sanatize($message));
            $response = sb_db_query('INSERT INTO sb_messages(user_id, message, creation_time, status_code, attachments, payload, conversation_id) VALUES ("' . $sender_id . '", "' . sb_db_escape($message) . '", "' . gmdate('Y-m-d H:i:s') . '", 0, "' . $attachments_json . '", "' . ($payload != false ? sb_db_json_enconde(sb_sanatize($payload)) : '') . '", "' . $conversation_id . '")', true);
            if ($queue && !sb_is_agent()) {
                $status = sb_db_get('SELECT status_code FROM sb_conversations WHERE id = "' . $conversation_id . '" LIMIT 1');
                if ($status['status_code'] == 3) {
                    sb_routing_assign_conversation(null, $conversation_id);
                    $queue = true;
                } else {
                    $queue = false;
                }
            }
            if ($conversation_status_code != 'skip') {
                if ($conversation_status_code == -1 || $conversation_status_code === false || !in_array($conversation_status_code, [0, 1, 2, 3, 4])) {
                    $cookie_unknow_notify = isset($_COOKIE['sb-dialogflow-unknow-notify']);
                    $conversation_status_code = $is_sender_agent && sb_isset($sender, 'user_type') != 'bot' && !$cookie_unknow_notify ? 1 : (defined('SB_DIALOGFLOW') && (sb_get_setting('bot-unknow-notify') || sb_get_multi_setting('dialogflow-human-takeover', 'dialogflow-human-takeover-active')) && !$cookie_unknow_notify && !isset($_COOKIE['sb-dialogflow-disabled']) ? 1 : 2);
                }
                sb_db_query('UPDATE sb_conversations SET status_code = ' . $conversation_status_code . ' WHERE id = "' . $conversation_id . '" LIMIT 1');
            }
            if (sb_is_error($response)) return $response;
            if ($pusher) {
                $payload = ['conversation_user_id' => $user_id, 'message_id' => $response, 'conversation_id' => $conversation_id];
                sb_pusher_trigger('private-user-' . $user_id, 'new-message', $payload);
                sb_pusher_trigger('global', 'update-conversations', $payload);
                sb_update_users_last_activity($sender_id);
            }
            if ($push_notifications && $sender && !(empty($message) && empty($attachments))) {
                sb_push_notification(sb_get_user_name($sender), $message, strpos($sender['profile_image'], 'user.svg') ? sb_get_setting('notifications-icon', SB_URL . '/media/icon.png') : $sender['profile_image'], $is_sender_agent ? $user_id : 'agents', $conversation_id);
            }
            return ['message-id' => $response, 'queue' => $queue];
        }
        return new SBError('security-error', 'sb_send_message');
    } else {
        return new SBError('active-user-not-found', 'sb_send_message');
    }
}

function sb_update_message($message_id, $message = false, $attachments = false, $payload = false) {
    return sb_update_or_delete_message('update', $message_id, $message, $attachments, $payload);
}

function sb_delete_message($message_id) {
    return sb_update_or_delete_message('delete', $message_id);
}

function sb_update_or_delete_message($action, $message_id, $message = false, $attachments = false, $payload = false) {
    $pusher = sb_pusher_active();
    $security = sb_is_agent() || !empty($GLOBALS['SB_FORCE_ADMIN']);
    $conversation = false;
    $user_id = false;
    $response = false;
    if (!$security || $pusher) {
        $conversation = sb_db_get('SELECT id, user_id FROM sb_conversations WHERE id = (SELECT conversation_id FROM sb_messages WHERE id = "' . $message_id . '" LIMIT 1) LIMIT 1');
        $user_id = sb_isset($conversation, 'user_id');
        if ($user_id == sb_get_active_user_ID()) {
            $security = true;
        }
    }
    if ($security) {
        if ($action == 'update') {
            if ($message === false && $payload === false && $attachments === false) return new SBValidationError('missing-arguments');
            if ($attachments !== false) $attachments = sb_sanatize(sb_json_array($attachments, false));
            if ($payload !== false) $payload = sb_sanatize(sb_json_array($payload, false));
            $response = sb_db_query('UPDATE sb_messages SET ' . ($message !== false ? 'message = "' . sb_db_escape(sb_sanatize($message)) . '",' : '') . ' creation_time = "' . gmdate('Y-m-d H:i:s') . '"' . ($payload !== false ? ', payload = "' . sb_db_json_enconde($payload) . '"' : '') . ($attachments !== false ? ', attachments = "' . sb_db_json_enconde($attachments) . '"' : '') . ' WHERE id = "' . $message_id . '" LIMIT 1');
        }
        if ($action == 'delete') {
            $response = sb_db_query('DELETE FROM sb_messages WHERE id = "' . $message_id . '" LIMIT 1');
        }
        if ($response && $pusher) {
            $payload = ['conversation_user_id' => $user_id, 'message_id' => $message_id, 'conversation_id' => $conversation['id']];
            sb_pusher_trigger('private-user-' . $user_id, 'new-message', $payload);
            sb_pusher_trigger('global', 'update-conversations', $payload);
        }
        return $response;
    }
    return new SBError('security-error', 'sb_' . $action . '_message');
}

function sb_close_message($bot_id, $conversation_id) {
    $message = sb_get_multi_setting('close-message', 'close-msg');
    if ($message != false) {
        return sb_send_message($bot_id, $conversation_id, $message, [], 3, ['type' => 'close-message']);
    }
    return false;
}

function sb_merge_fields($message, $marge_fields_values = []) {
    $replace = '';
    $marge_fields = ['user_name', 'user_email'];
    $marge_field = '';
    if (defined('SB_WOOCOMMERCE')) {
        $message = sb_woocommerce_merge_fields($message, $marge_fields_values);
    }
    for ($i = 0; $i < count($marge_fields); $i++) {
        if (strpos($message, '{' . $marge_fields[$i]) !== false) {
            $marge_field = '{' . $marge_fields[$i] . '}';
            $value = isset($marge_fields_values[$i]) ? $marge_fields_values[$i] : false;
            switch ($marge_fields[$i]) {
                case 'user_name':
                    $replace = sb_get_user_name($value);
                    break;
                case 'user_email':
                    $replace = $value ? $value : sb_isset(sb_get_active_user(), 'email', '');
                    break;
            }
        }
    }
    $message = str_replace($marge_field, $replace, $message);
    return $message;
}

/*
 * -----------------------------------------------------------
 * # RICH MESSAGES
 * -----------------------------------------------------------
 *
 * 1. Get the custom rich messages ids including the built in ones
 * 2. Get the rich message with the given name
 * 3. Escape a rich message shortcode value
 * 4. Return the full shortcode and its parameters
 *
 */

function sb_get_rich_messages_ids() {
    $result = sb_get_external_setting('rich-messages');
    $ids = ['email' , 'registration' , 'login', 'timetable', 'articles'];
    if (is_array($result) && isset($result['rich-messages']) && is_array($result['rich-messages'][0])) {
        for ($i = 0; $i < count($result['rich-messages'][0]); $i++) {
            array_push($ids, $result['rich-messages'][0][$i]['rich-message-name']);
        }
        return $ids;
    }
    if (defined('SB_WOOCOMMERCE')) {
        $ids = array_merge($ids, ['woocommerce-cart']);
    }
    return $ids;
}

function sb_get_rich_message($name) {
    if (in_array($name, ['registration', 'registration-tickets', 'login', 'login-tickets', 'timetable', 'articles', 'woocommerce-cart'])) {
        $title = '';
        $message = '';
        $success = '';
        switch ($name) {
            case 'registration-tickets':
            case 'registration':
                $registration_tickets = $name == 'registration-tickets';
                $active_user = sb_get_active_user();
                $last_name = sb_get_setting('registration-last-name');
                $user = $active_user != false && !sb_is_agent($active_user['user_type']) ? sb_get_user($active_user['id'], true) : ['profile_image' => '', 'first_name' => '', 'last_name' => '', 'email' => '', 'password' => '', 'user_type' => 'visitor', 'details' => []];
                $visitor = $user['user_type'] == 'visitor' || $user['user_type'] == 'lead';
                $settings = sb_get_setting('registration');
                $registration_fields = sb_get_setting('registration-fields');
                $title = sb_(sb_isset($settings, 'registration-title', 'Create new account'));
                $message = sb_(sb_isset($settings, 'registration-msg', 'Please create a new account before using our chat.'));
                $success = sb_(sb_isset($settings, 'registration-success', ''));
                $profile_image = sb_get_setting('registration-profile-img') ? '<div id="profile_image" data-type="image" class="sb-input sb-input-image sb-profile-image"><span>' . sb_('Profile image') . '</span><div' . ($user['profile_image'] != '' && strpos($user['profile_image'], 'media/user.svg') == false ? ' data-value="' . $user['profile_image'] . '" style="background-image:url(\'' . $user['profile_image'] . '\')"' : '') . ' class="image">' . ($user['profile_image'] != '' && strpos($user['profile_image'], 'media/user.svg') == false ? '<i class="sb-icon-close"></i>' : '') . '</div></div>' : '';
                $password = (!$registration_tickets && sb_get_setting('registration-password')) || sb_get_setting('wp-users-system') == 'wp' || ($registration_tickets && !sb_get_setting('tickets-registration-disable-password')) ? '<div id="password" data-type="text" class="sb-input sb-input-password"><span>' . sb_('Password') . '</span><input value="' . ($user['password'] != '' ? '********' : '') . '" autocomplete="false" type="password" required></div><div id="password-check" data-type="text" class="sb-input sb-input-password"><span>' . sb_('Repeat password') . '</span><input value="' . ($user['password'] != '' ? '********' : '') . '" autocomplete="false" type="password" required></div>' : '';
                $link = $settings['registration-terms-link'] != '' || $settings['registration-privacy-link'] != '' ? '<div class="sb-link-area">' . sb_('By clicking the button below, you agree to our') . ' <a target="_blank" href="' . sb_isset($settings, 'registration-terms-link', $settings['registration-privacy-link']) . '">' . sb_($settings['registration-terms-link'] != '' ? 'Terms of service' : 'Privacy Policy') . '</a>' . ($settings['registration-privacy-link'] != '' && $settings['registration-terms-link'] != '' ? ' ' . sb_('and') . ' <a target="_blank" href="' . $settings['registration-privacy-link'] . '">' . sb_('Privacy Policy') . '</a>' : '') . '.</div>' : '';
                $code = '<div class="sb-form-main sb-form">' . $profile_image . '<div id="first_name" data-type="text" class="sb-input sb-input-text"><span>' . sb_($last_name ? 'First name' : 'Name') . '</span><input value="' . ($visitor ? '' : $user['first_name']) . '" autocomplete="false" type="text" required></div>' . ($last_name ? '<div id="last_name" data-type="text" class="sb-input sb-input-text"><span>' . sb_('Last name') . '</span><input value="' . ($visitor ? '' : $user['last_name'])  . '" autocomplete="false" type="text" required></div>' : '') . '<div id="email" data-type="text" class="sb-input sb-input-text"><span>' . sb_('Email') . '</span><input value="' . $user['email'] . '" autocomplete="off" type="email" required></div>' . $password . '</div><div class="sb-form-extra sb-form">';

                $extra = [];
                if (isset($user['details'])) {
                    for ($i = 0; $i < count($user['details']); $i++) {
                        $extra[$user['details'][$i]['slug']] = $user['details'][$i]['value'];
                    }
                }

                foreach ($registration_fields as $key => $value) {
                    if ($value) {
                        $key = str_replace('reg-', '', $key);
                        $name = str_replace('-', ' ', $key);
                        $filled = (isset($extra[$name]) ? ' value="' . $extra[$name] . '"': '');
                        $type = $type_cnt = 'text';
                        $custom_input = false;
                        switch ($key) {
                            case 'birthday':
                                $type = 'date';
                                break;
                            case 'twitter':
                            case 'linkedin':
                            case 'facebook':
                            case 'pinterest':
                            case 'instagram':
                            case 'website':
                                $type = 'url';
                                break;
                            case 'phone':
                                $type_cnt = 'select-input';
                                $custom_input = '<div>' . sb_select_phone() . '</div><input' . $filled . ' autocomplete="false" type="tel" pattern="[0-9]+"' . (sb_get_setting('registration-phone-required') ? ' required' : '') . '>';
                                break;
                            case 'country':
                                $type_cnt = 'select';
                                $custom_input = sb_select_countries();
                                break;
                            case 'language':
                                $type_cnt = 'select';
                                $custom_input = sb_select_languages();
                                break;
                        }
                        $code .= '<div id="' . $key . '" data-type="' . $type_cnt . '" class="sb-input sb-input-' . $type_cnt . '"><span>' . sb_(ucfirst($name)) . '</span>' . ($custom_input ? $custom_input : '<input' . $filled . ' autocomplete="false" type="' . $type . '">') . '</div>';
                    }
                }
                if (sb_get_setting('registration-extra')) {
                    $additional_fields = sb_get_setting('user-additional-fields');
                    if ($additional_fields != false) {
                        for ($i = 0; $i < count($additional_fields); $i++) {
                            $value = $additional_fields[$i];
                            $name = $value['extra-field-name'];
                            $filled = isset($extra[$value['extra-field-slug']]) ? ' value="' . $extra[$value['extra-field-slug']] . '"' : '';
                            if ($name != '') {
                                $code .= '<div id="' . $value['extra-field-slug'] . '" data-type="text" class="sb-input sb-input-text"><span>' . sb_(ucfirst($name)) . '</span><input' . $filled . ' autocomplete="false" type="text"></div>';
                            }
                        }
                    }
                }

                $code .= '</div>' . $link . '<div class="sb-buttons"><div class="sb-btn sb-submit">' . sb_($visitor ? sb_isset($settings, 'registration-btn-text', 'Create account') : 'Update account') . '</div>' . ($password  != '' ? '<div class="sb-btn-text sb-login-area">' . sb_('Sign in instead') . '</div>': '') . '</div>';
                break;
            case 'login-tickets':
            case 'login':
                $settings = sb_get_setting('login');
                $title = sb_(sb_isset($settings, 'login-title', 'Login'));
                $message = sb_($settings['login-msg']);
                $code = '<div class="sb-form"><div id="email" class="sb-input"><span>' . sb_('Email') . '</span><input autocomplete="false" type="email"></div><div id="password" class="sb-input"><span>' . sb_('Password') . '</span><input autocomplete="false" type="password"></div></div><div class="sb-buttons"><div class="sb-btn sb-submit-login">' . sb_('Sign in') . '</div>' . (sb_get_setting('registration-required') == 'login' ? '' : '<div class="sb-btn-text sb-registration-area">' . sb_('Create new account') . '</div>') . '</div>';
                break;
            case 'timetable':
                $settings = sb_get_settings();
                $timetable = sb_isset($settings, 'timetable', [false])[0];
                $title = $settings['chat-timetable'][0]['chat-timetable-title'][0];
                $message = $settings['chat-timetable'][0]['chat-timetable-msg'][0];
                $title = sb_($title == '' ? 'Office hours' : $title);
                $message = sb_($message);
                $code = '<div class="sb-timetable" data-offset="' . sb_get_setting('timetable-utc') . '">';
                if ($timetable != false) {
                    foreach ($timetable as $day => $hours) {
                        if ($hours[0][0] != '') {
                            $code .= '<div><div>' . sb_(ucfirst($day)) . '</div><div data-time="' . $hours[0][0] . '|' . $hours[1][0] . '|' . $hours[2][0] . '|' . $hours[3][0] . '"></div></div>';
                        }
                    }
                }
                $code .= '<span></span></div>';
                break;
            case 'articles':
                $articles_title = sb_get_setting('articles-title');
                $code = '<div class="sb-dashboard-articles"><div class="sb-title">' . sb_($articles_title == '' ? 'Help Center' : $articles_title) . '</div><div class="sb-input sb-input-btn"><input placeholder="' . sb_('Search for articles...') . '" autocomplete="off"><div class="sb-submit-articles sb-icon-arrow-right"></div></div><div class="sb-articles">';
                $articles = sb_get_articles(-1, 2, false, false, sb_get_user_language());
                for ($i = 0; $i < count($articles); $i++) {
                    $code .= '<div data-id="' . $articles[$i]['id'] . '"><div>' . $articles[$i]['title'] . '</div><span>' . $articles[$i]['content'] . '</span></div>';
                }
                $code .= '</div><div class="sb-btn sb-btn-all-articles">' .sb_('All articles') . '</div></div>';
                break;
            case 'woocommerce-cart':
                $code = sb_woocommerce_rich_messages($name);
                break;
        }
        return ($title == '' ? '' : '<div class="sb-top">' . $title . '</div>') . ($message == '' ? '' : '<div class="sb-text">' . $message . '</div>') . $code  .  '<div data-success="' . $success . '" class="sb-info"></div>';
    } else {
        $result = sb_get_external_setting('rich-messages');
        if (is_array($result)) {
            for ($i = 0; $i < count($result['rich-messages'][0]); $i++) {
                $item = $result['rich-messages'][0][$i];
                if ($item['rich-message-name'] == $name) {
                    return $item['rich-message-content'];
                }
            }
        }
    }
    return false;
}

function sb_rich_value($value, $merge_fields = true, $tranlsate = true, $shortcodes = false) {
    $value = str_replace('"', '\'', strip_tags($value));
    if (!$shortcodes) $value = str_replace(['[', ']'], '', $value);
    if ($tranlsate) $value = sb_($value);
    return $merge_fields ? sb_merge_fields($value) : $value;
}

function sb_get_shortcode($message, $name, $type = 'merge') {
    $separator =  $type == 'merge' ? ['{', '}'] : ['[', ']'];
    $response = false;
    $name = $separator[0] . $name;
    if (strpos($message, $name) !== false) {
        $position = strpos($message, $name);
        $code = substr($message, $position, strpos($message, $separator[1], $position) + 1);
        $response = ['shortcode' => $code];
        $values = [];
        if (preg_match_all('/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w-]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|\'([^\']*)\'(?:\s|$)|(\S+)(?:\s|$)/', substr($code,1,-1), $values, PREG_SET_ORDER)) {
            for ($i = 0; $i < count($values); $i++){
                if (count($values[$i]) == 3 && !empty($values[$i][1]) && !empty($values[$i][2])){
                    $response[$values[$i][1]] = $values[$i][2];
                }
            }
        }
    }
    return $response;
}

/*
 * -----------------------------------------------------------
 * # TYPING STATUS
 * -----------------------------------------------------------
 *
 * 1. Check if the user is typing on the chat
 * 2. Check if an agent is typing in a conversation
 * 3. Set the user typing status
 *
 */

function sb_is_typing($user_id, $conversation_id) {
    $typing = sb_db_get('SELECT COUNT(*) as typing FROM sb_users WHERE id = "' . $user_id . '" AND typing = "' . $conversation_id . '"');
    return $typing['typing'] != 0;
}

function sb_is_agent_typing($conversation_id) {
    return sb_db_get('SELECT id, first_name, last_name FROM sb_users WHERE typing = ' . $conversation_id . ' AND (user_type = "agent" OR user_type = "admin") AND id <> ' . sb_get_active_user_ID());
}

function sb_set_typing($user_id, $conversation_id) {
    return sb_db_query('UPDATE sb_users SET typing = ' . $conversation_id . ' WHERE id = "' . $user_id . '"');
}

/*
 * -----------------------------------------------------------
 * # TRANSLATIONS
 * -----------------------------------------------------------
 *
 * 1. Return the translation of a string
 * 2. Echo the translation of a string
 * 3. Initialize the translations
 * 4. Return the current translations array
 * 5. Return all the translations of both admin and front areas of all languages
 * 6. Return the translations of a language
 * 7. Save a translation langauge file and a copy of it as backup
 * 8. Restore a translation language file from a backup
 * 9. Return the user langauge code
 * 10. Translate a string in the given language
 *
 */

function sb_($string) {
    global $SB_TRANSLATIONS;
    if (!isset($SB_TRANSLATIONS)) {
        sb_init_translations();
    }
    return empty($SB_TRANSLATIONS[$string]) ? $string : $SB_TRANSLATIONS[$string];
}

function sb_e($string) {
    echo sb_($string);
}

function sb_init_translations() {
    global $SB_TRANSLATIONS;
    global $SB_LANGUAGE;

    if (!empty($SB_LANGUAGE) && $SB_LANGUAGE[0] != 'en') {
        $path = SB_PATH . '/resources/languages/' . $SB_LANGUAGE[1] . '/' . $SB_LANGUAGE[0] . '.json';
        if (file_exists($path)) {
            $SB_TRANSLATIONS = json_decode(file_get_contents($path), true);
        }  else {
            $SB_TRANSLATIONS = false;
        }
    } else if (!isset($SB_LANGUAGE)) {
        $language = sb_get_user_language();
        $admin = sb_is_agent();
        $admin_lang = $admin && defined('SB_ADMIN_LANG') ? strtolower(SB_ADMIN_LANG) : false;
        if (((!$language || $language == 'en') && !$admin_lang) || ($admin && !sb_get_setting('admin-auto-translations') && !$admin_lang) || (!$admin && !isset($_GET['lang']) && !sb_get_setting('front-auto-translations'))) {
            $SB_TRANSLATIONS = false;
        } else {
            switch($language) {
                case 'nn':
                case 'nb':
                    $language = 'no';
                    break;
            }
            if ($admin_lang) $language = $admin_lang;
            $area = $admin ? 'admin' : 'front';
            $path = SB_PATH . '/resources/languages/' . $area . '/' . $language . '.json';
            if (file_exists($path)) {
                $SB_TRANSLATIONS = json_decode(file_get_contents($path), true);
                $SB_LANGUAGE = [$language, $area];
            }  else {
                $SB_TRANSLATIONS = false;
            }
        }
        if ($SB_TRANSLATIONS == false) {
            $SB_LANGUAGE = false;
        }
    } else {
        $SB_TRANSLATIONS = false;
    }
}

function sb_get_current_translations() {
    global $SB_TRANSLATIONS;
    if (!isset($SB_TRANSLATIONS)) {
        sb_init_translations();
    }
    return $SB_TRANSLATIONS;
}

function sb_get_translations($is_user = false, $language_code = false) {
    $translations = [];
    if ($is_user && !file_exists(SB_PATH . '/uploads/languages')) {
        return [];
    }
    $path = $is_user ? '/uploads' : '/resources';
    $language_codes = json_decode(file_get_contents(SB_PATH . '/resources/languages/language-codes.json'), true);
    $front_files = scandir(SB_PATH . $path . '/languages/front');
    $admin_files = scandir(SB_PATH . $path . '/languages/admin');
    for ($i = 0; $i < count($front_files); $i++)  {
        $code = substr($front_files[$i], 0, -5);
        if ($language_code && $language_code != $code) continue;
        if (isset($language_codes[$code])) {
            $translations[$code] = ['name' => $language_codes[$code], 'front' => json_decode(file_get_contents(SB_PATH . $path . '/languages/front/' . $front_files[$i]), true)];
        }
    }
    for ($i = 0; $i < count($admin_files); $i++)  {
        $code = substr($admin_files[$i], 0, -5);
        if ($language_code && $language_code != $code) continue;
        if (isset($translations[$code])) {
            $translations[$code]['admin'] = json_decode(file_get_contents(SB_PATH . $path . '/languages/admin/' . $admin_files[$i]), true);
        }
    }
    return $translations;
}

function sb_get_translation($language_code) {
    return sb_get_translations(false, $language_code)[$language_code];
}

function sb_save_translations($translations) {
    if (!file_exists(SB_PATH . '/uploads/languages')) {
        mkdir(SB_PATH . '/uploads/languages', 0777, true);
        mkdir(SB_PATH . '/uploads/languages/front', 0777, true);
        mkdir(SB_PATH . '/uploads/languages/admin', 0777, true);
    }
    if (is_string($translations)) $translations = json_decode($translations, true);
    foreach ($translations as $key => $translation) {
        foreach ($translation as $key_area => $translations_list) {
            $json = json_encode($translations_list, JSON_INVALID_UTF8_IGNORE);
            if ($json != false) {
                $paths = ['resources', 'uploads'];
                for ($i = 0; $i < 2; $i++)  {
                    sb_file(SB_PATH . '/' . $paths[$i] . '/languages/' . $key_area . '/' . $key . '.json', $json);
                }
            }
        }
    }
    return true;
}

function sb_restore_user_translations() {
    $translations_all = sb_get_translations();
    $translations_user = sb_get_translations(true);
    foreach ($translations_user as $key => $translations) {
        $paths = ['front', 'admin'];
        for ($i = 0; $i < 2; $i++)  {
            if (isset($translations_all[$key]) && isset($translations_all[$key][$paths[$i]])) {
                foreach ($translations_all[$key][$paths[$i]] as $key_two => $translation) {
                    if (!isset($translations[$paths[$i]][$key_two])) {
                        $translations[$paths[$i]][$key_two] = $translations_all[$key][$paths[$i]][$key_two];
                    }
                }
            }
            sb_file(SB_PATH . '/resources/languages/' . $paths[$i] . '/'. $key . '.json', json_encode($translations[$paths[$i]], JSON_INVALID_UTF8_IGNORE));
        }
    }
}

function sb_get_user_language($user_id = false) {
    if ($user_id) {
        $language = sb_get_user_extra($user_id, 'browser_language');
        $language = $language ? $language : sb_get_user_extra($user_id, 'language');
        if ($language) return $language;
    }
    global $SB_LANGUAGE;
    if (empty($SB_LANGUAGE)) {
        return isset($_GET['lang']) ? $_GET['lang'] : strtolower(substr(sb_isset($_SERVER, 'HTTP_ACCEPT_LANGUAGE', '  '), 0, 2));
    }
    return $SB_LANGUAGE[0];
}

function sb_translate_string($string, $language_code) {
    global $SB_LANGUAGE;
    global $SB_TRANSLATIONS;
    $translations = [];
    if (!empty($SB_LANGUAGE) && $SB_LANGUAGE[0] == $language_code && !empty($SB_TRANSLATIONS)) {
        $translations = $SB_TRANSLATIONS;
    } else {
        $path = SB_PATH . '/resources/languages/front/' . $language_code . '.json';
        if (file_exists($path)) {
            $translations = json_decode(file_get_contents($path), true);
        }
    }
    return empty($translations[$string]) ? $string : $translations[$string];
}

/*
 * -----------------------------------------------------------
 * # SETTINGS
 * -----------------------------------------------------------
 *
 * 1. Populate the admin area with the settings of the file /resources/json/settings.json
 * 2. Pupulate the admin area of the apps
 * 3. Return the HTML code of a setting element
 * 4. Save the all settings and external settings
 * 5. Save an external setting
 * 6. Return the settings array
 * 7. Return all settings and external settings
 * 8. Return the setting with the given name
 * 9. Return a single setting of a multi values setting
 * 10. Return the external setting with the given name
 * 11. Return a multilingual external setting
 * 12. Return the settings of the front-end
 * 13. Return the setting of block message
 * 14. Return the HTML code of the color palette
 * 15. Export all settings and external settings
 * 16. Import all settings and external settings
 *
 */

function sb_populate_settings($category, $settings, $echo = true) {
    if (!isset($settings) && file_exists(SB_PATH . '/resources/json/settings.json')) {
        $settings = json_decode(file_get_contents(SB_PATH . '/resources/json/settings.json'), true);
    }
    $settings = $settings[$category];
    $code = '';
    for ($i = 0; $i < count($settings); $i++) {
        $code .= sb_get_setting_code($settings[$i]);
    }
    if ($echo) {
        echo $code;
        return true;
    } else {
        return $code;
    }
}

function sb_populate_app_settings($app_name) {
    $file = SB_PATH . '/apps/' . $app_name . '/settings.json';
    $settings = [$app_name => []];
    if (file_exists($file)) {
        $settings[$app_name] = json_decode(file_get_contents($file), true);
    }
    return sb_populate_settings($app_name, $settings, false);
}

function sb_get_setting_code($array) {
    if (isset($array)) {
        $id = $array['id'];
        $type = $array['type'];
        $content = '<div id="' . $id . '" data-type="' . $type . '"' . (isset($array['setting']) ? ' data-setting="' . $array['setting'] . '"' : '') .' class="sb-setting sb-type-' . $type . '"><div class="sb-setting-content"><h2>' . sb_($array["title"]) . '</h2><p>' . sb_($array["content"]) . (isset($array["help"]) ? '<a href="' . $array["help"] . '" target="_blank" class="sb-icon-help"></a>' : '') . '</p></div><div class="input">';
        switch ($type) {
            case 'color':
                $content .= '<input type="text"><i class="sb-close sb-icon-close"></i>';
                break;
            case 'text':
                $content .= '<input type="text">';
                break;
            case 'password':
                $content .= '<input type="password">';
                break;
            case 'textarea':
                $content .= '<textarea></textarea>';
                break;
            case 'select':
                $content .= '<select>';
                for ($i = 0; $i < count($array['value']); $i++) {
                    $content .= '<option value="' . $array['value'][$i][0] . '">' . sb_($array['value'][$i][1]) . '</option>';
                }
                $content .= '</select>';
                break;
            case 'checkbox':
                $content .= '<input type="checkbox">';
                break;
            case 'radio':
                for ($i = 0; $i < count($array['value']); $i++) {
                    $content .= '<div><input type="radio" name="' . $id . '" value="' . strtolower(str_replace(' ', '-', $array['value'][$i])) . '"><label>' . $array["value"][$i] . '</label></div>';
                }
                break;
            case 'number':
                $content .= '<input type="number">' . (isset($array['unit']) ? '<label>' . $array['unit'] . '</label>' : '');
                break;
            case 'upload':
                $content .= (empty($array['text-field']) ? '' : '<input type="url">') . '<a class="sb-btn">' . sb_(sb_isset($array, 'button-text', 'Choose file')) . '</a>';
                break;
            case 'upload-image':
                $content .= '<div class="image"' . (isset($array['background-size']) ? ' style="background-size: ' . $array['background-size'] . '"' : '')  . '><i class="sb-icon-close"></i></div>';
                break;
            case 'input-button':
                $content .= '<input type="text"><a class="sb-btn">' . sb_($array['button-text']) . '</a>';
                break;
            case 'button':
                $content .= '<a class="sb-btn" target="_blank" href="' . sb_($array['button-url']) . '">' . sb_($array['button-text']) . '</a>';
                break;
            case 'multi-input':
                for ($i = 0; $i < count($array['value']); $i++) {
                    $sub_type = $array['value'][$i]['type'];
                    $content .= '<div id="' . $array['value'][$i]['id'] . '" data-type="' . $sub_type . '" class="multi-input-' . $array['value'][$i]['type'] . '"><label>' . sb_($array['value'][$i]['title']) . '</label>';
                    switch ($sub_type) {
                        case 'text':
                            $content .= '<input type="text">';
                            break;
                        case 'password':
                            $content .= '<input type="password">';
                            break;
                        case 'number':
                            $content .= '<input type="number">';
                            break;
                        case 'textarea':
                            $content .= '<textarea></textarea>';
                            break;
                        case 'upload':
                            $content .= '<input type="url"><button type="button">' . sb_('Choose file') . '</button>';
                            break;
                        case 'upload-image':
                            $content .= '<div class="image"><i class="sb-icon-close"></i></div>';
                            break;
                        case 'checkbox':
                            $content .= '<input type="checkbox">';
                            break;
                        case 'select':
                            $content .= '<select>';
                            $items = $array['value'][$i]['value'];
                            for ($j = 0; $j < count($items); $j++) {
                                $content .= '<option value="' . $items[$j][0] . '">' . sb_($items[$j][1]) . '</option>';
                            }
                            $content .= '</select>';
                            break;
                        case 'button':
                            $content .= '<a class="sb-btn" target="_blank" href="' . sb_($array['value'][$i]['button-url']) . '">' . sb_($array['value'][$i]['button-text']) . '</a>';
                    }
                    $content .= '</div>';
                }
                break;
            case 'range':
                $range = (key_exists('range', $array) ? $array['range'] : array(0, 100));
                $unit = (key_exists('unit', $array) ? '<label>' . $array['unit'] . '</label>' : '');
                $content .= '<label class="range-value">' . $range[0] . '</label><input type="range" min="' . $range[0] .'" max="' . $range[1] .'" value="' . $range[0] . '" />' . $unit;
                break;
            case 'repeater':
                $content .= '<div class="sb-repeater"><div class="repeater-item">';
                for ($i = 0; $i < count($array['items']); $i++) {
                    $item = $array['items'][$i];
                    $content .= '<div>' . (isset($item['name']) ? '<label>' . sb_($item['name']) . '</label>' : '');
                    switch ($item['type']) {
                        case 'text':
                            $content .= '<input data-id="' . $item['id'] . '" type="text">';
                            break;
                        case 'number':
                            $content .= '<input data-id="' . $item['id'] . '" type="number">';
                            break;
                        case 'textarea':
                            $content .= '<textarea data-id="' . $item['id'] . '"></textarea>';
                            break;
                        case 'checkbox':
                            $content .= '<input data-id="' . $item['id'] . '" type="checkbox">';
                            break;
                        case 'auto-id':
                            $content .= '<input data-type="auto-id" data-id="' . $item['id'] . '" value="1" type="text" readonly="true">';
                            break;
                        case 'hidden':
                            $content .= '<input data-id="' . $item['id'] . '" type="hidden">';
                            break;
                        case 'color-palette':
                            $content .= sb_color_palette($item['id']);
                            break;
                        case 'upload-image':
                            $content .= '<div data-type="upload-image"><div data-id="' . $item['id'] . '" class="image"><i class="sb-icon-close"></i></div></div>';
                            break;
                    }
                    $content .= '</div>';
                }
                $content .= '<i class="sb-icon-close"></i></div></div><a class="sb-btn sb-repeater-add">' . sb_('Add new item') . '</a>';
                break;
            case 'timetable':
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                $hours = [['', ''], ['00:00', '12:00 am'], ['00:30', '12:30 am'], ['01:00', '1:00 am'], ['01:30', '1:30 am'], ['02:00', '2:00 am'], ['02:30', '2:30 am'], ['03:00', '3:00 am'], ['03:30', '3:30 am'], ['04:00', '4:00 am'], ['04:30', '4:30 am'], ['05:00', '5:00 am'], ['05:30', '5:30 am'], ['06:00', '6:00 am'], ['06:30', '6:30 am'], ['07:00', '7:00 am'], ['07:30', '7:30 am'], ['08:00', '8:00 am'], ['08:30', '8:30 am'], ['09:00', '9:00 am'], ['09:30', '9:30 am'], ['10:00', '10:00 am'], ['10:30', '10:30 am'], ['11:00', '11:00 am'], ['11:30', '11:30 am'], ['12:00', '12:00 pm'], ['12:30', '12:30 pm'], ['13:00', '1:00 pm'], ['13:30', '1:30 pm'], ['14:00', '2:00 pm'], ['14:30', '2:30 pm'], ['15:00', '3:00 pm'], ['15:30', '3:30 pm'], ['16:00', '4:00 pm'], ['16:30', '4:30 pm'], ['17:00', '5:00 pm'], ['17:30', '5:30 pm'], ['18:00', '6:00 pm'], ['18:30', '6:30 pm'], ['19:00', '7:00 pm'], ['19:30', '7:30 pm'], ['20:00', '8:00 pm'], ['20:30', '8:30 pm'], ['21:00', '9:00 pm'], ['21:30', '9:30 pm'], ['22:00', '10:00 pm'], ['22:30', '10:30 pm'], ['23:00', '11:00 pm'], ['23:30', '11:30 pm'], ['closed', sb_('Closed')]];
                $select = '<div class="sb-custom-select">';
                for ($i = 0; $i < count($hours); $i++) {
                    $select .= '<span data-value="' . $hours[$i][0] . '">' . $hours[$i][1] . '</span>';
                }
                $content .= '<div class="sb-timetable">';
                for ($i = 0; $i < 7; $i++) {
                    $content .= '<div data-day="' . strtolower($days[$i]) . '"><label>' . sb_($days[$i]) . '</label><div><div></div><span>' . sb_('To') . '</span><div></div><span>' . sb_('And') . '</span><div></div><span>' . sb_('To') . '</span><div></div></div></div>';
                }
                $content .= $select . '</div></div>';
                break;
            case 'select-images':
                $content .= '<div class="sb-icon-close"></div>';
                for ($i = 0; $i < count($array['images']); $i++) {
                    $content .= '<div data-value="' . $array['images'][$i] . '" style="background-image: url(\'' . SB_URL . '/media/' . $array['images'][$i] . '\')"></div>';
                }
                break;
        }
        if (isset($array['setting']) && ($type == 'multi-input' || !empty($array['multilingual']))) {
            $content .= '<div class="sb-language-switcher-cnt"><label>' . sb_('Languages') . '</label></div>';
        }
        return $content . '</div></div>';
    }
    return '';
}

function sb_save_settings($settings, $external_settings = [], $external_settings_translations = []) {
    if (isset($settings)) {
        global $SB_SETTINGS;
        $settings_encoded = sb_db_json_enconde($settings);
        if (isset($settings_encoded) && is_string($settings_encoded)) {

            // Save main settings
            $query = 'INSERT INTO sb_settings(name, value) VALUES (\'settings\', \'' . $settings_encoded . '\') ON DUPLICATE KEY UPDATE value = \'' . $settings_encoded . '\'';
            $result = sb_db_query($query);
            if (sb_is_error($result)) {
                return $result;
            }

            // Save external settings
            foreach ($external_settings as $key => $value) {
                sb_save_external_setting($key, $value);
            }

            // Save external settings translations
            $db = '';
            foreach ($external_settings_translations as $key => $value) {
                $name = 'external-settings-translations-' . $key;
                sb_save_external_setting($name, $value);
                $db .=  '"' . $name . '",';
            }
            sb_db_query('DELETE FROM sb_settings WHERE name LIKE "external-settings-translations-%" AND name NOT IN (' . substr($db, 0, -1) . ')');

            // Update bot
            sb_update_bot($settings['bot-name'][0], $settings['bot-image'][0]);

            $SB_SETTINGS = $settings;
            return true;
        } else {
            return new SBError('json-encode-error', 'sb_save_settings');
        }
    } else {
        return new SBError('settings-not-found', 'sb_save_settings');
    }
}

function sb_save_external_setting($name, $value) {
    $settings_encoded = sb_db_json_enconde($value);
    return JSON_ERROR_NONE !== json_last_error() ? json_last_error_msg() : sb_db_query('INSERT INTO sb_settings(name, value) VALUES (\'' . sb_sanatize($name) . '\', \'' . $settings_encoded . '\') ON DUPLICATE KEY UPDATE value = \'' . $settings_encoded . '\'');
}

function sb_get_settings() {
    global $SB_SETTINGS;
    if (isset($SB_SETTINGS)) {
        return $SB_SETTINGS;
    } else {
        $SB_SETTINGS = sb_get_external_setting('settings', []);
        return $SB_SETTINGS;
    }
}

function sb_get_all_settings() {
    $translations = [];
    $settings = [];
    $rows = sb_db_get('SELECT value FROM sb_settings WHERE name="emails" || name="rich-messages" || name="wc-emails"', false);
    for ($i = 0; $i < count($rows); $i++) {
        $settings = array_merge($settings, json_decode($rows[$i]['value'], true));
    }
    $rows = sb_db_get('SELECT name, value FROM sb_settings WHERE name LIKE "external-settings-translations-%"', false);
    for ($i = 0; $i < count($rows); $i++) {
        $translations[substr($rows[$i]['name'], -2)] = json_decode($rows[$i]['value'], true);
    }
    return array_merge(sb_get_settings(), $settings, ['external-settings-translations' => $translations]);
}

function sb_get_setting($id, $default = false) {
    $settings = sb_get_settings();
    if (!sb_is_error($settings)) {
        if (isset($settings[$id]) && !empty($settings[$id][0])) {
            $setting = $settings[$id][0];
            if (is_array($setting) && !isset($setting[0])) {
                $settings_result = [];
                foreach ($setting as $key => $value) {
                    $settings_result[$key] = $value[0];
                }
                return $settings_result;
            } else {
                return $setting;
            }
        } else {
            return $default;
        }
    } else {
        return $settings;
    }
}

function sb_get_multi_setting($id, $sub_id, $default = false) {
    $setting = sb_get_setting($id);
    if ($setting && !empty($setting[$sub_id])) {
        return $setting[$sub_id];
    }
    return $default;
}

function sb_get_external_setting($name, $default = false) {
    $result = sb_db_get('SELECT value FROM sb_settings WHERE name = "' . $name . '"', false);
    $settings = [];
    if (empty($result)) return $default;
    if (sb_is_error($settings)) return $settings;
    if (!is_array($result)) {
        return $result;
    }
    if (count($result) == 1) {
        return json_decode($result[0]['value'], true);
    }
    for ($i = 0; $i < count($result); $i++) {
        $settings = array_merge($settings, json_decode($result[$i]['value'], true));
    }
    return $settings;
}

function sb_get_multilingual_setting($name, $sub_name, $language = false) {
    $language = $language ? $language : sb_get_user_language();
    $value = $language && $language != 'en' ? sb_isset(sb_get_external_setting('external-settings-translations-' . $language), $sub_name) : false;
    if ($value) return $value;
    $value = sb_isset(sb_get_external_setting($name), $sub_name);
    if ($value && is_array($value)) {
        $value = $value[0];
        if (!isset($value[0]) || !is_string($value[0])) {
            foreach ($value as $key => $setting) {
                $value[$key] = $setting[0];
            }
        }
    }
    return $value;
}

// Deprecated from V 3.1.6
function sb_get_external_settings($name, $default = false) {
    return sb_get_external_setting($name, $default);
}

function sb_get_front_settings() {
    global $SB_LANGUAGE;
    $active_user = sb_get_active_user();
    $pusher = sb_pusher_active();
    $return = [
        'registration-required' => sb_get_setting('registration-required'),
        'registration-timetable' => sb_get_setting('registration-timetable'),
        'registration-offline' => sb_get_setting('registration-offline'),
        'registration-link' => sb_get_setting('registration-link', ''),
        'registration-details' => sb_get_setting('registration-user-details-success'),
        'visitors-registration' => sb_get_setting('visitors-registration'),
        'privacy' => sb_get_multi_setting('privacy', 'privacy-active'),
        'popup' => sb_get_block_setting('popup'),
        'popup-mobile-hidden' => sb_get_multi_setting('popup-message', 'popup-mobile-hidden'),
        'welcome' => sb_get_multi_setting('welcome-message', 'welcome-active'),
        'welcome-trigger' => sb_get_multi_setting('welcome-message', 'welcome-trigger', 'load'),
        'welcome-delay' => sb_get_multi_setting('welcome-message', 'welcome-delay', 2000),
        'welcome-disable-office-hours' => sb_get_multi_setting('welcome-message', 'welcome-disable-office-hours'),
        'subscribe' => sb_get_multi_setting('subscribe-message', 'subscribe-active'),
        'subscribe-delay' => sb_get_multi_setting('subscribe-message', 'subscribe-delay', 2000),
        'follow' => sb_get_block_setting('follow'),
        'chat-manual-init' => sb_get_setting('chat-manual-init'),
        'chat-login-init' => sb_get_setting('chat-login-init'),
        'chat-sound' => sb_get_setting('chat-sound', 'n'),
        'header-name' => sb_get_setting('header-name', ''),
        'desktop-notifications' => sb_get_setting('desktop-notifications') && !sb_get_multi_setting('push-notifications', 'push-notifications-active'),
        'flash-notifications' => sb_get_setting('flash-notifications'),
        'push-notifications' => sb_get_multi_setting('push-notifications', 'push-notifications-active'),
        'notifications-icon' => sb_get_setting('notifications-icon', SB_URL . '/media/icon.png'),
        'bot-id' => sb_get_bot_id(),
        'bot-name' => sb_get_setting('bot-name', ''),
        'bot-image' => sb_get_setting('bot-image', ''),
        'bot-delay' => sb_get_setting('dialogflow-bot-delay', 2000),
        'bot-office-hours' => sb_get_setting('dialogflow-timetable'),
        'bot-unknow-notify' => sb_get_setting('bot-unknow-notify'),
        'dialogflow-active' => defined('SB_DIALOGFLOW') && sb_get_setting('dialogflow-active'),
        'dialogflow-human-takeover' => false,
        'dialogflow-welcome' => false,
        'slack-active' => defined('SB_SLACK') && sb_get_setting('slack-active'),
        'rich-messages' => sb_get_rich_messages_ids(),
        'display-users-thumb' => sb_get_setting('display-users-thumb'),
        'hide-agents-thumb' => sb_get_setting('hide-agents-thumb'),
        'notify-agent-email' => sb_get_setting('notify-agent-email') || sb_get_multi_setting('email-piping', 'email-piping-active'),
        'translations' => sb_get_current_translations(),
        'auto-open' => sb_get_setting('auto-open'),
        'office-hours' => sb_office_hours(),
        'disable-office-hours' => sb_get_setting('chat-timetable-disable'),
        'disable-offline' => sb_get_setting('chat-offline-disable'),
        'timetable' => sb_get_multi_setting('chat-timetable', 'chat-timetable-active'),
        'timetable-type' => sb_get_multi_setting('chat-timetable', 'chat-timetable-type'),
        'timetable-hide' => sb_get_multi_setting('chat-timetable', 'chat-timetable-hide'),
        'timetable-disable-agents' => sb_get_multi_setting('chat-timetable', 'chat-timetable-agents'),
        'articles' => sb_get_setting('articles-active'),
        'articles-title' => sb_get_setting('articles-title', ''),
        'init-dashboard' => sb_get_setting('init-dashboard') && !sb_get_setting('disable-dashboard'),
        'wp-users-system' => sb_get_setting('wp-users-system', 'sb'),
        'wp-logout' => sb_get_setting('wp-logout'),
        'queue' => sb_get_multi_setting('queue', 'queue-active'),
        'queue-message' => sb_get_multi_setting('queue', 'queue-message', ''),
        'queue-message-success' => sb_get_multi_setting('queue', 'queue-message-success', ''),
        'queue-response-time' => sb_get_multi_setting('queue', 'queue-response-time', 5),
        'routing' => !sb_get_multi_setting('queue', 'queue-active') && sb_get_setting('routing'),
        'hide-conversations-routing' => !sb_get_multi_setting('queue', 'queue-active') && sb_get_multi_setting('agent-hide-conversations', 'agent-hide-conversations-active') && sb_get_multi_setting('agent-hide-conversations', 'agent-hide-conversations-routing'),
        'webhooks' => sb_get_multi_setting('webhooks', 'webhooks-active'),
        'agents-online' => sb_agents_online(),
        'cron' => date('H') != sb_get_external_setting('cron'),
        'cron-email-piping' => sb_get_multi_setting('email-piping', 'email-piping-active') && date('i') != sb_get_external_setting('cron-email-piping'),
        'cron-email-piping-active' => sb_get_multi_setting('email-piping', 'email-piping-active'),
        'wp' => defined('SB_WP'),
        'perfex' => defined('SB_PERFEX'),
        'whmcs' => defined('SB_WHMCS'),
        'aecommerce' => defined('SB_AECOMMERCE'),
        'messenger' => defined('SB_MESSENGER'),
        'pusher' => $pusher,
        'cookie-domain' => sb_get_setting('cookie-domain'),
        'visitor-default-name' => sb_get_setting('visitor-default-name', 'User'),
        'sms-active-agents' => sb_get_multi_setting('sms', 'sms-active-agents'),
        'language-detection' => false,
        'automations' => sb_automations_run_all()
    ];
    if ($return['push-notifications']) {
        $return['push-notifications-id'] = sb_get_multi_setting('push-notifications', 'push-notifications-id');
        $return['push-notifications-url'] = sb_get_multi_setting('push-notifications', 'push-notifications-sw-url');
        $return['push-notifications-users'] = sb_get_multi_setting('push-notifications', 'push-notifications-users-active');
    }
    if ($return['pusher']) {
        $return['pusher-key'] = sb_get_multi_setting('pusher', 'pusher-key');
        $return['pusher-cluster'] = sb_get_multi_setting('pusher', 'pusher-cluster');
    }
    if ($return['timetable-hide'] || !empty($return['timetable-type'])) {
        $return['timetable-message'] = [sb_(sb_get_multi_setting('chat-timetable', 'chat-timetable-title')), sb_(sb_get_multi_setting('chat-timetable', 'chat-timetable-msg'))];
    }

    if (defined('SB_TICKETS')) {
        $return['tickets'] = true;
        $return['tickets-registration-required'] = sb_get_setting('tickets-registration-required');
        $return['tickets-registration-redirect'] = sb_get_setting('tickets-registration-redirect', '');
        $return['tickets-default-form'] = sb_get_setting('tickets-registration-disable-password') ? 'registration' : sb_get_setting('tickets-default-form', 'login');
        $return['tickets-conversations-title-user'] = sb_get_setting('tickets-conversations-title-user');
        $return['tickets-welcome-active'] = sb_get_multi_setting('tickets-welcome-message', 'tickets-welcome-message-active');
        $return['tickets-welcome-message'] = sb_merge_fields(sb_(sb_get_multi_setting('tickets-welcome-message', 'tickets-welcome-message-msg')));
        $return['tickets-conversation-name'] = sb_get_setting('tickets-conversation-name', '');
        $return['tickets-enter-button'] = sb_get_setting('tickets-enter-button');
        $return['tickets-manual-init'] = sb_get_setting('tickets-manual-init');
        $return['tickets-default-department'] = sb_get_setting('tickets-default-department');
    }
    if (defined('SB_WOOCOMMERCE')) {
        $return['woocommerce'] = true;
        $return['woocommerce-returning-visitor'] = !in_array(sb_isset($active_user, 'user_type'), ['user', 'agent', 'admin']) && sb_get_multi_setting('wc-returning-visitor', 'wc-returning-visitor-active');
    }
    if ($return['dialogflow-active']) {
        $human_takeover = sb_get_setting('dialogflow-human-takeover');
        if ($human_takeover != false && $human_takeover['dialogflow-human-takeover-active']) {
            $return['dialogflow-human-takeover'] = ['message' => sb_($human_takeover['dialogflow-human-takeover-message']), 'success' => sb_($human_takeover['dialogflow-human-takeover-message-confirmation']), 'email' => $human_takeover['dialogflow-human-takeover-email'], 'email-message' => sb_(sb_isset($human_takeover, 'dialogflow-human-takeover-email-message')), 'confirm' => sb_(sb_isset($human_takeover, 'dialogflow-human-takeover-confirm', 'Yes')), 'cancel' => sb_(sb_isset($human_takeover, 'dialogflow-human-takeover-cancel', 'No')), 'disable-bot' => sb_isset($human_takeover, 'dialogflow-human-disable-bot')];
        }
        $return['dialogflow-welcome'] = sb_get_setting('dialogflow-welcome');
        $return['dialogflow-disable'] = sb_get_setting('dialogflow-disable');
        $return['dialogflow-offline-message'] = sb_get_setting('dialogflow-offline-message');
    } else if (defined('SB_DIALOGFLOW') && sb_get_multi_setting('dialogflow-language-detection', 'dialogflow-language-detection-active')) {
        $return['language-detection'] = true;
    }
    if ($active_user) {
        $user_id = $active_user['id'];
        if (!sb_is_agent($active_user)) {
            if (isset($_POST['current_url'])) sb_current_url($user_id, $_POST['current_url']);
            else if (isset($_SERVER['HTTP_REFERER'])) sb_current_url($user_id, $_SERVER['HTTP_REFERER']);
            if ($pusher) sb_pusher_trigger('private-user-' . $user_id, 'init');
        }
        sb_update_users_last_activity($user_id);
    }
    return $return;
}

function sb_get_block_setting($value) {
    switch ($value)  {
    	case 'privacy':
            $settings = sb_get_setting('privacy');
            return ['active' => $settings['privacy-active'], 'title' => sb_rich_value($settings['privacy-title']), 'message' => sb_rich_value($settings['privacy-msg']), 'decline' => sb_rich_value($settings['privacy-msg-decline']), 'link' => $settings['privacy-link'], 'link-name' => sb_rich_value(sb_isset($settings, 'privacy-link-text', ''), false), 'btn-approve' => sb_rich_value($settings['privacy-btn-approve'], false), 'btn-decline' => sb_rich_value($settings['privacy-btn-decline'], false)];
        case 'popup':
            $settings = sb_get_setting('popup-message');
            return ['active' => $settings['popup-active'], 'title' => sb_rich_value($settings['popup-title']), 'message' => sb_rich_value($settings['popup-msg']), 'image' => $settings['popup-image']];
        case 'welcome':
            $settings = sb_get_setting('welcome-message');
            return ['active' => $settings['welcome-active'], 'message' => sb_($settings['welcome-msg']), 'open' => $settings['welcome-open'], 'sound' => $settings['welcome-sound']];
        case 'follow':
            $settings = sb_get_setting('follow-message');
            return ['active' => $settings['follow-active'], 'title' => sb_rich_value($settings['follow-title']), 'message' => sb_rich_value($settings['follow-msg'], true, true, true), 'name' => $settings['follow-name'] ? 'true' : 'false', 'last-name' => sb_isset($settings, 'follow-last-name') ? 'true' : 'false', 'phone' => sb_isset($settings, 'follow-phone') ? 'true' : 'false', 'phone-required' => sb_isset($settings, 'follow-phone-required') ? 'true' : 'false', 'phone-codes' => sb_get_multi_setting('performance', 'performance-phone-codes') ? false : array_values(json_decode(file_get_contents(SB_PATH . '/resources/json/phone.json'), true)), 'success' => sb_rich_value($settings['follow-success']), 'placeholder' => sb_rich_value(sb_isset($settings, 'follow-placeholder', 'Email'), false), 'delay' => sb_isset($settings, 'follow-delay'), 'disable-office-hours' => sb_isset($settings, 'follow-disable-office-hours')];
        case 'subscribe':
            $settings = sb_get_setting('subscribe-message');
            $settings_follow = sb_get_setting('follow-message');
            $message = '[email id="sb-subscribe-form" title="' . sb_rich_value($settings['subscribe-title']) . '" message="' . sb_rich_value($settings['subscribe-msg']) . '" success="' . sb_rich_value($settings['subscribe-msg-success']) . '" placeholder="' . sb_rich_value(sb_isset($settings, 'follow-placeholder', 'Email'), false) . '" name="' . ($settings_follow['follow-name'] ? 'true' : 'false') . '" last-name="' . ($settings_follow['follow-last-name'] ? 'true' : 'false') . '"]';
            return ['active' => $settings['subscribe-active'], 'message' => $message, 'sound' => $settings['subscribe-sound']];
    }
    return false;
}

function sb_color_palette($id = '') {
    return '<div data-type="color-palette" data-value="" data-id="' . $id . '" class="sb-color-palette"><span></span><ul><li data-value=""></li><li data-value="red"></li><li data-value="yellow"></li><li data-value="green"></li><li data-value="pink"></li><li data-value="gray"></li><li data-value="blue"></li></ul></div>';
}

function sb_export_settings() {
    $setting_keys = ['automations', 'emails', 'rich-messages', 'settings'];
    $settings = [];
    for ($i = 0; $i < count($setting_keys); $i++) {
        $value = sb_isset(sb_db_get('SELECT value FROM sb_settings WHERE name = "' . $setting_keys[$i] . '"'), 'value');
        if ($value) {
            $value = json_decode($value, true);
            if ($value) $settings[$setting_keys[$i]] = $value;
        }
    }
    $settings = json_encode($settings, JSON_INVALID_UTF8_IGNORE);
    if ($settings) {
        $name = 'settings' . '_' . rand(100000, 999999999) . '.json';
        $response = sb_file(SB_PATH . '/uploads/' . $name, $settings);
        return $response ? (SB_URL . '/uploads/' . $name) : $response;
    }
    return JSON_ERROR_NONE !== json_last_error() ? json_last_error_msg() : false;
}

function sb_import_settings($file_url) {
    $settings = json_decode(sb_download($file_url), true);
    if ($settings) {
        foreach ($settings as $key => $setting){
            sb_save_external_setting($key, $setting);
        }
        unlink(SB_PATH . substr($file_url, strpos($file_url, '/uploads/')));
        return true;
    }
    return JSON_ERROR_NONE !== json_last_error() ? json_last_error_msg() : false;
}

/*
 * -----------------------------------------------------------
 * # AGENTS RATING
 * -----------------------------------------------------------
 *
 * 1. Save the ratings of a conversation
 * 2. Return the ratings of a conversation
 *
 */

function sb_set_rating($settings, $payload = false, $message_id = false, $message = false) {
    if (!isset($settings['conversation_id'])) {
        return new SBValidationError('conversation-id-not-found');
    } else if (sb_conversation_security_error($settings['conversation_id'])) {
        return new SBError('security-error', 'sb_set_rating');
    }
    if (isset($settings['rating'])) {
        $ratings = sb_get_external_setting('ratings');
        if (!isset($ratings)) {
            $ratings = [];
        }
        $ratings[$settings['conversation_id']] = $settings;
        sb_save_external_setting('ratings', $ratings);
        if ($message_id != false) {
            sb_update_message($message_id, $message, false, $payload);
        }
        return true;
    }
    return false;
}

function sb_get_rating($agent_id) {
    $ratings = sb_get_external_setting('ratings');
    $positive = 0;
    $negative = 0;
    if (!empty($ratings)) {
        foreach ($ratings as $rating) {
            if (sb_isset($rating, 'agent_id', -1) == $agent_id){
                if ($rating['rating'] == 1) {
                    $positive++;
                } else {
                    $negative++;
                }
            }
        }
    }
    return [$positive, $negative];
}

/*
 * -----------------------------------------------------------
 * # ARTICLES
 * -----------------------------------------------------------
 *
 * 1. Save all articles
 * 2. Save all articles categories
 * 3. Returns all articles
 * 4. Returns all articles categories
 * 5. Search articles
 * 6. Article ratings
 *
 */

function sb_save_articles($articles, $categories = false, $translations = false) {
    if ($categories == 'delete_all') sb_save_external_setting('articles-categories', []);
    else if ($categories) sb_save_external_setting('articles-categories', $categories);
    if ($translations) {
        $db = '';
        foreach ($translations as $key => $value) {
            $name = 'articles-translations-' . $key;
            sb_save_external_setting($name, $value);
            $db .=  '"' . $name . '",';
        }
        sb_db_query('DELETE FROM sb_settings WHERE name LIKE "articles-translations-%" AND name NOT IN (' . substr($db, 0, -1) . ')');
    }
    return sb_save_external_setting('articles', $articles);
}

function sb_save_articles_categories($categories) {
    return sb_save_external_setting('articles-categories', $categories);
}

function sb_get_articles($article_id = -1, $count = false, $full = false, $categories = false, $language = false) {
    $languages_all = $language == 'all';
    if ($language == 'en') $language = false;
    $articles = sb_get_external_setting($language && !$languages_all ? 'articles-translations-' . $language : 'articles');
    $articles_translations = [];
    $return = [];
    if ($language && empty($articles)) $articles = sb_get_external_setting('articles');
    if ($articles != false) {
        $articles_count = count($articles);
        if ($article_id == -1) {
            $count = $count === false ? $articles_count : ($articles_count < $count ? $articles_count : $count);
            for ($i = 0; $i < $count; $i++) {
                if ($articles[$i]['title'] != '') {
                    if (!$full) {
                        if (strlen($articles[$i]['content']) > 100) {
                            $articles[$i]['content'] = mb_substr($articles[$i]['content'], 0, 100) . '...';
                        }
                        $articles[$i]['content'] = strip_tags($articles[$i]['content']);
                    }
                    array_push($return, $articles[$i]);
                }
            }
        } else {
            for ($i = 0; $i < $articles_count; $i++) {
                if ($articles[$i]['id'] == $article_id) {
                    sb_reports_update('articles-views', false, false, $article_id);
                    return $articles[$i];
                }
            }
            return false;
        }
    }
    if ($languages_all) {
        $rows = sb_db_get('SELECT name, value FROM sb_settings WHERE name LIKE "articles-translations-%"', false);
        for ($i = 0; $i < count($rows); $i++) {
            $articles_translations[substr($rows[$i]['name'], -2)] = json_decode($rows[$i]['value'], true);
        }
    }
    return $categories || $languages_all ? [$return, sb_get_articles_categories(), $articles_translations] : $return;
}

function sb_get_articles_categories() {
    return sb_get_external_setting('articles-categories', []);
}

function sb_search_articles($search, $language = false) {
    $articles = sb_get_external_setting($language ? 'articles-translations-' . $language : 'articles');
    $return = [];
    $search = strtolower($search);
    if ($language && empty($articles)) $articles = sb_get_external_setting('articles');
    if ($articles) {
        for ($i = 0; $i < count($articles); $i++) {
            if (strpos(strtolower($articles[$i]['title']), $search) !== false || strpos(strtolower($articles[$i]['content']), $search)) {
                $articles[$i]['content'] = mb_substr($articles[$i]['content'], 0, 100);
                array_push($return, $articles[$i]);
            }
        }
    }
    sb_reports_update('articles-searches', $search);
    return $return;
}

function sb_article_ratings($article_id, $rating = false) {
    $ratings = sb_isset(sb_db_get('SELECT value FROM sb_reports WHERE name = "article-ratings" AND extra = "' . $article_id . '" LIMIT 1'), 'value', []);
    if ($rating) {
        if (empty($ratings)) {
            return sb_db_query('INSERT INTO sb_reports (name, value, creation_time, external_id, extra) VALUES ("article-ratings", "[' . sb_sanatize($rating) . ']", "' . gmdate('Y-m-d') . '", NULL, "' . $article_id . '")');
        }  else {
            $ratings = json_decode($ratings);
            array_push($ratings, intval($rating));
            return sb_db_query('UPDATE sb_reports SET value = "' . json_encode(sb_sanatize($ratings)) . '" WHERE name = "article-ratings" AND extra = "' . $article_id . '"');
        }
    }
    return $ratings;
}

/*
 * ----------------------------------------------------------
 * # EMAIL
 * ----------------------------------------------------------
 *
 * 1. Create the email contents
 * 2. Create the email contents secondary function
 * 3. Send an email to the given address
 * 4. Send an email to the address of the given user ID
 * 5. Send a test email
 * 6. Check if the active user can send the requested email
 * 7. Email piping function
 * 8. Send the successfull subscription email
 * 9. Append the email header and the signature to an email content
 * 10. Convert the text formatting of Support Board to HTML
 * 11. Remove the text formatting of Support Board
 * 12. Newsletter
 *
 */

function sb_email_create($recipient_id, $sender_name, $sender_profile_image, $message, $attachments = [], $department = false, $conversation_id = false) {
    $recipient = false;
    $recipient_name = '';
    $recipient_email = '';
    $recipient_user_type = 'agent';
    if ($recipient_id == 'email-test') {
        $recipient_name = 'Test user';
    } else if ($recipient_id == -1 || $recipient_id == 'agents' || $recipient_id == 'all-agents' || strpos($recipient_id, 'department-') !== false) {
        $department = $department ? $department : (strpos($recipient_id, 'department-') !== false ? substr($department, 11) : false);
        $agents = sb_db_get('SELECT id, first_name, last_name, email FROM sb_users WHERE (user_type = "agent" OR user_type = "admin") ' . (empty($department) || $department == -1 ? ($recipient_id == 'agents' ? ' AND department IS NULL OR department = ""' : '') : ' AND department = ' . $department), false);
        $online_agents_ids = sb_get_online_users_ids(true);
        for ($i = 0; $i < count($agents); $i++) {
            if (!in_array($agents[$i]['id'], $online_agents_ids)) {
                $recipient_name .= sb_get_user_name($agents[$i]) . ', ';
                $recipient_email .= $agents[$i]['email'] . ',';
            }
        }
        $recipient_name = mb_substr($recipient_name, 0, -2);
        $recipient_email = substr($recipient_email, 0, -1);
    } else {
        if (!sb_email_security($recipient_id) && sb_get_active_user_ID() != $recipient_id) {
            return new SBError('security-error', 'sb_email_create');
        }
        $recipient = sb_get_user($recipient_id);
        if ($recipient == false || $recipient['email'] == '') {
            return new SBValidationError('email-not-found');
        }
        $recipient_name = sb_get_user_name($recipient);
        $recipient_email = $recipient['email'];
        $recipient_user_type = $recipient['user_type'];
    }
    $suffix = sb_is_agent($recipient_user_type) ? 'agent' : 'user';
    $settings = sb_get_multilingual_setting('emails', 'email-' . $suffix, $recipient_id == -1 ? 'en' : sb_get_user_language($recipient_id));
    $email = sb_email_create_content($settings['email-' . $suffix . '-subject'], $settings['email-' . $suffix . '-content'], $attachments, ['conversation_url_parameter' => ($recipient && $conversation_id ? ('?conversation=' . $conversation_id . '&token=' . $recipient['token']) : ''), 'message' => $message, 'recipient_name' => $recipient_name, 'sender_name' => $sender_name, 'sender_profile_image' => $sender_profile_image, 'conversation_id' => $conversation_id]);
    $piping = !empty($conversation_id) && sb_get_multi_setting('email-piping', 'email-piping-active') ? (' | SB' . $conversation_id . '-' . rand(100, 9999)) : '';
    $piping_delimiter = !empty($piping) && sb_get_multi_setting('email-piping', 'email-piping-delimiter') ? ('<div style="color:#b5b5b5">### ' . sb_('Please type your reply above this line') . ' ###</div><br><br>') : '';
    return sb_email_send($recipient_email, $email[0], $piping_delimiter . $email[1], $piping);
}

function sb_email_create_content($subject, $message, $attachments, $replacements) {
    $attachments_code = '';
    if (empty($attachments)) $attachments = [];
    for ($i = 0; $i < count($attachments); $i++) {
        $attachments_code .= '<a style="display:block;text-decoration:none;line-height:25px;color:rgb(102, 102, 102);" href="' . str_replace(' ', '%20', $attachments[$i][1]) . '">' . $attachments[$i][0] . '</a>';
    }
    if ($attachments_code != '') {
        $attachments_code = '<div style="margin-top: 30px">' . $attachments_code . '</div>';
    }
    if ($subject == '') {
        $subject = '[Support Board] New message from {sender_name}';
    }
    if ($message == '') {
        $message = '<br />Hello {recipient_name}!<br /><br />{message}{attachments}
                              <table cellspacing="0" border="0" cellpadding="0" bgcolor="transparent" style="border:none;border-collapse:separate;border-spacing:0;margin:0;table-layout:fixed">
                                <tr>
                                    <td valign="middle" width="50" align="left" style="border:none;padding:0;vertical-align:middle">
                                      <img width="40" height="40" src="{sender_profile_image}" style="border-radius:20px;display:inline-block;outline:none;">
                                    </td>
                                  <td valign="middle" align="left" style="border:none;font-family:Helvetica,Arial,sans-serif;padding:0;vertical-align:middle">
                                    <strong style="color:#000">{sender_name}</strong>
                                  </td>
                                </tr>
                             </table>';
    }
    $subject = str_replace(['{recipient_name}', '{sender_name}'], [$replacements['recipient_name'], $replacements['sender_name']], $subject);
    $message = str_replace(['{conversation_url_parameter}', '{recipient_name}', '{sender_name}', '{sender_profile_image}', '{message}', '{attachments}', '{conversation_link}'], ['conversation_url_parameter' => sb_isset($replacements, 'conversation_url_parameter', ''), $replacements['recipient_name'], $replacements['sender_name'],  $replacements['sender_profile_image'], $replacements['message'], $attachments_code, (SB_URL . '/admin.php' . (isset($replacements['conversation_id']) ? '?conversation=' . $replacements['conversation_id'] : ''))], $message);
    return [$subject, $message];
}

function sb_email_send($to, $subject, $message, $sender_suffix = '') {
    $settings = sb_get_setting('email-server');
    if (empty($to)) return false;
    if (file_exists(SB_PATH . '/vendor/phpmailer/PHPMailerAutoload.php') && $settings['email-server-host'] != '') {
        require_once SB_PATH . '/vendor/phpmailer/PHPMailerAutoload.php';
        $port = $settings['email-server-port'];
        $mail = new PHPMailer;
        $message = nl2br(trim(sb_text_formatting_to_html($message)));
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isSMTP();
        $mail->Host = $settings['email-server-host'];
        $mail->SMTPAuth = true;
        $mail->Username = $settings['email-server-user'];
        $mail->Password = $settings['email-server-password'];
        $mail->SMTPSecure = $port == 25 ? '' : ($port == 465 ? 'ssl' : 'tls');
        $mail->Port = $port;
        $mail->setFrom($settings['email-server-from'], sb_isset($settings, 'email-sender-name', '') . $sender_suffix);
        $mail->isHTML(true);
        $mail->Subject = trim($subject);
        $mail->Body    = $message;
        $mail->AltBody = $message;
        if (strpos($to, ',') > 0) {
            $emails = explode(',', $to);
            for ($i = 0; $i < count($emails); $i++) {
                $mail->addAddress($emails[$i]);
            }
        } else {
            $mail->addAddress($to);
        }
        if(!$mail->send()) {
            return new SBError('email-error', 'sb_email_send', $mail->ErrorInfo);
        } else {
            return true;
        }
    } else {
        return mail($to, $subject, $message);
    }
}

function sb_email($recipient_id, $message, $attachments = [], $sender_id = -1) {
    if ($recipient_id == false || $message == false || $message == '') {
        return new SBValidationError('missing-user-id-or-message');
    }
    if (!sb_email_security($recipient_id)) {
        return new SBError('security-error', 'sb_email');
    }
    $sender = $sender_id == -1 ? sb_get_active_user() : sb_get_user($sender_id);
    $user = sb_get_user($recipient_id);
    if ($sender != false && isset($sender['id']) && $user != false && isset($user['id'])) {
        if ($user['email'] == '') {
            return new SBValidationError('user-email-not-found');
        }
        $email_type = sb_is_agent($user['id']) ? 'agent' : 'user';
        $emails = sb_get_multilingual_setting('emails', 'email-' . $email_type, sb_get_user_language($recipient_id));
        $email = sb_email_create_content($emails['email-' . $email_type . '-subject'], $emails['email-' . $email_type . '-content'], $attachments, ['message' => $message, 'recipient_name' => sb_get_user_name($user), 'sender_name' => sb_get_user_name($sender), 'sender_profile_image' => $sender['profile_image']]);
        return sb_email_send($user['email'], $email[0], $email[1]);
    } else {
        return new SBError('user-or-sender-not-found', 'sb_email');
    }
}

function sb_email_send_test($to, $email_type) {
    $user = sb_get_active_user();
    $name = sb_get_user_name($user);
    $image = SB_URL . '/media/user.png';
    $attachments = [['Example link', $image], ['Example link two', $image]];
    $settings = sb_get_multilingual_setting('emails', 'email-' . $email_type);
    $email = sb_email_create_content($settings['email-' . $email_type . '-subject'], $settings['email-' . $email_type . '-content'], $attachments, ['message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam', 'recipient_name' => $name, 'sender_name' => $name, 'sender_profile_image' => $user['profile_image']]);
    return sb_email_send($to, $email[0], $email[1]);
}

function sb_email_security($user_id) {
    if (sb_is_agent() || !empty($GLOBALS['SB_FORCE_ADMIN'])) {
        return true;
    } else {
        $user = sb_db_get('SELECT user_type FROM sb_users WHERE id = ' . $user_id);
        return !sb_is_error($user) && isset($user['user_type']) && sb_is_agent($user['user_type']);
    }
}

function sb_email_piping($force = false) {
    if (!$force && date('i') == sb_get_external_setting('cron-email-piping')) return false;
    ignore_user_abort(true);
    set_time_limit(1000);
    sb_save_external_setting('cron-email-piping', date('i'));
    $settings = sb_get_setting('email-piping');
    if ($settings && !empty($settings['email-piping-active'])) {
        $port = $settings['email-piping-port'];
        $host = $settings['email-piping-host'];
        $all_emails = sb_isset($settings, 'email-piping-all');
        $today = date('d F Y');
        $last_check = sb_get_external_setting('email-piping-check');
        $inbox = imap_open('{' . $host . ':' . $port . '/' . ($port == 143 || $port == 993 ? 'imap' : 'pop3') . ($port == 995 || $port == 993 ? '/ssl' : '') . ($port == 995 ? '/novalidate-cert' : '') . '}INBOX', $settings['email-piping-user'], $settings['email-piping-password']);
        $attachments_path = sb_upload_path() . '/' . date('d-m-y') . '/';
        $attachments_url = sb_upload_path(true) . '/' . date('d-m-y') . '/';
        if ($inbox) {
            $emails = imap_search($inbox, 'ALL SINCE "' . (empty($last_check) ? $today : $last_check) . '"');
            if ($emails) {
                $history = sb_get_external_setting('email-piping-history', []);
                $history_new = [];
                rsort($emails);
                foreach ($emails as $email_number) {
                    $overview = imap_fetch_overview($inbox, $email_number, 0);
                    $to = $overview[0]->to;
                    $from = $overview[0]->from;
                    $follow_up = strpos($to, '| SB');
                    if ($all_emails || $follow_up) {
                        $conversation_id = false;
                        $id = false;
                        if ($follow_up) {
                            $conversation_id = substr($to, strpos($to, '| SB') + 4);
                            $conversation_id = substr($conversation_id, 0, strpos($conversation_id, '<') - 1);
                            $conversation_id = explode('-', $conversation_id);
                            $id = hash('sha1', $conversation_id[1] . $overview[0]->date);
                            $conversation_id = $conversation_id[0];
                        } else {
                            $id = hash('sha1', $from . $overview[0]->date);
                        }
                        if (!in_array($id, $history)) {
                            $from_email = mb_strpos($from, '<') ? trim(mb_substr($from, mb_strpos($from, '<') + 1, -1)) : $from;
                            $from_name = mb_strpos($from, '<') && mb_strpos($from, '=') === false && mb_strpos($from, '?') === false ? trim(mb_substr($from, 0, mb_strpos($from, '<'))) : '';
                            $sender = sb_db_get('SELECT * FROM sb_users WHERE email = "' . $from_email . '" LIMIT 1');
                            if (!$sender) {
                                $sender = sb_add_user(['email' => $from_email, 'first_name' => $from_name]);
                                $sender = sb_db_get('SELECT * FROM sb_users WHERE id = ' . $sender);
                            }
                            if ($sender && ($follow_up || !sb_is_agent($sender))) {
                                $message = imap_fetchbody($inbox, $email_number, 1);
                                $structure = imap_fetchstructure($inbox, $email_number);
                                $agent = $sender['user_type'] == 'agent' || $sender['user_type'] == 'admin';

                                // Message encoding
                                if (isset($structure->parts) && count($structure->parts)) {
                                    switch ($structure->parts[0]->encoding) {
                                        case 0:
                                            $message = imap_8bit($message);
                                            break;
                                        case 1:
                                            $message = imap_8bit($message);
                                            break;
                                        case 2:
                                            $message = imap_binary($message);
                                            break;
                                        case 3:
                                            $message = imap_base64($message);
                                            break;
                                        default:
                                            $message = quoted_printable_decode($message);
                                            break;
                                    }
                                }

                                // Message formatting
                                $position = mb_strpos($message, ' | SB');
                                if ($position) {
                                    $part_1 = mb_substr($message, 0, $position);
                                    $part_1 = mb_substr($part_1, 0, mb_strpos($part_1, PHP_EOL));
                                    $part_2 = mb_substr($message, $position);
                                    $part_2 = mb_substr($part_2, mb_strpos($part_2, PHP_EOL));
                                    $message =  $part_1 . $part_2;
                                }
                                $position = mb_strpos($message, $from_name . ' <');
                                if ($position) {
                                    $message = mb_substr($message, 0, sb_mb_strpos_reverse($message, PHP_EOL, $position));
                                }
                                $position = mb_strpos($message, 'Content-Type:');
                                if ($position) {
                                    $message = mb_substr($message, mb_strpos(mb_substr($message, $position), PHP_EOL) + $position);
                                    $position = mb_strpos($message, 'Content-Type:');
                                    if ($position) {
                                        $message = mb_substr($message, 0, $position);
                                    }
                                }
                                $position = mb_strpos($message, '______________________________');
                                if ($position) {
                                    $message = mb_substr($message, 0, $position);
                                }
                                $position = mb_strpos($message, 'Outlook');
                                if ($position) {
                                    $message = mb_substr($message, 0, mb_strrpos($message, "\n", $position * -1));
                                }
                                $position = mb_strpos($message, 'Content-Transfer-Encoding:');
                                $position_2 = mb_strpos($message, 'Content-Type: text/plain');
                                if ($position) {
                                    if ($position_2 && $position_2 < $position) {
                                        $message = mb_substr($message, mb_strpos($message, "\n", $position_2), mb_strpos($message, "\n", $position));
                                    } else {
                                        $message = mb_substr($message, mb_strpos($message, "\n", $position));
                                    }
                                    $position = mb_strpos($message, ': ');
                                    if ($position) {
                                        $position_2 = mb_strpos(mb_substr($message, $position + 2, mb_strpos($message, "\n", $position)), ':');
                                        if ($position_2) {
                                            $message = mb_substr($message, 0, mb_strrpos(mb_substr($message, 0, mb_strpos($message, "\n", $position ) - 2), "\n", $position * -1));
                                        }
                                    }
                                }
                                $strings_check = ['> wrote:', '--0'];
                                for ($i = 0; $i < count($strings_check); $i++) {
                                    if (mb_strpos($message, $strings_check[$i])) {
                                        $message = mb_substr($message, 0, sb_mb_strpos_reverse($message, PHP_EOL, mb_strpos($message, $strings_check[$i])));
                                    }
                                }
                                $message = str_replace(['wrote:' . PHP_EOL, 'wrote:'], '', $message);
                                if ($settings['email-piping-delimiter'] && mb_strpos($message, '### ')) {
                                    $message = str_replace('> ###', '###', $message);
                                    $message = mb_substr($message, 0, mb_strpos($message, '### '));
                                }
                                if (!empty($message)) {
                                    $message = preg_replace('/(<(script|style)\b[^>]*>).*?(<\/\2>)/is', "$1$3", $message);
                                    $message = strip_tags($message);
                                    $message = preg_replace("/\[image[\s\S]+?\]/", '', $message);
                                    $message = str_replace('&nbsp;', ' ', $message);
                                    if (mb_strpos($message, PHP_EOL . '> ')) {
                                        $message = mb_substr($message, 0, mb_strpos($message, PHP_EOL . '> ') - 2);
                                    }
                                    while (strpos($message, ' ' . PHP_EOL) !== false || strpos($message, PHP_EOL . ' ') !== false) {
                                        $message = str_replace([' ' . PHP_EOL, PHP_EOL . ' '], PHP_EOL, $message);
                                    }
                                    while (strpos($message, PHP_EOL . PHP_EOL . PHP_EOL) !== false) {
                                        $message = str_replace(PHP_EOL . PHP_EOL . PHP_EOL, PHP_EOL . PHP_EOL, $message);
                                    }
                                    $message = trim($message);
                                }

                                // Attachments
                                $attachments = [];
                                $attachments_2 = [];
                                if (isset($structure->parts) && count($structure->parts)) {
                                    for ($i = 0; $i < count($structure->parts); $i++) {
                                        $attachments[$i] = ['is_attachment' => false, 'filename' => '', 'attachment' => ''];
                                        if ($structure->parts[$i]->ifdparameters) {
                                            foreach($structure->parts[$i]->dparameters as $object) {
                                                if (strtolower($object->attribute) == 'filename') {
                                                    $attachments[$i]['is_attachment'] = true;
                                                    $attachments[$i]['filename'] = $object->value;
                                                }
                                            }
                                        }
                                        if ($structure->parts[$i]->ifparameters) {
                                            foreach($structure->parts[$i]->parameters as $object) {
                                                if (strtolower($object->attribute) == 'name') {
                                                    $attachments[$i]['is_attachment'] = true;
                                                    $attachments[$i]['filename'] = $object->value;
                                                }
                                            }
                                        }
                                        if ($attachments[$i]['is_attachment']) {
                                            $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i + 1);
                                            if ($structure->parts[$i]->encoding == 3) {
                                                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                                            } else if ($structure->parts[$i]->encoding == 4) {
                                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                                            }
                                        }
                                    }
                                }
                                if (count($attachments) && !file_exists($attachments_path)) mkdir($attachments_path, 0777, true);
                                for ($i = 0; $i < count($attachments); $i++) {
                                    if ($attachments[$i]['is_attachment']) {
                                        $file_name = rand(1000, 99999) . '_' . $attachments[$i]['filename'];
                                        sb_file($attachments_path . $file_name, $attachments[$i]['attachment']);
                                        array_push($attachments_2, [$attachments[$i]['filename'], $attachments_url . $file_name]);
                                    }
                                }

                                // Send message
                                if (!empty($message)) {
                                    $GLOBALS['SB_FORCE_ADMIN'] = true;
                                    if (!$follow_up) $conversation_id = sb_new_conversation($sender['id'])['details']['id'];
                                    sb_send_message($sender['id'], $conversation_id, $message, $attachments_2, ($agent ? 1 : 2));

                                    // Notifications
                                    $recipient = get_user_from_conversation($conversation_id, !$agent);
                                    if (isset($recipient['id']) && !sb_is_user_online($recipient['id'])) {
                                        if (($agent && sb_get_setting('notify-user-email')) || (!$agent && sb_get_setting('notify-agent-email'))) {
                                            sb_email_create($recipient['id'], sb_get_user_name($sender), $sender['profile_image'], $message, $attachments_2, false, $conversation_id);
                                        }
                                        if (($agent && sb_get_multi_setting('sms', 'sms-active-users')) || (!$agent && sb_get_multi_setting('sms', 'sms-active-agents'))) {
                                            $phone = sb_get_user_extra($recipient['id'], 'phone');
                                            if ($phone) {
                                                sb_send_sms($message, $phone, true, $conversation_id);
                                            }
                                        }
                                    } else if (!$follow_up && sb_get_setting('notify-agent-email')) {
                                        sb_send_agents_notifications('agents', $message, false, $conversation_id, $attachments_2);
                                    }
                                    $GLOBALS['SB_FORCE_ADMIN'] = false;
                                }
                                array_push($history_new, $id);
                            }
                        }
                    }
                }
                if ($last_check != $today) $history = [];
                sb_save_external_setting('email-piping-history', array_merge($history, $history_new));
            }
            if ($last_check != $today) {
                sb_save_external_setting('email-piping-check', $today);
            }
            imap_close($inbox);
            return true;
        }
        return new SBError('connection-error', 'sb_email_piping', imap_last_error());
    }
    return true;
}

function sb_subscribe_email($email) {
    $settings = sb_get_multilingual_setting('emails', 'email-subscribe');
    $subject = $settings['email-subscribe-subject'];
    $content = $settings['email-subscribe-content'];
    sb_reports_update('subscribe');
    if ($settings && !empty($subject) && !empty($content)) {
        return sb_email_send($email, sb_merge_fields($subject), sb_merge_fields($content));
    }
    return false;
}

function sb_email_default_parts($body, $user_id = false) {
    $lang = $user_id ? sb_get_user_language($user_id) : 'en';
    return sb_get_multilingual_setting('emails', 'email-header', $lang, '') . PHP_EOL . $body . PHP_EOL . sb_get_multilingual_setting('emails', 'email-signature', $lang, '');
}

function sb_text_formatting_to_html($message, $clear = false) {
    $regex = $clear ? [['/\*(.*?)\*/', '', ''], ['/__(.*?)__/', '', ''], ['/~(.*?)~/', '', ''], ['/```(.*?)```/', '', ''], ['/`(.*?)`/', '', '']] : [['/\*(.*?)\*/', '<b>', '</b>'], ['/__(.*?)__/', '<em>', '</em>'], ['/~(.*?)~/', '<del>', '</del>'], ['/```(.*?)```/', '<code>', '</code>'], ['/`(.*?)`/', '<code>', '</code>']];
    for ($i = 0; $i < count($regex); $i++) {
        $values = [];
        if (preg_match_all($regex[$i][0], $message, $values, PREG_SET_ORDER)) {
            for ($j = 0; $j < count($values); $j++){
                $message = str_replace($values[$j][0], $regex[$i][1] . $values[$j][1] . $regex[$i][2], $message);
            }
        }
    }
    return $message;
}

function sb_clear_text_formatting($message) {
    return sb_text_formatting_to_html($message, true);
}

// add_user, update_user, update_user_detail, followup, subscription, shortcode
function sb_newsletter($email, $first_name = '', $last_name = '') {
    $settings = sb_get_setting('newsletter');
    if ($settings['newsletter-active']) {
        $post_fields = '';
        $header = [];
        $url = false;
        $list_id = $settings['newsletter-list-id'];
        $key = $settings['newsletter-key'];
        $type = 'POST';
        switch ($settings['newsletter-service']) {
            case 'mailchimp':
                $url = 'https://' . substr($key, strpos($key, '-') + 1) . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/';
                $post_fields = ['email_address' => $email, 'status' => 'subscribed', 'merge_fields' => ['FNAME' => $first_name, 'LNAME' => $last_name]];
                $header = ['Content-Type: application/json', 'Authorization: Basic ' . base64_encode('user:' . $key)];
                break;
            case 'sendinblue':
                $url = 'https://api.sendinblue.com/v3/contacts';
                $post_fields = ['email' => $email, 'listIds' => [$list_id], 'updateEnabled' => false, 'attributes' => ['FIRSTNAME' => $first_name, 'LASTNAME' => $last_name]];
                $header = ['Accept: application/json', 'Content-Type: application/json', 'api-key: ' . $key];
                break;
            case 'sendgrid':
                $url = 'https://api.sendgrid.com/v3/marketing/contacts';
                $post_fields = ['list_ids' => [$list_id], 'contacts' => [['email' => $email, 'first_name' => $first_name, 'last_name' => $last_name]]];
                $header = ['Content-Type: application/json', 'Authorization: Bearer ' . $key];
                $type = 'PUT';
                break;
            case 'elasticemail':
                $url = 'https://api.elasticemail.com/v2/contact/add?email=' . $email .  '&publicAccountID=' . $key . '&listName=' . urlencode($list_id) . '&firstName=' . urlencode($first_name) . '&lastName=' . urlencode($last_name) . '&sendActivation=false';
                $type = 'GET';
                break;
            case 'campaignmonitor':
                $url = 'https://api.createsend.com/api/v3.2/subscribers/' . $list_id . '.json';
                $post_fields = ['EmailAddress' => $email, 'name' => $first_name . ' ' . $last_name, 'ConsentToTrack'=> 'Yes', 'Resubscribe' => true, 'RestartSubscriptionBasedAutoresponders' => true, 'CustomFields' => []];
                $header = ['Content-Type: application/json', 'Authorization: Basic ' . base64_encode($key)];
                break;
        }
        if ($url) {
            $response = sb_curl($url, json_encode($post_fields), $header, $type);
            return $response;
        }
    }
    return false;
}

/*
 * ----------------------------------------------------------
 * # INSTALLATION
 * ----------------------------------------------------------
 *
 * 1. Plugin installation function
 * 2. Update the config.php file
 * 3. Return the upload path or url
 * 4. Return the installation directory name
 *
 */

function sb_installation($details, $force = false) {
    $database = [];
    $not_cloud = !defined('SB_CLOUD');
    if (sb_db_check_connection() === true && !$force) {
        return true;
    }
    if (!isset($details['db-name']) || !isset($details['db-user']) || !isset($details['db-password']) || !isset($details['db-host'])) {
        return new SBValidationError('missing-details');
    } else {
        $database = ['name' => $details['db-name'][0], 'user' => $details['db-user'][0], 'password' => $details['db-password'][0], 'host' => $details['db-host'][0], 'port' => (isset($details['db-port']) && $details['db-port'][0] != '' ? intval($details['db-port'][0]) : ini_get('mysqli.default_port'))];
    }
    if ($not_cloud) {
        if (!isset($details['url'])) {
            return new SBValidationError('missing-url');
        } else if (substr($details['url'], -1) == '/') {
            $details['url'] = substr($details['url'], 0, -1);
        }
    }
    $connection_check = sb_db_check_connection($database['name'], $database['user'], $database['password'], $database['host'], $database['port']);
    $db_respones = [];
    $success = '';
    if ($connection_check === true) {

        // Create the database
        $connection = new mysqli($database['host'], $database['user'], $database['password'], $database['name'], $database['port']);
        $connection->set_charset('utf8mb4');
        $db_respones['users'] = $connection->query('CREATE TABLE IF NOT EXISTS sb_users (id INT NOT NULL AUTO_INCREMENT, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, password VARCHAR(100), email VARCHAR(255) UNIQUE, profile_image VARCHAR(255), user_type VARCHAR(10) NOT NULL, creation_time DATETIME NOT NULL, token VARCHAR(50) NOT NULL UNIQUE, last_activity DATETIME, typing INT DEFAULT -1, department TINYINT, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
        $db_respones['users_data'] = $connection->query('CREATE TABLE IF NOT EXISTS sb_users_data (id INT NOT NULL AUTO_INCREMENT, user_id INT NOT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, value TEXT NOT NULL, PRIMARY KEY (id), FOREIGN KEY (user_id) REFERENCES sb_users(id) ON DELETE CASCADE, UNIQUE INDEX sb_users_data_index (user_id, slug)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
        $db_respones['conversations'] = $connection->query('CREATE TABLE IF NOT EXISTS sb_conversations (id int NOT NULL AUTO_INCREMENT, user_id INT NOT NULL, title VARCHAR(255), creation_time DATETIME NOT NULL, status_code TINYINT DEFAULT 0, department TINYINT, agent_id INT, source VARCHAR(2), extra VARCHAR(255), PRIMARY KEY (id), FOREIGN KEY (agent_id) REFERENCES sb_users(id) ON DELETE CASCADE, FOREIGN KEY (user_id) REFERENCES sb_users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
        $db_respones['messages'] = $connection->query('CREATE TABLE IF NOT EXISTS sb_messages (id int NOT NULL AUTO_INCREMENT, user_id INT NOT NULL, message TEXT NOT NULL, creation_time DATETIME NOT NULL, status_code TINYINT DEFAULT 0, attachments TEXT, payload TEXT, conversation_id INT NOT NULL, PRIMARY KEY (id), FOREIGN KEY (user_id) REFERENCES sb_users(id) ON DELETE CASCADE, FOREIGN KEY (conversation_id) REFERENCES sb_conversations(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin');
        $db_respones['settings'] = $connection->query('CREATE TABLE IF NOT EXISTS sb_settings (name VARCHAR(255) NOT NULL, value LONGTEXT, PRIMARY KEY (name)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
        $db_respones['reports'] = $connection->query('CREATE TABLE IF NOT EXISTS sb_reports (id INT NOT NULL AUTO_INCREMENT, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, creation_time DATE NOT NULL, external_id INT, extra VARCHAR(255), PRIMARY KEY (id), UNIQUE INDEX sb_reports_data_index (name, value, creation_time)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        // Create the admin user
        if (isset($details['first-name']) && isset($details['last-name']) && isset($details['email']) && isset($details['password'])) {
            $now = gmdate('Y-m-d H:i:s');
            $token = bin2hex(openssl_random_pseudo_bytes(20));
            $db_respones['admin'] = $connection->query('INSERT IGNORE INTO sb_users(first_name, last_name, password, email, profile_image, user_type, creation_time, token, last_activity) VALUES ("' . sb_db_escape($details['first-name'][0]) . '", "' . sb_db_escape($details['last-name'][0]) . '", "' . sb_db_escape(defined('SB_WP') ? $details['password'][0] : password_hash($details['password'][0], PASSWORD_DEFAULT)) . '", "' . $details['email'][0] . '", "' . $details['url'] . '/media/user.svg' . '", "admin", "' . $now . '", "' . $token . '", "' . $now . '")');
        }

        // Create the config.php file
        if ($not_cloud) {
            $raw = file_get_contents(SB_PATH . '/resources/config-source.php');
            $raw = str_replace(['[url]', '[name]', '[user]', '[password]', '[host]', '[port]'], [$details['url'], $database['name'], $database['user'], $database['password'], $database['host'], (isset($details['db-port']) && $details['db-port'][0] != '' ? $database['port'] : '')], $raw);
            if (defined('SB_WP')) {
                $raw = str_replace('/* [extra] */', sb_wp_config(), $raw);
            }
            sb_file(SB_PATH . '/config.php', $raw);
        }

        // Return
        sb_get('https://board.support/synch/index.php?site=' . $details['url']);
        foreach ($db_respones as $key => $value) {
            if ($value !== true) {
                $success .= $key . ': ' . ($value === false ? 'false' : $value) . ',';
            }
        }
        if ($success == '') {
            return true;
        } else {
            return substr($success, 0, -1);
        }
    } else {
        return $connection_check;
    }
}

function sb_write_config_extra($content) {
    $raw = file_get_contents(SB_PATH . '/config.php');
    sb_file(SB_PATH . '/config.php', str_replace('?>', $content . PHP_EOL . PHP_EOL . '?>', $raw));
}

function sb_upload_path($url = false) {
    return defined('SB_UPLOAD_PATH') && SB_UPLOAD_PATH != '' && defined('SB_UPLOAD_URL') && SB_UPLOAD_URL != '' ? ($url ? SB_UPLOAD_URL : SB_UPLOAD_PATH) : ($url ? SB_URL : SB_PATH) . '/uploads';
}

function sb_dir_name() {
    return substr(SB_URL, strrpos(SB_URL, '/') + 1);
}

/*
 * ----------------------------------------------------------
 * # APPS AND UPDATES
 * ----------------------------------------------------------
 *
 * 1. Get the plugin and apps versions and install, activate and update apps
 * 2. Check if the app license is valid and install or update it
 * 3. Install or update an app
 * 4. Update Support Board and all apps
 * 5. Compatibility function for new versions
 * 6. Check if there are updates available
 *
 */

function sb_get_versions() {
    return json_decode(sb_download('https://board.support/synch/versions.json'), true);
}

function sb_app_get_key($app_name) {
    $keys = sb_get_external_setting('app-keys');
    return isset($keys[$app_name]) ? $keys[$app_name] : '';
}

function sb_app_activation($app_name, $key) {
    $key = trim($key);
    $response = json_decode(sb_download('https://board.support/synch/updates.php?' . $app_name . '=' . $key), true);
    if (!isset($response[$app_name]) || $response[$app_name] == '') {
        return new SBValidationError('invalid-key');
    }
    if ($response[$app_name] == 'expired') {
        return new SBValidationError('expired');
    }
    return sb_app_update($app_name, $response[$app_name], $key);
}

function sb_app_update($app_name, $file_name, $key = false) {
    if ($file_name == '') {
        return new SBValidationError('temporary-file-name-not-found');
    }
    $key = trim($key);
    $error = '';
    $zip = sb_download('http://board.support/synch/temp/' . $file_name);
    if ($zip != false) {
        $file_path = SB_PATH . '/uploads/' . $app_name . '.zip';
        file_put_contents($file_path, $zip);
        if (file_exists($file_path)) {
            $zip = new ZipArchive;
            if ($zip->open($file_path) === true) {
                $zip->extractTo($app_name == 'sb' ? (defined('SB_WP') ? substr(SB_PATH, 0, -13) : SB_PATH) : SB_PATH . '/apps');
                $zip->close();
                unlink($file_path);
                if ($app_name == 'sb') {
                    sb_restore_user_translations();
                    return 'success';
                }
                if (file_exists(SB_PATH . '/apps/' . $app_name)) {
                    if (!empty($key)) {
                        $keys = sb_get_external_setting('app-keys');
                        $keys[$app_name] = $key;
                        sb_save_external_setting('app-keys', $keys);
                    }
                    return 'success';
                } else {
                    $error = 'zip-extraction-error';
                }
            } else {
                $error = 'zip-error';
            }
        } else {
            $error = 'file-not-found';
        }
    } else {
        $error = 'download-error';
    }
    if ($error != '') {
        return new SBValidationError($error);
    }
    return false;
}

function sb_update() {
    $envato_code = sb_get_setting('envato-purchase-code');
    if ($envato_code == '') {
        return new SBValidationError('envato-purchase-code-not-found');
    }
    $latest_versions = sb_get_versions();
    $installed_apps_versions = ['dialogflow' => defined('SB_DIALOGFLOW') ? SB_DIALOGFLOW : -1, 'slack' => defined('SB_SLACK') ? SB_SLACK : -1, 'tickets' => defined('SB_TICKETS') ? SB_TICKETS : -1, 'woocommerce' => defined('SB_WOOCOMMERCE') ? SB_WOOCOMMERCE : -1, 'ump' => defined('SB_UMP') ? SB_UMP : -1, 'perfex' => defined('SB_PERFEX') ? SB_PERFEX : -1, 'whmcs' => defined('SB_WHMCS') ? SB_WHMCS : -1, 'aecommerce' => defined('SB_AECOMMERCE') ? SB_AECOMMERCE : -1, 'messenger' => defined('SB_MESSENGER') ? SB_MESSENGER : -1, 'whatsapp' => defined('SB_WHATSAPP') ? SB_WHATSAPP : -1, 'armember' => defined('SB_ARMEMBER') ? SB_ARMEMBER : -1];
    $keys = sb_get_external_setting('app-keys');
    $result = [];
    $link = SB_VERSION != $latest_versions['sb'] ? 'sb=' . trim($envato_code) . '&' : '';
    foreach ($installed_apps_versions as $key => $value) {
        if ($value != -1 && $value != $latest_versions[$key]) {
            if (isset($keys[$key])) {
                $link .= $key . '=' . trim($keys[$key]) . '&';
            } else {
                $result[$key] = 'license-key-not-found';
            }
        }
    }
    $downloads = json_decode(sb_download('https://board.support/synch/updates.php?' . substr($link, 0, -1)), true);
    if (empty($downloads)) return new SBValidationError('envato-purchase-code-not-found');
    foreach ($downloads as $key => $value) {
        if ($value != '') {
            $result[$key] = sb_app_update($key, $value);
        }
    }
    return $result;
}

function sb_updates_validation() {
    if (!isset($_COOKIE['sb-updates']) || $_COOKIE['sb-updates'] != SB_VERSION) {

        // Support Board 3.2.3 | 03-2021
        sb_db_query('ALTER TABLE sb_conversations ADD COLUMN `source` VARCHAR(2)');
        sb_db_query('ALTER TABLE sb_conversations ADD COLUMN `extra` VARCHAR(255)');

        // Support Board 3.2.0 | 03-2021
        sb_db_query('ALTER TABLE sb_reports DROP INDEX sb_reports_data_index');
        sb_db_query('ALTER TABLE sb_reports ADD UNIQUE INDEX sb_reports_data_index (name, value, creation_time, extra)');

        // Support Board 3.1.8 | 02-2021
        sb_db_query('ALTER TABLE sb_settings MODIFY COLUMN value LONGTEXT');

        // Support Board 3.1.7 | 01-2021
        sb_db_query('CREATE TABLE IF NOT EXISTS sb_reports (id INT NOT NULL AUTO_INCREMENT, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, creation_time DATE NOT NULL, external_id INT, extra VARCHAR(255), PRIMARY KEY (id), UNIQUE INDEX sb_reports_data_index (name, value, creation_time)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        // Save the cookie for 1 year
        if (!headers_sent()) {
            setcookie('sb-updates', SB_VERSION, time() + 31556926, '/');
        }
    }
}

function sb_updates_available() {
    $latest_versions = sb_get_versions();
    if (SB_VERSION != $latest_versions['sb']) return true;
    $installed_apps_versions = ['dialogflow' => defined('SB_DIALOGFLOW') ? SB_DIALOGFLOW : -1, 'slack' => defined('SB_SLACK') ? SB_SLACK : -1, 'tickets' => defined('SB_TICKETS') ? SB_TICKETS : -1, 'woocommerce' => defined('SB_WOOCOMMERCE') ? SB_WOOCOMMERCE : -1, 'ump' => defined('SB_UMP') ? SB_UMP : -1, 'perfex' => defined('SB_PERFEX') ? SB_PERFEX : -1, 'whmcs' => defined('SB_WHMCS') ? SB_WHMCS : -1, 'aecommerce' => defined('SB_AECOMMERCE') ? SB_AECOMMERCE : -1, 'messenger' => defined('SB_MESSENGER') ? SB_MESSENGER : -1, 'whatsapp' => defined('SB_WHATSAPP') ? SB_WHATSAPP : -1, 'armember' => defined('SB_ARMEMBER') ? SB_ARMEMBER : -1];
    foreach ($installed_apps_versions as $key => $value) {
        if ($value != -1 && $value != $latest_versions[$key]) {
            return true;
        }
    }
    return false;
}

/*
 * ----------------------------------------------------------
 * # WEBHOOKS
 * ----------------------------------------------------------
 *
 * Send all the webhooks functions to the destination URL
 *
 */

function sb_webhooks($function_name, $parameters) {
    if (isset($function_name) && $function_name != '' && isset($parameters)) {
        $names = ['SBSMSSent' => 'sms-sent', 'SBLoginForm' => 'login', 'SBRegistrationForm' => 'registration', 'SBUserDeleted' => 'user-deleted', 'SBMessageSent' => 'message-sent', 'SBBotMessage' => 'bot-message', 'SBEmailSent' => 'email-sent', 'SBNewMessagesReceived' => 'new-message', 'SBNewConversationReceived' => 'new-conversation', 'SBNewConversationCreated' => 'new-conversation-created', 'SBActiveConversationStatusUpdated' => 'conversation-status-updated', 'SBSlackMessageSent' => 'slack-message-sent', 'SBMessageDeleted' => 'message-deleted',  'SBRichMessageSubmit' => 'rich-message', 'SBNewEmailAddress' => 'new-email-address'];
        if (isset($names[$function_name])) {
            $webhooks = sb_get_setting('webhooks');
            if ($webhooks !== false && $webhooks['webhooks-url'] != '' && $webhooks['webhooks-active']) {
                $query = json_encode(['function' => $names[$function_name], 'key' =>$webhooks['webhooks-key'], 'data' => $parameters, 'sender-url' => (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '')]);
                if ($query != false) {
                    return sb_curl($webhooks['webhooks-url'], $query, [ 'Content-Type: application/json', 'Content-Length: ' . strlen($query)]);
                } else {
                    return new SBError('webhook-json-error');
                }
            } else {
                return new SBValidationError('webhook-not-active-or-empty-url');
            }
        } else {
            return new SBValidationError('webhook-not-found');
        }
    } else {
        return new SBError('invalid-function-or-parameters');
    }
}

/*
 * ----------------------------------------------------------
 * # PUSHER
 * ----------------------------------------------------------
 *
 * 1. Send a Push notification.
 * 2. Trigger a event on a channel
 * 3. Get all online users including admins and agents
 * 4. Check if there is at least one agent online
 * 5. Initialize the Pusher PHP SDK
 *
 */

function sb_push_notification($title = '', $message = '', $icon = '', $interest = false, $conversation_id = false) {
    $recipient_agent = false;
    if ($interest == 'agents' || strpos($interest, 'department-') !== false) {
        $agents = sb_db_get('SELECT id FROM sb_users WHERE (user_type = "admin" OR user_type = "agent") AND ' . ($interest == 'agents' ? 'department IS NULL OR department = ""' : ' department = ' . substr($interest, 11)), false);
        $interest = [];
        for ($i = 0; $i < count($agents); $i++) {
            array_push($interest, $agents[$i]['id']);
        }
        $recipient_agent = true;
    } else if (is_numeric($interest) || is_array($interest)) {
        $agents_ids = sb_get_agents_ids();
        $is_user = !sb_is_agent();
        if (is_numeric($interest)) {
            if (!in_array(intval($interest), $agents_ids)) {
                if ($is_user && empty($GLOBALS['SB_FORCE_ADMIN'])) return new SBError('security-error', 'sb_push_notification');
            } else $recipient_agent = true;
        } else {
            for ($i = 0; $i < count($interest); $i++){
                if (!in_array(intval($interest[$i]), $agents_ids)) {
                    if ($is_user && empty($GLOBALS['SB_FORCE_ADMIN'])) return new SBError('security-error', 'sb_push_notification');
                } else $recipient_agent = true;
            }
        }
    } else if ($interest == 'all-agents') $interest == 'agents';
    if (empty($icon)) {
        $icon = sb_get_setting('notifications-icon');
        if ($icon == '') {
            $icon = SB_URL . '/media/icon.png';
        }
    }
    if (sb_is_agent() && !$recipient_agent) {
        $link = $conversation_id ? sb_isset(sb_db_get('SELECT B.value FROM sb_conversations A, sb_users_data B WHERE A.id = ' . $conversation_id . ' AND A.user_id = B.user_id AND B.slug = "current_url" LIMIT 1'), 'value', '') : false;
    } else {
        $link = SB_URL . '/admin.php';
    }
    $instance_ID = sb_get_multi_setting('push-notifications', 'push-notifications-id');
    $query = ',"web":{"notification":{"title":"' . str_replace('"', '', $title) . '","body":"' . str_replace('"', '', sb_clear_text_formatting(preg_replace('/\r|\n/', ' ', $message))) . '","icon":"' . $icon . '"' . (empty($link) ? '' : ',"deep_link":"' . $link . '"' ) . ',"hide_notification_if_site_has_focus":true}, "data": {"conversation_id":"' . $conversation_id . '","user_id":"' . sb_get_active_user_ID() . '"}}}';
    $url = 'https://' . $instance_ID . '.pushnotifications.pusher.com/publish_api/v1/instances/' . $instance_ID . '/publishes';
    $header = [ 'Content-Type: application/json', 'Authorization: Bearer ' . sb_get_multi_setting('push-notifications', 'push-notifications-key') ];
    if (empty($interest)) return false;
    if (is_array($interest) && count($interest) > 100) {
        $interests = [];
        $index = 0;
        $count = count($interest);
        for ($i = 0; $i < $count; $i++) {
            array_push($interests, $interest[$i]);
            $index++;
            if ($index == 100 || $i == $count - 1) {
                $response = sb_curl($url, '{"interests":' . json_encode($interests) . $query, $header);
                $interests = [];
                $index = 0;
            }
        }
    } else {
        $response = sb_curl($url, '{"interests":' . (is_array($interest) ? json_encode($interest) : '["' . str_replace(' ', '', $interest) . '"]') . $query, $header);
    }
    return isset($response['error']) ? trigger_error($response['description']) : $response;
}

function sb_pusher_trigger($channel, $event, $data = []) {
    $pusher = sb_pusher_init();
    $user_id = sb_get_active_user_ID();
    $data['user_id'] = $user_id;
    $security = false;
    switch ($event){
        case 'add-user-presence':
        case 'init':
        case 'new-message':
        case 'typing': {
                if (sb_is_agent() || $channel == 'private-user-' . $user_id) {
                    $security = true;
                }
            }
        case 'update-conversations':
            if ($user_id) $security = true;
    }
    if ($security) {
        if (is_array($channel) && count($channel) > 100) {
            $channels = [];
            $index = 0;
            $count = count($channel);
            for ($i = 0; $i < $count; $i++) {
                array_push($channels, $channel[$i]);
                $index++;
                if ($index == 100 || $i == $count - 1) {
                    $response = $pusher->trigger($channels, $event, $data);
                    $channels = [];
                    $index = 0;
                }
            }
            return $response;
        } else {
            return $pusher->trigger($channel, $event, $data);
        }
    }
    return new SBError('pusher-security-error', 'sb_pusher_trigger');
}

function sb_pusher_get_online_users() {
    $index = 1;
    $pusher = sb_pusher_init();
    $continue = true;
    $users = [];
    while ($continue) {
        $channel = $pusher->get_users_info('presence-' . $index);
        if (!empty($channel)) {
            $channel = $channel->users;
            $users = array_merge($users, $channel);
            if (count($channel) > 98) {
                $continue = true;
                $index++;
            } else $continue = false;
        } else $continue = false;
    }
    return $users;
}

function sb_pusher_agents_online() {
    $agents_id = sb_get_agents_ids();
    $users = sb_pusher_get_online_users();
    for ($i = 0; $i < count($users); $i++) {
        if (in_array($users[$i]->id, $agents_id)) {
            return true;
        }
    }
    return false;
}

function sb_pusher_active() {
    return sb_get_multi_setting('pusher', 'pusher-active');
}

function sb_pusher_init() {
    require_once SB_PATH . '/vendor/pusher/autoload.php';
    $settings = sb_get_setting('pusher');
    return new Pusher\Pusher($settings['pusher-key'], $settings['pusher-secret'], $settings['pusher-id'], ['cluster' => $settings['pusher-cluster']]);
}

/*
 * ----------------------------------------------------------
 * # CRON JOBS
 * ----------------------------------------------------------
 *
 * 1. Run the cron jobs
 * 2. Add a new cron job
 *
 */

function sb_cron_jobs() {
    ignore_user_abort(true);
    set_time_limit(180);
    $now = date('H');
    $cron_functions = sb_get_external_setting('cron-functions');
    if (defined('SB_WOOCOMMERCE')) {
        sb_woocommerce_cron_jobs($cron_functions);
    }
    if (defined('SB_AECOMMERCE')) {
        sb_aecommerce_clean_carts();
    }
    sb_clean_data();
    sb_db_query('DELETE FROM sb_settings WHERE name="cron-functions"');
    sb_save_external_setting('cron', $now);
}

function sb_cron_jobs_add($key, $content = false, $job_time = false) {

    // Add the job to the cron jobs
    $cron_functions = sb_get_external_setting('cron-functions');
    if (empty($cron_functions) || empty($cron_functions['value'])) {
        sb_save_external_setting('cron-functions', [$key]);
    } else {
        $cron_functions = json_decode($cron_functions['value'], true);
        if (!in_array($key, $cron_functions)) {
            array_push($cron_functions, $key);
            sb_db_query('UPDATE sb_settings SET value = \'' . sb_db_json_enconde($cron_functions) . '\' WHERE name = "cron-functions"');
        }
    }

    // Set the cron job data
    if (!empty($content) && !empty($job_time)) {
        $user = sb_get_active_user();
        if ($user) {
            $key = 'cron-' . $key;
            $scheduled = sb_get_external_setting($key);
            if (empty($scheduled)) {
                $scheduled = [];
            }
            $scheduled[$user['id']] = [$content, strtotime('+' . $job_time)];
            sb_save_external_setting($key, $scheduled);
        }
    }
}

/*
 * -----------------------------------------------------------
 * # UTILITY
 * -----------------------------------------------------------
 *
 * 1. Check if a value and key of an array exists and is not empty and return it
 * 2. Check if a number and key of an array exists and is not empty and return it
 * 3. Encrypt a string or decrypt an encrypted string
 * 4. Convert a string to a slug or a slug to a string
 * 5. Send a curl request
 * 6. Return the content of a URL as a string
 * 7. Return the content of a URL as a string via GET
 * 8. Create a CSV file from an array
 * 9. Create a new file containing the given content and save it in the destination path.
 * 10. Delete a file
 * 11. Debug function
 * 12. Convert a JSON string to an array
 *
 */

function sb_isset($array, $key, $default = false) {
    if (sb_is_error($array) || sb_is_validation_error($array)) return $array;
    return !empty($array) && isset($array[$key]) && $array[$key] !== '' ? $array[$key] : $default;
}

function sb_isset_num($value) {
    return $value != -1 && $value != '' && !is_null($value) && !is_bool($value);
}

function sb_encryption($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = defined('SB_CLOUD_KEY') ? SB_CLOUD_KEY : sb_get_setting('envato-purchase-code', 'supportboard');
    $secret_iv = 'supportboard_iv';
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
        if (substr($output, -1) == '=') $output = substr($output, 0, -1);
    } else if ($action == 'decrypt'){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        if ($output === false && $secret_key != 'supportboard') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, hash('sha256', 'supportboard'), 0, $iv);
        }
    }
    return $output;
}

function sb_string_slug($string, $action = 'slug') {
    $string = trim($string);
    if ($action == 'slug') {
        return strtolower(str_replace(' ', '-', $string));
    } else if ($action == 'string') {
        return ucfirst(strtolower(str_replace('-', ' ', $string)));
    }
    return $string;
}

function sb_curl($url, $post_fields = '', $header = [], $type = 'POST') {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'SB');
    switch ($type) {
        case 'PUT':
        case 'PATCH':
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($post_fields) ? $post_fields : http_build_query($post_fields));
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 7);
            if ($type == 'PATCH') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            }
            if ($type == 'PUT') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            }
            break;
        case 'GET':
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt($ch, CURLOPT_TIMEOUT, 70);
            curl_setopt($ch, CURLOPT_HEADER, false);
            break;
        case 'DOWNLOAD':
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt($ch, CURLOPT_TIMEOUT, 70);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            break;
        case 'FILE':
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            curl_setopt($ch, CURLOPT_TIMEOUT, 400);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $path = sb_upload_path() . '/' . date('d-m-y');
            if (!file_exists($path)) mkdir($path, 0777, true);
            if (strpos($url, '?')) $url = substr($url, 0, strpos($url, '?'));
            $file = fopen($path . '/' . basename($url), 'wb');
            curl_setopt($ch, CURLOPT_FILE, $file);
            break;
    }
    if (!empty($header)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    $response = curl_exec($ch);
    if (curl_errno($ch) > 0) {
        $error = curl_error($ch);
        curl_close($ch);
        return $error;
    }
    curl_close($ch);
    switch ($type) {
        case 'PATCH':
        case 'POST':
            $response = json_decode($response, true);
            if (JSON_ERROR_NONE !== json_last_error()) $response = json_last_error_msg();
            break;
        case 'FILE':
            return sb_upload_path(true) . '/' . date('d-m-y') . '/' . basename($url);
    }
    return $response;
}

function sb_download($url) {
    return sb_curl($url, '', '', 'DOWNLOAD');
}

function sb_download_file($url, $file_name = false) {
    $url = sb_curl($url, '', '', 'FILE');
    if ($file_name && !sb_is_error($url) && !empty($url)) {
        $date = date('d-m-y');
        $path = sb_upload_path() . '/' . $date;
        rename($path . '/' . basename($url), $path . '/' . $file_name);
        $url = sb_upload_path(true) . '/' . $date .  '/' . $file_name;
    }
    return $url;
}

function sb_get($url) {
    return sb_curl($url, '', '', 'GET');
}

function sb_csv($items, $header, $filename) {
    $file = fopen(sb_upload_path() . '/' . $filename . '.csv', 'w');
    if (isset($header)) {
        fputcsv($file, $header);
    }
    for ($i = 0; $i < count($items); $i++) {
    	fputcsv($file, $items[$i]);
    }
    fclose($file);
    return sb_upload_path(true) . '/' . $filename . '.csv?v=' . rand(1, 100);
}

function sb_file($path, $content) {
    try {
        $file = fopen($path, 'w');
        fwrite($file, $content);
        fclose($file);
        return true;
    }
    catch (Exception $e) {
        return $e->getMessage();
    }
}

function sb_file_delete($path) {
    if (strpos($path, 'http') !== false) {
        $path = SB_PATH . '\uploads\\' . basename($path);
    }
    return unlink($path);
}

function sb_debug($value) {
    $value = is_string($value) ? $value : json_encode($value);
    if (file_exists('debug.txt')) {
        $value = file_get_contents('debug.txt') . PHP_EOL . $value;
    }
    sb_file('debug.txt', $value);
}

function sb_json_array($json, $default = []) {
    if (is_string($json)) {
        $json = json_decode($json, true);
        return $json === false || $json === null ? $default : $json;
    } else {
        return $json;
    }
}

/*
 * -----------------------------------------------------------
 * # MISCELLANEOUS
 * -----------------------------------------------------------
 *
 * 1. Return the departments array
 * 2. Echo the departments list
 * 3. Check if the current time is within the office hours
 * 4. Generate the CSS with values setted in the settings area
 * 5. Delete visitors older than 24h, messages in trash older than 30 days. Archive conversation older than 24h with status code equal to 4 (pending user reply).
 * 6. Check the system for requirements and issues
 * 7. Chat editor
 * 8. Escape and sanatize a string
 * 9. Return the position of the least occurence on left searching from right to left
 *
 */

function sb_get_departments() {
    $items = sb_get_setting('departments');
    $count = is_array($items) ? count($items) : 0;
    $departments = [];
    if ($count) {
        for ($i = 0; $i < $count; $i++) {
            $departments[$items[$i]['department-id']] = ['name' => sb_($items[$i]['department-name']), 'color' => $items[$i]['department-color'], 'image' => (empty($items[$i]['department-image']) ? '' : $items[$i]['department-image'])];
        }
    }
    return $departments;
}

function sb_departments($type) {
    $items = sb_get_setting('departments');
    $count = is_array($items) ? count($items) : 0;
    if ($count) {
        switch ($type) {
            case 'select':
                $code = '<div id="department" data-type="select" class="sb-input sb-input-select"><span>' . sb_('Department') . '</span><select><option value=""></option>';
                for ($i = 0; $i < $count; $i++) {
                    $code .= '<option value="' . $items[$i]['department-id'] . '">' . ucfirst(sb_($items[$i]['department-name'])) . '</option>';
                }
                echo $code . '</select></div>';
                break;
            case 'custom-select':
                $code = '<div class="sb-inline sb-inline-departments"><h3>' . sb_('Department') . '</h3><div id="conversation-department" class="sb-select sb-select-colors"><p>' . sb_('General') . '</p><ul><li data-id="" data-value="">' . sb_('General') . '</li>';
                for ($i = 0; $i < $count; $i++) {
                    $code .= '<li data-id="' . $items[$i]['department-id'] . '" data-value="' . (isset($items[$i]['department-color']) ? $items[$i]['department-color'] : '') . '">' . ucfirst(sb_($items[$i]['department-name'])) . '</li>';
                }
                echo $code . '</ul></div></div>';
                break;
            case 'dashboard':
                $settings = sb_get_setting('departments-settings');
                if ($settings != false) {
                    $is_image = sb_isset($settings, 'departments-images') && sb_isset($items[0],'department-image') != '';
                    $code = '<div class="sb-dashboard-departments"><div class="sb-title">' .  sb_(sb_isset($settings, 'departments-title', 'Departments')) . '</div><div class="sb-departments-list">';
                    for ($i = 0; $i < $count; $i++) {
                        $code .= '<div data-id="' . $items[$i]['department-id'] . '">' . ($is_image ? '<img src="' . $items[$i]['department-image'] . '">' : '<div data-color="' . sb_isset($items[$i], 'department-color') . '"></div>') . '<span>' . sb_($items[$i]['department-name']) . '</span></div>';
                    }
                    echo $code . '</div></div>';
                    break;
                }
        }
    }
}

function sb_office_hours() {
    $settings = sb_get_settings();
    $timetable = sb_isset($settings, 'timetable', [[]])[0];
    $today = strtolower(gmdate('l'));
    if (isset($timetable[$today]) && $timetable[$today][0][0] != '') {
        $now_hours = intval(gmdate('H'));
        $now_minutes = intval(gmdate('i'));
        $status = false;
        $offset = intval(sb_get_setting('timetable-utc', 0));
        for ($i = 0; $i < 3; $i+=2){
            if ($timetable[$today][$i][0] != '' && $timetable[$today][$i][0] != 'closed' && $timetable[$today][$i + 1][0] != '') {
                $hours_start = intval(explode(':', $timetable[$today][$i][0])[0]) + $offset;
                $minutes_start = intval(explode(':',$timetable[$today][$i][0])[1]);
                $hours_end = intval(explode(':', $timetable[$today][$i + 1][0])[0]) + $offset;
                $minutes_end = intval(explode(':', $timetable[$today][$i + 1][0])[1]);
                if (($now_hours > $hours_start || ($now_hours == $hours_start && $now_minutes >= $minutes_start)) && ($now_hours < $hours_end || ($now_hours == $hours_end && $now_minutes <= $minutes_end))) {
                    $status = true;
                    break;
                }
            }
        }
        return $status;
    }
    return true;
}

function sb_css($color_1 = false, $color_2 = false, $color_3 = false, $return = false) {
    $css = '';
    $color_1 = $color_1 ? $color_1 : sb_get_setting('color-1');
    $color_2 = $color_2 ? $color_2 : sb_get_setting('color-2');
    $color_3 = $color_3 ? $color_3 : sb_get_setting('color-3');
    $chat_button_offset_top = sb_get_multi_setting('chat-button-offset', 'chat-button-offset-top');
    $chat_button_offset_bottom = sb_get_multi_setting('chat-button-offset', 'chat-button-offset-bottom');
    $chat_button_offset_right = sb_get_multi_setting('chat-button-offset', 'chat-button-offset-right');
    $chat_button_offset_left = sb_get_multi_setting('chat-button-offset', 'chat-button-offset-left');
    $chat_button_offset_left_mobile = sb_get_multi_setting('chat-button-offset', 'chat-button-offset-mobile');
    $chat_button_offset_left_mobile = $chat_button_offset_left_mobile == 'desktop' ? ['@media (min-width: 768px) {', '}'] : ($chat_button_offset_left_mobile == 'mobile' ? ['@media (max-width: 768px) {', '}'] : ['', '']);
    if ($color_1 != '') {
        $css .= '.sb-chat-btn, .sb-chat>div>.sb-header,.sb-chat .sb-dashboard>div>.sb-btn:hover,.sb-chat .sb-scroll-area .sb-header,.sb-input.sb-input-btn>div,div ul.sb-menu li:hover,
                 .sb-select ul li:hover,.sb-popup.sb-emoji .sb-emoji-bar>div.sb-active, .sb-popup.sb-emoji .sb-emoji-bar>div:hover,.sb-btn,a.sb-btn,.sb-rich-message[disabled] .sb-buttons .sb-btn { background-color: ' . $color_1 . '; }';
        $css .= '.sb-chat .sb-dashboard>div>.sb-btn,.sb-search-btn>input,.sb-input>input:focus, .sb-input>select:focus, .sb-input>textarea:focus,.sb-input.sb-input-image .image:hover { border-color: ' . $color_1 . '; }';
        $css .= '.sb-chat .sb-dashboard>div>.sb-btn,.sb-editor .sb-bar-icons>div:hover:before,.sb-articles>div:hover>div,.sb-main .sb-btn-text:hover,.sb-editor .sb-submit,
                 .sb-select p:hover,div ul.sb-menu li.sb-active, .sb-select ul li.sb-active,.sb-search-btn>i:hover,.sb-search-btn.sb-active i,.sb-rich-message .sb-input>span.sb-active:not(.sb-filled),
                 .sb-input.sb-input-image .image:hover:before,.sb-rich-message .sb-card .sb-card-btn,.sb-slider-arrow:hover,.sb-loading:before,.sb-articles>div.sb-title { color: ' . $color_1 . '; }';
        $css .= '.sb-search-btn>input:focus,.sb-input>input:focus, .sb-input>select:focus, .sb-input>textarea:focus,.sb-input.sb-input-image .image:hover { box-shadow: 0 0 5px rgba(104, 104, 104, 0.2); }';
        $css .= '.sb-list>div.sb-rich-cnt { border-top-color: ' . $color_1 . '; }';
    }
    if ($color_2 != '') {
        $css .= '.sb-chat-btn:hover,.sb-input.sb-input-btn>div:hover,.sb-btn:hover,a.sb-btn:hover,.sb-rich-message .sb-card .sb-card-btn:hover { background-color: ' . $color_2 . '; }';
        $css .= '.sb-list>.sb-right .sb-message, .sb-list>.sb-right .sb-message a,.sb-editor .sb-submit:hover { color: ' . $color_2 . '; }';
    }
    if ($color_3 != '') {
        $css .= '.sb-list>.sb-right,.sb-user-conversations>li:hover { background-color: ' . $color_3 . '; }';
    }
    if ($chat_button_offset_top) {
        $css .= $chat_button_offset_left_mobile[0] . '.sb-chat-btn { top: ' . $chat_button_offset_top . 'px; }' . $chat_button_offset_left_mobile[1];
    }
    if ($chat_button_offset_bottom) {
        $css .= $chat_button_offset_left_mobile[0] . '.sb-chat-btn { bottom: ' . $chat_button_offset_bottom . 'px; }' . $chat_button_offset_left_mobile[1];
    }
    if ($chat_button_offset_right) {
        $css .= $chat_button_offset_left_mobile[0] . '.sb-chat-btn { right: ' . $chat_button_offset_right . 'px; }' . $chat_button_offset_left_mobile[1];
    }
    if ($chat_button_offset_left) {
        $css .= $chat_button_offset_left_mobile[0] . '.sb-chat-btn { left: ' . $chat_button_offset_left . 'px; }' . $chat_button_offset_left_mobile[1];
    }
    if ($return) return $css;
    if ($css != '') {
        echo '<style>' . $css . '</style>';
    }
    return false;
}

function sb_clean_data() {
    $time_24h = gmdate('Y-m-d H:i:s', time() - 86400);
    $time_30d = gmdate('Y-m-d H:i:s', time() - 2592000);
    sb_db_query('DELETE FROM sb_users WHERE user_type = "visitor" AND creation_time < "' . $time_24h . '"');
    sb_db_query('DELETE FROM sb_conversations WHERE status_code = 4 AND creation_time < "' . $time_30d . '"');
    if (sb_get_setting('admin-auto-archive')) {
        sb_db_query('UPDATE sb_conversations SET status_code = 3 WHERE (status_code = 1 OR status_code = 0) AND id IN (SELECT conversation_id FROM sb_messages WHERE creation_time IN (SELECT max(creation_time) latest_creation_time FROM sb_messages GROUP BY conversation_id) AND creation_time < "' . $time_24h . '")');
    }
    return true;
}

function sb_system_requirements() {
    $checks = [];

    // PHP version
    $checks['php-version'] = version_compare(PHP_VERSION, '7.2.0') >= 0;

    // ZipArchive
    $checks['zip-archive'] = class_exists('ZipArchive');

    // File permissions
    $permissions = [['plugin', SB_PATH], ['uploads', sb_upload_path()], ['apps', SB_PATH . '/apps'], ['languages', SB_PATH . '/resources/languages']];
    for ($i = 0; $i < count($permissions); $i++) {
        $path = $permissions[$i][1] . '/sb-permissions-check.txt';
        sb_file($path, 'permissions-check');
        $checks[$permissions[$i][0] . '-folder'] = file_exists($path) && file_get_contents($path) == 'permissions-check';
        if (file_exists($path)) {
            unlink($path);
        }
    }

    // AJAX file
    $checks['ajax'] = function_exists('curl_init') && sb_download(SB_URL . '/include/ajax.php') == 'true';

    // cURL
    $checks['curl'] = function_exists('curl_version') && is_array(sb_get_versions());

    // MySQL UTF8MB4 support
    $checks['UTF8mb4'] = !sb_is_error(sb_db_query('SET NAMES UTF8mb4'));

    return $checks;
}

function sb_select_countries() {
    $code = '<select><option value=""></option>';
    $countries = array_keys(json_decode(file_get_contents(SB_PATH . '/resources/json/countries.json'), true));
    for ($i = 0; $i < count($countries); $i++) {
    	$code .= '<option value="' . $countries[$i] . '">' . sb_($countries[$i]) . '</option>';
    }
    return $code . '</select>';
}

function sb_select_languages() {
    $code = '<select><option value=""></option>';
    $languages = json_decode(file_get_contents(SB_PATH . '/resources/languages/language-codes.json'), true);
    foreach ($languages as $key => $value) {
    	$code .= '<option value="' . $key . '">' . sb_($value) . '</option>';
    }
    return $code . '</select>';
}

function sb_select_phone() {
    $code = '<select><option value=""></option>';
    $phones = array_values(json_decode(file_get_contents(SB_PATH . '/resources/json/phone.json'), true));
    for ($i = 0; $i < count($phones); $i++) {
    	$code .= '<option value="+' . $phones[$i] . '">+' . $phones[$i] . '</option>';
    }
    return $code . '</select>';
}

function sb_component_editor($admin = false) { ?>
<div class="sb-editor">
    <?php if ($admin) echo '<div class="sb-labels"></div>' ?>
    <div class="sb-textarea">
        <textarea placeholder="<?php sb_e('Write a message...') ?>"></textarea>
    </div>
    <div class="sb-attachments"></div>
    <?php if ($admin && sb_get_multi_setting('dialogflow-smart-reply', 'dialogflow-smart-reply-active')) echo '<div class="sb-suggestions"></div>' ?>
    <div class="sb-bar">
        <div class="sb-bar-icons">
            <?php if ($admin || !sb_get_setting('disable-uploads')) echo '<div class="sb-btn-attachment" data-sb-tooltip="' . sb_('Attach a file') . '"></div>'; ?>
            <div class="sb-btn-saved-replies" data-sb-tooltip="<?php sb_e('Add a saved reply') ?>"></div>
            <div class="sb-btn-emoji" data-sb-tooltip="<?php sb_e('Add an emoji') ?>"></div>
            <?php if ($admin && defined('SB_WOOCOMMERCE')) echo '<div class="sb-btn-woocommerce" data-sb-tooltip="' . sb_('Add a product') . '"></div>' ?>
        </div>
        <div class="sb-icon-send sb-submit" data-sb-tooltip="<?php sb_e('Send message') ?>"></div>
        <img class="sb-loader" src="<?php echo SB_URL ?>/media/loader.svg" alt="" />
    </div>
    <div class="sb-popup sb-emoji">
        <div class="sb-header">
            <div class="sb-select">
                <p>
                    <?php sb_e('All') ?>
                </p>
                <ul>
                    <li data-value="all" class="sb-active">
                        <?php sb_e('All') ?>
                    </li>
                    <li data-value="Smileys">
                        <?php sb_e('Smileys & Emotions') ?>
                    </li>
                    <li data-value="People">
                        <?php sb_e('People & Body') ?>
                    </li>
                    <li data-value="Animals">
                        <?php sb_e('Animals & Nature') ?>
                    </li>
                    <li data-value="Food">
                        <?php sb_e('Food & Drink') ?>
                    </li>
                    <li data-value="Travel">
                        <?php sb_e('Travel & Places') ?>
                    </li>
                    <li data-value="Activities">
                        <?php sb_e('Activities') ?>
                    </li>
                    <li data-value="Objects">
                        <?php sb_e('Objects') ?>
                    </li>
                    <li data-value="Symbols">
                        <?php sb_e('Symbols') ?>
                    </li>
                </ul>
            </div>
            <div class="sb-search-btn">
                <i class="sb-icon sb-icon-search"></i>
                <input type="text" placeholder="<?php sb_e('Search emoji...') ?>" />
            </div>
        </div>
        <div class="sb-emoji-list">
            <ul></ul>
        </div>
        <div class="sb-emoji-bar"></div>
    </div>
    <?php if ($admin) { ?>
    <div class="sb-popup sb-replies">
        <div class="sb-header">
            <div class="sb-title">
                <?php sb_e('Saved replies') ?>
            </div>
            <div class="sb-search-btn">
                <i class="sb-icon sb-icon-search"></i>
                <input type="text" autocomplete="false" placeholder="<?php sb_e('Search replies...') ?>" />
            </div>
        </div>
        <div class="sb-replies-list sb-scroll-area">
            <ul class="sb-loading"></ul>
        </div>
    </div>
    <?php if (defined('SB_WOOCOMMERCE')) sb_woocommerce_products_popup() ?>
    <?php } ?>
    <form class="sb-upload-form-editor" action="#" method="post" enctype="multipart/form-data">
        <input type="file" name="files[]" class="sb-upload-files" multiple />
    </form>
</div>
<?php }

function sb_sanatize($string) {
    if (!is_string($string)) return $string;
    $string = str_replace(['<script', '</script'], ['&lt;script', '&lt;/script'], $string);
    $string = str_replace(['javascript:', 'onclick=', 'onerror='], '', $string);
    return htmlspecialchars($string, ENT_NOQUOTES | ENT_SUBSTITUTE, 'utf-8');
}

function sb_strpos_reverse($string, $search, $offset){
    return strrpos(substr($string, 0, $offset), $search);
}

function sb_mb_strpos_reverse($string, $search, $offset){
    return mb_strrpos(mb_substr($string, 0, $offset), $search);
}

/*
 * -----------------------------------------------------------
 * # REPORTS
 * -----------------------------------------------------------
 *
 * 1. Return the data of a report
 * 2. Update the values of a report
 *
 */

function sb_reports($report_name, $date_start = false, $date_end = false) {
    $date = '';
    $data = [];
    $data_final = [];
    $title = '';
    $table = ['Date', 'Count'];
    $description = '';
    $period = [];
    $query = '';
    $time_range = true;
    $label_type = 1;
    $chart_type = 'line';

    // Set up date range
    if ($date_start) {
        $date_start = date('Y-m-d', strtotime(str_replace('/', '-', $date_start)));
        $date = 'A.creation_time >= "' . $date_start . '"';
    }
    if ($date_end) {
        $date_end = date('Y-m-d', strtotime(str_replace('/', '-', $date_end)));
        $date .= ($date == '' ? '' : ' AND ') . 'A.creation_time <= "' . $date_end . '"';
    }

    // Get the data
    switch ($report_name) {
        case 'conversations':
            $query = 'SELECT A.creation_time FROM sb_conversations A, sb_users B WHERE B.id = A.user_id AND B.user_type <> "visitor"';
            $title = 'Conversations count';
            $description = 'Count of new conversations started by users.';
            break;
        case 'missed-conversations':
            $query = 'SELECT creation_time FROM sb_conversations A WHERE id NOT IN (SELECT conversation_id FROM sb_messages A, sb_users B WHERE A.user_id = B.id AND (B.user_type = "agent" OR B.user_type = "admin"))';
            $title = 'Missed conversations count';
            $description = 'Count of conversations without a reply from an human agent. Conversations with a reply from the bot are counted.';
            break;
        case 'conversations-time':
            $query = 'SELECT creation_time, conversation_id FROM sb_messages A';
            $title = 'Average conversations duration';
            $description = 'Average conversations duration. Messages sent more than 7 days after the previous message are counted as part of a new conversation.';
            $table = ['Date', 'Average time'];
            $label_type = 2;
            break;
        case 'visitors':
            $query = 'SELECT creation_time, value FROM sb_reports A WHERE name = "visitors"';
            $title = 'Visitors registrations count';
            $description = 'Visitors count. Visitors are users who have not started any conversations and who are not registered.';
            break;
        case 'leads':
            $query = 'SELECT creation_time FROM sb_users A WHERE user_type = "lead"';
            $title = 'Leads registrations count';
            $description = 'Leads count. Leads are users who have started at least one conversation but who are not registered.';
            break;
        case 'users':
            $query = 'SELECT creation_time FROM sb_users A WHERE user_type = "user"';
            $title = 'Users registrations count';
            $description = 'Users count. Users are registered with an email address.';
            break;
        case 'agents-response-time':
            $title = 'Average agents response time';
            $description = 'Average time for agents to send the first reply after the user sends the first message.';
            $table = ['Agent name', 'Average time'];
            $time_range = false;
            $chart_type = 'bar';
            $label_type = 2;
            break;
        case 'agents-conversations':
            $title = 'Agents conversations count';
            $description = 'Number of conversations which at least one reply from the agent.';
            $table = ['Agent name', 'Count'];
            $chart_type = 'bar';
            $time_range = false;
            break;
        case 'agents-conversations-time':
            $query = 'SELECT creation_time, conversation_id FROM sb_messages A';
            $title = 'Average agents conversations duration';
            $description = 'Average conversations duration of each agent. Messages sent more than 7 days after the previous message are counted as part of a new conversation.';
            $table = ['Agent name', 'Average time'];
            $chart_type = 'bar';
            $label_type = 2;
            $time_range = false;
            break;
        case 'agents-ratings':
            $title = 'Agent ratings';
            $description = 'Ratings assigned to agents.';
            $table = ['Agent name', 'Ratings'];
            $chart_type = 'horizontalBar';
            $time_range = false;
            $label_type = 3;
            break;
        case 'countries':
            $title = 'Users countries';
            $description = 'Countries of users who started at least one chat.';
            $table = ['Country', 'Count'];
            $time_range = false;
            $chart_type = 'pie';
            $label_type = 4;
            break;
        case 'languages':
            $title = 'Users languages';
            $description = 'Languages of users who started at least one chat.';
            $table = ['Language', 'Count'];
            $time_range = false;
            $chart_type = 'pie';
            $label_type = 4;
            break;
        case 'browsers':
            $title = 'Users browsers';
            $description = 'Browsers used by users who started at least one chat.';
            $table = ['Browser', 'Count'];
            $time_range = false;
            $chart_type = 'pie';
            $label_type = 4;
            break;
        case 'os':
            $title = 'Users operating systems';
            $description = 'Operating systems used by users who started at least one chat.';
            $table = ['Operating system', 'Count'];
            $time_range = false;
            $chart_type = 'pie';
            $label_type = 4;
            break;
        case 'subscribe':
            $query = 'SELECT creation_time, value FROM sb_reports A WHERE name = "subscribe"';
            $title = 'Subscribe emails count';
            $description = 'Number of users who registered their email via subscribe message.';
            break;
        case 'follow-up':
            $query = 'SELECT creation_time, value FROM sb_reports A WHERE name = "follow-up"';
            $title = 'Follow-up emails count';
            $description = 'Number of users who registered their email via follow-up message.';
            break;
        case 'registrations':
            $query = 'SELECT creation_time, value FROM sb_reports A WHERE name = "registrations"';
            $title = 'Registrations count';
            $description = 'Number of users who created an account via the registration form of the chat.';
            break;
        case 'articles-searches':
            $query = 'SELECT creation_time, value FROM sb_reports A WHERE name = "articles-searches"';
            $title = 'Articles searches';
            $description = 'Articles searches made by users.';
            $table = ['Date', 'Search terms'];
            break;
        case 'articles-ratings':
            $query = 'SELECT value, extra FROM sb_reports A WHERE name = "article-ratings"';
            $title = 'Articles ratings';
            $description = 'Ratings assigned to articles.';
            $table = ['Article name', 'Ratings'];
            $chart_type = 'horizontalBar';
            $time_range = false;
            $label_type = 3;
            break;
        case 'articles-views-single':
        case 'articles-views':
            $query = 'SELECT creation_time, value, extra FROM sb_reports A WHERE name = "articles-views"';
            $title = 'Articles views';
            $description = 'Number of times articles have been viewed by users.';
            if ($report_name == 'articles-views-single') {
                $chart_type = 'horizontalBar';
                $time_range = false;
                $table = ['Article', 'Count'];
            }
            break;
        case 'sms-automations':
        case 'email-automations':
        case 'message-automations':
            $query = 'SELECT creation_time, value FROM sb_reports A WHERE name = "' . $report_name . '"';
            $title = $description = sb_string_slug($report_name, 'string') . ' count';
            break;
        case 'direct-sms':
        case 'direct-emails':
        case 'direct-messages':
            $query = 'SELECT creation_time, value FROM sb_reports A WHERE name = "' . $report_name . '"';
            $name = $report_name == 'direct-emails' ? 'emails' : ($report_name == 'direct-messages' ? 'chat messages' : 'text messages');
            $title = 'Direct ' . $name;
            $description = 'Direct ' . $name . ' sent to users. The details column shows the first part of the message and the number of users to which it has been sent to.';
            $table = ['Date', 'Details'];
            break;
    }
    switch ($report_name) {
        case 'sms-automations':
        case 'email-automations':
        case 'message-automations':
        case 'registrations':
        case 'follow-up':
        case 'subscribe':
        case 'users':
        case 'leads':
        case 'visitors':
        case 'conversations':
        case 'missed-conversations':
            $rows = sb_db_get($query . ($date == '' ? '' : ' AND ' . $date) . ' ORDER BY STR_TO_DATE(A.creation_time, "%Y-%m-%d %T")', false);
            $sum = !in_array($report_name, ['visitors', 'subscribe', 'follow-up', 'registrations', 'message-automations', 'email-automations', 'sms-automations']);
            for ($i = 0; $i < count($rows); $i++) {
                $date_row = date('d/m/Y', strtotime($rows[$i]['creation_time']));
                $data[$date_row] = $sum ? [empty($data[$date_row]) ? 1 : $data[$date_row][0] + 1] : [$rows[$i]['value']];
            }
            break;
        case 'agents-conversations-time':
        case 'conversations-time':
            $rows = sb_db_get($query . ($date == '' ? '' : ' WHERE ' . $date) . ' ORDER BY STR_TO_DATE(creation_time, "%Y-%m-%d %T")', false);
            $count = count($rows);
            if ($count == 0) return false;
            $last_id = $rows[0]['conversation_id'];
            $first_time = $rows[0]['creation_time'];
            $times = [];
            $agents_times = $report_name == 'agents-conversations-time';
            for ($i = 1; $i < $count; $i++) {
                $time = $rows[$i]['creation_time'];
                if (($rows[$i]['conversation_id'] != $last_id) || (strtotime('+7 day', strtotime($first_time)) < strtotime($time))) {
                    $last_time = strtotime($rows[$i - 1]['creation_time']);
                    array_push($times, [$agents_times ? $last_id : date('d/m/Y', $last_time), $last_time - strtotime($first_time)]);
                    $first_time = $time;
                    $last_id = $rows[$i]['conversation_id'];
                }
            }
            if ($agents_times) {
                $agents_counts = [];
                $agents_conversations = [];
                $rows = sb_db_get('SELECT conversation_id, first_name, last_name FROM sb_messages A, sb_users B WHERE A.user_id = B.id AND (B.user_type = "agent" OR  B.user_type = "admin") GROUP BY conversation_id', false);
                for ($i = 0; $i < count($rows); $i++) {
                    $agents_conversations[$rows[$i]['conversation_id']] = $rows[$i]['first_name'] . ' ' . $rows[$i]['last_name'];
                }
                for ($i = 0; $i < count($times); $i++) {
                    if (isset($agents_conversations[$times[$i][0]])) {
                        $name = $agents_conversations[$times[$i][0]];
                        $data[$name] = empty($data[$name]) ? $times[$i][1] : $data[$name] + $times[$i][1];
                        $agents_counts[$name] = empty($agents_counts[$name]) ? 1 : $agents_counts[$name] + 1;
                    }
                }
                foreach ($data as $key => $value) {
                    $data[$key] = [intval($value / $agents_counts[$key]), gmdate('H:i:s', $value / $agents_counts[$key])];
                }
            } else {
                for ($i = 0; $i < count($times); $i++) {
                    $time = $times[$i][0];
                    $count = 0;
                    $sum = 0;
                    if (!isset($data[$time])) {
                        for ($y = 0; $y < count($times); $y++) {
                            if ($times[$y][0] == $time) {
                                $sum += $times[$y][1];
                                $count++;
                            }
                        }
                        $data[$time] = [intval($sum / $count), gmdate('H:i:s', $sum / $count)];
                    }
                }
            }
            break;
        case 'agents-conversations':
            $rows = sb_db_get('SELECT first_name, last_name FROM sb_messages A, sb_users B WHERE A.user_id = B.id AND (B.user_type = "agent" OR  B.user_type = "admin") ' . ($date == '' ? '' : ' AND ' . $date) . ' GROUP BY conversation_id, B.id', false);
            for ($i = 0; $i < count($rows); $i++) {
                $name = $rows[$i]['first_name'] . ' ' . $rows[$i]['last_name'];
                $data[$name] = [empty($data[$name]) ? 1 : $data[$name][0] + 1];
            }
            break;
        case 'agents-response-time':
            $conversations = sb_db_get('SELECT A.user_id, B.user_type, A.conversation_id, A.creation_time FROM sb_messages A, sb_users B WHERE B.id = A.user_id AND A.conversation_id IN (SELECT conversation_id FROM sb_messages A WHERE user_id IN (SELECT id FROM sb_users WHERE user_type = "agent" OR user_type = "admin") ' . ($date == '' ? '' : ' AND ' . $date) . ') ORDER BY A.conversation_id, STR_TO_DATE(A.creation_time, "%Y-%m-%d %T")', false);
            $count = count($conversations);
            if ($count == 0) return false;
            $agents = [];
            $active_conversation = $conversations[0];
            $skip = false;
            $agents_ids = '';
            for ($i = 1; $i < $count; $i++) {
                if ($skip) {
                    if ($active_conversation['conversation_id'] != $conversations[$i]['conversation_id']) {
                        $active_conversation = $conversations[$i];
                        $skip = false;
                    }
                    continue;
                }
                if ($conversations[$i]['user_type'] == 'agent' || $conversations[$i]['user_type'] == 'admin') {
                    $conversation_time = strtotime($conversations[$i]['creation_time']) - strtotime($active_conversation['creation_time']);
                    $agent_id = $conversations[$i]['user_id'];
                    if (!isset($agents[$agent_id])) {
                        $agents[$agent_id] = [];
                        $agents_ids .= $agent_id . ',';
                    }
                    array_push($agents[$agent_id], $conversation_time);
                    $skip = true;
                }
            }
            $rows = sb_db_get('SELECT id, first_name, last_name FROM sb_users WHERE id IN (' . substr($agents_ids, 0, -1) . ')', false);
            $agent_names = [];
            for ($i = 0; $i < count($rows); $i++) {
                $agent_names[$rows[$i]['id']] = $rows[$i]['first_name'] . ' ' . $rows[$i]['last_name'];
            }
            foreach ($agents as $key => $times) {
                $sum = 0;
                $count = count($times);
                for ($i = 0; $i < $count; $i++) {
                    $sum += $times[$i];
                }
                $data[$agent_names[$key]] = [intval($sum / $count), gmdate('H:i:s', $sum / $count)];
            }
            break;
        case 'articles-ratings':
        case 'agents-ratings':
            $article = $report_name == 'articles-ratings';
            $ratings = $article ? sb_db_get($query, false) : sb_get_external_setting('ratings');
            if ($ratings) {
                $rows = $article ? sb_get_articles() : sb_db_get('SELECT id, first_name, last_name FROM sb_users WHERE user_type = "agent" OR user_type = "admin"', false);
                $items = [];
                for ($i = 0; $i < count($rows); $i++) {
                    $items[$rows[$i]['id']] = $article ? $rows[$i]['title'] : $rows[$i]['first_name'] . ' ' . $rows[$i]['last_name'];
                }
                if ($article) {
                    for ($i = 0; $i < count($ratings); $i++) {
                        $rating = $ratings[$i];
                        if (isset($rating['extra'])) {
                            $id = $rating['extra'];
                            if (isset($items[$id])) {
                                $article_ratings = json_decode($rating['value']);
                                $positives = 0;
                                $negatives = 0;
                                $name = strlen($items[$id]) > 40 ? substr($items[$id], 0, 40) . '...' : $items[$id];
                                for ($y = 0; $y < count($article_ratings); $y++) {
                                    $positives += $article_ratings[$y] == 1 ? 1 : 0;
                                    $negatives += $article_ratings[$y] == 1 ? 0 : 1;
                                }
                                $data[$name] = [$positives, $negatives];
                            }
                        }
                    }
                } else {
                    foreach ($ratings as $rating) {
                        if (isset($rating['agent_id'])) {
                            $id = $rating['agent_id'];
                            if (isset($items[$id])) {
                                $positive = $rating['rating'] == 1 ? 1 : 0;
                                $negative = $rating['rating'] == 1 ? 0 : 1;
                                $name = $items[$id];
                                $data[$name] = isset($data[$name]) ? [$data[$name][0] + $positive, $data[$name][1] + $negative] : [$positive, $negative];
                            }
                        }
                    }
                }
                foreach ($data as $key => $value) {
                    $positive = $value[0];
                    $negative = $value[1];
                    $average = round($positive / ($negative + $positive) * 100, 2);
                    $data[$key] = [$average, '<i class="sb-icon-like"></i>' . $positive . ' (' . $average . '%) <i class="sb-icon-dislike"></i>' . $negative];
                }
            }
            break;
        case 'articles-views':
        case 'articles-views-single':
            $rows = sb_db_get($query . ($date == '' ? '' : ' AND ' . $date) . ' ORDER BY STR_TO_DATE(A.creation_time, "%Y-%m-%d %T")', false);
            $single = $report_name == 'articles-views-single';
            for ($i = 0; $i < count($rows); $i++) {
                $date_row = $single ? $rows[$i]['extra'] : date('d/m/Y', strtotime($rows[$i]['creation_time']));
                $data[$date_row] = [intval($rows[$i]['value']) + (empty($data[$date_row]) ? 0 : $data[$date_row][0])];
            }
            if ($single) {
                $articles = sb_get_articles();
                $data_names = [];
                for ($i = 0; $i < count($articles); $i++) {
                    $id = $articles[$i]['id'];
                    if (isset($data[$id])) {
                        $article_title = $articles[$i]['title'];
                        $data_names[strlen($article_title) > 40 ? substr($article_title, 0, 40) . '...' : $article_title] = $data[$id];
                    }
                }
                $data = $data_names;
            }
            break;
        case 'os':
        case 'browsers':
        case 'languages':
        case 'countries':
            $field = 'location';
            $is_languages = $report_name == 'languages';
            $is_browser = $report_name == 'browsers';
            $is_os = $report_name == 'os';
            $is_country = $report_name == 'countries';
            if ($is_languages) $field = 'browser_language';
            if ($is_browser) $field = 'browser';
            if ($is_os) $field = 'os';
            $language_codes = json_decode(file_get_contents(SB_PATH . '/resources/languages/language-codes.json'), true);
            $country_codes = $is_country ? json_decode(file_get_contents(SB_PATH . '/resources/json/countries.json'), true) : false;
            $rows = sb_db_get('SELECT value FROM sb_users_data WHERE slug = "' . $field . '" AND user_id IN (SELECT id FROM sb_users A WHERE (user_type = "lead" OR user_type = "user")' . ($date == '' ? '' : ' AND ' . $date) . ')', false);
            $total = 0;
            $flags = [];
            for ($i = 0; $i < count($rows); $i++) {
                $value = $rows[$i]['value'];
                $valid = false;
                if ($is_country && strpos($value, ',')) {
                    $value = trim(substr($value, strpos($value, ',') + 1));
                    $valid = true;
                }
                if ($is_languages && isset($language_codes[strtolower($value)])) {
                    $language_code = strtolower($value);
                    $value = $language_codes[$language_code];
                    if (!isset($flags[$value]) && file_exists(SB_PATH . '/media/flags/languages/' . $language_code . '.png')) {
                        $flags[$value] = $language_code;
                    }
                    $valid = true;
                }
                if ($is_country && isset($country_codes[ucfirst($value)])) {
                    $country_code = strtolower($country_codes[ucfirst($value)]);
                    if (!isset($flags[$country_code]) && file_exists(SB_PATH . '/media/flags/countries/' . $country_code . '.png')) {
                        $flags[$value] = $country_code;
                    }
                    $valid = true;
                }
                if ($valid || $is_browser || $is_os) {
                    $data[$value] = empty($data[$value]) ? 1 : $data[$value] + 1;
                    $total++;
                }
            }
            arsort($data);
            foreach ($data as $key => $value) {
                $image = '';
                if (isset($flags[$key])) $image = '<img class="sb-flag" src="' . SB_URL . '/media/flags/languages/' . $flags[$key] . '.png" />';
                if ($is_browser) {
                    $lowercase = strtolower($key);
                    if (strpos($lowercase, 'chrome') !== false) {
                        $image = 'chrome';
                    } else if (strpos($lowercase, 'edge') !== false) {
                        $image = 'edge';
                    } else if (strpos($lowercase, 'firefox') !== false) {
                        $image = 'firefox';
                    } else if (strpos($lowercase, 'opera') !== false) {
                        $image = 'opera';
                    } else if (strpos($lowercase, 'safari') !== false) {
                        $image = 'safari';
                    }
                    if ($image) $image = '<img src="' . SB_URL . '/media/devices/' . $image . '.svg" />';
                }
                if ($is_os) {
                    $lowercase = strtolower($key);
                    if (strpos($lowercase, 'windows') !== false) {
                        $image = 'windows';
                    } else if (strpos($lowercase, 'mac') !== false || strpos($lowercase, 'apple') !== false || strpos($lowercase, 'ipad') !== false || strpos($lowercase, 'iphone') !== false) {
                        $image = 'apple';
                    } else if (strpos($lowercase, 'android') !== false) {
                        $image = 'android';
                    } else if (strpos($lowercase, 'linux') !== false) {
                        $image = 'linux';
                    } else if (strpos($lowercase, 'ubuntu') !== false) {
                        $image = 'ubuntu';
                    }
                    if ($image) $image = '<img src="' . SB_URL . '/media/devices/' . $image . '.svg" />';
                }
                $data[$key] = [$value, $image . $value . ' (' . round($value / $total * 100, 2) . '%)'];
            }
            break;
        case 'direct-sms':
        case 'direct-emails':
        case 'direct-messages':
        case 'articles-searches':
            $rows = sb_db_get($query . ($date == '' ? '' : ' AND ' . $date) . ' ORDER BY STR_TO_DATE(A.creation_time, "%Y-%m-%d %T")', false);
            for ($i = 0; $i < count($rows); $i++) {
                $date_row = date('d/m/Y', strtotime($rows[$i]['creation_time']));
                $search = '<div>' . $rows[$i]['value'] . '</div>';
                $data[$date_row] = empty($data[$date_row]) ? [1, $search] : [$data[$date_row][0] + 1, $data[$date_row][1] . $search];
            }
            break;
    }

    // Generate all days, months, years within the date range
    if (!count($data)) return false;
    if ($time_range) {
        if (!$date_start) {
            $date_start = date('Y-m-d', strtotime(str_replace('/', '-', array_keys($data)[0])));
        }
        if (!$date_end) {
            $date_end = date('Y-m-d', strtotime(str_replace('/', '-', array_keys($data)[count($data) - 1])));
        }
        $period = new DatePeriod(new DateTime($date_start), new DateInterval('P1D'), new DateTime(date('Y-m-d', strtotime($date_end . '+1 days'))));
        $period = iterator_to_array($period);
        $period_count = count($period);
        $date_format = $period_count > 730 ? 'Y' : ($period_count > 60 ? 'm/Y' : 'd/m/Y');
        $is_array = count(reset($data)) > 1;
        $counts = [];
        $average = $label_type == 2;
        for ($i = 0; $i < $period_count; $i++) {
            $key = $period[$i]->format($date_format);
            $key_original = $period[$i]->format('d/m/Y');
            $value = empty($data[$key_original]) ? 0 : $data[$key_original][0];
            $data_final[$key] = [empty($data_final[$key]) ? $value : $data_final[$key][0] + $value];
            if ($average) $counts[$key] = empty($counts[$key]) ? 1 : $counts[$key] + 1;
            if ($is_array) array_push($data_final[$key], empty($data[$key_original][1]) ? '' : $data[$key_original][1]);
        }
        if ($average && $period_count > 60) {
            foreach ($data_final as $key => $value) {
                $data_final[$key] = [intval($value[0] / $counts[$key]), gmdate('H:i:s', $value[0] / $counts[$key])];
            }
        }
    } else {
        $data_final = $data;
    }

    // Return the data
    return ['title' => sb_($title), 'description' => sb_($description), 'data' => $data_final, 'table' => $table, 'table-inverse' => $time_range, 'label_type' => $label_type, 'chart_type' => $chart_type];
}

function sb_reports_update($name, $value = false, $external_id = false, $extra = false) {
    $now = gmdate('Y-m-d');
    switch ($name) {
        case 'direct-sms':
        case 'direct-emails':
        case 'direct-messages':
        case 'articles-searches':
            return sb_db_query('INSERT INTO sb_reports (name, value, creation_time, external_id, extra) VALUES ("' . $name . '", "' . sb_db_escape(sb_sanatize($value)) . '", "' . $now . '", NULL, NULL)');
        case 'articles-views':
            $where = ' WHERE name = "articles-views" AND extra = "' . $extra . '" AND creation_time = "' . $now . '"';
            $count = sb_db_get('SELECT value FROM sb_reports' . $where);
            return sb_db_query(empty($count) ? 'INSERT INTO sb_reports (name, value, creation_time, external_id, extra) VALUES ("' . $name . '", 1, "' . $now . '", NULL, "' . $extra . '")' : 'UPDATE sb_reports SET value = ' . (intval($count['value']) + 1) . $where);
        default:
            $where = ' WHERE name = "' . $name . '" AND creation_time = "' . $now . '"';
            $count = sb_db_get('SELECT value FROM sb_reports' . $where);
            return sb_db_query(empty($count) ? 'INSERT INTO sb_reports (name, value, creation_time, external_id, extra) VALUES ("' . $name . '", 1, "' . $now . '", ' . ($external_id === false  ? 'NULL' : '"' . $external_id . "'") . ', ' . ($extra === false  ? 'NULL' : '"' . $extra . "'") . ')' : 'UPDATE sb_reports SET value = ' . (intval($count['value']) + 1) . $where);
    }
}

/*
 * -----------------------------------------------------------
 * # SMS
 * -----------------------------------------------------------
 *
 */

function sb_send_sms($message, $to, $template = true, $conversation_id = true) {
    $settings = sb_get_setting('sms');
    $to_agents = $to == 'agents' || $to == 'all-agents' || strpos($to, 'department-') !== false;

    // Retrive phone number
    if ($to_agents) {
        $phones = sb_db_get('SELECT A.id, value FROM sb_users A, sb_users_data B WHERE A.id = B.user_id AND (user_type = "agent" OR user_type = "admin") AND slug = "phone"' . ($to == 'agents' ? ' AND (department IS NULL OR department = "")' : (strpos($to, 'department-') !== false ? ' AND department = ' . substr($to, 11) : '')), false);
        $online_agents_ids = sb_get_online_users_ids(true);
        for ($i = 0; $i < count($phones); $i++) {
            if (!in_array($phones[$i]['id'], $online_agents_ids)) {
                sb_send_sms($message, $phones[$i]['value'], $template, $conversation_id);
            }
        }
        return false;
    } else if (strpos($to, '+') === false && substr($to, 0, 2) != '00') {
        $to = sb_get_user_extra($to, 'phone');
        if (empty($to)) return false;
    }

    // Recipient user details, security, and merge fields
    $user = sb_get_user_by('phone', $to);
    if (!sb_is_agent() && !sb_is_agent($user) && sb_get_active_user_ID() != sb_isset($user, 'id') && empty($GLOBALS['SB_FORCE_ADMIN'])) {
        return new SBError('security-error', 'sb_send_sms');
    }
    $message_template = $template ? sb_($settings[sb_is_agent() && empty($GLOBALS['SB_FORCE_ADMIN']) && !$to_agents ? 'sms-message-user' : 'sms-message-agent']) : false;
    $message = $message_template ? str_replace('{message}', $message, $message_template) : $message;
    $message = str_replace(['{conversation_url_parameter}', '{recipient_name}', '{sender_name}', '{recipient_email}', '{sender_email}'], [$conversation_id && $user ? ('?conversation=' . $conversation_id . '&token=' . $user['token']) : '', sb_get_user_name($user), sb_get_user_name(), sb_isset($user, 'email'), sb_isset(sb_get_active_user(), 'email', '')], sb_merge_fields($message));

    // Send the SMS
    $query = ['Body' => sb_clear_text_formatting(strip_tags($message)), 'From' => $settings['sms-sender'], 'To' => $to];
    return sb_curl('https://api.twilio.com/2010-04-01/Accounts/' . $settings['sms-user'] . '/Messages.json', $query, [ 'Authorization: Basic  ' . base64_encode($settings['sms-user'] . ':' . $settings['sms-token']) ]);
}

/*
 * -----------------------------------------------------------
 * # AUTOMATIONS
 * -----------------------------------------------------------
 *
 * 1. Get all automations
 * 2. Save all automations
 * 3. Run all valid automations and return the ones which need client-side validations
 * 4. Check if an automation is valid and can be executed
 * 5. Execute an automation
 *
 */

function sb_automations_get() {
    $types = ['messages', 'emails', 'sms', 'popups', 'design'];
    $automations = sb_get_external_setting('automations');
    $translations = [];
    $rows = sb_db_get('SELECT name, value FROM sb_settings WHERE name LIKE "automations-translations-%"', false);
    for ($i = 0; $i < count($rows); $i++) {
        $translations[substr($rows[$i]['name'], -2)] = json_decode($rows[$i]['value'], true);
    }
    for ($i = 0; $i < count($types); $i++) {
        if (!isset($automations[$types[$i]])) $automations[$types[$i]] = [];
    }
    return [$automations, $translations];
}

function sb_automations_save($automations, $translations = false) {
    if ($translations) {
        $db = '';
        foreach ($translations as $key => $value) {
            $name = 'automations-translations-' . $key;
            sb_save_external_setting($name, $value);
            $db .=  '"' . $name . '",';
        }
        sb_db_query('DELETE FROM sb_settings WHERE name LIKE "automations-translations-%" AND name NOT IN (' . substr($db, 0, -1) . ')');
    }
    return sb_save_external_setting('automations', empty($automations) ? [] : $automations);
}

function sb_automations_run_all() {
    if (sb_is_agent()) return false;
    $response = [];
    $automations_all = sb_automations_get();
    $user_language = sb_get_user_language();
    foreach ($automations_all[0] as $type => $automations) {
        for ($i = 0; $i < count($automations); $i++) {
            $automations[$i]['type'] = $type;
            $validation = sb_automations_validate($automations[$i]);
            if ($validation) {
                $automation_id = $automations[$i]['id'];
                $conditions = $validation['conditions'];

                // Translations
                if ($user_language && isset($automations_all[1][$user_language])) {
                    $translations = sb_isset($automations_all[1][$user_language], $type, []);
                    for ($x = 0; $x < count($translations); $x++) {
                        if ($translations[$x]['id'] == $automation_id) {
                            $automations[$i] = $translations[$x];
                            $automations[$i]['type'] = $type;
                            break;
                        }
                    }
                }
                if ($validation['repeat_id']) $automations[$i]['repeat_id'] = $validation['repeat_id'];
                if (count($conditions) || $type == 'popups' || $type == 'design' || !sb_get_active_user()) {

                    // Automation with client-side conditions, server-side invalid conditions, or popup, design
                    $automations[$i]['conditions'] = $conditions;
                    array_push($response, $automations[$i]);
                } else {

                    // Run automation
                    sb_automations_run($automations[$i]);
                }
            }
        }
    }
    return $response;
}

function sb_automations_validate($automation) {
    $conditions = sb_isset($automation, 'conditions', []);
    $invalid_conditions = [];
    $repeat_id = false;
    $valid = false;
    $active_user = sb_get_active_user();
    $active_user_id = sb_isset($active_user, 'id');
    for ($i = 0; $i < count($conditions); $i++) {
        $valid = false;
        $criteria = $conditions[$i][1];
        switch ($conditions[$i][0]) {
            case 'datetime':
                $now = time();
                $offset = intval(sb_get_setting('timetable-utc', 0)) * 3600;
                if ($criteria == 'is-between') {
                    $dates = explode(' - ', $conditions[$i][2]);
                    if (count($dates) == 2) {
                        $unix = date_timestamp_get(DateTime::createFromFormat('d/m/Y H:i', $dates[0] . (strpos($dates[0], ':') ? '' : ' 00:00'))) + (strpos($dates[0], ':') ? $offset : 0);
                        $unix_end = date_timestamp_get(DateTime::createFromFormat('d/m/Y H:i', $dates[1] . (strpos($dates[1], ':') ? '' : ' 23:59'))) + (strpos($dates[1], ':') ? $offset : 0);
                        $valid = ($now >= $unix) && ($now <= $unix_end);
                        $continue = true;
                    }
                } else if ($criteria == 'is-exactly') {
                    $is_time = strpos($conditions[$i][2], ':');
                    $unix = date_timestamp_get(DateTime::createFromFormat('d/m/Y H:i', $conditions[$i][2] . ($is_time ? '' : ' 00:00'))) + $offset;
                    $valid = $now == $unix || (!$is_time && $now > $unix && $now < $unix + 86400);
                }
                if (!$valid) {
                    for ($j = 0; $j < count($conditions); $j++) {
                        if ($conditions[$j][0] == 'repeat') {
                            $condition = $conditions[$j][1];
                            if ($criteria == 'is-between' && $continue) {
                                $hhmm = false;
                                $hhmm_end = false;
                                if (strpos($dates[0], ':') && strpos($dates[1], ':')) {
                                    $hhmm = strtotime(date('Y-m-d ' . explode(' ', $dates[0])[1])) + $offset;
                                    $hhmm_end = strtotime(date('Y-m-d ' . explode(' ', $dates[1])[1])) + $offset;
                                }
                                if ($condition == 'every-day') {
                                    $valid = $hhmm ? ($now >= $hhmm) && ($now <= $hhmm_end) : true;
                                    $repeat_id = $valid ? date('z') : false;
                                } else {
                                    $letter = $condition == 'every-week' ? 'w' : ($condition == 'every-month' ? 'd' : 'z');
                                    $letter_value_now = date($letter);
                                    $letter_value_unix = date($letter, $unix);
                                    $letter_value_unix_end = date($letter, $unix_end);
                                    if ($letter == 'z') {
                                        $letter_value_now -= date('L');
                                        $letter_value_unix -= date('L', $unix);
                                        $letter_value_unix_end -= date('L', $unix_end);
                                    }
                                    $valid = ($letter_value_now >= $letter_value_unix) && (date($letter, strtotime('+' . ($letter_value_unix_end - $letter_value_unix - (($letter_value_now >= $letter_value_unix) && ($letter_value_now <= $letter_value_unix_end) ? $letter_value_now - $letter_value_unix : 0)) . ' days')) <= $letter_value_unix_end);
                                    if ($valid && $hhmm) $valid = ($now >= $hhmm) && ($now <= $hhmm_end);
                                    $repeat_id = $valid ? $letter_value_now : false;
                                }
                            } else if ($criteria == 'is-exactly') {
                                if ($condition == 'every-day') {
                                    $valid = true;
                                    $repeat_id = date('z');
                                } else {
                                    $letter = $condition == 'every-week' ? 'w' : ($condition == 'every-month' ? 'd' : 'z');
                                    $valid = $letter == 'z' ? ((date($letter, $unix) - date('L', $unix)) == (date($letter) - date('L'))) : (date($letter, $unix) == date($letter));
                                    $repeat_id = $valid ? date($letter) : false;
                                }
                            }
                            break;
                        }
                    }
                }
                break;
            case 'include_urls':
            case 'exclude_urls':
                $url =  str_replace(['https://', 'http://', 'www.'], '', sb_isset($_POST, 'current_url', $_SERVER['HTTP_REFERER']));
                $checks = explode(',', $conditions[$i][2]);
                $include = $conditions[$i][0] == 'include_urls';
                if (!$include) $valid = true;
                for ($j = 0; $j < count($checks); $j++) {
                    $checks[$j] = trim(str_replace(['https://', 'http://', 'www.'], '', $checks[$j]));
                    if (($criteria == 'contains' && strpos($url . '/', $checks[$j]) !== false) || ($criteria == 'does-not-contain' && strpos($url, $checks[$j]) === false) || ($criteria == 'is-exactly' && $checks[$j] == $url) || ($criteria == 'is-not' && $checks[$j] != $url)) {
                        $valid = $include;
                        break;
                    }
                }
                break;
            case 'user_type':
                if ($active_user) {
                    $user_type = sb_isset($active_user, 'user_type');
                    $valid = ($criteria == 'is-visitor' && $user_type == 'visitor') || ($criteria == 'is-lead' && $user_type == 'is-lead') || ($criteria == 'is-user' && $user_type == 'user') || ($criteria == 'is-not-visitor' && $user_type != 'visitor') || ($criteria == 'is-not-lead' && $user_type != 'lead') || ($criteria == 'is-not-user' && $user_type != 'user');
                } else {
                    $valid = true;
                    array_push($invalid_conditions, $conditions[$i]);
                }
                break;
            case 'cities':
            case 'languages':
            case 'countries':
                if ($active_user) {
                    if ($conditions[$i][0] == 'languages') {
                        $user_value = sb_get_user_extra($active_user_id, 'browser_language');
                        if (!$user_value) {
                            $user_value = sb_get_user_extra($$active_user_id, 'language');
                            if (strlen($user_value) > 2) $user_value = substr($user_value, 0, 2);
                        }
                    } else if ($conditions[$i][0] == 'cities') {
                        $user_value = sb_get_user_extra($active_user_id, 'location');
                        if ($user_value) {
                            $user_value = substr($user_value, 0, strpos($user_value, ','));
                        } else {
                            $user_value = sb_get_user_extra($active_user_id, 'city');
                        }
                    } else {
                        $user_value = sb_get_user_extra($active_user_id, 'country_code');
                        if (!$user_value) {
                            $user_value = sb_get_user_extra($active_user_id, 'country');
                            if (!$user_value) {
                                $user_value = sb_get_user_extra($active_user_id, 'location');
                                if ($user_value) {
                                    $user_value = trim(substr($user_value, strpos($user_value, ',')));
                                }
                            }
                            if ($user_value) {
                                $countries = json_decode(file_get_contents(SB_PATH . '/resources/json/countries.json'), true);
                                if (isset($countries[$user_value])) $user_value = $countries[$user_value];
                                else if (strlen($user_value) > 2) $user_value = substr($user_value, 0, 2);
                            }
                        }
                    }
                    if ($user_value) {
                        $user_value = strtolower(trim($user_value));
                        $condition_values = explode(',', $criteria);
                        for ($j = 0; $j < count($condition_values); $j++) {
                            if (strtolower(trim($condition_values[$j])) == $user_value) {
                                $valid = true;
                                break;
                            }
                        }
                    }
                } else {
                    $valid = true;
                    array_push($invalid_conditions, $conditions[$i]);
                }
                break;
            case 'returning_visitor':
                $is_first_visitor = $criteria == 'first-time-visitor';
                if ($active_user) {
                    $times = sb_db_get('SELECT creation_time, last_activity FROM sb_users WHERE id = ' . $active_user_id);
                    $difference = strtotime($times['last_activity']) - strtotime($times['creation_time']);
                    $valid = $is_first_visitor ? $difference < 86400 : $difference > 86400;
                } else if ($is_first_visitor) $valid = true;
                break;
            case 'repeat':
                $valid = true;
                break;
            default:
                $valid = true;
                array_push($invalid_conditions, $conditions[$i]);
                break;
        }
        if (!$valid) break;
    }
    if ($valid) {

        // Check if automation already sent
        $history = sb_get_external_setting('automations-history', []);
        $sent = false;
        if ($active_user) {
            for ($x = 0, $length = count($history); $x < $length; $x++) {
                if ($history[$x][0] == $active_user_id && $history[$x][1] == $automation['id'] && (!$repeat_id || (count($history[$x]) > 2 && $history[$x][2] == $repeat_id))) {
                    $sent = true;
                    break;
                }
            }
        }
        if (!$sent) {

            // Check user details conditions
            if ($automation['type'] == 'emails' && (!$active_user || empty($active_user['email']))) {
                array_push($invalid_conditions, ['user_email']);
            } else if ($automation['type'] == 'sms' && !sb_get_user_extra($active_user_id, 'phone')) {
                array_push($invalid_conditions, ['user_phone']);
            }

            // Return the result
            return ['conditions' => $invalid_conditions, 'repeat_id' => $repeat_id];
        }
    }
    return false;
}

function sb_automations_run($automation, $validate = false) {
    $active_user = sb_get_active_user();
    $response = false;
    if ($validate) {
        $validation = sb_automations_validate($automation);
        if (!$validation || count($validation['conditions']) > 0) return false;
    }
    if ($active_user) {
        $active_user_id = $active_user['id'];
        switch ($automation['type']) {
            case 'messages':
                $response = sb_send_message(sb_get_bot_id(), sb_get_last_conversation_id_or_create($active_user_id, 3), $automation['message'], [], 3, '{ "event": "open-chat" }');
                sb_reports_update('message-automations');
                break;
            case 'emails':
                $response = empty($active_user['email']) ? false : sb_email_send($active_user['email'], sb_merge_fields($automation['subject']), sb_merge_fields(sb_email_default_parts($automation['message'], $active_user_id)));
                sb_reports_update('email-automations');
                break;
            case 'sms':
                $phone = sb_get_user_extra($active_user_id, 'phone');
                $response = $phone ? sb_send_sms(sb_merge_fields($automation['message']), $phone, false) : false;
                sb_reports_update('sms-automations');
                break;
            default:
                trigger_error('Invalid automation type in sb_automations_run()');
                return false;
        }
        $history = sb_get_external_setting('automations-history', []);
        $history_value = [$active_user['id'], $automation['id']];
        if (count($history) > 10000) $history = array_slice($history, 1000);
        if (isset($automation['repeat_id'])) array_push($history_value, $automation['repeat_id']);
        if ($response) array_push($history, $history_value);
        sb_save_external_setting('automations-history', $history);
    }
    return $response;
}

?>