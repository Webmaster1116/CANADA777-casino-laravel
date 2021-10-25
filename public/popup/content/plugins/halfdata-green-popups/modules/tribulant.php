<?php
/* Tribulant Newsletters integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_tribulant_class {
	var $default_parameters = array(
		'list-id' => "",
		'fields' => array('email' => '')
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-tribulant-settings-html', array(&$this, "admin_settings_html"));
		}
		add_filter('lepopup_integrations_do_tribulant', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (class_exists('wpMailPlugin') && function_exists('wpml_get_mailinglists') && !array_key_exists("tribulant", $_providers)) $_providers["tribulant"] = esc_html__('Tribulant Newsletters', 'lepopup');
		return $_providers;
	}
	
	function admin_settings_html() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options')) {
			if (!class_exists('wpMailPlugin') || !function_exists('wpml_get_mailinglists')) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Tribulant Newsletters plugin not installed.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$lists = wpml_get_mailinglists();
			if (empty($lists)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('No Tribulant Newsletters lists found.', 'lepopup'));
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
			foreach ($lists as $list) {
				$html .= '
						<option value="'.esc_html($list->id).'"'.($list->id == $data['list-id'] ? ' selected="selected"' : '').'>'.esc_html($list->title).'</option>';
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
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Tribulant Newsletters fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>';
			if (function_exists('wpml_get_fields')) {
				$custom_fields = wpml_get_fields();
				if (!empty($custom_fields) && !(sizeof($custom_fields) == 1 && $custom_fields[0]->slug == 'list')) {				
					foreach ($custom_fields as $custom_field) {
						if ($custom_field->slug != 'list') {
							$html .= '
							<tr>
								<th>'.esc_html($custom_field->title).'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields['.esc_html($custom_field->slug).']" value="'.(array_key_exists($custom_field->slug, $data['fields']) ? esc_html($data['fields'][$custom_field->slug]) : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html($custom_field->title.' ('.$custom_field->slug.')').'</label>
								</td>
							</tr>';
						}
					}
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
		global $wpdb, $lepopup, $Subscriber;
		if (!class_exists('wpMailPlugin')) return $_result;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;
		if (empty($data['list-id'])) return $_result;
		try {
			$user_data['Subscriber'] = array(
				'active' => 'Y',
				'email' => $data['fields']['email'],
				'mandatory' => 'N',
				'mailinglists' => array($data['list-id']));
			if (function_exists('wpml_get_fields')) {
				$custom_fields = wpml_get_fields();
				foreach($custom_fields as $custom_field) {
					if (!in_array($custom_field->slug, array('email', 'list'))) {
						if (array_key_exists($custom_field->slug, $data['fields'])) $user_data['Subscriber'][$custom_field->slug] = $data['fields'][$custom_field->slug];
					}
				}
			}
			if (!$Subscriber->save($user_data)) {
				if (array_key_exists('Subscriber', $Subscriber->data)) {
					if ($Subscriber->data['Subscriber']->id) {
						$user_data['Subscriber']['id'] = $Subscriber->data['Subscriber']->id;
						$user_data['Subscriber']['mailinglists'] = $Subscriber->data['Subscriber']->mailinglists;
						$Subscriber->save($user_data);
					}
				}
			}
		} catch (Exception $e) {
		}
		return $_result;
	}
}
$lepopup_tribulant = new lepopup_tribulant_class();
?>