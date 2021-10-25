<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Slider Revolution Backup Add-on
 */
 
 if( !defined( 'ABSPATH') ) exit();
?>
<div id="rev_addon_backup_settings_slideout" class="rs-sbs-slideout-wrapper" style="display:none">
	<div class="rs-sbs-header">
		<div class="rs-sbs-step"><i class="eg-icon-cog"></i></div>
		<div class="rs-sbs-title"><?php _e('How to use Backups', 'rs_backup'); ?></div>
		<div class="rs-sbs-close"><i class="eg-icon-cancel"></i></div>
	</div>
	<div class="tp-clearfix"></div>
	<div class="rs-sbs-slideout-inner">

	<!-- Start Settings -->
		<h3 class="tp-steps wb"><span>1</span> <?php _e('"Add-ons" Tab','rs_backup'); ?></h3>
		<img src="<?php echo RS_BACKUP_PLUGIN_URL . "admin/assets/images/tutorial1.png"; ?>">
		<div class="wb-featuretext"><?php _e('Right after installing the Backup add-on, you are ready for action!','rs_backup'); ?></div>

		<h3 class="tp-steps wb"><span>2</span> <?php _e('Select "Backups"','rs_backup'); ?></h3>
		<img src="<?php echo RS_BACKUP_PLUGIN_URL . "admin/assets/images/tutorial2.png"; ?>">
		<div class="wb-featuretext"><?php _e('Click on the Backups button to reveal the "Show available Backups for this Slide" button.','rs_backup'); ?></div>

		<h3 class="tp-steps wb"><span>3</span> <?php _e('Backups List','rs_backup'); ?></h3>
		<img src="<?php echo RS_BACKUP_PLUGIN_URL . "admin/assets/images/tutorial3.png"; ?>">
		<div class="wb-featuretext"><?php _e('Preview or load directly the desired backup from a rotating list of 11 backups.','rs_backup'); ?></div>

		<h3 class="tp-steps wb"><span>4</span> <?php _e('Preview Backup','rs_backup'); ?></h3>
		<img src="<?php echo RS_BACKUP_PLUGIN_URL . "admin/assets/images/tutorial4.png"; ?>">
		<div class="wb-featuretext"><?php _e('Preview the slide backup with the possibility to load it.','rs_backup'); ?></div>

	<!-- End Settings -->
	
	</div>
</div>