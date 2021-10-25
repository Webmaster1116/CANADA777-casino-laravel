<?php
/* Yandex.Money integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_yandexmoney_class {
	var $default_parameters = array(
		"wallet-id" => "",
		"secret-word" => "",
		"label-prefix" => "",
		"payment-type" => "AC",
		"item-name" => "",
		"amount" => "",
		"currency" => "RUB",
		"success-url" => ""
	);
	var $currency_list = array("RUB");
	
	function __construct() {
		$domain = parse_url(get_bloginfo('url'), PHP_URL_HOST);
		$this->default_parameters['label-prefix'] = 'gf-'.preg_replace('/[^a-zA-Z0-9\s]/', '', str_replace("www.", "", $domain));
		if (is_admin()) {
			add_filter('lepopup_payment_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-yandexmoney-settings-html', array(&$this, "admin_settings_html"));
			add_filter('lepopup_payment_gateways_transaction_html_yandexmoney', array(&$this, 'admin_details'), 10, 3);
		}
		add_filter('lepopup_payment_gateways_do_yandexmoney', array(&$this, 'front_submit'), 10, 2);
		add_action("init", array(&$this, "front_init"));
	}
	
	function providers($_providers) {
		if (!array_key_exists("yandexmoney", $_providers)) $_providers["yandexmoney"] = esc_html__('Yandex.Money', 'lepopup');
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
				'.sprintf(esc_html__('Important! Make sure that you set the following URL for HTTP-notices in your %sYandex.Money Dashboard%s.', 'lepopup'), '<a href="https://money.yandex.ru/myservices/online.xml" target="_blank">', '</a>').'
				<input type="text" readonly="readonly" value="'.(defined('UAP_CORE') ? esc_html(admin_url('do.php').'?lepopup-ipn=yandexmoney') : esc_html(get_bloginfo('url').'/?lepopup-ipn=yandexmoney')).'" onclick="this.focus();this.select();" />
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Wallet ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter your Wallet ID.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="wallet-id" value="'.esc_html($data['wallet-id']).'" placeholder="4100..." />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find it on %sSettings%s page.', 'lepopup'), '<a href="https://money.yandex.ru/settings" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Secret Word', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter valid Secret Word.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="secret-word" value="'.esc_html($data['secret-word']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find it on the same page where you entered URL for HTTP-notices in your %sYandex.Money Dashboard%s.', 'lepopup'), '<a href="https://money.yandex.ru/myservices/online.xml" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Label prefix', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter random alphanumeric string. It is required for some intermediate services to split HTTP-notices. If you do not use them, just ignore this parameter and leave it as is.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="label-prefix" value="'.esc_html($data['label-prefix']).'" placeholder="" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Payment type', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please select payment type.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-bar-selector"><input type="radio" value="PC" name="payment-type" id="payment-type-'.$checkbox_id.'-pc"'.($data['payment-type'] == 'PC' ? ' checked="checked"' : '').' /><label for="payment-type-'.$checkbox_id.'-pc">'.esc_html__('Wallet', 'lepopup').'</label><input type="radio" value="AC" name="payment-type" id="payment-type-'.$checkbox_id.'-ac"'.($data['payment-type'] == 'AC' ? ' checked="checked"' : '').' /><label for="payment-type-'.$checkbox_id.'-ac">'.esc_html__('Bank Card', 'lepopup').'</label></div>
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
		if (empty($data['wallet-id']) || empty($data['secret-word'])) return $_result;
		if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) return $_result;
		$html = '
	<form method="POST" action="https://money.yandex.ru/quickpay/confirm.xml" target="_top" style="display: none !important;">
		<input type="hidden" name="receiver" value="'.esc_html($data['wallet-id']).'">
		<input type="hidden" name="formcomment" value="'.(!empty($data['item-name']) ? esc_html($data['item-name']) : 'Fee').'">
		<input type="hidden" name="short-dest" value="'.(!empty($data['item-name']) ? esc_html($data['item-name']) : 'Fee').'">
		<input type="hidden" name="label" value="'.esc_html($data['label-prefix']).':'.esc_html($data["record-id"]).'">
		<input type="hidden" name="quickpay-form" value="small">
		<input type="hidden" name="targets" value="'.(!empty($data['item-name']) ? esc_html($data['item-name']) : 'Fee').'">
		<input type="hidden" name="sum" value="'.esc_html(number_format($data['amount'], 2, '.', '')).'" data-type="number">
		<input type="hidden" name="need-fio" value="false">
		<input type="hidden" name="need-email" value="false"> 
		<input type="hidden" name="need-phone" value="false">
		<input type="hidden" name="need-address" value="false">
		<input type="hidden" name="paymentType" value="'.esc_html($data['payment-type']).'">
		<input type="hidden" name="successURL" value="'.(empty($data['success-url']) ? esc_html($_SERVER["HTTP_REFERER"]) : esc_html($data['success-url'])).'">
		<input type="submit" class="lepopup-pay" value="Submit">
	</form>';		
		$result = array('status' => 'OK', 'form' => $html, 'amount' => number_format($data['amount'], 2, '.', ''), 'currency' => $data["currency"], 'gateway-id' => $data['id']);
		return $result;
	}
	
	function front_init() {
		global $wpdb, $lepopup;
		$form_object = null;
		if (array_key_exists('lepopup-ipn', $_REQUEST) && $_REQUEST['lepopup-ipn'] == 'yandexmoney') {
			if (!array_key_exists('notification_type', $_REQUEST) || !array_key_exists('operation_id', $_REQUEST) || !array_key_exists('sha1_hash', $_REQUEST)) {
				http_response_code(400);
				exit;
			}
			$transaction_details = array();
			foreach ($_POST as $key => $value) {
				$transaction_details[$key] = stripslashes($value);
			}

			$item_id = stripslashes($_POST['label']);
			if (($pos = strrpos($item_id, ":")) !== false) $item_id = intval(substr($item_id, $pos+1));
			$payment_status = "Completed";
			$item_name = stripslashes($_POST['operation_id']);
			$transaction_type = stripslashes($_POST['notification_type']);
			$txn_id = stripslashes($_POST['operation_id']);
			$payer_id = (empty($_POST['operation_id']) ? esc_html__('Card holder', 'lepopup') : $_POST['operation_id']);
			$gross_total = stripslashes($_POST['withdraw_amount']);
			$mc_currency = 'RUB';
			$payer_name = esc_html__('Yandex.Money payer', 'lepopup');
			$sha1_hash = stripslashes($_POST['sha1_hash']);

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
							if (floatval($gross_total) < floatval($record_details["amount"]) || $mc_currency != $record_details["currency"]) $payment_status = "Error: invalid amount";
							else {
								$hash_data = stripslashes($_POST['notification_type']).'&'.stripslashes($_POST['operation_id']).'&'.stripslashes($_POST['amount']).'&'.stripslashes($_POST['currency']).'&'.stripslashes($_POST['datetime']).'&'.stripslashes($_POST['sender']).'&'.stripslashes($_POST['codepro']).'&'.$payment_gateway['data']['secret-word'].'&'.stripslashes($_POST['label']);
								if (sha1($hash_data) != $sha1_hash) $payment_status = "Error: invalid signature";
							}
						}
					}
				}
			}
			$sql = "INSERT INTO ".$wpdb->prefix."lepopup_transactions (record_id, provider, payer_name, payer_email, gross, currency, payment_status, transaction_type, txn_id, details, created, deleted ) VALUES (
				'".$item_id."',
				'yandexmoney',
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
		foreach($details as $key => $value) {
			$html .= '
			<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html($key).'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.esc_html($value).'</td></tr>';
		}
		$html .= '
		</table>';
		
		return $html;
	}
}
$lepopup_yandexmoney = new lepopup_yandexmoney_class();
?>