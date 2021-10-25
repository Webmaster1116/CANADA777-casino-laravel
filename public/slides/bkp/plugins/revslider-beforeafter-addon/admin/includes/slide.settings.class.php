<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_BEFOREAFTER_PLUGIN_PATH . 'framework/slide.settings.admin.class.php');

class RsBeforeAfterSlideSettingsAdmin extends RsAddonBeforeAfterSlideSettingsAdmin {
	
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
		
		$_def_direction    = $_slider->getParam('beforeafter_def_direction',    'horizontal');
		$_def_delay        = $_slider->getParam('beforeafter_def_delay',        '500');
		$_def_time         = $_slider->getParam('beforeafter_def_time',         '750');
		$_def_easing       = $_slider->getParam('beforeafter_def_easing',       'Power2.easeInOut');
		$_def_animateout   = $_slider->getParam('beforeafter_def_animateout',   'fade');
		$_def_bouncearrows = $_slider->getParam('beforeafter_def_bouncearrows', 'none');
		$_def_bouncetype   = $_slider->getParam('beforeafter_def_bouncetype',   'repel');
		$_def_bounceamount = $_slider->getParam('beforeafter_def_bounceamount', '5');
		$_def_bouncespeed  = $_slider->getParam('beforeafter_def_bouncespeed',  '1500');
		$_def_bounceeasing = $_slider->getParam('beforeafter_def_bounceeasing', 'ease-in-out');
		$_def_bouncedelay  = $_slider->getParam('beforeafter_def_bouncedelay',  '0');
		$_def_shiftarrows  = $_slider->getParam('beforeafter_def_shiftarrows',  false);
		$_def_shiftoffset  = $_slider->getParam('beforeafter_def_shiftoffset',  '10');
		$_def_shifttiming  = $_slider->getParam('beforeafter_def_shifttiming',  '300');
		$_def_shifteasing  = $_slider->getParam('beforeafter_def_shifteasing',  'ease');
		$_def_shiftdelay   = $_slider->getParam('beforeafter_def_shiftdelay',   '0');
		
		$_enabled      = $_slide->getParam('beforeafter_enabled',      false) == 'true' ? ' checked' : '';
		$_direction    = $_slide->getParam('beforeafter_direction',    $_def_direction);
		$_delay        = $_slide->getParam('beforeafter_delay',        $_def_delay);
		$_time         = $_slide->getParam('beforeafter_time',         $_def_time);
		$_easing       = $_slide->getParam('beforeafter_easing',       $_def_easing);
		$_animateout   = $_slide->getParam('beforeafter_animateout',   $_def_animateout);
		
		$_bounceArrows = $_slide->getParam('beforeafter_bouncearrows', $_def_bouncearrows);
		$_bounceType   = $_slide->getParam('beforeafter_bouncetype',   $_def_bouncetype);
		$_bounceAmount = $_slide->getParam('beforeafter_bounceamount', $_def_bounceamount);
		$_bounceSpeed  = $_slide->getParam('beforeafter_bouncespeed',  $_def_bouncespeed);
		$_bounceEasing = $_slide->getParam('beforeafter_bounceeasing', $_def_bounceeasing);
		$_bounceDelay  = $_slide->getParam('beforeafter_bouncedelay',  $_def_bouncedelay);
		
		$_shiftArrows = $_slide->getParam('beforeafter_shiftarrows', $_def_shiftarrows) == 'true' ? ' checked' : '';
		$_shiftOffset = $_slide->getParam('beforeafter_shiftoffset', $_def_shiftoffset);
		$_shiftTiming = $_slide->getParam('beforeafter_shifttiming', $_def_shifttiming);
		$_shiftEasing = $_slide->getParam('beforeafter_shifteasing', $_def_shifteasing);
		$_shiftDelay  = $_slide->getParam('beforeafter_shiftdelay',  $_def_shiftdelay);
		
