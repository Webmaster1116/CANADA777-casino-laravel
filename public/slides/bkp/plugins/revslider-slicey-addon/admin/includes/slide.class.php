<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_SLICEY_PLUGIN_PATH . 'framework/slide.admin.class.php');

class RsSliceySlideAdmin extends RsAddonSliceySlideAdmin {
	
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
		
		$_def_easing     = $_slider->getParam('slicey_def_easing',     'Linear.easeNone');
		$_def_time       = $_slider->getParam('slicey_def_time',       '7000');
		$_def_scale      = $_slider->getParam('slicey_def_scale',      '150');
		$_def_blur       = $_slider->getParam('slicey_def_blur',       '10');
		$_def_strength   = $_slider->getParam('slicey_def_strength',   '3');
		$_def_color      = $_slider->getParam('slicey_def_color',      'rgba(0, 0, 0, 0.25)');
		$_def_offset     = $_slider->getParam('slicey_def_offset',     '20');
		$_def_width      = $_slider->getParam('slicey_def_width',      '200');
		$_def_height     = $_slider->getParam('slicey_def_height',     '400');
		$_def_blurgstart = $_slider->getParam('slicey_def_blurgstart', '0');
		$_def_blurgend   = $_slider->getParam('slicey_def_blurgend',   '0');
		$_def_blurlstart = $_slider->getParam('slicey_def_blurlstart', 'inherit');
		$_def_blurlend   = $_slider->getParam('slicey_def_blurlend',   'inherit');
		
		$_options = $_slide->getParam('slicey_globals', false);
		$_defaults = array(
				
			'enabled'    => 'false',
			'time'       => $_def_time,
			'easing'     => $_def_easing,
			'scale'      => $_def_scale,
			'blur'       => $_def_blur,
			'strength'   => $_def_strength,
			'color'      => $_def_color,
			'blurgstart' => $_def_blurgstart,
			'blurgend'   => $_def_blurgend
		
		);
		
		if($_options) $_options = json_decode(stripslashes($_options), true);
		else $_options = $_defaults;
		
		$_options = array_merge($_defaults, $_options);
		
		$_time        = RevSliderFunctions::getVal($_options, 'time',       $_def_time);
		$_scale       = RevSliderFunctions::getVal($_options, 'scale',      $_def_scale);
		$_easing      = RevSliderFunctions::getVal($_options, 'easing',     $_def_easing);
		$_blur        = RevSliderFunctions::getVal($_options, 'blur',       $_def_blur);
		$_strength    = RevSliderFunctions::getVal($_options, 'strength',   $_def_strength);
		$_color       = RevSliderFunctions::getVal($_options, 'color',      $_def_color);
		$_blurgstart  = RevSliderFunctions::getVal($_options, 'blurgstart', $_def_blurgstart);
		$_blurgend    = RevSliderFunctions::getVal($_options, 'blurgend',   $_def_blurgend);
		
		$_textDomain = 'rs_' . static::$_Title;
		
		$_markup = '<div id="slicey-addon-wrap">
			
			<div id="slicey-main-settings" class="slicey-global-settings">
				
				<span class="rs-layer-toolbar-box" style="border-left: none;min-width:110px;">
					<span>' . __('Global Settings:', $_textDomain) . '</span> 
				</span>
				
				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon eg-icon-resize-full-2 rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Scale To', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="slicey_scale" 
						id="slicey_scale" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Scale To', $_textDomain) . '" 
						value="' . $_scale . '" 
						data-slicey-value="' . $_scale . '" 
						data-selects="Custom||110||120||130||140||150" 
						data-svalues ="125||110||120||130||140||150" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/> %
				
				<span class="rs-layer-toolbar-space" style="margin-right:15px"></span>		
					
					<i class="rs-mini-layer-icon rs-icon-clock rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Animation Duration', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						id="slicey_time" 
						name="slicey_time" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Animation Duration', $_textDomain) . '" 
						value="' . $_time . '" 
						data-slicey-value="' . $_time . '" 
						data-selects="Custom||1000||2500||5000||7500||9000" 
						data-svalues ="6000||1000||2500||5000||7500||90000" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/> ms
					
				<span class="rs-layer-toolbar-space" style="margin-right:15px"></span>
					
					<i class="rs-mini-layer-icon rs-icon-easing rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Easing', $_textDomain) . '"></i>
					<select value="' . $_easing . '" data-slicey-value="' . $_easing . '" id="slicey_easing" name="slicey_easing" class="rs-layer-input-field tipsy_enabled_top" original-title="' . __('Easing', $_textDomain) . '">
						<option value="Linear.easeNone">Linear.easeNone</option>
						<option value="Power0.easeIn">Power0.easeIn  (linear)</option>
						<option value="Power0.easeInOut">Power0.easeInOut  (linear)</option>
						<option value="Power0.easeOut">Power0.easeOut  (linear)</option>
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
					
				</span>
			</div>

			<div id="slicey-main-blur-settings" class="slicey-global-settings">
				
				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon fa-icon-spinner rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Global Blur', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						id="slicey_blurgstart" 
						name="slicey_blurgstart" 
						style="min-width:70px !important"
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Global Blur Start', $_textDomain) . '" 
						value="' . $_blurgstart . '" 
						data-slicey-value="' . $_blurgstart . '" 
						data-selects="Custom||0||2||4||5||10||12||20" 
						data-svalues ="3||0||2||4||5||10||12||20" 
						data-icons="wrench||filter||filter||filter||filter||filter||filter||filter" 
						
					/>
					
					<span class="rs-layer-toolbar-space" style="margin-right:5px"></span>
					
					<input 
					
