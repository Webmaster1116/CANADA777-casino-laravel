<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

require_once(RS_PARTICLES_PLUGIN_PATH . 'framework/slider.front.class.php');

class RsParticlesSliderFront extends RsAddonParticlesSliderFront {
	
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
		$_enabled = $_slider->getParam($_title . '_enabled', false) == 'true';

		if($_enabled && wp_is_mobile()) $_enabled = $_slider->getParam('particles_hide_on_mobile', true) == 'false';
		if($_enabled) {
			
			$_tabs  = "\t\t\t\t\t\t";
			$_tabsa = "\t\t\t\t\t\t\t";
			$_tabsb = "\t\t\t\t\t\t\t\t";
			$_tabsc = "\t\t\t\t\t\t\t\t\t";
			$_tabsd = "\t\t\t\t\t\t\t\t\t\t";
			$_tabse = "\t\t\t\t\t\t\t\t\t\t\t";
			
			
			$_num_particles         = intval($_slider->getParam('particles_number_value', '80'));
			$_zindex                = $_slider->getParam('particles_zindex', '1');
			
			$_size_value            = ceil(intval($_slider->getParam('particles_size_value', '3')) * 0.5);
			$_size_min_value        = $_slider->getParam('particles_size_min_value', '2');
			$_size_anim_min         = ceil(intval($_slider->getParam('particles_size_anim_min', '0')) * 0.5);
			
			$_size_random           = $_slider->getParam('particles_size_random', 'true') == 'true' ? 'true' : 'false';
			$_size_anim_enable      = $_slider->getParam('particles_size_anim_enable', 'false') == 'false' ? 'false' : 'true';
			$_size_anim_sync        = $_slider->getParam('particles_size_anim_sync', 'false') == 'false' ? 'false' : 'true';
			
			$_opacity_value         = intval($_slider->getParam('particles_opacity_value', '50')) * 0.01;
			$_opacity_min_value     = intval($_slider->getParam('particles_opacity_min_value', '25')) * 0.01;
			$_opacity_anim_min      = intval($_slider->getParam('particles_opacity_anim_min', '10')) * 0.01;
			$_opacity_random        = $_slider->getParam('particles_opacity_random', 'false') == 'false' ? 'false' : 'true';
			$_opacity_anim_enable   = $_slider->getParam('particles_opacity_anim_enable', 'false') == 'false' ? 'false' : 'true';
			$_opacity_anim_sync     = $_slider->getParam('particles_opacity_anim_sync', 'false') == 'false' ? 'false' : 'true';
			$_opacity_anim_speed    = intval($_slider->getParam('particles_opacity_anim_speed', '1'));
			
			$_border_width          = intval($_slider->getParam('particles_border_width', '1'));
			$_border_opacity        = intval($_slider->getParam('particles_border_opacity', '100')) * 0.01;
			$_border_enable         = $_slider->getParam('particles_border_enable', 'false') == 'false' ? 'false' : 'true';
			
			$_line_opacity          = intval($_slider->getParam('particles_line_opacity', '40')) * 0.01;
			$_line_enable           = $_slider->getParam('particles_line_enable', 'false') == 'false' ? 'false' : 'true';
			
			$_move_speed            = intval($_slider->getParam('particles_move_speed', '6'));
			$_move_min_speed        = intval($_slider->getParam('particles_move_speed_min', '3'));
			$_move_direction        = $_slider->getParam('particles_move_direction', 'none');
			$_move_enable           = $_slider->getParam('particles_move_enable', 'true') == 'true' ? 'true' : 'false';
			$_move_random           = $_slider->getParam('particles_move_random', 'false') == 'false' ? 'false' : 'true';
			$_move_straight         = $_slider->getParam('particles_move_straight', 'false') == 'true' ? 'false' : 'true';
			$_move_bounce           = $_slider->getParam('particles_move_bounce', 'false') == 'false' ? 'out' : 'bounce';
			
			$_onhover_enable        = $_slider->getParam('particles_onhover_enable', 'false') == 'false' ? 'false' : 'true';
			$_onclick_enable        = $_slider->getParam('particles_onclick_enable', 'false') == 'false' ? 'false' : 'true';
			
			$_bubble_opacity        = intval($_slider->getParam('particles_modes_bubble_opacity', '80')) * 0.01;
			$_grab_opacity          = intval($_slider->getParam('particles_modes_grab_opacity', '50')) * 0.01;
			
			$_shape_type            = $_slider->getParam('particles_shape_type', 'circle');
			$_svg_markup            = '';
			
			
			/*
				Extra checks to make sure values are within reasonable range
			*/
			$_move_speed        = max(min($_move_speed, 50), $_move_speed, 1);
			$_size_value        = max(min($_size_value, 250), $_size_value, 1);
			$_line_opacity      = max(min($_line_opacity, 1), $_line_opacity, 0);
			$_grab_opacity      = max(min($_grab_opacity, 1), $_grab_opacity, 0.1);
			$_bubble_opacity    = max(min($_bubble_opacity, 1), $_bubble_opacity, 0);
			$_opacity_value     = max(min($_opacity_value, 1), $_opacity_value, 0.1);
			$_border_opacity    = max(min($_border_opacity, 1), $_border_opacity, 0);
			$_num_particles     = max(min($_num_particles, 500), $_num_particles, 1);
			$_move_min_speed    = max(min($_move_min_speed, 50), $_move_min_speed, 1);
			$_size_min_value    = max(min($_size_min_value, 250), $_size_min_value, 0.1);
			$_opacity_anim_min  = max(min($_opacity_anim_min, 1), $_opacity_anim_min, 0);
			$_opacity_min_value = max(min($_opacity_min_value, 1), $_opacity_min_value, 0.1);
			
			if(intval($_size_min_value) >= 2) {
				
				$_size_min_value = ceil(intval($_size_min_value) * 0.5);
				
			}
			else {
				
				$_size_min_value = floatval($_size_min_value) * 0.5;
				
			}
			
			if(in_array($_shape_type, array('circle', 'edge', 'triangle', 'polygon', 'star')) === false) {
				
				$_svgs = RsParticlesSvg::$_SVGs;
				$_svg_markup = $_svgs[$_shape_type];
				$_shape_type = 'image';
				
			}
			
			if($_move_direction === 'none') {
				
				$_move_straight = 'false';
				
			}
			else if($_move_direction === 'static') {
				
				$_move_direction = 'none';
				$_move_straight = 'true';
				$_move_random = 'false';
				
			}
			
			if($_zindex === 'default') $_zindex = 1;
			if($_border_enable === 'false' || $_border_opacity === 0) $_border_width = 0;
			
			echo $_tabs  . 'particles: {startSlide: "' . $_slider->getParam('particles_start_slide', 'first') . '", endSlide: "' . $_slider->getParam('particles_end_slide', 'last') . '", zIndex: "' . $_zindex . '",' . "\n";
			
			echo $_tabsa . 'particles: {' . "\n";
			echo $_tabsb . 'number: {value: ' . $_num_particles . '}, color: {value: "' . $_slider->getParam('particles_color_value', '#ffffff') . '"},' . "\n";
			echo $_tabsb . 'shape: {' . "\n";
			
			echo $_tabsc . 'type: "' . $_shape_type . '", stroke: {width: ' . $_border_width . ', color: "' . $_slider->getParam('particles_border_color', '#ffffff') . '", opacity: ' . $_border_opacity . '},' . "\n";
			
			echo $_tabsc . 'image: {src: "' . $_svg_markup . '"}' . "\n";
			echo $_tabsb . '},' . "\n";
			
			echo $_tabsb . 'opacity: {value: ' . $_opacity_value . ', random: ' . $_opacity_random . ', min: ' . $_opacity_min_value . ', anim: {enable: ' . $_opacity_anim_enable . ', speed: ' . $_opacity_anim_speed . ', opacity_min: ' . $_opacity_anim_min . ', sync: ' . $_opacity_anim_sync . '}},' . "\n";
			
			echo $_tabsb . 'size: {value: ' . $_size_value . ', random: ' . $_size_random . ', min: ' . $_size_min_value . ', anim: {enable: ' . $_size_anim_enable . ', speed: ' . intval($_slider->getParam('particles_size_anim_speed', '40')) . ', size_min: ' . $_size_anim_min . ', sync: ' . $_size_anim_sync . '}},' . "\n";
			
			echo $_tabsb . 'line_linked: {enable: ' . $_line_enable . ', distance: ' . intval($_slider->getParam('particles_line_distance', '150')) . ', color: "' . $_slider->getParam('particles_line_color', '#ffffff') . '", opacity: ' . $_line_opacity . ', width: ' . intval($_slider->getParam('particles_line_width', '1')) . '},' . "\n";
			
			echo $_tabsb . 'move: {enable: ' . $_move_enable . ', speed: ' . $_move_speed . ', direction: "' . $_move_direction . '", random: ' . $_move_random . ', min_speed: ' . $_move_min_speed . ', straight: ' . $_move_straight . ', out_mode: "' . $_move_bounce . '"}},' . "\n";
			
			echo $_tabsa . 'interactivity: {' . "\n";
			
			echo $_tabsb . 'events: {onhover: {enable: ' . $_onhover_enable . ', mode: "' . $_slider->getParam('particles_onhover_mode', 'repulse') . '"}, onclick: {enable: ' . $_onclick_enable . ', mode: "' . $_slider->getParam('particles_onclick_mode', 'repulse') . '"}},' . "\n";
			
			echo $_tabsb . 'modes: {grab: {distance: ' . intval($_slider->getParam('particles_modes_grab_distance', '400')) . ', line_linked: {opacity: ' . $_grab_opacity . '}}, bubble: {distance: ' . intval($_slider->getParam('particles_modes_bubble_distance', '400')) . ', size: ' . intval($_slider->getParam('particles_modes_bubble_size', '40')) . ', opacity: ' . $_bubble_opacity . '}, repulse: {distance: ' . intval($_slider->getParam('particles_modes_repulse_distance', '200')) . '}}' . "\n";
			
			echo $_tabsa . '}' . "\n";
			echo $_tabs  . '},' . "\n";
			
		}
	
	}
	
}
?>