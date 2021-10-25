<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_FILMSTRIP_PLUGIN_PATH . 'framework/slide.admin.class.php');

class RsFilmstripSlideAdmin extends RsAddonFilmstripSlideAdmin {
	
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
		
		$_defSize      = $_slider->getParam('filmstrip_def_img_size', 'full');
		$_defMobile    = $_slider->getParam('filmstrip_default_mobile', false);
		$_defAlt       = $_slider->getParam('filmstrip_def_alt_type', 'media_library');
		$_defDirection = $_slider->getParam('filmstrip_def_direction', 'right-to-left');
		$_defTimes     = $_slider->getParam('filmstrip_def_times', '30,30,30,30');
		$_sliderType   = $_slider->getParam('source_type', 'gallery');
		$_wpSizes      = RevSliderBase::get_all_image_sizes($_sliderType);
		
		$_enabled      = $_slide->getParam('filmstrip_enabled', false) == 'true' ? ' checked' : '';
		$_mobile       = $_slide->getParam('filmstrip_mobile', $_defMobile) == 'true' ? ' checked' : '';
		$_direction    = $_slide->getParam('filmstrip_direction', $_defDirection);
		$_times        = $_slide->getParam('filmstrip_times', $_defTimes);
		$_data         = $_slide->getParam('filmstrip_settings', ''); 
		
		$_speeds       = explode(',', $_times);
		$_textDomain   = 'rs_' . static::$_Title;
		$_defObjAlt    = $_defAlt === 'media_library' ? 'file_name' : $_defAlt;
		
		$_moves = array(
		
			'right-to-left' => __('Right to Left', $_textDomain),
			'left-to-right' => __('Left to Right', $_textDomain),
			'top-to-bottom' => __('Top to Bottom', $_textDomain),
			'bottom-to-top' => __('Bottom to Top', $_textDomain)
			
		);
		
		$_s1 = isset($_speeds[0]) ? $_speeds[0] : '30';
		$_s2 = isset($_speeds[1]) ? $_speeds[1] : '30';
		$_s3 = isset($_speeds[2]) ? $_speeds[2] : '30';
		$_s4 = isset($_speeds[3]) ? $_speeds[3] : '30';
		
		if($_enabled) {
			
			$_showSettings = 'block';
			$_isEnabled = 'true';
			$_toggleClass = '';
			
		}
		else {
			
			$_showSettings = 'none';
			$_isEnabled = 'false';
			$_toggleClass = ' class="filmstrip-addon-toggle"';
			
		}
		
		$_markup = '<div id="filmstrip-addon-wrap">
			
			<span id="filmstrip-addon-enable" class="rs-layer-toolbar-box">
				
				<i class="rs-mini-layer-icon eg-icon-picture-1 rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Enable/Disable Background Filmstrip', $_textDomain) . '"></i>
				<input type="checkbox" id="filmstrip_enabled" name="filmstrip_enabled" class="tp-moderncheckbox tipsy_enabled_top" original-title="' . __('Enable/Disable Background Filmstrip', $_textDomain) . '"' . $_enabled . ' />
				
			</span>
			
			<div id="filmstrip-main-settings"' . $_toggleClass . '>
				
				<span id="filmstrip-toggle-settings" class="rs-layer-toolbar-box tipsy_enabled_top" original-title="' . __('Show/Hide Item Settings', $_textDomain) . '">
					<i class="eg-icon-down-open"></i>
				
				</span><span class="rs-layer-toolbar-box">
				
					<label>' . __('Move From:', $_textDomain) . '</label> 
					<select id="filmstrip_direction" name="filmstrip_direction">';
							
						foreach($_moves as $_key => $_value) {
							
							$_selected = $_key !== $_direction ? '' : ' selected';
							$_markup .= '<option value="' . $_key . '"' . $_selected . '>' . $_value . '</option>';
							
						}
						
					$_markup .= '</select>
				
				</span><span class="rs-layer-toolbar-box filmstrip-speed-inputs">
				
					<label>' . __('Speeds:', $_textDomain) . '</label> 
		
					<span class="filmstrip-device-icon filmstrip-device-desktop tipsy_enabled_top" original-title="' . __('Desktop Speed in seconds', $_textDomain) . '"></span>
					<input 
					
