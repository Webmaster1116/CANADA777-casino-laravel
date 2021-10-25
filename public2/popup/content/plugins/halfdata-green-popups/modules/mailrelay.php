<?php
/* Mailrelay integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_mailrelay_class {
	var $default_parameters = array(
		"hostname" => "",
		"api-key" => "",
		"groups" => array(),
		'fields' => array('email' => '', 'name' => '')
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-mailrelay-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-mailrelay-groups', array(&$this, "admin_groups_html"));
		}
		add_filter('lepopup_integrations_do_mailrelay', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("mailrelay", $_providers)) $_providers["mailrelay"] = esc_html__('Mailrelay', 'lepopup');
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
					<label>'.esc_html__('Hostname', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Mailrelay hostname.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="hostname" value="'.esc_html($data['hostname']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Enter your Mailrelay hostname. Usually it looks like %shostname.ipzmarketing.com%s.', 'lepopup'), '<code>', '</code>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('API Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Mailrelay API Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
					<label class="lepopup-integrations-description">'.esc_html__('Find Mailrelay API Key in your Dashboard >> Settings >> API Keys.', 'lepopup').'</label>
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
			if (!empty($data['api-key']) && !empty($data['hostname'])) {
				$groups_data = $this->get_groups_html($data['hostname'], $data['api-key'], $data['groups']);
				if ($groups_data['status'] == 'OK') $html .= $groups_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="groups" data-deps="hostname,api-key"><i class="fas fa-download"></i><label>'.esc_html__('Load Groups', 'lepopup').'</label></a>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Mailrelay fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>
							<tr>
								<th>'.esc_html__('Email', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[email]" value="'.(array_key_exists('email', $data['fields']) ? esc_html($data['fields']['email']) : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Email address', 'lepopup').'</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('Name', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[name]" value="'.(array_key_exists('name', $data['fields']) ? esc_html($data['fields']['name']) : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Name', 'lepopup').'</label>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>';
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = $html;
			echo json_encode($return_object);
		}
		exit;
	}
	
	function admin_groups_html() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;
			if (!is_array($deps) || !array_key_exists('api-key', $deps) || empty($deps['api-key']) || !array_key_exists('hostname', $deps) || empty($deps['hostname'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid Hostname or API Key.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_groups_html($deps['hostname'], $deps['api-key'], $this->default_parameters['groups']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_groups_html($_hostname, $_key, $_groups) {
		global $wpdb, $lepopup;
		
		$post_data = array(
			'function' => 'getGroups',
			'offset' => 0,
			'count' => 100
		);
		$result = $this->connect($_hostname, $_key, $post_data);
		$groups_html = '';
		if (!empty($result) && is_array($result) && array_key_exists('status', $result)) {
			if (array_key_exists('error', $result)) {
				return array('status' => 'ERROR', 'message' => $result['error']);
			} else {
				if (array_key_exists('data', $result) && is_array($result['data']) && sizeof($result['data']) > 0) {
					$groups_html .= '
					<div class="lepopup-properties-pure" style="margin: 0 0 10px 0;">';
					foreach ($result['data'] as $group) {
						if (array_key_exists($group['id'], $_groups)) $checked = $_groups[$group['id']];
						else $checked = 'off';
						$checkbox_id = $lepopup->random_string(16);
						$groups_html .= '
					<div class="lepopup-properties-pure" style="margin: 3px 0;">
						<input class="lepopup-checkbox lepopup-checkbox-classic lepopup-checkbox-medium" id="group-'.esc_html($checkbox_id).'" type="checkbox" value="on" name="groups['.esc_html($group['id']).']"'.($checked == 'on' ? ' checked="checked"' : '').' /><label for="group-'.esc_html($checkbox_id).'"></label> '.esc_html($group['name']).'
					</div>';
					}
					$groups_html .= '
					</div>';
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
		if (empty($data['api-key']) || empty($data['hostname'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$post_data = array(
			'function' => 'getSubscribers',
			'email' => $data['fields']['email']
		);

		$result = $this->connect($data['hostname'], $data['api-key'], $post_data);
		if (!empty($result) && is_array($result) && array_key_exists('status', $result) && $result['status'] != '0') {
			if (sizeof($result['data']) > 0) {
				$post_data = array(
					'function' => 'updateSubscriber',
					'id' => $result['data'][0]['id'],
					'email' => $data['fields']['email'],
					'name' => $data['fields']['name'],
					'groups' => array_keys($data['groups'])
				);
				if (is_array($result['data'][0]['groups'])) {
					foreach ($result['data'][0]['groups'] as $group) {
						if (!in_array($group['group_id'], $post_data['groups'])) $post_data['groups'][] = $group['group_id'];
					}
				}
				$result = $this->connect($data['hostname'], $data['api-key'], $post_data);
			} else {
				$post_data = array(
					'function' => 'addSubscriber',
					'email' => $data['fields']['email'],
					'name' => $data['fields']['name']
				);
				$post_data['groups'] = array_keys($data['groups']);
				$result = $this->connect($data['hostname'], $data['api-key'], $post_data);
			}
		}
		return $_result;
	}
	
	function connect($_hostname, $_api_key, $_data = array()) {
		$_data['apiKey'] = $_api_key;
		try {
			$hostname = strtolower($_hostname);
			if (substr($hostname, 0, 7) != "http://" && substr($hostname, 0, 8) != "https://") $hostname = 'https://'.$hostname;
			$hostname = parse_url($hostname, PHP_URL_HOST);
			$url = 'https://'.$hostname.'/ccm/admin/api/version/2/&type=json';
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($_data));
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
$lepopup_mailrelay = new lepopup_mailrelay_class();
?>