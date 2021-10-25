<?php
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_ajax_class {
	function __construct() {
		if (is_admin()) {
			add_action('wp_ajax_lepopup-settings-save', array(&$this, "admin_settings_save"));
			add_action('wp_ajax_lepopup-advanced-settings-save', array(&$this, "admin_advanced_settings_save"));
			add_action('wp_ajax_lepopup-cookies-reset', array(&$this, "admin_reset_cookie"));
			add_action('wp_ajax_lepopup-forms-status-toggle', array(&$this, "admin_forms_status_toggle"));
			add_action('wp_ajax_lepopup-forms-delete', array(&$this, "admin_forms_delete"));
			add_action('wp_ajax_lepopup-forms-duplicate', array(&$this, "admin_forms_duplicate"));
			add_action('wp_ajax_lepopup-stats-reset', array(&$this, "admin_stats_reset"));
			add_action('wp_ajax_lepopup-records-delete', array(&$this, "admin_records_delete"));
			add_action('wp_ajax_lepopup-bulk-records-delete', array(&$this, "admin_bulk_records_delete"));
			add_action('wp_ajax_lepopup-transactions-delete', array(&$this, "admin_transactions_delete"));
			add_action('wp_ajax_lepopup-bulk-transactions-delete', array(&$this, "admin_bulk_transactions_delete"));
			add_action('wp_ajax_lepopup-form-save', array(&$this, "admin_form_save"));
			add_action('wp_ajax_lepopup-record-details', array(&$this, "admin_record_details"));
			add_action('wp_ajax_lepopup-record-field-empty', array(&$this, "admin_field_empty"));
			add_action('wp_ajax_lepopup-record-field-remove', array(&$this, "admin_field_delete"));
			add_action('wp_ajax_lepopup-record-field-load-editor', array(&$this, "admin_field_load_editor"));
			add_action('wp_ajax_lepopup-record-field-save', array(&$this, "admin_field_save"));
			add_action('wp_ajax_lepopup-transaction-details', array(&$this, "admin_transaction_details"));
			add_action('wp_ajax_lepopup-stats-load', array(&$this, "admin_stats_load"));
			add_action('wp_ajax_lepopup-field-analytics-load', array(&$this, "admin_field_analytics_load"));
			add_action('wp_ajax_lepopup-form-using', array(&$this, "admin_form_using"));
			add_action('wp_ajax_lepopup-campaign-using', array(&$this, "admin_campaign_using"));
			add_action('wp_ajax_lepopup-campaign-properties', array(&$this, "admin_campaign_properties"));
			add_action('wp_ajax_lepopup-campaign-stats', array(&$this, "admin_campaign_stats"));
			add_action('wp_ajax_lepopup-campaign-save', array(&$this, "admin_campaign_save"));
			add_action('wp_ajax_lepopup-campaigns-status-toggle', array(&$this, "admin_campaigns_status_toggle"));
			add_action('wp_ajax_lepopup-campaigns-delete', array(&$this, "admin_campaigns_delete"));
			add_action('wp_ajax_lepopup-campaigns-stats-reset', array(&$this, "admin_campaigns_stats_reset"));
			add_action('wp_ajax_lepopup-migrate', array(&$this, "admin_migrate"));
			if (!defined('UAP_CORE')) {
				add_action('wp_ajax_lepopup-target-properties', array(&$this, "admin_target_properties"));
				add_action('wp_ajax_lepopup-target-taxonomies', array(&$this, "admin_target_taxonomies"));
				add_action('wp_ajax_lepopup-target-posts', array(&$this, "admin_target_posts"));
				add_action('wp_ajax_lepopup-target-save', array(&$this, "admin_target_save"));
				add_action('wp_ajax_lepopup-targets-save-list', array(&$this, "admin_targets_save_list"));
			}

			add_action('wp_ajax_lepopup-async-init', array(&$this, "front_async_init"));
			add_action('wp_ajax_nopriv_lepopup-async-init', array(&$this, "front_async_init"));
			add_action('wp_ajax_lepopup-remote-init', array(&$this, "front_remote_init"));
			add_action('wp_ajax_nopriv_lepopup-remote-init', array(&$this, "front_remote_init"));
			add_action('wp_ajax_lepopup-upload', array(&$this, "front_upload"));
			add_action('wp_ajax_nopriv_lepopup-upload', array(&$this, "front_upload"));
			add_action('wp_ajax_lepopup-upload-progress', array(&$this, "front_upload_progress"));
			add_action('wp_ajax_nopriv_lepopup-upload-progress', array(&$this, "front_upload_progress"));
			add_action('wp_ajax_lepopup-upload-delete', array(&$this, "front_upload_delete"));
			add_action('wp_ajax_nopriv_lepopup-upload-delete', array(&$this, "front_upload_delete"));
			add_action('wp_ajax_lepopup-front-submit', array(&$this, "front_submit"));
			add_action('wp_ajax_nopriv_lepopup-front-submit', array(&$this, "front_submit"));
			add_action('wp_ajax_lepopup-front-next', array(&$this, "front_submit"));
			add_action('wp_ajax_nopriv_lepopup-front-next', array(&$this, "front_submit"));
			add_action('wp_ajax_lepopup-front-add-impression', array(&$this, "front_add_impression"));
			add_action('wp_ajax_nopriv_lepopup-front-add-impression', array(&$this, "front_add_impression"));
			add_action('wp_ajax_lepopup-front-popup-load', array(&$this, "front_popup_load"));
			add_action('wp_ajax_nopriv_lepopup-front-popup-load', array(&$this, "front_popup_load"));
			/* Personal Data - 2020-12-09 - begin */
			include_once(dirname(__FILE__).'/core-personal.php');
			$lepopup_personal = new lepopup_personal_data_class();
			/* Personal Data - 2020-12-09 - end */
		}
	}

	function admin_reset_cookie() {
		global $lepopup;
		if (current_user_can('manage_options')) {
			$lepopup->options["cookie-value"] = time();
			$lepopup->update_options();
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['message'] = __('Cookies successfully reset.', 'lepopup');
			echo json_encode($return_object);
		}
		exit;
	}

	function admin_settings_save() {
		global $wpdb, $lepopup;
		if ($lepopup->demo_mode) {
			echo json_encode(array('status' => 'ERROR', 'message' => esc_html__('This operation disabled in DEMO mode.', 'lepopup')));
			exit;
		}
		if (current_user_can('manage_options')) {
			$lepopup->populate_options();

			$errors = array();
			
			if ($lepopup->options['fa-enable'] == 'on') {
				if ($lepopup->options['fa-solid-enable'] != 'on' && $lepopup->options['fa-regular-enable'] != 'on' && $lepopup->options['fa-brands-enable'] != 'on') $errors[] = esc_html__('Select at least one Font Awesome Icon pack.', 'lepopup');
			}
			if (empty($lepopup->options['from-email']) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $lepopup->options['from-email'])) $errors[] = esc_html__('Invalid Sender email.', 'lepopup');
			
			$errors = apply_filters('lepopup_options_check', $errors);
			
			if (!empty($errors)) {
				$return_object = array();
				$return_object['status'] = 'ERROR';
				$return_object['message'] = esc_html__('Attention! Please correct the errors below and try again.', 'lepopup').'<br />'.implode('<br />', $errors);
				echo json_encode($return_object);
				exit;
			}
			$lepopup->update_options();
			do_action('lepopup_options_update');
			
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['message'] = esc_html__('Settings successfully saved.', 'lepopup');
			echo json_encode($return_object);
		}
		exit;
	}
	
	function admin_advanced_settings_save() {
		global $wpdb, $lepopup;
		if ($lepopup->demo_mode) {
			echo json_encode(array('status' => 'ERROR', 'message' => esc_html__('This operation disabled in DEMO mode.', 'lepopup')));
			exit;
		}
		if (current_user_can('manage_options')) {
			$lepopup->populate_advanced_options();

			$errors = array();
			if (!empty($errors)) {
				$return_object = array();
				$return_object['status'] = 'ERROR';
				$return_object['message'] = esc_html__('Attention! Please correct the errors below and try again.', 'lepopup').'<ul><li>'.implode('</li><li>', $errors).'</li></ul>';
				echo json_encode($return_object);
				exit;
			}
			$lepopup->update_advanced_options();
			
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['message'] = esc_html__('Settings successfully saved.', 'lepopup');
			echo json_encode($return_object);
		}
		exit;
	}
	
	function admin_forms_status_toggle() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$form_id = null;
			if (array_key_exists('form-id', $_REQUEST)) {
				$form_id = intval($_REQUEST['form-id']);
				$form_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND id = '".esc_sql($form_id)."'", ARRAY_A);
				if (empty($form_details)) $form_id = null;
			}
			if (empty($form_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested form not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			if ($_REQUEST['form-status'] == 'active') {
				$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_forms SET active = '0' WHERE deleted = '0' AND id = '".esc_sql($form_id)."'");
				$return_data = array(
					'status' => 'OK',
					'message' => esc_html__('The popup successfully deactivated.', 'lepopup'),
					'form_action' => esc_html__('Activate', 'lepopup'),
					'form_action_doing' => esc_html__('Activating...', 'lepopup'),
					'form_status' => 'inactive',
					'form_status_label' => esc_html__('No', 'lepopup')
				);
			} else {
				$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_forms SET active = '1' WHERE deleted = '0' AND id = '".esc_sql($form_id)."'");
				$return_data = array(
					'status' => 'OK',
					'message' => esc_html__('The popup successfully activated.', 'lepopup'),
					'form_action' => esc_html__('Deactivate', 'lepopup'),
					'form_action_doing' => esc_html__('Deactivating...', 'lepopup'),
					'form_status' => 'active',
					'form_status_label' => esc_html__('Yes', 'lepopup')
				);
			}
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}
	
	function admin_forms_delete() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$form_id = null;
			if (array_key_exists('form-id', $_REQUEST)) {
				$form_id = intval($_REQUEST['form-id']);
				$form_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND id = '".$form_id."'", ARRAY_A);
				if (empty($form_details)) $form_id = null;
			}
			if (empty($form_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested form not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_forms SET deleted = '1' WHERE deleted = '0' AND id = '".esc_sql($form_id)."'");
			$return_data = array(
				'status' => 'OK',
				'message' => esc_html__('The popup successfully deleted.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_forms_duplicate() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$form_id = null;
			if (array_key_exists('form-id', $_REQUEST)) {
				$form_id = intval($_REQUEST['form-id']);
				$form_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND id = '".esc_sql($form_id)."'", ARRAY_A);
				if (empty($form_details)) $form_id = null;
			}
			if (empty($form_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested form not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}

			$base = $form_details['slug'];
			$pos = strrpos($form_details['slug'], '-');
			if ($pos !== false) {
				$last_part = substr($form_details['slug'], $pos+1);
				if (strlen($last_part) == 0 || ctype_digit($last_part)) $base = substr($form_details['slug'], 0, $pos);
			}
			$base .= '-';
			$rows = $wpdb->get_results("SELECT slug FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND slug LIKE '".esc_sql($wpdb->esc_like($base))."%'", ARRAY_A);
			$suffix = 2;
			foreach ($rows as $row) {
				$slug_suffix = str_replace($base, '', $row['slug']);
				if (ctype_digit($slug_suffix) && $slug_suffix >= $suffix) $suffix = $slug_suffix+1;
			}
			$slug = $base.$suffix;
			
			$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_forms (name,slug,options,pages,elements,active,created,modified,deleted) VALUES (
				'".esc_sql($form_details['name'])."','".esc_sql($slug)."','".esc_sql($form_details['options'])."','".esc_sql($form_details['pages'])."','".esc_sql($form_details['elements'])."','".esc_sql($form_details['active'])."','".esc_sql(time())."','".esc_sql(time())."','0')");

			$return_data = array(
				'status' => 'OK',
				'message' => esc_html__('The popup successfully duplicated.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_stats_reset() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$form_id = null;
			if (array_key_exists('form-id', $_REQUEST)) {
				$form_id = intval($_REQUEST['form-id']);
				$form_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND id = '".esc_sql($form_id)."'", ARRAY_A);
				if (empty($form_details)) $form_id = null;
			}
			if (empty($form_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested form not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_stats SET impressions = '0', submits = '0', confirmed = '0', payments = '0' WHERE form_id = '".esc_sql($form_id)."'");

			$return_data = array(
				'status' => 'OK',
				'message' => esc_html__('The popup statistics successfully reset.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_form_save() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			
			$form_id = null;
			$form_options = null;
			$form_details = array();
			if (array_key_exists('form-id', $_REQUEST)) {
				$form_id = intval($_REQUEST['form-id']);
				$form_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND id = '".esc_sql($form_id)."'", ARRAY_A);
				if (empty($form_details)) $form_id = null;
			}
			$form_slug = 'popup-'.date("Y-m-d-h-i-s");
			if (array_key_exists('form-slug', $_REQUEST)) {
				$form_slug_raw = trim($_REQUEST['form-slug']);
				$form_slug = preg_replace('/[^a-zA-Z0-9-]/', '', $form_slug_raw);
				$form_slug = trim($form_slug, "-");
				if (strlen($form_slug) == 0 || $form_slug != $form_slug_raw) {
					$return_data = array(
						'status' => 'ERROR',
						'message' => esc_html__('Popup slug must be an alpna-numeric string with hyphens.', 'lepopup')
					);
					if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
					else echo json_encode($return_data);
					exit;
				} else if (in_array($form_slug, array('same', 'default'))) {
					$return_data = array(
						'status' => 'ERROR',
						'message' => esc_html__('This slug is reserved for internal use.', 'lepopup')
					);
					if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
					else echo json_encode($return_data);
					exit;
				}
				$total = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND slug = '".esc_sql($form_slug)."'".(empty($form_id) ? "" : " AND id != '".esc_sql($form_id)."'"), ARRAY_A);
				if (!empty($total) && $total['total'] > 0) {
					$return_data = array(
						'status' => 'ERROR',
						'message' => sprintf(esc_html__('Popup with slug "%s" already exists.', 'lepopup'), $form_slug)
					);
					if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
					else echo json_encode($return_data);
					exit;
				}
				$total = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."lepopup_campaigns WHERE deleted = '0' AND slug = '".esc_sql($form_slug)."'", ARRAY_A);
				if (!empty($total) && $total['total'] > 0) {
					$return_data = array(
						'status' => 'ERROR',
						'message' => sprintf(esc_html__('Campaign with slug "%s" already exists.', 'lepopup'), $form_slug)
					);
					if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
					else echo json_encode($return_data);
					exit;
				}
			}
			
			$default_form_options = $lepopup->default_form_options();
			$form_options = $default_form_options;
			if (array_key_exists('form-options', $_REQUEST)) {
				$form_options_new = json_decode(base64_decode(trim(stripslashes($_REQUEST['form-options']))), true);
				if (is_array($form_options_new) && !empty($form_options_new)) $form_options = array_merge($form_options, $form_options_new);
				else {
					$return_data = array(
						'status' => 'ERROR',
						'message' => esc_html__('Popup Options sent incorrectly. Do not close this page and contact Green Forms author.', 'lepopup')
					);
					if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
					else echo json_encode($return_data);
					exit;
				}
			} else {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Popup Options sent incorrectly. Do not close this page and contact Green Forms author.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			if (empty($form_options['name'])) $form_options['name'] = esc_html__('Untitled', 'lepopup');

			$form_pages = array();
			$default_page_options = $lepopup->default_form_options("page");
			$default_page_confirmation_options = $lepopup->default_form_options("page-confirmation");
			if (array_key_exists('form-pages', $_REQUEST) && is_array($_REQUEST['form-pages'])) {
				foreach($_REQUEST['form-pages'] as $encoded_page) {
					$page_options = json_decode(base64_decode(trim(stripslashes($encoded_page))), true);
					if (is_array($page_options)) {
						if (!array_key_exists('type', $page_options)) $page_options['type'] = 'page';
						if ($page_options['type'] == 'page') $page_options = array_merge($default_page_options, $page_options);
						else $page_options = array_merge($default_page_confirmation_options, $page_options);
						$form_pages[] = $page_options;
					}
				}
			}
			if (empty($form_pages)) {
				$return_data = array(
						'status' => 'ERROR',
						'message' => esc_html__('Popup Pages sent incorrectly. Do not close this page and contact Green Forms author.', 'lepopup')
					);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			
			$form_elements = array();
			if (array_key_exists('form-elements', $_REQUEST) && is_array($_REQUEST['form-elements'])) {
				foreach($_REQUEST['form-elements'] as $encoded_element) {
					$element_options = json_decode(base64_decode(trim(stripslashes($encoded_element))), true);
					if (is_array($element_options) && array_key_exists('type', $element_options)) {
						if ($element_options['type'] == 'signature') $form_options['cross-domain'] = 'off';
						$default_element_options = $lepopup->default_form_options($element_options['type']);
						$element_options = array_merge($default_element_options, $element_options);
						$form_elements[] = json_encode($element_options);
					}
				}
			}
			if (empty($form_elements)) {
				$return_data = array(
						'status' => 'ERROR',
						'message' => esc_html__('Popup Elements are empty or sent incorrectly. Do not close this page and contact Green Forms author.', 'lepopup')
					);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			
			if (empty($form_id)) {
				$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_forms (name,slug,options,pages,elements,cache_time,active,created,modified,deleted) VALUES (
					'".esc_sql($form_options['name'])."','".esc_sql($form_slug)."','".esc_sql(json_encode($form_options))."','".esc_sql(json_encode($form_pages))."','".esc_sql(json_encode($form_elements))."','0','".esc_sql($form_options['active'] == 'on' ? 1 : 0)."','".esc_sql(time())."','".esc_sql(time())."','0')");
				$form_id = $wpdb->insert_id;
			} else {
				$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_forms SET 
					name = '".esc_sql($form_options['name'])."',
					slug = '".esc_sql($form_slug)."',
					options = '".esc_sql(json_encode($form_options))."',
					pages = '".esc_sql(json_encode($form_pages))."',
					elements = '".esc_sql(json_encode($form_elements))."',
					cache_time = '0',
					active = '".esc_sql($form_options['active'] == 'on' ? 1 : 0)."',
					modified = '".esc_sql(time())."'
					WHERE deleted = '0' AND id = '".esc_sql($form_id)."'");
			}
			$return_data = array(
				'status' => 'OK',
				'form_id' => $form_id,
				'form_name' => $form_options['name'],
				'message' => esc_html__('The popup successfully saved.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_records_delete() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$record_id = null;
			if (array_key_exists('record-id', $_REQUEST)) {
				$record_id = intval($_REQUEST['record-id']);
				$record_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_records WHERE deleted = '0' AND id = '".esc_sql($record_id)."'", ARRAY_A);
				if (empty($record_details)) $record_id = null;
			}
			if (empty($record_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested record not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_records SET deleted = '1' WHERE deleted = '0' AND id = '".esc_sql($record_details['id'])."'");
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_fieldvalues SET deleted = '1' WHERE deleted = '0' AND record_id = '".esc_sql($record_details['id'])."'");
			$lepopup->uploads_delete($record_details['id']);
			
			$return_data = array(
				'status' => 'OK',
				'message' => esc_html__('The record successfully deleted.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_bulk_records_delete() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$records = array();
			if (array_key_exists('records', $_REQUEST) && is_array($_REQUEST['records'])) {
				foreach ($_REQUEST['records'] as $record_id) {
					$records[] = intval($record_id);
				}
			}
			if (empty($records)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('No records selected.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_records SET deleted = '1' WHERE deleted = '0' AND id IN ('".implode("','", $records)."')");
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_fieldvalues SET deleted = '1' WHERE deleted = '0' AND record_id IN ('".implode("','", $records)."')");
			$lepopup->uploads_delete($records);
			
			$return_data = array(
				'status' => 'OK',
				'message' => esc_html__('Selected records successfully deleted.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_record_details() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$record_id = null;
			if (array_key_exists('record-id', $_REQUEST)) $record_id = intval($_REQUEST['record-id']);
			$return_data = $lepopup->log_record_details_html($record_id);
			
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}
	
	function admin_field_load_editor() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$record_id = null;
			if (array_key_exists('record-id', $_REQUEST)) {
				$record_id = intval($_REQUEST['record-id']);
				$record_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_records WHERE deleted = '0' AND id = '".esc_sql($record_id)."'", ARRAY_A);
				if (empty($record_details)) $record_id = null;
			}
			if (empty($record_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested record not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			$field_id = null;
			if (array_key_exists('field-id', $_REQUEST)) {
				$field_id = intval($_REQUEST['field-id']);
			}
			$fields = json_decode($record_details['fields'], true);
			if (empty($field_id) || empty($fields) || !array_key_exists($field_id, $fields)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested field not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}

			if (!class_exists("lepopup_form")) include_once(dirname(__FILE__).'/core-form.php');
			$form_object = new lepopup_form($record_details['form_id']);
			if (empty($form_object->id)) $form_id = null;
			else $form_id = $form_object->id;
			if (empty($form_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Form does not exist.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			$form_object->form_data = json_decode($record_details['fields'], true);
			$return_data = $form_object->get_field_editor($field_id, $fields[$field_id]);
			if ($return_data['status'] == 'OK') {
				$return_data['html'] .= '<div class="lepopup-record-field-editor-buttons"><a class="lepopup-admin-button" href="#" onclick="return lepopup_record_field_save(this);"><i class="fas fa-save"></i><label>'.esc_html__('Save', 'lepopup').'</label></a><a class="lepopup-admin-button lepopup-admin-button-gray" href="#" onclick="return lepopup_record_field_cancel_editor(this);"><i class="fas fa-times"></i><label>'.esc_html__('Cancel', 'lepopup').'</label></a></div>';
			}
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_field_save() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$record_id = null;
			if (array_key_exists('record-id', $_REQUEST)) {
				$record_id = intval($_REQUEST['record-id']);
				$record_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_records WHERE deleted = '0' AND id = '".esc_sql($record_id)."'", ARRAY_A);
				if (empty($record_details)) $record_id = null;
			}
			if (empty($record_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested record not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			$field_id = null;
			if (array_key_exists('field-id', $_REQUEST)) {
				$field_id = intval($_REQUEST['field-id']);
			}
			$fields = json_decode($record_details['fields'], true);
			if (empty($field_id) || empty($fields) || !array_key_exists($field_id, $fields)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested field not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			if (!array_key_exists('value', $_REQUEST)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('New value not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			$value = array();
			parse_str(base64_decode($_REQUEST['value']), $value);
			$fields[$field_id] = $value['value'];
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_records SET fields = '".esc_sql(json_encode($fields))."' WHERE deleted = '0' AND id = '".esc_sql($record_details['id'])."'");
			$wpdb->query("DELETE FROM ".$wpdb->prefix."lepopup_fieldvalues WHERE deleted = '0' AND record_id = '".esc_sql($record_details['id'])."' AND field_id = '".esc_sql($field_id)."'");
			$datestamp = date('Ymd', time()+3600*$lepopup->gmt_offset);
			if (is_array($value['value'])) {
				foreach($value['value'] as $option) {
					$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_fieldvalues (form_id, record_id, field_id, value, datestamp, deleted) VALUES (
						'".esc_sql($record_details['form_id'])."','".esc_sql($record_details['id'])."','".esc_sql($field_id)."','".esc_sql($option)."','".esc_sql($datestamp)."','0')");
				}
			} else {
				$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_fieldvalues (form_id, record_id, field_id, value, datestamp, deleted) VALUES (
					'".esc_sql($record_details['form_id'])."','".esc_sql($record_details['id'])."','".esc_sql($field_id)."','".esc_sql($value['value'])."','".esc_sql($datestamp)."','0')");
			}

			if (is_array($value['value'])) {
				foreach ($value['value'] as $key => $values_value) {
					$values_value = trim($values_value);
					if ($values_value == "") $value['value'][$key] = "-";
					else $value['value'][$key] = esc_html($values_value);
				}
				$html = implode("<br />", $value['value']);
			} else if ($value['value'] != "") {
				$value_strings = explode("\n", $value['value']);
				foreach ($value_strings as $key => $values_value) {
					$value_strings[$key] = esc_html(trim($values_value));
				}
				$html = implode("<br />", $value_strings);
			} else $html = "-";

			$return_data = array(
				'status' => 'OK',
				'html' => $html,
				'message' => esc_html__('The field value successfully saved.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);

		}
		exit;
	}
	
	function admin_field_empty() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$record_id = null;
			if (array_key_exists('record-id', $_REQUEST)) {
				$record_id = intval($_REQUEST['record-id']);
				$record_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_records WHERE deleted = '0' AND id = '".esc_sql($record_id)."'", ARRAY_A);
				if (empty($record_details)) $record_id = null;
			}
			if (empty($record_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested record not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			$field_id = null;
			if (array_key_exists('field-id', $_REQUEST)) {
				$field_id = intval($_REQUEST['field-id']);
			}
			$fields = json_decode($record_details['fields'], true);
			if (empty($field_id) || empty($fields) || !array_key_exists($field_id, $fields)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested field not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			$fields[$field_id] = "";
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_records SET fields = '".esc_sql(json_encode($fields))."' WHERE deleted = '0' AND id = '".esc_sql($record_details['id'])."'");
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_fieldvalues SET value = '' WHERE deleted = '0' AND record_id = '".esc_sql($record_details['id'])."' AND field_id = '".esc_sql($field_id)."'");
			$wpdb->query("DELETE t1 FROM ".$wpdb->prefix."lepopup_fieldvalues t1 INNER JOIN ".$wpdb->prefix."lepopup_fieldvalues t2 WHERE t1.id < t2.id AND t1.record_id = t2.record_id AND t1.field_id = t2.field_id AND t1.value = '' and t2.value = ''");
			$lepopup->uploads_delete($record_details['id'], $field_id);

			$return_data = array(
				'status' => 'OK',
				'message' => esc_html__('The field successfully emptied.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_field_delete() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$record_id = null;
			if (array_key_exists('record-id', $_REQUEST)) {
				$record_id = intval($_REQUEST['record-id']);
				$record_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_records WHERE deleted = '0' AND id = '".esc_sql($record_id)."'", ARRAY_A);
				if (empty($record_details)) $record_id = null;
			}
			if (empty($record_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested record not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			$field_id = null;
			if (array_key_exists('field-id', $_REQUEST)) {
				$field_id = intval($_REQUEST['field-id']);
			}
			$fields = json_decode($record_details['fields'], true);
			if (empty($field_id) || empty($fields) || !array_key_exists($field_id, $fields)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested field not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			unset($fields[$field_id]);
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_records SET fields = '".esc_sql(json_encode($fields))."' WHERE deleted = '0' AND id = '".esc_sql($record_details['id'])."'");
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_fieldvalues SET deleted = '1' WHERE deleted = '0' AND record_id = '".esc_sql($record_details['id'])."' AND field_id = '".esc_sql($field_id)."'");
			$lepopup->uploads_delete($record_details['id'], $field_id);

			$return_data = array(
				'status' => 'OK',
				'message' => esc_html__('The field successfully emptied.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_transactions_delete() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$record_id = null;
			if (array_key_exists('transaction-id', $_REQUEST)) {
				$record_id = intval($_REQUEST['transaction-id']);
				$record_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_transactions WHERE deleted = '0' AND id = '".esc_sql($record_id)."'", ARRAY_A);
				if (empty($record_details)) $record_id = null;
			}
			if (empty($record_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested transaction not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_transactions SET deleted = '1' WHERE deleted = '0' AND id = '".esc_sql($record_id)."'");
			$return_data = array(
				'status' => 'OK',
				'message' => esc_html__('The transaction successfully deleted.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_bulk_transactions_delete() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$records = array();
			if (array_key_exists('records', $_REQUEST) && is_array($_REQUEST['records'])) {
				foreach ($_REQUEST['records'] as $record_id) {
					$records[] = intval($record_id);
				}
			}
			if (empty($records)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('No transactions selected.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_transactions SET deleted = '1' WHERE deleted = '0' AND id IN ('".implode("','", $records)."')");
			
			$return_data = array(
				'status' => 'OK',
				'message' => esc_html__('Selected transactions successfully deleted.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_transaction_details() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$record_id = null;
			if (array_key_exists('transaction-id', $_REQUEST)) $record_id = intval($_REQUEST['transaction-id']);
			$return_data = $lepopup->transaction_details_html($record_id);
			
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_stats_load() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			
			if (!array_key_exists('start-date', $_REQUEST) || !array_key_exists('end-date', $_REQUEST) || !array_key_exists('form-id', $_REQUEST)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Invalid request.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			
			$form_id = null;
			if ($_REQUEST['form-id'] != 0) {
				$form_id = intval($_REQUEST['form-id']);
				$form_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND id = '".esc_sql($form_id)."'", ARRAY_A);
				if (empty($form_details)) $form_id = null;
			}
			
			try {
				$start_date = new DateTime($_REQUEST['start-date']);
				$end_date = new DateTime($_REQUEST['end-date']);
			} catch (Exception $e) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Invalid dates.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}

			if ($end_date->diff($start_date)->days > 366) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Date interval is too large.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			
			if ($start_date > $end_date) $output = $lepopup->stats_array($form_id, $end_date, $start_date);
			else $output = $lepopup->stats_array($form_id, $start_date, $end_date);
			
			$return_data = array(
				'status' => 'OK',
				'data' => $output
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_field_analytics_load() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			
			if (!array_key_exists('start-date', $_REQUEST) || !array_key_exists('end-date', $_REQUEST) || !array_key_exists('form-id', $_REQUEST)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Invalid request.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			
			$form_id = null;
			if ($_REQUEST['form-id'] != 0) {
				if (!class_exists("lepopup_form")) include_once(dirname(__FILE__).'/core-form.php');
				$form_object = new lepopup_form(intval($_REQUEST['form-id']));
				if (empty($form_object->id)) $form_id = null;
				else $form_id = $form_object->id;
			}
			if (empty($form_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Select existing form.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			if ($_REQUEST['period'] == 'on') {
				try {
					$start_date = new DateTime($_REQUEST['start-date']);
					$end_date = new DateTime($_REQUEST['end-date']);
				} catch (Exception $e) {
					$return_data = array(
						'status' => 'ERROR',
						'message' => esc_html__('Invalid dates.', 'lepopup')
					);
					if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
					else echo json_encode($return_data);
					exit;
				}
			} else {
				$start_date = new DateTime('2000-01-01');
				$end_date = new DateTime('2030-12-31');
			}

			if ($start_date > $end_date) $output = $form_object->field_analytics_array($end_date, $start_date);
			else $output = $form_object->field_analytics_array($start_date, $end_date);
			
			$return_data = array(
				'status' => 'OK',
				'data' => $output
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_form_using() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$form_id = null;
			if ($_REQUEST['item-id'] != 0) {
				$form_id = intval($_REQUEST['item-id']);
				$form_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND id = '".esc_sql($form_id)."'", ARRAY_A);
				if (empty($form_details)) $form_id = null;
			}
			if (empty($form_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested item not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			if (defined('UAP_CORE')) {
				$html = '
			<div class="lepopup-using-details">
				<table class="lepopup-using-table">
					<tr>
						<td colspan="2">
							<span>'.sprintf(esc_html__('Important! Make sure that you properly embedded script into your website, as it is said on %sHow To Use%s page.', 'lepopup'), '<a target="_blank" href="?page=lepopup-using">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnClick (standard)', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('Use the following URL with a link/button (href attribute):', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="#lepopup-'.esc_html($form_details['slug']).'" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnClick (JavaScript)', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('Add the following attribute to your HTML-element:', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="'.esc_html('onclick="lepopup_popup_open(\''.esc_html($form_details['slug']).'\'); return false;"').'" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('JavaScript', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('Use the following javascript function to open the popup:', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="lepopup_popup_open(\''.esc_html($form_details['slug']).'\');" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnLoad', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('To display the popup, when website loaded (OnLoad popup). Insert the following JavaScript-snippet at the end of body section of the page (below lepopup.js).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onload", {
    item:        "'.esc_html($form_details['slug']).'",
    item_mobile: "'.esc_html($form_details['slug']).'",
    mode:        "every-time",
    period:      24,
    delay:       0,
    close_delay: 0
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnScroll', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('To display the popup, when user scroll down the page (OnScroll popup). Insert the following JavaScript-snippet at the end of body section of the page (below lepopup.js).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onscroll", {
    item:        "'.esc_html($form_details['slug']).'",
    item_mobile: "'.esc_html($form_details['slug']).'",
    mode:        "every-time",
    period:      24,
    offset:      "50%"
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnExit', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('To display the popup, when user moves mouse cursor to top edge of browser window, assuming that he/she is going to leave the page (OnExit popup). Insert the following JavaScript-snippet at the end of body section of the page (below lepopup.js).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onexit", {
    item:        "'.esc_html($form_details['slug']).'",
    mode:        "every-time",
    period:      24
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnInactivity', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('To display the popup, when user does nothing on website for certain period of time (OnInactivity popup). Insert the following JavaScript-snippet at the end of body section of the page (below lepopup.js).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onidle", {
    item:        "'.esc_html($form_details['slug']).'",
    item_mobile: "'.esc_html($form_details['slug']).'",
    mode:        "every-time",
    period:      24,
    delay:       30
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnAdBlockDetected', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('To display the popup, when AdBlock or similar software detected (OnAdBlockDetected popup). Insert the following JavaScript-snippet at the end of body section of the page (below lepopup.js).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onadb", {
    item:        "'.esc_html($form_details['slug']).'",
    item_mobile: "'.esc_html($form_details['slug']).'",
    mode:        "every-time",
    period:      24
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('Inline', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('Use the following HTML-snippet to embed the popup as inline object.', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="&lt;div class=\'lepopup-inline\' data-slug=\''.esc_html($form_details['slug']).'\'&gt;&lt;/div&gt;" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('Link locker (manual)', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('The alternate way to lock links is to construct locking URL manually. Use this method if your link is located in area which does not support shortcodes.', 'lepopup').'</span>
							<input type="text" value="" placeholder="'.esc_html__('Paste original URL here...', 'lepopup').'" data-slug="'.esc_html($form_details['slug']).'" oninput="jQuery(\'#lepopup-locking-url\').val(\'#lepopup-\'+jQuery(this).attr(\'data-slug\')+\':\'+lepopup_encode64(jQuery(this).val()));" />
							<span>'.esc_html__('Locking URL.', 'lepopup').'</span>
							<input type="text" id="lepopup-locking-url" readonly="readonly" value="" placeholder="..." onclick="this.focus();this.select();" />
						</td>
					</tr>
				</table>
			</div>';
			} else {
				$html = '
			<div class="lepopup-using-details">
				<table class="lepopup-using-table">
					<tr>
						<th>'.esc_html__('OnClick (standard)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('Use the following URL with a link/button (href attribute):', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="#lepopup-'.esc_html($form_details['slug']).'" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnClick (JavaScript)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('Add the following attribute to your HTML-element:', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="'.esc_html('onclick="lepopup_popup_open(\''.esc_html($form_details['slug']).'\'); return false;"').'" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('JavaScript', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('Use the following javascript function to open the popup:', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="lepopup_popup_open(\''.esc_html($form_details['slug']).'\');" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnLoad (standard)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.sprintf(esc_html__('To display the popup, when website loaded (OnLoad popup), %screate OnLoad target%s and make it active (drag and drop from Passive area to Active area). While creating target, you can configure how and where the popup must be displayed.', 'lepopup'), '<a href="'.admin_url('admin.php').'?page=lepopup-targeting">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnLoad (JavaScript)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('The alternate way to display the popup, when website loaded (OnLoad popup). Insert the following JavaScript-snippet into body section of the page (between tags <body> and </body>).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onload", {
    item:        "'.esc_html($form_details['slug']).'",
    item_mobile: "'.esc_html($form_details['slug']).'",
    mode:        "every-time",
    period:      24,
    delay:       0,
    close_delay: 0
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnScroll (standard)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.sprintf(esc_html__('To display the popup, when user scroll down the page (OnScroll popup), %screate OnScroll target%s and make it active (drag and drop from Passive area to Active area). While creating target, you can configure how and where the popup must be displayed.', 'lepopup'), '<a href="'.admin_url('admin.php').'?page=lepopup-targeting&event=onscroll">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnScroll (JavaScript)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('The alternate way to display the popup, when user scroll down the page (OnScroll popup). Insert the following JavaScript-snippet into body section of the page (between tags <body> and </body>).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onscroll", {
    item:        "'.esc_html($form_details['slug']).'",
    item_mobile: "'.esc_html($form_details['slug']).'",
    mode:        "every-time",
    period:      24,
    offset:      "50%"
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnExit (standard)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.sprintf(esc_html__('To display the popup, when user moves mouse cursor to top edge of browser window, assuming that he/she is going to leave the page (OnExit popup), %screate OnExit target%s and make it active (drag and drop from Passive area to Active area). While creating target, you can configure how and where the popup must be displayed.', 'lepopup'), '<a href="'.admin_url('admin.php').'?page=lepopup-targeting&event=onexit">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnExit (JavaScript)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('The alternate way to display the popup, when user moves mouse cursor to top edge of browser window, assuming that he/she is going to leave the page (OnExit popup). Insert the following JavaScript-snippet into body section of the page (between tags <body> and </body>).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onexit", {
    item:        "'.esc_html($form_details['slug']).'",
    mode:        "every-time",
    period:      24
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnInactivity (standard)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.sprintf(esc_html__('To display the popup, when user does nothing on website for certain period of time (OnInactivity popup), %screate OnInactivity target%s and make it active (drag and drop from Passive area to Active area). While creating target, you can configure how and where the popup must be displayed.', 'lepopup'), '<a href="'.admin_url('admin.php').'?page=lepopup-targeting&event=onidle">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnInactivity (JavaScript)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('The alternate way to display the popup, when user does nothing on website for certain period of time (OnInactivity popup). Insert the following JavaScript-snippet into body section of the page (between tags <body> and </body>).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onidle", {
    item:        "'.esc_html($form_details['slug']).'",
    item_mobile: "'.esc_html($form_details['slug']).'",
    mode:        "every-time",
    period:      24,
    delay:       30
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnAdBlockDetected (standard)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.sprintf(esc_html__('To display the popup, when AdBlock or similar software detected (OnAdBlockDetected popup), %screate OnAdBlockDetected target%s and make it active (drag and drop from Passive area to Active area). While creating target, you can configure how and where the popup must be displayed.', 'lepopup'), '<a href="'.admin_url('admin.php').'?page=lepopup-targeting&event=onabd">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnAdBlockDetected (JavaScript)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('The alternate way to display the popup, when AdBlock or similar software detected (OnAdBlockDetected popup). Insert the following JavaScript-snippet into body section of the page (between tags <body> and </body>).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onadb", {
    item:        "'.esc_html($form_details['slug']).'",
    item_mobile: "'.esc_html($form_details['slug']).'",
    mode:        "every-time",
    period:      24
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('Inline (Gutenberg block)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('In case of using Gutenberg content editor you can add the popup as a standard Gutenberg Block. Find "Green Popups" under Widgets category.', 'lepopup').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('Inline (shortcode)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('Use the following shortcode to embed the popup as inline object.', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="[lepopup slug=\''.esc_html($form_details['slug']).'\']" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('Inline (PHP)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('Use the following PHP-code to embed the popup into theme files:', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="'.esc_html('<?php do_shortcode("[lepopup slug=\''.esc_html($form_details['slug']).'\']"); ?>').'" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('Inline (HTML)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('Use the following HTML-snippet to embed the popup as inline object. For local use this method works when "Async Init" mode activated on Advanced Settings page.', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="&lt;div class=\'lepopup-inline\' data-slug=\''.esc_html($form_details['slug']).'\'&gt;&lt;/div&gt;" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('ContentStart (inline)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.sprintf(esc_html__('Automatically insert the popup as an inline object at the beginning of posts/pages/products/etc. (ContentStart object), %screate ContentStart (inline) target%s and make it active (drag and drop from Passive area to Active area). While creating target, you can configure how and where the popup must be displayed.', 'lepopup'), '<a href="'.admin_url('admin.php').'?page=lepopup-targeting&event=inlinepostbegin">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('ContentEnd (inline)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.sprintf(esc_html__('Automatically insert the popup as an inline object at the end of posts/pages/products/etc. (ContentEnd object), %screate ContentEnd (inline) target%s and make it active (drag and drop from Passive area to Active area). While creating target, you can configure how and where the popup must be displayed.', 'lepopup'), '<a href="'.admin_url('admin.php').'?page=lepopup-targeting&event=inlinepostend">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('Widget', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('Go to Appearance >> Widgets and drag the Green Popups widget into the desired sidebar. You will be able to select this form from the dropdown options while configuring widget.', 'lepopup').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('Link locker (shortcode)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('You can lock access to certain links on your website. It means that when user clicks locked link, the popup appears. User must submit the popup form. After that link becomes available. You may have many different links locked by the same popup. Once the popup submitted all these links become available. Wrap your links (link is an <a>-tag, not URL) with shortcodes.', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="[lepopuplinklocker slug=\''.esc_html($form_details['slug']).'\'] ... [/lepopuplinklocker]" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('Link locker (manual)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('The alternate way to lock links is to construct locking URL manually. Use this method if your link is located in area which does not support shortcodes.', 'lepopup').'</span>
							<input type="text" value="" placeholder="'.esc_html__('Paste original URL here...', 'lepopup').'" data-slug="'.esc_html($form_details['slug']).'" oninput="jQuery(\'#lepopup-locking-url\').val(\'#lepopup-\'+jQuery(this).attr(\'data-slug\')+\':\'+lepopup_encode64(jQuery(this).val()));" />
							<span>'.esc_html__('Locking URL.', 'lepopup').'</span>
							<input type="text" id="lepopup-locking-url" readonly="readonly" value="" placeholder="..." onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('Remote use', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('Use the popup with any non-WordPress pages of the site or with 3rd party sites. How to do it?', 'lepopup').'</span>
							<ol>
								<li>
									<span>'.sprintf(esc_html__('Make sure that non-WordPress page has %sDOCTYPE%s. If not, add the following line as a first line of HTML-document:', 'lepopup'), '<code>', '</code>').'</span>
									<input type="text" readonly="readonly" value="'.esc_html('<!DOCTYPE html>').'" onclick="this.focus();this.select();" />
								</li>
								<li>
									<span>'.sprintf(esc_html__('Make sure that website loads jQuery version 1.9 or higher. If not, add the following line into %shead%s section of HTML-document:', 'lepopup'), '<code>', '</code>').'</span>
									<input type="text" readonly="readonly" value="'.esc_html('<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>').'" onclick="this.focus();this.select();" />
								</li>
								<li>
									<span>'.sprintf(esc_html__('Copy the following JS-snippet and paste it into HTML-document. You need paste it at the end of %sbody%s section (above closing %s</body>%s tag).', 'lepopup'), '<code>', '</code>', '<code>', '</code>').'</span>
									<input type="text" readonly="readonly" value="'.esc_html('<script id="lepopup-remote" src="'.$lepopup->plugins_url.'/js/lepopup'.($lepopup->advanced_options['minified-sources'] == 'on' ? '.min' : '').'.js?ver='.LEPOPUP_VERSION.'" data-handler="'.admin_url('admin-ajax.php').'"></script>').'" onclick="this.focus();this.select();" />
									<span>'.esc_html__('PS: You need do it one time only, even if you use several popups on the same page.', 'lepopup').'</span>
								</li>
								<li>
									<span>'.sprintf(esc_html__('Use any method marked by blue dot %s (see above).', 'lepopup'), '<span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span>').'</span>
								</li>
							</ol>
						</td>
					</tr>
				</table>
			</div>';
			}
			$return_data = array(
				'status' => 'OK',
				'html' => $html,
				'form_name' => esc_html($form_details['name'])
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_campaign_using() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$item_id = null;
			if ($_REQUEST['item-id'] != 0) {
				$item_id = intval($_REQUEST['item-id']);
				$campaign_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_campaigns WHERE deleted = '0' AND id = '".esc_sql($item_id)."'", ARRAY_A);
				if (empty($campaign_details)) $item_id = null;
			}
			if (empty($item_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested item not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			if (defined('UAP_CORE')) {
				$html = '
			<div class="lepopup-using-details">
				<table class="lepopup-using-table">
					<tr>
						<td colspan="2">
							<span>'.sprintf(esc_html__('Important! Make sure that you properly embedded script into your website, as it is said on %sHow To Use%s page.', 'lepopup'), '<a target="_blank" href="?page=lepopup-using">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnClick (standard)', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('Use the following URL with a link/button (href attribute):', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="#lepopup-'.esc_html($campaign_details['slug']).'" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnClick (JavaScript)', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('Add the following attribute to your HTML-element:', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="'.esc_html('onclick="lepopup_popup_open(\''.esc_html($campaign_details['slug']).'\'); return false;"').'" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('JavaScript', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('Use the following javascript function to open the popup:', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="lepopup_popup_open(\''.esc_html($campaign_details['slug']).'\');" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnLoad', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('To display the popup, when website loaded (OnLoad popup). Insert the following JavaScript-snippet at the end of body section of the page (below lepopup.js).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onload", {
    item:        "'.esc_html($campaign_details['slug']).'",
    item_mobile: "'.esc_html($campaign_details['slug']).'",
    mode:        "every-time",
    period:      24,
    delay:       0,
    close_delay: 0
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnScroll', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('To display the popup, when user scroll down the page (OnScroll popup). Insert the following JavaScript-snippet at the end of body section of the page (below lepopup.js).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onscroll", {
    item:        "'.esc_html($campaign_details['slug']).'",
    item_mobile: "'.esc_html($campaign_details['slug']).'",
    mode:        "every-time",
    period:      24,
    offset:      "50%"
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnExit', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('To display the popup, when user moves mouse cursor to top edge of browser window, assuming that he/she is going to leave the page (OnExit popup). Insert the following JavaScript-snippet at the end of body section of the page (below lepopup.js).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onexit", {
    item:        "'.esc_html($campaign_details['slug']).'",
    mode:        "every-time",
    period:      24
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnInactivity', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('To display the popup, when user does nothing on website for certain period of time (OnInactivity popup). Insert the following JavaScript-snippet at the end of body section of the page (below lepopup.js).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onidle", {
    item:        "'.esc_html($campaign_details['slug']).'",
    item_mobile: "'.esc_html($campaign_details['slug']).'",
    mode:        "every-time",
    period:      24,
    delay:       30
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnAdBlockDetected', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('To display the popup, when AdBlock or similar software detected (OnAdBlockDetected popup). Insert the following JavaScript-snippet at the end of body section of the page (below lepopup.js).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onadb", {
    item:        "'.esc_html($campaign_details['slug']).'",
    item_mobile: "'.esc_html($campaign_details['slug']).'",
    mode:        "every-time",
    period:      24
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('Link locker (manual)', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('The alternate way to lock links is to construct locking URL manually. Use this method if your link is located in area which does not support shortcodes.', 'lepopup').'</span>
							<input type="text" value="" placeholder="'.esc_html__('Paste original URL here...', 'lepopup').'" data-slug="'.esc_html($campaign_details['slug']).'" oninput="jQuery(\'#lepopup-locking-url\').val(\'#lepopup-\'+jQuery(this).attr(\'data-slug\')+\':\'+lepopup_encode64(jQuery(this).val()));" />
							<span>'.esc_html__('Locking URL.', 'lepopup').'</span>
							<input type="text" id="lepopup-locking-url" readonly="readonly" value="" placeholder="..." onclick="this.focus();this.select();" />
						</td>
					</tr>
				</table>
			</div>';
			} else {
				$html = '
			<div class="lepopup-using-details">
				<table class="lepopup-using-table">
					<tr>
						<th>'.esc_html__('OnClick (standard)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('Use the following URL with a link/button (href attribute):', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="#lepopup-'.esc_html($campaign_details['slug']).'" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnClick (JavaScript)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('Add the following attribute to your HTML-element:', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="'.esc_html('onclick="lepopup_popup_open(\''.esc_html($campaign_details['slug']).'\'); return false;"').'" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('JavaScript', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('Use the following javascript function to open the popup:', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="lepopup_popup_open(\''.esc_html($campaign_details['slug']).'\');" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnLoad (standard)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.sprintf(esc_html__('To display the popup, when website loaded (OnLoad popup), %screate OnLoad target%s and make it active (drag and drop from Passive area to Active area). While creating target, you can configure how and where the popup must be displayed.', 'lepopup'), '<a href="'.admin_url('admin.php').'?page=lepopup-targeting">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnLoad (JavaScript)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('The alternate way to display the popup, when website loaded (OnLoad popup). Insert the following JavaScript-snippet into body section of the page (between tags <body> and </body>).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onload", {
    item:        "'.esc_html($campaign_details['slug']).'",
    item_mobile: "'.esc_html($campaign_details['slug']).'",
    mode:        "every-time",
    period:      24,
    delay:       0,
    close_delay: 0
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnScroll (standard)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.sprintf(esc_html__('To display the popup, when user scroll down the page (OnScroll popup), %screate OnScroll target%s and make it active (drag and drop from Passive area to Active area). While creating target, you can configure how and where the popup must be displayed.', 'lepopup'), '<a href="'.admin_url('admin.php').'?page=lepopup-targeting&event=onscroll">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnScroll (JavaScript)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('The alternate way to display the popup, when user scroll down the page (OnScroll popup). Insert the following JavaScript-snippet into body section of the page (between tags <body> and </body>).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onscroll", {
    item:        "'.esc_html($campaign_details['slug']).'",
    item_mobile: "'.esc_html($campaign_details['slug']).'",
    mode:        "every-time",
    period:      24,
    offset:      "50%"
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnExit (standard)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.sprintf(esc_html__('To display the popup, when user moves mouse cursor to top edge of browser window, assuming that he/she is going to leave the page (OnExit popup), %screate OnExit target%s and make it active (drag and drop from Passive area to Active area). While creating target, you can configure how and where the popup must be displayed.', 'lepopup'), '<a href="'.admin_url('admin.php').'?page=lepopup-targeting&event=onexit">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnExit (JavaScript)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('The alternate way to display the popup, when user moves mouse cursor to top edge of browser window, assuming that he/she is going to leave the page (OnExit popup). Insert the following JavaScript-snippet into body section of the page (between tags <body> and </body>).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onexit", {
    item:        "'.esc_html($campaign_details['slug']).'",
    mode:        "every-time",
    period:      24
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnInactivity (standard)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.sprintf(esc_html__('To display the popup, when user does nothing on website for certain period of time (OnInactivity popup), %screate OnInactivity target%s and make it active (drag and drop from Passive area to Active area). While creating target, you can configure how and where the popup must be displayed.', 'lepopup'), '<a href="'.admin_url('admin.php').'?page=lepopup-targeting&event=onidle">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnInactivity (JavaScript)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('The alternate way to display the popup, when user does nothing on website for certain period of time (OnInactivity popup). Insert the following JavaScript-snippet into body section of the page (between tags <body> and </body>).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onidle", {
    item:        "'.esc_html($campaign_details['slug']).'",
    item_mobile: "'.esc_html($campaign_details['slug']).'",
    mode:        "every-time",
    period:      24,
    delay:       30
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnAdBlockDetected (standard)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.sprintf(esc_html__('To display the popup, when AdBlock or similar software detected (OnAdBlockDetected popup), %screate OnAdBlockDetected target%s and make it active (drag and drop from Passive area to Active area). While creating target, you can configure how and where the popup must be displayed.', 'lepopup'), '<a href="'.admin_url('admin.php').'?page=lepopup-targeting&event=onabd">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('OnAdBlockDetected (JavaScript)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('The alternate way to display the popup, when AdBlock or similar software detected (OnAdBlockDetected popup). Insert the following JavaScript-snippet into body section of the page (between tags <body> and </body>).', 'lepopup').'</span>
							<pre>&lt;script&gt;
lepopup_add_event("onadb", {
    item:        "'.esc_html($campaign_details['slug']).'",
    item_mobile: "'.esc_html($campaign_details['slug']).'",
    mode:        "every-time",
    period:      24
});
&lt;/script&gt;</pre>
							<span>'.sprintf(esc_html__('Please find a detailed description of parameters in %sdocumentation%s.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'</span>
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('Link locker (shortcode)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('You can lock access to certain links on your website. It means that when user clicks locked link, the popup appears. User must submit the popup form. After that link becomes available. You may have many different links locked by the same popup. Once the popup submitted all these links become available. Wrap your links (link is an <a>-tag, not URL) with shortcodes.', 'lepopup').'</span>
							<input type="text" readonly="readonly" value="[lepopuplinklocker slug=\''.esc_html($campaign_details['slug']).'\'] ... [/lepopuplinklocker]" onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('Link locker (manual)', 'lepopup').'<div class="lepopup-dots"><span class="lepopup-dot lepopup-dot-green" title="'.esc_html__('Available for local use', 'lepopup').'"></span><span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span></div></th>
						<td>
							<span>'.esc_html__('The alternate way to lock links is to construct locking URL manually. Use this method if your link is located in area which does not support shortcodes.', 'lepopup').'</span>
							<input type="text" value="" placeholder="'.esc_html__('Paste original URL here...', 'lepopup').'" data-slug="'.esc_html($campaign_details['slug']).'" oninput="jQuery(\'#lepopup-locking-url\').val(\'#lepopup-\'+jQuery(this).attr(\'data-slug\')+\':\'+lepopup_encode64(jQuery(this).val()));" />
							<span>'.esc_html__('Locking URL.', 'lepopup').'</span>
							<input type="text" id="lepopup-locking-url" readonly="readonly" value="" placeholder="..." onclick="this.focus();this.select();" />
						</td>
					</tr>
					<tr>
						<th>'.esc_html__('Remote use', 'lepopup').'</th>
						<td>
							<span>'.esc_html__('Use the popup with any non-WordPress pages of the site or with 3rd party sites. How to do it?', 'lepopup').'</span>
							<ol>
								<li>
									<span>'.sprintf(esc_html__('Make sure that non-WordPress page has %sDOCTYPE%s. If not, add the following line as a first line of HTML-document:', 'lepopup'), '<code>', '</code>').'</span>
									<input type="text" readonly="readonly" value="'.esc_html('<!DOCTYPE html>').'" onclick="this.focus();this.select();" />
								</li>
								<li>
									<span>'.sprintf(esc_html__('Make sure that website loads jQuery version 1.9 or higher. If not, add the following line into %shead%s section of HTML-document:', 'lepopup'), '<code>', '</code>').'</span>
									<input type="text" readonly="readonly" value="'.esc_html('<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>').'" onclick="this.focus();this.select();" />
								</li>
								<li>
									<span>'.sprintf(esc_html__('Copy the following JS-snippet and paste it into HTML-document. You need paste it at the end of %sbody%s section (above closing %s</body>%s tag).', 'lepopup'), '<code>', '</code>', '<code>', '</code>').'</span>
									<input type="text" readonly="readonly" value="'.esc_html('<script id="lepopup-remote" src="'.$lepopup->plugins_url.'/js/lepopup'.($lepopup->advanced_options['minified-sources'] == 'on' ? '.min' : '').'.js?ver='.LEPOPUP_VERSION.'" data-handler="'.admin_url('admin-ajax.php').'"></script>').'" onclick="this.focus();this.select();" />
									<span>'.esc_html__('PS: You need do it one time only, even if you use several popups on the same page.', 'lepopup').'</span>
								</li>
								<li>
									<span>'.sprintf(esc_html__('Use any method marked by blue dot %s (see above).', 'lepopup'), '<span class="lepopup-dot lepopup-dot-blue" title="'.esc_html__('Available for remote use', 'lepopup').'"></span>').'</span>
								</li>
							</ol>
						</td>
					</tr>
				</table>
			</div>';
			}
			$return_data = array(
				'status' => 'OK',
				'html' => $html,
				'form_name' => esc_html($campaign_details['name'])
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_campaigns_status_toggle() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$campaign_id = null;
			if (array_key_exists('campaign-id', $_REQUEST)) {
				$campaign_id = intval($_REQUEST['campaign-id']);
				$campaign_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_campaigns WHERE deleted = '0' AND id = '".esc_sql($campaign_id)."'", ARRAY_A);
				if (empty($campaign_details)) $campaign_id = null;
			}
			if (empty($campaign_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested campaign not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			if ($_REQUEST['campaign-status'] == 'active') {
				$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_campaigns SET active = '0' WHERE deleted = '0' AND id = '".esc_sql($campaign_id)."'");
				$return_data = array(
					'status' => 'OK',
					'message' => esc_html__('The campaign successfully deactivated.', 'lepopup'),
					'campaign_action' => esc_html__('Activate', 'lepopup'),
					'campaign_action_doing' => esc_html__('Activating...', 'lepopup'),
					'campaign_status' => 'inactive',
					'campaign_status_label' => esc_html__('No', 'lepopup')
				);
			} else {
				$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_campaigns SET active = '1' WHERE deleted = '0' AND id = '".esc_sql($campaign_id)."'");
				$return_data = array(
					'status' => 'OK',
					'message' => esc_html__('The campaign successfully activated.', 'lepopup'),
					'campaign_action' => esc_html__('Deactivate', 'lepopup'),
					'campaign_action_doing' => esc_html__('Deactivating...', 'lepopup'),
					'campaign_status' => 'active',
					'campaign_status_label' => esc_html__('Yes', 'lepopup')
				);
			}
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}
	
	function admin_campaigns_delete() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$campaign_id = null;
			if (array_key_exists('campaign-id', $_REQUEST)) {
				$campaign_id = intval($_REQUEST['campaign-id']);
				$campaign_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_campaigns WHERE deleted = '0' AND id = '".$campaign_id."'", ARRAY_A);
				if (empty($campaign_details)) $campaign_id = null;
			}
			if (empty($campaign_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested campaign not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_campaigns SET deleted = '1' WHERE deleted = '0' AND id = '".esc_sql($campaign_id)."'");
			$return_data = array(
				'status' => 'OK',
				'message' => esc_html__('The campaign successfully deleted.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_campaigns_stats_reset() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$campaign_id = null;
			if (array_key_exists('campaign-id', $_REQUEST)) {
				$campaign_id = intval($_REQUEST['campaign-id']);
				$campaign_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_campaigns WHERE deleted = '0' AND id = '".$campaign_id."'", ARRAY_A);
				if (empty($campaign_details)) $campaign_id = null;
			}
			if (empty($campaign_id)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested campaign not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_campaign_items SET impressions = '0', submits = '0' WHERE deleted = '0' AND campaign_id = '".esc_sql($campaign_id)."'");
			$return_data = array(
				'status' => 'OK',
				'message' => esc_html__('The stats successfully reset.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_campaign_properties() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$campaign_id = null;
			$campaign_details = null;
			if ($_REQUEST['campaign-id'] != 0) {
				$campaign_id = intval($_REQUEST['campaign-id']);
				$campaign_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_campaigns WHERE deleted = '0' AND id = '".esc_sql($campaign_id)."'", ARRAY_A);
				if (empty($campaign_details)) $campaign_id = null;
			}
			if (!empty($campaign_details)) $sql = "SELECT t1.*, t2.id AS item_id FROM ".$wpdb->prefix."lepopup_forms t1 LEFT JOIN ".$wpdb->prefix."lepopup_campaign_items t2 ON t2.form_id = t1.id AND t2.deleted = '0' AND t2.campaign_id = '".$campaign_details['id']."' WHERE t1.deleted = '0' ORDER BY t1.created DESC";
			else $sql = "SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' ORDER BY created DESC";
			$rows = $wpdb->get_results($sql, ARRAY_A);
			
			$html = '
		<form class="lepopup-campaign-properties-form" enctype="multipart/form-data" method="post" style="margin: 0px" action="'.admin_url('admin.php').'">
			<div class="lepopup-properties-item" data-id="name">
				<div class="lepopup-properties-label"><label>'.esc_html__('Name', 'lepopup').'</label></div>
				<div class="lepopup-properties-tooltip"><i class="fas fa-question-circle lepopup-tooltip-anchor tooltipstered"></i><div class="lepopup-tooltip-content">'.esc_html__('The name helps to identify the campaign.', 'lepopup').'</div></div>
				<div class="lepopup-properties-content"><input type="text" name="campaign-name" id="lepopup-campaign-name" value="'.(!empty($campaign_details) ? esc_html($campaign_details['name']) : esc_html__('Default Campaign', 'lepopup')).'" placeholder="..."></div>
			</div>
			<div class="lepopup-properties-item" data-id="slug">
				<div class="lepopup-properties-label"><label>'.esc_html__('Slug', 'lepopup').'</label></div>
				<div class="lepopup-properties-tooltip"><i class="fas fa-question-circle lepopup-tooltip-anchor tooltipstered"></i><div class="lepopup-tooltip-content">'.esc_html__('Unique slug/ID of the campaign.', 'lepopup').'</div></div>
				<div class="lepopup-properties-content"><input type="text" name="campaign-slug" id="lepopup-campaign-slug" value="'.(!empty($campaign_details) ? esc_html($campaign_details['slug']) : 'campaign-'.date("Y-m-d-H-i-s")).'" placeholder="..."></div>
			</div>
			<div class="lepopup-properties-item" data-id="popups">
				<div class="lepopup-properties-label"><label>'.esc_html__('Popups', 'lepopup').'</label></div>
				<div class="lepopup-properties-tooltip"><i class="fas fa-question-circle lepopup-tooltip-anchor tooltipstered"></i><div class="lepopup-tooltip-content">'.esc_html__('Select popups that you would like to include into campaign.', 'lepopup').'</div></div>
				<div class="lepopup-properties-content"><div class="lepopup-settings">';
			if (sizeof($rows) > 0) {
				foreach ($rows as $row) {
					$html .= '
						<div class="lepopup-settings-checkbox-container"><input type="checkbox" id="lepopup-popup-'.esc_html($row['id']).'" name="campaign-popups[]" value="'.esc_html($row['id']).'"'.(isset($row['item_id']) && intval($row['item_id']) > 0 ? ' checked="checked"' : '').'><label for="lepopup-popup-'.esc_html($row['id']).'"></label><label for="lepopup-popup-'.esc_html($row['id']).'">'.esc_html($row['name']).($row['active'] != 1 ? ' <span class="lepopup-badge lepopup-badge-danger">'.__('Inactive', 'lepopup').'</span>' : '').'</label></div>';
				}
			} else {
				$html .= esc_html__('Create at least one popup to start A/B Campaign.', 'lepopup');
			}
			$html .= '
				</div></div>
			</div>
			<input type="hidden" name="action" value="lepopup-campaign-save" />
			<input type="hidden" name="campaign-id" value="'.(empty($campaign_details) ? '0' : intval($campaign_details['id'])).'" />
		</form>';
			$return_data = array(
				'status' => 'OK',
				'html' => $html,
				'campaign_name' => esc_html($campaign_details['name'])
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_campaign_stats() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			$campaign_id = null;
			$campaign_details = null;
			if ($_REQUEST['campaign-id'] != 0) {
				$campaign_id = intval($_REQUEST['campaign-id']);
				$campaign_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_campaigns WHERE deleted = '0' AND id = '".esc_sql($campaign_id)."'", ARRAY_A);
				if (empty($campaign_details)) $campaign_id = null;
			}
			if (empty($campaign_details)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Requested campaign not found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			
			$sql = "SELECT t1.*, t2.name AS form_name, t2.slug AS form_slug FROM ".$wpdb->prefix."lepopup_campaign_items t1 LEFT JOIN ".$wpdb->prefix."lepopup_forms t2 ON t1.form_id = t2.id WHERE t1.deleted = '0' AND t1.campaign_id = '".$campaign_details['id']."' AND t2.deleted = '0' ORDER BY t2.created ASC";
			$rows = $wpdb->get_results($sql, ARRAY_A);
			$output = array();
			foreach ($rows as $row) {
				$output[$row['form_slug']] = array(
					'impressions' => $row['impressions'],
					'submits' => $row['submits'],
					'ctrs' => ($row['impressions'] == 0 ? 0 : number_format(100*$row['submits']/$row['impressions'], 2, '.', '')),
					'label' => $row['form_name']
				);
			}
			$return_data = array(
				'status' => 'OK',
				'data' => $output,
				'campaign_name' => esc_html($campaign_details['name'])
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_campaign_save() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$callback = '';
			if (isset($_REQUEST['callback'])) {
				header("Content-type: text/javascript");
				$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
			}
			
			$campaign_id = null;
			$campaign_details = array();
			if (array_key_exists('campaign-id', $_REQUEST)) {
				$campaign_id = intval($_REQUEST['campaign-id']);
				$campaign_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_campaigns WHERE deleted = '0' AND id = '".esc_sql($campaign_id)."'", ARRAY_A);
				if (empty($campaign_details)) $form_id = null;
			}
			$campaign_slug = 'campaign-'.date("Y-m-d-h-i-s");
			if (array_key_exists('campaign-slug', $_REQUEST)) {
				$campaign_slug_raw = trim($_REQUEST['campaign-slug']);
				$campaign_slug = preg_replace('/[^a-zA-Z0-9-]/', '', $campaign_slug_raw);
				$campaign_slug = trim($campaign_slug, "-");
				if (strlen($campaign_slug) == 0 || $campaign_slug != $campaign_slug_raw) {
					$return_data = array(
						'status' => 'ERROR',
						'message' => esc_html__('Campaign slug must be an alpna-numeric string with hyphens.', 'lepopup')
					);
					if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
					else echo json_encode($return_data);
					exit;
				} else if (in_array($campaign_slug, array('same', 'default'))) {
					$return_data = array(
						'status' => 'ERROR',
						'message' => esc_html__('This slug is reserved for internal use.', 'lepopup')
					);
					if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
					else echo json_encode($return_data);
					exit;
				}
				$total = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."lepopup_campaigns WHERE deleted = '0' AND slug = '".esc_sql($campaign_slug)."'".(empty($campaign_id) ? "" : " AND id != '".esc_sql($campaign_id)."'"), ARRAY_A);
				if (!empty($total) && $total['total'] > 0) {
					$return_data = array(
						'status' => 'ERROR',
						'message' => sprintf(esc_html__('Campaign with slug "%s" already exists.', 'lepopup'), $campaign_slug)
					);
					if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
					else echo json_encode($return_data);
					exit;
				}
				$total = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND slug = '".esc_sql($campaign_slug)."'", ARRAY_A);
				if (!empty($total) && $total['total'] > 0) {
					$return_data = array(
						'status' => 'ERROR',
						'message' => sprintf(esc_html__('Popup with slug "%s" already exists.', 'lepopup'), $campaign_slug)
					);
					if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
					else echo json_encode($return_data);
					exit;
				}
			}
			if (array_key_exists('campaign-name', $_REQUEST)) {
				$campaign_name = trim($_REQUEST['campaign-name']);
				if (empty($campaign_name)) $campaign_name = esc_html__('Untitled Campaign', 'lepopup');
			} else $campaign_name = esc_html__('Untitled Campaign', 'lepopup');
			
			if (!array_key_exists('campaign-popups', $_REQUEST) || !is_array($_REQUEST['campaign-popups']) || empty($_REQUEST['campaign-popups'])) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('Select at least one popup.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			
			if (empty($campaign_details)) {
				$sql = "INSERT INTO ".$wpdb->prefix."lepopup_campaigns (
					name, slug, options, active, created, deleted) VALUES (
					'".esc_sql($campaign_name)."', '".esc_sql($campaign_slug)."', '', '1', '".time()."', '0')";
				$wpdb->query($sql);
				$campaign_id = $wpdb->insert_id;
				foreach ($_REQUEST['campaign-popups'] as $form_id) {
					$sql = "INSERT INTO ".$wpdb->prefix."lepopup_campaign_items (
						campaign_id, form_id, impressions, submits, created, deleted) VALUES (
						'".intval($campaign_id)."',
						'".intval($form_id)."',
						'0', '0', '".time()."', '0')";
					$wpdb->query($sql);
				}
			} else {
				$campaign_id = $campaign_details['id'];
				$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_campaigns SET name = '".esc_sql($campaign_name)."', slug = '".esc_sql($campaign_slug)."' WHERE id = '".$campaign_details['id']."'");
				$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_campaign_items SET deleted = '1' WHERE campaign_id = '".$campaign_details['id']."'");
				foreach ($_REQUEST['campaign-popups'] as $form_id) {
					$item_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_campaign_items WHERE campaign_id = '".$campaign_details['id']."' AND form_id = '".$form_id."'", ARRAY_A);
					if (!empty($item_details)) {
						$sql = "UPDATE ".$wpdb->prefix."lepopup_campaign_items SET deleted = '0' WHERE id = '".$item_details['id']."'";
						$wpdb->query($sql);
					} else {
						$sql = "INSERT INTO ".$wpdb->prefix."lepopup_campaign_items (
							campaign_id, form_id, impressions, submits, created, deleted) VALUES (
							'".intval($campaign_details['id'])."',
							'".intval($form_id)."',
							'0', '0', '".time()."', '0')";
						$wpdb->query($sql);
					}
				}
			}
			$return_data = array(
				'status' => 'OK',
				'message' => esc_html__('The campaign successfully saved.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
		}
		exit;
	}

	function admin_target_properties() {
		global $wpdb;
		include_once(dirname(__FILE__).'/core-targeting.php');
		$targeting = new lepopup_class_targeting();
		$targeting->admin_target_properties();
		exit;
	}
	function admin_target_taxonomies() {
		global $wpdb;
		include_once(dirname(__FILE__).'/core-targeting.php');
		$targeting = new lepopup_class_targeting();
		$targeting->admin_target_taxonomies();
		exit;
	}
	function admin_target_posts() {
		global $wpdb;
		include_once(dirname(__FILE__).'/core-targeting.php');
		$targeting = new lepopup_class_targeting();
		$targeting->admin_target_posts();
		exit;
	}
	function admin_target_save() {
		global $wpdb;
		include_once(dirname(__FILE__).'/core-targeting.php');
		$targeting = new lepopup_class_targeting();
		$targeting->admin_save();
		exit;
	}
	function admin_targets_save_list() {
		global $wpdb;
		include_once(dirname(__FILE__).'/core-targeting.php');
		$targeting = new lepopup_class_targeting();
		$targeting->admin_save_list();
		exit;
	}

	function admin_migrate() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options')) {
			include_once(dirname(__FILE__).'/core-legacy.php');
			$legacy = new lepopup_legacy_class();
			$legacy->migrate();
		}
		exit;
	}

	function front_async_init() {
		global $lepopup, $wpdb;
		$callback = '';
		if (isset($_REQUEST['callback'])) {
			header("Content-type: text/javascript");
			$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
		}

		if (array_key_exists('content-id', $_REQUEST)) $raw_post_id = $_REQUEST['content-id'];
		else $raw_post_id = 0;
		
		if (array_key_exists('url', $_REQUEST)) $url = $_REQUEST['url'];
		else $url = '';
		
		include_once(dirname(__FILE__).'/core-targeting.php');
		$targeting = new lepopup_class_targeting();
		$event_data = $targeting->get_events_data($raw_post_id, $url);

		$preloaded_items = '';
		$forms = array();
		if ($lepopup->options['preload'] == 'on') {
			$forms = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND active = '1'", ARRAY_A);
		} else if ($lepopup->options['preload-event-popups'] == 'on') {
			if (is_array($event_data['event-items']) && !empty($event_data['event-items'])) {
				$form_ids = array();
				$sql = "SELECT t1.*, t2.slug FROM ".$wpdb->prefix."lepopup_campaign_items t1 LEFT JOIN ".$wpdb->prefix."lepopup_campaigns t2 ON t2.id = t1.campaign_id WHERE t1.deleted = '0' AND t2.deleted = '0' AND t2.active = '1' AND t2.slug IN ('".implode("','", $event_data['event-items'])."')";
				$rows = $wpdb->get_results($sql, ARRAY_A);
				foreach ($rows as $row) {
					$form_ids[] = $row['form_id'];
				}
				$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND active = '1' AND slug IN ('".implode("','", $event_data['event-items'])."')", ARRAY_A);
				foreach ($rows as $row) {
					$form_ids[] = $row['id'];
				}
				$form_ids = array_unique($form_ids);
				if (!empty($form_ids)) {
					$forms = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND active = '1' AND id IN ('".implode("','", $form_ids)."')", ARRAY_A);
				}
			}
		}
		$xd = false;
		if (array_key_exists('hostname', $_REQUEST)) {
			$server_name = str_replace('www.', '', strtolower($_SERVER['SERVER_NAME']));
			$http_host = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
			$hostname = str_replace('www.', '', strtolower($_REQUEST['hostname']));
			if ($hostname != $server_name && $hostname != $http_host) $xd = true;
			else {
				if (array_key_exists('HTTP_REFERER', $_SERVER) && !empty($_SERVER['HTTP_REFERER'])) {
					$ref_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
					$ref_host = str_replace('www.', '', strtolower($ref_host));
					if ($ref_host !== false && $ref_host != $server_name && $ref_host != $http_host) $xd = true;
				}
			}
		} else $xd = true;
		
		if (!empty($forms)) {
			if (!class_exists("lepopup_form")) include_once(dirname(__FILE__).'/core-form.php');
			foreach ($forms as $form) {
				$form_object = new lepopup_form($form['id']);
				if (!empty($form_object->id)) {
					$form = $form_object->get_form_html();
					if (is_array($form) && array_key_exists('style', $form) && array_key_exists('html', $form)) {
						$html = '<div class="lepopup-popup-container" id="lepopup-popup-'.esc_html($form_object->id).'" onclick="jQuery(\'#lepopup-popup-'.esc_html($form_object->id).'-overlay\').click();">'.$form['html'].'</div>';
						$preloaded_items .= $form['style'].$html;
					}
				}
			}
		}

		$inline_forms = array();
		if (array_key_exists('inline-slugs', $_REQUEST)) {
			$form_slugs = explode(',', $_REQUEST['inline-slugs']);
			if (is_array($form_slugs) && sizeof($form_slugs) > 0) {
				include_once(dirname(__FILE__).'/core-front.php');
				foreach($form_slugs as $form_slug) {
					$inline_forms[] = lepopup_front_class::shortcode_handler(array('slug' => $form_slug, 'xd' => $xd));
				}
			}
		}

		$return_data = array();
		$return_data['status'] = 'OK';
		$return_data['events-data'] = $event_data['events-data'];
		$return_data['items-html'] = $preloaded_items;
		$return_data['inline-forms'] = $inline_forms;

		if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
		else echo json_encode($return_data);
		exit;
	}
	
	function front_submit() {
		global $wpdb, $lepopup;
		$callback = '';
		if (isset($_REQUEST['callback'])) {
			header("Content-type: text/javascript");
			$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
		}
		$xd = false;
		if (array_key_exists('hostname', $_REQUEST)) {
			$server_name = str_replace('www.', '', strtolower($_SERVER['SERVER_NAME']));
			$http_host = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
			$hostname = str_replace('www.', '', strtolower($_REQUEST['hostname']));
			if ($hostname != $server_name && $hostname != $http_host) $xd = true;
			else {
				if (array_key_exists('HTTP_REFERER', $_SERVER) && !empty($_SERVER['HTTP_REFERER'])) {
					$ref_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
					$ref_host = str_replace('www.', '', strtolower($ref_host));
					if ($ref_host !== false && $ref_host != $server_name && $ref_host != $http_host) $xd = true;
				}
			}
		} else $xd = true;
		
		if (!class_exists("lepopup_form")) include_once(dirname(__FILE__).'/core-form.php');
		$form_object = new lepopup_form(intval($_REQUEST['form-id']));
		if (!empty($form_object->id)) {
			if ($xd === false || $form_object->form_options['cross-domain'] == 'on') {
				if (array_key_exists('page-id', $_REQUEST)) $page_id = intval($_REQUEST['page-id']);
				else $page_id = 0;
				$pages = $form_object->get_pages();
				if (array_key_exists($page_id, $pages)) {
					$form_data = array();
					parse_str(base64_decode($_REQUEST['form-data']), $form_data);
					
					$form_object->set_form_data($form_data);
					$form_object->set_form_info();
					
					do_action("lepopup_populate_form_extra", $form_object->form_extra);
					
					$errors_all = $form_object->validate_form_data();
					
					if ($errors_all === false) {
						$return_data = array(
							'status' => 'FATAL',
							'message' => esc_html__('Requested form not found.', 'lepopup')
						);
					} else if (!is_array($errors_all)) {
						$return_data = array(
							'status' => 'FATAL',
							'message' => esc_html__('Unexpected error.', 'lepopup')
						);
					} else {
						$errors = array();
						if (!empty($errors_all)) {
							foreach ($pages as $key => $elements) {
								if ($form_object->is_page_visible($key)) {
									foreach ($elements as $element_id) {
										if (array_key_exists($element_id, $errors_all)) {
											$errors[$key.':'.$element_id] = $errors_all[$element_id];
										}
									}
								}
								if ($key == $page_id) break;
							}
						}
						if (empty($errors)) {
							$next_page_id = $form_object->get_next_page_id($page_id);
							if ($next_page_id === false) {
								$return_data = array(
									'status' => 'FATAL',
									'message' => esc_html__('Requested page not found.', 'lepopup')
								);
							} else if ($next_page_id === true || $next_page_id == 'confirmation') {
								$payment_ok = false;
								$log_record = $form_object->save_data();
								
								do_action("lepopup_populate_record_id", $log_record['id']);
								
								if (array_key_exists('campaign-slug', $_REQUEST) && !empty($_REQUEST['campaign-slug'])) {
									$campaign_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_campaigns WHERE deleted = '0' AND slug = '".esc_sql($_REQUEST['campaign-slug'])."'", ARRAY_A);
									if (!empty($campaign_details)) {
										$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_campaign_items SET submits = submits + 1 WHERE form_id = '".intval($form_object->id)."' AND campaign_id = '".intval($campaign_details['id'])."'");
									}
								}
								$confirmation = $form_object->get_confirmation();
								$return_data = array(
									'status' => 'OK',
									'record-id' => $form_object->record_id
								);
								if (empty($confirmation)) {
									$return_data['type'] = 'close';
									$return_data['reset-form'] = 'on';
								} else {
									$return_data['type'] = $confirmation['type'];
									$return_data['reset-form'] = $confirmation['reset-form'];
									if (in_array($confirmation['type'], array('page-redirect', 'redirect'))) $return_data['url'] = $form_object->replace_shortcodes($confirmation['url'], array(), true); // UF-checked
									if (in_array($confirmation['type'], array('page', 'form', 'page-redirect', 'page-payment'))) $return_data['delay'] = $confirmation['delay'];
									if (in_array($confirmation['type'], array('form'))) $return_data['form'] = $confirmation['form'];
									if (in_array($confirmation['type'], array('page-payment', 'payment'))) {
										$payment_gateway = $form_object->get_payment_gateway($confirmation['payment-gateway']);
										if (!empty($payment_gateway) && is_array($payment_gateway)) {
											$data = $form_object->replace_shortcodes($payment_gateway['data']); // UF-checked
											$data['record-id'] = $log_record['id'];
											$data['form-id'] = $form_object->id;
											$data['form-name'] = $form_object->name;
											$payment_data = apply_filters('lepopup_payment_gateways_do_'.$payment_gateway['provider'], null, $data);
											if (!empty($payment_data) && is_array($payment_data) && $payment_data['status'] == 'OK') {
												$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_records SET amount = '".esc_sql($payment_data['amount'])."', currency = '".esc_sql($payment_data['currency'])."', gateway_id = '".esc_sql($payment_gateway['id'])."', status = '".esc_sql(LEPOPUP_RECORD_STATUS_UNPAID)."' WHERE id = '".esc_sql($log_record['id'])."'");
												if (array_key_exists('form', $payment_data)) $return_data['payment-form'] = $payment_data['form'];
												else if (array_key_exists('message', $payment_data)) $return_data['payment-message'] = $payment_data['message'];
												else if (array_key_exists('stripe', $payment_data)) $return_data['stripe'] = $payment_data['stripe'];
												else if (array_key_exists('payumoney', $payment_data)) $return_data['payumoney'] = $payment_data['payumoney'];
												$payment_ok = true;
											} else if (!empty($payment_data) && is_array($payment_data) && $payment_data['status'] == 'ERROR' && array_key_exists('message', $payment_data)) {
												$return_data['error'] = $payment_data['message'];
											}
										}
										if (!$payment_ok) {
											if (in_array($confirmation['type'], array('page-payment'))) $return_data['type'] = 'page';
											else $return_data['type'] = 'close';
										}
									}
								}
								if (!defined('HALFDATA_DEMO') || HALFDATA_DEMO != true || current_user_can('manage_options')) {
									if (!$payment_ok) {
										$shortcode_addons = array('{{confirmation-url}}' => (defined('UAP_CORE') ? admin_url('do.php') : get_bloginfo('url').'/').'?lepopup-confirm='.$log_record['str-id']);
										if ($form_object->form_options['double-enable'] == 'on') {
											$to = $form_object->replace_shortcodes($form_object->form_options['double-email-recipient']); // UF-checked
											if (!empty($to) && preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $to)) {
												$subject = $form_object->replace_shortcodes($form_object->form_options['double-email-subject'], $shortcode_addons, false, true); // UF-checked
												$message = $form_object->replace_shortcodes($form_object->form_options['double-email-message'], $shortcode_addons, false, true); // UF-checked
												if (strpos(strtolower($message), '<html') === false) $message = str_replace(array("\n", "\r"), array("<br />", ""), $message);
												$from_email = $form_object->replace_shortcodes($form_object->form_options['double-from-email']); // UF-checked
												if (empty($from_email) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $from_email)) $from_email = $lepopup->options['from-email'];
												$from_name = $form_object->replace_shortcodes($form_object->form_options['double-from-name'], array(), false, true); // UF-checked
												$mail_headers = "Content-Type: text/html; charset=UTF-8\r\n";
												$mail_headers .= "From: ".(empty($from_name) ? esc_html($from_email) : $from_name)." <".esc_html($from_email).">\r\n";
												$mail_headers .= "X-Mailer: PHP/".phpversion()."\r\n";
												wp_mail($to, $subject, $message, $mail_headers);
											}
										}
									}
									$form_object->do_notifications("submit", ($payment_ok ? array('payment-amount' => $payment_data['amount'], 'payment-currency' => $payment_data['currency'], 'payment-status' => esc_html__('Unpaid', 'lepopup')) : array()));
									$integrations_data = $form_object->do_integrations("submit");
									if (array_key_exists('forms', $integrations_data)) $return_data['forms'] = implode('', $integrations_data['forms']);
									do_action('lepopup_submitted', $form_object);
									$form_object->update_extra();
								}
							} else {
								$return_data = array(
									'status' => 'NEXT',
									'page' => $next_page_id
								);
							}
						} else {
							$return_data = array(
								'status' => 'ERROR',
								'errors' => $errors
							);
						}
					}
				} else {
					$return_data = array(
						'status' => 'FATAL',
						'message' => esc_html__('Requested page not found.', 'lepopup')
					);
				}
			} else {
				$return_data = array(
					'status' => 'FATAL',
					'message' => esc_html__('Cross-domain calls are not allowed for this popup.', 'lepopup')
				);
			}
		} else {
			$return_data = array(
				'status' => 'FATAL',
				'message' => esc_html__('Requested popup not found.', 'lepopup')
			);
		}
		if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
		else echo json_encode($return_data);
		exit;
	}

	function front_add_impression() {
		global $wpdb, $lepopup;
		$callback = '';
		if (isset($_REQUEST['callback'])) {
			header("Content-type: text/javascript");
			$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
		}
		if (array_key_exists('form-ids', $_REQUEST)) {
			$campaign_id = null;
			if (array_key_exists('campaign-slug', $_REQUEST) && !empty($_REQUEST['campaign-slug'])) {
				$campaign_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_campaigns WHERE deleted = '0' AND slug = '".esc_sql($_REQUEST['campaign-slug'])."'", ARRAY_A);
				if (!empty($campaign_details)) $campaign_id = $campaign_details['id'];
			}
			$form_ids = explode(',', $_REQUEST['form-ids']);
			for ($i=0; $i<sizeof($form_ids); $i++) {
				$form_id = intval($form_ids[$i]);
				$this->_add_impression($form_id);
				if (!empty($campaign_id)) {
					$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_campaign_items SET impressions = impressions + 1 WHERE form_id = '".intval($form_id)."' AND campaign_id = '".intval($campaign_id)."'");
				}
			}
			$return_data = array(
				'status' => 'OK',
				'consts'=> array('ip' => $_SERVER['REMOTE_ADDR'])
			);
			if (is_user_logged_in()) {
				$current_user = wp_get_current_user();
				$return_data['consts']['wp-user-login'] = $current_user->user_login;
				$return_data['consts']['wp-user-email'] = $current_user->user_email;
			}
		} else {
			$return_data = array(
				'status' => 'FATAL',
				'message' => esc_html__('Invalid request.', 'lepopup')
			);
		}
		if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
		else echo json_encode($return_data);
		exit;
	}
	
	function front_popup_load() {
		global $wpdb, $lepopup;
		$callback = '';
		$html = '';
		if (isset($_REQUEST['callback'])) {
			header("Content-type: text/javascript");
			$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
		}

		$xd = false;
		if (array_key_exists('hostname', $_REQUEST)) {
			$server_name = str_replace('www.', '', strtolower($_SERVER['SERVER_NAME']));
			$http_host = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
			$hostname = str_replace('www.', '', strtolower($_REQUEST['hostname']));
			if ($hostname != $server_name && $hostname != $http_host) $xd = true;
			else {
				if (array_key_exists('HTTP_REFERER', $_SERVER) && !empty($_SERVER['HTTP_REFERER'])) {
					$ref_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
					$ref_host = str_replace('www.', '', strtolower($ref_host));
					if ($ref_host !== false && $ref_host != $server_name && $ref_host != $http_host) $xd = true;
				}
			}
		} else $xd = true;

		if (array_key_exists('form-slug', $_REQUEST)) {
			if (!class_exists("lepopup_form")) include_once(dirname(__FILE__).'/core-form.php');
			if (array_key_exists('preview', $_REQUEST) && $_REQUEST["preview"] == "on") $ignore_status = true;
			else $ignore_status = false;
			$form_object = new lepopup_form($_REQUEST['form-slug'], false, $ignore_status);
			if (!empty($form_object->id)) {
				if ($xd === true && $form_object->form_options['cross-domain'] != 'on') {
					$return_data = array('status' => 'ERROR', 'message' => esc_html__('Cross-domain calls are not allowed for this popup.', 'lepopup'));
				} else {
					$form = $form_object->get_form_html();
					if (is_array($form) && array_key_exists('style', $form) && array_key_exists('html', $form)) {
						$html = '<div class="lepopup-popup-container" id="lepopup-popup-'.esc_html($form_object->id).'" onclick="jQuery(\'#lepopup-popup-'.esc_html($form_object->id).'-overlay\').click();">'.$form['html'].'</div>';
						if (array_key_exists('form-style', $_REQUEST) && $_REQUEST['form-style'] == 'off') $return_data = array('status' => 'OK', 'html' => $html);
						else $return_data = array('status' => 'OK', 'html' => $form['style'].$html);
						//$this->_add_impression($form_object->id);
					} else {
						$return_data = array('status' => 'ERROR');
					}
				}
			} else {
				$return_data = array('status' => 'ERROR');
			}
		} else {
			$return_data = array('status' => 'ERROR');
		}
		if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
		else echo json_encode($return_data);
		exit;
	}

	function _add_impression($_form_id) {
		global $wpdb, $lepopup;
		$form_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND id = '".esc_sql($_form_id)."'", ARRAY_A);
		if (empty($form_details)) return;
		$datestamp = date('Ymd', time()+3600*$lepopup->gmt_offset);
		$timestamp = date('h', time()+3600*$lepopup->gmt_offset);
		$stats_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_stats WHERE form_id = '".esc_sql($_form_id)."' AND datestamp = '".esc_sql($datestamp)."' AND timestamp = '".esc_sql($timestamp)."'", ARRAY_A);
		if (!empty($stats_details)) {
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_stats SET impressions = impressions + 1 WHERE id = '".esc_sql($stats_details['id'])."'");
		} else {
			$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_stats (form_id, impressions, submits, confirmed, payments, datestamp, timestamp, deleted) VALUES ('".esc_sql($_form_id)."', '1', '0', '0', '0', '".esc_sql($datestamp)."', '".esc_sql($timestamp)."', '0')");
		}
	}

	function front_upload() {
		global $lepopup, $wpdb;
		if (array_key_exists('upload-id', $_REQUEST) && array_key_exists('form-id', $_REQUEST) && array_key_exists('element-id', $_REQUEST)) {
			$upload_id = preg_replace('/[^a-zA-Z0-9-]/', '', $_REQUEST['upload-id']);
			if (!empty($upload_id)) {
				if (!class_exists("lepopup_form")) include_once(dirname(__FILE__).'/core-form.php');
				$form_object = new lepopup_form(intval($_REQUEST['form-id']));
				if (!empty($form_object->id)) {
					$element_idx = false;
					for ($i=0; $i<sizeof($form_object->form_elements); $i++) {
						if ($form_object->form_elements[$i]['id'] == $_REQUEST['element-id'] && $form_object->form_elements[$i]['type'] == 'file') {
							$element_idx = $i;
						}
					}
					if ($element_idx !== false) {
						if (array_key_exists('files', $_FILES) && sizeof($_FILES['files']) > 0 && sizeof($_FILES['files']['name']) > 0) {
							
							$allowed_extensions_raw = explode(',', $form_object->form_elements[$element_idx]['allowed-extensions']);
							$allowed_extensions = array();
							foreach ($allowed_extensions_raw as $extension) {
								$extension = trim(trim($extension), '.');
								if (!empty($extension)) $allowed_extensions[] = strtolower($extension);
							}
						
							$upload_dir = wp_upload_dir();
							wp_mkdir_p($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$form_object->id);
							if (!file_exists($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$form_object->id.'/.htaccess')) {
								file_put_contents($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$form_object->id.'/.htaccess', 'deny from all');
							}
							foreach ($_FILES["files"]["error"] as $key => $error) {
								if ($error == UPLOAD_ERR_OK) {
									$filename_original = basename($_FILES["files"]["name"][$key]);
									$upload_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_uploads WHERE deleted = '0' AND upload_id = '".esc_sql($upload_id)."' AND filename_original = '".esc_sql($filename_original)."'", ARRAY_A);
									if (empty($upload_details)) {
										$ext = pathinfo($filename_original, PATHINFO_EXTENSION);
										$ext = strtolower($ext);
										$max_size = intval($form_object->form_elements[$element_idx]['max-size'])*1024*1024;
										if ((!empty($allowed_extensions) && !in_array($ext, $allowed_extensions)) || substr($ext, 0, 3) == "php") {
											$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_uploads (record_id, form_id, element_id, upload_id, str_id, status, message, filename, filename_original, created, deleted) VALUES ('0', '".esc_sql($form_object->id)."', '0', '".esc_sql($upload_id)."', '', '".esc_sql(LEPOPUP_UPLOAD_STATUS_ERROR)."', '".esc_sql($form_object->form_elements[$element_idx]['allowed-extensions-error'])."', '', '".esc_sql($filename_original)."', '".esc_sql(time())."', '0')");
										} else if ($max_size > 0 && $_FILES["files"]["size"][$key] > $max_size) {
											$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_uploads (record_id, form_id, element_id, upload_id, str_id, status, message, filename, filename_original, created, deleted) VALUES ('0', '".esc_sql($form_object->id)."', '0', '".esc_sql($upload_id)."', '', '".esc_sql(LEPOPUP_UPLOAD_STATUS_ERROR)."', '".esc_sql($form_object->form_elements[$element_idx]['max-size-error'])."', '', '".esc_sql($filename_original)."', '".esc_sql(time())."', '0')");
										} else {
											$filename = '_'.$lepopup->random_string(32).(!empty($ext) ? '.'.$ext : '');
											$moved = move_uploaded_file($_FILES["files"]["tmp_name"][$key], $upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$form_object->id.'/'.$filename);
											if ($moved) {
												$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_uploads (record_id, form_id, element_id, upload_id, str_id, status, message, filename, filename_original, created, deleted) VALUES ('0', '".esc_sql($form_object->id)."', '0', '".esc_sql($upload_id)."', '', '".esc_sql(LEPOPUP_UPLOAD_STATUS_OK)."', '', '".esc_sql($filename)."', '".esc_sql($filename_original)."', '".esc_sql(time())."', '0')");
											} else {
												$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_uploads (record_id, form_id, element_id, upload_id, str_id, status, message, filename, filename_original, created, deleted) VALUES ('0', '".esc_sql($form_object->id)."', '0', '".esc_sql($upload_id)."', '', '".esc_sql(LEPOPUP_UPLOAD_STATUS_ERROR)."', '".esc_sql(esc_html__('Can not move uploaded file.', 'lepopup'))."', '".esc_sql($filename)."', '".esc_sql($filename_original)."', '".esc_sql(time())."', '0')");
											}
										}
									}
								} else {
									$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_uploads (record_id, form_id, element_id, upload_id, str_id, status, message, filename, filename_original, created, deleted) VALUES ('0', '".esc_sql($form_object->id)."', '0', '".esc_sql($upload_id)."', '', '".esc_sql(LEPOPUP_UPLOAD_STATUS_ERROR)."', '".esc_sql(esc_html__('Can not process uploaded file.', 'lepopup'))."', '', '".esc_sql($filename_original)."', '".esc_sql(time())."', '0')");
								}
							}						
						}
					}
				}
			}
		}
		echo 'Upload Completed!';
		exit;
	}
	
	function front_upload_progress() {
		global $wpdb, $lepopup;
		$callback = '';
		$html = '';
		if (isset($_REQUEST['callback'])) {
			header("Content-type: text/javascript");
			$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
		}
		if (array_key_exists('upload-id', $_REQUEST)) {
			$upload_id = preg_replace('/[^a-zA-Z0-9-]/', '', $_REQUEST['upload-id']);
			if (!empty($upload_id)) {
				$uploads = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_uploads WHERE deleted = '0' AND upload_id = '".esc_sql($upload_id)."' AND str_id = '' AND status != '".esc_sql(LEPOPUP_UPLOAD_STATUS_DELETED)."'", ARRAY_A);
				if (!empty($uploads)) {
					$return_data = array('status' => 'OK', 'result' => array());
					foreach ($uploads as $upload_details) {
						switch($upload_details['status']) {
							case LEPOPUP_UPLOAD_STATUS_ERROR:
								$file_data = array(
									'status' => 'ERROR',
									'message' => $upload_details['message'],
									'name' => $upload_details['filename_original']
								);
								$wpdb->query("DELETE FROM ".$wpdb->prefix."lepopup_uploads WHERE id = '".esc_sql($upload_details['id'])."'");
								break;
							default:
								$str_id = $lepopup->random_string(16);
								$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_uploads SET str_id='".esc_sql($str_id)."' WHERE id = '".esc_sql($upload_details['id'])."'");
								$file_data = array(
									'status' => 'OK',
									'uid' => $str_id,
									'name' => $upload_details['filename_original']
								);
								
								break;
						}
						$return_data['result'][] = $file_data;
					}
				} else {
					if (array_key_exists('last-request', $_REQUEST)) {
						$return_data = array('status' => 'ERROR');
					} else {
						$return_data = array('status' => 'LOADING');
						$key = ini_get("session.upload_progress.prefix").$upload_id;
						if (array_key_exists($key, $_SESSION) && !empty($_SESSION[$key])) {
							foreach ($_SESSION[$key]['files'] as $file) {
								$return_data['progress'][] = array('name' => $file['name'], 'bytes_processed' => $file['bytes_processed']);
							}
						}
					}
				}
			} else {
				$return_data = array('status' => 'ERROR', 'message' => esc_html__('Invalid request.', 'lepopup'));
			}
		} else {
			$return_data = array('status' => 'ERROR', 'message' => esc_html__('Invalid request.', 'lepopup'));
		}
		if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
		else echo json_encode($return_data);
		exit;
	}
	
	function front_upload_delete() {
		global $wpdb, $lepopup;
		$callback = '';
		$html = '';
		if (isset($_REQUEST['callback'])) {
			header("Content-type: text/javascript");
			$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
		}
		if (array_key_exists('upload-id', $_REQUEST) && array_key_exists('name', $_REQUEST)) {
			$upload_id = preg_replace('/[^a-zA-Z0-9-]/', '', $_REQUEST['upload-id']);
			$name = stripslashes(trim($_REQUEST['name']));
			if (!empty($upload_id) && !empty($name)) {
				$upload_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_uploads WHERE deleted = '0' AND upload_id = '".esc_sql($upload_id)."' AND filename_original = '".esc_sql($name)."'", ARRAY_A);
				if ($upload_details) {
					if ($upload_details['status'] == LEPOPUP_UPLOAD_STATUS_OK) {
						$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_uploads SET status = '".esc_sql(LEPOPUP_UPLOAD_STATUS_DELETED)."' WHERE id = '".esc_sql($upload_details['id'])."'");
						$upload_dir = wp_upload_dir();
						if (file_exists($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$upload_details['form_id'].'/'.$upload_details['filename']) && is_file($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$upload_details['form_id'].'/'.$upload_details['filename'])) {
							unlink($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$upload_details['form_id'].'/'.$upload_details['filename']);
						}
					}
				} else {
					$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_uploads (record_id, form_id, element_id, upload_id, str_id, status, message, filename, filename_original, created, deleted) VALUES ('0', '0', '0', '".esc_sql($upload_id)."', '', '".esc_sql(LEPOPUP_UPLOAD_STATUS_DELETED)."', '', '', '".esc_sql($name)."', '".esc_sql(time())."', '0')");
				}
				$return_data = array('status' => 'OK');
			} else {
				$return_data = array('status' => 'ERROR', 'message' => esc_html__('Invalid request.', 'lepopup'));
			}
		} else {
			$return_data = array('status' => 'ERROR', 'message' => esc_html__('Invalid request.', 'lepopup'));
		}
		if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
		else echo json_encode($return_data);
		exit;
	}

	function front_remote_init() {
		global $wpdb, $lepopup;
		$callback = '';
		if (isset($_REQUEST['callback'])) {
			header("Content-type: text/javascript");
			$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
		}
		if ($lepopup->advanced_options['minified-sources'] == 'on') $min = '.min';
		else $min = '';
		$return_data = array(
			'status' => 'OK',
			'consts'=> array('ip' => $_SERVER['REMOTE_ADDR'])
		);
		if (is_user_logged_in()) {
			$current_user = wp_get_current_user();
			$return_data['consts']['wp-user-login'] = $current_user->user_login;
			$return_data['consts']['wp-user-email'] = $current_user->user_email;
		}
		$return_data['resources']['css'][] = $lepopup->plugins_url.'/css/style'.$min.'.css?ver='.LEPOPUP_VERSION;
		if ($lepopup->options['fa-enable'] == 'on') {
			if ($lepopup->options['fa-css-disable'] != 'on') {
				if ($lepopup->options['fa-solid-enable'] == 'on' && $lepopup->options['fa-regular-enable'] == 'on' && $lepopup->options['fa-brands-enable'] == 'on') $return_data['resources']['css'][] = $lepopup->plugins_url.'/css/fontawesome-all'.$min.'.css?ver='.LEPOPUP_VERSION;
				else {
					$return_data['resources']['css'][] = $lepopup->plugins_url.'/css/fontawesome'.$min.'.css?ver='.LEPOPUP_VERSION;
					if ($lepopup->options['fa-solid-enable'] == 'on') $return_data['resources']['css'][] = $lepopup->plugins_url.'/css/fontawesome-solid'.$min.'.css?ver='.LEPOPUP_VERSION;
					if ($lepopup->options['fa-regular-enable'] == 'on') $return_data['resources']['css'][] = $lepopup->plugins_url.'/css/fontawesome-regular'.$min.'.css?ver='.LEPOPUP_VERSION;
					if ($lepopup->options['fa-brands-enable'] == 'on') $return_data['resources']['css'][] = $lepopup->plugins_url.'/css/fontawesome-brands'.$min.'.css?ver='.LEPOPUP_VERSION;
				}
			}
		}
		$return_data['resources']['css'][] = $lepopup->plugins_url.'/css/lepopup-if'.$min.'.css?ver='.LEPOPUP_VERSION;
		$return_data['plugins'] = array();
		if ($lepopup->options['signature-enable'] == 'on') {
			if ($lepopup->options['signature-js-disable'] != 'on') {
				$return_data['resources']['js'][] = $lepopup->plugins_url.'/js/signature_pad'.$min.'.js?ver='.LEPOPUP_VERSION;
			}
			$return_data['plugins'][] = 'signature_pad';
		}
		if ($lepopup->options['airdatepicker-enable'] == 'on') {
			if ($lepopup->options['airdatepicker-js-disable'] != 'on') {
				$return_data['resources']['css'][] = $lepopup->plugins_url.'/css/airdatepicker'.$min.'.css?ver='.LEPOPUP_VERSION;
				$return_data['resources']['js'][] = $lepopup->plugins_url.'/js/airdatepicker'.$min.'.js?ver='.LEPOPUP_VERSION;
			}
			$return_data['plugins'][] = 'airdatepicker';
		}
		if ($lepopup->options['range-slider-enable'] == 'on') {
			if ($lepopup->options['range-slider-js-disable'] != 'on') {
				$return_data['resources']['css'][] = $lepopup->plugins_url.'/css/ion.rangeSlider'.$min.'.css?ver='.LEPOPUP_VERSION;
				$return_data['resources']['js'][] = $lepopup->plugins_url.'/js/ion.rangeSlider'.$min.'.js?ver='.LEPOPUP_VERSION;
			}
			$return_data['plugins'][] = 'ion.rangeSlider';
		}
		if ($lepopup->options['jsep-enable'] == 'on') {
			if ($lepopup->options['jsep-js-disable'] != 'on') {
				$return_data['resources']['js'][] = $lepopup->plugins_url.'/js/jsep'.$min.'.js?ver='.LEPOPUP_VERSION;
			}
			$return_data['plugins'][] = 'jsep';
		}
		if ($lepopup->options['mask-enable'] == 'on') {
			if ($lepopup->options['mask-js-disable'] != 'on') {
				$return_data['resources']['js'][] = $lepopup->plugins_url.'/js/jquery.mask'.$min.'.js?ver='.LEPOPUP_VERSION;
			}
			$return_data['plugins'][] = 'jquery.mask';
		}
		if ($lepopup->options['adblock-detector-enable'] == 'on') {
			$return_data['resources']['js'][] = "https://static.doubleclick.net/instream/ad_status.js";
		}
		$return_data['adb-enabled'] = $lepopup->options['adblock-detector-enable'];
		$return_data['ga-tracking'] = $lepopup->options['ga-tracking'];
		$return_data['cookie-value'] = $lepopup->options['cookie-value'];
		
		if (array_key_exists('preview', $_REQUEST) && $_REQUEST['preview'] == 'on') $ignore_status = true;
		else $ignore_status = false;
		
		$forms = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0'".($ignore_status ? '' : " AND active = '1'"), ARRAY_A);
		$default_form_options = $lepopup->default_form_options();
		$return_data['overlays'] = array();
		foreach ($forms as $form_details) {
			$form_options = json_decode($form_details['options'], true);
			if (!empty($form_options) && is_array($form_options)) $form_options = array_merge($default_form_options, $form_options);
			else $form_options = $default_form_options;
			$return_data['overlays'][$form_details['slug']] = array($form_details['id'], $form_options['position'], $form_options['overlay-enable'], (empty($form_options['overlay-color']) ? 'transparent' : $form_options['overlay-color']), $form_options['overlay-click'], $form_options['overlay-animation'], (empty($form_options["spinner-color-color1"]) ? '#FF5722' : $form_options["spinner-color-color1"]), (empty($form_options["spinner-color-color2"]) ? '#FF9800' : $form_options["spinner-color-color2"]), (empty($form_options["spinner-color-color3"]) ? '#FFC107' : $form_options["spinner-color-color3"]), (array_key_exists('cookie-lifetime', $form_options) ? intval($form_options['cookie-lifetime']) : '365'));
		}
		$campaigns = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_campaigns WHERE deleted = '0' AND active = '1'", ARRAY_A);
		$return_data['campaigns'] = array();
		foreach ($campaigns as $campaigns_details) {
			$form_slugs = $wpdb->get_results("SELECT t2.slug FROM ".$wpdb->prefix."lepopup_campaign_items t1 LEFT JOIN ".$wpdb->prefix."lepopup_forms t2 ON t2.id = t1.form_id WHERE t1.deleted = '0' AND t2.deleted = '0' AND t2.active = '1' AND t1.campaign_id = '".esc_sql($campaigns_details['id'])."'", ARRAY_A);
			$forms_array = array();
			foreach($form_slugs as $form_slug) {
				if (!empty($form_slug['slug'])) $forms_array[] = $form_slug['slug'];
			}
			if (!empty($forms_array)) $return_data['campaigns'][$campaigns_details['slug']] = $forms_array;
		}

		$return_data["inline-forms"] = array();
		$xd = false;
		if (array_key_exists('hostname', $_REQUEST)) {
			$server_name = str_replace('www.', '', strtolower($_SERVER['SERVER_NAME']));
			$http_host = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
			$hostname = str_replace('www.', '', strtolower($_REQUEST['hostname']));
			if ($hostname != $server_name && $hostname != $http_host) $xd = true;
			else {
				if (array_key_exists('HTTP_REFERER', $_SERVER) && !empty($_SERVER['HTTP_REFERER'])) {
					$ref_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
					$ref_host = str_replace('www.', '', strtolower($ref_host));
					if ($ref_host !== false && $ref_host != $server_name && $ref_host != $http_host) $xd = true;
				}
			}
		} else $xd = true;
		if (array_key_exists('inline-slugs', $_REQUEST)) {
			$form_slugs = explode(',', $_REQUEST['inline-slugs']);
			if (is_array($form_slugs) && sizeof($form_slugs) > 0) {
				include_once(dirname(__FILE__).'/core-front.php');
				foreach($form_slugs as $form_slug) {
					$return_data['inline-forms'][] = lepopup_front_class::shortcode_handler(array('slug' => $form_slug, 'xd' => $xd));
				}
			}
		}
		$return_data = apply_filters('lepopup_remote_parameters', $return_data);
		if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
		else echo json_encode($return_data);
		exit;
	}
	
}
?>