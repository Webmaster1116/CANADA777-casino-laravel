<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

require_once(RS_SLICEY_PLUGIN_PATH . 'framework/slider.admin.class.php');

class RsSliceySliderAdmin extends RsAddonSliceySliderAdmin {
	
	protected static $_Icon,
					 $_Title,
					 $_Markup,
					 $_Version,
					 $_JavaScript;
	
	public function __construct($_title, $_version) {
		
		static::$_Title = $_title;
		static::$_Version = $_version;
		parent::init();
		
	}
	
	protected static function _init($_slider) {
		
		$_enabled    = RevSliderFunctions::getVal($_slider, 'slicey_enabled',        false) == 'true' ? ' checked' : '';
		$_time       = RevSliderFunctions::getVal($_slider, 'slicey_def_time',       '7000');
		$_easing     = RevSliderFunctions::getVal($_slider, 'slicey_def_easing',     'Linear.easeNone');
		$_scale      = RevSliderFunctions::getVal($_slider, 'slicey_def_scale',      '150');
		$_blur       = RevSliderFunctions::getVal($_slider, 'slicey_def_blur',       '10');
		$_strength   = RevSliderFunctions::getVal($_slider, 'slicey_def_strength',   '3');
		$_color      = RevSliderFunctions::getVal($_slider, 'slicey_def_color',      'rgba(0, 0, 0, 0.25)');
		$_offset     = RevSliderFunctions::getVal($_slider, 'slicey_def_offset',     '20');
		$_width      = RevSliderFunctions::getVal($_slider, 'slicey_def_width',      '200');
		$_height     = RevSliderFunctions::getVal($_slider, 'slicey_def_height',     '400');
		$_blurgstart = RevSliderFunctions::getVal($_slider, 'slicey_def_blurgstart', '0');
		$_blurgend   = RevSliderFunctions::getVal($_slider, 'slicey_def_blurgend',   '0');
		$_blurlstart = RevSliderFunctions::getVal($_slider, 'slicey_def_blurlstart', 'inherit');
		$_blurlend   = RevSliderFunctions::getVal($_slider, 'slicey_def_blurlend',   'inherit');
		
		$_showSettings = $_enabled ? 'block' : 'none';
		$_textDomain = 'rs_' . static::$_Title;
		
		$_markup = '<div id="slicey-addon-settings">
		
			<span class="label" id="label_slicey_enabled" origtitle="' . __("Enable/Disable the Slicey Add-On for the Slider.<br><br>", $_textDomain) . '">' . __('Enable Slicey for Slides', $_textDomain) . '</span>
			<input type="checkbox" class="tp-moderncheckbox withlabel" id="slicey_enabled" name="slicey_enabled"' . $_enabled . ' 
				onchange="document.getElementById(\'slicey-settings\').style.display=this.checked ? \'block\' : \'none\'" />
			
			<div id="slicey-settings" style="display: ' . $_showSettings . '; margin-top: 7px">
				
				<h4>Global Defaults</h4>
				
				<span class="label" id="label_slicey_def_scale" origtitle="' . __("The default scaling percentage for the main background image.<br><br>", $_textDomain) . '">' . __('Scale To', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel slicey-min-max" data-min="100" data-max="1000" id="slicey_def_scale" name="slicey_def_scale" value="' . $_scale . '" /> %
				<br>
				
				<span class="label" id="label_slicey_def_time" origtitle="' . __("The default amount of time the effect should last for (in milliseconds).<br><br>", $_textDomain) . '">' . __('Duration', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel slicey-min-max" data-min="1000" data-max="100000" id="slicey_def_time" name="slicey_def_time" value="' . $_time . '" /> ms
				<br>
				
				<span class="label" id="label_slicey_def_easing" origtitle="' . __("The default easing equation to apply to the effect. Use Linear.easeNone for a traditional effect.<br><br>", $_textDomain) . '">' . __('Easing', $_textDomain) . '</span>
				<select value="' . $_easing . '" id="slicey_def_easing" name="slicey_def_easing" class="withlabel">
					<option value="Linear.easeNone">Linear.easeNone</option>
					<option value="Power0.easeIn">Power0.easeIn </option>
					<option value="Power0.easeInOut">Power0.easeInOut</option>
					<option value="Power0.easeOut">Power0.easeOut</option>
					<option value="Power1.easeIn">Power1.easeIn</option>
					<option value="Power1.easeInOut">Power1.easeInOut</option>
					<option value="Power1.easeOut">Power1.easeOut</option>
					<option value="Power2.easeIn">Power2.easeIn</option>
					<option value="Power2.easeInOut">Power2.easeInOut</option>
					<option value="Power2.easeOut">Power2.easeOut</option>
					<option value="Power3.easeIn">Power3.easeIn</option>
					<option value="Power3.easeInOut">Power3.easeInOut</option>
					<option value="Power3.easeOut">Power3.easeOut</option>
					<option value="Power4.easeIn">Power4.easeIn</option>
					<option value="Power4.easeInOut">Power4.easeInOut</option>
					<option value="Power4.easeOut">Power4.easeOut</option>
					<option value="Back.easeIn">Back.easeIn</option>
					<option value="Back.easeInOut">Back.easeInOut</option>
					<option value="Back.easeOut">Back.easeOut</option>
					<option value="Bounce.easeIn">Bounce.easeIn</option>
					<option value="Bounce.easeInOut">Bounce.easeInOut</option>
					<option value="Bounce.easeOut">Bounce.easeOut</option>
					<option value="Circ.easeIn">Circ.easeIn</option>
					<option value="Circ.easeInOut">Circ.easeInOut</option>
					<option value="Circ.easeOut">Circ.easeOut</option>
					<option value="Elastic.easeIn">Elastic.easeIn</option>
					<option value="Elastic.easeInOut">Elastic.easeInOut</option>
					<option value="Elastic.easeOut">Elastic.easeOut</option>
					<option value="Expo.easeIn">Expo.easeIn</option>
					<option value="Expo.easeInOut">Expo.easeInOut</option>
					<option value="Expo.easeOut">Expo.easeOut</option>
					<option value="Sine.easeIn">Sine.easeIn</option>
					<option value="Sine.easeInOut">Sine.easeInOut</option>
					<option value="Sine.easeOut">Sine.easeOut</option>
					<option value="SlowMo.ease">SlowMo.ease</option>
				</select>
				
				<span class="label" id="label_slicey_def_blurgstart" origtitle="' . __("The default starting CSS3 blur filter to the main background image.<br><br>", $_textDomain) . '">' . __('Blur Start', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel" id="slicey_def_blurgstart" name="slicey_def_blurgstart" value="' . $_blurgstart . '" /> px
				<br>
				
				<span class="label" id="label_slicey_def_blurgend" origtitle="' . __("The default ending CSS3 blur filter to the main background image.<br><br>", $_textDomain) . '">' . __('Blur End', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel" id="slicey_def_blurgend" name="slicey_def_blurgend" value="' . $_blurgend . '" /> px
				<br>
				
				<span class="label" id="label_slicey_def_blur" origtitle="' . __("The default blur amount to apply to the CSS3 Box Shadow for the Slicey Layers.<br><br>", $_textDomain) . '">' . __('Shadow Blur', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel" id="slicey_def_blur" name="slicey_def_blur" value="' . $_blur . '" /> px
				<br>
				
				<span class="label" id="label_slicey_def_strength" origtitle="' . __("The default box-shadow strength to apply to the Slicey Layers.<br><br>", $_textDomain) . '">' . __('Shadow Strength', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel" id="slicey_def_strength" name="slicey_def_strength" value="' . $_strength . '" /> px
				<br>
				
				<span class="label" id="label_slicey_def_color" origtitle="' . __("The default box-shadow color for the Slicey Layers.<br><br>", $_textDomain) . '">' . __('Shadow Color', $_textDomain) . '</span>
				<input type="hidden" class="text-sidebar withlabel" id="slicey_def_color" name="slicey_def_color" value="' . $_color . '" />
				<br>
				
				<h4>Layer Defaults</h4>
				<span class="label" id="label_slicey_def_offset" origtitle="' . __("The default offset scale for the Slicey Layer. This offset value is what creates the Slicey Effect.<br><br>", $_textDomain) . '">' . __('Scale Offset', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel" id="slicey_def_offset" name="slicey_def_offset" value="' . $_offset . '" /> %
				<br>
				
				<span class="label" id="label_slicey_def_width" origtitle="' . __("The default Slicey Layer Width.<br><br>", $_textDomain) . '">' . __('Width', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel" id="slicey_def_width" name="slicey_def_width" value="' . $_width . '" /> px
				<br>
				
				<span class="label" id="label_slicey_def_height" origtitle="' . __("The default Slicey Layer Height.<br><br>", $_textDomain) . '">' . __('Height', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel" id="slicey_def_height" name="slicey_def_height" value="' . $_height . '" /> px
				<br>
				
				<span class="label" id="label_slicey_def_blurlstart" origtitle="' . __("The default starting CSS3 blur filter to the Slicey Layer.<br><br>", $_textDomain) . '">' . __('Blur Start', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel" id="slicey_def_blurlstart" name="slicey_def_blurlstart" value="' . $_blurlstart . '" /> px
				<br>
				
				<span class="label" id="label_slicey_def_blurlend" origtitle="' . __("The default ending CSS3 blur filter to the Slicey Layer.<br><br>", $_textDomain) . '">' . __('Blur End', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel" id="slicey_def_blurlend" name="slicey_def_blurlend" value="' . $_blurlend . '" /> px
				<br>
					
			</div>
			
			<style type="text/css">.setting_box .fa-icon-object-ungroup:before {position: relative; top: 3px} #slicey_def_color_wrap {position: relative; top: 8px}</style>
			
		</div>';
		
		static::$_Markup = $_markup;
		static::$_Icon = 'fa-icon-object-ungroup';
		static::$_JavaScript = '
			
			jQuery(function() {
				
				// activate the color picker
				jQuery("#slicey_def_color").tpColorPicker({mode: "single", wrapId: "slicey_def_color_wrap", editing: "Shadow Color"});
				
				// handle inputs with min/max values
				jQuery(".slicey-min-max").on("change", function() {
					
					this.value = Math.max(parseFloat(this.getAttribute("data-min")), 
								 Math.min(parseFloat(this.getAttribute("data-max")), parseFloat(this.value))); 
					
				});
				
			});
		
		';
		
	}
	
}
?>