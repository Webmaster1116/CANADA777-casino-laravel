<?php
/*
Plugin Name: Slider Revolution Whiteboard Add-on
Plugin URI: http://www.themepunch.com/
Description: Create Hand-Drawn Presentations that are understandable, memorable & engaging
Author: ThemePunch
Version: 1.0.6.1
Author URI: http://themepunch.com
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WHITEBOARD_PLUGIN_URL', str_replace('index.php','',plugins_url( 'index.php', __FILE__ )));
define( 'WHITEBOARD_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'WHITEBOARD_FILE_PATH', __FILE__ );
define( 'WHITEBOARD_VERSION', '1.0.6.1');


require_once(WHITEBOARD_PLUGIN_PATH.'includes/base.class.php');

add_action('plugins_loaded', 'rs_whiteboard_init');

function rs_whiteboard_init(){
	$wb_base = new rs_whiteboard_base();
}

?>