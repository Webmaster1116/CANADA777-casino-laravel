<?php
/* Newsman integration for Green Popups */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_newsman_class {
	var $default_parameters = array(
		"user-id" => "",
		"api-key" => "",
		"list" => "",
		"list-id" => "",
		"fields" => array(
			"email" => "",
			"firstname" => "",
			"lastname" => ""
		),
		"custom-names" => array(),
		"custom-values" => array()
	);
	var $fields_meta;
	function __construct() {
		$this->fields_meta = array(
			'email' => array('title' => esc_html__('Email', 'lepopup'), 'description' => esc_html__('Email Address. Mandatory field.', 'lepopup')),
			'firstname' => array('title' => esc_html__('First name', 'lepopup'), 'description' => esc_html__('First name of the contact. Mandatory field.', 'lepopup')),
			'lastname' => array('title' => esc_html__('Last name', 'lepopup'), 'description' => esc_html__('Last name of the contact. Mandatory field.', 'lepopup'))
		);
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-newsman-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-newsman-list', array(&$this, "admin_lists"));
		}
		add_filter('lepopup_integrations_do_newsman', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("newsman", $_providers)) $_providers["newsman"] = esc_html__('Newsman', 'lepopup');
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
					<label>'.esc_html__('User ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your User ID.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="user-id" value="'.esc_html($data['user-id']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find User ID %shere%s.', 'lepopup'), '<a href="https://ssl.newsman.app/manager/account#api" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('API Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your API Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find API Key %shere%s.', 'lepopup'), '<a href="https://ssl.newsman.app/manager/account#api" target="_blank">', '</a>').'</label>
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
						<input type="text" name="list" value="'.esc_html($data['list']).'" data-deps="user-id,api-key" readonly="readonly" />
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
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Newsman fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>';
		foreach ($this->default_parameters['fields'] as $key => $value) {
			$html .= '
							<tr>
								<th>'.esc_html($this->fields_meta[$key]['title']).'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields['.$key.']" value="'.esc_html(array_key_exists($key, $data['fields']) ? $data['fields'][$key] : $value).'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html($this->fields_meta[$key]['description'].' ('.$key.')').'</label>
								</td>
							</tr>';
		}
		$html .= '				
						</table>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Custom Properties', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Configure Newsman custom properties and map form fields to them.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline lepopup-integrations-custom" data-names="custom-names" data-values="custom-values">
						<table>
							<tr>
								<th>'.esc_html__('Name', 'lepopup').'</th>
								<td>'.esc_html__('Value', 'lepopup').'</td>
								<td style="width: 32px;"></td>
							</tr>';
		$i = 0;
		foreach ($data['custom-names'] as $key => $value) {
			if (empty($value)) continue;
			$html .= '
							<tr>
								<th>
									<input type="text" value="'.esc_html($value).'" class="lepopup-integrations-custom-name widefat" data-custom="on" />
								</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" value="'.esc_html(array_key_exists($key, $data['custom-values']) ? $data['custom-values'][$key] : '').'" class="lepopup-integrations-custom-value widefat" data-custom="on" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
								</td>
								<td class="lepopup-middle-center">'.($i > 0 ? '<a class="lepopup-integrations-custom-remove" href="#" onclick="jQuery(this).closest(\'tr\').remove(); return false;"><i class="fas fa-trash-alt"></i></a>' : '').'</td>
							</tr>';
			$i++;
		}
		if ($i == 0) {
			$html .= '
							<tr>
								<th>
									<input type="text" value="" class="lepopup-integrations-custom-name widefat" data-custom="on" />
								</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" value="" class="lepopup-integrations-custom-value widefat" data-custom="on" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
								</td>
								<td></td>
							</tr>';
		}
		$html .= '				
							<tr style="display: none;" class="lepopup-integrations-custom-template">
								<th>
									<input type="text" value="" class="lepopup-integrations-custom-name widefat" data-custom="on" />
								</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" value="" class="lepopup-integrations-custom-value widefat" data-custom="on" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
								</td>
								<td class="lepopup-middle-center"><a class="lepopup-integrations-custom-remove" href="#" onclick="jQuery(this).closest(\'tr\').remove(); return false;"><i class="fas fa-trash-alt"></i></a></td>
							</tr>
							<tr>
								<td colspan="3">
									<a class="lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small" href="#" onclick="return lepopup_integrations_custom_add(this);"><i class="fas fa-plus"></i><label>'.esc_html__('Add Custom Field', 'lepopup').'</label></a>
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
	
	function admin_lists() {
		global $wpdb, $lepopup;
		$lists = array();
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;

			if (!is_array($deps) || !array_key_exists('user-id', $deps) || empty($deps['user-id']) || !array_key_exists('api-key', $deps) || empty($deps['api-key'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Key.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			
			$result = $this->connect($deps['user-id'], $deps['api-key'], 'list.all.json');
			if (is_array($result)) {
				foreach($result as $list) {
					if (is_array($list)) {
						if (array_key_exists('list_id', $list) && array_key_exists('list_name', $list)) {
							$lists[$list['list_id']] = $list['list_name'];
						}
					}
				}
			} else {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API credentials.', 'lepopup'));
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

	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['user-id']) || empty($data['api-key']) || empty($data['list-id'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$result = $this->connect($data['user-id'], $data['api-key'], 'subscriber.getByEmail.json?list_id='.urlencode($data['list-id']).'&email='.urlencode($data['fields']['email']));
		if (is_array($result) && array_key_exists('subscriber_id', $result)) {
			$subscriber_id = $result['subscriber_id'];
			$post_data = array(
				'subscriber_id' => $subscriber_id
			);
			foreach ($data['fields'] as $key => $value) {
				$post_data[$key] = $value;
			}
			$result = $this->connect($data['user-id'], $data['api-key'], 'subscriber.update.json', $post_data);
			$post_data = array(
				'subscriber_id' => $subscriber_id,
				'props' => array()
			);
			if (!empty($data['custom-names'])) {
				foreach($data['custom-names'] as $key => $name) {
					if (!empty($name) && !empty($data['custom-values'][$key])) $post_data['props'][$name] = $data['custom-values'][$key];
				}
			}
			if (!empty($post_data['props'])) {
				$result = $this->connect($data['user-id'], $data['api-key'], 'subscriber.updateProps.json', $post_data);
			}
		} else {
			$post_data = array(
				'list_id' => $data['list-id'],
				'ip' => '10.10.10.10', //$_SERVER['REMOTE_ADDR'],
				'props' => array()
			);
			foreach ($data['fields'] as $key => $value) {
				$post_data[$key] = $value;
			}
			if (!empty($data['custom-names'])) {
				foreach($data['custom-names'] as $key => $name) {
					if (!empty($name) && !empty($data['custom-values'][$key])) $post_data['props'][$name] = $data['custom-values'][$key];
				}
			}
			$result = $this->connect($data['user-id'], $data['api-key'], 'subscriber.saveSubscribe.json', $post_data);
		}
		return $_result;
	}
	
	function connect($_user_id, $_api_key, $_path, $_data = array(), $_method = '') {
		try {
			$url = 'https://ssl.newsman.app/api/1.2/rest/'.urlencode($_user_id).'/'.urlencode($_api_key).'/'.ltrim($_path, '/');
			$curl = curl_init($url);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($_data));
			}
			if (!empty($_method)) {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_method);
			}
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
}
$lepopup_newsman = new lepopup_newsman_class();
?>