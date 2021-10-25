<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsBeforeAfterSlideFront {
	
	private $title;
	
	public function __construct($_title) {
		
		$this->title = $_title;
		add_action('revslider_add_li_data', array($this, 'write_slide_attributes'), 10, 3);
		add_action('revslider_add_layer_attributes', array($this, 'write_layer_attributes'), 10, 3);
	
	}
	
	public function write_layer_attributes($_layer, $_slide, $_slider) {
		
		// check if enabled from slider
		$_enabled = $_slider->getParam('beforeafter_enabled', false) == 'true';
		if(empty($_enabled)) return;
		
		// check if enabled for slide
		$_enabled = $_slide->getParam('beforeafter_enabled', false) == 'true';
		if(!empty($_enabled)) {
			
			$_position = RevSliderFunctions::getVal($_layer, 'beforeafter', array('position' => 'before'));
			$_position = RevSliderFunctions::getVal($_position, 'position', 'before');

			echo '			data-beforeafter="'    . $_position . '" ' . "\n";
			
		}
	
	}
	
	public function write_slide_attributes($_slider, $_slide) {
		
		// check if enabled from slider
		$_enabled = $_slider->getParam('beforeafter_enabled', false) == 'true';
		if(empty($_enabled)) return;
		
		// check if enabled for slide
		$_enabled = $_slide->getParam('beforeafter_enabled', false) == 'true';
		if(!empty($_enabled)) {
		
			$_options = json_decode(stripslashes($_slide->getParam('beforeafter_globals', '')), true);
			$_bgColor = 'transparent';
			
			if(class_exists('TPColorpicker')) {
			
				$_bgColor = $_slide->getParam('bg_color_beforeafter', 'transparent');
				$_bgColor = TPColorpicker::get($_bgColor);
				
			}
			
			$_imageUrl = '';
			$_bgType   = $_slide->getParam('background_type_beforeafter', 'trans');
			$_bgFit    = $_slide->getParam('bg_fit_beforeafter',          'cover');
			$_bgPos    = $_slide->getParam('bg_position_beforeafter',     'center center');
			$_filter   = $_slide->getParam('media-filter-type',           'none');
			$_bounce   = $_slide->getParam('beforeafter_bouncearrows',    'none');
			$_shift    = $_slide->getParam('beforeafter_shiftarrows',     false) == 'true' ? true : false;
			
			if($_bgType === 'image') {
				
				$_imageUrl = $_slide->getParam('image_url_beforeafter', '');
				$_imageId  = $_slide->getParam('image_id_beforeafter',  '');
				
				if(!empty($_imageId)) {
					
					$_imageSize = $_slide->getParam('image_source_type_beforeafter', '');
					if(!empty($_imageSize)) {
						
						$_img = wp_get_attachment_image_src($_imageId, $_imageSize);
						if(!empty($_img)) $_imageUrl = $_img[0];
						
					}
				
				}
				
			}
			else if($_bgType === 'external') {
				
				$_imageUrl = $_slide->getParam('bg_external_beforeafter', '');
				
			}
			
			$_settings = array(
				
				'bgColor'   => $_bgColor,
				'bgType'    => $_bgType,
				'bgImage'   => $_imageUrl,
				'bgFit'     => $_bgFit,
				'bgPos'     => $_bgPos,
				'bgRepeat'  => $_slide->getParam('bg_repeat_beforeafter',   'no-repeat'),
				'direction' => $_slide->getParam('beforeafter_direction',   'horizontal'),
				'easing'    => $_slide->getParam('beforeafter_easing',      'Power2.easeInOut'),
				'delay'     => $_slide->getParam('beforeafter_delay',       '500'),
				'time'      => $_slide->getParam('beforeafter_time',        '750'),
				'out'       => $_slide->getParam('beforeafter_animateout',  'fade'),
				'carousel'  => $_slider->getParam('slider-type',            'standard') !== 'carousel' ? false : true
				
			);
			
			if($_bgFit === 'percentage') {
				
				$_x = $_slide->getParam('bg_fit_x_beforeafter', '100');
				$_y = $_slide->getParam('bg_fit_y_beforeafter', '100');
				$_settings['bgFit'] = $_x . '% ' . $_y . '%';
				
			}
			
			if($_bgPos === 'percentage') {
				
				$_x = $_slide->getParam('bg_position_x_beforeafter', '0');
				$_y = $_slide->getParam('bg_position_y_beforeafter', '0');
				$_settings['bgPos'] = $_x . '% ' . $_y . '%';
				
			}
			
			if($_filter !== 'none') $_settings['filter'] = $_filter;
			if($_bounce !== 'none') {
				
				$_settings['bounceArrows'] = $_bounce;
				$_settings['bounceType']   = $_slide->getParam('beforeafter_bouncetype',   'repel');
				$_settings['bounceAmount'] = $_slide->getParam('beforeafter_bounceamount', '10');
				$_settings['bounceSpeed']  = $_slide->getParam('beforeafter_bouncespeed',  '1000');
				$_settings['bounceEasing'] = $_slide->getParam('beforeafter_bounceeasing', 'ease-in-out');
				$_settings['bounceDelay']  = $_slide->getParam('beforeafter_bouncedelay',  '0');
				
			}
			
			if(!empty($_shift)) {
				
				$_offset = $_slide->getParam('beforeafter_shiftoffset',  '10');
				if(intval($_offset) > 0) {
				
					$_settings['shiftOffset'] = $_offset;
					$_settings['shiftTiming'] = $_slide->getParam('beforeafter_shifttiming', '300');
					$_settings['shiftEasing'] = $_slide->getParam('beforeafter_shifteasing', 'ease');
					$_settings['shiftDelay']  = $_slide->getParam('beforeafter_shiftdelay',  '0');
					
				}
				
			}
			
			if(preg_match('/youtube|vimeo|html5/', $_bgType)) {
				
				$_muteVideo                  = $_slide->getParam('video_mute_beforeafter', true)  == 'true' ? true : false;
				$_settings['muteVideo']      = $_muteVideo;
				$_settings['forceCover']     = $_slide->getParam('video_force_cover_beforeafter',    true)  == 'true' ? true : false;
				$_settings['rewindOnStart']  = $_slide->getParam('video_force_rewind_beforeafter',   true)  == 'true' ? true : false;
				$_settings['nextSlideOnEnd'] = $_slide->getParam('video_nextslide_beforeafter',      false) == 'true' ? true : false;
				$_settings['dottedOverlay']  = $_slide->getParam('video_dotted_overlay_beforeafter', 'none');
				$_settings['aspectRatio']    = $_slide->getParam('video_ratio_beforeafter',          '16:9');
				$_settings['videoStartAt']   = $_slide->getParam('video_start_at_beforeafter',       '');
				$_settings['videoEndAt']     = $_slide->getParam('video_end_at_beforeafter',         '');
				$_settings['loopVideo']      = $_slide->getParam('video_loop_beforeafter',           'none');
				
				switch($_bgType) {
				
					case 'html5':
					
						$_settings['videoMpeg'] = $_slide->getParam('bg_mpeg_beforeafter', '');
						$_settings['videoWebm'] = $_slide->getParam('bg_webm_beforeafter', '');
						$_settings['videoOgv']  = $_slide->getParam('bg_ogv_beforeafter',  '');
					
					break;
					
					case 'youtube':
						
						$_videoVolume = $_slide->getParam('video_volume_beforeafter', '');
						
						$_settings['videoId']     = $_slide->getParam('bg_youtube_beforeafter',      '');
						$_settings['videoSpeed']  = $_slide->getParam('video_speed_beforeafter',     '1');
						$_settings['videoVolume'] = $_videoVolume;
						
						$_args = $_slide->getParam('video_arguments_beforeafter', 'hd=1&wmode=opaque&showinfo=0&rel=0;');
						$_baseArgs = 'version=3&enablejsapi=1&html5=1&';
						if(empty($_muteVideo) && !empty($_videoVolume)) $_baseArgs .= 'volume=' . $_videoVolume . '&';
 						
						$_setBase = is_ssl() ? 'https://' : 'http://';
						$_origin = ';origin=' . $_setBase . $_SERVER['SERVER_NAME'] . ';';
						$_settings['youtubeArgs'] = $_baseArgs . $_args . $_origin;
					
					break;
					
					case 'vimeo':
						
						$_settings['videoId']     = $_slide->getParam('bg_vimeo_beforeafter',            '');
						$_settings['videoVolume'] = $_slide->getParam('video_volume_beforeafter',        '');
						$_settings['vimeoArgs']   = $_slide->getParam('video_arguments_vim_beforeafter', 'title=0&byline=0&portrait=0&api=1');
					
					break;
				
				}
				
			}
			
			$_options = array_merge($_options, $_settings);
			echo " data-beforeafter='" . json_encode($_options) . "'";
			
		}
		
	}
	
}