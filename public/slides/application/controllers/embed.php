<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Nwdthemes Standalone Slider Revolution
 *
 * @package     StandaloneRevslider
 * @author		Nwdthemes <mail@nwdthemes.com>
 * @link		http://nwdthemes.com/
 * @copyright   Copyright (c) 2015. Nwdthemes
 * @license     http://themeforest.net/licenses/terms/regular
 */

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
$wp_version = 'NA';
$revslider_screens = array();
$revslider_fonts = array();

class Embed extends CI_Controller {

	/**
	 * Constructor
	 */

	public function __construct() {
		global $wpdb;
		parent::__construct();
        if ( !defined('RS_IMAGE_PATH') ) {
            define('RS_IMAGE_PATH', RS_IMAGE_PATH_COMMON);
            define('RS_THUMB_PATH', RS_IMAGE_PATH . '/' . RS_THUMB_FOLDER);
            define('WP_CONTENT_DIR', RS_IMAGE_PATH);
        }
        force_config_ssl();
		$this->load->model('wpdb_model', 'WPDB');
		$wpdb = $this->WPDB;
        $this->load->library('plugin');
        $this->_loadPlugins();
	}

	/**
	 * Get embed code
	 */

	public function index() {

		$key = $this->input->post('key');
        $alias = $this->input->post('alias');
        if ($key === FALSE || empty($alias)) {
            exit();
        }

		$revSliderFront = new RevSliderFront();
        RevSliderFront::onAddScripts();

		$content = '';

		if ($key == 0) {
			$custom_css = RevSliderOperations::getStaticCss();
			$custom_css = RevSliderCssParser::compress_css($custom_css);
			$content .= '<style type="text/css">' . $custom_css . '</style>' . "\n";
		}

		ob_start();
		RevSliderOutput::setSerial($key);
		$slider = RevSliderOutput::putSlider($alias);
		$content .= ob_get_contents();
		ob_clean();
		ob_end_clean();

		ob_start();
        $revSliderFront::load_icon_fonts();
        $revSliderFront::add_setREVStartSize();
		$content = ob_get_contents() . $content;
		ob_clean();
		ob_end_clean();

		do_action('wp_enqueue_scripts');

        $assets = array();

		foreach (self::get_instance()->data->get('styles', array()) as $style) {
            $assets[] = array(
                'file' => force_ssl($style),
                'include' => '<link rel="stylesheet" href="' . force_ssl($style) . '" type="text/css" media="all" />'
            );
		}

		foreach (self::get_instance()->data->get('scripts', array()) as $script) {
            $assets[] = array(
                'file' => force_ssl($script),
                'include' => '<script type="text/javascript" src="' . force_ssl($script) . '"></script>'
            );
		}

        if(!empty($slider)){
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

        $data = array(
            'assets' => $assets,
            'slider' => $content
        );
		echo json_encode($data);
	}

    /**
     *  Load plugins
     */

    private function _loadPlugins() {
        foreach ($this->plugin->getActivePlugins() as $plugin) {
            if (file_exists(FCPATH . WP_PLUGIN_DIR . $plugin)) {
                include FCPATH . WP_PLUGIN_DIR . $plugin;
            }
        }
        do_action('plugins_loaded');
    }
}
