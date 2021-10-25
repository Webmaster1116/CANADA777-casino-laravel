<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

require_once(RS_FILMSTRIP_PLUGIN_PATH . 'framework/slider.admin.class.php');

class RsFilmstripSliderAdmin extends RsAddonFilmstripSliderAdmin {
	
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
		
		$_enabled      = RevSliderFunctions::getVal($_slider, 'filmstrip_enabled',        false) == 'true' ? ' checked' : '';
		$_defMobile    = RevSliderFunctions::getVal($_slider, 'filmstrip_default_mobile', false) == 'true' ? ' checked' : '';
		$_defDirection = RevSliderFunctions::getVal($_slider, 'filmstrip_def_direction',  'right-to-left');
		$_defTimes     = RevSliderFunctions::getVal($_slider, 'filmstrip_def_times',      '30,30,30,30');
		$_defSize      = RevSliderFunctions::getVal($_slider, 'filmstrip_def_img_size',   'full');
		$_sliderType   = RevSliderFunctions::getVal($_slider, 'source_type',              'gallery');
		
		$_imageSizes   = RevSliderBase::get_all_image_sizes($_sliderType);
		$_showSettings = $_enabled ? 'block' : 'none';
		$_textDomain   = 'rs_' . static::$_Title;
		$_times        = explode(',', $_defTimes);
		$_moves        = array(
		
			'right-to-left' => __('Right to Left', $_textDomain),
			'left-to-right' => __('Left to Right', $_textDomain),
			'top-to-bottom' => __('Top to Bottom', $_textDomain),
			'bottom-to-top' => __('Bottom to Top', $_textDomain)
			
		);
		
		$_s1 = isset($_times[0]) ? $_times[0] : '30';
		$_s2 = isset($_times[1]) ? $_times[1] : '30';
		$_s3 = isset($_times[2]) ? $_times[2] : '30';
		$_s4 = isset($_times[3]) ? $_times[3] : '30';
		
		$_markup = '<div id="filmstrip-addon-settings">
		
			<span class="label" id="label_filmstrip_enabled" origtitle="' . __("Enable/Disable the FilmStrip Add-On<br><br>", $_textDomain) . '">' . __('Enable FilmStrip for Slides', $_textDomain) . '</span>
			<input type="checkbox" class="tp-moderncheckbox withlabel" id="filmstrip_enabled" name="filmstrip_enabled"' . $_enabled . ' 
				onchange="document.getElementById(\'filmstrip-settings\').style.display=this.checked ? \'block\' : \'none\'" />
			
			<div id="filmstrip-settings" style="display: ' . $_showSettings . '; margin-top: 7px">
				
				<h4>Default Settings</h4>
				
				<span class="label" id="label_filmstrip_def_direction" origtitle="' . __("The default motion direction for the FilmStrip<br><br>", $_textDomain) . '">' . __('Direction', $_textDomain) . '</span>
				<select class="withlabel" id="filmstrip_def_direction" name="filmstrip_def_direction">';
				
				foreach($_moves as $_key => $_value) {
							
					$_selected = $_key !== $_defDirection ? '' : ' selected';
					$_markup .= '<option value="' . $_key . '"' . $_selected . '>' . $_value . '</option>';
					
				}
				
				$_markup .= '</select><br>
				
				<span class="label" id="label_filmstrip_def_img_size" origtitle="' . __("The default image size to use for WP Media Library images.<br><br>", $_textDomain) . '">' . __('Image Size', $_textDomain) . '</span>
				<select class="withlabel" id="filmstrip_def_img_size" name="filmstrip_def_img_size">';
				
				foreach($_imageSizes as $_key => $_value) {
					
					$_selected = $_key !== $_defSize ? '' : ' selected';
					$_markup .= '<option value="' . $_key . '"' . $_selected . '>' . ucwords(preg_replace('/\-|_/', ' ', $_value)) . '</option>';
				
				}
				
				$_markup .= '</select><br>
				
				<span class="label" id="label_filmstrip_desktop_time" origtitle="' . __("The default desktop speed in seconds<br><br>", $_textDomain) . '">' . __('Desktop Speed', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel filmstrip-def-time" id="filmstrip_desktop_time" value="' . $_s1 . '" />
				<br>
				
