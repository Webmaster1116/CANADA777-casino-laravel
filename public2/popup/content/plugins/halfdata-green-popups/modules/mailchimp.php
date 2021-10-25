<?php
/* MailChimp integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_mailchimp_class {
	var $default_parameters = array(
		"api-key" => "",
		"list" => "",
		"list-id" => "",
		"groups" => array(),
		"fields" => array('EMAIL' => ''),
		"double" => "off",
		"tags" => ""
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-mailchimp-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-mailchimp-list', array(&$this, "admin_lists"));
			add_action('wp_ajax_lepopup-mailchimp-fields', array(&$this, "admin_fields_html"));
			add_action('wp_ajax_lepopup-mailchimp-groups', array(&$this, "admin_groups_html"));
		}
		add_filter('lepopup_integrations_do_mailchimp', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("mailchimp", $_providers)) $_providers["mailchimp"] = esc_html__('MailChimp', 'lepopup');
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
					<label>'.esc_html__('API Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your MailChimp API Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find your MailChimp API Key %shere%s.', 'lepopup'), '<a href="https://admin.mailchimp.com/account/api-key-popup" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('List ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select desired List ID.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-group lepopup-integrations-ajax-options">
						<input type="text" name="list" value="'.esc_html($data['list']).'" data-deps="api-key" readonly="readonly" />
						<input type="hidden" name="list-id" value="'.esc_html($data['list-id']).'" />
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to MailChimp fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>
							<tr>
								<th>EMAIL</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[EMAIL]" value="'.esc_html(array_key_exists('EMAIL', $data['fields']) ? $data['fields']['EMAIL'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Email Address', 'lepopup').'</label>
								</td>
							</tr>
						</table>
					</div>
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['api-key']) && !empty($data['list-id'])) {
				$fields_data = $this->get_fields_html($data['api-key'], $data['list-id'], $data['fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="fields" data-deps="api-key,list-id"><i class="fas fa-download"></i><label>'.esc_html__('Load Fields', 'lepopup').'</label></a>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Groups', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select groups.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['api-key']) && !empty($data['list-id'])) {
				$groups_data = $this->get_groups_html($data['api-key'], $data['list-id'], $data['groups']);
				if ($groups_data['status'] == 'OK') $html .= $groups_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="groups" data-deps="api-key,list-id"><i class="fas fa-download"></i><label>'.esc_html__('Load Groups', 'lepopup').'</label></a>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Tags', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Comma-separated list of tags. Tagging lets you bring your own contact structure into Mailchimp and label contacts based on data only you know about them.', 'lepopup').'</div>
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
	
	function admin_lists() {
		global $wpdb, $lepopup;
		$lists = array();
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;

			if (!is_array($deps) || !array_key_exists('api-key', $deps) || empty($deps['api-key'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Key.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}

			$result = $this->connect($deps['api-key'], 'lists?count=100');
			if (is_array($result) && array_key_exists('total_items', $result)) {
				if (intval($result['total_items']) > 0) {
					foreach ($result['lists'] as $list) {
						if (is_array($list)) {
							if (array_key_exists('id', $list) && array_key_exists('name', $list)) {
								$lists[$list['id']] = $list['name'];
							}
						}
					}
				} else {
					$return_object = array('status' => 'ERROR', 'message' => esc_html__('No lists found.', 'lepopup'));
					echo json_encode($return_object);
					exit;
				}
			} else {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Key.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (empty($lists)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('No lists found.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['items'] = $lists;
			echo json_encode($return_object);
		}
		exit;
	}

	function admin_fields_html() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;
			if (!is_array($deps) || !array_key_exists('api-key', $deps) || empty($deps['api-key']) || !array_key_exists('list-id', $deps) || empty($deps['list-id'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Key or List ID.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_fields_html($deps['api-key'], $deps['list-id'], $this->default_parameters['fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_key, $_list, $_fields) {
		global $wpdb, $lepopup;
		$result = $this->connect($_key, 'lists/'.urlencode($_list).'/merge-fields?count=100');
		$fields_html = '';
		if (!empty($result) && is_array($result)) {
			if (array_key_exists('status', $result)) {
				return array('status' => 'ERROR', 'message' => $result['title']);
			} else {
				if (array_key_exists('total_items', $result) && $result['total_items'] > 0) {
					$fields_html = '
			<table>';
					foreach ($result['merge_fields'] as $field) {
						if (is_array($field)) {
							if (array_key_exists('tag', $field) && array_key_exists('name', $field)) {
								$fields_html .= '
				<tr>
					<th>'.esc_html($field['tag']).'</th>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="fields['.esc_html($field['tag']).']" value="'.esc_html(array_key_exists($field['tag'], $_fields) ? $_fields[$field['tag']] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<label class="lepopup-integrations-description">'.esc_html($field['name']).'</label>
					</td>
				</tr>';
							}
						}
					}
					$fields_html .= '
			</table>';
				} else {
					return array('status' => 'ERROR', 'message' => esc_html__('No fields found.', 'lepopup'));
				}
			}
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid API Key.', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $fields_html);
	}

	function admin_groups_html() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;
			if (!is_array($deps) || !array_key_exists('api-key', $deps) || empty($deps['api-key']) || !array_key_exists('list-id', $deps) || empty($deps['list-id'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Key or List ID.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_groups_html($deps['api-key'], $deps['list-id'], $this->default_parameters['groups']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_groups_html($_key, $_list, $_groups) {
		global $wpdb, $lepopup;
		$result = $this->connect($_key, 'lists/'.urlencode($_list).'/interest-categories?count=100');
		$groups_html = '';
		if (!empty($result) && is_array($result)) {
			if (array_key_exists('status', $result)) {
				return array('status' => 'ERROR', 'message' => $result['title']);
			} else {
				if (array_key_exists('total_items', $result) && $result['total_items'] > 0) {
					foreach ($result['categories'] as $category) {
						$result2 = $this->connect($_key, 'lists/'.urlencode($_list).'/interest-categories/'.$category['id'].'/interests?count=100');
						if (!empty($result2) && is_array($result2) && array_key_exists('total_items', $result2) && $result2['total_items'] > 0) {
							$groups_html .= '
				<div class="lepopup-properties-pure" style="margin: 5px 0;">
					<strong>'.$category['title'].'</strong>';
							foreach ($result2['interests'] as $interest) {
								if (array_key_exists($category['id'].'-'.$interest['id'], $_groups)) $checked = $_groups[$category['id'].'-'.$interest['id']];
								else $checked = 'off';
								$checkbox_id = $lepopup->random_string(16);
								$groups_html .= '
					<div class="lepopup-properties-pure" style="margin: 2px 0;">
						<input class="lepopup-checkbox lepopup-checkbox-classic lepopup-checkbox-medium" id="group-'.esc_html($checkbox_id).'" type="checkbox" value="on" name="groups['.esc_html($category['id'].'-'.$interest['id']).']"'.($checked == 'on' ? ' checked="checked"' : '').' /><label for="group-'.esc_html($checkbox_id).'"></label> '.esc_html($interest['name']).'
					</div>';
							}
							$groups_html .= '</div>';
						}
					}
				} else {
					return array('status' => 'ERROR', 'message' => esc_html__('No groups found.', 'lepopup'));
				}
			}
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid API Key.', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $groups_html);
	}
	
	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['api-key']) || empty($data['list-id'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['EMAIL']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['EMAIL'])) return $_result;

		$result = $this->connect($data['api-key'], 'lists/'.urlencode($data['list-id']).'/members/'.md5(strtolower($data['fields']['EMAIL'])));

		$status = '';
		if (array_key_exists('status', $result)) $status = $result['status'];
		if (array_key_exists('status', $result) && $result['status'] == 'pending') {
			$this->connect($data['api-key'], 'lists/'.urlencode($data['list-id']).'/members/'.md5(strtolower($data['fields']['EMAIL'])), array(), 'DELETE');
		} else {
			if (array_key_exists('merge_fields', $result)) $merge_fields = $result['merge_fields'];
			if (array_key_exists('interests', $result)) $interests = $result['interests'];
		}
			
		$merge_fields = array();
		foreach ($data['fields'] as $key => $value) {
			if (!empty($value) && $key != 'EMAIL') {
				$merge_fields[$key] = $value;
			}
		}
			
		$interests = array();
		foreach ((array)$data['groups'] as $interest => $checked) {
			if ($checked == 'on' && !empty($interest) && strpos($interest, '-') !== false) {
				$key = null;
				list($tmp, $key) = explode("-", $interest, 2);
				if (!empty($key)) $interests[$key] = true;
			}
		}
			
		$output = array(
			'ip_signup' => $_SERVER['REMOTE_ADDR'],
			'email_address' => $data['fields']['EMAIL'],
			'status' => $data['double'] == 'on' ? (!empty($status) && $status != 'pending' ? 'subscribed' : 'pending') : 'subscribed',
			'status_if_new' => $data['double'] == 'on' ? 'pending' : 'subscribed'
		);
		if (!empty($merge_fields)) {
			$output['merge_fields'] = $merge_fields;
		}
		if (!empty($interests)) {
			$output['interests'] = $interests;
		}
		
		$result = $this->connect($data['api-key'], 'lists/'.urlencode($data['list-id']).'/members/'.md5(strtolower($data['fields']['EMAIL'])), $output, 'PUT');

		$tags_sanitized = array();
		$tags_raw = explode(',', $data['tags']);
		foreach ($tags_raw as $tag_raw) {
			$tag_raw = trim($tag_raw);
			if (!empty($tag_raw)) $tags_sanitized[] = $tag_raw;
		}
		if (sizeof($tags_sanitized) > 0) {
			$tags = array('tags' => array());
			foreach ($tags_sanitized as $tag_sanitized) {
				$tags['tags'][] = array('name' => $tag_sanitized, 'status' => 'active');
			}
			$result = $this->connect($data['api-key'], 'lists/'.urlencode($data['list-id']).'/members/'.md5(strtolower($data['fields']['EMAIL'])).'/tags', $tags);
		}
		return $_result;
	}
	
	function connect($_api_key, $_path, $_data = array(), $_method = '') {
		$dc = "us1";
		if (strstr($_api_key, "-")) {
			list($key, $dc) = explode("-", $_api_key, 2);
			if (!$dc) $dc = "us1";
		}
		$headers = array(
			'Content-Type: application/json;charset=UTF-8',
			'Accept: application/json'
		);
		try {
			$url = 'https://'.$dc.'.api.mailchimp.com/3.0/'.ltrim($_path, '/');
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, 'lepopup:'.$_api_key);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($_data));
			}
			if (!empty($_method)) {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_method);
			}
			curl_setopt($curl, CURLOPT_TIMEOUT, 20);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
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
$lepopup_mailchimp = new lepopup_mailchimp_class();
?>