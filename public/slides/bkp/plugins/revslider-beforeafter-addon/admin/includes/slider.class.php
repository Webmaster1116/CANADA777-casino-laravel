<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

require_once(RS_BEFOREAFTER_PLUGIN_PATH . 'framework/slider.admin.class.php');

class RsBeforeAfterSliderAdmin extends RsAddonBeforeAfterSliderAdmin {
	
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
		
		$_enabled    = RevSliderFunctions::getVal($_slider, 'beforeafter_enabled',        false) == 'true' ? ' checked' : '';
		$_moveTo     = RevSliderFunctions::getVal($_slider, 'beforeafter_def_moveto',     '50%|50%|50%|50%');
		$_direction  = RevSliderFunctions::getVal($_slider, 'beforeafter_def_direction',  'horizontal');
		$_time       = RevSliderFunctions::getVal($_slider, 'beforeafter_def_time',       '750');
		$_delay      = RevSliderFunctions::getVal($_slider, 'beforeafter_def_delay',      '500');
		$_easing     = RevSliderFunctions::getVal($_slider, 'beforeafter_def_easing',     'Power2.easeInOut');
		$_animateOut = RevSliderFunctions::getVal($_slider, 'beforeafter_def_animateout', 'fade');
		
		$_arrowLeft   = RevSliderFunctions::getVal($_slider, 'beforeafter_left_arrow',   'fa-icon-caret-left');
		$_arrowRight  = RevSliderFunctions::getVal($_slider, 'beforeafter_right_arrow',  'fa-icon-caret-right');
		$_arrowTop    = RevSliderFunctions::getVal($_slider, 'beforeafter_top_arrow',    'fa-icon-caret-up');
		$_arrowBottom = RevSliderFunctions::getVal($_slider, 'beforeafter_bottom_arrow', 'fa-icon-caret-down');
		
		$_arrowColor   = RevSliderFunctions::getVal($_slider, 'beforeafter_arrow_color',    '#ffffff');
		$_arrowSize    = RevSliderFunctions::getVal($_slider, 'beforeafter_arrow_size',     '32');
		$_arrowSpacing = RevSliderFunctions::getVal($_slider, 'beforeafter_arrow_spacing',  '5');
		$_arrowPadding = RevSliderFunctions::getVal($_slider, 'beforeafter_arrow_padding',  '0');
		$_arrowRadius  = RevSliderFunctions::getVal($_slider, 'beforeafter_arrow_radius',   '0');
		$_arrowBgColor = RevSliderFunctions::getVal($_slider, 'beforeafter_arrow_bg_color', 'transparent');
		
		$_arrowShadow      = RevSliderFunctions::getVal($_slider, 'beforeafter_arrow_shadow',       false) == 'true' ? ' checked' : '';
		$_arrowShadowBlur  = RevSliderFunctions::getVal($_slider, 'beforeafter_arrow_shadow_blur',  '10');
		$_arrowShadowColor = RevSliderFunctions::getVal($_slider, 'beforeafter_arrow_shadow_color', 'rgba(0, 0, 0, 0.35)');
		
		$_arrowBorder      = RevSliderFunctions::getVal($_slider, 'beforeafter_arrow_border',       false) == 'true' ? ' checked' : '';
		$_arrowBorderSize  = RevSliderFunctions::getVal($_slider, 'beforeafter_arrow_border_size',  '1');
		$_arrowBorderColor = RevSliderFunctions::getVal($_slider, 'beforeafter_arrow_border_color', '#000000');
		
		$_boxShadow         = RevSliderFunctions::getVal($_slider, 'beforeafter_box_shadow',           false) == 'true' ? ' checked' : '';
		$_boxShadowBlur     = RevSliderFunctions::getVal($_slider, 'beforeafter_box_shadow_blur',     '10');
		$_boxShadowStrength = RevSliderFunctions::getVal($_slider, 'beforeafter_box_shadow_strength', '3');
		$_boxShadowColor    = RevSliderFunctions::getVal($_slider, 'beforeafter_box_shadow_color',    'rgba(0, 0, 0, 0.35)');
		
		$_dividerSize           = RevSliderFunctions::getVal($_slider, 'beforeafter_divider_size',            '1');
		$_dividerColor          = RevSliderFunctions::getVal($_slider, 'beforeafter_divider_color',           '#ffffff');
		$_dividerShadow         = RevSliderFunctions::getVal($_slider, 'beforeafter_divider_shadow',          false) == 'true' ? ' checked' : '';
		$_dividerShadowBlur     = RevSliderFunctions::getVal($_slider, 'beforeafter_divider_shadow_blur',     '10');
		$_dividerShadowStrength = RevSliderFunctions::getVal($_slider, 'beforeafter_divider_shadow_strength', '3');
		$_dividerShadowColor    = RevSliderFunctions::getVal($_slider, 'beforeafter_divider_shadow_color',    'rgba(0, 0, 0, 0.35)');
		
		$_onClick     = RevSliderFunctions::getVal($_slider, 'beforeafter_onclick',      true) == 'true' ? ' checked' : '';
		$_clickTime   = RevSliderFunctions::getVal($_slider, 'beforeafter_click_time',   '500');
		$_clickEasing = RevSliderFunctions::getVal($_slider, 'beforeafter_click_easing', 'Power2.easeOut');
		$_cursor      = RevSliderFunctions::getVal($_slider, 'beforeafter_cursor',       'pointer');
		
