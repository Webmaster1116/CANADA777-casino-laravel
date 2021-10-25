<?php
if (!class_exists('halfdata_update_v1')) {
	class halfdata_update_v1 {
		var $slug;
		var $purchase_code;
		var $plugin_file;
		var $api_url = 'https://halfdata.com/update/';
		function __construct($_plugin_file, $_slug, $_purchase_code) {
			$this->slug = $_slug;
			$this->purchase_code = preg_replace('/[^a-zA-Z0-9-]/', '', $_purchase_code);
			$this->plugin_file = basename(dirname($_plugin_file)).'/'.basename($_plugin_file);
			if (is_admin()) {
				add_filter('pre_set_site_transient_update_plugins', array(&$this, 'check_for_plugin_update'));
				add_filter('plugins_api', array(&$this, 'plugin_api_call'), 10, 3);
			}
		}
		function check_for_plugin_update($_checked_data) {
			global $wp_version;
			
			if (empty($_checked_data->checked))
				return $_checked_data;
			if (!array_key_exists($this->plugin_file, (array)$_checked_data->checked))
				return $_checked_data;
			
			$request_string = array(
				'body' => array(
					'action' => 'basic_check', 
					'slug' => $this->slug,
					'version' => $_checked_data->checked[$this->plugin_file],
					'purchase-code' => $this->purchase_code,
					'website' => get_bloginfo('url')
				),
				'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo('url')
			);
			$raw_response = wp_remote_post($this->api_url, $request_string);
			
			if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)) {
				$response = json_decode($raw_response['body'], false);
			}
			if (!empty($response) && is_object($response)) {
				$response->icons = (array)$response->icons;
				$_checked_data->response[$this->plugin_file] = $response;
			}
			return $_checked_data;
		}
		function plugin_api_call($_res, $_action, $_args) {
			global $wp_version;

			if (!isset($_args->slug) || ($_args->slug != $this->slug))
				return $_res;
			
			$plugin_info = get_site_transient('update_plugins');
			$current_version = $plugin_info->checked[$this->plugin_file];
			$_args->version = $current_version;
			
			$request_string = array(
				'body' => array(
					'action' => $_action,
					'website' => get_bloginfo('url'),
					'purchase-code' => $this->purchase_code
				),
				'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo('url')
			);
			$request_string['body'] = array_merge($request_string['body'], (array)$_args);
			$request = wp_remote_post($this->api_url, $request_string);
			
			if (is_wp_error($request)) {
				$res = new WP_Error('plugins_api_failed', 'An Unexpected HTTP Error occurred during the API request.', $request->get_error_message());
			} else {
				$res = json_decode($request['body'], false);
				$res->sections = (array)$res->sections;
				if ($res === false) {
					$res = new WP_Error('plugins_api_failed', 'An unknown error occurred', $request['body']);
				}
			}
			return $res;
		}
	}
}
?>