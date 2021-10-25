<?php
/* AWeber integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
define('LEPOPUP_AWEBER_APPID', '8ac652ac');
class lepopup_aweber_class {
	var $options = array(
		"aweber-consumer-key" => "",
		"aweber-consumer-secret" => "",
		"aweber-access-key" => "",
		"aweber-access-secret" => ""
	);
	var $default_parameters = array(
		'list-id' => "",
		'email' => "",
		'name' => "",
		'fields' => array(),
		'fieldnames' => array(),
		'tags' => '',
		'notes' => '',
		'ad-tracking' => "green-forms"
	);
	
	function __construct() {
		$this->get_options();
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('lepopup_options_show', array(&$this, 'admin_options_show'));
			add_action('wp_ajax_lepopup-aweber-auth-code', array(&$this, "admin_auth_code"));
			add_action('wp_ajax_lepopup-aweber-connect', array(&$this, "admin_connect"));
			add_action('wp_ajax_lepopup-aweber-disconnect', array(&$this, "admin_disconnect"));
			add_action('wp_ajax_lepopup-aweber-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-aweber-fields', array(&$this, "admin_fields_html"));
		}
		add_filter('lepopup_integrations_do_aweber', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("aweber", $_providers)) $_providers["aweber"] = esc_html__('AWeber', 'lepopup');
		return $_providers;
	}

	function get_options() {
		foreach ($this->options as $key => $value) {
			$this->options[$key] = get_option('lepopup-'.$key, $this->options[$key]);
		}
	}
	function update_options() {
		if (current_user_can('manage_options')) {
			foreach ($this->options as $key => $value) {
				update_option('lepopup-'.$key, $value);
			}
		}
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
			<h3 id="aweber-settings">'.esc_html__('AWeber Connection', 'lepopup').'</h3>';
		$account_id = null;
		if (!empty($this->options['aweber-access-key']) && !empty($this->options['aweber-access-secret'])) {
			$accounts = $this->connect_api($this->options['aweber-consumer-key'], $this->options['aweber-consumer-secret'], $this->options['aweber-access-key'], $this->options['aweber-access-secret'], 'accounts', array(), 'GET');
			if (is_array($accounts) && array_key_exists('entries', $accounts) && sizeof($accounts['entries']) > 0 && !empty($accounts['entries'][0]['id'])) {
				$account_id = $accounts['entries'][0]['id'];
			}
		}
		if (!$account_id) {
			echo '
			<div id="lepopup-aweber-connection">
				<table class="lepopup-useroptions">
					<tr>
						<th>'.esc_html__('Authorization code', 'lepopup').':</th>
						<td>
							<input type="text" id="lepopup-aweber-auth-code" value="" class="widefat" placeholder="'.esc_html__('AWeber Authorization Code', 'lepopup').'">
							<br /><em>Get your authorization code <a target="_blank" href="'.admin_url('admin-ajax.php').'?action=lepopup-aweber-auth-code" onclick="window.open(\''.admin_url('admin-ajax.php').'?action=lepopup-aweber-auth-code\', \'_blank\', \'height=560,width=720,menubar=no,scrollbars=no,status=no,toolbar=no\'); return false;">'.esc_html__('here', 'lepopup').'</a></em>.
						</td>
					</tr>
					<tr>
						<th></th>
						<td>
							<a class="lepopup-button lepopup-button-small" onclick="return lepopup_aweber_connect(this);"><i class="fas fa-check"></i><label>'.esc_html__('Connect to AWeber', 'lepopup').'</label></a>
							<br /><em>'.esc_html__('Click the button to connect to AWeber.', 'lepopup').'</em>
						</td>
					</tr>
				</table>
			</div>';
		} else {
			echo '
			<div id="lepopup-aweber-connection">
				<table class="lepopup-useroptions">
					<tr>
						<th>'.esc_html__('Connected', 'lepopup').':</th>
						<td>
							<a class="lepopup-button lepopup-button-small" onclick="return lepopup_aweber_disconnect(this);"><i class="fas fa-times"></i><label>'.esc_html__('Disconnect from AWeber', 'lepopup').'</label></a>
							<br /><em>'.esc_html__('Click the button to disconnect from AWeber.', 'lepopup').'</em>
						</td>
					</tr>
				</table>
			</div>';
		}
		echo '
			<script>
				var lepopup_aweber_connecting = false;
				function lepopup_aweber_connect(_object) {
					if (lepopup_aweber_connecting) return false;
					jQuery(_object).find("i").attr("class", "fas fa-spinner fa-spin");
					jQuery(_object).addClass("lepopup-button-disabled");
					lepopup_aweber_connecting = true;
					var post_data = {"action" : "lepopup-aweber-connect", "lepopup-auth-code": jQuery("#lepopup-aweber-auth-code").val()};
					jQuery.ajax({
						type	: "POST",
						url		: "'.admin_url('admin-ajax.php').'", 
						data	: post_data,
						success	: function(return_data) {
							jQuery(_object).find("i").attr("class", "fas fa-times");
							jQuery(_object).removeClass("lepopup-button-disabled");
							try {
								var data = jQuery.parseJSON(return_data);
								if (data.status == "OK") {
									jQuery("#lepopup-aweber-connection").slideUp(350, function() {
										jQuery("#lepopup-aweber-connection").html(data.html);
										jQuery("#lepopup-aweber-connection").slideDown(350);
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
							lepopup_aweber_connecting = false;
						},
						error	: function(XMLHttpRequest, textStatus, errorThrown) {
							jQuery(_object).find("i").attr("class", "fas fa-times");
							jQuery(_object).removeClass("lepopup-button-disabled");
							lepopup_global_message_show("danger", "Something went wrong. We got unexpected server response.");
							lepopup_aweber_connecting = false;
						}
					});
					return false;
				}
				function lepopup_aweber_disconnect(_object) {
					if (lepopup_aweber_connecting) return false;
					jQuery(_object).find("i").attr("class", "fas fa-spinner fa-spin");
					jQuery(_object).addClass("lepopup-button-disabled");
					lepopup_aweber_connecting = true;
					var post_data = {"action" : "lepopup-aweber-disconnect"};
					jQuery.ajax({
						type	: "POST",
						url		: "'.admin_url('admin-ajax.php').'", 
						data	: post_data,
						success	: function(return_data) {
							jQuery(_object).find("i").attr("class", "fas fa-times");
							jQuery(_object).removeClass("lepopup-button-disabled");
							try {
								var data = jQuery.parseJSON(return_data);
								if (data.status == "OK") {
									jQuery("#lepopup-aweber-connection").slideUp(350, function() {
										jQuery("#lepopup-aweber-connection").html(data.html);
										jQuery("#lepopup-aweber-connection").slideDown(350);
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
							lepopup_aweber_connecting = false;
						},
						error	: function(XMLHttpRequest, textStatus, errorThrown) {
							jQuery(_object).find("i").attr("class", "fas fa-times");
							jQuery(_object).removeClass("lepopup-button-disabled");
							lepopup_global_message_show("danger", "Something went wrong. We got unexpected server response.");
							lepopup_aweber_connecting = false;
						}
					});
					return false;
				}
			</script>';
	}

	function admin_auth_code() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options')) {
			echo '
<!DOCTYPE html>
<html>
<head>
	<title>'.esc_html__('Get AWeber Authorization Code', 'lepopup').'</title>
	<link rel="stylesheet" media="all" href="'.$lepopup->plugins_url.'/css/oauth.css" />
	<link rel="stylesheet" media="all" href="'.$lepopup->plugins_url.'/css/fontawesome-all.min.css" />
	<script src="'.$lepopup->plugins_url.'/js/jquery.min.js" type="text/javascript"></script>
	<script>
		function appid_changed() {
			jQuery("a").each(function() {
				var href = jQuery(this).attr("data-href");
				if (href) {
					href = href.replace("{app-id}", jQuery("#app-id").val());
					jQuery(this).attr("href", href);
				}
			});
		}
	</script>
</head>
<body>
	<div class="main-container">
		<h1>'.esc_html__('Get AWeber Authorization Code', 'lepopup').'</h1>
		<ol>
			<li>
				Register new Application (create App ID) in <a target="_blank" href="https://labs.aweber.com/apps">AWeberAPI Console</a> or use existing App ID: <code>'.LEPOPUP_AWEBER_APPID.'</code>. If you register your own Application, please make sure that checkbox "Request Subscriber Data" on Permission Settings is set.
			</li>
			<li>
				Enter App ID below.
				<input type="text" id="app-id" name="app-id" value="'.LEPOPUP_AWEBER_APPID.'" placeholder="App ID" oninput="appid_changed();" />
			</li>
		</ol>
		<div class="button-container">
			<a class="button" data-href="https://auth.aweber.com/1.0/oauth/authorize_app/{app-id}" href="https://auth.aweber.com/1.0/oauth/authorize_app/'.LEPOPUP_AWEBER_APPID.'"><i class="fas fa-check"></i><label>Get Authorization Code</label></a>
		</div>
	</div>
</body>
</html>';
		}
		exit;
	}

	function admin_connect() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (!array_key_exists("lepopup-auth-code", $_REQUEST) || empty($_REQUEST['lepopup-auth-code'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid AWeber Authorization Code.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$auth_code = trim(stripslashes($_REQUEST['lepopup-auth-code']));
			$values = explode('|', $auth_code);
			if (sizeof($values) < 5) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid AWeber Authorization Code.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$this->options['aweber-consumer-key'] = $values[0];
			$this->options['aweber-consumer-secret'] = $values[1];
			
			$data = array('oauth_verifier' => $values[4]);
			$response = $this->connect_auth($this->options['aweber-consumer-key'], $this->options['aweber-consumer-secret'], $values[2], $values[3], $data);
			if (is_array($response)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid AWeber Authorization Code.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			parse_str($response, $result);
			if (!array_key_exists('oauth_token', $result) || empty($result['oauth_token']) || !array_key_exists('oauth_token_secret', $result)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid AWeber Authorization Code.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$this->options['aweber-access-key'] = $result['oauth_token'];
			$this->options['aweber-access-secret'] = $result['oauth_token_secret'];
			$this->update_options();
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['message'] = esc_html__('Successfully connected from AWeber!', 'lepopup');
			$return_object['html'] = '
				<table class="lepopup-useroptions">
					<tr>
						<th>'.esc_html__('Connected', 'lepopup').':</th>
						<td>
							<a class="lepopup-button lepopup-button-small" onclick="return lepopup_aweber_disconnect(this);"><i class="fas fa-times"></i><label>'.esc_html__('Disconnect from AWeber', 'lepopup').'</label></a>
							<br /><em>'.esc_html__('Click the button to disconnect from AWeber.', 'lepopup').'</em>
						</td>
					</tr>
				</table>';
			echo json_encode($return_object);
			exit;
		}
	}

	function admin_disconnect() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			$this->options = array(
				"aweber-consumer-key" => "",
				"aweber-consumer-secret" => "",
				"aweber-access-key" => "",
				"aweber-access-secret" => ""
			);
			$this->update_options();
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['message'] = esc_html__('Successfully disconnected from AWeber!', 'lepopup');
			$return_object['html'] = '
				<table class="lepopup-useroptions">
						<tr>
							<th>'.esc_html__('Authorization code', 'lepopup').':</th>
							<td>
								<input type="text" id="lepopup-aweber-auth-code" value="" class="widefat" placeholder="AWeber Authorization Code">
								<br /><em>Get your authorization code <a target="_blank" href="'.admin_url('admin-ajax.php').'?action=lepopup-aweber-auth-code" onclick="window.open(\''.admin_url('admin-ajax.php').'?action=lepopup-aweber-auth-code\', \'_blank\', \'height=560,width=720,menubar=no,scrollbars=no,status=no,toolbar=no\'); return false;">'.esc_html__('here', 'lepopup').'</a></em>.
							</td>
						</tr>
						<tr>
							<th></th>
							<td>
								<a class="lepopup-button lepopup-button-small" onclick="return lepopup_aweber_connect(this);"><i class="fas fa-check"></i><label>'.esc_html__('Connect to AWeber', 'lepopup').'</label></a>
								<br /><em>'.esc_html__('Click the button to connect to AWeber.', 'lepopup').'</em>
							</td>
						</tr>
				</table>';
			echo json_encode($return_object);
		}
		exit;
	}

	function admin_settings_html() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options')) {
			$account_id = null;
			if (!empty($this->options['aweber-access-key']) && !empty($this->options['aweber-access-secret'])) {
				$accounts = $this->connect_api($this->options['aweber-consumer-key'], $this->options['aweber-consumer-secret'], $this->options['aweber-access-key'], $this->options['aweber-access-secret'], 'accounts', array(), 'GET');
				if (is_array($accounts) && array_key_exists('entries', $accounts) && sizeof($accounts['entries']) > 0 && !empty($accounts['entries'][0]['id'])) {
					$account_id = $accounts['entries'][0]['id'];
				}
			}
			if (empty($account_id)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Connect your AWeber account on General Settings page.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$lists = $this->connect_api($this->options['aweber-consumer-key'], $this->options['aweber-consumer-secret'], $this->options['aweber-access-key'], $this->options['aweber-access-secret'], 'accounts/'.$account_id.'/lists', array(), 'GET');
			if (!is_array($lists) || !array_key_exists('entries', $lists) || sizeof($lists['entries']) == 0) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Create at least one List in your AWeber account.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			
			if (array_key_exists('data', $_REQUEST)) {
				$data = json_decode(base64_decode(trim(stripslashes($_REQUEST['data']))), true);
				if (is_array($data)) $data = array_merge($this->default_parameters, $data);
				else $data = $this->default_parameters;
			} else $data = $this->default_parameters;
			
			$tags = explode(',', $data['tags']);
			$ready_tags = array();
			foreach($tags as $tag) {
				$tag = trim($tag);
				if (strlen($tag) > 0) $ready_tags[] = $tag;
			}
			$data['tags'] = implode(', ', $ready_tags);
			
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
					
				if (empty($data['list-id'])) $data['list-id'] = $lists['entries'][0]['id'];
				foreach ($lists['entries'] as $list) {
					$html .= '
						<option value="'.esc_html($list['id']).'"'.($list['id'] == $data['list-id'] ? ' selected="selected"' : '').'>'.esc_html($list['id'].' | '.$list['name']).'</option>';
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
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to AWeber fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>
							<tr>
								<th>'.esc_html__('Email', 'lepopup').'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="email" value="'.esc_html(array_key_exists('email', $data) ? $data['email'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Email Address', 'lepopup').'</label>
								</td>
							</tr>
							<tr>
								<th>'.esc_html__('Name', 'lepopup').'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="name" value="'.esc_html(array_key_exists('name', $data) ? $data['name'] : '').'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html__('Name of the contact', 'lepopup').'</label>
								</td>
							</tr>
						</table>
					</div>
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">';
			$fields_data = $this->get_fields_html($account_id, $data['list-id'], $data['fields']);
			if ($fields_data['status'] == 'OK') $html .= $fields_data['html'];
			$html .= '
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="fields" data-deps="list-id"><i class="fas fa-download"></i><label>'.esc_html__('Load Fields', 'lepopup').'</label></a>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Tags', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter comma-separated list of tags applied to the contact.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="tags" value="'.esc_html($data['tags']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Notes', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter notes applied to the contact (max 60 sybmols).', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="notes" value="'.esc_html($data['notes']).'" class="widefat" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Ad Tracking', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Ad Tracking info applied to the contact.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="ad-tracking" value="'.esc_html($data['ad-tracking']).'" class="widefat" />
				</div>
			</div>';
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = $html;
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
			if (!is_array($deps) || !array_key_exists('list-id', $deps) || empty($deps['list-id'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid List ID.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			$account_id = null;
			if (!empty($this->options['aweber-access-key']) && !empty($this->options['aweber-access-secret'])) {
				$accounts = $this->connect_api($this->options['aweber-consumer-key'], $this->options['aweber-consumer-secret'], $this->options['aweber-access-key'], $this->options['aweber-access-secret'], 'accounts', array(), 'GET');
				if (is_array($accounts) && array_key_exists('entries', $accounts) && sizeof($accounts['entries']) > 0 && !empty($accounts['entries'][0]['id'])) {
					$account_id = $accounts['entries'][0]['id'];
				}
			}
			if (empty($account_id)) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Connect your AWeber account on General Settings page.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			
			$return_object = $this->get_fields_html($account_id, $deps['list-id'], $this->default_parameters['fields']);
			echo json_encode($return_object);
		}
		exit;
	}
	
	function get_fields_html($_account_id, $_list, $_fields) {
		global $wpdb, $lepopup;
		$result = $this->connect_api($this->options['aweber-consumer-key'], $this->options['aweber-consumer-secret'], $this->options['aweber-access-key'], $this->options['aweber-access-secret'], 'accounts/'.$_account_id.'/lists/'.$_list.'/custom_fields', array(), 'GET');
		$fields_html = '';
		if (!empty($result) && is_array($result)) {
			if (array_key_exists('status', $result)) {
				return array('status' => 'ERROR', 'message' => $result['title']);
			} else {
				if (array_key_exists('entries', $result) && sizeof($result['entries']) > 0) {
					$fields_html = '
			<table>';
					foreach ($result['entries'] as $field) {
						if (is_array($field)) {
							if (array_key_exists('id', $field) && array_key_exists('name', $field)) {
								$fields_html .= '
				<tr>
					<th>'.esc_html($field['name']).'</th>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="fields['.esc_html($field['id']).']" value="'.esc_html(array_key_exists($field['id'], $_fields) ? $_fields[$field['id']] : '').'" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<input type="hidden" name="fieldnames['.esc_html($field['id']).']" value="'.esc_html($field['name']).'" />
						<label class="lepopup-integrations-description">'.esc_html($field['name']).'</label>
					</td>
				</tr>';
							}
						}
					}
					$fields_html .= '
			</table>';
				} else {
					return array('status' => 'ERROR', 'message' => esc_html__('No custom fields found.', 'lepopup'));
				}
			}
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Inavlid server response.', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $fields_html);
	}
	
	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['email'])) return $_result;

		$post_data = array(
			'email' => $data['email'],
			'ip_address' => $_SERVER['REMOTE_ADDR'],
			'name' => $data['name'],
			'ad_tracking' => $data['ad-tracking'],
			'last_followup_message_number_sent' => 0,
			'misc_notes' => $data['notes']
		);
		$custom_fields = array();
		if (!empty($data['fields']) && is_array($data['fields'])) {
			foreach ($data['fields'] as $key => $value) {
				if (!empty($value)) {
					$custom_fields[$data['fieldnames'][$key]] = $data['fields'][$key];
				}
			}
		}
		if (!empty($custom_fields)) $post_data['custom_fields'] = json_encode($custom_fields);
		
		$tags_raw = explode(',', $data['tags']);
		$tags = array();
		foreach($tags_raw as $tag) {
			$tag = trim($tag);
			if (strlen($tag) > 0) $tags[] = $tag;
		}
		if (!empty($tags)) $post_data['tags'] = json_encode($tags);
		
		$account_id = null;
		if (!empty($this->options['aweber-access-key']) && !empty($this->options['aweber-access-secret'])) {
			$accounts = $this->connect_api($this->options['aweber-consumer-key'], $this->options['aweber-consumer-secret'], $this->options['aweber-access-key'], $this->options['aweber-access-secret'], 'accounts', array(), 'GET');
			if (is_array($accounts) && array_key_exists('entries', $accounts) && sizeof($accounts['entries']) > 0 && !empty($accounts['entries'][0]['id'])) {
				$account_id = $accounts['entries'][0]['id'];
			}
		}
		if (!empty($account_id)) {
			$result = $this->connect_api($this->options['aweber-consumer-key'], $this->options['aweber-consumer-secret'], $this->options['aweber-access-key'], $this->options['aweber-access-secret'], 'accounts/'.$account_id.'/lists/'.$data['list-id'].'/subscribers', array('ws.op' => 'find', 'email' => $data['email']), 'GET');
			if (array_key_exists('entries', $result) && sizeof($result['entries']) > 0) {
				$post_data['status'] = 'subscribed';
				if (!empty($tags)) $post_data['tags'] = json_encode(array('add' => $tags));
				$result = $this->connect_api($this->options['aweber-consumer-key'], $this->options['aweber-consumer-secret'], $this->options['aweber-access-key'], $this->options['aweber-access-secret'], 'accounts/'.$account_id.'/lists/'.$data['list-id'].'/subscribers/'.$result['entries'][0]['id'], $post_data, 'PATCH');
			} else {
				$result = $this->connect_api($this->options['aweber-consumer-key'], $this->options['aweber-consumer-secret'], $this->options['aweber-access-key'], $this->options['aweber-access-secret'], 'accounts/'.$account_id.'/lists/'.$data['list-id'].'/subscribers', $post_data, 'POST');
			}
		}
		return $_result;
	}
	
	function connect_auth($_consumer_key, $_consumer_secret, $_access_token, $_access_secret, $_data) {
		try {
			$url = 'https://auth.aweber.com/1.0/oauth/access_token';
			$timestamp = time();
			$data = array(
				'oauth_token' => 			$_access_token,
				'oauth_consumer_key' =>		$_consumer_key,
				'oauth_version' => 			'1.0',
				'oauth_timestamp' => 		$timestamp,
				'oauth_signature_method' => 'HMAC-SHA1',
				'oauth_nonce' => 			md5($timestamp.'-'.rand(10000,99999).'-'.uniqid())
			);
			$data = array_merge($_data, $data);
			$data = $this->_sign_request('POST', $url, $data, $_consumer_secret, $_access_secret);
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			if ($httpCode >= 400) $result = json_decode($response, true);
			else $result = $response;
		} catch (Exception $e) {
			$result = false;
		}
		return $result;
	}

	function connect_api($_consumer_key, $_consumer_secret, $_access_token, $_access_secret, $_path, $_data, $_method = 'GET') {
		try {
			$url = 'https://api.aweber.com/1.0'.(empty($_path) ? '' : '/'.ltrim($_path, '/'));
			$timestamp = time();
			$data = array(
				'oauth_token' => 			$_access_token,
				'oauth_consumer_key' =>		$_consumer_key,
				'oauth_version' => 			'1.0',
				'oauth_timestamp' => 		$timestamp,
				'oauth_signature_method' => 'HMAC-SHA1',
				'oauth_nonce' => 			md5($timestamp.'-'.rand(10000,99999).'-'.uniqid())
			);
			$data = array_merge($_data, $data);
			$data = $this->_sign_request($_method, $url, $data, $_consumer_secret, $_access_secret);
			ksort($data);
			$params = array();
			foreach ($data as $key => $value) {
				$params[] = $key.'='.rawurlencode(utf8_encode($value));
			}
			$query = implode('&', $params);
			if ($_method == 'GET') {
				if (strpos($url, '?') === false) $url .= '?'.$query;
				else $url .= '?'.$query;
			}
			$curl = curl_init($url);
			if ($_method != 'GET') {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
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
	
    function _sign_request($_method, $_url, $_data, $_consumer_secret, $_token_secret) {
        $_method = rawurlencode(utf8_encode(strtoupper($_method)));
        $query = parse_url($_url, PHP_URL_QUERY);
        if ($query) {
            $_url = array_shift(explode('?', $_url, 2));
            $items = explode('&', $query);
            foreach ($items as $item) {
                list($key, $value) = explode('=', $item);
                $_data[$key] = $value;
            }
        }
		$_url = rawurlencode(utf8_encode($_url));
        ksort($_data);
        $data_str = '';
        foreach ($_data as $key => $val) {
            if (!empty($data_str)) $data_str .= '&';
            $data_str .= $key.'='.rawurlencode(utf8_encode($val));
        }
        $signature_base = $_method.'&'.$_url.'&'.rawurlencode(utf8_encode($data_str));
        $signature_key  = $_consumer_secret.'&'.$_token_secret;
		
        $_data['oauth_signature'] = base64_encode(hash_hmac('sha1', $signature_base, $signature_key, true));
        ksort($_data);
        return $_data;
    }
}
$lepopup_aweber = new lepopup_aweber_class();
?>