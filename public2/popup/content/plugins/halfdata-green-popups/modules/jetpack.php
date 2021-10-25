<?php
/* Jetpack Subscriptions integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_jetpack_class {
	var $default_parameters = array(
		'fields' => array('email' => '')
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-jetpack-settings-html', array(&$this, "admin_settings_html"));
		}
		add_filter('lepopup_integrations_do_jetpack', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (class_exists('Jetpack_Subscriptions') && !array_key_exists("jetpack", $_providers)) $_providers["jetpack"] = esc_html__('Jetpack Subscriptions', 'lepopup');
		return $_providers;
	}
	
	function admin_settings_html() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options')) {
			if (!class_exists('Jetpack_Subscriptions')) {
				if (class_exists('Jetpack')) {
					$return_object = array('status' => 'ERROR', 'message' => esc_html__('Jetpack plugin not connected.', 'lepopup'));
					echo json_encode($return_object);
					exit;
				} else {
					$return_object = array('status' => 'ERROR', 'message' => esc_html__('Jetpack plugin not installed.', 'lepopup'));
					echo json_encode($return_object);
					exit;
				}
			}
			if (array_key_exists('data', $_REQUEST)) {
				$data = json_decode(base64_decode(trim(stripslashes($_REQUEST['data']))), true);
				if (is_array($data)) $data = array_merge($this->default_parameters, $data);
				else $data = $this->default_parameters;
			} else $data = $this->default_parameters;
			$html .= '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Jetpack Subscriptions fields.', 'lepopup').'</div>
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
									<label class="lepopup-integrations-description">'.esc_html__('Email Address', 'lepopup').'</label>
								</td>
							</tr>
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
		if (!class_exists('Jetpack_Subscriptions')) return $_result;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;
		
		$subscribe = Jetpack_Subscriptions::subscribe($data['fields']['email'], 0, false);
		return $_result;
	}
}
$lepopup_jetpack = new lepopup_jetpack_class();
?>