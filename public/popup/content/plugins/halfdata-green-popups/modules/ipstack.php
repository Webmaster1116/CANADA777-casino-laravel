<?php
/* IPStack integration for Green Popups */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_ipstack_class {
	var $options = array(
		"api-key" => ""
	);
	
	function __construct() {
		$this->get_options();
		if (is_admin()) {
			add_filter('lepopup_geoip_services', array(&$this, 'geoip_services'), 10, 1);
			add_action('lepopup_geoip_service_options_show', array(&$this, "admin_options_html"), 10, 1);
			add_filter('lepopup_options_check', array(&$this, 'admin_options_check'));
			add_action('lepopup_options_update', array(&$this, 'admin_options_update'));
		}
		add_filter('lepopup_geoip_params_ipstack', array(&$this, 'geoip_params'), 10, 1);
		add_filter('lepopup_geoip_data_ipstack', array(&$this, 'geoip_data'), 10, 2);
	}

	function get_options() {
		foreach ($this->options as $key => $value) {
			$this->options[$key] = get_option('lepopup-ipstack-'.$key, $this->options[$key]);
		}
	}
	
	function update_options() {
		if (current_user_can('manage_options')) {
			foreach ($this->options as $key => $value) {
				update_option('lepopup-ipstack-'.$key, $value);
			}
		}
	}

	function populate_options() {
		foreach ($this->options as $key => $value) {
			if (isset($_POST['lepopup-ipstack-'.$key])) {
				$this->options[$key] = trim(stripslashes($_POST['lepopup-ipstack-'.$key]));
			}
		}
	}
	
	function geoip_services($_geoip_services) {
		if (!array_key_exists("ipstack", $_geoip_services)) $_geoip_services["ipstack"] = esc_html__('ipstack', 'lepopup');
		return $_geoip_services;
	}
	
	function admin_options_html($_active_service) {
		global $wpdb, $lepopup;
		echo '
				<tr class="lepopup-geoip-service-options lepopup-geoip-service-ipstack"'.($_active_service == 'ipstack' ? ' style="display: table-row;"' : '').'>
					<th>'.esc_html__('ipstack API Key', 'lepopup').':</th>
					<td>
						<input type="text" id="lepopup-ipstack-api-key" name="lepopup-ipstack-api-key" value="'.esc_html($this->options['api-key']).'" class="widefat" />
						<br /><em>'.sprintf(esc_html__('Please enter ipstack API Key. You can find it in the %sipstack dashboard%s.', 'lepopup'), '<a href="https://ipstack.com/dashboard" target="_blank">', '</a>').'</em>
					</td>
				</tr>';
	}

	function admin_options_check($_errors) {
		global $lepopup;
		$this->populate_options();
		if ($lepopup->options['geoip-service'] == 'ipstack') {
			if (empty($this->options['api-key'])) $_errors[] = esc_html__('Invalid ipstack API Key.', 'lepopup');
		}
		return $_errors;
	}

	function admin_options_update() {
		$this->populate_options();
		$this->update_options();
	}
	
	function geoip_params($_params) {
		return array('country', 'region', 'city', 'zip');
	}

	function geoip_data($_data, $_ip) {
		global $wpdb, $lepopup;
		$data = array('country' => '', 'region' => '', 'city' => '', 'zip' => '');
		$geo_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_geoip WHERE deleted = '0' AND ip = '".esc_sql($_ip)."' AND service = 'ipstack'", ARRAY_A);
		if (!empty($geo_details) && $geo_details['created']+7*24*3600 >= time()) {
			$data['country'] = $geo_details['country'];
			$data['region'] = $geo_details['region'];
			$data['city'] = $geo_details['city'];
			$data['zip'] = $geo_details['zip'];
			return $data;
		}
		$result = $this->connect($this->options['api-key'], $_ip);
		if (!empty($result) && is_array($result)) {
			if (array_key_exists('country_code', $result) && !empty($result['country_code'])) $data['country'] = $result['country_code'];
			if (array_key_exists('region_name', $result) && !empty($result['region_name'])) $data['region'] = $result['region_name'];
			if (array_key_exists('city', $result) && !empty($result['city'])) $data['city'] = $result['city'];
			if (array_key_exists('zip', $result) && !empty($result['zip'])) $data['zip'] = str_replace(" ", "", $result['zip']);
			if (!empty($data['country']) || !empty($data['region']) || !empty($data['city']) || !empty($data['zip'])) {
				if (!empty($geo_details)) {
					$sql = "UPDATE ".$wpdb->prefix."lepopup_geoip SET
						country = '".esc_sql($data['country'])."',
						region = '".esc_sql($data['region'])."',
						city = '".esc_sql($data['city'])."',
						zip = '".esc_sql($data['zip'])."',
						created = '".time()."'
						WHERE id = '".esc_sql($geo_details['id'])."'";
					$wpdb->query($sql);
				} else {
					$sql = "INSERT INTO ".$wpdb->prefix."lepopup_geoip (
						ip, country, region, city, zip, service, created, deleted) VALUES (
						'".esc_sql($_ip)."',
						'".esc_sql($data['country'])."',
						'".esc_sql($data['region'])."',
						'".esc_sql($data['city'])."',
						'".esc_sql($data['zip'])."',
						'ipstack', '".time()."', '0')";
					$wpdb->query($sql);
				}
			}
		}
		return $data;
	}

	function connect($_api_key, $_ip) {
		try {
			$url = 'http://api.ipstack.com/'.urlencode($_ip).'?access_key='.urlencode($_api_key);
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_TIMEOUT, 5);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
			//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($curl);
			curl_close($curl);
			$result = json_decode($response, true);
		} catch (Exception $e) {
			$result = null;
		}
		return $result;
	}
	
}
$lepopup_ipstack = new lepopup_ipstack_class();
?>