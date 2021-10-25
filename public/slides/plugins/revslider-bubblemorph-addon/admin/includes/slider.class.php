<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

require_once(RS_BUBBLEMORPH_PLUGIN_PATH . 'framework/slider.admin.class.php');

class RsBubblemorphSliderAdmin extends RsAddonBubblemorphSliderAdmin {
	
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
		
		$_enabled       = RevSliderFunctions::getVal($_slider, 'bubblemorph_enabled',   false) == 'true' ? ' checked' : '';
		$_bubble_width  = RevSliderFunctions::getVal($_slider, 'bubblemorph_def_bubblewidth', '100%');
		$_bubble_height = RevSliderFunctions::getVal($_slider, 'bubblemorph_def_bubbleheight', '100%');
		$_color         = RevSliderFunctions::getVal($_slider, 'bubblemorph_def_color', '{&type&:&radial&,&angle&:&0&,&colors&:[{&r&:255,&g&:146,&b&:152,&a&:&1&,&position&:0,&align&:&top&},{&r&:255,&g&:146,&b&:152,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:228,&g&:0,&b&:142,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:228,&g&:0,&b&:142,&a&:&1&,&position&:100,&align&:&top&}]}');
		
		$_max_bubbles   = RevSliderFunctions::getVal($_slider, 'bubblemorph_def_maxbubbles',  '6');
		$_blur_strength = RevSliderFunctions::getVal($_slider, 'bubblemorph_def_blurstrength', '0');
		$_blur_color    = RevSliderFunctions::getVal($_slider, 'bubblemorph_def_blurcolor', 'rgba(0, 0, 0, 0.35)');
		$_blurx         = RevSliderFunctions::getVal($_slider, 'bubblemorph_def_blurx', '0');
		$_blury         = RevSliderFunctions::getVal($_slider, 'bubblemorph_def_blury', '0');
		
		$_bufferx       = RevSliderFunctions::getVal($_slider, 'bubblemorph_def_bufferx', '0');
		$_buffery       = RevSliderFunctions::getVal($_slider, 'bubblemorph_def_buffery', '0');
		$_speedx        = RevSliderFunctions::getVal($_slider, 'bubblemorph_def_speedx', '0.25');
		$_speedy        = RevSliderFunctions::getVal($_slider, 'bubblemorph_def_speedy', '1');
		
		$_border_color  = RevSliderFunctions::getVal($_slider, 'bubblemorph_def_bordercolor', '#000000');
		$_border_size   = RevSliderFunctions::getVal($_slider, 'bubblemorph_def_bordersize',  '0');
		
		$_showSettings = $_enabled ? 'block' : 'none';
		$_textDomain = 'rs_' . static::$_Title;
		
		$_markup = '<div id="bubblemorph-addon-settings">
		
			<span class="label" id="label_bubblemorph_enabled" origtitle="' . __("Enable/Disable the Bubblemorph Add-On for the Slider.<br><br>", $_textDomain) . '">' . __('Enable Bubblemorph Addon', $_textDomain) . '</span>
			<input type="checkbox" class="tp-moderncheckbox withlabel" id="bubblemorph_enabled" name="bubblemorph_enabled"' . $_enabled . ' 
				onchange="document.getElementById(\'bubblemorph-settings\').style.display=this.checked ? \'block\' : \'none\'" />
			
			<div id="bubblemorph-settings" style="display: ' . $_showSettings . '; margin-top: 7px">
				
				<h4>Global Defaults</h4>
				
				<span class="label" id="label_bubblemorph_def_bubblewidth" origtitle="' . __("The BubbleMorph Layer Default Width<br><br>", $_textDomain) . '">' . __('Width', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel" id="bubblemorph_def_bubblewidth" name="bubblemorph_def_bubblewidth" value="' . $_bubble_width . '" />
				<br>
				
				<span class="label" id="label_bubblemorph_def_bubbleheight" origtitle="' . __("The BubbleMorph Layer Default Height<br><br>", $_textDomain) . '">' . __('Height', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel" id="bubblemorph_def_bubbleheight" name="bubblemorph_def_bubbleheight" value="' . $_bubble_height . '" />
				<br>
				
				<span class="label" id="label_bubblemorph_def_colors" origtitle="' . __("The default color for BubbleMorph Layers.<br><br>", $_textDomain) . '">' . __('Color', $_textDomain) . '</span>
				<input type="hidden" class="text-sidebar bubblemorph-addon-color" id="bubblemorph_def_color" name="bubblemorph_def_color" value="' . $_color . '" data-editing="Bubbles Color" />
				<br>
				
				<span class="label" id="label_bubblemorph_def_maxbubbles" origtitle="' . __("Default maximum Morphs<br><br>", $_textDomain) . '">' . __('Max Morphs', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel bubblemorph-min-max" data-min="2" data-max="100" id="bubblemorph_def_maxbubbles" name="bubblemorph_def_maxbubbles" value="' . $_max_bubbles . '" />
				<br>
				
