<?php
/* Geolocation IP Detection plugin integration for Green Popups */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_geoipdetect_class {
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_geoip_services', array(&$this, 'geoip_services'), 10, 1);
		}
		add_filter('lepopup_geoip_params_geoipdetect', array(&$this, 'geoip_params'), 10, 1);
		add_filter('lepopup_geoip_data_geoipdetect', array(&$this, 'geoip_data'), 10, 2);
	}

	function geoip_services($_geoip_services) {
		if (function_exists('geoip_detect2_get_info_from_ip')) {
			if (!array_key_exists("geoipdetect", $_geoip_services)) $_geoip_services["geoipdetect"] = esc_html__('Geolocation IP Detection plugin', 'lepopup');
		}
		return $_geoip_services;
	}
	
	function geoip_params($_params) {
		return array('country');
	}

	function geoip_data($_data, $_ip) {
		global $wpdb, $lepopup;
		$data = array('country' => '');
		$geo_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_geoip WHERE deleted = '0' AND ip = '".esc_sql($_ip)."' AND service = 'geoipdetect'", ARRAY_A);
		if (!empty($geo_details) && $geo_details['created']+7*24*3600 >= time()) {
			$data['country'] = $geo_details['country'];
			return $data;
		}
		if (function_exists('geoip_detect2_get_info_from_ip')) {
			$result = geoip_detect2_get_info_from_ip($_ip);
			if (!empty($result) && is_object($result)) {
				if (property_exists($result, 'country') && is_object($result->country) && !empty($result->country) && !empty($result->country->isoCode)) $data['country'] = $result->country->isoCode;
				if (!empty($data['country'])) {
					if (!empty($geo_details)) {
						$sql = "UPDATE ".$wpdb->prefix."lepopup_geoip SET
							country = '".esc_sql($data['country'])."'
							created = '".time()."'
							WHERE id = '".esc_sql($geo_details['id'])."'";
						$wpdb->query($sql);
					} else {
						$sql = "INSERT INTO ".$wpdb->prefix."lepopup_geoip (
							ip, country, region, city, zip, service, created, deleted) VALUES (
							'".esc_sql($_ip)."',
							'".esc_sql($data['country'])."',
							'', '', '', 'geoipdetect', '".time()."', '0')";
						$wpdb->query($sql);
					}
				}
			}
		}
		return $data;
	}
}
$lepopup_geoipdetect = new lepopup_geoipdetect_class();
?>