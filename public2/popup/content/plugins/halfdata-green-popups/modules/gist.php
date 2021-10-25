<?php
/* Gist integration for Green Popups */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_gist_class {
	var $default_parameters = array(
		"api-key" => "",
		"fields" => array(
			"email" => "",
			"name" => "",
			"phone" => ""
		),
		"custom-names" => array(),
		"custom-values" => array(),
		"tags" => ""
	);
	var $fields_meta;
	function __construct() {
		$this->fields_meta = array(
			'email' => esc_html__('Email', 'lepopup'),
			'name' => esc_html__('Name', 'lepopup'),
			'phone' => esc_html__('Phone #', 'lepopup')
		);
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-gist-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-gist-list', array(&$this, "admin_lists"));
		}
		add_filter('lepopup_integrations_do_gist', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("gist", $_providers)) $_providers["gist"] = esc_html__('Gist', 'lepopup');
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
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your API Key. Find it in Gist Dashboard >> Settings >> API & Integrations >> API Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Gist fields.', 'lepopup').'</div>
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
					<label>'.esc_html__('Custom Properties', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Configure Gist custom properties and map form fields to them. Make sure that custom property already exists in your Gist account.', 'lepopup').'</div>
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
					<div class="lepopup-tooltip-content">'.esc_html__('Comma-separated list of tags. Tags allow you to mark or label your contacts and list them using that tag.', 'lepopup').'</div>
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
	
	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['api-key'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$post_data = array(
			'email' => $data['fields']['email'],
			'last_seen_ip' => $_SERVER['REMOTE_ADDR']
		);

		foreach ($data['fields'] as $key => $value) {
			if (!empty($value) && $key != 'email') {
				$post_data[$key] = $value;
			}
		}

		$properties = array();
		if (!empty($data['custom-names'])) {
			foreach($data['custom-names'] as $key => $name) {
				if (!empty($name) && !empty($data['custom-values'][$key])) $properties[$name] = $data['custom-values'][$key];
			}
		}
		$post_data['custom_properties'] = $properties;

		$tags_sanitized = array();
		$tags_raw = explode(',', $data['tags']);
		foreach ($tags_raw as $tag_raw) {
			$tag_raw = trim($tag_raw);
			if (!empty($tag_raw)) $tags_sanitized[] = $tag_raw;
		}
		
		$post_data['tags'] = $tags_sanitized;

		$result = $this->connect($data['api-key'], 'contacts', $post_data);
		
		return $_result;
	}
	
	function connect($_api_key, $_path, $_data = array(), $_method = '') {
		$headers = array(
			'Content-Type: application/json;charset=UTF-8',
			'Accept: application/json',
			'Authorization: Bearer '.$_api_key
		);
		try {
			$url = 'https://api.getgist.com/'.ltrim($_path, '/');
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
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
$lepopup_gist = new lepopup_gist_class();
?>