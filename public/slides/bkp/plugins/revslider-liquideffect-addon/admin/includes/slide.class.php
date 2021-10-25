<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_LIQUIDEFFECT_PLUGIN_PATH . 'framework/slide.admin.class.php');
require_once(RS_LIQUIDEFFECT_PLUGIN_PATH . 'admin/includes/templates.class.php');

class RsLiquidEffectSlideAdmin extends RsAddonLiquidEffectSlideAdmin {
	
	protected static $_Title,
					 $_Markup,
					 $_JavaScript;
	
	public function __construct($_title) {
		
		static::$_Title = $_title;
		parent::init();
		
	}

	protected static function _init($_slider, $_slide) {
		
		$_slider = $_slider->getParams();
		
		$_def_image       = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_image',     'ripple');
		$_def_custommap   = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_custommap', '');
		$_def_size        = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_size',      'Large');
		
		$_def_autoplay    = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_autoplay',  true);
		$_def_speedx      = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_speedx',    '2');
		$_def_speedy      = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_speedy',    '20');
		$_def_scalex      = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_scalex',    '20');
		$_def_scaley      = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_scaley',    '20');
		$_def_rotationx   = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_rotationx', '20');
		$_def_rotationy   = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_rotationy', '0');
		$_def_rotation    = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_rotation',  '0');
		
		$_def_transition  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transition',  true);
		$_def_transcross  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transcross',  true);
		$_def_transpower  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transpower',  false);
		$_def_transtime   = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transtime',   '2000');
		$_def_easing      = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_easing',      'Power3.easeOut');
		$_def_transitionx = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transitionx', '2');
		$_def_transitiony = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transitiony',  '1280');
		$_def_transpeedx  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transpeedx',   '2');
		$_def_transpeedy  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transpeedy',   '100');
		$_def_transrotx   = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transrotx',    '20');
		$_def_transroty   = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transroty',    '0');
		$_def_transrot    = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_transrot',     '0');
		
		$_def_interactive  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_interactive',  false);
		$_def_mobile       = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_mobile',       false);
		$_def_event        = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_event',        'mousemove');
		$_def_intertime    = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_intertime',    '500');
		$_def_intereasing  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_intereasing',  'Power2.easeOut');
		$_def_interscalex  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_interscalex',  '2');
		$_def_interscaley  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_interscaley',  '1280');
		$_def_interotation = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_interotation', '0');
		$_def_interspeedx  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_interspeedx',  '0');
		$_def_interspeedy  = RevSliderFunctions::getVal($_slider, 'liquideffect_defaults_interspeedy',  '0');
		
		$_enabled  = $_slide->getParam('liquideffect_enabled',  false) == 'true' ? ' checked' : '';
		$_autoplay = $_slide->getParam('liquideffect_autoplay', $_def_autoplay) == 'true' ? ' checked' : '';
		
		$_image     = $_slide->getParam('liquideffect_image',     $_def_image);
		$_custommap = $_slide->getParam('liquideffect_custommap', $_def_custommap);
		$_size      = $_slide->getParam('liquideffect_size',      $_def_size);
		
		$_speedx = $_slide->getParam('liquideffect_speedx', $_def_speedx);
		$_speedy = $_slide->getParam('liquideffect_speedy', $_def_speedy);
		
		$_rotationx = $_slide->getParam('liquideffect_rotationx', $_def_rotationx);
		$_rotationy = $_slide->getParam('liquideffect_rotationy', $_def_rotationy);
		$_rotation  = $_slide->getParam('liquideffect_rotation',  $_def_rotation);
		
		$_scalex = $_slide->getParam('liquideffect_scalex', $_def_scalex);
		$_scaley = $_slide->getParam('liquideffect_scaley', $_def_scaley);
		
		$_transition  = $_slide->getParam('liquideffect_transition',  $_def_transition) == 'true' ? ' checked' : '';
		$_transcross  = $_slide->getParam('liquideffect_transcross',  $_def_transcross) == 'true' ? ' checked' : '';
		$_transpower  = $_slide->getParam('liquideffect_transpower',  $_def_transpower) == 'true' ? ' checked' : '';
		
		$_transtime   = $_slide->getParam('liquideffect_transtime',   $_def_transtime);
		$_easing      = $_slide->getParam('liquideffect_easing',      $_def_easing);
		$_transitionx = $_slide->getParam('liquideffect_transitionx', $_def_transitionx);
		$_transitiony = $_slide->getParam('liquideffect_transitiony', $_def_transitiony);
		$_transpeedx  = $_slide->getParam('liquideffect_transpeedx',  $_def_transpeedx);
		$_transpeedy  = $_slide->getParam('liquideffect_transpeedy',  $_def_transpeedy);
		$_transrotx   = $_slide->getParam('liquideffect_transrotx',   $_def_transrotx);
		$_transroty   = $_slide->getParam('liquideffect_transroty',   $_def_transroty);
		$_transrot    = $_slide->getParam('liquideffect_transrot',    $_def_transrot);
		
		$_interactive  = $_slide->getParam('liquideffect_interactive',  $_def_interactive) == 'true' ? ' checked' : '';
		$_mobile       = $_slide->getParam('liquideffect_mobile',       $_def_mobile)      == 'true' ? ' checked' : '';
		$_event        = $_slide->getParam('liquideffect_event',        $_def_event);
		$_intertime    = $_slide->getParam('liquideffect_intertime',    $_def_intertime);
		$_intereasing  = $_slide->getParam('liquideffect_intereasing',  $_def_intereasing);
		$_interscalex  = $_slide->getParam('liquideffect_interscalex',  $_def_interscalex);
		$_interscaley  = $_slide->getParam('liquideffect_interscaley',  $_def_interscaley);
		$_interotation = $_slide->getParam('liquideffect_interotation', $_def_interotation);
		$_interspeedx  = $_slide->getParam('liquideffect_interspeedx',  $_def_interspeedx);
		$_interspeedy  = $_slide->getParam('liquideffect_interspeedy',  $_def_interspeedy);
		
		$_autoplayEnabled    = $_autoplay    ? 'block' : 'none';
		$_interactiveEnabled = $_interactive ? 'block' : 'none';
		$_transitionEnabled  = $_transition  ? 'block' : 'none';
		$_templatebtndisplay = $_image !== 'Custom Map' ? 'inline-block' : 'none';
		$_loadcustommap      = $_image !== 'Custom Map' ? 'none' : 'inline-block';
		$_custommapdisplay   = $_image === 'Custom Map' && empty($_custommap) ? 'none' : 'block';
		
		$_mapurl             = RS_LIQUIDEFFECT_PLUGIN_URL . 'public/assets/images/';
		$_preview            = $_image !== 'Custom Map' ? $_mapurl . strtolower($_image) . '_small.jpg' : $_custommap;
		
		$_textDomain = 'rs_' . static::$_Title;
		$_templates  = RsLiquidEffectTemplates::$_Templates;
		
		$_selectimage = __('Select Image');
		$_confirm     = __('Load default settings for this image map? (will override current settings).', $_textDomain);
		
		$_maps    = array('Ripple', 'Clouds', 'Crystalize', 'Fibers', 'Pointilize', 'Rings', 'Spiral', 'Maze', 'Glitch', 'Swirl', 'Custom Map');
		$_events  = array('mousedown', 'mousemove');
		$_sizes   = array('Large', 'Small');
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
		
		if($_enabled) {
			
			$_isEnabled = 'true';
			$_showSettings = 'block';
			
		}
		else {
			
			$_isEnabled = 'false';
			$_showSettings = 'none';
			
		}
		
		$_markup = 
		
		'<div style="padding: 2px 15px">

			<div>
				
				<p>
			
					<label>' . __('Enable AddOn:', $_textDomain) . '</label> 
					<span style="display:inline-block; margin-right:20px;line-height:27px">
						<input type="checkbox" id="liquideffect_enabled" name="liquideffect_enabled" class="tp-moderncheckbox"' . $_enabled . ' data-skip="true" />
					</span>
					<span class="description" id="liquid-bg-enabled">' . __('Enable the Liquid Effect Add-On for this Slide', $_textDomain) . '</span>
					<span class="description" id="liquid-bg-disabled" style="color: #c0392b; font-style: italic; font-weight: bold">' . __('Important Notice: Slide\'s Main Background Image not set!', $_textDomain) . '</span>
					
