<?php
/* Razorpay integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_razorpay_class {
	var $default_parameters = array(
		"key-id" => "",
		"key-secret" => "",
		"business-name" => "",
		"item-name" => "",
		"phone" => "",
		"email" => "",
		"amount" => "",
		"currency" => "INR",
		"success-url" => "",
		"cancel-url" => ""
	);
	var $currency_list = array("AED", "ALL", "AMD", "ARS", "AUD", "AWG", "BBD", "BDT", "BMD", "BND", "BOB", "BSD", "BWP", "BZD", "CAD", "CHF", "CNY", "COP", "CRC", "CUP", "CZK", "DKK", "DOP", "DZD", "EGP", "ETB", "EUR", "FJD", "GBP", "GIP", "GHS", "GMD", "GTQ", "GYD", "HKD", "HNL", "HRK", "HTG", "HUF", "IDR", "ILS", "INR", "JMD", "KES", "KGS", "KHR", "KYD", "KZT", "LAK", "LBP", "LKR", "LRD", "LSL", "MAD", "MDL", "MKD", "MMK", "MNT", "MOP", "MUR", "MVR", "MWK", "MXN", "MYR", "NAD", "NGN", "NIO", "NOK", "NPR", "NZD", "PEN", "PGK", "PHP", "PKR", "QAR", "RUB", "SAR", "SCR", "SEK", "SGD", "SLL", "SOS", "SSP", "SVC", "SZL", "THB", "TTD", "TZS", "USD", "UYU", "UZS", "YER", "ZAR");
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_payment_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-razorpay-settings-html', array(&$this, "admin_settings_html"));
			add_filter('lepopup_payment_gateways_transaction_html_razorpay', array(&$this, 'admin_details'), 10, 3);
		}
		add_filter('lepopup_payment_gateways_do_razorpay', array(&$this, 'front_submit'), 10, 2);
		add_action("init", array(&$this, "front_init"));
	}
	
	function providers($_providers) {
		if (!array_key_exists("razorpay", $_providers)) $_providers["razorpay"] = esc_html__('Razorpay', 'lepopup');
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
					<h4>'.sprintf(esc_html__('Important! Make sure that you created webhook with following parameters in your %sRazorpay Dashboard%s.', 'lepopup'), '<a href="https://dashboard.razorpay.com/#/app/webhooks" target="_blank">', '</a>').'</h4>
					<div class="lepopup-properties-item">
						<div class="lepopup-properties-label">
							<label>'.esc_html__('Webhook URL', 'lepopup').'</label>
						</div>
						<div class="lepopup-properties-content">
							<input type="text" readonly="readonly" value="'.(defined('UAP_CORE') ? esc_html(admin_url('do.php').'?lepopup-ipn=razorpay') : esc_html(get_bloginfo('url').'/?lepopup-ipn=razorpay')).'" onclick="this.focus();this.select();" />
						</div>
					</div>
					<div class="lepopup-properties-item">
						<div class="lepopup-properties-label">
							<label>'.esc_html__('Secret', 'lepopup').'</label>
						</div>
						<div class="lepopup-properties-content">
							<input type="text" readonly="readonly" value="'.esc_html($lepopup->installation_uid).'" onclick="this.focus();this.select();" />
						</div>
					</div>
					<div class="lepopup-properties-item">
						<div class="lepopup-properties-label">
							<label>'.esc_html__('Active Events', 'lepopup').'</label>
						</div>
						<div class="lepopup-properties-content">
							<input type="text" readonly="readonly" value="order.paid" onclick="this.focus();this.select();" />
						</div>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Key ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter a valid Key ID.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="key-id" value="'.esc_html($data['key-id']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find it on %sAPI Keys%s page.', 'lepopup'), '<a href="https://dashboard.razorpay.com/#/app/keys" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Key Secret', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter a valid Key Secret.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="key-secret" value="'.esc_html($data['key-secret']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find it on %sAPI Keys%s page.', 'lepopup'), '<a href="https://dashboard.razorpay.com/#/app/keys" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Business name', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter a business name to be shown in the checkout form.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-input-shortcode-selector">
						<input type="text" name="business-name" value="'.(empty($data['business-name']) ? esc_html(get_bloginfo('name')) : esc_html($data['business-name'])).'" />
						<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Item name', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter the item name.', 'lepopup').'</div>
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
					<label>'.esc_html__('Payer\'s phone', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter mobile number or landline number of the payer. This parameter can not be empty.', 'lepopup').'</div>
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
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter email address of the payer. This parameter can not be empty.', 'lepopup').'</div>
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
		if (empty($data['key-id']) || empty($data['key-secret'])) return $_result;
		if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) return $_result;
		$result = array();
		$params = array(
			'amount' => intval($data['amount']*100),
			'currency' => $data['currency'],
			'payment_capture' => 1,
			'receipt' => $data["record-id"].'-'.$lepopup->random_string(16)
		);
		$order_data = $this->connect($data['key-id'], $data['key-secret'], 'orders', $params);
		if (array_key_exists('error', $order_data)) {
			return array('status' => 'ERROR', 'message' => rtrim($order_data['error']['description'], '.').'.');
		} else if (array_key_exists('id', $order_data)) {
			$html = '
	<form action="https://api.razorpay.com/v1/checkout/embedded" method="post" target="_top" style="display: none !important;">
		<input type="hidden" name="key_id" value="'.esc_html($data['key-id']).'">
		<input type="hidden" name="order_id" value="'.esc_html($order_data['id']).'">
		<input type="hidden" name="amount" value="'.intval($data['amount']*100).'">
		<input type="hidden" name="currency" value="'.esc_html($data["currency"]).'">
		<input type="hidden" name="name" value="'.esc_html($data['business-name']).'">
		<input type="hidden" name="prefill[email]" value="'.esc_html($data['email']).'">
		<input type="hidden" name="prefill[contact]" value="'.esc_html($data['phone']).'">
		<input type="hidden" name="description" value="'.esc_html($data['item-name']).'">
		<input type="hidden" name="callback_url" value="'.(empty($data['success-url']) ? esc_html($_SERVER["HTTP_REFERER"]) : esc_html($data['success-url'])).'">
		<input type="hidden" name="cancel_url" value="'.(empty($data['cancel-url']) ? esc_html($_SERVER["HTTP_REFERER"]) : esc_html($data['cancel-url'])).'">
		<input type="submit" class="lepopup-pay" value="Submit">
	</form>';		
			$result = array('status' => 'OK', 'form' => $html, 'amount' => number_format($data['amount'], 2, '.', ''), 'currency' => $data["currency"], 'gateway-id' => $data['id']);
			
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Invalid server response.', 'lepopup'));
		}
		return $result;
	}
	
	function front_init() {
		global $wpdb, $lepopup;
		$form_object = null;
		if (array_key_exists('lepopup-ipn', $_REQUEST) && $_REQUEST['lepopup-ipn'] == 'razorpay') {
			$payload = @file_get_contents('php://input');
			$headers = getallheaders();
			if (!is_array($headers) || !array_key_exists('X-Razorpay-Signature', $headers)) {
				http_response_code(200);
				exit;
			}
			$header_signature = strtolower($headers['X-Razorpay-Signature']);
			$expected_signature = strtolower(hash_hmac('sha256', $payload, $lepopup->installation_uid));
			if ($header_signature != $expected_signature) {
				http_response_code(200);
				exit;
			}
				
			$post_data = json_decode($payload, true);
			if (empty($post_data) || !is_array($post_data) || !array_key_exists('payload', $post_data) || !array_key_exists('event', $post_data) || $post_data['event'] != 'order.paid') {
				http_response_code(200);
				exit;
			}

			$id_parts = explode('-', $post_data['payload']['order']['entity']['receipt']);
			if (sizeof($id_parts) != 2) {
				http_response_code(200);
				exit;
			}

			$item_id = intval($id_parts[0]);
			$payment_status = $post_data['payload']['order']['entity']['status'];
			$transaction_type = $post_data['payload']['payment']['entity']['method'];
			$txn_id = $post_data['payload']['payment']['entity']['id'];
			$payer_id = !empty($post_data['payload']['payment']['entity']['email']) ? $post_data['payload']['payment']['entity']['email'] : (!empty($post_data['payload']['payment']['entity']['phone']) ? $post_data['payload']['payment']['entity']['phone'] : 'Razorpay Payer');
			$payer_name = !empty($post_data['payload']['payment']['entity']['phone']) ? $post_data['payload']['payment']['entity']['phone'] : (!empty($post_data['payload']['payment']['entity']['email']) ? $post_data['payload']['payment']['entity']['email'] : 'Razorpay Payer');
			$mc_currency = strtoupper($post_data['payload']['payment']['entity']['currency']);
			$gross_total = number_format($post_data['payload']['payment']['entity']['amount']/100, 2, '.', '');

			if ($payment_status == "paid") {
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
							if (floatval($gross_total) < floatval($record_details["amount"]) || $mc_currency != $record_details["currency"]) $payment_status = "Error: invalid amount";
						}
					}
				}
			}
			$sql = "INSERT INTO ".$wpdb->prefix."lepopup_transactions (record_id, provider, payer_name, payer_email, gross, currency, payment_status, transaction_type, txn_id, details, created, deleted ) VALUES (
				'".$item_id."',
				'razorpay',
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
				http_response_code(200);
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
			http_response_code(200);
			exit;
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
	function connect($_key_id, $_key_secret, $_path, $_data = array(), $_method = '') {
		$headers = array(
			'Content-Type: application/json;charset=UTF-8',
			'Accept: application/json'
		);
		try {
			$url = 'https://api.razorpay.com/v1/'.ltrim($_path, '/');
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, $_key_id.':'.$_key_secret);
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
$lepopup_razorpay = new lepopup_razorpay_class();
?>