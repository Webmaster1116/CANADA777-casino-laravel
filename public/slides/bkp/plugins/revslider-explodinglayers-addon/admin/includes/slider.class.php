<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

require_once(RS_EXPLODINGLAYERS_PLUGIN_PATH . 'framework/slider.admin.class.php');

class RsExplodinglayersSliderAdmin extends RsAddonExplodinglayersSliderAdmin {
	
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
		
		$_enabled       = RevSliderFunctions::getVal($_slider, 'explodinglayers_enabled', false) == 'true' ? ' checked' : '';
		
		$_type_in       = RevSliderFunctions::getVal($_slider, 'explodinglayers_type_in', 'circle');
		$_style_in      = RevSliderFunctions::getVal($_slider, 'explodinglayers_style_in', 'fill');
		
		$_size_in       = RevSliderFunctions::getVal($_slider, 'explodinglayers_size_in', '5');
		$_speed_in      = RevSliderFunctions::getVal($_slider, 'explodinglayers_speed_in', '1');
		$_color_in      = RevSliderFunctions::getVal($_slider, 'explodinglayers_color_in', '#000000');
		
		$_density_in    = RevSliderFunctions::getVal($_slider, 'explodinglayers_density_in', '1');
		$_power_in      = RevSliderFunctions::getVal($_slider, 'explodinglayers_power_in', '2');
		$_padding_in    = RevSliderFunctions::getVal($_slider, 'explodinglayers_padding_in', '150');
		$_direction_in  = RevSliderFunctions::getVal($_slider, 'explodinglayers_direction_in', 'left');
		
		$_type_out      = RevSliderFunctions::getVal($_slider, 'explodinglayers_type_out', 'circle');
		$_style_out     = RevSliderFunctions::getVal($_slider, 'explodinglayers_style_out', 'fill');
		
		$_size_out      = RevSliderFunctions::getVal($_slider, 'explodinglayers_size_out', '5');
		$_speed_out     = RevSliderFunctions::getVal($_slider, 'explodinglayers_speed_out', '1');
		$_color_out     = RevSliderFunctions::getVal($_slider, 'explodinglayers_color_out', '#000000');
		
		$_density_out   = RevSliderFunctions::getVal($_slider, 'explodinglayers_density_out', '1');
		$_power_out     = RevSliderFunctions::getVal($_slider, 'explodinglayers_power_out', '2');
		$_padding_out   = RevSliderFunctions::getVal($_slider, 'explodinglayers_padding_out', '150');
		$_direction_out = RevSliderFunctions::getVal($_slider, 'explodinglayers_direction_out', 'left');
		
		$_randomsize_in   = RevSliderFunctions::getVal($_slider, 'explodinglayers_randomsize_in',   false) == 'true' ? ' checked' : '';
		$_randomsize_out  = RevSliderFunctions::getVal($_slider, 'explodinglayers_randomsize_out',  false) == 'true' ? ' checked' : '';
		$_randomspeed_in  = RevSliderFunctions::getVal($_slider, 'explodinglayers_randomspeed_in',  false) == 'true' ? ' checked' : '';
		$_randomspeed_out = RevSliderFunctions::getVal($_slider, 'explodinglayers_randomspeed_out', false) == 'true' ? ' checked' : '';
		$_sync_in         = RevSliderFunctions::getVal($_slider, 'explodinglayers_sync_in',         false) == 'true' ? ' checked' : '';
		
		$_showSettings = $_enabled ? 'block' : 'none';
		$_textDomain   = 'rs_' . static::$_Title;
		
		$_styles     = array('fill', 'stroke');
		$_directions = array('left', 'right', 'top', 'bottom');
		
		$_svgStart = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#34495E" d="';
		$_svgEnd   = '"></svg>';
		
		$_icons         = RsExplodingLayersSvg::$_SVGs;
		$_selectedClass = $_type_in !== 'circle' ? '' : ' explodinglayers-selected';
		
		$_markup = '<div id="explodinglayers-addon-settings">
		
			<span class="label" id="label_explodinglayers_enabled" origtitle="' . __("Enable/Disable the Explodinglayers Add-On for the Slider.<br><br>", $_textDomain) . '">' . __('Enable Addon', $_textDomain) . '</span>
			<input type="checkbox" class="tp-moderncheckbox withlabel" id="explodinglayers_enabled" name="explodinglayers_enabled"' . $_enabled . ' 
				onchange="document.getElementById(\'explodinglayers-settings\').style.display=this.checked ? \'block\' : \'none\'" />
			
