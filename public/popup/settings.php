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
if ($is_logged === false) {
	header('Location: '.admin_url('login.php'));
	exit;
}
if (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true) {
	header('Location: '.$options['url'].'?page='.HALFDATA_BASE_PAGE);
}

include_once(dirname(__FILE__).'/inc/plugins.php');
$page = array(
	'slug' => 'settings',
	'page-title' => esc_html__('General Settings', 'hap')
);

do_action('init');
do_action('admin_init');

do_action('admin_menu');
do_action('admin_enqueue_scripts');
include_once(dirname(__FILE__).'/inc/header.php');
?>
<div id="settings-data">
	<h2><?php echo esc_html__('General Settings', 'hap'); ?></h2>
	<div class="hap-panel">
		<h3><?php echo esc_html__('Mailing Settings', 'hap'); ?></h3>
		<div class="hap-panel-content">
			<div class="hap-parameter">
				<label><?php echo esc_html__('Mailing Method', 'hap'); ?>:</label>
				<div>
					<div class="hap-bar-selector">
						<input class="hap-radio" id="mail_method_mail" type="radio" name="mail_method" value="mail"<?php echo $options['mail_method'] == 'smtp' ? '' : ' checked="checked"'; ?> onchange="toggle_mail_method(this);"><label for="mail_method_mail"><?php echo esc_html__('PHP Mail() Function', 'hap'); ?></label><input class="hap-radio" id="mail_method_smtp" type="radio" name="mail_method" value="smtp"<?php echo $options['mail_method'] == 'smtp' ? ' checked="checked"' : ''; ?> onchange="toggle_mail_method(this);"><label for="mail_method_smtp"><?php echo esc_html__('SMTP', 'hap'); ?></label>
					</div>
					<em><?php echo esc_html__('All e-mail messages are sent using this mailing method.', 'hap'); ?></em>
				</div>
			</div>
			<div id="mail-method-mail"<?php echo $options['mail_method'] == 'smtp' ? ' style="display: none;"' : ''; ?>>
				<div class="hap-parameter">
					<label><?php echo esc_html__('Sender Name', 'hap'); ?>:</label>
					<div>
						<input type="text" id="mail_from_name" name="mail_from_name" value="<?php echo esc_html($options['mail_from_name']); ?>" class="widefat" />
						<em><?php echo esc_html__('Please enter sender name. All e-mail messages are sent using this name as "FROM:" header value.', 'hap'); ?></em>
					</div>
				</div>
				<div class="hap-parameter">
					<label><?php echo esc_html__('Sender Email', 'hap'); ?>:</label>
					<div>
						<input type="email" id="mail_from_email" name="mail_from_email" value="<?php echo esc_html($options['mail_from_email']); ?>" class="widefat" />
						<em><?php echo esc_html__('Please enter sender e-mail. All e-mail messages are sent using this e-mail as "FROM:" header value. It is recommended to set existing e-mail address.', 'hap'); ?></em>
					</div>
				</div>
			</div>
			<div id="mail-method-smtp"<?php echo $options['mail_method'] == 'smtp' ? '' : ' style="display: none;"'; ?>>
				<div class="hap-parameter">
					<label><?php echo esc_html__('Sender Name', 'hap'); ?>:</label>
					<div>
						<input type="text" id="smtp_from_name" name="smtp_from_name" value="<?php echo esc_html($options['smtp_from_name']); ?>" class="widefat" />
						<em><?php echo esc_html__('Please enter sender name. All e-mail messages are sent using this name as "FROM:" header value.', 'hap'); ?></em>
					</div>
				</div>
				<div class="hap-parameter">
					<label><?php echo esc_html__('Sender Email', 'hap'); ?>:</label>
					<div>
						<input type="email" id="smtp_from_email" name="smtp_from_email" value="<?php echo esc_html($options['smtp_from_email']); ?>" class="widefat" />
						<em><?php echo esc_html__('Please enter sender e-mail. All e-mail messages are sent using this e-mail as "FROM:" header value.', 'hap'); ?></em>
					</div>
				</div>
				<div class="hap-parameter">
					<label><?php echo esc_html__('Encryption', 'hap'); ?>:</label>
					<div>
						<select id="smtp_secure" name="smtp_secure">
<?php
			foreach ($smtp_secures as $key => $value) {
				echo '
							<option value="'.esc_html($key).'"'.($key == $options['smtp_secure'] ? ' selected="selected"' : '').'>'.esc_html($value).'</option>';
			}
