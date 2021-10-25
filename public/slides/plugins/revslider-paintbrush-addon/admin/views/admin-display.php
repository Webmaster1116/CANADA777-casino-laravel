<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/
 
 /*
 
	test widget
	http://pastebin.com/J0wB676U
 
 */
 
if(!defined('ABSPATH')) exit();

?>

<div id="rev_addon_paintbrush_settings_slideout" class="rs-sbs-slideout-wrapper" style="display:none">

	<div class="rs-sbs-header">
		<div class="rs-sbs-step"><i class="eg-icon-cog"></i></div>
		<div class="rs-sbs-title"><?php _e('Setting up the Paint-Brush Add-On', 'rs_paintbrush'); ?></div>
		<div class="rs-sbs-close"><i class="eg-icon-cancel"></i></div>
	</div>
	
	<div class="tp-clearfix"></div>
	
	<div class="rs-sbs-slideout-inner">
		
		<h3><span>1</span> Enable the PaintBrush Add-On from the <a href="<?php echo RS_PAINTBRUSH_PLUGIN_URL; ?>admin/assets/images/slider-settings-button.jpg" target="_blank">Slider Settings</a></h3>
		<img src="<?php echo RS_PAINTBRUSH_PLUGIN_URL; ?>admin/assets/images/tutorial0.jpg" />
		<br><br>
		
		<h3><span>2</span> Inside the <a href="<?php echo RS_PAINTBRUSH_PLUGIN_URL; ?>admin/assets/images/slide-editor.jpg" target="_blank">Slide Editor</a>, set a main background image</h3>
		<a href="<?php echo RS_PAINTBRUSH_PLUGIN_URL; ?>admin/assets/images/slide-main-bg.jpg" target="_blank">
			<img src="<?php echo RS_PAINTBRUSH_PLUGIN_URL; ?>admin/assets/images/tutorial1.jpg" />
		</a>
		<br><br>
		
		<h3><span>3</span> Enable the PaintBrush feature for an individual Slide from the <a href="<?php echo RS_PAINTBRUSH_PLUGIN_URL; ?>admin/assets/images/slide-settings.jpg" target="_blank">Slide Settings</a></h3>
		<a href="<?php echo RS_PAINTBRUSH_PLUGIN_URL; ?>admin/assets/images/slide-settings.jpg" target="_blank">
			<img src="<?php echo RS_PAINTBRUSH_PLUGIN_URL; ?>admin/assets/images/tutorial2.jpg" />
		</a>
		<br><br>
		<a href="<?php echo RS_PAINTBRUSH_PLUGIN_URL; ?>admin/assets/images/slide-settings.jpg" target="_blank">
			<img src="<?php echo RS_PAINTBRUSH_PLUGIN_URL; ?>admin/assets/images/tutorial3.jpg" />
		</a>
		<br><br>
		
		<h3><span>4</span> Set a custom image to paint, or paint the slide's main background image and blur the original</h3>
		<img src="<?php echo RS_PAINTBRUSH_PLUGIN_URL; ?>admin/assets/images/tutorial4.jpg" />
		<br><br>
		
		<h3><span>5</span> Adjust the brush and mobile settings</h3>
		<img src="<?php echo RS_PAINTBRUSH_PLUGIN_URL; ?>admin/assets/images/tutorial5.jpg" />
		<br><br>
		<img src="<?php echo RS_PAINTBRUSH_PLUGIN_URL; ?>admin/assets/images/tutorial6.jpg" />
		
	</div>
	
	<style type="text/css">
		
		#rev_addon_paintbrush_settings_slideout a img {transition: opacity 0.2s ease}
		#rev_addon_paintbrush_settings_slideout a:hover img {opacity: 0.65}
	
	</style>
	
</div>