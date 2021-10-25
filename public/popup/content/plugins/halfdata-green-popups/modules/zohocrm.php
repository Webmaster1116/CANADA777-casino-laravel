<?php
/* Zoho CRM integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_zohocrm_class {
	var $options = array(
		"zohocrm-connecting" => 0,
		"zohocrm-client-id" => "",
		"zohocrm-client-secret" => "",
		"zohocrm-redirect-uri" => "",
		"zohocrm-dc" => "",
		"zohocrm-refresh-token" => "",
		"zohocrm-api-domain" => "",
		"zohocrm-connection-data" => array()
	);
	var $default_parameters = array(
		"fields" => array('Email' => '', 'Company' => 'My Company', 'Last_Name' => '')
	);
	
	function __construct() {
		$this->get_options();
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('lepopup_options_show', array(&$this, 'admin_options_show'));
			add_action('wp_ajax_lepopup-zohocrm-connect', array(&$this, "admin_connect"));
			add_action('wp_ajax_nopriv_lepopup-zohocrm-connect', array(&$this, "admin_connect"));
			add_action('wp_ajax_lepopup-zohocrm-disconnect', array(&$this, "admin_disconnect"));
			add_action('wp_ajax_lepopup-zohocrm-connected', array(&$this, "admin_connected"));
			add_action('wp_ajax_lepopup-zohocrm-settings-html', array(&$this, "admin_settings_html"));
		}
		add_filter('lepopup_integrations_do_zohocrm', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("zohocrm", $_providers)) $_providers["zohocrm"] = esc_html__('Zoho CRM', 'lepopup');
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
			<h3 id="zohocrm-settings">'.esc_html__('Zoho CRM Connection', 'lepopup').'</h3>';
		$account = null;
		if ($this->options['zohocrm-refresh-token']) {
			$data = array(
				'refresh_token' => $this->options["zohocrm-refresh-token"],
				'client_id' => $this->options["zohocrm-client-id"],
				'client_secret' => $this->options["zohocrm-client-secret"],
				'grant_type' => 'refresh_token'
			);
			$result = $this->connect_auth($this->options["zohocrm-dc"], '', $data);				
			if (is_array($result) && array_key_exists('access_token', $result)) {
				$account = true;
			}
		}
		if (!$account) {
			echo '
			<div id="lepopup-zohocrm-connection">
				<table class="lepopup_useroptions">
					<tr>
						<th>'.esc_html__('Connect', 'lepopup').':</th>
						<td>
							<a class="lepopup-button lepopup-button-small" onclick="window.open(\''.admin_url('admin-ajax.php').'?action=lepopup-zohocrm-connect\', \'_blank\', \'height=560,width=720,menubar=no,scrollbars=no,status=no,toolbar=no\'); return false;"><i class="fas fa-check"></i><label>'.esc_html__('Connect to Zoho CRM', 'lepopup').'</label></a>
							<br /><em>'.esc_html__('Click the button to connect to Zoho CRM.', 'lepopup').'</em>
						</td>
					</tr>
				</table>
			</div>';
		} else {
			echo '
			<div id="lepopup-zohocrm-connection">
				<table class="lepopup_useroptions">
					<tr>
						<th>'.esc_html__('Connected', 'lepopup').':</th>
						<td>
							<a class="lepopup-button lepopup-button-small" onclick="return lepopup_zohocrm_disconnect(this);"><i class="fas fa-times"></i><label>'.esc_html__('Disconnect from Zoho CRM', 'lepopup').'</label></a>
							<br /><em>'.esc_html__('Click the button to disconnect from Zoho CRM.', 'lepopup').'</em>
						</td>
					</tr>
				</table>
			</div>';
		}
		echo '
			<script>
				var lepopup_zohocrm_connecting = false;
				function lepopup_zohocrm_connected() {
					if (lepopup_zohocrm_connecting) return false;
					var button_object = jQuery("#lepopup-zohocrm-connection .lepopup-button");
					jQuery(button_object).find("i").attr("class", "fas fa-spinner fa-spin");
					jQuery(button_object).addClass("lepopup-button-disabled");
					lepopup_zohocrm_connecting = true;
					var post_data = {"action" : "lepopup-zohocrm-connected"};
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
									jQuery("#lepopup-zohocrm-connection").slideUp(350, function() {
										jQuery("#lepopup-zohocrm-connection").html(data.html);
										jQuery("#lepopup-zohocrm-connection").slideDown(350);
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
							lepopup_zohocrm_connecting = false;
						},
						error	: function(XMLHttpRequest, textStatus, errorThrown) {
							jQuery(button_object).find("i").attr("class", "fas fa-times");
							jQuery(button_object).removeClass("lepopup-button-disabled");
							lepopup_global_message_show("danger", "Something went wrong. We got unexpected server response.");
							lepopup_zohocrm_connecting = false;
						}
					});
					return false;
				}
				function lepopup_zohocrm_disconnect(_button) {
					if (lepopup_zohocrm_connecting) return false;
					var button_object = _button;
					jQuery(button_object).find("i").attr("class", "fas fa-spinner fa-spin");
					jQuery(button_object).addClass("lepopup-button-disabled");
					lepopup_zohocrm_connecting = true;
					var post_data = {"action" : "lepopup-zohocrm-disconnect"};
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
									jQuery("#lepopup-zohocrm-connection").slideUp(350, function() {
										jQuery("#lepopup-zohocrm-connection").html(data.html);
										jQuery("#lepopup-zohocrm-connection").slideDown(350);
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
							lepopup_zohocrm_connecting = false;
						},
						error	: function(XMLHttpRequest, textStatus, errorThrown) {
							jQuery(button_object).find("i").attr("class", "fas fa-times");
							jQuery(button_object).removeClass("lepopup-button-disabled");
							lepopup_global_message_show("danger", "Something went wrong. We got unexpected server response.");
							lepopup_zohocrm_connecting = false;
						}
					});
					return false;
				}
			</script>';
	}

	function admin_connect() {
		global $wpdb, $lepopup;
		if (array_key_exists("do", $_REQUEST)) {
			switch ($_REQUEST['do']) {
				case 'save-data':
					if (current_user_can('manage_options')) {
						$this->options["zohocrm-connection-data"] = array();
						if (array_key_exists("domain", $_REQUEST)) $this->options["zohocrm-connection-data"]["domain"] = trim(stripslashes($_REQUEST['domain']));
						else $this->options["zohocrm-connection-data"]["domain"] = 'com';
						if (array_key_exists("redirect-uri", $_REQUEST)) $this->options["zohocrm-connection-data"]["redirect-uri"] = trim(stripslashes($_REQUEST['redirect-uri']));
						else $this->options["zohocrm-connection-data"]["redirect-uri"] = '';
						if (array_key_exists("client-id", $_REQUEST)) $this->options["zohocrm-connection-data"]["client-id"] = trim(stripslashes($_REQUEST['client-id']));
						else $this->options["zohocrm-connection-data"]["client-id"] = '';
						if (array_key_exists("client-secret", $_REQUEST)) $this->options["zohocrm-connection-data"]["client-secret"] = trim(stripslashes($_REQUEST['client-secret']));
						else $this->options["zohocrm-connection-data"]["client-secret"] = '';
						$this->options["zohocrm-connecting"] = time();
						$this->update_options();
						$return_data = array(
							'status' => 'OK',
							'url' => 'https://accounts.zoho.'.$this->options["zohocrm-connection-data"]["domain"].'/oauth/v2/auth?scope=ZohoCRM.modules.ALL,ZohoCRM.settings.ALL&client_id='.urlencode($this->options["zohocrm-connection-data"]["client-id"]).'&response_type=code&access_type=offline&redirect_uri='.urlencode($this->options["zohocrm-connection-data"]["redirect-uri"])
						);
						echo json_encode($return_data);
					}
					exit;
					break;
					
				case 'connect':
					if (array_key_exists("code", $_REQUEST) && $this->options["zohocrm-connecting"] + 300 > time()) {
						$data = array(
							'code' => $_REQUEST['code'],
							'redirect_uri' => $this->options["zohocrm-connection-data"]["redirect-uri"],
							'client_id' => $this->options["zohocrm-connection-data"]["client-id"],
							'client_secret' => $this->options["zohocrm-connection-data"]["client-secret"],
							'grant_type' => 'authorization_code'
						);
						$result = $this->connect_auth($this->options["zohocrm-connection-data"]["domain"], '', $data);
						if (is_array($result) && array_key_exists('refresh_token', $result)) {
							$this->options['zohocrm-refresh-token'] = $result['refresh_token'];
							$this->options['zohocrm-api-domain'] = $result['api_domain'];
							$this->options['zohocrm-dc'] = $this->options["zohocrm-connection-data"]["domain"];
							$this->options['zohocrm-redirect-uri'] = $this->options["zohocrm-connection-data"]["redirect-uri"];
							$this->options['zohocrm-client-id'] = $this->options["zohocrm-connection-data"]["client-id"];
							$this->options['zohocrm-client-secret'] = $this->options["zohocrm-connection-data"]["client-secret"];
							$this->options["zohocrm-connecting"] = 0;
							$this->update_options();
							$content = esc_html__('Success!', 'lepopup').'<script>window.opener.lepopup_zohocrm_connected(); window.close();</script>';
						} else if (is_array($result)) {
							$content = esc_html__('Invalid connection credentials. Please try again.', 'lepopup').'<div><a class="button" href="'.admin_url('admin-ajax.php').'?action=lepopup-zohocrm-connect"><i class="fas fa-check"></i><label>'.esc_html__('Try again', 'lepopup').'</label></a></div>';
						} else {
							$content = esc_html__('Something went wrong. We got unexpected server response. Please try again.', 'lepopup').'<div><a class="button" href="'.admin_url('admin-ajax.php').'?action=lepopup-zohocrm-connect"><i class="fas fa-check"></i><label>'.esc_html__('Try again', 'lepopup').'</label></a></div>';
						}
					} else {
						$content = esc_html__('Something went wrong. We got unexpected server response. Please try again.', 'lepopup').'<div><a class="button" href="'.admin_url('admin-ajax.php').'?action=lepopup-zohocrm-connect"><i class="fas fa-check"></i><label>'.esc_html__('Try again', 'lepopup').'</label></a></div>';
					}
					break;
					
				default:
					if (current_user_can('manage_options')) {
						$content = esc_html__('Invalid URL. Please try again.', 'lepopup').'<div><a class="button" href="'.admin_url('admin-ajax.php').'?action=lepopup-zohocrm-connect"><i class="fas fa-check"></i><label>'.esc_html__('Try again', 'lepopup').'</label></a></div>';
					} else {
						$content = esc_html__('Invalid URL.', 'lepopup');
					}
					break;
			}
			echo '
<!DOCTYPE html>
<html>
<head>
	<title>'.esc_html__('Connect to Zoho CRM', 'lepopup').'</title>
	<link rel="stylesheet" media="all" href="'.$lepopup->plugins_url.'/css/oauth.css" />
	<link rel="stylesheet" media="all" href="'.$lepopup->plugins_url.'/css/fontawesome-all.min.css" />
	<script src="'.$lepopup->plugins_url.'/js/jquery.min.js" type="text/javascript"></script>
</head>
<body>
	<div class="main-container">
		<h1>'.esc_html__('Connect to Zoho CRM', 'lepopup').'</h1>
		<div class="content">'.$content.'</div>
	</div>
</body>
</html>';
			exit;
		}
		if (current_user_can('manage_options')) {
			echo '
<!DOCTYPE html>
<html>
<head>
	<title>'.esc_html__('Connect to Zoho CRM', 'lepopup').'</title>
	<link rel="stylesheet" media="all" href="'.$lepopup->plugins_url.'/css/oauth.css" />
	<link rel="stylesheet" media="all" href="'.$lepopup->plugins_url.'/css/fontawesome-all.min.css" />
	<script src="'.$lepopup->plugins_url.'/js/jquery.min.js" type="text/javascript"></script>
	<script>
		function domain_changed() {
			jQuery("a").each(function() {
				var href = jQuery(this).attr("data-href");
				if (href) {
					href = href.replace("{domain}", jQuery("#domain").val());
					jQuery(this).attr("href", href);
				}
			});
		}
		var connecting = false;
		function connect(_button) {
			if (connecting) return false;
			var button_object = _button;
			jQuery(button_object).find("i").attr("class", "fas fa-spinner fa-spin");
			jQuery(button_object).addClass("button-disabled");
			connecting = true;
			var post_data = {"action" : "lepopup-zohocrm-connect", "do" : "save-data", "domain" : jQuery("#domain").val(), "redirect-uri" : jQuery("#redirect-uri").val(), "client-id" : jQuery("#client-id").val(), "client-secret" : jQuery("#client-secret").val()};
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
		<h1>'.esc_html__('Connect to Zoho CRM', 'lepopup').'</h1>
		<ol>
			<li>
				Select your Zoho domain:
				<select id="domain" name="domain" onchange="domain_changed();">
					<option value="com">zoho.com</option>
					<option value="eu">zoho.eu</option>
					<option value="com.cn">zoho.com.cn</option>
					<option value="in">zoho.in</option>
				</select>
			</li>
			<li>
				Register new Application (create Client ID) in <a data-href="https://accounts.zoho.{domain}/developerconsole" target="_blank" href="https://accounts.zoho.com/developerconsole">Zoho Developer Console</a>. Use the following URL as Authorized Redirect URIs:
				<input type="text" readonly="readonly" id="redirect-uri" name="redirect-uri" onclick="this.focus();this.select();" value="'.admin_url('admin-ajax.php').'?action=lepopup-zohocrm-connect&do=connect" />
				For more details please read chapter <a data-href="https://www.zoho.{domain}/crm/help/api/v2/#oauth-request" target="_blank" href="https://www.zoho.com/crm/help/api/v2/#oauth-request">Register your application</a>.
			</li>
			<li>
				Enter Client ID and Client Secret into fields below.
				<input type="text" id="client-id" name="client-id" value="" placeholder="'.esc_html__('Client ID', 'lepopup').'" />
				<input type="text" id="client-secret" name="client-secret" value="" placeholder="'.esc_html__('Client Secret', 'lepopup').'" />
			</li>
		</ol>
		<div class="button-container">
			<a class="button" href="#" onclick="return connect(this);"><i class="fas fa-check"></i><label>'.esc_html__('Connect to Zoho CRM', 'lepopup').'</label></a>			
		</div>
	</div>
</body>
</html>';
		}
		exit;
	}

	function admin_disconnect() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			$data = array(
				'token' => $this->options["zohocrm-refresh-token"]
			);
			$result = $this->connect_auth($this->options["zohocrm-dc"], '/revoke', $data);
			if (is_array($result) && array_key_exists('status', $result) && $result['status'] == 'success') {
				$this->options['zohocrm-refresh-token'] = '';
				$this->options['zohocrm-api-domain'] = '';
				$this->options['zohocrm-dc'] = '';
				$this->options['zohocrm-redirect-uri'] = '';
				$this->options['zohocrm-client-id'] = '';
				$this->options['zohocrm-client-secret'] = '';
				$this->options["zohocrm-connection-data"] = array();
				$this->update_options();
					
				$return_object = array();
				$return_object['status'] = 'OK';
				$return_object['message'] = esc_html__('Successfully disconnected from Zoho CRM!', 'lepopup');
				$return_object['html'] = '
					<table class="lepopup_useroptions">
						<tr>
							<th>'.esc_html__('Connect', 'lepopup').':</th>
							<td>
								<a class="lepopup-button lepopup-button-small" onclick="window.open(\''.admin_url('admin-ajax.php').'?action=lepopup-zohocrm-connect\', \'_blank\', \'height=560,width=720,menubar=no,scrollbars=no,status=no,toolbar=no\'); return false;"><i class="fas fa-check"></i><label>'.esc_html__('Connect to Zoho CRM', 'lepopup').'</label></a>
								<br /><em>'.esc_html__('Click the button to connect to Zoho CRM.', 'lepopup').'</em>
							</td>
						</tr>
					</table>';
				echo json_encode($return_object);
				exit;
			}
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Can not disconnect from Zoho CRM.', 'lepopup');
			echo json_encode($return_object);
			exit;
		}
		exit;
	}

	function admin_connected() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			$data = array(
				'refresh_token' => $this->options["zohocrm-refresh-token"],
				'client_id' => $this->options["zohocrm-client-id"],
				'client_secret' => $this->options["zohocrm-client-secret"],
				'grant_type' => 'refresh_token'
			);
			$result = $this->connect_auth($this->options["zohocrm-dc"], '', $data);
			if (is_array($result) && array_key_exists('access_token', $result)) {
				$return_object = array();
				$return_object['status'] = 'OK';
				$return_object['message'] = esc_html__('Successfully connected to Zoho CRM!', 'lepopup');
				$return_object['html'] = '
				<table class="lepopup_useroptions">
					<tr>
						<th>'.esc_html__('Connected', 'lepopup').':</th>
						<td>
							<a class="lepopup-button lepopup-button-small" onclick="return lepopup_zohocrm_disconnect(this);"><i class="fas fa-times"></i><label>'.esc_html__('Disconnect from Zoho CRM', 'lepopup').'</label></a>
							<br /><em>'.esc_html__('Click the button to disconnect from Zoho CRM.', 'lepopup').'</em>
						</td>
					</tr>
				</table>';
				echo json_encode($return_object);
				exit;
			}
			$return_object = array();
			$return_object['status'] = 'ERROR';
			$return_object['message'] = esc_html__('Can not connect to Zoho CRM.', 'lepopup');
			echo json_encode($return_object);
			exit;
		}
		exit;
	}
	
	function admin_settings_html() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options')) {
			$access_token = null;
			if ($this->options['zohocrm-refresh-token']) {
				$data = array(
					'refresh_token' => $this->options["zohocrm-refresh-token"],
					'client_id' => $this->options["zohocrm-client-id"],
					'client_secret' => $this->options["zohocrm-client-secret"],
					'grant_type' => 'refresh_token'
				);
				$result = $this->connect_auth($this->options["zohocrm-dc"], '', $data);				
				if (is_array($result) && array_key_exists('access_token', $result)) {
					$access_token = $result['access_token'];
				}
			}
			if (empty($access_token)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Connect your Zoho CRM account on General Settings page.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if (array_key_exists('data', $_REQUEST)) {
				$data = json_decode(base64_decode(trim(stripslashes($_REQUEST['data']))), true);
				if (is_array($data)) $data = array_merge($this->default_parameters, $data);
				else $data = $this->default_parameters;
			} else $data = $this->default_parameters;
			$fields_data = $this->admin_get_fields_html($access_token, $data['fields']);
			if ($fields_data['status'] == 'OK') $fields_html = $fields_data['html'];
			else {
				echo json_encode($fields_data);
				exit;
			}
			$html = '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Fields', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to Zoho CRM fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
					'.$fields_html.'
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
	
	function admin_get_fields_html($_token, $_fields) {
		$result = $this->connect_api($_token, '/settings/fields?module=leads');
		$fields = '';
		$idx = 0;
		if (is_array($result) && array_key_exists('fields', $result)) {
			if (sizeof($result['fields']) > 0) {
				$fields = '
			<table>';
				foreach ($result['fields'] as $field) {
					if (!in_array($field['data_type'], array('lookup', 'ownerlookup', 'boolean'))) {
						$fields .= '
						<tr>
							<th>'.esc_html($field['field_label']).'</th>
							<td>';
						if ($field['data_type'] == 'picklist') {
							$fields .= '
								<select name="fields['.esc_html($field['api_name']).']" class="widefat">';
							foreach ($field['pick_list_values'] as $val) {
								$fields .= '
									<option value="'.esc_html($val['actual_value']).'"'.(array_key_exists($field['api_name'], $_fields) && $_fields[$field['api_name']] == $val['actual_value'] ? ' selected="selected"' : '').'>'.esc_html($val['display_value']).'</option>';
							}
							$fields .= '
								</select>';
						} else {
							$fields .= '
								<div class="lepopup-input-shortcode-selector">
									<input type="text" name="fields['.esc_html($field['api_name']).']" value="'.esc_html(array_key_exists($field['api_name'], $_fields) ? $_fields[$field['api_name']] : '').'" class="widefat" />
									<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
								</div>';
						}
						$fields .= '
								<label class="lepopup-integrations-description">'.esc_html($field['field_label']).'</label>
							</td>
						</tr>';
					}
				}
				$fields .= '
			</table>';
			} else {
				return array('status' => 'ERROR', 'message' => esc_html__('No fields found.', 'lepopup'));
			}
		} else if (is_array($result) && array_key_exists('message', $result)) {
			return array('status' => 'ERROR', 'message' => ucwords($result['message']));
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid server response.', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $fields);
	}
	
	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		//if (empty($data['fields']['Email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['Email'])) return $_result;

		if ($this->options['zohocrm-refresh-token']) {
			$post_data = array(
				'refresh_token' => $this->options["zohocrm-refresh-token"],
				'client_id' => $this->options["zohocrm-client-id"],
				'client_secret' => $this->options["zohocrm-client-secret"],
				'grant_type' => 'refresh_token'
			);
			$result = $this->connect_auth($this->options["zohocrm-dc"], '', $post_data);
			if (is_array($result) && array_key_exists('access_token', $result)) {
				$access_token = $result['access_token'];
				$post_data = array();
				foreach ($data['fields'] as $key => $value) {
					if (!empty($value)) $post_data[$key] = $value;
				}
				$result = $this->connect_api($access_token, '/leads/upsert', array('data' => array($post_data)));
			}
		}
		return $_result;
	}
	
	function connect_auth($_domain, $_path, $_data) {
		try {
			$url = 'https://accounts.zoho.'.$_domain.'/oauth/v2/token'.(empty($_path) ? '' : '/'.ltrim($_path, '/'));
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($_data));
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
	function connect_api($_token, $_path, $_data = array()) {
		try {
			$headers = array(
				'Authorization: Zoho-Oauthtoken '.$_token
			);
			$url = rtrim($this->options["zohocrm-api-domain"], '/').'/crm/v2/'.ltrim($_path, '/');
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($_data));
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
$lepopup_zohocrm = new lepopup_zohocrm_class();
?>