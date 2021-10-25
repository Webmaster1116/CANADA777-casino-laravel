<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_EXPLODINGLAYERS_PLUGIN_PATH . 'framework/slide.admin.class.php');

class RsExplodinglayersSlideAdmin extends RsAddonExplodinglayersSlideAdmin {
	
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
		
		$_type_in  = $_slider->getParam('explodinglayers_type_in',  'circle');
		$_style_in = $_slider->getParam('explodinglayers_style_in', 'fill');
		$_size_in  = $_slider->getParam('explodinglayers_size_in',  '5');
		$_color_in = $_slider->getParam('explodinglayers_color_in', '#000000');
		
		$_speed_in     = $_slider->getParam('explodinglayers_speed_in',     '1');
		$_density_in   = $_slider->getParam('explodinglayers_density_in',   '1');
		$_power_in     = $_slider->getParam('explodinglayers_power_in',     '2');
		$_padding_in   = $_slider->getParam('explodinglayers_padding_in',   '150');
		$_direction_in = $_slider->getParam('explodinglayers_direction_in', 'left');
		
		$_type_out  = $_slider->getParam('explodinglayers_type_out',  'circle');
		$_style_out = $_slider->getParam('explodinglayers_style_out', 'fill');
		$_size_out  = $_slider->getParam('explodinglayers_size_out',  '5');
		$_color_out = $_slider->getParam('explodinglayers_color_out', '#000000');
		
		$_speed_out     = $_slider->getParam('explodinglayers_speed_out',     '1');
		$_density_out   = $_slider->getParam('explodinglayers_density_out',   '1');
		$_power_out     = $_slider->getParam('explodinglayers_power_out',     '2');
		$_padding_out   = $_slider->getParam('explodinglayers_padding_out',   '150');
		$_direction_out = $_slider->getParam('explodinglayers_direction_out', 'left');
		
		$_randomsize_in   = $_slider->getParam('explodinglayers_randomsize_in',   false) == 'true' ? 'on' : 'off';
		$_randomsize_out  = $_slider->getParam('explodinglayers_randomsize_out',  false) == 'true' ? 'on' : 'off';
		$_randomspeed_in  = $_slider->getParam('explodinglayers_randomspeed_in',  false) == 'true' ? 'on' : 'off';
		$_randomspeed_out = $_slider->getParam('explodinglayers_randomspeed_out', false) == 'true' ? 'on' : 'off';
		$_sync_in         = $_slider->getParam('explodinglayers_sync_in',         false) == 'true' ? 'on' : 'off';
		
		$_randomsize_in_checked   = $_randomsize_in   === 'on' ? ' checked' : '';
		$_randomsize_out_checked  = $_randomsize_out  === 'on' ? ' checked' : '';
		$_randomspeed_in_checked  = $_randomspeed_in  === 'on' ? ' checked' : '';
		$_randomspeed_out_checked = $_randomspeed_out === 'on' ? ' checked' : '';
		$_sync_in_checked         = $_sync_in         === 'on' ? ' checked' : '';
		
		$_styles     = array('fill', 'stroke');
		$_directions = array('left', 'right', 'top', 'bottom');
		
		$_svgStart = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#34495E" d="';
		$_svgEnd   = '"></svg>';
		
		$_icons      = RsExplodingLayersSvg::$_SVGs;
		$_textDomain = 'rs_' . static::$_Title;
		
		$_markup = '<div id="explodinglayers-addon-wrap">
			
			<div id="explodinglayers-main-settings-in" class="explodinglayers-main-settings explodinglayers_hideable">
				
				<ul class="rs-layer-animation-settings-tabs">
					<li data-content="shape" class="selected">Particle</li>
					<li data-content="customshape">Custom Particle</li>
					<li data-content="style">Style / Animation</li>
				</ul>
				
				<div class="explodinglayers-container explodinglayers-shape rs-layer-toolbar-box">
					
					<span data-icon="circle" class="explodinglayers-icon explodinglayers-selected"><span class="explodinglayers-circle"></span></span>';
					foreach($_icons as $key => $val) {
						$_markup .= '<span data-icon="' . $key . '" class="explodinglayers-icon">' . $_svgStart . $val . $_svgEnd . '</span>';
					}
					
					$_markup .= '<input type="hidden" id="explodinglayers_type_in" name="explodinglayers_type_in" class="explodinglayers-type" />

