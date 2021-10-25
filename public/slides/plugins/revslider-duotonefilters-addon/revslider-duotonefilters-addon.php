<?php
/*
Plugin Name: Slider Revolution Duotone-Filters Add-On
Plugin URI: https://www.themepunch.com/
Description: Add Duotone-Filters to your Slider Images
Author: ThemePunch
Version: 1.0.2.1
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-duotonefilters-admin'
	'rs-duotonefilters-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_DUOTONEFILTERS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_DUOTONEFILTERS_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_DUOTONEFILTERS_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_duotonefilters_init(){

	new RsDuotoneFiltersBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_duotonefilters_init');


?>