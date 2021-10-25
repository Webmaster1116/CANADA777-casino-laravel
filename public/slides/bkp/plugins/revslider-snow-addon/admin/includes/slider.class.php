<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_SNOW_PLUGIN_PATH . 'framework/slider.admin.class.php');

class RsSnowSliderAdmin extends RsAddonSnowSliderAdmin {
	
	protected static $_Icon,
					 $_Title,
					 $_Markup,
					 $_JavaScript;
	
	public function __construct($_title) {
		
		static::$_Title = $_title;
		parent::init();
		
	}
	
	protected static function _init($_slider) {
		
		$_enabled               = RevSliderFunctions::getVal($_slider, 'snow_enabled', false) == 'true' ? ' checked' : '';
		$_startSlide            = RevSliderFunctions::getVal($_slider, 'snow_start_slide', 'first');
		$_endSlide              = RevSliderFunctions::getVal($_slider, 'snow_end_slide', 'last');
		$_maxFlakes             = RevSliderFunctions::getVal($_slider, 'snow_max_num', '400');
		$_minSize               = RevSliderFunctions::getVal($_slider, 'snow_min_size', '0.2');
		$_maxSize               = RevSliderFunctions::getVal($_slider, 'snow_max_size', '6');
		$_minOpacity            = RevSliderFunctions::getVal($_slider, 'snow_min_opacity', '0.3');
		$_maxOpacity            = RevSliderFunctions::getVal($_slider, 'snow_max_opacity', '1');
		$_minSpeed              = RevSliderFunctions::getVal($_slider, 'snow_min_speed', '30');
		$_maxSpeed              = RevSliderFunctions::getVal($_slider, 'snow_max_speed', '100');
		$_minSinus              = RevSliderFunctions::getVal($_slider, 'snow_min_sinus', '1');
		$_maxSinus              = RevSliderFunctions::getVal($_slider, 'snow_max_sinus', '100');
		$_alias                 = RevSliderFunctions::getVal($_slider, 'alias', '');
		$_showSettings          = $_enabled ? 'block' : 'none';
		$_textDomain            = 'rs_' . static::$_Title;
		$_total                 = 2;
		
		if($_alias) {
		
			$_clas = new RevSlider();
			$_rev_sliders = $_clas->getArrSliders();
			foreach($_rev_sliders as $_rev_slider) {
				
				if($_alias === $_rev_slider->getAlias()) $_total = $_rev_slider->getNumSlides();
				
			}
			
		}
		
		static::$_Markup = 
		
		'<span class="label" id="label_snow_enabled" origtitle="' . __("Enable/Disable Holiday Snow for this Slider", $_textDomain) . '">' . __('Add Holiday Snow', $_textDomain) . '</span>
		<input type="checkbox" class="tp-moderncheckbox withlabel" id="snow_enabled" name="snow_enabled"' . $_enabled . ' 
			onchange="document.getElementById(\'snow-settings\').style.display=this.checked ? \'block\' : \'none\'" />
		
		<div id="snow-settings" style="display: ' . $_showSettings . '">
		
			<h4>General Settings</h4>
			
			<span class="label" id="label_snow_start_slide" origtitle="' . __('Start Snowing on Slide Number X', $_textDomain) . '">' . __("Start on Slide", $_textDomain) . '</span>
			<select class="withlabel" id="snow_start_slide" name="snow_start_slide" data-default="0">
				<option value="first"';
				
				if($_startSlide === 'first') static::$_Markup .= ' selected';
				static::$_Markup .= '>First Slide</option>';
				
				for($i = 2; $i < $_total + 1; $i++) {
					
					static::$_Markup .= '<option value="' . $i . '"';
					if($_startSlide == $i) static::$_Markup .= ' selected';
					static::$_Markup .= '>' . $i . '</option>';
					
				}
				
			static::$_Markup .= '</select>
			<br>
			
			<span class="label" id="label_snow_end_slide" origtitle="' . __('End Snowing on Slide Number X', $_textDomain) . '">' . __("End on Slide", $_textDomain) . '</span>
			<select class="withlabel" id="snow_end_slide" name="snow_end_slide" data-default="0">
				<option value="last"';
				
				if($_endSlide === 'last') static::$_Markup .= ' selected';
				static::$_Markup .= '>Last Slide</option>';
				
				for($i = 1; $i < $_total; $i++) {
					
					static::$_Markup .= '<option value="' . $i . '"';
					if($_endSlide == $i) static::$_Markup .= ' selected';
					static::$_Markup .= '>' . $i . '</option>';
					
				}
				
			static::$_Markup .= '</select>
			<br>
			
			<h4>Snowflake Settings</h4>
			
			<span class="label" id="label_snow_max" origtitle="' . __('Maximum Number of Snowflakes', $_textDomain) . '">' . __("Max Snowflakes", $_textDomain) . '</span>
			<input type="text" class="text-sidebar withlabel" id="snow_max_num" name="snow_max_num" value="' . $_maxFlakes . '" data-default="400" />
			<br>
			
			<span class="label" id="label_snow_min_size" origtitle="' . __('Minimum Snowflake Size in pixels', $_textDomain) . '">' . __("Min Snowflake Size", $_textDomain) . '</span>
			<input type="text" class="text-sidebar withlabel" id="snow_min_size" name="snow_min_size" value="' . $_minSize . '" data-default="0.2" />
			<span>px</span>
			<br>
			
			<span class="label" id="label_snow_max_size" origtitle="' . __('Maximum Snowflake Size in pixels', $_textDomain) . '">' . __("Max Snowflake Size", $_textDomain) . '</span>
			<input type="text" class="text-sidebar withlabel" id="snow_max_size" name="snow_max_size" value="' . $_maxSize . '" data-default="6" />
			<span>px</span>
			<br>
			
			<span class="label" id="label_snow_min_opacity" origtitle="' . __('Minimum Snowflake Transparency (0-1)', $_textDomain) . '">' . __("Min Opacity (0-1)", $_textDomain) . '</span>
			<input type="text" class="text-sidebar withlabel" id="snow_min_opacity" name="snow_min_opacity" value="' . $_minOpacity . '" data-default="0.3" />
			<br>
			
			<span class="label" id="label_snow_max_opacity" origtitle="' . __('Maximum Snowflake Transparency (0-1)', $_textDomain) . '">' . __("Max Opacity (0-1)", $_textDomain) . '</span>
			<input type="text" class="text-sidebar withlabel" id="snow_max_opacity" name="snow_max_opacity" value="' . $_maxOpacity . '" data-default="1" />
			<br>
			
			<span class="label" id="label_snow_min_speed" origtitle="' . __('Higher the number, the faster the speed', $_textDomain) . '">' . __("Min Speed", $_textDomain) . '</span>
			<input type="text" class="text-sidebar withlabel" id="snow_min_speed" name="snow_min_speed" value="' . $_minSpeed . '" data-default="30" />
			<br>
			
			<span class="label" id="label_snow_max_speed" origtitle="' . __('Higher the number, the faster the speed', $_textDomain) . '">' . __("Max Speed", $_textDomain) . '</span>
			<input type="text" class="text-sidebar withlabel" id="snow_max_speed" name="snow_max_speed" value="' . $_maxSpeed . '" data-default="100" />
			<br>
			
			<span class="label" id="label_snow_min_sinus" origtitle="' . __('Applies additional variation to the falling snow', $_textDomain) . '">' . __("Min Amplitude", $_textDomain) . '</span>
			<input type="text" class="text-sidebar withlabel" id="snow_min_sinus" name="snow_min_sinus" value="' . $_minSinus . '" data-default="1" />
			<br>
			
			<span class="label" id="label_snow_min_sinus" origtitle="' . __('Applies additional variation to the falling snow', $_textDomain) . '">' . __("Max Amplitude", $_textDomain) . '</span>
			<input type="text" class="text-sidebar withlabel" id="snow_max_sinus" name="snow_max_sinus" value="' . $_maxSinus . '" data-default="100" />
			<br>
			
			<div class="toggle-custom-navigation-style visible" onclick="resetSnowDefaults()">Reset to Default Values</div>
			
		</div>';
		
		static::$_Icon = 'eg-icon-magic';
		static::$_JavaScript = 'function resetSnowDefaults() {
			
			if(window.confirm("Reset Settings to their Defaults?")) {
			
				jQuery("#snow-settings").children("select, input").each(function() {
					
					this[this.type === "text" ? "value" : "selectedIndex"] = this.getAttribute("data-default");
					
				});
				
			}
			
		}';
		
	}
}
?>