				</div>
				
				<div class="explodinglayers-container explodinglayers-customshape rs-layer-toolbar-box" style="display: none">
					
					<span style="display: none" data-icon="custom" class="explodinglayers-icon explodinglayers-icon-custom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path class="explodinglayers-path" fill="#34495E" d=""></path></svg></span>
					<a href="javascript:void(0)" class="explodinglayers_custom_shape"><i class="fa-icon-book"></i><span class="add-layer-txt">Object Library</span></a>
			
				</div>
				
				<div class="explodinglayers-container explodinglayers-style rs-layer-toolbar-box" style="display: none">
					
					<i class="rs-mini-layer-icon rs-icon-droplet rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Particle Style', $_textDomain) . '"></i>
					<select class="rs-layer-input-field tipsy_enabled_top" id="explodinglayers_style_in" name="explodinglayers_style_in" original-title="' . __('Particle Style', $_textDomain) . '">';
						foreach($_styles as $_val) {
							$_markup .= '<option value="' . $_val . '">' . ucfirst($_val) . '</option>';
						}
					$_markup .= '</select>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon fa-icon-spinner rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Particle Size', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="explodinglayers_size_in" 
						id="explodinglayers_size_in" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Particle Size', $_textDomain) . '" 
						value="' . $_size_in . '" 
						data-explodinglayers-value="' . $_size_in . '" 
						data-selects="Custom||10||15||20||25||30" 
						data-svalues="5||10||15||20||25||30" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon eg-icon-resize-full-2 rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Randomize Size', $_textDomain) . '"></i>
					<input type="checkbox" id="explodinglayers_randomsize_in" name="explodinglayers_randomsize_in" class="tipsy_enabled_top tp-moderncheckbox explodinglayers-activate-toolbox" original-title="' . __('Randomize Size', $_textDomain) . '"' . $_randomsize_in_checked . '>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon rs-icon-color rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Particles Color', $_textDomain) . '"></i>
					<input id="explodinglayers_color_in" class="explodinglayers-layer-color" name="explodinglayers_color_in" type="hidden" value="' . $_color_in . '" data-explodinglayers-value="' . $_color_in . '" data-editing="Particles Color" />
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon eg-icon-move rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Explosion Direction', $_textDomain) . '"></i>
					<select class="rs-layer-input-field tipsy_enabled_top" id="explodinglayers_direction_in" name="explodinglayers_direction_in" original-title="' . __('Explosion Direction', $_textDomain) . '">';
						foreach($_directions as $_val) {
							$_markup .= '<option value="' . $_val . '">' . ucfirst($_val) . '</option>';
						}
					$_markup .= '</select>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon fa-icon-plug rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Anti-Gravity', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="explodinglayers_speed_in" 
						id="explodinglayers_speed_in" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Anti-Gravity', $_textDomain) . '" 
						value="' . $_speed_in . '" 
						data-explodinglayers-value="' . $_speed_in . '" 
						data-selects="Custom||2||3||4||5||10" 
						data-svalues="1||2||3||4||5||10" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon rs-icon-zoffset rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Randomize Gravity', $_textDomain) . '"></i>
					<input type="checkbox" id="explodinglayers_randomspeed_in" name="explodinglayers_randomspeed_in" class="tipsy_enabled_top tp-moderncheckbox explodinglayers-activate-toolbox" original-title="' . __('Randomize Gravity', $_textDomain) . '"' . $_randomspeed_in_checked . '>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon fa-icon-flask rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Particle Density', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="explodinglayers_density_in" 
						id="explodinglayers_density_in" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Particle Density', $_textDomain) . '" 
						value="' . $_density_in . '" 
						data-explodinglayers-value="' . $_density_in . '" 
						data-selects="Custom||2||3||4||5||10" 
						data-svalues="1||2||3||4||5||10" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon fa-icon-flash rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Particle Swarm', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="explodinglayers_power_in" 
						id="explodinglayers_power_in" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Particle Swarm', $_textDomain) . '" 
						value="' . $_power_in . '" 
						data-explodinglayers-value="' . $_power_in . '" 
						data-selects="Custom||2||3||4||5||10" 
						data-svalues="1||2||3||4||5||10" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon rs-icon-padding rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Explosion Padding', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="explodinglayers_padding_in" 
						id="explodinglayers_padding_in" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Explosion Padding', $_textDomain) . '" 
						value="' . $_padding_in . '" 
						data-explodinglayers-value="' . $_padding_in . '" 
						data-selects="Custom||0||50||100||150||200" 
						data-svalues="125||0||50||100||150||200" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon rs-icon-chooser-2 rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Sync Helper', $_textDomain) . '"></i>
					<input type="checkbox" id="explodinglayers_sync_in" name="explodinglayers_sync_in" class="tipsy_enabled_top tp-moderncheckbox explodinglayers-activate-toolbox" original-title="' . __('Sync Helper', $_textDomain) . '"' . $_sync_in_checked . '>
					
