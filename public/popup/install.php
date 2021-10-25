<?php
include_once(dirname(__FILE__).'/inc/functions.php');
include_once(dirname(__FILE__).'/inc/common.php');
include_once(dirname(__FILE__).'/inc/icdb.php');

$url_base = '//'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$filename = basename(__FILE__);
if (($pos = strpos($url_base, $filename)) !== false) $url_base = substr($url_base, 0, $pos);
$url_base = rtrim($url_base, '/').'/';

$actions = array('start', 'connect-db', 'save-config', 'create-admin');
$wpdb = null;
$db_ready = false;
$admin_created = false;
$tables_created = false;
if (file_exists(dirname(__FILE__).'/inc/config.php')) {
	include_once(dirname(__FILE__).'/inc/config.php');
	try {
		$wpdb = new ICDB(UAP_DB_HOST, UAP_DB_HOST_PORT, UAP_DB_NAME, UAP_DB_USER, UAP_DB_PASSWORD, UAP_TABLE_PREFIX);
		$db_ready = true;
		create_tables();
		$tables_created = true;
		get_options();
		if (!empty($options['login']) && !empty($options['password']) && !empty($options['url'])) $admin_created = true;
	} catch (Exception $e) {
	}
}
if ($db_ready && $tables_created && $admin_created) {
	if (isset($_POST['action'])) {
		$step = 5;
	} else {
		header('Location: '.$url_base);
		exit;
	}
} else if (!isset($_POST['action']) || !in_array($_POST['action'], $actions)) {
	include_once(dirname(__FILE__).'/inc/installer_header.php');
?>
	<h1><?php echo esc_html__('Admin Panel Setup', 'hap'); ?></h1>
	<div class="row">
		<?php echo esc_html__("Hi, I am a Wizard. I gonna help you to setup Admin Panel. You just need perform several simple steps. Let's start?", 'hap'); ?>
	</div>
	<div class="row"></div>
	<div class="row right">
		<input type="hidden" name="action" value="start" />
		<a id="continue" class="button" href="#" onclick="return continue_handler();"><i class="fas fa-angle-double-right"></i> <?php echo esc_html__('Continue', 'hap'); ?></a>
	</div>
<?php
	include_once(dirname(__FILE__).'/inc/installer_footer.php');
	exit;
} else {
	switch ($_POST['action']) {
		case 'start':
			if ($admin_created) $step = 5;
			else if ($tables_created) $step = 4;
			else $step = 2;
			break;
		
		case 'connect-db':
			if ($admin_created) $step = 5;
			else if ($tables_created) $step = 4;
			else {
				$host = trim(stripslashes($_POST['hostname']));
				$port = trim(stripslashes($_POST['port']));
				$username = trim(stripslashes($_POST['username']));
				$password = trim(stripslashes($_POST['password']));
				$database = trim(stripslashes($_POST['database']));
				$prefix = trim(stripslashes($_POST['prefix']));
				$errors = array();
				if (empty($host) || !is_hostname($host)) $errors[] = esc_html__('Inavlid MySQL Hostname.', 'hap');
				if (!empty($port) && $port != preg_replace('/[^0-9]/', '', $port)) $errors[] = esc_html__('Port value must be a number.', 'hap');
				if (empty($username)) $errors[] = esc_html__('Username can not be empty.', 'hap');
				else if (strpos($username, "'") !== false) $errors[] = esc_html__('Username can not contain single quote symbol.', 'hap');
				if (empty($database)) $errors[] = esc_html__('Invalid Database name.', 'hap');
				else if (strpos($database, "'") !== false) $errors[] = esc_html__('Database can npt contain single quote symbol.', 'hap');
				if (strpos($password, "'") !== false) $errors[] = esc_html__('Password can not contain single quote symbol.', 'hap');
				if (!preg_match('/^[a-zA-Z]+[a-zA-Z_]+$/', $prefix)) $errors[] = esc_html__('Table Prefix must contain letters and/or underscore symbol.', 'hap');
				if (!empty($errors)) {
					$return_object = array();
					$return_object['status'] = 'ERROR';
					$return_object['message'] = implode('<br />', $errors);
					echo json_encode($return_object);
					exit;
				}
				try {
					$wpdb = new ICDB($host, $port, $database, $username, $password, $prefix);
				} catch (Exception $e) {
					$return_object = array();
					$return_object['status'] = 'ERROR';
					$return_object['message'] = esc_html__('Can not connect to MySQL database using provided credentials.', 'hap');
					echo json_encode($return_object);
					exit;
				}
				try {
					create_tables();
				} catch (Exception $e) {
					$return_object = array();
					$return_object['status'] = 'ERROR';
					$return_object['message'] = sprintf(esc_html__('Can not create database tables. Make sure that user %s has sufficient privileges to manipulate database.', 'hap'), '<strong>'.esc_html($username).'</strong>');
					echo json_encode($return_object);
					exit;
				}
				$config_content = "<?php
define('UAP_DB_HOST', '".$host."');
define('UAP_DB_HOST_PORT', '".$port."');
define('UAP_DB_USER', '".$username."');
define('UAP_DB_PASSWORD', '".$password."');
define('UAP_DB_NAME', '".$database."');
define('UAP_TABLE_PREFIX', '".$prefix."');
?>";
				$result = file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'config.php', $config_content);
				if ($result !== false) {
					$login = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."options WHERE options_key = 'login'");
					$password = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."options WHERE options_key = 'password'");
					if (!empty($login) && !empty($password)) $step = 5;
					else $step = 4;
				} else $step = 3;
			}
			break;

		case 'save-config':
			if ($admin_created) $step = 5;
			else if ($tables_created) $step = 4;
			else {
				$return_object = array();
				$return_object['status'] = 'ERROR';
				$return_object['message'] = esc_html__('Hm. Seems config.php still does not contain correct database credentials. Please update it as it is said above.', 'hap');
				echo json_encode($return_object);
				exit;
			}
			break;

		case 'create-admin':
			if ($admin_created) $step = 5;
			else if (!$tables_created || !$db_ready) {
				$return_object = array();
				$return_object['status'] = 'ERROR';
				$return_object['message'] = esc_html__('Something went wrong. We still can not connect to database. Please try setup procedure again. Just refresh the page.', 'hap');
				echo json_encode($return_object);
				exit;
			} else {
				$email = strtolower(trim(stripslashes($_POST['email'])));
				$password = trim(stripslashes($_POST['password']));
				$errors = array();
				if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,20})$/i", $email)) $errors[] = esc_html__('Invalid e-mail format.', 'hap');
				if (strlen($password) < 6) $errors[] = esc_html__('Password length must be at least 6 characters.', 'hap');
				if (!empty($errors)) {
					$return_object = array();
					$return_object['status'] = 'ERROR';
					$return_object['message'] = implode('<br />', $errors);
					echo json_encode($return_object);
					exit;
				}
				try {
					$row = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."options WHERE options_key = 'login'");
					if ($row) {
						$wpdb->query("UPDATE ".$wpdb->prefix."options SET options_value = '".esc_sql($email)."' WHERE options_key = 'login'");
					} else {
						$wpdb->query("INSERT INTO ".$wpdb->prefix."options (options_key, options_value) VALUES ('login', '".esc_sql($email)."')");
					}
					$row = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."options WHERE options_key = 'password'");
					if ($row) {
						$wpdb->query("UPDATE ".$wpdb->prefix."options SET options_value = '".esc_sql(password_hash($password, PASSWORD_DEFAULT))."' WHERE options_key = 'password'");
					} else {
						$wpdb->query("INSERT INTO ".$wpdb->prefix."options (options_key, options_value) VALUES ('password', '".esc_sql(password_hash($password, PASSWORD_DEFAULT))."')");
					}
					$row = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."options WHERE options_key = 'url'");
					if ($row) {
						$wpdb->query("UPDATE ".$wpdb->prefix."options SET options_value = '".esc_sql($url_base)."' WHERE options_key = 'url'");
					} else {
						$wpdb->query("INSERT INTO ".$wpdb->prefix."options (options_key, options_value) VALUES ('url', '".esc_sql($url_base)."')");
					}
					$step = 5;
				} catch (Exception $e) {
					$return_object = array();
					$return_object['status'] = 'ERROR';
					$return_object['message'] = sprintf(esc_html__('Can not insert record into table. Make sure that user %s has sufficient privileges to manipulate database.', 'hap'), '<strong>'.esc_html(UAP_DB_USERNAME).'</strong>');
					echo json_encode($return_object);
					exit;
				}
			}
			break;
			
		default:
			echo esc_html__('We do not have to be here. Never.', 'hap');
			exit;
	}
}
	$return_object = array();
	$return_object['status'] = 'OK';
	$return_object['html'] = esc_html__('We do not have to see this message. Never.', 'hap');
