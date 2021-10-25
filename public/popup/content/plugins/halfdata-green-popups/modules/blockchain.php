<?php
/* Blockchain integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_blockchain_class {
	var $default_parameters = array(
		"api-key" => "",
		"xpub" => "",
		"secret" => "",
		"confirmations" => 0,
		"amount" => "",
		"currency" => "USD"
	);
	var $currency_list = array("BTC", "USD", "ISK", "HKD", "TWD", "CHF", "EUR", "DKK", "CLP", "CAD", "CNY", "THB", "AUD", "SGD", "KRW", "JPY", "PLN", "GBP", "SEK", "NZD", "BRL", "RUB");
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_payment_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-blockchain-settings-html', array(&$this, "admin_settings_html"));
			add_filter('lepopup_payment_gateways_transaction_html_blockchain', array(&$this, 'admin_details'), 10, 3);
		}
		add_filter('lepopup_payment_gateways_do_blockchain', array(&$this, 'front_submit'), 10, 2);
		add_action("init", array(&$this, "front_init"));
	}
	
	function providers($_providers) {
		if (!array_key_exists("blockchain", $_providers)) $_providers["blockchain"] = esc_html__('Blockchain', 'lepopup');
		return $_providers;
	}
	
	function admin_settings_html() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options')) {
			$this->default_parameters['secret'] = $lepopup->random_string(16);
			if (array_key_exists('data', $_REQUEST)) {
				$data = json_decode(base64_decode(trim(stripslashes($_REQUEST['data']))), true);
				if (is_array($data)) $data = array_merge($this->default_parameters, $data);
				else $data = $this->default_parameters;
			} else $data = $this->default_parameters;
			$html = '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Receive Payments API Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter a valid Receive Payments API Key. You cannot use the standard blockchain wallet API key for Receive Payments V2, and vice versa.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="api-key" value="'.esc_html($data['api-key']).'" />
					<label class="lepopup-integrations-description">'.esc_html__('Please apply for an API Key at', 'lepopup').' <a href="https://api.blockchain.info/v2/apikey/request/" target="_blank">https://api.blockchain.info/v2/apikey/request/</a></label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('xPub', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please put Extended Public Key (xPub). Find it in your Blockchain account: Settings >> Addresses >> Manage >> More Options >> Show xPub.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="xpub" value="'.esc_html($data['xpub']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Secret', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter random alphanumeric string.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="secret" value="'.esc_html($data['secret']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Confirmations', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please set the number of confirmations of transaction.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<select name="confirmations">';
			for ($i=0; $i<7; $i++) {
				$html .= '
						<option value="'.$i.'"'.($data['confirmations'] == $i ? ' selected="selected"' : '').'>'.$i.'</option>';
			}
			$html .= '									
					</select>
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
			</div>>';
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
		if (empty($data['api-key']) || empty($data['xpub']) || empty($data['secret'])) return $_result;
		if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) return $_result;
		
		$btc_amount = number_format($data['amount'], 8, ".", "");
		if ($data["currency"] != 'BTC') {
			$curl = curl_init('https://blockchain.info/tobtc?currency='.$data["currency"].'&value='.number_format($data['amount'], 2, ".", ""));
			curl_setopt($curl, CURLOPT_POST, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 10);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$btc_amount = curl_exec($curl);
			curl_close($curl);
			if (is_numeric($btc_amount)) {
				$btc_amount = number_format(floatval($btc_amount), 8, ".", "");
			}
			if ($btc_amount == 0 || !is_numeric($btc_amount) || empty($btc_amount)) {
				return array('status' => 'ERROR', 'message' => 'Can not convert to BTC.');
			}
		}
		$callback_base = defined('UAP_CORE') ? admin_url('do.php') : get_bloginfo('url').'/';
		$url = 'https://api.blockchain.info/v2/receive?xpub='.$data['xpub'].'&callback='.urlencode($callback_base.'?lepopup-ipn=blockchain&record_id='.$data["record-id"].'&btc_amount='.number_format($btc_amount, 8, ".", "").'&amount='.number_format($btc_amount, 8, ".", "").'&secret='.$data['secret']).'&key='.$data['api-key'].'&gap_limit=1000';
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 20);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				
		$json = curl_exec($curl);
		curl_close($curl);
		if ($json === false) {
			return array('status' => 'ERROR', 'message' => 'Payment gateway is not available now (error 1).');
		}
		$post = json_decode($json, true);
		if (!$post) {
			return array('status' => 'ERROR', 'message' => 'Payment gateway is not available now (error 2).');
		}
		if (!array_key_exists('address', $post)) {
			$return_data = array('status' => 'ERROR');
			if (array_key_exists('description', $post)) $return_data['message'] = ucfirst($post['description']);
			else if (array_key_exists('message', $post)) $return_data['message'] = ucfirst($post['message']);
			else $return_data['message'] = 'Payment gateway is not available now (error 3).';
			return $return_data;
		}
		$html = sprintf(esc_html__('Please send %s BTC to %s to complete the payment.', 'lepopup'), number_format($btc_amount, 8, ".", ""), $post['address']);
		$result = array('status' => 'OK', 'message' => $html, 'amount' => number_format($btc_amount, 8, '.', ''), 'currency' => 'BTC', 'gateway-id' => $data['id']);
		return $result;
	}
	
	function front_init() {
		global $wpdb, $lepopup;
		$form_object = null;
		if (array_key_exists('lepopup-ipn', $_GET) && $_GET['lepopup-ipn'] == 'blockchain') {
			if (empty($_GET['transaction_hash']) || empty($_GET['secret'])) die();
			$transaction_details = array("operator" => "blockchain");
			foreach ($_GET as $key => $value) {
				$transaction_details[$key] = urldecode($value);
			}

			$item_id = intval($_GET['record_id']);
			$payer_id = 'Anonymous';
			$transaction_type = "bitcoins";
			$payer_name = $payer_id;
			$gross_total = intval($_GET['value'])/100000000;
			$amount = floatval($_GET['amount']);
			$confirmations = intval($_GET['confirmations']);
			$txn_id = urldecode($_GET['transaction_hash']);
			$secret = urldecode($_GET['secret']);
			$mc_currency = 'BTC';
			$payment_status = "Confirmed";

			$tx_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_transactions WHERE txn_id = '".esc_sql($txn_id)."'", ARRAY_A);
			if ($tx_details) {
				echo '*ok*';
				exit;
			}

			if ($payment_status == "Confirmed") {
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
							if ($confirmations < $payment_gateway['data']['confirmations']) exit;
							if (floatval($gross_total) < floatval($amount)) $payment_status = "Error: invalid amount";
							else if ($secret != $payment_gateway['data']['secret']) $payment_status = "Error: invalid secret";
							else if (isset($_GET['test'])) $payment_status = "Test";
						}
					}
				}
			}
			$sql = "INSERT INTO ".$wpdb->prefix."lepopup_transactions (record_id, provider, payer_name, payer_email, gross, currency, payment_status, transaction_type, txn_id, details, created, deleted ) VALUES (
				'".esc_sql($item_id)."',
				'blockchain',
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
			
			if ($payment_status == "Confirmed") {
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
			echo '*ok*';
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
$lepopup_blockchain = new lepopup_blockchain_class();
?>