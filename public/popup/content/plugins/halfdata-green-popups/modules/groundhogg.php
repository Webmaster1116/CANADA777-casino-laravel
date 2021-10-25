<?php
/* Groundhogg integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_groundhogg_class {
	var $default_parameters = array(
		'owner-id' => "",
		'fields' => array('email' => '', 'first_name' => '', 'last_name' => ''),
		'meta' => array(
			'primary_phone'	=> '',
			'primary_phone_extension' => '',
			'street_address_1' => '',
			'street_address_2' => '',
			'city' => '',
			'postal_zip' => '',
			'country' => ''
		),
		"custom-names" => array(),
		"custom-values" => array(),
		"tags" => "",
		'double' => "off",
	);
	var $fields_meta;
	function __construct() {
		$this->fields_meta = array(
			'primary_phone'	=> esc_html__('Primary phone', 'lepopup'),
			'primary_phone_extension' => esc_html__('Phone extension', 'lepopup'),
			'street_address_1' => esc_html__('Street address 1', 'lepopup'),
			'street_address_2' => esc_html__('Street address 2', 'lepopup'),
			'city' => esc_html__('City', 'lepopup'),
			'postal_zip' => esc_html__('Postal code', 'lepopup'),
			'country' => esc_html__('Country', 'lepopup'),
		);
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-groundhogg-settings-html', array(&$this, "admin_settings_html"));
		}
		add_filter('lepopup_integrations_do_groundhogg', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (class_exists('Groundhogg\Plugin')) $_providers["groundhogg"] = esc_html__('Groundhogg', 'lepopup');
		return $_providers;
	}
	
	function admin_settings_html() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options')) {
			if (!class_exists('Groundhogg\Plugin')) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Groundhogg plugin not installed.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$owners = get_users(array('role__in' => array('administrator', 'marketer', 'sales_manager')));
			if (array_key_exists('data', $_REQUEST)) {
				$data = json_decode(base64_decode(trim(stripslashes($_REQUEST['data']))), true);
				if (is_array($data)) $data = array_merge($this->default_parameters, $data);
				else $data = $this->default_parameters;
			} else $data = $this->default_parameters;
			$checkbox_id = $lepopup->random_string();
			$html = '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Owner', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select the owner of the contact.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<select type="text" name="owner-id" value="'.esc_html($data['owner-id']).'">';
			foreach ($owners as $owner) {
				$html .= '
						<option value="'.esc_html($owner->ID).'"'.($owner->ID == $data['owner-id'] ? ' selected="selected"' : '').'>'.sprintf(esc_html('%s (%s)'), $owner->display_name, $owner->user_email).'</option>';
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
					<div class="lepopup-tooltip-content">'.esc_html__('Map popup fields to Groundhogg fields.', 'lepopup').'</div>
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
										<input type="text" name="fields[first_name]" value="'.(array_key_exists('first_name', $data['fields']) ? esc_html($data['fields']['first_name']) : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">{first_name}</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('Last name', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[last_name]" value="'.(array_key_exists('last_name', $data['fields']) ? esc_html($data['fields']['last_name']) : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">{last_name}</label>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Meta', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map popup fields to Groundhogg meta fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>';
		foreach ($this->default_parameters['meta'] as $key => $value) {
			$html .= '
							<tr>
								<th>'.esc_html($this->fields_meta[$key]).'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="meta['.$key.']" value="'.esc_html(array_key_exists($key, $data['meta']) ? $data['meta'][$key] : $value).'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html($this->fields_meta[$key].' ('.$key.')').'</label>
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
					<label>'.esc_html__('Custom meta', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Configure Groundhogg custom meta fields and map popup fields to them.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline lepopup-integrations-custom" data-names="custom-names" data-values="custom-values">
						<table>
							<tr>
								<th>'.esc_html__('Name', 'lepopup').'</th>
								<td>'.esc_html__('Value', 'lepopup').'</td>
								<td style="width: 32px;"></td>
							</tr>';
		$i = 0;
		foreach ($data['custom-names'] as $key => $value) {
			if (empty($value)) continue;
			$html .= '
							<tr>
								<th>
									<input type="text" value="'.esc_html($value).'" class="lepopup-integrations-custom-name widefat" data-custom="on" />
								</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" value="'.esc_html(array_key_exists($key, $data['custom-values']) ? $data['custom-values'][$key] : '').'" class="lepopup-integrations-custom-value widefat" data-custom="on" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
								</td>
								<td class="lepopup-middle-center">'.($i > 0 ? '<a class="lepopup-integrations-custom-remove" href="#" onclick="jQuery(this).closest(\'tr\').remove(); return false;"><i class="fas fa-trash-alt"></i></a>' : '').'</td>
							</tr>';
			$i++;
		}
		if ($i == 0) {
			$html .= '
							<tr>
								<th>
									<input type="text" value="" class="lepopup-integrations-custom-name widefat" data-custom="on" />
								</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" value="" class="lepopup-integrations-custom-value widefat" data-custom="on" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
								</td>
								<td></td>
							</tr>';
		}
		$html .= '				
							<tr style="display: none;" class="lepopup-integrations-custom-template">
								<th>
									<input type="text" value="" class="lepopup-integrations-custom-name widefat" data-custom="on" />
								</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" value="" class="lepopup-integrations-custom-value widefat" data-custom="on" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
								</td>
								<td class="lepopup-middle-center"><a class="lepopup-integrations-custom-remove" href="#" onclick="jQuery(this).closest(\'tr\').remove(); return false;"><i class="fas fa-trash-alt"></i></a></td>
							</tr>
							<tr>
								<td colspan="3">
									<a class="lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small" href="#" onclick="return lepopup_integrations_custom_add(this);"><i class="fas fa-plus"></i><label>'.esc_html__('Add Custom Field', 'lepopup').'</label></a>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Tags', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Comma-separated list of tags.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="tags" value="'.esc_html($data['tags']).'" />
				</div>
			</div>
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
		if (!class_exists('Groundhogg\Plugin')) return $_result;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;
		try {
			$args = array(
				'email' => $data['fields']['email'],
				'first_name' => $data['fields']['first_name'],
				'last_name' => $data['fields']['last_name'],
				'owner_id' => $data['owner-id'],
				'optin_status' => $data['double'] == "on" ? Groundhogg\Preferences::UNCONFIRMED : Groundhogg\Preferences::CONFIRMED,
			);
			$meta = array(
				'ip_address' => $_SERVER['REMOTE_ADDR']
			);
			foreach ($data['meta'] as $key => $value) {
				if (!empty($value)) {
					$meta[$key] = $value;
				}
			}
			if (!empty($data['custom-names'])) {
				foreach($data['custom-names'] as $key => $name) {
					if (!empty($name) && !empty($data['custom-values'][$key])) $meta[$name] = $data['custom-values'][$key];
				}
			}
			$tags = array();
			$tags_raw = explode(',', $data['tags']);
			foreach ($tags_raw as $tag_raw) {
				$tag_raw = trim($tag_raw);
				if (!empty($tag_raw)) $tags[] = $tag_raw;
			}
			$args = array_map('sanitize_text_field', $args);
			if (Groundhogg\Plugin::$instance->dbs->get_db('contacts')->exists($args['email'])) {
				$contact = Groundhogg\get_contactdata($args['email']);
				$contact->update($args);
			} else {
				$contact_id = Groundhogg\Plugin::$instance->dbs->get_db('contacts')->add($args);
				if (!$contact_id) return $_result;
				$contact = Groundhogg\get_contactdata($contact_id);
			}
			if (!empty($meta)) {
				foreach ($meta as $key => $value) {
					$contact->update_meta(sanitize_key($key), sanitize_text_field($value));
				}
			}
			if (!empty($tags)) {
				$contact->add_tag($tags);
			}
		} catch (Exception $e) {
			
		}
		return $_result;
	}
}
$lepopup_groundhogg = new lepopup_groundhogg_class();
?>