			<div id="explodinglayers-settings" style="display: ' . $_showSettings . '; margin-top: 7px">
				
				<h4>' . __('Global Default Options', $_textDomain) . '</h4>
				
				<ul class="main-options-small-tabs" style="display:inline-block">
					<li data-content="#explodinglayers-in" class="selected">Animation In</li>
					<li data-content="#explodinglayers-out" class="">Animation Out</li>
				</ul>
				
				<div id="explodinglayers-in">
					
					<span style="display: block; overflow: hidden; max-height: 0" class="label" id="label_explodinglayers-type-in" origtitle="' . __("The Shape to draw for each particle<br><br>", $_textDomain) . '">' . __('Particle Shape', $_textDomain) . '</span>
					<div class="explodinglayers-shape withlabel" id="explodinglayers-type-in">
					
						<span data-icon="circle" class="explodinglayers-icon' . $_selectedClass . '"><span class="explodinglayers-circle"></span></span>';
						foreach($_icons as $key => $val) {
							$_selectedClass = $key !== $_type_in ? '' : ' explodinglayers-selected';
							$_markup .= '<span data-icon="' . $key . '" class="explodinglayers-icon' . $_selectedClass . '">' . $_svgStart . $val . $_svgEnd . '</span>';
						}
						
						$_markup .= '<input type="hidden" id="explodinglayers_type_in" name="explodinglayers_type_in" class="explodinglayers-type" value="' . $_type_in . '" />
					
					</div>
					
					<span class="label" id="label_explodinglayers_style_in" origtitle="' . __("Solid Color = Fill<br>Transparent w/ Border = Stroke<br><br>", $_textDomain) . '">' . __('Particle Style', $_textDomain) . '</span>
					<select class="withlabel" id="explodinglayers_style_in" name="explodinglayers_style_in" value="' . $_style_in . '">';
						
						foreach($_styles as $_val) {
							$_checked = $_val !== $_style_in ? '' : ' selected';
							$_markup .= '<option value="' . $_val . '"' . $_checked . '>' . ucfirst($_val) . '</option>';
						}
						
					$_markup .= '</select>
					
					<span class="label" id="label_explodinglayers_size_in" origtitle="' . __("Size in pixels for each shape<br><br>", $_textDomain) . '">' . __('Particle Size', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel explodinglayers-min-max" data-min="1" data-max="100" id="explodinglayers_size_in" name="explodinglayers_size_in" value="' . $_size_in . '" />
					<br>
					
					<span class="label" id="label_explodinglayers_randomsize_in" origtitle="' . __("Randomize the size value by a power of 50%<br><br>", $_textDomain) . '">' . __('Randomize Size', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="explodinglayers_randomsize_in" name="explodinglayers_randomsize_in"' . $_randomsize_in . ' />
					<br>
					
					<span class="label" id="label_explodinglayers_color_ins" origtitle="' . __("The color for the drawn shape<br><br>", $_textDomain) . '">' . __('Particle Color', $_textDomain) . '</span>
					<input type="hidden" class="text-sidebar explodinglayers-addon-color" id="explodinglayers_color_in" name="explodinglayers_color_in" value="' . $_color_in . '" data-editing="Particles Color" />
					<br>
					
					<span class="label" id="label_explodinglayers_speed_in" origtitle="' . __("The gravity power for each particle<br><br>", $_textDomain) . '">' . __('Anti-Gravity', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel explodinglayers-min-max" data-min="1" data-max="100" id="explodinglayers_speed_in" name="explodinglayers_speed_in" value="' . $_speed_in . '" />
					<br>
					
					<span class="label" id="label_explodinglayers_randomspeed_in" origtitle="' . __("Randomize the gravity value by a power of 50%<br><br>", $_textDomain) . '">' . __('Randomize Gravity', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="explodinglayers_randomspeed_in" name="explodinglayers_randomspeed_in"' . $_randomspeed_in . ' />
					<br>
					
					<span class="label" id="label_explodinglayers_density_in" origtitle="' . __("Increse the number of particles by a power of this value<br><br>", $_textDomain) . '">' . __('Particle Density', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel explodinglayers-min-max" data-min="1" data-max="100" " id="explodinglayers_density_in" name="explodinglayers_density_in" value="' . $_density_in . '" />
					<br>
					
					<span class="label" id="label_explodinglayers_direction_in" origtitle="' . __("Wipe the element into and out of view from this direction<br><br>", $_textDomain) . '">' . __('Animation Direction', $_textDomain) . '</span>
					<select class="withlabel" id="explodinglayers_direction_in" value="' . $_direction_in . '">';
						
