<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_Refresh_Addon
 * @subpackage Revslider_Refresh_Addon/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Revslider_Refresh_Addon
 * @subpackage Revslider_Refresh_Addon/public
 * @author     ThemePunch <info@themepunch.com>
 */
class Revslider_Refresh_Addon_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/revslider-refresh-addon-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/revslider-refresh-addon-public.js', array( 'jquery' ), $this->version, false );

	}

	public function add_addon_js($slider , $slider_html_id){
		$sliderParams = $slider->getParams();

		$slider_id = $slider->getId();
		$revslider_refresh_enabled = RevSliderFunctions::getVal($sliderParams,'revslider-refresh-enabled','false');
		$revslider_refresh_min = intval(RevSliderFunctions::getVal($sliderParams,'revslider-refresh-min','0'));
		$revslider_refresh_type = RevSliderFunctions::getVal($sliderParams,'revslider-refresh-type','time');
		$revslider_refresh_loops = intval(RevSliderFunctions::getVal($sliderParams,'revslider-refresh-loops', 0));
		$revslider_refresh_slide = intval(RevSliderFunctions::getVal($sliderParams,'revslider-refresh-slide', 0));
		$revslider_refresh_url_enabled = RevSliderFunctions::getVal($sliderParams,'revslider-refresh-url-enabled','false');
		$revslider_refresh_custom_url = RevSliderFunctions::getVal($sliderParams,'revslider-refresh-custom-url','');

		if(!$revslider_refresh_url_enabled || empty($revslider_refresh_custom_url) || $revslider_refresh_custom_url == "http://" || $revslider_refresh_custom_url == "https://"){
			$refresh_url_js = "self.location.reload();";
		}
		else{
			$refresh_url_js = 'self.location.href="'.$revslider_refresh_custom_url.'";';
		}
		
		if($revslider_refresh_enabled == "true"){
			switch ($revslider_refresh_type) {
				case 'time':
					if($revslider_refresh_min > 0){
						echo "setInterval(function(){ ".$refresh_url_js." }, ". $revslider_refresh_min*60*1000 .");";
					}
					break;
				case 'slide': ?>
						var currentSlide;

						revapi<?php echo $slider_id; ?>.bind('revolution.slide.onchange', function(event, data) {
						     currentSlide = data.slideIndex;
						});

						revapi<?php echo $slider_id; ?>.bind('revolution.slide.onbeforeswap', function(event, data) {
							if(currentSlide === <?php echo $revslider_refresh_slide; ?>){
						    	revapi<?php echo $slider_id; ?>.revkill();
						    	<?php echo $refresh_url_js; ?>
							}
						});
					<?php
					break;
				case 'loops': ?>
					var loops = <?php echo $revslider_refresh_loops;?>;
					var totalSlides=0;

					revapi<?php echo $slider_id; ?>.bind("revolution.slide.onloaded",function (e) {
						totalSlides = revapi<?php echo $slider_id; ?>.revmaxslide();
						//totalSlides = totalSlides*<?php echo $revslider_refresh_loops;?>;
					});
					
					revapi<?php echo $slider_id; ?>.bind('revolution.slide.onchange', function(event, data) {
					     var currentSlide = data.slideIndex;
					     if(currentSlide === totalSlides)
					      loops--;
					});

					revapi<?php echo $slider_id; ?>.bind('revolution.slide.onbeforeswap', function(event, data) {
						if(!loops){
					    	revapi<?php echo $slider_id; ?>.revkill();
					    	<?php echo $refresh_url_js; ?>
						}
					});
					 <?php
					break;
			}
		}
		?>
		<?php
	}

	public function display_slider() {
		die(do_shortcode('[rev_slider alias="'.$_POST["slider"].'"]'));
	}

	
}
