<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsTypewriterSlideFront {
	
	private $title,
			$layers = array();
	
	public function __construct($_title) {
		
		$this->title = $_title;
		
		add_action('revslider_add_layer_attributes', array($this, 'write_layer_attributes'), 10, 3);
		add_filter('revslider_layer_content', array($this, 'check_layer_content'), 10, 5); 
	
	}
	
	// possibly populate Layer Text from post-meta
	public function check_layer_content($_content, $_html, $_sliderId, $_slide, $_layer) {
		
		$_layerID = 'slide-' . $_slide->getID() . '-layer-' . RevSliderFunctions::getVal($_layer, 'unique_id', '');
		
		if(!array_key_exists($_layerID, $this->layers)) {
			
			return $_content;
			
		}	
		
		return rawurldecode($this->layers[$_layerID]);
		
	}
	
	public function write_layer_attributes($_layer, $_slide, $_slider) {
		
		// bounce for non-TextLayers
		if(RevSliderFunctions::getVal($_layer, 'type', '') !== 'text') return;
		
		// bounce if Typewriter not enabled
		$_addOn = RevSliderFunctions::getVal($_layer, $this->title, array());
		if(empty($_addOn) || RevSliderFunctions::getVal($_addOn, 'enabled', 'off') === 'off') return;
		
		// bounce if Layer has no actual text
		$_layerText = RevSliderFunctions::getVal($_layer, 'text', '');
		if(!$_layerText) return;
		
		// quick check for non-gallery slider
		if($_slide->isFromPost()) {
			
			// deeper check for post-based slider
			$_sliderType = $_slider->getParam('source_type', 'gallery');
			if($_sliderType === 'posts' || $_sliderType === 'specific_posts' || $_sliderType === 'woocommerce') {

				// check Layer Text for special "sequence/multiline" shortcode
				// Example: [rs-typewriter default="{{title}}"]
				if(has_shortcode($_layerText, 'rs-typewriter')) {
					
					$_postMeta = get_post_meta(RevSliderFunctions::getVal($_slide->getPostData(), 'ID', array()));
					if(!empty($_postMeta)) {
						
						// get Typewriter meta
						$_postMeta = RevSliderFunctions::getVal($_postMeta, 'rs-addon-typewriter', '');
						
						// possibly replace shortcode with meta
						if(!empty($_postMeta)) {
							
							$_postMeta = $_postMeta[0];
							
							if(!empty($_postMeta) && $_postMeta !== '') {
							
								$_postMeta = explode(',', $_postMeta);
								$_layerID = 'slide-' . $_slide->getID() . '-layer-' . RevSliderFunctions::getVal($_layer, 'unique_id', '');
								
								$this->layers[$_layerID] = array_shift($_postMeta);
								if(!empty($_postMeta)) $_addOn->lines = implode(',', $_postMeta);
								
							}
							
						}	
					}	
				}	
			}	
		}
		
		$styles             = RevSliderFunctions::getVal($_layer, 'deformation', array());
		$_addOn->background = RevSliderFunctions::getVal($styles, 'background-color', 'transparent') !== 'transparent' && 
							  RevSliderFunctions::getVal($styles, 'background-transparency', '0') !== '0' ? 'on' : 'off';
				
		echo "			data-" . $this->title . "='" . $this->jsonEncode($_addOn) . "'" . "\n";
		
	}
	
	private function jsonEncode($obj) {
		
		return !empty($obj) ? json_encode($obj) : '';
		
	}
	
}
?>