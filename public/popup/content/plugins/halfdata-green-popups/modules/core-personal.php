<?php
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_personal_data_class {
	function __construct() {
		global $lepopup;
		if (is_admin()) {
			add_filter('wp_privacy_personal_data_exporters', array(&$this, 'personal_data_exporters'), 2);
			add_filter('wp_privacy_personal_data_erasers', array(&$this, 'personal_data_erasers'), 2);
		}
	}

	function personal_data_exporters($_exporters) {
		$_exporters['ulp'] = array(
			'exporter_friendly_name' => esc_html__('Green Popups', 'lepopup'),
			'callback' => array(&$this, 'personal_data_exporter')
		);
		return $_exporters;
	}
	
	function personal_data_exporter($_email_address, $_page = 1) {
		global $wpdb, $lepopup;
		if (empty($_email_address)) {
			return array(
				'data' => array(),
				'done' => true
			);
		}
		$data_to_export = array();

		$forms = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_forms ORDER BY id DESC", ARRAY_A);
		$default_form_options = $lepopup->default_form_options();
		foreach ($forms as $form_details) {
			$form_options = json_decode($form_details['options'], true);
			if (!empty($form_options)) $form_options = array_merge($default_form_options, $form_options);
			else $form_options = $default_form_options;
			if (empty($form_options['personal-keys'])) continue;
			$like_body = array();
			foreach ((array)$form_options['personal-keys'] as $key) {
				$like_body[] = "fields LIKE '%".'"'.esc_sql($key).'":"'.esc_sql($_email_address).'"'."%'";
			}
			if (empty($like_body)) continue;
			$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_records WHERE form_id = '".esc_sql($form_details['id'])."' AND (".implode(' OR ', $like_body).")", ARRAY_A);
			foreach ($rows as $row) {
				$data = array(
					'group_id' => 'lepopup-form-'.$row['form_id'],
					'group_label' => esc_html__('Green Popups', 'lepopup').': '.$form_details['name'],
					'item_id' => 'lepopup-records-'.$row['id']
				);
				$data['data'][] = array('name' => esc_html__('Record ID', 'lepopup'), 'value' => $row['id']);
				$data['data'][] = array('name' => esc_html__('Form', 'lepopup'), 'value' => $form_details['name']);
				if (!empty($row['fields'])) $data['data'][] = array('name' => esc_html__('Raw Data', 'lepopup'), 'value' => $row['fields']);
				$data['data'][] = array('name' => esc_html__('Created', 'lepopup'), 'value' => $lepopup->unixtime_string($row['created']));
				if ($row['deleted'] != 0) $data['data'][] = array('name' => esc_html__('Deleted', 'lepopup'), 'value' => 'yes');
				$data_to_export[] = $data;
			}
			break;
		}
		
		$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_transactions WHERE payer_email = '".esc_sql($_email_address)."' OR payer_name = '".esc_sql($_email_address)."' ORDER BY created DESC", ARRAY_A);
		foreach ($rows as $row) {
			$data = array(
				'group_id' => 'lepopup-transactions',
				'group_label' => esc_html__('Green Popups: Transactions', 'lepopup'),
				'item_id' => 'lepopup-transactions-'.$row['id']
			);
			if (!empty($row['payer_name'])) $data['data'][] = array('name' => esc_html__('Payer name', 'lepopup'), 'value' => $row['payer_name']);
			if (!empty($row['payer_email'])) $data['data'][] = array('name' => esc_html__('Payer email', 'lepopup'), 'value' => $row['payer_email']);
			$data['data'][] = array('name' => esc_html__('Amount', 'lepopup'), 'value' => ($row['currency'] == 'BTC' ? number_format($row['gross'], 8, ".", "") : number_format($row['gross'], 2, ".", "")).' '.$row['currency']);
			if (!empty($row['payment_status'])) $data['data'][] = array('name' => esc_html__('Status', 'lepopup'), 'value' => $row['payment_status']);
			if (!empty($row['transaction_type'])) $data['data'][] = array('name' => esc_html__('Type', 'lepopup'), 'value' => $row['transaction_type']);
			if (!empty($row['details'])) $data['data'][] = array('name' => esc_html__('Details', 'lepopup'), 'value' => $row['details']);
			$data['data'][] = array('name' => esc_html__('Created', 'lepopup'), 'value' => $lepopup->unixtime_string($row['created']));
			if ($row['deleted'] != 0) $data['data'][] = array('name' => esc_html__('Deleted', 'lepopup'), 'value' => 'yes');
			$data_to_export[] = $data;
		}
		
		return array(
			'data' => $data_to_export,
			'done' => true
		);
	}
	
	function personal_data_erasers($_erasers) {
		$_erasers['lepopup'] = array(
			'eraser_friendly_name' => esc_html__('Green Popups', 'lepopup'),
			'callback' => array(&$this, 'personal_data_eraser')
		);
		return $_erasers;
	}

	function personal_data_eraser($_email_address, $_page = 1) {
		global $wpdb, $lepopup;
		if (empty($_email_address)) {
			return array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);
		}
		$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."lepopup_transactions WHERE payer_email = '".esc_sql($_email_address)."' OR payer_name = '".esc_sql($_email_address)."'", ARRAY_A);
		$total = $tmp["total"];
		$wpdb->query("DELETE FROM ".$wpdb->prefix."lepopup_transactions WHERE payer_email = '".esc_sql($_email_address)."' OR payer_name = '".esc_sql($_email_address)."'");
		
		$forms = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_forms ORDER BY id DESC", ARRAY_A);
		$default_form_options = $lepopup->default_form_options();
		foreach ($forms as $form_details) {
			$form_options = json_decode($form_details['options'], true);
			if (!empty($form_options)) $form_options = array_merge($default_form_options, $form_options);
			else $form_options = $default_form_options;
			if (empty($form_options['personal-keys'])) continue;
			$like_body = array();
			foreach ((array)$form_options['personal-keys'] as $key) {
				$like_body[] = "fields LIKE '%".'"'.esc_sql($key).'":"'.esc_sql($_email_address).'"'."%'";
			}
			if (empty($like_body)) continue;
			$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_records WHERE form_id = '".esc_sql($form_details['id'])."' AND (".implode(' OR ', $like_body).")", ARRAY_A);
			$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."lepopup_records WHERE form_id = '".esc_sql($form_details['id'])."' AND (".implode(' OR ', $like_body).")", ARRAY_A);
			$total += $tmp["total"];
			$wpdb->query("DELETE FROM ".$wpdb->prefix."lepopup_records WHERE form_id = '".esc_sql($form_details['id'])."' AND (".implode(' OR ', $like_body).")");
		}
		return array(
			'items_removed'  => $total,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}
}
?>