<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(RS_PLUGIN_PATH . 'includes/globals.class.php');
include_once APPPATH . "libraries/Gettext/autoloader.php";

use Gettext\Translator;

/**
 * Nwdthemes Standalone Slider Revolution
 *
 * @package     StandaloneRevslider
 * @author		Nwdthemes <mail@nwdthemes.com>
 * @link		http://nwdthemes.com/
 * @copyright   Copyright (c) 2015. Nwdthemes
 * @license     http://themeforest.net/licenses/terms/regular
 */

class RS_Controller extends CI_Controller {

	public $translator;

	/**
	 * Constructor
	 */
	public function __construct() {

        // No cache headers
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

		parent::__construct();

		// Check the database settings
		if (defined('ENVIRONMENT') && ENVIRONMENT == 'install') {
			redirect(base_url().'install/');
		}

		// check if admin user exists
		$this->load->model('user_model', 'User');
		if ( ! $this->User->exists()) {
			redirect(base_url().'install/');
		}

        force_config_ssl();

        $this->load->library('session');

        //include plugins
        $this->_includePlugins();

        //set image path if not set yet by plugins
        if ( !defined('RS_IMAGE_PATH') ) {
            define('RS_IMAGE_PATH', RS_IMAGE_PATH_COMMON);
            define('RS_THUMB_PATH', RS_IMAGE_PATH . '/' . RS_THUMB_FOLDER);

            define('WP_CONTENT_DIR', RS_IMAGE_PATH);
        }

        // define product slug

        define('RS_PLUGIN_SLUG', apply_filters('set_revslider_slug', 'visual-editor'));

		// Check for user session
		if ( ! $this->session->userdata('user_id') ) {

			if ($this->input->is_ajax_request()) {
				$response = array(
					'success'		=> true,
					'message'		=> __('Your session has expired. Please log in again.'),
					'is_redirect'	=> true,
					'redirect_url'	=> site_url('c=account&m=login')
				);
				header('Content-Type: application/json');
				echo json_encode($response);
                die();
			}

			if ($this->input->post('client_action') == 'preview_slider') {
				$response = '<html><body><script type="text/javascript">parent.location.reload()</script></body></html>';
				header('Content-Type: text/html');
				echo $response;
                die();
			}

            $controller = $this->router->fetch_class();
            if ( 'account' != $controller ) {
                redirect('c=account&m=login');
            }
		}

		// Set language
		if ($this->input->get('lang')) {
			set_language($this->input->get('lang'));
		}

		// Set translation
		$translations = Gettext\Translations::fromPoFile(RS_PLUGIN_PATH . 'languages/revslider-' . get_language() . '.po');
		$this->translator = new Translator();
		$this->translator->loadTranslations($translations);
	}

    /**
     *  include plugins
     */
    private function _includePlugins() {

        $this->load->library('plugin');

		do_action('plugins_included_before');

        // temporary force no admin to load plugins as for frontend
/*        if ($this->input->get('client_action') == 'preview_slider' && $this->input->get('only_markup') == 'true') {
            forceNoAdmin(true);
        }*/

        foreach ($this->plugin->getActivePlugins() as $plugin) {
            if (file_exists(FCPATH . WP_PLUGIN_DIR . $plugin)) {
                include FCPATH . WP_PLUGIN_DIR . $plugin;
            }
		}

		//forceNoAdmin(false);

        do_action('plugins_included_after');
    }

}