if ($step == 2) {
	$return_object['html'] = '
	<h1>'.esc_html__('Setup Database', 'hap').'</h1>
	<div class="row">
		<label class="cell" for="hostname">'.esc_html__('MySQL Hostname', 'hap').':</label>
		<div class="cell">
			<input type="text" name="hostname" value="localhost" placeholder="localhost" />
			<span>'.esc_html__('Enter MySQL server hostname. Usually it is localhost, but we recommend to clarify this parameter from your hosting provider.', 'hap').'</span>
		</div>
	</div>
	<div class="row">
		<label class="cell" for="port">'.esc_html__('Port', 'hap').':</label>
		<div class="cell">
			<input type="text" name="port" value="" placeholder="3306" />
			<span>'.esc_html__('Enter MySQL server port. Leave it empty if you do not know the port or it is standard 3306.', 'hap').'</span>
		</div>
	</div>
	<div class="row">
		<label class="cell" for="username">'.esc_html__('Username', 'hap').':</label>
		<div class="cell">
			<input type="text" name="username" value="" placeholder="'.esc_html__('Username', 'hap').'" />
			<span>'.esc_html__('Enter MySQL server username. Find it in your hosting control panel.', 'hap').'</span>
		</div>
	</div>
	<div class="row">
		<label class="cell" for="password">'.esc_html__('Password', 'hap').':</label>
		<div class="cell">
			<input type="text" name="password" value="" placeholder="'.esc_html__('Password', 'hap').'" />
			<span>'.esc_html__('Enter password for MySQL server user. Find it in your hosting control panel.', 'hap').'</span>
		</div>
	</div>
	<div class="row">
		<label class="cell" for="database">'.esc_html__('Database', 'hap').':</label>
		<div class="cell">
			<input type="text" name="database" value="" placeholder="'.esc_html__('Database', 'hap').'" />
			<span>'.esc_html__('Enter MySQL database name. Find it in your hosting control panel.', 'hap').'</span>
		</div>
	</div>
	<div class="row">
		<label class="cell" for="prefix">'.esc_html__('Table Prefix', 'hap').':</label>
		<div class="cell">
			<input type="text" name="prefix" value="uap_" placeholder="'.esc_html__('Table Prefix', 'hap').'" />
			<span>'.esc_html__('Enter prefix for MySQL tables. If you plan to have several installations of admin panel, use unique prefix for each installation.', 'hap').'</span>
		</div>
	</div>
	<div class="row right">
		<input type="hidden" name="action" value="connect-db" />
		<a id="continue" class="button" href="#" onclick="return continue_handler();"><i class="fas fa-angle-double-right"></i> '.esc_html__('Continue', 'hap').'</a>
	</div>';
} else if ($step == 3) {
	$return_object['html'] = '
	<h1>'.esc_html__('Save Config File', 'hap').'</h1>
	<div class="row">
		'.sprintf(esc_html__('Unfortunately, we could not save database credentials into config.php (due to file permissions). You have to do it manually. Please edit file %s and overwrite its content with the following code.', 'hap'), '<br /><strong>'.dirname(__FILE__).DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'config.php'.'</strong><br />').'
		<textarea readonly="readonly" onclick="this.focus();this.select();">'.esc_html($config_content).'</textarea>
	</div>
	<div class="row right">
		<input type="hidden" name="action" value="save-config" />
		<a id="continue" class="button" href="#" onclick="return continue_handler();"><i class="fas fa-angle-double-right"></i> '.esc_html__('Continue', 'hap').'</a>
	</div>';
} else if ($step == 4) {
	$return_object['html'] = '
	<h1>'.esc_html__('Create Admin Account', 'hap').'</h1>
	<div class="row">
		<label class="cell" for="email">'.esc_html__('E-mail', 'hap').':</label>
		<div class="cell">
			<input type="text" name="email" placeholder="admin@website.com" />
			<span>'.esc_html__('E-mail address is your login to enter Admin Panel.', 'hap').'</span>
		</div>
	</div>
	<div class="row">
		<label class="cell" for="password">'.esc_html__('Password', 'hap').':</label>
		<div class="cell">
			<input type="text" name="password" placeholder="'.esc_html__('Password', 'hap').'" />
			<span>'.esc_html__('Use this password to enter Admin Panel.', 'hap').'</span>
		</div>
	</div>
	<div class="row right">
		<input type="hidden" name="action" value="create-admin" />
		<a id="continue" class="button" href="#" onclick="return continue_handler();"><i class="fas fa-angle-double-right"></i> '.esc_html__('Continue', 'hap').'</a>
	</div>';
} else if ($step == 5) {
	$return_object['html'] = '
	<h1>Finished</h1>
	<div class="row">
		'.esc_html__('Congratulation! Installation successfully completed. Now you can enter Admin Panel using created login/password and work there. Good luck!', 'hap').'
	</div>
	<div class="row"></div>
	<div class="row right">
		<a id="continue" class="button" href="'.$url_base.'"><i class="fas fa-angle-double-right"></i> '.esc_html__('Finish', 'hap').'</a>
	</div>';

}
echo json_encode($return_object);
exit;

?>