				</p>
				
			</div>
				
		</div>
			
		<div style="background-color: #DDD; height: 1px"></div>
			
		<div id="liquideffect-settings-div" style="padding: 15px; display: ' . $_showSettings . '">
					
			<div>	
					
				<ul id="liquideffect-menu" class="rs-layer-main-image-tabs" style="padding-bottom: 10px">
					<li data-content="liquideffect-map-div" class="selected">General</li>	
					<li data-content="liquideffect-animate-div">Animation</li>					
					<li data-content="liquideffect-transition-div">Slide Transition</li>
					<li data-content="liquideffect-interactive-div">Interaction</li>
				</ul>
				
				<div id="liquideffect-map-div" class="liquideffect-div">
				
					<p>
						
						<label>' . __('Settings Template:', $_textDomain) . '</label> 
						<select id="liquideffect_settings_templates" value="" data-skip="true">
							
							<option value="void">- Load Settings Template -</option>';
							foreach($_templates as $_key => $_value) {
								
								$_title = preg_replace("/\_/", " ", $_key);
								$_markup .= '<option value="' . $_key . '">' . $_title . '</option>';
								
							}
							
						$_markup .= '</select>
						
						<a href="javascript:void(0)" id="liquideffect-load-map" class="button-primary revblue" style="vertical-align: top; margin-right: 20px; min-width: 110px; display: none">Load Template
						</a><span class="description">' . __('Select/Load a prebuilt settings template', $_textDomain) . '</span>
					
