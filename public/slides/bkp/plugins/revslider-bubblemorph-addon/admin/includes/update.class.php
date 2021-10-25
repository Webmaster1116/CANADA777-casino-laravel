<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RevAddOnBubblemorphUpdate {
	private $plugin_url			= 'http://codecanyon.net/item/slider-revolution-responsive-wordpress-plugin/2751380';
	private $remote_url			= 'http://updates.themepunch.tools/check_for_updates.php';
	private $remote_url_info	= 'http://updates.themepunch.tools/addons/revslider-bubblemorph-addon/revslider-bubblemorph-addon.php';
	private $plugin_slug		= 'revslider-bubblemorph-addon';
	private $plugin_path		= 'revslider-bubblemorph-addon/revslider-bubblemorph-addon.php';
	private $version;
	private $plugins;
	private $option;
	
	
	public function __construct($version) {
		$this->option = $this->plugin_slug . '_update_info';
		$this->version = $version;
		$this->_retrieve_version_info();
	}

	public function delete_update_transients() {
		delete_transient( 'update_themes' );
		delete_transient( 'update_plugins' );
		delete_site_transient( 'update_plugins' );
		delete_site_transient( 'update_themes' );
	}
	
	
	public function add_update_checks(){
		
		add_filter('pre_set_site_transient_update_plugins', array(&$this, 'set_update_transient'));
		add_filter('plugins_api', array(&$this, 'set_updates_api_results'), 10, 3);
		
	}
	
	
	public function set_update_transient($transient) {
	
		$this->_check_updates();

		if(isset($transient) && !isset($transient->response)) {
			$transient->response = array();
		}

		if(!empty($this->data->basic) && is_object($this->data->basic)) {
			if(version_compare($this->version, $this->data->basic->version, '<')) {

				$this->data->basic->new_version = $this->data->basic->version;
				$transient->response[$this->plugin_path] = $this->data->basic;
			}
		}
		
		return $transient;
	}
	
	
	public function set_updates_api_results($result, $action, $args) {
	
		$this->_check_updates();

		if(isset($args->slug) && $args->slug == $this->plugin_slug && $action == 'plugin_information') {
			if(is_object($this->data->full) && !empty($this->data->full)) {
				$result = $this->data->full;
			}
		}
		
		return $result;
	}


	protected function _check_updates() {
		//reset saved options
		//update_option($this->option, false);
		
		$force_check = false;
		
		if( (isset($_GET['checkforupdates']) && $_GET['checkforupdates'] == 'true') || isset($_GET["force-check"])) $force_check = true;
		

		// Get data
		if(empty($this->data)) {
			$data = get_option($this->option, false);
			$data = $data ? $data : new stdClass;

			$this->data = is_object($data) ? $data : maybe_unserialize($data);
		}
		
		$last_check = get_option('revslider_bubblemorph_addon-update-check');


		if($last_check == false){ //first time called
			$last_check = time();
			update_option('revslider_bubblemorph_addon-update-check', $last_check);
		}
		
		// Check for updates
		if(time() - $last_check > 172800 || $force_check == true){
			
			$data = $this->_retrieve_update_info();	

			if(isset($data->basic)) {
				update_option('revslider_bubblemorph_addon-update-check', time());
				
				$this->data->checked = time();
				$this->data->basic = $data->basic;
				$this->data->full = $data->full;
					
				//update_option('revslider_bubblemorph_addon-stable-version', $data->full->stable);
				update_option('revslider_bubblemorph_addon-latest-version', $data->full->version);
			}
			
		}

		// Save results
		update_option($this->option, $this->data);
	}


	public function _retrieve_update_info() {

		global $wp_version;
		$data = new stdClass;

		// Build request
		
		$validated = get_option('revslider_bubblemorph_addon-valid', 'false');
		$purchase = (get_option('revslider-valid', 'false') == 'true') ? get_option('revslider-code', '') : '';
		$rattr = array(
			'code' => urlencode($purchase),
			'version' => urlencode($this->version)
		);

		$request = wp_remote_post($this->remote_url_info, array(
			'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo('url'),
			'body' => $rattr
		));

		if(!is_wp_error($request)) {
			if($response = maybe_unserialize($request['body'])) {
				if(is_object($response)) {
					$data = $response;
					
					$data->basic->url = $this->plugin_url;
					$data->full->url = $this->plugin_url;
					$data->full->external = 1;
				}
			}
		}
		
		return $data;
	}
	
	
	public function _retrieve_version_info($force_check = false) {
		global $wp_version;
		
		$last_check = get_option('revslider-bubblemorph-addon-update-check-short');
		if($last_check == false){ //first time called
			$last_check = time();
			update_option('revslider-bubblemorph-addon-update-check-short', $last_check);
		}
				
		// Check for updates
		if(time() - $last_check > 172800 || $force_check == true){
			
			
			update_option('revslider-bubblemorph-addon-update-check-short', time());
			
			$purchase = (get_option('revslider-valid', 'false') == 'true') ? get_option('revslider-code', '') : '';
			
			
			$response = wp_remote_post($this->remote_url, array(
				'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo('url'),
				'body' => array(
					'item' => urlencode('revslider-bubblemorph-addon'),
					'version' => urlencode($this->version),
					'code' => urlencode($purchase)
				)
			));

			$response_code = wp_remote_retrieve_response_code( $response );
			$version_info = wp_remote_retrieve_body( $response );

			if ( $response_code != 200 || is_wp_error( $version_info ) ) {
				update_option('revslider_bubblemorph_addon-connection', false);
				return false;
			}else{
				update_option('revslider_bubblemorph_addon-connection', true);
			}
			
			/*
			$version_info = json_decode($version_info);
			if(isset($version_info->version)){
				update_option('revslider_bubblemorph_addon-latest-version', $version_info->version);
			}
			
			if(isset($version_info->notices)){
				update_option('revslider_bubblemorph_addon-notices', $version_info->notices);
			}
			
			if(isset($version_info->dashboard)){
				update_option('revslider_bubblemorph_addon-dashboard', $version_info->dashboard);
			}
			
			if(isset($version_info->deactivated) && $version_info->deactivated === true){
				if(get_option('revslider_bubblemorph_addon-valid', 'false') == 'true'){
					//remove validation, add notice
					update_option('revslider_bubblemorph_addon-valid', 'false');
					update_option('revslider_bubblemorph_addon-deact-notice', true);
				}
			}
			*/
		}
		
		if($force_check == true){ //force that the update will be directly searched
			update_option('revslider-bubblemorph-addon-update-check', '');
		}
		
	}
}
?>