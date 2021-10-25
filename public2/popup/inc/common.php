<?php
if (array_key_exists('debug', $_GET)) error_reporting(-1);
else error_reporting(0);
define('UAP_CORE', 1);
define('UAP_TITLE', esc_html__('Admin Panel', 'hap'));
define('ABSPATH', rtrim(dirname(dirname(__FILE__)), '/').'/');
define('HALFDATA_DEMO', false);
define('HALFDATA_DEMO_LOGIN', 'demo');
define('HALFDATA_DEMO_PASSWORD', 'demo');
define('HALFDATA_BASE_PAGE', 'lepopup');
$options = array(
	'login' => '',
	'password' => '',
	'url' => '',
	'login_redirect' => 'dashboard',
	'timezone_string' => 'UTC',
	'gmt_offset' => 0,
	'mail_method' => 'mail',
	'mail_from_name' => UAP_TITLE,
	'mail_from_email' => 'noreply@'.str_replace("www.", "", $_SERVER["SERVER_NAME"]),
	'smtp_server' => '',
	'smtp_port' => '',
	'smtp_secure' => 'none',
	'smtp_username' => '',
	'smtp_password' => '',
	'smtp_from_name' => UAP_TITLE,
	'smtp_from_email' => 'noreply@'.str_replace("www.", "", $_SERVER["SERVER_NAME"])
);
$mail_methods = array('mail' => 'PHP Mail()', 'smtp' => 'SMTP');
$smtp_secures = array('none' => 'None', 'ssl' => 'SSL', 'tls' => 'TLS');

$folders = array();
if (!file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'plugins')) mkdir(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'plugins', 0777, true);
if (!file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data')) mkdir(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data', 0777, true);
if (!file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'temp')) mkdir(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'temp', 0777, true);

if (!is_writable(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'plugins')) {
	$folders[] = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'plugins';
} else {
	if (!file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'index.html')) {
		$result = file_put_contents(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'index.html', '<html><head><script>location.href="http://codecanyon.net/user/halfdata/portfolio?ref=halfdata";</script></head><body></body></html>');
		if (!$result) $folders[] = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'plugins';
	}
}
if (!is_writable(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data')) {
	$folders[] = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data';
} else {
	if (!file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'index.html')) {
		$result = file_put_contents(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'index.html', '<html><head><script>location.href="http://codecanyon.net/user/halfdata/portfolio?ref=halfdata";</script></head><body></body></html>');
		if (!$result) $folders[] = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data';
	}
}
if (!is_writable(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'temp')) {
	$folders[] = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'temp';
} else {
	if (!file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'index.html')) {
		$result = file_put_contents(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'index.html', '<html><head><script>location.href="http://codecanyon.net/user/halfdata/portfolio?ref=halfdata";</script></head><body></body></html>');
	}
	if (!file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'plugins.txt')) {
		$items = '[{"slug":"green-forms","name":"Green Forms","url":"https://greenforms.pro/","icon":"far fa-newspaper"},{"slug":"green-popups","name":"Green Popups","url":"https://codecanyon.net/item/layered-popups-for-wordpress/5978263?ref=halfdata","icon":"far fa-newspaper"},{"slug":"green-popups-tabs","name":"Side Tabs - Green Popups Add-On","url":"https://codecanyon.net/item/side-tabs-layered-popups-addon/10335326?ref=halfdata","icon":"fas fa-arrow-circle-right"},{"slug":"digital-paybox","name":"Digital Paybox","url":"https://codecanyon.net/item/digital-paybox-pay-and-download/2637036?ref=halfdata","icon":"fas fa-dollar-sign"},{"slug":"code-shop","name":"Code Shop","url":"https://codecanyon.net/item/code-shop-for-wordpress/5687817?ref=halfdata","icon":"far fa-credit-card"},{"slug":"stripe-green-downloads","name":"Stripe Green Downloads","url":"https://codecanyon.net/item/stripe-instant-downloads/5182437?ref=halfdata","icon":"fab fa-stripe"},{"slug":"paypal-green-downloads","name":"PayPal Green Downloads","url":"https://codecanyon.net/item/paypal-green-downloads-wordpress-plugin/25381797?ref=halfdata","icon":"fab fa-paypal"},{"slug":"green-box","name":"Green Box","url":"https://codecanyon.net/item/banner-manager-for-wordpress/2561323?ref=halfdata","icon":"fas fa-ad"},{"slug":"green-donations","name":"Green Donations","url":"https://codecanyon.net/item/donation-manager-for-wordpress/2380174?ref=halfdata","icon":"far fa-money-bill-alt"},{"slug":"green-lines","name":"Green Lines","url":"https://codecanyon.net/item/simple-string/1922394?ref=halfdata","icon":"fas fa-ad"}]';
		$result = file_put_contents(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'plugins.txt', $items);
		if (!$result) $folders[] = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'temp';
	}
}
$global_message = '';
if (!empty($folders)) {
	$global_message = '<div class="global-message global-message-danger">'.sprintf(esc_html__('Please make sure that the following directories exist and writable (set permissions 0777): %s', 'hap'), '<br /><em>'.implode('<br />', $folders).'</em>').'</div>';
	$writeable = false;
} else $writeable = true;

if (array_key_exists('m', $_REQUEST) && !empty($_REQUEST['m'])) {
	if ($_REQUEST['m'] == 'pi') $global_message .= '<div id="upload-message"><div class="global-message global-message-success">'.esc_html__('Plugin successfully installed.', 'hap').'</div></div>';
	else if ($_REQUEST['m'] == 'pa') $global_message .= '<div id="upload-message"><div class="global-message global-message-success">'.esc_html__('Plugin successfully activated.', 'hap').'</div></div>';
	else if ($_REQUEST['m'] == 'pd') $global_message .= '<div id="upload-message"><div class="global-message global-message-success">'.esc_html__('Plugin successfully deactivated.', 'hap').'</div></div>';
}

$wp_filters = array();
$scripts = array();
$styles = array();
$menu = array();

class WP_Error {
	var $message;
	function __construct($_code = '', $_message = '', $_data = '') {
		$this->message = $_message;
	}
	function get_error_message() {
		return $this->message;
	}
}
date_default_timezone_set('UTC');
header('Content-type: text/html; charset=utf-8');
?>