<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_BEFOREAFTER_PLUGIN_PATH . 'framework/slide.layers.admin.class.php');

class RsBeforeAfterSlideLayersAdmin extends RsAddonBeforeAfterSlideLayersAdmin {
	
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
		
		$_def_moveto = $_slider->getParam('beforeafter_def_moveto', '50%|50%|50%|50%');
		$_moveto = $_slide->getParam('beforeafter_moveto', $_def_moveto);
		
		$_notebook_view = $_slider->getParam('enable_custom_size_notebook', 'off') === 'on' ? 'inline-block' : 'none';
		$_tablet_view   = $_slider->getParam('enable_custom_size_tablet',   'off') === 'on' ? 'inline-block' : 'none';
		$_phone_view    = $_slider->getParam('enable_custom_size_iphone',   'off') === 'on' ? 'inline-block' : 'none';
		
		$_viewports  = array('Desktop' => 'inline-block', 'Notebook' => $_notebook_view, 'Tablet' => $_tablet_view, 'Phone' => $_phone_view);
		$_views      = array('all', 'before', 'after');
		$_moves      = explode('|', $_moveto);
		$_textDomain = 'rs_' . static::$_Title;
		
		$_markup = '<div id="beforeafter-addon-wrap">
			
			<div id="beforeafter-global-settings" class="before-after-section beforeafter-globals">
				
				<span class="rs-layer-toolbar-box before-after-vertical" style="border-left: none; min-width:110px">
					<span>' . __('Global Settings:', $_textDomain) . '</span> 
				</span>
				
				<span class="rs-layer-toolbar-box before-after-vertical">';
					
						$_i = 0;
						foreach($_viewports as $_key => $_val) {
							
							$_value = isset($_moves[$_i]) ? $_moves[$_i] : '50%';
							$_markup .= '<span style="display: ' . $_val . '">
								
								<i class="rs-mini-layer-icon beforeafter-device-' . strtolower($_key) . ' rs-toolbar-icon tipsy_enabled_top" original-title="' . $_key . __(' Reveal Point', $_textDomain) . '"></i>
								<input 
								
									type="text" 
									id="beforeafter_moveto_' . $_i . '" 
									class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top beforeafter-moveto beforeafter-pos" 
									original-title="' . $_key . __(' Reveal Point', $_textDomain) . '" 
									value="' . $_value . '" 
									data-beforeafter-value="' . $_value . '" 
									data-selects="Custom||25%||50%||75%||100%" 
									data-svalues ="500px||25%||50%||75%||100%" 
									data-icons="wrench||filter||filter||filter||filter" 
									
								/></span>';
							$_i++;
							
						}
					
					$_markup .= '<input id="beforeafter_moveto" type="hidden" name="beforeafter_moveto" data-beforeafter-value="' . $_moveto . '" value="' . $_moveto . '" />
				</span>

			</div>
			
			<div id="beforeafter-view-settings" class="before-after-section before-after-row">
			
				<span class="rs-layer-toolbar-box before-after-vertical" style="border-left: none; min-width:110px">
					<span>' . __('Admin BG View:', $_textDomain) . '</span> 
				</span>
				
				<span class="rs-layer-toolbar-box before-after-vertical">
					
					<i id="beforeafter-view-icon" class="rs-mini-layer-icon fa-icon-adjust rs-toolbar-icon tipsy_enabled_top beforeafter-icon" original-title="' . __('Toggle Before/After Slide BG', $_textDomain) . '"></i>
					<select value="" id="beforeafter_bg_view" class="rs-layer-input-field tipsy_enabled_top" original-title="' . __('Toggle Before/After Slide BG', $_textDomain) . '">
						<option value="before"> ' . __('Before', $_textDomain) . '</option>
						<option value="after"> ' . __('After', $_textDomain) . '</option>
					</select>
					
				</span>
			
			</div>

			<div id="beforeafter-layer-settings" class="before-after-section before-after-row">
				
				<span class="rs-layer-toolbar-box before-after-vertical" style="border-left: none; min-width:110px">
					<span>' . __('Layer Settings:', $_textDomain) . '</span> 
				</span>
				
				<span class="rs-layer-toolbar-box before-after-vertical">
					
					<i id="beforeafter-icon" class="rs-mini-layer-icon fa-icon-adjust rs-toolbar-icon tipsy_enabled_top beforeafter-icon" original-title="' . __('Before/After Position for the Selected Layer', $_textDomain) . '"></i>
					<select value="" id="beforeafter_position" name="beforeafter_position" class="rs-layer-input-field tipsy_enabled_top" original-title="' . __('Before/After Position for the Selected Layer', $_textDomain) . '">
						<option value="before"> ' . __('Before', $_textDomain) . '</option>
						<option value="after"> ' . __('After', $_textDomain) . '</option>
					</select>
					
				</span>
				
			</div>
			
		</div>';
		
		static::$_Markup     = $_markup;
		static::$_JavaScript = '
		
		var RsAddonBeforeAfter = {
			
			lang: {
				"before": "' . __('Show BEFORE Layers', $_textDomain) . '", 
				"after": "' . __('Show AFTER Layers', $_textDomain) . '",
				"all": "' . __('Show ALL Layers', $_textDomain) . '"
			},
			layers: {"position": "before"},
			globals: {"moveto": "' . $_moveto . '"}
			
		};';
		
	}
}