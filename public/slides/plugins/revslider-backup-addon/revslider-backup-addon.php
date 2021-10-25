<?php
/*
Plugin Name: Slider Revolution Backup Add-on
Plugin URI: http://www.themepunch.com/
Description: Make Backups Revisions for your safety
Author: ThemePunch
Version: 1.0.2
Author URI: http://themepunch.com
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'RS_BACKUP_PLUGIN_URL', str_replace('index.php','',plugins_url( 'index.php', __FILE__ )));
define( 'RS_BACKUP_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'RS_BACKUP_FILE_PATH', __FILE__ );
define( 'RS_BACKUP_VERSION', '1.0.2');


require_once(RS_BACKUP_PLUGIN_PATH.'includes/base.class.php');

add_action('plugins_loaded', 'rs_backup_init');

function rs_backup_init(){
	$wb_base = new rs_backup_base();
}


/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array('rs_backup_slide', 'create_tables' ));

?>