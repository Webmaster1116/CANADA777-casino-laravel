<?php
if (!defined('UAP_CORE')) die('What are you doing here?');

$plugins = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."plugins WHERE active = '1' ORDER BY slug ASC", ARRAY_A);
foreach ($plugins as $plugin) {
	if (file_exists(dirname(dirname(__FILE__)).'/content/plugins/'.$plugin['file'])) include_once(dirname(dirname(__FILE__)).'/content/plugins/'.$plugin['file']);
}
?>