						type="text" 
						class="filmstrip-speed textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Desktop Movement Speed in seconds', $_textDomain) . '" 
						value="' . $_s1 . '" 
						data-selects="Custom||10s||20s||30s||40s||50s||75s||100s" 
						data-svalues ="30||10||20||30||40||50||75||100" 
						data-icons="wrench||filter||filter||filter||filter||filter||filter||filter" 
						
					/>
					
					<span class="filmstrip-device-icon filmstrip-device-notebook tipsy_enabled_top" original-title="' . __('Notebook Speed in seconds', $_textDomain) . '"></span>	
					<input 
					
						type="text" 
						class="filmstrip-speed textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Notebook Movement Speed in seconds', $_textDomain) . '" 
						value="' . $_s2 . '" 
						data-selects="Custom||10s||20s||30s||40s||50s||75s||100s" 
						data-svalues ="30||10||20||30||40||50||75||100" 
						data-icons="wrench||filter||filter||filter||filter||filter||filter||filter" 
						
					/>
					
					<span class="filmstrip-device-icon filmstrip-device-tablet tipsy_enabled_top" original-title="' . __('Tablet Speed in seconds', $_textDomain) . '"></span>
					<input 
					
						type="text" 
						class="filmstrip-speed textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Tablet Movement Speed in seconds', $_textDomain) . '" 
						value="' . $_s3 . '" 
						data-selects="Custom||10s||20s||30s||40s||50s||75s||100s" 
						data-svalues ="30||10||20||30||40||50||75||100" 
						data-icons="wrench||filter||filter||filter||filter||filter||filter||filter" 
						
					/>
					
					<span class="filmstrip-device-icon filmstrip-device-mobile tipsy_enabled_top" original-title="' . __('Smartphone Speed in seconds', $_textDomain) . '"></span>
					<input 
					
						type="text" 
						class="filmstrip-speed textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Smartphone Movement Speed in seconds', $_textDomain) . '" 
						value="' . $_s4 . '" 
						data-selects="Custom||10s||20s||30s||40s||50s||75s||100s" 
						data-svalues ="30||10||20||30||40||50||75||100" 
						data-icons="wrench||filter||filter||filter||filter||filter||filter||filter" 
						
					/>
					
					<input id="filmstrip_times" type="hidden" name="filmstrip_times" value="' . $_times . '" />
				
				</span><span id="filmstrip-addon-enable" class="rs-layer-toolbar-box">
				
					<label class="tipsy_enabled_top" original-title="' . __('Enable/Disable on Mobile', $_textDomain) . '">' . __('Disable on Mobile:', $_textDomain) . '</label> 
					<input type="checkbox" id="filmstrip_mobile" name="filmstrip_mobile" class="tp-moderncheckbox tipsy_enabled_top" original-title="' . __('Enable/Disable Background Filmstrip', $_textDomain) . '"' . $_mobile . ' />
					
				</span>
				
			</div>
			
			<div id="filmstrip-item-settings">
			
				<div id="filmstrip-wpimage-options" class="filmstrip-item-options filmstrip-divider">
					
