<?php
/* Mailster integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_mailster_class {
	var $default_parameters = array(
		'list-id' => "",
		'double' => "off",
		'fields' => array('email' => '', 'firstname' => '', 'lastname' => '')
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-mailster-settings-html', array(&$this, "admin_settings_html"));
		}
		add_filter('lepopup_integrations_do_mailster', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (function_exists('mailster') && !array_key_exists("mailster", $_providers)) $_providers["mailster"] = esc_html__('Mailster', 'lepopup');
		return $_providers;
	}
	
	function admin_settings_html() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options')) {
			if (!function_exists('mailster') || !function_exists('mailster_option')) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Mailster plugin not installed.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$lists = mailster('lists')->get();
			if (empty($lists)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('No Mailster lists found.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (array_key_exists('data', $_REQUEST)) {
				$data = json_decode(base64_decode(trim(stripslashes($_REQUEST['data']))), true);
				if (is_array($data)) $data = array_merge($this->default_parameters, $data);
				else $data = $this->default_parameters;
			} else $data = $this->default_parameters;
			$checkbox_id = $lepopup->random_string();
			$html = '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('List', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select desired List.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<select type="text" name="list-id" value="'.esc_html($data['list-id']).'">';
			foreach ($lists as $list) {
				$html .= '
						<option value="'.esc_html($list->ID).'"'.($list->ID == $data['list-id'] ? ' selected="selected"' : '').'>'.esc_html($list->name).'</option>';
			}
			$html .= '
					</select>
				</div>
			</div>';
			$custom_fields = mailster_option('custom_field', array());
			$html .= '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Mailster fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>
							<tr>
								<th>'.esc_html__('Email', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[email]" value="'.(array_key_exists('email', $data['fields']) ? esc_html($data['fields']['email']) : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">{email}</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('First name', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[firstname]" value="'.(array_key_exists('firstname', $data['fields']) ? esc_html($data['fields']['firstname']) : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">{firstname}</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('Last name', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[lastname]" value="'.(array_key_exists('lastname', $data['fields']) ? esc_html($data['fields']['lastname']) : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">{lastname}</label>
								</td>
							</tr>';
			foreach ($custom_fields as $id => $cdata) {
				$html .= '
							<tr>
								<th>'.esc_html($cdata['name']).'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields['.esc_html($id).']" value="'.(array_key_exists($id, $data['fields']) ? esc_html($data['fields'][$id]) : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html('{'.$id.'}').'</label>
								</td>
							</tr>';
			}
			$html .= '
						</table>
					</div>
				</div>
			</div>';
			$html .= '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Double opt-in', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Control whether a double opt-in confirmation message is sent.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input class="lepopup-checkbox-toggle" type="checkbox" value="on" id="double-'.esc_html($checkbox_id).'" name="double"'.($data['double'] == 'on' ? ' checked="checked"' : '').' /><label for="double-'.esc_html($checkbox_id).'"></label>
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
		if (!function_exists('mailster')) return $_result;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;
		if (empty($data['list-id'])) return $_result;
		$list = mailster('lists')->get($data['list-id']);
		if (empty($list)) return $_result;
		try {
			if ($data['double'] == "on") $double = true;
			else $double = false;
			$mailster_subscriber = mailster('subscribers')->get_by_mail($data['fields']['email']);
			$entry = array(
				'firstname' => $data['fields']['firstname'],
				'lastname' => $data['fields']['lastname'],
				'email' => $data['fields']['email'],
				'ip' => $_SERVER['REMOTE_ADDR'],
				'signup_ip' => $_SERVER['REMOTE_ADDR'],
				'referer' => $_SERVER['HTTP_REFERER'],
				'signup' =>time()
			);
			if (!$mailster_subscriber || (is_object($mailster_subscriber) && $mailster_subscriber->status != 1)) $entry['status'] = $double ? 0 : 1;
			if (function_exists('mailster_option')) {
				$custom_fields = mailster_option('custom_field', array());
				foreach($custom_fields as $key => $value) {
					if (array_key_exists($key, $data['fields'])) $entry[$key] = $data['fields'][$key];
				}
			}
			$subscriber_id = mailster('subscribers')->add($entry, true);
			if (is_wp_error($subscriber_id)) return $_result;
			$result = mailster('subscribers')->assign_lists($subscriber_id, array($list->ID));
		} catch (Exception $e) {
		}
		return $_result;
	}
}
$lepopup_mailster = new lepopup_mailster_class();
?>