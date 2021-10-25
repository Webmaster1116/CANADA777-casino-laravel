<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsBubblemorphSlideFront {
	
	private $title;
	
	public function __construct($_title) {
		
		$this->title = $_title;
		add_action('revslider_add_layer_attributes', array($this, 'write_layer_attributes'), 10, 3);
		add_filter('revslider_putCreativeLayer', array($this, 'check_bubblemorph'), 10, 3);
	
	}
	
	public function check_bubblemorph($_layers, $_slider, $_static_slide) {
			
		$_enabled = $_slider->slider->getParam('bubblemorph_enabled', false) == 'true';
		if(!$_enabled) {
			
			$_ar = array();
			foreach($_layers as $_layer) {
				
				$_isBubblemorph = false;
				if(array_key_exists('subtype', $_layer)) {
					
					$_bubblemorph = RevSliderFunctions::getVal($_layer, 'subtype', false);
					$_isBubblemorph = $_bubblemorph === 'bubblemorph';
					
				}
				
				if(!$_isBubblemorph) $_ar[] = $_layer;
				
			}
			
			return $_ar;
			
		}

		return $_layers;
		
	}
	
	public function write_layer_attributes($_layer, $_slide, $_slider) {
		
		$_enabled = $_slider->getParam('bubblemorph_enabled', false) == 'true';
		if(!$_enabled) return;
		
		$_options = $_slide->getParam('bubblemorph_globals', false);
		
		if(!$_options) return;
		$_options = json_decode(stripslashes($_options), true);
		
		$_enabled = $_options['enabled'];
		if(!$_enabled) return;
		
		$_subtype = RevSliderFunctions::getVal($_layer, 'subtype', '');
		if($_subtype && $_subtype === 'bubblemorph') {
			
			$_bubblemorph = RevSliderFunctions::getVal($_layer, 'bubblemorph', false);
			if($_bubblemorph) {
				
				$_deformation = RevSliderFunctions::getVal($_layer, 'deformation', false);
				if(!empty($_deformation)) {
					
					$_deformation = json_decode(json_encode($_deformation), true);
					if(isset($_deformation['background-color']) && !empty($_deformation['background-color'])) {
						
						$_max           = RevSliderFunctions::getVal($_bubblemorph, 'max',          '6');
						$_blur_strength = RevSliderFunctions::getVal($_bubblemorph, 'blurstrength', '0');
						$_border_size   = RevSliderFunctions::getVal($_bubblemorph, 'bordersize',   '0');
						$_bufferx       = RevSliderFunctions::getVal($_bubblemorph, 'bufferx',      '0');
						$_buffery       = RevSliderFunctions::getVal($_bubblemorph, 'buffery',      '0');
						$_speedx        = RevSliderFunctions::getVal($_bubblemorph, 'speedx',       '0.25');
						$_speedy        = RevSliderFunctions::getVal($_bubblemorph, 'speedy',       '1');
						
						if(!is_array($_max)) $_max = array($_max, $_max, $_max, $_max);
						if(!is_array($_blur_strength)) $_blur_strength = array($_blur_strength, $_blur_strength, $_blur_strength, $_blur_strength);
						if(!is_array($_border_size)) $_border_size = array($_border_size, $_border_size, $_border_size, $_border_size);
						if(!is_array($_bufferx)) $_bufferx = array($_bufferx, $_bufferx, $_bufferx, $_bufferx);
						if(!is_array($_buffery)) $_buffery = array($_buffery, $_buffery, $_buffery, $_buffery);
						if(!is_array($_speedx)) $_speedx = array($_speedx, $_speedx, $_speedx, $_speedx);
						if(!is_array($_speedy)) $_speedy = array($_speedy, $_speedy, $_speedy, $_speedy);
						
						for($i = 0; $i < count($_speedx); $i++) {
							
							if($_speedx[$i] === 'inherit') $_speedx[$i] = $_speedx[$i - 1];
							if(floatval($_speedx[$i]) <= 0) $_speedx[$i] = 1;
							
						}
						
						for($i = 0; $i < count($_speedy); $i++) {
							
							if($_speedy[$i] === 'inherit') $_speedy[$i] = $_speedy[$i - 1];
							if(floatval($_speedy[$i]) <= 0) $_speedy[$i] = 1;
							
						}
						
						$_max           = implode('|', $_max);
						$_bufferx       = implode('|', $_bufferx);
						$_buffery       = implode('|', $_buffery);
						$_speedx        = implode('|', $_speedx);
						$_speedy        = implode('|', $_speedy);
						  
						$_bgcolor = $_deformation['background-color'];
						
						echo '			data-bubblesbg="'      . $_bgcolor . '" ' . "\n";
						echo '			data-numbubbles="'     . $_max     . '" ' . "\n";
						echo '			data-bubblesbufferx="' . $_bufferx . '" ' . "\n";
						echo '			data-bubblesbuffery="' . $_buffery . '" ' . "\n";
						echo '			data-bubblesspeedx="'  . $_speedx  . '" ' . "\n";
						echo '			data-bubblesspeedy="'  . $_speedy  . '" ' . "\n";
						
						for($i = 0; $i < count($_blur_strength); $i++) {
						
							if($_blur_strength[$i] === 'inherit') $_blur_strength[$i] = $_blur_strength[$i - 1];
							if(is_numeric($_blur_strength[$i]) && intval($_blur_strength[$i]) > 0) {
								
								$_blur_color = RevSliderFunctions::getVal($_bubblemorph, 'blurcolor', 'rgba(0, 0, 0, 0.35)');
								$_blur_x     = RevSliderFunctions::getVal($_bubblemorph, 'blurx', '0');
								$_blur_y     = RevSliderFunctions::getVal($_bubblemorph, 'blury', '0');
								
								if(!is_array($_blur_color)) $_blur_color = array($_blur_color, $_blur_color, $_blur_color, $_blur_color);
								if(!is_array($_blur_x)) $_blur_x = array($_blur_x, $_blur_x, $_blur_x, $_blur_x);
								if(!is_array($_blur_y)) $_blur_y = array($_blur_y, $_blur_y, $_blur_y, $_blur_y);
								
								$_blur_strength = implode('|', $_blur_strength);
								$_blur_color    = implode('|', $_blur_color);
								$_blur_x        = implode('|', $_blur_x);
								$_blur_y        = implode('|', $_blur_y);
								
								echo '			data-bubblesblur="'      . $_blur_strength . '" ' . "\n";
								echo '			data-bubblesblurcolor="' . $_blur_color    . '" ' . "\n";
								echo '			data-bubblesblurx="'     . $_blur_x        . '" ' . "\n";
								echo '			data-bubblesblury="'     . $_blur_y        . '" ' . "\n";
								
								break;
								
							}
							
						}
						
						for($i = 0; $i < count($_border_size); $i++) {
						
							if($_border_size[$i] === 'inherit') $_border_size[$i] = $_border_size[$i - 1];
							if(is_numeric($_border_size[$i]) && intval($_border_size[$i]) > 0) {
								
								$_border_color = RevSliderFunctions::getVal($_bubblemorph, 'bordercolor', '#000000');
								if(!is_array($_border_color)) $_border_color = array($_border_color, $_border_color, $_border_color, $_border_color);
								
								$_border_color = implode('|', $_border_color);
								$_border_size  = implode('|', $_border_size);
								
								echo '			data-bubblesbordercolor="' . $_border_color . '" ' . "\n";
								echo '			data-bubblesbordersize="'  . $_border_size  . '" ' . "\n";
								
								break;
								
							}
							
						}
						
					}
					
				}
				
			}
		
		}
	
	}
	
}
?>