					</p>
					
					<p style="margin-bottom: 0">
						
						<label>' . __('Distortion Map:', $_textDomain) . '</label> 
						<select id="liquideffect_image" name="liquideffect_image" value="' . $_image . '">';
								
							foreach($_maps as $_map) {
								
								$_selected = $_map !== $_image ? '' : ' selected';
								$_markup .= '<option value="' . $_map . '"' . $_selected . '>' . $_map . '</option>';
								
							}
							
						$_markup .= '</select>
						<a href="javascript:void(0)" id="liquideffect-load-custom" class="button-primary revblue" style="vertical-align: top; text-align: center; margin-right: 20px; min-width: 110px; display: ' . $_loadcustommap . '">Choose Image
						</a><span class="description">' . __('Select/Upload a Custom Map', $_textDomain) . '</span>
						
					</p>
					
					<p id="liquideffect_size_div" style="margin-bottom: 0; display: ' . $_templatebtndisplay . '">
						
						<label>' . __('Map Size:', $_textDomain) . '</label> 
						<select class="withlabel" id="liquideffect_size" name="liquideffect_size" value="' . $_size . '">';
				
							foreach($_sizes as $_sizing) {
								
								$_selected = $_sizing !== $_size ? '' : ' selected';
								$_markup .= '<option value="' . $_sizing . '"' . $_selected . '>' . $_sizing . '</option>';
								
							}
						
						$_markup .= '</select>
						<span class="description">' . __('Small = 512x512 version, Large = 2048x2048 version', $_textDomain) . '</span>
					
					</p>
					
					<p id="liquideffect_custom" style="display:' . $_custommapdisplay . '">
					
						<label></label> 
						<img id="liquideffect_customimg" src="' . $_preview . '">
						<input type="hidden" id="liquideffect_custommap" name="liquideffect_custommap" value="' . $_custommap . '">
					
					</p>
				
				</div>
			
				<div id="liquideffect-animate-div" class="liquideffect-div" style="display: none">
					
					<p>
					