		$_bgSource   = $_slide->getParam('background_type_beforeafter', 'trans');
		$_bgColor    = $_slide->getParam('bg_color_beforeafter',        '#e7e7e7');
		$_bgImageUrl = $_slide->getParam('image_url_beforeafter',       '');
		$_bgImageID  = $_slide->getParam('image_id_beforeafter',        '');
		$_bgExternal = $_slide->getParam('bg_external_beforeafter',     '');
		$_bgYoutube  = $_slide->getParam('bg_youtube_beforeafter',      '');
		$_bgVimeo    = $_slide->getParam('bg_vimeo_beforeafter',        '');
		$_bgMpeg     = $_slide->getParam('bg_mpeg_beforeafter',         '');
		$_bgWebm     = $_slide->getParam('bg_webm_beforeafter',         '');
		$_bgOgv      = $_slide->getParam('bg_ogv_beforeafter',          '');
		
		$_bgSize    = $_slide->getParam('image_source_type_beforeafter', '');
		$_bgFit     = $_slide->getParam('bg_fit_beforeafter',            'cover');
		$_bgFitX    = $_slide->getParam('bg_fit_x_beforeafter',          '100');
		$_bgFitY    = $_slide->getParam('bg_fit_y_beforeafter',          '100');
		$_bgPos     = $_slide->getParam('bg_position_beforeafter',       'center center');
		$_bgPosX    = $_slide->getParam('bg_position_x_beforeafter',     '0');
		$_bgPosY    = $_slide->getParam('bg_position_y_beforeafter',     '0');
		$_bgRepeat  = $_slide->getParam('bg_repeat_beforeafter',         'no-repeat');
		
		$_overlay       = $_slide->getParam('video_dotted_overlay_beforeafter', 'none');
		$_aspect        = $_slide->getParam('video_ratio_beforeafter',          '16:9');
		$_startAt       = $_slide->getParam('video_start_at_beforeafter',       '');
		$_endAt         = $_slide->getParam('video_end_at_beforeafter',         '');
		$_loopVideo     = $_slide->getParam('video_loop_beforeafter',           'none');
		$_videoVolume   = $_slide->getParam('video_volume_beforeafter',         '');
		$_videoSpeed    = $_slide->getParam('video_speed_beforeafter',          '1');
		$_youtubeParams = $_slide->getParam('video_arguments_beforeafter',      'hd=1&wmode=opaque&showinfo=0&rel=0;');
		$_vimeoParams   = $_slide->getParam('video_arguments_vim_beforeafter',  'title=0&byline=0&portrait=0&api=1');
		
		$_forceCover = $_slide->getParam('video_force_cover_beforeafter',  true)  == 'true' ? ' checked' : '';
		$_rewind     = $_slide->getParam('video_force_rewind_beforeafter', true)  == 'true' ? ' checked' : '';
		$_muteVideo  = $_slide->getParam('video_mute_beforeafter',         true)  == 'true' ? ' checked' : '';
		$_nextSlide  = $_slide->getParam('video_nextslide_beforeafter',    false) == 'true' ? ' checked' : '';
		
		$_textDomain    = 'rs_' . static::$_Title;
		$_show_settings = !empty($_enabled)         ? 'block' : 'none';
		$_shiftOptions  = !empty($_shiftArrows)     ? 'block' : 'none';
		$_bounceOptions = $_bounceArrows !== 'none' ? 'block' : 'none';
		
