<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsPanoramaSlideFront {
	
	private $title;
	
	public function __construct($_title) {
		
		$this->title = $_title;
		add_action('revslider_add_li_data', array($this, 'write_slide_attributes'), 10, 3);
		add_filter('rs_action_output_layer_action', array($this, 'write_layer_actions'), 10, 7);
	
	}
	
	public function write_layer_actions($_events, $_action, $_all_actions, $_num, $_slide) {
		
		// check to make sure the action is a panorama
		if(strpos($_action, 'panorama') === false) return $_events;
		
		// mouse event;
		$_event = $_all_actions->tooltip_event[$_num];
		
		// normalize events for mobile
		if(wp_is_mobile()) {
			
			switch($_event) {
				
				case 'mousedown':
				case 'mouseenter':
				
					$_event = 'touchstart';
				
				break;
				
				case 'mouseup':
				case 'mouseleave':
				
					$_event = 'touchend';
				
				break;
				
			}
			
		}
		
		$_events[] = array(
		
			'event' => $_event,
			'action' => $_action,
			'percentage' => $_all_actions->panorama_amount[$_num],
			'delay' => $_all_actions->action_delay[$_num]
			
		);
		
		return $_events;
		
	}
	
	public function write_slide_attributes($_slider, $_slide) {
		
		// check if enabled from slider
		$_enabled = $_slider->getParam('panorama_enabled', false) == 'true';
		if(empty($_enabled)) return;
		
		// check if enabled for slide
		$_enabled = $_slide->getParam('panorama_enabled', false) == 'true';
		if(empty($_enabled)) return;
		
		// make sure slide has background image set as main source
		$_enabled = $_slide->getParam('background_type', 'image');
		if(empty($_enabled)) return;
		
		// make sure image exists
		$_image = $_slide->getParam('image', false);
		if(empty($_image)) return;
			
		$_autoplay        = $_slide->getParam('panorama_autoplay',         false) == 'true' ? 'true' : 'false';
		$_mousehweelZoom  = $_slide->getParam('panorama_mousewheel_zoom',  false) == 'true' ? 'true' : 'false';
		$_smoothZoom      = $_slide->getParam('panorama_smooth_zoom',      false) == 'true' ? 'true' : 'false';
		$_direction       = $_slide->getParam('panorama_direction',        'forward');
		$_control         = $_slide->getParam('panorama_controls',         'throw');
		$_speed           = $_slide->getParam('panorama_speed',            '100');
		$_throwSpeed      = $_slide->getParam('panorama_throw_speed',      '750');
		$_zoomMin         = $_slide->getParam('panorama_zoom_min',         '75');
		$_zoomMax         = $_slide->getParam('panorama_zoom_max',         '150');
		$_cameraFov       = $_slide->getParam('panorama_camera_fov',       '75');
		$_cameraFar       = $_slide->getParam('panorama_camera_far',       '1000');
		$_sphereRadius    = $_slide->getParam('panorama_sphere_radius',    '100');
		$_sphereWsegments = $_slide->getParam('panorama_sphere_wsegments', '100');
		$_sphereHsegments = $_slide->getParam('panorama_sphere_hsegments', '40');
		
		$_options = array(
			
			'image'             => $_image,
			'autoplay'          => $_autoplay,
			'mousewheelZoom'    => $_mousehweelZoom,
			'smoothZoom'        => $_smoothZoom,
			'autoplayDirection' => $_direction,
			'controls'          => $_control ,
			'autoplaySpeed'     => $_speed,
			'throwSpeed'        => $_throwSpeed,
			'zoomMin'           => $_zoomMin,
			'zoomMax'           => $_zoomMax,
			'cameraFov'         => $_cameraFov,
			'cameraFar'         => $_cameraFar,
			'sphereRadius'      => $_sphereRadius,
			'sphereWsegments'   => $_sphereWsegments,
			'sphereHsegments'   => $_sphereHsegments
		
		);
		
		echo " data-panorama='" . json_encode($_options) . "'";
		
	}
	
}
?>