		$_bounceArrows = RevSliderFunctions::getVal($_slider, 'beforeafter_def_bouncearrows', 'none');
		$_bounceAmount = RevSliderFunctions::getVal($_slider, 'beforeafter_def_bounceamount', '5');
		$_bounceSpeed  = RevSliderFunctions::getVal($_slider, 'beforeafter_def_bouncespeed',  '1500');
		$_bounceType   = RevSliderFunctions::getVal($_slider, 'beforeafter_def_bouncetype',   'repel');
		$_bounceEasing = RevSliderFunctions::getVal($_slider, 'beforeafter_def_bounceeasing', 'ease-in-out');
		$_bounceDelay  = RevSliderFunctions::getVal($_slider, 'beforeafter_def_bouncedelay',  '0');
		
		$_shiftArrows = RevSliderFunctions::getVal($_slider, 'beforeafter_def_shiftarrows', false) == 'true' ? ' checked' : '';
		$_shiftOffset = RevSliderFunctions::getVal($_slider, 'beforeafter_def_shiftoffset', '10');
		$_shiftTiming = RevSliderFunctions::getVal($_slider, 'beforeafter_def_shifttiming', '300');
		$_shiftEasing = RevSliderFunctions::getVal($_slider, 'beforeafter_def_shifteasing', 'ease');
		$_shiftDelay  = RevSliderFunctions::getVal($_slider, 'beforeafter_def_shiftdelay',  '0');
		
		$_textDomain  = 'rs_' . static::$_Title;
		$_outs        = array('fade', 'collapse');
		$_bounceTypes = array('repel', 'attract');
		$_viewports   = array('Desktop', 'Notebook', 'Tablet', 'Phone');
		$_eases       = array('linear', 'ease', 'ease-out', 'ease-in', 'ease-in-out');
		$_bounces     = array('none' => 'None', 'initial' => 'On Initial Reveal', 'infinite' => 'Infinite Loop', 'once' => 'Until First Grab');
		$_cursors     = array('pointer', 'default', 'none', 'cell', 'crosshair', 'move', 'all-scroll', 'col-resize', 'row-resize', 'ew-resize', 'ns-resize');
		$_moves       = explode('|', $_moveTo);
		
		$_showSettings        = !empty($_enabled)         ? 'block' : 'none';
		$_clickActive         = !empty($_onClick)         ? 'block' : 'none';
		$_arrowShadowActive   = !empty($_arrowShadow)     ? 'block' : 'none';
		$_dividerShadowActive = !empty($_dividerShadow)   ? 'block' : 'none';
		$_arrowBorderActive   = !empty($_arrowBorder)     ? 'block' : 'none';
		$_boxShadowActive     = !empty($_boxShadow)       ? 'block' : 'none';
		$_shiftOptions        = !empty($_shiftArrows)     ? 'block' : 'none';
		$_bounceOptions       = $_bounceArrows !== 'none' ? 'block' : 'none';
		
