<?php
/**
 * Nwdthemes Standalone Slider Revolution
 *
 * @package     StandaloneRevslider
 * @author		Nwdthemes <mail@nwdthemes.com>
 * @link		http://nwdthemes.com/
 * @copyright   Copyright (c) 2015. Nwdthemes
 * @license     http://themeforest.net/licenses/terms/regular
 */

global $revSliderVersion, $base_url, $system_folder, $application_folder, $CFG, $UNI, $revSliderEmbedder, $wpdb;

$base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$base_url .= "://".$_SERVER['HTTP_HOST'];
$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
$base_url = str_replace('/install', '', $base_url);

// CI base data
$system_folder = "system";
$application_folder = 'application';

if (strpos($system_folder, '/') === FALSE)
{
	if (function_exists('realpath') AND @realpath(dirname(__FILE__)) !== FALSE)
		$system_folder = realpath(dirname(__FILE__)).'/'.$system_folder;
}
else
{
	$system_folder = str_replace("\\", "/", $system_folder);
}

// CI constants

define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('EXT', '.'.pathinfo(__FILE__, PATHINFO_EXTENSION));
define('BASEPATH', $system_folder.'/');
define('FCPATH', str_replace(SELF, '', __FILE__));
define('BASEURL', $base_url);
define('APPPATH', FCPATH . $application_folder.'/');

// Include dev configs

if (file_exists(APPPATH.'config/dev.php')) {
    include APPPATH.'config/dev.php';
}

// CI classes include

require($system_folder . '/database/DB.php');
require($system_folder . '/core/Config.php');
require($system_folder . '/core/Common.php');
require($system_folder . '/core/Loader.php');
require($system_folder . '/core/Utf8.php');
require($system_folder . '/core/Input.php');
require($application_folder . '/config/constants.php');
require($application_folder . '/config/revslider.php');
require($system_folder . '/helpers/url_helper.php');
require($application_folder . '/libraries/data.php');
require($application_folder . '/libraries/plugin.php');
require($application_folder . '/helpers/trace_helper.php');
require($application_folder . '/helpers/general_helper.php');
require($application_folder . '/helpers/option_helper.php');
require($application_folder . '/helpers/language_helper.php');
require($application_folder . '/helpers/images_helper.php');
require($application_folder . '/helpers/plugin_helper.php');

//include framework files
require_once(RS_PLUGIN_PATH . 'includes/framework/include-framework.php');

//include bases
require_once($folderIncludes . 'base.class.php');
require_once($folderIncludes . 'elements-base.class.php');
require_once($folderIncludes . 'base-admin.class.php');
require_once($folderIncludes . 'base-front.class.php');

//include product files
require_once(RS_PLUGIN_PATH . 'includes/globals.class.php');
require_once(RS_PLUGIN_PATH . 'includes/operations.class.php');
require_once(RS_PLUGIN_PATH . 'includes/slider.class.php');
require_once(RS_PLUGIN_PATH . 'includes/output.class.php');
require_once(RS_PLUGIN_PATH . 'includes/slide.class.php');
require_once(RS_PLUGIN_PATH . 'includes/navigation.class.php');
require_once(RS_PLUGIN_PATH . 'includes/object-library.class.php');
require_once(RS_PLUGIN_PATH . 'includes/template.class.php');
require_once(RS_PLUGIN_PATH . 'includes/external-sources.class.php');
require_once(RS_PLUGIN_PATH . 'includes/tinybox.class.php');
require_once(RS_PLUGIN_PATH . 'includes/extension.class.php');
require_once(RS_PLUGIN_PATH . 'public/revslider-front.class.php');

// slider version
$revSliderVersion = RevSliderGlobals::SLIDER_REVISION;

// Rev Slider Embedder Class

$CFG = new CI_Config;
$CFG->set_item('rs_image_sizes', $config['rs_image_sizes']);
$UNI = new CI_Utf8;

class RevSliderEmbedder {

	private static $instance;
	public static $table_prefix;
	public $load;
	public $db;
	public $config;
	public $input;
	public $utf8;

