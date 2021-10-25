<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_BUBBLEMORPH_PLUGIN_PATH . 'framework/slide.admin.class.php');

class RsBubblemorphSlideAdmin extends RsAddonBubblemorphSlideAdmin {
	
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
		
		$_def_color  = $_slider->getParam('bubblemorph_def_color',  '{&type&:&radial&,&angle&:&0&,&colors&:[{&r&:255,&g&:146,&b&:152,&a&:&1&,&position&:0,&align&:&top&},{&r&:255,&g&:146,&b&:152,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:228,&g&:0,&b&:142,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:228,&g&:0,&b&:142,&a&:&1&,&position&:100,&align&:&top&}]}');
		$_def_width  = $_slider->getParam('bubblemorph_def_bubblewidth', '400');
		$_def_height = $_slider->getParam('bubblemorph_def_bubbleheight','400');
		$_def_max    = $_slider->getParam('bubblemorph_def_maxbubbles', '6');
		
		$_def_blurstrength = $_slider->getParam('bubblemorph_def_blurstrength', '0');
		$_def_blurcolor    = $_slider->getParam('bubblemorph_def_blurcolor',    'rgba(0, 0, 0, 0.35)');
		$_def_blurx        = $_slider->getParam('bubblemorph_def_blurx',        '0');
		$_def_blury        = $_slider->getParam('bubblemorph_def_blury',        '0');
		
		$_def_bordercolor  = $_slider->getParam('bubblemorph_def_bordercolor', '#000000');
		$_def_bordersize   = $_slider->getParam('bubblemorph_def_bordersize',  '0');
		
		$_def_bufferx      = $_slider->getParam('bubblemorph_def_bufferx', '0');
		$_def_buffery      = $_slider->getParam('bubblemorph_def_buffery', '0');
		$_def_speedx       = $_slider->getParam('bubblemorph_def_speedx', '0.25');
		$_def_speedy       = $_slider->getParam('bubblemorph_def_speedy', '1');
		
		$_checked1 = $_def_width  != '100%' ? '' : ' checked';
		$_checked2 = $_def_height != '100%' ? '' : ' checked';
		
		$_disabled1 = empty($_checked1) ? '' : ' disabled';
		$_disabled2 = empty($_checked2) ? '' : ' disabled';
		
		if(!empty($_disabled1)) $_def_width  = str_replace('%', '', 400);
		if(!empty($_disabled2)) $_def_height = str_replace('%', '', 400);
		
		$_options  = $_slide->getParam('bubblemorph_globals', false);
		$_defaults = array('enabled' => 'false');
		
		if($_options) $_options = json_decode(stripslashes($_options), true);
		else $_options = $_defaults;
		
		$_options = array_merge($_defaults, $_options);
		$_textDomain = 'rs_' . static::$_Title;
		
		$_markup = '<div id="bubblemorph-addon-wrap">
			
			<div id="bubblemorph-main-settings">
				
				<span class="rs-layer-toolbar-box" style="border-left: none; padding: 6px 10px;  min-width: 80px">
					<span style="position: relative; top: 2px">' . __('Max/Speed/Buffer', $_textDomain) . '</span> 
				</span>
				
				<span class="rs-layer-toolbar-box" style="padding: 6px 20px 0 20px; min-height: 35px">
					<i class="rs-mini-layer-icon eg-icon-picture-1 rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Maximum Morphs', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="bubblemorph_max" 
						id="bubblemorph_max" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Maximum Morphs', $_textDomain) . '" 
						value="' . $_def_max . '" 
						data-bubblemorph-value="' . $_def_max . '" 
						data-selects="Custom||3||4||5||6||10" 
						data-svalues="2||3||4||5||6||10" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
				</span>
				
				<span class="rs-layer-toolbar-box" style="padding: 6px 20px 0 20px; min-height: 35px">
					<i class="rs-mini-layer-icon rs-icon-transition rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Speed X/Y', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="bubblemorph_speedx" 
						id="bubblemorph_speedx" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('SpeedX', $_textDomain) . '" 
						value="' . $_def_speedx . '" 
						data-bubblemorph-value="' . $_def_speedx . '" 
						data-selects="Custom||0.1||0.25||0.5||0.75||1" 
						data-svalues="1.5||0.1||0.25||0.5||0.75||1" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
					<input 
					
						type="text" 
						name="bubblemorph_speedy" 
						id="bubblemorph_speedy" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('SpeedY', $_textDomain) . '" 
						value="' . $_def_speedy . '" 
						data-bubblemorph-value="' . $_def_speedy . '" 
						data-selects="Custom||0.1||0.25||0.5||0.75||1" 
						data-svalues="1.5||0.1||0.25||0.5||0.75||1" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
				</span>
				
