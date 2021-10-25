<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

class RsAddonPaintBrushSliderAdmin {
	
	protected function init() {
		
		add_filter('revslider_slider_addons', array($this, 'add_addon_settings'), 10, 2);
		
	}
	
	public function add_addon_settings($_settings, $_slider){
		
		static::_init($_slider);
		
		$_settings[static::$_Title] = array(
		
			'title'		 => 'Paint-Brush',
			'icon'		 => static::$_Icon,
			'markup'	 => static::$_Markup,
		    'javascript' => static::$_JavaScript
		   
		);
		
		return $_settings;
		
	}
	
}
?>