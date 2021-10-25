<?php
/* Rapidmail integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_rapidmail_class {
	var $default_parameters = array(
		"api-username" => "",
		"api-password" => "",
		"list" => "",
		"list-id" => "",
		"fields" => array(
			"email" => "",
			"firstname" => "",
			"lastname" => "",
			"gender" => "",
			"title" => "",
			"zip" => "",
			"birthdate" => "",
			"extra1" => "",
			"extra2" => "",
			"extra3" => "",
			"extra4" => "",
			"extra5" => "",
			"extra6" => "",
			"extra7" => "",
			"extra8" => "",
			"extra9" => "",
			"extra10" => ""
		)
	);
	var $fields_meta;
	function __construct() {
		$this->fields_meta = array(
			'email' => esc_html__('Email address', 'lepopup'),
			'firstname' => esc_html__('First name', 'lepopup'),
			'lastname' => esc_html__('Last name', 'lepopup'),
			'gender' => esc_html__('Gender', 'lepopup'),
			'title' => esc_html__('Title', 'lepopup'),
			'zip' => esc_html__('ZIP Code', 'lepopup'),
			'birthdate' => esc_html__('Birtdate', 'lepopup'),
			'extra1' => esc_html__('Extra 1', 'lepopup'),
			'extra2' => esc_html__('Extra 2', 'lepopup'),
			'extra3' => esc_html__('Extra 3', 'lepopup'),
			'extra4' => esc_html__('Extra 4', 'lepopup'),
			'extra5' => esc_html__('Extra 5', 'lepopup'),
			'extra6' => esc_html__('Extra 6', 'lepopup'),
			'extra7' => esc_html__('Extra 4', 'lepopup'),
			'extra8' => esc_html__('Extra 8', 'lepopup'),
			'extra9' => esc_html__('Extra 9', 'lepopup'),
			'extra10' => esc_html__('Extra 10', 'lepopup')
		);
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-rapidmail-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-rapidmail-list', array(&$this, "admin_lists"));
		}
		add_filter('lepopup_integrations_do_rapidmail', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("rapidmail", $_providers)) $_providers["rapidmail"] = esc_html__('Rapidmail', 'lepopup');
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
					<label>'.esc_html__('API Username', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Rapidmail API Username.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-username" value="'.esc_html($data['api-username']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find your Rapidmail API Username %shere%s.', 'lepopup'), '<a href="https://my.rapidmail.de/api/v3/userlist.html" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('API Password', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Rapidmail API Password.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-password" value="'.esc_html($data['api-password']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find your Rapidmail API Password %shere%s.', 'lepopup'), '<a href="https://my.rapidmail.de/api/v3/userlist.html" target="_blank">', '</a>').'</label>
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
						<input type="text" name="list" value="'.esc_html($data['list']).'" data-deps="api-username,api-password" readonly="readonly" />
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
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Rapidmail fields.', 'lepopup').'</div>
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
										<input type="text" name="fields['.esc_html($key).']" value="'.esc_html(array_key_exists($key, $data['fields']) ? $data['fields'][$key] : $value).'" class="widefat" />
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

			if (!is_array($deps) || !array_key_exists('api-username', $deps) || empty($deps['api-username']) || !array_key_exists('api-password', $deps) || empty($deps['api-password'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}

			$result = $this->connect($deps['api-username'], $deps['api-password'], 'recipientlists');

			if (is_array($result)) {
				if (array_key_exists('_embedded', $result)) {
					if (array_key_exists('recipientlists', $result['_embedded']) && sizeof($result['_embedded']['recipientlists']) > 0) {
						foreach ($result['_embedded']['recipientlists'] as $list) {
							if (is_array($list)) {
								if (array_key_exists('id', $list) && array_key_exists('name', $list)) {
									$lists[$list['id']] = $list['name'];
								}
							}
						}
					} else {
						$return_object = array('status' => 'ERROR', 'message' => esc_html__('No lists found.', 'lepopup'));
						echo json_encode($return_object);
						exit;
					}
				} else {
					$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Credentials.', 'lepopup'));
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
		if (empty($data['api-username']) || empty($data['api-password']) || empty($data['list-id'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$post_data = array(
			'created_ip' => $_SERVER['REMOTE_ADDR'],
			'status' => 'active'
		);
		foreach($data['fields'] as $key => $value) {
			if (!empty($value)) $post_data[$key] = $data['fields'][$key];
		}
		$result = $this->connect($data['api-username'], $data['api-password'], 'recipients?recipientlist_id='.urlencode($data['list-id']).'&email='.urlencode($data['fields']['email']));
		if (array_key_exists('_embedded', $result) && array_key_exists('recipients', $result['_embedded']) && sizeof($result['_embedded']['recipients']) > 0) {
			$result = $this->connect($data['api-username'], $data['api-password'], 'recipients/'.urlencode($result['_embedded']['recipients'][0]['id']), $post_data, 'PATCH');
		} else {
			$post_data['recipientlist_id'] = $data['list-id'];
			$result = $this->connect($data['api-username'], $data['api-password'], 'recipients', $post_data);
		}
		return $_result;
	}
	
	function connect($_api_username, $_api_password, $_path, $_data = array(), $_method = '') {
		$headers = array(
			'Content-Type: application/json;charset=UTF-8',
			'Accept: application/json'
		);
		try {
			$url = 'https://apiv3.emailsys.net/v1/'.ltrim($_path, '/');
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, $_api_username.':'.$_api_password);
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
$lepopup_rapidmail = new lepopup_rapidmail_class();
?>