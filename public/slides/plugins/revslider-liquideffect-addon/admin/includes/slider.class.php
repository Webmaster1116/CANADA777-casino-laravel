<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_LIQUIDEFFECT_PLUGIN_PATH . 'framework/slider.admin.class.php');
require_once(RS_LIQUIDEFFECT_PLUGIN_PATH . 'admin/includes/templates.class.php');

class RsLiquidEffectSliderAdmin extends RsAddonLiquidEffectSliderAdmin {
	
	protected static $_Icon,
					 $_Title,
					 $_Markup,
					 $_JavaScript;
	
	public function __construct($_title) {
		
		static::$_Title = $_title;
		parent::init();
		
	}
	
	protected static function _init($_slider) {
		
		$_enabled   = RevSliderFunctions::getVal($_slider, 'liquideffect_enabled', false) == 'true' ? ' checked' : '';
		$_image     = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_image',     'ripple');
		$_size      = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_size',      'Large');
		$_custommap = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_custommap', '');
		
		$_autoplay  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_autoplay',  true) == 'true' ? ' checked' : '';
		$_speedx    = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_speedx',    '2');
		$_speedy    = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_speedy',    '20');
		$_scalex    = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_scalex',    '20');
		$_scaley    = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_scaley',    '20');
		$_rotationx = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_rotationx', '20');
		$_rotationy = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_rotationy', '0');
		$_rotation  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_rotation',  '0');
		
		$_transition  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transition',  true) == 'true' ? ' checked' : '';
		$_transcross  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transcross',  true) == 'true' ? ' checked' : '';
		$_transpower  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transpower',  false) == 'true' ? ' checked' : '';
		$_transtime   = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transtime',   '1000');
		$_transitionx = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transitionx', '2');
		$_transitiony = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transitiony', '1280');
		$_transpeedx  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transpeedx',  '2');
		$_transpeedy  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transpeedy',  '100');
		$_transrotx   = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transrotx',   '20');
		$_transroty   = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transroty',   '0');
		$_transrot    = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transrot',    '0');
		$_easing      = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_easing',      'Power3.easeOut');
		
		$_interactive  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_interactive',  false) == 'true' ? ' checked' : '';
		$_mobile       = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_mobile',       false) == 'true' ? ' checked' : '';
		$_event        = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_event',        'mousemove');
		$_intertime    = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_intertime',    '500');
		$_interscalex  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_interscalex',  '2');
		$_interscaley  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_interscaley',  '1280');
		$_interspeedx  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_interspeedx',  '0');
		$_interspeedy  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_interspeedy',  '0');
		$_interotation = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_interotation', '0');
		$_intereasing  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_intereasing',  'Power2.easeOut');
		
		$_textDomain = 'rs_' . static::$_Title;
		$_templates  = RsLiquidEffectTemplates::$_Templates;
		
		$_selectimage = __('Select Image');
		$_confirm     = __('Load default settings for this image map? (will override current defaults).', $_textDomain);
		
		$_showSettings       = $_enabled     ? 'block' : 'none';
		$_autoplayEnabled    = $_autoplay    ? 'block' : 'none';
		$_interactiveEnabled = $_interactive ? 'block' : 'none';
		$_transitionEnabled  = $_transition  ? 'block' : 'none';
		$_templatebtndisplay = $_image !== 'Custom Map' ? 'inline-block' : 'none';
		$_customMapDisplay   = $_image === 'Custom Map' && empty($_custommap) ? 'none' : 'block';
		$_mapurl             = RS_LIQUIDEFFECT_PLUGIN_URL . 'public/assets/images/';
		$_preview            = $_image !== 'Custom Map' ? $_mapurl . strtolower($_image) . '_small.jpg' : $_custommap;
		
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
		
		$_maps   = array('Ripple', 'Clouds', 'Crystalize', 'Fibers', 'Pointilize', 'Rings', 'Spiral', 'Maze', 'Glitch', 'Swirl', 'Custom Map');
		$_events = array('mousedown', 'mousemove');
		$_sizes  = array('Small', 'Large');
		$_markup = 
		
		'<span class="label" id="label_liquideffect_enabled" origtitle="' . __("Enable/Disable Distortion Effect for this Slider", $_textDomain) . '">' . __('Use Distortion', $_textDomain) . '</span>
		<input type="checkbox" class="tp-moderncheckbox withlabel" id="liquideffect_enabled" name="liquideffect_enabled"' . $_enabled . ' 
			onchange="document.getElementById(\'liquideffect-default-settings\').style.display=this.checked ? \'block\' : \'none\'" />
		
		<div id="liquideffect-default-settings" style="display: ' . $_showSettings . '">
		
			<h4>Default Slide Settings</h4>
			
			<ul id="liquideffect-menu" class="main-options-small-tabs" style="margin-top: 20px; border-bottom: 0">
				<li data-content="liquideffect-general-div" class="selected">General</li>					
				<li data-content="liquideffect-animate-div">Animation</li>					
				<li data-content="liquideffect-transition-div">Slide Transition</li>
				<li data-content="liquideffect-interactive-div">Interaction</li>
			</ul>
			
			<div id="liquideffect-general-div" class="liquideffect-div">
			
				<span class="label" id="label_liquideffect_settings_templates" origtitle="' . __('Select/Load a prebuilt settings template', $_textDomain) . '">' . __("Settings Templates", $_textDomain) . '</span>
				<select class="withlabel" id="liquideffect_settings_templates" value="' . $_image . '">
								
					<option value="void">- Load Template -</option>';
					foreach($_templates as $_key => $_value) {
						
						$_title = preg_replace("/\_/", " ", $_key);
						$_markup .= '<option value="' . $_key . '">' . $_title . '</option>';
						
					}
					
				$_markup .= '</select>
				
				<div id="liquideffect_templates_div" style="display: none">
					<span class="label"></span>
					<a id="liquideffect_templates" href="javascript:void(0)" class="button-primary revblue" style="text-align: center">Load Template</a>
				</div>
				
				<span class="label" id="label_liquideffect_defaults_image" origtitle="' . __('Select/Upload a Distortion Map for the effect', $_textDomain) . '">' . __("Distortion Map", $_textDomain) . '</span>
				<select class="withlabel" id="liquideffect_defaults_image" name="liquideffect_defaults_image" value="' . $_image . '">';
				foreach($_maps as $_map) {
					
					$_selected = $_map !== $_image ? '' : ' selected';
					$_markup .= '<option value="' . $_map . '"' . $_selected . '>' . $_map . '</option>';
					
				}
				
				$_markup .= '</select>
				
				<div id="liquideffect_size_div" style=display: ' . $_templatebtndisplay . '">
					
					<span class="label" id="label_liquideffect_defaults_size" origtitle="' . __('Small = 512x512 version, Large = 2048x2048 version', $_textDomain) . '">' . __("Map Size", $_textDomain) . '</span>
					<select class="withlabel" id="liquideffect_defaults_size" name="liquideffect_defaults_size" value="' . $_size . '">';
					
					foreach($_sizes as $_sizing) {
						
						$_selected = $_sizing !== $_size ? '' : ' selected';
						$_markup .= '<option value="' . $_sizing . '"' . $_selected . '>' . $_sizing . '</option>';
						
					}
				
					$_markup .= '</select>
				
				</div>
				
				<div id="liquideffect-load-custom" style="display: none">
					<span class="label"></span>
					<a id="liquideffect_load_image" href="javascript:void(0)" class="button-primary revblue" style="text-align: center">Choose Image</a>
				</div>
				
				<div id="liquideffect_custom" style="margin: 5px 0 0 1px; display:' . $_customMapDisplay . '">
					<span class="label"></span>
					<img id="liquideffect_customimg" src="' . $_preview . '">
					<input type="hidden" id="liquideffect_defaults_custommap" name="liquideffect_defaults_custommap" value="' . $_custommap . '">
				</div>
			
			</div>
			
			<div id="liquideffect-animate-div" class="liquideffect-div" style="display: none">
			
				<span class="label" id="label_liquideffect_defaults_autoplay" origtitle="' . __('Continously animate the Slide\'s main background image', $_textDomain) . '">' . __("Auto Animate", $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="liquideffect_defaults_autoplay" name="liquideffect_defaults_autoplay"' . $_autoplay . ' 
					onchange="document.getElementById(\'liquideffect-autoplay-settings\').style.display=this.checked ? \'block\' : \'none\'" />
				
				<div id="liquideffect-autoplay-settings" class="withsublabels" style="display: ' . $_autoplayEnabled . '">
				
					<span class="label" id="label_liquideffect_defaults_speedx" origtitle="' . __('Speed for the displacement map\'s left movement', $_textDomain) . '">' . __("Speed X", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_speedx" name="liquideffect_defaults_speedx" value="' . $_speedx . '" />
					<br>
					
					<span class="label" id="label_liquideffect_defaults_speedy" origtitle="' . __('Speed for the displacement map\'s top movement', $_textDomain) . '">' . __("Speed Y", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_speedy" name="liquideffect_defaults_speedy" value="' . $_speedy . '" />
					<br>
					
					<span class="label" id="label_liquideffect_defaults_rotationx" origtitle="' . __('rotationX movement for the displacement map', $_textDomain) . '">' . __("Rotation X", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_rotationx" name="liquideffect_defaults_rotationx" value="' . $_rotationx . '" />
					<br>
					
					<span class="label" id="label_liquideffect_defaults_rotationy" origtitle="' . __('rotationY movement for the displacement map', $_textDomain) . '">' . __("Rotation Y", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_rotationy" name="liquideffect_defaults_rotationy" value="' . $_rotationy . '" />
					<br>
					
					<span class="label" id="label_liquideffect_defaults_rotation" origtitle="' . __('2d rotation movement for the displacement map', $_textDomain) . '">' . __("Rotation", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_rotation" name="liquideffect_defaults_rotation" value="' . $_rotation . '" />
					<br>
					
					<span class="label" id="label_liquideffect_defaults_scalex" origtitle="' . __('Initial scaleX value for the displacement map', $_textDomain) . '">' . __("Scale X", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_scalex" name="liquideffect_defaults_scalex" value="' . $_scalex . '" />
					<br>
					
					<span class="label" id="label_liquideffect_defaults_scaley" origtitle="' . __('Initial scaleY value for the displacement map', $_textDomain) . '">' . __("Scale Y", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_scaley" name="liquideffect_defaults_scaley" value="' . $_scaley . '" />
					<br>
					
				</div>
				
			</div>
			
			<div id="liquideffect-transition-div" class="liquideffect-div" style="display: none">
			
				<span class="label" id="label_liquideffect_defaults_transition" origtitle="' . __('Enable special slide transition', $_textDomain) . '">' . __("Special Transition", $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="liquideffect_defaults_transition" name="liquideffect_defaults_transition"' . $_transition . ' 
					onchange="document.getElementById(\'liquideffect-transition-settings\').style.display=this.checked ? \'block\' : \'none\'" />
				
				<div id="liquideffect-transition-settings" class="withsublabels" style="display: ' . $_transitionEnabled . '">
					
					<span class="label" id="label_liquideffect_defaults_transcross" origtitle="' . __('Use back-to-back transitions', $_textDomain) . '">' . __("Stringed Transition", $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="liquideffect_defaults_transcross" name="liquideffect_defaults_transcross"' . $_transcross . '/>
					<br>
					
					<span class="label" id="label_liquideffect_defaults_transtime" origtitle="' . __('The duration time for the transition', $_textDomain) . '">' . __("Duration", $_textDomain) . '</span> 
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_transtime" name="liquideffect_defaults_transtime" value="' . $_transtime . '" /> ms
					<br>
					
					<span class="label" id="label_liquideffect_defaults_easing" origtitle="' . __('The easing for the transition', $_textDomain) . '">' . __("Easing", $_textDomain) . '</span> 
					<select class="withlabel" value="' . $_easing . '" id="liquideffect_defaults_easing" name="liquideffect_defaults_easing">';
							
						foreach($_easings as $_ease) {
							
							$_selected = $_ease !== $_easing ? '' : ' selected';
							$_markup .= '<option value="' . $_ease . '"' . $_selected . '>' . $_ease . '</option>';
							
						}
						
					$_markup .= '</select>
					<br>
					
					<span class="label" id="label_liquideffect_defaults_transpeedx" origtitle="' . __('Animate the speedX value by this offset number', $_textDomain) . '">' . __("Speed X Offset", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_transpeedx" name="liquideffect_defaults_transpeedx" value="' . $_transpeedx . '" />
					<br>
					
					<span class="label" id="label_liquideffect_defaults_transpeedy" origtitle="' . __('Animate the speedY value by this offset number', $_textDomain) . '">' . __("Speed Y Offset", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_transpeedy" name="liquideffect_defaults_transpeedy" value="' . $_transpeedy . '" />
					<br>
					
					<span class="label" id="label_liquideffect_defaults_transrotx" origtitle="' . __('Animate the rotationX value by this offset number', $_textDomain) . '">' . __("Rotation X Offset", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_transrotx" name="liquideffect_defaults_transrotx" value="' . $_transrotx . '" />
					<br>
					
					<span class="label" id="label_liquideffect_defaults_transroty" origtitle="' . __('Animate the rotationY value by this offset number', $_textDomain) . '">' . __("Rotation Y Offset", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_transroty" name="liquideffect_defaults_transroty" value="' . $_transroty . '" />
					<br>
					
					<span class="label" id="label_liquideffect_defaults_transrot" origtitle="' . __('Animate the 2D rotation value by this offset number', $_textDomain) . '">' . __("Rotation Offset", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_transrot" name="liquideffect_defaults_transrot" value="' . $_transrot . '" />
					<br>
				
					<span class="label" id="label_liquideffect_defaults_transitionx" origtitle="' . __('Animate the scaleX value by this offset number', $_textDomain) . '">' . __("Scale X Offset", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_transitionx" name="liquideffect_defaults_transitionx" value="' . $_transitionx . '" />
					<br>
					
					<span class="label" id="label_liquideffect_defaults_transitiony" origtitle="' . __('Animate the scaleY value by this offset number', $_textDomain) . '">' . __("Scale Y Offset", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_transitiony" name="liquideffect_defaults_transitiony" value="' . $_transitiony . '" />
					<br>
					
					<span class="label" id="label_liquideffect_defaults_transpower" origtitle="' . __('Apply extra power to the transition', $_textDomain) . '">' . __("Enhanced Distortion", $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="liquideffect_defaults_transpower" name="liquideffect_defaults_transpower"' . $_transpower . '/>
					<br>
					
				</div>
				
			</div>
			
			<div id="liquideffect-interactive-div" class="liquideffect-div" style="display: none">
			
				<span class="label" id="label_liquideffect_defaults_interactive" origtitle="' . __('Enable mouse interation for the effect', $_textDomain) . '">' . __("User Interactive", $_textDomain) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="liquideffect_defaults_interactive" name="liquideffect_defaults_interactive"' . $_interactive . ' 
					onchange="document.getElementById(\'liquideffect-interactive-settings\').style.display=this.checked ? \'block\' : \'none\'" />
				
				<div id="liquideffect-interactive-settings" class="withsublabels" style="display: ' . $_interactiveEnabled . '">
					
					<span class="label" id="label_liquideffect_defaults_event" origtitle="' . __('The mouse event to trigger the movement', $_textDomain) . '">' . __("Mouse Event", $_textDomain) . '</span>
					<select class="withlabel" id="liquideffect_defaults_event" name="liquideffect_defaults_event" value="' . $_event . '">';
						foreach($_events as $_evt) {
							
							$_selected = $_evt !== $_event ? '' : ' selected';
							$_markup .= '<option value="' . $_evt . '"' . $_selected . '>' . $_evt . '</option>';
							
						}
					
					$_markup .= '</select>
					
					<span class="label" id="label_liquideffect_defaults_intertime" origtitle="' . __('The mouse interaction transition time', $_textDomain) . '">' . __("Duration", $_textDomain) . '</span> 
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_intertime" name="liquideffect_defaults_intertime" value="' . $_intertime . '" /> ms
					<br>
					
					<span class="label" id="label_liquideffect_defaults_intereasing" origtitle="' . __('The easing function for the transition', $_textDomain) . '">' . __("Easing", $_textDomain) . '</span> 
					<select class="withlabel" value="' . $_intereasing . '" id="liquideffect_defaults_intereasing" name="liquideffect_defaults_intereasing">';
							
						foreach($_easings as $_ease) {
							
							$_selected = $_ease !== $_intereasing ? '' : ' selected';
							$_markup .= '<option value="' . $_ease . '"' . $_selected . '>' . $_ease . '</option>';
							
						}
						
					$_markup .= '</select>
					<br>
					
					<span class="label" id="label_liquideffect_defaults_interspeedx" origtitle="' . __('Animate the speedX value by this offset number', $_textDomain) . '">' . __("Speed X Offset", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_interspeedx" name="liquideffect_defaults_interspeedx" value="' . $_interspeedx . '" />
					<br>
					
					<span class="label" id="label_liquideffect_defaults_interspeedy" origtitle="' . __('Animate the speedY value by this offset number', $_textDomain) . '">' . __("Speed Y Offset", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_interspeedy" name="liquideffect_defaults_interspeedy" value="' . $_interspeedy . '" />
					<br>
				
					<span class="label" id="label_liquideffect_defaults_interscalex" origtitle="' . __('Animate the scaleX value by this offset number', $_textDomain) . '">' . __("Scale X Offset", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_interscalex" name="liquideffect_defaults_interscalex" value="' . $_interscalex . '" />
					<br>
					
					<span class="label" id="label_liquideffect_defaults_interscaley" origtitle="' . __('Animate the scaleY value by this offset number', $_textDomain) . '">' . __("Scale Y Offset", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_interscaley" name="liquideffect_defaults_interscaley" value="' . $_interscaley . '" />
					<br>
					
					<span class="label" id="label_liquideffect_defaults_interotation" origtitle="' . __('Animate the 2D rotation value by this offset number', $_textDomain) . '">' . __("Rotation Offset", $_textDomain) . '</span>
					<input type="text" class="text-sidebar withlabel" id="liquideffect_defaults_interotation" name="liquideffect_defaults_interotation" value="' . $_interotation . '" />
					<br>
					
					<span class="label" id="label_liquideffect_defaults_mobile" origtitle="' . __('Disable mouse events on mobile devices', $_textDomain) . '">' . __("Disable on Mobile", $_textDomain) . '</span>
					<input type="checkbox" class="tp-moderncheckbox withlabel" id="liquideffect_defaults_mobile" name="liquideffect_defaults_mobile"' . $_mobile . '/>
					<br>
					
				</div>
				
			</div>
			
			<style type="text/css">
				#liquideffect-default-settings select, #liquideffect-default-settings .button-primary {min-width: 130px !important}
				#liquideffect-default-settings .button-primary {margin: 2px 0 10px 1px !important}
				#liquideffect_custom img {max-width: 130px; height: auto}
			</style>
			
		</div>';
		
		static::$_Markup = $_markup;
		static::$_Icon = 'eg-icon-tint';
		static::$_JavaScript = '
			
			jQuery(function() {
				
				var menu = jQuery("#liquideffect-menu li").off().on("click", function() {
					
					menu.removeClass("selected");
					this.className = "selected";
					jQuery(".liquideffect-div").hide();
					document.getElementById(this.getAttribute("data-content")).style.display = "block";
					
				});
				
				document.getElementById("liquideffect_load_image").addEventListener("click", function() {
					
					UniteAdminRev.openAddImageDialog("' . $_selectimage . '", function(url, id) {
					
						document.getElementById("liquideffect_defaults_custommap").value = url;
						document.getElementById("liquideffect_customimg").src = url;
						document.getElementById("liquideffect_custom").style.display = "block";
					
					});
					
				});
				
				var url = "' . $_mapurl . '";
				jQuery("#liquideffect_defaults_image").on("change", function() {
					
					var val = this.options[this.selectedIndex].value, d1, d2, d3 = "block", src;
					if(val !== "Custom Map") {
						
						d1 = "block";
						d2 = "none";
						src = url + val.toLowerCase() + "_small.jpg";
						
					}
					else {
						
						d1 = "none";
						d2 = "inline-block";
						src = document.getElementById("liquideffect_defaults_custommap").value;
						if(!src) d3 = "none";
						
					}
					
					document.getElementById("liquideffect_size_div").style.display = d1;
					document.getElementById("liquideffect-load-custom").style.display = d2;
					document.getElementById("liquideffect_custom").style.display = d3;
					document.getElementById("liquideffect_customimg").src = src;
					
				});
				
				function loadSettings(settings) {
					
					for(var prop in settings) {
						
						if(!settings.hasOwnProperty(prop)) continue;
						
						var val = settings[prop],
							el = jQuery("#" + prop.replace("liquideffect_", "liquideffect_defaults_"));

						if(el.is("select") || el[0].type === "text") {
							
							el.val(val).change();
							
						}
						else {
						
							val = val == "true";
							el.prop("checked", val).change();
							
						}
						
					}
					
				}
				
				document.getElementById("liquideffect_templates").addEventListener("click", function() {
					
					if(window.confirm("' . $_confirm . '")) {
						
						var templates = document.getElementById("liquideffect_settings_templates"),
							val = templates.options[templates.selectedIndex].value;
							
						if(LiquidEffectTemplates.hasOwnProperty(val)) loadSettings(LiquidEffectTemplates[val]);
						
					}
					
				});
				
				jQuery("#liquideffect_settings_templates").on("change", function() {
					
					var display = this.options[this.selectedIndex].value !== "void" ? "inline-block" : "none";
					document.getElementById("liquideffect_templates_div").style.display = display;
					
				});
				
			});
			
			var LiquidEffectTemplates = ' . json_encode($_templates) . ';
		
		';
		
	}
	
	public function export_slider($data, $slides, $sliderParams, $useDummy) {
		
		foreach($slides as $slide) {
			
			$image = (isset($slide['params']) && isset($slide['params']['liquideffect_custommap'])) ? $slide['params']['liquideffect_custommap'] : '';
			if(!empty($image)) $data['usedImages'][$image] = true;
			
		}
		
		return $data;
		
	}
	
	public function import_slider($data, $slide_type, $image_path) {
		
		if(isset($data['params']) && isset($data['params']['liquideffect_custommap'])) {
			
			$image = $data['params']['liquideffect_custommap'];
			if(!empty($image)) {
				
				$url = RevSliderBase::check_file_in_zip($image_path, $image, $data['sliderParams']['alias'], $data['alreadyImported']);
				$url = RevSliderFunctionsWP::getImageUrlFromPath($url);
				if(!empty($url)) $data['params']['liquideffect_custommap'] = $url;
				
			}
		}

		return $data;
		
	}
	
}
?>