?>
						</select>					
						<em><?php echo esc_html__('SMTP connection encryption system.', 'hap'); ?></em>
					</div>
				</div>
				<div class="hap-parameter">
					<label><?php echo esc_html__('Server', 'hap'); ?>:</label>
					<div>
						<input type="text" id="smtp_server" name="smtp_server" value="<?php echo esc_html($options['smtp_server']); ?>" class="widefat" />
						<em><?php echo esc_html__('Hostname of the mail server.', 'hap'); ?></em>
					</div>
				</div>
				<div class="hap-parameter">
					<label><?php echo esc_html__('Port', 'hap'); ?>:</label>
					<div>
						<input type="text" id="smtp_port" name="smtp_port" value="<?php echo esc_html($options['smtp_port']); ?>" class="widefat" />
						<em><?php echo esc_html__('Port of the mail server.', 'hap'); ?></em>
					</div>
				</div>
				<div class="hap-parameter">
					<label><?php echo esc_html__('Username', 'hap'); ?>:</label>
					<div>
						<input type="text" id="smtp_username" name="smtp_username" value="<?php echo esc_html($options['smtp_username']); ?>" class="widefat" />
						<em><?php echo esc_html__('Username to use for SMTP authentication.', 'hap'); ?></em>
					</div>
				</div>
				<div class="hap-parameter">
					<label><?php echo esc_html__('Password', 'hap'); ?>:</label>
					<div>
						<input type="text" id="smtp_password" name="smtp_password" value="<?php echo esc_html($options['smtp_password']); ?>" class="widefat" />
						<em><?php echo esc_html__('Password to use for SMTP authentication.', 'hap'); ?></em>
					</div>
				</div>
			</div>
			<div class="hap-parameter">
				<label></label>
				<div>
					<a id="test-mailing-button" class="button-primary" onclick="return test_mailing(this);"><i class="far fa-envelope"></i> <?php echo esc_html__('Test Mailing', 'hap'); ?></a>
					<em><?php echo sprintf(esc_html__('Press button and check your inbox (%s). If you do not see test message, something does not work. Do not forget to check SPAM folder.', 'hap'), esc_html($options['login'])); ?></em>
				</div>
			</div>
		</div>
	</div>
	<div class="hap-panel">
		<h3><?php echo esc_html__('Miscellaneous Settings', 'hap'); ?></h3>
		<div class="hap-panel-content">
			<div class="hap-parameter">
				<label><?php echo esc_html__('Timezone', 'hap'); ?>:</label>
				<div>
					<select id="timezone_string" name="timezone_string" class="widefat">
<?php
	echo timezone_choice($options['timezone_string']);
?>
					</select>
					<em><?php echo sprintf(esc_html__('Choose either a city in the same timezone as you or a UTC timezone offset. Current UTC is %s', 'hap'), date('Y-m-d H:i')); ?></em>
				</div>
			</div>
			<div class="hap-parameter">
				<label><?php echo esc_html__('Redirect after login', 'hap'); ?>:</label>
				<div>
					<select id="login_redirect" name="login_redirect" class="widefat">
<?php
	echo '
						<option value="dashboard"'.($options['login_redirect'] == 'dashboard' ? ' selected="selected"' : '').'>'.esc_html__('Dashboard', 'hap').'</option>';
	foreach($menu as $slug => $item) {
		if (array_key_exists('submenu', $item)) {
			foreach ($item['submenu'] as $submenu_slug => $submenu_item) {
				echo '
						<option value="'.esc_html($submenu_slug).'"'.($options['login_redirect'] == $submenu_slug ? ' selected="selected"' : '').'>'.esc_html($item['menu-title'].': '.$submenu_item['menu-title']).'</option>';
			}
		}
	}
?>
					</select>
					<em><?php echo esc_html__('Select the page where to redirect you after successful login.', 'hap'); ?></em>
				</div>
			</div>
		</div>
	</div>
	<div class="hap-panel">
		<h3><?php echo esc_html__('Access Settings', 'hap'); ?></h3>
		<div class="hap-panel-content">
			<div class="hap-parameter">
				<label><?php echo esc_html__('Email', 'hap'); ?>:</label>
				<div>
					<input type="email" id="email" name="email" value="<?php echo esc_html($options['login']); ?>" class="widefat" />
					<em><?php echo esc_html__('Your email address is used as login to access Admin Panel.', 'hap'); ?></em>
				</div>
			</div>
			<div class="hap-parameter">
				<label><?php echo esc_html__('Password', 'hap'); ?>:</label>
				<div>
					<input type="password" id="password" name="password" value="" class="widefat" />
					<em><?php echo esc_html__('Enter new password. Leave it empty if you do not want to change the password.', 'hap'); ?></em>
				</div>
			</div>
			<div class="hap-parameter">
				<label></label>
				<div>
					<input type="password" id="repeat_password" name="repeat_password" value="" class="widefat" />
					<em><?php echo esc_html__('Repeat your new password.', 'hap'); ?></em>
				</div>
			</div>
		</div>
	</div>
	<hr>
	<div class="alignright">
		<input type="hidden" name="action" value="save-settings" />
		<a id="save-settings-button" class="button-primary pull-right" onclick="return save_settings(this);"><i class="fas fa-check"></i> <?php echo esc_html__('Save Settings', 'hap'); ?></a>
	</div>
</div>
<?php
include_once(dirname(__FILE__).'/inc/footer.php');
?>
