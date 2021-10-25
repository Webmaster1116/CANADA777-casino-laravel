<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

require_once(RS_PARTICLES_PLUGIN_PATH . 'framework/slider.admin.class.php');

class RsParticlesSliderAdmin extends RsAddonParticlesSliderAdmin {
	
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
		
		$_enabled               = RevSliderFunctions::getVal($_slider, 'particles_enabled', false) == 'true' ? ' checked' : '';
		$_hideOnMobile          = RevSliderFunctions::getVal($_slider, 'particles_hide_on_mobile', true) == 'true' ? ' checked' : '';
		
		$_startSlide            = RevSliderFunctions::getVal($_slider, 'particles_start_slide', 'first');
		$_endSlide              = RevSliderFunctions::getVal($_slider, 'particles_end_slide', 'last');
		$_zIndex                = RevSliderFunctions::getVal($_slider, 'particles_zindex', 'default');
		
		$_numParticles          = RevSliderFunctions::getVal($_slider, 'particles_number_value', '80');
		$_sizeValue             = RevSliderFunctions::getVal($_slider, 'particles_size_value', '3');
		$_sizeMinValue          = RevSliderFunctions::getVal($_slider, 'particles_size_min_value', '1');
		
		$_moveSpeed             = RevSliderFunctions::getVal($_slider, 'particles_move_speed', '6');
		$_moveSpeedMin          = RevSliderFunctions::getVal($_slider, 'particles_move_speed_min', '3'); 
		$_borderWidth           = RevSliderFunctions::getVal($_slider, 'particles_border_width', '1');
		$_lineWidth             = RevSliderFunctions::getVal($_slider, 'particles_line_width', '1');
		$_opacityValue          = RevSliderFunctions::getVal($_slider, 'particles_opacity_value', '50');
		$_opacityMinValue       = RevSliderFunctions::getVal($_slider, 'particles_opacity_min_value', '25');
		$_lineOpacity           = RevSliderFunctions::getVal($_slider, 'particles_line_opacity', '40');
		$_lineDistance          = RevSliderFunctions::getVal($_slider, 'particles_line_distance', '150');
		$_borderOpacity         = RevSliderFunctions::getVal($_slider, 'particles_border_opacity', '100');
		$_particleColors        = RevSliderFunctions::getVal($_slider, 'particles_color_value', '#ffffff');
		$_borderColors          = RevSliderFunctions::getVal($_slider, 'particles_border_color', '#ffffff');
		$_lineColors            = RevSliderFunctions::getVal($_slider, 'particles_line_color', '#ffffff');
		$_moveDirection         = RevSliderFunctions::getVal($_slider, 'particles_move_direction', 'none');
		$_hoverMode             = RevSliderFunctions::getVal($_slider, 'particles_onhover_mode', 'repulse');
		$_clickMode             = RevSliderFunctions::getVal($_slider, 'particles_onclick_mode', 'repulse');
		
		$_repulseDistance       = RevSliderFunctions::getVal($_slider, 'particles_modes_repulse_distance', '200');
		$_bubbleDistance        = RevSliderFunctions::getVal($_slider, 'particles_modes_bubble_distance', '400');
		$_bubbleSize            = RevSliderFunctions::getVal($_slider, 'particles_modes_bubble_size', '40');
		$_bubbleOpacity         = RevSliderFunctions::getVal($_slider, 'particles_modes_bubble_opacity', '40');
		$_bubbleSpeed           = RevSliderFunctions::getVal($_slider, 'particles_modes_bubble_speed', '3');
		$_grabDistance          = RevSliderFunctions::getVal($_slider, 'particles_modes_grab_distance', '400');
		$_grabOpacity           = RevSliderFunctions::getVal($_slider, 'particles_modes_grab_opacity', '50');
		$_sizeAnimSpeed         = RevSliderFunctions::getVal($_slider, 'particles_size_anim_speed', '40');
		$_sizeAnimMin           = RevSliderFunctions::getVal($_slider, 'particles_size_anim_min', '1');
		$_opacityAnimSpeed      = RevSliderFunctions::getVal($_slider, 'particles_opacity_anim_speed', '3');
		$_opacityAnimMin        = RevSliderFunctions::getVal($_slider, 'particles_opacity_anim_min', '0');
		
