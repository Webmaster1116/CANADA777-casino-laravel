<?php
/* Mailgun integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_mailgun_class {
	var $default_parameters = array(
		"api-key" => "",
		"region" => "us",
		"list" => "",
		"list-id" => "",
		"fields" => array(
			"email" => "",
			"name" => ""
		),
		"custom-names" => array(),
		"custom-values" => array()
	);
	var $fields_meta;
	function __construct() {
		$this->fields_meta = array(
			'email' => esc_html__('Email address', 'lepopup'),
			'name' => esc_html__('Name', 'lepopup')
		);
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-mailgun-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-mailgun-list', array(&$this, "admin_lists"));
		}
		add_filter('lepopup_integrations_do_mailgun', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("mailgun", $_providers)) $_providers["mailgun"] = esc_html__('Mailgun', 'lepopup');
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
					<label>'.esc_html__('Private API Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Mailgun Private API Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find your Mailgun API Key %shere%s.', 'lepopup'), '<a href="https://app.mailgun.com/app/account/security/api_keys" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Region', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select your domain region.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<select name="region">
						<option value="us"'.($data['region'] == 'us' ? ' selected="selected"' : '').'>'.esc_html__('US', 'lepopup').'</option>
						<option value="eu"'.($data['region'] == 'eu' ? ' selected="selected"' : '').'>'.esc_html__('EU', 'lepopup').'</option>
					</select>
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
						<input type="text" name="list" value="'.esc_html($data['list']).'" data-deps="api-key,region" readonly="readonly" />
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
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Mailgun fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>';
		foreach ($this->default_parameters['fields'] as $key => $value) {
			$html .= '
							<tr>
								<th>'.esc_html($this->fields_meta[$key]).'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields['.$key.']" value="'.esc_html(array_key_exists($key, $data['fields']) ? $data['fields'][$key] : $value).'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html($this->fields_meta[$key].' ('.$key.')').'</label>
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
					<label>'.esc_html__('Parameters', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Configure Mailgun parameters and map form fields to them. Parameter name must contain only these characters: A-Z, a-z, 0-9 and _ (parameter name is case sensitive).', 'lepopup').'</div>
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

			if (!is_array($deps) || !array_key_exists('api-key', $deps) || empty($deps['api-key']) || !array_key_exists('region', $deps) || empty($deps['region'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}

			$result = $this->connect($deps['region'], $deps['api-key'], 'lists/pages?limit=100');
			if (is_array($result) && !empty($result)) {
				if (array_key_exists('message', $result)) {
					$return_object = array('status' => 'ERROR', 'message' => esc_html($result['message']));
					echo json_encode($return_object);
					exit;
				} else if (array_key_exists('items', $result) && is_array($result['items']) && sizeof($result['items']) > 0) {
					foreach ($result['items'] as $list) {
						if (is_array($list)) {
							if (array_key_exists('address', $list) && array_key_exists('name', $list)) {
								$lists[$list['address']] = $list['name'];
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
	
	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['api-key']) || empty($data['list-id'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$post_data = array(
			'address' => $data['fields']['email'],
			'name' => $data['fields']['name'],
			'upsert' => 'yes',
			'subscribed' => 'yes'
		);
		$vars = array();
		if (!empty($data['custom-names'])) {
			$post_data['customProperties'] = array();
			foreach($data['custom-names'] as $key => $name) {
				$name = preg_replace('/[^a-zA-Z0-9_]/', '', $name);
				if (!empty($name) && !empty($data['custom-values'][$key])) $vars[$name] = $data['custom-values'][$key];
			}
		}
		if (!empty($vars)) $post_data['vars'] = json_encode($vars);
		$result = $this->connect($data['region'], $data['api-key'], 'lists/'.$data['list-id'].'/members', $post_data);
		return $_result;
	}
	
	function connect($_region, $_api_key, $_path, $_data = array(), $_method = '') {
		if ($_region == 'eu') $region = '.eu';
		else $region = '';
		$headers = array(
			'Accept: application/json'
		);
		try {
			$url = 'https://api'.$region.'.mailgun.net/v3/'.ltrim($_path, '/');
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_USERPWD, 'api:'.$_api_key);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($_data));
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
$lepopup_mailgun = new lepopup_mailgun_class();
?>