						foreach($_directions as $_val) {
							$_checked = $_val !== $_direction_in ? '' : ' selected';
							$_markup .= '<option value="' . $_val . '"' . $_checked . '>' . ucfirst($_val) . '</option>';
						}
						
					$_markup .= '</select>
					
					<span class="label" id="label_explodinglayers_power_in" origtitle="' . __("Creates a tubular swarming effeft by a power of this value<br><br>", $_textDomain) . '">' . __('Particle Swarm', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel explodinglayers-min-max" data-min="1" data-max="100" " id="explodinglayers_power_in" name="explodinglayers_power_in" value="' . $_power_in . '" />
					<br>
					
					<span class="label" id="label_explodinglayers_padding_in" origtitle="' . __("Increases the bounding box where the particles are visible outside the element<br><br>", $_textDomain) . '">' . __('Explosion Padding', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel explodinglayers-min-max" data-min="0" data-max="2000" " id="explodinglayers_padding_in" name="explodinglayers_padding_in" value="' . $_padding_in . '" />
					<br>
					
					<span class="label" id="label_explodinglayers_sync_in" origtitle="' . __("Useful for better syncing animations when the frame rate drops<br><br>", $_textDomain) . '">' . __('Sync Helper', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="explodinglayers_sync_in" name="explodinglayers_sync_in"' . $_sync_in . ' />
					<br>
					
				</div>
				
				<div id="explodinglayers-out" style="display: none">
					
					<span style="display: block; overflow: hidden; max-height: 0" class="label" id="label_explodinglayers-type-out" origtitle="' . __("The Shape to draw for each particle<br><br>", $_textDomain) . '">' . __('Particle Shape', $_textDomain) . '</span>
					<div class="explodinglayers-shape withlabel" id="explodinglayers-type-out">';
						
						$_selectedClass = $_type_out !== 'circle' ? '' : ' explodinglayers-selected';
						$_markup .= '<span data-icon="circle" class="explodinglayers-icon' . $_selectedClass . '"><span class="explodinglayers-circle"></span></span>';
						
						foreach($_icons as $key => $val) {
							$_selectedClass = $key !== $_type_out ? '' : ' explodinglayers-selected';
							$_markup .= '<span data-icon="' . $key . '" class="explodinglayers-icon' . $_selectedClass . '">' . $_svgStart . $val . $_svgEnd . '</span>';
						}
						
						$_markup .= '<input type="hidden" id="explodinglayers_type_out" name="explodinglayers_type_out" class="explodinglayers-type" value="' . $_type_out . '" />
					
					</div>
					
					<span class="label" id="label_explodinglayers_style_out" origtitle="' . __("Solid Color = Fill<br>Transparent w/ Border = Stroke<br><br>", $_textDomain) . '">' . __('Particle Style', $_textDomain) . '</span>
					<select class="withlabel" id="explodinglayers_style_out" name="explodinglayers_style_out" value="' . $_style_out . '">';
						
						foreach($_styles as $_val) {
							$_checked = $_val !== $_style_out ? '' : ' selected';
							$_markup .= '<option value="' . $_val . '"' . $_checked . '>' . ucfirst($_val) . '</option>';
						}
						
					$_markup .= '</select>
					
					<span class="label" id="label_explodinglayers_size_out" origtitle="' . __("Size in pixels for each shape<br><br>", $_textDomain) . '">' . __('Particle Size', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel explodinglayers-min-max" data-min="1" data-max="100" id="explodinglayers_size_out" name="explodinglayers_size_out" value="' . $_size_out . '" />
					<br>
					
					<span class="label" id="label_explodinglayers_randomsize_out" origtitle="' . __("Randomize the size value by a power of 50%<br><br>", $_textDomain) . '">' . __('Randomize Size', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="explodinglayers_randomsize_out" name="explodinglayers_randomsize_out"' . $_randomsize_out . ' />
					<br>
					
					<span class="label" id="label_explodinglayers_color_outs" origtitle="' . __("The color for the drawn shape<br><br>", $_textDomain) . '">' . __('Particle Color', $_textDomain) . '</span>
					<input type="hidden" class="text-sidebar explodinglayers-addon-color" id="explodinglayers_color_out" name="explodinglayers_color_out" value="' . $_color_out . '" data-editing="Particles Color" />
					<br>
					
