<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

require_once(RS_PAINTBRUSH_PLUGIN_PATH . 'framework/slider.admin.class.php');

class RsPaintBrushSliderAdmin extends RsAddonPaintBrushSliderAdmin {
	
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
		
		$_enabled    = RevSliderFunctions::getVal($_slider, 'paintbrush_enabled',    false) == 'true' ? ' checked' : '';
		$_disappear  = RevSliderFunctions::getVal($_slider, 'paintbrush_disappear',  false) == 'true' ? ' checked' : '';
		$_blur       = RevSliderFunctions::getVal($_slider, 'paintbrush_blur',       false) == 'true' ? ' checked' : '';
		$_responsive = RevSliderFunctions::getVal($_slider, 'paintbrush_responsive', false) == 'true' ? ' checked' : '';
		$_mobile     = RevSliderFunctions::getVal($_slider, 'paintbrush_mobile',     false) == 'true' ? ' checked' : '';
		$_fallback   = RevSliderFunctions::getVal($_slider, 'paintbrush_fallback',   false) == 'true' ? ' checked' : '';
		$_fixedges   = RevSliderFunctions::getVal($_slider, 'paintbrush_fixedges',   false) == 'true' ? ' checked' : '';
		$_scaleblur  = RevSliderFunctions::getVal($_slider, 'paintbrush_scaleblur',  false) == 'true' ? ' checked' : '';
		$_style      = RevSliderFunctions::getVal($_slider, 'paintbrush_style',      'round');
		$_size       = RevSliderFunctions::getVal($_slider, 'paintbrush_size',       '80');
		$_blurAmount = RevSliderFunctions::getVal($_slider, 'paintbrush_blurAmount', '10');
		$_fadeTime   = RevSliderFunctions::getVal($_slider, 'paintbrush_fadetime',   '1000');
		$_edgeAmount = RevSliderFunctions::getVal($_slider, 'paintbrush_edgeamount', '10%');
		
		$_textDomain   = 'rs_' . static::$_Title;
		$_styles       = array('round', 'square', 'butt');
		
		$_showSettings   = !empty($_enabled)   ? 'block' : 'none';
		$_showFadeTime   = !empty($_disappear) ? 'block' : 'none';
		$_showBlurAmount = !empty($_blur)      ? 'block' : 'none';
		$_showFallback   = !empty($_fallback)  ? 'block' : 'none';
		$_showEdgeAmount = !empty($_fixedges)  ? 'block' : 'none';
		
		$_markup = '<div id="paintbrush-addon-settings">
		
			<span class="label" id="label_paintbrush_enabled" origtitle="' . __("Enable/Disable the Paint-Brush Add-On for the Slider.<br><br>", $_textDomain) . '">' . __('Enable Addon for this Slider', $_textDomain) . '</span> 
			<input type="checkbox" class="tp-moderncheckbox withlabel" id="paintbrush_enabled" name="paintbrush_enabled"' . $_enabled . ' onchange="document.getElementById(\'paintbrush-settings\').style.display=this.checked ? \'block\' : \'none\'" />
			
			<div id="paintbrush-settings" style="display: ' . $_showSettings . '">
				
				<span class="label" id="label_paintbrush_style" origtitle="' . __("The HTML5 Canvas lineCap style<br><br>", $_textDomain) . '">' . __('Brush Style', $_textDomain) . '</span>
				<select id="paintbrush_style" class="withlabel" name="paintbrush_style" value="' . $_style . '">';
				
					foreach($_styles as $_styl) {
						
						$_selected = $_styl !== $_style ? '' : ' selected';
						$_markup .= '<option value="' . $_styl . '"' . $_selected . '>' . ucfirst($_styl) . '</option>';
						
					}

				$_markup .= '</select>
				<br>
				
				<span class="label" id="label_paintbrush_size" origtitle="' . __("The HTML5 Canvas lineWidth<br><br>", $_textDomain) . '">' . __('Brush Size', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel paintbrush-min-max" data-default-value="' . $_size . '" data-min="5" data-max="500" id="paintbrush_size" name="paintbrush_size" value="' . $_size . '" /> px
				<br>
				
				<span class="label" id="label_paintbrush_responsive" origtitle="' . __("Scale the brush size as the slider resizes<br><br>", $_textDomain) . '">' . __('Responsive Size', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="paintbrush_responsive" name="paintbrush_responsive"' . $_responsive . ' />
				<br>
				
				<span class="label" id="label_paintbrush_disappear" origtitle="' . __('Choose if the paint should fade away after drawing<br><br>', $_textDomain) . '">' . __('Disappear', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="paintbrush_disappear" name="paintbrush_disappear"' . $_disappear . ' 
				onchange="document.getElementById(\'paintbrush-fade-time\').style.display=this.checked ? \'block\' : \'none\'" />
				