		$_moveEnabled           = RevSliderFunctions::getVal($_slider, 'particles_move_enable', true) == 'true' ? ' checked' : '';
		$_lineEnabled           = RevSliderFunctions::getVal($_slider, 'particles_line_enable', false) == 'true' ? ' checked' : '';
		$_borderEnabled         = RevSliderFunctions::getVal($_slider, 'particles_border_enable', false) == 'true' ? ' checked' : '';
		$_sizeRandom            = RevSliderFunctions::getVal($_slider, 'particles_size_random', true) == 'true' ? ' checked' : '';
		$_opacityRandom         = RevSliderFunctions::getVal($_slider, 'particles_opacity_random', false) == 'true' ? ' checked' : '';
		$_moveStraight          = RevSliderFunctions::getVal($_slider, 'particles_move_straight', true) == 'true' ? ' checked' : '';
		$_randomSpeed           = RevSliderFunctions::getVal($_slider, 'particles_move_random', true) == 'true' ? ' checked' : '';
		$_moveBounce            = RevSliderFunctions::getVal($_slider, 'particles_move_bounce', false) == 'true' ? ' checked' : '';
		$_hoverEnable           = RevSliderFunctions::getVal($_slider, 'particles_onhover_enable', false) == 'true' ? ' checked' : '';
		$_clickEnable           = RevSliderFunctions::getVal($_slider, 'particles_onclick_enable', false) == 'true' ? ' checked' : '';
		$_sizeAnimEnable        = RevSliderFunctions::getVal($_slider, 'particles_size_anim_enable', false) == 'true' ? ' checked' : '';
		$_sizeAnimSync          = RevSliderFunctions::getVal($_slider, 'particles_size_anim_sync', false) == 'true' ? ' checked' : '';
		$_opacityAnimSync       = RevSliderFunctions::getVal($_slider, 'particles_opacity_anim_sync', false) == 'true' ? ' checked' : '';
		$_opacityAnimEnable     = RevSliderFunctions::getVal($_slider, 'particles_opacity_anim_enable', false) == 'true' ? ' checked' : '';
		
		$_showSettings          = $_enabled ? 'block' : 'none';
		$_hoverSettings         = $_hoverEnable ? 'block' : 'none';
		$_clickSettings         = $_clickEnable ? 'block' : 'none';
		$_moveSettings          = $_moveEnabled ? 'block' : 'none';
		$_showNotice            = $_moveEnabled ? 'none' : 'block';
		$_lineSettings          = $_lineEnabled ? 'block' : 'none';
		$_minSpeedSettings      = $_randomSpeed ? 'block' : 'none';
		$_sizeMinSettings       = $_sizeRandom ? 'block' : 'none';
		$_borderSettings        = $_borderEnabled ? 'block' : 'none';
		$_sizeAnimSettings      = $_sizeAnimEnable ? 'block' : 'none';
		$_minOpacitySettings    = $_opacityRandom ? 'block' : 'none';
		$_opacityAnimSettings   = $_opacityAnimEnable ? 'block' : 'none';
		$_directionOptions      = $_moveDirection === 'none' || $_moveDirection === 'static' ? 'none' : 'block';
		
		$_alias                 = RevSliderFunctions::getVal($_slider, 'alias', '');
		$_custom                = get_option('revslider_addon_particles_templates');
		$_core                  = rsParticlesTemplates::$_Templates;
		$_textDomain            = 'rs_' . static::$_Title;
		$_markup                = '';
		$_totalSlides           = 2;
		
		$_svgStart              = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#34495E" d="';
		$_svgEnd                = '"></svg>';
		
		$_selectedIcon          = RevSliderFunctions::getVal($_slider, 'particles_shape_type', 'circle');
		$_selectedClass         = $_selectedIcon !== 'circle' ? '' : ' particle-selected';
		
		$_colors                = explode(',', $_particleColors);
		$_lines                 = explode(',', $_lineColors);
		$_borders               = explode(',', $_borderColors);
		
		$_colorStart            = '<span class="particles-color-picker">';
		$_colorField            = '<input type="text" class="rs-layer-input-field tipsy_enabled_top particles-color-input" title="Select a Color" data-editing="';
		$_colorFieldMiddle      = '" value="';
		$_colorEnd              = '" /><a class="button-primary revblue particles-add-color">+</a><a class="button-primary revred particles-remove-color"><span>+</span></a></span>';
		
		$_clicks                = array('bubble', 'repulse');
		$_hovers                = array('repulse', 'grab', 'bubble');
		$_directions            = array('none', 'static', 'top', 'right', 'bottom', 'left', 'top-left', 'top-right', 'bottom-left', 'bottom-right');
		
		$_events                = $_hoverEnable || $_clickEnable;
		$_grabSettings          = $_hoverEnable && $_hoverMode === 'grab' ? 'block' : 'none';
		$_bubbleSettings        = $_events  && ($_hoverMode === 'bubble' || $_clickMode === 'bubble') ? 'block' : 'none';
		$_repulseSettings       = $_events  && ($_hoverMode === 'repulse' || $_clickMode === 'repulse') ? 'block' : 'none';
		
