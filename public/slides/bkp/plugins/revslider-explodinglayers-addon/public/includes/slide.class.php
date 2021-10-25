<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsExplodinglayersSlideFront {
	
	private $title;
	private $numbers = array(
			
		'padding_in'   => array('min' => 0,   'default' => 150),
		'padding_out'  => array('min' => 0,   'default' => 150),
		'size_in'      => array('min' => 1,   'default' => 5),
		'size_out'     => array('min' => 1,   'default' => 5),
		'speed_in'     => array('min' => 0.1, 'default' => 1),
		'speed_out'    => array('min' => 0.1, 'default' => 1),
		'density_in'   => array('min' => 1,   'default' => 1),
		'density_out'  => array('min' => 1,   'default' => 1),
		'power_in'     => array('min' => 0,   'default' => 2),
		'power_out'    => array('min' => 0,   'default' => 2),
		'duration_in'  => array('min' => 300, 'default' => 1000),
		'duration_out' => array('min' => 300, 'default' => 1000)
	
	);
	
	public function __construct($_title) {
		
		$this->title = $_title;
		add_action('revslider_add_layer_attributes', array($this, 'write_layer_attributes'), 10, 3);
	
	}
	
	private function svg($_key, $_ar) {
		
		if(strpos($_key, 'type_') === false) return $_ar;
		
		$_obj = array();
		$_svgs = RsExplodingLayersSvg::$_SVGs;
		
		foreach($_ar as $_val) {
			
			$_obj[] = $_val !== 'inherit' ? isset($_svgs[$_val]) ? $_svgs[$_val] : $_val : 'inherit';
		
		}
		return $_obj;
		
	}
	
	private function numeric($_key, $_ar) {
		
		if(!in_array($_key, $this->numbers)) return $_ar;
		
		$_obj = array();
		foreach($_ar as $_val) {
			
			if($_val !== 'inherit') {
				
				if(!is_numeric($_val)) $_val = $this->numbers[$_key]['default'];
				$_val = floatval($_val);
				$_obj[] = max($_val, $this->numbers[$_key]['min']);
				
			}
			else {
				
				$_obj[] = 'inherit';
				
			}
			
		}
		
		return $_obj;
		
	}
	
	public function write_layer_attributes($_layer, $_slide, $_slider) {
		
		$_enabled = $_slider->getParam('explodinglayers_enabled', false) == 'true';
		if(!$_enabled) return;
		
		$_options = RevSliderFunctions::getVal($_layer, 'explodinglayers', false);
		if(empty($_options)) return;
		
		$_frames = RevSliderFunctions::getVal($_layer, 'frames', array());
		if(empty($_frames)) return;
		
		$_start = RevSliderFunctions::getVal($_frames, 'frame_0', array());
		$_end = RevSliderFunctions::getVal($_frames, 'frame_999', array());
		
		if(empty($_start) || empty($_end)) return;
		
		$_easing_in = RevSliderFunctions::getVal($_start, 'easing', 'Power2.easeOut');
		$_speed_in = RevSliderFunctions::getVal($_start, 'speed', 1000);
		
		$_easing_out = RevSliderFunctions::getVal($_end, 'easing', 'Power2.easeOut');
		$_speed_out = RevSliderFunctions::getVal($_end, 'speed', 1000);
		
		$_options_in = array();
		$_options_out = array();
		$_svgs = RsExplodingLayersSvg::$_SVGs;
		
		$_enabled_in = RevSliderFunctions::getVal($_start, 'animation', false) === 'explodinglayers';
		$_out_transition = RevSliderFunctions::getVal($_end, 'animation', false);
		
		$_autoreverse = $_enabled_in && $_out_transition === 'auto';
		$_enabled_out = $_out_transition === 'explodinglayers' || $_autoreverse;
		
		if(is_array($_options)) {
			
			$_options['easing_in'] = $_easing_in;
			$_options['duration_in'] = $_speed_in;
			
			if(!$_autoreverse) {
				$_options['easing_out'] = $_easing_out;
				$_options['duration_out'] = $_speed_out;
			}
			
		}
		else {
		
			$_options->easing_in = $_easing_in;
			$_options->duration_in = $_speed_in;
			
			if(!$_autoreverse) {
				$_options->easing_out = $_easing_out;
				$_options->duration_out = $_speed_out;
			}
		
		}
		
		foreach($_options as $_key => $_value) {
			
			if(!is_array($_value)) $_value = array($_value, $_value, $_value, $_value);
			$_value = $this->numeric($_key, $_value);
			$_value = $this->svg($_key, $_value);

			if(strpos($_key, '_out') === false) $_options_in[$_key] = $_value;
			else if(!$_autoreverse) $_options_out[$_key] = $_value;
			
		}
		
		if($_autoreverse) {
			
			foreach($_options_in as $_key => $_value) {
				
				if(strpos($_key, '_in') === false) continue;
				$_key = str_replace('_in', '_out', $_key);
				
				if($_key === 'easing_out') {
					$_value = $_easing_out;
				}
				else if($_key === 'duration_out') {
					$_value = $_speed_out;
				}

				if(!is_array($_value)) $_value = array($_value, $_value, $_value, $_value);
				$_options_out[$_key] = $_value;
				
			}
			
		}
		
		if($_enabled_in && !empty($_options_in)) {

			$_options_in = json_encode($_options_in);
			echo "			data-explodinglayersin='" . $_options_in . "' " . "\n";
			
		}
		
		if($_enabled_out && !empty($_options_out)) {
			
			$_options_out = json_encode($_options_out);
			echo "			data-explodinglayersout='" . $_options_out . "' " . "\n";
			
		}
	
	}
	
}
?>