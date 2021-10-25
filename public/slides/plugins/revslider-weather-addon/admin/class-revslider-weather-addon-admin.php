<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_Weather_Addon
 * @subpackage Revslider_Weather_Addon/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Revslider_Weather_Addon
 * @subpackage Revslider_Weather_Addon/admin
 * @author     ThemePunch <info@themepunch.com>
 */
class Revslider_Weather_Addon_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Revslider_Weather_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Revslider_Weather_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/revslider-weather-addon-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Revslider_Weather_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Revslider_Weather_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if (isset($_GET["page"]) && $_GET["page"]=="rev_addon"){
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/revslider-weather-addon-admin.js', array( 'jquery' ), $this->version, false );
		}

	}

	/**
	 * Add the slide option button
	 *
	 * @since    1.0.0
	 */
	public function add_addon_settings_slide($_settings, $_slide, $_slider){
		
		// only add to slide editor if enabled from slider settings first
		$sliderParams = $_slider->getParams();
		if(isset($sliderParams["revslider-weather-enabled"]) && $sliderParams["revslider-weather-enabled"] == 'true') {
			$slideParams = $_slide->getParams();
			
			
			ob_start();
			include "partials/revslider-weather-addon-slide-options-display.php";
			$markup = ob_get_clean();
			
			$_settings["weather"] = array(
			
				'title'		 => "Weather",
				'icon'		 => 'eg-icon-soundcloud-1',
				'markup'	 => $markup,
				'javascript' => 'jQuery("#revslider-weather-location-type").change(function(){
					jQuery(".location-wrapper").hide();
					jQuery("#location-"+jQuery("#revslider-weather-location-type").val()+"-wrapper").show();
				});'
			   
			);
			
		}
		
		return $_settings;
		
	}

	public function add_addon_settings_slider($_settings, $_slider){
		

		$sliderParams = $_slider;

		ob_start();
		include "partials/revslider-weather-addon-slider-options-display.php";
		$markup = ob_get_clean();

		$_settings['weather'] = array(
		
			'title'		 => "Weather",
			'icon'		 => 'eg-icon-soundcloud-1',
			'markup'	 => $markup,
		    'javascript' => '
		    	jQuery("#revslider_weather_enabled").change(function(){
					if(jQuery(this).attr("checked")=="checked") jQuery("#revslider_weather_settings").show();
					else jQuery("#revslider_weather_settings").hide();
				});
		    	jQuery("#revslider-weather-location-type").change(function(){
					jQuery(".location-wrapper").hide();
					jQuery("#location-"+jQuery("#revslider-weather-location-type").val()+"-wrapper").show();
				});'
		   
		);
		
		return $_settings;
		
	}

	/**
	 * Add placeholders for Weather options
	 *
	 * @since    1.0.0
	 */
	public function add_placeholder() {
		echo '
			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_title%\')">%weather_title%</a></td><td>'.__('Weather - Title',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_temp%\')">%weather_temp%</a></td><td>'.__('Weather - Temp',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_code%\')">%weather_code%</a></td><td>'.__('Weather - Code',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_date%\')">%weather_date%</a></td><td>'.__('Weather - Date',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_day%\')">%weather_day%</a></td><td>'.__('Weather - Day',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_todayCode%\')">%weather_todayCode%</a></td><td>'.__('Weather - TodayCode',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_currently%\')">%weather_currently%</a></td><td>'.__('Weather - Currently',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_high%\')">%weather_high%</a></td><td>'.__('Weather - High',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_low%\')">%weather_low%</a></td><td>'.__('Weather - Low',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_text%\')">%weather_text%</a></td><td>'.__('Weather - Text',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_humidity%\')">%weather_humidity%</a></td><td>'.__('Weather - Humidity',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_pressure%\')">%weather_pressure%</a></td><td>'.__('Weather - Pressure',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_rising%\')">%weather_rising%</a></td><td>'.__('Weather - Rising',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_visbility%\')">%weather_visbility%</a></td><td>'.__('Weather - Visibility',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_sunrise%\')">%weather_sunrise%</a></td><td>'.__('Weather - Sunrise',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_sunset%\')">%weather_sunset%</a></td><td>'.__('Weather - Sunset',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_city%\')">%weather_city%</a></td><td>'.__('Weather - City',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_country%\')">%weather_country%</a></td><td>'.__('Weather - Country',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_region%\')">%weather_region%</a></td><td>'.__('Weather - Region',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_updated%\')">%weather_updated%</a></td><td>'.__('Weather - Updated',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_link%\')">%weather_link%</a></td><td>'.__('Weather - Link',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_heatindex%\')">%weather_heatindex%</a></td><td>'.__('Weather - Heatindex',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_thumbnail%\')">%weather_thumbnail%</a></td><td>'.__('Weather - Thumbnail',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_image%\')">%weather_image%</a></td><td>'.__('Weather - Image',"revslider-weather-addon").'</td></tr>

			<tr id="revslider_weather_icon_placeholder"><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_icon%\')">%weather_icon%</a></td><td>'.__('Weather - Icon',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_units_temp%\')">%weather_units_temp%</a></td><td>'.__('Weather - Units Temp',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_units_distance%\')">%weather_units_distance%</a></td><td>'.__('Weather - Units Distance',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_units_pressure%\')">%weather_units_pressure%</a></td><td>'.__('Weather - Units Pressure',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_units_speed%\')">%weather_units_speed%</a></td><td>'.__('Weather - Units Speed',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_wind_chill%\')">%weather_wind_chill%</a></td><td>'.__('Weather - Wind Chill',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_wind_direction%\')">%weather_wind_direction%</a></td><td>'.__('Weather - Wind Direction',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_wind_speed%\')">%weather_wind_speed%</a></td><td>'.__('Weather - Wind Speed',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_alt_temp%\')">%weather_alt_temp%</a></td><td>'.__('Weather - Alt Temp',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_alt_high%\')">%weather_alt_high%</a></td><td>'.__('Weather - Alt High',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_alt_low%\')">%weather_alt_low%</a></td><td>'.__('Weather - Alt Low',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_alt_unit%\')">%weather_alt_unit%</a></td><td>'.__('Weather - Alt Unit',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_description%\')">%weather_description%</a></td><td>'.__('Weather - Description',"revslider-weather-addon").'</td></tr>


			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_date_forecast:x%\')">%weather_date_forecast:x%</a></td><td>'.__('Weather - ForeCast Date x Days from now (x:1-9)',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_day_forecast:x%\')">%weather_day_forecast:x%</a></td><td>'.__('Weather - ForeCast Day x Day',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_code_forecast:x%\')">%weather_code_forecast:x%</a></td><td>'.__('Weather - ForeCast Day x Code',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_high_forecast:x%\')">%weather_high_forecast:x%</a></td><td>'.__('Weather - ForeCast Day x High',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_low_forecast:x%\')">%weather_low_forecast:x%</a></td><td>'.__('Weather - ForeCast Day x Low',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_alt_high_forecast:x%\')">%weather_alt_high_forecast:x%</a></td><td>'.__('Weather - ForeCast Day x Alt High',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_alt_low_forecast:x%\')">%weather_alt_low_forecast:x%</a></td><td>'.__('Weather - ForeCast Day x Alt Low',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_thumbnail_forecast:x%\')">%weather_thumbnail_forecast:x%</a></td><td>'.__('Weather - ForeCast Day x Thumbnail',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_image_forecast:x%\')">%weather_image_forecast:x%</a></td><td>'.__('Weather - ForeCast Day x Image',"revslider-weather-addon").'</td></tr>

			<tr id="revslider_weather_icon_placeholder"><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_icon_forecast:x%\')">%weather_icon_forecast:x%</a></td><td>'.__('Weather - ForeCast Day x Icon',"revslider-weather-addon").'</td></tr>

			<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'%weather_text_forecast:x%\')">%weather_text_forecast:x%</a></td><td>'.__('Weather - ForeCast Day x Text',"revslider-weather-addon").'</td></tr>

		';
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'partials/revslider-weather-addon-admin-display.php' );
	}

}
