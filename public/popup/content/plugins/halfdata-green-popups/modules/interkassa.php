<?php
/* InterKassa integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_interkassa_class {
	var $default_parameters = array(
		"checkout-id" => "",
		"secret-key" => "",
		"sign-algorithm" => "md5",
		"item-name" => "",
		"amount" => "",
		"currency" => "USD",
		"success-url" => "",
		"cancel-url" => ""
	);
	var $currency_list = array("USD", "EUR", "RUB", "UAH");
	var $sign_algorithms = array(
		'md5' => 'MD5',
		'sha256' => 'SHA256'
	);
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_payment_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-interkassa-settings-html', array(&$this, "admin_settings_html"));
			add_filter('lepopup_payment_gateways_transaction_html_interkassa', array(&$this, 'admin_details'), 10, 3);
		}
		add_filter('lepopup_payment_gateways_do_interkassa', array(&$this, 'front_submit'), 10, 2);
		add_action("init", array(&$this, "front_init"));
	}
	
	function providers($_providers) {
		if (!array_key_exists("interkassa", $_providers)) $_providers["interkassa"] = esc_html__('InterKassa', 'lepopup');
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
					<label>'.esc_html__('Checkout ID', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter a valid InterKassa Checkout ID.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="checkout-id" value="'.esc_html($data['checkout-id']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find your Checkout ID on %sMy Checkouts%s page.', 'lepopup'), '<a href="https://www.interkassa.com/account/checkout/" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Sign Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter Sign Key. Find it on Checkout Settings page, Security tab in your InterKassa account.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="secret-key" value="'.esc_html($data['secret-key']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Sign Algorithm', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter Sign Algorithm. It must be the same as on Checkout Settings page, Security tab in your InterKassa account.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<select name="sign-algorithm">';
			foreach ($this->sign_algorithms as $key => $value) {
				$html .= '
						<option value="'.esc_html($key).'"'.($key == $data['sign-algorithm'] ? ' selected="selected"' : '').'>'.esc_html($value).'</option>';
			}
			$html .= '
					</select>
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
		if (empty($data['checkout-id']) || empty($data['secret-key'])) return $_result;
		if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) return $_result;
		$params = array();
		$params['ik_co_id'] = esc_html($data['checkout-id']);
		$params['ik_am'] = number_format($data['amount'], 2, '.', '');
		$params['ik_cur'] = esc_html($data["currency"]);
		$params['ik_pm_no'] = $data["record-id"].'-'.time();
		$params['ik_desc'] = (!empty($data['item-name']) ? esc_html($data['item-name']) : 'Fee');
		$params['ik_ia_u'] = defined('UAP_CORE') ? esc_html(admin_url('do.php').'?lepopup-ipn=interkassa') : esc_html(get_bloginfo('url').'/?lepopup-ipn=interkassa');
		$params['ik_ia_m'] = 'POST';
		$params['ik_suc_u'] = empty($data['success-url']) ? esc_html($_SERVER["HTTP_REFERER"]) : esc_html($data['success-url']);
		$params['ik_suc_m'] = 'GET';
		$params['ik_fal_u'] = empty($data['cancel-url']) ? esc_html($_SERVER["HTTP_REFERER"]) : esc_html($data['cancel-url']);
		$params['ik_fal_m'] = 'GET';
		ksort($params, SORT_STRING);
		$params_string = implode(':', $params).':'.$data['secret-key'];
		if ($data['sign-algorithm'] == 'md5') {
			$params['ik_sign'] = base64_encode(md5($params_string, true));
		} else if ($data['sign-algorithm'] == 'sha256') {
			$params['ik_sign'] = base64_encode(hash('sha256', $params_string, true));
		}
		$html = '
	<form action="https://sci.interkassa.com/" method="post" target="_top" style="display:none; !important;">';
					foreach($params as $key => $value) {
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
		if (array_key_exists('lepopup-ipn', $_REQUEST) && $_REQUEST['lepopup-ipn'] == 'interkassa') {
			if (!array_key_exists('ik_pm_no', $_REQUEST) || !array_key_exists('ik_co_prs_id', $_REQUEST)) die();
			$transaction_details = array();
			$sign_data = array();
			foreach ($_POST as $key => $value) {
				$transaction_details[$key] = stripslashes($value);
				if (strtolower(substr($key, 0, 3)) == 'ik_' && $key != 'ik_sign') $sign_data[$key] = $value;
			}
			$item_id = $_POST['ik_pm_no'];
			if (($pos = strpos($item_id, "-")) !== false) $item_id = intval(substr($item_id, 0, $pos));
			$item_name = stripslashes($_POST['ik_desc']);
			$payment_status = stripslashes($_POST['ik_inv_st']);
			$transaction_type = stripslashes($_POST['ik_pw_via']);
			$txn_id = stripslashes($_POST['ik_co_prs_id']);
			$seller_id = stripslashes($_POST['ik_co_id']);
			$payer_id = stripslashes($_POST['ik_x_email']);
			$gross_total = stripslashes($_POST['ik_am']);
			$mc_currency = stripslashes($_POST['ik_cur']);
			$payer_name = 'InterKassa payer';

			if ($payment_status == "success") {
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
							if (strtolower($seller_id) != strtolower($payment_gateway['data']['checkout-id'])) $payment_status = "Error: invalid recipient";
							if (floatval($gross_total) < floatval($record_details["amount"]) || $mc_currency != $record_details["currency"]) $payment_status = "Error: invalid amount";
							else {
								ksort($sign_data, SORT_STRING);
								array_push($sign_data, $payment_gateway['data']['secret-key']);
								$signString = implode(':', $sign_data);
								if ($payment_gateway['data']['sign-algorithm'] == 'md5') {
									$sign = base64_encode(md5($signString, true));
								} else if ($payment_gateway['data']['sign-algorithm'] == 'sha256') {
									$sign = base64_encode(hash('sha256', $signString, true));
								}
								if ($_POST['ik_sign'] != $sign) $payment_status = "Error: invalid signature";
							}
						}
					}
				}
			}

			$sql = "INSERT INTO ".$wpdb->prefix."lepopup_transactions (record_id, provider, payer_name, payer_email, gross, currency, payment_status, transaction_type, txn_id, details, created, deleted ) VALUES (
				'".$item_id."',
				'interkassa',
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
			
			if ($payment_status == "success") {
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
$lepopup_interkassa = new lepopup_interkassa_class();
?>