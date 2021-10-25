<?php
/*
Plugin Name: Slider Revolution Background Slicey Add-On
Plugin URI: http://www.themepunch.com/
Description: Display a continously sliding set of images for your slide backgrounds
Author: ThemePunch
Version: 1.0.2
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-slicey-admin'
	'rs-slicey-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_SLICEY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_SLICEY_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_SLICEY_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_slicey_init(){

	new RsSliceyBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_slicey_init');


?>