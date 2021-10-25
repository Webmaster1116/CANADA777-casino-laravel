<?php
/*
Plugin Name: Slider Revolution Distortion Effect AddOn
Plugin URI: http://www.themepunch.com/
Description: Enhance your slides with distortion effects
Author: ThemePunch
Version: 1.0.2
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-liquideffect-admin'
	'rs-liquideffect-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_LIQUIDEFFECT_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_LIQUIDEFFECT_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_LIQUIDEFFECT_PLUGIN_PATH . 'includes/base.class.php');

if(is_admin()) {

	add_filter('revslider_exportSlider_usedMedia', array('RsLiquidEffectSliderAdmin', 'export_slider'), 10, 4);
	add_filter('revslider_importSliderFromPost_modify_data', array('RsLiquidEffectSliderAdmin', 'import_slider'), 10, 3);
	
}

/**
* handle everyting by calling the following function *
**/
function rs_liquideffect_init(){
	
	new RsLiquideffectBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_liquideffect_init');


?>