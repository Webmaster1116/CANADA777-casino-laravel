<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

require_once(RS_POLYFOLD_PLUGIN_PATH . 'framework/slider.admin.class.php');

class RsPolyfoldSliderAdmin extends RsAddonPolyfoldSliderAdmin {
	
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
	
	// admin view
	protected static function _init($_slider) {
		
		$_topEnabled       = RevSliderFunctions::getVal($_slider, 'polyfold_top_enabled',        false) == 'true' ? ' checked' : '';
		$_bottomEnabled    = RevSliderFunctions::getVal($_slider, 'polyfold_bottom_enabled',     false) == 'true' ? ' checked' : '';
		$_topMobile        = RevSliderFunctions::getVal($_slider, 'polyfold_top_hide_mobile',    false) == 'true' ? ' checked' : '';
		$_bottomMobile     = RevSliderFunctions::getVal($_slider, 'polyfold_bottom_hide_mobile', false) == 'true' ? ' checked' : '';
		$_topNegative      = RevSliderFunctions::getVal($_slider, 'polyfold_top_negative',       false) == 'true' ? ' checked' : '';
		$_bottomNegative   = RevSliderFunctions::getVal($_slider, 'polyfold_bottom_negative',    false) == 'true' ? ' checked' : '';
		$_topAnimated      = RevSliderFunctions::getVal($_slider, 'polyfold_top_animated',       false) == 'true' ? ' checked' : '';
		$_bottomAnimated   = RevSliderFunctions::getVal($_slider, 'polyfold_bottom_animated',    false) == 'true' ? ' checked' : '';
		$_topInverted      = RevSliderFunctions::getVal($_slider, 'polyfold_top_inverted',       false) == 'true' ? ' checked' : '';
		$_bottomInverted   = RevSliderFunctions::getVal($_slider, 'polyfold_bottom_inverted',    false) == 'true' ? ' checked' : '';
		$_topResponsive    = RevSliderFunctions::getVal($_slider, 'polyfold_top_responsive',     true)  == 'true' ? ' checked' : '';
		$_bottomResponsive = RevSliderFunctions::getVal($_slider, 'polyfold_bottom_responsive',  true)  == 'true' ? ' checked' : '';
		$_topScroll        = RevSliderFunctions::getVal($_slider, 'polyfold_top_scroll',         true)  == 'true' ? ' checked' : '';
		$_bottomScroll     = RevSliderFunctions::getVal($_slider, 'polyfold_bottom_scroll',      true)  == 'true' ? ' checked' : '';
		
		$_topHeight        = RevSliderFunctions::getVal($_slider, 'polyfold_top_height',         '100');
		$_bottomHeight     = RevSliderFunctions::getVal($_slider, 'polyfold_bottom_height',      '100');
		$_topLeftWidth     = RevSliderFunctions::getVal($_slider, 'polyfold_top_left_width',     '50');
		$_bottomLeftWidth  = RevSliderFunctions::getVal($_slider, 'polyfold_bottom_left_width',  '50');
		$_topRightWidth    = RevSliderFunctions::getVal($_slider, 'polyfold_top_right_width',    '50');
		$_bottomRightWidth = RevSliderFunctions::getVal($_slider, 'polyfold_bottom_right_width', '50');
		$_topTime          = RevSliderFunctions::getVal($_slider, 'polyfold_top_time',           '0.3');
		$_bottomTime       = RevSliderFunctions::getVal($_slider, 'polyfold_bottom_time',        '0.3');
		$_topPlacement     = RevSliderFunctions::getVal($_slider, 'polyfold_top_placement',      '1');
		$_bottomPlacement  = RevSliderFunctions::getVal($_slider, 'polyfold_bottom_placement',   '1');
		
		$_topRange         = RevSliderFunctions::getVal($_slider, 'polyfold_top_range',          'slider');
		$_bottomRange      = RevSliderFunctions::getVal($_slider, 'polyfold_bottom_range',       'slider');
		$_topPoint         = RevSliderFunctions::getVal($_slider, 'polyfold_top_point',          'sides');
		$_bottomPoint      = RevSliderFunctions::getVal($_slider, 'polyfold_bottom_point',       'sides');
		$_topColor         = RevSliderFunctions::getVal($_slider, 'polyfold_top_color',          '#ffffff');
		$_bottomColor      = RevSliderFunctions::getVal($_slider, 'polyfold_bottom_color',       '#ffffff');
		$_topEase          = RevSliderFunctions::getVal($_slider, 'polyfold_top_ease',           'ease-out');
		$_bottomEase       = RevSliderFunctions::getVal($_slider, 'polyfold_bottom_ease',        'ease-out');
		
		$_topSettings      = $_topEnabled      ? 'block' : 'none';
		$_bottomSettings   = $_bottomEnabled   ? 'block' : 'none';
		$_topScrollSets    = $_topScroll       ? 'block' : 'none';
		$_bottomScrollSets = $_bottomScroll    ? 'block' : 'none';
		$_topAnimatedSets  = $_topAnimated     ? 'block' : 'none';
		$_botAnimatedSets  = $_bottomAnimated  ? 'block' : 'none';
		
		if(!$_topColor)    $_topColor    = '#ffffff';
		if(!$_bottomColor) $_bottomColor = '#ffffff';
		
		$_textDomain       = 'rs_' . static::$_Title;
		$_points           = array('sides', 'center');
		$_ranges           = array('slider', 'window');
		$_easings          = array('ease-out', 'ease-in', 'ease-in-out', 'ease', 'linear');
		$_placements       = array('1' => 'Above Entire Slider', '2' => 'Behind Navigation', '3' => 'Behind Static Layers');
		
		$_markup = '<style type="text/css">#viewWrapper .polyfold-settings .wp-picker-active .wp-picker-input-wrap {display: block}</style>
		
		<ul class="main-options-small-tabs" style="display:inline-block">
			<li id="polyfold_top_edge" data-content="#particles-top-edge" class="selected">Top Edge</li>
			<li id="polyfold_bottom_edge" data-content="#particles-bottom-edge">Bottom Edge</li>
		</ul>
		
		<div id="particles-top-edge">
		
			<span class="label" id="label_polyfold_top_enabled" origtitle="' . __("Enable/Disable Top Edges for the Polyfold<br>Add-On.<br><br>", $_textDomain) . '">' . __('Enable Top Edges', $_textDomain) . '</span>
			<input type="checkbox" class="tp-moderncheckbox withlabel" id="polyfold_top_enabled" name="polyfold_top_enabled"' . $_topEnabled . ' 
				onchange="document.getElementById(\'polyfold-top-settings\').style.display=this.checked ? \'block\' : \'none\'" />
			
			<div id="polyfold-top-settings" class="polyfold-settings" style="display: ' . $_topSettings . '; margin-top: 7px">
				
				<h4>Top Edge Settings</h4>
				
				<span class="label polyfold-color-label" id="label_polyfold_top_color" origtitle="' . __('The selected color should match your web page\'s background color.<br><br>', $_textDomain) . '">' . __("Page Background Color", $_textDomain) . '</span>
				<input id="polyfold_top_color" name="polyfold_top_color" type="text" class="rs-layer-input-field tipsy_enabled_top polyfold-color-input" title="Select a Color" value="' . $_topColor . '" />
				<br>
				
				<span class="label" id="label_polyfold_top_scroll" origtitle="' . __('Draw the edges as the slider is scrolled into and out of view.<br><br>', $_textDomain) . '">' . __("Draw on-scroll", $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="polyfold_top_scroll" name="polyfold_top_scroll"' . $_topScroll . ' 
					onchange="document.getElementById(\'polyfold-top-scroll\').style.display=this.checked ? \'block\' : \'none\'" />
				
				<div id="polyfold-top-scroll" class="withsublabels" style="display: ' . $_topScrollSets . '">
					
					<span class="label" id="label_polyfold_top_range" origtitle="' . __("Calculate angles based on the Slider's position within the window compared to the overall Slider height or the overall Window height<br><br>", $_textDomain) . '">' . __('Drawing Range', $_textDomain) . '</span>
					<select class="withlabel" id="polyfold_top_range" name="polyfold_top_range">';
					
					foreach($_ranges as $_range) {
						
						$_selected = $_range === $_topRange ? ' selected' : '';
						$_markup .= '<option value="' . $_range . '"' . $_selected . '>' . ucfirst($_range) . ' Height</option>';
					
					}
					
					$_markup .= '</select>
					<br>
					
					<span class="label" id="label_polyfold_top_animated" origtitle="' . __("Animate the edges as the page is scrolled<br><br>", $_textDomain) . '">' . __('Use Transition', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="polyfold_top_animated" name="polyfold_top_animated"' . $_topAnimated . ' 
					onchange="document.getElementById(\'polyfold-top-animated\').style.display=this.checked ? \'block\' : \'none\'" />
					
					<div id="polyfold-top-animated" class="withsublabels" style="display: ' . $_topAnimatedSets . '; padding-left: 20px">
						
						<span class="label" id="label_polyfold_top_ease" origtitle="' . __("The transition timing equation<br><br>", $_textDomain) . '">' . __('Transition Easing', $_textDomain) . '</span>
						<select class="withlabel" id="polyfold_top_ease" name="polyfold_top_ease">';
						
						foreach($_easings as $_ease) {
							
							$_selected = $_ease !== $_topEase ? '' : ' selected';
							$_markup .= '<option value="' . $_ease . '"' . $_selected . '>' . $_ease . '</option>';
						
						}
						
						$_markup .= '</select>
						<br>
						
						<span class="label" id="label_polyfold_top_time" origtitle="' . __("The animation time in seconds (a number between 0.3-1.0 is recommended)<br><br>", $_textDomain) . '">' . __('Transition Time', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel" id="polyfold_top_time" name="polyfold_top_time" value="' . $_topTime . '" />
						
					</div>
					
					<span class="label" id="label_polyfold_top_inverted" origtitle="' . __("Reverse the drawing as the page is scrolled<br><br>", $_textDomain) . '">' . __('Inverted Scroll', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="polyfold_top_inverted" name="polyfold_top_inverted"' . $_topInverted . ' />
					
				</div>
				
				<span class="label" id="label_polyfold_top_left_width" origtitle="' . __("How long the edge should span across the slider (0-100%).<br><br>", $_textDomain) . '">' . __('Left Edge Width', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel polyfold-min-max" id="polyfold_top_left_width" name="polyfold_top_left_width" data-min="0" data-max="100" value="' . $_topLeftWidth . '" /> <span>%</span>
				<br>
				
				<span class="label" id="label_polyfold_top_right_width" origtitle="' . __("How long the edge should span across the slider (0-100%).<br><br>", $_textDomain) . '">' . __('Right Edge Width', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel polyfold-min-max" id="polyfold_top_right_width" name="polyfold_top_right_width" data-min="0" data-max="100" value="' . $_topRightWidth . '" /> <span>%</span>
				<br>
				
				<span class="label" id="label_polyfold_top_height" origtitle="' . __("The default height in pixels for the edges<br><br>", $_textDomain) . '">' . __('Default Height', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel" id="polyfold_top_height" name="polyfold_top_height" value="' . $_topHeight . '" /> <span>px</span>
				<br>
				
				<span class="label" id="label_polyfold_top_responsive" origtitle="' . __("Dynamically adjust the Polyfold Height as the slider is resized.<br><br>", $_textDomain) . '">' . __('Responsive Height', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="polyfold_top_responsive" name="polyfold_top_responsive"' . $_topResponsive . ' />
				<br>
				
				<span class="label" id="label_polyfold_top_negative" origtitle="' . __("Draw the edges in the opposite direction<br><br>", $_textDomain) . '">' . __('Inverted Angles', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="polyfold_top_negative" name="polyfold_top_negative"' . $_topNegative . ' />
				<br>
				
				<span class="label" id="label_polyfold_top_point" origtitle="' . __("The starting point from where the drawing begins<br><br>", $_textDomain) . '">' . __('Draw Edges from', $_textDomain) . '</span>
				<select class="withlabel" id="polyfold_top_point" name="polyfold_top_point">';
				
				foreach($_points as $_point) {
					
					$_selected = $_point !== $_topPoint ? '' : ' selected';
					$_markup .= '<option value="' . $_point . '"' . $_selected . '>Slider ' . ucfirst($_point) . '</option>';
				
				}
				
				$_markup .= '</select>
				<br>
				
				<span class="label" id="label_polyfold_top_placement" origtitle="' . __("z-index positioning for what level the edges should be drawn at.<br><br>", $_textDomain) . '">' . __('Draw the Edges', $_textDomain) . '</span>
				<select class="withlabel" id="polyfold_top_placement" name="polyfold_top_placement">';
				
				foreach($_placements as $_key => $_value) {
					
					$_selected = $_key !== $_topPlacement ? '' : ' selected';
					$_markup .= '<option value="' . $_key . '"' . $_selected . '>' . $_value . '</option>';
				
				}
				
				$_markup .= '</select>
				<br>
				
				<span class="label" id="label_polyfold_top_hide_mobile" origtitle="' . __("Disable the Polyfold Effect on Mobile Devices<br><br>", $_textDomain) . '">' . __('Disable on Mobile', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="polyfold_top_hide_mobile" name="polyfold_top_hide_mobile"' . $_topMobile . ' />
				
			</div>
			
		</div>
		
		<div id="particles-bottom-edge" style="display: none">
			
			<span class="label" id="label_polyfold_bottom_enabled" origtitle="' . __("Enable/Disable Bottom Edges for the Polyfold<br>Add-On.<br><br>", $_textDomain) . '">' . __('Enable Bottom Edges', $_textDomain) . '</span>
			<input type="checkbox" class="tp-moderncheckbox withlabel" id="polyfold_bottom_enabled" name="polyfold_bottom_enabled"' . $_bottomEnabled . ' 
				onchange="document.getElementById(\'polyfold-bottom-settings\').style.display=this.checked ? \'block\' : \'none\'" />
			
			<div id="polyfold-bottom-settings" class="polyfold-settings" style="display: ' . $_bottomSettings . '; margin-top: 7px">
				
				<h4>Bottom Edge Settings</h4>
				
				<span class="label polyfold-color-label" id="label_polyfold_bottom_color" origtitle="' . __('The selected color should match your web page\'s background color.<br><br>', $_textDomain) . '">' . __("Page Background Color", $_textDomain) . '</span>
				<input id="polyfold_bottom_color" name="polyfold_bottom_color" type="text" class="rs-layer-input-field tipsy_enabled_bottom polyfold-color-input" title="Select a Color" value="' . $_bottomColor . '" />
				<br>
				
				<span class="label" id="label_polyfold_bottom_scroll" origtitle="' . __('Draw the edges as the slider is scrolled into and out of view.<br><br>', $_textDomain) . '">' . __("Draw on-scroll", $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="polyfold_bottom_scroll" name="polyfold_bottom_scroll"' . $_bottomScroll . ' 
					onchange="document.getElementById(\'polyfold-bottom-scroll\').style.display=this.checked ? \'block\' : \'none\'" />
				
				<div id="polyfold-bottom-scroll" class="withsublabels" style="display: ' . $_bottomScrollSets . '">
					
					<span class="label" id="label_polyfold_bottom_range" origtitle="' . __("Calculate angles based on the Slider's position within the window compared to the overall Slider height or the overall Window height<br><br>", $_textDomain) . '">' . __('Drawing Range', $_textDomain) . '</span>
					<select class="withlabel" id="polyfold_bottom_range" name="polyfold_bottom_range">';
					
					foreach($_ranges as $_range) {
						
						$_selected = $_range === $_bottomRange ? ' selected' : '';
						$_markup .= '<option value="' . $_range . '"' . $_selected . '>' . ucfirst($_range) . ' Height</option>';
					
					}
					
					$_markup .= '</select>
					<br>
					
					<span class="label" id="label_polyfold_bottom_animated" origtitle="' . __("Animate the edges as the page is scrolled<br><br>", $_textDomain) . '">' . __('Use Transition', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="polyfold_bottom_animated" name="polyfold_bottom_animated"' . $_bottomAnimated . ' 
					onchange="document.getElementById(\'polyfold-bottom-animated\').style.display=this.checked ? \'block\' : \'none\'" />
					
					<div id="polyfold-bottom-animated" class="withsublabels" style="display: ' . $_botAnimatedSets . '; padding-left: 20px">
						
						<span class="label" id="label_polyfold_bottom_ease" origtitle="' . __("The transition timing equation<br><br>", $_textDomain) . '">' . __('Transition Easing', $_textDomain) . '</span>
						<select class="withlabel" id="polyfold_bottom_ease" name="polyfold_bottom_ease">';
						
						foreach($_easings as $_ease) {
							
							$_selected = $_ease !== $_bottomEase ? '' : ' selected';
							$_markup .= '<option value="' . $_ease . '"' . $_selected . '>' . $_ease . '</option>';
						
						}
						
						$_markup .= '</select>
						<br>
						
						<span class="label" id="label_polyfold_bottom_time" origtitle="' . __("The animation time in seconds (a number between 0.3-1.0 is recommended)<br><br>", $_textDomain) . '">' . __('Transition Time', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel" id="polyfold_bottom_time" name="polyfold_bottom_time" value="' . $_bottomTime . '" />
						
					</div>
					
					<span class="label" id="label_polyfold_bottom_inverted" origtitle="' . __("Reverse the drawing as the page is scrolled<br><br>", $_textDomain) . '">' . __('Inverted Scroll', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="polyfold_bottom_inverted" name="polyfold_bottom_inverted"' . $_bottomInverted . ' />
					
				</div>
				
				<span class="label" id="label_polyfold_bottom_left_width" origtitle="' . __("How long the edge should span across the slider (0-100%).<br><br>", $_textDomain) . '">' . __('Left Edge Width', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel polyfold-min-max" id="polyfold_bottom_left_width" name="polyfold_bottom_left_width" data-min="0" data-max="100" value="' . $_bottomLeftWidth . '" /> <span>%</span>
				<br>
				
				<span class="label" id="label_polyfold_bottom_right_width" origtitle="' . __("How long the edge should span across the slider (0-100%).<br><br>", $_textDomain) . '">' . __('Right Edge Width', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel polyfold-min-max" id="polyfold_bottom_right_width" name="polyfold_bottom_right_width" data-min="0" data-max="100" value="' . $_bottomRightWidth . '" /> <span>%</span>
				<br>
				
				<span class="label" id="label_polyfold_bottom_height" origtitle="' . __("The default height in pixels for the edges<br><br>", $_textDomain) . '">' . __('Default Height', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel" id="polyfold_bottom_height" name="polyfold_bottom_height" value="' . $_bottomHeight . '" /> <span>px</span>
				<br>
				
				<span class="label" id="label_polyfold_bottom_responsive" origtitle="' . __("Dynamically adjust the Polyfold Height as the slider is resized.<br><br>", $_textDomain) . '">' . __('Responsive Height', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="polyfold_bottom_responsive" name="polyfold_bottom_responsive"' . $_bottomResponsive . ' />
				<br>
				
				<span class="label" id="label_polyfold_bottom_negative" origtitle="' . __("Draw the edges in the opposite direction<br><br>", $_textDomain) . '">' . __('Inverted Angles', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="polyfold_bottom_negative" name="polyfold_bottom_negative"' . $_bottomNegative . ' />
				<br>
				
				<span class="label" id="label_polyfold_bottom_point" origtitle="' . __("The starting point from where the drawing begins<br><br>", $_textDomain) . '">' . __('Draw Edges from', $_textDomain) . '</span>
				<select class="withlabel" id="polyfold_bottom_point" name="polyfold_bottom_point">';
				
				foreach($_points as $_point) {
					
					$_selected = $_point !== $_bottomPoint ? '' : ' selected';
					$_markup .= '<option value="' . $_point . '"' . $_selected . '>Slider ' . ucfirst($_point) . '</option>';
				
				}
				
				$_markup .= '</select>
				<br>
				
				<span class="label" id="label_polyfold_bottom_placement" origtitle="' . __("z-index positioning for what level the edges should be drawn at.<br><br>", $_textDomain) . '">' . __('Draw the Edges', $_textDomain) . '</span>
				<select class="withlabel" id="polyfold_bottom_placement" name="polyfold_bottom_placement">';
				
				foreach($_placements as $_key => $_value) {
					
					$_selected = $_key !== $_bottomPlacement ? '' : ' selected';
					$_markup .= '<option value="' . $_key . '"' . $_selected . '>' . $_value . '</option>';
				
				}
				
				$_markup .= '</select>
				<br>
				
				<span class="label" id="label_polyfold_bottom_hide_mobile" origtitle="' . __("Disable the Polyfold Effect on Mobile Devices<br><br>", $_textDomain) . '">' . __('Disable on Mobile', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="polyfold_bottom_hide_mobile" name="polyfold_bottom_hide_mobile"' . $_bottomMobile . ' />
				
			</div>
			
		</div>';
		
		static::$_Markup = $_markup;
		static::$_Icon = 'eg-icon-layers-alt';
		static::$_JavaScript = '
			
			jQuery(function() {
				
				// activate the color picker
				if(jQuery.fn.tpColorPicker) {
					
					jQuery(".polyfold-color-input").tpColorPicker({mode: "basic", editing: "Polyfold Edge Color", wrapClasses: "polyfold-cp", init: function() {
						
						jQuery(".polyfold-cp").css({position: "relative", top: 8});
						
					}});
					
				}
				else {
					
					jQuery(".polyfold-color-input").wpColorPicker({palettes: false, height: 250, border: false, change: function(evt, ui) {
						this.value = ui.color.toString();
					}});
					
				}
				
				// handle inputs with min/max values
				jQuery(".polyfold-min-max").on("change", function() {
					
					this.value = Math.max(parseFloat(this.getAttribute("data-min")), 
								 Math.min(parseFloat(this.getAttribute("data-max")), parseFloat(this.value))); 
					
				});
				
			});
		
		';
		
	}
	
}
?>