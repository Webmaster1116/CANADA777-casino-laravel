<?php
/* Ontraport integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_ontraport_class {
	var $default_parameters = array(
		'app-id' => '',
		'api-key' => '',
		'tags' => array(),
		'sequences' => array(),
		'fields' => array()
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-ontraport-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-ontraport-tags', array(&$this, "admin_tags_html"));
			add_action('wp_ajax_lepopup-ontraport-tags-more', array(&$this, "admin_tags_more_html"));
			add_action('wp_ajax_lepopup-ontraport-sequences', array(&$this, "admin_sequences_html"));
			add_action('wp_ajax_lepopup-ontraport-sequences-more', array(&$this, "admin_sequences_more_html"));
			add_action('wp_ajax_lepopup-ontraport-fields', array(&$this, "admin_fields_html"));
		}
		add_filter('lepopup_integrations_do_ontraport', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("ontraport", $_providers)) $_providers["ontraport"] = esc_html__('Ontraport', 'lepopup');
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
					<label>'.esc_html__('App ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Ontraport App ID.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="app-id" value="'.esc_html($data['app-id']).'" />
					<label class="lepopup-integrations-description">Find your Ontraport App ID in <a href="https://app.ontraport.com/#!/api_settings/listAll" target="_blank">Administration Settings</a>.</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('API Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Ontraport API Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
					<label class="lepopup-integrations-description">Find your Ontraport API Key in <a href="https://app.ontraport.com/#!/api_settings/listAll" target="_blank">Administration Settings</a>.</label>
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
			if (!empty($data['app-id']) && !empty($data['api-key'])) {
				$tags_data = $this->get_tags_html($data['app-id'], $data['api-key'], $data['tags']);
				if ($tags_data['status'] == 'OK') $html .= $tags_data['html'];
			}
			$html .= '
					</div>
					<input type="hidden" data-skip="on" name="tags-selected" value="'.esc_html(implode(',', $data['tags'])).'" />
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="tags" data-deps="app-id,api-key,tags-selected"><i class="fas fa-download"></i><label>'.esc_html__('Load Tags', 'lepopup').'</label></a>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Sequences', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select sequences.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['app-id']) && !empty($data['api-key'])) {
				$sequences_data = $this->get_sequences_html($data['app-id'], $data['api-key'], $data['sequences']);
				if ($sequences_data['status'] == 'OK') $html .= $sequences_data['html'];
			}
			$html .= '
					</div>
					<input type="hidden" data-skip="on" name="sequences-selected" value="'.esc_html(implode(',', $data['sequences'])).'" />
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="sequences" data-deps="app-id,api-key,sequences-selected"><i class="fas fa-download"></i><label>'.esc_html__('Load Sequences', 'lepopup').'</label></a>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Ontraport fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			if (!empty($data['app-id']) && !empty($data['api-key'])) {
				$fields_data = $this->get_fields_html($data['app-id'], $data['api-key'], $data['fields']);
				if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			}
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="fields" data-deps="app-id,api-key"><i class="fas fa-download"></i><label>'.esc_html__('Load Fields', 'lepopup').'</label></a>
				</div>
			</div>';
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = $html;
			echo json_encode($return_object);
		}
		exit;
	}

	function admin_tags_html() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;
			if (!is_array($deps) || !array_key_exists('app-id', $deps) || empty($deps['app-id']) || !array_key_exists('api-key', $deps) || empty($deps['api-key']) || !array_key_exists('tags-selected', $deps)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (empty($deps['tags-selected'])) $tags = array();
			else $tags = explode(',', $deps['tags-selected']);
			$return_object = $this->get_tags_html($deps['app-id'], $deps['api-key'], $tags);
			echo json_encode($return_object);
		}
		exit;
	}

	function admin_tags_more_html() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;
			if (!is_array($deps) || !array_key_exists('app-id', $deps) || empty($deps['app-id']) || !array_key_exists('api-key', $deps) || empty($deps['api-key']) || !array_key_exists('tags-selected', $deps)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (!array_key_exists('offset', $deps)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid offset.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (empty($deps['tags-selected'])) $tags = array();
			else $tags = explode(',', $deps['tags-selected']);
			$return_object = $this->_get_tags_html($deps['app-id'], $deps['api-key'], intval($deps['offset']), $tags, false);
			echo json_encode($return_object);
		}
		exit;
	}
	
	function get_tags_html($_app_id, $_api_key, $_tags) {
		global $wpdb, $lepopup;

		$tags_selected = $this->_get_tags_html($_app_id, $_api_key, 0, $_tags, true);
		if ($tags_selected['status'] == 'ERROR') return $tags_selected;

		$tags_all = $this->_get_tags_html($_app_id, $_api_key, 0, $_tags, false);
		if ($tags_all['status'] == 'ERROR') return $tags_all;
		
		$html = '
<div class="lepopup-integrations-ajax-multiselect" data-next-offset="'.esc_html($tags_all['offset']).'" data-action="tags-more" data-deps="app-id,api-key,tags-selected" onscroll="lepopup_integrations_ajax_multiselect_scroll(this);">
	'.$tags_selected['html'].$tags_all['html'].'
	<div class="lepopup-integrations-ajax-multiselect-loading"><i class="fas fa-spin fa-spinner"></i></div>
</div>';
		return array('status' => 'OK', 'html' => $html, 'offset' => $tags_all['offset']);
	}

	function _get_tags_html($_app_id, $_api_key, $_offset = 0, $_tags = array(), $_only_tags = false) {
		global $lepopup;
		if ($_only_tags && empty($_tags)) return array('status' => 'OK', 'html' => '', 'offset' => -1);
		$result = $this->connect($_app_id, $_api_key, 'Tags?'.($_only_tags ? 'ids='.implode(',',$_tags).'&' : '').'range=50&start='.$_offset.'&sort=tag_id&sortDir=asc');
		if (empty($result) || !is_array($result)) {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid API credentials .', 'lepopup'));
		}
		if (array_key_exists('data', $result) && empty($result['data'])) {
			return array('status' => 'ERROR', 'message' => esc_html__('No more Tags found.', 'lepopup'));
		}
		$html = '';
		foreach ($result['data'] as $tag) {
			if (!$_only_tags && in_array($tag['tag_id'], $_tags)) continue;
			$checkbox_id = $lepopup->random_string(16);
			$html .= '<div class="lepopup-integrations-ajax-multiselect-record"><input type="checkbox" class="lepopup-checkbox lepopup-checkbox-classic lepopup-checkbox-medium" id="lepopup-integrations-ontraport-'.esc_html($checkbox_id).'" name="tags['.esc_html($tag['tag_id']).']" value="'.esc_html($tag['tag_id']).'"'.($_only_tags ? ' checked="checked"' : '').'><label for="lepopup-integrations-ontraport-'.esc_html($checkbox_id).'"></label><label for="lepopup-integrations-ontraport-'.esc_html($checkbox_id).'">'.esc_html($tag['tag_name'].' (ID: '.$tag['tag_id'].')').'</label></div>';
		}
		if (sizeof($result['data']) >= 50) $_offset += 50;
		else $_offset = -1;
		return array('status' => 'OK', 'html' => $html, 'offset' => $_offset);
	}

	function admin_sequences_html() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;
			if (!is_array($deps) || !array_key_exists('app-id', $deps) || empty($deps['app-id']) || !array_key_exists('api-key', $deps) || empty($deps['api-key']) || !array_key_exists('sequences-selected', $deps)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (empty($deps['sequences-selected'])) $sequences = array();
			else $sequences = explode(',', $deps['sequences-selected']);
			$return_object = $this->get_sequences_html($deps['app-id'], $deps['api-key'], $sequences);
			echo json_encode($return_object);
		}
		exit;
	}

	function admin_sequences_more_html() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;
			if (!is_array($deps) || !array_key_exists('app-id', $deps) || empty($deps['app-id']) || !array_key_exists('api-key', $deps) || empty($deps['api-key']) || !array_key_exists('sequences-selected', $deps)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (!array_key_exists('offset', $deps)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid offset.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (empty($deps['sequences-selected'])) $sequences = array();
			else $sequences = explode(',', $deps['sequences-selected']);
			$return_object = $this->_get_sequences_html($deps['app-id'], $deps['api-key'], intval($deps['offset']), $sequences, false);
			echo json_encode($return_object);
		}
		exit;
	}
	
	function get_sequences_html($_app_id, $_api_key, $_sequences) {
		global $wpdb, $lepopup;

		$sequences_selected = $this->_get_sequences_html($_app_id, $_api_key, 0, $_sequences, true);
		if ($sequences_selected['status'] == 'ERROR') return $sequences_selected;

		$sequences_all = $this->_get_sequences_html($_app_id, $_api_key, 0, $_sequences, false);
		if ($sequences_all['status'] == 'ERROR') return $sequences_all;
		
		$html = '
<div class="lepopup-integrations-ajax-multiselect" data-next-offset="'.esc_html($sequences_all['offset']).'" data-action="sequences-more" data-deps="app-id,api-key,sequences-selected" onscroll="lepopup_integrations_ajax_multiselect_scroll(this);">
	'.$sequences_selected['html'].$sequences_all['html'].'
	<div class="lepopup-integrations-ajax-multiselect-loading"><i class="fas fa-spin fa-spinner"></i></div>
</div>';
		return array('status' => 'OK', 'html' => $html, 'offset' => $sequences_all['offset']);
	}

	function _get_sequences_html($_app_id, $_api_key, $_offset = 0, $_sequences = array(), $_only_sequences = false) {
		global $lepopup;
		if ($_only_sequences && empty($_sequences)) return array('status' => 'OK', 'html' => '', 'offset' => -1);
		$result = $this->connect($_app_id, $_api_key, 'Sequences?'.($_only_sequences ? 'ids='.implode(',',$_sequences).'&' : '').'range=50&start='.$_offset.'&sort=drip_id&sortDir=asc');
		if (empty($result) || !is_array($result)) {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid API credentials .', 'lepopup'));
		}
		if (array_key_exists('data', $result) && empty($result['data'])) {
			return array('status' => 'ERROR', 'message' => esc_html__('No more Sequences found.', 'lepopup'));
		}
		$html = '';
		foreach ($result['data'] as $sequence) {
			if (!$_only_sequences && in_array($sequence['drip_id'], $_sequences)) continue;
			$checkbox_id = $lepopup->random_string(16);
			$html .= '<div class="lepopup-integrations-ajax-multiselect-record"><input type="checkbox" class="lepopup-checkbox lepopup-checkbox-classic lepopup-checkbox-medium" id="lepopup-integrations-ontraport-'.esc_html($checkbox_id).'" name="sequences['.esc_html($sequence['drip_id']).']" value="'.esc_html($sequence['drip_id']).'"'.($_only_sequences ? ' checked="checked"' : '').'><label for="lepopup-integrations-ontraport-'.esc_html($checkbox_id).'"></label><label for="lepopup-integrations-ontraport-'.esc_html($checkbox_id).'">'.esc_html($sequence['name'].' (ID: '.$sequence['drip_id'].')').'</label></div>';
		}
		if (sizeof($result['data']) >= 50) $_offset += 50;
		else $_offset = -1;
		return array('status' => 'OK', 'html' => $html, 'offset' => $_offset);
	}
	
	function admin_fields_html() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;
			if (!is_array($deps) || !array_key_exists('app-id', $deps) || empty($deps['app-id']) || !array_key_exists('api-key', $deps) || empty($deps['api-key'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$return_object = $this->get_fields_html($deps['app-id'], $deps['api-key'], $this->default_parameters['fields']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_fields_html($_app_id, $_api_key, $_fields) {
		global $wpdb, $lepopup;
		$result = $this->connect($_app_id, $_api_key, 'Contacts/meta');
		if (empty($result) || !is_array($result)) {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid API credentials.', 'lepopup'));
		}
		if (array_key_exists('data', $result) && (empty($result['data']) || empty($result['data'][0]['fields']))) {
			return array('status' => 'ERROR', 'message' => esc_html__('No fields found.', 'lepopup'));
		}
		$fields_html = '
			<table>';
		foreach ($result['data'][0]['fields'] as $field_key => $field_value) {
			if (is_array($field_value)) {
				if (is_array($field_value)) {
					if ($field_value['editable']) {
						$fields_html .= '
				<tr>
					<th>'.esc_html($field_value['alias']).'</td>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="fields['.esc_html($field_key).']" value="'.esc_html(array_key_exists($field_key, $_fields) ? $_fields[$field_key] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<label class="lepopup-integrations-description">'.esc_html($field_key).'</label>
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
		if (empty($data['app-id']) || empty($data['api-key'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$post_data = $data['fields'];
		$post_data['objectID'] = 0;
		
		$result = $this->connect($data['app-id'], $data['api-key'], 'object/getByEmail?objectID=0&email='.$data['fields']['email']);
		if (is_array($result) && array_key_exists('data', $result) && array_key_exists('id', $result['data'])) {
			$object_id = $result['data']['id'];
			$post_data['id'] = $object_id;
			$result = $this->connect($data['app-id'], $data['api-key'], 'objects', $post_data, 'PUT');
		} else {
			$result = $this->connect($data['app-id'], $data['api-key'], 'objects', $post_data);
			if (is_array($result) && array_key_exists('data', $result) && array_key_exists('id', $result['data'])) $object_id = $result['data']['id'];
			else return $_result;
		}
		if (!empty($data['tags'])) {
			$post_data = array('objectID' => 0, 'add_list' => implode(',', $data['tags']), 'ids' => $object_id);
			$result = $this->connect($data['app-id'], $data['api-key'], 'objects/tag', $post_data, 'PUT');
		}
		if (!empty($data['sequences'])) {
			$post_data = array('objectID' => 0, 'add_list' => implode(',', $data['sequences']), 'ids' => $object_id);
			$result = $this->connect($data['app-id'], $data['api-key'], 'objects/subscribe', $post_data, 'PUT');
		}
		return $_result;
	}
	
	function connect($_app_id, $_api_key, $_path, $_data = array(), $_method = '') {
		$headers = array(
			'Api-Key: '.$_api_key,
			'Api-Appid: '.$_app_id
		);
		try {
			$url = 'https://api.ontraport.com/1/'.ltrim($_path, '/');
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($_data));
			}
			if (!empty($_method)) {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_method);
			}
			curl_setopt($curl, CURLOPT_TIMEOUT, 120);
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
$lepopup_ontraport = new lepopup_ontraport_class();
?>