				</div>
				
			</div>
			
			<div id="explodinglayers-main-settings-out" class="explodinglayers-main-settings explodinglayers_hideable">
				
				<ul class="rs-layer-animation-settings-tabs">
					<li data-content="shape" class="selected">Shape</li>
					<li data-content="customshape">Custom Particle</li>
					<li data-content="style">Style / Animation</li>
				</ul>
				
				<div class="explodinglayers-container explodinglayers-shape rs-layer-toolbar-box">
				
					<span data-icon="circle" class="explodinglayers-icon explodinglayers-selected"><span class="explodinglayers-circle"></span></span>';
					foreach($_icons as $key => $val) {
						$_markup .= '<span data-icon="' . $key . '" class="explodinglayers-icon">' . $_svgStart . $val . $_svgEnd . '</span>';
					}
					
					$_markup .= '<input type="hidden" id="explodinglayers_type_out" name="explodinglayers_type_out" class="explodinglayers-type" />
				
				</div>
				
				<div class="explodinglayers-container explodinglayers-customshape rs-layer-toolbar-box" style="display: none">
					
					<span style="display: none" data-icon="custom" class="explodinglayers-icon explodinglayers-icon-custom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path class="explodinglayers-path" fill="#34495E" d=""></path></svg></span>
					<a href="javascript:void(0)" class="explodinglayers_custom_shape"><i class="fa-icon-book"></i><span class="add-layer-txt">Object Library</span></a>
			
				</div>
				
				<div class="explodinglayers-container explodinglayers-style rs-layer-toolbar-box" style="display: none">
					
					<i class="rs-mini-layer-icon rs-icon-droplet rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Particle Style', $_textDomain) . '"></i>
					<select class="rs-layer-input-field tipsy_enabled_top" id="explodinglayers_style_out" name="explodinglayers_style_out" original-title="' . __('Particle Style', $_textDomain) . '">';
						foreach($_styles as $_val) {
							$_markup .= '<option value="' . $_val . '">' . ucfirst($_val) . '</option>';
						}
					$_markup .= '</select>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon fa-icon-spinner rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Particle Size', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="explodinglayers_size_out" 
						id="explodinglayers_size_out" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Particle Size', $_textDomain) . '" 
						value="' . $_size_out . '" 
						data-explodinglayers-value="' . $_size_out . '" 
						data-selects="Custom||10||15||20||25||30" 
						data-svalues="5||10||15||20||25||30" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon eg-icon-resize-full-2 rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Randomize Size', $_textDomain) . '"></i>
					<input type="checkbox" id="explodinglayers_randomsize_out" name="explodinglayers_randomsize_out" class="tipsy_enabled_top tp-moderncheckbox explodinglayers-activate-toolbox" original-title="' . __('Randomize Size', $_textDomain) . '"' . $_randomsize_out_checked . '>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon rs-icon-color rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Particles Color', $_textDomain) . '"></i>
					<input id="explodinglayers_color_out" class="explodinglayers-layer-color" name="explodinglayers_color_out" type="hidden" value="' . $_color_out . '" data-explodinglayers-value="' . $_color_out . '" data-editing="Particles Color" />
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon eg-icon-move rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Explosion Direction', $_textDomain) . '"></i>
					<select class="rs-layer-input-field tipsy_enabled_top" id="explodinglayers_direction_out" name="explodinglayers_direction_out" original-title="' . __('Explosion Direction', $_textDomain) . '">';
						foreach($_directions as $_val) {
							$_markup .= '<option value="' . $_val . '">' . ucfirst($_val) . '</option>';
						}
					$_markup .= '</select>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon fa-icon-plug rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Anti-Gravity', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="explodinglayers_speed_out" 
						id="explodinglayers_speed_out" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Anti-Gravity', $_textDomain) . '" 
						value="' . $_speed_out . '" 
						data-explodinglayers-value="' . $_speed_out . '" 
						data-selects="Custom||2||3||4||5||10" 
						data-svalues="1||2||3||4||5||10" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon rs-icon-zoffset rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Randomize Gravity', $_textDomain) . '"></i>
					<input type="checkbox" id="explodinglayers_randomspeed_out" name="explodinglayers_randomspeed_out" class="tipsy_enabled_top tp-moderncheckbox explodinglayers-activate-toolbox" original-title="' . __('Randomize Gravity', $_textDomain) . '"' . $_randomspeed_out_checked . '>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon fa-icon-flask rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Particle Density', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="explodinglayers_density_out" 
						id="explodinglayers_density_out" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Particle Density', $_textDomain) . '" 
						value="' . $_density_out . '" 
						data-explodinglayers-value="' . $_density_out . '" 
						data-selects="Custom||2||3||4||5||10" 
						data-svalues="1||2||3||4||5||10" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon fa-icon-flash rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Particle Swarm', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="explodinglayers_power_out" 
						id="explodinglayers_power_out" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Particle Swarm', $_textDomain) . '" 
						value="' . $_power_out . '" 
						data-explodinglayers-value="' . $_power_out . '" 
						data-selects="Custom||2||3||4||5||10" 
						data-svalues="1||2||3||4||5||10" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
					
