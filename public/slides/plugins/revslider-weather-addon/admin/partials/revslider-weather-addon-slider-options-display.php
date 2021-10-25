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

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
		<span class="label" id="label_weather_enabled" origtitle="<?php _e("Enable/Disable Weather Info for this Slider<br><br>", 'revslider-weather-location-type');?>"> <?php _e('Enable Weather', 'revslider-weather-location-type');?></span>
		<?php $revslider_weather_enabled = RevSliderFunctions::getVal($sliderParams,'revslider-weather-enabled',''); ?>
		<input type="checkbox" class="tp-moderncheckbox withlabel" id="revslider_weather_enabled" name="revslider-weather-enabled" <?php checked( $revslider_weather_enabled, "true", 1 );?>/>

		<?php $revslider_weather_switch = !empty($revslider_weather_enabled) &&  $revslider_weather_enabled == "true" ? '' : 'style="display:none"'; ?>
		<span id="revslider_weather_settings" <?php echo $revslider_weather_switch; ?>>	
	
			<span id="label_evslider_weather_location_name" class=" label" origtitle="<?php _e('Refresh rate in minutes (refreshs also with every start, 0 = no dynamic refresh)', 'revslider-weather-addon');?>"><?php _e('Refresh Rate', 'revslider-weather-location-type');?> </span>
			<input 
			
				type="number" 
				name="revslider-weather-refresh" 
				class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
				original-title="<?php _e('Refresh rate in minutes (refreshs also with every start, 0 = no dynamic refresh)', 'revslider-weather-addon');?>" 
				value="<?php echo RevSliderFunctions::getVal($sliderParams,'revslider-weather-refresh','0'); ?>""
				
			/>
		
			<span class="label" origtitle="Settings can be changed on each Slide<br><br>"><h4>Default Settings</h4></span>
			<span id="label_revslider_weather_location_type" class="label" origtitle="Location definition type, free text or the concrete WOEID<br><br>"><?php _e('Location&nbsp;Type', 'revslider-weather-location-type');?> </span>
			<?php $revslider_weather_location_type = RevSliderFunctions::getVal($sliderParams,'revslider-weather-location-type','name'); ?>
			<select id="revslider-weather-location-type" name="revslider-weather-location-type" class="text-sidebar withlabel">
				<option value="name" <?php selected( $revslider_weather_location_type, "name", 1 );?>><?php _e('City/Location Name', 'revslider-weather-addon');?></option>
				<option value="woeid" <?php selected( $revslider_weather_location_type, "woeid", 1 );?>><?php _e('WOEID', 'revslider-weather-addon');?></option>
			</select>
			<div class="clear"></div>

			<?php $revslider_weather_location_type_switch = $revslider_weather_location_type == 'name' ? '' : 'style="display:none"'; ?>
			<span id="location-name-wrapper" <?php echo $revslider_weather_location_type_switch; ?> class=" location-wrapper">
				
				<span id="label_evslider_weather_location_name" class=" label" origtitle="Location: City name"><?php _e('City', 'revslider-weather-location-type');?> </span>
				<input 
				
					type="text" 
					name="revslider-weather-location-name" 
					class="textbox-caption withlabel input-deepselects rs-layer-input-field" 
					original-title="<?php _e('Location: City name', 'revslider-weather-addon');?>" 
					value="<?php echo RevSliderFunctions::getVal($sliderParams,'revslider-weather-location-name','Cologne'); ?>"
					
				/>
				
			</span>
			<div class="clear"></div>
			<?php $revslider_weather_location_type_switch = $revslider_weather_location_type == 'woeid' ? '' : 'style="display:none"' ?>

			<span id="location-woeid-wrapper" <?php echo $revslider_weather_location_type_switch; ?> class=" location-wrapper">
				
				<span id="label_evslider_weather_location_woeid" class="label" origtitle="Yahoo! Where on Earth ID - WOEID">WOEID </span>
				<input 
				
					type="number" 
					name="revslider-weather-location-woeid" 
					class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
					original-title="<?php _e('Yahoo! Where on Earth ID - WOEID', 'revslider-weather-addon');?>" 
					value="<?php echo RevSliderFunctions::getVal($sliderParams,'revslider-weather-location-woeid','667931'); ?>"
					
				/>
				
			</span>
			<div class="clear"></div>
			<span>
						
				<span id="label_evslider_weather_location_woeid" class="label" origtitle="Fahrenheit or Celsius (°F or °C)"><?php _e('Units', 'revslider-weather-location-type');?> </span>
				<?php $revslider_weather_unit = RevSliderFunctions::getVal($sliderParams,'revslider-weather-unit','f'); ?>
				<select name="revslider-weather-unit" class="rs-layer-input-field tipsy_enabled_top" original-title="<?php _e('Fahrenheit or Celsius (°F or °C)', 'revslider-weather-addon');?>">
					<option value="f" <?php selected( $revslider_weather_unit, "f", 1 );?>><?php _e('°F', 'revslider-weather-addon');?></option>
					<option value="c" <?php selected( $revslider_weather_unit, "c", 1 );?>><?php _e('°C', 'revslider-weather-addon');?></option>
				</select>
						
			</span>

		</span>	
	