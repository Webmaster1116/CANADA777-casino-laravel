<?php
/* VerticalResponse integration for Green Popups */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_verticalresponse_class {
	var $options = array(
		"verticalresponse-connecting" => 0,
		"verticalresponse-client-id" => "",
		"verticalresponse-client-secret" => "",
		"verticalresponse-redirect-uri" => "",
		"verticalresponse-access-token" => "",
		"verticalresponse-connection-data" => array()
	);
	var $default_parameters = array(
		"fields" => array('email' => ''),
		'list-id' => ''
	);
	
	function __construct() {
		$this->get_options();
		add_action('init', array(&$this, 'admin_init'));
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('lepopup_options_show', array(&$this, 'admin_options_show'));
			add_action('wp_ajax_lepopup-verticalresponse-init-connection', array(&$this, "admin_init_connection"));
			add_action('wp_ajax_nopriv_lepopup-verticalresponse-init-connection', array(&$this, "admin_init_connection"));
			add_action('wp_ajax_lepopup-verticalresponse-disconnect', array(&$this, "admin_disconnect"));
			add_action('wp_ajax_lepopup-verticalresponse-connected', array(&$this, "admin_connected"));
			add_action('wp_ajax_lepopup-verticalresponse-settings-html', array(&$this, "admin_settings_html"));
		}
		add_filter('lepopup_integrations_do_verticalresponse', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("verticalresponse", $_providers)) $_providers["verticalresponse"] = esc_html__('VerticalResponse', 'lepopup');
		return $_providers;
	}

	function get_options() {
		foreach ($this->options as $key => $value) {
			$this->options[$key] = get_option('lepopup-'.$key, $this->options[$key]);
		}
	}
	function update_options() {
		//if (current_user_can('manage_options')) {
			foreach ($this->options as $key => $value) {
				update_option('lepopup-'.$key, $value);
			}
		//}
	}
	function populate_options() {
		foreach ($this->options as $key => $value) {
			if (isset($_POST['lepopup-'.$key])) {
				$this->options[$key] = trim(stripslashes($_POST['lepopup-'.$key]));
			}
		}
	}
	function admin_options_show() {
		echo '
			<h3 id="verticalresponse-settings">'.esc_html__('VerticalResponse Connection', 'lepopup').'</h3>';
		$account = null;
		if (!$this->options['verticalresponse-access-token']) {
			echo '
			<div id="lepopup-verticalresponse-connection">
				<table class="lepopup_useroptions">
					<tr>
						<th>'.esc_html__('Connect', 'lepopup').':</th>
						<td>
							<a class="lepopup-button lepopup-button-small" onclick="window.open(\''.admin_url('admin.php').'?action=lepopup-verticalresponse-connect\', \'_blank\', \'height=480,width=720,menubar=no,scrollbars=no,status=no,toolbar=no\'); return false;"><i class="fas fa-check"></i><label>'.esc_html__('Connect to VerticalResponse', 'lepopup').'</label></a>
							<br /><em>'.esc_html__('Click the button to connect to VerticalResponse.', 'lepopup').'</em>
						</td>
					</tr>
				</table>
			</div>';
		} else {
			echo '
			<div id="lepopup-verticalresponse-connection">
				<table class="lepopup_useroptions">
					<tr>
						<th>'.esc_html__('Connected', 'lepopup').':</th>
						<td>
							<a class="lepopup-button lepopup-button-small" onclick="return lepopup_verticalresponse_disconnect(this);"><i class="fas fa-times"></i><label>'.esc_html__('Disconnect from VerticalResponse', 'lepopup').'</label></a>
							<br /><em>'.esc_html__('Click the button to disconnect from VerticalResponse.', 'lepopup').'</em>
						</td>
					</tr>
				</table>
			</div>';
		}
		echo '
			<script>
				var lepopup_verticalresponse_connecting = false;
				function lepopup_verticalresponse_connected() {
					if (lepopup_verticalresponse_connecting) return false;
					var button_object = jQuery("#lepopup-verticalresponse-connection .lepopup-button");
					jQuery(button_object).find("i").attr("class", "fas fa-spinner fa-spin");
					jQuery(button_object).addClass("lepopup-button-disabled");
					lepopup_verticalresponse_connecting = true;
					var post_data = {"action" : "lepopup-verticalresponse-connected"};
					jQuery.ajax({
						type	: "POST",
						url		: "'.admin_url('admin-ajax.php').'", 
						data	: post_data,
						success	: function(return_data) {
							jQuery(button_object).find("i").attr("class", "fas fa-times");
							jQuery(button_object).removeClass("lepopup-button-disabled");
							try {
								var data = jQuery.parseJSON(return_data);
								if (data.status == "OK") {
									jQuery("#lepopup-verticalresponse-connection").slideUp(350, function() {
										jQuery("#lepopup-verticalresponse-connection").html(data.html);
										jQuery("#lepopup-verticalresponse-connection").slideDown(350);
									});
									lepopup_global_message_show("success", data.message);
								} else if (data.status == "ERROR") {
									lepopup_global_message_show("danger", data.message);
								} else {
									lepopup_global_message_show("danger", "Something went wrong. We got unexpected server response.");
								}
							} catch(error) {
								lepopup_global_message_show("danger", "Something went wrong. We got unexpected server response.");
							}
							lepopup_verticalresponse_connecting = false;
						},
						error	: function(XMLHttpRequest, textStatus, errorThrown) {
							jQuery(button_object).find("i").attr("class", "fas fa-times");
							jQuery(button_object).removeClass("lepopup-button-disabled");
							lepopup_global_message_show("danger", "Something went wrong. We got unexpected server response.");
							lepopup_verticalresponse_connecting = false;
						}
					});
					return false;
				}
				function lepopup_verticalresponse_disconnect(_button) {
					if (lepopup_verticalresponse_connecting) return false;
					var button_object = _button;
					jQuery(button_object).find("i").attr("class", "fas fa-spinner fa-spin");
					jQuery(button_object).addClass("lepopup-button-disabled");
					lepopup_verticalresponse_connecting = true;
					var post_data = {"action" : "lepopup-verticalresponse-disconnect"};
					jQuery.ajax({
						type	: "POST",
						url		: "'.admin_url('admin-ajax.php').'", 
						data	: post_data,
						success	: function(return_data) {
							jQuery(button_object).find("i").attr("class", "fas fa-times");
							jQuery(button_object).removeClass("lepopup-button-disabled");
							try {
								var data = jQuery.parseJSON(return_data);
								if (data.status == "OK") {
									jQuery("#lepopup-verticalresponse-connection").slideUp(350, function() {
										jQuery("#lepopup-verticalresponse-connection").html(data.html);
										jQuery("#lepopup-verticalresponse-connection").slideDown(350);
									});
									lepopup_global_message_show("success", data.message);
								} else if (data.status == "ERROR") {
									lepopup_global_message_show("danger", data.message);
								} else {
									lepopup_global_message_show("danger", "Something went wrong. We got unexpected server response.");
								}
							} catch(error) {
								lepopup_global_message_show("danger", "Something went wrong. We got unexpected server response.");
							}
							lepopup_verticalresponse_connecting = false;
						},
						error	: function(XMLHttpRequest, textStatus, errorThrown) {
							jQuery(button_object).find("i").attr("class", "fas fa-times");
							jQuery(button_object).removeClass("lepopup-button-disabled");
							lepopup_global_message_show("danger", "Something went wrong. We got unexpected server response.");
							lepopup_verticalresponse_connecting = false;
						}
					});
					return false;
				}
			</script>';
	}

	function admin_init_connection() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options')) {
			$this->options["verticalresponse-connection-data"] = array();
			if (array_key_exists("redirect-uri", $_REQUEST)) $this->options["verticalresponse-connection-data"]["redirect-uri"] = trim(stripslashes($_REQUEST['redirect-uri']));
			else $this->options["verticalresponse-connection-data"]["redirect-uri"] = '';
			if (array_key_exists("client-id", $_REQUEST)) $this->options["verticalresponse-connection-data"]["client-id"] = trim(stripslashes($_REQUEST['client-id']));
			else $this->options["verticalresponse-connection-data"]["client-id"] = '';
			if (array_key_exists("client-secret", $_REQUEST)) $this->options["verticalresponse-connection-data"]["client-secret"] = trim(stripslashes($_REQUEST['client-secret']));
			else $this->options["verticalresponse-connection-data"]["client-secret"] = '';
			$this->options["verticalresponse-connecting"] = time();
			$this->update_options();
			$return_data = array(
				'status' => 'OK',
				'url' => 'https://vrapi.verticalresponse.com/api/v1/oauth/authorize?client_id='.urlencode($this->options["verticalresponse-connection-data"]["client-id"]).'&redirect_uri='.urlencode($this->options["verticalresponse-connection-data"]["redirect-uri"])
			);
			echo json_encode($return_data);
		}
		exit;
	}

	function admin_init() {
		global $wpdb, $lepopup;
		if (array_key_exists('REQUEST_URI', $_SERVER) && strpos($_SERVER['REQUEST_URI'], 'lepopup-verticalresponse-redirect-uri') !== false && current_user_can('manage_options')) {
			if (array_key_exists("code", $_REQUEST) && $this->options["verticalresponse-connecting"] + 300 > time()) {
				$result = $this->connect_access_token($this->options["verticalresponse-connection-data"]["client-id"], $this->options["verticalresponse-connection-data"]["client-secret"], $this->options["verticalresponse-connection-data"]["redirect-uri"], $_REQUEST['code']);
				if (is_array($result) && array_key_exists('access_token', $result)) {
					$this->options['verticalresponse-access-token'] = $result['access_token'];
					$this->options['verticalresponse-redirect-uri'] = $this->options["verticalresponse-connection-data"]["redirect-uri"];
					$this->options['verticalresponse-client-id'] = $this->options["verticalresponse-connection-data"]["client-id"];
					$this->options['verticalresponse-client-secret'] = $this->options["verticalresponse-connection-data"]["client-secret"];
					$this->options["verticalresponse-connecting"] = 0;
					$this->update_options();
					$content = esc_html__('Success!', 'lepopup').'<script>window.opener.lepopup_verticalresponse_connected(); window.close();</script>';
				} else if (is_array($result)) {
					$content = esc_html__('Invalid connection credentials. Please try again.', 'lepopup').'<div><a class="button" href="'.admin_url('admin-ajax.php').'?action=lepopup-verticalresponse-connect"><i class="fas fa-check"></i><label>'.esc_html__('Try again', 'lepopup').'</label></a></div>';
				} else {
					$content = esc_html__('Something went wrong. We got unexpected server response. Please try again.', 'lepopup').'<div><a class="button" href="'.admin_url('admin-ajax.php').'?action=lepopup-verticalresponse-connect"><i class="fas fa-check"></i><label>'.esc_html__('Try again', 'lepopup').'</label></a></div>';
				}
			} else {
				$content = esc_html__('Something went wrong. We got unexpected server response. Please try again.', 'lepopup').'<div><a class="button" href="'.admin_url('admin-ajax.php').'?action=lepopup-verticalresponse-connect"><i class="fas fa-check"></i><label>'.esc_html__('Try again', 'lepopup').'</label></a></div>';
			}
			echo '
<!DOCTYPE html>
<html>
<head>
	<title>'.esc_html__('Connect to VerticalResponse', 'lepopup').'</title>
	<link rel="stylesheet" media="all" href="'.$lepopup->plugins_url.'/css/oauth.css" />
	<link rel="stylesheet" media="all" href="'.$lepopup->plugins_url.'/css/fontawesome-all.min.css" />
	<script src="'.$lepopup->plugins_url.'/js/jquery.min.js" type="text/javascript"></script>
</head>
<body>
	<div class="main-container">
		<h1>'.esc_html__('Connect to VerticalResponse', 'lepopup').'</h1>
		<div class="content">'.$content.'</div>
	</div>
</body>
</html>';
			exit;
		}
		if (array_key_exists('action', $_REQUEST) && $_REQUEST['action'] == 'lepopup-verticalresponse-connect') {
			if (current_user_can('manage_options')) {
				echo '
<!DOCTYPE html>
<html>
<head>
	<title>'.esc_html__('Connect to VerticalResponse', 'lepopup').'</title>
	<link rel="stylesheet" media="all" href="'.$lepopup->plugins_url.'/css/oauth.css" />
	<link rel="stylesheet" media="all" href="'.$lepopup->plugins_url.'/css/fontawesome-all.min.css" />
	<script src="'.$lepopup->plugins_url.'/js/jquery.min.js" type="text/javascript"></script>
	<script>
		var connecting = false;
		function connect(_button) {
			if (connecting) return false;
			var button_object = _button;
			jQuery(button_object).find("i").attr("class", "fas fa-spinner fa-spin");
			jQuery(button_object).addClass("button-disabled");
			connecting = true;
			var post_data = {"action" : "lepopup-verticalresponse-init-connection", "redirect-uri" : jQuery("#redirect-uri").val(), "client-id" : jQuery("#client-id").val(), "client-secret" : jQuery("#client-secret").val()};
			jQuery.ajax({
				type	: "POST",
				url		: "'.admin_url('admin-ajax.php').'", 
				data	: post_data,
				success	: function(return_data) {
					jQuery(button_object).find("i").attr("class", "fas fa-check");
					jQuery(button_object).removeClass("button-disabled");
					try {
						var data = jQuery.parseJSON(return_data);
						if (data.status == "OK") {
							location.href = data.url;
						} else if (data.status == "ERROR") {
							alert(data.message);
						} else {
							alert(\'Something went wrong. We got unexpected server response.\');
						}
					} catch(error) {
						alert(\'Something went wrong. We got unexpected server response.\');
					}
					connecting = false;
				},
				error	: function(XMLHttpRequest, textStatus, errorThrown) {
					jQuery(button_object).find("i").attr("class", "fas fa-check");
					jQuery(button_object).removeClass("button-disabled");
					connecting = false;
				}
			});
			return false;
		}
	</script>
</head>
<body>
	<div class="main-container">
		<h1>'.esc_html__('Connect to VerticalResponse', 'lepopup').'</h1>
		<ol>
			<li>
				Register new Application (create Client ID) in <a data-href="https://developer.verticalresponse.com/apps/register" target="_blank" href="https://developer.verticalresponse.com/apps/register">VerticalResponse Developer Console</a>.
				<input type="hidden" readonly="readonly" id="redirect-uri" name="redirect-uri" onclick="this.focus();this.select();" value="'.(defined('UAP_CORE') ? esc_html(admin_url('do.php')) : esc_html(rtrim(get_bloginfo('url'), '/'))).'/lepopup-verticalresponse-redirect-uri/'.'" />
			</li>
			<li>
				Enter Client ID / Key and Client Secret into fields below.
				<input type="text" id="client-id" name="client-id" value="" placeholder="'.esc_html__('Client ID / Key', 'lepopup').'" />
				<input type="text" id="client-secret" name="client-secret" value="" placeholder="'.esc_html__('Client Secret', 'lepopup').'" />
			</li>
		</ol>
		<div class="button-container">
			<a class="button" href="#" onclick="return connect(this);"><i class="fas fa-check"></i><label>'.esc_html__('Connect to VerticalResponse', 'lepopup').'</label></a>			
		</div>
	</div>
</body>
</html>';
			}
			exit;
		}
	}

	function admin_disconnect() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			$this->options['verticalresponse-access-token'] = '';
			$this->options['verticalresponse-redirect-uri'] = '';
			$this->options['verticalresponse-client-id'] = '';
			$this->options['verticalresponse-client-secret'] = '';
			$this->options["verticalresponse-connection-data"] = array();
			$this->update_options();
					
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['message'] = esc_html__('Successfully disconnected from VerticalResponse!', 'lepopup');
			$return_object['html'] = '
				<table class="lepopup_useroptions">
					<tr>
						<th>'.esc_html__('Connect', 'lepopup').':</th>
						<td>
							<a class="lepopup-button lepopup-button-small" onclick="window.open(\''.admin_url('admin.php').'?action=lepopup-verticalresponse-connect\', \'_blank\', \'height=480,width=720,menubar=no,scrollbars=no,status=no,toolbar=no\'); return false;"><i class="fas fa-check"></i><label>'.esc_html__('Connect to VerticalResponse', 'lepopup').'</label></a>
							<br /><em>'.esc_html__('Click the button to connect to VerticalResponse.', 'lepopup').'</em>
						</td>
					</tr>
				</table>';
			echo json_encode($return_object);
			exit;
		}
		exit;
	}

	function admin_connected() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['message'] = esc_html__('Successfully connected to VerticalResponse!', 'lepopup');
			$return_object['html'] = '
			<table class="lepopup_useroptions">
				<tr>
					<th>'.esc_html__('Connected', 'lepopup').':</th>
					<td>
						<a class="lepopup-button lepopup-button-small" onclick="return lepopup_verticalresponse_disconnect(this);"><i class="fas fa-times"></i><label>'.esc_html__('Disconnect from VerticalResponse', 'lepopup').'</label></a>
						<br /><em>'.esc_html__('Click the button to disconnect from VerticalResponse.', 'lepopup').'</em>
					</td>
				</tr>
			</table>';
			echo json_encode($return_object);
			exit;
		}
		exit;
	}
	
	function admin_settings_html() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options')) {
			if (empty($this->options['verticalresponse-access-token'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Connect your VerticalResponse account on General Settings page.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			
			$lists = $this->connect_api($this->options['verticalresponse-access-token'], 'lists');
			if (!array_key_exists('count', $lists)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Connect your VerticalResponse account on General Settings page.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			} else if ($lists['count'] == 0) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Create at least one list in your VerticalResponse account.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$fields = $this->connect_api($this->options['verticalresponse-access-token'], 'contacts/fields?type=all');
			if (!array_key_exists('attributes', $fields)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Connect your VerticalResponse account on General Settings page.', 'lepopup'));
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
					<label>'.esc_html__('List ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select desired List ID.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<select name="list-id" class="widefat">';
			foreach ($lists['items'] as $list) {
				$html .= '
						<option value="'.esc_html($list['attributes']['id']).'"'.($list['attributes']['id'] == $data['list-id'] ? ' selected="selected"' : '').'>'.esc_html($list['attributes']['id'].' | '.$list['attributes']['name']).'</option>';
			}
			$html .= '
					</select>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to VerticalResponse fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>';
			foreach ($fields['attributes'] as $field_id => $field_label) {
				if ($field_id == 'custom') continue;
				$html .= '
							<tr>
								<th>'.esc_html($field_label).'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields['.esc_html($field_id).']" value="'.esc_html(array_key_exists($field_id, $data['fields']) ? $data['fields'][$field_id] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html($field_label).' ('.esc_html($field_id).')</label>
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
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		if ($this->options['verticalresponse-access-token']) {
			$post_data = array();
			foreach ($data['fields'] as $key => $value) {
				if (!empty($value)) $post_data[$key] = $value;
			}
			$result = $this->connect_api($this->options['verticalresponse-access-token'], 'contacts?email='.urlencode($data['fields']['email']));
			if (empty($result) || !array_key_exists('count', $result) || (array_key_exists('count', $result) && $result['count'] == 0)) {
				$result = $this->connect_api($this->options['verticalresponse-access-token'], 'lists/'.urlencode($data['list-id']).'/contacts', $post_data);
			} else {
				unset($post_data['email']);
				$result = $this->connect_api($this->options['verticalresponse-access-token'], 'contacts/'.urlencode($result['items'][0]['attributes']['id']), $post_data, 'PUT');
				$result = $this->connect_api($this->options['verticalresponse-access-token'], 'lists/'.urlencode($data['list-id']).'/contacts', array('email' => $data['fields']['email']));
			}
		}
		return $_result;
	}
	
	function connect_access_token($_client_id, $_client_secret, $_redirect_uri, $_code) {
		try {
			$url = 'https://vrapi.verticalresponse.com/api/v1/oauth/access_token?client_id='.urlencode($_client_id).'&client_secret='.urlencode($_client_secret).'&redirect_uri='.urlencode($_redirect_uri).'&code='.urlencode($_code);
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_POST, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
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
	function connect_api($_token, $_path, $_data = array(), $_method = '') {
		try {
			$headers = array(
				'Content-Type: application/json;charset=UTF-8',
				'Accept: application/json',
				'Authorization: Bearer '.$_token
			);
			$url = 'https://vrapi.verticalresponse.com/api/v1/'.ltrim($_path, '/');
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($_data));
			}
			if (!empty($_method)) {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_method);
			}
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
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
$lepopup_verticalresponse = new lepopup_verticalresponse_class();
?>