				<span class="label" id="label_bubblemorph_def_bufferx" origtitle="' . __("Helps to prevents bleeding outside the stage<br><br>", $_textDomain) . '">' . __('Edge BufferX', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel bubblemorph-min-max" data-min="0" data-max="1000" id="bubblemorph_def_bufferx" name="bubblemorph_def_bufferx" value="' . $_bufferx . '" />
				<br>
				
				<span class="label" id="label_bubblemorph_def_buffery" origtitle="' . __("Helps to prevents bleeding outside the stage<br><br>", $_textDomain) . '">' . __('Edge BufferY', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel bubblemorph-min-max" data-min="0" data-max="1000" id="bubblemorph_def_buffery" name="bubblemorph_def_buffery" value="' . $_buffery . '" />
				<br>
				
				<span class="label" id="label_bubblemorph_def_speedx" origtitle="' . __("Default Speed = Math.random() * magnifier<br><br>", $_textDomain) . '">' . __('SpeedX Magnifier', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel bubblemorph-min-max" data-min="0.01" data-max="1000" id="bubblemorph_def_speedx" name="bubblemorph_def_speedx" value="' . $_speedx . '" />
				<br>
				
				<span class="label" id="label_bubblemorph_def_speedy" origtitle="' . __("Default Speed = Math.random() * magnifier<br><br>", $_textDomain) . '">' . __('SpeedY Magnifier', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel bubblemorph-min-max" data-min="0.01" data-max="1000" id="bubblemorph_def_speedy" name="bubblemorph_def_speedy" value="' . $_speedy . '" />
				<br>
				
				<span class="label" id="label_bubblemorph_def_blurstrength" origtitle="' . __("Default Shadow Strength<br><br>", $_textDomain) . '">' . __('Shadow Strength', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel bubblemorph-min-max" data-min="0" data-max="1000" id="bubblemorph_def_blurstrength" name="bubblemorph_def_blurstrength" value="' . $_blur_strength . '" />
				<br>
				
				<span class="label" id="label_bubblemorph_def_blurcolors" origtitle="' . __("Default Shadow Color<br><br>", $_textDomain) . '">' . __('Shadow Color', $_textDomain) . '</span>
				<input type="hidden" class="text-sidebar bubblemorph-addon-color" id="bubblemorph_def_blurcolor" name="bubblemorph_def_blurcolor" value="' . $_blur_color . '" data-editing="Shadow Color" data-mode="single" />
				<br>
				
				<span class="label" id="label_bubblemorph_def_blurx" origtitle="' . __("Default left offset for the shadow<br><br>", $_textDomain) . '">' . __('Shadow OffsetX', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel bubblemorph-min-max" data-min="0" data-max="1000" id="bubblemorph_def_blurx" name="bubblemorph_def_blurx" value="' . $_blurx . '" />
				<br>
				
				<span class="label" id="label_bubblemorph_def_blury" origtitle="' . __("Default top offset for the shadow<br><br>", $_textDomain) . '">' . __('Shadow OffsetY', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel bubblemorph-min-max" data-min="0" data-max="1000" id="bubblemorph_def_blury" name="bubblemorph_def_blury" value="' . $_blury . '" />
				<br>
				
				<span class="label" id="label_bubblemorph_def_bordersize" origtitle="' . __("Default Border Size<br><br>", $_textDomain) . '">' . __('Border Size', $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel bubblemorph-min-max" data-min="0" data-max="1000" id="bubblemorph_def_bordersize" name="bubblemorph_def_bordersize" value="' . $_border_size . '" />
				<br>
				
				<span class="label" id="label_bubblemorph_def_bordercolors" origtitle="' . __("Default Border Color<br><br>", $_textDomain) . '">' . __('Border Color', $_textDomain) . '</span>
				<input type="hidden" class="text-sidebar bubblemorph-addon-color" id="bubblemorph_def_bordercolor" name="bubblemorph_def_bordercolor" value="' . $_border_color . '" data-editing="Border Color" data-mode="single" />
				<br>
					
			</div>
			
			<style type="text/css">.setting_box .fa-icon-maxcdn:before {position: relative; top: 3px} .bubblemorph_def_color_wrap {position: relative; top: 8px}</style>
			
		</div>';
		
		static::$_Markup = $_markup;
		static::$_Icon = 'fa-icon-maxcdn';
		static::$_JavaScript = '
			
			jQuery(function() {
				
				jQuery(".bubblemorph-addon-color").each(function() {
				
					var color = jQuery(this),
						val = color.val();
					
					color.tpColorPicker({
						
						wrapClasses: "bubblemorph_def_color_wrap withlabel",
						wrapId: this.id + "s",
						init: function() {color.val(val);},
						change: function(a, b, c) {if(c) a.val(JSON.stringify(c).replace(/\"/g, "&"));}
						
					});
					
				});
				
				// handle inputs with min/max values
				jQuery(".bubblemorph-min-max").on("change", function() {
					
					this.value = Math.max(parseFloat(this.getAttribute("data-min")), 
								 Math.min(parseFloat(this.getAttribute("data-max")), parseFloat(this.value))); 
					
				});
				
			});
		
		';
		
	}
	
}
?>