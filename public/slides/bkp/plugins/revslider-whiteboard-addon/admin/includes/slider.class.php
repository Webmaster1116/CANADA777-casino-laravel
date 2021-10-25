<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class rs_whiteboard_slider {
	
	public static function init(){
		
		add_filter('revslider_slider_addons', array('rs_whiteboard_slider', 'add_whiteboard_settings'), 10, 2);
		
		if(isset($_GET["page"]) && $_GET["page"]=="revslider"){
			add_action('admin_enqueue_scripts', array('rs_whiteboard_slider', 'wb_enqueue_styles'));
			add_action('admin_enqueue_scripts', array('rs_whiteboard_slider', 'wb_enqueue_scripts'));
		}
	}
	
	public static function wb_enqueue_styles(){
		wp_register_style('revslider-whiteboard-plugin-settings', WHITEBOARD_PLUGIN_URL . 'admin/assets/css/whiteboard-admin.css', array(), WHITEBOARD_VERSION);
		wp_enqueue_style('revslider-whiteboard-plugin-settings');
	}
	
	
	public static function wb_enqueue_scripts(){
		wp_register_script('revslider-whiteboard-plugin-js', WHITEBOARD_PLUGIN_URL . 'admin/assets/js/whiteboard-admin.js', array(), WHITEBOARD_VERSION);
		wp_enqueue_script('revslider-whiteboard-plugin-js');
	}
	
	
	public static function add_whiteboard_settings($settings, $slider_params){
		
		$wb_writehand_direction = RevSliderFunctions::getVal($slider_params, "wb_writehand_direction", "ltr");
		$wb_writehand_source = RevSliderFunctions::getVal($slider_params, "wb_writehand_source", "1");
		$wb_movehand_source = RevSliderFunctions::getVal($slider_params, "wb_movehand_source", "1");
		$wb_writehand_type = RevSliderFunctions::getVal($slider_params, "wb_writehand_type", "right");
		$wb_movehand_type = RevSliderFunctions::getVal($slider_params, "wb_movehand_type", "right");
		$wb_enable = RevSliderFunctions::getVal($slider_params, 'wb_enable', 'off');
		
		$wb_enable = ($wb_enable === 'on') ? ' checked="checked"' : '';
		
		$markup = '
<span class="label" id="label_wb_enable" origtitle="'.__("Enable/Disable Whiteboard for Slider Revolution", 'rs_whiteboard').'">'.__("Use Whiteboard", 'rs_whiteboard').'</span>
<input type="checkbox" class="tp-moderncheckbox withlabel" id="wb_enable" name="wb_enable"'.$wb_enable.'" data-unchecked="off">
<div class="clear"></div>

<div id="wb-settings-wrapper" style="display: none;">
<ul class="main-options-small-tabs" style="display:inline-block; ">
	<li data-content="#wb-writehand" class="selected">'.__('Draw/Write Hand', 'rs_whiteboard').'</li>
	<li data-content="#wb-movehand" class="">'.__('Move Hand', 'rs_whiteboard').'</li>	
</ul>';

		// WRITE HAND SETTINGS
		$markup .= '<div id="wb-writehand">';		
		$markup .= '<span id="label_wb_writehand_source" class="label" origtitle="'.__("Select your hand/mouse source", 'rs_whiteboard').'">'.__("Source", 'rs_whiteboard').'</span>

<select id="wb_writehand_source" name="wb_writehand_source" class="withlabel">
	<option value="1"';
		if($wb_writehand_source == "1") $markup .= ' selected="selected"';
		$markup .= '>'.__("Default", 'rs_whiteboard').'</option>	
	<option value="custom"';
		if($wb_writehand_source == "custom") $markup .= ' selected="selected"';
		$markup .= '>'.__("Custom", 'rs_whiteboard').'</option>
</select>';

		$markup .= '<div class="clear"></div>
<div class="wb_writehand_source_custom_wrapper" style="display: none;">
	<span class="label" id="label_wb_writehand_source_custom" origtitle="'.__("The source of the hand/mouse.", 'rs_whiteboard').'">'.__("Mouse/Hand URL", 'rs_whiteboard').'</span>
	<input type="text" class="text-sidebar-long withlabel" style="width: 104px;" id="wb_writehand_source_custom" name="wb_writehand_source_custom" value="'. RevSliderFunctions::getVal($slider_params, 'wb_writehand_source_custom', '').'"> <a href="javascript:void(0)" data-hand="write" class="button-image-select-wb-hand-img button-primary revblue">'.__('Set', 'rs_whiteboard').'</a>
	<div class="clear"></div>
</div>

<span class="label" style="vertical-align:top">'.__("Preview", 'rs_whiteboard').'</span>
<span class="wb_writehand_preview wb_hand_preview"></span>
<div class="clear"></div>

<span class="label" id="label_wb_writehand_width" origtitle="'.__("The width of the hand/mouse.", 'rs_whiteboard').'">'.__("Width", 'rs_whiteboard').'</span>
<input type="text" class="text-sidebar-long withlabel" id="wb_writehand_width" name="wb_writehand_width" value="'. RevSliderFunctions::getVal($slider_params, 'wb_writehand_width', '572').'"> <span>px</span>
<div class="clear"></div>

<span class="label" id="label_wb_writehand_height" origtitle="'.__("The height of the hand/mouse.", 'rs_whiteboard').'">'.__("Height", 'rs_whiteboard').'</span>
<input type="text" class="text-sidebar-long withlabel" id="wb_writehand_height" name="wb_writehand_height" value="'. RevSliderFunctions::getVal($slider_params, 'wb_writehand_height', '691').'"> <span>px</span>
<div class="clear"></div>

<span class="label" id="label_wb_writehand_origin_x" origtitle="'.__("The origin of the hand/mouse.", 'rs_whiteboard').'">'.__("Origin x/y", 'rs_whiteboard').'</span>
<input type="text" class="withlabel" style="width: 45px" id="wb_writehand_origin_x" name="wb_writehand_origin_x" value="'. RevSliderFunctions::getVal($slider_params, 'wb_writehand_origin_x', '50').'"> 
<input type="text" class="" style="width: 45px" id="wb_writehand_origin_y" name="wb_writehand_origin_y" value="'. RevSliderFunctions::getVal($slider_params, 'wb_writehand_origin_y', '50').'">   <a href="javascript:void(0)" data-hand="write" class="button-image-select-wb-hand-origin button-primary revblue">'.__('Set', 'rs_whiteboard').'</a>
<div class="clear"></div>

<h4>'.__("Draw/Write Hand Defaults", 'rs_whiteboard').'</h4>

<span class="label" id="label_wb_writehand_type" origtitle="'.__("Choose the Draw/Write Hand type.", 'rs_whiteboard').'">'.__("Type", 'rs_whiteboard').'</span>
<select id="wb_writehand_type" name="wb_writehand_type" class="withlabel">
	<option value="right"';
		if($wb_writehand_type == "right") $markup .= ' selected="selected"';
		$markup .= '>'.__("Right", 'rs_whiteboard').'</option>
	<option value="left"';
		if($wb_writehand_type == "left") $markup .= ' selected="selected"';
		$markup .= '>'.__("Left", 'rs_whiteboard').'</option>
</select>

<span class="label" id="label_wb_movehand_direction" origtitle="'.__("Choose the Draw/Write direction.", 'rs_whiteboard').'">'.__("Direction", 'rs_whiteboard').'</span>
<select id="wb_writehand_direction" name="wb_writehand_direction" class="withlabel">
	<option value="ltr"';
		if($wb_writehand_direction == "ltr") $markup .= ' selected="selected"';
		$markup .= '>'.__("Left to Right", 'rs_whiteboard').'</option>
	<option value="left"';
		if($wb_writehand_direction == "rtl" || $wb_writehand_direction == "left") $markup .= ' selected="selected"';
		$markup .= '>'.__("Right to Left", 'rs_whiteboard').'</option>
	<option value="top"';
		if($wb_writehand_direction == "btt" || $wb_writehand_direction == "top") $markup .= ' selected="selected"';
		$markup .= '>'.__("Bottom to Top", 'rs_whiteboard').'</option>
	<option value="bottom"';
		if($wb_writehand_direction == "ttb" || $wb_writehand_direction == "bottom") $markup .= ' selected="selected"';
		$markup .= '>'.__("Top to Bottom", 'rs_whiteboard').'</option>
</select>

<span class="label" id="label_wb_global_writehand_jitter" origtitle="'.__("The global hand jittering distance.", 'rs_whiteboard').'">'.__("Jittering Distance", 'rs_whiteboard').'</span>
<input type="text" class="text-sidebar-long withlabel" id="wb_global_writehand_jitter" name="wb_global_writehand_jitter" value="'. RevSliderFunctions::getVal($slider_params, 'wb_global_writehand_jitter', '80').'"> <span>%</span>
<div class="clear"></div>

<span class="label" id="label_wb_global_writehand_jitter_repeat" origtitle="'.__("The global hand jittering repeat per animation speed.", 'rs_whiteboard').'">'.__("Jittering Repeat", 'rs_whiteboard').'</span>
<input type="text" class="text-sidebar-long withlabel" id="wb_global_writehand_jitter_repeat" name="wb_global_writehand_jitter_repeat" value="'. RevSliderFunctions::getVal($slider_params, 'wb_global_writehand_jitter_repeat', '5').'">
<div class="clear"></div>

<span class="label" id="label_wb_global_writehand_jitter_offset" origtitle="'.__("The global hand jittering vertical offset.", 'rs_whiteboard').'">'.__("Jittering Vertical Offset", 'rs_whiteboard').'</span>
<input type="text" class="text-sidebar-long withlabel" id="wb_global_writehand_jitter_offset" name="wb_global_writehand_jitter_offset" value="'. RevSliderFunctions::getVal($slider_params, 'wb_global_writehand_jitter_offset', '10').'"> <span>%</span>
<div class="clear"></div>

<span class="label" id="label_wb_global_writehand_angle" origtitle="'.__("The max global hand angle during Write/Move Process", 'rs_whiteboard').'">'.__("Max Rotation Angle", 'rs_whiteboard').'</span>
<input type="text" class="text-sidebar-long withlabel" id="wb_global_writehand_angle" name="wb_global_writehand_angle" value="'. RevSliderFunctions::getVal($slider_params, 'wb_global_writehand_angle', '10').'"> <span>°</span>
<div class="clear"></div>

<span class="label" id="label_wb_global_writehand_angle_repeat" origtitle="'.__("The Amount of Rotation Variations during Process", 'rs_whiteboard').'">'.__("Rotation Variations", 'rs_whiteboard').'</span>
<input type="text" class="text-sidebar-long withlabel" id="wb_global_writehand_angle_repeat" name="wb_global_writehand_angle_repeat" value="'. RevSliderFunctions::getVal($slider_params, 'wb_global_writehand_angle_repeat', '3').'">
<div class="clear"></div>

</div>';

		// MOVE HAND SETTINGS
		$markup .= '<div id="wb-movehand" style="display:none">';		
		$markup .= '<span id="label_wb_movehand_source" class="label" origtitle="'.__("Select your hand/mouse source", 'rs_whiteboard').'">'.__("Source", 'rs_whiteboard').'</span>
		
<select id="wb_movehand_source" name="wb_movehand_source" class="withlabel">
	<option value="1"';
		if($wb_movehand_source == "1") $markup .= ' selected="selected"';
		$markup .= '>'.__("Default", 'rs_whiteboard').'</option>	
	<option value="custom"';
		if($wb_movehand_source == "custom") $markup .= ' selected="selected"';
		$markup .= '>'.__("Custom", 'rs_whiteboard').'</option>
</select>';

		$markup .= '<div class="clear"></div>
<div class="wb_movehand_source_custom_wrapper" style="display: none;">
	<span class="label" id="label_wb_movehand_source_custom" origtitle="'.__("The source of the hand/mouse.", 'rs_whiteboard').'">'.__("Mouse/Hand URL", 'rs_whiteboard').'</span>
	<input type="text" class="text-sidebar-long withlabel" style="width: 104px;" id="wb_movehand_source_custom" name="wb_movehand_source_custom" value="'. RevSliderFunctions::getVal($slider_params, 'wb_movehand_source_custom', '').'"> <a href="javascript:void(0)" data-hand="move" class="button-image-select-wb-hand-img button-primary revblue">'.__('Set', 'rs_whiteboard').'</a>
	<div class="clear"></div>
</div>

<span class="label" style="vertical-align:top">'.__("Preview", 'rs_whiteboard').'</span>
<span class="wb_movehand_preview wb_hand_preview"></span>
<div class="clear"></div>

<span class="label" id="label_wb_movehand_width" origtitle="'.__("The width of the hand/mouse.", 'rs_whiteboard').'">'.__("Width", 'rs_whiteboard').'</span>
<input type="text" class="text-sidebar-long withlabel" id="wb_movehand_width" name="wb_movehand_width" value="'. RevSliderFunctions::getVal($slider_params, 'wb_movehand_width', '400').'"> <span>px</span>
<div class="clear"></div>

<span class="label" id="label_wb_movehand_height" origtitle="'.__("The height of the hand/mouse.", 'rs_whiteboard').'">'.__("Height", 'rs_whiteboard').'</span>
<input type="text" class="text-sidebar-long withlabel" id="wb_movehand_height" name="wb_movehand_height" value="'. RevSliderFunctions::getVal($slider_params, 'wb_movehand_height', '1000').'"> <span>px</span>
<div class="clear"></div>

<span class="label" id="label_wb_movehand_origin_x" origtitle="'.__("The origin of the hand/mouse.", 'rs_whiteboard').'">'.__("Origin x/y", 'rs_whiteboard').'</span>
<input type="text" class="withlabel" style="width: 45px" id="wb_movehand_origin_x" name="wb_movehand_origin_x" value="'. RevSliderFunctions::getVal($slider_params, 'wb_movehand_origin_x', '50').'"> 
<input type="text" class="" style="width: 45px" id="wb_movehand_origin_y" name="wb_movehand_origin_y" value="'. RevSliderFunctions::getVal($slider_params, 'wb_movehand_origin_y', '50').'">   <a href="javascript:void(0)" data-hand="move" class="button-image-select-wb-hand-origin button-primary revblue">'.__('Set', 'rs_whiteboard').'</a>
<div class="clear"></div>

<h4>'.__("Move Hand Defaults", 'rs_whiteboard').'</h4>

<span class="label" id="label_wb_movehand_type" origtitle="'.__("Choose the Draw/Write Hand type.", 'rs_whiteboard').'">'.__("Type", 'rs_whiteboard').'</span>
<select id="wb_movehand_type" name="wb_movehand_type" class="withlabel">
	<option value="right"';
		if($wb_movehand_type == "right") $markup .= ' selected="selected"';
		$markup .= '>'.__("Right", 'rs_whiteboard').'</option>
	<option value="left"';
		if($wb_movehand_type == "left") $markup .= ' selected="selected"';
		$markup .= '>'.__("Left", 'rs_whiteboard').'</option>
</select>

</div>';

		$markup .='<div class="wb-origin-dialog" style="display:none" title="'.__('Set Origin', 'rs_whiteboard').'">	
	<div id="wb-origin-selector-wrapper">
		<!-- ADD IMAGE IN HERE AS YOU WISH ON IMAGE DIALOG OPEN -->
	</div>
</div>
</div>
';
		
		$settings['whiteboard'] = array(
			'title'		=> __('Whiteboard', 'rs_whiteboard'),
			'icon'		=> 'eg-icon-clipboard',
			'markup'	=> $markup,
			'javascript' => '

				var wb_writehand_sources= [	{x:49, y:50, src:"'.WHITEBOARD_PLUGIN_URL."assets/images/write_right_angle.png".'"},
											{x:49, y:50, src:"'.WHITEBOARD_PLUGIN_URL."assets/images/write_right_angle.png".'"},
											{x:49, y:50, src:"'.WHITEBOARD_PLUGIN_URL."assets/images/write_right_angle.png".'"}],
					wb_movehand_sources= [	{x:186, y:66, src:"'.WHITEBOARD_PLUGIN_URL."assets/images/hand_point_right.png".'"},
											{x:186, y:66, src:"'.WHITEBOARD_PLUGIN_URL."assets/images/hand_point_right.png".'"},
											{x:186, y:66, src:"'.WHITEBOARD_PLUGIN_URL."assets/images/hand_point_right.png".'"}];

			'
		);
		
		return $settings;
	}
}
?>