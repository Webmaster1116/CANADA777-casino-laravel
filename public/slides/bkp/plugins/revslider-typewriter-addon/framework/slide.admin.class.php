<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsAddonSlideAdmin {
	
	protected function init() {
		
		add_filter('revslider_slide_addons', array($this, 'add_addon_settings'), 10, 3);
		
	}
	
	public function add_addon_settings($_settings, $_slide, $_slider){
		
		// only add to slide editor if enabled from slider settings first
		if($_slider->getParam(static::$_Title . '_defaults_enabled', false) == 'true') {
		
			static::_init($_slider);
			
			$_settings[static::$_Title] = array(
			
				'title'		 => ucfirst(static::$_Title) . '_',
				'markup'	 => static::$_Markup,
				'javascript' => static::$_JavaScript
			   
			);
			
		}
		
		return $_settings;
		
	}
	
}
?>