						<label>' . __('Auto Animate:', $_textDomain) . '</label> 
						<span style="display:inline-block; width:200px; margin-right:20px;line-height:27px">
							<input type="checkbox" id="liquideffect_autoplay" name="liquideffect_autoplay" class="tp-moderncheckbox"' . $_autoplay . ' onchange="document.getElementById(\'liquideffect-autoplay-settings\').style.display = this.checked ? \'block\' : \'none\';" />
						</span>
						<span class="description">' . __('Continously animate the Slide\'s main background image', $_textDomain) . '</span>
						
					</p>
					
					<div id="liquideffect-autoplay-settings" style="display: ' . $_autoplayEnabled . '">
					
						<p>
							<label>' . __('Speed X:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_speedx" name="liquideffect_speedx" value="' . $_speedx . '">
							<span class="description">' . __('Speed for the displacement map\'s left movement', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Speed Y:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_speedy" name="liquideffect_speedy" value="' . $_speedy . '">
							<span class="description">' . __('Speed for the displacement map\'s top movement', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Rotation X:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_rotationx" name="liquideffect_rotationx" value="' . $_rotationx . '">
							<span class="description">' . __('rotationX movement for the displacement map', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Rotation Y:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_rotationy" name="liquideffect_rotationy" value="' . $_rotationy . '">
							<span class="description">' . __('rotationY movement for the displacement map', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Rotation (2D):', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_rotation" name="liquideffect_rotation" value="' . $_rotation . '">
							<span class="description">' . __('2d rotation movement for the displacement map', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Scale X:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_scalex" name="liquideffect_scalex" value="' . $_scalex . '">
							<span class="description">' . __('Initial scaleX value for the displacement map', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Scale Y:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_scaley" name="liquideffect_scaley" value="' . $_scaley . '">
							<span class="description">' . __('Initial scaleY value for the displacement map', $_textDomain) . '</span>
						</p>
					
					</div>
					
				</div>
				
				<div id="liquideffect-transition-div" class="liquideffect-div" style="display: none">
				
					<p>
					
						<label>' . __('Slide Transition:', $_textDomain) . '</label> 
						<span style="display:inline-block; width:200px; margin-right:20px;line-height:27px">
							<input type="checkbox" id="liquideffect_transition" name="liquideffect_transition" class="tp-moderncheckbox"' . $_transition . ' onchange="document.getElementById(\'liquideffect-transition-settings\').style.display = this.checked ? \'block\' : \'none\';" />
						</span>
						<span class="description">' . __('Enable special slide transition', $_textDomain) . '</span>
						
					</p>
					
					<div id="liquideffect-transition-settings" style="display: ' . $_transitionEnabled . '">
						
						<p>
							<label>' . __('Stringed Transition:', $_textDomain) . '</label> 
							<span style="display:inline-block; width:200px; margin-right:20px;line-height:27px">
								<input type="checkbox" id="liquideffect_transcross" name="liquideffect_transcross" class="tp-moderncheckbox"' . $_transcross . ' />
							</span>
							<span class="description">' . __('Use back-to-back transitions', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Duration:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_transtime" name="liquideffect_transtime" value="' . $_transtime . '">
							<span class="description">' . __('The transition\'s total time', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Easing:', $_textDomain) . '</label>
							<select value="' . $_easing . '" id="liquideffect_easing" name="liquideffect_easing">';
								
								foreach($_easings as $_ease) {
									
									$_selected = $_ease !== $_easing ? '' : ' selected';
									$_markup .= '<option value="' . $_ease . '"' . $_selected . '>' . $_ease . '</option>';
									
								}
								
							$_markup .= '</select>
							<span class="description">' . __('The easing function applied to the transition', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Speed X Offset:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_transpeedx" name="liquideffect_transpeedx" value="' . $_transpeedx . '">
							<span class="description">' . __('Animate the speedX value by this offset number', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Speed Y Offset:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_transpeedy" name="liquideffect_transpeedy" value="' . $_transpeedy . '">
							<span class="description">' . __('Animate the speedY value by this offset number', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Rotation X Offset:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_transrotx" name="liquideffect_transrotx" value="' . $_transrotx . '">
							<span class="description">' . __('Animate the rotationX value by this offset number', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Rotation Y Offset:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_transroty" name="liquideffect_transroty" value="' . $_transroty . '">
							<span class="description">' . __('Animate the rotationY value by this offset number', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Rotation (2D) Offset:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_transrot" name="liquideffect_transrot" value="' . $_transrot . '">
							<span class="description">' . __('Animate the 2D rotation value by this offset number', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Scale X Offset:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_transitionx" name="liquideffect_transitionx" value="' . $_transitionx . '">
							<span class="description">' . __('Animate the scaleX value by this offset number', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Scale Y Offset:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_transitiony" name="liquideffect_transitiony" value="' . $_transitiony . '">
							<span class="description">' . __('Animate the scaleY value by this offset number', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Enhanced Distortion:', $_textDomain) . '</label> 
							<span style="display:inline-block; width:200px; margin-right:20px;line-height:27px">
								<input type="checkbox" id="liquideffect_transpower" name="liquideffect_transpower" class="tp-moderncheckbox"' . $_transpower . ' />
							</span>
							<span class="description">' . __('Apply extra power to the transition', $_textDomain) . '</span>
						</p>
					
					</div>
					
				</div>
				
				<div id="liquideffect-interactive-div" class="liquideffect-div" style="display: none">
				
					<p>
					
						<label>' . __('User Interactive:', $_textDomain) . '</label> 
						<span style="display:inline-block; width:200px; margin-right:20px;line-height:27px">
							<input type="checkbox" id="liquideffect_interactive" name="liquideffect_interactive" class="tp-moderncheckbox"' . $_interactive . ' onchange="document.getElementById(\'liquideffect-interactive-settings\').style.display = this.checked ? \'block\' : \'none\';" />
						</span>
						<span class="description">' . __('Enable mouse interation for the effect', $_textDomain) . '</span>
						
					</p>
					
					<div id="liquideffect-interactive-settings" style="display: ' . $_interactiveEnabled . '">
						
						<p>
							<label>' . __('Mouse Event:', $_textDomain) . '</label> 
							<select id="liquideffect_event" name="liquideffect_event" value="' . $_event . '">';
								foreach($_events as $_evt) {
									
									$_selected = $_evt !== $_event ? '' : ' selected';
									$_markup .= '<option value="' . $_evt . '"' . $_selected . '>' . $_evt . '</option>';
									
								}
							
							$_markup .= '</select>
							<span class="description">' . __('The mouse event to trigger the movement', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Duration:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_intertime" name="liquideffect_intertime" value="' . $_intertime . '">
							<span class="description">' . __('The mouse interaction transition time', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Easing:', $_textDomain) . '</label>
							<select value="' . $_easing . '" id="liquideffect_intereasing" name="liquideffect_intereasing">';
								
								foreach($_easings as $_ease) {
									
									$_selected = $_ease !== $_intereasing ? '' : ' selected';
									$_markup .= '<option value="' . $_ease . '"' . $_selected . '>' . $_ease . '</option>';
									
								}
								
							$_markup .= '</select>
							<span class="description">' . __('The easing function for the transition', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Speed X Offset:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_interspeedx" name="liquideffect_interspeedx" value="' . $_interspeedx . '">
							<span class="description">' . __('Animate the speedX value by this offset number', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Speed Y Offset:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_interspeedy" name="liquideffect_interspeedy" value="' . $_interspeedy . '">
							<span class="description">' . __('Animate the speedY value by this offset number', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Scale X Offset:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_interscalex" name="liquideffect_interscalex" value="' . $_interscalex . '">
							<span class="description">' . __('Animate the scaleX value by this offset number', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Scale Y Offset:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_interscaley" name="liquideffect_interscaley" value="' . $_interscaley . '">
							<span class="description">' . __('Animate the scaleY value by this offset number', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Rotation Offset:', $_textDomain) . '</label> 
							<input type="text" class="small-text" id="liquideffect_interotation" name="liquideffect_interotation" value="' . $_interotation . '">
							<span class="description">' . __('Animate the 2D rotation value by this offset number', $_textDomain) . '</span>
						</p>
						
						<p>
							<label>' . __('Disable on Mobile:', $_textDomain) . '</label> 
							<span style="display:inline-block; width:200px; margin-right:20px;line-height:27px">
								<input type="checkbox" id="liquideffect_mobile" name="liquideffect_mobile" class="tp-moderncheckbox"' . $_mobile . ' />
							</span>
							<span class="description">' . __('Disable mouse events on mobile devices', $_textDomain) . '</span>
						</p>
					
					</div>
					
				</div>
				
			</div>
			
			<style type="text/css">
			
				#liquideffect_custom img {max-width: 100px; height: auto}
				.liquideffect-bg-message #liquideffect-settings-div {opacity: 0.5; pointer-events: none}
				.mainbg-sub-kenburns-selector {display: inline-block !important}
				.liquideffect-enabled .mainbg-sub-kenburns-selector, .liquideffect-enabled .mainbg-sub-filtres-selector, .liquideffect-enabled #v_sgs_mp_4 {display: none !important}
				#rs-addon-settings-trigger-liquideffect {border-radius: 0px; padding: 0px; color: transparent; background: url(' . RS_LIQUIDEFFECT_PLUGIN_URL . 'admin/assets/images/addon_liquideffect.png); background-size: 153px 54px; background-position: top center; width: 153px; height: 27px}
				#rs-addon-settings-trigger-liquideffect.selected, #rs-addon-settings-trigger-liquideffect:hover {background-position: bottom center}
				
			</style>
			
		</div>'; 
		
		static::$_Markup     = $_markup;
		static::$_JavaScript = '
		
			jQuery(function() {
				
				if(jQuery(".rev_static_layers").length) {
					
					var numAddOns = jQuery("#rs-addon-wrapper-button-row").children();
					if(numAddOns.length === 2) jQuery(".rs-slide-settings-tabs").children().last().remove();
					else jQuery("#rs-addon-settings-trigger-liquideffect").parent().remove();
					return;
					
				}
				
				var menu = jQuery("#liquideffect-menu li").off().on("click", function() {
					
					menu.removeClass("selected");
					this.className = "selected";
					jQuery(".liquideffect-div").hide();
					document.getElementById(this.getAttribute("data-content")).style.display = "block";
					
				});
				
				var filter,
					url = "' . $_mapurl . '";
				
				jQuery("#liquideffect_image").on("change", function() {
					
					var val = this.options[this.selectedIndex].value, d1, d2, d3 = "block", src;
					if(val !== "Custom Map") {
						
						d1 = "block";
						d2 = "none";
						src = url + val.toLowerCase() + "_small.jpg";
						
					}
					else {
						
						d1 = "none";
						d2 = "inline-block";
						src = document.getElementById("liquideffect_custommap").value;
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
							el = jQuery("#" + prop);
						
						if(el.is("select") || el[0].type === "text") {
							el.val(val).change();
						}
						else {
							val = val == "true";
							el.prop("checked", val).change();
						}
						
					}
					
				}
				
				document.getElementById("liquideffect-load-map").addEventListener("click", function() {

					if(window.confirm("' . $_confirm . '")) {
						
						var templates = document.getElementById("liquideffect_settings_templates"),
							val = templates.options[templates.selectedIndex].value;
							
						if(LiquidEffectTemplates.hasOwnProperty(val)) loadSettings(LiquidEffectTemplates[val]);
						
					}
					
				});
				
				document.getElementById("liquideffect-load-custom").addEventListener("click", function() {
					
					UniteAdminRev.openAddImageDialog("' . $_selectimage . '", function(url, id) {
					
						document.getElementById("liquideffect_custommap").value = url;
						document.getElementById("liquideffect_customimg").src = url;
						document.getElementById("liquideffect_custom").style.display = "block";
					
					});
					
				});
				
				jQuery("#liquideffect_enabled").on("change", function() {
					
					var display = this.checked ? "block" : "none";
					document.getElementById("liquideffect-settings-div").style.display = display;
					checkEnabled(this.checked);
					
				});
				
				jQuery("#liquideffect_settings_templates").on("change", function() {
					
					var display = this.options[this.selectedIndex].value !== "void" ? "inline-block" : "none";
					document.getElementById("liquideffect-load-map").style.display = display;
					
				});
				
				function checkBg() {
					
					var bgSet = document.getElementById("radio_back_image").checked;
					if(bgSet) bgSet = document.getElementById("image_url").value;
					
					var d1 = document.getElementById("liquid-bg-enabled"),
						d2 = document.getElementById("liquid-bg-disabled");
					
					if(bgSet) {
						d1.style.display = "inline";
						d2.style.display = "none";
					}
					else {
						d2.style.display = "inline";
						d1.style.display = "none";
					}
					
				}
				
				function checkEnabled(enabled) {
					
					var method = enabled ? "addClass" : "removeClass";
					jQuery("body")[method]("liquideffect-enabled");
					
					if(enabled) {
						
						filter = jQuery(".inst-filter-griditem.selected").attr("data-type");
						jQuery("#inst-filter-grid > div").first().click();
						
					}
					else if(filter !== "none") {
						
						jQuery(".inst-filter-griditem[data-type=" + filter + "]").click();
						filter = "none";
						
					}
					
					checkBg();
					
				}
				
				checkEnabled(' . $_isEnabled . ');
				jQuery("li[data-content=#slide-addon-wrapper]").on("click", checkBg);
				
			});
			
			var LiquidEffectTemplates = ' . json_encode($_templates) . ';
		
		';
		
	}
}
?>