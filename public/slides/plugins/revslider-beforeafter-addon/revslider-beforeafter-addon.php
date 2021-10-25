<?php
/*
Plugin Name: Slider Revolution Before/After Add-On
Plugin URI: https://www.themepunch.com/
Description: Create Before/After content for your Slides
Author: ThemePunch
Version: 1.0.3.2
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:

	'rs-beforeafter-admin'
	'rs-beforeafter-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_BEFOREAFTER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_BEFOREAFTER_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_BEFOREAFTER_PLUGIN_PATH . 'includes/base.class.php');

if(is_admin()) {

	add_filter('revslider_exportSlider_usedMedia', array('RsBeforeAfterSliderAdmin', 'export_slider'), 10, 4);
	add_filter('revslider_importSliderFromPost_modify_data', array('RsBeforeAfterSliderAdmin', 'import_slider'), 10, 3);

}

/**
* handle everyting by calling the following function *
**/
function rs_beforeafter_init(){

	new RsBeforeAfterBase();

}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_beforeafter_init');