<?php
/* Pipedrive integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_pipedrive_class {
	var $default_parameters = array(
		"api-key" => "",
		"list" => "",
		"list-id" => "",
		"groups" => array(),
		"fields" => array('email' => '', 'name' => '', 'phone' => ''),
		"visible" => "1"
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-pipedrive-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-pipedrive-list', array(&$this, "admin_lists"));
			add_action('wp_ajax_lepopup-pipedrive-fields', array(&$this, "admin_fields_html"));
		}
		add_filter('lepopup_integrations_do_pipedrive', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("pipedrive", $_providers)) $_providers["pipedrive"] = esc_html__('Pipedrive', 'lepopup');
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
					<label>'.esc_html__('API Token', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Pipedrive API Token.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
					<label class="lepopup-integrations-description">'.esc_html__('Find API Token in your account on Settings page.', 'lepopup').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Organization', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select the ID of the organization the contact will belong to.', 'lepopup').'</div>
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
					<div class="lepopup-tooltip-content">'.esc_html__('Map popup fields to Pipedrive fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>
							<tr>
								<th>'.esc_html__('Email', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[email]" value="'.esc_html(array_key_exists('email', $data['fields']) ? $data['fields']['email'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Email address associated with the contact', 'lepopup').'</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('Name', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[name]" value="'.esc_html(array_key_exists('name', $data['fields']) ? $data['fields']['name'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Name of the contact', 'lepopup').'</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('Phone', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[phone]" value="'.esc_html(array_key_exists('phone', $data['fields']) ? $data['fields']['phone'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Phone number associated with the contact', 'lepopup').'</label>
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
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="fields" data-deps="api-key,list-id"><i class="fas fa-download"></i><label>'.esc_html__('Load Custom Fields', 'lepopup').'</label></a>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Visibility', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Visibility of the contact.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<select name="visible">
						<option value="1"'.($data['visible'] == 1 ? ' selected="selected"' : '').'>'.esc_html__('Owner & followers (private)', 'lepopup').'</option>
						<option value="3"'.($data['visible'] != 1 ? ' selected="selected"' : '').'>'.esc_html__('Entire company (shared)', 'lepopup').'</option>
					</select>
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
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Token.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}

			$result = $this->connect($deps['api-key'], 'organizations?start=0&limit=1000');
			if (is_array($result) && array_key_exists('success', $result)) {
				if ($result['success']) {
					if (!empty($result['data']) && is_array($result['data'])) {
						foreach ($result['data'] as $list) {
							if (is_array($list)) {
								if (array_key_exists('id', $list) && array_key_exists('name', $list)) {
									$lists[$list['id']] = $list['name'];
								}
							}
						}
					} else {
						$return_object = array('status' => 'ERROR', 'message' => esc_html__('No organizations found.', 'lepopup'));
						echo json_encode($return_object);
						exit;
					}
				} else {
					$return_object = array('status' => 'ERROR', 'message' => (array_key_exists('error', $result) ? esc_html(ucfirst($result['error'])) : esc_html__('Invalid server response.', 'lepopup')));
					echo json_encode($return_object);
					exit;
				}
			} else {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid server response.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (empty($lists)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('No organizations found.', 'lepopup'));
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
			if (!is_array($deps) || !array_key_exists('api-key', $deps) || empty($deps['api-key'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Token.', 'lepopup'));
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
		$result = $this->connect($_key, 'personFields');
		$fields_html = '';
		if (is_array($result) && array_key_exists('success', $result)) {
			if (!$result['success']) {
				return array('status' => 'ERROR', 'message' => (array_key_exists('error', $result) ? esc_html(ucfirst($result['error'])) : esc_html__('Invalid server response.', 'lepopup')));
			} else {
				if (!empty($result['data']) && is_array($result['data'])) {	
					$fields_html = '
			<table>';
					$found = false;
					foreach ($result['data'] as $field) {
						if (is_array($field)) {
							if (array_key_exists('key', $field) && array_key_exists('name', $field) && $field['edit_flag']) {
								$found = true;
								$fields_html .= '
				<tr>
					<th>'.esc_html($field['name']).'</th>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="fields['.esc_html($field['key']).']" value="'.esc_html(array_key_exists($field['key'], $_fields) ? $_fields[$field['key']] : '').'" class="widefat" />
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
					if (!$found) {
						return array('status' => 'ERROR', 'message' => esc_html__('No custom fields found.', 'lepopup'));
					}
				} else {
					return array('status' => 'ERROR', 'message' => esc_html__('No custom fields found.', 'lepopup'));
				}
			}
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid server response.', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $fields_html);
	}

	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['api-key']) || empty($data['list-id'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['name'])) return $_result;
		
		$output = array(
			'name' => $data['fields']['name'],
			'org_id' => $data['list-id'],
			'visible_to' => $data['visible']
		);
		if (!empty($data['fields']['email']) && preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) $output['email'] = $data['fields']['email'];
		if (!empty($data['fields']['phone'])) $output['phone'] = $data['fields']['phone'];
		foreach ($data['fields'] as $key => $value) {
			if (!empty($value) && !in_array($key, array('name', 'email', 'phone'))) {
				$output[$key] = $value;
			}
		}

		$method = 'POST';
		$person_id = null;
		if (!empty($data['fields']['email'])) {
			$result = $this->connect($data['api-key'], 'persons/find?term='.urlencode($data['fields']['email']).'&start=0&limit=1'.(!empty($data['list-id']) ? '&org_id='.$data['list-id'] : '').'&search_by_email=1');
			if (array_key_exists('success', $result) && $result['success']) {
				if (!empty($result['data'])) {
					$person_id = $result['data'][0]['id'];
					$method = 'PUT';
				}
			}
		}
		$result = $this->connect($data['api-key'], 'persons'.(!empty($person_id) ? '/'.$person_id : ''), $output, $method);
		
		return $_result;
	}
	
	function connect($_api_key, $_path, $_data = array(), $_method = '') {
		$headers = array(
			'Content-Type: application/json;charset=UTF-8',
			'Accept: application/json'
		);
		try {
			$url = 'https://api.pipedrive.com/v1/'.ltrim($_path, '/');
			if (strpos($_path, '?') !== false) $url .= '&api_token='.$_api_key;
			else $url .= '?api_token='.$_api_key;
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($_data));
			}
			if (!empty($_method)) {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_method);
			}
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
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
$lepopup_pipedrive = new lepopup_pipedrive_class();
?>