<?php
/* Intercom integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_intercom_class {
	var $default_parameters = array(
		"access-token" => "",
		"fields" => array('email' => '', 'phone' => '', 'name' => ''),
		"role" => "lead",
		"tags" => array()
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-intercom-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-intercom-fields', array(&$this, "admin_fields_html"));
			add_action('wp_ajax_lepopup-intercom-tags', array(&$this, "admin_tags_html"));
		}
		add_filter('lepopup_integrations_do_intercom', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("intercom", $_providers)) $_providers["intercom"] = esc_html__('Intercom', 'lepopup');
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
					<label>'.esc_html__('Access Token', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Intercom Access Token.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="access-token" value="'.esc_html($data['access-token']).'" />
					<label class="lepopup-integrations-description">'.esc_html__('Create new App for Internal integration at Intercom Dashboard >> Settings >> Developers >> Developer Hub.', 'lepopup').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Role', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('The role of the contact.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<select name="role">
						<option value="user"'.($data['role'] == 'user' ? ' selected="selected"' : '').'>'.esc_html__('User', 'lepopup').'</option>
						<option value="lead"'.($data['role'] != 'user' ? ' selected="selected"' : '').'>'.esc_html__('Lead', 'lepopup').'</option>
					</select>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Intercom fields.', 'lepopup').'</div>
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
									<label class="lepopup-integrations-description">'.esc_html__('Email address associated with the contact (lead)', 'lepopup').'</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('Name', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[name]" value="'.esc_html(array_key_exists('name', $data['fields']) ? $data['fields']['name'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Name of the contact (lead)', 'lepopup').'</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('Phone', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[phone]" value="'.esc_html(array_key_exists('phone', $data['fields']) ? $data['fields']['phone'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Phone number associated with the contact (lead)', 'lepopup').'</label>
								</td>
							</tr>
						</table>
					</div>
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['access-token'])) {
				$fields_data = $this->get_fields_html($data['access-token'], $data['fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="fields" data-deps="access-token"><i class="fas fa-download"></i><label>'.esc_html__('Load Fields', 'lepopup').'</label></a>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Tags', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select tags.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['access-token'])) {
				$tags_data = $this->get_tags_html($data['access-token'], $data['tags']);
				if ($tags_data['status'] == 'OK') $html .= $tags_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="tags" data-deps="access-token"><i class="fas fa-download"></i><label>'.esc_html__('Load Tags', 'lepopup').'</label></a>
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
			if (!is_array($deps) || !array_key_exists('access-token', $deps) || empty($deps['access-token'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid Access Token.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_fields_html($deps['access-token'], $this->default_parameters['fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_key, $_fields) {
		global $wpdb, $lepopup;
		$result = $this->connect($_key, 'data_attributes?model=contact');
		$fields_html = '';
		if (!empty($result) && is_array($result) && array_key_exists('type', $result) && in_array($result['type'], array('list', 'error.list'))) {
			if ($result['type'] == 'error.list') {
				return array('status' => 'ERROR', 'message' => $result['errors'][0]['message']);
			} else {
				$found = false;
				$fields_html = '
			<table>';
				foreach ($result['data'] as $field) {
					if (is_array($field)) {
						if (array_key_exists('name', $field) && array_key_exists('label', $field) && array_key_exists('api_writable', $field) && $field['api_writable'] && array_key_exists('custom', $field) && $field['custom'] && !in_array($field['name'], array('name', 'email', 'phone', 'external_id'))) {
							$found = true;
							$fields_html .= '
				<tr>
					<th>'.esc_html($field['label']).'</th>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="fields['.esc_html($field['name']).']" value="'.esc_html(array_key_exists($field['name'], $_fields) ? $_fields[$field['name']] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<label class="lepopup-integrations-description">'.(array_key_exists('description', $field) ? esc_html($field['description']) : esc_html($field['label'])).'</label>
					</td>
				</tr>';
						}
					}
				}
				if (!$found) {
					return array('status' => 'ERROR', 'message' => esc_html__('No custom fields found.', 'lepopup'));
				}
				
				$fields_html .= '
			</table>';
			}
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid server response.', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $fields_html);
	}

	function admin_tags_html() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;
			if (!is_array($deps) || !array_key_exists('access-token', $deps) || empty($deps['access-token'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid Access Token.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_tags_html($deps['access-token'], $this->default_parameters['tags']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_tags_html($_key, $_tags) {
		global $wpdb, $lepopup;
		$result = $this->connect($_key, 'tags');
		$tags_html = '';
		if (!empty($result) && is_array($result) && array_key_exists('type', $result) && in_array($result['type'], array('list', 'error.list'))) {
			if ($result['type'] == 'error.list') {
				return array('status' => 'ERROR', 'message' => $result['errors'][0]['message']);
			} else {
				if (array_key_exists('data', $result) && sizeof($result['data']) > 0) {
					$tags_html .= '
					<div class="lepopup-properties-pure" style="margin: 0 0 10px 0;">';
					foreach ($result['data'] as $tag) {
						if (array_key_exists($tag['id'], $_tags)) $checked = $_tags[$tag['id']];
						else $checked = 'off';
						$checkbox_id = $lepopup->random_string(16);
						$tags_html .= '
					<div class="lepopup-properties-pure" style="margin: 3px 0;">
						<input class="lepopup-checkbox lepopup-checkbox-classic lepopup-checkbox-medium" id="tag-'.esc_html($checkbox_id).'" type="checkbox" value="on" name="tags['.esc_html($tag['id']).']"'.($checked == 'on' ? ' checked="checked"' : '').' /><label for="tag-'.esc_html($checkbox_id).'"></label> '.esc_html($tag['name']).'
					</div>';
					}
					$tags_html .= '
					</div>';
				} else {
					return array('status' => 'ERROR', 'message' => esc_html__('No tags found.', 'lepopup'));
				}
			}
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid server response.', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $tags_html);
	}

	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['access-token'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$post_data = array(
			'query' => array(
				'field' => 'email',
				'operator' => '=',
				'value' => $data['fields']['email']
			)
		);
		$result = $this->connect($data['access-token'], 'contacts/search', $post_data);
		
		$post_data = array(
			'role' => $data['role'],
			'email' => $data['fields']['email'],
			'last_seen_ip' => $_SERVER['REMOTE_ADDR'],
			'custom_attributes' => array()
		);
		if (!empty($data['fields']['name'])) $post_data['name'] = $data['fields']['name'];
		if (!empty($data['fields']['phone'])) $post_data['phone'] = $data['fields']['phone'];
		foreach ($data['fields'] as $key => $value) {
			if (!empty($value) && !in_array($key, array('email', 'name', 'phone'))) {
				$post_data['custom_attributes'][$key] = $value;
			}
		}
		$contact_id = null;
		if (!empty($result) && is_array($result) && array_key_exists('type', $result) && $result['type'] == 'list' && !empty($result['data'])) {
			$contact_id = $result['data'][0]['id'];
			$result = $this->connect($data['access-token'], 'contacts/'.$result['data'][0]['id'], $post_data, 'PUT');
		} else {
			$result = $this->connect($data['access-token'], 'contacts', $post_data);
			if (is_array($result) && array_key_exists('id', $result)) $contact_id = $result['id'];
		}
		if (!empty($contact_id)) {
			foreach ((array)$data['tags'] as $tag => $value) {
				$result = $this->connect($data['access-token'], 'contacts/'.$contact_id.'/tags', array('id' => $tag));
			}
		}

		return $_result;
	}
	
	function connect($_access_token, $_path, $_data = array(), $_method = '') {
		$headers = array(
			'Authorization: Bearer '.$_access_token,
			'Content-Type: application/json;charset=UTF-8',
			'Accept: application/json'
		);
		try {
			$url = 'https://api.intercom.io/'.ltrim($_path, '/');
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
$lepopup_intercom = new lepopup_intercom_class();
?>