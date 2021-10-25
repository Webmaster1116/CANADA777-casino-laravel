<?php
/* Perfect Money integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_perfectmoney_class {
	var $default_parameters = array(
		"account-id" => "",
		"payee-name" => "",
		"passphrase" => "",
		"item-name" => "",
		"amount" => "",
		"currency" => "USD",
		"success-url" => "",
		"cancel-url" => ""
	);
	var $currency_list = array("USD", "EUR");
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_payment_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-perfectmoney-settings-html', array(&$this, "admin_settings_html"));
			add_filter('lepopup_payment_gateways_transaction_html_perfectmoney', array(&$this, 'admin_details'), 10, 3);
		}
		add_filter('lepopup_payment_gateways_do_perfectmoney', array(&$this, 'front_submit'), 10, 2);
		add_action("init", array(&$this, "front_init"));
	}
	
	function providers($_providers) {
		if (!array_key_exists("perfectmoney", $_providers)) $_providers["perfectmoney"] = esc_html__('Perfect Money', 'lepopup');
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
			$html = '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Payee Account', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('The merchant Perfect Money account to which the payment is to be made.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="account-id" value="'.esc_html($data['account-id']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Payee Name', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('The name the merchant wishes to have displayed as the Payee on the Perfect Money payment form.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="payee-name" value="'.esc_html($data['payee-name']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Alternate Passphrase', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter valid Alternate Passphrase.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="passphrase" value="'.esc_html($data['passphrase']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find your Alternate Passphrase on %sSettings%s page.', 'lepopup'), '<a href="https://perfectmoney.is/settings.html" target="_blank">', '</a>').'</label>
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
		if (empty($data['account-id']) || empty($data['passphrase'])) return $_result;
		if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) return $_result;
		$html = '
	<form action="https://perfectmoney.is/api/step1.asp" method="post" target="_top" style="display:none !important;">
		<input type="hidden" name="PAYEE_ACCOUNT" value="'.esc_html($data['account-id']).'">
		<input type="hidden" name="PAYEE_NAME" value="'.esc_html($data['payee-name']).'">
		<input type="hidden" name="PAYMENT_AMOUNT" value="'.number_format($data['amount'], 2, '.', '').'">
		<input type="hidden" name="PAYMENT_UNITS" value="'.esc_html($data["currency"]).'">
		<input type="hidden" name="SUGGESTED_MEMO" value="'.(!empty($data['item-name']) ? esc_html($data['item-name']) : 'Fee').'">
		<input type="hidden" name="SUGGESTED_MEMO_NOCHANGE" value="1">
		<input type="hidden" name="PAYMENT_ID" value="'.$data["record-id"].'">
		<input type="hidden" name="PAYMENT_URL" value="'.(empty($data['success-url']) ? esc_html($_SERVER["HTTP_REFERER"]) : esc_html($data['success-url'])).'">
		<input type="hidden" name="PAYMENT_URL_METHOD" value="LINK">
		<input type="hidden" name="NOPAYMENT_URL" value="'.(empty($data['cancel-url']) ? esc_html($_SERVER["HTTP_REFERER"]) : esc_html($data['cancel-url'])).'">
		<input type="hidden" name="NOPAYMENT_URL_METHOD" value="LINK">
		<input type="hidden" name="STATUS_URL" value="'.(defined('UAP_CORE') ? esc_html(admin_url('do.php').'?lepopup-ipn=perfectmoney') : esc_html(get_bloginfo('url').'/?lepopup-ipn=perfectmoney')).'">
		<input type="hidden" name="BAGGAGE_FIELDS" value="lepopupipn payer">
		<input type="hidden" name="lepopupipn" value="perfectmoney">
		<input type="hidden" name="payer" value="">
		<input type="submit" class="lepopup-pay" value="Submit">
	</form>';
		$result = array('status' => 'OK', 'form' => $html, 'amount' => number_format($data['amount'], 2, '.', ''), 'currency' => $data["currency"], 'gateway-id' => $data['id']);
		return $result;
	}
	
	function front_init() {
		global $wpdb, $lepopup;
		$form_object = null;
		if (array_key_exists('lepopupipn', $_REQUEST) && $_REQUEST['lepopupipn'] == 'perfectmoney') {
			if (empty($_POST['PAYMENT_ID']) || empty($_POST['PAYEE_ACCOUNT'])) die();
			$transaction_details = array();
			foreach ($_POST as $key => $value) {
				$transaction_details[$key] = stripslashes($value);
			}
			
			$item_id = intval($_POST['PAYMENT_ID']);
			$payment_status = "Completed";
			$transaction_type = "payment";
			$txn_id = stripslashes($_POST['PAYMENT_BATCH_NUM']);
			$seller_id = stripslashes($_POST['PAYEE_ACCOUNT']);
			$v2_hash = stripslashes($_POST['V2_HASH']);
			$gross_total = stripslashes($_POST['PAYMENT_AMOUNT']);
			$mc_currency = stripslashes($_POST['PAYMENT_UNITS']);
			$payer_id = stripslashes($_POST['PAYER_ACCOUNT']);
			$payer_name = $payer_id;

			if ($payment_status == "Completed") {
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
							$str = $_POST['PAYMENT_ID'].':'.$_POST['PAYEE_ACCOUNT'].':'.
								$_POST['PAYMENT_AMOUNT'].':'.$_POST['PAYMENT_UNITS'].':'.
								$_POST['PAYMENT_BATCH_NUM'].':'.
								$_POST['PAYER_ACCOUNT'].':'.strtoupper(md5($payment_gateway['data']['passphrase'])).':'.
								$_POST['TIMESTAMPGMT'];
							$hash = strtoupper(md5($str));
							if ($v2_hash != $hash) $payment_status = "Error: invalid hash";
							else if (floatval($gross_total) < floatval($record_details["amount"]) || $mc_currency != $record_details["currency"]) $payment_status = "Error: invalid amount";
						}
					}
				}
			}
			$sql = "INSERT INTO ".$wpdb->prefix."lepopup_transactions (record_id, provider, payer_name, payer_email, gross, currency, payment_status, transaction_type, txn_id, details, created, deleted ) VALUES (
				'".$item_id."',
				'perfectmoney',
				'".esc_sql($payer_name)."',
				'".esc_sql($payer_id)."',
				'".esc_sql(floatval($gross_total))."',
				'".esc_sql($mc_currency)."',
				'".esc_sql($payment_status)."',
				'".esc_sql($transaction_type)."',
				'".esc_sql($txn_id)."',
				'".esc_sql(json_encode($transaction_details))."',
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
			exit;
		}
	}
	function admin_details($_html, $_transaction_details, $_pdf = false) {
		global $wpdb, $lepopup;
		$html = $_html;
		$details = json_decode($_transaction_details['details'], true);
		$html = '
		<table class="lepopup-record-details-table">';
		foreach($details as $key => $value) {
			$html .= '
			<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html($key).'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.esc_html(urldecode($value)).'</td></tr>';
		}
		$html .= '
		</table>';
		
		return $html;
	}
}
$lepopup_perfectmoney = new lepopup_perfectmoney_class();
?>