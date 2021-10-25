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

//Default Values

$revslider_weather_location_type_def 	= isset($sliderParams["revslider-weather-location-type"]) 	? $sliderParams["revslider-weather-location-type"] 	: '';
$revslider_weather_location_name_def 	= isset($sliderParams["revslider-weather-location-name"]) 	? $sliderParams["revslider-weather-location-name"] 	: '';
$revslider_weather_location_woeid_def 	= isset($sliderParams["revslider-weather-location-woeid"]) 	? $sliderParams["revslider-weather-location-woeid"] : '';
$revslider_weather_unit_def 			= isset($sliderParams["revslider-weather-unit"]) 			? $sliderParams["revslider-weather-unit"] 			: ''; 



?>


<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="revslider-weather-addon-settings">		
	<div id="revslider-weather-addon-settings-regular" class="revslider-weather-settings">
	
		<span class="rs-layer-toolbar-box">
					
			<i class="rs-mini-layer-icon eg-icon-resize-full-2 rs-toolbar-icon tipsy_enabled_top" original-title="<?php _e('Location definition type, free text or the concrete WOEID', 'revslider-weather-addon');?>"></i>
			<?php $revslider_weather_location_type = RevSliderFunctions::getVal($slideParams,'revslider-weather-location-type', $revslider_weather_location_type_def); ?>
			<select id="revslider-weather-location-type" name="revslider-weather-location-type" class="rs-layer-input-field tipsy_enabled_top" original-title="<?php _e('Location definition type, free text or the concrete WOEID', 'revslider-weather-addon');?>">
				<option value="name" <?php selected( $revslider_weather_location_type, "name", 1 );?>><?php _e('City/Location Name', 'revslider-weather-addon');?></option>
				<option value="woeid" <?php selected( $revslider_weather_location_type, "woeid", 1 );?>><?php _e('WOEID', 'revslider-weather-addon');?></option>
			</select>
					
		</span>

		<?php $revslider_weather_location_type_switch = $revslider_weather_location_type == 'name' ? '' : 'style="display:none"' ?>

		<span id="location-name-wrapper" <?php echo $revslider_weather_location_type_switch; ?> class="rs-layer-toolbar-box location-wrapper">
			
			<i class="rs-mini-layer-icon eg-icon-pin-outline rs-toolbar-icon tipsy_enabled_top" original-title="<?php _e('Location: City name', 'revslider-weather-addon');?>"></i>
			<input 
			
				type="text" 
				name="revslider-weather-location-name" 
				class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
				original-title="<?php _e('Location: City name', 'revslider-weather-addon');?>" 
				value="<?php echo RevSliderFunctions::getVal($slideParams,'revslider-weather-location-name',$revslider_weather_location_name_def ); ?>"
				
			/>
			
		</span>

		<?php $revslider_weather_location_type_switch = $revslider_weather_location_type == 'woeid' ? '' : 'style="display:none"' ?>

		<span id="location-woeid-wrapper" <?php echo $revslider_weather_location_type_switch; ?> class="rs-layer-toolbar-box location-wrapper">
			
			<i class="rs-mini-layer-icon eg-icon-pin-outline rs-toolbar-icon tipsy_enabled_top" original-title="<?php _e('Yahoo! Where on Earth ID - WOEID', 'revslider-weather-addon');?>"></i>
			<input 
			
				type="number" 
				name="revslider-weather-location-woeid" 
				class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
				original-title="<?php _e('Yahoo! Where on Earth ID - WOEID', 'revslider-weather-addon');?>" 
				value="<?php echo RevSliderFunctions::getVal($slideParams,'revslider-weather-location-woeid',$revslider_weather_location_woeid_def); ?>"
				
			/>
			
		</span>

		<span class="rs-layer-toolbar-box">
					
			<i class="rs-mini-layer-icon eg-icon-magic rs-toolbar-icon tipsy_enabled_top" original-title="<?php _e('Fahrenheit or Celsius (°F or °C)', 'revslider-weather-addon');?>"></i>
			<?php $revslider_weather_unit = RevSliderFunctions::getVal($slideParams,'revslider-weather-unit', $revslider_weather_unit_def); ?>
			<select name="revslider-weather-unit" class="rs-layer-input-field tipsy_enabled_top" original-title="<?php _e('Fahrenheit or Celsius (°F or °C)', 'revslider-weather-addon');?>">
				<option value="f" <?php selected( $revslider_weather_unit, "f", 1 );?>><?php _e('°F', 'revslider-weather-addon');?></option>
				<option value="c" <?php selected( $revslider_weather_unit, "c", 1 );?>><?php _e('°C', 'revslider-weather-addon');?></option>
			</select>
					
		</span>

		<!--span class="rs-layer-toolbar-box">
			
			<i class="rs-mini-layer-icon rs-icon-chooser-2 rs-toolbar-icon tipsy_enabled_top" original-title="<?php _e('Refresh rate in minutes (refreshs also with every start)', 'revslider-weather-addon');?>"></i>
			<input 
			
				type="number" 
				name="revslider-weather-refresh" 
				class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
				original-title="<?php _e('Refresh rate in minutes (refreshs also with every start)', 'revslider-weather-addon');?>" 
				value="<?php echo RevSliderFunctions::getVal($slideParams,'revslider-weather-refresh','60'); ?>"
				
			/>
			
		</span-->
			
	</div>
</div>