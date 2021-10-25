<?php
/* PayFast integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_payfast_class {
	var $default_parameters = array(
		"merchant-id" => "",
		"merchant-key" => "",
		"passphrase" => "",
		"item-name" => "",
		"amount" => "",
		"currency" => "ZAR",
		"sandbox" => "off",
		"success-url" => "",
		"cancel-url" => ""
	);
	var $currency_list = array("ZAR");
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_payment_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-payfast-settings-html', array(&$this, "admin_settings_html"));
			add_filter('lepopup_payment_gateways_transaction_html_payfast', array(&$this, 'admin_details'), 10, 3);
		}
		add_filter('lepopup_payment_gateways_do_payfast', array(&$this, 'front_submit'), 10, 2);
		add_action("init", array(&$this, "front_init"));
	}
	
	function providers($_providers) {
		if (!array_key_exists("payfast", $_providers)) $_providers["payfast"] = esc_html__('PayFast', 'lepopup');
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
					<label>'.esc_html__('Sandbox', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enable sandbox mode.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input class="lepopup-checkbox-toggle" type="checkbox" value="on" id="sandbox-'.esc_html($checkbox_id).'" name="sandbox"'.($data['sandbox'] == 'on' ? ' checked="checked"' : '').' /><label for="sandbox-'.esc_html($checkbox_id).'"></label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Merchant ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter a valid PayFast Merchant ID. The Merchant ID as given by the PayFast system. Used to uniquely identify the receiving account. This can be found on the merchant’s settings page.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="merchant-id" value="'.esc_html($data['merchant-id']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Merchant Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter a valid PayFast Merchant Key. The Merchant Key as given by the PayFast system. Used to uniquely identify the receiving account. This provides an extra level of certainty concerning the correct account as both the ID and the Key must be correct in order for the transaction to proceed. This can be found on the merchant’s settings page.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="merchant-key" value="'.esc_html($data['merchant-key']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Passphrase', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter a valid Passphrase. The passphrase is an optional/extra security feature, made up of a maximum of 32 characters, which is set by the Merchant in the Settings section of PayFast Dashboard.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="passphrase" value="'.esc_html($data['passphrase']).'" />
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
					<div class="lepopup-tooltip-content">'.esc_html__('The URL where the user is returned to after payment has been successfully taken.', 'lepopup').'</div>
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
					<div class="lepopup-tooltip-content">'.esc_html__('The URL where the user should be redirected if they choose to cancel their payment while on the PayFast system.', 'lepopup').'</div>
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
		if (empty($data['merchant-id'])) return $_result;
		if (empty($data['merchant-key'])) return $_result;
		if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) return $_result;
		$post_data = array(
			'merchant_id' => $data['merchant-id'],
			'merchant_key' => $data['merchant-key'],
			'return_url' => (empty($data['success-url']) ? $_SERVER["HTTP_REFERER"] : $data['success-url']),
			'cancel_url' => (empty($data['cancel-url']) ? $_SERVER["HTTP_REFERER"] : $data['cancel-url']),
			'notify_url' => (defined('UAP_CORE') ? admin_url('do.php').'?lepopup-ipn=payfast' : get_bloginfo('url').'/?lepopup-ipn=payfast'),
			'm_payment_id' => $data["record-id"].'-'.time(),
			'amount' => number_format($data['amount'], 2, '.', ''),
			'item_name' => (!empty($data['item-name']) ? $data['item-name'] : 'Fee'),
			'custom_str1' => ($data['sandbox'] == 'on' ? 'sandbox' : 'product')
		);
		$hash_string = '';
		foreach($post_data as $key => $value) {
			if(!empty($value)) {
				$hash_string .= $key .'='. urlencode(trim($value)).'&';
			}
		}
		$hash_string = substr($hash_string, 0, -1);
		if(!empty($data['passphrase'])) {
			$hash_string .= '&passphrase='.urlencode(trim($data['passphrase']));
		}   
		$post_data['signature'] = md5($hash_string);		
		$html = '
	<form action="https://'.($data['sandbox'] == 'on' ? 'sandbox' : 'www').'.payfast.co.za/eng/process" method="post" target="_top" style="display:none; !important;">';
					foreach($post_data as $key => $value) {
						$html .= '
		<input type="hidden" name="'.esc_html($key).'" value="'.esc_html($value).'">';
					}
					$html .= '
		<input type="submit" class="lepopup-pay" value="Submit">
	</form>';
		$result = array('status' => 'OK', 'form' => $html, 'amount' => number_format($data['amount'], 2, '.', ''), 'currency' => $data["currency"], 'gateway-id' => $data['id']);
		return $result;
	}
	
	function front_init() {
		global $wpdb, $lepopup;
		$form_object = null;
		if (array_key_exists('lepopup-ipn', $_REQUEST) && $_REQUEST['lepopup-ipn'] == 'payfast') {
			if (!array_key_exists('pf_payment_id', $_REQUEST)) exit;
			$transaction_details = array();
			foreach ($_POST as $key => $value) {
				$transaction_details[$key] = stripslashes($value);
				$value = urlencode(stripslashes($value));
				$request .= "&".$key."=".$value;
			}
			$url = 'https://'.(array_key_exists('custom_str1', $_REQUEST) && $_REQUEST['custom_str1'] == 'sandbox' ? 'sandbox' : 'www').'.payfast.co.za/eng/query/validate';
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close', 'User-Agent: Green Forms'));
			$result = curl_exec($ch);
			curl_close($ch);
			if (substr(trim($result), 0, 5) != "VALID") exit;

			$item_id = $_POST['m_payment_id'];
			if (($pos = strpos($item_id, "-")) !== false) $item_id = intval(substr($item_id, 0, $pos));
			$item_id = intval($item_id);
			$item_name = stripslashes($_POST['item_name']);
			$gross_total = stripslashes($_POST['amount_gross']);
			$payment_status = stripslashes($_POST['payment_status']);
			$transaction_type = 'payment';
			$txn_id = stripslashes($_POST['pf_payment_id']);
			$merchant_id = stripslashes($_POST['merchant_id']);
			$payer_id = stripslashes($_POST['email_address']);
			$payer_name = trim(stripslashes($_POST['name_first']).' '.stripslashes($_POST['name_last']));
			$mc_currency = 'ZAR';
			if (empty($payer_id)) $payer_id = 'Unknown';
			if (empty($payer_name)) $payer_name = 'Unknown';

			if ($payment_status == "COMPLETE") {
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
							if (strtolower($merchant_id) != strtolower($payment_gateway['data']['merchant-id'])) $payment_status = "Error: invalid recipient";
							else {
								if (floatval($gross_total) < floatval($record_details["amount"])) $payment_status = "Error: invalid amount";
							}
							$mc_currency = $record_details["currency"];
						}
					}
				}
			}
			$sql = "INSERT INTO ".$wpdb->prefix."lepopup_transactions (record_id, provider, payer_name, payer_email, gross, currency, payment_status, transaction_type, txn_id, details, created, deleted ) VALUES (
				'".$item_id."',
				'payfast',
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
			
			if ($payment_status == "COMPLETE") {
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
$lepopup_payfast = new lepopup_payfast_class();
?>