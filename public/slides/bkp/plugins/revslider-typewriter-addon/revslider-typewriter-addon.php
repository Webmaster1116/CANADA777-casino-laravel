<?php
/*
Plugin Name: Slider Revolution Typewriter Effect
Plugin URI: http://www.themepunch.com/
Description: Enhance your slider's text with typewriter effects
Author: ThemePunch
Version: 1.0.3.1
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:

	'rs-typewriter-admin'
	'rs-typewriter-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_TYPEWRITER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_TYPEWRITER_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_TYPEWRITER_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_typewriter_init(){

	new RsTypewriterBase();

}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_typewriter_init');


?>