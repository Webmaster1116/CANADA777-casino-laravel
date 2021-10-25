<?php
/* Salesflare integration for Green Popups */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_salesflare_class {
	var $default_parameters = array(
		"api-key" => "",
		"account" => "",
		"account-id" => "",
		"fields" => array(
			'email' => '',
			'prefix' => '',
			'firstname' => '',
			'middle' => '',
			'lastname' => '',
			'suffix' => '',
			'birth_date' => '',
			'phone_number' => '',
			'fax_number' => ''
		),
		"custom-fields" => array(),
		"tags" => ""
	);
	var $fields_meta;
	
	function __construct() {
		$this->fields_meta = array(
			'email' => array('title' => 'E-mail', 'description' => 'E-mail address of contact/recipient.'),
			'prefix' => array('title' => 'Prefix', 'description' => 'Prefix of the contact (Mr., Mrs, Miss, etc.).'),
			'firstname' => array('title' => 'First name', 'description' => 'First name of the contact.'),
			'middle' => array('title' => 'Middle name', 'description' => 'Middle name of the contact.'),
			'lastname' => array('title' => 'Last name', 'description' => 'Last name of the contact.'),
			'suffix' => array('title' => 'Suffix', 'description' => 'Suffix of the contact.'),
			'birth_date' => array('title' => 'Birthdate', 'description' => 'Date of birth of the contact.'),
			'phone_number' => array('title' => 'Phone #', 'description' => 'Phone number of the contact.'),
			'fax_number' => array('title' => 'Fax #', 'description' => 'Fax number of the contact.')
		);
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-salesflare-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-salesflare-custom-fields', array(&$this, "admin_fields_html"));
		}
		add_filter('lepopup_integrations_do_salesflare', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("salesflare", $_providers)) $_providers["salesflare"] = esc_html__('Salesflare', 'lepopup');
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
					<label>'.esc_html__('API Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Salesflare API Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find your Salesflare API Key %shere%s.', 'lepopup'), '<a href="https://app.salesflare.com/#/settings/apikeys" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Salesflare system properties.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>';
		foreach ($this->default_parameters['fields'] as $key => $value) {
			$html .= '
							<tr>
								<th>'.esc_html($this->fields_meta[$key]['title']).'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields['.$key.']" value="'.esc_html(array_key_exists($key, $data['fields']) ? $data['fields'][$key] : $value).'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html($this->fields_meta[$key]['description'].' ('.$key.')').'</label>
								</td>
							</tr>';
		}
		$html .= '				
						</table>
					</div>
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['api-key'])) {
				$fields_data = $this->get_fields_html($data['api-key'], $data['custom-fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="custom-fields" data-deps="api-key"><i class="fas fa-download"></i><label>'.esc_html__('Load Custom Fields', 'lepopup').'</label></a>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Tags', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Comma-separated list of tags.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="tags" value="'.esc_html($data['tags']).'" />
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
			if (!is_array($deps) || !array_key_exists('api-key', $deps) || empty($deps['api-key'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Key.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_fields_html($deps['api-key'], $this->default_parameters['custom-fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_key, $_fields) {
		global $wpdb, $lepopup;
		$result = $this->connect($_key, 'customfields/contacts');
		$fields_html = '';
		if (is_array($result)) {
			if (empty($result)) {
				return array('status' => 'ERROR', 'message' => esc_html__('No custom fields found.', 'lepopup'));
			} else if (array_key_exists('error', $result)) {
				return array('status' => 'ERROR', 'message' => $result['error']);
			} else {
				$fields_html = '
			<table>';
					foreach ($result as $field) {
						if (is_array($field)) {
							if (array_key_exists('api_field', $field) && array_key_exists('name', $field)) {
								$fields_html .= '
				<tr>
					<th>'.esc_html($field['name']).'</th>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="custom-fields['.esc_html($field['api_field']).']" value="'.esc_html(array_key_exists($field['api_field'], $_fields) ? $_fields[$field['api_field']] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<label class="lepopup-integrations-description">'.esc_html($field['name'].' ('.$field['api_field'].')').'</label>
					</td>
				</tr>';
							}
						}
					}
					$fields_html .= '
			</table>';
			}
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid API Key.', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $fields_html);
	}

	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['api-key'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$output = array(
			'email' => $data['fields']['email'],
			'custom' => array()
		);
		foreach ($data['fields'] as $key => $value) {
			if (!empty($value) && $key != 'email') {
				$output[$key] = $value;
			}
		}
		foreach ($data['custom-fields'] as $key => $value) {
			if (!empty($value)) {
				$output['custom'][$key] = $value;
			}
		}
		$tags_sanitized = array();
		$tags_raw = explode(',', $data['tags']);
		foreach ($tags_raw as $tag_raw) {
			$tag_raw = trim($tag_raw);
			if (!empty($tag_raw)) $tags_sanitized[] = $tag_raw;
		}
		if (!empty($tags_sanitized)) $output['tags'] = $tags_sanitized;
		
		$result = $this->connect($data['api-key'], 'contacts?email='.strtolower($data['fields']['email']));
		if (!empty($result) && is_array($result) && !array_key_exists('error', $result)) {
			$result = $this->connect($data['api-key'], 'contacts/'.$result[0]['id'], $output, 'PUT');
		} else {
			$result = $this->connect($data['api-key'], 'contacts', $output);
		}
		return $_result;
	}
	
	function connect($_api_key, $_path, $_data = array(), $_method = '') {
		$headers = array(
			'Content-Type: application/json;charset=UTF-8',
			'Accept: application/json',
			'Authorization: Bearer '.$_api_key
		);
		try {
			$url = 'https://api.salesflare.com/'.ltrim($_path, '/');
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($_data));
			}
			if (!empty($_method)) {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_method);
			}
			curl_setopt($curl, CURLOPT_TIMEOUT, 20);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
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
$lepopup_salesflare = new lepopup_salesflare_class();
?>