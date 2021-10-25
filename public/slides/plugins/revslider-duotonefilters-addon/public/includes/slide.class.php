<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsDuotoneFiltersSlideFront {
	
	private $title;
	
	public function __construct($_title) {
		
		$this->title = $_title;
		add_action('revslider_add_li_data', array($this, 'write_slide_attributes'), 10, 3);
	
	}
	
	public function write_slide_attributes($_slider, $_slide) {
		
		// check if enabled from slider
		$_enabled = $_slider->getParam('duotonefilters_enabled', false) == 'true';
		if(empty($_enabled)) return;
		
		// check if enabled for slide
		$_filter = $_slide->getParam('duotonefilter_addon', 'rs-duotone-none');
		
		if($_filter !== 'rs-duotone-none') {
		
			echo " data-duotonefilter='" . $_filter . "'";
			
		}
		
	}
	
}
?>