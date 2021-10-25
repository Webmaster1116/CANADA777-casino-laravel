<?php
/* ActiveTrail integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_activetrail_class {
	var $default_parameters = array(
		"api-key" => "",
		"groups" => array(),
		"fields" => array('email' => '')
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-activetrail-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-activetrail-fields', array(&$this, "admin_fields_html"));
			add_action('wp_ajax_lepopup-activetrail-groups', array(&$this, "admin_groups_html"));
		}
		add_filter('lepopup_integrations_do_activetrail', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("activetrail", $_providers)) $_providers["activetrail"] = esc_html__('ActiveTrail', 'lepopup');
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
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your ActiveTrail Access Token.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Create ActiveTrail Access Token on %sSettings%s page.', 'lepopup'), '<a href="https://app.activetrail.com/Members/Settings/ApiApps.aspx" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to ActiveTrail fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>
							<tr>
								<th>Email</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[email]" value="'.esc_html(array_key_exists('email', $data['fields']) ? $data['fields']['email'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Email Address', 'lepopup').'</label>
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
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Groups', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select groups.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['api-key'])) {
				$groups_data = $this->get_groups_html($data['api-key'], $data['groups']);
				if ($groups_data['status'] == 'OK') $html .= $groups_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="groups" data-deps="api-key,list-id"><i class="fas fa-download"></i><label>'.esc_html__('Load Groups', 'lepopup').'</label></a>
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
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid Access Token.', 'lepopup'));
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
		$result = $this->connect($_key, 'account/contactFields');
		$fields_html = '';
		if (!empty($result) && is_array($result)) {
			if (array_key_exists('Message', $result)) {
				return array('status' => 'ERROR', 'message' => $result['Message']);
			} else {
				if (sizeof($result) > 0) {
					$fields_html = '
			<table>';
					foreach ($result as $field) {
						if (is_array($field)) {
							if (array_key_exists('field_name', $field) && array_key_exists('field_display_name', $field)) {
								$field_name = strtolower($field['field_name']);
								$field_name = str_replace(array('firstname', 'lastname', 'zipcode'), array('first_name', 'last_name', 'zip_code'), $field_name);
								$fields_html .= '
				<tr>
					<th>'.esc_html($field['field_display_name']).'</td>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="fields['.esc_html($field_name).']" value="'.esc_html(array_key_exists($field_name, $_fields) ? $_fields[$field_name] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<label class="lepopup-integrations-description">'.esc_html($field['field_display_name']).'</label>
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

	function admin_groups_html() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;
			if (!is_array($deps) || !array_key_exists('api-key', $deps) || empty($deps['api-key'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid Access Token.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_groups_html($deps['api-key'], $this->default_parameters['groups']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_groups_html($_key, $_groups) {
		global $wpdb, $lepopup;
		$result = $this->connect($_key, 'groups?Page=1&Limit=100');
		$groups_html = '';
		if (!empty($result) && is_array($result)) {
			if (array_key_exists('Message', $result)) {
				return array('status' => 'ERROR', 'message' => $result['Message']);
			} else {
				if (sizeof($result) > 0) {
					$groups_html .= '<div style="margin-bottom: 10px;">';
					foreach ($result as $group) {
						if (array_key_exists($group['id'], $_groups)) $checked = $_groups[$group['id']];
						else $checked = 'off';
						$checkbox_id = $lepopup->random_string(16);
						$groups_html .= '
					<div class="lepopup-properties-pure" style="margin: 2px 0;">
						<input class="lepopup-checkbox lepopup-checkbox-classic lepopup-checkbox-medium" id="group-'.esc_html($checkbox_id).'" type="checkbox" value="on" name="groups['.esc_html($group['id']).']"'.($checked == 'on' ? ' checked="checked"' : '').' /><label for="group-'.esc_html($checkbox_id).'"></label> '.esc_html($group['name']).'
					</div>';
					}
					$groups_html .= '</div>';
				} else {
					return array('status' => 'ERROR', 'message' => esc_html__('No groups found.', 'lepopup'));
				}
			}
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid server response.', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $groups_html);
	}
	
	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['api-key'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$output = array(
			'subscribe_ip' => $_SERVER['REMOTE_ADDR'],
			'email' => $data['fields']['email'],
			'status' => 1
		);
		foreach ($data['fields'] as $key => $value) {
			if (!empty($value) && $key != 'email') {
				$output[$key] = $value;
			}
		}
		$result = $this->connect($data['api-key'], 'contacts?SearchTerm='.$data['fields']['email']);
		if (is_array($result)) {
			if (array_key_exists('Message', $result) && !array_key_exists('id', $result[0])) {
				$result = $this->connect($data['api-key'], 'contacts', $output);
				$contact_id = $result['id'];
			} else {
				$contact_id = $result[0]['id'];
				$result = $this->connect($data['api-key'], 'contacts/'.$contact_id, $output, 'PUT');
			}
		}
		foreach ($data['groups'] as $group_id => $checked) {
			if ($checked == 'on') {
				$result = $this->connect($data['api-key'], 'groups/'.$group_id.'/members', $output);
			}
		}
		return $_result;
	}
	
	function connect($_api_key, $_path, $_data = array(), $_method = '') {
		$headers = array(
			'Authorization: '.$_api_key,
			'Content-Type: application/json;charset=UTF-8',
			'Accept: application/json'
		);
		try {
			$url = 'https://webapi.mymarketing.co.il/api/'.ltrim($_path, '/');
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
$lepopup_activetrail = new lepopup_activetrail_class();
?>