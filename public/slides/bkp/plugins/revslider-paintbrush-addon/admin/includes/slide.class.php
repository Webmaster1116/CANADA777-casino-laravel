<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_PAINTBRUSH_PLUGIN_PATH . 'framework/slide.admin.class.php');

class RsPaintBrushSlideAdmin extends RsAddonPaintBrushSlideAdmin {
	
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
		
		$_def_disappear  = $_slider->getParam('paintbrush_disappear',  false);
		$_def_blur       = $_slider->getParam('paintbrush_blur',       false);
		$_def_responsive = $_slider->getParam('paintbrush_responsive', false);
		$_def_mobile     = $_slider->getParam('paintbrush_mobile',     false);
		$_def_fallback   = $_slider->getParam('paintbrush_fallback',   false);
		$_def_fixedges   = $_slider->getParam('paintbrush_fixedges',   false);
		$_def_scaleblur  = $_slider->getParam('paintbrush_scaleblur',  false);
		$_def_style      = $_slider->getParam('paintbrush_style',      'round');
		$_def_size       = $_slider->getParam('paintbrush_size',       '80');
		$_def_blurAmount = $_slider->getParam('paintbrush_bluramount', '10');
		$_def_fadeTime   = $_slider->getParam('paintbrush_fadetime',   '1000');
		$_def_edgeAmount = $_slider->getParam('paintbrush_edgeamount', '10%');
		
		$_enabled    = $_slide->getParam('paintbrush_enabled',    false) == 'true' ? ' checked' : '';
		$_disappear  = $_slide->getParam('paintbrush_disappear',  $_def_disappear)  == 'true' ? ' checked' : '';
		$_blur       = $_slide->getParam('paintbrush_blur',       $_def_blur)       == 'true' ? ' checked' : '';
		$_responsive = $_slide->getParam('paintbrush_responsive', $_def_responsive) == 'true' ? ' checked' : '';
		$_mobile     = $_slide->getParam('paintbrush_mobile',     $_def_mobile)     == 'true' ? ' checked' : '';
		$_fallback   = $_slide->getParam('paintbrush_fallback',   $_def_fallback)   == 'true' ? ' checked' : '';
		$_fixedges   = $_slide->getParam('paintbrush_fixedges',   $_def_fixedges)   == 'true' ? ' checked' : '';
		$_scaleblur  = $_slide->getParam('paintbrush_scaleblur',  $_def_scaleblur)  == 'true' ? ' checked' : '';
		
		$_style      = $_slide->getParam('paintbrush_style',      $_def_style);
		$_size       = $_slide->getParam('paintbrush_size',       $_def_size);
		$_blurAmount = $_slide->getParam('paintbrush_bluramount', $_def_blurAmount);
		$_fadeTime   = $_slide->getParam('paintbrush_fadetime',   $_def_fadeTime);
		$_edgeAmount = $_slide->getParam('paintbrush_edgeamount', $_def_edgeAmount);
		
		$_textDomain = 'rs_' . static::$_Title;
		$_styles     = array('round', 'square', 'butt');
			
		$_showFadeTime   = !empty($_disappear) ? 'block' : 'none';
		$_showBlurAmount = !empty($_blur)      ? 'block' : 'none';
		$_showFallback   = !empty($_mobile)    ? 'block' : 'none';
		$_showEdgeAmount = !empty($_fixedges)  ? 'block' : 'none';
		$_isEnabled      = !empty($_enabled)   ? 'true'  : 'false';
		
		$_source      = $_slide->getParam('paintbrush_source', 'local');
		$_img         = $_slide->getParam('paintbrush_img', '');
		$_transparent = plugins_url( '../assets/images/trans_tile.png', __FILE__);
		
		$_src = empty($_img) ? $_transparent : $_img;
		$_js_img = !empty($_img) ? $_img : $_transparent;
		$_showPreview = $_src !== $_transparent ? 'inline-block' : 'none';
		
		$_markup = '<div id="paintbrush-addon-settings-wrap">
			
			<p>
				<label>Enable/Disable</label>
				<input type="checkbox" id="paintbrush_enabled" name="paintbrush_enabled" class="tp-moderncheckbox"' . $_enabled . ' />
				<span class="description">' . __('Activate the Paint-Brush Add-On for this Slide', $_textDomain) . '</span>
			</p>
			
