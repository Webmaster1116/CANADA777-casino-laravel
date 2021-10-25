<?php
/* Drip integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_drip_class {
	var $default_parameters = array(
		"api-token" => "",
		"account" => "",
		"account-id" => "",
		"campaign" => "",
		"campaign-id" => "",
		"fields" => array(
			'email' => '',
			'first_name' => '',
			'last_name' => '',
			'address1' => '',
			'address2' => '',
			'city' => '',
			'state' => '',
			'country' => '',
			'zip' => '',
			'phone' => ''
		),
		"custom-fields" => array(),
		"tags" => "",
		"eu-consent" => ""
	);
	var $fields_meta;
	function __construct() {
		$this->fields_meta = array(
			'email' => array('title' => esc_html__('Email', 'lepopup'), 'description' => esc_html__('The subscriber\'s email address.', 'lepopup')),
			'first_name' => array('title' => esc_html__('First name', 'lepopup'), 'description' => esc_html__('The subscriber\'s first name.', 'lepopup')),
			'last_name' => array('title' => esc_html__('Last name', 'lepopup'), 'description' => esc_html__('The subscriber\'s first name.', 'lepopup')),
			'address1' => array('title' => esc_html__('Address 1', 'lepopup'), 'description' => esc_html__('The subscriber\'s mailing address.', 'lepopup')),
			'address2' => array('title' => esc_html__('Address 2', 'lepopup'), 'description' => esc_html__('An additional field for the subscriber\'s mailing address.', 'lepopup')),
			'city' => array('title' => esc_html__('City', 'lepopup'), 'description' => esc_html__('The city, town, or village in which the subscriber resides.', 'lepopup')),
			'state' => array('title' => esc_html__('Region', 'lepopup'), 'description' => esc_html__('The region in which the subscriber resides. Typically a province, a state, or a prefecture.', 'lepopup')),
			'country' => array('title' => esc_html__('Country', 'lepopup'), 'description' => esc_html__('The country in which the subscriber resides.', 'lepopup')),
			'zip' => array('title' => esc_html__('Postal code', 'lepopup'), 'description' => esc_html__('The postal code in which the subscriber resides, also known as zip, postcode, Eircode, etc.', 'lepopup')),
			'phone' => array('title' => esc_html__('Phone #', 'lepopup'), 'description' => esc_html__('The subscriber\'s primary phone number.', 'lepopup'))
		);
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-drip-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-drip-account', array(&$this, "admin_accounts"));
			add_action('wp_ajax_lepopup-drip-campaign', array(&$this, "admin_campaigns"));
			add_action('wp_ajax_lepopup-drip-custom-fields', array(&$this, "admin_fields_html"));
		}
		add_filter('lepopup_integrations_do_drip', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("drip", $_providers)) $_providers["drip"] = esc_html__('Drip', 'lepopup');
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
					<label>'.esc_html__('API Token', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Drip API Token.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-token" value="'.esc_html($data['api-token']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find your Drip API Token %shere%s.', 'lepopup'), '<a href="https://www.getdrip.com/user/edit" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Account ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select Account ID.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-group lepopup-integrations-ajax-options">
						<input type="text" name="account" value="'.esc_html($data['account']).'" data-deps="api-token" readonly="readonly" />
						<input type="hidden" name="account-id" value="'.esc_html($data['account-id']).'" />
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Campaign ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select Campaign ID.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-group lepopup-integrations-ajax-options">
						<input type="text" name="campaign" value="'.esc_html($data['campaign']).'" data-deps="api-token,account-id" readonly="readonly" />
						<input type="hidden" name="campaign-id" value="'.esc_html($data['campaign-id']).'" />
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Drip fields.', 'lepopup').'</div>
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
									<label class="lepopup-integrations-description">'.esc_html($this->fields_meta[$key]['description']).'</label>
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
					<label>'.esc_html__('Custom Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Drip custom fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['api-token']) && !empty($data['account-id'])) {
				$fields_data = $this->get_fields_html($data['api-token'], $data['account-id'], $data['custom-fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="custom-fields" data-deps="api-token,account-id"><i class="fas fa-download"></i><label>'.esc_html__('Load Custom Fields', 'lepopup').'</label></a>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('EU Consent', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Specify whether the subscriber granted or denied GDPR consent. You can use field shortcode to associate EU Consent field with the form field.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-input-shortcode-selector">
						<input type="text" name="eu-consent" value="'.esc_html($data['eu-consent']).'" class="widefat" />
						<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Tags', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Specify comma-separated list of tags that applies to subscribers.', 'lepopup').'</div>
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
	
	function admin_accounts() {
		global $wpdb, $lepopup;
		$accounts = array();
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;

			if (!is_array($deps) || !array_key_exists('api-token', $deps) || empty($deps['api-token'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Token.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}

			$result = $this->connect($deps['api-token'], 'accounts');
			if ($result && array_key_exists('accounts', $result)) {
				if (is_array($result['accounts']) && sizeof($result['accounts']) > 0) {
					foreach ($result['accounts'] as $account) {
						if (is_array($account)) {
							if (array_key_exists('id', $account) && array_key_exists('name', $account)) {
								$accounts[$account['id']] = $account['name'];
							}
						}
					}
				} else {
					$return_object = array('status' => 'ERROR', 'message' => esc_html__('No accounts found.', 'lepopup'));
					echo json_encode($return_object);
					exit;
				}
			} else {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Token.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (empty($accounts)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('No accounts found.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['items'] = $accounts;
			echo json_encode($return_object);
		}
		exit;
	}

	function admin_campaigns() {
		global $wpdb, $lepopup;
		$campaigns = array();
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;

			if (!is_array($deps) || !array_key_exists('api-token', $deps) || empty($deps['api-token']) || !array_key_exists('account-id', $deps) || empty($deps['account-id'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Token or Account ID.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}

			$result = $this->connect($deps['api-token'], urlencode($deps['account-id']).'/campaigns');
			if ($result && array_key_exists('campaigns', $result)) {
				if (is_array($result['campaigns']) && sizeof($result['campaigns']) > 0) {
					foreach ($result['campaigns'] as $campaign) {
						if (is_array($campaign)) {
							if (array_key_exists('id', $campaign) && array_key_exists('name', $campaign)) {
								$campaigns[$campaign['id']] = $campaign['name'];
							}
						}
					}
				} else {
					$return_object = array('status' => 'ERROR', 'message' => esc_html__('No campaigns found.', 'lepopup'));
					echo json_encode($return_object);
					exit;
				}
			} else {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Token or Account ID.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (empty($campaigns)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('No campaigns found.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['items'] = $campaigns;
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
			if (!is_array($deps) || !array_key_exists('api-token', $deps) || empty($deps['api-token']) || !array_key_exists('account-id', $deps) || empty($deps['account-id'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Token or Account ID.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_fields_html($deps['api-token'], $deps['account-id'], $this->default_parameters['custom-fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_api_token, $_account_id, $_fields) {
		global $wpdb, $lepopup;
		$result = $this->connect($_api_token, urlencode($_account_id).'/custom_field_identifiers');
		$fields_html = '';
		if (!empty($result) && is_array($result)) {
			if (array_key_exists('errors', $result)) {
				return array('status' => 'ERROR', 'message' => $result['errors'][0]['message']);
			} else if (!array_key_exists('custom_field_identifiers', $result)) {
				return array('status' => 'ERROR', 'message' => __('Inavlid server response.', 'lepopup'));
			} else {
				if (is_array($result['custom_field_identifiers']) && sizeof($result['custom_field_identifiers']) > 0) {
					$unique_fields = array();
					foreach ($result['custom_field_identifiers'] as $field) {
						if (!array_key_exists($field, $this->default_parameters['fields'])) {
							$unique_fields[] = $field;
						}
					}
					if (sizeof($unique_fields) > 0) {
						$fields_html = '
			<table>';
						foreach ($unique_fields as $field) {
								$fields_html .= '
				<tr>
					<th>'.esc_html($field).'</th>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="custom-fields['.esc_html($field).']" value="'.esc_html(array_key_exists($field, $_fields) ? $_fields[$field] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<label class="lepopup-integrations-description">'.esc_html($field).'</label>
					</td>
				</tr>';
						}
						$fields_html .= '
			</table>';
					} else {
						return array('status' => 'ERROR', 'message' => __('No custom fields found.', 'lepopup'));
					}
				} else {
					return array('status' => 'ERROR', 'message' => esc_html__('No fields found.', 'lepopup'));
				}
			}
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid API Token or Account ID.', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $fields_html);
	}

	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['api-token']) || empty($data['account-id'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$eu_consent = 'unknown';
		if (in_array(strtolower($data['eu-consent']), array('on', 'true', 'yes', '1', 'granted'))) $eu_consent = 'granted';
		else if (in_array(strtolower($data['eu-consent']), array('off', 'false', 'no', '0', 'denied'))) $eu_consent = 'denied';
		$post_data = array(
			'email' => $data['fields']['email'], 
			'ip_address' => $_SERVER['REMOTE_ADDR'],
			'eu_consent' => $eu_consent,
			'status' => 'active'
		);
		foreach ($data['fields'] as $key => $value) {
			if (!empty($value) && $key != 'email') {
				$post_data[$key] = $value;
			}
		}
		if (is_array($data['custom-fields'])) {
			foreach ($data['custom-fields'] as $key => $value) {
				if (!empty($value)) $post_data['custom_fields'][$key] = $value;
			}
		}
		$tags_sanitized = array();
		$tags_raw = explode(',', $data['tags']);
		foreach ($tags_raw as $tag_raw) {
			$tag_raw = trim($tag_raw);
			if (!empty($tag_raw)) $tags_sanitized[] = $tag_raw;
		}
		if (!empty($tags_sanitized)) $post_data['tags'] = $tags_sanitized;

		$result = $this->connect($data['api-token'], urlencode($data['account-id']).'/subscribers', array("subscribers" => array($post_data)));
		if (!empty($data['campaign-id'])) $result = $this->connect($data['api-token'], urlencode($data['account-id']).'/campaigns/'.urlencode($data['campaign-id']).'/subscribers', array("subscribers" => array(array('email' => $data['fields']['email'], 'eu_consent' => $eu_consent))));

		return $_result;
	}
	
	function connect($_api_token, $_path, $_data = array(), $_method = '') {
		$headers = array(
			'Content-Type: application/vnd.api+json',
			'Accept: application/json'
		);
		try {
			$url = 'https://api.getdrip.com/v2/'.ltrim($_path, '/');
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, $_api_token);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($_data));
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
$lepopup_drip = new lepopup_drip_class();
?>