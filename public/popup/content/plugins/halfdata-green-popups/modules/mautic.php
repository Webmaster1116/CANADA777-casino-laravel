<?php
/* Mautic integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_mautic_class {
	var $default_parameters = array(
		"api-url" => "",
		"username" => "",
		"password" => "",
		"owner" => "",
		"owner-id" => "",
		"campaign" => "",
		"campaign-id" => "",
		"segment" => "",
		"segment-id" => "",
		"fields" => array(),
		"tags" => ""
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-mautic-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-mautic-owner', array(&$this, "admin_owners"));
			add_action('wp_ajax_lepopup-mautic-campaign', array(&$this, "admin_campaigns"));
			add_action('wp_ajax_lepopup-mautic-segment', array(&$this, "admin_segments"));
			add_action('wp_ajax_lepopup-mautic-fields', array(&$this, "admin_fields_html"));
		}
		add_filter('lepopup_integrations_do_mautic', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("mautic", $_providers)) $_providers["mautic"] = esc_html__('Mautic', 'lepopup');
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
			<div class="lepopup-integrations-important">
				<div class="lepopup-integrations-important-content">
					<h4>'.esc_html__('Important! This module requires enabled HTTP Basic Auth. Please do it in your Mautic account on "Settings >> Configuration >> API Settings" page.', 'lepopup').'</h4>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('URL', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Mautic installation URL.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-url" value="'.esc_html($data['api-url']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Username', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter Mautic username to access your account.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="username" value="'.esc_html($data['username']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Password', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter Mautic password to access your account.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="password" value="'.esc_html($data['password']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Owner ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select ID of a Mautic user to assign this contact to.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-group lepopup-integrations-ajax-options">
						<input type="text" name="owner" value="'.esc_html($data['owner']).'" data-deps="api-url,username,password" readonly="readonly" />
						<input type="hidden" name="owner-id" value="'.esc_html($data['owner-id']).'" />
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Campaign ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select campaign ID to add this contact to.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-group lepopup-integrations-ajax-options">
						<input type="text" name="campaign" value="'.esc_html($data['campaign']).'" data-deps="api-url,username,password" readonly="readonly" />
						<input type="hidden" name="campaign-id" value="'.esc_html($data['campaign-id']).'" />
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Segment ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select segment ID to add this contact to.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-group lepopup-integrations-ajax-options">
						<input type="text" name="segment" value="'.esc_html($data['segment']).'" data-deps="api-url,username,password" readonly="readonly" />
						<input type="hidden" name="segment-id" value="'.esc_html($data['segment-id']).'" />
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Mautic fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['api-url']) && !empty($data['username']) && !empty($data['password'])) {
				$fields_data = $this->get_fields_html($data['api-url'], $data['username'], $data['password'], $data['fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="fields" data-deps="api-url,username,password"><i class="fas fa-download"></i><label>'.esc_html__('Load Fields', 'lepopup').'</label></a>
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
			</div>';
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = $html;
			echo json_encode($return_object);
		}
		exit;
	}
	
	function admin_owners() {
		global $wpdb, $lepopup;
		$lists = array();
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;

			if (!is_array($deps) || !array_key_exists('api-url', $deps) || empty($deps['api-url']) || !array_key_exists('username', $deps) || empty($deps['username']) || !array_key_exists('password', $deps) || empty($deps['password'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid Credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$result = $this->connect($deps['api-url'], $deps['username'], $deps['password'], 'api/contacts/list/owners');
			if(!$result || !is_array($result)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid server response.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (array_key_exists('errors', $result)) {
				$return_object = array('status' => 'ERROR', 'message' => $result['errors'][0]['message']);
				echo json_encode($return_object);
				exit;
			}
			$owners = array();
			foreach($result as $owner) {
				if (is_array($owner)) {
					if (array_key_exists('id', $owner) && array_key_exists('firstName', $owner) && array_key_exists('lastName', $owner)) {
						$owners[$owner['id']] = $owner['firstName'].' '.$owner['lastName'];
					}
				}
			}
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['items'] = $owners;
			echo json_encode($return_object);
		}
		exit;
	}

	function admin_campaigns() {
		global $wpdb, $lepopup;
		$lists = array();
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;

			if (!is_array($deps) || !array_key_exists('api-url', $deps) || empty($deps['api-url']) || !array_key_exists('username', $deps) || empty($deps['username']) || !array_key_exists('password', $deps) || empty($deps['password'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid Credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$result = $this->connect($deps['api-url'], $deps['username'], $deps['password'], 'api/campaigns');
			if(!$result || !is_array($result)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid server response.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (array_key_exists('errors', $result)) {
				$return_object = array('status' => 'ERROR', 'message' => $result['errors'][0]['message']);
				echo json_encode($return_object);
				exit;
			}
			$campaigns = array('0' => esc_html__('None', 'lepopup'));
			foreach($result['campaigns'] as $campaign) {
				if (is_array($campaign)) {
					if (array_key_exists('id', $campaign) && array_key_exists('name', $campaign)) {
						$campaigns[$campaign['id']] = $campaign['name'];
					}
				}
			}
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['items'] = $campaigns;
			echo json_encode($return_object);
		}
		exit;
	}

	function admin_segments() {
		global $wpdb, $lepopup;
		$lists = array();
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;

			if (!is_array($deps) || !array_key_exists('api-url', $deps) || empty($deps['api-url']) || !array_key_exists('username', $deps) || empty($deps['username']) || !array_key_exists('password', $deps) || empty($deps['password'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid Credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$result = $this->connect($deps['api-url'], $deps['username'], $deps['password'], 'api/segments');
			if(!$result || !is_array($result)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid server response.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (array_key_exists('errors', $result)) {
				$return_object = array('status' => 'ERROR', 'message' => $result['errors'][0]['message']);
				echo json_encode($return_object);
				exit;
			}
			$segments = array('0' => esc_html__('None', 'lepopup'));
			foreach($result['lists'] as $segment) {
				if (is_array($segment)) {
					if (array_key_exists('id', $segment) && array_key_exists('name', $segment)) {
						$segments[$segment['id']] = $segment['name'];
					}
				}
			}
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['items'] = $segments;
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
			if (!is_array($deps) || !array_key_exists('api-url', $deps) || empty($deps['api-url']) || !array_key_exists('username', $deps) || empty($deps['username']) || !array_key_exists('password', $deps) || empty($deps['password'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid Credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_fields_html($deps['api-url'], $deps['username'], $deps['password'], $this->default_parameters['fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_url, $_username, $_password, $_fields) {
		global $wpdb, $lepopup;
		$result = $this->connect($_url, $_username, $_password, 'api/contacts/list/fields');
		if(!$result || !is_array($result)) {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid server response.', 'lepopup'));
		}
		if (array_key_exists('errors', $result)) {
			return array('status' => 'ERROR', 'message' => $result['errors'][0]['message']);
		}
		if (empty($result)) {
			return array('status' => 'ERROR', 'message' => esc_html__('No fields found.', 'lepopup'));
		}
		$processed_aliases = array();
		$fields_html = '
			<table>';
		foreach ($result as $field) {
			if (is_array($field)) {
				if (array_key_exists('alias', $field) && array_key_exists('label', $field)) {
					if (!in_array($field['alias'], $processed_aliases)) {
						$processed_aliases[] = $field['alias'];
						$fields_html .= '
				<tr>
					<th>'.esc_html($field['label']).'</td>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="fields['.esc_html($field['alias']).']" value="'.esc_html(array_key_exists($field['alias'], $_fields) ? $_fields[$field['alias']] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<label class="lepopup-integrations-description">'.esc_html($field['label'].' ('.$field['alias'].')').'</label>
					</td>
				</tr>';
					}
				}
			}
		}
		$fields_html .= '
			</table>';
		return array('status' => 'OK', 'html' => $fields_html);
	}

	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['api-url']) || empty($data['username']) || empty($data['password'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$post_data = array(
			'ipAddress' => $_SERVER['REMOTE_ADDR'],
			'overwriteWithBlank' => false
		);
		if (!empty($data['owner-id'])) $post_data['owner'] = $data['owner-id'];
		foreach ($data['fields'] as $key => $value) {
			if (!empty($value)) {
				$post_data[$key] = $value;
			}
		}
		$tags_sanitized = array();
		$tags_raw = explode(',', $data['tags']);
		foreach ($tags_raw as $tag_raw) {
			$tag_raw = trim($tag_raw);
			if (!empty($tag_raw)) $tags_sanitized[] = $tag_raw;
		}
		if (sizeof($tags_sanitized) > 0) {
			$post_data['tags'] = $tags_sanitized;
		}
		
		$result = $this->connect($data['api-url'], $data['username'], $data['password'], 'api/contacts?search='.urlencode($data['fields']['email']));
		if (empty($result) || !array($result) || array_key_exists('errors', $result) || $result['total'] == 0) {
			$result = $this->connect($data['api-url'], $data['username'], $data['password'], 'api/contacts/new', $post_data);
			$contact_id = $result['contact']['id'];
		} else {
			$contact_details = reset($result['contacts']);
			$contact_id = $contact_details['id'];
			$result = $this->connect($data['api-url'], $data['username'], $data['password'], 'api/contacts/'.urlencode($contact_id).'/edit', $post_data, 'PUT');
		}
		if (!empty($data['segment-id']) && $data['segment-id'] != 0) {
			$result = $this->connect($data['api-url'], $data['username'], $data['password'], 'api/segments/'.urlencode($data['segment-id']).'/contact/'.urlencode($contact_id).'/add', array(), 'POST');
		}
		if (!empty($data['campaign-id']) && $data['campaign-id'] != 0) {;
			$result = $this->connect($data['api-url'], $data['username'], $data['password'], 'api/campaigns/'.urlencode($data['campaign-id']).'/contact/'.urlencode($contact_id).'/add', array(), 'POST');
		}
		return $_result;
	}
	
	function connect($_url, $_username, $_password, $_path, $_data = array(), $_method = '') {
		$url = rtrim($_url, '/').'/'.ltrim($_path, '/');
		$headers = array(
			'Authorization: Basic '.base64_encode($_username.':'.$_password),
			'Content-Type: application/json;charset=UTF-8',
			'Accept: application/json'
		);
		try {
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
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
$lepopup_mautic = new lepopup_mautic_class();
?>