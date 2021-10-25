<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_PANORAMA_PLUGIN_PATH . 'framework/slide.admin.class.php');

class RsPanoramaSlideAdmin extends RsAddonPanoramaSlideAdmin {
	
	protected static $_Path,
					 $_Title,
					 $_Markup,
					 $_JavaScript;
	
	public function __construct($_title, $_path) {
		
		static::$_Title = $_title;
		static::$_Path = $_path;
		parent::init();
		
	}
	
	protected static function _init($_slider, $_slide) {
		
		$_def_autoplay        = $_slider->getParam('panorama_autoplay',         false);
		$_def_mousehweelZoom  = $_slider->getParam('panorama_mousewheel_zoom',  false);
		$_def_smoothZoom      = $_slider->getParam('panorama_smooth_zoom',      true);
		$_def_direction       = $_slider->getParam('panorama_direction',        'forward');
		$_def_control         = $_slider->getParam('panorama_controls',         'throw');
		$_def_speed           = $_slider->getParam('panorama_speed',            '100');
		$_def_throwSpeed      = $_slider->getParam('panorama_throw_speed',      '750');
		$_def_zoomMin         = $_slider->getParam('panorama_zoom_min',         '75');
		$_def_zoomMax         = $_slider->getParam('panorama_zoom_max',         '150');
		$_def_cameraFov       = $_slider->getParam('panorama_camera_fov',       '75');
		$_def_cameraFar       = $_slider->getParam('panorama_camera_far',       '1000');
		$_def_sphereRadius    = $_slider->getParam('panorama_sphere_radius',    '100');
		$_def_sphereWsegments = $_slider->getParam('panorama_sphere_wsegments', '100');
		$_def_sphereHsegments = $_slider->getParam('panorama_sphere_hsegments', '40');
		
		$_enabled         = $_slide->getParam('panorama_enabled',          false)                == 'true' ? ' checked' : '';
		$_autoplay        = $_slide->getParam('panorama_autoplay',         $_def_autoplay)       == 'true' ? ' checked' : '';
		$_mousehweelZoom  = $_slide->getParam('panorama_mousewheel_zoom',  $_def_mousehweelZoom) == 'true' ? ' checked' : '';
		$_smoothZoom      = $_slide->getParam('panorama_smooth_zoom',      $_def_smoothZoom)     == 'true' ? ' checked' : '';
		$_direction       = $_slide->getParam('panorama_direction',        $_def_direction);
		$_control         = $_slide->getParam('panorama_controls',         $_def_control);
		$_speed           = $_slide->getParam('panorama_speed',            $_def_speed);
		$_throwSpeed      = $_slide->getParam('panorama_throw_speed',      $_def_throwSpeed);
		$_zoomMin         = $_slide->getParam('panorama_zoom_min',         $_def_zoomMin);
		$_zoomMax         = $_slide->getParam('panorama_zoom_max',         $_def_zoomMax);
		$_cameraFov       = $_slide->getParam('panorama_camera_fov',       $_def_cameraFov);
		$_cameraFar       = $_slide->getParam('panorama_camera_far',       $_def_cameraFar);
		$_sphereRadius    = $_slide->getParam('panorama_sphere_radius',    $_def_sphereRadius);
		$_sphereWsegments = $_slide->getParam('panorama_sphere_wsegments', $_def_sphereWsegments);
		$_sphereHsegments = $_slide->getParam('panorama_sphere_hsegments', $_def_sphereHsegments);
		
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
		
		$_markup = '<div id="panorama-addon-settings-wrap">
			
			<p>
				<label>' . __("Enable/Disable:", $_textDomain) . '</label>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="panorama_enabled" name="panorama_enabled"' . $_enabled . ' onchange="document.getElementById(\'panorama-settings\').style.display=this.checked ? \'block\' : \'none\'" />
				<span class="description panorama-description">' . __('Activate Panorama for this Slide', $_textDomain) . '</span>
			</p>
			
			<div id="panorama-settings" style="display: ' . $_showSettings . '">
				
				<p>
					<label>' . __('Autoplay:', $_textDomain) . '</label>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="panorama_autoplay" name="panorama_autoplay"' . $_autoplay . ' onchange="document.getElementById(\'panorama-autoplay-settings\').style.display=this.checked ? \'block\' : \'none\'" />
					<span class="description panorama-description">' . __('Move the panorama in a continous loop', $_textDomain) . '</span>
				</p>
				
				<div id="panorama-autoplay-settings" style="display: ' . $_autoplaySets . '; margin-left: 20px">
					
					<p>
						<label>' . __('Direction:', $_textDomain) . '</label>
						<select id="panorama_direction" class="withlabel" name="panorama_direction" value="' . $_direction . '">;';
						
							foreach($_directions as $_directn) {
								
								$_selected = $_directn === $_direction ? ' selected' : '';
								$_markup .= '<option value="' . $_directn . '"' . $_selected . '>' . __(ucfirst($_directn), $_textDomain) . '</option>';
								
							}

						$_markup .= '</select>
						<span class="description">' . __('Move the picture left-to-right or right-to-left', $_textDomain) . '</span>
					</p>
					
					<p>
						<label>' . __('Speed:', $_textDomain) . '</label>
						<input type="text" class="text-sidebar withlabel" id="panorama_speed" name="panorama_speed" value="' . $_speed . '" />
						<span class="description">' . __('Movement speed for the autoplay option (30-250 recommended)', $_textDomain) . '</span>
					</p>
					
				</div>
				
				<p>
					<label>' . __('Interaction:', $_textDomain) . '</label>
					<select id="panorama_controls" class="withlabel" name="panorama_controls" value="' . $_direction . '">;';
					
						foreach($_controls as $_key => $_value) {
							
							$_selected = $_key !== $_control ? '' : ' selected';
							$_markup .= '<option value="' . $_key . '"' . $_selected . '>' . $_value . '</option>';
							
						}

					$_markup .= '</select>
					<span class="description">' . __('Add mouse-interaction to control the panorama', $_textDomain) . '</span>
				</p>
				
				<div id="panorama-throw-speed" style="display: ' . $_throwSettings . '; margin-left: 20px">
					
					<p>
						<label>' . __('Drag Speed:', $_textDomain) . '</label>
						<input type="text" class="text-sidebar withlabel panorama-min-max" data-min="300" data-max="975" id="panorama_throw_speed" name="panorama_throw_speed" data-orig="' . $_throwSpeed . '" value="' . $_throwSpeed . '" />
						<span class="description">' . __('Movement speed for the "throw" control option (500-900 recommended)', $_textDomain) . '</span>
					</p>
					
				</div>
				
				<p>
					<label>' . __('Zoom Settings', $_textDomain) . '</label>
					<input type="checkbox" class="tp-moderncheckbox" onchange="document.getElementById(\'panorama-zoom-settings\').style.display=this.checked ? \'block\' : \'none\'" />
					<span class="description panorama-description">' . __('Zoom-In / Zoom-Out Options', $_textDomain) . '</span>
				</p>
				
				<div id="panorama-zoom-settings" style="display: none; margin-left: 20px">
					
					<p>
						<label>' . __('Mousehweel Zoom:', $_textDomain) . '</label>
						<input type="checkbox" class="tp-moderncheckbox withlabel" id="panorama_mousewheel_zoom" name="panorama_mousewheel_zoom"' . $_mousehweelZoom . ' />
						<span class="description panorama-description">' . __('Mouse wheel zooms the picture in and out', $_textDomain) . '</span>
					</p>
					
					<p>
						<label>' . __('Smooth Zoom:', $_textDomain) . '</label>
						<input type="checkbox" class="tp-moderncheckbox withlabel" id="panorama_smooth_zoom" name="panorama_smooth_zoom"' . $_smoothZoom . ' />
						<span class="description panorama-description">' . __('Adds a transition as the zooming occurs', $_textDomain) . '</span>
					</p>
					
					<p>
						<label>' . __('Zoom Min:', $_textDomain) . '</label>
						<input type="text" class="panorama-min-max" id="panorama_zoom_min" name="panorama_zoom_min" value="' . $_zoomMin . '" data-orig="' . $_zoomMin . '" data-min="25" data-max="100" />
						<span class="description">' . __('The minimum zoom percentage (25-100)', $_textDomain) . '</span>
					</p>
					
					<p>
						<label>' . __('Zoom Max:', $_textDomain) . '</label>
						<input type="text" class="text-sidebar withlabel panorama-min-max" id="panorama_zoom_max" name="panorama_zoom_max" value="' . $_zoomMax . '" data-orig="' . $_zoomMax . '" data-min="100" data-max="175" />
						<span class="description">' . __('The maximum zoom percentage (100-175)', $_textDomain) . '</span>
					</p>
				
				</div>
				
				<p>
					<label>' . __('Advanced Settings', $_textDomain) . '</label>
					<input type="checkbox" class="tp-moderncheckbox" onchange="document.getElementById(\'panorama-advanced-settings\').style.display=this.checked ? \'block\' : \'none\'" />
					<span class="description panorama-description">' . __('Advanced 3D Camera/Sphere Options', $_textDomain) . '</span>
				</p>
				
				<div id="panorama-advanced-settings" style="display: none; margin-left: 20px">
					
					<p>
						<label>' . __('Camera Fov', $_textDomain) . '</label>
						<input type="text" class="text-sidebar withlabel" id="panorama_camera_fov" name="panorama_camera_fov" value="' . $_cameraFov . '" />
						<span class="description">' . __('Camera frustum vertical field of view - Recommended: 75', $_textDomain) . '</span>
					</p>
					
					<p>
						<label>' . __('Camera Far', $_textDomain) . '</label>
						<input type="text" class="text-sidebar withlabel" id="panorama_camera_far" name="panorama_camera_far" value="' . $_cameraFar . '" />
						<span class="description">' . __('Camera frustum far plane - Recommended: 1000', $_textDomain) . '</span>
					</p>
					
					<p>
						<label>' . __('Sphere Radius', $_textDomain) . '</label>
						<input type="text" class="text-sidebar withlabel" id="panorama_sphere_radius" name="panorama_sphere_radius" value="' . $_sphereRadius . '" />
						<span class="description">' . __('Sphere Radius - Recommended: 100', $_textDomain) . '</span>
					</p>
					
					<p>
						<label>' . __('Width Segments', $_textDomain) . '</label>
						<input type="text" class="text-sidebar withlabel" id="panorama_sphere_wsegments" name="panorama_sphere_wsegments" value="' . $_sphereWsegments . '" />
						<span class="description">' . __('Number of horizontal segments - Recommended: 100', $_textDomain) . '</span>
					</p>
					
					<p>
						<label>' . __('Height Segments', $_textDomain) . '</label>
						<input type="text" class="text-sidebar withlabel" id="panorama_sphere_hsegments" name="panorama_sphere_hsegments" value="' . $_sphereHsegments . '" />
						<span class="description">' . __('Number of vertical segments - Recommended: 40', $_textDomain) . '</span>
					</p>
				
				</div>
					
			</div>
			
		</div>'; 
		
		static::$_Markup     = $_markup;
		static::$_JavaScript = 'var RsAddonPanorama = true;';
		
	}
}
?>