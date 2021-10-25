<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsLiquidEffectSlideFront {
	
	private $title;
	
	public function __construct($_title) {
		
		$this->title = $_title;
		add_action('revslider_add_li_data', array($this, 'write_slide_attributes'), 10, 3);
	
	}
	
	public function write_slide_attributes($_slider, $_slide) {
		
		$_enabled = $_slider->getParam('liquideffect_enabled', false) == 'true';
		if(!$_enabled) return;
		
		$_enabled = $_slide->getParam('liquideffect_enabled', false) == 'true';
		if(!$_enabled) return;
		
		$_imagesize = 'external';
		$_bgtype = $_slide->getParam('background_type', false);
		
		if($_bgtype !== 'image' && $_bgtype !== 'external') return;
		if($_bgtype !== 'external') {
		
			$_imageURL = $_slide->getImageURL();
			if(!$_imageURL) return;
			
			$_imageID = RevSliderFunctionsWP::get_image_id_by_url($_imageURL);
			if($_imageID) {
				$_bgsize = $_slide->getParam('image_source_type', 'full');
				$_image = wp_get_attachment_image_src($_imageID, $_bgsize);
				if($_image) $_imagesize = $_image[1] . '|' . $_image[2];
			}
			
		}
		
		$_imagemap = $_slide->getParam('liquideffect_image', 'ripple');
		$_autoplay = $_slide->getParam('liquideffect_autoplay', 'true') == 'true';
		$_transition  = $_slide->getParam('liquideffect_transition', 'false') == 'true';
		$_interactive = $_slide->getParam('liquideffect_interactive', 'false') == 'true';
		
		if($_imagemap !== 'Custom Map') {
			
			$_size = $_slide->getParam('liquideffect_size', 'Large');
			$_imagemap = plugins_url('assets/images/' . strtolower($_imagemap) . '_' . strtolower($_size) . '.jpg', dirname(__FILE__));
			
		}
		else {
			
			$_imagemap = $_slide->getParam('liquideffect_custommap', false);
			if(empty($_imagemap)) $_imagemap = plugins_url('assets/images/ripple.jpg', dirname(__FILE__));
			
		}
		
		$_settings = array(
			
			'image' => $_imagemap,
			'imagesize' => $_imagesize,
			
		);
		
		if($_autoplay) {
			
			$_speedx = $_slide->getParam('liquideffect_speedx', '10');
			$_speedy = $_slide->getParam('liquideffect_speedy', '3');
			
			$_scalex = $_slide->getParam('liquideffect_scalex', '20');
			$_scaley = $_slide->getParam('liquideffect_scaley', '20');
			
			$_rotationx = $_slide->getParam('liquideffect_rotationx', '10');
			$_rotationy = $_slide->getParam('liquideffect_rotationy', '0');
			$_rotation  = $_slide->getParam('liquideffect_rotation', '0');
			
			if(!is_numeric($_scalex)) $_scalex = '20';
			$_scalex = floatval($_scalex);
			
			if(!is_numeric($_scaley)) $_scaley = '20';
			$_scaley = floatval($_scaley);
			
			if(!is_numeric($_speedy)) $_speedy = '3';
			$_speedy = floatval($_speedy);
			
			if(!is_numeric($_speedx)) $_speedx = '10';
			$_speedx = floatval($_speedx);
			
			if(!is_numeric($_speedy)) $_speedy = '3';
			$_speedy = floatval($_speedy);
			
			if(!is_numeric($_rotationx)) $_rotationx = '10';
			$_rotationx = floatval($_rotationx);
			
			if(!is_numeric($_rotationy)) $_rotationy = '0';
			$_rotationy = floatval($_rotationy);
			
			if(!is_numeric($_rotation)) $_rotation = '0';
			$_rotation = floatval($_rotation);
			
			$_settings['autoplay']  = true;
			$_settings['scalex']    = $_scalex;
			$_settings['scaley']    = $_scaley;
			$_settings['speedx']    = $_speedx;
			$_settings['speedy']    = $_speedy;
			$_settings['rotationx'] = $_rotationx;
			$_settings['rotationy'] = $_rotationy;
			$_settings['rotation']  = $_rotation;
			
		}
		else {
			
			$_settings['autoplay']  = false;
			$_settings['scalex']    = 0;
			$_settings['scaley']    = 0;
			$_settings['speedx']    = 0;
			$_settings['speedy']    = 0;
			$_settings['rotationx'] = 0;
			$_settings['rotationy'] = 0;
			$_settings['rotation']  = 0;
			
		}
		
		if($_transition) {
			
			$_transcross = $_slide->getParam('liquideffect_transcross', 'false') == 'true' ? true : false;
			$_transpower = $_slide->getParam('liquideffect_transpower', 'false') == 'true' ? true : false;
			$_transtime  = $_slide->getParam('liquideffect_transtime', '1000');
			$_easing     = $_slide->getParam('liquideffect_easing', 'Power3.easeOut');
			
			$_transitionx = $_slide->getParam('liquideffect_transitionx', '200');
			$_transitiony = $_slide->getParam('liquideffect_transitiony', '70');
			
			$_transpeedx = $_slide->getParam('liquideffect_transpeedx', '0');
			$_transpeedy = $_slide->getParam('liquideffect_transpeedy', '100');
			
			$_transrotx = $_slide->getParam('liquideffect_transrotx', '20');
			$_transroty = $_slide->getParam('liquideffect_transroty', '0');
			$_transrot  = $_slide->getParam('liquideffect_transrot',  '0');
			
			if(!is_numeric($_transtime)) $_transtime = '1000';
			$_transtime = intval($_transtime);
			if(!$_transtime) $_transtime = 1000;
			
			if(!is_numeric($_transitionx)) $_transitionx = '200';
			$_transitionx = floatval($_transitionx);
			
			if(!is_numeric($_transitiony)) $_transitiony = '70';
			$_transitiony = floatval($_transitiony);
			
			if(!is_numeric($_transpeedx)) $_transpeedx = '0';
			$_transpeedx = floatval($_transpeedx);
			
			if(!is_numeric($_transpeedy)) $_transpeedy = '100';
			$_transpeedy = floatval($_transpeedy);
			
			if(!is_numeric($_transrotx)) $_transrotx = '20';
			$_transrotx = floatval($_transrotx);
			
			if(!is_numeric($_transroty)) $_transroty = '0';
			$_transroty = floatval($_transroty);
			
			if(!is_numeric($_transrot)) $_transrot = '0';
			$_transrot = floatval($_transrot);
			
			$_settings['transtime']   = $_transtime;
			$_settings['easing']      = $_easing;
			$_settings['transcross']  = $_transcross;
			$_settings['transpower']  = $_transpower;
			$_settings['transitionx'] = $_transitionx;
			$_settings['transitiony'] = $_transitiony;
			$_settings['transpeedx']  = $_transpeedx;
			$_settings['transpeedy']  = $_transpeedy;
			$_settings['transrotx']   = $_transrotx;
			$_settings['transroty']   = $_transroty;
			$_settings['transrot']    = $_transrot;
			
		}
		else {
			
			$_settings['transtime']   = 2000;
			$_settings['easing']      = 'Power3.easeOut';
			$_settings['transcross']  = false;
			$_settings['transpower']  = false;
			$_settings['transitionx'] = 0;
			$_settings['transitiony'] = 0;
			$_settings['transpeedx']  = 0;
			$_settings['transpeedy']  = 0;
			$_settings['transrotx']   = 0;
			$_settings['transroty']   = 0;
			$_settings['transrot']    = 0;
			
		}
		
		if($_interactive) {
			
			$_event        = $_slide->getParam('liquideffect_event', 'click');
			$_intertime    = $_slide->getParam('liquideffect_intertime', '1000');
			$_intereasing  = $_slide->getParam('liquideffect_intereasing', 'Power3.easeOut');
			$_interscalex  = $_slide->getParam('liquideffect_interscalex', '200');
			$_interscaley  = $_slide->getParam('liquideffect_interscaley', '70');
			$_interotation = $_slide->getParam('liquideffect_interotation', '180');
			$_interspeedx  = $_slide->getParam('liquideffect_interspeedx', '0');
			$_interspeedy  = $_slide->getParam('liquideffect_interspeedy', '0');
			$_mobile       = $_slide->getParam('liquideffect_mobile', 'false') == 'true' ? true : false;
			
			if(!is_numeric($_intertime)) $_intertime = '1000';
			$_intertime = intval($_intertime);
			if(!$_intertime) $_intertime = 1000;
			
			if(!is_numeric($_interscalex)) $_interscalex = '200';
			$_interscalex = floatval($_interscalex);
			
			if(!is_numeric($_interscaley)) $_interscaley = '70';
			$_interscaley = floatval($_interscaley);
			
			if(!is_numeric($_interotation)) $_interotation = '180';
			$_interotation = floatval($_interotation);
			
			if(!is_numeric($_interspeedx)) $_interspeedx = '0';
			$_interspeedx = floatval($_interspeedx);
			
			if(!is_numeric($_interspeedy)) $_interspeedy = '0';
			$_interspeedy = floatval($_interspeedy);
			
			$_settings['interactive']  = true;
			$_settings['event']        = $_event;
			$_settings['mobile']       = $_mobile;
			$_settings['intertime']    = $_intertime;
			$_settings['intereasing']  = $_intereasing;
			$_settings['interscalex']  = $_interscalex;
			$_settings['interscaley']  = $_interscaley;
			$_settings['interotation'] = $_interotation;
			$_settings['interspeedx']  = $_interspeedx;
			$_settings['interspeedy']  = $_interspeedy;
			
		}
		
		echo " data-liquideffect='" . json_encode($_settings) . "'";
		
	}
	
}
?>