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
include_once(dirname(__FILE__).'/inc/plugins.php');
$page = array(
	'slug' => 'dashboard',
	'page-title' => esc_html__('Dashboard', 'hap')
);
do_action('init');
do_action('admin_init');

do_action('admin_menu');
if (isset($_REQUEST['page'])) {
	foreach ($menu as $slug => $item) {
		if (array_key_exists('submenu', $item)) {
			$found = false;
			foreach ($item['submenu'] as $submenu_slug => $submenu_item) {
				if ($_REQUEST['page'] == $submenu_slug) {
					$page = $submenu_item;
					$page['slug'] = $submenu_slug;
					$page['parent'] = $slug;
					$found = true;
					break;
				}
			}
			if ($found) break;
		} else if ($_REQUEST['page'] == $slug) {
			$page = $item;
			$page['slug'] = $slug;
			break;
		}
	}
}
if (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true && $page['slug'] == 'dashboard') {
	header('Location: '.$options['url'].'?page='.HALFDATA_BASE_PAGE);
}
do_action('admin_enqueue_scripts');
include_once(dirname(__FILE__).'/inc/header.php');
?>
<?php
if ($page['slug'] == 'dashboard') {
	if ($writeable) {
?>
		<div style="display:none;">
			<iframe id="upload-target" name="upload-target" height="0" width="0" frameborder="0" onload="plugin_uploaded();"></iframe>
			<form id="upload-form" action="<?php echo admin_url('ajax.php'); ?>" method="post" enctype="multipart/form-data" target="upload-target" onsubmit="jQuery('#plugins-item-new i').attr('class', 'fas fa-spinner fa-spin');" >
			<input id="upload-plugin" name="upload-plugin" type="file" accept=".zip" onchange="jQuery('#upload-form').submit();" />
			<input id="action" name="action" type="hidden" value="upload-plugin" />
			<input type="submit" value="U" />
			</form>
		</div>
<?php
		$plugins = array();
		$items = scandir(dirname(__FILE__).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'plugins', 0);
		foreach ($items as $directory) {
			if (is_dir(dirname(__FILE__).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$directory) && $directory != '.' && $directory != '..') {
				if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$directory.DIRECTORY_SEPARATOR.'uap.txt')) {
					$info = json_decode(file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$directory.DIRECTORY_SEPARATOR.'uap.txt'), true);
					if (is_array($info) && !empty($info)) {
						$plugin_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."plugins WHERE slug = '".esc_sql($info['slug'])."'", ARRAY_A);
						if ($plugin_details) {
							$info['active'] = $plugin_details['active'] == 1 ? true : false;
							$wpdb->query("UPDATE ".$wpdb->prefix."plugins SET
								uap = '".esc_sql(intval($info['uap']))."',
								file = '".esc_sql($directory.DIRECTORY_SEPARATOR.$info['file'])."'
								WHERE slug = '".esc_sql($info['slug'])."'");
						} else {
							$info['active'] = false;
							$wpdb->query("INSERT INTO ".$wpdb->prefix."plugins (slug, uap, version, file, active, registered) VALUES (
								'".esc_sql($info['slug'])."', 
								'".esc_sql(intval($info['uap']))."',
								'0',
								'".esc_sql($directory.DIRECTORY_SEPARATOR.$info['file'])."',
								'0',
								'".esc_sql(time())."')");
						}
						$plugins[$info['slug']] = $info;
					}
				}
			}
		}
		if (sizeof($plugins) == 0) {
			$wpdb->query("DELETE FROM ".$wpdb->prefix."plugins");
		} else {
			$slugs = array_keys($plugins);
			foreach ($slugs as $key => $value) {
				$slugs[$key] = esc_sql($value);
			}
			$wpdb->query("DELETE FROM ".$wpdb->prefix."plugins WHERE slug NOT IN ('".implode("','", $slugs)."')");
		}
?>
		<h2><?php echo esc_html__('Installed Plugins', 'hap'); ?></h2>
		<div class="plugins">
<?php
		foreach ($plugins as $slug => $details) {
			echo '
			<div class="plugins-item'.($details['active'] ? ' plugins-item-active' : '').'" onclick="toggle_plugin(this, \''.esc_html($slug).'\', \''.($details['active'] ? 'deactivate' : 'activate').'\');">
				<i class="'.esc_html(!empty($details['icon']) ? $details['icon'] : 'far fa-file-alt').'"></i>
				<h4>'.esc_html($details['name']).'</h4>
				<label>'.esc_html__('Version', 'hap').':</label> '.esc_html($details['version']).'<br />
				<label>'.esc_html__('Status', 'hap').':</label> '.($details['active'] ? esc_html__('Active. Deactivate?', 'hap') : esc_html__('Not Active. Activate?', 'hap')).'
				<div class="plugins-item-spinner"><i class="fas fa-spinner fa-spin"></i></div>
			</div>';
		}
?>
			<a id="plugins-item-new" href="#" onclick="jQuery('#upload-plugin').click(); return false;"><i class="fas fa-plus"></i> <?php echo esc_html__('Add New Plugin', 'hap'); ?></a>
		</div>
<?php		
		$items_file = dirname(__FILE__).DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'plugins.txt';
		$items = null;
		$content = null;
		$use_cache = false;
		if (file_exists($items_file)) {
			if (filemtime($items_file)+3600*24*7 > time()) {
				$use_cache = true;
			}
		}
		if (!$use_cache) {
			try {
				$curl = curl_init('https://halfdata.com/hap/plugins.txt');
				curl_setopt($curl, CURLOPT_TIMEOUT, 5);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36');
				$response = curl_exec($curl);
				if (curl_error($curl)) {
					curl_close($curl);
				} else {
					$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
					curl_close($curl);
					if ($http_code == '200') {
						$result = json_decode($response, true);
						if ($result) $content = $response;
					}
				}
				if ($content) file_put_contents($items_file, $content);
				else file_put_contents($items_file, '', FILE_APPEND);
			} catch (Exception $e) {
			}
		}
		if (!$content) {
			if (file_exists($items_file)) $content = file_get_contents($items_file);
		}
		if ($content) $items = json_decode($content, true);
		if (is_array($items) && !empty($items)) {
			echo '
			<h2>'.esc_html__('Available Plugins', 'hap').'</h2>
			<div class="plugins">';
			foreach ($items as $details) {
				echo '
				<a class="plugins-item plugins-item-active" href="'.esc_html($details['url']).'" target="_blank">
					<i class="'.esc_html(!empty($details['icon']) ? $details['icon'] : 'far fa-file-alt').'"></i>
					<h4>'.esc_html($details['name']).'</h4>
					'.esc_html__('Read more...', 'hap').'
				</a>';
			}
			echo '
			</div>';
		}
	}
} else {
	if (!empty($page['function'])) {
		call_user_func_array($page['function'], array());
	}
}
include_once(dirname(__FILE__).'/inc/footer.php');
?>