				<span class="rs-layer-toolbar-box" style="padding: 6px 20px 0 20px; min-height: 35px">
					<i class="rs-mini-layer-icon eg-icon-move rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Edge Buffer X/Y', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="bubblemorph_bufferx" 
						id="bubblemorph_bufferx" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Edge BufferX', $_textDomain) . '" 
						value="' . $_def_bufferx . '" 
						data-bubblemorph-value="' . $_def_bufferx . '" 
						data-selects="Custom||5||10||25||50||100" 
						data-svalues="20||5||10||25||50||100" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
					<input 
					
						type="text" 
						name="bubblemorph_buffery" 
						id="bubblemorph_buffery" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Edge BufferY', $_textDomain) . '" 
						value="' . $_def_buffery . '" 
						data-bubblemorph-value="' . $_def_buffery . '" 
						data-selects="Custom||5||10||25||50||100" 
						data-svalues="20||5||10||25||50||100" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
				</span>
				
				<div style="height: 1px; background-color: #DDD"></div>
				
				<span class="rs-layer-toolbar-box" style="border-left: none; padding: 6px 10px; min-width: 111px">
					<span style-"position: relative; top: 2px">' . __('Shadow/Border', $_textDomain) . '</span> 
				</span>
				
				<span class="rs-layer-toolbar-box" style="padding: 6px 20px 0 20px; min-height: 35px">
					<i class="rs-mini-layer-icon eg-icon-arrow-combo rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Shadow Strength', $_textDomain) . '" style="margin-left: -1px"></i>
					<input 
					
						type="text" 
						name="bubblemorph_blurstrength" 
						id="bubblemorph_blurstrength" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Shadow Strength', $_textDomain) . '" 
						value="' . $_def_blurstrength . '" 
						data-bubblemorph-value="' . $_def_blurstrength . '" 
						data-selects="Custom||3||4||5||10||20" 
						data-svalues="2||3||4||5||10||20" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
				</span>
				
				<span class="rs-layer-toolbar-box" style="padding: 6px 20px 0 20px; min-height: 35px">
					<i class="rs-mini-layer-icon rs-icon-color rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Shadow Color', $_textDomain) . '"></i>
					<input id="bubblemorph_blurcolor" class="bubblemorph-layer-color" name="bubblemorph_blurcolor" type="hidden" value="' . $_def_blurcolor . '" data-bubblemorph-value="' . $_def_blurcolor . '" data-editing="Shadow Color" data-mode="single" />
				</span>
				
				<span class="rs-layer-toolbar-box" style="padding: 6px 20px 0 20px; min-height: 35px">
					<i class="rs-mini-layer-icon fa-icon-spinner rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Shadow Offset X/Y', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="bubblemorph_blurx" 
						id="bubblemorph_blurx" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Shadow offsetX', $_textDomain) . '" 
						value="' . $_def_blurx . '" 
						data-bubblemorph-value="' . $_def_blurx . '" 
						data-selects="Custom||0||3||5||10||20" 
						data-svalues="2||0||3||5||10||20" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
					<input 
						
						type="text" 
						name="bubblemorph_blury" 
						id="bubblemorph_blury" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Shadow offsetY', $_textDomain) . '" 
						value="' . $_def_blury . '" 
						data-bubblemorph-value="' . $_def_blury . '" 
						data-selects="Custom||0||3||5||10||20" 
						data-svalues="2||0||3||5||10||20" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
				</span>
				
				<span class="rs-layer-toolbar-box" style="padding: 6px 20px 0 20px; min-height: 35px">
					<i class="rs-mini-layer-icon eg-icon-arrow-combo rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Border Size', $_textDomain) . '" style="margin-left: -5px"></i>
					<input 
					
						type="text" 
						name="bubblemorph_bordersize" 
						id="bubblemorph_bordersize" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Border Size', $_textDomain) . '" 
						value="' . $_def_bordersize . '" 
						data-bubblemorph-value="' . $_def_bordersize . '" 
						data-selects="Custom||2||3||5||10||20" 
						data-svalues="1||2||3||5||10||20" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
				</span>
				
				<span class="rs-layer-toolbar-box" style="padding: 6px 20px 0 20px; min-height: 35px">
					<i class="rs-mini-layer-icon rs-icon-color rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Border Color', $_textDomain) . '"></i>
					<input id="bubblemorph_bordercolor" class="bubblemorph-layer-color" name="bubblemorph_bordercolor" type="hidden" value="' . $_def_bordercolor . '" data-bubblemorph-value="' . $_def_bordercolor . '" data-editing="Border Color" data-mode="single" />
				</span>
				
			</div>
			
			<div id="bubblemorph_new_layer_dialog" title="' . __("Add New Bubblemorph Layer", $_textDomain) . '">
				
				<div>
					<span class="bubblemorph-def-title">' . __("Width: ", $_textDomain) . '</span>
					<input id="bubblemorph_new_layer_width" type="text" class="ads-input rs-layer-input-field bubbles-addon-field" value="' . $_def_width . '"' . $_disabled1 . ' /> 
					
					<span class="bubblemorph-def-title full-size">' . __("Full Width: ", $_textDomain) . '</span>
					<input type="checkbox" name="bubbles_width" class="bubbles-addon-checkbox tp-moderncheckbox"' . $_checked1 . '>
				</div>
				