		$_icons = array_merge(array(
		
			'edge' => 'M4 4h16v16H4z', 
			'triangle' => 'M12 4L4 20L20 20z', 
			'polygon' => 'M5 4 L17 4 L22 12 L17 20 L8 20 L3 12 L8 4 Z', 
			'star' => 'M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z'

		), rsParticlesSvg::$_SVGs);
		
		if($_alias) {
		
			$_clas = new RevSlider();
			$_revSliders = $_clas->getArrSliders();
			foreach($_revSliders as $_revSlider) {
				
				if($_alias === $_revSlider->getAlias()) $_totalSlides = $_revSlider->getNumSlides();
				
			}
			
		}
		
		$_markup = '<span class="label" id="label_particles_enabled" origtitle="' . __("Enable/Disable Particle Effects for this Slider<br><br>", $_textDomain) . '">' . __('Enable Particle Effects', $_textDomain) . '</span>
		<input type="checkbox" class="tp-moderncheckbox withlabel" id="particles_enabled" name="particles_enabled"' . $_enabled . ' 
			onchange="document.getElementById(\'particles-settings\').style.display=this.checked ? \'block\' : \'none\'" />
		
		<div id="particles-settings" style="display: ' . $_showSettings . '">
		
			<h4>Slider Settings</h4>
			
			<span class="label" id="label_particles_start_slide" origtitle="' . __('Start Particles on Slide Number X<br><br>', $_textDomain) . '">' . __("Start on Slide", $_textDomain) . '</span>
			<select class="withlabel" id="particles_start_slide" name="particles_start_slide" data-skip="true">
				<option value="first"';
				
				if($_startSlide === 'first') $_markup .= ' selected';
				$_markup .= '>First Slide</option>';
				
				for($i = 2; $i < $_totalSlides + 1; $i++) {
					
					$_markup .= '<option value="' . $i . '"';
					if($_startSlide == $i) $_markup .= ' selected';
					$_markup .= '>' . $i . '</option>';
					
				}
				
			$_markup .= '</select>
			<br>
			
			<span class="label" id="label_particles_end_slide" origtitle="' . __('End Particles on Slide Number X<br><br>', $_textDomain) . '">' . __("End on Slide", $_textDomain) . '</span>
			<select class="withlabel" id="particles_end_slide" name="particles_end_slide" data-skip="true">
				<option value="last"';
				
				if($_endSlide === 'last') $_markup .= ' selected';
				$_markup .= '>Last Slide</option>';
				
				for($i = 1; $i < $_totalSlides; $i++) {
					
					$_markup .= '<option value="' . $i . '"';
					if($_endSlide == $i) $_markup .= ' selected';
					$_markup .= '>' . $i . '</option>';
					
				}
			
			$_markup .= '</select>
			<br>
			
			<span class="label" id="label_particles_hide_on_mobile" origtitle="' . __("Disable the Particles on Mobile Devices (recommended for performance considerations)<br><br>", $_textDomain) . '">' . __('Disable on Mobile', $_textDomain) . '</span>
			<input type="checkbox" class="tp-moderncheckbox withlabel" id="particles_hide_on_mobile" name="particles_hide_on_mobile" data-skip="true"' . $_hideOnMobile . ' />
			<br>
			
			<h4>Settings Templates</h4>
			<span class="label" id="label_particles_templates" origtitle="' . __('Load Settings from a Template.<br><br>IMPORTANT: This will override your existing particle settings!<br><br>', $_textDomain) . '">' . __("Select a Template", $_textDomain) . '</span>
			<select class="withlabel" id="particles_templates" name="particles_templates" data-skip="true">
				<option disabled></option>
				<option class="particles-option-label"  disabled>' . __('Default Templates', $_textDomain) . '</option>
				<option disabled>----------------------------</option>';
				
				foreach($_core as $_key => $_value) {
					
					$_markup .= '<option value="' . $_key . '">' . ucwords(str_replace('_', ' ', $_key)) . '</option>';
					
				}
				
				$_markup .= '<option disabled></option>
				<option class="particles-option-label" disabled>' . __('Custom Templates', $_textDomain) . '</option>
				<option disabled>----------------------------</option>';
				
				if($_custom) {
				
					foreach($_custom as $_key => $_value) {
					
						$_markup .= '<option data-candelete="true" value="' . $_key . '">' . ucwords(str_replace('_', ' ', $_key)) . '</option>';
						
					}
					
				}

			$_markup .= '<option id="particles-templates-last-option" disabled></option></select>
			
			<div class="particles-no-wrap">
				<div id="particles-load-template" class="toggle-custom-navigation-style revblue visible">Load Settings from Selected Template</div>
				<div id="particles-delete-template" class="toggle-custom-navigation-style revred visible" style="display: none">Delete</div>
			</div>
			
