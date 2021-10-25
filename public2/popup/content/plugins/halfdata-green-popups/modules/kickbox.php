<?php
/* Kickbox integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_kickbox_class {
	var $options = array(
		"api-key" => ""
	);
	
	function __construct() {
		$this->get_options();
		if (is_admin()) {
			add_filter('lepopup_email_validators', array(&$this, 'email_validators'), 10, 1);
			add_action('lepopup_email_validator_options_show', array(&$this, "admin_options_html"), 10, 1);
			add_filter('lepopup_options_check', array(&$this, 'admin_options_check'));
			add_action('lepopup_options_update', array(&$this, 'admin_options_update'));
		}
		add_filter('lepopup_validate_email_do_kickbox', array(&$this, 'validate_email'), 10, 2);
	}

	function get_options() {
		foreach ($this->options as $key => $value) {
			$this->options[$key] = get_option('lepopup-kickbox-'.$key, $this->options[$key]);
		}
	}
	function update_options() {
		if (current_user_can('manage_options')) {
			foreach ($this->options as $key => $value) {
				update_option('lepopup-kickbox-'.$key, $value);
			}
		}
	}

	function populate_options() {
		foreach ($this->options as $key => $value) {
			if (isset($_POST['lepopup-kickbox-'.$key])) {
				$this->options[$key] = trim(stripslashes($_POST['lepopup-kickbox-'.$key]));
			}
		}
	}
	
	function email_validators($_email_validators) {
		if (!array_key_exists("kickbox", $_email_validators)) $_email_validators["kickbox"] = esc_html__('Kickbox', 'lepopup');
		return $_email_validators;
	}
	
	function admin_options_html($_active_validator) {
		global $wpdb, $lepopup;
		echo '
				<tr class="lepopup-email-validator-options lepopup-email-validator-kickbox"'.($_active_validator == 'kickbox' ? ' style="display: table-row;"' : '').'>
					<th>'.esc_html__('Kickbox API Key', 'lepopup').':</th>
					<td>
						<input type="text" id="lepopup-kickbox-api-key" name="lepopup-kickbox-api-key" value="'.esc_html($this->options['api-key']).'" class="widefat" />
						<br /><em>'.sprintf(esc_html__('Please enter Kickbox API Key. You can find it in the %sAPI Keys%s.', 'lepopup'), '<a href="https://app.kickbox.com/settings/keys" target="_blank">', '</a>').'</em>
					</td>
				</tr>';
	}

	function admin_options_check($_errors) {
		global $lepopup;
		$this->populate_options();
		if ($lepopup->options['email-validator'] == 'kickbox') {
			if (empty($this->options['api-key'])) $_errors[] = esc_html__('Invalid Kickbox API Key.', 'lepopup');
		}
		return $_errors;
	}

	function admin_options_update() {
		$this->populate_options();
		$this->update_options();
	}
	
	function validate_email($_result, $_email) {
		global $wpdb, $lepopup;
		$valid = true;
		$result = $this->connect($this->options['api-key'], $_email);
		if (is_array($result) && array_key_exists('result', $result) && $result['result'] != 'deliverable' && $result['result'] != 'risky') $valid = false;
		return $_result && $valid;
	}

	function connect($_api_key, $_email) {
		try {
			$url = 'https://api.kickbox.io/v2/verify?apikey='.urlencode($_api_key).'&email='.$_email;
			$curl = curl_init($url);
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
$lepopup_kickbox = new lepopup_kickbox_class();
?>