<?php
/* ConvertKit integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_convertkit_class {
	var $default_parameters = array(
		"api-key" => "",
		"list" => "0 | None",
		"list-id" => "0",
		"sequence" => "0 | None",
		"sequence-id" => "0",
		"tags" => array(),
		"fields" => array('email' => '', 'first_name' => '')
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-convertkit-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-convertkit-list', array(&$this, "admin_lists"));
			add_action('wp_ajax_lepopup-convertkit-sequence', array(&$this, "admin_sequences"));
			add_action('wp_ajax_lepopup-convertkit-fields', array(&$this, "admin_fields_html"));
			add_action('wp_ajax_lepopup-convertkit-tags', array(&$this, "admin_tags_html"));
		}
		add_filter('lepopup_integrations_do_convertkit', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("convertkit", $_providers)) $_providers["convertkit"] = esc_html__('ConvertKit', 'lepopup');
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
					<label>'.esc_html__('API Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your ConvertKit API Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find your ConvertKit API Key %shere%s.', 'lepopup'), '<a href="https://app.convertkit.com/account/edit" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Form', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select desired form. Either form or sequence must be selected.', 'lepopup').'</div>
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
					<label>'.esc_html__('Sequence', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select desired sequence. Either form or sequence must be selected.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-group lepopup-integrations-ajax-options">
						<input type="text" name="sequence" value="'.esc_html($data['sequence']).'" data-deps="api-key" readonly="readonly" />
						<input type="hidden" name="sequence-id" value="'.esc_html($data['sequence-id']).'" />
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to ConvertKit fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>
							<tr>
								<th>'.esc_html__('Email', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[email]" value="'.esc_html(array_key_exists('email', $data['fields']) ? $data['fields']['email'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Email address', 'lepopup').'</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('First name', 'lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[first_name]" value="'.esc_html(array_key_exists('first_name', $data['fields']) ? $data['fields']['first_name'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('First name', 'lepopup').'</label>
								</td>
							</tr>
						</table>
					</div>
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['api-key'])) {
				$fields_data = $this->get_fields_html($data['api-key'], $data['fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="fields" data-deps="api-key"><i class="fas fa-download"></i><label>'.esc_html__('Load Fields', 'lepopup').'</label></a>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Tags', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select tags.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['api-key'])) {
				$groups_data = $this->get_tags_html($data['api-key'], $data['tags']);
				if ($groups_data['status'] == 'OK') $html .= $groups_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="tags" data-deps="api-key"><i class="fas fa-download"></i><label>'.esc_html__('Load Tags', 'lepopup').'</label></a>
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
		$lists = array('0' => 'None');
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

			$result = $this->connect($deps['api-key'], 'forms');
			if (is_array($result) && !empty($result)) {
				if (array_key_exists('error', $result)) {
					$return_object = array('status' => 'ERROR', 'message' => esc_html($result['error']));
					echo json_encode($return_object);
					exit;
				}
				if (array_key_exists('forms', $result) && sizeof($result['forms']) > 0) {
					foreach ($result['forms'] as $list) {
						if (is_array($list)) {
							if (array_key_exists('id', $list) && array_key_exists('name', $list)) {
								$lists[$list['id']] = $list['name'];
							}
						}
					}
				}
			} else {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid server response.', 'lepopup'));
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

	function admin_sequences() {
		global $wpdb, $lepopup;
		$lists = array('0' => 'None');
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

			$result = $this->connect($deps['api-key'], 'sequences');
			if (is_array($result) && !empty($result)) {
				if (array_key_exists('error', $result)) {
					$return_object = array('status' => 'ERROR', 'message' => esc_html($result['error']));
					echo json_encode($return_object);
					exit;
				}
				if (array_key_exists('courses', $result) && sizeof($result['courses']) > 0) {
					foreach ($result['courses'] as $list) {
						if (is_array($list)) {
							if (array_key_exists('id', $list) && array_key_exists('name', $list)) {
								$lists[$list['id']] = $list['name'];
							}
						}
					}
				}
			} else {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid server response.', 'lepopup'));
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
			if (!is_array($deps) || !array_key_exists('api-key', $deps) || empty($deps['api-key'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API Key.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_fields_html($deps['api-key'], $this->default_parameters['fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_key, $_fields) {
		global $wpdb, $lepopup;
		$result = $this->connect($_key, 'custom_fields');
		$fields_html = '';
		if (!empty($result) && is_array($result)) {
			if (array_key_exists('error', $result)) {
				return array('status' => 'ERROR', 'message' => esc_html($result['error']));
			} else {
				if (array_key_exists('custom_fields', $result) && sizeof($result['custom_fields']) > 0) {
					$fields_html = '
			<table>';
					foreach ($result['custom_fields'] as $field) {
						if (is_array($field)) {
							if (array_key_exists('key', $field) && array_key_exists('label', $field)) {
								$fields_html .= '
				<tr>
					<th>'.esc_html($field['label']).'</th>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="fields['.esc_html($field['key']).']" value="'.esc_html(array_key_exists($field['key'], $_fields) ? $_fields[$field['key']] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<label class="lepopup-integrations-description">'.esc_html($field['label'].' ('.$field['key'].')').'</label>
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

	function admin_tags_html() {
		global $wpdb;
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
			$return_object = $this->get_tags_html($deps['api-key'], $this->default_parameters['tags']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_tags_html($_key, $_tags) {
		global $wpdb, $lepopup;
		$result = $this->connect($_key, 'tags');
		$tags_html = '';
		if (!empty($result) && is_array($result)) {
			if (array_key_exists('error', $result)) {
				return array('status' => 'ERROR', 'message' => esc_html($result['error']));
			} else {
				if (array_key_exists('tags', $result) && sizeof($result['tags'] > 0)) {
					foreach ($result['tags'] as $tag) {
						if (array_key_exists($tag['id'], $_tags)) $checked = $_tags[$tag['id']];
						else $checked = 'off';
						$checkbox_id = $lepopup->random_string(16);
						$tags_html .= '
				<div class="lepopup-properties-pure" style="margin: 4px 0;">
					<input class="lepopup-checkbox lepopup-checkbox-classic lepopup-checkbox-medium" id="tag-'.esc_html($checkbox_id).'" type="checkbox" value="on" name="tags['.$tag['id'].']"'.($checked == 'on' ? ' checked="checked"' : '').' /><label for="tag-'.esc_html($checkbox_id).'"></label><label for="tag-'.esc_html($checkbox_id).'">'.esc_html($tag['name']).'</label>
				</div>';
					}
				} else {
					return array('status' => 'ERROR', 'message' => esc_html__('No tags found.', 'lepopup'));
				}
			}
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid API Key.', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $tags_html);
	}
	
	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['api-key'])) return $_result;
		if ((empty($data['list-id']) || $data['list-id'] == 0) && (empty($data['sequence-id']) || $data['sequence-id'] == 0)) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$post_data = array(
			'email' => $data['fields']['email'],
			'first_name' => $data['fields']['first_name'],
			'fields' => array(),
			'tags' => implode(',', array_keys($data['tags']))
		);
		foreach ($data['fields'] as $key => $value) {
			if (!empty($value) && $key != 'email' && $key != 'first_name') {
				$post_data['fields'][$key] = $value;
			}
		}
		if (!empty($data['list-id']) && $data['list-id'] != 0) {
			$result = $this->connect($data['api-key'], 'forms/'.urlencode($data['list-id']).'/subscribe', $post_data);
		}
		if (!empty($data['sequence-id']) && $data['sequence-id'] != 0) {
			$result = $this->connect($data['api-key'], 'courses/'.urlencode($data['sequence-id']).'/subscribe', $post_data);
		}
		return $_result;
	}
	
	function connect($_api_key, $_path, $_data = array(), $_method = '') {
		$url = 'https://api.convertkit.com/v3/'.ltrim($_path, '/').'?api_key='.$_api_key;
		try {
			$curl = curl_init($url);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($_data));
			}
			if (!empty($_method)) {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_method);
			}
			curl_setopt($curl, CURLOPT_TIMEOUT, 10);
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
$lepopup_convertkit = new lepopup_convertkit_class();
?>