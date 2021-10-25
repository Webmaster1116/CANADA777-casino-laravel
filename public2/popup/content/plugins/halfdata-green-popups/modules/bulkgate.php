<?php
/* BulkGate integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_bulkgate_class {
	var $default_parameters = array(
		"application-id" => "",
		"application-token" => "",
		"sender-id" => "gSystem",
		"sender-value" => "",
		"phone" => "",
		"message" => "",
		"unicode" => "off",
		"mode" => "transactional",
		"duplicates-check" => "off"
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-bulkgate-settings-html', array(&$this, "admin_settings_html"));
		}
		add_filter('lepopup_integrations_do_bulkgate', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("bulkgate", $_providers)) $_providers["bulkgate"] = esc_html__('BulkGate', 'lepopup');
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
					<label>'.esc_html__('Application ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your BulkGate Application ID.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="application-id" value="'.esc_html($data['application-id']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Create Simple API module in %sBulkGate Dashboard%s and take Application ID there.', 'lepopup'), '<a href="https://portal.bulkgate.com/application/" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Application Token', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your BulkGate Application Token.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="application-token" value="'.esc_html($data['application-token']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Create Simple API module in %sBulkGate Dashboard%s and take Application Token there.', 'lepopup'), '<a href="https://portal.bulkgate.com/application/" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('SMS type', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select the type of SMS: transactional or promotional.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<select name="mode">
						<option value="transactional"'.($data['mode'] == 'transactional' ? ' selected="selected"' : '').'>'.esc_html__('Transactional', 'lepopup').'</option>
						<option value="promotional"'.($data['mode'] == 'promotional' ? ' selected="selected"' : '').'>'.esc_html__('Promotional', 'lepopup').'</option>
					</select>
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find the difference between Transactional and Promotional SMS %shere%s.', 'lepopup'), '<a href="https://help.bulkgate.com/docs/en/difference-promotional-transactional-sms.html" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Sender ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Sender ID', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-input-shortcode-selector">
						<select name="sender-id" onchange="if(jQuery(this).val() == \'gSystem\'){jQuery(this).closest(\'.lepopup-integrations-content\').find(\'.lepopup-properties-sender-value\').hide();}else{jQuery(this).closest(\'.lepopup-integrations-content\').find(\'.lepopup-properties-sender-value\').show();}">
							<option value="gSystem"'.($data['sender-id'] == "gSystem" ? ' selected="selected"' : '').'>'.esc_html__('System', 'lepopup').'</option>
							<option value="gOwn"'.($data['sender-id'] == "gOwn" ? ' selected="selected"' : '').'>'.esc_html__('Own number', 'lepopup').'</option>
						</select>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item lepopup-properties-sender-value"'.($data['sender-id'] == "gSystem" ? ' style="display:none;"' : '').'>
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Sender phone number', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Your phone number in an international format (phone number must be verified).', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-input-shortcode-selector">
						<input type="text" name="sender-value" value="'.esc_html($data['sender-value']).'" />
						<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Phone number', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('The phone number in an international format (including country and area code).', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-input-shortcode-selector">
						<input type="text" name="phone" value="'.esc_html($data['phone']).'" />
						<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Unicode', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('If enabled, SMS is sent in unicode mode.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input class="lepopup-checkbox-toggle" type="checkbox" value="on" id="unicode-'.esc_html($checkbox_id).'" name="unicode"'.($data['unicode'] == 'on' ? ' checked="checked"' : '').' /><label for="unicode-'.esc_html($checkbox_id).'"></label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Message', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Text of SMS message (max. 612 characters, or 268 characters if Unicode is used).', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-input-shortcode-selector">
						<textarea name="message">'.esc_html($data['message']).'</textarea>
						<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Check duplicates', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Prevent sending duplicate messages to the same phone number. Messages with the same text sent to the same number will be removed if there is a time interval shorter than 5 mins.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input class="lepopup-checkbox-toggle" type="checkbox" value="on" id="duplicates-check-'.esc_html($checkbox_id).'" name="duplicates-check"'.($data['duplicates-check'] == 'on' ? ' checked="checked"' : '').' /><label for="duplicates-check-'.esc_html($checkbox_id).'"></label>
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
		if (empty($data['application-id']) || empty($data['application-token'])) return $_result;
		if (empty($data['phone']) || empty($data['message'])) return $_result;

		$headers = array(
			'Content-Type: application/json;charset=UTF-8',
			'Accept: application/json'
		);
		$post_data = array(
			'application_id' => $data['application-id'],
			'application_token' => $data['application-token'],
			'number' => $data['phone'],
			'text' => $data['message'],
			'unicode' => $data['unicode'] == 'on' ? true : false,
			'duplicates_check' => $data['duplicates-check'] == 'on' ? true : false
		);
		if ($data['sender-id'] == 'gOwn' && !empty($data['sender-value'])) {
			$post_data['sender_id'] = 'gOwn';
			$post_data['sender_id_value'] = $data['sender-value'];
		}
		try {
			$url = 'https://portal.bulkgate.com/api/1.0/simple/'.($data['mode'] == 'transactional' ? 'transactional' : 'promotional');
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));
			curl_setopt($curl, CURLOPT_TIMEOUT, 20);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($curl);
			curl_close($curl);
		} catch (Exception $e) {
		}
		return $_result;
	}
}
$lepopup_bulkgate = new lepopup_bulkgate_class();
?>