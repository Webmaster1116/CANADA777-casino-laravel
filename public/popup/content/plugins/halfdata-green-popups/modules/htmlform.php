<?php
/* HTML Form integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_htmlform_class {
	var $default_parameters = array(
		"html" => "",
		"action" => "",
		"method" => "",
		"field-names" => array(),
		"field-values" => array(),
		"client-side" => "off",
		"target" => "iframe"
	);
	var $targets = array(
		'iframe' => 'Hidden iframe',
		'top' => 'Same browser tab'
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-htmlform-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-htmlform-connect', array(&$this, "connect"));
			add_action('wp_ajax_lepopup-htmlform-disconnect', array(&$this, "disconnect"));
		}
		add_filter('lepopup_integrations_do_htmlform', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("htmlform", $_providers)) $_providers["htmlform"] = esc_html__('HTML Form', 'lepopup');
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
			$form_options_html = $this->get_form_options($data['html'], $data['action'], $data['method'], $data['field-names'], $data['field-values']);
			$html = '
			<div class="lepopup-htmlform-form">'.$form_options_html.'</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Client side', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enable this option if you want form to be submitted from client side. Otherwise, it is submitted from server side.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input class="lepopup-checkbox-toggle" onchange="jQuery(this).is(\':checked\') ? jQuery(\'.lepopup-htmlform-client-only\').fadeIn(200) : jQuery(\'.lepopup-htmlform-client-only\').fadeOut(200);" type="checkbox" value="on" id="client-side-'.esc_html($checkbox_id).'" name="client-side"'.($data['client-side'] == 'on' ? ' checked="checked"' : '').' /><label for="client-side-'.esc_html($checkbox_id).'"></label>
				</div>
			</div>
			<div class="lepopup-properties-item lepopup-htmlform-client-only"'.($data['client-side'] != 'on' ? ' style="display:none;"' : '').'>
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Target', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select where to display the response that is received after submitting the form.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<select name="target">';
			foreach ($this->targets as $key => $value) {
				$html .= '
						<option value="'.esc_html($key).'"'.($data['target'] == $key ? ' selected="selected"' : '').'>'.esc_html($value).'</option>';
			}
			$html .= '
					
					</select>
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
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['action'])) return $_result;
		if ($data['client-side'] == 'off') {
			$request = array();
			foreach ($data['field-names'] as $key => $name) {
				$request[$name] = $data['field-values'][$key];
			}
			$action = $data['action'];
			if ($data['method'] == 'get') {
				if (strpos($action, '?') === false) $action .= '?'.http_build_query($request);
				else $action .= '&'.http_build_query($request);
			}
			if (substr($action, 0, 2) == '//') $action = 'http:'.$action;
			try {
				$curl = curl_init($action);
				if ($data['method'] != 'get') {
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($request));
				}
				curl_setopt($curl, CURLOPT_TIMEOUT, 10);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
				curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36');
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				$response = curl_exec($curl);
				curl_close($curl);
			} catch (Exception $e) {
			}
		} else {
			$form = '';
			$id = 'f'.$lepopup->random_string(16);
			if ($data['target'] == 'iframe') {
				$target = $id;
				$form = '<iframe style="display: none !important;" name="'.esc_html($id).'"></iframe>';
			} else $target = '_top';
			$form .= '<form style="display: none !important;" method="'.esc_html($data['method']).'" action="'.esc_html($data['action']).'" target="'.esc_html($target).'">';
			foreach ($data['field-names'] as $key => $name) {
				$form .= '<input type="hidden" name="'.esc_html($name).'" value="'.esc_html($data['field-values'][$key]).'">';
			}
			$form .= '<input class="lepopup-send" id="submit-'.esc_html($id).'" type="submit" value="Submit"></form>';
			if (!array_key_exists('forms', $_result)) $_result['forms'] = array();
			$_result['forms'][] = $form;
		}
		
		return $_result;
	}

	function connect() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (!isset($_POST['html'])) {
				$return_data = array();
				$return_data['status'] = 'ERROR';
				$return_data['message'] = esc_html__('Hey dude, you have done an invalid request.', 'lepopup');
				echo json_encode($return_data);
				exit;
			}
			$form_html = trim(stripslashes($_POST['html']));
			if (empty($form_html)) {
				$return_data = array();
				$return_data['status'] = 'ERROR';
				$return_data['message'] = esc_html__('Please copy-paste HTML-form.', 'lepopup');
				echo json_encode($return_data);
				exit;
			}
			
			if (function_exists('libxml_use_internal_errors')) libxml_use_internal_errors(true);
			
			$dom = new DOMDocument();
			$dom->loadHTML('<?xml encoding="utf-8" ?>'.$form_html);
			if (!$dom) {
				$return_data = array();
				$return_data['status'] = 'ERROR';
				$return_data['message'] = esc_html__('Can not parse provided HTML-code.', 'lepopup');
				echo json_encode($return_data);
				exit;
			}
			$xpath = new DOMXPath($dom);
			$dom_forms = $xpath->query('//form');
			if (!$dom_forms) {
				$return_data = array();
				$return_data['status'] = 'ERROR';
				$return_data['message'] = esc_html__('Can not parse provided HTML-code.', 'lepopup');
				echo json_encode($return_data);
				exit;
			}
			if ($dom_forms->length == 0) {
				$return_data = array();
				$return_data['status'] = 'ERROR';
				$return_data['message'] = esc_html__('Can not find any form in provided HTML-code.', 'lepopup');
				echo json_encode($return_data);
				exit;
			}
			if ($dom_forms->length > 1) {
				$return_data = array();
				$return_data['status'] = 'ERROR';
				$return_data['message'] = esc_html__('Too many forms found in provided HTML-code.', 'lepopup');
				echo json_encode($return_data);
				exit;
			}
			$action = $dom_forms->item(0)->getAttribute('action');
			if (empty($action)) {
				$return_data = array();
				$return_data['status'] = 'ERROR';
				$return_data['message'] = esc_html__('No "action" attribute found in provided HTML-form.', 'lepopup');
				echo json_encode($return_data);
				exit;
			}
			if (substr($action, 0, 2) != '//' && substr(strtolower($action), 0, 7) != 'http://' && substr(strtolower($action), 0, 8) != 'https://') {
				$return_data = array();
				$return_data['status'] = 'ERROR';
				$return_data['message'] = esc_html__('Form "action" attribute must be a full URL.', 'lepopup');
				echo json_encode($return_data);
				exit;
			}
			
			$method = strtolower($dom_forms->item(0)->getAttribute('method'));
			if (empty($method)) $method = 'get';
			
			$field_names = array();
			$field_values = array();
			$dom_inputs = $xpath->query('//input', $dom_forms->item(0));
			foreach ($dom_inputs as $input) {
				$name = $input->getAttribute('name');
				if (!empty($name)) {
					if (!in_array($name, $field_names)) {
						$field_names[] = $name;
						$field_values[] = $input->getAttribute('value');
					}
				}
			}
			$dom_inputs = $xpath->query('//textarea', $dom_forms->item(0));
			foreach ($dom_inputs as $input) {
				$name = $input->getAttribute('name');
				if (!empty($name)) {
					if (!in_array($name, $field_names)) {
						$field_names[] = $name;
						$field_values[] = $input->textContent;
					}
				}
			}
			$dom_inputs = $xpath->query('//select', $dom_forms->item(0));
			foreach ($dom_inputs as $input) {
				$name = $input->getAttribute('name');
				if (!empty($name)) {
					if (!in_array($name, $field_names)) {
						$dom_options = $xpath->query('//option', $dom_inputs->item(0));
						if ($dom_options->length > 0) {
							$field_names[] = $name;
							$field_values[] = $dom_options->item(0)->getAttribute('value');
						}
					}
				}
			}
			if (function_exists('libxml_clear_errors')) libxml_clear_errors();
			
			$return_data = array();
			$return_data['status'] = 'OK';
			$return_data['message'] = esc_html__('Form successfully connected.', 'lepopup');
			$return_data['html'] = $this->get_form_options(base64_encode($form_html), $action, $method, $field_names, $field_values);
			echo json_encode($return_data);
		}
		exit;
	}
	function disconnect() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (isset($_POST['html'])) {
				$form_html = base64_decode(trim(stripslashes($_POST['html'])));
				if (!$form_html) {
					$return_data = array();
					$return_data['status'] = 'ERROR';
					$return_data['message'] = esc_html__('Hey dude, you have done an invalid request.', 'lepopup');
					echo json_encode($return_data);
					exit;
				}
			} else $form_html = '';
			$return_data = array();
			$return_data['status'] = 'OK';
			$return_data['message'] = esc_html__('Form successfully disconnected.', 'lepopup');
			$return_data['html'] = $this->get_form_options($form_html, '');
			echo json_encode($return_data);
		}
		exit;
	}
	
	function get_form_options($_html_form = '', $_action = '', $_method = '', $_field_names = array(), $_field_values = array()) {
		$html = '';
		if (empty($_action)) {
			$html .= '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('HTML Form', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Copy-paste HTML-code of 3rd party form and connect it. Important! It must be pure HTML-form with form-tag, but not javascript-driven form.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<textarea name="html" style="height: 120px;">'.esc_html($_html_form).'</textarea>
					<input type="hidden" name="action" value="">
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_htmlform_connect(this);"><i class="fas fa-random"></i><label>'.esc_html__('Connect Form', 'lepopup').'</label></a>
				</div>
			</div>';
		} else {
			$html .= '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Action URL', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Specifies where to send the form data when it is submitted.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<code>'.esc_html($_action).'</code>
					<input type="hidden" name="action" value="'.esc_html($_action).'" />
					<input type="hidden" name="html" value="'.esc_html($_html_form).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Method', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Specifies how to send the form data when it is submitted.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<code>'.esc_html(strtoupper($_method)).'</code>
					<input type="hidden" name="method" value="'.esc_html($_method).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to HTML Form fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline lepopup-integrations-custom" data-names="field-names" data-values="field-values" data-all="on">';
			foreach ($_field_names as $key => $name) {
				if (empty($name)) continue;
				$html .= '<table>
							<tr>
								<th>
									'.esc_html($name).'
									<input type="hidden" value="'.esc_html($name).'" class="lepopup-integrations-custom-name" data-custom="on" />
								</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" value="'.esc_html($_field_values[$key]).'" class="lepopup-integrations-custom-value widefat" data-custom="on" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
								</td>
							</tr>
						</table>';
			}
			$html .= '
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Disconnect', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Click the button to disconnect current HTML form.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_htmlform_disconnect(this);"><i class="fas fa-times"></i><label>'.esc_html__('Disconnect Form', 'lepopup').'</label></a>
				</div>
			</div>';
		}
		return $html;
	}
}
$lepopup_htmlform = new lepopup_htmlform_class();
?>