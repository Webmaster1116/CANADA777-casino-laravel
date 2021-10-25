<?php
/**
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

<div id="rev_addon_liquideffect_settings_slideout" class="rs-sbs-slideout-wrapper" style="display:none">

	<div class="rs-sbs-header">
		<div class="rs-sbs-step"><i class="eg-icon-cog"></i></div>
		<div class="rs-sbs-title"><?php _e('How to use Distortion', 'rs_liquideffect'); ?></div>
		<div class="rs-sbs-close"><i class="eg-icon-cancel"></i></div>
	</div>
	
	<div class="tp-clearfix"></div>
	
	<div class="rs-sbs-slideout-inner">
		
		<h3><span>1</span> <?php _e('"Enable" the Distortion','rs_liquideffect'); ?></h3>
		<img src="<?php echo RS_LIQUIDEFFECT_PLUGIN_URL . "admin/assets/images/tutorial0.jpg"; ?>">
		<div class="featuretext"><?php _e('From the', 'rs_liquideffect'); ?> <a href="<?php echo RS_LIQUIDEFFECT_PLUGIN_URL . 'admin/assets/images/slider-settings.jpg'; ?>" target="_blank"><?php _e('Slider\'s Main Settings Page', 'rs_liquideffect'); ?></a><?php _e(', enable the Distortion', 'rs_liquideffect'); ?></div>
		
		<h3><span>2</span> <?php _e('"Set" a Background Image','rs_liquideffect'); ?></h3>
		<img src="<?php echo RS_LIQUIDEFFECT_PLUGIN_URL . "admin/assets/images/tutorial1.jpg"; ?>">
		<div class="featuretext"><?php _e('Visit the', 'rs_liquideffect'); ?> <a href="<?php echo RS_LIQUIDEFFECT_PLUGIN_URL . 'admin/assets/images/slide-editor.jpg'; ?>" target="_blank"><?php _e('Slide Editor', 'rs_liquideffect'); ?></a><?php _e(' and set a main background image', 'rs_liquideffect'); ?></div>
		
		<h3><span>3</span> <?php _e('"Add-ons" Tab','rs_liquideffect'); ?></h3>
		<img src="<?php echo RS_LIQUIDEFFECT_PLUGIN_URL . "admin/assets/images/tutorial2.jpg"; ?>">
		<div class="featuretext"><?php _e('From the', 'rs_liquideffect'); ?> <a href="<?php echo RS_LIQUIDEFFECT_PLUGIN_URL . 'admin/assets/images/slide-editor.jpg'; ?>" target="_blank"><?php _e('Slide Editor', 'rs_liquideffect'); ?></a><?php _e(', select the Add-ons tab', 'rs_liquideffect'); ?></div>
		
		<h3><span>4</span> <?php _e('"Enable" the Distortion','rs_liquideffect'); ?></h3>
		<img src="<?php echo RS_LIQUIDEFFECT_PLUGIN_URL . "admin/assets/images/tutorial3.jpg"; ?>">
		
		<h3><span>5</span> <?php _e('"Load" a Template','rs_liquideffect'); ?></h3>
		<img src="<?php echo RS_LIQUIDEFFECT_PLUGIN_URL . "admin/assets/images/tutorial4.jpg"; ?>">
		<div class="featuretext"><?php _e('Select and load settings template to create different effects', 'rs_liquideffect'); ?></div><br><br>

	</div>
	
</div>