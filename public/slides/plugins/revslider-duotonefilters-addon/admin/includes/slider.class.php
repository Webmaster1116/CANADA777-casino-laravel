<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

require_once(RS_DUOTONEFILTERS_PLUGIN_PATH . 'framework/slider.admin.class.php');

class RsDuotoneFiltersSliderAdmin extends RsAddonDuotoneFiltersSliderAdmin {
	
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
		
		$_enabled     = RevSliderFunctions::getVal($_slider, 'duotonefilters_enabled', false) == 'true' ? ' checked' : '';
		$_simplified  = RevSliderFunctions::getVal($_slider, 'duotonefilters_simplified', false) == 'true' ? ' checked' : '';
		$_easing      = RevSliderFunctions::getVal($_slider, 'duotonefilters_easing', 'ease-in');
		$_timing      = RevSliderFunctions::getVal($_slider, 'duotonefilters_timing', '750');
		$_display     = $_simplified ? 'block' : 'none';
		$_textDomain  = 'rs_' . static::$_Title;
		
		$_easings = array(
			
			'ease'           => 'ease', 
			'ease-in'        => 'ease-in',
			'ease-out'       => 'ease-out',
			'ease-in-out'    => 'ease-in-out',
			'linear'         => 'linear',
			'easeInCubic'    => 'cubic-bezier(0.550, 0.055, 0.675, 0.190)',
			'easeOutCubic'   => 'cubic-bezier(0.215, 0.610, 0.355, 1.000)',
			'easeInOutCubic' => 'cubic-bezier(0.645, 0.045, 0.355, 1.000)',
			'easeInQuart'    => 'cubic-bezier(0.895, 0.030, 0.685, 0.220)',
			'easeOutQuart'   => 'cubic-bezier(0.165, 0.840, 0.440, 1.000)',
			'easeInOutQuart' => 'cubic-bezier(0.770, 0.000, 0.175, 1.000)'
		
		);
		
		$_markup = '
		
		<div id="duotonefilters-addon-settings">
		
			<span class="label" id="label_duotonefilters_enabled" origtitle="' . __("Enable/Disable the Duotone Filters Add-On for the Slider.<br><br>", $_textDomain) . '">' . __('Enable Addon for this Slider', $_textDomain) . '</span> 
			<input type="checkbox" class="tp-moderncheckbox withlabel" id="duotonefilters_enabled" name="duotonefilters_enabled"' . $_enabled . ' />
			<br>
			
			<span class="label" id="label_duotonefilters_simplified" origtitle="' . __("Override the default slide transitions with a smooth cross-fade<br><br>", $_textDomain) . '">' . __('Simplify Transitions', $_textDomain) . '</span> 
			<input type="checkbox" class="tp-moderncheckbox withlabel" id="duotonefilters_simplified" name="duotonefilters_simplified"' . $_simplified . ' onchange="document.getElementById(\'duotone-transition-settings\').style.display=this.checked ? \'block\' : \'none\'" />
			<br>
			
			<div id="duotone-transition-settings" style="display: ' . $_display . '">
			
				<span class="label" id="label_duotonefilters_easing" origtitle="' . __("CSS easing equation for the simplified cross-fade<br><br>", $_textDomain) . '">' . __('Easing', $_textDomain) . '</span>
				<select id="duotonefilters_easing" class="withlabel" name="duotonefilters_easing" value="' . $_easing . '">';
					
				foreach($_easings as $_key => $_val) {
					$_selected = $_val !== $_easing ? '' : ' selected';
					$_markup .= '<option value="' . $_val . '"' . $_selected . '>' . $_key . '</option>';
				}
				
				$_markup .= '</select>
			
				<span class="label" id="label_duotonefilters_timing" origtitle="' . __("Transition timing for the simplified transition (in milliseconds)<br><br>", $_textDomain) . '">' . __('Duration', $_textDomain) . '</span> 
				<input type="text" class="text-sidebar withlabel" id="duotonefilters_timing" name="duotonefilters_timing" value="' . $_timing . '" /> ms
			
			</div>
			
		</div>';
		
		static::$_Markup = $_markup;
		static::$_Icon = 'eg-icon-magic';
		static::$_JavaScript = '';
		
	}
	
}
?>