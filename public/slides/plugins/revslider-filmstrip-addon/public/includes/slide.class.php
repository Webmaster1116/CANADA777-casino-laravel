<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsFilmstripSlideFront {
	
	private $title;
	
	public function __construct($_title) {
		
		$this->title = $_title;
		add_action('revslider_add_li_data', array($this, 'write_slide_attributes'), 10, 3);
	
	}
	
	public function write_slide_attributes($_slider, $_slide) {
		
		
		$_enabled = $_slider->getParam('filmstrip_enabled', false) == 'true';
		if(!$_enabled) return;
		
		$_enabled = $_slide->getParam('filmstrip_enabled', false) == 'true';
		if(!$_enabled) return;
		
		if(wp_is_mobile()) {
		
			$_defMobile = $_slider->getParam('filmstrip_default_mobile', false);
			$_mobile = $_slide->getParam('filmstrip_mobile', $_defMobile)  == 'true';
			if($_mobile) return;
			
		}
		
		$_datas = $_slide->getParam('filmstrip_settings', ''); 
		if(!$_datas) return;
		
		$_datas = json_decode(stripslashes($_datas), true);
		if(!$_datas) return;
		
		$_defDirection = $_slider->getParam('filmstrip_def_direction', 'right-to-left');
		$_direction    = $_slide->getParam('filmstrip_direction', $_defDirection);
		$_defTimes     = $_slider->getParam('filmstrip_def_times', '30,30,30,30');
		$_times        = $_slide->getParam('filmstrip_times', $_defTimes);
		$_filter       = $_slide->getParam('media-filter-type', '');
		$_imgs         = array();
		
		foreach($_datas as $_data) {
			
			$_alt     = '';
			$_url     = isset($_data['url']) ? $_data['url']    : '';
			$_type    = isset($_data['type']) ? $_data['type']   : '';
			$_altText = isset($_data['alt']) ? $_data['alt']    : '';
			$_custom  = isset($_data['custom']) ? $_data['custom'] : '';
			
			if($_type === 'wpimage') {
				
				$_ids = isset($_data['ids']) ? $_data['ids'] : false;
				if($_ids) {
					
					$_size = isset($_data['size']) ? $_data['size'] : 'full';
					if(!$_size) $_size = 'full';
					
					$_url = wp_get_attachment_image_src($_ids, $_size);
					$_url = $_url ? $_url[0] : '';
					
					if($_altText === 'media_library') {
						
						$_alt = get_post_meta($_ids, '_wp_attachment_image_alt', true);
						if(!$_alt) $_alt = ''; 
						
					}
				}
			}
			
			if(!$_alt) {
			
				if($_altText === 'file_name') {
					
					$_info = pathinfo($_url);
					$_alt = $_info['filename'];
					
				}
				else {
					$_alt = $_custom;
				}
				
			}
			
			$_imgs[] = array('url' => $_url, 'alt' => $_alt);
			
		}
		
		$_settings = array(
		
			'direction' => $_direction,
			'filter' => $_filter,
			'times' => $_times,
			'imgs' => $_imgs
		
		);
		
		echo " data-filmstrip='" . json_encode($_settings) . "'";
		
	}
	
}
?>