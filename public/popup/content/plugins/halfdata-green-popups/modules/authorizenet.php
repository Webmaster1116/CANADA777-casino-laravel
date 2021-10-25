<?php
/* Authorize.Net integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_authorizenet_class {
	var $default_parameters = array(
		"mode" => "live",
		"login-id" => "",
		"transaction-key" => "",
		"signature-key" => "",
		"item-name" => "",
		"amount" => "",
		"currency" => "USD",
		"success-url" => "",
		"cancel-url" => ""
	);
	var $currency_list = array("USD");
	
	function __construct() {
		
		if (is_admin()) {
			add_filter('lepopup_payment_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-authorizenet-settings-html', array(&$this, "admin_settings_html"));
			add_filter('lepopup_payment_gateways_transaction_html_authorizenet', array(&$this, 'admin_details'), 10, 3);
		}
		add_filter('lepopup_payment_gateways_do_authorizenet', array(&$this, 'front_submit'), 10, 2);
		add_action("init", array(&$this, "front_init"));
	}
	
	function providers($_providers) {
		if (!array_key_exists("authorizenet", $_providers)) $_providers["authorizenet"] = esc_html__('Authorize.Net', 'lepopup');
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
				'.esc_html__('Important! Make sure that you set the following Webhook URL in your Authorize.Net account ((Account > Settings > Business Settings > Notification Settings > Webhooks)).', 'lepopup').'
				<input type="text" readonly="readonly" value="'.(defined('UAP_CORE') ? esc_html(admin_url('do.php')) : esc_html(get_bloginfo('url').'/lepopup-authnet-ipn-handler/')).'" onclick="this.focus();this.select();" />
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Mode', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please select the mode of Authorize.Net integration: Live or Sandbox.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<select name="mode">
						<option value="live"'.($data['mode'] == 'live' ? ' selected="selected"' : '').'>'.esc_html__('Live', 'lepopup').'</option>
						<option value="sandbox"'.($data['mode'] == 'sandbox' ? ' selected="selected"' : '').'>'.esc_html__('Sandbox', 'lepopup').'</option>
					</select>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('API Login ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter valid API Login ID. If you do not know API Login ID, go to your Authorize.Net account settings and click "API Credentials & Keys".', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="login-id" value="'.esc_html($data['login-id']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Transaction Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter valid Transaction Key. If you do not know Transaction Key, go to your Authorize.Net account settings and click "API Credentials & Keys".', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="transaction-key" value="'.esc_html($data['transaction-key']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Signature Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter valid Signature Key. If you do not know Signature Key, go to your Authorize.Net account settings and click "API Credentials & Keys".', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="signature-key" value="'.esc_html($data['signature-key']).'" />
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
		if (empty($data['login-id']) || empty($data['transaction-key']) || empty($data['signature-key'])) return $_result;
		if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) return $_result;
		$post_data = array(
			'getHostedPaymentPageRequest' => array(
				'merchantAuthentication' => array(
					'name' => $data['login-id'],
					'transactionKey' => $data['transaction-key']
				),
				'refId' => $lepopup->installation_uid.'-'.$data["record-id"],
					'transactionRequest' => array(
					'transactionType' => 'authCaptureTransaction',
					'amount' => $data['amount']
				),
				'hostedPaymentSettings' => array(
					'setting' => array(
						array(
							'settingName' => 'hostedPaymentReturnOptions',
							'settingValue' => json_encode(array(
								'showReceipt' => true,
								'url' => (empty($data['success-url']) ? $_SERVER["HTTP_REFERER"] : $data['success-url']),
								'urlText' => __('Continue', 'lepopup'),
								'cancelUrl' => (empty($data['cancel-url']) ? $_SERVER["HTTP_REFERER"] : $data['cancel-url']),
								'cancelUrlText' => __('Cancel', 'lepopup'),
							))
						),
						array(
							'settingName' => 'hostedPaymentBillingAddressOptions',
							'settingValue' => json_encode(array(
								'show' => false,
								'required' => false
							))
						),
						array(
							'settingName' => 'hostedPaymentCustomerOptions',
							'settingValue' => json_encode(array(
								'showEmail' => true,
								'requiredEmail' => false,
								'addPaymentProfile' => false
							))
						)
					)
				)
			)
		);
		$payment = $this->connect($data['mode'], $post_data);

		if (empty($payment) || !is_array($payment)) {
			$result = array('status' => 'ERROR', 'message' => esc_html__('Can not connect to Authorize.Net server.', 'lepopup'));
		} else if (array_key_exists('token', $payment)) {
			$html .= '
	<form action="'.($data['mode'] == "live" ? 'https://accept.authorize.net/payment/payment' : 'https://test.authorize.net/payment/payment').'" method="post" target="_top" style="display:none !important;">
		<input type="hidden" name="token" value="'.esc_html($payment['token']).'">
		<input type="submit" class="lepopup-pay" value="Submit">
	</form>';
			$result = array('status' => 'OK', 'form' => $html, 'amount' => number_format($data['amount'], 2, '.', ''), 'currency' => $data["currency"], 'gateway-id' => $data['id']);
		} else if (array_key_exists('messages', $payment) && is_array($payment['messages']) && array_key_exists('message', $payment['messages']) && is_array($payment['messages']['message'])) {
			$result = array('status' => 'ERROR', 'message' => $payment['messages']['message'][0]['text']);
		} else {
			$result = array('status' => 'ERROR', 'message' => esc_html__('Invalid response from Authorize.net server.', 'lepopup'));
		}
		return $result;
	}
	
	function front_init() {
		global $wpdb, $lepopup;
		$form_object = null;
		if ((array_key_exists('REQUEST_URI', $_SERVER) && strpos($_SERVER['REQUEST_URI'], 'lepopup-authnet-ipn-handler') !== false) || defined('UAP_CORE')) {
			$headers = getallheaders();
			if (is_array($headers) && array_key_exists('X-ANET-Signature', $headers)) {
				$payload = @file_get_contents('php://input');
				$post_data = json_decode($payload, true);
				if (!empty($post_data) && is_array($post_data) && array_key_exists('payload', $post_data) && is_array($post_data['payload']) && array_key_exists('merchantReferenceId', $post_data['payload']) && array_key_exists('entityName', $post_data['payload']) && $post_data['payload']['entityName'] == 'transaction') {
					$refid_parts = explode('-', $post_data['payload']['merchantReferenceId']);
					if ($refid_parts[0] == $lepopup->installation_uid) {
						$item_id = intval($refid_parts[1]);
						$payment_status = $post_data['eventType'];
						$transaction_type = 'Tx';
						$txn_id = $post_data['payload']['id'];
						$payer_id = '';
						$payer_name = 'Authorize.Net Payer';
						$gross_total = $post_data['payload']['authAmount'];
						$mc_currency = "USD";
						$statuses = array(
							'net.authorize.payment.authcapture.created' => 'Completed',
							'net.authorize.payment.authorization.created' => 'Completed',
							'net.authorize.payment.capture.created' => 'Completed',
							'net.authorize.payment.priorAuthCapture.created' => 'PriorAuthCapture',
							'net.authorize.payment.refund.created' => 'Refund',
							'net.authorize.payment.void.created' => 'Void'
						);
						if (array_key_exists($payment_status, $statuses)) {
							$record_details = $wpdb->get_row("SELECT t1.*, t2.name AS form_name, t2.options AS form_options, t2.elements AS form_elements FROM ".$wpdb->prefix."lepopup_records t1 LEFT JOIN ".$wpdb->prefix."lepopup_forms t2 ON t2.id = t1.form_id WHERE t1.deleted = '0' AND t1.id = '".esc_sql(intval($item_id))."'", ARRAY_A);
							if (!$record_details || !is_array($record_details) || !array_key_exists('gateway_id', $record_details)) $payment_status = "Error: no record";
							else {
								$payment_status = $statuses[$payment_status];
								if ($payment_status == 'Completed') {
									if (!class_exists("lepopup_form")) include_once(dirname(__FILE__).'/core-form.php');
									$form_object = new lepopup_form(intval($record_details['form_id']));
									if (empty($form_object->id)) $payment_status = "Error: no form";
									else {
										$payment_gateway = $form_object->get_payment_gateway($record_details['gateway_id']);
										if (empty($payment_gateway) || !is_array($payment_gateway)) $payment_status = "Error: no payment gateway";
										else {
											$signature_parts = explode('=', $headers['X-ANET-Signature']);
											$hash = strtoupper(hash_hmac("sha512", $payload, trim($payment_gateway['data']['signature-key'])));
											if ($hash != strtoupper($signature_parts[1])) $payment_status = "Error: invalid hash";
											else if (floatval($gross_total) < floatval($record_details["amount"]) || $mc_currency != $record_details["currency"]) $payment_status = "Error: invalid amount";
											else {
												$request = array(
													'getTransactionDetailsRequest' => array(
														'merchantAuthentication' => array(
															'name' => $payment_gateway['data']['login-id'],
															'transactionKey' => $payment_gateway['data']['transaction-key']
														),
														'transId' => $post_data['payload']['id']
													)
												);
												$result = $this->connect($payment_gateway['data']['mode'], $request);
												if (empty($result) || !is_array($result) || !array_key_exists('transaction', $result)) $payment_status = "Error: invalid tx";
												else {
													$post_data = array_merge($post_data, $result);
													if (array_key_exists('customer', $result['transaction']) && array_key_exists('email', $result['transaction']['customer']) && !empty($result['transaction']['customer']['email'])) {
														$payer_id = $result['transaction']['customer']['email'];
														$payer_name = $result['transaction']['customer']['email'];
													}
													//if (array_key_exists('transactionType', $result['transaction']) && !empty($result['transaction']['transactionType'])) {
													//	$transaction_type = $result['transaction']['transactionType'];
													//}
												}
												
											}
										}
									}
								}
							}
							$sql = "INSERT INTO ".$wpdb->prefix."lepopup_transactions (record_id, provider, payer_name, payer_email, gross, currency, payment_status, transaction_type, txn_id, details, created, deleted ) VALUES (
								'".$item_id."',
								'authorizenet',
								'".esc_sql($payer_name)."',
								'".esc_sql($payer_id)."',
								'".esc_sql(number_format($gross_total, 2, '.', ''))."',
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
					}
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
	
	function connect($_mode, $_data) {
		$headers = array(
			'Content-Type: application/json;charset=UTF-8',
			'Accept: application/json'
		);
		try {
			$url = ($_mode == "live") ? 'https://api.authorize.net/xml/v1/request.api' : 'https://apitest.authorize.net/xml/v1/request.api';
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($_data));
			curl_setopt($curl, CURLOPT_TIMEOUT, 20);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($curl);
			curl_close($curl);
			$result = json_decode(preg_replace('/^'.pack('H*','EFBBBF').'/', '', $response), true);
		} catch (Exception $e) {
			$result = false;
		}
		return $result;
	}
	
}
$lepopup_authorizenet = new lepopup_authorizenet_class();
?>