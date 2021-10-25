<?php
/*
Plugin Name: Slider Revolution Polyfold Scroll Effect
Plugin URI: http://www.themepunch.com/
Description: Add sharp edges to your sliders as they scroll into and out of view
Author: ThemePunch
Version: 1.0.2
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-polyfold-admin'
	'rs-polyfold-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_POLYFOLD_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_POLYFOLD_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_POLYFOLD_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_polyfold_init(){

	new RsPolyfoldBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_polyfold_init');


?>