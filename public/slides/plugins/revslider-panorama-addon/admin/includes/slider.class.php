<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

require_once(RS_PANORAMA_PLUGIN_PATH . 'framework/slider.admin.class.php');

class RsPanoramaSliderAdmin extends RsAddonPanoramaSliderAdmin {
	
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
		
		$_enabled         = RevSliderFunctions::getVal($_slider, 'panorama_enabled',          false) == 'true' ? ' checked' : '';
		$_autoplay        = RevSliderFunctions::getVal($_slider, 'panorama_autoplay',         false) == 'true' ? ' checked' : '';
		$_mousehweelZoom  = RevSliderFunctions::getVal($_slider, 'panorama_mousewheel_zoom',  false) == 'true' ? ' checked' : '';
		$_smoothZoom      = RevSliderFunctions::getVal($_slider, 'panorama_smooth_zoom',      true)  == 'true' ? ' checked' : '';
		$_direction       = RevSliderFunctions::getVal($_slider, 'panorama_direction',        'forward');
		$_control         = RevSliderFunctions::getVal($_slider, 'panorama_controls',         'throw');
		$_speed           = RevSliderFunctions::getVal($_slider, 'panorama_speed',            '100');
		$_throwSpeed      = RevSliderFunctions::getVal($_slider, 'panorama_throw_speed',      '750');
		$_zoomMin         = RevSliderFunctions::getVal($_slider, 'panorama_zoom_min',         '75');
		$_zoomMax         = RevSliderFunctions::getVal($_slider, 'panorama_zoom_max',         '150');
		$_cameraFov       = RevSliderFunctions::getVal($_slider, 'panorama_camera_fov',       '75');
		$_cameraFar       = RevSliderFunctions::getVal($_slider, 'panorama_camera_far',       '1000');
		$_sphereRadius    = RevSliderFunctions::getVal($_slider, 'panorama_sphere_radius',    '100');
		$_sphereWsegments = RevSliderFunctions::getVal($_slider, 'panorama_sphere_wsegments', '100');
		$_sphereHsegments = RevSliderFunctions::getVal($_slider, 'panorama_sphere_hsegments', '40');
		
		$_textDomain    = 'rs_' . static::$_Title;
		$_showSettings  = !empty($_enabled)        ? 'block' : 'none';
		$_autoplaySets  = !empty($_autoplay)       ? 'block' : 'none';
		$_throwSettings = $_control === 'throw'    ? 'block' : 'none';
		
		$_directions = array('forward', 'backward');
		$_controls   = array(
			
			'throw' => 'Drag Smooth',
			'drag'  => 'Drag Simple',
			'mouse' => 'Mouse Move',
			'click' => 'Mouse Click',
			'none'  => 'None'
		
		);
		
		$_markup = '<div id="panorama-addon-settings">
		
			<span class="label" id="label_panorama_enabled" origtitle="' . __("Enable/Disable the Panorama Add-On for the Slider.<br><br>", $_textDomain) . '">' . __('Enable Addon for this Slider', $_textDomain) . '</span> 
			<input type="checkbox" class="tp-moderncheckbox withlabel" id="panorama_enabled" name="panorama_enabled"' . $_enabled . ' onchange="document.getElementById(\'panorama-settings\').style.display=this.checked ? \'block\' : \'none\'" />
			
			<div id="panorama-settings" style="display: ' . $_showSettings . '">
				
				<h4>Default Settings</h4>
				<span class="label" id="label_panorama_autoplay" origtitle="' . __("<br><br>", $_textDomain) . '">' . __('Autoplay', $_textDomain) . '</span> 
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="panorama_autoplay" name="panorama_autoplay"' . $_autoplay . ' onchange="document.getElementById(\'panorama-autoplay-settings\').style.display=this.checked ? \'block\' : \'none\'" />
				<br>
				
				<div id="panorama-autoplay-settings" class="withsublabels" style="display: ' . $_autoplaySets . '">
				
					<span class="label" id="label_panorama_direction" origtitle="' . __("<br><br>", $_textDomain) . '">' . __('Direction', $_textDomain) . '</span>
					<select id="panorama_direction" class="withlabel" name="panorama_direction" value="' . $_direction . '">;';
					
						foreach($_directions as $_directn) {
							
							$_selected = $_directn === $_direction ? ' selected' : '';
							$_markup .= '<option value="' . $_directn . '"' . $_selected . '>' . __(ucfirst($_directn), $_textDomain) . '</option>';
							
						}

					$_markup .= '</select>
					
					<span class="label" id="label_panorama_speed" origtitle="' . __("Recommended: 100<br><br>", $_textDomain) . '">' . __('Speed', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="panorama_speed" name="panorama_speed" value="' . $_speed . '" />
					<br>
					
				</div>
				
				<span class="label" id="label_panorama_controls" origtitle="' . __("<br><br>", $_textDomain) . '">' . __('Interaction', $_textDomain) . '</span>
				<select id="panorama_controls" class="withlabel" name="panorama_controls" value="' . $_direction . '">;';
				
					foreach($_controls as $_key => $_value) {
						
						$_selected = $_key !== $_control ? '' : ' selected';
						$_markup .= '<option value="' . $_key . '"' . $_selected . '>' . $_value . '</option>';
						
					}

				$_markup .= '</select>
				
				<div id="panorama-throw-speed" class="withsublabels" style="display: ' . $_throwSettings . '">
				
