<?php
/* Twilio integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_twilio_class {
	var $default_parameters = array(
		"account-sid" => "",
		"auth-token" => "",
		"to" => "",
		"from" => "",
		"body" => ""
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-twilio-settings-html', array(&$this, "admin_settings_html"));
		}
		add_filter('lepopup_integrations_do_twilio', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("twilio", $_providers)) $_providers["twilio"] = esc_html__('Twilio', 'lepopup');
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
					<label>'.esc_html__('Account SID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Twilio Account SID.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="account-sid" value="'.esc_html($data['account-sid']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('You can find your Account SID in the %sTwilio Console%s.', 'lepopup'), '<a href="https://www.twilio.com/console" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Auth Token', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Twilio Auth Token.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="auth-token" value="'.esc_html($data['auth-token']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('You can find your Auth Token in the %sTwilio Console%s.', 'lepopup'), '<a href="https://www.twilio.com/console" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('To', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('This parameter determines the destination phone number for your SMS message. Format this number with a "+" and a country code, e.g., +16175551212.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-input-shortcode-selector">
						<input type="text" name="to" value="'.esc_html($data['to']).'" />
						<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('From', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('From specifies the Twilio phone number or short code that sends this message. This must be a Twilio phone number that you own, formatted with a "+" and country code, e.g. +16175551212. To get your first Twilio phone number, head on over to the console and find a number you like with SMS capabilities. If you are interested in using a short code, you can apply for one via the console as well. Note that you cannot spoof messages from your personal cell phone number without porting your number to Twilio first.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-input-shortcode-selector">
						<input type="text" name="from" value="'.esc_html($data['from']).'" />
						<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Message', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('This is the full text of the message you want to send, limited to 1600 characters. If the body of your message is more than 160 GSM-7 characters (or 70 UCS-2 characters), Twilio will send the message as a segmented SMS.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-input-shortcode-selector">
						<textarea name="body">'.esc_html($data['body']).'</textarea>
						<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
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
	
	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['account-sid']) || empty($data['auth-token'])) return $_result;
		if (empty($data['to']) || empty($data['from']) || empty($data['body'])) return $_result;

		$post_data = array(
			'Body' => $data['body'],
			'From' => '+'.preg_replace('/[^0-9]/', '', $data['from']),
			'To' => '+'.preg_replace('/[^0-9]/', '', $data['to'])
		);
		try {
			$url = 'https://api.twilio.com/2010-04-01/Accounts/'.urlencode($data['account-sid']).'/Messages.json';
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, $data['account-sid'].':'.$data['auth-token']);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
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
$lepopup_twilio = new lepopup_twilio_class();
?>