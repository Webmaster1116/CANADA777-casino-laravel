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

<div id="rev_addon_typewriter_settings_slideout" class="rs-sbs-slideout-wrapper" style="display:none">

	<div class="rs-sbs-header">
		<div class="rs-sbs-step"><i class="eg-icon-cog"></i></div>
		<div class="rs-sbs-title"><?php _e('How to use the Typewriter', 'rs_typewriter'); ?></div>
		<div class="rs-sbs-close"><i class="eg-icon-cancel"></i></div>
	</div>
	
	<div class="tp-clearfix"></div>
	
	<div class="rs-sbs-slideout-inner">
		
		<h3><span>1</span> <?php _e('"Enable" the Typewriter','rs_typewriter'); ?></h3>
		<img src="<?php echo RS_TYPEWRITER_PLUGIN_URL . "admin/assets/images/tutorial0.jpg"; ?>">
		<div class="featuretext"><?php _e('From the', 'rs_typewriter'); ?> <a href="https://www.themepunch.com/revslider-doc/slider-settings/" target="_blank"><?php _e('Slider\'s Main Settings Page', 'rs_typewriter'); ?></a><?php _e(', enable the Typewriter', 'rs_typewriter'); ?></div>
		
		<h3><span>2</span> <?php _e('"Add-ons" Tab','rs_typewriter'); ?></h3>
		<img src="<?php echo RS_TYPEWRITER_PLUGIN_URL . "admin/assets/images/tutorial1.jpg"; ?>">
		<div class="featuretext"><?php _e('From the', 'rs_typewriter'); ?> <a href="https://www.themepunch.com/revslider-doc/slide-layers/" target="_blank"><?php _e('Slide Editor', 'rs_typewriter'); ?></a><?php _e(', select the Add-ons tab', 'rs_typewriter'); ?></div>

		<h3><span>3</span> <?php _e('Select "Typewriter"','rs_typewriter'); ?></h3>
		<img src="<?php echo RS_TYPEWRITER_PLUGIN_URL . "admin/assets/images/tutorial2.jpg"; ?>">
		<div class="featuretext"><?php _e('Select a layer you would like to add the Typewriter effect to, and then select the "Typewriter" option.', 'rs_typewriter'); ?></div>

		<h3><span>4</span> <?php _e('"Enable" the Effect','rs_typewriter'); ?></h3>
		<img src="<?php echo RS_TYPEWRITER_PLUGIN_URL . "admin/assets/images/tutorial3.jpg"; ?>">
		<div class="featuretext"><?php _e('Select the first option to enable the effect.', 'rs_typewriter'); ?></div>

		<h3><span>5</span> <?php _e('"Adjust" the Options','rs_typewriter'); ?></h3>
		<img src="<?php echo RS_TYPEWRITER_PLUGIN_URL . "admin/assets/images/tutorial4.jpg"; ?>">
		<div class="featuretext"><?php _e("Hover your mouse over the icons to view the option's description", 'rs_typewriter'); ?></div>

		<h3><span>6</span> <?php _e('"Learn" More','rs_typewriter'); ?></h3>
		<div class="documentation"><a href="https://www.themepunch.com/revslider-doc/add-on-typewriter/" target="_blank"><?php _e('Click here to view full documentation', 'rs_typewriter'); ?></a></div>
		
	</div>
</div>