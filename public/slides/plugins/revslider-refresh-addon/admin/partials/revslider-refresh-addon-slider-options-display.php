<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_refresh_Addon
 * @subpackage Revslider_refresh_Addon/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
		<span class="label" id="label_refresh_enabled" origtitle="<?php _e("Enable/Disable (Re)load for this Slider<br><br>", 'revslider_refresh_addon');?>"> <?php _e('Enable (Re)load', 'revslider_refresh_addon');?></span>
		<?php $revslider_refresh_enabled = RevSliderFunctions::getVal($sliderParams,'revslider-refresh-enabled',''); ?>
		<input type="checkbox" class="tp-moderncheckbox withlabel" id="revslider_refresh_enabled" name="revslider-refresh-enabled" <?php checked( $revslider_refresh_enabled, "true", 1 );?>/>

		<?php $revslider_refresh_switch = !empty($revslider_refresh_enabled) &&  $revslider_refresh_enabled == "true" ? '' : 'style="display:none"'; ?>
		<span id="revslider_refresh_settings" <?php echo $revslider_refresh_switch; ?>>	
			
			<span id="label_revslider_refresh_type" class=" label" origtitle="<?php _e('When do you want the URL to be loaded', 'revslider-refresh-addon');?>"><?php _e('Event', 'revslider_refresh_addon');?> </span>
			<select id="revslider-refresh-type"  name="revslider-refresh-type" >
				<?php $refresh_type = RevSliderFunctions::getVal($sliderParams,'revslider-refresh-type','time'); ?>
				<option value="time" <?php selected( $refresh_type, "time", 1 ); ?>><?php _e('After x minutes', 'revslider_refresh_addon');?></option>
				<option value="slide" <?php selected( $refresh_type, "slide", 1 ); ?>><?php _e('After slide number x', 'revslider_refresh_addon');?></option>
				<option value="loops" <?php selected( $refresh_type, "loops", 1 ); ?>><?php _e('After x loops', 'revslider_refresh_addon');?></option>
			</select>

			<div class="refresh-type-wrapper" id="refresh-type-time-wrapper">
				<span id="label_revslider_refresh_min" class=" label" origtitle="<?php _e('Load URL after x minutes', 'revslider-refresh-addon');?>"><?php _e('Minutes', 'revslider_refresh_addon');?> </span>
				<input 
				
					type="number" 
					name="revslider-refresh-min" 
					class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top rs-refresh-input" 
					original-title="<?php _e('Load URL after x minutes', 'revslider-refresh-addon');?>" 
					value="<?php echo RevSliderFunctions::getVal($sliderParams,'revslider-refresh-min','0'); ?>""
					
				/>
			</div>

			<div class="refresh-type-wrapper" id="refresh-type-slide-wrapper">
				<span id="label_revslider_refresh_slide" class=" label" origtitle="<?php _e('Load URL after slide x', 'revslider-refresh-addon');?>"><?php _e('Slide Number', 'revslider_refresh_addon');?> </span>
				<input 
				
					type="number" 
					name="revslider-refresh-slide" 
					class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top rs-refresh-input" 
					original-title="<?php _e('Load URL after slide x', 'revslider-refresh-addon');?>" 
					value="<?php echo RevSliderFunctions::getVal($sliderParams,'revslider-refresh-slide','0'); ?>""
					
				/>
			</div>

			<div class="refresh-type-wrapper" id="refresh-type-slides-wrapper">
				<span id="label_revslider_refresh_slides" class=" label" origtitle="<?php _e('Load URL after x slides', 'revslider-refresh-addon');?>"><?php _e('Slides', 'revslider_refresh_addon');?> </span>
				<input 
				
					type="number" 
					name="revslider-refresh-slides" 
					class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top rs-refresh-input" 
					original-title="<?php _e('Load URL after slide x', 'revslider-refresh-addon');?>" 
					value="<?php echo RevSliderFunctions::getVal($sliderParams,'revslider-refresh-slides','0'); ?>""
					
				/>
			</div>

			<div class="refresh-type-wrapper" id="refresh-type-loops-wrapper">
				<span id="label_revslider_refresh_loops" class=" label" origtitle="<?php _e('Load URL after x loops', 'revslider-refresh-addon');?>"><?php _e('Loops', 'revslider_refresh_addon');?> </span>
				<input 
				
					type="number" 
					name="revslider-refresh-loops" 
					class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top rs-refresh-input" 
					original-title="<?php _e('Load URL after x loops', 'revslider-refresh-addon');?>" 
					value="<?php echo RevSliderFunctions::getVal($sliderParams,'revslider-refresh-loops','0'); ?>""
					
				/>
			</div>

			<?php $revslider_refresh_url_enable = RevSliderFunctions::getVal($sliderParams,'revslider-refresh-url-enable',''); ?>
			<span id="label_revslider_refresh_url_enable" class=" label" origtitle="<?php _e('Off = Reloads current URL', 'revslider-refresh-addon');?>"><?php _e('Custom URL', 'revslider_refresh_addon');?> </span>
			<input type="checkbox" class="tp-moderncheckbox withlabel" id="revslider_refresh_url_enable" name="revslider-refresh-url-enable" <?php checked( $revslider_refresh_url_enable, "true", 1 );?>/>

			<?php $revslider_refresh_url_switch = !empty($revslider_refresh_url_enable) &&  $revslider_refresh_url_enable == "true" ? '' : 'style="display:none"'; ?>
			<div id="revslider_refresh_custom_url_wrapper" <?php echo $revslider_refresh_url_switch; ?>>	
				<span id="label_revslider_refresh_custom_url" class=" label" origtitle="<?php _e('Load custom URL', 'revslider-refresh-addon');?>"><?php _e('URL', 'revslider_refresh_addon');?> </span>
				<input 
				
					type="text" 
					name="revslider-refresh-custom-url" 
					class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top rs-refresh-input" 
					original-title="<?php _e('Load custom URL', 'revslider-refresh-addon');?>" 
					value="<?php echo RevSliderFunctions::getVal($sliderParams,'revslider-refresh-custom-url','http://'); ?>""
					
				/>
			</div>
		</span>	