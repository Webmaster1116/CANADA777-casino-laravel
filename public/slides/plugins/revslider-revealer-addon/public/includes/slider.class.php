<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_REVEALER_PLUGIN_PATH . 'framework/slider.front.class.php');

class RsRevealerSliderFront extends RsAddonRevealerSliderFront {
	
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
		$tabs = "\t\t\t";
		$tabsa = "\t\t\t\t";
		
		if($_slider->getParam($_title . '_enabled', false) == 'true') {
			
			$_color = $_slider->getParam('revealer_color', '#000000');
			$_spinner = $_slider->getParam('revealer_spinner', 'default');
			$_spinnerColor = $_slider->getParam('revealer_spinner_color', '#FFFFFF');
			
			$_overlay_enabled = $_slider->getParam('revealer_overlay_enabled', false) == 'true';
			if($_overlay_enabled) $_overlay_color = $_slider->getParam('revealer_overlay_color', '#000000');
			
			if(class_exists('TPColorpicker')) {
				
				$_color = TPColorpicker::get($_color);
				if($_overlay_enabled) $_overlay_color = TPColorpicker::get($_overlay_color);
				
				if($_spinner == '2') {
					$_spinnerColor = TPColorpicker::processRgba($_spinnerColor);
					$_spinnerColor = str_replace('rgb', 'rgba', $_spinnerColor);
					$_spinnerColor = str_replace(')', ',', $_spinnerColor);
				}
				
			}
			
			echo $tabs . 'revealer: {' . "\n";
			echo $tabsa . 'direction: "' . $_slider->getParam('revealer_direction', 'open_horizontal') . '",' . "\n";
			echo $tabsa . 'color: "' . $_color . '",' . "\n";
			echo $tabsa . 'duration: "' . $_slider->getParam('revealer_duration', '500') . '",' . "\n";
			echo $tabsa . 'delay: "' . $_slider->getParam('revealer_delay', '0') . '",' . "\n";
			echo $tabsa . 'easing: "' . $_slider->getParam('revealer_easing', 'Power2.easeOut') . '",' . "\n";
			
			if($_overlay_enabled) {
				echo $tabsa . 'overlay_enabled: true,' . "\n";
				echo $tabsa . 'overlay_color: "' . $_overlay_color . '",' . "\n";
				echo $tabsa . 'overlay_duration: "' . $_slider->getParam('revealer_overlay_duration', '500') . '",' . "\n";
				echo $tabsa . 'overlay_delay: "' . $_slider->getParam('revealer_overlay_delay', '0') . '",' . "\n";
				echo $tabsa . 'overlay_easing: "' . $_slider->getParam('revealer_overlay_easing', 'Power2.easeOut') . '",' . "\n";
			}
			
			echo $tabsa . 'spinner: "' . $_spinner . '",' . "\n";
			echo $tabsa . 'spinnerColor: "' . $_spinnerColor . '",' . "\n";
			echo $tabs . '},' . "\n";
			
		}
	
	}
	
}
?>