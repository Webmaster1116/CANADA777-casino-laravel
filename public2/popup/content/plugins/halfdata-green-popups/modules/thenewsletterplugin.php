<?php
/* The Newsletter Plugin integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_thenewsletterplugin_class {
	var $default_parameters = array(
		'lists' => array(),
		'fields' => array('email' => '', 'name' => '', 'surname' => '')
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-thenewsletterplugin-settings-html', array(&$this, "admin_settings_html"));
		}
		add_filter('lepopup_integrations_do_thenewsletterplugin', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (class_exists('Newsletter') && !array_key_exists("thenewsletterplugin", $_providers)) $_providers["thenewsletterplugin"] = esc_html__('The Newsletter Plugin', 'lepopup');
		return $_providers;
	}
	
	function admin_settings_html() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options')) {
			if (!class_exists('Newsletter')) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('The Newsletter Plugin not installed.', 'lepopup'));
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
					<label>'.esc_html__('Lists', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select desired List.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			$exists = false;
			$options_list = get_option('newsletter_subscription_lists');
			if (is_array($options_list)) {
				for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
					if (empty($options_list['list_'.$i])) continue;
					if (array_key_exists('list_'.$i, $data['lists'])) $checked = $data['lists']['list_'.$i];
					else $checked = 'off';
					$checkbox_id = $lepopup->random_string(16);
					$html .= '
					<div class="lepopup-properties-pure" style="margin: 4px 0;">
						<input class="lepopup-checkbox lepopup-checkbox-classic lepopup-checkbox-medium" id="list-'.esc_html($checkbox_id).'" type="checkbox" value="on" name="lists[list_'.$i.']"'.($checked == 'on' ? ' checked="checked"' : '').' /><label for="list-'.esc_html($checkbox_id).'"></label><label for="list-'.esc_html($checkbox_id).'">'.esc_html($options_list['list_'.$i]).'</label>
					</div>';
					$exists = true;
				}
			}
			if ($exists) $html .= '<label class="lepopup-integrations-description">'.esc_html__('Select contact lists.', 'lepopup').'</label>';
			else $html .= '<strong>'.esc_html__('No contact lists found.', 'lepopup').'</strong>';
			$html .= '
					</div>
				</div>
			</div>';
			$custom_fields = get_option('custom_field', array());
			$html .= '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to The Newsletter Plugin fields.', 'lepopup').'</div>
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
									<label class="lepopup-integrations-description">'.esc_html__('Email address of the contact.', 'lepopup').'</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('First name', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[name]" value="'.(array_key_exists('name', $data['fields']) ? esc_html($data['fields']['name']) : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('First name (or full name) of the contact.', 'lepopup').'</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('Last name', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[surname]" value="'.(array_key_exists('surname', $data['fields']) ? esc_html($data['fields']['surname']) : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Last name of the contact.', 'lepopup').'</label>
								</td>
							</tr>';
			$options_profile = get_option('newsletter_profile');
			if (is_array($options_profile)) {
				for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
					if (!array_key_exists('profile_' . $i, $options_profile) || empty($options_profile['profile_' . $i])) continue;
					$html .= '
							<tr>
								<th>'.esc_html($options_profile['profile_'.$i]).'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[profile_'.$i.']" value="'.(array_key_exists('profile_'.$i, $data['fields']) ? esc_html($data['fields']['profile_'.$i]) : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html($options_profile['profile_'.$i]).'</label>
								</td>
							</tr>';
				}
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
	
	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		if (!class_exists('Newsletter')) return $_result;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;
		try {
			$entry = array(
				'email' => $data['fields']['email'],
				'status' => 'C'
			);
			$options_feed = get_option('newsletter_feed', array());
			if (array_key_exists('add_new', $options_feed) && $options_feed['add_new'] == 1) $entry['feed'] = 1;
			$options_followup = get_option('newsletter_followup', array());
			if (array_key_exists('add_new', $options_followup) && $options_followup['add_new'] == 1) {
				$entry['followup'] = 1;
				$entry['followup_time'] = time() + $options_followup['interval'] * 3600;
			}
			foreach ($data['fields'] as $key => $value) {
				if (!empty($value) && $key != 'email') {
					$entry[$key] = $value;
				}
			}
			if (array_key_exists('lists', $data) && is_array($data['lists'])) {
				foreach($data['lists'] as $key => $value) {
					if (!empty($value) && $value == 'on') {
						$entry[$key] = 1;
					}
				}
			}
			$user = NewsletterUsers::instance()->get_user($data['fields']['email']);
			if (is_object($user) && !empty($user->id)) $entry['id'] = $user->id;
			$result = NewsletterUsers::instance()->save_user($entry);
		} catch (Exception $e) {
		}
		return $_result;
	}
}
$lepopup_thenewsletterplugin = new lepopup_thenewsletterplugin_class();
?>