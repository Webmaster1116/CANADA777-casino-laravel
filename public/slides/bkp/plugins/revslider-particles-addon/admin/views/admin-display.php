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

<div id="rev_addon_particles_settings_slideout" class="rs-sbs-slideout-wrapper" style="display:none">

	<div class="rs-sbs-header">
		<div class="rs-sbs-step"><i class="eg-icon-cog"></i></div>
		<div class="rs-sbs-title"><?php _e('How to use the Particles Add-On', 'rs_particles'); ?></div>
		<div class="rs-sbs-close"><i class="eg-icon-cancel"></i></div>
	</div>
	
	<div class="tp-clearfix"></div>
	
	<div class="rs-sbs-slideout-inner">
		
		<h3><span>1</span> <?php _e('"Enable" the Particles','rs_particles'); ?></h3>
		<img src="<?php echo RS_PARTICLES_PLUGIN_URL . "admin/assets/images/tutorial0.jpg"; ?>">

		<h3><span>2</span> <?php _e('"Select" and load a Template\'s settings','rs_particles'); ?></h3>
		<img src="<?php echo RS_PARTICLES_PLUGIN_URL . "admin/assets/images/tutorial1.jpg"; ?>">

		<h3><span>3</span> <?php _e('"Apply" your Custom Settings','rs_particles'); ?></h3>
		<img src="<?php echo RS_PARTICLES_PLUGIN_URL . "admin/assets/images/tutorial2.jpg"; ?>">
		
		<h3><span>4</span> <?php _e('"Save" the Slider','rs_particles'); ?></h3>
		<img src="<?php echo RS_PARTICLES_PLUGIN_URL . "admin/assets/images/tutorial3.jpg"; ?>">
		
		<h3><span>5</span> <?php _e('"Preview" your Particles','rs_particles'); ?></h3>
		<img src="<?php echo RS_PARTICLES_PLUGIN_URL . "admin/assets/images/tutorial4.jpg"; ?>">
		
	</div>
</div>