			<h4>Custom Settings</h4>
			
			<ul class="main-options-small-tabs" style="display:inline-block">
				<li id="particles_1" data-content="#particles-general" class="selected">Particles</li>
				<li id="particles_2" data-content="#particles-styles">Styles</li>
				<li id="particles_2" data-content="#particles-movement">Movement</li>
				<li id="particles_3" data-content="#particles-interactivity">Interactivity</li>
				<li id="particles_4" data-content="#particles-pulse">Pulse</li>	
			</ul>
			
			<div id="particles-general">
				
				<div style="margin-top: 11px">
				<span data-icon="circle" class="particles-icon' . $_selectedClass . '"><span class="particles-circle"></span></span>';
				
				foreach($_icons as $key => $value) {
					
					$_selectedClass = $key !== $_selectedIcon ? '' : ' particle-selected';
					$_markup .= '<span data-icon="' . $key . '" class="particles-icon' . $_selectedClass . '">' . $_svgStart . $value . $_svgEnd . '</span>';
					
				}
				
				$_markup .= '<input type="hidden" id="particles_shape_type" name="particles_shape_type" value="' . $_selectedIcon . '" />
				</div>
				
				<br>
				<span class="label" id="label_particles_number_value" origtitle="' . __('Maximum number of particles.  The lower the number, the better the performance.<br><br>', $_textDomain) . '">' . __("Number of Particles", $_textDomain) . '</span>
				<input type="text" data-min="1" data-max="500" class="text-sidebar withlabel particles-min-max" id="particles_number_value" name="particles_number_value" value="' . $_numParticles . '" />
				<br>
				
				<span class="label" id="label_particles_size_value" origtitle="' . __('Default particle size<br><br>', $_textDomain) . '">' . __("Particle Size", $_textDomain) . '</span>
				<input type="text" data-min="1" data-max="250" class="text-sidebar withlabel particles-min-max" id="particles_size_value" name="particles_size_value" value="' . $_sizeValue . '" />
				<br>
				
				<span class="label" id="label_particles_size_random" origtitle="' . __("Particle sizes will vary.  Default size will be used as the maximum.<br><br>", $_textDomain) . '">' . __('Random Sizes', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="particles_size_random" name="particles_size_random"' . $_sizeRandom . ' onchange="document.getElementById(\'particles-size-min-settings\').style.display=this.checked ? \'block\' : \'none\';" />
				
				<div id="particles-size-min-settings" style="display: ' . $_sizeMinSettings . '">
				
					<span class="label" id="label_particles_size_min_value" origtitle="' . __('Minimum particle size when Random Sizes are enabled<br><br>', $_textDomain) . '">' . __("Min. Size", $_textDomain) . '</span>
					<input type="text" data-min="0.1" data-max="250" class="text-sidebar withlabel particles-min-max" id="particles_size_min_value" name="particles_size_min_value" value="' . $_sizeMinValue . '" />
					<br>
					
				</div>
			
			</div>
			
			<div id="particles-styles" style="display: none">
				
				<span class="label particles-color-label" id="label_particles_color_value" origtitle="' . __('Select your Particle Colors<br><br>', $_textDomain) . '">' . __("Particle Colors", $_textDomain) . '</span>
				<div class="particle-colors-wrap" data-editing="' . __('Particle Color', $_textDomain) . '">';
				
					foreach($_colors as $_color) {
						
						$_markup .= $_colorStart . $_colorField . __('Particle Color', $_textDomain) . $_colorFieldMiddle . trim($_color) . $_colorEnd;
						
					}
					
					$_markup .= 
					'<input type="hidden" class="particles-color-input-value" id="particles_color_value" name="particles_color_value" value="' . $_particleColors . '" />
				</div>
				<div style="clear: both"></div>
				
				<span class="label" id="label_particles_opacity_value" origtitle="' . __('Particle Opacity (1-100)<br><br>', $_textDomain) . '">' . __("Particle Transparency", $_textDomain) . '</span>
				<input type="text" data-min="1" data-max="100" class="text-sidebar withlabel particles-min-max" id="particles_opacity_value" name="particles_opacity_value" value="' . $_opacityValue . '" /> <span>%</span>
				<br>
				
				<span class="label" id="label_particles_opacity_random" origtitle="' . __("Particle opacity will vary.  Default opacity will be used as the maximum.<br><br>", $_textDomain) . '">' . __('Random Transparency', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="particles_opacity_random" name="particles_opacity_random"' . $_opacityRandom . ' 
				onchange="document.getElementById(\'particles-min-opacity-settings\').style.display=this.checked ? \'block\' : \'none\';" />
				
