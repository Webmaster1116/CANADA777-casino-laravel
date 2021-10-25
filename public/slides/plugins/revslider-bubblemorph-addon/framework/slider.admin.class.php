<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

class RsAddonBubblemorphSliderAdmin {
	
	protected function init() {
		
		add_filter('revslider_slider_addons', array($this, 'add_addon_settings'), 10, 2);
		
	}
	
	public function add_addon_settings($_settings, $_slider){
		
		static::_init($_slider);
		
		$_settings[static::$_Title] = array(
		
			'title'		 => 'BubbleMorph',
			'icon'		 => static::$_Icon,
			'markup'	 => static::$_Markup,
		    'javascript' => static::$_JavaScript
		   
		);
		
		return $_settings;
		
	}
	
}
?>