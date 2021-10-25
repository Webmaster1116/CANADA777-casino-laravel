<?php
define('DOING_AJAX', true);
include_once(dirname(__FILE__).'/inc/functions.php');
include_once(dirname(__FILE__).'/inc/common.php');
include_once(dirname(__FILE__).'/inc/icdb.php');

$wpdb = null;
$ready = false;
if (file_exists(dirname(__FILE__).'/inc/config.php')) {
	include_once(dirname(__FILE__).'/inc/config.php');
	try {
		$wpdb = new ICDB(UAP_DB_HOST, UAP_DB_HOST_PORT, UAP_DB_NAME, UAP_DB_USER, UAP_DB_PASSWORD, UAP_TABLE_PREFIX);
		create_tables();
		get_options();
		if (!empty($options['login']) && !empty($options['password']) && !empty($options['url'])) $ready = true;
	} catch (Exception $e) {
	}
}
if (!$ready) {
	if (isset($_REQUEST['action'])) {
		$return_object = array();
		$return_object['status'] = 'ERROR';
		$return_object['message'] = esc_html__('Please install Admin Panel properly.', 'hap');
		echo json_encode($return_object);
		exit;
	}
	header('Location: '.admin_url('install.php'));
	exit;
}
$is_logged = false;
$session_id = '';
if (isset($_COOKIE['uap-auth'])) {
	$session_id = preg_replace('/[^a-zA-Z0-9]/', '', $_COOKIE['uap-auth']);
	$session_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."sessions WHERE session_id = '".esc_sql($session_id)."' AND registered + valid_period > '".esc_sql(time())."'");
	if ($session_details) {
		$wpdb->query("UPDATE ".$wpdb->prefix."sessions SET registered = '".esc_sql(time())."', ip = '".esc_sql($_SERVER['REMOTE_ADDR'])."' WHERE session_id = '".esc_sql($session_id)."'");
		$is_logged = true;
	}
}
include_once(dirname(__FILE__).'/inc/plugins.php');

do_action('init');
if ($is_logged) do_action('admin_init');

header('Access-Control-Allow-Origin: *');

