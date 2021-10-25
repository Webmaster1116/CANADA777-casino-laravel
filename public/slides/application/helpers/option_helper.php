<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Nwdthemes Standalone Slider Revolution
 *
 * @package     StandaloneRevslider
 * @author		Nwdthemes <mail@nwdthemes.com>
 * @link		http://nwdthemes.com/
 * @copyright   Copyright (c) 2015. Nwdthemes
 * @license     http://themeforest.net/licenses/terms/regular
 */

if( ! function_exists('get_option')) {

    /**
     *	Get option
     *
     *	@param	string	Handle
     *	@param	string	Default value
     *	@return	string	Option value
     */

	function get_option($handle, $default = false) {
		$ci = &get_instance();
		$ci->load->model('option_model', 'Option');

		if ($value = $ci->Option->get_option($handle)) {
			if ((strpos($value, 'a:') !== false
                || strpos($value, 's:') !== false
                || strpos($value, 'O:') !== false
                || strpos($value, 'i:') !== false
                || strpos($value, 'b:') !== false)
                && (($unserializedValue = @unserialize($value)) !== false || $value == 'b:0;')) {
				$value = $unserializedValue;
			}
			return $value;
		} else {
			return $default;
		}
	}

}

if( ! function_exists('update_option')) {
    
    /**
     * Update option
     *
     * @param string $handle
     * @param string value
     */

	function update_option($handle, $option = '') {
		$ci = &get_instance();
		$ci->load->model('option_model', 'Option');
		$ci->Option->update_option($handle, $option);
	}
    
}

if( ! function_exists('add_option')) {

    /**
     * Add option
     *
     * @param string $handle
     * @param string value
     */

	function add_option($handle, $option = '', $deprecated = '', $autoload = 'yes') {
		update_option($handle, $option);
        return true;
	}

}

if( ! function_exists('delete_option')) {

    /**
     * Delete option
     *
     * @param string $handle
     */

	function delete_option($handle) {
		$ci = &get_instance();
		$ci->load->model('option_model', 'Option');
		$ci->Option->delete_option($handle);
	}

}