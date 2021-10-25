<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsSliceySlideFront {
	
	private $title;
	
	public function __construct($_title) {
		
		$this->title = $_title;
		add_action('revslider_add_li_data', array($this, 'write_slide_attributes'), 10, 3);
		add_action('revslider_add_layer_attributes', array($this, 'write_layer_attributes'), 10, 3);
		add_filter('revslider_putCreativeLayer', array($this, 'check_slicey'), 10, 3);
	
	}
	
	public function check_slicey($_layers, $_slider, $_static_slide) {
			
		$_enabled = $_slider->slider->getParam('slicey_enabled', false) == 'true';
		if(!$_enabled) {
			
			$_ar = array();
			foreach($_layers as $_layer) {
				
				$_isSlicey = false;
				if(array_key_exists('subtype', $_layer)) {
					
					$_slicey = RevSliderFunctions::getVal($_layer, 'subtype', false);
					$_isSlicey = $_slicey === 'slicey';
					
				}
				
				if(!$_isSlicey) $_ar[] = $_layer;
				
			}
			
			return $_ar;
			
		}

		return $_layers;
		
	}
	
	public function write_layer_attributes($_layer, $_slide, $_slider) {
		
		$_subtype = RevSliderFunctions::getVal($_layer, 'subtype', '');
		if($_subtype && $_subtype === 'slicey') {
			
			$_slicey = RevSliderFunctions::getVal($_layer, 'slicey', false);
			if($_slicey) {
				
				$_offset    = RevSliderFunctions::getVal($_slicey, 'scale_offset', false);
				$_blurStart = RevSliderFunctions::getVal($_slicey, 'blurlstart',  'inherit');
				$_blurEnd   = RevSliderFunctions::getVal($_slicey, 'blurlend',    'inherit');
				
				if($_offset !== false) {
				
					echo '			data-slicey_offset="'    . $_offset . '" ' . "\n";
					echo '			data-slicey_blurstart="' . $_blurStart . '" ' . "\n";
					echo '			data-slicey_blurend="'   . $_blurEnd . '" ' . "\n";
					
				}
				
			}
		
		}
	
	}
	
	public function write_slide_attributes($_slider, $_slide) {
		
		$_enabled = $_slider->getParam('slicey_enabled', false) == 'true';
		if(!$_enabled) return;
		
		$_options = $_slide->getParam('slicey_globals', false);
		
		if(!$_options) return;
		$_options = json_decode(stripslashes($_options), true);
		
		$_enabled = $_options['enabled'];
		if(!$_enabled) return;
		
		echo ' data-slicey_shadow="' . '0px 0px ' . $_options['blur'] . 'px ' . $_options['strength'] . 'px ' . $_options['color'] . '"';
		
	}
	
}
?>