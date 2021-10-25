<?php
/* PayUmoney integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_payumoney_class {
	var $default_parameters = array(
		"merchant-key" => "",
		"merchant-salt" => "",
		"item-name" => "",
		"first-name" => "Payer",
		"email" => "",
		"phone" => "",
		"amount" => "",
		"currency" => "USD",
		"success-url" => "",
		"cancel-url" => ""
	);
	var $currency_list = array("INR");
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_payment_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-payumoney-settings-html', array(&$this, "admin_settings_html"));
			add_filter('lepopup_payment_gateways_transaction_html_payumoney', array(&$this, 'admin_details'), 10, 3);
		}
		add_filter('lepopup_payment_gateways_do_payumoney', array(&$this, 'front_submit'), 10, 2);
		add_filter('lepopup_remote_parameters', array(&$this, 'remote_parameters'), 10, 1);
		add_action("init", array(&$this, "front_init"));
		add_action('wp_head', array(&$this, 'header_modifier'), 99);
	}
	
	function providers($_providers) {
		if (!array_key_exists("payumoney", $_providers)) $_providers["payumoney"] = esc_html__('PayUmoney', 'lepopup');
		return $_providers;
	}

	function header_modifier() {
		global $lepopup;
		echo '<script id="bolt" src="https://checkout-static.citruspay.com/bolt/run/bolt.min.js" bolt-color="e34524" bolt-logo=""></script>';
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
					<h4>'.sprintf(esc_html__('Important! Make sure that you created webhook with following parameters in your %sPayUmoney Dashboard%s.', 'lepopup'), '<a href="https://www.payumoneymoney.com/merchant-dashboard/#/webhook-settings" target="_blank">', '</a>').'</h4>
					<div class="lepopup-properties-item">
						<div class="lepopup-properties-label">
							<label>'.esc_html__('Webhook Type', 'lepopup').'</label>
						</div>
						<div class="lepopup-properties-content">
							<input type="text" readonly="readonly" value="Payments" onclick="this.focus();this.select();" />
						</div>
					</div>
					<div class="lepopup-properties-item">
						<div class="lepopup-properties-label">
							<label>'.esc_html__('Webhook Event', 'lepopup').'</label>
						</div>
						<div class="lepopup-properties-content">
							<input type="text" readonly="readonly" value="Successful Payment" onclick="this.focus();this.select();" />
						</div>
					</div>
					<div class="lepopup-properties-item">
						<div class="lepopup-properties-label">
							<label>'.esc_html__('Webhook URL', 'lepopup').'</label>
						</div>
						<div class="lepopup-properties-content">
							<input type="text" readonly="readonly" value="'.(defined('UAP_CORE') ? esc_html(admin_url('do.php')) : esc_html(get_bloginfo('url').'/lepopup-payumoney-ipn-handler/')).'" onclick="this.focus();this.select();" />
						</div>
					</div>
					<div class="lepopup-properties-item">
						<div class="lepopup-properties-label">
							<label>'.esc_html__('Authorization Header Key', 'lepopup').'</label>
						</div>
						<div class="lepopup-properties-content">
							<input type="text" readonly="readonly" value="LEPOPUP-UID" onclick="this.focus();this.select();" />
						</div>
					</div>
					<div class="lepopup-properties-item">
						<div class="lepopup-properties-label">
							<label>'.esc_html__('Authorization Header Value', 'lepopup').'</label>
						</div>
						<div class="lepopup-properties-content">
							<input type="text" readonly="readonly" value="'.esc_html($lepopup->installation_uid).'" onclick="this.focus();this.select();" />
						</div>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Merchant Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter valid Merchant Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="merchant-key" value="'.esc_html($data['merchant-key']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find it on %sMerchant Dashboard%s page.', 'lepopup'), '<a href="https://www.payumoneymoney.com/merchant-dashboard/" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Merchant Salt', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter valid Merchant Salt.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="merchant-salt" value="'.esc_html($data['merchant-salt']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find it on %sMerchant Dashboard%s page.', 'lepopup'), '<a href="https://www.payumoneymoney.com/merchant-dashboard/" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Product Description', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter the product name.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-input-shortcode-selector">
						<input type="text" name="item-name" value="'.(empty($data['item-name']) ? esc_html__('Membership Fee', 'lepopup') : esc_html($data['item-name'])).'" />
						<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Amount', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Set amount to pay and currency.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-group">
						<div class="lepopup-properties-content-9dimes lepopup-input-shortcode-selector">
							<input type="text" name="amount" value="'.esc_html($data['amount']).'" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
						<div class="lepopup-properties-content-dime">
							<select name="currency" class="lepopup-100px">';
			foreach ($this->currency_list as $currency) {
				$html .= '<option value="'.esc_html($currency).'"'.($data['currency'] == $currency ? ' selected="selected"' : '').'>'.esc_html($currency).'</option>';
			}
			$html .= '
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Payer\'s First Name', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter the First Name of the payer. Allowed characters: (only alphabets a-z are allowed).', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-input-shortcode-selector">
						<input type="text" name="first-name" value="'.(empty($data['first-name']) ? esc_html__('Payer', 'lepopup') : esc_html($data['first-name'])).'" />
						<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Payer\'s phone', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter mobile number or landline number of the payer (numeric value only).', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-input-shortcode-selector">
						<input type="text" name="phone" value="'.esc_html($data['phone']).'" />
						<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Payer\'s email', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter email address of the payer.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-input-shortcode-selector">
						<input type="text" name="email" value="'.esc_html($data['email']).'" />
						<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Successful payment URL', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('All payers are redirected to this URL after successful payment.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-input-shortcode-selector">
						<input type="text" name="success-url" value="'.esc_html($data['success-url']).'" />
						<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Cancel URL', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('All payers are redirected to this URL in case of failed/cancelled payment.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-input-shortcode-selector">
						<input type="text" name="cancel-url" value="'.esc_html($data['cancel-url']).'" />
						<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
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
		if (empty($data['merchant-key']) || empty($data['merchant-salt'])) return $_result;
		if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) return $_result;
		$payumoney_request = array(
			'key' => $data['merchant-key'],
			'txnid' => $data["record-id"].'-'.$lepopup->random_string(16),
			'amount' => number_format($data['amount'], 2, '.', ''),
			'productinfo' => (!empty($data['item-name']) ? $data['item-name'] : 'Fee'),
			'firstname' => $data['first-name'],
			'email' => $data['email'],
			'udf1' => $lepopup->installation_uid
		);
		$hash_sequence = implode('|', $payumoney_request).'||||||||||'.$data['merchant-salt'];
		$hash = hash("sha512", $hash_sequence);
		$payumoney_request = array_merge($payumoney_request, array(
				'surl' => (empty($data['success-url']) ? $_SERVER["HTTP_REFERER"] : $data['success-url']),
				'furl' => (empty($data['cancel-url']) ? $_SERVER["HTTP_REFERER"] : $data['cancel-url']),
				'hash' => $hash,
				'phone' => $data['phone'],
				'mode' => 'dropout'
			)
		);
		return array('status' => 'OK', 'payumoney' => array('request-data' => $payumoney_request), 'amount' => number_format($data['amount'], 2, '.', ''), 'currency' => $data["currency"], 'gateway-id' => $data['id']);
	}
	
	function front_init() {
		global $wpdb, $lepopup;
		$form_object = null;
		if ((array_key_exists('REQUEST_URI', $_SERVER) && strpos($_SERVER['REQUEST_URI'], 'lepopup-payumoney-ipn-handler') !== false) || defined('UAP_CORE')) {
			$headers = getallheaders();
			if (is_array($headers) && array_key_exists('LEPOPUP-UID', $headers) && $headers['LEPOPUP-UID'] == $lepopup->installation_uid) {
				$payload = @file_get_contents('php://input');
				$post_data = json_decode($payload, true);

				$udf1 = $post_data['udf1'];
				if ($udf1 == $lepopup->installation_uid) {
					$txnid_parts = explode('-', $post_data['merchantTransactionId']);
					$item_id = intval($txnid_parts[0]);
					$payment_status = ucfirst($post_data['status']);
					$transaction_type = $post_data['paymentMode'];
					$txn_id = $post_data['paymentId'];
					$payer_id = !empty($post_data['customerEmail']) ? $post_data['customerEmail'] : (!empty($post_data['customerPhone']) ? $post_data['customerPhone'] : 'PayUmoney Payer');
					$payer_name = !empty($post_data['customerPhone']) ? $post_data['customerPhone'] : (!empty($post_data['customerEmail']) ? $post_data['customerEmail'] : 'PayUmoney Payer');
					$gross_total = $post_data['amount'];
					$mc_currency = "INR";
					$hash_signature = $post_data['hash'];

					if ($payment_status == "Success") {
						$payment_status = "Completed";
						$record_details = $wpdb->get_row("SELECT t1.*, t2.name AS form_name, t2.options AS form_options, t2.elements AS form_elements FROM ".$wpdb->prefix."lepopup_records t1 LEFT JOIN ".$wpdb->prefix."lepopup_forms t2 ON t2.id = t1.form_id WHERE t1.deleted = '0' AND t1.id = '".esc_sql(intval($item_id))."'", ARRAY_A);
						if (!$record_details || !is_array($record_details) || !array_key_exists('gateway_id', $record_details)) $payment_status = "Error: no record";
						else {
							if (!class_exists("lepopup_form")) include_once(dirname(__FILE__).'/core-form.php');
							$form_object = new lepopup_form(intval($record_details['form_id']));
							if (empty($form_object->id)) $payment_status = "Error: no form";
							else {
								$payment_gateway = $form_object->get_payment_gateway($record_details['gateway_id']);
								if (empty($payment_gateway) || !is_array($payment_gateway)) $payment_status = "Error: no payment gateway";
								else {
									$hash_sequence = $payment_gateway['data']['merchant-salt'].'|'.$post_data['status'].'||||||||||'.$post_data['udf1'].$post_data['customerEmail'].'|'.$post_data['customerName'].'|'.$post_data['productInfo'].'|'.$post_data['amount'].'|'.$post_data['merchantTransactionId'].'|'.$payment_gateway['data']['merchant-key'];
									//$hash = hash("sha512", $hash_sequence);
									//if ($hash_signature != $hash) $payment_status = "Error: invalid hash";
									//else 
									if (floatval($gross_total) < floatval($record_details["amount"]) || $mc_currency != $record_details["currency"]) $payment_status = "Error: invalid amount";
								}
							}
						}
					}

					$sql = "INSERT INTO ".$wpdb->prefix."lepopup_transactions (record_id, provider, payer_name, payer_email, gross, currency, payment_status, transaction_type, txn_id, details, created, deleted ) VALUES (
						'".$item_id."',
						'payumoney',
						'".esc_sql($payer_name)."',
						'".esc_sql($payer_id)."',
						'".esc_sql(floatval($gross_total))."',
						'".esc_sql($mc_currency)."',
						'".esc_sql($payment_status)."',
						'".esc_sql($transaction_type)."',
						'".esc_sql($txn_id)."',
						'".esc_sql(json_encode($post_data))."',
						'".esc_sql(time())."',
						'0'
					)";
					$wpdb->query($sql);
					
					if ($payment_status == "Completed") {
						$datestamp = date('Ymd');
						$timestamp = date('h');
						$stats_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_stats WHERE form_id = '".esc_sql($form_object->id)."' AND datestamp = '".esc_sql($datestamp)."' AND timestamp = '".esc_sql($timestamp)."'", ARRAY_A);
						if (!empty($stats_details)) {
							$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_stats SET payments = payments + 1 WHERE id = '".esc_sql($stats_details['id'])."'");
						} else {
							$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_stats (form_id, impressions, submits, confirmed, payments, datestamp, timestamp, deleted) VALUES ('".esc_sql($form_object->id)."', '0', '0', '0', '1', '".esc_sql($datestamp)."', '".esc_sql($timestamp)."', '0')");
						}
						$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_records SET status = '".esc_sql(LEPOPUP_RECORD_STATUS_PAID)."' WHERE id = '".esc_sql($item_id)."'");
						$form_object->form_data = json_decode($record_details['fields'], true);
						$form_object->form_info = json_decode($record_details['info'], true);
						$form_object->record_id = $record_details['id'];
						$form_object->do_notifications("payment-success", array('payment-amount' => $gross_total, 'payment-currency' => $mc_currency, 'payment-status' => $payment_status));
						$form_object->do_integrations("payment-success");
						do_action('lepopup_successfully_paid', $form_object);
					} else {
						$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_records SET status = '".esc_sql(LEPOPUP_RECORD_STATUS_UNPAID)."' WHERE id = '".esc_sql($item_id)."'");
						if (!empty($form_object) && !empty($form_object->id)) {
							$form_object->form_data = json_decode($record_details['fields'], true);
							$form_object->form_info = json_decode($record_details['info'], true);
							$form_object->record_id = $record_details['id'];
							$form_object->do_notifications("payment-fail", array('payment-amount' => $gross_total, 'payment-currency' => $mc_currency, 'payment-status' => $payment_status));
							$form_object->do_integrations("payment-fail");
							do_action('lepopup_unsuccessfully_paid', $form_object);
						}
					}
				}
				http_response_code(200);
				exit;
			} else {
				if (!defined('UAP_CORE')) {
					http_response_code(200);
					exit;
				}
			}
		}
	}
	function admin_details($_html, $_transaction_details, $_pdf = false) {
		global $wpdb, $lepopup;
		$html = $_html;
		$details = json_decode($_transaction_details['details'], true);
		$html = '
		<table class="lepopup-record-details-table">';
		$html .= $this->_admin_details($details, 0, $_pdf);
		$html .= '
		</table>';
		
		return $html;
	}
	function _admin_details($_details, $_level = 0, $_pdf = false) {
		$html = '';
		foreach($_details as $key => $value) {
			if (is_array($value)) $html .= '<tr><td class="lepopup-record-details-table-name" style="'.($_pdf ? 'width:33%;' : '').'padding-left:'.number_format(0.4+$_level*1, 2, '.', '').'em;">'.esc_html($key).'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>...</td></tr>'.$this->_admin_details($value, $_level+1, $_pdf);
			else $html .= '
				<tr><td class="lepopup-record-details-table-name" style="'.($_pdf ? 'width:33%;' : '').'padding-left:'.number_format(0.4+$_level*1, 2, '.', '').'em;">'.esc_html($key).'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.esc_html(urldecode($value)).'</td></tr>';
		}
		return $html;
	}
	function remote_parameters($_parameters) {
		global $wpdb, $lepopup;
		$_parameters['resources']['js'][] = array(
			'src' => 'https://checkout-static.citruspay.com/bolt/run/bolt.min.js',
			'id' => 'bolt',
			'bolt-color' => 'e34524',
			'bolt-logo' => ''
		);
		return $_parameters;
	}
}
$lepopup_payumoney = new lepopup_payumoney_class();
?>