					<span class="label" id="label_panorama_throw_speed" origtitle="' . __("Recommended: 500-900<br><br>", $_textDomain) . '">' . __('Drag Speed', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel panorama-min-max" data-min="300" data-max="975" id="panorama_throw_speed" name="panorama_throw_speed" data-orig="' . $_throwSpeed . '" value="' . $_throwSpeed . '" />
					<br>
					
				</div>
				
				<span class="label" origtitle="' . __("<br><br>", $_textDomain) . '">' . __('Zoom Settings', $_textDomain) . '</span> 
				<input type="checkbox" class="tp-moderncheckbox" onchange="document.getElementById(\'panorama-zoom-settings\').style.display=this.checked ? \'block\' : \'none\'" />
				<br>
				
				<div id="panorama-zoom-settings" class="withsublabels" style="display: none">
				
					<span class="label" id="label_panorama_mousewheel_zoom" origtitle="' . __("<br><br>", $_textDomain) . '">' . __('Mousehweel Zoom', $_textDomain) . '</span> 
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="panorama_mousewheel_zoom" name="panorama_mousewheel_zoom"' . $_mousehweelZoom . ' />
					<br>
					
					<span class="label" id="label_panorama_smooth_zoom" origtitle="' . __("<br><br>", $_textDomain) . '">' . __('Smooth Zoom', $_textDomain) . '</span> 
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="panorama_smooth_zoom" name="panorama_smooth_zoom"' . $_smoothZoom . ' />
					<br>
					
					<span class="label" id="label_panorama_zoom_min" origtitle="' . __("<br><br>", $_textDomain) . '">' . __('Zoom Min', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel panorama-min-max" id="panorama_zoom_min" name="panorama_zoom_min" value="' . $_zoomMin . '" data-orig="' . $_zoomMin . '" data-min="25" data-max="100" /> %
					<br>
					
					<span class="label" id="label_panorama_zoom_max" origtitle="' . __("<br><br>", $_textDomain) . '">' . __('Zoom Max', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel panorama-min-max" id="panorama_zoom_max" name="panorama_zoom_max" value="' . $_zoomMax . '" data-orig="' . $_zoomMax . '" data-min="100" data-max="175" /> %
					<br>
				
				</div>
				
				<span class="label" origtitle="' . __("<br><br>", $_textDomain) . '">' . __('Advanced Settings', $_textDomain) . '</span> 
				<input type="checkbox" class="tp-moderncheckbox" onchange="document.getElementById(\'panorama-advanced-settings\').style.display=this.checked ? \'block\' : \'none\'" />
				<br>
				
				<div id="panorama-advanced-settings" class="withsublabels" style="display: none">
				
					<span class="label" id="label_panorama_camera_fov" origtitle="' . __("Camera frustum vertical field of view - Recommended: 75<br><br>", $_textDomain) . '">' . __('Camera Fov', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="panorama_camera_fov" name="panorama_camera_fov" value="' . $_cameraFov . '" />
					<br>
					
					<span class="label" id="label_panorama_camera_far" origtitle="' . __("Camera frustum far plane - Recommended: 1000<br><br>", $_textDomain) . '">' . __('Camera Far', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="panorama_camera_far" name="panorama_camera_far" value="' . $_cameraFar . '" />
					<br>
					
					<span class="label" id="label_panorama_sphere_radius" origtitle="' . __("Sphere Radius - Recommended: 100<br><br>", $_textDomain) . '">' . __('Sphere Radius', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="panorama_sphere_radius" name="panorama_sphere_radius" value="' . $_sphereRadius . '" />
					<br>
					
					<span class="label" id="label_panorama_sphere_wsegments" origtitle="' . __("Number of horizontal segments - Recommended: 100<br><br>", $_textDomain) . '">' . __('Width Segments', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="panorama_sphere_wsegments" name="panorama_sphere_wsegments" value="' . $_sphereWsegments . '" />
					<br>
					
					<span class="label" id="label_panorama_sphere_hsegments" origtitle="' . __("Number of vertical segments - Recommended: 40<br><br>", $_textDomain) . '">' . __('Height Segments', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="panorama_sphere_hsegments" name="panorama_sphere_hsegments" value="' . $_sphereHsegments . '" />
					<br>
				
				</div>
					
			</div>
			
		</div>';
		
		static::$_Markup = $_markup;
		static::$_Icon = 'eg-icon-magic';
		static::$_JavaScript = '
		
			document.addEventListener("DOMContentLoaded", function() {
				
				document.getElementById("panorama_controls").addEventListener("change", function() {
					
					var display = this.value !== "throw" ? "none" : "block";
					document.getElementById("panorama-throw-speed").style.display = display;
					
				});
				
				function onMinMax() {
					
					var min = parseInt(this.getAttribute("data-min"), 10),
						max = parseInt(this.getAttribute("data-max"), 10),
						val = parseInt(this.value, 10);
						
					if(isNaN(val)) val = 0;
					if(val < min || val > max) val = parseInt(this.getAttribute("data-orig"), 10);
					this.value = val;
					
				}
				
				var minMax = document.getElementsByClassName("panorama-min-max"),
					i = minMax ? minMax.length : 0;
					
				while(i--) minMax[i].addEventListener("change", onMinMax);
				
			});
		
		';
		
	}
	
}
?>