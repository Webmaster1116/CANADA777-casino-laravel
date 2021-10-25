<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnBubblemorphNotice {
	
	private $title,
			$notice,
			$txtDomain;
	
	public function __construct($_notice, $_title) {
		
		$this->notice = $_notice;
		$this->title = ucfirst($_title);
		$this->txtDomain = 'rs_' . $_title;
		
		add_action('admin_notices', array($this, 'add_notice'));
	
	}
	
	/**
	 * Add notice
	 **/
	public function add_notice() {
		
		$_notice = $this->notice;
		$_title = $this->title;
		
		switch($_notice) {
				
			case 'add_notice_activation':
			
				$_notice = 'The <a href="?page=rev_addon">' . $_title . ' Add-On</a> requires an active ' . 
						   '<a href="https://www.themepunch.com/revslider-doc/activate-copy-slider-revolution/" target="_blank">Purchase Code Registration</a>';
			
			break;
			
			case 'add_notice_plugin':
			
				$_notice = '<a href="https://revolution.themepunch.com/" target="_blank">Slider Revolution</a> required to use the ' . $_title . ' Add-On';
			
			break;
			
			case 'add_notice_version':
			
				$_notice = 'The ' . $_title . ' Add-On requires Slider Revolution ' . RsAddOnBubblemorphBase::MINIMUM_VERSION . 
						   '+  <a href="https://www.themepunch.com/faq/how-to-update-the-slider/" target="_blank">Update Slider Revolution</a>';
			
			break;
			
		}
		
		?>
		<div class="error below-h2 soc-notice-wrap" id="message"><p><?php _e($_notice, $this->txtDomain); ?></p></div>
		<?php
		
	}
	
}

?>