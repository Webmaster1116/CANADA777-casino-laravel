<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_Refresh_Addon
 * @subpackage Revslider_Refresh_Addon/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="revslider_refresh_addon_settings_slideout" class="rs-sbs-slideout-wrapper" style="display:none">
	<div class="rs-sbs-header">
		<div class="rs-sbs-step"><i class="eg-icon-cog"></i></div>
		<div class="rs-sbs-title"><?php _e('How To use the (Re)Load Addon', 'revslider-refresh-addon'); ?></div>
		<div class="rs-sbs-close"><i class="eg-icon-cancel"></i></div>
	</div>
	<div class="tp-clearfix"></div>
	<div class="rs-sbs-slideout-inner">
	<!-- Start Settings -->
		<h3 class="tp-steps wb"><span>1</span> <?php _e('Activate (Re)Load for your Slider','revslider-refresh-addon'); ?></h3>
		<img src="<?php echo REV_ADDON_REFRESH_URL . "admin/images/reload_url_1.jpeg"; ?>">

		<h3 class="tp-steps wb"><span>2</span> <?php _e('Set (Re)Load Event','revslider-refresh-addon'); ?></h3>
		<img src="<?php echo REV_ADDON_REFRESH_URL . "admin/images/reload_url_2.jpeg"; ?>">
		<div class="wb-featuretext"><?php _e('Select what triggers the (Re)Load action.','revslider-refresh-addon'); ?></div>

		<h3 class="tp-steps wb"><span>3</span> <?php _e('Set Target URL','revslider-refresh-addon'); ?></h3>
		<img src="<?php echo REV_ADDON_REFRESH_URL . "admin/images/reload_url_3.jpeg"; ?>">
		<div class="wb-featuretext"><?php _e('Leave turned off for reloading current page or set URL.'); ?></div>
	<!-- End Settings -->
	</div>
</div>