<?php
/* ActiveCampaign integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_activecampaign_class {
	var $default_parameters = array(
		"api-url" => "",
		"api-key" => "",
		"list" => "",
		"list-id" => "",
		"fields" => array('email' => '', 'first_name' => '', 'last_name' => '', 'phone' => '', 'orgname' => ''),
		"tags" => ""
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-activecampaign-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-activecampaign-list', array(&$this, "admin_lists"));
			add_action('wp_ajax_lepopup-activecampaign-fields', array(&$this, "admin_fields_html"));
			add_action('wp_ajax_lepopup-activecampaign-groups', array(&$this, "admin_groups_html"));
		}
		add_filter('lepopup_integrations_do_activecampaign', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("activecampaign", $_providers)) $_providers["activecampaign"] = esc_html__('ActiveCampaign', 'lepopup');
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
					<label>'.esc_html__('API URL', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your ActiveCampaign API URL. To get API URL please go to your ActiveCampaign Account >> Settings >> Developer.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-url" value="'.esc_html($data['api-url']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('API Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your ActiveCampaign API Key. To get API Key please go to your ActiveCampaign Account >> Settings >> Developer.', 'lepopup').'</div>
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
						<input type="text" name="list" value="'.esc_html($data['list']).'" data-deps="api-url,api-key" readonly="readonly" />
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
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to ActiveCampaign fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>
							<tr>
								<th>'.esc_html__('Email', 'lepopup').'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[email]" value="'.esc_html(array_key_exists('email', $data['fields']) ? $data['fields']['email'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Email Address', 'lepopup').'</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('First name', 'lepopup').'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[first_name]" value="'.esc_html(array_key_exists('first_name', $data['fields']) ? $data['fields']['first_name'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('First name', 'lepopup').'</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('Last name', 'lepopup').'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[last_name]" value="'.esc_html(array_key_exists('last_name', $data['fields']) ? $data['fields']['last_name'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Last name', 'lepopup').'</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('Phone number', 'lepopup').'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[phone]" value="'.esc_html(array_key_exists('phone', $data['fields']) ? $data['fields']['phone'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Phone number', 'lepopup').'</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('Organization', 'lepopup').'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[orgname]" value="'.esc_html(array_key_exists('orgname', $data['fields']) ? $data['fields']['orgname'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Organization name. Must have CRM feature for this.', 'lepopup').'</label>
								</td>
							</tr>
						</table>
					</div>
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['api-url']) && !empty($data['api-key']) && !empty($data['list-id'])) {
				$fields_data = $this->get_fields_html($data['api-url'], $data['api-key'], $data['list-id'], $data['fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="fields" data-deps="api-url,api-key,list-id"><i class="fas fa-download"></i><label>'.esc_html__('Load Fields', 'lepopup').'</label></a>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Tags', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter comma-separated list of tags.', 'lepopup').'</div>
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

			if (!is_array($deps) || !array_key_exists('api-url', $deps) || empty($deps['api-url']) || !array_key_exists('api-key', $deps) || empty($deps['api-key'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}

			$result = $this->connect($deps['api-url'], $deps['api-key'], 'admin/api.php?api_action=list_list&ids=all');
			if(!$result || !is_array($result) || !array_key_exists('result_code', $result)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid server response.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if ($result['result_code'] != 1) {
				$return_object = array('status' => 'ERROR', 'message' => $result['result_message']);
				echo json_encode($return_object);
				exit;
			}
			$lists = array();
			foreach ($result as $key => $value) {
				if (is_array($value)) $lists[$value['id']] = $value['name'];
			}
			if(empty($lists)) {
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
			if (!is_array($deps) || !array_key_exists('api-url', $deps) || empty($deps['api-url']) || !array_key_exists('api-key', $deps) || empty($deps['api-key']) || !array_key_exists('list-id', $deps) || empty($deps['list-id'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Credentials or List ID.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_fields_html($deps['api-url'], $deps['api-key'], $deps['list-id'], $this->default_parameters['fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_api_url, $_api_key, $_list, $_fields) {
		global $wpdb, $lepopup;
		$result = $this->connect($_api_url, $_api_key, 'admin/api.php?api_action=list_field_view&ids=all');
		
		if(!$result || !is_array($result) || !array_key_exists('result_code', $result)) {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid server response.', 'lepopup'));
		}
		if ($result['result_code'] != 1) {
			return array('status' => 'ERROR', 'message' => $result['result_message']);
		}

		$fields_html = '
			<table>';
		foreach ($result as $field) {
			if (is_array($field)) {
				if (array_key_exists('id', $field) && array_key_exists('title', $field)) {
					$fields_html .= '
				<tr>
					<th>'.esc_html($field['title']).'</td>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="fields['.esc_html($field['id']).']" value="'.esc_html(array_key_exists($field['id'], $_fields) ? $_fields[$field['id']] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<label class="lepopup-integrations-description">'.esc_html($field['title'].' (ID: '.esc_html($field['id']).')').'</label>
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
		if (empty($data['api-url']) || empty($data['api-key']) || empty($data['list-id'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$post_data = array(
			'api_action' => 'contact_add',
			'p['.$data['list-id'].']' => $data['list-id'],
			'email' => $data['fields']['email'],
			'ip4' => $_SERVER['REMOTE_ADDR']
		);
		if (array_key_exists('first_name', $data['fields']) && !empty($data['fields']['first_name'])) $post_data['first_name'] = $data['fields']['first_name'];
		if (array_key_exists('last_name', $data['fields']) && !empty($data['fields']['last_name'])) $post_data['last_name'] = $data['fields']['last_name'];
		if (array_key_exists('phone', $data['fields']) && !empty($data['fields']['phone'])) $post_data['phone'] = $data['fields']['phone'];
		if (array_key_exists('orgname', $data['fields']) && !empty($data['fields']['orgname'])) $post_data['orgname'] = $data['fields']['orgname'];
		
		$tags_original = explode(',', $data['tags']);
		$tags = array();
		foreach ($tags_original as $tag) {
			$tag = trim($tag);
			if (!empty($tag)) $tags[] = $tag;
		}
		if (!empty($tags)) $post_data['tags'] = implode(', ', $tags);
		$static_fields = array_keys($this->default_parameters['fields']);
		foreach ($data['fields'] as $key => $value) {
			if (!in_array($key, $static_fields) && !empty($value)) {
				$post_data['field['.$key.',0]'] = $value;
			}
		}
	
		
		$result = $this->connect($data['api-url'], $data['api-key'], 'admin/api.php', $post_data);
		if (is_array($result) && array_key_exists('result_code', $result) && $result['result_code'] == 0) {
			$post_data['api_action'] = 'contact_edit';
			$post_data['overwrite'] = 0;
			$post_data['id'] = $result[0]['id'];
			if (isset($post_data['tags'])) unset($post_data['tags']);
			$result = $this->connect($data['api-url'], $data['api-key'], 'admin/api.php', $post_data);
			if (!empty($tags)) {
				$post_data = array(
					'api_action' => 'contact_tag_add',
					'email' => $data['fields']['email'],
					'tags' => (sizeof($tags) == 1 ? $tags[0] : $tags)
				);
				$result = $this->connect($data['api-url'], $data['api-key'], 'admin/api.php', $post_data);
			}
		}
		return $_result;
	}
	
	function connect($_api_url, $_api_key, $_path, $_data = null) {
		$url = rtrim($_api_url, '/').'/'.rtrim($_path, '/');
		if (empty($data)) {
			$url .= (strpos($url, '?') === false ? '?' : '&').'api_key='.urlencode($_api_key).'&api_output=json';
		} else {
			$data['api_key'] = $_api_key;
			$data['api_output'] = 'json';
		}
		try {
			$curl = curl_init($url);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($_data));
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
$lepopup_activecampaign = new lepopup_activecampaign_class();
?>