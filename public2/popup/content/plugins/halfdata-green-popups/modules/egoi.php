<?php
/* E-goi integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_egoi_class {
	var $default_parameters = array(
		"api-key" => "",
		"list" => "",
		"list-id" => "",
		"fields" => array(
			'email' => '',
			'cellphone' => '',
			'phone' => '',
			'first_name' => '',
			'last_name' => ''
		),
		"custom-fields" => array()
	);
	var $fields_meta;
	
	function __construct() {
		$this->fields_meta = array(
			'email' => array('title' => 'E-mail', 'description' => 'E-mail address of contact.'),
			'cellphone' => array('title' => 'Cell phone #', 'description' => 'Cell phone number of the contact.'),
			'phone' => array('title' => 'Phone #', 'description' => 'Phone number of the contact.'),
			'first_name' => array('title' => 'First name', 'description' => 'First name of the contact.'),
			'last_name' => array('title' => 'Last name', 'description' => 'Last name of the contact.')
		);
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-egoi-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-egoi-list', array(&$this, "admin_lists"));
			add_action('wp_ajax_lepopup-egoi-custom-fields', array(&$this, "admin_fields_html"));
		}
		add_filter('lepopup_integrations_do_egoi', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("egoi", $_providers)) $_providers["egoi"] = esc_html__('E-goi', 'lepopup');
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
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your E-goi API Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find your E-goi API Key %shere%s.', 'lepopup'), '<a href="https://bo.egoiapp.com/#/integrations" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('List ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select desired List ID.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-group lepopup-integrations-ajax-options">
						<input type="text" name="list" value="'.esc_html($data['list']).'" data-deps="api-key" readonly="readonly" />
						<input type="hidden" name="list-id" value="'.esc_html($data['list-id']).'" />
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to E-goi fields.', 'lepopup').'</div>
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
			if (!empty($data['api-key']) && !empty($data['list-id'])) {
				$fields_data = $this->get_fields_html($data['api-key'], $data['list-id'], $data['custom-fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="custom-fields" data-deps="api-key,list-id"><i class="fas fa-download"></i><label>'.esc_html__('Load Custom Fields', 'lepopup').'</label></a>
				</div>
			</div>';
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = $html;
			echo json_encode($return_object);
		}
		exit;
	}
	
	function admin_lists() {
		global $wpdb, $lepopup;
		$lists = array();
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

			$result = $this->connect($deps['api-key'], 'lists?offset=0&limit=100');
			if (is_array($result) && array_key_exists('total_items', $result)) {
				if (intval($result['total_items']) > 0) {
					foreach ($result['items'] as $list) {
						if (is_array($list)) {
							if (array_key_exists('list_id', $list) && array_key_exists('public_name', $list)) {
								$lists[$list['list_id']] = $list['public_name'];
							}
						}
					}
				} else {
					$return_object = array('status' => 'ERROR', 'message' => esc_html__('No lists found.', 'lepopup'));
					echo json_encode($return_object);
					exit;
				}
			} else {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Key.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (empty($lists)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('No lists found.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['items'] = $lists;
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
			if (!is_array($deps) || !array_key_exists('api-key', $deps) || empty($deps['api-key']) || !array_key_exists('list-id', $deps) || empty($deps['list-id'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Key or List ID.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_fields_html($deps['api-key'], $deps['list-id'], $this->default_parameters['custom-fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_key, $_list, $_fields) {
		global $wpdb, $lepopup;
		$result = $this->connect($_key, 'lists/'.urlencode($_list).'/fields?offset=0&limit=100');
		$fields_html = '';
		if (!empty($result) && is_array($result)) {
			if (array_key_exists('status', $result)) {
				return array('status' => 'ERROR', 'message' => $result['title']);
			} else {
				foreach ($result as $field) {
					if (is_array($field)) {
						if (array_key_exists('type', $field) && $field['type'] == 'extra' && array_key_exists('field_id', $field) && array_key_exists('name', $field)) {
							$fields_html .= '
				<tr>
					<th>'.esc_html($field['name']).'</th>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="custom-fields['.esc_html($field['field_id']).']" value="'.esc_html(array_key_exists($field['field_id'], $_fields) ? $_fields[$field['field_id']] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<label class="lepopup-integrations-description">'.esc_html($field['name']).'</label>
					</td>
				</tr>';
						}
					}
				}
				if (!empty($fields_html)) {
					$fields_html = '<table>'.$fields_html.'</table>';
				} else {
					return array('status' => 'ERROR', 'message' => esc_html__('No custom fields found.', 'lepopup'));
				}
			}
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid API Key.', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $fields_html);
	}

	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['api-key']) || empty($data['list-id'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;

		$result = null;
		if (!empty($data['fields']['email']) && preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) {
			$result = $this->connect($data['api-key'], 'contacts/search?type=email&contact='.$data['fields']['email']);
			if (!is_array($result) || empty($result) || !array_key_exists('items', $result) || empty($result['items'])) $result = null;
		}
		if (empty($result) && !empty($data['fields']['cellphone'])) {
			$result = $this->connect($data['api-key'], 'contacts/search?type=cellphone&contact='.$data['fields']['cellphone']);
			if (!is_array($result) || empty($result) || !array_key_exists('items', $result) || empty($result['items'])) $result = null;
		}
		if (empty($result) && !empty($data['fields']['phone'])) {
			$result = $this->connect($data['api-key'], 'contacts/search?type=phone&contact='.$data['fields']['phone']);
		}
		$output = array(
			'base' => array(
				'status' => 'active'
			),
			'extra' => array()
		);
		foreach ($data['fields'] as $key => $value) {
			if (!empty($value)) {
				$output['base'][$key] = $value;
			}
		}
		foreach ($data['custom-fields'] as $key => $value) {
			if (!empty($value)) {
				$output['extra'][] = array('field_id' => $key, 'value' => $value);
			}
		}
		
		if (!is_array($result) || empty($result) || !array_key_exists('items', $result) || empty($result['items'])) {
			$result = $this->connect($data['api-key'], 'lists/'.urlencode($data['list-id']).'/contacts', $output);
		} else {
			$result = $this->connect($data['api-key'], 'lists/'.urlencode($data['list-id']).'/contacts/'.$result['items'][0]['contact_id'], $output, 'PATCH');
		}
		return $_result;
	}
	
	function connect($_api_key, $_path, $_data = array(), $_method = '') {
		$headers = array(
			'Content-Type: application/json;charset=UTF-8',
			'Accept: application/json',
			'Apikey: '.$_api_key
		);
		try {
			$url = 'https://api.egoiapp.com/'.ltrim($_path, '/');
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
$lepopup_egoi = new lepopup_egoi_class();
?>