				<div id="particles-min-opacity-settings" style="display: ' . $_minOpacitySettings . '">
				
					<span class="label" id="label_particles_opacity_min_value" origtitle="' . __('Minimum particle opacity (1-100) when Random Opacity is enabled<br><br>', $_textDomain) . '">' . __("Min Transparency", $_textDomain) . '</span>
					<input type="text" data-min="1" data-max="100" class="text-sidebar withlabel particles-min-max" id="particles_opacity_min_value" name="particles_opacity_min_value" value="' . $_opacityMinValue . '" /> <span>%</span>
					<br>
				
				</div>
				
				<span class="label" id="label_particles_border_enable" origtitle="' . __("Enable Particle Border Styles<br><br>", $_textDomain) . '">' . __('Borders/Stroke', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="particles_border_enable" name="particles_border_enable"' . $_borderEnabled . '  onchange="document.getElementById(\'particle-border-settings\').style.display=this.checked ? \'block\' : \'none\';" />
				
				<div id="particle-border-settings" class="withsublabels" style="display: ' . $_borderSettings . '">

					<span class="label particles-color-label" id="label_particles_border_color" origtitle="' . __('Select your Border Colors<br><br>', $_textDomain) . '">' . __("Border Colors", $_textDomain) . '</span>
					<div class="particle-colors-wrap" data-editing="' . __('Border Color', $_textDomain) . '">';
					
						foreach($_borders as $_border) {
							
							$_markup .= $_colorStart . $_colorField . __('Border Color', $_textDomain) . $_colorFieldMiddle . trim($_color) . $_colorEnd;
							
						}
						
						$_markup .= '<div class="particles-add-color toggle-custom-navigation-style visible">Add Border Color</div>
						<input type="hidden" class="particles-color-input-value" id="particles_border_color" name="particles_border_color" value="' . $_borderColors . '" />
					</div>
					<div style="clear: both"></div>
					
					<span class="label" id="label_particles_border_size" origtitle="' . __('Set a border size for the particles<br><br>', $_textDomain) . '">' . __("Border Size", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="particles_border_size" name="particles_border_size" value="' . $_borderWidth . '" /> <span>px</span>
					<br>
					
					<span class="label" id="label_particles_border_opacity" origtitle="' . __('Border Opacity (0-100)<br><br> . ', $_textDomain) . '">' . __("Border Transparency", $_textDomain) . '</span>
					<input type="text" data-min="0" data-max="100" class="text-sidebar withlabel particles-min-max" id="particles_border_opacity" name="particles_border_opacity" value="' . $_borderOpacity . '" /> <span>%</span>
				
				</div>
				
				<span class="label" id="label_particles_line_enable" origtitle="' . __("Connect particles with lines<br><br>", $_textDomain) . '">' . __('Connected Lines', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="particles_line_enable" name="particles_line_enable"' . $_lineEnabled . '  onchange="document.getElementById(\'particle-line-settings\').style.display=this.checked ? \'block\' : \'none\';" />
				
				<div id="particle-line-settings" class="withsublabels" style="display: ' . $_lineSettings . '">

					<span class="label particles-color-label" id="label_particles_line_color" origtitle="' . __('Select your Connected Line Colors<br><br>', $_textDomain) . '">' . __("Line Colors", $_textDomain) . '</span>
					<div class="particle-colors-wrap" data-editing="' . __('Line Color', $_textDomain) . '">';
					
						foreach($_lines as $_line) {
							
							$_markup .= $_colorStart . $_colorField . __('Line Color', $_textDomain) . $_colorFieldMiddle . trim($_color) . $_colorEnd;
							
						}
						
						$_markup .= '<div class="particles-add-color toggle-custom-navigation-style visible">Add Line Color</div>
						<input type="hidden" class="particles-color-input-value" id="particles_line_color" name="particles_line_color" value="' . $_lineColors . '" />
					</div>
					<div style="clear: both"></div>
					
					<span class="label" id="label_particles_line_width" origtitle="' . __('Set a size for the connected lines.<br><br>', $_textDomain) . '">' . __("Line Width", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="particles_line_width" name="particles_line_width" value="' . $_lineWidth . '" /> <span>px</span>
					<br>
					
					<span class="label" id="label_particles_line_opacity" origtitle="' . __('Line Opacity (0-100).<br><br>', $_textDomain) . '">' . __("Line Transparency", $_textDomain) . '</span>
					<input type="text" data-min="0" data-max="100" class="text-sidebar withlabel particles-min-max" id="particles_line_opacity" name="particles_line_opacity" value="' . $_lineOpacity . '" /> <span>%</span>
					<br>
					