					<span class="label" id="label_explodinglayers_speed_out" origtitle="' . __("The gravity power for each particle<br><br>", $_textDomain) . '">' . __('Anti-Gravity', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel explodinglayers-min-max" data-min="1" data-max="100" id="explodinglayers_speed_out" name="explodinglayers_speed_out" value="' . $_speed_out . '" />
					<br>
					
					<span class="label" id="label_explodinglayers_randomspeed_out" origtitle="' . __("Randomize the gravity value by a power of 50%<br><br>", $_textDomain) . '">' . __('Randomize Grvity', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="explodinglayers_randomspeed_out" name="explodinglayers_randomspeed_out"' . $_randomspeed_out . ' />
					<br>
					
					<span class="label" id="label_explodinglayers_density_out" origtitle="' . __("Increse the number of particles by a power of this value<br><br>", $_textDomain) . '">' . __('Particle Density', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel explodinglayers-min-max" data-min="1" data-max="100" " id="explodinglayers_density_out" name="explodinglayers_density_out" value="' . $_density_out . '" />
					<br>
					
					<span class="label" id="label_explodinglayers_direction_out" origtitle="' . __("<br><br>", $_textDomain) . '">' . __('Animation Direction', $_textDomain) . '</span>
					<select class="withlabel" id="explodinglayers_direction_out" value="' . $_direction_out . '">';
						
						foreach($_directions as $_val) {
							$_checked = $_val !== $_direction_out ? '' : ' selected';
							$_markup .= '<option value="' . $_val . '"' . $_checked . '>' . ucfirst($_val) . '</option>';
						}
						
					$_markup .= '</select>
					
					<span class="label" id="label_explodinglayers_power_out" origtitle="' . __("Creates a tubular swarming effeft by a power of this value<br><br>", $_textDomain) . '">' . __('Particle Swarm', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel explodinglayers-min-max" data-min="1" data-max="100" " id="explodinglayers_power_out" name="explodinglayers_power_out" value="' . $_power_out . '" />
					<br>
					
					<span class="label" id="label_explodinglayers_padding_out" origtitle="' . __("Increases the bounding box where the particles are visible outside the element<br><br>", $_textDomain) . '">' . __('Explosion Padding', $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel explodinglayers-min-max" data-min="0" data-max="2000" " id="explodinglayers_padding_out" name="explodinglayers_padding_out" value="' . $_padding_out . '" />
					
				</div>
					
			</div>
			
			<style type="text/css">
			
				.setting_box .fa-icon-legal:before {position: relative; top: 3px} 
				.explodinglayers_def_color_wrap {position: relative; top: 8px}
				.explodinglayers-shape {margin: 11px 0 17px 0}
				.explodinglayers-icon {
					
					padding: 2px; 
					border: 1px solid #EEE; 
					margin: 3px; 
					display: inline-block;
					vertical-align: top;
					line-height: 0;
					cursor: pointer;
					
				}

				.explodinglayers-icon:hover, 
				.explodinglayers-selected {
					
					border: 1px solid #34495E;
					box-shadow: 0 0 0 1px #34495E;
					
				}

				.explodinglayers-selected {pointer-events: none}
				.explodinglayers-circle {
					
					width: 18px;
					height: 18px;
					margin: 3px;
					display: block;
					border-radius: 50%;
					background: #34495E;
					
				}

				
			</style>
			
		</div>';
		
		static::$_Markup = $_markup;
		static::$_Icon = 'fa-icon-legal';
		static::$_JavaScript = '
			
			jQuery(function() {
				
				jQuery(".explodinglayers-addon-color").each(function() {
				
					jQuery(this).tpColorPicker({
						
						wrapClasses: "explodinglayers_def_color_wrap withlabel",
						wrapId: this.id + "s",
						change: function(a, b, c) {if(c) a.val(JSON.stringify(c).replace(/\"/g, "&"));}
						
					});
					
				});
				
				// handle inputs with min/max values
				jQuery(".explodinglayers-min-max").on("change", function() {
					
					this.value = Math.max(parseFloat(this.getAttribute("data-min")), 
								 Math.min(parseFloat(this.getAttribute("data-max")), parseFloat(this.value))); 
					
				});
				
				jQuery(".explodinglayers-icon").on("click", function() {
			
					var $this = jQuery(this),
						par = $this.closest(".explodinglayers-shape");
						
					par.find(".explodinglayers-type").val($this.attr("data-icon"));
					par.find(".explodinglayers-icon").removeClass("explodinglayers-selected");
					$this.addClass("explodinglayers-selected");
					
				});
				
			});
		
		';
		
	}
	
}
?>