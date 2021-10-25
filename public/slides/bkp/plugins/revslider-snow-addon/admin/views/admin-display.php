<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */
 
 /*
 
	create fake "Add-On" block to test this widget
	http://pastebin.com/J0wB676U
 
 */
 
if(!defined('ABSPATH')) exit();

?>

<div id="rev_addon_snow_settings_slideout" class="rs-sbs-slideout-wrapper" style="display:none">

	<div class="rs-sbs-header">
		<div class="rs-sbs-step"><i class="eg-icon-cog"></i></div>
		<div class="rs-sbs-title"><?php _e('How to use the Snow Add-On', 'rs_snow'); ?></div>
		<div class="rs-sbs-close"><i class="eg-icon-cancel"></i></div>
	</div>
	
	<div class="tp-clearfix"></div>
	
	<div class="rs-sbs-slideout-inner">
		
		<h3><span>1</span> <?php _e('"Enable" the Snow','rs_snow'); ?></h3>
		<img src="<?php echo RS_SNOW_PLUGIN_URL . "admin/assets/images/tutorial0.jpg"; ?>">

		<h3><span>2</span> <?php _e('"Adjust" the Options','rs_snow'); ?></h3>
		<img src="<?php echo RS_SNOW_PLUGIN_URL . "admin/assets/images/tutorial1.jpg"; ?>">

		<h3><span>3</span> <?php _e('"Watch it snow!','rs_snow'); ?></h3>
		<img src="<?php echo RS_SNOW_PLUGIN_URL . "admin/assets/images/tutorial2.jpg"; ?>">
		
	</div>
</div>