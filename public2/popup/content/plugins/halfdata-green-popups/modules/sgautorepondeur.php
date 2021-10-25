<?php
/* SG Autorepondeur integration for Green Popups */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_sgautorepondeur_class {
	var $default_parameters = array(
		"member-id" => "",
		"api-key" => "",
		"list" => "",
		"list-id" => "",
		"fields" => array(
			'email' => '',
			'nom' => '',
			'prenom' => '',
			'civilite' => '',
			'adresse' => '',
			'codepostal' => '',
			'ville' => '',
			'pays' => '',
			'telephone' => '',
			'mobile' => '',
			'champs_1' => '',
			'champs_2' => '',
			'champs_3' => '',
			'champs_4' => '',
			'champs_5' => '',
			'champs_6' => '',
			'champs_7' => '',
			'champs_8' => '',
			'champs_9' => '',
			'champs_10' => '',
			'champs_11' => '',
			'champs_12' => '',
			'champs_13' => '',
			'champs_14' => '',
			'champs_15' => '',
			'champs_16' => ''
		)
	);
	var $fields_meta;
	function __construct() {
		$this->fields_meta = array(
			'email' => array('title' => 'E-mail', 'description' => 'Adresse email de l\'abonné.'),
			'nom' => array('title' => 'Nom', 'description' => 'Nom de l\'abonné.'),
			'prenom' => array('title' => 'Prenom', 'description' => 'Prénom de l\'abonné.'),
			'civilite' => array('title' => 'Civilité', 'description' => 'Civilité de l\'abonné (M, Mme, Mlle).'),
			'adresse' => array('title' => 'Adresse', 'description' => 'Adresse de l\'abonné.'),
			'codepostal' => array('title' => 'Code postal', 'description' => 'Code postal de l\'abonné.'),
			'ville' => array('title' => 'Ville', 'description' => 'Ville de l\'abonné.'),
			'pays' => array('title' => 'Pays', 'description' => 'Pays de l\'abonné.'),
			'telephone' => array('title' => 'Telephone #', 'description' => 'Numéro de téléphone de l\'abonné.'),
			'mobile' => array('title' => 'Mobile #', 'description' => 'Numéro de téléphone mobile de l\'abonné.')
		);
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-sgautorepondeur-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-sgautorepondeur-list', array(&$this, "admin_lists"));
		}
		add_filter('lepopup_integrations_do_sgautorepondeur', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("sgautorepondeur", $_providers)) $_providers["sgautorepondeur"] = esc_html__('SG Autorepondeur', 'lepopup');
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
					<label>'.esc_html__('Identifiant', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Identifiant.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="member-id" value="'.esc_html($data['member-id']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find your Identifiant %shere%s.', 'lepopup'), '<a href="https://sg-autorepondeur.com/app/profile.php" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Code API', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your Code API.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find Code API %shere%s.', 'lepopup'), '<a href="https://sg-autorepondeur.com/app/profile.php" target="_blank">', '</a>').'</label>
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
						<input type="text" name="list" value="'.esc_html($data['list']).'" data-deps="member-id,api-key" readonly="readonly" />
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
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to SG Autorepondeur fields.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-static-inline">
						<table>';
		foreach ($this->default_parameters['fields'] as $key => $value) {
			if (strpos($key, 'champs_') !== false) {
				$number = substr($key, strlen('champs_'));
				$title = 'Champ '.$number;
				$description = 'Champ personnalisés '.$number.' de l\'abonné.';
			} else {
				$title = $this->fields_meta[$key]['title'];
				$description = $this->fields_meta[$key]['description'];
			}
			$html .= '
							<tr>
								<th>'.esc_html($title).'</td>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="fields['.$key.']" value="'.esc_html(array_key_exists($key, $data['fields']) ? $data['fields'][$key] : $value).'" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
									<label class="lepopup-integrations-description">'.esc_html($description).'</label>
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
	
	function admin_lists() {
		global $wpdb, $lepopup;
		$lists = array();
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;

			if (!is_array($deps) || !array_key_exists('member-id', $deps) || empty($deps['member-id']) || !array_key_exists('api-key', $deps) || empty($deps['api-key'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid API credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			
			$result = $this->connect($deps['member-id'], $deps['api-key'], 'get_list');
			if (is_array($result)) {
				if (array_key_exists('valid', $result) && $result['valid']) {
					foreach($result['reponse'] as $list) {
						if (is_array($list)) {
							if (array_key_exists('listeid', $list) && array_key_exists('nom', $list)) {
								$lists[$list['listeid']] = $list['nom'];
							}
						}
					}
				} else {
					$return_object = array('status' => 'ERROR', 'message' => esc_html__('No lists found.', 'lepopup'));
					echo json_encode($return_object);
					exit;
				}
			} else {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid server response.', 'lepopup'));
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

	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (empty($data['member-id']) || empty($data['api-key']) || empty($data['list-id'])) return $_result;
		if (empty($data['fields']) || !is_array($data['fields'])) return $_result;
		if (empty($data['fields']['email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $data['fields']['email'])) return $_result;

		$post_data = array(
			'listeid' => $data['list-id'],
			'ip' => $_SERVER["REMOTE_ADDR"]
		);
		foreach ($data['fields'] as $key => $value) {
			if (!empty($value)) {
				$post_data[$key] = $value;
			}
		}
		$result = $this->connect($data['member-id'], $data['api-key'], 'set_subscriber', $post_data);
		return $_result;
	}
	
	function connect($_member_id, $_api_key, $_path, $_data = array(), $_method = '') {
		try {
			$url = 'https://sg-autorepondeur.com/API_V2/';
			$data = array_merge($_data, array('membreid' => $_member_id, 'codeactivation' => $_api_key, 'action' => $_path));
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
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
$lepopup_sgautorepondeur = new lepopup_sgautorepondeur_class();
?>