switch ($_REQUEST['action']) {
	case 'save-settings':
		if (!current_user_can('manage_options')) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Sorry, you do not have permissions to perform this operation. Please login as administrator.', 'hap');
			echo json_encode($return_object);
			exit;
		}
		if (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('This operation is disabled in demo mode.', 'hap');
			echo json_encode($return_object);
			exit;
		}
		foreach ($options as $key => $value) {
			if ($key != 'password') {
				if (isset($_POST[$key])) {
					$options[$key] = trim(stripslashes($_POST[$key]));
				}
			}
		}
		if (isset($_POST['email'])) $options['login'] = trim(stripslashes($_POST['email']));
		$errors = array();
		if ($options['mail_method'] == 'mail') {
			if (empty($options['mail_from_name'])) $errors[] = esc_html__('Invalid sender name.', 'hap');
			if ($options['mail_from_email'] == '' || !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $options['mail_from_email'])) $errors[] = esc_html__('Invalid sender e-mail.', 'hap');
		} else if ($options['mail_method'] == 'smtp') {
			if (empty($options['smtp_from_name'])) $errors[] = esc_html__('Invalid sender name.', 'hap');
			if (empty($options['smtp_server']) || !is_hostname($options['smtp_server'])) $errors[] = esc_html__('Invalid SMTP server.', 'hap');
			if (empty($options['smtp_port']) || !ctype_digit($options['smtp_port'])) $errors[] = esc_html__('Invalid SMTP port.', 'hap');
			if (empty($options['smtp_username'])) $errors[] = esc_html__('Invalid SMTP username.', 'hap');
			if (empty($options['smtp_password'])) $errors[] = esc_html__('Invalid SMTP password.', 'hap');
		}
		if ($options['login'] == '' || !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $options['login'])) $errors[] = esc_html__('E-mail must be a valid e-mail address.', 'hap');
		if (isset($_POST['password'])) $password = trim(stripslashes($_POST['password']));
		else $password = '';
		if (isset($_POST['repeat_password'])) $repeat_password = trim(stripslashes($_POST['repeat_password']));
		else $repeat_password = '';
		if (!empty($password)) {
			if ($password == $repeat_password) {
				if (strlen($password) < 6) $errors[] = esc_html__('Password length must be at least 6 characters.', 'hap');
				else $options['password'] = password_hash($password, PASSWORD_DEFAULT);
			} else $errors[] = esc_html__('Password and its confirmation are not equal.', 'hap');
		}

		if (empty($options['timezone_string'])) {
			$options['gmt_offset'] = 0;
		} else if (preg_match( '/^UTC[+-]/', $options['timezone_string'])) {
			$options['gmt_offset'] = preg_replace( '/UTC\+?/', '', $options['timezone_string']);
		} else {
			$timezone_object = timezone_open($options['timezone_string']);
			$datetime_object = date_create();
			if ( false === $timezone_object || false === $datetime_object ) $options['gmt_offset'] = 0;
			else $options['gmt_offset'] = round(timezone_offset_get($timezone_object, $datetime_object)/3600, 2);
		}
		
		if (!empty($errors)) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Attention! Please correct the errors below and try again.', 'hap').'<br /><i class="fas fa-angle-double-right"></i> '.implode('<br /><i class="fas fa-angle-double-right"></i> ', $errors);
			echo json_encode($return_object);
			exit;
		}
		update_options();
		$return_object = array();
		$return_object['status'] = 'OK';
		$return_object['message'] = esc_html__('Settings successfully saved.', 'hap');
		echo json_encode($return_object);
		exit;
		break;

	case 'test-mailing':
		if (!current_user_can('manage_options')) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Sorry, you do not have permissions to perform this operation. Please login as administrator.', 'hap');
			echo json_encode($return_object);
			exit;
		}
		if (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('This operation is disabled in demo mode.', 'hap');
			echo json_encode($return_object);
			exit;
		}
		foreach ($options as $key => $value) {
			if (isset($_POST[$key])) {
				$options[$key] = trim(stripslashes($_POST[$key]));
			}
		}
		$message = sprintf(esc_html__('This is a test message. It was sent by %s (%s) using the following mailing parameters.', 'hap'), esc_html(UAP_TITLE), esc_html($options['url'])).'<br />';
		if ($options['mail_method'] == 'smtp') {
			$message .= esc_html__('Method: SMTP', 'hap').'<br />'.esc_html__('Sender Name', 'hap').': '.$options['smtp_from_name'].'<br />'.esc_html__('Sender Email', 'hap').': '.$options['smtp_from_email'].'<br />'.esc_html__('Encryption', 'hap').': '.$options['smtp_secure'].'<br />'.esc_html__('Server', 'hap').': '.$options['smtp_server'].'<br />'.esc_html__('Port', 'hap').': '.$options['smtp_port'].'<br />'.esc_html__('Username', 'hap').': '.$options['smtp_username'].'<br />'.esc_html__('Password', 'hap').': '.$options['smtp_password'];
		} else {
			$message .= esc_html__('Method: PHP Mail() function', 'hap').'<br />'.esc_html__('Sender Name', 'hap').': '.$options['mail_from_name'].'<br />'.esc_html__('Sender Email', 'hap').': '.$options['mail_from_email'];
		}
		
		$result = wp_mail($options['login'], esc_html__('Test Message', 'hap'), $message, '', array(), true);
		if ($result !== true) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = $result;
			echo '<hap-debug>'.json_encode($return_object).'</hap-debug>';
			exit;
		}
		
		$return_object = array();
		$return_object['status'] = 'OK';
		$return_object['message'] = sprintf(esc_html__('Test message successfully sent. Please check your inbox (%s).', 'hap'), esc_html($options['login']));
		echo json_encode($return_object);
		exit;
		break;
		
	case 'upload-plugin':
		if (!current_user_can('manage_options')) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Sorry, you do not have permissions to perform this operation. Please login as administrator.', 'hap');
			echo '<html><body>'.json_encode($return_object).'</body></html>';
			exit;
		}
		if (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('This operation is disabled in demo mode.', 'hap');
			echo '<html><body>'.json_encode($return_object).'</body></html>';
			exit;
		}
		if (empty($_FILES["upload-plugin"]["tmp_name"])) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('File was not uploaded properly. Please check its size. Probably it is too large.', 'hap');
			echo '<html><body>'.json_encode($return_object).'</body></html>';
			exit;
		}
		if (!class_exists('ZipArchive')) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('This operation requires "ZipArchive" class. It is not found.', 'hap');
			echo '<html><body>'.json_encode($return_object).'</body></html>';
			exit;
		}
		$zip = new ZipArchive();
		if($zip->open($_FILES["upload-plugin"]["tmp_name"]) !== true) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Can not open uploaded file.', 'hap');
			echo '<html><body>'.json_encode($return_object).'</body></html>';
			exit;
		}
		if (($directory = $zip->getNameIndex($i)) === false) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Uploaded zip-archive seems to be empty.', 'hap');
			echo '<html><body>'.json_encode($return_object).'</body></html>';
			exit;
		}
		$directory = rtrim($directory, '/');
		if ($zip->locateName($directory.'/uap.txt') === false) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Uploaded zip-archive is not compatible plugin.', 'hap');
			echo '<html><body>'.json_encode($return_object).'</body></html>';
			exit;
		}
		if (($info_encoded = $zip->getFromName($directory.'/uap.txt')) === false) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Can not read plugin info.', 'hap');
			echo '<html><body>'.json_encode($return_object).'</body></html>';
			exit;
		}
		$info = json_decode($info_encoded, true);
		if (!$info) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Can not read plugin info.', 'hap');
			echo '<html><body>'.json_encode($return_object).'</body></html>';
			exit;
		}
		if (!array_key_exists('slug', $info) || !array_key_exists('uap', $info) || !array_key_exists('version', $info) || !array_key_exists('file', $info) || $zip->locateName($directory.'/'.$info['file']) === false) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Invalid plugin info.', 'hap');
			echo '<html><body>'.json_encode($return_object).'</body></html>';
			exit;
		}
		$plugin_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."plugins WHERE slug = '".esc_sql($info['slug'])."'");
		if ($plugin_details || file_exists(dirname(__FILE__).'/content/plugins/'.$directory)) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Plugin already installed.', 'hap');
			echo '<html><body>'.json_encode($return_object).'</body></html>';
			exit;
		}
		if ($zip->extractTo(dirname(__FILE__).'/content/plugins/') === false) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Can not extract plugin from zip-archive.', 'hap');
			echo '<html><body>'.json_encode($return_object).'</body></html>';
			exit;
		}
		$return_object = array();
		$return_object['status'] = 'OK';
		$return_object['message'] = esc_html__('Plugin successfully installed!', 'hap');
		$return_object['url'] = admin_url('admin.php').'?m=pi';
		echo '<html><body>'.json_encode($return_object).'</body></html>';
		exit;
		break;

	case 'toggle-plugin':
		if (!current_user_can('manage_options')) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Sorry, you do not have permissions to perform this operation. Please login as administrator.', 'hap');
			echo json_encode($return_object);
			exit;
		}
		if (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('This operation is disabled in demo mode.', 'hap');
			echo json_encode($return_object);
			exit;
		}
		$slug = trim(stripslashes($_POST['slug']));
		$type = trim(stripslashes($_POST['type']));
		$plugin_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."plugins WHERE slug = '".esc_sql($slug)."'", ARRAY_A);
		if (!$plugin_details || !file_exists(dirname(__FILE__).'/content/plugins/'.$plugin_details['file'])) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Plugin not found.', 'hap');
			echo json_encode($return_object);
			exit;
		}
		if ($plugin_details['uap'] > UAP_CORE) {
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Plugin requires new version of Admin Panel. Please update it.', 'hap');
			echo json_encode($return_object);
			exit;
		}
		$return_object = array();
		$return_object['status'] = 'OK';
		if ($type == 'activate') {
			$wpdb->query("UPDATE ".$wpdb->prefix."plugins SET active = '1' WHERE slug = '".esc_sql($slug)."'");
			$message = 'pa';
		} else {
			$wpdb->query("UPDATE ".$wpdb->prefix."plugins SET active = '0' WHERE slug = '".esc_sql($slug)."'");
			$message = 'pd';
		}
		$return_object['message'] = $message;
		$return_object['url'] = admin_url('admin.php').'?m='.$message;
		echo json_encode($return_object);
		break;
		
	default:
		if ($is_logged) {
			if (array_key_exists('wp_ajax_'.$_REQUEST['action'], $wp_filters)) do_action('wp_ajax_'.$_REQUEST['action']);
		} else {
			if (array_key_exists('wp_ajax_nopriv_'.$_REQUEST['action'], $wp_filters)) do_action('wp_ajax_nopriv_'.$_REQUEST['action']);
		}
		echo '0';
		break;
}
?>