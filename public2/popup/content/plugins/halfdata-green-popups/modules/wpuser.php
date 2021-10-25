<?php
/* Create / update WordPress user. */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_wpuser_class {
	var $default_parameters = array(
		'role' => 'subscriber',
		'fields' => array(
			'user_email' => '',
			'user_pass' => '',
			'first_name' => '',
			'last_name' => '',
			'user_url' => ''
		),
		'notification' => 'on',
		'allow-update' => 'off'
	);
	var $field_labels = array(
		'user_email' => array('title' => 'Email', 'description' => 'E-mail address of the user.'),
		'user_pass' => array('title' => 'Password', 'description' => 'Password of the user.'),
		'first_name' => array('title' => 'First name', 'description' => 'First name of the user.'),
		'last_name' => array('title' => 'Last name', 'description' => 'Last name of the user.'),
		'user_url' => array('title' => 'Website URL', 'description' => 'Website URL of the user.')
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-wpuser-settings-html', array(&$this, "admin_settings_html"));
		}
		add_filter('lepopup_integrations_do_wpuser', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("wpuser", $_providers)) $_providers["wpuser"] = esc_html__('WP User (create)', 'lepopup');
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
					<label>'.esc_html__('User role', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select the role for the newly created user.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<select type="text" name="role" value="'.esc_html($data['role']).'">';
			$roles = get_editable_roles();
			foreach ($roles as $key => $value) {
				$html .= '
						<option'.($data['role'] == $key ? ' selected="selected"' : '').' value="'.esc_html($key).'">'.esc_html($value['name']).'</option>';
			}
			$html .= '
					</select>
				</div>
			</div>';
			$html .= '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to WP User fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>';
			foreach ($this->default_parameters['fields'] as $key => $value) {
				$html .= '
							<tr>
								<th>'.esc_html($this->field_labels[$key]['title']).'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields['.esc_html($key).']" value="'.(array_key_exists($key, $data['fields']) ? esc_html($data['fields'][$key]) : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html($this->field_labels[$key]['description']).'</label>
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
					<label>'.esc_html__('User notification', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Email login credentials to a newly-registered user (standard WP message). This option should be enabled if you did not request a password from the user.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input class="lepopup-checkbox-toggle" type="checkbox" value="on" id="notification-'.esc_html($checkbox_id).'" name="notification"'.($data['notification'] == 'on' ? ' checked="checked"' : '').' /><label for="notification-'.esc_html($checkbox_id).'"></label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Allow updates', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Update user data for already existing users. Existing user must have the same user role.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input class="lepopup-checkbox-toggle" type="checkbox" value="on" id="allow-update-'.esc_html($checkbox_id).'" name="allow-update"'.($data['allow-update'] == 'on' ? ' checked="checked"' : '').' /><label for="allow-update-'.esc_html($checkbox_id).'"></label>
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
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		$data['fields']['user_pass'] = trim($data['fields']['user_pass']);
		if (empty($data['fields']['user_pass']) && $data['notification'] != 'on') return $_result;
		if (empty($data['fields']['user_email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['user_email'])) return $_result;
		
		$post_data = array(
			'role' => $data['role'],
			'user_email' => $data['fields']['user_email'],
			'user_login' => $data['fields']['user_email']
		);
		foreach ($data['fields'] as $key => $value) {
			if (!empty($value) && $key != 'user_email' && $key != 'user_login' && $key != 'user_pass') {
				$post_data[$key] = $value;
			}
		}
		$user_id = username_exists($data['fields']['user_email']);
		if (!$user_id) $user_id = email_exists($data['fields']['user_email']);
		if (!$user_id) {
			if (empty($data['fields']['user_pass'])) $post_data['user_pass'] = wp_generate_password();
			else $post_data['user_pass'] = $data['fields']['user_pass'];
			$user_id = wp_insert_user($post_data);
			if (!is_wp_error($user_id)) {
				if ($data['notification'] == 'on') {
					wp_new_user_notification($user_id, null, 'both');
				}
			}
		} else {
			if ($data['allow-update'] == 'on') {
				$user = get_userdata($user_id);
				if ($user) {
					if (in_array($data['role'], $user->roles)) {
						$post_data['ID'] = $user_id;
						wp_update_user($post_data);
					}
				}
			}
		}
		return $_result;
	}
}
$lepopup_wpuser = new lepopup_wpuser_class();

/* Login WordPress user. */
class lepopup_wplogin_class {
	var $default_parameters = array(
		'fields' => array(
			'user_login' => '',
			'user_pass' => '',
			'remember' => ''
		)
	);
	var $field_labels = array(
		'user_login' => array('title' => 'Login', 'description' => 'Login of the user.'),
		'user_pass' => array('title' => 'Password', 'description' => 'Password of the user.'),
		'remember' => array('title' => 'Remember me', 'description' => 'Remember me. Any non-empty value is considered as "checked".')
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-wplogin-settings-html', array(&$this, "admin_settings_html"));
		}
		add_filter('lepopup_integrations_do_wplogin', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("wplogin", $_providers)) $_providers["wplogin"] = esc_html__('WP User (login)', 'lepopup');
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
			$html = '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to WP User fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>';
			foreach ($this->default_parameters['fields'] as $key => $value) {
				$html .= '
							<tr>
								<th>'.esc_html($this->field_labels[$key]['title']).'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields['.esc_html($key).']" value="'.(array_key_exists($key, $data['fields']) ? esc_html($data['fields'][$key]) : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html($this->field_labels[$key]['description']).'</label>
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
	
	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		$data['fields']['user_pass'] = trim($data['fields']['user_pass']);
		if (empty($data['fields']['user_login']) || empty($data['fields']['user_pass'])) return $_result;
		$credentials = array(
			'user_login' => $data['fields']['user_login'],
			'user_password' => $data['fields']['user_pass'],
			'remember' => !empty($data['fields']['remember'])
		);
		$user = wp_signon($credentials, true);
		return $_result;
	}
}
$lepopup_wplogin = new lepopup_wplogin_class();
?>