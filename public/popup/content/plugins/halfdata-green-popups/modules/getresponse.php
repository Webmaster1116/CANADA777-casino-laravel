<?php
/* GetResponse integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_getresponse_class {
	var $default_parameters = array(
		"api-key" => "",
		"campaign" => "",
		"campaign-id" => "",
		"fields" => array('email' => '', 'name' => '')
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-getresponse-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-getresponse-campaign', array(&$this, "admin_campaigns"));
			add_action('wp_ajax_lepopup-getresponse-fields', array(&$this, "admin_fields_html"));
		}
		add_filter('lepopup_integrations_do_getresponse', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("getresponse", $_providers)) $_providers["getresponse"] = esc_html__('GetResponse', 'lepopup');
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
			$html = '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('API Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your GetResponse API Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
					<label class="lepopup-integrations-description">Find your GetResponse API Key <a href="https://app.getresponse.com/my_api_key.html" target="_blank">here</a>.</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Campaign ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select desired Campaign ID.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-group lepopup-integrations-ajax-options">
						<input type="text" name="campaign" value="'.esc_html($data['campaign']).'" data-deps="api-key" readonly="readonly" />
						<input type="hidden" name="campaign-id" value="'.esc_html($data['campaign-id']).'" />
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to GetResponse fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>
							<tr>
								<th>'.esc_html__('Email', 'lepopup').'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[email]" value="'.esc_html(array_key_exists('email', $data['fields']) ? $data['fields']['email'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Email Address', 'lepopup').'</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('Name', 'lepopup').'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[name]" value="'.esc_html(array_key_exists('name', $data['fields']) ? $data['fields']['name'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Full name', 'lepopup').'</label>
								</td>
							</tr>
						</table>
					</div>
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['api-key'])) {
				$fields_data = $this->get_fields_html($data['api-key'], $data['fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="fields" data-deps="api-key"><i class="fas fa-download"></i><label>'.esc_html__('Load Fields', 'lepopup').'</label></a>
				</div>
			</div>';
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = $html;
			echo json_encode($return_object);
		}
		exit;
	}
	
	function admin_campaigns() {
		global $wpdb, $lepopup;
		$campaigns = array();
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

			$result = $this->connect($deps['api-key'], 'campaigns?page=1&perPage=100');
			
			if (is_array($result) && !empty($result)) {
				if (array_key_exists('code', $result)) {
					$return_object = array('status' => 'ERROR', 'message' => esc_html($result['codeDescription']));
					echo json_encode($return_object);
					exit;
				} else {
					foreach ($result as $campaign) {
						if (is_array($campaign)) {
							if (array_key_exists('campaignId', $campaign) && array_key_exists('name', $campaign)) {
								$campaigns[$campaign['campaignId']] = $campaign['name'];
							}
						}
					}
				}
			} else {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Inavlid server response.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (empty($campaigns)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('No campaigns found.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['items'] = $campaigns;
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
			$return_object = $this->get_fields_html($deps['api-key'], $this->default_parameters['fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_key, $_fields) {
		global $wpdb, $lepopup;
		$result = $this->connect($_key, 'custom-fields?page=1&perPage=100');
		$fields_html = '';
		if (!empty($result) && is_array($result)) {
			if (array_key_exists('code', $result)) {
				return array('status' => 'ERROR', 'message' => esc_html($result['codeDescription']));
			} else {
				if (!empty($result)) {
					$fields_html = '
			<table>';
					foreach ($result as $field) {
						if (is_array($field)) {
							if (array_key_exists('customFieldId', $field) && array_key_exists('name', $field)) {
								$fields_html .= '
				<tr>
					<th>'.esc_html(ucfirst(str_replace('_', ' ', $field['name']))).'</td>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="fields['.esc_html($field['customFieldId']).']" value="'.esc_html(array_key_exists($field['customFieldId'], $_fields) ? $_fields[$field['customFieldId']] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<label class="lepopup-integrations-description">'.esc_html($field['name']).'</label>
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
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid server response..', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $fields_html);
	}

	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['api-key']) || empty($data['campaign-id'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$post_data = array(
			'campaign' => array('campaignId' => $data['campaign-id']),
			'name' => $data['fields']['name'],
			'email' => $data['fields']['email'],
			'dayOfCycle' => 0,
			'ipAddress' => $_SERVER['REMOTE_ADDR']
		);
		foreach ($data['fields'] as $key => $value) {
			if ($key != 'name' && $key != 'email' && !empty($value)) {
				$post_data['customFieldValues'][] = array('customFieldId' => $key, 'value' => array($value));
			}
		}
		$result = $this->connect($data['api-key'], 'contacts?query[email]='.$data['fields']['email']);
		if (empty($result)) {
			$result = $this->connect($data['api-key'], 'contacts', $post_data);
		} else {
			$contact_id = $result[0]['contactId'];
			$result = $this->connect($data['api-key'], 'contacts/'.$contact_id, $post_data);
		}
		return $_result;
	}
	
	function connect($_api_key, $_path, $_data = array(), $_method = '') {
		$headers = array(
			'X-Auth-Token: api-key '.$_api_key,
			'Content-Type: application/json;charset=UTF-8',
			'Accept: application/json'
		);
		try {
			$url = 'https://api.getresponse.com/v3/'.ltrim($_path, '/');
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($_data));
			}
			if (!empty($_method)) {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_method);
			}
			curl_setopt($curl, CURLOPT_TIMEOUT, 120);
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
$lepopup_getresponse = new lepopup_getresponse_class();
?>