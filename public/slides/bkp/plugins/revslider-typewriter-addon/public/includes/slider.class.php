<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_TYPEWRITER_PLUGIN_PATH . 'framework/slider.front.class.php');

class RsTypewriterSliderFront extends RsAddonSliderFront {
	
	protected static $_Version,
					 $_PluginUrl, 
					 $_PluginTitle;
					 
	public function __construct($_version, $_pluginUrl, $_pluginTitle, $_isAdmin = false) {
		
		static::$_Version     = $_version;
		static::$_PluginUrl   = $_pluginUrl;
		static::$_PluginTitle = $_pluginTitle;
		
		if(!$_isAdmin) {
		
			parent::enqueueScripts();
			
		}
		else {
		
			parent::enqueuePreview();
			
		}
		
		parent::writeInitScript();
		
	}
	
}
?>