<?php
/* MailPoet integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_mailpoet_class {
	var $default_parameters = array(
		'list-id' => "",
		'fields' => array('email' => '', 'firstname' => '', 'lastname' => '')
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-mailpoet-settings-html', array(&$this, "admin_settings_html"));
		}
		add_filter('lepopup_integrations_do_mailpoet', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (class_exists('\MailPoet\API\API') && !array_key_exists("mailpoet", $_providers)) $_providers["mailpoet"] = esc_html__('MailPoet', 'lepopup');
		return $_providers;
	}
	
	function admin_settings_html() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options')) {
			if (!class_exists('\MailPoet\API\API')) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('MailPoet plugin not installed.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$mailpoet_lists = \MailPoet\API\API::MP('v1')->getLists();
			if (sizeof($mailpoet_lists) == 0) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('No MailPoet lists fount.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (array_key_exists('data', $_REQUEST)) {
				$data = json_decode(base64_decode(trim(stripslashes($_REQUEST['data']))), true);
				if (is_array($data)) $data = array_merge($this->default_parameters, $data);
				else $data = $this->default_parameters;
			} else $data = $this->default_parameters;
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
			foreach ($mailpoet_lists as $list) {
				$html .= '
						<option value="'.esc_html($list['id']).'"'.($list['id'] == $data['list-id'] ? ' selected="selected"' : '').'>'.esc_html($list['name']).'</option>';
			}
			$html .= '
					</select>
				</div>
			</div>';
			$subscriber_fields = \MailPoet\API\API::MP('v1')->getSubscriberFields();
			$html .= '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to MailPoet fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>';
			foreach ($subscriber_fields as $custom_field) {
				$html .= '
							<tr>
								<th>'.esc_html($custom_field['name']).'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields['.esc_html($custom_field['id']).']" value="'.(array_key_exists($custom_field['id'], $data['fields']) ? esc_html($data['fields'][$custom_field['id']]) : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html($custom_field['name']).'</label>
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
		if (!class_exists('\MailPoet\API\API')) return $_result;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (!array_key_exists('email', $data['fields']) || empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;
		if (empty($data['list-id'])) return $_result;
		try {
			$subscriber = \MailPoet\API\API::MP('v1')->getSubscriber($data['fields']['email']);
			$subscriber = \MailPoet\API\API::MP('v1')->subscribeToLists($subscriber['id'], array($data['list-id']), array());
		} catch (Exception $e) {
			try {
				$subscriber = \MailPoet\API\API::MP('v1')->addSubscriber($data['fields'], array($data['list-id']), array());
			} catch (Exception $e) {
			}
		}
		return $_result;
	}
}
$lepopup_mailpoet = new lepopup_mailpoet_class();
?>