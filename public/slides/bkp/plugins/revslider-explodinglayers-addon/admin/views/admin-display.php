<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/
 
 /*
 
	create fake "Add-On" block to test this widget
	http://pastebin.com/J0wB676U
 
 */
 
if(!defined('ABSPATH')) exit();

?>

<div id="rev_addon_explodinglayers_settings_slideout" class="rs-sbs-slideout-wrapper" style="display:none">

	<div class="rs-sbs-header">
		<div class="rs-sbs-step"><i class="eg-icon-cog"></i></div>
		<div class="rs-sbs-title"><?php _e('Enable Exploding Layers Add-On', 'rs_explodinglayers'); ?></div>
		<div class="rs-sbs-close"><i class="eg-icon-cancel"></i></div>
	</div>
	
	<div class="tp-clearfix"></div>
	
	<div class="rs-sbs-slideout-inner">
		
		<h3 style="line-height: 24px"><span>1</span> <?php _e('"Enable" the Exploding Layers Add-On from the', 'rs_explodinglayers'); ?> <a href="<?php echo RS_EXPLODINGLAYERS_PLUGIN_URL . "admin/assets/images/slider-settings.jpg"; ?>" target="_blank"><?php _e('Slider Settings', 'rs_explodinglayers'); ?></a></h3>
		<img src="<?php echo RS_EXPLODINGLAYERS_PLUGIN_URL . "admin/assets/images/tutorial0.jpg"; ?>">

		<h3><span>2</span> <?php _e('"Add" a Layer inside the', 'rs_explodinglayers'); ?> <a href="<?php echo RS_EXPLODINGLAYERS_PLUGIN_URL . "admin/assets/images/slide-settings.jpg"; ?>" target="_blank"><?php _e('Slide Editor', 'rs_explodinglayers'); ?></a></h3>
		<img src="<?php echo RS_EXPLODINGLAYERS_PLUGIN_URL . "admin/assets/images/tutorial1.jpg"; ?>">

		<h3><span>3</span> <?php _e('"Enable" the Exploding Layers effect for the Layer\'s Animation', 'rs_explodinglayers'); ?></h3>
		<img src="<?php echo RS_EXPLODINGLAYERS_PLUGIN_URL . "admin/assets/images/tutorial2.jpg"; ?>">
		
		<h3><span>4</span> <?php _e('"Open" the settings panel for the animation', 'rs_explodinglayers'); ?></h3>
		<img src="<?php echo RS_EXPLODINGLAYERS_PLUGIN_URL . "admin/assets/images/tutorial3.jpg"; ?>">
		
		<h3><span>5</span> <?php _e('"Adjust" the settings for the effect', 'rs_explodinglayers'); ?></h3>
		<img src="<?php echo RS_EXPLODINGLAYERS_PLUGIN_URL . "admin/assets/images/tutorial4.jpg"; ?>">
		
		<h3><span>6</span> <?php _e('"Preview" the Slide to see the Effect', 'rs_explodinglayers'); ?></h3>
		<img src="<?php echo RS_EXPLODINGLAYERS_PLUGIN_URL . "admin/assets/images/tutorial5.jpg"; ?>">
		
	</div>
</div>