						type="text" 
						id="slicey_blurgend" 
						name="slicey_blurgend" 
						style="min-width:70px !important"
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Global Blur End', $_textDomain) . '" 
						value="' . $_blurgend . '" 
						data-slicey-value="' . $_blurgend . '" 
						data-selects="Custom||0||2||4||5||10||12||20" 
						data-svalues ="3||0||2||4||5||10||12||20" 
						data-icons="wrench||filter||filter||filter||filter||filter||filter||filter"  
						
					/>
					
				</span>
			</div>

			<div id="slicey-main-shadow-settings" class="slicey-global-settings">	
							
				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon fa-icon-eyedropper rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Shadow Blur', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="slicey_blur" 
						id="slicey_blur" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Shadow Blur', $_textDomain) . '" 
						value="' . $_blur . '" 
						data-slicey-value="' . $_blur . '" 
						data-selects="Custom||10||20||35||50||75||100" 
						data-svalues ="15||10||20||35||50||75||100" 
						data-icons="wrench||filter||filter||filter||filter||filter||filter" 
						
					/> px
					
					<span class="rs-layer-toolbar-space" style="margin-right:15px"></span>
					
					<i class="rs-mini-layer-icon eg-icon-arrow-combo rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Shadow Strength', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="slicey_strength" 
						id="slicey_strength" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Shadow Strength', $_textDomain) . '" 
						value="' . $_strength . '" 
						data-slicey-value="' . $_strength . '" 
						data-selects="Custom||1||3||5||10||15||20" 
						data-svalues ="2||1||3||5||10||15||20" 
						data-icons="wrench||filter||filter||filter||filter||filter||filter" 
						
					/> px
					
					<span class="rs-layer-toolbar-space" style="margin-right:15px"></span>
					
					<i class="rs-mini-layer-icon rs-icon-color rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Shadow Color', $_textDomain) . '"></i>
					<input id="slicey_color" name="slicey_color" type="hidden" value="' . $_color . '" data-slicey-value="' . $_color . '" />
					
				</span>
				
			</div>
			
			<div id="slicey-layer-settings">
				
				<span class="rs-layer-toolbar-box" style="border-left: none; min-width:110px;">
					<span>' . __('Layer Settings:', $_textDomain) . '</span> 
				</span>
				
				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon rs-icon-zoffset rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Scale Offset', $_textDomain) . '" style="margin-right:12px"></i>
					<input 
					
						type="text" 
						id="slicey_scale_offset" 
						name="slicey_scale_offset" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Scale Offset', $_textDomain) . '" 
						value="10" 
						data-selects="Custom||5||10||15||20||25||50" 
						data-svalues ="2||5||10||15||20||25||50" 
						data-icons="wrench||filter||filter||filter||filter||filter||filter" 
						
					/> %
					
				</span>

				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon fa-icon-spinner rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Blur Start', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						id="slicey_blurlstart" 
						name="slicey_blurlstart" 
						style="min-width:70px !important"
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Blur Start', $_textDomain) . '" 
						value="inherit" 
						data-selects="Inherit||Custom||0||2||4||5||10||12||20" 
						data-svalues ="inherit||3||0||2||4||5||10||12||20" 
						data-icons="export||wrench||filter||filter||filter||filter||filter||filter||filter" 
						
					/>
					
					<span class="rs-layer-toolbar-space" style="margin-right:5px"></span>
					
					<input 
					
						type="text" 
						id="slicey_blurlend" 
						name="slicey_blurlend" 
						style="min-width:70px !important"
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Blur End', $_textDomain) . '" 
						value="inherit" 
						data-selects="Inherit||Custom||0||2||4||5||10||12||20" 
						data-svalues ="inherit||3||0||2||4||5||10||12||20" 
						data-icons="export||wrench||filter||filter||filter||filter||filter||filter||filter" 
						
					/>
					
				</span>
			
			</div>
			
			<div id="slicey_new_layer_dialog" title="' . __("Add New Slicey Layer", $_textDomain) . '">
				
				<div>
					<span>' . __("Width: ", $_textDomain) . '</span>
					<input id="slicey_new_layer_width" type="text" class="ads-input rs-layer-input-field" value="' . $_def_width . '" />
				</div>
				
				<div>
					<span>' . __("Height: ", $_textDomain) . '</span>
					<input id="slicey_new_layer_height" type="text" class="ads-input rs-layer-input-field" value="' . $_def_height . '" />
				</div>
				
				<div>
					<span>' . __("Scale Offset: ", $_textDomain) . '</span>
					<input id="slicey_new_layer_depth" type="text" class="ads-input rs-layer-input-field" value="' . $_def_offset . '" />
				</div>
				
				<div>
					<span>' . __("Blur Start: ", $_textDomain) . '</span>
					<input id="slicey_new_layer_blurlstart" type="text" class="ads-input rs-layer-input-field" value="' . $_def_blurlstart . '" />
				</div>
				
				<div>
					<span>' . __("Blur End: ", $_textDomain) . '</span>
					<input id="slicey_new_layer_blurlend" type="text" class="ads-input rs-layer-input-field" value="' . $_def_blurlend . '" />
				</div>
				
			</div>
			
		</div>';
		
		static::$_Markup     = $_markup;
		static::$_JavaScript = '
		
		var RsAddonSlicey = {
			
			layers: {
					
				"scale_offset": "' . $_def_offset     . '",
				"blurlstart": "'   . $_def_blurlstart . '",
				"blurlend": "'     . $_def_blurlend   . '"
				
			},
			
			globals: {';
			
			$comma = '';
			foreach($_options as $prop => $val) {
				
				static::$_JavaScript .= $comma . '"' . $prop . '":"' . $val . '"';
				$comma = ',';
				
			}
		
			static::$_JavaScript .= '} 
			
		};';
		
	}
}
?>