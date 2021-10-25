<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/
 
if(!defined('ABSPATH')) exit();

?>

<div id="rev_addon_panorama_settings_slideout" class="rs-sbs-slideout-wrapper" style="display:none">

	<div class="rs-sbs-header">
		<div class="rs-sbs-step"><i class="eg-icon-cog"></i></div>
		<div class="rs-sbs-title"><?php _e('Setting up the Panorama Add-On', 'rs_panorama'); ?></div>
		<div class="rs-sbs-close"><i class="eg-icon-cancel"></i></div>
	</div>
	
	<div class="tp-clearfix"></div>
	
	<div class="rs-sbs-slideout-inner">
		
		<h3><span>1</span> Enable the Panorama Add-On from the <a href="<?php echo RS_PANORAMA_PLUGIN_URL; ?>admin/assets/images/slider-settings-button.jpg" target="_blank">Slider Settings</a></h3>
		<img src="<?php echo RS_PANORAMA_PLUGIN_URL; ?>admin/assets/images/tutorial0.jpg" />

		<h3><span>2</span> Enable the Panorama feature for an individual Slide from the <a href="<?php echo RS_PANORAMA_PLUGIN_URL; ?>admin/assets/images/slide_settings.jpg" target="_blank">Slide Settings</a></h3>
		<a href="<?php echo RS_PANORAMA_PLUGIN_URL; ?>admin/assets/images/slide_settings.jpg" target="_blank">
			<img src="<?php echo RS_PANORAMA_PLUGIN_URL; ?>admin/assets/images/tutorial1.jpg" />
		</a>
		<br><br>
		<img src="<?php echo RS_PANORAMA_PLUGIN_URL; ?>admin/assets/images/tutorial2.jpg" />

		<h3><span>3</span> Add a panorama image as the slide's main background</h3>
		<p style="text-align: center">Background images should be sized by the<br>"power of 2", i.e. 4096x2048, 2048x1024, etc.</p>
		<img src="<?php echo RS_PANORAMA_PLUGIN_URL; ?>admin/assets/images/tutorial3.jpg" />
		
	</div>
	
	<style type="text/css">
		
		#rev_addon_panorama_settings_slideout a img {transition: opacity 0.2s ease}
		#rev_addon_panorama_settings_slideout a:hover img {opacity: 0.65}
	
	</style>
	
</div>