					<span class="rs-layer-toolbar-box"><label>' . __('Selected Item\'s Image Size:', $_textDomain) . '</label> 
						<select class="withlabel filmstrip-item-option filmstrip-option-size" data-setting="size">';
					
						foreach($_wpSizes as $_key => $_value) {
							
							$_markup .= '<option value="' . $_key . '">' . $_value . '</option>';
						
						}
					
						$_markup .= '</select>
					
					</span><span class="rs-layer-toolbar-box"><label>' . __('Selected Item\'s Alt Text:', $_textDomain) . '</label> 
						<select class="filmstrip-item-option filmstrip-option-alt" data-setting="alt">
							<option value="media_library">From Media Library</option>
							<option value="file_name">From File Name</option>
							<option value="custom">Custom</option>
						</select>
						<input type="text" class="small-text filmstrip-item-option filmstrip-option-custom" data-setting="custom" value="">
					</span>
					
				</div>
				
				<div id="filmstrip-objlib-options" class="filmstrip-item-options filmstrip-divider">
					
					<span class="rs-layer-toolbar-box"><label>' . __('Selected Item\'s Image Size:', $_textDomain) . '</label> 
						<select class="filmstrip-item-option filmstrip-option-size" data-setting="size">		
							<option value="original">Original</option>
							<option value="large">Large</option>
							<option value="medium">Medium</option>
							<option value="small">Small</option>
							<option value="thumb">Thumbnail</option>
						</select>
					
					</span><span class="rs-layer-toolbar-box"><label>' . __('Selected Item\'s Alt Text:', $_textDomain) . '</label> 
						<select class="filmstrip-item-option filmstrip-option-alt" data-setting="alt">
							<option value="file_name">From File Name</option>
							<option value="custom">Custom</option>
						</select>
						<input type="text" class="small-text filmstrip-item-option filmstrip-option-custom" data-setting="custom" value="">
					</span>
					
				</div>
				
				<div id="filmstrip-external-options" class="filmstrip-item-options filmstrip-divider">
					
					<span class="rs-layer-toolbar-box"><label>' . __('Selected Item\'s Alt Text:', $_textDomain) . '</label> 
						<select class="filmstrip-item-option filmstrip-option-alt" data-setting="alt">
							<option value="file_name">From File Name</option>
							<option value="custom">Custom</option>
						</select>
						<input type="text" class="small-text filmstrip-item-option filmstrip-option-custom" data-setting="custom" value="">
					</span>
					
				</div>
				
			</div>
			
			<div id="filmstrip-addon-content" class="filmstrip-divider" style="display: ' . $_showSettings . '">';
			
				if($_data) {
					
					$_data = stripslashes($_data);
					$_settings = json_decode($_data, true);
					
					if($_settings) {
						
						$_selected = ' filmstrip-item-selected';
						foreach($_settings as $_setting) {
							
							$_wid    = '';
							$_url    = isset($_setting['url'])    ? $_setting['url']    : '';
							$_ids    = isset($_setting['ids'])    ? $_setting['ids']    : '';
							$_type   = isset($_setting['type'])   ? $_setting['type']   : '';
							$_size   = isset($_setting['size'])   ? $_setting['size']   : '';
							$_thumb  = isset($_setting['thumb'])  ? $_setting['thumb']  : '';
							$_alt    = isset($_setting['alt'])    ? $_setting['alt']    : '';
							$_custom = isset($_setting['custom']) ? $_setting['custom'] : '';
							
							// fallback
							if(!$_url || !$_thumb) {
								
								$_url = static::$_Path . 'admin/assets/images/trans_tile.png';
								$_wid = ' width="128"';
								$_thumb = $_url;
								
							}
							
							$_markup .= '<div class="filmstrip-item' . $_selected . '" data-url="' . $_url . '" data-ids="' . $_ids . '" data-type="' . $_type . '" data-size="' . $_size . '"  data-thumb="' . $_thumb . '" data-alt="' . $_alt . '" data-custom="' . $_custom . '">
								<img src="' . $_thumb . '"' . $_wid . ' />
								<span class="filmstrip-item-toolbar">
									<i class="eg-icon-cog filmstrip-edit-settings revblack">
									</i><i class="eg-icon-trash filmstrip-delete-item revred"></i>
								</span>
							</div>';
							$_selected = '';
							
						}
						
					}
					else {
						
						$_data = '';
						
					}
			
				}
				
				$_markup .= '<div id="filmstrip-add-new">
					
					<div class="filmstrip-add-button"><i class="eg-icon-plus"></i></div>
					<div class="filmstrip-add-selections">
						<span id="filmstrip_addon_wpimage" class="tipsy_enabled_top" original-title="' . __('WP Media Library', $_textDomain) . '"> <i class="fa-icon-wordpress"></i></span>
						<span id="filmstrip_addon_objlib" class="tipsy_enabled_top" original-title="' . __('Object Library', $_textDomain) . '"><i class="fa-icon-book"></i></span>
						<span id="filmstrip_addon_external" class="tipsy_enabled_top" original-title="' . __('External URL', $_textDomain) . '"><i class="fa-icon-globe"></i></span>
					</div>
				
				</div>
				
				<div style="clear: left"></div>
			
			</div>
			
			<div id="filmstrip_external_image" title="' . __("Add Image by URL", $_textDomain) . '">
				<div>
					<span>' . __("External Image URL", $_textDomain) . '</span>
					<input id="filmstrip_external_url" type="text" value="" />
				</div>
			</div>
			
			<input id="filmstrip_settings" type="hidden" name="filmstrip_settings" value=\'' . $_data . '\' />
			
		</div>';
		
		static::$_Markup     = $_markup;
		static::$_JavaScript = '
		
			var FilmStripSettings = {
			
				enabled: ' . $_isEnabled . ', 
				size: "' . $_defSize . '", 
				alt: "' . $_defAlt . '", 
				objAlt: "' . $_defObjAlt . '"
				
			};';
		
	}
}
?>