		$_bounces       = array('none' => 'None', 'initial' => 'On Initial Reveal', 'infinite' => 'Infinite Loop', 'once' => 'Until First Grab');
		$_eases         = array('linear', 'ease', 'ease-out', 'ease-in', 'ease-in-out');
		$_directions    = array('horizontal', 'vertical');
		$_bounceTypes   = array('repel', 'attract');
		$_outs          = array('fade', 'collapse');
		$_easings       = array(
			
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
		
		$_slides = $_slider->getSlides();
		$_bgs = array();
		
		foreach($_slides as $_slide) {
			
			$_active = $_slide->getParam('beforeafter_enabled', false) == 'true';
			$_type   = $_slide->getParam('background_type_beforeafter', 'trans');
			$_bg     = 'transparent';
			
			switch($_type) {
				
				case 'image':
				
					$_id = $_slide->getParam('image_id_beforeafter', false);
					if(!empty($_id)) {
						
						$_bg = wp_get_attachment_image_src($_id);
						if(!empty($_bg)) $_bg = $_slide->getParam('image_url_beforeafter', '');
						
					}
					else {
						
						if(!empty($_bg)) $_bg = $_slide->getParam('image_url_beforeafter', '');
						
					}
					
				break;
				
				case 'solid':
				
					$_bg = $_slide->getParam('bg_color_beforeafter', 'transparent');
				
				break;
				
				case 'trans':
				
					$_bg = 'transparent';
				
				break;
				
				case 'external':
				
					$_bg = $_slide->getParam('bg_external_beforeafter', '');
				
				break;
				
			}
			
			$_bgs[] = array('active' => $_active, 'type' => $_type, 'source' => $_bg);
			
		}
		
		$_markup = '<div id="beforeafter-addon-settings-wrap">
			
			<p>
				<label>Enable/Disable:</label>
				<input type="checkbox" id="beforeafter_enabled" name="beforeafter_enabled" class="tp-moderncheckbox"' . $_enabled . ' onchange="document.getElementById(\'beforeafter-settings\').style.display=this.checked ? \'block\' : \'none\'" />
				<span class="description" style="margin-left: 20px">' . __('Activate the Before/After Add-On for this Slide', $_textDomain) . '</span>
			</p>
			
			<div id="beforeafter-settings" style="display: ' . $_show_settings . '">
				
				<p>
					<label>' . __('Reveal Direction:', $_textDomain) . '</label>
					<select class="rs-layer-input-field" id="beforeafter_direction" name="beforeafter_direction" value="' . $_direction . '">';
					
						foreach($_directions as $_direct) {
							
							$_selected = $_direct === $_direction ? ' selected' : '';
							$_markup .= '<option value="' . $_direct . '"' . $_selected . '>' . __(ucfirst($_direct), $_textDomain) . '</option>';
							
						}
						
					$_markup .= '</select>
					<span class="description">' . __('Reveal content from left to right or top to bottom', $_textDomain) . '</span>
				</p>
				
				<p>
					<label>' . __('Animation Delay:', $_textDomain) . '</label>
					<input type="text" class="small-text before-after-input" id="beforeafter_delay" name="beforeafter_delay" value="' . $_delay . '" data-default-value="' . $_delay . '">
					<span class="description">' . __('Optional delay in milliseconds for the initial reveal', $_textDomain) . '</span>
				</p>
				
				<p>
					<label>' . __('Animation Duration:', $_textDomain) . '</label>
					<input type="text" class="small-text before-after-input" id="beforeafter_time" name="beforeafter_time" value="' . $_time . '" data-default-value="' . $_time . '">
					<span class="description">' . __('The initial reveal\'s animation time', $_textDomain) . '</span>
				</p>
				
				<p>
					<label>' . __('Animation Easing:', $_textDomain) . '</label>
					<select value="' . $_easing . '" id="beforeafter_easing" name="beforeafter_easing">';
						
						foreach($_easings as $_ease) {
							
							$_selected = $_ease !== $_easing ? '' : ' selected';
							$_markup .= '<option value="' . $_ease . '"' . $_selected . '>' . $_ease . '</option>';
							
						}
						