	/**
	 * Constructor
	 *
	 */
	public function __construct() {

        if ( !defined('RS_IMAGE_PATH') ) {
            define('RS_IMAGE_PATH', RS_IMAGE_PATH_COMMON);
            define('RS_THUMB_PATH', RS_IMAGE_PATH . '/' . RS_THUMB_FOLDER);
            define('WP_CONTENT_DIR', RS_IMAGE_PATH);
        }

        global $CFG;
		global $UNI;

		$CFG->set_item('global_xss_filtering', FALSE);
		$CFG->set_item('csrf_protection', FALSE);

		self::$instance =& $this;
		self::$instance->config = & $CFG;
		self::$instance->load = new CI_Loader;
		self::$instance->utf8 = & $UNI;
		self::$instance->input = new CI_Input;
		self::$instance->data = new Data;

        include(APPPATH.'config/database'.EXT);
		$this->db = DB('default', true);
		$this->db->db_connect();
		$this->db->db_select();

		self::$table_prefix = $db[$active_group]['dbprefix'];

		RevSliderGlobals::$table_sliders = self::$table_prefix . RevSliderGlobals::TABLE_SLIDERS_NAME;
		RevSliderGlobals::$table_slides = self::$table_prefix . RevSliderGlobals::TABLE_SLIDES_NAME;
		RevSliderGlobals::$table_static_slides = self::$table_prefix . RevSliderGlobals::TABLE_STATIC_SLIDES_NAME;
		RevSliderGlobals::$table_settings = self::$table_prefix . RevSliderGlobals::TABLE_SETTINGS_NAME;
		RevSliderGlobals::$table_css = self::$table_prefix . RevSliderGlobals::TABLE_CSS_NAME;
		RevSliderGlobals::$table_layer_anims = self::$table_prefix . RevSliderGlobals::TABLE_LAYER_ANIMS_NAME;
		RevSliderGlobals::$table_navigation = self::$table_prefix . RevSliderGlobals::TABLE_NAVIGATION_NAME;
	}

	/**
	 * Returns current instance of Embedder
	 *
	 */
	public static function &get_instance() {
		return self::$instance;
	}

    /**
     * check if slider exist
     *
     * @param string $alias
     * @return bool
     */
	public static function isSliderExist($alias) {
        $slider = new RevSlider();
        return $slider->isAliasExistsInDB($alias);
	}

	/**
	 *	Output header includes
	 *
	 *	@param	boolean $jQueryInclude	Include jQuery
	 *	@param	boolean	$directOutput	Direct output or return code
	 *  @return string
	 */
	public static function headIncludes($jQueryInclude = true, $directOutput = true) {
        $output = self::cssIncludes($directOutput);
        $output .= self::jsIncludes($jQueryInclude, $directOutput);
		return $output;
	}

	/**
	 *	Output CSS includes
	 *
	 *	@param	boolean	$directOutput	Direct output or return code
	 *  @return string
	 */
	public static function cssIncludes($directOutput = true) {

		$output = '';
		$output .= '<link rel="stylesheet" href="' . RS_PLUGIN_URL . 'public/assets/css/settings.css' . '" type="text/css" media="all" />' . "\n";

		$custom_css = RevSliderOperations::getStaticCss();
		$custom_css = RevSliderCssParser::compress_css($custom_css);
		if ( $custom_css != '' ) {
			$output .= '<style type="text/css">' . $custom_css . '</style>' . "\n";
		}

		if ($directOutput) {
            echo $output;
        }
		return $output;
	}

	/**
	 *	Output JS includes
	 *
	 *	@param	boolean $jQueryInclude	Include jQuery
	 *	@param	boolean	$directOutput	Direct output or return code
	 *  @return string|null
	 */

	public static function jsIncludes($jQueryInclude = true, $directOutput = true) {

		$output = '';

		if ($jQueryInclude) {
			$output .= '<script type="text/javascript" src="' . RS_BASE_URL . 'assets/js/includes/jquery/jquery.js' . '"></script>' . "\n";
		}

		$output .= '<script type="text/javascript" src="' . RS_PLUGIN_URL .'public/assets/js/jquery.themepunch.tools.min.js' . '"></script>' . "\n";
		$output .= '<script type="text/javascript" src="' . RS_PLUGIN_URL .'public/assets/js/jquery.themepunch.revolution.min.js' . '"></script>' . "\n";

		if ($directOutput) {
            echo $output;
        }
		return $output;
	}

	/**
	 *	Put Slider
	 *
	 *	@param	int|string $data Slider ID or Alias
	 *	@param	boolean	$directOutput	Direct output or return code
	 *	@return string
	 */

