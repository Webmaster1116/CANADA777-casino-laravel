<?php
/* Constant Contact integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
define('LEPOPUP_CONSTANT_CONTACT_DEFAULT_API_KEY', 'byk44ey5gc6nkha7vrxmdg8s');
class lepopup_constantcontact_class {
	var $default_parameters = array(
		"api-key" => LEPOPUP_CONSTANT_CONTACT_DEFAULT_API_KEY,
		"token" => "",
		"list" => "",
		"list-id" => "",
		"fields" => array(
			'email' => '',
			'first_name' => '',
			'last_name' => '',
			'prefix_name' => '',
			'cell_phone' => '',
			'home_phone' => '',
			'company_name' => '',
			'job_title' => '',
			'work_phone' => ''
		)
	);
	var $fields_meta;
	function __construct() {
		$this->fields_meta = array(
			'email' => array('title' => esc_html__('Email', 'lepopup'), 'description' => esc_html__('Email address of the contact.', 'lepopup')),
			'first_name' => array('title' => esc_html__('First name', 'lepopup'), 'description' => esc_html__('First name of the contact.', 'lepopup')),
			'last_name' => array('title' => esc_html__('Last name', 'lepopup'), 'description' => esc_html__('Last name of the contact.', 'lepopup')),
			'cell_phone' => array('title' => esc_html__('Cell phone #', 'lepopup'), 'description' => esc_html__('Cell phone number of the contact.', 'lepopup')),
			'company_name' => array('title' => esc_html__('Organization', 'lepopup'), 'description' => esc_html__('Organization name the contact works for.', 'lepopup')),
			'home_phone' => array('title' => esc_html__('Home phone #', 'lepopup'), 'description' => esc_html__('Home phone number of the contact.', 'lepopup')),
			'work_phone' => array('title' => esc_html__('Work phone #', 'lepopup'), 'description' => esc_html__('Work phone number of the contact.', 'lepopup')),
			'job_title' => array('title' => esc_html__('Job title', 'lepopup'), 'description' => esc_html__('Job title of the contact.', 'lepopup')),
			'prefix_name' => array('title' => esc_html__('Name prefix', 'lepopup'), 'description' => esc_html__('Salutation (Mr., Ms., Sir, Mrs., Dr., etc).', 'lepopup'))
		);
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-constantcontact-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-constantcontact-list', array(&$this, "admin_lists"));
		}
		add_filter('lepopup_integrations_do_constantcontact', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("constantcontact", $_providers)) $_providers["constantcontact"] = esc_html__('Constant Contact', 'lepopup');
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
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your API Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" oninput="lepopup_properties_integrations_constantcontact_apikey_changed(this);" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Enter your API Key. You can use %sDefault API Key%s or get your own API Key %shere%s.', 'lepopup'), '<a href="#" onclick="jQuery(this).parent().parent().find(\'input\').val(\''.LEPOPUP_CONSTANT_CONTACT_DEFAULT_API_KEY.'\'); lepopup_properties_integrations_constantcontact_apikey_changed(this); return false;">', '</a>', '<a href="https://constantcontact.mashery.com/apps/mykeys" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Access Token', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Access Token.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="token" value="'.esc_html($data['token']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Enter your Access Token. You can get it %shere%s.', 'lepopup'), '<a class="lepopup-constantcontact-token-link" href="https://oauth2.constantcontact.com/oauth2/password.htm?client_id='.urlencode($data['api-key']).'" data-href="https://oauth2.constantcontact.com/oauth2/password.htm?client_id={api-key}" target="_blank">', '</a>').'</label>
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
						<input type="text" name="list" value="'.esc_html($data['list']).'" data-deps="api-key,token" readonly="readonly" />
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
					<div class="lepopup-tooltip-content">'.esc_html__('Map popup fields to Constant Contact fields.', 'lepopup').'</div>
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

			if (!is_array($deps) || !array_key_exists('api-key', $deps) || empty($deps['api-key']) || !array_key_exists('token', $deps) || empty($deps['token'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			
			$result = $this->connect($deps['api-key'], $deps['token'], 'lists');
			if (is_array($result)) {
				if (sizeof($result) == 0) {
					$return_object = array('status' => 'ERROR', 'message' => esc_html__('No lists found.', 'lepopup'));
					echo json_encode($return_object);
					exit;
				}
				foreach($result as $list) {
					if (array_key_exists("error_message", $list)) {
						$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API credentials.', 'lepopup'));
						echo json_encode($return_object);
						exit;
					}
					if (is_array($list)) {
						if (array_key_exists('id', $list) && array_key_exists('name', $list)) {
							$lists[$list['id']] = $list['name'];
						}
					}
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
		if (empty($data['api-key']) || empty($data['token']) || empty($data['list-id'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$result = $this->connect($data['api-key'], $data['token'], 'contacts?email='.urlencode($data['fields']['email']));
		if(!empty($result) && is_array($result) && array_key_exists('results', $result)) {
			if (!empty($result['results'])) {
				$contact = $result['results'][0];
				$contact_original = $contact;
				$update = true;
				foreach ($contact['lists'] as $list) {
					if ($list['id'] == $data['list-id']) {
						$update = false;
						break;
					}
				}
				if ($update) {
					$contact['lists'][] = array('id' => $data['list-id']);
				}
				foreach ($data['fields'] as $key => $value) {
					if (!empty($value) && $key != 'email') {
						$contact[$key] = $value;
					}
				}
				if ($contact != $contact_original) {
					$result = $this->connect($data['api-key'], $data['token'], 'contacts/'.$contact['id'].'?action_by=ACTION_BY_VISITOR', $contact, 'PUT');
				}
			} else {
				$contact = array(
					'email_addresses' => array(
						array('email_address' => $data['fields']['email'])
					),
					'lists' => array(
						array(
							'id' => $data['list-id']
						)
					)
				);
				foreach ($data['fields'] as $key => $value) {
					if (!empty($value) && $key != 'email') {
						$contact[$key] = $value;
					}
				}
				$result = $this->connect($data['api-key'], $data['token'], 'contacts?action_by=ACTION_BY_VISITOR', $contact);
			}
		}
		return $_result;
	}
	
	function connect($_api_key, $_token, $_path, $_data = array(), $_method = '') {
		try {
			$url = 'https://api.constantcontact.com/v2/'.ltrim($_path, '/').(strpos($_path, '?') === false ? '?' : '&').'api_key='.urlencode($_api_key);
			$header = array(
				'Content-Type: application/json',
				'Authorization: Bearer '.$_token
			);
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
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
$lepopup_constantcontact = new lepopup_constantcontact_class();
?>