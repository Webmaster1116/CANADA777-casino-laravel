<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_REVEALER_PLUGIN_PATH . 'public/includes/preloaders.class.php');
require_once(RS_REVEALER_PLUGIN_PATH . 'framework/slider.admin.class.php');

class RsRevealerSliderAdmin extends RsAddonRevealerSliderAdmin {
	
	protected static $_Icon,
					 $_Title,
					 $_Markup,
					 $_JavaScript;
	
	public function __construct($_title) {
		
		static::$_Title = $_title;
		parent::init();
		
	}
	
	protected static function _init($_slider) {
		
		$_enabled          = RevSliderFunctions::getVal($_slider, 'revealer_enabled', false) == 'true' ? ' checked' : '';
		$_overlay_enabled  = RevSliderFunctions::getVal($_slider, 'revealer_overlay_enabled', false) == 'true' ? ' checked' : '';
		$_direction        = RevSliderFunctions::getVal($_slider, 'revealer_direction', 'open_horizontal');
		
		$_color            = RevSliderFunctions::getVal($_slider, 'revealer_color', '#000000');
		$_duration         = RevSliderFunctions::getVal($_slider, 'revealer_duration', '500');
		$_delay            = RevSliderFunctions::getVal($_slider, 'revealer_delay', '0');
		$_easing           = RevSliderFunctions::getVal($_slider, 'revealer_easing', 'Power2.easeOut');
		
		$_overlay_color    = RevSliderFunctions::getVal($_slider, 'revealer_overlay_color', '#000000');
		$_overlay_duration = RevSliderFunctions::getVal($_slider, 'revealer_overlay_duration', '500');
		$_overlay_delay    = RevSliderFunctions::getVal($_slider, 'revealer_overlay_delay', '0');
		$_overlay_easing   = RevSliderFunctions::getVal($_slider, 'revealer_overlay_easing', 'Power2.easeOut');
		
		$_spinner          = RevSliderFunctions::getVal($_slider, 'revealer_spinner', 'default');
		$_spinner_color    = RevSliderFunctions::getVal($_slider, 'revealer_spinner_color', '#FFFFFF');
		
		$_showOverlay      = $_overlay_enabled ? 'block' : 'none';
		$_showSettings     = $_enabled ? 'block' : 'none';
		$_textDomain       = 'rs_' . static::$_Title;
		
		$_preloaders       = RsAddOnRevealPreloaders::getPreloaders();
		$_preloaders       = json_encode($_preloaders);
		$_preloaders       = addslashes($_preloaders);
		
		$_directions = array(
			
			'none'               => 'None',
			'CURTAINS OPEN'      => 'disabled',
			'open_horizontal'    => 'Open Horizontal',
			'open_vertical'      => 'Open Vertical',
			'split_left_corner'  => 'Open Left Corner',
			'split_right_corner' => 'Open Right Corner',
			'CIRCULAR'           => 'disabled',
			'shrink_circle'      => 'Shrink Circle',
			'expand_circle'      => 'Expand Circle',
			'CORNER TO CORNER'   => 'disabled',
			'left_to_right'      => 'Left to Right',
			'right_to_left'      => 'Right to Left',
			'top_to_bottom'      => 'Top to Bottom',
			'bottom_to_top'      => 'Bottom to Top',
			'SIDE TO SIDE'       => 'disabled',
			'tlbr_skew'          => 'Top Left to Bottom Right',
			'trbl_skew'          => 'Top Right to Bottom Left',
			'bltr_skew'          => 'Bottom Left to Top Right',
			'brtl_skew'          => 'Bottom Right to Top Left',
		
		);
		
		$_easings = array(
			
			'Linear.easeNone', 
			'Power0.easeIn',
			'Power0.easeInOut',
			'Power0.easeOut',
			'Power1.easeIn',
			'Power1.easeInOut',
			'Power1.easeOut',
			'Power2.easeIn',
			'Power2.easeInOut',
			'Power2.easeOut',
			'Power3.easeIn',
			'Power3.easeInOut',
			'Power3.easeOut',
			'Power4.easeIn',
			'Power4.easeInOut',
			'Power4.easeOut',
			'Back.easeIn',
			'Back.easeInOut',
			'Back.easeOut',
			'Bounce.easeIn',
			'Bounce.easeInOut',
			'Bounce.easeOut',
			'Circ.easeIn',
			'Circ.easeInOut',
			'Circ.easeOut',
			'Elastic.easeIn',
			'Elastic.easeInOut',
			'Elastic.easeOut',
			'Expo.easeIn',
			'Expo.easeInOut',
			'Expo.easeOut',
			'Sine.easeIn',
			'Sine.easeInOut',
			'Sine.easeOut',
			'SlowMo.ease'
		
		);
		
		$_spinners = array(
		
			'default' => 'Default',
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
			'7' => '7',
			'8' => '8',
			'9' => '9',
			'10' => '10'
		
		);
		
		static::$_Markup = 
		
		'<span class="label" id="label_revealer_enabled" origtitle="' . __("Enable/Disable Reveal Preloaders for this Slider<br><br>", $_textDomain) . '">' . __('Activate Reveal Preloaders', $_textDomain) . '</span>
		<input type="checkbox" class="tp-moderncheckbox withlabel" id="revealer_enabled" name="revealer_enabled"' . $_enabled . ' 
			onchange="document.getElementById(\'revealer-settings\').style.display=this.checked ? \'block\' : \'none\'" />
		
		<div id="revealer-settings" style="display: ' . $_showSettings . '">
		
			<h4>Revealer Settings</h4>
			
			<span class="label" id="label_revealer_direction" origtitle="' . __('The Opening Reveal Option/Direction', $_textDomain) . '">' . __("Opening Reveal", $_textDomain) . '</span>
			<select class="withlabel" id="revealer_direction" name="revealer_direction">';
				
				foreach($_directions as $_key => $_val) {
					
					if($_val !== 'disabled') {
						$_selected = $_key !== $_direction ? '' : ' selected';
						static::$_Markup .= '<option value="' . $_key . '"' . $_selected . '>' . $_val . '</option>';
					}
					else {	
						static::$_Markup .= '<option disabled>' . $_key . '</option>';
					}
					
				}
				
			static::$_Markup .= '</select>
			
			<div id="revealer-opening-settings">
			
				<div id="revealer-color-wrap">
					<span class="label" id="label_revealer_color" origtitle="' . __('The reveal transition color<br><br>', $_textDomain) . '">' . __("Reveal Color", $_textDomain) . '</span>
					<input id="revealer_color" name="revealer_color" type="text" class="revealer-color-picker rs-layer-input-field tipsy_enabled_top" title="Select a Color" value="' . $_color . '" data-editing="Reveal Color" />
					<br>
				</div>
				
				<span class="label" id="label_revealer_easing" origtitle="' . __('The Reveal Easing Function', $_textDomain) . '">' . __("Reveal Easing", $_textDomain) . '</span>
				<select class="withlabel" id="revealer_easing" name="revealer_easing">';
					
					foreach($_easings as $_ease) {
						
						$_selected = $_ease !== $_easing ? '' : ' selected';
						static::$_Markup .= '<option value="' . $_ease . '"' . $_selected . '>' . $_ease . '</option>';
						
					}
					
				static::$_Markup .= '</select>
				<br>
				
				<span class="label id="label_revealer_duration" origtitle="' . __('The reveal transition duration<br><br>', $_textDomain) . '">' . __("Reveal Duration", $_textDomain) . '</span>
				<input id="revealer_duration" name="revealer_duration" type="text" class="rs-layer-input-field tipsy_enabled_top" title="Transition Duration" value="' . $_duration . '" />
				<br>
				
				<span class="label id="label_revealer_delay" origtitle="' . __('Delay before the opening begins<br><br>', $_textDomain) . '">' . __("Reveal Delay", $_textDomain) . '</span>
				<input id="revealer_delay" name="revealer_delay" type="text" class="rs-layer-input-field tipsy_enabled_top" title="Reveal Delay" value="' . $_delay . '" />
				<br>
				
				<span class="label" id="label_revealer_overlay_enabled" origtitle="' . __("Enable/Disable Initial Overlay<br><br>", $_textDomain) . '">' . __('Enable Initial Overlay', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="revealer_overlay_enabled" name="revealer_overlay_enabled"' . $_overlay_enabled . ' 
					onchange="document.getElementById(\'revealer-overlay-settings\').style.display=this.checked ? \'block\' : \'none\'" />
				
				<div id="revealer-overlay-settings" style="display: ' . $_showOverlay . '">
					
					<div class="withsublabels">
					
						<span class="label" id="label_revealer_overlay_color" origtitle="' . __('The initial overlay for the reveal effect<br><br>', $_textDomain) . '">' . __("Overlay Color", $_textDomain) . '</span>
						<input id="revealer_overlay_color" name="revealer_overlay_color" type="text" class="revealer-color-picker rs-layer-input-field tipsy_enabled_top" title="Select an Overlay Color" value="' . $_overlay_color . '" data-editing="Reveal Overlay" />
						<br>
					
						<span class="label" id="label_revealer_overlay_easing" origtitle="' . __('The Overlay Easing Function', $_textDomain) . '">' . __("Overlay Easing", $_textDomain) . '</span>
						<select class="withlabel" id="revealer_overlay_easing" name="revealer_overlay_easing">';
							
							foreach($_easings as $_ease) {
								
								$_selected = $_ease !== $_overlay_easing ? '' : ' selected';
								static::$_Markup .= '<option value="' . $_ease . '"' . $_selected . '>' . $_ease . '</option>';
								
							}
							
						static::$_Markup .= '</select>
						<br>
						
						<span class="label id="label_revealer_overlay_duration" origtitle="' . __('The overlay fadeout duration<br><br>', $_textDomain) . '">' . __("Overlay Duration", $_textDomain) . '</span>
						<input id="revealer_overlay_duration" name="revealer_overlay_duration" type="text" class="rs-layer-input-field tipsy_enabled_top" title="Transition Duration" value="' . $_overlay_duration . '" />
						<br>
						
						<span class="label id="label_revealer_overlay_delay" origtitle="' . __('Delay before the overlay animation begins<br><br>', $_textDomain) . '">' . __("Overlay Delay", $_textDomain) . '</span>
						<input id="revealer_overlay_delay" name="revealer_overlay_delay" type="text" class="rs-layer-input-field tipsy_enabled_top" title="Overlay Delay" value="' . $_overlay_delay . '" />
					
					</div>
						
				</div>
				
			</div>
			
			<h4>Spinner Settings</h4>
			
			<span class="label" id="label_revealer_spinner" origtitle="' . __('The animated preloading spinner<br><br>', $_textDomain) . '">' . __("Animated Spinner", $_textDomain) . '</span>
			<select id="revealer_spinner" name="revealer_spinner" class="withlabel">';
			
			foreach($_spinners as $_key => $_val) {
				
				$_selected = $_key != $_spinner ? '' : ' selected';
				static::$_Markup .= '<option value="' . $_key . '"' . $_selected . '>' . $_val . '</option>';
				
			}

			static::$_Markup .= '</select>
			
			<div id="revealer-spinner-wrap">
				<span class="label" id="label_revealer_spinner_color" origtitle="' . __('The animated spinner color<br><br>', $_textDomain) . '">' . __("Spinner Color", $_textDomain) . '</span>
				<input id="revealer_spinner_color" name="revealer_spinner_color" type="text" class="revealer-color-picker rs-layer-input-field tipsy_enabled_top" title="Select a Color" value="' . $_spinner_color . '" data-editing="Spinner Color" data-mode="basic" />
				<div id="revealer_spinner_preview"></div>
			</div>
			
			<style type="text/css">
			
				#revealer-settings .label {min-width: 150px !important} 
				#revealer_direction option:disabled {font-weight: bold}
				#revealer_spinner_preview {width: 100%; height: 100px; position: relative; margin-top: 20px; background: #F1C40F}
				
			</style>
			
		</div>';

		static::$_Icon = 'eg-icon-magic';
		static::$_JavaScript = "var rsAddOnPreloaders = '" . $_preloaders . "';";
		
	}
}
?>