	public static function putRevSlider($data, $directOutput = true) {

		// Do not output Slider if we are on mobile
		ob_start();
		$slider = RevSliderOutput::putSlider($data);
		$content = ob_get_contents();
		ob_clean();
		ob_end_clean();

        ob_start();
        do_action('wp_footer');
        $content = ob_get_contents() . $content;
        ob_clean();
        ob_end_clean();

        if(!empty($slider)){

            $content = self::add_styles_and_scripts($slider)
                     . self::load_icon_fonts()
                     . RevSliderFront::add_setREVStartSize()
                     . $content;

            // Do not output Slider if we are on mobile
            $disable_on_mobile = $slider->getParam("disable_on_mobile","off");
            if($disable_on_mobile == 'on'){
                $mobile = (strstr($_SERVER['HTTP_USER_AGENT'],'Android') || strstr($_SERVER['HTTP_USER_AGENT'],'webOS') || strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') ||strstr($_SERVER['HTTP_USER_AGENT'],'iPod') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad') || strstr($_SERVER['HTTP_USER_AGENT'],'Windows Phone') || wp_is_mobile()) ? true : false;
                if($mobile) return false;
            }

            $show_alternate = $slider->getParam("show_alternative_type","off");

            if($show_alternate == 'mobile' || $show_alternate == 'mobile-ie8'){
                if(strstr($_SERVER['HTTP_USER_AGENT'],'Android') || strstr($_SERVER['HTTP_USER_AGENT'],'webOS') || strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') ||strstr($_SERVER['HTTP_USER_AGENT'],'iPod') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad') || strstr($_SERVER['HTTP_USER_AGENT'],'Windows Phone') || wp_is_mobile()){
                    $show_alternate_image = $slider->getParam("show_alternate_image","");
                    $content = '<img class="tp-slider-alternative-image" src="'.$show_alternate_image.'" data-no-retina>';
                }
            }
        }

		if ($directOutput) {
            echo $content;
        }
		return $content;
	}

	/**
	 *	Add icon fonts
	 */

	public static function load_icon_fonts(){
		global $fa_icon_var,$pe_7s_var;
		$content = '';
		if($fa_icon_var) $content .= "<link rel='stylesheet' property='stylesheet' id='rs-icon-set-fa-icon-css'  href='" . RS_PLUGIN_URL . "public/assets/fonts/font-awesome/css/font-awesome.css' type='text/css' media='all' />";
		if($pe_7s_var) $content .= "<link rel='stylesheet' property='stylesheet' id='rs-icon-set-pe-7s-css'  href='" . RS_PLUGIN_URL . "public/assets/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css' type='text/css' media='all' />";
		return $content;
	}

    /**
     * Load additional styles and scripts
     *
     * @param RevSliderSlider $slider
     * @return string
     */

    public static function add_styles_and_scripts($slider) {

        do_action('wp_enqueue_scripts');

        $content = '';

		foreach (self::get_instance()->data->get('styles', array()) as $style) {
			$content .= '<link rel="stylesheet" type="text/css"  media="all" href="' . force_ssl($style) . '" />' . "\n";
		}

		foreach (self::get_instance()->data->get('scripts', array()) as $script) {
			$content .= '<script type="text/javascript" src="' . force_ssl($script) . '"></script>' . "\n";
		}

        $localizeScripts = self::$instance->data->get('localize_scripts');
        if ($localizeScripts) {
            $content .= '<script type="text/javascript">' . "\n";
            foreach ($localizeScripts as $localizeScript) {
                $content .= 'var ' . $localizeScript['var'] . ' = ' . json_encode($localizeScript['lang']) . "\n";;
            }
            $content .= '</script>' . "\n";
        }

        return $content;
    }

	public function enqueue_styles(){
	}
}

// Create new instance

$revSliderEmbedder = new RevSliderEmbedder();
$revSliderEmbedder->plugin = new Plugin;

force_config_ssl();

// Global DB instance

$revSliderEmbedder->load->model('wpdb_model', 'WPDB');
$wpdb = $revSliderEmbedder->WPDB;

// Add actions and filters

add_filter('punchfonts_modify_url', array('RevSliderFront', 'modify_punch_url'));
add_action('wp_enqueue_scripts', array($revSliderEmbedder, 'enqueue_styles'));

// Load plugins

foreach ($revSliderEmbedder->plugin->getActivePlugins() as $plugin) {
    if (file_exists(FCPATH . WP_PLUGIN_DIR . $plugin)) {
        include FCPATH . WP_PLUGIN_DIR . $plugin;
    }
}
do_action('plugins_loaded');

// Get intance globally

function &get_instance() {
	global $revSliderEmbedder;
	return $revSliderEmbedder::get_instance();
}

// define plugin url
$rs_base_url = base_url();
if ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ) {
	$rs_base_url = str_replace('http://', 'https://', $rs_base_url);
} else {
	$rs_base_url = str_replace('https://', 'http://', $rs_base_url);
}
define('RS_BASE_URL', $rs_base_url );
define('RS_PLUGIN_URL', $rs_base_url . 'revslider/');