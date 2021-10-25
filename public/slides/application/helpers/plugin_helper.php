<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Nwdthemes Standalone Slider Revolution
 *
 * @package     StandaloneRevslider
 * @author		Nwdthemes <mail@nwdthemes.com>
 * @link		http://nwdthemes.com/
 * @copyright   Copyright (c) 2016. Nwdthemes
 * @license     http://themeforest.net/licenses/terms/regular
 */



if( ! function_exists('is_plugin_active')) {

	/**
	 *	Checks if addon activated
	 *
	 *  @param  string  $plugin
	 *	@return	boolean
	 */

	function is_plugin_active($plugin) {
        $_ci = &get_instance();
        $_ci->load->library('plugin');
		return $_ci->plugin->isPluginActive($plugin);
	}

}


if( ! function_exists('is_plugin_inactive')) {

	/**
	 *	Checks if addon not activated
	 *
	 *  @param  string  $plugin
	 *	@return	boolean
	 */

	function is_plugin_inactive($plugin) {
        $_ci = &get_instance();
        $_ci->load->library('plugin');
		return ! $_ci->plugin->isPluginActive($plugin);
	}

}


if( ! function_exists('activate_plugin')) {

	/**
	 *	Activate plugin
	 *
	 *  @param  string  $plugin
	 *	@return	boolean
	 */

	function activate_plugin($plugin) {
        $_ci = &get_instance();
        $_ci->load->library('plugin');
		return $_ci->plugin->activatePlugin($plugin);
	}

}


if( ! function_exists('deactivate_plugins')) {

	/**
	 *	Deactivate plugin
	 *
	 *  @param  string  $plugin
	 *	@return	boolean
	 */

	function deactivate_plugins($plugin) {
        $_ci = &get_instance();
        $_ci->load->library('plugin');
		return $_ci->plugin->deactivatePlugin($plugin);
	}

}


if( ! function_exists('get_plugins')) {

	/**
	 *	Get plugins
	 *
	 *	return	boolean
	 */

	function get_plugins() {
        $_ci = &get_instance();
        $_ci->load->library('plugin');
        return $_ci->plugin->getPlugins();
	}

}


if( ! function_exists('plugins_url')) {

	/**
	 *	Get plugins url
	 *
	 *  @param  string  $file
	 *  @param  string  $plugin
	 *	@return	string
	 */

	function plugins_url($file, $plugin) {
        $_ci = &get_instance();
        $_ci->load->library('plugin');
        return $_ci->plugin->pluginUrl($plugin) . '/' . $file;
	}

}

if( ! function_exists('plugin_dir_url')) {
    /**
     *	Get plugin dir url
     *
     *  @param  string  $plugin
     *	@return	string
     */
    function plugin_dir_url($plugin) {
        $_ci = &get_instance();
        $_ci->load->library('plugin');
        return $_ci->plugin->pluginUrl($plugin) . '/';
    }
}

if( ! function_exists('plugin_dir_path')) {
	/**
	 *	Get plugin dir path
	 *
	 *  @param  string  $plugin
	 *	@return	string
	 */
	function plugin_dir_path($plugin) {
        $_ci = &get_instance();
        $_ci->load->library('plugin');
        return $_ci->plugin->pluginDir($plugin);
	}
}

if( ! function_exists('plugin_basename')) {

    /**
     *	Get plugin name
     *
     *  @param  string  $plugin
     *	@return	string
     */

    function plugin_basename($plugin) {
        $_ci = &get_instance();
        $_ci->load->library('plugin');
        return $_ci->plugin->pluginName($plugin);
    }

}

if( ! function_exists('update_plugin')) {

    /**
     *	Update plugin
     *
     *  @param  string  $plugin
     *	@return	boolean
     */

    function update_plugin($plugin) {
        $_ci = &get_instance();
        $_ci->load->library('plugin');
        return $_ci->plugin->updatePlugin($plugin);
    }

}
