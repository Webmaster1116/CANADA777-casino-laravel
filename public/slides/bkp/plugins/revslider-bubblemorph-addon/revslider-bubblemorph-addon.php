<?php
/*
Plugin Name: Slider Revolution BubbleMorph Add-On
Plugin URI: http://www.themepunch.com/
Description: Spice up your slides with a Bubble Morph effect
Author: ThemePunch
Version: 1.0.0
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-bubblemorph-admin'
	'rs-bubblemorph-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_BUBBLEMORPH_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_BUBBLEMORPH_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_BUBBLEMORPH_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_bubblemorph_init(){

	new RsBubblemorphBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_bubblemorph_init');


?>