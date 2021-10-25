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


/**
 * Get translated string
 *
 * @param string $string
 * @return string
 */

if( ! function_exists('__'))
{
	function __($string = '')
	{
		$ci = &get_instance();
		if ( ! isset($ci->translator))
		{
			return $string;
		}
		return $ci->translator->gettext($string);
	}
}

/**
 * Output translation string
 *
 * @param string $string
 */

if( ! function_exists('_e'))
{
	function _e($string = '')
	{
		echo __($string);
	}
}

/**
 * Set current language
 *
 * @param string Languag
 */

if( ! function_exists('set_language'))
{
	function set_language($lang) {
		$ci = &get_instance();
        if (array_key_exists($lang, $ci->config->item('available_languages'))) {
    		if (isset($ci->session)) $ci->session->set_userdata('language', $lang);
    		update_option('language', $lang);
        }
	}
}

/**
 * Get current language
 *
 * @return string Languag
 */

if( ! function_exists('get_language'))
{
	function get_language() {
		$ci = &get_instance();
		$lang = isset($ci->session) ? $ci->session->userdata('language') : '';
		if ( ! $lang) {
			$lang = get_option('language', $ci->config->item('default_lang_code'));
		}
        if ( ! array_key_exists($lang, $ci->config->item('available_languages'))) {
            $lang = $ci->config->item('default_lang_code');
        }
		return $lang;
	}
}