<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsPaintBrushSlideFront {
	
	private $title;
	
	public function __construct($_title) {
		
		$this->title = $_title;
		add_action('revslider_add_li_data', array($this, 'write_slide_attributes'), 10, 3);
	
	}
	
	public function write_slide_attributes($_slider, $_slide) {
		
		// check if enabled from slider
		$_enabled = $_slider->getParam('paintbrush_enabled', false) == 'true';
		if(empty($_enabled)) return;
		
		// check if enabled for slide
		$_enabled = $_slide->getParam('paintbrush_enabled', false) == 'true';
		if(empty($_enabled)) return;
		
		$_source = $_slide->getParam('paintbrush_source', 'local');
		$_image = $_slide->getParam('paintbrush_img', '');
		
		$_src = false;
		switch($_source) {
			
			case 'local':
			
				$_src = $_image;
			
			break;
			
			case 'main':
			
				$_tpe = $_slide->getParam('background_type', 'trans');
				if($_tpe === 'image') $_src = $_slide->getImageURL();
			
			break;
			
		}
		
		if(!empty($_src)) {
			
			$_mobile = wp_is_mobile();
			if($_mobile) {
				
				$_bounce = $_slide->getParam('paintbrush_mobile', false) == 'true';
				if(!empty($_bounce)) {
					
					$_fallback = $_slide->getParam('paintbrush_fallback', false) == 'true';
					if(!empty($_fallback)) echo ' data-revaddonpaintbrushfallback="' . $_src . '"';
					return;
					
				}
				
			}
			
			$_size     = $_slide->getParam('paintbrush_size', '80');
			$_amount   = $_slide->getParam('paintbrush_bluramount', '10');
			$_time     = $_slide->getParam('paintbrush_fadetime', '1000');
			$_edgeFix  = $_slide->getParam('paintbrush_edgeamount', '10');
			$_fixEdges = $_slide->getParam('paintbrush_fixedges', false) == 'true' ? true : false;
			
			$_size    = intval($_size);
			$_amount  = intval($_amount);
			$_time    = intval($_time);
			$_edgeFix = intval($_edgeFix);
			
			$_settings = array(
				
				'size'       => $_size,
				'origsize'   => $_size,
				'blurAmount' => $_amount,
				'fadetime'   => $_time,
				'image'      => $_src,
				'edgefix'    => $_edgeFix,
				'fixedges'   => $_fixEdges,
				'style'      => $_slide->getParam('paintbrush_style', 'round'),
				'blur'       => $_slide->getParam('paintbrush_blur', false) == 'true' ? true : false,
				'scaleblur'  => $_slide->getParam('paintbrush_scaleblur', false) == 'true' ? true : false,
				'responsive' => $_slide->getParam('paintbrush_responsive', false) == 'true' ? true : false,
				'disappear'  => $_slide->getParam('paintbrush_disappear', false) == 'true' ? true : false,
				'carousel'   => $_slider->getParam('slider-type', 'standard') !== 'carousel' ? false : true
				
			);
			
			echo " data-revaddonpaintbrush='" . json_encode($_settings) . "'";
			if(!empty($_fixEdges)) echo ' data-revaddonpaintbrushedges="' . $_fixEdges . '"';
			
		}
		
	}
	
}
?>