		$_icons = array(
		
			'fa-icon-chevron-left', 
			'fa-icon-chevron-right', 
			'fa-icon-caret-left', 
			'fa-icon-caret-right', 
			'fa-icon-arrow-left', 
			'fa-icon-arrow-right', 
			'fa-icon-backward', 
			'fa-icon-forward', 
			'fa-icon-angle-double-left', 
			'fa-icon-angle-double-right', 
			'fa-icon-angle-double-up', 
			'fa-icon-angle-double-down', 
			'fa-icon-angle-left', 
			'fa-icon-angle-right', 
			'fa-icon-angle-up', 
			'fa-icon-angle-down', 
			'fa-icon-long-arrow-left', 
			'fa-icon-long-arrow-right', 
			'fa-icon-long-arrow-up', 
			'fa-icon-long-arrow-down', 
			'fa-icon-arrow-up', 
			'fa-icon-arrow-down', 
			'fa-icon-caret-up', 
			'fa-icon-caret-down'
		
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
		
		$_markup = '<div id="beforeafter-addon-settings">
		
			<span class="label" id="label_beforeafter_enabled" origtitle="' . __("Enable/Disable the Before/After Add-On for the Slider.<br><br>", $_textDomain) . '">' . __('Enable Addon for this Slider', $_textDomain) . '</span> 
			<input type="checkbox" class="tp-moderncheckbox withlabel" id="beforeafter_enabled" name="beforeafter_enabled"' . $_enabled . ' onchange="document.getElementById(\'beforeafter-settings\').style.display=this.checked ? \'block\' : \'none\'" />
			
			<div id="beforeafter-settings" style="display: ' . $_showSettings . '">
				
				<br>
				<ul class="main-options-small-tabs" style="display:inline-block">
					<li id="beforeafter_1" data-content="#beforeafter-arrow-styles" class="selected">Arrows</li>
					<li id="beforeafter_2" data-content="#beforeafter-drag-container">Drag Container</li>
					<li id="beforeafter_3" data-content="#beforeafter-line-styles">Divider Line</li>
					<li id="beforeafter_4" data-content="#beforeafter-defaults">Defaults</li>
					<li id="beforeafter_5" data-content="#beforeafter-misc">Misc.</li>
				</ul>
				
				<div id="beforeafter-arrow-styles">
					
					<span class="label" id="label_beforeafter_left_arrow" origtitle="' . __("Select arrows for horizontal direction mode<br><br>", $_textDomain) . '">' . __('Horizontal Arrows', $_textDomain) . '</span>
					<input type="hidden" id="beforeafter_left_arrow" name="beforeafter_left_arrow" value="' . $_arrowLeft . '" />
					<input type="hidden" id="beforeafter_right_arrow" name="beforeafter_right_arrow" value="' . $_arrowRight . '" />
					<span class="before-after-icon-option beforeafter-icon" data-arrow="left" data-icon="' . $_arrowLeft . '"><i id="before-after-icon-left" class="' . $_arrowLeft . '"></i></span
					><span class="before-after-icon-option beforeafter-icon" data-arrow="right" data-icon="' . $_arrowRight . '"><i id="before-after-icon-right" class="' . $_arrowRight . '"></i></span>
					<br>
					
					<span class="label" id="label_beforeafter_top_arrow" origtitle="' . __("Select arrows for vertical direction mode<br><br>", $_textDomain) . '">' . __('Vertical Arrows', $_textDomain) . '</span>
					<input type="hidden" id="beforeafter_top_arrow" name="beforeafter_top_arrow" value="' . $_arrowTop . '" />
					<input type="hidden" id="beforeafter_bottom_arrow" name="beforeafter_bottom_arrow" value="' . $_arrowBottom . '" />
					<span class="before-after-icon-option beforeafter-icon" data-arrow="top" data-icon="' . $_arrowTop . '"><i id="before-after-icon-top" class="' . $_arrowTop . '"></i></span
					><span class="before-after-icon-option beforeafter-icon" data-arrow="bottom" data-icon="' . $_arrowBottom . '"><i id="before-after-icon-bottom" class="' . $_arrowBottom . '"></i></span>
					<br>
					
					<span class="label" id="label_beforeafter_arrow_size" origtitle="' . __("The CSS font-size for the arrow icons<br><br>", $_textDomain) . '">' . __('Icon Size', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_arrowSize . '" data-min="0" data-max="128" id="beforeafter_arrow_size" name="beforeafter_arrow_size" value="' . $_arrowSize . '" /> px
					<br>
					
					<span class="label" id="label_beforeafter_arrow_spacing" origtitle="' . __("Spacing in pixels between the arrow icons<br><br>", $_textDomain) . '">' . __('Icon Spacing', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_arrowSpacing . '" data-min="-50" data-max="500" id="beforeafter_arrow_spacing" name="beforeafter_arrow_spacing" value="' . $_arrowSpacing . '" /> px
					<br>
					
					<span class="label" id="label_beforeafter_arrow_color" origtitle="' . __("Select a color for the arrow icons<br><br>", $_textDomain) . '">' . __('Icon Color', $_textDomain) . '</span>
					<input type="hidden" name="beforeafter_arrow_color" class="rs-layer-input-field before-after-color" data-editing="Arrows Icon Color" value="' . $_arrowColor . '" />
					<br>
					
					<span class="label" id="label_beforeafter_arrow_shadow" origtitle="' . __("Apply a CSS text-shadow to the arrow icons<br><br>", $_textDomain) . '">' . __('Icon Shadow', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="beforeafter_arrow_shadow" name="beforeafter_arrow_shadow"' . $_arrowShadow . ' 
					onchange="document.getElementById(\'beforeafter-arrow-shadow-settings\').style.display=this.checked ? \'block\' : \'none\'" />
					<br>
					
					<div id="beforeafter-arrow-shadow-settings" class="withsublabels" style="display: ' . $_arrowShadowActive . '">
					
						<span class="label" id="label_beforeafter_arrow_shadow_blur" origtitle="' . __("The blur strength for the CSS text-shadow for the arrow icons.<br><br>", $_textDomain) . '">' . __('Shadow Blur', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_arrowShadowBlur . '" data-min="0" data-max="999" id="beforeafter_arrow_shadow_blur" name="beforeafter_arrow_shadow_blur" value="' . $_arrowShadowBlur . '" /> px
						<br>
						
						<span class="label" id="label_beforeafter_arrow_shadow_color" origtitle="' . __("CSS text-shadow color for the arrow icons<br><br>", $_textDomain) . '">' . __('Shadow Color', $_textDomain) . '</span>
						<input type="hidden" name="beforeafter_arrow_shadow_color" class="rs-layer-input-field before-after-color" data-editing="Icon Shadow Color" value="' . $_arrowShadowColor . '" />
						<br>
						
					</div>
					
				</div>
				
				<div id="beforeafter-drag-container" style="display: none">
				
					<span class="label" id="label_beforeafter_arrow_padding" origtitle="' . __("Padding in pixels for the arrow icons container<br><br>", $_textDomain) . '">' . __('Padding', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_arrowPadding . '" data-min="0" data-max="500" id="beforeafter_arrow_padding" name="beforeafter_arrow_padding" value="' . $_arrowPadding . '" /> px
					<br>
					
					<span class="label" id="label_beforeafter_arrow_radius" origtitle="' . __("CSS border-radius for the arrows container.  Accepts percetages or pixels.<br><br>", $_textDomain) . '">' . __('Border Radius', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="beforeafter_arrow_radius" name="beforeafter_arrow_radius" value="' . $_arrowRadius . '" />
					<br>
					
					<span class="label" id="label_beforeafter_arrow_bg_color" origtitle="' . __("Background color for the arrow icons container<br><br>", $_textDomain) . '">' . __('BG Color', $_textDomain) . '</span>
					<input type="hidden" name="beforeafter_arrow_bg_color" class="rs-layer-input-field before-after-color" data-editing="Arrows BG Color" value="' . $_arrowBgColor . '" />
					<br>
					
					<span class="label" id="label_beforeafter_arrow_border" origtitle="' . __("Enable a CSS border for the arrow icons container<br><br>", $_textDomain) . '">' . __('Border', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="beforeafter_arrow_border" name="beforeafter_arrow_border"' . $_arrowBorder . ' onchange="document.getElementById(\'beforeafter-arrow-border\').style.display=this.checked ? \'block\' : \'none\'" />
					<br>
					
					<div id="beforeafter-arrow-border" class="withsublabels" style="display: ' . $_arrowBorderActive . '">
					
						<span class="label" id="label_beforeafter_arrow_border_size" origtitle="' . __("CSS border-width for the arrows icon container<br><br>", $_textDomain) . '">' . __('Border Size', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel beforeafter-min-max"  data-default-value="' . $_arrowBorderSize . '"data-min="0" data-max="250" id="beforeafter_arrow_border_size" name="beforeafter_arrow_border_size" value="' . $_arrowBorderSize . '" /> px
						<br>
						
						<span class="label" id="label_beforeafter_arrow_border_color" origtitle="' . __("border-color for the arrows icon container<br><br>", $_textDomain) . '">' . __('Border Color', $_textDomain) . '</span>
						<input type="hidden" name="beforeafter_arrow_border_color" class="rs-layer-input-field before-after-color" data-editing="Arrows Border Color" value="' . $_arrowBorderColor . '" />
						<br>
						
					</div>
					
					<span class="label" id="label_beforeafter_box_shadow" origtitle="' . __("Enable a CSS box-shadow for the arrow icons container.<br><br>", $_textDomain) . '">' . __('Box Shadow', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="beforeafter_box_shadow" name="beforeafter_box_shadow"' . $_boxShadow . ' onchange="document.getElementById(\'beforeafter-arrow-box-shadow\').style.display=this.checked ? \'block\' : \'none\'" />
					<br>
					
					<div id="beforeafter-arrow-box-shadow" class="withsublabels" style="display: ' . $_boxShadowActive . '">
					
						<span class="label" id="label_beforeafter_box_shadow_blur" origtitle="' . __("The box-shadow blur spread for the arrow icons container.<br><br>", $_textDomain) . '">' . __('Shadow Blur', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel beforeafter-min-max"  data-default-value="' . $_boxShadowBlur . '"data-min="0" data-max="999" id="beforeafter_box_shadow_blur" name="beforeafter_box_shadow_blur" value="' . $_boxShadowBlur . '" /> px
						<br>
						
						<span class="label" id="label_beforeafter_box_shadow_strength" origtitle="' . __("The box-shadow blur strength for the arrow icons container.<br><br>", $_textDomain) . '">' . __('Shadow Strength', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_boxShadowStrength . '" data-min="0" data-max="999" id="beforeafter_box_shadow_strength" name="beforeafter_box_shadow_strength" value="' . $_boxShadowStrength . '" /> px
						<br>
						
						<span class="label" id="label_beforeafter_box_shadow_color" origtitle="' . __("The box-shadow color for the arrow icons container<br><br>", $_textDomain) . '">' . __('Shadow Color', $_textDomain) . '</span>
						<input type="hidden" name="beforeafter_box_shadow_color" class="rs-layer-input-field before-after-color" data-editing="Arrows Box Shadow Color" value="' . $_boxShadowColor . '" />
						<br>
						
					</div>
				
				</div>
				
				<div id="beforeafter-line-styles" style="display: none">
					
					<span class="label" id="label_beforeafter_divider_size" origtitle="' . __("The size of the divider line in pixels.  For no line enter the number 0.<br><br>", $_textDomain) . '">' . __('Line Size', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_dividerSize . '" data-min="0" data-max="500" id="beforeafter_divider_size" name="beforeafter_divider_size" value="' . $_dividerSize . '" /> px
					<br>
					
					<span class="label" id="label_beforeafter_divider_color" origtitle="' . __("The color for the divider line<br><br>", $_textDomain) . '">' . __('Line Color', $_textDomain) . '</span>
					<input type="hidden" id="beforeafter_divider_color" name="beforeafter_divider_color" class="rs-layer-input-field before-after-color" data-editing="Divider Color" value="' . $_dividerColor . '" />
					<br>
					
					<span class="label" id="label_beforeafter_shadow" origtitle="' . __("Enable a CSS box-shadow for the divider line<br><br>", $_textDomain) . '">' . __('Line Shadow', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="beforeafter_shadow" name="beforeafter_divider_shadow"' . $_dividerShadow . ' 
					onchange="document.getElementById(\'beforeafter-line-shadow-settings\').style.display=this.checked ? \'block\' : \'none\'" />
					<br>
					
					<div id="beforeafter-line-shadow-settings" class="withsublabels" style="display: ' . $_dividerShadowActive . '">
					
						<span class="label" id="label_beforeafter_divider_shadow_blur" origtitle="' . __("The box-shadow blur spread for the divider line<br><br>", $_textDomain) . '">' . __('Shadow Blur', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_dividerShadowBlur . '" data-min="0" data-max="999" id="beforeafter_divider_shadow_blur" name="beforeafter_divider_shadow_blur" value="' . $_dividerShadowBlur . '" /> px
						<br>
						
						<span class="label" id="label_beforeafter_divider_shadow_strength" origtitle="' . __("The box-shadow blur strength for the divider line.<br><br>", $_textDomain) . '">' . __('Shadow Strength', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_dividerShadowStrength . '" data-min="0" data-max="999" id="beforeafter_divider_shadow_strength" name="beforeafter_divider_shadow_strength" value="' . $_dividerShadowStrength . '" /> px
						<br>
						
						<span class="label" id="label_beforeafter_divider_shadow_color" origtitle="' . __("The box-shadow color for the divider line<br><br>", $_textDomain) . '">' . __('Shadow Color', $_textDomain) . '</span>
						<input type="hidden" id="beforeafter_divider_shadow_color" name="beforeafter_divider_shadow_color" class="rs-layer-input-field before-after-color" data-editing="Line Shadow Color" value="' . $_dividerShadowColor . '" />
						<br>
						
					</div>
					
				</div>
				
				<div id="beforeafter-defaults" style="display: none">
					
					<span class="label" id="label_beforeafter_def_direction" origtitle="' . __("Select which way the before/after should reveal<br><br>", $_textDomain) . '">' . __('Reveal Direction', $_textDomain) . '</span>
					<select id="beforeafter_def_direction" class="withlabel" name="beforeafter_def_direction" value="' . $_direction . '">
						<option value="horizontal">' . __('Horizontal', $_textDomain) . '</option>
						<option value="vertical">' . __('Vertical', $_textDomain) . '</option>
					</select>
					<br>';
						
					$_i = 0;
					foreach($_viewports as $_viewport) {
						
						$_value = isset($_moves[$_i]) ? $_moves[$_i] : '50%';
						$_markup .= '
					
						<span class="label" id="label_beforeafter_moveto_' . $_i . '" origtitle="' . __("Accepts both pixels and percentages<br><br>", $_textDomain) . '">' . $_viewport . __(' Reveal Point', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel beforeafter-def-moveto" id="beforeafter_moveto_' . $_i . '" value="' . $_value . '" />
						<br>';
						$_i++;
							
					}
				
					$_markup .= '<input id="beforeafter_def_moveto" type="hidden" name="beforeafter_def_moveto" value="' . $_moveTo . '" />
					
					<span class="label" id="label_beforeafter_def_delay" origtitle="' . __("An optional delay in milliseconds before the reveal occurs.<br><br>", $_textDomain) . '">' . __('Reveal Start Delay', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_delay . '" data-min="0" data-max="10000" id="beforeafter_def_delay" name="beforeafter_def_delay" value="' . $_delay . '" /> ms
					<br>
					
					<span class="label" id="label_beforeafter_def_time" origtitle="' . __("The duration of the animation as it occurs (in milliseconds).<br><br>", $_textDomain) . '">' . __('Animation Duration', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_time . '" data-min="100" data-max="10000" id="beforeafter_def_time" name="beforeafter_def_time" value="' . $_time . '" /> ms
					<br>
					
					<span class="label" id="label_beforeafter_def_easing" origtitle="' . __("The animation timing equation<br><br>", $_textDomain) . '">' . __('Animation Easing', $_textDomain) . '</span>
					<select value="' . $_easing . '" id="beforeafter_def_easing" name="beforeafter_def_easing" class="withlabel">';
						
						foreach($_easings as $_ease) {
							
							$_selected = $_ease !== $_easing ? '' : ' selected';
							$_markup .= '<option value="' . $_ease . '"' . $_selected . '>' . $_ease . '</option>';
							
						}
						
					$_markup .= '</select>
					
					<span class="label" id="label_beforeafter_def_animateout" origtitle="' . __('Choose how the current <em>After</em> content should animate out when the slide changes.<br><br>', $_textDomain) . '">' . __('Animate Out', $_textDomain) . '</span>
					<select value="' . $_animateOut . '" id="beforeafter_def_animateout" name="beforeafter_def_animateout" class="withlabel">';
						
						foreach($_outs as $_out) {
							
							$_selected = $_out !== $_animateOut ? '' : ' selected';
							$_markup .= '<option value="' . $_out . '"' . $_selected . '>' . ucfirst($_out) . '</option>';
							
						}
						
					$_markup .= '</select>
					<br>
					
					<span class="label" id="label_beforeafter_def_bouncearrows" origtitle="' . __('Teaser animation for the drag arrows<br><br>', $_textDomain) . '">' . __('Arrows Teaser', $_textDomain) . '</span>
					<select value="' . $_bounceArrows . '" id="beforeafter_def_bouncearrows" name="beforeafter_def_bouncearrows" class="withlabel">';
						
						foreach($_bounces as $_key => $_value) {
							
							$_selected = $_key !== $_bounceArrows ? '' : ' selected';
							$_markup .= '<option value="' . $_key . '"' . $_selected . '>' . $_value . '</option>';
							
						}
						
					$_markup .= '</select>
					
					<div id="beforeafter-bounce-options" class="withsublabels" style="display: ' . $_bounceOptions . '">
						
						<span class="label" id="label_beforeafter_def_bouncetype" origtitle="' . __("The direction the arrows should bounce toward<br><br>", $_textDomain) . '">' . __('Bounce Type', $_textDomain) . '</span>
						<select value="' . $_bounceType . '" id="beforeafter_def_bouncetype" name="beforeafter_def_bouncetype" class="withlabel">';
						
							foreach($_bounceTypes as $_bounce) {
								
								$_selected = $_bounce !== $_bounceType ? '' : ' selected';
								$_markup .= '<option value="' . $_bounce . '"' . $_selected . '>' . ucfirst($_bounce) . '</option>';
								
							}
							
						$_markup .= '</select>
					
						<span class="label" id="label_beforeafter_def_bounceamount" origtitle="' . __("The distance in pixels the arrows should bounce.<br><br>", $_textDomain) . '">' . __('Bounce Amount', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_bounceAmount . '" data-min="0" data-max="1000" id="beforeafter_def_bounceamount" name="beforeafter_def_bounceamount" value="' . $_bounceAmount . '" /> px
						<br>
						
						<span class="label" id="label_beforeafter_def_bouncespeed" origtitle="' . __("The animation time for each bounce sequence<br><br>", $_textDomain) . '">' . __('Bounce Speed', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_bounceSpeed . '" data-min="100" data-max="5000" id="beforeafter_def_bouncespeed" name="beforeafter_def_bouncespeed" value="' . $_bounceSpeed . '" /> ms
						<br>
						
						<span class="label" id="label_beforeafter_def_bounceeasing" origtitle="' . __("The bounce animations transition type<br><br>", $_textDomain) . '">' . __('Bounce Easing', $_textDomain) . '</span>
						<select value="' . $_bounceEasing . '" id="beforeafter_def_bounceeasing" name="beforeafter_def_bounceeasing" class="withlabel">';
						
							foreach($_eases as $_ease) {
								
								$_selected = $_ease !== $_bounceEasing ? '' : ' selected';
								$_markup .= '<option value="' . $_ease . '"' . $_selected . '>' . $_ease . '</option>';
								
							}
							
						$_markup .= '</select>
						<br>
						
						<span class="label" id="label_beforeafter_def_bouncedelay" origtitle="' . __("Optional delay in milliseconds before the arrows start to bounce.<br><br>", $_textDomain) . '">' . __('Start Delay', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_bounceDelay . '" data-min="0" data-max="10000" id="beforeafter_def_bouncedelay" name="beforeafter_def_bouncedelay" value="' . $_bounceDelay . '" /> ms
						<br>
						
					</div>
					
					<span class="label" id="label_beforeafter_def_shiftarrows" origtitle="' . __("Animate the arrows into place after the initial reveal.<br><br>", $_textDomain) . '">' . __('Arrows Transition', $_textDomain) . '</span> 
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="beforeafter_def_shiftarrows" name="beforeafter_def_shiftarrows"' . $_shiftArrows . ' onchange="document.getElementById(\'beforeafter-shift-options\').style.display=this.checked ? \'block\' : \'none\'" />
					
					<div id="beforeafter-shift-options" class="withsublabels" style="display: ' . $_shiftOptions . '">
						
						<span class="label" id="label_beforeafter_def_shiftoffset" origtitle="' . __("The initial offset for the arrows<br><br>", $_textDomain) . '">' . __('Initial Offset', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_shiftOffset . '" data-min="0" data-max="10000" id="beforeafter_def_shiftoffset" name="beforeafter_def_shiftoffset" value="' . $_shiftOffset . '" /> px
						<br>
						
						<span class="label" id="label_beforeafter_def_shifttiming" origtitle="' . __("The transition time in milliseconds<br><br>", $_textDomain) . '">' . __('Speed', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_shiftTiming . '" data-min="0" data-max="5000" id="beforeafter_def_shifttiming" name="beforeafter_def_shifttiming" value="' . $_shiftTiming . '" /> ms
						<br>
						
						<span class="label" id="label_beforeafter_def_shifteasing" origtitle="' . __("The transition type for the animation<br><br>", $_textDomain) . '">' . __('Easing', $_textDomain) . '</span>
						<select value="' . $_shiftEasing . '" id="beforeafter_def_shifteasing" name="beforeafter_def_shifteasing" class="withlabel">';
						
							foreach($_eases as $_ease) {
								
								$_selected = $_ease !== $_shiftEasing ? '' : ' selected';
								$_markup .= '<option value="' . $_ease . '"' . $_selected . '>' . $_ease . '</option>';
								
							}
							
						$_markup .= '</select>
						<br>
						
						<span class="label" id="label_beforeafter_def_shiftdelay" origtitle="' . __("Optional delay in milliseconds for the transition.<br><br>", $_textDomain) . '">' . __('Delay', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_shiftDelay . '" data-min="0" data-max="10000" id="beforeafter_def_shiftdelay" name="beforeafter_def_shiftdelay" value="' . $_shiftDelay . '" /> ms
					
					</div>
					
				</div>
				
				<div id="beforeafter-misc" style="display: none">
					
					<span class="label" id="label_beforeafter_onclick" origtitle="' . __("Clicking a point on the slider will move the reveal line automatically to that point.<br><br>", $_textDomain) . '">' . __('Animate on Stage Click', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="beforeafter_onclick" name="beforeafter_onclick"' . $_onClick . ' onchange="document.getElementById(\'beforeafter-onclick\').style.display=this.checked ? \'block\' : \'none\'" />
					<br>
					
					<div id="beforeafter-onclick" class="withsublabels" style="display: ' . $_clickActive . '">
					
						<span class="label" id="label_beforeafter_click_time" origtitle="' . __("The duration of the animation as it occurs (in milliseconds).<br><br>", $_textDomain) . '">' . __('Duration', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel beforeafter-min-max" data-default-value="' . $_clickTime . '" data-min="100" data-max="10000" id="beforeafter_click_time" name="beforeafter_click_time" value="' . $_clickTime . '" /> ms
						<br>
						
						<span class="label" id="label_beforeafter_click_easing" origtitle="' . __("The animation timing equation<br><br>", $_textDomain) . '">' . __('Easing', $_textDomain) . '</span>
						<select value="' . $_clickEasing . '" id="beforeafter_click_easing" name="beforeafter_click_easing" class="withlabel">';
							
							foreach($_easings as $_ease) {
								
								$_selected = $_ease !== $_clickEasing ? '' : ' selected';
								$_markup .= '<option value="' . $_ease . '"' . $_selected . '>' . $_ease . '</option>';
								
							}
							
						$_markup .= '</select>
					
					</div>
					
					<span class="label" id="label_beforeafter_cursor" origtitle="' . __("The CSS mouse cursor to be displayed when the mouse hovers over the arrow icon container.<br><br>", $_textDomain) . '">' . __('Mouse Cursor', $_textDomain) . '</span>
					<select value="' . $_cursor . '" id="beforeafter_cursor" name="beforeafter_cursor" class="withlabel">';
						
						foreach($_cursors as $_cur) {
							
							$_selected = $_cur !== $_cursor ? '' : ' selected';
							$_markup .= '<option value="' . $_cur . '"' . $_selected . '>' . $_cur . '</option>';
							
						}
						
					$_markup .= '</select>
					
				</div>
				
				<div id="before-after-icon-selector"><div>';
					foreach($_icons as $_icon) {
						
						$_markup .= '<span class="before-after-icon-option before-after-icon-select" data-icon="' . $_icon . '"><i class="' . $_icon . '"></i></span>';
						
					}
					
				$_markup .= '</div></div>
					
			</div>
			
			<style type="text/css">
			
				.setting_box .fa-icon-adjust {width: 17px; margin-left: 3px}
				.setting_box .fa-icon-adjust:before {position: relative; top: 3px} 
				.beforeafter_color_wrap {position: relative; top: 8px}
				
				#before-after-icon-selector {
					width: 375px;
					top: 120px;
					left: 0;
					right: 0;
					z-index: 120000;
					display: none;
					position: absolute;
					background: #fff;
					padding: 10px 0;
					/*border: 10px solid #fff;*/
					/*box-shadow: 0px 20px 20px rgba(0,0,0,0.15);*/
				}
				
