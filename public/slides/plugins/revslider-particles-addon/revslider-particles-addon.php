<?php
/*
Plugin Name: Slider Revolution Particles Effect
Plugin URI: http://www.themepunch.com/
Description: Add interactive particle animations to your sliders
Author: ThemePunch
Version: 1.0.6
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-particles-admin'
	'rs-particles-front'

*/

// delete_option('revslider_addon_particles_templates');

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_PARTICLES_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_PARTICLES_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_PARTICLES_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_particles_init(){

	new RsParticlesBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_particles_init');


?>