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

class RS_Security extends CI_Security {

	public function csrf_verify() {
		if ( ! (isset($_GET['c']) && $_GET['c'] == 'account'))
		{
			return $this;
		}
		return parent::csrf_verify();
	}

	public function xss_clean($str, $is_image = FALSE) {
		if ( ! (isset($_GET['c']) && $_GET['c'] == 'account'))
		{
			return $str;
		}
		return parent::xss_clean($str, $is_image);
	}

}