			<div id="paintbrush-settings">
				
				<ul class="rs-layer-main-image-tabs" style="display: inline-block">
					<li data-content="paintbrush-source" class="paintbrush-menu-item selected">Source</li>
					<li data-content="paintbrush-options" class="paintbrush-menu-item">Settings</li>
					<li data-content="paintbrush-mobile" class="paintbrush-menu-item">Mobile</li>
				</ul>
				
				<div class="paintbrush-container" id="paintbrush-source">
					
					<div class="paintbrush-column">
						
						<p style="line-height: 30px">
							
							<label>Custom Image</label> 
							<input type="radio" name="paintbrush_source" value="local" class="paintbrush-bg" data-container="tp-bgimagewpsrc_paintbrushlocal" id="radio_back_image_beforeafter" ' . checked($_source, 'local', false) . ' /> 
							<span id="tp-bgimagewpsrc_paintbrushlocal" class="bgsrcchanger-div-paintbrush">
							
								<a href="javascript:void(0)" id="button_change_image_paintbrush" class="button-primary revblue" original-title="">
									<i class="fa-icon-wordpress" style="margin-right: 10px"></i>Media Library
								</a>
								
								<a href="javascript:void(0)" id="button_change_image_objlib_paintbrush" class="button-primary revpurple" original-title="">
									<i class="fa-icon-book" style="margin-right: 10px"></i>Object Library
								</a>
								
							</span>
							
						</p>
						
						<p>
							