					<span class="label" id="label_particles_line_distance" origtitle="' . __('Draw lines when particles are within this distance of one another.<br><br>A number between 50-250 is recommended.<br><br>', $_textDomain) . '">' . __("Connect Between..", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="particles_line_distance" name="particles_line_distance" value="' . $_lineDistance . '" /> <span>px</span>
				
				</div>
				
				<span class="label" id="label_particles_zindex" origtitle="' . __('Special option useful for placing the particles on top of certain Slide Layers.  If not needed, leave set to default.<br><br>', $_textDomain) . '">' . __("z-Index", $_textDomain) . '</span>
				<input type="text" class="text-sidebar withlabel" id="particles_zindex" name="particles_zindex" value="' . $_zIndex . '" data-skip="true" />
			
			</div>
			
			<div id="particles-movement" style="display: none">
				
				<span class="label" id="label_particles_move_enable" origtitle="' . __("Enable/Disable Particle Movement<br><br>", $_textDomain) . '">' . __('Particle Movement', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="particles_move_enable" name="particles_move_enable"' . $_moveEnabled . '  onchange="document.getElementById(\'particles-move-settings\').style.display=this.checked ? \'block\' : \'none\';document.getElementById(\'particles-interactivity-notice\').style.display=this.checked ? \'none\' : \'block\';document.getElementById(\'particles-interactivity-notice-2\').style.display=this.checked ? \'none\' : \'block\';" />
				
				<div id="particles-move-settings" style="display: ' . $_moveSettings . '">
					
					<span class="label" id="label_particles_move_speed" origtitle="' . __('The particle movement speed.  A number between 1-5 is recommended.<br><br>', $_textDomain) . '">' . __("Speed", $_textDomain) . '</span>
					<input type="text" data-min="1" data-max="50" class="text-sidebar withlabel particles-min-max" id="particles_move_speed" name="particles_move_speed" value="' . $_moveSpeed . '" />
					<br>
					
					<span class="label" id="label_particles_move_random" origtitle="' . __("Particle speeds will vary.  Default speed will be used as the maximum.<br><br>", $_textDomain) . '">' . __('Varying Speed', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="particles_move_random" name="particles_move_random"' . $_randomSpeed . ' onchange="document.getElementById(\'particle-min-speed-settings\').style.display=this.checked ? \'block\' : \'none\';" />
					<br>
					
					<div id="particle-min-speed-settings" style="display: ' . $_minSpeedSettings . '">
					
						<span class="label" id="label_particles_move_speed_min" origtitle="' . __('The minimum movement speed when Varying Speeds are used.  A number between 1-5 is recommended.<br><br>', $_textDomain) . '">' . __("Min. Speed", $_textDomain) . '</span>
						<input type="text" data-min="1" data-max="50" class="text-sidebar withlabel particles-min-max" id="particles_move_speed_min" name="particles_move_speed_min" value="' . $_moveSpeedMin . '" />
						<br>
						
					</div>
					
					<span class="label" id="label_particles_move_direction" origtitle="' . __('Move particles in random directions or choose a specific side/corner to move the particles toward.<br><br>', $_textDomain) . '">' . __("Direction", $_textDomain) . '</span>
					<select class="withlabel" id="particles_move_direction" name="particles_move_direction">';
						
						foreach($_directions as $_direction) {
							
							$_selected = $_direction !== $_moveDirection ? '' : ' selected';
							$_title = $_direction === 'none' ? 'Random' : ucwords(str_replace('-', ' ', $_direction));
							$_markup .= '<option value="' . $_direction . '"' . $_selected . '>' . $_title . '</option>';
							
						}
						
					$_markup .= '</select>
					
					<div id="particle-direction-options" class="withsublabels" style="display: ' . $_directionOptions . '">
					
						<span class="label" id="label_particles_move_straight" origtitle="' . __("Allow for small movement variations as the particles head towards the selected side/corner.<br><br>", $_textDomain) . '">' . __('Varying Movement', $_textDomain) . '</span>
						<input type="checkbox" class="tp-moderncheckbox withlabel" id="particles_move_straight" name="particles_move_straight"' . $_moveStraight . ' />
						<br>
					
					</div>
					
					<span class="label" id="label_particles_move_bounce" origtitle="' . __("Creates a ping-pong effect where the particles will bounce off the sides of the slider.<br><br>", $_textDomain) . '">' . __('Bounce', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="particles_move_bounce" name="particles_move_bounce"' . $_moveBounce . ' />
					
				</div>
			
			</div>
			
			<div id="particles-interactivity" style="display: none">
				
				<div class="particles-notice revred" id="particles-interactivity-notice" style="display: ' . $_showNotice . '">' . __('Interactivity only works when "Movement" is enabled!', $_textDomain) . '</div>
				
