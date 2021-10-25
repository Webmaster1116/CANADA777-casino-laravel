<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class rs_whiteboard_fe_slide {
	
	public static function init(){
		
		add_action('revslider_add_layer_attributes', array('rs_whiteboard_fe_slide', 'add_layer_values'), 10, 3);
		
	}
	
	public static function add_layer_values($layer, $slide, $slider){
		if($slider->getParam("wb_enable","off") === 'off'){
			return false;
		}
		
		$attr = array();
		
		//$slider->getParam('handle', 'default');
		//$slide->getParam('handle', 'default');
		
		$whiteboard = (array)RevSliderFunctions::getVal($layer, "whiteboard", array());
		
		$hand_function = RevSliderFunctions::getVal($whiteboard, "hand_function", 'off');
		switch($hand_function){
			case 'write':
				$attr['hand_function'] = $hand_function;
				if(RevSliderFunctions::getVal($whiteboard, "hand_type", 'right') !== 'right')
					$attr['hand_type'] = RevSliderFunctions::getVal($whiteboard, "hand_type", 'right');
				if(RevSliderFunctions::getVal($whiteboard, "jitter_distance", '80') !== '80')
					$attr['jitter_distance'] = RevSliderFunctions::getVal($whiteboard, "jitter_distance", '80');
				if(RevSliderFunctions::getVal($whiteboard, "jitter_distance_horizontal", '100') !== '100')
					$attr['jitter_distance_horizontal'] = RevSliderFunctions::getVal($whiteboard, "jitter_distance_horizontal", '100');
				if(RevSliderFunctions::getVal($whiteboard, "jitter_repeat", '5') !== '5')
					$attr['jitter_repeat'] = RevSliderFunctions::getVal($whiteboard, "jitter_repeat", '5');
				if(RevSliderFunctions::getVal($whiteboard, "jitter_offset", '10') !== '10')
					$attr['jitter_offset'] = RevSliderFunctions::getVal($whiteboard, "jitter_offset", '10');
				if(RevSliderFunctions::getVal($whiteboard, "jitter_offset_horizontal", '00') !== '00')
					$attr['jitter_offset_horizontal'] = RevSliderFunctions::getVal($whiteboard, "jitter_offset_horizontal", '0');
				if(RevSliderFunctions::getVal($whiteboard, "hand_angle", '10') !== '10')
					$attr['hand_angle'] = RevSliderFunctions::getVal($whiteboard, "hand_angle", '10');
				if(RevSliderFunctions::getVal($whiteboard, "hand_angle_repeat", '3') !== '3')
					$attr['hand_angle_repeat'] = RevSliderFunctions::getVal($whiteboard, "hand_angle_repeat", '3');
				if(RevSliderFunctions::getVal($whiteboard, "hand_gotolayer", 'off') !== 'off')
					$attr['goto_next_layer'] = RevSliderFunctions::getVal($whiteboard, "hand_gotolayer", 'off');
				
			break;
			case 'draw':
				$attr['hand_function'] = $hand_function;
				if(RevSliderFunctions::getVal($whiteboard, "hand_type", 'right') !== 'right')
					$attr['hand_type'] = RevSliderFunctions::getVal($whiteboard, "hand_type", 'right');				
				if(RevSliderFunctions::getVal($whiteboard, "jitter_repeat", '5') !== '5')
					$attr['jitter_repeat'] = RevSliderFunctions::getVal($whiteboard, "jitter_repeat", '5');
				if(RevSliderFunctions::getVal($whiteboard, "jitter_distance", '80') !== '80')
					$attr['jitter_distance'] = RevSliderFunctions::getVal($whiteboard, "jitter_distance", '80');
				if(RevSliderFunctions::getVal($whiteboard, "jitter_offset", '10') !== '10')
					$attr['jitter_offset'] = RevSliderFunctions::getVal($whiteboard, "jitter_offset", '10');
				if(RevSliderFunctions::getVal($whiteboard, "jitter_distance_horizontal", '100') !== '100')
					$attr['jitter_distance_horizontal'] = RevSliderFunctions::getVal($whiteboard, "jitter_distance_horizontal", '100');
				if(RevSliderFunctions::getVal($whiteboard, "jitter_offset_horizontal", '0') !== '0')
					$attr['jitter_offset_horizontal'] = RevSliderFunctions::getVal($whiteboard, "jitter_offset_horizontal", '0');
				if(RevSliderFunctions::getVal($whiteboard, "hand_angle", '10') !== '10')
					$attr['hand_angle'] = RevSliderFunctions::getVal($whiteboard, "hand_angle", '10');
				if(RevSliderFunctions::getVal($whiteboard, "hand_angle_repeat", '3') !== '3')
					$attr['hand_angle_repeat'] = RevSliderFunctions::getVal($whiteboard, "hand_angle_repeat", '3');
				if(RevSliderFunctions::getVal($whiteboard, "hand_gotolayer", 'off') !== 'off')
					$attr['goto_next_layer'] = RevSliderFunctions::getVal($whiteboard, "hand_gotolayer", 'off');
				
				if(RevSliderFunctions::getVal($whiteboard, "hand_direction", 'left_to_right') !== 'left_to_right')
					$attr['hand_direction'] = RevSliderFunctions::getVal($whiteboard, "hand_direction", 'left_to_right');
			break;
			case 'move':
				$attr['hand_function'] = $hand_function;
				if(RevSliderFunctions::getVal($whiteboard, "hand_type", 'right') !== 'right')
					$attr['hand_type'] = RevSliderFunctions::getVal($whiteboard, "hand_type", 'right');

				$attr['hand_full_rotation'] = RevSliderFunctions::getVal($whiteboard, "hand_full_rotation", '0');
				$attr['hand_x_offset'] = RevSliderFunctions::getVal($whiteboard, "hand_x_offset", '0');
				$attr['hand_y_offset'] = RevSliderFunctions::getVal($whiteboard, "hand_y_offset", '0');
			break;
			case 'off':
				//do nothing
			break;
		}
		
		if(!empty($attr)){
			$slider->setParam('wb_is_used', true);
			echo "			data-whiteboard='".self::json_encode_for_frontend($attr)."'"."\n";
		}
		
	}
	
	
	public static function json_encode_for_frontend($arr){
		$json = '';
		if(!empty($arr)){
			$json = json_encode($arr);
		}
		
		return($json);
	}
	
}
?>