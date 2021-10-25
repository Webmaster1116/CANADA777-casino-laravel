<?php
/* FluentCRM integration for Green Popups */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_fluentcrm_class {
	var $default_parameters = array(
		'lists' => array(),
		'tags' => array(),
		'fields' => array(
			'email' => '',
			'prefix' => '',
			'first_name' => '',
			'last_name' => '',
			'date_of_birth' => '',
			'address_line_1' => '',
			'address_line_2' => '',
			'city' => '',
			'country' => '',
			'state' => '',
			'postal_code' => '',
			'phone' => ''
		)
	);
	var $fields_meta;
	
	function __construct() {
		$this->fields_meta = array(
			'email' => array('title' => 'E-mail', 'description' => 'E-mail address of contact/recipient.'),
			'prefix' => array('title' => 'Prefix', 'description' => 'Prefix of the contact (Mr., Mrs, Miss, etc.).'),
			'first_name' => array('title' => 'First name', 'description' => 'First name of the contact.'),
			'last_name' => array('title' => 'Last name', 'description' => 'Last name of the contact.'),
			'address_line_1' => array('title' => 'Address 1', 'description' => 'Address line 1 of the contact.'),
			'address_line_2' => array('title' => 'Address 2', 'description' => 'Address line 2 of the contact.'),
			'city' => array('title' => 'City', 'description' => 'City of the contact.'),
			'country' => array('title' => 'Country', 'description' => 'Country of the contact.'),
			'state' => array('title' => 'State', 'description' => 'State or province of the contact.'),
			'postal_code' => array('title' => 'Postal code', 'description' => 'ZIP or postal code of the contact.'),
			'phone' => array('title' => 'Phone #', 'description' => 'Phone number of the contact.'),
			'date_of_birth' => array('title' => 'Birthdate', 'description' => 'Date of birth of the contact.')
		);
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-fluentcrm-settings-html', array(&$this, "admin_settings_html"));
		}
		add_filter('lepopup_integrations_do_fluentcrm', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (function_exists('FluentCrmApi') && !array_key_exists("fluentcrm", $_providers)) $_providers["fluentcrm"] = esc_html__('FluentCRM', 'lepopup');
		return $_providers;
	}
	
	function admin_settings_html() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options')) {
			if (!function_exists('FluentCrmApi')) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('FluentCRM not installed.', 'lepopup'));
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
					<label>'.esc_html__('Lists', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select desired List.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			$exists = false;
			$lists_api = FluentCrmApi('lists');
			$lists = $lists_api->all();
			foreach ($lists as $list) {
				if (array_key_exists($list->id, $data['lists'])) $checked = $data['lists'][$list->id];
				else $checked = 'off';
				$checkbox_id = $lepopup->random_string(16);
					$html .= '
					<div class="lepopup-properties-pure" style="margin: 4px 0;">
						<input class="lepopup-checkbox lepopup-checkbox-classic lepopup-checkbox-medium" id="list-'.esc_html($checkbox_id).'" type="checkbox" value="on" name="lists['.$list->id.']"'.($checked == 'on' ? ' checked="checked"' : '').' /><label for="list-'.esc_html($checkbox_id).'"></label><label for="list-'.esc_html($checkbox_id).'">'.esc_html($list->title).'</label>
					</div>';
				$exists = true;
			}
			if ($exists) $html .= '<label class="lepopup-integrations-description">'.esc_html__('Select contact lists.', 'lepopup').'</label>';
			else $html .= '<strong>'.esc_html__('No contact lists found.', 'lepopup').'</strong>';
			$html .= '
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Tags', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select desired tags.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			$exists = false;
			$tags_api = FluentCrmApi('tags');
			$tags = $tags_api->all();
			foreach ($tags as $tag) {
				if (array_key_exists($tag->id, $data['tags'])) $checked = $data['tags'][$tag->id];
				else $checked = 'off';
				$checkbox_id = $lepopup->random_string(16);
					$html .= '
					<div class="lepopup-properties-pure" style="margin: 4px 0;">
						<input class="lepopup-checkbox lepopup-checkbox-classic lepopup-checkbox-medium" id="tag-'.esc_html($checkbox_id).'" type="checkbox" value="on" name="tags['.$tag->id.']"'.($checked == 'on' ? ' checked="checked"' : '').' /><label for="tag-'.esc_html($checkbox_id).'"></label><label for="tag-'.esc_html($checkbox_id).'">'.esc_html($tag->title).'</label>
					</div>';
				$exists = true;
			}
			if ($exists) $html .= '<label class="lepopup-integrations-description">'.esc_html__('Select tags.', 'lepopup').'</label>';
			else $html .= '<strong>'.esc_html__('No tags found.', 'lepopup').'</strong>';
			$html .= '
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to FluentCRM system properties.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>';
		foreach ($this->default_parameters['fields'] as $key => $value) {
			$html .= '
							<tr>
								<th>'.esc_html($this->fields_meta[$key]['title']).'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields['.$key.']" value="'.esc_html(array_key_exists($key, $data['fields']) ? $data['fields'][$key] : $value).'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html($this->fields_meta[$key]['description'].' ('.$key.')').'</label>
								</td>
								<td style="width: 32px;"></td>
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
		if (!function_exists('FluentCrmApi')) return $_result;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;
		try {
			$entry = array(
				'email' => $data['fields']['email'],
				'status' => 'subscribed',
				'ip' => $_SERVER['REMOTE_ADDR'],
				'lists' => array(),
				'tags' => array()
			);
			foreach ($data['fields'] as $key => $value) {
				if (!empty($value) && $key != 'email') {
					$entry[$key] = $value;
				}
			}
			if (array_key_exists('lists', $data) && is_array($data['lists'])) {
				foreach($data['lists'] as $key => $value) {
					if (!empty($value) && $value == 'on') {
						$entry['lists'][] = $key;
					}
				}
			}
			if (array_key_exists('tags', $data) && is_array($data['tags'])) {
				foreach($data['tags'] as $key => $value) {
					if (!empty($value) && $value == 'on') {
						$entry['tags'][] = $key;
					}
				}
			}
			$contact_api = FluentCrmApi('contacts');
			$result = $contact_api->createOrUpdate($entry);
		} catch (Exception $e) {
		}
		return $_result;
	}
}
$lepopup_fluentcrm = new lepopup_fluentcrm_class();
?>