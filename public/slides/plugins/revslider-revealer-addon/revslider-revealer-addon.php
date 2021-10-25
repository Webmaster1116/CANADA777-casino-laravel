<?php
/*
Plugin Name: Slider Revolution Reveal Preloaders
Plugin URI: http://www.themepunch.com/
Description: Reveal your sliders in style
Author: ThemePunch
Version: 1.0.1
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-revealer-admin'
	'rs-revealer-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_REVEALER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_REVEALER_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_REVEALER_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_revealer_init(){
	
	new RsRevealerBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_revealer_init');


?>