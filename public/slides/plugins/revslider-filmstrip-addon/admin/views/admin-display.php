<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/
 
 /*
 
	create fake "Add-On" block to test this widget
	http://pastebin.com/J0wB676U
 
 */
 
if(!defined('ABSPATH')) exit();

?>

<div id="rev_addon_filmstrip_settings_slideout" class="rs-sbs-slideout-wrapper" style="display:none">

	<div class="rs-sbs-header">
		<div class="rs-sbs-step"><i class="eg-icon-cog"></i></div>
		<div class="rs-sbs-title"><?php _e('How to use the FilmStrip Add-On', 'rs_filmstrip'); ?></div>
		<div class="rs-sbs-close"><i class="eg-icon-cancel"></i></div>
	</div>
	
	<div class="tp-clearfix"></div>
	
	<div class="rs-sbs-slideout-inner">
		
		<h3><span>1</span> <?php _e('"Enable" FilmStrip for the <a href="https://cdntphome-themepunchgbr.netdna-ssl.com/wp-content/uploads/2016/12/particles-5.jpg" target="_blank">Slider Settings</a>', 'rs_filmstrip'); ?></h3>
		<img src="<?php echo RS_FILMSTRIP_PLUGIN_URL . "admin/assets/images/tutorial0.jpg"; ?>">

		<h3><span>2</span> <?php _e('"Enable" FilmStrip for one of your <a href="https://cdntphome-themepunchgbr.netdna-ssl.com/wp-content/uploads/2016/10/slide-settings.jpg" target="_blank">Slides</a>', 'rs_filmstrip'); ?></h3>
		<img src="<?php echo RS_FILMSTRIP_PLUGIN_URL . "admin/assets/images/tutorial1.jpg"; ?>">
		<br><br>
		<img src="<?php echo RS_FILMSTRIP_PLUGIN_URL . "admin/assets/images/tutorial2.jpg"; ?>">

		<h3><span>3</span> <?php _e('"Add" some images to the FilmStrip', 'rs_filmstrip'); ?></h3>
		<img src="<?php echo RS_FILMSTRIP_PLUGIN_URL . "admin/assets/images/tutorial3.jpg"; ?>">
		<br><br>
		<img src="<?php echo RS_FILMSTRIP_PLUGIN_URL . "admin/assets/images/tutorial4.jpg"; ?>">
		
		<h3><span>4</span> <?php _e('"Adjust" the FilmStrip Settings', 'rs_filmstrip'); ?></h3>
		<img src="<?php echo RS_FILMSTRIP_PLUGIN_URL . "admin/assets/images/tutorial5.jpg"; ?>">
		
		<h3><span>5</span> <?php _e('"Preview" the FilmStrip', 'rs_filmstrip'); ?></h3>
		<img src="<?php echo RS_FILMSTRIP_PLUGIN_URL . "admin/assets/images/tutorial6.jpg"; ?>">
		
	</div>
</div>