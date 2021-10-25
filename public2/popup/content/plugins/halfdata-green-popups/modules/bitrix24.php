<?php
/* Bitrix24 integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_bitrix24_class {
	var $default_parameters = array(
		"api-url" => "",
		"fields" => array('EMAIL' => '')
	);
	var $multiple = array('PHONE', 'EMAIL', 'WEB', 'IM');
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-bitrix24-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-bitrix24-fields', array(&$this, "admin_fields_html"));
		}
		add_filter('lepopup_integrations_do_bitrix24', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("bitrix24", $_providers)) $_providers["bitrix24"] = esc_html__('Bitrix24', 'lepopup');
		return $_providers;
	}
	
	function admin_settings_html() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options')) {
			if (array_key_exists('data', $_REQUEST)) {
				$data = json_decode(base64_decode(trim(stripslashes($_REQUEST['data']))), true);
				if (is_array($data)) $data = array_merge($this->default_parameters, $data);
				else $data = $this->default_parameters;
			} else $data = $this->default_parameters;
			$checkbox_id = $lepopup->random_string();
			$html = '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('REST call example URL', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Bitrix24 REST call example URL. In your Bitrix24 account go to Applications >> Webhooks and create Inbound webhook with CRM access permissions. Paste provided REST call example URL here.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-url" value="'.esc_html($data['api-url']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Bitrix24 fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['api-url'])) {
				$fields_data = $this->get_fields_html($data['api-url'], $data['fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="fields" data-deps="api-url"><i class="fas fa-download"></i><label>'.esc_html__('Load Fields', 'lepopup').'</label></a>
				</div>
			</div>';
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = $html;
			echo json_encode($return_object);
		}
		exit;
	}
	
	function admin_fields_html() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;
			if (!is_array($deps) || !array_key_exists('api-url', $deps) || empty($deps['api-url'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid REST call example URL.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_fields_html($deps['api-url'], $this->default_parameters['fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_url, $_fields) {
		global $wpdb, $lepopup;
		$url_path = parse_url($_url, PHP_URL_PATH);
		if ($url_path) {
			$url_path_parts = explode('/', trim($url_path, '/'));
			if (sizeof($url_path_parts) != 4 || $url_path_parts[0] != 'rest' || $url_path_parts[3] != 'profile' || !is_numeric($url_path_parts[1])) return array('status' => 'ERROR', 'message' => esc_html__('Bitrix24 REST call example URL must look like "https://<xxxxxxx>.bitrix24.ru/rest/<n>/<xxxxxxxxxxxxx>/profile/".', 'lepopup'));
		} else return array('status' => 'ERROR', 'message' => esc_html__('Invalid Bitrix24 REST call example URL.', 'lepopup'));
		
		$result = $this->connect($_url, 'crm.lead.fields');
		$fields_html = '';
		if (!empty($result) && is_array($result)) {
			if (array_key_exists('error_description', $result)) {
				return array('status' => 'ERROR', 'message' => $result['error_description']);
			} else {
				if (array_key_exists('result', $result) && sizeof($result['result']) > 0) {
					$fields_html = '
			<table>';
					foreach ($result['result'] as $id => $field) {
						if (is_array($field)) {
							if ((array_key_exists('title', $field) || array_key_exists('listLabel', $field)) && array_key_exists('isReadOnly', $field) && $field['isReadOnly'] === false) {
								$fields_html .= '
				<tr>
					<th>'.(array_key_exists('listLabel', $field) && !empty($field['listLabel']) ? esc_html($field['listLabel']) : esc_html($field['title'])).'</th>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="fields['.esc_html($id).']" value="'.esc_html(array_key_exists($id, $_fields) ? $_fields[$id] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<label class="lepopup-integrations-description">'.esc_html((array_key_exists('listLabel', $field) && !empty($field['listLabel']) ? $field['listLabel'] : $field['title']).' ('.$id.')').'</label>
					</td>
				</tr>';
							}
						}
					}
					$fields_html .= '
			</table>';
				} else {
					return array('status' => 'ERROR', 'message' => esc_html__('No fields found.', 'lepopup'));
				}
			}
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid REST call example URL.', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $fields_html);
	}

	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['api-url'])) return $_result;
		$url_path = parse_url($data['api-url'], PHP_URL_PATH);
		if ($url_path) {
			$url_path_parts = explode('/', trim($url_path, '/'));
			if (sizeof($url_path_parts) != 4 || $url_path_parts[0] != 'rest' || $url_path_parts[3] != 'profile' || !is_numeric($url_path_parts[1])) return $_result;
		} else return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;

		$post_data = array(
			'fields' => array(),
			'params' => array("REGISTER_SONET_EVENT" => "Y")
		);
		foreach($data['fields'] as $id => $value) {
			if (!empty($value)) {
				if (in_array($id, $this->multiple)) $post_data['fields'][$id] = array(array("VALUE" => $value, "VALUE_TYPE" => "HOME"));
				else $post_data['fields'][$id] = $value;
			}
		}

		if (array_key_exists('EMAIL', $data['fields']) && !empty($data['fields']['EMAIL']) && preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['EMAIL'])) {
			$result = $this->connect($data['api-url'], 'crm.lead.list/?filter[EMAIL]='.strtolower($data['fields']['EMAIL']));
		} else $result = null;
		if (!empty($result) && is_array($result) && array_key_exists('result', $result) && sizeof($result['result']) > 0) {
			$post_data['id'] = $result['result'][0]['ID'];
			$result = $this->connect($data['api-url'], 'crm.lead.update', $post_data);
		} else {
			$result = $this->connect($data['api-url'], 'crm.lead.add', $post_data);
		}
		
		return $_result;
	}
	
	function connect($_url, $_path, $_data = array(), $_method = '') {
		$url = rtrim(str_replace('/profile', '/', $_url), '/').'/'.ltrim($_path, '/');
		try {
			$curl = curl_init($url);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($_data));
			}
			if (!empty($_method)) {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_method);
			}
			curl_setopt($curl, CURLOPT_TIMEOUT, 10);
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
			$result = false;
		}
		return $result;
	}
}
$lepopup_bitrix24 = new lepopup_bitrix24_class();
?>