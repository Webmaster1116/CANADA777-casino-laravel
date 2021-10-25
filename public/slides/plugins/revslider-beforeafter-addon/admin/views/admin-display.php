<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/
 
 /*
 
	test widget
	http://pastebin.com/J0wB676U
 
 */
 
if(!defined('ABSPATH')) exit();

?>

<div id="rev_addon_beforeafter_settings_slideout" class="rs-sbs-slideout-wrapper" style="display:none">

	<div class="rs-sbs-header">
		<div class="rs-sbs-step"><i class="eg-icon-cog"></i></div>
		<div class="rs-sbs-title"><?php _e('Setting up the BeforeAfter Add-On', 'rs_beforeafter'); ?></div>
		<div class="rs-sbs-close"><i class="eg-icon-cancel"></i></div>
	</div>
	
	<div class="tp-clearfix"></div>
	
	<div class="rs-sbs-slideout-inner">
		
		<h3><span>1</span> Enable the Before/After Add-On from the <a href="<?php echo RS_BEFOREAFTER_PLUGIN_URL; ?>admin/assets/images/slider-settings-button.jpg" target="_blank">Slider Settings</a></h3>
		<img src="<?php echo RS_BEFOREAFTER_PLUGIN_URL; ?>admin/assets/images/tutorial0.jpg" />

		<h3><span>2</span> Enable the Before/After feature for an individual Slide from the <a href="<?php echo RS_BEFOREAFTER_PLUGIN_URL; ?>admin/assets/images/slide_settings.jpg" target="_blank">Slide Settings</a></h3>
		<a href="<?php echo RS_BEFOREAFTER_PLUGIN_URL; ?>admin/assets/images/slide_settings.jpg" target="_blank">
			<img src="<?php echo RS_BEFOREAFTER_PLUGIN_URL; ?>admin/assets/images/tutorial1.jpg" />
		</a>
		<br><br>
		<img src="<?php echo RS_BEFOREAFTER_PLUGIN_URL; ?>admin/assets/images/tutorial2.jpg" />

		<h3><span>3</span> Add a background for the "After" view</h3>
		<img src="<?php echo RS_BEFOREAFTER_PLUGIN_URL; ?>admin/assets/images/tutorial3.jpg" />
		
		<h3><span>4</span> <a href="<?php echo RS_BEFOREAFTER_PLUGIN_URL; ?>admin/assets/images/add-new-layer-1.jpg" target="_blank">Add a Layer</a> and designate it for the <a href="<?php echo RS_BEFOREAFTER_PLUGIN_URL; ?>admin/assets/images/layer_settings.jpg" target="_blank">"Before" or "After" view</a></h3>
		<a href="<?php echo RS_BEFOREAFTER_PLUGIN_URL; ?>admin/assets/images/layer_settings.jpg" target="_blank">
			<img src="<?php echo RS_BEFOREAFTER_PLUGIN_URL; ?>admin/assets/images/tutorial4.jpg" />
		</a>
		<br><br>
		<img src="<?php echo RS_BEFOREAFTER_PLUGIN_URL; ?>admin/assets/images/tutorial5.jpg" />
		
		<h3><span>5</span> Toggle the Layers visibility between the "Before" & "After" views</h3>
		<img src="<?php echo RS_BEFOREAFTER_PLUGIN_URL; ?>admin/assets/images/tutorial6.jpg" />
		
		<h3><span>6</span> Red represents a "Before" Layer, and Green is used for "After" Layers</h3>
		<img src="<?php echo RS_BEFOREAFTER_PLUGIN_URL; ?>admin/assets/images/tutorial7.jpg" />
		<br><br>
		<img src="<?php echo RS_BEFOREAFTER_PLUGIN_URL; ?>admin/assets/images/tutorial8.jpg" />
		<br><br>
		
	</div>
	
	<style type="text/css">
		
		#rev_addon_beforeafter_settings_slideout a img {transition: opacity 0.2s ease}
		#rev_addon_beforeafter_settings_slideout a:hover img {opacity: 0.65}
	
	</style>
	
</div>