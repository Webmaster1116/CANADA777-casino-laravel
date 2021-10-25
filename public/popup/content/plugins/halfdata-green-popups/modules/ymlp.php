<?php
/* Your Mailing List Provider integration for Green Popups */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_ymlp_class {
	var $default_parameters = array(
		"api-key" => "",
		"username" => "",
		"list" => "",
		"list-id" => "",
		"fields" => array('EMAIL' => '')
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-ymlp-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-ymlp-list', array(&$this, "admin_lists"));
			add_action('wp_ajax_lepopup-ymlp-fields', array(&$this, "admin_fields_html"));
		}
		add_filter('lepopup_integrations_do_ymlp', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("ymlp", $_providers)) $_providers["ymlp"] = esc_html__('Your Mailing List Provider', 'lepopup');
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
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Your Mailing List Provider API Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
					<label class="lepopup-integrations-description">'.esc_html__('Find API Key in Your Account >> Configuration >> API.', 'lepopup').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Username', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Your Mailing List Provider Username.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="username" value="'.esc_html($data['username']).'" />
					<label class="lepopup-integrations-description">'.esc_html__('Find Username in Your Account >> Account >> Login Credentials.', 'lepopup').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Group ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select desired group.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-group lepopup-integrations-ajax-options">
						<input type="text" name="list" value="'.esc_html($data['list']).'" data-deps="api-key,username" readonly="readonly" />
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
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Your Mailing List Provider fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>
							<tr>
								<th>'.esc_html__('Email', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[EMAIL]" value="'.esc_html(array_key_exists('EMAIL', $data['fields']) ? $data['fields']['EMAIL'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Email Address', 'lepopup').'</label>
								</td>
							</tr>
						</table>
					</div>
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['api-key']) && !empty($data['username'])) {
				$fields_data = $this->get_fields_html($data['api-key'], $data['username'], $data['fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="fields" data-deps="api-key,username"><i class="fas fa-download"></i><label>'.esc_html__('Load Fields', 'lepopup').'</label></a>
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

			if (!is_array($deps) || !array_key_exists('api-key', $deps) || empty($deps['api-key']) || !array_key_exists('username', $deps) || empty($deps['username'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}

			$result = $this->connect($deps['api-key'], $deps['username'], 'Groups.GetList');
			if (is_array($result)) {
				if (array_key_exists('Output', $result)) {
					$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API credentials.', 'lepopup'));
					echo json_encode($return_object);
					exit;
				}
				if (sizeof($result) > 0) {
					foreach ($result as $list) {
						if (is_array($list)) {
							if (array_key_exists('ID', $list) && array_key_exists('GroupName', $list)) {
								$lists[$list['ID']] = $list['GroupName'];
							}
						}
					}
				} else {
					$return_object = array('status' => 'ERROR', 'message' => esc_html__('No lists found.', 'lepopup'));
					echo json_encode($return_object);
					exit;
				}
			} else {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid server response.', 'lepopup'));
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
			if (!is_array($deps) || !array_key_exists('api-key', $deps) || empty($deps['api-key']) || !array_key_exists('username', $deps) || empty($deps['username'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_fields_html($deps['api-key'], $deps['username'], $this->default_parameters['fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_key, $_username, $_fields) {
		global $wpdb, $lepopup;
		$result = $this->connect($_key, $_username, 'Fields.GetList');
		$fields_html = '';
		if (is_array($result)) {
			if (array_key_exists('Output', $result)) {
				return array('status' => 'ERROR', 'message' => esc_html__('Invalid API credentials.', 'lepopup'));
			} else {
				if (sizeof($result) > 0) {
					$fields_html = '
			<table>';
					foreach ($result as $field) {
						if (is_array($field)) {
							if (array_key_exists('ID', $field) && array_key_exists('FieldName', $field) && $field['Alias'] != 'EMAIL') {
								$fields_html .= '
				<tr>
					<th>'.esc_html($field['FieldName']).'</th>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="fields['.esc_html($field['ID']).']" value="'.esc_html(array_key_exists($field['ID'], $_fields) ? $_fields[$field['ID']] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<label class="lepopup-integrations-description">'.esc_html($field['FieldName']).'</label>
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
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid server response.', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $fields_html);
	}

	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['api-key']) || empty($data['username']) || empty($data['list-id'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['EMAIL']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['EMAIL'])) return $_result;

		$post_data = array(
			'Email' => $data['fields']['EMAIL'],
			'GroupID' => $data['list-id'],
			'OverruleUnsubscribedBounced' => '1'
		);
		foreach ($data['fields'] as $key => $value) {
			if (!empty($value) && $key != 'EMAIL') {
				$post_data['Field'.$key] = $value;
			}
		}
		$result = $this->connect($data['api-key'], $data['username'], 'Contacts.Add', $post_data);
		return $_result;
	}
	
	function connect($_api_key, $_username, $_path, $_data = array(), $_method = '') {
		$data = array(
			'Key' => $_api_key,
			'Username' => $_username,
			'Output' => 'JSON'
		);
		$data = array_merge($data, $_data);
		try {
			$url = 'https://www.ymlp.com/api/'.ltrim($_path, '/');
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
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
$lepopup_ymlp = new lepopup_ymlp_class();
?>