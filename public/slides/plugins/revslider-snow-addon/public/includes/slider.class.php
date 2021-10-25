<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_SNOW_PLUGIN_PATH . 'framework/slider.front.class.php');

class RsSnowSliderFront extends RsAddonSnowSliderFront {
	
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
		add_action('revslider_fe_javascript_option_output', array($this, 'write_init_options'), 10, 1);
		
	}
	
	public function write_init_options($_slider) {
		
		$_title = static::$_PluginTitle;
		$tabs = "\t\t\t\t\t\t";
		$tabsa = "\t\t\t\t\t\t\t";
		
		if($_slider->getParam($_title . '_enabled', false) == 'true') {
			
			echo $tabs . 'snow: {' . "\n";
			echo $tabsa . 'startSlide: "' . $_slider->getParam('snow_start_slide', 'first') . '",' . "\n";
			echo $tabsa . 'endSlide: "'   . $_slider->getParam('snow_end_slide', 'last') . '",' . "\n";
			echo $tabsa . 'maxNum: "'     . $_slider->getParam('snow_max_num', '400') . '",' . "\n";
			echo $tabsa . 'minSize: "'    . $_slider->getParam('snow_min_size', '0.2') . '",' . "\n";
			echo $tabsa . 'maxSize: "'    . $_slider->getParam('snow_max_size', '6') . '",' . "\n";
			echo $tabsa . 'minOpacity: "' . $_slider->getParam('snow_min_opacity', '0.3') . '",' . "\n";
			echo $tabsa . 'maxOpacity: "' . $_slider->getParam('snow_max_opacity', '1') . '",' . "\n";
			echo $tabsa . 'minSpeed: "'   . $_slider->getParam('snow_min_speed', '30') . '",' . "\n";
			echo $tabsa . 'maxSpeed: "'   . $_slider->getParam('snow_max_speed', '100') . '",' . "\n";
			echo $tabsa . 'minSinus: "'   . $_slider->getParam('snow_min_sinus', '1') . '",' . "\n";
			echo $tabsa . 'maxSinus: "'   . $_slider->getParam('snow_max_sinus', '100') . '",' . "\n";
			echo $tabs . '},' . "\n";
			
		}
	
	}
	
}
?>