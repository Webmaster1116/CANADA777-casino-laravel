<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_Weather_Addon
 * @subpackage Revslider_Weather_Addon/admin/partials
 */
?>

<div id="revslider_weather_addon_settings_slideout" class="rs-sbs-slideout-wrapper" style="display:none">
	<div class="rs-sbs-header">
		<div class="rs-sbs-step"><i class="eg-icon-cog"></i></div>
		<div class="rs-sbs-title"><?php _e('How To use the Weather Addon', 'revslider-weather-addon'); ?></div>
		<div class="rs-sbs-close"><i class="eg-icon-cancel"></i></div>
	</div>
	<div class="tp-clearfix"></div>
	<div class="rs-sbs-slideout-inner">
	<!-- Start Settings -->
		<h3 class="tp-steps wb"><span>1</span> <?php _e('Activate Weather for your Slider','revslider-weather-addon'); ?></h3>
		<img src="<?php echo REV_ADDON_WEATHER_URL . "admin/images/tutorial1.jpg"; ?>">

		<h3 class="tp-steps wb"><span>2</span> <?php _e('Set refresh rate','revslider-weather-addon'); ?></h3>
		<img src="<?php echo REV_ADDON_WEATHER_URL . "admin/images/tutorial2.jpg"; ?>">
		<div class="wb-featuretext"><?php _e('Set in minutes if Slider runs for a long time. Set 0 for no refresh.','revslider-weather-addon'); ?></div>

		<h3 class="tp-steps wb"><span>3</span> <?php _e('Fill out weather defaults','revslider-weather-addon'); ?></h3>
		<img src="<?php echo REV_ADDON_WEATHER_URL . "admin/images/tutorial3.jpg"; ?>">
		<div class="wb-featuretext"><?php _e('Fill the defaults for the weather options. You can set single weather options on each slide.'); ?></div>

		<h3 class="tp-steps wb"><span>4</span> <?php _e('Set weather details on slide','revslider-weather-addon'); ?></h3>
		<img src="<?php echo REV_ADDON_WEATHER_URL . "admin/images/tutorial4.jpg"; ?>">
		
		<h3 class="tp-steps wb"><span>5</span> <?php _e('Use Placeholders to display info','revslider-weather-addon'); ?></h3>
		<img src="<?php echo REV_ADDON_WEATHER_URL . "admin/images/tutorial5.jpg"; ?>">

	<!-- End Settings -->
	</div>
</div>