				<div id="paintbrush-fade-time" class="withsublabels" style="display: ' . $_showFadeTime . '">
				
					<span class="label" id="label_paintbrush_fadetime" origtitle="' . __("The amount of time before the paint disappears<br><br>", $_textDomain) . '">' . __('Fade Time', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel paintbrush-min-max" data-default-value="' . $_fadeTime . '" data-min="100" data-max="10000" id="paintbrush_fadetime" name="paintbrush_fadetime" value="' . $_fadeTime . '" /> ms
				
				</div>
				
				<span class="label" id="label_paintbrush_blur" origtitle="' . __("Choose to Blur the slide main background image<br><br>", $_textDomain) . '">' . __('Blur Image', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="paintbrush_blur" name="paintbrush_blur"' . $_blur . ' 
				onchange="document.getElementById(\'paintbrush-blur-amount\').style.display=this.checked ? \'block\' : \'none\'" />
				
				<div id="paintbrush-blur-amount" class="withsublabels" style="display: ' . $_showBlurAmount . '">
				
					<span class="label" id="label_paintbrush_bluramount" origtitle="' . __("The CSS3 Blur filter amount in pixels<br><br>", $_textDomain) . '">' . __('Blur Amount', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel paintbrush-min-max" data-default-value="' . $_blurAmount . '" data-min="1" data-max="100" id="paintbrush_bluramount" name="paintbrush_bluramount" value="' . $_blurAmount . '" /> px
					
					<span class="label" id="label_paintbrush_scaleblur" origtitle="' . __("Blur value will be adjusted as the slider resizes<br><br>", $_textDomain) . '">' . __('Responsive Blur', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="paintbrush_scaleblur" name="paintbrush_scaleblur"' . $_scaleblur . ' />
					
					<span class="label" id="label_paintbrush_fixedges" origtitle="' . __("Stretch the image to help remove blur soft edges<br><br>", $_textDomain) . '">' . __('Fix Soft Edges', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="paintbrush_fixedges" name="paintbrush_fixedges"' . $_fixedges . ' 
					onchange="document.getElementById(\'paintbrush-soft-edges\').style.display=this.checked ? \'block\' : \'none\'" />
					
					<div id="paintbrush-soft-edges" class="withsublabels" style="display: ' . $_showEdgeAmount . '">
					
						<span class="label" id="label_paintbrush_edgeamount" origtitle="' . __("Stretch the image (percentage) to help fix blur effect soft edges<br><br>", $_textDomain) . '">' . __('Stretch Amount', $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel paintbrush-min-max" id="paintbrush_edgeamount" name="paintbrush_edgeamount" value="' . $_edgeAmount . '" data-default-value="' . $_edgeAmount . '" data-min="0" data-max="100" /> %
					
					</div>
				
				</div>
				
				<span class="label" id="label_paintbrush_mobile" origtitle="' . __("Recommended for web pages with scrollable content<br><br>", $_textDomain) . '">' . __('Disable on Mobile', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="paintbrush_mobile" name="paintbrush_mobile"' . $_mobile . ' 
				onchange="document.getElementById(\'paintbrush-fallback\').style.display=this.checked ? \'block\' : \'none\'" />
				
				<div id="paintbrush-fallback" class="withsublabels" style="display: ' . $_showFallback . '">
				
					<span class="label" id="label_paintbrush_fallback" origtitle="' . __("Use the chosen Paint-Brush Image as the slide main background when the effect is disabled on mobile<br><br>", $_textDomain) . '">' . __('Use Fallback Image', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="paintbrush_fallback" name="paintbrush_fallback"' . $_fallback . ' />
					
				</div>
					
			</div>
			
		</div>';
		
		static::$_Markup = $_markup;
		static::$_Icon = 'fa-icon-paint-brush';
		static::$_JavaScript = '
			
			jQuery(function() {
				
				jQuery(".paintbrush-min-max").on("change", function() {
					
					var val = this.value;
					if(val === "") val = 0;
					
					val = Math.max(parseInt(this.getAttribute("data-min"), 10), 
						  Math.min(parseInt(this.getAttribute("data-max"), 10), parseInt(val, 10)));
					
					if(!isNaN(val)) this.value = val;
					else this.value = this.getAttribute("data-default-value");
					
				});
				
				$("#paintbrush-addon-settings").closest(".setting_box").find(".fa-icon-paint-brush").css("margin-top", 7);
			
			});
		
		';
		
	}
	
}
?>