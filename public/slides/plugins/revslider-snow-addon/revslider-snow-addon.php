<?php
/*
Plugin Name: Slider Revolution Holiday Snow
Plugin URI: http://www.themepunch.com/
Description: Add animated snow to any Slider
Author: ThemePunch
Version: 1.0.5
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-snow-admin'
	'rs-snow-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_SNOW_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_SNOW_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_SNOW_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_snow_init(){
	
	new RsSnowBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_snow_init');


?>