					<span class="rs-layer-toolbar-space"></span>
					
					<i class="rs-mini-layer-icon rs-icon-padding rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Explosion Padding', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="explodinglayers_padding_out" 
						id="explodinglayers_padding_out" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Explosion Padding', $_textDomain) . '" 
						value="' . $_padding_out . '" 
						data-explodinglayers-value="' . $_padding_out . '" 
						data-selects="Custom||0||50||100||150||200" 
						data-svalues="125||0||50||100||150||200" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>

				</div>
				
			</div>
			
			<span id="explodinglayers-activate-wrap-in" class="explodinglayers-activate-wrap rs-layer-toolbar-box" style="margin-left: 3px">
				<i class="rs-mini-layer-icon fa-icon-legal rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Activate Exploding Layers', $_textDomain) . '"></i>
				<input type="checkbox" id="explodinglayers-activate-in" class="explodinglayers-activate tipsy_enabled_top tp-moderncheckbox" original-title="' . __('Activate Exploding Layers', $_textDomain) . '">
			</span>
			<span id="explodinglayers-activate-wrap-out" class="explodinglayers-activate-wrap rs-layer-toolbar-box" style="margin-left: 3px">
				<i class="rs-mini-layer-icon fa-icon-legal rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Activate Exploding Layers', $_textDomain) . '"></i>
				<input type="checkbox" id="explodinglayers-activate-out" class="explodinglayers-activate tipsy_enabled_top tp-moderncheckbox" original-title="' . __('Activate Exploding Layers', $_textDomain) . '">
			</span>
			
		</div>';
		
		static::$_Markup     = $_markup;
		static::$_JavaScript = '

		var RsAddonExplodingLayers = {
			
			layers: {
				
				type_in:         "' . $_type_in        . '",
				style_in:        "' . $_style_in       . '",
				size_in:         "' . $_size_in        . '",
				color_in:        "' . $_color_in       . '",
				speed_in:        "' . $_speed_in       . '",
				density_in:      "' . $_density_in     . '",
				power_in:        "' . $_power_in       . '",
				padding_in:      "' . $_padding_in     . '",
				direction_in:    "' . $_direction_in   . '",
				randomsize_in:   "' . $_randomsize_in  . '",
				randomspeed_in:  "' . $_randomspeed_in . '",
				sync_in:         "' . $_sync_in        . '",
				
				type_out:        "' . $_type_out        . '",
				style_out:       "' . $_style_out       . '",
				size_out:        "' . $_size_out        . '",
				color_out:       "' . $_color_out       . '",
				speed_out:       "' . $_speed_out       . '",
				density_out:     "' . $_density_out     . '",
				power_out:       "' . $_power_out       . '",
				padding_out:     "' . $_padding_out     . '",
				direction_out:   "' . $_direction_out   . '",
				randomsize_out:  "' . $_randomsize_out  . '",
				randomspeed_out: "' . $_randomspeed_out . '",
					
			}
			
		};';
		
	}
}
?>