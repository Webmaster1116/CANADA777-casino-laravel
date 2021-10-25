<?php
/* AgileCRM integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_agilecrm_class {
	var $default_parameters = array(
		"url" => "",
		"email" => "",
		"api-key" => "",
		"list" => '0 | Do not add contact to campaign',
		"list-id" => "",
		"tags" => "",
		"fields" => array(
			'email' => '',
			'first_name' => '',
			'last_name' => '',
			'title' => '',
			'company' => '',
			'address' => '',
			'city' => '',
			'country' => '',
			'state' => '',
			'zip' => '',
			'phone' => '',
			'website' => ''
		),
		"custom-names" => array(),
		"custom-values" => array()
	);
	var $fields_meta;
	function __construct() {
		$this->fields_meta = array(
			'email' => array('title' => 'E-mail', 'description' => 'E-mail address of contact/recipient.'),
			'first_name' => array('title' => 'First name', 'description' => 'First name of the contact.'),
			'last_name' => array('title' => 'Last name', 'description' => 'Last name of the contact.'),
			'title' => array('title' => 'Title', 'description' => 'Title of the contact (Mr., Mrs, Miss, etc.).'),
			'company' => array('title' => 'Company', 'description' => 'Organization name the contact works for.'),
			'address' => array('title' => 'Address', 'description' => 'Address of the contact.'),
			'city' => array('title' => 'City', 'description' => 'City of the contact.'),
			'country' => array('title' => 'Country', 'description' => 'Country of the contact.'),
			'state' => array('title' => 'State', 'description' => 'State or province of the contact.'),
			'zip' => array('title' => 'Postal code', 'description' => 'ZIP or postal code of the contact.'),
			'phone' => array('title' => 'Phone #', 'description' => 'Phone number of the contact.'),
			'website' => array('title' => 'Website URL', 'description' => 'Website URL of the contact.')
		);
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-agilecrm-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-agilecrm-list', array(&$this, "admin_lists"));
			add_action('wp_ajax_lepopup-agilecrm-fields', array(&$this, "admin_fields_html"));
			add_action('wp_ajax_lepopup-agilecrm-groups', array(&$this, "admin_groups_html"));
		}
		add_filter('lepopup_integrations_do_agilecrm', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("agilecrm", $_providers)) $_providers["agilecrm"] = esc_html__('AgileCRM', 'lepopup');
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
					<label>'.esc_html__('Site URL', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter unique website address of your account. Usually it looks like', 'lepopup').' <code>https://SITE-NAME.agilecrm.com</code></div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="url" value="'.esc_html($data['url']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Email', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter email address of your AgileCRM account (i.e. email address that you used to create AgileCRM account).', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="email" value="'.esc_html($data['email']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('API Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your AgileCRM REST API Key. Find it in your AgileCRM account, click "Admin Settings" and "Developers & API".', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Campaign ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select desired Campaign ID.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-group lepopup-integrations-ajax-options">
						<input type="text" name="list" value="'.esc_html($data['list']).'" data-deps="url,email,api-key" readonly="readonly" />
						<input type="hidden" name="list-id" value="'.esc_html($data['list-id']).'" />
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('System Properties', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to AgileCRM system properties.', 'lepopup').'</div>
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
								<td style="width: 32px;"></td>
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
					<div class="lepopup-tooltip-content">'.esc_html__('Configure AgileCRM custom properties and map form fields to them.', 'lepopup').'</div>
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
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Tags', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('If you want to tag contact with tags, drop them here (comma-separated string).', 'lepopup').'</div>
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
		$lists = array(esc_html__('Do not add contact to campaign', 'lepopup'));
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;
			
			if (!is_array($deps) || !array_key_exists('url', $deps) || empty($deps['url']) || !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $deps['url']) || !array_key_exists('email', $deps) || empty($deps['email']) || !array_key_exists('api-key', $deps) || empty($deps['api-key'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			
			$result = $this->connect($deps['url'], $deps['email'], $deps['api-key'], 'dev/api/workflows');
			if (is_array($result)) {
				if ($result['http_code'] != 200) {
					$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid response from AgileCRM.', 'lepopup'));
					echo json_encode($return_object);
					exit;
				}
				foreach($result['result'] as $list) {
					if (is_array($list)) {
						if (array_key_exists('id', $list) && array_key_exists('name', $list)) {
							$lists[$list['id']] = $list['name'];
						}
					}
				}
			} else {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Can not connect to AgileCRM Site URL.', 'lepopup'));
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
		if (empty($data['api-key']) || empty($data['url']) || !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $data['url']) || empty($data['email'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$post_data = array(
			'properties' => array(array(
				'type' => 'SYSTEM',
				'name' => 'email',
				'value' => $data['fields']['email']
			))
		);
		$address = array();
		foreach ($data['fields'] as $key => $value) {
			if (!empty($value) && $key != 'email') {
				if ($key == 'address' || $key == 'city' || $key == 'state' || $key == 'country' || $key == 'zip') {
					$address[$key] = $data['fields'][$key];
				} else {
					$post_data['properties'][] = array(
						'type' => 'SYSTEM',
						'name' => $key,
						'value' => $data['fields'][$key]
					);
				}
			}
		}
		if (!empty($address)) {
			$post_data['properties'][] = array(
				'type' => 'SYSTEM',
				'name' => 'address',
				'value' => json_encode($address)
			);
		}
		if (!empty($data['custom-names'])) {
			foreach($data['custom-names'] as $key => $name) {
				if (!empty($name) && !empty($data['custom-values'][$key])) {
					$post_data['properties'][] = array(
						'type' => 'CUSTOM',
						'name' => $name,
						'value' => $data['custom-values'][$key]
					);
				}
			}
		}
		$tags_raw = explode(',', $data['tags']);
		$tags = array();
		foreach ($tags_raw as $tag) {
			$tag = trim($tag);
			if (!empty($tag)) $tags[] = $tag;
		}
		
		$result = $this->connect($data['url'], $data['email'], $data['api-key'], 'dev/api/contacts/search/email/'.$data['fields']['email']);
		if ($result['http_code'] == 200) {
			$contact_id = $result['result']['id'];
			$post_data['id'] = $contact_id;
			$result = $this->connect($data['url'], $data['email'], $data['api-key'], 'dev/api/contacts/edit-properties', $post_data, 'PUT');
			if (!empty($tags)) {
				$tags_data = array(
					'id' => $contact_id,
					'tags' => $tags
				);
				$result = $this->connect($data['url'], $data['email'], $data['api-key'], 'dev/api/contacts/edit/tags', $tags_data, 'PUT');
			}
		} else {
			if (!empty($tags)) $post_data['tags'] = $tags;
			$result = $this->connect($data['url'], $data['email'], $data['api-key'], 'dev/api/contacts', $post_data);
		}
		if (!empty($data['list-id'])) {
			$post_data = array(
				'email' => $data['fields']['email'],
				'workflow-id' => $data['list-id']
			);
			$result = $this->connect($data['url'], $data['email'], $data['api-key'], 'dev/api/campaigns/enroll/email', $post_data, 'POST', 'application/x-www-form-urlencoded');
		}
		return $_result;
	}
	
	function connect($_url, $_email, $_api_key, $_path, $_data = array(), $_method = '', $_content_type = 'application/json') {
		$url = rtrim($_url, '/').'/'.ltrim($_path, '/');
		$headers = array(
			'Accept: application/json',
			'Content-Type: '.$_content_type
		);
		if ($_content_type == 'application/json') $post_data = json_encode($_data);
		else $post_data = http_build_query($_data);
		
		try {
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_USERPWD, $_email.':'.$_api_key);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_UNRESTRICTED_AUTH, true);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
			}
			if (!empty($_method)) {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_method);
			}
			curl_setopt($curl, CURLOPT_TIMEOUT, 20);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($curl);
			$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			$response = preg_replace('/"id":(\d+)/', '"id":"$1"', $response);
			$result = json_decode($response, true);
		} catch (Exception $e) {
			return false;
		}
		return array('http_code' => $http_code, 'result' => $result);
	}
}
$lepopup_agilecrm = new lepopup_agilecrm_class();
?>