<?php

/*
 * ==========================================================
 * ADMINISTRATION PAGE
 * ==========================================================
 *
 * Administration page to manage the settings and reply to the users.
 *
 */

global $SB_CONNECTION;
define('SB_PATH', getcwd());
if (!file_exists('config.php')) {
    $raw = str_replace(['[url]', '[name]', '[user]', '[password]', '[host]', '[port]'], '', file_get_contents('resources/config-source.php'));
    $file = fopen('config.php', 'w');
    fwrite($file, $raw);
    fclose($file);
}
require('config.php');
require('include/functions.php');
$is_cloud = defined('SB_CLOUD');
if ($is_cloud && (!defined('SB_DB_NAME') || empty($_COOKIE['sb-cloud']) || !sb_is_agent())) die('<script>document.location = "' . CLOUD_URL . '/account?login"</script>');
if ($is_cloud) sb_cloud_membership_validation();
$connection_check = sb_db_check_connection();
$connection_success = $connection_check === true;
$minify = false;
$sb_url = '';
if ($connection_success) {
    $sb_url = SB_URL . '/';
    $minify = sb_get_multi_setting('performance', 'performance-minify');
    sb_updates_validation();
}
require('include/components.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no" />
    <meta name="theme-color" content="#566069" />
    <title>
        <?php echo $connection_success && sb_get_setting('admin-title') ? sb_get_setting('admin-title') : 'Support Board' ?>
    </title>
    <script src="<?php echo $sb_url . 'js/min/jquery.min.js?v=' . SB_VERSION ?>"></script>
    <script src="<?php echo $sb_url . ($is_cloud || $minify ? 'js/min/main.min.js?v=' : 'js/main.js?v=') . SB_VERSION ?>"></script>
    <script src="<?php echo $sb_url . ($is_cloud || $minify ? 'js/min/admin.min.js?v=' : 'js/admin.js?v=') . SB_VERSION ?>"></script>
    <link rel="stylesheet" href="<?php echo $sb_url . 'css/min/admin.min.css?v=' . SB_VERSION ?>" media="all" />
    <link rel="stylesheet" href="<?php echo $sb_url . 'css/min/responsive-admin.min.css?v=' . SB_VERSION ?>" media="(max-width: 428px)" />
    <?php if (sb_get_setting('rtl-admin')) echo '<link rel="stylesheet" href="' . $sb_url . 'css/min/rtl-admin.min.css?v=' . SB_VERSION . '" />' ?>
    <link rel="shortcut icon" type="image/png" href="<?php echo $sb_url ?>media/icon.png" />
    <link rel="apple-touch-icon" href="<?php echo $sb_url ?>resources/pwa/icons/icon-192x192.png" />
    <link rel="manifest" href="<?php echo $sb_url ?>resources/pwa/manifest.json" />
    <?php if ($is_cloud) echo '<script src="' . CLOUD_URL . '/account/js/admin.js?v=' . SB_VERSION . '"></script>' ?>
    <?php
    if ($connection_success) {
        sb_js_global();
        sb_js_admin();
    }
    ?>
</head>
<body>
    <?php
    if (!$connection_success) {
        sb_installation_box($connection_check);
        die();
    }
    sb_component_admin();
    ?>
</body>
</html>