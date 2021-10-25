<?php
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
	if (isset($_POST['action'])) {
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
		$wpdb->query("UPDATE ".$wpdb->prefix."sessions SET registered = '".esc_sql(time())."', ip = '".$_SERVER['REMOTE_ADDR']."' WHERE session_id = '".esc_sql($session_id)."'");
		$is_logged = true;
	}
}
if ($is_logged === true) {
	if (isset($_GET['logout'])) {
		if (!empty($session_id)) {
			$wpdb->query("UPDATE ".$wpdb->prefix."sessions SET valid_period = '0' WHERE session_id = '".esc_sql($session_id)."'");
		}
		$is_logged = false;
	} else if (isset($_POST['action'])) {
		$return_object = array();
		$return_object['status'] = 'OK';
		$return_object['url'] = admin_url('admin.php');
		echo json_encode($return_object);
		exit;
	} else {
		header('Location: '.admin_url('admin.php'));
		exit;
	}
}
if (isset($_POST['action'])) {
	switch ($_POST['action']) {
		case 'login':
			if (isset($_POST['password'])) $password = trim(stripslashes($_POST['password']));
			else $password = '';
			if (isset($_POST['login'])) $login = trim(stripslashes($_POST['login']));
			else $login = '';
			$return_object = array();
			if (($login == $options['login'] && password_verify($password, $options['password'])) || (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true && $login == HALFDATA_DEMO_LOGIN && $password == HALFDATA_DEMO_PASSWORD)) {
				$session_id = random_string(16);
				$wpdb->query("INSERT INTO ".$wpdb->prefix."sessions (ip, session_id, registered, valid_period) VALUES ('".esc_sql($_SERVER['REMOTE_ADDR'])."', '".esc_sql($session_id)."', '".esc_sql(time())."', '7200')");
				if (PHP_VERSION_ID < 70300) setcookie('uap-auth', $session_id, time()+3600*24*180, '; samesite=strict');
				else setcookie('uap-auth', $session_id, array('lifetime' => time()+3600*24*180, 'samesite' => 'Strict'));
				$return_object['status'] = 'OK';
				$return_object['url'] = admin_url('admin.php').'?page='.rawurlencode($options['login_redirect']);
			} else {
				$return_object['status'] = 'ERROR';
				if (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true) $return_object['message'] = esc_html__('Invalid login or password!', 'hap');
				else $return_object['message'] = esc_html__('Invalid email or password!', 'hap');
			}
			echo json_encode($return_object);
			exit;
			break;
			
		case 'reset-password':
			if (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true) {
				$return_object = array();
				$return_object['status'] = 'ERROR';
				$return_object['message'] =esc_html__( 'This feature disabled in demo mode.', 'hap');
				echo json_encode($return_object);
				exit;
			}
			if (isset($_POST['login'])) $login = strtolower(trim(stripslashes($_POST['login'])));
			else $login = '';
			if ($login != $options['login']) {
				$return_object = array();
				$return_object['status'] = 'ERROR';
				$return_object['message'] = esc_html__('Invalid e-mail address.', 'hap');
				echo json_encode($return_object);
				exit;
			}
			$new_password = random_string(12);
			$wpdb->query("UPDATE ".$wpdb->prefix."options SET options_value = '".esc_sql(password_hash($new_password, PASSWORD_DEFAULT))."' WHERE options_key = 'password'");
			$message = sprintf(esc_html__('Hi %s,%sYou have requested new password to access Admin Panel. Here it is:%s%s%sRegards,%sAdmin Panel', 'hap'), esc_html($login), '<br /><br />', '<br /><br />', esc_html($new_password), '<br /><br />', '<br />');
			if (wp_mail($login, esc_html__('New password for Admin Panel', 'hap'), $message)) {
				$return_object = array();
				$return_object['status'] = 'OK';
				$return_object['html'] = sprintf(esc_html__('E-mail with new password for Admin Panel has been sent successfully. Check your inbox and %senter Admin Panel%s.', 'hap'), '<a class="switch-to-login" href="#" onclick="return switch_reset();">', '</a>');
				echo json_encode($return_object);
				exit;
			} else {
				$return_object = array();
				$return_object['status'] = 'ERROR';
				$return_object['message'] = esc_html__('Hm. Something went wrong. We could not send e-mail with new password.', 'hap');
				echo json_encode($return_object);
				exit;
			}
			break;
			
		default:
			echo esc_html__('You do not have to be here. Never.', 'hap');
			exit;
	}
}
	include_once(dirname(__FILE__).'/inc/login_header.php');
?>
<div class="content-box" id="login-form">
	<h1><?php echo esc_html__('Enter Admin Panel', 'hap'); ?></h1>
<?php 
	if (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true) {
		echo '
	<div class="row">
		<label>'.esc_html__('Login', 'hap').': <strong>'.esc_html(HALFDATA_DEMO_LOGIN).'</strong></label><br />
		<label>'.esc_html__('Password', 'hap').': <strong>'.esc_html(HALFDATA_DEMO_PASSWORD).'</strong></label>
	</div>';
	}
?>	
	<div class="row">
		<label><?php echo (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true ? esc_html__('Login', 'hap') : esc_html__('Email', 'hap')); ?>:</label>
		<input class="input-field" type="email" name="login" value="<?php echo (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true ? HALFDATA_DEMO_LOGIN : ''); ?>" placeholder="<?php echo esc_html__('Email', 'hap'); ?>" />
	</div>
	<div class="row">
		<label><?php echo esc_html__('Password', 'hap'); ?>:</label>
		<input class="input-field" type="password" name="password" value="<?php echo (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true ? HALFDATA_DEMO_PASSWORD : ''); ?>" placeholder="<?php echo esc_html__('Password', 'hap'); ?>" />
	</div>
	<div class="row right">
		<?php echo (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true ? '' : '<a class="switch-form" href="#" onclick="return switch_login();">'.esc_html__('Forgot Password?', 'hap').'</a>'); ?>
		<input type="hidden" name="action" value="login" />
		<a id="login" class="button" href="#" onclick="return login();"><i class="fas fa-angle-double-right"></i> <?php echo esc_html__('Login', 'hap'); ?></a>
	</div>
</div>
<?php 
	if (!defined('HALFDATA_DEMO') || HALFDATA_DEMO !== true) {
		echo '
<div class="content-box" id="reset-form" style="display: none;">
	<h1>'.esc_html__('Reset Password', 'hap').'</h1>
	<div class="row">
		<label>'.esc_html__('Email', 'hap').':</label>
		<input class="input-field" type="email" name="login" value="" placeholder="'.esc_html__('Email', 'hap').'" />
	</div>
	<div class="row right">
		<a class="switch-form" href="#" onclick="return switch_reset();">'.esc_html__('I remember password!', 'hap').'</a>
		<input type="hidden" name="action" value="reset-password" />
		<a id="reset" class="button" href="#" onclick="return reset_password();"><i class="fas fa-angle-double-right"></i> '.esc_html__('Reset', 'hap').'</a>
	</div>
</div>';
	}
	include_once(dirname(__FILE__).'/inc/login_footer.php');

?>