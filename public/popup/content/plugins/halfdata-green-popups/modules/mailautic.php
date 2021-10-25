<?php
/* Mailautic integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_mailautic_class {
	var $default_parameters = array(
		"public-key" => "",
		"private-key" => "",
		"list" => "",
		"list-id" => "",
		"fields" => array()
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-mailautic-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-mailautic-list', array(&$this, "admin_lists"));
			add_action('wp_ajax_lepopup-mailautic-fields', array(&$this, "admin_fields_html"));
			add_action('wp_ajax_lepopup-mailautic-groups', array(&$this, "admin_groups_html"));
		}
		add_filter('lepopup_integrations_do_mailautic', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("mailautic", $_providers)) $_providers["mailautic"] = esc_html__('Mailautic', 'lepopup');
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
					<label>'.esc_html__('Public Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Mailautic API Public Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="public-key" value="'.esc_html($data['public-key']).'" />
					<label class="lepopup-integrations-description">Find your Mailautic API Public Key <a href="https://app.mailautic.io/customer/api-keys/index" target="_blank">here</a>.</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Private Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Mailautic API Private Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="private-key" value="'.esc_html($data['private-key']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find your Mailautic API Private Key %shere%s.', 'lepopup'), '<a href="https://app.mailautic.io/customer/api-keys/index" target="_blank">', '</a>').'</label>
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
						<input type="text" name="list" value="'.esc_html($data['list']).'" data-deps="public-key,private-key" readonly="readonly" />
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
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Mailautic fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['public-key']) && !empty($data['private-key']) && !empty($data['list-id'])) {
				$fields_data = $this->get_fields_html($data['public-key'], $data['private-key'], $data['list-id'], $data['fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="fields" data-deps="public-key,private-key,list-id"><i class="fas fa-download"></i><label>'.esc_html__('Load Fields', 'lepopup').'</label></a>
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

			if (!is_array($deps) || !array_key_exists('public-key', $deps) || empty($deps['public-key']) || !array_key_exists('private-key', $deps) || empty($deps['private-key'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}

			$result = $this->connect($deps['public-key'], $deps['private-key'], 'lists?page=1&per_page=9999');
			if(!$result || !is_array($result)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid server response.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (!array_key_exists('status', $result) || $result['status'] != 'success') {
				$return_object = array('status' => 'ERROR', 'message' => $result['error']);
				echo json_encode($return_object);
				exit;
			}
			if (!array_key_exists('data', $result) || !array_key_exists('count', $result['data']) || $result['data']['count'] == 0) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('No lists found.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}

			$lists = array();
			foreach ($result['data']['records'] as $key => $value) {
				$lists[$value['general']['list_uid']] = $value['general']['name'];
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
			if (!is_array($deps) || !array_key_exists('public-key', $deps) || empty($deps['public-key']) || !array_key_exists('private-key', $deps) || empty($deps['private-key']) || !array_key_exists('list-id', $deps) || empty($deps['list-id'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Credentials or List ID.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_fields_html($deps['public-key'], $deps['private-key'], $deps['list-id'], $this->default_parameters['fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_public_key, $_private_key, $_list, $_fields) {
		global $wpdb, $lepopup;
		$result = $this->connect($_public_key, $_private_key, 'lists/'.$_list.'/fields?page=1&per_page=9999');
		if(!$result || !is_array($result)) {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid server response.', 'lepopup'));
		}
		if (!array_key_exists('status', $result) || $result['status'] != 'success') {
			return array('status' => 'ERROR', 'message' => $result['error']);
		}
		if (!array_key_exists('data', $result) || !array_key_exists('records', $result['data']) || sizeof($result['data']['records']) == 0) {
			return array('status' => 'ERROR', 'message' => esc_html__('No fields found.', 'lepopup'));
		}
		
		$fields_html = '
			<table>';
		foreach ($result['data']['records'] as $field) {
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
		if (empty($data['public-key']) || empty($data['private-key']) || empty($data['list-id'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['EMAIL']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['EMAIL'])) return $_result;

		$result = $this->connect($data['public-key'], $data['private-key'], 'lists/'.$data['list-id'].'/subscribers/search-by-email?EMAIL='.urlencode($data['fields']['EMAIL']));
		if(!$result || !is_array($result) || !array_key_exists('status', $result)) return $_result;
		$post_data = array(
			'EMAIL' => $data['fields']['EMAIL'], 
			'details' => array('ip_address' => $_SERVER['REMOTE_ADDR'])
		);
		foreach ($data['fields'] as $key => $value) {
			if (!empty($value) && $key != 'EMAIL') {
				$post_data[$key] = $value;
			}
		}
		
		if ($result['status'] != 'success') {
			$result = $this->connect($data['public-key'], $data['private-key'], 'lists/'.$data['list-id'].'/subscribers', 'POST', $post_data);
		} else {
			$result = $this->connect($data['public-key'], $data['private-key'], 'lists/'.$data['list-id'].'/subscribers/'.$result['data']['subscriber_uid'], 'PUT', $post_data);
		}
		return $_result;
	}
	
	function connect($_public_key, $_private_key, $_path, $_method = 'GET', $_data = null) {
		try {
			$url = 'https://app.mailautic.io/api/'.rtrim($_path, '/');
			$timestamp = time();
			$headers = array(
				'X-MW-PUBLIC-KEY' => $_public_key,
				'X-MW-REMOTE-ADDR' => $_SERVER['REMOTE_ADDR'],
				'X-MW-TIMESTAMP' => $timestamp
			);
			if (is_array($_data) && !empty($_data)) $signature_data = array_merge($headers, $_data);
			else $signature_data = $headers;
			ksort($signature_data, SORT_STRING);
			$signature_string = strtoupper($_method).' '.$url.(strpos($url, '?') === false ? '?' : '&').http_build_query($signature_data, '', '&');
			$signature = hash_hmac('sha1', $signature_string, $_private_key, false);
			$headers['X-MW-SIGNATURE'] = $signature;
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_USERAGENT, 'MailWizzApi Client version 1.0');
			curl_setopt($ch, CURLOPT_AUTOREFERER , true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$curl_headers = array();
			$headers['X-HTTP-Method-Override'] = strtoupper($_method);
			foreach($headers as $name => $value) {
				$curl_headers[] = $name.': '.$value;
			}
			curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
			if (is_array($_data) && !empty($_data)) {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($_method));
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_data, '', '&'));
			}
			$response = curl_exec($ch);
			curl_close($ch);
			$result = json_decode($response, true);
		} catch (Exception $e) {
			$result = false;
		}
		return $result;
	}
}
$lepopup_mailautic = new lepopup_mailautic_class();
?>