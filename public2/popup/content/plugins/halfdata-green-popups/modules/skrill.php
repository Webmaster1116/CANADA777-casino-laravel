<?php
/* Skrill integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_skrill_class {
	var $default_parameters = array(
		"merchant-email" => "",
		"secret-word" => "",
		"item-name" => "",
		"amount" => "",
		"currency" => "USD",
		"success-url" => "",
		"cancel-url" => ""
	);
	var $currency_list = array("EUR","TWD","USD","THB","GBP","CZK","HKD","HUF","SGD","SKK","JPY","EEK","CAD","BGN","AUD","PLN","CHF","ISK","DKK","INR","SEK","LVL","NOK","KRW","ILS","ZAR","MYR","RON","NZD","HRK","TRY","LTL","AED","JOD","MAD","OMR","QAR","RSD","SAR","TND");
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_payment_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-skrill-settings-html', array(&$this, "admin_settings_html"));
			add_filter('lepopup_payment_gateways_transaction_html_skrill', array(&$this, 'admin_details'), 10, 3);
		}
		add_filter('lepopup_payment_gateways_do_skrill', array(&$this, 'front_submit'), 10, 2);
		add_action("init", array(&$this, "front_init"));
	}
	
	function providers($_providers) {
		if (!array_key_exists("skrill", $_providers)) $_providers["skrill"] = esc_html__('Skrill', 'lepopup');
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
					<label>'.esc_html__('Merchant email', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Email address of your Skrill merchant account.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="merchant-email" value="'.esc_html($data['merchant-email']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Secret word', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter a valid secret word.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="secret-word" value="'.esc_html($data['secret-word']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find secret word on %sDeveloper Settings%s page.', 'lepopup'), '<a href="https://account.skrill.com/user_settings#!#developer_settings" target="_blank">', '</a>').'</label>
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
		if (empty($data['merchant-email']) || empty($data['secret-word'])) return $_result;
		if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) return $_result;
		$post_data = array(
			'pay_to_email' => $data['merchant-email'],
			'recipient_description' => (!empty($data['item-name']) ? esc_html($data['item-name']) : 'Fee'),
			'transaction_id' => $data["record-id"].'-'.time(),
			'return_url' => empty($data['success-url']) ? esc_html($_SERVER["HTTP_REFERER"]) : esc_html($data['success-url']),
			'cancel_url' => empty($data['cancel-url']) ? esc_html($_SERVER["HTTP_REFERER"]) : esc_html($data['cancel-url']),
			'status_url' => defined('UAP_CORE') ? esc_html(admin_url('do.php').'?lepopup-ipn=skrill') : esc_html(get_bloginfo('url').'/?lepopup-ipn=skrill'),
			'language' => 'EN',
			'prepare_only' => 1,
			'merchant_fields' => 'record',
			'record' => $data["record-id"],
			'amount' => $data['amount'],
			'currency' => $data['currency'],
			'detail1_description' => 'Item: ',
			'detail1_text' => (!empty($data['item-name']) ? esc_html($data['item-name']) : 'Fee')
		);
		try {
			$curl = curl_init('https://pay.skrill.com/');
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
			curl_setopt($curl, CURLOPT_TIMEOUT, 120);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($curl);
			$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			if ($http_code == 200) {
				$html = '
	<form action="https://pay.skrill.com/" method="post" target="_top" style="display:none; !important;">
		<input type="hidden" name="sid" value="'.esc_html($response).'">
		<input type="submit" class="lepopup-pay" value="Submit">
	</form>';
				$result = array('status' => 'OK', 'form' => $html, 'amount' => number_format($data['amount'], 2, '.', ''), 'currency' => $data["currency"], 'gateway-id' => $data['id']);
				return $result;
			} else {
				$result = json_decode($response, true);
				if (is_array($result) && array_key_exists('message', $result)) {
					return array('status' => 'ERROR', 'message' => rtrim($result['message'], '.').'.');
				} else {
					return array('status' => 'ERROR', 'message' => 'Invalid server response.');
				}
			}
		} catch(Exception $e) {
			$body = $e->getJsonBody();
			return array('status' => 'ERROR', 'message' => rtrim($body['error']['message'], '.').'.');
		}
	}
	
	function front_init() {
		global $wpdb, $lepopup;
		$form_object = null;
		if (array_key_exists('lepopup-ipn', $_REQUEST) && $_REQUEST['lepopup-ipn'] == 'skrill') {
			if (!array_key_exists('md5sig', $_REQUEST) || !array_key_exists('pay_to_email', $_REQUEST) || !array_key_exists('transaction_id', $_REQUEST)) die();
			$transaction_details = array();
			foreach ($_POST as $key => $value) {
				$transaction_details[$key] = stripslashes($value);
			}
			$item_id = $_POST['transaction_id'];
			if (($pos = strpos($item_id, "-")) !== false) $item_id = intval(substr($item_id, 0, $pos));
			$payment_status = stripslashes($_POST['status']);
			$transaction_type = stripslashes($_POST['payment_type']);
			$txn_id = stripslashes($_POST['mb_transaction_id']);
			$seller_id = stripslashes($_POST['pay_to_email']);
			$payer_id = stripslashes($_POST['pay_from_email']);
			$gross_total = stripslashes($_POST['amount']);
			$mc_currency = stripslashes($_POST['currency']);
			$payer_name = $payer_id;
			$md5sig = stripslashes($_POST['md5sig']);

			if ($payment_status == 2) $payment_status = 'Completed';
			else if ($payment_status == 0) $payment_status = 'Pending';
			else if ($payment_status == -1) $payment_status = 'Cancelled';
			else if ($payment_status == -2) $payment_status = 'Failed';
			else if ($payment_status == -3) $payment_status = 'Chargeback';

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
							$hash = strtoupper(md5($_POST['merchant_id'].$_POST['transaction_id'].strtoupper(md5($payment_gateway['data']['secret-word'])).$_POST['mb_amount'].$_POST['mb_currency'].$_POST['status']));
							if (strtolower($seller_id) != strtolower($payment_gateway['data']['merchant-email'])) $payment_status = "Error: invalid recipient";
							else if ($md5sig != $hash) $payment_status = "Error: invalid signature";
							else if (floatval($gross_total) < floatval($record_details["amount"]) || $mc_currency != $record_details["currency"]) $payment_status = "Error: invalid amount";
						}
					}
				}
			}

			$sql = "INSERT INTO ".$wpdb->prefix."lepopup_transactions (record_id, provider, payer_name, payer_email, gross, currency, payment_status, transaction_type, txn_id, details, created, deleted ) VALUES (
				'".$item_id."',
				'skrill',
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
	
}
$lepopup_skrill = new lepopup_skrill_class();
?>