				<span class="label" id="label_particles_onhover_enable" origtitle="' . __("Enable/Disable Mouse Hovers<br><br>", $_textDomain) . '">' . __('Mouse Hovers', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="particles_onhover_enable" name="particles_onhover_enable"' . $_hoverEnable . '  onchange="document.getElementById(\'particles-hover-mode\').style.display=this.checked ? \'block\' : \'none\';" />
				
				<div id="particles-hover-mode" class="withsublabels" style="display: ' . $_hoverSettings . '">
				
					<span class="label" id="label_particles_onhover_mode" origtitle="' . __('Choose the Hover Option<br><br>', $_textDomain) . '">' . __("Hover Mode", $_textDomain) . '</span>
					<select class="withlabel" id="particles_onhover_mode" name="particles_onhover_mode">';
						
						foreach($_hovers as $_hover) {
							
							$_selected = $_hover !== $_hoverMode ? '' : ' selected';
							$_markup .= '<option value="' . $_hover . '"' . $_selected . '>' . ucfirst($_hover) . '</option>';
							
						}
						
					$_markup .= '</select>
				
				</div>
				
				<span class="label" id="label_particles_onclick_enable" origtitle="' . __("Enable/Disable Click Actions<br><br>IMPORTANT:<br>Click Actions will not work if regular Slide Links are enabled.<br><br>", $_textDomain) . '">' . __('Click Actions', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="particles_onclick_enable" name="particles_onclick_enable"' . $_clickEnable . '  onchange="document.getElementById(\'particles-click-mode\').style.display=this.checked ? \'block\' : \'none\';" />
				
				<div id="particles-click-mode" class="withsublabels" style="display: ' . $_clickSettings . '">
				
					<span class="label" id="label_particles_onclick_mode" origtitle="' . __('Choose the Click Option<br><br>', $_textDomain) . '">' . __("Click Mode", $_textDomain) . '</span>
					<select class="withlabel" id="particles_onclick_mode" name="particles_onclick_mode">';
						
						foreach($_clicks as $_click) {
							
							$_selected = $_click !== $_clickMode ? '' : ' selected';
							$_markup .= '<option value="' . $_click . '"' . $_selected . '>' . ucfirst($_click) . '</option>';
							
						}
						
					$_markup .= '</select>
				
				</div>
				
				<div id="particles-mode-repulse" class="particle-event-mode" style="display: ' . $_repulseSettings . '">
					
					<span><strong>Repulse Mode</strong></span>
					<div class="withsublabels">
					
						<span class="label" id="label_particles_modes_repulse_distance" origtitle="' . __('The distance in pixels the particles will move away from the mouse<br><br>', $_textDomain) . '">' . __("Distance", $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel" id="particles_modes_repulse_distance" name="particles_modes_repulse_distance" value="' . $_repulseDistance . '" />
						
					</div>
				
				</div>
				
				<div id="particles-mode-bubble" class="particle-event-mode" style="display: ' . $_bubbleSettings . '">
					
					<span><strong>Bubble Mode</strong></span>
					<div class="withsublabels">
					
						<span class="label" id="label_particles_modes_bubble_distance" origtitle="' . __('Particles within this distance (in pixels) will bubble/zoom.<br><br>', $_textDomain) . '">' . __("Distance", $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel" id="particles_modes_bubble_distance" name="particles_modes_bubble_distance" value="' . $_bubbleDistance . '" />
						<br>
						
						<span class="label" id="label_particles_modes_bubble_size" origtitle="' . __('The max size in pixels the particles will zoom to.<br><br>', $_textDomain) . '">' . __("Size", $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel" id="particles_modes_bubble_size" name="particles_modes_bubble_size" value="' . $_bubbleSize . '" />
						<br>
						
						<span class="label" id="label_particles_modes_bubble_opacity" origtitle="' . __('The Bubbled Particles Opacity (1-100)<br><br>', $_textDomain) . '">' . __("Opacity", $_textDomain) . '</span>
						<input type="text" data-min="1" data-max="100" class="text-sidebar withlabel particles-min-max" id="particles_modes_bubble_opacity" name="particles_modes_bubble_opacity" value="' . $_bubbleOpacity . '" />
						
					</div>
				
				</div>
				
				<div id="particles-mode-grab" class="particle-event-mode" style="display: ' . $_grabSettings . '">
				
					<span><strong>Grab Mode</strong></span>
					<div class="withsublabels">
						<span class="label" id="label_particles_modes_grab_distance" origtitle="' . __('The distance in pixels the particles will move away from the mouse<br><br>', $_textDomain) . '">' . __("Distance", $_textDomain) . '</span>
						<input type="text" class="text-sidebar withlabel" id="particles_modes_grab_distance" name="particles_modes_grab_distance" value="' . $_grabDistance . '" />
						<br>
						