					$_markup .= '</select>
					<span class="description">' . __('The initial reveal animation\'s transition type', $_textDomain) . '</span>
				</p>
				
				<p>
					<label>' . __('Animation Out:', $_textDomain) . '</label>
					<select value="' . $_animateout . '" id="beforeafter_animateout" name="beforeafter_animateout">';
						
						foreach($_outs as $_out) {
							
							$_selected = $_out !== $_animateout ? '' : ' selected';
							$_markup .= '<option value="' . $_out . '"' . $_selected . '>' . ucfirst($_out) . '</option>';
							
						}
						
					$_markup .= '</select>
					<span class="description">' . __('Choose how the "After" content should animate out when the slide changes', $_textDomain) . '</span>
				</p>
				
				<p>
					<label>' . __('Arrows Teaser:', $_textDomain) . '</label>
					<select value="' . $_bounceArrows . '" id="beforeafter_bouncearrows" name="beforeafter_bouncearrows">';
						
						foreach($_bounces as $_key => $_value) {
							
							$_selected = $_key !== $_bounceArrows ? '' : ' selected';
							$_markup .= '<option value="' . $_key . '"' . $_selected . '>' . $_value . '</option>';
							
						}
						
					$_markup .= '</select>
					<span class="description">' . __('Teaser animation for the drag arrows', $_textDomain) . '</span>
				</p>
				
				<div id="beforeafter-bounce-options" style="display: ' . $_bounceOptions . '">
					
					<p>
						<label>' . __('Bounce Type:', $_textDomain) . '</label>
						<select value="' . $_bounceType . '" id="beforeafter_bouncetype" name="beforeafter_bouncetype">';
							
							foreach($_bounceTypes as $_bounce) {
								
								$_selected = $_bounce !== $_bounceType ? '' : ' selected';
								$_markup .= '<option value="' . $_bounce . '"' . $_selected . '>' . ucfirst($_bounce) . '</option>';
								
							}
							
						$_markup .= '</select>
						<span class="description">' . __('If arrows should move away or toward each other', $_textDomain) . '</span>
					</p>
					
					<p>
						<label>' . __('Bounce Amount (px)', $_textDomain) . '</label>
						<input type="text" class="small-text before-after-input" id="beforeafter_bounceamount" name="beforeafter_bounceamount" value="' . $_bounceAmount . '" data-default-value="' . $_bounceAmount . '">
						<span class="description">' . __('The distance in pixels the arrows should bounce', $_textDomain) . '</span>
					</p>
					
					<p>
						<label>' . __('Bounce Speed (ms)', $_textDomain) . '</label>
						<input type="text" class="small-text before-after-input" id="beforeafter_bouncespeed" name="beforeafter_bouncespeed" value="' . $_bounceSpeed . '" data-default-value="' . $_bounceSpeed . '">
						<span class="description">' . __('The animation time in milliseconds for each bounce sequence', $_textDomain) . '</span>
					</p>
					
					<p>
						<label>' . __('Bounce Easing:', $_textDomain) . '</label>
						<select value="' . $_bounceEasing . '" id="beforeafter_bounceeasing" name="beforeafter_bounceeasing">';
							
							foreach($_eases as $_ease) {
								
								$_selected = $_ease !== $_bounceEasing ? '' : ' selected';
								$_markup .= '<option value="' . $_ease . '"' . $_selected . '>' . $_ease . '</option>';
								
							}
							
						$_markup .= '</select>
						<span class="description">' . __('The bounce animation\'s transition type', $_textDomain) . '</span>
					</p>
					
					<p>
						<label>' . __('Start Delay', $_textDomain) . '</label>
						<input type="text" class="small-text before-after-input" id="beforeafter_bouncedelay" name="beforeafter_bouncedelay" value="' . $_bounceDelay . '" data-default-value="' . $_bounceDelay . '">
						<span class="description">' . __('Optional delay in milliseconds before the arrows start to bounce', $_textDomain) . '</span>
					</p>
					
				</div>
				
				<p>
					<label>Arrows Transition:</label>
					<input type="checkbox" id="beforeafter_shiftarrows" name="beforeafter_shiftarrows" class="tp-moderncheckbox"' . $_shiftArrows . ' onchange="document.getElementById(\'beforeafter-shift-options\').style.display=this.checked ? \'block\' : \'none\'" />
					<span class="description" style="margin-left: 20px">' . __('Animate the arrows into place after the initial reveal', $_textDomain) . '</span>
				</p>
				
				<div id="beforeafter-shift-options" style="display: ' . $_shiftOptions . '">
				
					<p>
						<label>' . __('Initial Offset (px)', $_textDomain) . '</label>
						<input type="text" class="small-text before-after-input" id="beforeafter_shiftoffset" name="beforeafter_shiftoffset" value="' . $_shiftOffset . '" data-default-value="' . $_shiftOffset . '">
						<span class="description">' . __('The initial offset for the arrows', $_textDomain) . '</span>
					</p>
					
					<p>
						<label>' . __('Speed (ms)', $_textDomain) . '</label>
						<input type="text" class="small-text before-after-input" id="beforeafter_shifttiming" name="beforeafter_shifttiming" value="' . $_shiftTiming . '" data-default-value="' . $_shiftTiming . '">
						<span class="description">' . __('The transition time in milliseconds', $_textDomain) . '</span>
					</p>
					
					<p>
						<label>' . __('Easing:', $_textDomain) . '</label>
						<select value="' . $_shiftEasing . '" id="beforeafter_shifteasing" name="beforeafter_shifteasing">';
							
							foreach($_eases as $_ease) {
								
								$_selected = $_ease !== $_shiftEasing ? '' : ' selected';
								$_markup .= '<option value="' . $_ease . '"' . $_selected . '>' . $_ease . '</option>';
								
							}
							
						$_markup .= '</select>
						<span class="description">' . __('The animation\'s transition type', $_textDomain) . '</span>
					</p>
					
					<p>
						<label>' . __('Delay (ms)', $_textDomain) . '</label>
						<input type="text" class="small-text before-after-input" id="beforeafter_shiftdelay" name="beforeafter_shiftdelay" value="' . $_shiftDelay . '" data-default-value="' . $_shiftDelay . '">
						<span class="description">' . __('Optional delay in milliseconds for the transition', $_textDomain) . '</span>
					</p>
				
				</div>
				
				<input type="checkbox" class="tp-moderncheckbox beforeafter-settings-onoff" data-placement="beforeafter_video_force_cover" id="video_force_cover_beforeafter" name="video_force_cover_beforeafter"' . $_forceCover . '>
				<input type="checkbox" class="tp-moderncheckbox beforeafter-settings-onoff" data-placement="beforeafter_video_nextslide" id="video_nextslide_beforeafter" name="video_nextslide_beforeafter"' . $_nextSlide . '>
				<input type="checkbox" class="tp-moderncheckbox beforeafter-settings-onoff" data-placement="beforeafter_video_force_rewind" id="video_force_rewind_beforeafter" name="video_force_rewind_beforeafter"' . $_rewind . '>
				<input type="checkbox" class="tp-moderncheckbox beforeafter-settings-onoff" data-placement="beforeafter_video_mute" id="video_mute_beforeafter" name="video_mute_beforeafter"' . $_muteVideo . '>

			</div>
			
		</div>'; 
		
		static::$_Markup     = $_markup;
		static::$_JavaScript = "var RsAddonBeforeAfterBgSources = {
			
			slideBgs: '"      . json_encode($_bgs) . "',
			bgType: '"        . $_bgSource         . "',
			image: '"         . $_bgImageUrl       . "',
			id: '"            . $_bgImageID        . "',
			color: '"         . $_bgColor          . "',
			external: '"      . $_bgExternal       . "',
			youtube: '"       . $_bgYoutube        . "',
			vimeo: '"         . $_bgVimeo          . "',
			mpeg: '"          . $_bgMpeg           . "',
			webm: '"          . $_bgWebm           . "',
			ogv: '"           . $_bgOgv            . "',
			imageSize: '"     . $_bgSize           . "',
			bgFit:     '"     . $_bgFit            . "',
			bgPos:     '"     . $_bgPos            . "',
			bgfitx:    '"     . $_bgFitX           . "',
			bgfity:    '"     . $_bgFitY           . "',
			bgposx:    '"     . $_bgPosX           . "',
			bgposy:    '"     . $_bgPosY           . "',
			bgRepeat:  '"     . $_bgRepeat         . "',
			overlay:   '"     . $_overlay          . "',
			aspect:    '"     . $_aspect           . "',
			startat:   '"     . $_startAt          . "',
			endat:     '"     . $_endAt            . "',
			loopvideo: '"     . $_loopVideo        . "',
			videovolume: '"   . $_videoVolume      . "',
			videoSpeed: '"    . $_videoSpeed       . "',
			youtubeparams: '" . $_youtubeParams    . "',
			vimeoparams: '"   . $_vimeoParams      . "',
			lang: {
				select_after_image: '" . __('Select "After" Image', $_textDomain) . "',
				select_after_video: '" . __('Select "After" Video', $_textDomain) . "'
			}
			
		}";
		
	}
}