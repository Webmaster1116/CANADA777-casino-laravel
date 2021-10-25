<?php
/* Acelle Mail integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_acellemail_class {
	var $default_parameters = array(
		"api-url" => "",
		"token" => "",
		"list" => "",
		"list-id" => "",
		"fields" => array(),
		"tags" => ""
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-acellemail-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-acellemail-list', array(&$this, "admin_lists"));
			add_action('wp_ajax_lepopup-acellemail-fields', array(&$this, "admin_fields_html"));
		}
		add_filter('lepopup_integrations_do_acellemail', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("acellemail", $_providers)) $_providers["acellemail"] = esc_html__('Acelle Mail', 'lepopup');
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
					<label>'.esc_html__('API Endpoint', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Acelle Mail API Endpoint. You can get it on API page in your Acelle Mail account.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-url" value="'.esc_html($data['api-url']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('API Token', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Acelle Mail API Token. You can get it on API page in your Acelle Mail account.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="token" value="'.esc_html($data['token']).'" />
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
						<input type="text" name="list" value="'.esc_html($data['list']).'" data-deps="api-url,token" readonly="readonly" />
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
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Acelle Mail fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['api-url']) && !empty($data['token']) && !empty($data['list-id'])) {
				$fields_data = $this->get_fields_html($data['api-url'], $data['token'], $data['list-id'], $data['fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="fields" data-deps="api-url,token,private-key,list-id"><i class="fas fa-download"></i><label>'.esc_html__('Load Fields', 'lepopup').'</label></a>
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

			if (!is_array($deps) || !array_key_exists('api-url', $deps) || empty($deps['api-url']) || !array_key_exists('token', $deps) || empty($deps['token'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}

			$result = $this->connect($deps['api-url'], $deps['token'], 'lists');

			if(!$result || !is_array($result)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Credential or server response.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (sizeof($result) == 0) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('No lists found.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}

			$lists = array();
			foreach ($result as $list) {
				if (is_array($list)) {
					if (array_key_exists('uid', $list) && array_key_exists('name', $list)) {
						$lists[$list['uid']] = $list['name'];
					}
				}
			}
			if (sizeof($lists) == 0) {
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
			if (!is_array($deps) || !array_key_exists('api-url', $deps) || empty($deps['api-url']) || !array_key_exists('token', $deps) || empty($deps['token']) || !array_key_exists('list-id', $deps) || empty($deps['list-id'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Credentials or List ID.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_fields_html($deps['api-url'], $deps['token'], $deps['list-id'], $this->default_parameters['fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_api_url, $_token, $_list, $_fields) {
		global $wpdb, $lepopup;
		$result = $this->connect($_api_url, $_token, 'lists/'.urlencode($_list));
		if(!$result || !is_array($result)) {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid server response.', 'lepopup'));
		}
		if (!array_key_exists('list', $result)) {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid server response.', 'lepopup'));
		}
		if (!array_key_exists('fields', $result['list']) || $result['list']['fields'] == 0) {
			return array('status' => 'ERROR', 'message' => esc_html__('Make sure that you use latest version of Acelle Mail application.', 'lepopup'));
		}
		
		$fields_html = '
			<table>';
		foreach ($result['list']['fields'] as $field) {
			if (is_array($field)) {
				if (array_key_exists('tag', $field) && array_key_exists('label', $field)) {
					$fields_html .= '
				<tr>
					<th>'.esc_html($field['label']).'</td>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="fields['.esc_html($field['tag']).']" value="'.esc_html(array_key_exists($field['tag'], $_fields) ? $_fields[$field['tag']] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<label class="lepopup-integrations-description">'.esc_html($field['label'].' ('.$field['tag'].')').'</label>
					</td>
				</tr>';
				}
			}
		}
		$fields_html .= '
			</table>';
		return array('status' => 'OK', 'html' => $fields_html);
	}

	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['api-url']) || empty($data['token']) || empty($data['list-id'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['EMAIL']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['EMAIL'])) return $_result;

		$tags_sanitized = array();
		$tags_raw = explode(',', $data['tags']);
		foreach ($tags_raw as $tag_raw) {
			$tag_raw = trim($tag_raw);
			if (!empty($tag_raw)) $tags_sanitized[] = $tag_raw;
		}
		$post_data = array(
			'ip_address' => $_SERVER['REMOTE_ADDR'],
			'tag' => implode(',', $tags_sanitized)
		);
		foreach ($data['fields'] as $key => $value) {
			if (!empty($value)) {
				$post_data[$key] = $value;
			}
		}
		$post_data['list_uid'] = $data['list-id'];
		$result = $this->connect($data['api-url'], $data['token'], 'subscribers', $post_data);
		$result = $this->connect($data['api-url'], $data['token'], 'subscribers/email/'.strtolower($data['fields']['EMAIL']));
		if (!empty($result) && array_key_exists('subscribers', $result) && !empty($result['subscribers'])) {
			$subscriber_id = $result['subscribers'][0]['uid'];
			$result = $this->connect($data['api-url'], $data['token'], 'subscribers/'.$subscriber_id, $post_data, 'PATCH');
			$post_data = array('list_uid' => $data['list-id']);
			$result = $this->connect($data['api-url'], $data['token'], 'subscribers/'.$subscriber_id.'/subscribe', $post_data, 'PATCH');
		} else {
			$post_data['list_uid'] = $data['list-id'];
			$result = $this->connect($data['api-url'], $data['token'], 'subscribers', $post_data);
		}
		return $_result;
	}
	
	function connect($_api_url, $_api_key, $_path, $_data = array(), $_method = '') {
		$headers = array(
			'Accept: application/json'
		);
		try {
			$url = rtrim($_api_url, '/').'/'.ltrim($_path, '/');
			if (strpos($url, '?') === false) $url .= '?api_token='.$_api_key;
			else $url .= '&api_token='.$_api_key;
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $_data);
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
$lepopup_acellemail = new lepopup_acellemail_class();
?>