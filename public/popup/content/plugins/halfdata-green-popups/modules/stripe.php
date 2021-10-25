<?php
/* Stripe integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_stripe_class {
	var $default_parameters = array(
		"public-key" => "",
		"secret-key" => "",
		"webhook-secret" => "",
		"item-name" => "",
		"amount" => "",
		"currency" => "USD",
		"success-url" => "",
		"cancel-url" => ""
	);
	var $currency_list = array("AED", "AFN", "ALL", "AMD", "ANG", "AOA", "ARS", "AUD", "AWG", "AZN", "BAM", "BBD", "BDT", "BGN", "BIF", "BMD", "BND", "BOB", "BRL", "BSD", "BWP", "BZD", "CAD", "CDF", "CHF", "CLP", "CNY", "COP", "CRC", "CVE", "CZK", "DJF", "DKK", "DOP", "DZD", "EEK", "EGP", "ETB", "EUR", "FJD", "FKP", "GBP", "GEL", "GIP", "GMD", "GNF", "GTQ", "GYD", "HKD", "HNL", "HRK", "HTG", "HUF", "IDR", "ILS", "INR", "ISK", "JMD", "JPY", "KES", "KGS", "KHR", "KMF", "KRW", "KYD", "KZT", "LAK", "LBP", "LKR", "LRD", "LSL", "LTL", "LVL", "MAD", "MDL", "MGA", "MKD", "MNT", "MOP", "MRO", "MUR", "MVR", "MWK", "MXN", "MYR", "MZN", "NAD", "NGN", "NIO", "NOK", "NPR", "NZD", "PAB", "PEN", "PGK", "PHP", "PKR", "PLN", "PYG", "QAR", "RON", "RSD", "RUB", "RWF", "SAR", "SBD", "SCR", "SEK", "SGD", "SHP", "SLL", "SOS", "SRD", "STD", "SVC", "SZL", "THB", "TJS", "TOP", "TRY", "TTD", "TWD", "TZS", "UAH", "UGX", "USD", "UYU", "UZS", "VND", "VUV", "WST", "XAF", "XCD", "XOF", "XPF", "YER", "ZAR", "ZMW");
	var $no_100 = array("JPY");
	
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_payment_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-stripe-settings-html', array(&$this, "admin_settings_html"));
			add_filter('lepopup_payment_gateways_transaction_html_stripe', array(&$this, 'admin_details'), 10, 3);
		}
		add_filter('lepopup_payment_gateways_do_stripe', array(&$this, 'front_submit'), 10, 2);
		add_filter('lepopup_remote_parameters', array(&$this, 'remote_parameters'), 10, 1);
		add_action("init", array(&$this, "front_init"));
		add_action('lepopup_wp_enqueue_scripts', array(&$this, 'front_enqueue_scripts'), 99);
	}
	
	function providers($_providers) {
		if (!array_key_exists("stripe", $_providers)) $_providers["stripe"] = esc_html__('Stripe', 'lepopup');
		return $_providers;
	}

	function front_enqueue_scripts() {
		global $lepopup;
		wp_enqueue_script('stripe', 'https://js.stripe.com/v3/', array(), LEPOPUP_VERSION, true);
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
				'.sprintf(esc_html__('Important! Make sure that you created webhook with the following URL for event "checkout.session.completed" in your %sStripe Dashboard%s.', 'lepopup'), '<a href="https://dashboard.stripe.com/account/webhooks" target="_blank">', '</a>').'
				<input type="text" readonly="readonly" value="'.(defined('UAP_CORE') ? esc_html(admin_url('do.php').'?lepopup-ipn=stripe') : esc_html(get_bloginfo('url').'/?lepopup-ipn=stripe')).'" onclick="this.focus();this.select();" />
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Publishable Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter valid Publishable Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="public-key" value="'.esc_html($data['public-key']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find it on %sAPI Keys%s page.', 'lepopup'), '<a href="https://dashboard.stripe.com/account/apikeys" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Secret Key', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter valid Secret Key.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="secret-key" value="'.esc_html($data['secret-key']).'" />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find it on %sAPI Keys%s page.', 'lepopup'), '<a href="https://dashboard.stripe.com/account/apikeys" target="_blank">', '</a>').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Signing secret', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Please enter valid Signing secret for webhook that you created earlier.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="webhook-secret" value="'.esc_html($data['webhook-secret']).'" placeholder="whsec_..." />
					<label class="lepopup-integrations-description">'.sprintf(esc_html__('Find it on %sWebhooks%s page. Click webhook that you created earlier, and find "Signing secret" parameter.', 'lepopup'), '<a href="https://dashboard.stripe.com/account/webhooks" target="_blank">', '</a>').'</label>
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
		if (empty($data['public-key']) || empty($data['secret-key'])) return $_result;
		if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) return $_result;
		if (!class_exists("\Stripe\Stripe")) require_once(dirname(dirname(__FILE__)).'/libs/stripe/init.php');
		try {
			\Stripe\Stripe::setApiKey($data['secret-key']);
			if (in_array($data["currency"], $this->no_100)) $multiplier = 1;
			else $multiplier = 100;
			$stripe_session = \Stripe\Checkout\Session::create([
				'success_url' => (empty($data['success-url']) ? $_SERVER["HTTP_REFERER"] : $data['success-url']),
				'cancel_url' => (empty($data['cancel-url']) ? $_SERVER["HTTP_REFERER"] : $data['cancel-url']),
				'payment_method_types' => ['card'],
				'client_reference_id' => $data["record-id"].'-'.(defined('UAP_CORE') ? base64_encode(admin_url('do.php').'?lepopup-ipn=stripe') : base64_encode(get_bloginfo('url').'/?lepopup-ipn=stripe')),
				'line_items' => [
					[
						'amount' => intval($data['amount']*$multiplier),
						'currency' => $data["currency"],
						'name' => (!empty($data['item-name']) ? $data['item-name'] : 'Fee'),
						'quantity' => 1,
					],
				],
			], ['stripe_version' => '2019-03-14; checkout_sessions_beta=v1']);
			return array('status' => 'OK', 'stripe' => array('public-key' => $data['public-key'], 'session-id' => $stripe_session->id), 'amount' => number_format($data['amount'], 2, '.', ''), 'currency' => $data["currency"], 'gateway-id' => $data['id']);
		} catch(Exception $e) {
			$body = $e->getJsonBody();
			return array('status' => 'ERROR', 'message' => rtrim($body['error']['message'], '.').'.');
		}
	}
	
	function front_init() {
		global $wpdb, $lepopup;
		$form_object = null;
		if (array_key_exists('lepopup-ipn', $_REQUEST) && $_REQUEST['lepopup-ipn'] == 'stripe') {
			$payload = @file_get_contents('php://input');
			$post_data = json_decode($payload, true);
			if (empty($post_data) || !is_array($post_data) || !array_key_exists('type', $post_data) || $post_data['type'] != 'checkout.session.completed') {
				exit;
			}

			$id_parts = explode('-', $post_data['data']['object']['client_reference_id']);
			if (sizeof($id_parts) != 2) exit;
			$ref_url = (defined('UAP_CORE') ? base64_encode(admin_url('do.php').'?lepopup-ipn=stripe') : base64_encode(get_bloginfo('url').'/?lepopup-ipn=stripe'));
			if (empty($id_parts[1]) || $id_parts[1] != $ref_url) {
				exit;
			}

			$transaction_details = $post_data;

			$item_id = intval($id_parts[0]);
			$payment_status = "Completed";
			$transaction_type = implode(', ', $post_data['data']['object']['payment_method_types']);
			$txn_id = $post_data['data']['object']['payment_intent'];
			$payer_id = $post_data['data']['object']['customer_email'];

			if (array_key_exists('currency', $post_data['data']['object'])) $mc_currency = strtoupper($post_data['data']['object']['currency']);
			else $mc_currency = strtoupper($post_data['data']['object']['display_items'][0]['currency']);
			if (in_array($mc_currency, $this->no_100)) $multiplier = 1;
			else $multiplier = 100;
			if (array_key_exists('amount_total', $post_data['data']['object'])) $gross_total = number_format($post_data['data']['object']['amount_total']/$multiplier, 2, '.', '');
			else $gross_total = number_format($post_data['data']['object']['display_items'][0]['amount']/$multiplier, 2, '.', '');

			$payer_name = empty($post_data['data']['object']['customer']) ? 'Stripe Payer' : $post_data['data']['object']['customer'];
			$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

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
							require_once(dirname(dirname(__FILE__)).'/libs/stripe/init.php');
							try {
								\Stripe\Stripe::setApiKey($data['secret-key']);
								$event = \Stripe\Webhook::constructEvent($payload, $sig_header, $payment_gateway['data']['webhook-secret'], 12*3600);
							} catch(\UnexpectedValueException $e) {
								$payment_status = "Error: invalid payload";
							} catch(\Stripe\Error\SignatureVerification $e) {
								$payment_status = "Error: invalid signature";
							}							
						}
					}
				}
			}
			$sql = "INSERT INTO ".$wpdb->prefix."lepopup_transactions (record_id, provider, payer_name, payer_email, gross, currency, payment_status, transaction_type, txn_id, details, created, deleted ) VALUES (
				'".$item_id."',
				'stripe',
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
	function remote_parameters($_parameters) {
		global $wpdb, $lepopup;
		$_parameters['resources']['js'][] = 'https://js.stripe.com/v3/';
		return $_parameters;
	}
}
$lepopup_stripe = new lepopup_stripe_class();
?>