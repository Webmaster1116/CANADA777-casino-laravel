<?php
/*
Plugin Name: Slider Revolution Background FilmStrip Add-On
Plugin URI: http://www.themepunch.com/
Description: Display a continously rotating set of images for your slide backgrounds
Author: ThemePunch
Version: 1.0.2
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-filmstrip-admin'
	'rs-filmstrip-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_FILMSTRIP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_FILMSTRIP_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_FILMSTRIP_PLUGIN_PATH . 'includes/base.class.php');

if(is_admin()) {

	add_filter('revslider_exportSlider_usedMedia', array('RsFilmstripSliderAdmin', 'export_slider'), 10, 4);
	add_filter('revslider_importSliderFromPost_modify_data', array('RsFilmstripSliderAdmin', 'import_slider'), 10, 3);
	
}


/**
* handle everyting by calling the following function *
**/
function rs_filmstrip_init(){

	new RsFilmstripBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_filmstrip_init');


?>