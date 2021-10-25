<?php
/* Zapier integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_zapier_class {
	var $default_parameters = array(
		"webhook-url" => "",
		"custom-names" => array(),
		"custom-values" => array()
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-zapier-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-zapier-connect', array(&$this, "admin_send_sample"));
		}
		add_filter('lepopup_integrations_do_zapier', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("zapier", $_providers)) $_providers["zapier"] = esc_html__('Zapier', 'lepopup');
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
			<div class="lepopup-integrations-important">
				'.sprintf(esc_html__('Please create a new Zap on %sMy Zaps%s page. While creating new Zap choose "Webhooks" as a Trigger App and select "Catch Hook" as a Trigger.', 'lepopup'), '<a href="https://zapier.com/app/zaps" target="_blank">', '</a>').'
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Webhook URL', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Zapier has generated a custom webhook URL for you to send requests to.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="webhook-url" value="'.esc_html($data['webhook-url']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Create Zapier fields and map form fields to them.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline lepopup-integrations-custom" data-names="custom-names" data-values="custom-values" data-all="on">
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
									<a class="lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small" href="#" onclick="return lepopup_integrations_custom_add(this);"><i class="fas fa-plus"></i><label>'.esc_html__('Add Field', 'lepopup').'</label></a>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label></label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Click the button to send a request to the Webhook URL so Zapier can pull it in as a sample to set up your zap.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_zapier_connect(this);"><i class="far fa-paper-plane"></i><label>'.esc_html__('Send Sample Request', 'lepopup').'</label></a>
				</div>
			</div>';
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = $html;
			echo json_encode($return_object);
		}
		exit;
	}
	
	function admin_send_sample() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (array_key_exists('webhook-url', $_REQUEST)) {
				$webhook_url = base64_decode(trim(stripslashes($_REQUEST['webhook-url'])));
			} else $webhook_url = null;
			if (array_key_exists('fields', $_REQUEST)) {
				$fields = json_decode(base64_decode(trim(stripslashes($_REQUEST['fields']))), true);
				if (!is_array($fields)) $fields = null;
			} else $fields = null;
			if (empty($webhook_url) || substr($webhook_url, 0, strlen('https://hooks.zapier.com/hooks/catch/')) != 'https://hooks.zapier.com/hooks/catch/') {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid Webhook URL.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (empty($fields)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Form does not have sufficient fields.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$post_data = array();
			foreach ($fields as $key) {
				$post_data[$key] = $key;
			}
			$result = $this->connect($webhook_url, $post_data);
			if (empty($result) || !is_array($result) || !array_key_exists('status', $result)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Can not connect to Zapier.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = array('status' => 'OK', 'message' => esc_html__('Sample request successfully sent.', 'lepopup'));
			echo json_encode($return_object);
		}
		exit;
	}
	
	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['webhook-url']) || substr($data['webhook-url'], 0, strlen('https://hooks.zapier.com/hooks/catch/')) != 'https://hooks.zapier.com/hooks/catch/') return $_result;
		if (empty($data['custom-names']) || !is_array($data['custom-names']) || empty($data['custom-values']) || !is_array($data['custom-values'])) return $_result;

		$post_data = array();
		if (!empty($data['custom-names'])) {
			foreach($data['custom-names'] as $key => $name) {
				if (!empty($name)) {
					$post_data[$name] = $data['custom-values'][$key];
				}
			}
		}
		$result = $this->connect($data['webhook-url'], $post_data);
		return $_result;
	}
	
	function connect($_url, $_data = array()) {
		$headers = array(
			'Content-Type: application/json;charset=UTF-8',
			'Accept: application/json'
		);
		try {
			$curl = curl_init($_url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($_data));
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
$lepopup_zapier = new lepopup_zapier_class();
?>