				#before-after-icon-selector > div {	
					width: 280px;
					margin: 0 auto;
				}
				
				.before-after-icon-option {
					margin: 0px 2px 2px 0px;
					font-size: 14px;
					border: 1px solid #F1F1F1;
					cursor: pointer;
					background: #fff;
					position: relative;
					display: inline-block;
					width: 31px;
					height: 31px;
					line-height: 31px !important;
					text-align: center;
					vertical-align: middle;
				}
				
				#before-after-icon-selector .before-after-icon-option {
					float: left;
					display: block;
				}

				.before-after-icon-option:hover {
					background: #3498DB;
					border-color: #3498DB !important;
					color: #fff;
				}
				
				.before-after-icon-select.selected {
					border-color: #000 ;
				}
				
			</style>
			
		</div>';
		
		static::$_Markup = $_markup;
		static::$_Icon = 'fa-icon-adjust';
		static::$_JavaScript = '
			
			jQuery(function() {
				
				var iconSelector,
					selectedIcon,
					arrowDirection;
				
				function setId() {
						
					var $this = jQuery(this);
					$this.attr("id", $this.find(".before-after-color").attr("name"));
					
				}
				
				if(jQuery.fn.tpColorPicker) {
					
					jQuery(".before-after-color").each(function() {
						
						jQuery(this).tpColorPicker({mode: "single", wrapClasses: "beforeafter_color_wrap withlabel", init: function() {
							
							jQuery(".beforeafter_color_wrap").each(setId);
							
						}});
						
					});
					
				}
				else {
					
					jQuery(".before-after-color").wpColorPicker({palettes: false, height: 250, border: false, change: function(evt, ui) {
						this.value = ui.color.toString();
					}});
					
				}
				
				jQuery(".beforeafter-min-max").on("change", function() {
					
					var val = this.value;
					if(val === "") val = 0;
					
					val = Math.max(parseFloat(this.getAttribute("data-min")), 
						  Math.min(parseFloat(this.getAttribute("data-max")), parseFloat(val)));
					
					if(!isNaN(val)) this.value = val;
					else this.value = this.getAttribute("data-default-value");
					
				});
				
				jQuery("#beforeafter_arrow_radius").on("focusout", function() {
					
					var val = this.value,
						perc;
						
					if(val.search("%") !== -1) {
							
						perc = "%";
						val = val.replace("%", "");
						
					}
					else {

						perc = /[0-9]*\.?[0-9]+(px)?/.test(val) ? "px" : "";
						
					}
					
					val = parseInt(val, 10);
					
					if(isNaN(val)) val = "0";
					else val = Math.min(Math.max(0, val), 100);
					
					this.value = val + perc;
					
				});
				
				jQuery(".beforeafter-icon").on("click", function() {
					
					if(!iconSelector) iconSelector = jQuery("#before-after-icon-selector");
					
					selectedIcon = this;
					arrowDirection = this.getAttribute("data-arrow");
					
					iconSelector.find(".selected").removeClass("selected");
					iconSelector.find(".before-after-icon-select[data-icon=" + this.getAttribute("data-icon") + "]").addClass("selected");
					iconSelector.show();
					
				});
				
				jQuery(".before-after-icon-select").on("click", function() {
					
					var icon = this.getAttribute("data-icon");
					selectedIcon.setAttribute("data-icon", icon);
					
					document.getElementById("beforeafter_" + arrowDirection + "_arrow").value = icon;
					document.getElementById("before-after-icon-" + arrowDirection).className = icon;
					
					iconSelector.hide();
					
				});
				
				document.getElementById("beforeafter_def_bouncearrows").addEventListener("change", function() {
					
					var display = this.value !== "none" ? "block" : "none";
					document.getElementById("beforeafter-bounce-options").style.display = display;
					
				});
				
				var speeds,
					times,
					timings = jQuery("#beforeafter_def_moveto");
				
				function timeEach(i) {
					
					var $this = jQuery(this),
						val = $this.val(),
						perc = "";
					
					if(!val) {
						
						val = "50%";
						$this.val("50%");
						
					}
					
					if(val.search("%") !== -1) {
						
						perc = "%";
						val = val.replace("%", "");
						
						if(isNaN(val)) val = "50";
						else val = Math.min(Math.max(0, val), 100);
						
					}
					else {
						
						perc = /[0-9]*\.?[0-9]+(px)?/.test(val) ? "px" : "";
						
					}
					
					val = parseInt(val, 10);
					if(isNaN(val)) {
			
						val = 50;
						perc = "%";
						
					}
					
					val += perc;
					$this.val(val);
					
					if(i !== 0) speeds += "|";
					speeds += val;
					
				}
				
				times = jQuery(".beforeafter-def-moveto").on("focusout", function() {
					
					speeds = "";
					times.each(timeEach);
					timings.val(speeds);
					
				});
			
			});
		
		';
		
	}
	
	public function export_slider($_data, $_slides, $_sliderParams, $_useDummy) {
		
		foreach($_slides as $_slide) {
			
			// do slide params exist?
			if(!isset($_slide['params'])) continue;
			
			// does the slide have before/after data?
			if(!isset($_slide['params']['background_type_beforeafter'])) continue;
			
			// is the "after" bg source type an image?
			$_type = $_slide['params']['background_type_beforeafter'];
			if($_type !== 'image') continue;
			
			// is the "after" bg image set?
			if(!isset($_slide['params']['image_url_beforeafter'])) continue;
			
			// does the "after" image url exist?  If so, add it to the export image list
			$_image = $_slide['params']['image_url_beforeafter'];
			if(!empty($_image)) $_data['usedImages'][$_image] = true;
			
		}
		
		return $_data;
		
	}
	
	public static function import_slider($_data, $_slide_type, $_image_path) {

		// do the slide params exist and does the slide have before/after data?
		if(isset($_data['params'])) {
			
			if(isset($_data['params']['background_type_beforeafter'])) {
				
				// reset image id data in case it can't be retrieved upon import
				$_imageId = '';
				
				// is the "after" bg source set to "image" and is the image url set?
				$_type = $_data['params']['background_type_beforeafter'];
				if($_type === 'image' && isset($_data['params']['image_url_beforeafter'])) {
					
					// does the image url exist?
					$_image = $_data['params']['image_url_beforeafter'];
					if(!empty($_image)) {
						
						// convert the image url (domain paths get replaced)
						$_url = RevSliderBase::check_file_in_zip($_image_path, $_image, $_data['sliderParams']['alias'], $_data['alreadyImported']);
						$_url = RevSliderFunctionsWP::getImageUrlFromPath($_url);
						
						// if the url converted ok
						if(!empty($_url)) {
							
							// update the before/after data with the converted image url
							$_data['params']['image_url_beforeafter'] = $_url;
							
							// attempt to get the image's new ID and update the before/after data
							$_id = RevSliderFunctionsWP::get_image_id_by_url($_url);
							if(!empty($_id)) $_imageId = $_id;
							
						}
						
					}
					
				}
				
				// set the image ID to the new ID or to an empty string
				// this will prevent a "faulty" image ID from possibly existing when the frontend output runs
				$_data['params']['image_id_beforeafter'] = $_imageId;
				
			}

		}
		
		return $_data;
		
	}
	
}