						<span class="label" id="label_particles_modes_grab_opacity" origtitle="' . __('The Grabbed Lines Opacity (1-100)<br><br>', $_textDomain) . '">' . __("Opacity", $_textDomain) . '</span>
						<input type="text" data-min="10" data-max="100" class="text-sidebar withlabel particles-min-max" id="particles_modes_grab_opacity" name="particles_modes_grab_opacity" value="' . $_grabOpacity . '" />
					</div>
				
				</div>
			
			</div>
			
			<div id="particles-pulse" style="display: none">
				
				<div class="particles-notice revred" id="particles-interactivity-notice-2" style="display: ' . $_showNotice . '">' . __('Pulse only works when "Movement" is enabled!', $_textDomain) . '</div>
				
				<span class="label" id="label_particles_size_anim_enable" origtitle="' . __("Choose to animate the particle size<br><br>", $_textDomain) . '">' . __('Animate Particle Size', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="particles_size_anim_enable" name="particles_size_anim_enable"' . $_sizeAnimEnable . '  onchange="document.getElementById(\'particles-size-anim-settings\').style.display=this.checked ? \'block\' : \'none\';" />
				
				<div id="particles-size-anim-settings" class="withsublabels" style="display: ' . $_sizeAnimSettings . '">
					
					<span class="label" id="label_particles_size_anim_speed" origtitle="' . __('The animation speed.  A number between 10-100 is recommended.<br><br>', $_textDomain) . '">' . __("Speed", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="particles_size_anim_speed" name="particles_size_anim_speed" value="' . $_sizeAnimSpeed . '" />
					<br>
					
					<span class="label" id="label_particles_size_anim_min" origtitle="' . __('The minimum size to animate to.  Default particle size will be used as the maximum.<br><br>', $_textDomain) . '">' . __("Min. Size", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="particles_size_anim_min" name="particles_size_anim_min" value="' . $_sizeAnimMin . '" />
					<br>
					
					<span class="label" id="label_particles_size_anim_sync" origtitle="' . __("Sync the size animations of all particles.<br><br>", $_textDomain) . '">' . __('Sync', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="particles_size_anim_sync" name="particles_size_anim_sync"' . $_sizeAnimSync . ' />
				
				</div>
				
				<span class="label" id="label_particles_opacity_anim_enable" origtitle="' . __("Choose to animate the particle opacity<br><br>", $_textDomain) . '">' . __('Animate Particle Opacity', $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="particles_opacity_anim_enable" name="particles_opacity_anim_enable"' . $_opacityAnimEnable . '  onchange="document.getElementById(\'particles-opacity-anim-settings\').style.display=this.checked ? \'block\' : \'none\';" />
				
				<div id="particles-opacity-anim-settings" class="withsublabels" style="display: ' . $_opacityAnimSettings . '">
					
					<span class="label" id="label_particles_opacity_anim_speed" origtitle="' . __('The animation speed.  A number between 1-5 is recommended.<br><br>', $_textDomain) . '">' . __("Speed", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="particles_opacity_anim_speed" name="particles_opacity_anim_speed" value="' . $_opacityAnimSpeed . '" />
					<br>
					
					<span class="label" id="label_particles_opacity_anim_min" origtitle="' . __('The minimum transparency to animate to.  Default particle transparency will be used as the maximum.<br><br>', $_textDomain) . '">' . __("Min. Opacity", $_textDomain) . '</span>
					<input type="text" data-min="1" data-max="50" class="text-sidebar withlabel particles-min-max" id="particles_opacity_anim_min" name="particles_opacity_anim_min" value="' . $_opacityAnimMin . '" /> <span>%</span>
					<br>
					
					<span class="label" id="label_particles_opacity_anim_sync" origtitle="' . __("Sync the opacity animations of all particles.<br><br>", $_textDomain) . '">' . __('Sync', $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="particles_opacity_anim_sync" name="particles_opacity_anim_sync"' . $_opacityAnimSync . ' />
				
				</div>
			
			</div>
			
			<div id="particles-save-template" class="toggle-custom-navigation-style revgreen visible">Save Current Settings as Custom Template</div>
			
		</div>
		
		<div id="particles_save_as_template" title="' . __("Save as Template", $_textDomain) . '">
			<div>
				<span>' . __("Save As", $_textDomain) . '</span>
				<input id="particles_save_as_input" type="text" name="particles_save_as_input" value="" />
			</div>
		</div>';
		
		static::$_Markup = $_markup;
		static::$_Icon = 'eg-icon-puzzle';
		static::$_JavaScript = '';
		
	}
	
}
?>