				<span class="label" id="label_filmstrip_notebook_time" origtitle="' . __("The default notebook speed in seconds<br><br>", $_textDomain) . '">' . __('Notebook Speed', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel filmstrip-def-time" id="filmstrip_notebook_time" value="' . $_s2 . '" />
				<br>
				
				<span class="label" id="label_filmstrip_tablet_time" origtitle="' . __("The default tablet speed in seconds<br><br>", $_textDomain) . '">' . __('Notebook Speed', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel filmstrip-def-time" id="filmstrip_tablet_time" value="' . $_s3 . '" />
				<br>
				
				<span class="label" id="label_filmstrip_smartphone_time" origtitle="' . __("The default smartphone speed in seconds<br><br>", $_textDomain) . '">' . __('Smartphone Speed', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel filmstrip-def-time" id="filmstrip_smartphone_time" value="' . $_s4 . '" />
				<br>
				
				<span class="label" id="label_filmstrip_default_mobile" origtitle="' . __("Disable the FilmStrip Add-On by default on mobile devices.<br><br>", $_textDomain) . '">' . __('Disable on Mobile', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="filmstrip_default_mobile" name="filmstrip_default_mobile"' . $_defMobile . ' />
				
				<input id="filmstrip_def_times" type="hidden" name="filmstrip_def_times" value="' . $_defTimes . '" />
					
			</div>
			
		</div>';
		
		static::$_Markup = $_markup;
		static::$_Icon = 'eg-icon-picture-1';
		static::$_JavaScript = '
		
			jQuery(function() {
				
				var speeds,
					times,
					timings = jQuery("#filmstrip_def_times");
				
				function timeEach(i) {
					
					var $this = jQuery(this),
						val = $this.val();
					
					if(!val || val === "0") {
						val = "30";
						$this.val("30");
					}
					
					if(i !== 0) speeds += ",";
					speeds += parseInt(val, 10) || "30";
					
				}
				
				times = jQuery(".filmstrip-def-time").change(function() {
					
					speeds = "";
					times.each(timeEach);
					timings.val(speeds);
					
				});
				
			});';
		
		
	}
	
	
	public function export_slider($data, $slides, $sliderParams, $useDummy) {
		
		foreach($slides as $slide) {
			
			$images = (isset($slide['params']) && isset($slide['params']['filmstrip_settings'])) ? $slide['params']['filmstrip_settings'] : '';
			if(!empty($images)) {
				
				$images = stripslashes($images);
				$settings = json_decode($images, true);
				if(!empty($settings)) {
					
					foreach($settings as $setting) {
						
						if(isset($setting['url']) && $setting['url'] != '') $data['usedImages'][$setting['url']] = true;
						if(isset($setting['thumb']) && $setting['thumb'] != '') $data['usedImages'][$setting['thumb']] = true;
						
					}
					
				}
				
			}
			
		}
		
		return $data;
		
	}
	
	public function import_slider($data, $slide_type, $image_path) {
		
		if(isset($data['params']) && isset($data['params']['filmstrip_settings'])){
			
			$images = (isset($data['params']) && isset($data['params']['filmstrip_settings'])) ? $data['params']['filmstrip_settings'] : '';
			$images = stripslashes($images);
			$settings = json_decode($images, true);
			
			if(!empty($settings)) {
				
				foreach($settings as $key => $setting) {
					
					if(isset($setting['url'])){
						
						$url = RevSliderBase::check_file_in_zip($image_path, $settings[$key]['url'], $data['sliderParams']['alias'], $data['alreadyImported']);
						$url = RevSliderFunctionsWP::getImageUrlFromPath($url);
						
						$settings[$key]['url'] = $url;
						$settings[$key]['ids'] = RevSliderFunctionsWP::get_image_id_by_url($url);
					}
					
					if(isset($setting['thumb'])){
						
						$thumb = RevSliderBase::check_file_in_zip($image_path, $settings[$key]['thumb'], $data['sliderParams']['alias'], $data['alreadyImported']);
						$settings[$key]['thumb'] = RevSliderFunctionsWP::getImageUrlFromPath($thumb);
						
					}
				}
				
				$data['params']['filmstrip_settings'] = json_encode($settings);
			}
		}

		return $data;
		
	}
	
}
?>