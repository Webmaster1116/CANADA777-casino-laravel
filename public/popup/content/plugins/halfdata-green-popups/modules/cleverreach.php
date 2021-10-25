<?php
/* CleverReach integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_cleverreach_class {
	var $default_parameters = array(
		"client-id" => "",
		"client-secret" => "",
		"list" => "",
		"list-id" => "",
		"email" => "",
		"fields" => array(),
		"global-fields" => array(),
		"tags" => ""
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-cleverreach-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-cleverreach-list', array(&$this, "admin_lists"));
			add_action('wp_ajax_lepopup-cleverreach-fields', array(&$this, "admin_fields_html"));
		}
		add_filter('lepopup_integrations_do_cleverreach', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("cleverreach", $_providers)) $_providers["cleverreach"] = esc_html__('CleverReach', 'lepopup');
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
					<label>'.esc_html__('Client ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter Client ID of your OAuth App. Please go to CleverReach account >> My Account >> Extras >> REST API and click "Create OAuth". After that click created app and find Client ID there.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="client-id" value="'.esc_html($data['client-id']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Client Secret', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter Client Secret of your OAuth App. Find it the same way as Client ID.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="client-secret" value="'.esc_html($data['client-secret']).'" />
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
						<input type="text" name="list" value="'.esc_html($data['list']).'" data-deps="client-id,client-secret" readonly="readonly" />
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
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to CleverReach fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>
							<tr>
								<th>'.esc_html__('Email', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="email" value="'.esc_html($data['email']).'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Email Address', 'lepopup').'</label>
								</td>
							</tr>
						</table>
					</div>
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['client-id']) && !empty($data['client-secret']) && !empty($data['list-id'])) {
				$fields_data = $this->get_fields_html($data['client-id'], $data['client-secret'], $data['list-id'], $data['fields'], $data['global-fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="fields" data-deps="client-id,client-secret,list-id"><i class="fas fa-download"></i><label>'.esc_html__('Load Fields', 'lepopup').'</label></a>
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
	
	function admin_lists() {
		global $wpdb, $lepopup;
		$lists = array();
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;

			if (!is_array($deps) || !array_key_exists('client-id', $deps) || empty($deps['client-id']) || !array_key_exists('client-secret', $deps) || empty($deps['client-secret'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid OAuth Credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$token = $this->_get_token($deps['client-id'], $deps['client-secret']);
			if (!empty($token) && is_array($token)) {
				if (array_key_exists('access_token', $token)) {
					$result = $this->_connect($token['access_token'], 'groups.json');
					if (is_array($result)) {
						if (array_key_exists('error', $result)) {
							$return_object = array('status' => 'ERROR', 'message' => esc_html($result['error']['message']));
							echo json_encode($return_object);
							exit;
						} else if (sizeof($result) == 0) {
							$return_object = array('status' => 'ERROR', 'message' => esc_html__('Lists not found.', 'lepopup'));
							echo json_encode($return_object);
							exit;
						} else {
							foreach($result as $list) {
								if (is_array($list)) {
									if (array_key_exists('id', $list) && array_key_exists('name', $list)) {
										$lists[$list['id']] = $list['name'];
									}
								}
							}
						}
					} else {
						$return_object = array('status' => 'ERROR', 'message' => esc_html__('Unexpected server response.', 'lepopup'));
						echo json_encode($return_object);
						exit;
					}
				} else if (array_key_exists('error_description', $token)) {
					$return_object = array('status' => 'ERROR', 'message' => esc_html($token['error_description']));
					echo json_encode($return_object);
					exit;
				} else {
					$return_object = array('status' => 'ERROR', 'message' => esc_html__('Unexpected server response.', 'lepopup'));
					echo json_encode($return_object);
					exit;
				}
			} else {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Unexpected server response.', 'lepopup'));
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
			if (!is_array($deps) || !array_key_exists('client-id', $deps) || empty($deps['client-secret']) || !array_key_exists('client-secret', $deps) || empty($deps['client-id']) || !array_key_exists('list-id', $deps) || empty($deps['list-id'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid OAuth Credentials or List ID.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_fields_html($deps['client-id'], $deps['client-secret'], $deps['list-id'], $this->default_parameters['fields'], $this->default_parameters['global-fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_client_id, $_client_secret, $_list_id, $_fields, $_global_fields) {
		global $wpdb, $lepopup;
		$fields_html = '';
		$token = $this->_get_token($_client_id, $_client_secret);
		if (!empty($token) && is_array($token)) {
			if (array_key_exists('access_token', $token)) {
				$result_local = $this->_connect($token['access_token'], 'attributes.json?group_id='.$_list_id);
				$result_global = $this->_connect($token['access_token'], 'attributes.json');
				if (is_array($result_local)) {
					if (array_key_exists('error', $result_local)) {
						return array('status' => 'ERROR', 'message' => esc_html($result_local['error']['message']));
					} else {
						$result = array_merge($result_local, $result_global);
						if (sizeof($result) == 0) {
							return array('status' => 'ERROR', 'message' => esc_html__('No fields found.', 'lepopup'));
						} else {
							$fields_html = '
			<table>';
							foreach ($result_local as $field) {
								if (is_array($field)) {
									if (array_key_exists('name', $field) && array_key_exists('description', $field)) {
										$fields_html .= '
				<tr>
					<th>'.esc_html($field['description']).'</td>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="fields['.esc_html($field['name']).']" value="'.esc_html(array_key_exists($field['name'], $_fields) ? $_fields[$field['name']] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<label class="lepopup-integrations-description">'.esc_html($field['description'].' ('.$field['name'].')').'</label>
					</td>
				</tr>';
									}
								}
							}
							foreach ($result_global as $field) {
								if (is_array($field)) {
									if (array_key_exists('name', $field) && array_key_exists('description', $field)) {
										$fields_html .= '
				<tr>
					<th>'.esc_html($field['description']).'</td>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="global-fields['.esc_html($field['name']).']" value="'.esc_html(array_key_exists($field['name'], $_global_fields) ? $_global_fields[$field['name']] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<label class="lepopup-integrations-description">'.esc_html($field['description'].' ('.$field['name'].')').'</label>
					</td>
				</tr>';
									}
								}
							}
							$fields_html .= '
			</table>';
							
						}
					}
				} else {
					return array('status' => 'ERROR', 'message' => esc_html__('Unexpected server response.', 'lepopup'));
				}
			} else if (array_key_exists('error_description', $token)) {
				return array('status' => 'ERROR', 'message' => esc_html($token['error_description']));
			} else {
				return array('status' => 'ERROR', 'message' => esc_html__('Unexpected server response.', 'lepopup'));
			}
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Unexpected server response.', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $fields_html);
	}

	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['client-id']) || empty($data['client-secret']) || empty($data['list-id'])) return $_result;
		if (empty($data['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['email'])) return $_result;

		$post_data = array(
			"email" => $data['email'],
			"registered" => time(),
			"activated" => time(),
			"source" => 'Green Forms'
		);
		if (!empty($data['fields']) && is_array($data['fields'])) {
			foreach ($data['fields'] as $key => $value) {
				if (!empty($value)) {
					$post_data['attributes'][$key] = $value;
				}
			}
		}
		if (!empty($data['global-fields']) && is_array($data['global-fields'])) {
			foreach ($data['global-fields'] as $key => $value) {
				if (!empty($value)) {
					$post_data['global_attributes'][$key] = $value;
				}
			}
		}
		$tags_sanitized = array();
		$tags_raw = explode(',', $data['tags']);
		foreach ($tags_raw as $tag_raw) {
			$tag_raw = trim($tag_raw);
			if (!empty($tag_raw)) $tags_sanitized[] = $tag_raw;
		}
		if (!empty($tags_sanitized)) $post_data["tags"] = $tags_sanitized;
		
		$token = $this->_get_token($data['client-id'], $data['client-secret']);
		if (!empty($token) && is_array($token) && array_key_exists('access_token', $token)) {
			$result = $this->_connect($token['access_token'], 'groups.json/'.$data['list-id'].'/receivers/upsert', array($post_data));
		}
		return $_result;
	}
	
	function _get_token($_client_id, $_client_secret) {
		try {
			$curl = curl_init('https://rest.cleverreach.com/oauth/token.php');
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, $_client_id.':'.$_client_secret);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, array("grant_type" => "client_credentials"));
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

	function _connect($_access_token, $_path, $_data = array(), $_method = '') {
		$url = 'https://rest.cleverreach.com/v3/'.ltrim($_path, '/');
		if (strpos($url, '?') === false) $url .= '?token='.$_access_token;
		else $url .= '&token='.$_access_token;
		$headers = array(
			'Accept: application/json',
			'Content-Type: application/json'
		);
		try {
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array('postdata' => $_data)));
			}
			if (!empty($_method)) {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_method);
			}
			curl_setopt($curl, CURLOPT_TIMEOUT, 20);
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
$lepopup_cleverreach = new lepopup_cleverreach_class();
?>