				<div>
					<span class="bubblemorph-def-title">' . __("Height: ", $_textDomain) . '</span>
					<input id="bubblemorph_new_layer_height" type="text" class="ads-input rs-layer-input-field bubbles-addon-field" value="' . $_def_height . '"' . $_disabled2 . ' /> 
					
					<span class="bubblemorph-def-title full-size">' . __("Full Height: ", $_textDomain) . '</span>
					<input type="checkbox" name="bubbles_height" class="bubbles-addon-checkbox tp-moderncheckbox"' . $_checked2 . '>
				</div>
				
				<div>
					<span class="bubblemorph-def-title bubblemorph-color-title">' . __("Color: ", $_textDomain) . '</span>
					<input id="bubblemorph_new_layer_color" type="text" class="ads-input rs-layer-input-field" value="' . $_def_color . '" data-editing="Bubbles Color" />
				</div>
				
				<div>
					<span class="bubblemorph-def-title">' . __("Max Morphs: ", $_textDomain) . '</span>
					<input id="bubblemorph_new_layer_max" type="text" class="ads-input rs-layer-input-field" value="' . $_def_max . '" />
				</div>
				
				<div>
					<span class="bubblemorph-def-title">' . __("Edge BufferX: ", $_textDomain) . '</span>
					<input id="bubblemorph_new_layer_bufferx" type="text" class="ads-input rs-layer-input-field" value="' . $_def_bufferx . '" />
				</div>
				
				<div>
					<span class="bubblemorph-def-title">' . __("Edge BufferY: ", $_textDomain) . '</span>
					<input id="bubblemorph_new_layer_buffery" type="text" class="ads-input rs-layer-input-field" value="' . $_def_buffery . '" />
				</div>
				
				<div>
					<span class="bubblemorph-def-title">' . __("SpeedX: ", $_textDomain) . '</span>
					<input id="bubblemorph_new_layer_speedx" type="text" class="ads-input rs-layer-input-field" value="' . $_def_speedx . '" />
				</div>
				
				<div>
					<span class="bubblemorph-def-title">' . __("SpeedY: ", $_textDomain) . '</span>
					<input id="bubblemorph_new_layer_speedy" type="text" class="ads-input rs-layer-input-field" value="' . $_def_speedy . '" />
				</div>
				
				<div>
					<span class="bubblemorph-def-title">' . __("Shadow Strength: ", $_textDomain) . '</span>
					<input id="bubblemorph_new_layer_blurstrength" type="text" class="ads-input rs-layer-input-field" value="' . $_def_blurstrength . '" />
				</div>
				
				<div>
					<span class="bubblemorph-def-title bubblemorph-color-title">' . __("Shadow Color: ", $_textDomain) . '</span>
					<input id="bubblemorph_new_layer_blurcolor" type="text" class="ads-input rs-layer-input-field bubblemorph-new-layer-color" value="' . $_def_blurcolor . '" data-editing="Shadow Color" />
				</div>
				
				<div>
					<span class="bubblemorph-def-title">' . __("Shadow offsetX: ", $_textDomain) . '</span>
					<input id="bubblemorph_new_layer_blurx" type="text" class="ads-input rs-layer-input-field" value="' . $_def_blurx . '" />
				</div>
				
				<div>
					<span class="bubblemorph-def-title">' . __("Shadow offsetY: ", $_textDomain) . '</span>
					<input id="bubblemorph_new_layer_blury" type="text" class="ads-input rs-layer-input-field" value="' . $_def_blury . '" />
				</div>
				
				<div>
					<span class="bubblemorph-def-title">' . __("Border Size: ", $_textDomain) . '</span>
					<input id="bubblemorph_new_layer_bordersize" type="text" class="ads-input rs-layer-input-field" value="' . $_def_bordersize . '" />
				</div>
				
				<div>
					<span class="bubblemorph-def-title bubblemorph-color-title">' . __("Border Color: ", $_textDomain) . '</span>
					<input id="bubblemorph_new_layer_bordercolor" type="text" class="ads-input rs-layer-input-field bubblemorph-new-layer-color" value="' . $_def_bordercolor . '" data-editing="Border Color" />
				</div>
				
			</div>
			
		</div>';
		
		static::$_Markup     = $_markup;
		static::$_JavaScript = '

		var RsAddonBubbleMorph = {
			
			layers: {
				
				max: "' . $_def_max . '",
				blurstrength: "' . $_def_blurstrength . '",
				blurcolor: "' . $_def_blurcolor . '",
				blurx: "' . $_def_blurx . '",
				blury: "' . $_def_blury . '",
				bordercolor: "' . $_def_bordercolor . '",
				bordersize: "' . $_def_bordersize . '",
				bufferx: "' . $_def_bufferx . '",
				buffery: "' . $_def_buffery . '",
				speedx: "' . $_def_speedx . '",
				speedy: "' . $_def_speedy . '"
					
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