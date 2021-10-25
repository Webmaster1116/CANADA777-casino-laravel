<?php
/* BirdSend integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_birdsend_class {
	var $default_parameters = array(
		"access-token" => "",
		"sequence" => "",
		"sequence-id" => "",
		"fields" => array('email' => ''),
		"tags" => ""
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-birdsend-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-birdsend-sequence', array(&$this, "admin_sequences"));
			add_action('wp_ajax_lepopup-birdsend-fields', array(&$this, "admin_fields_html"));
		}
		add_filter('lepopup_integrations_do_birdsend', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("birdsend", $_providers)) $_providers["birdsend"] = esc_html__('BirdSend', 'lepopup');
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
				'.esc_html__('Important! Please go to your BirdSend Account >> Settings >> Integrations >> BirdSend Apps and create new App. After that go to Permissions tab of App settings and set them as "Write". Then go to Access Token tab of App settings and create Personal Access Token. Copy and Paste it into field below.', 'lepopup').'
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Personal Access Token', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Paste your Personal Access Token with "write" permissions.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="access-token" value="'.esc_html($data['access-token']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Sequence ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select Sequence ID.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-group lepopup-integrations-ajax-options">
						<input type="text" name="sequence" value="'.esc_html($data['sequence']).'" data-deps="access-token" readonly="readonly" />
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
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to BirdSend fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>
							<tr>
								<th>'.esc_html__('Email','lepopup').'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields[email]" value="'.esc_html(array_key_exists('email', $data['fields']) ? $data['fields']['email'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Email Address (email)', 'lepopup').'</label>
								</td>
							</tr>
						</table>
					</div>
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['access-token'])) {
				$fields_data = $this->get_fields_html($data['access-token'], $data['fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="fields" data-deps="access-token"><i class="fas fa-download"></i><label>'.esc_html__('Load Fields', 'lepopup').'</label></a>
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
	
	function admin_sequences() {
		global $wpdb, $lepopup;
		$sequences = array();
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;

			if (!is_array($deps) || !array_key_exists('access-token', $deps) || empty($deps['access-token'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid Personal Access Token.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}

			$result = $this->connect($deps['access-token'], 'sequences?per_page=100&page=1');
			if (is_array($result) && !empty($result)) {
				if (array_key_exists('data', $result)) {
					$sequences[0] = esc_html__('No Sequence', 'lepopup');
					if (!empty($result['data'])) {
						foreach ($result['data'] as $list) {
							if (is_array($list)) {
								if (array_key_exists('sequence_id', $list) && array_key_exists('name', $list)) {
									$sequences[$list['sequence_id']] = $list['name'];
								}
							}
						}
					}
				} else if (array_key_exists('status', $result) && array_key_exists('message', $result)) {
					$return_object = array('status' => 'ERROR', 'message' => $result['message']);
					echo json_encode($return_object);
					exit;
				} else {
					$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid server response.', 'lepopup'));
					echo json_encode($return_object);
					exit;
				}
			} else {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid server response.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			
			if (empty($sequences)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('No lists found.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['items'] = $sequences;
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
			if (!is_array($deps) || !array_key_exists('access-token', $deps) || empty($deps['access-token'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid Personal Access Token.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_fields_html($deps['access-token'], $this->default_parameters['fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_access_token, $_fields) {
		global $wpdb, $lepopup;
		$fields_html = '';
		$result = $this->connect($_access_token, 'fields?per_page=100&page=1');
		if (!empty($result) && is_array($result)) {
			if (array_key_exists('data', $result)) {
				if (!empty($result['data'])) {
					$fields_html = '
			<table>';
					foreach ($result['data'] as $field) {
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
			} else if (array_key_exists('status', $result) && array_key_exists('message', $result)) {
				return array('status' => 'ERROR', 'message' => $result['message']);
			} else {
				return array('status' => 'ERROR', 'message' => esc_html__('Invalid server response.', 'lepopup'));
			}
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid server response.', 'lepopup'));
		}
		
		return array('status' => 'OK', 'html' => $fields_html);
	}

	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['access-token'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$post_data = array(
			'ipaddress' => $_SERVER['REMOTE_ADDR'],
			'email' => $data['fields']['email']
		);
		$fields = array();
		if (!empty($data['fields']) && is_array($data['fields'])) {
			foreach ($data['fields'] as $key => $value) {
				if (!empty($value) && $key != 'email') {
					$fields[$key] = $value;
				}
			}
		}
		if (!empty($fields)) $post_data['fields'] = $fields;
		
		$tags = array();
		$tags_raw = explode(',', $data['tags']);
		foreach ($tags_raw as $tag_raw) {
			$tag_raw = trim($tag_raw);
			if (!empty($tag_raw)) $tags[] = $tag_raw;
		}

		$result = $this->connect($data['access-token'], 'contacts?search_by=email&keyword='.urlencode($data['fields']['email']));
		if (array_key_exists('data', $result) && !empty($result['data'])) {
			$contact_id = $result['data'][0]['contact_id'];
			$result = $this->connect($data['access-token'], 'contacts/'.$contact_id, $post_data, 'PATCH');
			if (!empty($tags)) $result = $this->connect($data['access-token'], 'contacts/'.$contact_id.'/tags', array('tags' => $tags));
			if (!empty($data['sequence-id'])) $result = $this->connect($data['access-token'], 'contacts/'.$contact_id.'/subscribe', array('sequence_id' => $data['sequence-id']));
		} else {
			if (!empty($data['sequence-id'])) $post_data['sequence_id'] = $data['sequence-id'];
			if (!empty($tags)) $post_data['tags'] = $tags;
			$result = $this->connect($data['access-token'], 'contacts', $post_data);
		}
		return $_result;
	}
	
	function connect($_access_token, $_path, $_data = array(), $_method = '') {
		$headers = array(
			'Authorization: Bearer '.$_access_token,
			'Content-Type: application/json;charset=UTF-8',
			'Accept: application/json'
		);
		try {
			$url = 'https://api.birdsend.co/v1/'.ltrim($_path, '/');
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
$lepopup_birdsend = new lepopup_birdsend_class();
?>