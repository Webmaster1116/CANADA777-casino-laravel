<?php
/* Moosend integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_moosend_class {
	var $default_parameters = array(
		"api-key" => "",
		"list" => "",
		"list-id" => "",
		'email' => "",
		'name' => "",
		"fields" => array(),
		"fieldnames" => array()
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-moosend-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-moosend-list', array(&$this, "admin_lists"));
			add_action('wp_ajax_lepopup-moosend-fields', array(&$this, "admin_fields_html"));
		}
		add_filter('lepopup_integrations_do_moosend', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("moosend", $_providers)) $_providers["moosend"] = esc_html__('Moosend', 'lepopup');
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
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Moosend API Key. You can get it on the settings page in your Moosend account.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
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
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Moosend fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>
							<tr>
								<th>'.esc_html__('Email', 'lepopup').'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="email" value="'.esc_html($data['email']).'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Email of the contact.', 'lepopup').'</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('Name', 'lepopup').'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="name" value="'.esc_html($data['name']).'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Name of the contact.', 'lepopup').'</label>
								</td>
							</tr>
						</table>
					</div>
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['api-key']) && !empty($data['list-id'])) {
				$fields_data = $this->get_fields_html($data['api-key'], $data['list-id'], $data['fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="fields" data-deps="api-key,list-id"><i class="fas fa-download"></i><label>'.esc_html__('Load Fields', 'lepopup').'</label></a>
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

			$result = $this->connect($deps['api-key'], 'lists/1/100.json');
			if (is_array($result) && array_key_exists('Error', $result) && is_null($result['Error']) && array_key_exists('Context', $result)) {
				if (is_array($result['Context']) && array_key_exists('MailingLists', $result['Context']) && sizeof($result['Context']['MailingLists'])) {
					foreach ($result['Context']['MailingLists'] as $list) {
						if (is_array($list)) {
							if (array_key_exists('ID', $list) && array_key_exists('Name', $list)) {
								$lists[$list['ID']] = $list['Name'];
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
			$return_object = $this->get_fields_html($deps['api-key'], $deps['list-id'], $this->default_parameters['fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_key, $_list, $_fields) {
		global $wpdb, $lepopup;
		$result = $this->connect($_key, 'lists/'.urlencode($_list).'/details.json');
		$fields_html = '';
		if (is_array($result) && array_key_exists('Error', $result) && is_null($result['Error']) && array_key_exists('Context', $result)) {
			if (array_key_exists('CustomFieldsDefinition', $result['Context']) && sizeof($result['Context']['CustomFieldsDefinition']) > 0) {
				$fields_html = '
			<table>';
				foreach ($result['Context']['CustomFieldsDefinition'] as $field) {
					if (is_array($field)) {
						if (array_key_exists('ID', $field) && array_key_exists('Name', $field)) {
							$fields_html .= '
				<tr>
					<th>'.esc_html($field['Name']).'</th>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="fields['.esc_html($field['ID']).']" value="'.esc_html(array_key_exists($field['ID'], $_fields) ? $_fields[$field['ID']] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<input type="hidden" name="fieldnames['.esc_html($field['ID']).']" value="'.esc_html($field['Name']).'" />
						<label class="lepopup-integrations-description">'.esc_html($field['Name']).' ('.$field['ID'].')</label>
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
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid API Key (or server response).', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $fields_html);
	}

	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['api-key']) || empty($data['list-id'])) return $_result;
		if (empty($data['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['email'])) return $_result;

		$post_data = array(
			'Email' => $data['email'],
			'Name' => $data['name']
		);
		if (!empty($data['fields']) && is_array($data['fields'])) {
			foreach ($data['fields'] as $key => $value) {
				if (!empty($value)) {
					$post_data['CustomFields'][] = $data['fieldnames'][$key].'='.$value;
				}
			}
		}
//		$result = $this->connect($data['api-key'], 'subscribers/'.urlencode($data['list-id']).'/view.json?Email='.urlencode($data['email']));
//		if (is_array($result) && array_key_exists('Context', $result) && is_array($result['Context']) && array_key_exists('ID', $result['Context'])) {
//			$result = $this->connect($data['api-key'], 'subscribers/'.urlencode($data['list-id']).'/update/'.urlencode($result['Context']['ID']).'.json', $post_data);
//		} else {
			$result = $this->connect($data['api-key'], 'subscribers/'.urlencode($data['list-id']).'/subscribe.json', $post_data);
//		}
		return $_result;
	}
	
	function connect($_api_key, $_path, $_data = array(), $_method = '') {
		$headers = array(
			'Content-Type: application/json;charset=UTF-8',
			'Accept: application/json'
		);
		try {
			$url = 'https://api.moosend.com/v3/'.ltrim($_path, '/');
			if (strpos($url, '?') === false) $url .= '?apikey='.urlencode($_api_key);
			else $url .= '&apikey='.urlencode($_api_key);
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
$lepopup_moosend = new lepopup_moosend_class();
?>