							<label>Slide Background</label> 
							<input type="radio" id="paintbrush_source_main" name="paintbrush_source" value="main" class="paintbrush-bg" data-container="tp-bgimagewpsrc_paintbrushmain" ' . checked($_source, 'main', false) . ' />
							<span id="tp-bgimagewpsrc_paintbrushmain" class="bgsrcchanger-div-paintbrush description">' . __('Paint the slide\'s main background image', $_textDomain) . '</span>

						</p>

						<p>
					
							<label>Blur Slide Image</label>
							<input type="checkbox" id="paintbrush_blur" name="paintbrush_blur" class="tp-moderncheckbox"' . $_blur . ' onchange="document.getElementById(\'paintbrush-bluramount\').style.display=this.checked ? \'block\' : \'none\'" />
							<span class="description">' . __('Auto-Blur the slide\'s main bg image', $_textDomain) . '</span>
						
						</p>
						
						<div id="paintbrush-bluramount" style="display: ' . $_showBlurAmount . '">
							
							<p>
								<label>' . __('Blur Amount', $_textDomain) . '</label>
								<input type="text" class="paintbrush-min-max" id="paintbrush_bluramount" name="paintbrush_bluramount" value="' . $_blurAmount . '" data-default-value="' . $_blurAmount . '" data-min="1" data-max="100" />
								<span class="description">' . __('The CSS3 Blur filter amount in pixels', $_textDomain) . '</span>
							</p>
							
							<p>
								<label>Responsive Blur</label>
								<input type="checkbox" id="paintbrush_scaleblur" name="paintbrush_scaleblur" class="tp-moderncheckbox"' . $_scaleblur . ' />
								<span class="description" style="white-space: nowrap">' . __('Blur value will be adjusted as the slider resizes', $_textDomain) . '</span>
							</p>
							
							<p>
								<label>Fix Soft Edges</label>
								<input type="checkbox" id="paintbrush_fixedges" name="paintbrush_fixedges" class="tp-moderncheckbox"' . $_fixedges . ' onchange="document.getElementById(\'paintbrush-soft-edges\').style.display=this.checked ? \'block\' : \'none\'" />
								<span class="description" style="white-space: nowrap">' . __('Stretch the image to help remove blur soft edges (Slide transition will be set to "Fade")', $_textDomain) . '</span>
							</p>

							<p id="paintbrush-soft-edges" style="display: ' . $_showEdgeAmount . '">
							
								<label>' . __('Stretch Amount', $_textDomain) . '</label>
								<input type="text" class="paintbrush-min-max" id="paintbrush_edgeamount" name="paintbrush_edgeamount" value="' . $_edgeAmount . '" data-default-value="' . $_edgeAmount . '" data-min="0" data-max="100" />
								<span class="description" style="white-space: nowrap">' . __('Stretch the image (percentage) to help fix blur effect soft edges', $_textDomain) . '</span>
							
							</p>
						
						</div>
						
					</div>
					
					<div class="paintbrush-column">
						
						<div id="paintbrush_bg_preview" style="display: ' . $_showPreview . '">
							
							<div id="paintbrush-bg-img" style="background-image: url(' . $_src . ')"></div>
							<div id="paintbrush_preview_label"><span>Paint-Brush Image</span></div>
							<input type="hidden" id="paintbrush_img" name="paintbrush_img" value="' . $_img . '" />
						
						</div>
						
					</div>
					
					<div style="clear: both"></div>
					
				</div>
				
				<div class="paintbrush-container" id="paintbrush-options" style="display: none">
				
					<p>
						
						<label>' . __('Brush Style', $_textDomain) . '</label>
						<select class="rs-layer-input-field" id="paintbrush_style" class="withlabel" name="paintbrush_style" value="' . $_style . '">';
						
							foreach($_styles as $_styl) {
								
								$_selected = $_styl !== $_style ? '' : ' selected';
								$_markup .= '<option value="' . $_styl . '"' . $_selected . '>' . ucfirst($_styl) . '</option>';
								
							}

						$_markup .= '</select>
						<span class="description">' . __('The HTML5 Canvas lineCap style', $_textDomain) . '</span>
						
					</p>
					
					<p>
					
						<label>' . __('Brush Size', $_textDomain) . '</label>
						<input type="text" class="paintbrush-min-max" id="paintbrush_size" name="paintbrush_size" value="' . $_size . '" data-default-value="' . $_size . '" data-min="5" data-max="500">
						<span class="description">' . __('The HTML5 Canvas lineWidth (in pixels)', $_textDomain) . '</span>
						
					</p>
					
					<p>
						<label>Responsive Size</label>
						<input type="checkbox" id="paintbrush_responsive" name="paintbrush_responsive" class="tp-moderncheckbox"' . $_responsive . ' />
						<span class="description">' . __('Scale the brush size as the slider resizes', $_textDomain) . '</span>
						
					</p>
					
					<p>
						<label>Stroke Disappear</label>
						<input type="checkbox" id="paintbrush_disappear" name="paintbrush_disappear" class="tp-moderncheckbox"' . $_disappear . ' onchange="document.getElementById(\'paintbrush-fadetime\').style.display=this.checked ? \'block\' : \'none\'" />
						<span class="description">' . __('Choose if the paint should fade away after drawing', $_textDomain) . '</span>
						
					</p>
					
					<p id="paintbrush-fadetime" style="display: ' . $_showFadeTime . '">
						
						<label>' . __('Fade time', $_textDomain) . '</label>
						<input type="text" class="paintbrush-min-max" id="paintbrush_fadetime" name="paintbrush_fadetime" value="' . $_fadeTime . '" data-default-value="' . $_fadeTime . '" data-min="100" data-max="10000" >
						<span class="description">' . __('The amount of time before the paint disappears (in milliseconds)', $_textDomain) . '</span>
					
					</p>
					
				</div>
				
				<div class="paintbrush-container" id="paintbrush-mobile" style="display: none">
				
					<p>
							
						<label>Disable on Mobile</label>
						<input type="checkbox" id="paintbrush_mobile" name="paintbrush_mobile" class="tp-moderncheckbox"' . $_mobile . ' onchange="document.getElementById(\'paintbrush-fallback\').style.display=this.checked ? \'block\' : \'none\'" />
						<span class="description">' . __('Recommended for web pages with scrollable content', $_textDomain) . '</span>
						
					</p>
					
					<p id="paintbrush-fallback" style="display: ' . $_showFallback . '">
					
						<label>Use Fallback Image</label>
						<input type="checkbox" id="paintbrush_fallback" name="paintbrush_fallback" class="tp-moderncheckbox"' . $_fallback . ' />
						<span class="description">' . __('Use the Paint-Brush Image below as the slide\'s main background on mobile', $_textDomain) . '</span>
					
					</p>
				
				</div>

			</div>
			
		</div>'; 
		
		static::$_Markup     = $_markup;
		static::$_JavaScript = "var RevAddonPaintBrushEnabled = {
			
			enabled: " . $_isEnabled . ",
			image: '" . $_js_img . "'
				
		};";
		
	}
}
?>