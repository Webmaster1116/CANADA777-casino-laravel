<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_Refresh_Addon
 * @subpackage Revslider_Refresh_Addon/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Revslider_Refresh_Addon
 * @subpackage Revslider_Refresh_Addon/admin
 * @author     ThemePunch <info@themepunch.com>
 */
class Revslider_Refresh_Addon_Admin {

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
		 * defined in Revslider_Refresh_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Revslider_Refresh_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if(isset($_GET["page"]) && $_GET["page"]=="rev_addon"){
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/revslider-refresh-addon-admin.css', array(), $this->version, 'all' );
		}
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
		 * defined in Revslider_Refresh_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Revslider_Refresh_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if(isset($_GET["page"]) && $_GET["page"]=="rev_addon"){
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/revslider-refresh-addon-admin.js', array( 'jquery' ), $this->version, false );
		}
	}


	public function add_addon_settings_slider($_settings, $_slider){
		
		$sliderParams = $_slider;

		ob_start();
		include "partials/revslider-refresh-addon-slider-options-display.php";
		$markup = ob_get_clean();

		$_settings['refresh'] = array(
		
			'title'		 => "(Re)Load URL",
			'icon'		 => 'eg-icon-ccw',
			'markup'	 => $markup,
		    'javascript' => '
		    	jQuery("#revslider_refresh_enabled").change(function(){
					if(jQuery(this).attr("checked")=="checked") jQuery("#revslider_refresh_settings").show();
					else jQuery("#revslider_refresh_settings").hide();
				});
				jQuery("#revslider_refresh_url_enable").change(function(){
					if(jQuery(this).attr("checked")=="checked") jQuery("#revslider_refresh_custom_url_wrapper").show();
					else jQuery("#revslider_refresh_custom_url_wrapper").hide();
				});
		    	jQuery("#revslider-refresh-type").change(function(){
					jQuery(".refresh-type-wrapper").hide();
					jQuery("#refresh-type-"+jQuery("#revslider-refresh-type").val()+"-wrapper").show();
				});
				jQuery("#revslider-refresh-type").change();
			'
		   
		);
		
		return $_settings;
		
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'partials/revslider-refresh-addon-admin-display.php' );
	}

}
