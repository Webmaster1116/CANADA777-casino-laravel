<?php
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_legacy_class {
	var $migrating_status = array(
		'settings' => false,
		'start-time' => 0,
		'popup-id' => 0,
		'campaign-id' => 0,
		'target-id' => 0,
		'record-id' => 0,
		'tab-id' => 0,
		'download-id' => 0,
		'popup-ids' => array(),
		'popup-slugs' => array(),
		'popup-fields' => array(),
		'campaign-ids' => array(),
		'campaign-slugs' => array(),
		'target-ids' => array(),
		'record-ids' => array(),
		'tab-ids' => array(),
		'tab-slugs' => array()
	);
	var $options_map = array(
		"cookie_value" => "cookie-value",
		"csv_separator" => "csv-separator",
		"ga_tracking" => "ga-tracking",
		"fa_enable" => "fa-enable",
		"fa_solid_enable" => "fa-solid-enable",
		"fa_regular_enable" => "fa-regular-enable",
		"fa_brands_enable" => "fa-brands-enable",
		"fa_css_disable" => "fa-css-disable",
		"mask_enable" => "mask-enable",
		"mask_js_disable" => "mask-js-disable",
		"preload_event_popups" => 'preload-event-popups',
		"from_name" => "from-name",
		"from_email" => "from-email",
		"purchase_code" => "purchase-code"
	);

	var $advanced_options_map = array(
		'enable_js' => 'enable-custom-js',
		'enable_mailchimp' => 'enable-mailchimp',
		'enable_intercom' => 'enable-intercom',
		'enable_pipedrive' => 'enable-pipedrive',
		'enable_mailrelay' => 'enable-mailrelay',
		'enable_mailgun' => 'enable-mailgun',
		'enable_bitrix24' => 'enable-bitrix24',
		'enable_birdsend' => 'enable-birdsend',
		'enable_conversio' => 'enable-conversio',
		'enable_rapidmail' => 'enable-rapidmail',
		'enable_sendfox' => 'enable-sendfox',
		'enable_omnisend' => 'enable-omnisend',
		'enable_moosend' => 'enable-moosend',
		'enable_zohocrm' => 'enable-zohocrm',
		'enable_acellemail' => 'enable-acellemail',
		'enable_mailfit' => 'enable-mailfit',
		'enable_mautic' => 'enable-mautic',
		'enable_activetrail' => 'enable-activetrail',
		'enable_jetpack' => 'enable-jetpack',
		'enable_drip' => 'enable-drip',
		'enable_hubspot' => 'enable-hubspot',
		'enable_klaviyo' => 'enable-klaviyo',
		'enable_cleverreach' => 'enable-cleverreach',
		'enable_agilecrm' => 'enable-agilecrm',
		'enable_sendinblue' => 'enable-sendinblue',
		'enable_sendgrid' => 'enable-sendgrid',
		'enable_aweber' => 'enable-aweber',
		'enable_getresponse' => 'enable-getresponse',
		'enable_madmimi' => 'enable-madmimi',
		'enable_convertkit' => 'enable-convertkit',
		'enable_campaignmonitor' => 'enable-campaignmonitor',
		'enable_salesautopilot' => 'enable-salesautopilot',
		'enable_thenewsletterplugin' => 'enable-thenewsletterplugin',
		'enable_sendy' => 'enable-sendy',
		'enable_activecampaign' => 'enable-activecampaign',
		'enable_ontraport' => 'enable-ontraport',
		'enable_mailerlite' => 'enable-mailerlite',
		'enable_sgautorepondeur' => 'enable-sgautorepondeur',
		'enable_mymail' => 'enable-mailster',
		'enable_tribulant' => 'enable-tribulant',
		'enable_sendpulse' => 'enable-sendpulse',
		'enable_mailpoet' => 'enable-mailpoet',
		'enable_freshmail' => 'enable-freshmail',
		'enable_ymlp' => 'enable-ymlp',
		'enable_htmlform' => 'enable-htmlform',
		'enable_wpuser' => 'enable-wpuser',
		'enable_mailwizz' => 'enable-mailwizz',
		'enable_mumara' => 'enable-mumara',
		'enable_avangemail' => 'enable-avangemail',
		'enable_mailautic' => 'enable-mailautic',
		'enable_mailjet' => 'enable-mailjet',
		'enable_constantcontact' => 'enable-constantcontact',
		'enable_thechecker' => 'enable-thechecker',
		'enable_kickbox' => 'enable-kickbox',
		'enable_emaillistverify' => 'enable-emaillistverify',
		'enable_clearout' => 'enable-clearout',
		'enable_truemail' => 'enable-truemail',
		'minified_sources' => 'minified-sources',
		'async_init' => 'async-init'
	);

	var $absent_modules = array(
		'enable_dotmailer' => 'dotMailer',
		'enable_mnb' => 'MyNewsletterBuilder',
		'enable_markethero' => 'Market Hero',
		'enable_kirimemail' => 'KIRIM.EMAIL',
		'enable_squalomail' => 'SqualoMail',
		'enable_unisender' => 'off',
		'enable_zohocampaigns' => 'Zoho Campaigns',
		'enable_mailigen' => 'Mailigen',
		'enable_sendloop' => 'SendLoop',
		'enable_perfit' => 'Perfit',
		'enable_streamsend' => 'Sream Send',
		'enable_vision6' => 'Vision6',
		'enable_mailleader' => 'Mail Leader',
		'enable_mpzmail' => 'MPZ Mail',
		'enable_stampready' => 'Stamp Ready',
		'enable_emailoctopus' => 'Email Octopus',
		'enable_firedrum' => 'FireDrum',
		'enable_userengage' => 'User Engage',
		'enable_sendlane' => 'Sendlane',
		'enable_emma' => 'Emma',
		'enable_esputnik' => 'eSputnik',
		'enable_easysendypro' => 'EasySendy Pro',
		'enable_mailkitchen' => 'Mail Kitchen',
		'enable_rocketresponder' => 'Rocket Responder',
		'enable_salesmanago' => 'SALESmanago',
		'enable_simplycast' => 'SimplyCast',
		'enable_totalsend' => 'TotalSend',
		'enable_campayn' => 'Campayn',
		'enable_elasticemail' => 'ElasticEmail',
		'enable_egoi' => 'Egoi',
		'enable_icontact' => 'iContact',
		'enable_interspire' => 'Interspire',
		'enable_benchmark' => 'Benchmark',
		'enable_fue' => 'Follow-Up Emails',
		'enable_mailboxmarketing' => 'Mailbox Marketing',
		'enable_enewsletter' => 'E-newsletter by WPMU DEV',
		'enable_arigatopro' => 'Arigato Pro',
		'enable_subscribe2' => 'Subscribe2',
		'enable_sendpress' => 'SendPress',
		'enable_sendreach' => 'SendReach',
		'enable_directmail' => 'Direct Mail',
		'enable_customerio' => 'Customerio',
		'enable_klicktipp' => 'Klick Tipp',
		'enable_bulkemailchecker' => 'BulkEmailChecker',
		'enable_proofy' => 'Proofy',
		'enable_neverbounce' => 'Never Bounce',
		'enable_hunter' => 'Hunter'
	);
	
	var $manual_modules = array(
		'enable_aweber' => 'AWeber',
		'enable_convertkit' => 'ConvertKit',
		'enable_intercom' => 'Intercom',
		'enable_mailrelay' => 'Mailrelay',
		'enable_pipedrive' => 'Pipedrive',
		'enable_sendinblue' => 'SendinBlue',
		'enable_thenewsletterplugin' => 'The Newsletter Plugin',
		'enable_zohocrm' => 'Zoho CRM'
	);
	function __construct() {
		global $lepopup;
		$migrating_status = json_decode(get_option('lepopup-migrating-status', '[]'), true);
		$this->migrating_status = array_merge($this->migrating_status, $migrating_status);
	}
	
	function status() {
		global $wpdb, $ulp;
		$status_output = array(
			'settings' => '',
			'popups' => array('done' => 0, 'total' => 0),
			'campaigns' => array('done' => 0, 'total' => 0),
			'targets' => array('done' => 0, 'total' => 0),
			'records' => array('done' => 0, 'total' => 0),
			'tabs' => array('done' => 0, 'total' => 0),
			'downloads' => array('done' => 0, 'total' => 0),
			'warning-message' => '',
			'info-message' => ''
		);
		if ($this->migrating_status['settings'] == true) $status_output['settings'] = 'done';
		$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_popups", ARRAY_A);
		$total = $tmp["total"];
		if ($total == 0) $status_output['popups'] = array('done' => 1, 'total' => 1);
		else {
			$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_popups WHERE id <= '".intval($this->migrating_status['popup-id'])."'", ARRAY_A);
			$status_output['popups'] = array('done' => $tmp["total"], 'total' => $total);
		}
		$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_campaigns", ARRAY_A);
		$total = $tmp["total"];
		if ($total == 0) $status_output['campaigns'] = array('done' => 1, 'total' => 1);
		else {
			$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_campaigns WHERE id <= '".intval($this->migrating_status['campaign-id'])."'", ARRAY_A);
			$status_output['campaigns'] = array('done' => $tmp["total"], 'total' => $total);
		}
		if (!defined('UAP_CORE')) {
			$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_targets", ARRAY_A);
			$total = $tmp["total"];
			if ($total == 0) $status_output['targets'] = array('done' => 1, 'total' => 1);
			else {
				$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_targets WHERE id <= '".intval($this->migrating_status['target-id'])."'", ARRAY_A);
				$status_output['targets'] = array('done' => $tmp["total"], 'total' => $total);
			}
		}
		$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_subscribers", ARRAY_A);
		$total = $tmp["total"];
		if ($total == 0) $status_output['records'] = array('done' => 1, 'total' => 1);
		else {
			$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_subscribers WHERE id <= '".intval($this->migrating_status['record-id'])."'", ARRAY_A);
			$status_output['records'] = array('done' => $tmp["total"], 'total' => $total);
		}
		if (class_exists('ulptabs_class') && class_exists('lepopuptab_class')) {
			$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_tabs", ARRAY_A);
			$total = $tmp["total"];
			if ($total == 0) $status_output['tabs'] = array('done' => 1, 'total' => 1);
			else {
				$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_tabs WHERE id <= '".intval($this->migrating_status['tab-id'])."'", ARRAY_A);
				$status_output['tabs'] = array('done' => $tmp["total"], 'total' => $total);
			}
		}
		if (class_exists('ulpdownload_class') && class_exists('lepopupdownload_class')) {
			$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_downloads", ARRAY_A);
			$total = $tmp["total"];
			if ($total == 0) $status_output['downloads'] = array('done' => 1, 'total' => 1);
			else {
				$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_downloads WHERE id <= '".intval($this->migrating_status['download-id'])."'", ARRAY_A);
				$status_output['downloads'] = array('done' => $tmp["total"], 'total' => $total);
			}
		}
		$absent_modules = array();
		foreach ($this->absent_modules as $key => $value) {
			if (array_key_exists($key, $ulp->ext_options) && $ulp->ext_options[$key] == 'on') {
				$absent_modules[] = $value;
			}
		}
		if (!empty($absent_modules)) $status_output['warning-message'] = sprintf(esc_html__('At that moment Green Popups does not have integrations with %s. If you use these modules, please %scontact us%s and we implement them shortly.', 'lepopup'), implode(', ', $absent_modules), '<a href="https://codecanyon.net/item/layered-popups-for-wordpress/5978263/support" target="_blank">', '</a>');
		$manual_modules = array();
		foreach ($this->manual_modules as $key => $value) {
			if (array_key_exists($key, $ulp->ext_options) && $ulp->ext_options[$key] == 'on') {
				$manual_modules[] = $value;
			}
		}
		if (!empty($manual_modules)) $status_output['info-message'] = sprintf(esc_html__('After migrating please manually configure integrations with %s.', 'lepopup'), implode(', ', $manual_modules));
		return $status_output;
	}
	
	function migrate() {
		global $wpdb, $lepopup, $ulp, $lepopuptab;
		$callback = '';
		if (isset($_REQUEST['callback'])) {
			header("Content-type: text/javascript");
			$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
		}
		$start_time = time();
		if (empty($ulp)) {
			$return_data = array(
				'status' => 'ERROR',
				'message' => esc_html__('Layered Popups not activated.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
			exit;
		}
		if ($this->migrating_status['start-time'] != 0 && $this->migrating_status['start-time'] + 30 > $start_time) {
			$return_data = array(
				'status' => 'ERROR',
				'message' => esc_html__('Migrating process already started.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
			exit;
		}

		$this->migrating_status['start-time'] = $start_time;
		update_option('lepopup-migrating-status', json_encode($this->migrating_status));

		$status_output = $this->status();
		
		if ($this->migrating_status['settings'] == false) {
			foreach ($this->options_map as $key => $value) {
				if (array_key_exists($key, $ulp->options) && array_key_exists($value, $lepopup->options)) {
					$lepopup->options[$value] = $ulp->options[$key];
				}
			}
			$lepopup->update_options();
			foreach ($this->advanced_options_map as $key => $value) {
				if (array_key_exists($key, $ulp->ext_options) && array_key_exists($value, $lepopup->advanced_options)) {
					$lepopup->advanced_options[$value] = $ulp->ext_options[$key];
				}
			}
			$lepopup->update_advanced_options();
			$this->migrating_status['settings'] = true;
			$status_output['settings'] = 'done';
			update_option('lepopup-migrating-status', json_encode($this->migrating_status));
		}
		
		$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_popups", ARRAY_A);
		$total = $tmp["total"];
		if ($total == 0) $total = 1;
		$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_popups WHERE id <= '".intval($this->migrating_status['popup-id'])."'", ARRAY_A);
		$done = $tmp["total"];
		$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ulp_popups WHERE id > '".intval($this->migrating_status['popup-id'])."' ORDER BY id ASC LIMIT 0, 100", ARRAY_A);
		if (sizeof($rows) > 0) {
			foreach ($rows as $row) {
				$slug = $this->_popup_slug($row['str_id']);
				$layers = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ulp_layers WHERE deleted = '0' AND popup_id = '".intval($row['id'])."' ORDER BY zindex, created ASC", ARRAY_A);
				$result = $this->_import($row, $layers, $slug);
				$this->migrating_status['popup-slugs'][$row['str_id']] = $slug;
				$this->migrating_status['popup-ids'][$row['id']] = $result['id'];
				$this->migrating_status['popup-fields'][$row['id']] = $result['fields'];
				$this->migrating_status['popup-id'] = $row['id'];
				$done++;
				$status_output['popups'] = array('done' => $done, 'total' => $total);
				update_option('lepopup-migrating-status', json_encode($this->migrating_status));
				$current_time = time();
				if ($current_time - $start_time > 5) {
					$this->migrating_status['start-time'] = 0;
					update_option('lepopup-migrating-status', json_encode($this->migrating_status));
					$return_data = array(
						'status' => 'CONTINUE',
						'data' => $status_output
					);
					if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
					else echo json_encode($return_data);
					exit;
				}
			}
			$this->migrating_status['start-time'] = 0;
			update_option('lepopup-migrating-status', json_encode($this->migrating_status));
			$return_data = array(
				'status' => 'CONTINUE',
				'data' => $status_output
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
			exit;
		} else {
			$status_output['popups'] = array('done' => $total, 'total' => $total);
		}

		$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_campaigns", ARRAY_A);
		$total = $tmp["total"];
		if ($total == 0) $total = 1;
		$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_campaigns WHERE id <= '".intval($this->migrating_status['campaign-id'])."'", ARRAY_A);
		$done = $tmp["total"];	
		$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ulp_campaigns WHERE id > '".intval($this->migrating_status['campaign-id'])."' ORDER BY id ASC LIMIT 0, 100", ARRAY_A);
		if (sizeof($rows) > 0) {
			foreach ($rows as $row) {
				$slug = $this->_campaign_slug($row['str_id']);
				$sql = "INSERT INTO ".$wpdb->prefix."lepopup_campaigns (
					name, slug, options, active, created, deleted) VALUES (
					'".esc_sql($row['title'])."', '".esc_sql($slug)."', '', '".($row['blocked'] == 1 ? 0 : 1)."', '".time()."', '".esc_sql($row['deleted'])."')";
				$wpdb->query($sql);
				$campaign_id = $wpdb->insert_id;
				$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ulp_campaign_items WHERE campaign_id = '".intval($row['id'])."'", ARRAY_A);
				foreach ($items as $item) {
					if (array_key_exists($item['popup_id'], $this->migrating_status['popup-ids'])) {
						$sql = "INSERT INTO ".$wpdb->prefix."lepopup_campaign_items (
							campaign_id, form_id, impressions, submits, created, deleted) VALUES (
							'".intval($campaign_id)."',
							'".intval($this->migrating_status['popup-ids'][$item['popup_id']])."',
							'".intval($item['impressions'])."', '".intval($item['clicks'])."', '".time()."', '".esc_sql($item['deleted'])."')";
						$wpdb->query($sql);
					}
				}
				
				$this->migrating_status['campaign-slugs'][$row['str_id']] = $slug;
				$this->migrating_status['campaign-ids'][$row['id']] = $campaign_id;
				$this->migrating_status['campaign-id'] = $row['id'];
				$done++;
				$status_output['campaigns'] = array('done' => $done, 'total' => $total);
				update_option('lepopup-migrating-status', json_encode($this->migrating_status));
				$current_time = time();
				if ($current_time - $start_time > 5) {
					$this->migrating_status['start-time'] = 0;
					update_option('lepopup-migrating-status', json_encode($this->migrating_status));
					$return_data = array(
						'status' => 'CONTINUE',
						'data' => $status_output
					);
					if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
					else echo json_encode($return_data);
					exit;
				}
			}
			$this->migrating_status['start-time'] = 0;
			update_option('lepopup-migrating-status', json_encode($this->migrating_status));
			$return_data = array(
				'status' => 'CONTINUE',
				'data' => $status_output
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
			exit;
		} else {
			$status_output['campaigns'] = array('done' => $total, 'total' => $total);
		}

		if (!defined('UAP_CORE')) {
			$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_targets", ARRAY_A);
			$total = $tmp["total"];
			if ($total == 0) $total = 1;
			$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_targets WHERE id <= '".intval($this->migrating_status['target-id'])."'", ARRAY_A);
			$done = $tmp["total"];	
			$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ulp_targets WHERE id > '".intval($this->migrating_status['target-id'])."' ORDER BY id ASC LIMIT 0, 100", ARRAY_A);
			if (sizeof($rows) > 0) {
				foreach ($rows as $row) {
					$ulp_target_options = array('mode' => 'every-time', 'mode_period' => 5, 'delay' => 0, 'close_delay' => 0, 'offset' => 600);
					$unserialized = unserialize($row['options']);
					if (is_array($unserialized)) $ulp_target_options = array_merge($ulp_target_options, $unserialized);
					$ulp_target_taxonomies = array();
					$unserialized = unserialize($row['taxonomies']);
					if (is_array($unserialized)) $ulp_target_taxonomies = array_merge($ulp_target_taxonomies, $unserialized);
					$ulp_target_posts = array();
					$unserialized = unserialize($row['posts']);
					if (is_array($unserialized)) $ulp_target_posts = array_merge($ulp_target_posts, $unserialized);
					$item = "";
					if (array_key_exists($row['popup'], $this->migrating_status['campaign-slugs'])) $item = $this->migrating_status['campaign-slugs'][$row['popup']];
					else if (array_key_exists($row['popup'], $this->migrating_status['popup-slugs'])) $item = $this->migrating_status['popup-slugs'][$row['popup']];
					else if ($row['popup'] == 'same') $item = 'same';
					$item_mobile = "";
					if (array_key_exists($row['popup_mobile'], $this->migrating_status['campaign-slugs'])) $item_mobile = $this->migrating_status['campaign-slugs'][$row['popup_mobile']];
					else if (array_key_exists($row['popup_mobile'], $this->migrating_status['popup-slugs'])) $item_mobile = $this->migrating_status['popup-slugs'][$row['popup_mobile']];
					else if ($row['popup_mobile'] == 'same') $item_mobile = 'same';
					$period = $row['period_enable'] == 0 ? 'always' : 'period';
					$target_options = array(
						'mode' => ($ulp_target_options['mode'] == 'once-session' ? 'once-period' : $ulp_target_options['mode']),
						'mode-period' => intval($ulp_target_options['mode_period'])*24,
						'mode-delay' => $ulp_target_options['delay'],
						'mode-close-delay' => $ulp_target_options['close_delay'],
						'mode-offset' => $ulp_target_options['offset'],
						'taxonomies' => $ulp_target_taxonomies,
						'posts' => $ulp_target_posts,
						'posts-all' => $row['posts_all'] == 1 ? 'on' : 'off'
					);
					$sql = "INSERT INTO ".$wpdb->prefix."lepopup_targets (
						event, item, item_mobile, post_type, period, period_start, period_end, user_roles, language, options, priority, active, created, deleted) VALUES (
						'".esc_sql($row['event'])."',
						'".esc_sql($item)."',
						'".esc_sql($item_mobile)."',
						'".esc_sql($row['post_type'])."',
						'".esc_sql($period)."',
						'".esc_sql($row['period_start'])."',
						'".esc_sql($row['period_end'])."',
						'".esc_sql($row['user_roles'])."',
						'".esc_sql($row['languages'])."',
						'".esc_sql(json_encode($target_options))."',
						'".esc_sql($row['priority'])."',
						'".esc_sql($row['active'])."',
						'".time()."',
						'".esc_sql($row['deleted'])."')";
					$wpdb->query($sql);
					$target_id = $wpdb->insert_id;
					$this->migrating_status['target-ids'][$row['id']] = $target_id;
					$this->migrating_status['target-id'] = $row['id'];
					$done++;
					$status_output['targets'] = array('done' => $done, 'total' => $total);
					update_option('lepopup-migrating-status', json_encode($this->migrating_status));
					$current_time = time();
					if ($current_time - $start_time > 5) {
						$this->migrating_status['start-time'] = 0;
						update_option('lepopup-migrating-status', json_encode($this->migrating_status));
						$return_data = array(
							'status' => 'CONTINUE',
							'data' => $status_output
						);
						if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
						else echo json_encode($return_data);
						exit;
					}
				}
				$this->migrating_status['start-time'] = 0;
				update_option('lepopup-migrating-status', json_encode($this->migrating_status));
				$return_data = array(
					'status' => 'CONTINUE',
					'data' => $status_output
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			} else {
				$status_output['targets'] = array('done' => $total, 'total' => $total);
			}
		}
		
		$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_subscribers", ARRAY_A);
		$total = $tmp["total"];
		if ($total == 0) $total = 1;
		$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_subscribers WHERE id <= '".intval($this->migrating_status['record-id'])."'", ARRAY_A);
		$done = $tmp["total"];	
		$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ulp_subscribers WHERE id > '".intval($this->migrating_status['record-id'])."' ORDER BY id ASC LIMIT 0, 100", ARRAY_A);
		if (sizeof($rows) > 0) {
			foreach ($rows as $row) {
				if (!array_key_exists($row['popup_id'], $this->migrating_status['popup-ids'])) continue;
				if (!array_key_exists($row['popup_id'], $this->migrating_status['popup-fields'])) continue;
				$form_id = $this->migrating_status['popup-ids'][$row['popup_id']];
				$ulp_custom_fields = unserialize($row['custom_fields']);
				$fields = array();
				foreach ($this->migrating_status['popup-fields'][$row['popup_id']] as $shortcode => $id) {
					if ($shortcode == '{subscription-email}') $fields[$id] = $row['email'];
					else if ($shortcode == '{subscription-name}') $fields[$id] = $row['name'];
					else if ($shortcode == '{subscription-phone}') $fields[$id] = $row['phone'];
					else if ($shortcode == '{subscription-message}') $fields[$id] = $row['message'];
					else if (strpos($shortcode, '{custom-field-') !== false && $ulp_custom_fields && is_array($ulp_custom_fields)) {
						preg_match('/{custom-field-([^\1]*)}/i', $shortcode, $field_id);
						if (array_key_exists($field_id[1], $ulp_custom_fields)) {
							$fields[$id] = $ulp_custom_fields[$field_id[1]]['value'];
						}
					}
				}
				$form_info = array();
				if ($ulp_custom_fields && is_array($ulp_custom_fields)) {
					if (array_key_exists('ip', $ulp_custom_fields)) $form_info['ip'] = $ulp_custom_fields['ip']['value'];
					if (array_key_exists('agent', $ulp_custom_fields)) $form_info['user-agent'] = $ulp_custom_fields['agent']['value'];
					if (array_key_exists('url', $ulp_custom_fields)) $form_info['url'] = $ulp_custom_fields['url']['value'];
				}
				$str_id = $lepopup->random_string(24);
				$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_records (form_id, personal_data_keys, unique_keys, fields, info, extra, status, str_id, amount, currency, created, deleted) VALUES (
					'".esc_sql($form_id)."','','','".esc_sql(json_encode($fields))."','".esc_sql(json_encode($form_info))."','".esc_sql(json_encode(array()))."','".esc_sql(LEPOPUP_RECORD_STATUS_NONE)."','".esc_sql($str_id)."','0','USD','".esc_sql($row['created'])."','".esc_sql($row['deleted'])."')");
				$record_id = $wpdb->insert_id;
				
				$this->migrating_status['record-ids'][$row['id']] = $record_id;
				$this->migrating_status['record-id'] = $row['id'];
				$done++;
				$status_output['records'] = array('done' => $done, 'total' => $total);
				update_option('lepopup-migrating-status', json_encode($this->migrating_status));
				$current_time = time();
				if ($current_time - $start_time > 5) {
					$this->migrating_status['start-time'] = 0;
					update_option('lepopup-migrating-status', json_encode($this->migrating_status));
					$return_data = array(
						'status' => 'CONTINUE',
						'data' => $status_output
					);
					if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
					else echo json_encode($return_data);
					exit;
				}
			}
			$this->migrating_status['start-time'] = 0;
			update_option('lepopup-migrating-status', json_encode($this->migrating_status));
			$return_data = array(
				'status' => 'CONTINUE',
				'data' => $status_output
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
			exit;
		} else {
			$status_output['records'] = array('done' => $total, 'total' => $total);
		}

		if (class_exists('ulptabs_class') && class_exists('lepopuptab_class')) {
			$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_tabs", ARRAY_A);
			$total = $tmp["total"];
			if ($total == 0) $total = 1;
			$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_tabs WHERE id <= '".intval($this->migrating_status['tab-id'])."'", ARRAY_A);
			$done = $tmp["total"];
			$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ulp_tabs WHERE id > '".intval($this->migrating_status['tab-id'])."' ORDER BY id ASC LIMIT 0, 100", ARRAY_A);
			if (sizeof($rows) > 0) {
				foreach ($rows as $row) {
					if (array_key_exists($row['popup'], $this->migrating_status['popup-slugs'])) $tab_item = $this->migrating_status['popup-slugs'][$row['popup']];
					else if (array_key_exists($row['popup'], $this->migrating_status['campaign-slugs'])) $tab_item = $this->migrating_status['campaign-slugs'][$row['popup']];
					else continue;
					$slug = $this->_tab_slug($row['str_id']);
					$ulp_options = unserialize($row['options']);
					$tab_options = array_merge($lepopuptab->default_tab_options, array(
						'position' => $ulp_options['position'],
						'backround-color' => $ulp_options['background_color'],
						'backround-image' => $ulp_options['background_url'],
						'label' => $ulp_options['label'],
						'icon-left' => $ulp_options['icon'],
						'text-size' => $ulp_options['font_size'],
						'text-color' => $ulp_options['font_color'],
						'disable-roles' => $ulp_options['disable_roles']
					));
					
					$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_tabs (name, slug, item, options, active, created, deleted)
						VALUES ('".esc_sql($row['title'])."', '".esc_sql($slug)."', '".esc_sql($tab_item)."', '".esc_sql(json_encode($tab_options))."', '".($row['blocked'] == 1 ? 0 : 1)."', '".$row['created']."', '".esc_sql($row['deleted'])."')");
					$tab_id = $wpdb->insert_id;

					$this->migrating_status['tab-slugs'][$row['str_id']] = $slug;
					$this->migrating_status['tab-ids'][$row['id']] = $tab_id;
					$this->migrating_status['tab-id'] = $row['id'];
					$done++;
					$status_output['tabs'] = array('done' => $done, 'total' => $total);
					update_option('lepopup-migrating-status', json_encode($this->migrating_status));
					$current_time = time();
					if ($current_time - $start_time > 5) {
						$this->migrating_status['start-time'] = 0;
						update_option('lepopup-migrating-status', json_encode($this->migrating_status));
						$return_data = array(
							'status' => 'CONTINUE',
							'data' => $status_output
						);
						if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
						else echo json_encode($return_data);
						exit;
					}
				}
				$this->migrating_status['start-time'] = 0;
				update_option('lepopup-migrating-status', json_encode($this->migrating_status));
				$return_data = array(
					'status' => 'CONTINUE',
					'data' => $status_output
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			} else {
				$status_output['tabs'] = array('done' => $total, 'total' => $total);
			}
		}

		if (class_exists('ulpdownload_class') && class_exists('lepopupdownload_class')) {
			$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_downloads", ARRAY_A);
			$total = $tmp["total"];
			if ($total == 0) $total = 1;
			$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."ulp_downloads WHERE id <= '".intval($this->migrating_status['download-id'])."'", ARRAY_A);
			$done = $tmp["total"];
			$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ulp_downloads WHERE id > '".intval($this->migrating_status['download-id'])."' ORDER BY id ASC LIMIT 0, 100", ARRAY_A);
			if (sizeof($rows) > 0) {
				foreach ($rows as $row) {
					if (array_key_exists($row['subscriber_id'], $this->migrating_status['record-ids'])) $record_id = $this->migrating_status['record-ids'][$row['subscriber_id']];
					else $record_id = 0;
					$attributes = unserialize($row['attributes']);
					
					$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_downloads (str_id, record_id, source, file, attributes, downloads, created, deleted)
							VALUES ('".esc_sql($row['str_id'])."', '".intval($record_id)."', '".esc_sql($row['source'])."', '".esc_sql($row['file'])."', '".esc_sql(json_encode($attributes))."', '".intval($row['downloads'])."', '".esc_sql($row['created'])."', '".esc_sql($row['deleted'])."')");
					$download_id = $wpdb->insert_id;

					$rows2 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ulp_downloads_log WHERE download_id = '".esc_sql($row['id'])."' ORDER BY created ASC", ARRAY_A);
					foreach ($rows2 as $row2) {
						$attributes = unserialize($row2['options']);
						$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_downloads_log (download_id, ip, options, created)
							VALUES ('".esc_sql($download_id)."', '".esc_sql($row2['ip'])."', '".esc_sql(json_encode($attributes))."', '".esc_sql($row2['created'])."')");
					}
					$this->migrating_status['download-id'] = $row['id'];
					$done++;
					$status_output['downloads'] = array('done' => $done, 'total' => $total);
					update_option('lepopup-migrating-status', json_encode($this->migrating_status));
					$current_time = time();
					if ($current_time - $start_time > 5) {
						$this->migrating_status['start-time'] = 0;
						update_option('lepopup-migrating-status', json_encode($this->migrating_status));
						$return_data = array(
							'status' => 'CONTINUE',
							'data' => $status_output
						);
						if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
						else echo json_encode($return_data);
						exit;
					}
				}
				$this->migrating_status['start-time'] = 0;
				update_option('lepopup-migrating-status', json_encode($this->migrating_status));
				$return_data = array(
					'status' => 'CONTINUE',
					'data' => $status_output
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			} else {
				$status_output['downloads'] = array('done' => $total, 'total' => $total);
			}
		}

		$return_data = array(
			'status' => 'OK',
			'data' => $status_output
		);
		if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
		else echo json_encode($return_data);
		exit;
	}
	
	function import_layered_popup($_file, $_url = null, $_name = null) {
		global $wpdb, $lepopup;

		if (!file_exists($_file)) {
			return esc_html__('Please make sure that you uploaded a valid popup file.', 'lepopup');
		}
		$lines = file($_file);
		$version = intval(trim($lines[0]));
		if ($version != 1) {
			return esc_html__('Version of the popup file is not supported.', 'lepopup');
		}
		if (sizeof($lines) != 3) {
			return esc_html__('Please make sure that you uploaded valid popup file.', 'lepopup');
		}
		$md5_hash = trim($lines[1]);
		$popup_data = trim($lines[2]);
		$popup_data = base64_decode($popup_data);
		if (!$popup_data || md5($popup_data) != $md5_hash) {
			return esc_html__('Please make sure that you uploaded valid popup file.', 'lepopup');
		}
		$popup = unserialize($popup_data);
		if ($popup === false) {
			return esc_html__('Please make sure that you uploaded valid popup file.', 'lepopup');
		}

		$slug = $lepopup->random_string(16);
		$popup_details = $popup['popup'];
		$layers = $popup['layers'];

		$this->_import($popup_details, $layers, $slug, $_url, $_name);
		return true;
	}
	
	function _import($_popup_details, $_popup_layers, $_slug, $_url = null, $_name = null) {
		global $wpdb, $lepopup;
		$default_popup_options = array(
			"title" => "",
			"width" => "640",
			"height" => "400",
			'position' => 'middle-center',
			'disable_overlay' => 'off',
			"overlay_color" => "#333333",
			"overlay_opacity" => 0.8,
			"overlay_animation" => "fadeIn",
			"ajax_spinner" => "classic",
			"ajax_spinner_color" => "#ffffff",
			"enable_close" => "on",
			"enable_enter" => "on",
			'name_placeholder' => 'Enter your name...',
			'email_placeholder' => 'Enter your e-mail...',
			'phone_placeholder' => 'Enter your phone number...',
			'phone_length' => '',
			'message_placeholder' => 'Enter your message...',
			'email_mandatory' => 'on',
			'name_mandatory' => 'off',
			'phone_mandatory' => 'off',
			'message_mandatory' => 'off',
			'phone_mask' => 'none',
			'phone_custom_mask' => '(000)000-0000',
			'button_label' => 'Subscribe',
			'button_icon' => 'fa-noicon',
			'button_label_loading' => 'Loading...',
			'button_color' => '#0147A3',
			'button_border_radius' => 2,
			'button_gradient' => 'on',
			'button_inherit_size' => 'off',
			'button_css' => '',
			'button_css_hover' => '',
			'input_border_color' => '#444444',
			'input_border_width' => 1,
			'input_border_radius' => 2,
			'input_background_color' => '#FFFFFF',
			'input_background_opacity' => 0.7,
			'input_icons' => 'off',
			'input_css' => '',
			'recaptcha_mandatory' => 'off',
			'recaptcha_theme' => 'light',
			'return_url' => '',
			'close_delay' => 0,
			'thanksgiving_popup' => '',
			'cookie_lifetime' => 360,
			"doubleoptin_enable" => "off",
			"doubleoptin_subject" => "",
			"doubleoptin_message" => "",
			"doubleoptin_confirmation_message" => "",
			"doubleoptin_redirect_url" => "",
			"welcomemail_enable" => "off",
			"welcomemail_subject" => "",
			"welcomemail_message" => "",
			"mail_enable" => "off",
			"mail_email" => "",
			"mail_subject" => "",
			"mail_message" => "",
			"mail_from" => "off"
		);
		$default_layer_options = array(
			"title" => "",
			"content" => "",
			"width" => "",
			"height" => "",
			"scrollbar" => "off",
			"left" => 0,
			"top" => 0,
			"background_color" => "",
			"background_hover_color" => "",
			"background_gradient" => "off",
			"background_gradient_to" => "",
			"background_gradient_angle" => "135",
			"background_hover_gradient_to" => "",
			"background_opacity" => 1,
			"background_image" => "",
			"background_image_repeat" => "repeat",
			"background_image_size" => "auto",
			"border_width" => 1,
			"border_style" => 'none',
			"border_color" => "",
			"border_hover_color" => "",
			"border_radius" => 0,
			"box_shadow" => "off",
			"box_shadow_h" => 0,
			"box_shadow_v" => 5,
			"box_shadow_blur" => 20,
			"box_shadow_spread" => 0,
			"box_shadow_color" => "#202020",
			"box_shadow_inset" => "off",
			"content_align" => "left",
			"padding_v" => 0,
			"padding_h" => 0,
			"index" => 5,
			"appearance" => "fade-in",
			"appearance_delay" => "200",
			"appearance_speed" => "1000",
			"font" => "arial",
			"font_color" => "#000000",
			"font_hover_color" => "",
			"font_weight" => "400",
			"font_size" => 14,
			"text_shadow_size" => 0,
			"text_shadow_color" => "#000000",
			"confirmation_layer" => "off",
			"inline_disable" => "off",
			"style" => ""
		);
		
		$popup_options = unserialize($_popup_details['options']);
		if (is_array($popup_options)) $popup_options = array_merge($default_popup_options, $popup_options);
		else $popup_options = $default_popup_options;

		$global_id = 1;
		$form_options = $lepopup->default_form_options();
		$form_page_options = $lepopup->default_form_options('page');
		if (!empty($_name)) $form_page_options['name'] = $_name;
		else $form_page_options['name'] = $_popup_details['title'];
		$form_page_options['type'] = 'page';
		$form_page_options['id'] = $global_id;
		$global_id++;
		$form_page_options['size-width'] = $popup_options['width'];
		$form_page_options['size-height'] = $popup_options['height'];
		$form_pages[] = $form_page_options;
		$form_page_options = $lepopup->default_form_options('page-confirmation');
		$form_page_options['type'] = 'page-confirmation';
		$form_page_options['id'] = 'confirmation';
		$form_pages[] = $form_page_options;
		if (!empty($_name)) $form_options['name'] = $_name;
		else $form_options['name'] = $_popup_details['title'];
		$form_options['cross-domain'] = 'on';
		$form_options['cookie-lifetime'] = $popup_options['cookie_lifetime'];
		$form_options['esc-enable'] = $popup_options['enable_close'];
		$form_options['enter-enable'] = $popup_options['enable_enter'];
		$form_options['position'] = $popup_options['position'];
		$form_options['overlay-enable'] = $popup_options['disable_overlay'] == 'on' ? 'off' : 'on';
		$form_options['overlay-animation'] = $popup_options['overlay_animation'];
		$color = $lepopup->get_rgb($popup_options['overlay_color']);
		if (!empty($color) && is_array($color)) {
			$form_options['overlay-color'] = 'rgba('.$color['r'].','.$color['g'].','.$color['b'].','.$popup_options['overlay_opacity'].')';
		}
		$form_options['overlay-click'] = $popup_options['enable_close'];
		
		$form_options['button-background-style-color'] = $popup_options['button_color'];
		$form_options['button-border-style-color'] = $popup_options['button_color'];
		if ($popup_options['button_border_radius'] < 3) $form_options['button-border-style-radius'] = '0';
		else if ($popup_options['button_border_radius'] < 5) $form_options['button-border-style-radius'] = '3';
		else if ($popup_options['button_border_radius'] < 10) $form_options['button-border-style-radius'] = '5';
		else if ($popup_options['button_border_radius'] < 18) $form_options['button-border-style-radius'] = '10';
		else $form_options['button-border-style-radius'] = 'max';

		$form_options['input-border-style-width'] = $popup_options['input_border_width'];
		$form_options['input-border-style-color'] = $popup_options['input_border_color'];
		$form_options['checkbox-radio-unchecked-color-color1'] = $popup_options['input_border_color'];
		$color = $lepopup->get_rgb($popup_options['input_background_color']);
		if (!empty($color) && is_array($color)) {
			$form_options['input-background-style-color'] = 'rgba('.$color['r'].','.$color['g'].','.$color['b'].','.$popup_options['input_background_opacity'].')';
			$form_options['checkbox-radio-unchecked-color-color2'] = $form_options['input-background-style-color'];
		} else $form_options['input-background-style-color'] = 'transparent';
		if ($popup_options['input_border_radius'] < 3) $form_options['input-border-style-radius'] = '0';
		else if ($popup_options['input_border_radius'] < 5) $form_options['input-border-style-radius'] = '3';
		else if ($popup_options['input_border_radius'] < 10) $form_options['input-border-style-radius'] = '5';
		else if ($popup_options['input_border_radius'] < 18) $form_options['input-border-style-radius'] = '10';
		else $form_options['input-border-style-radius'] = 'max';

		if ($popup_options['doubleoptin_enable'] == 'on') {
			$form_options['double-enable'] = $popup_options['doubleoptin_enable'];
			$form_options['double-email-subject'] = $popup_options['doubleoptin_subject'];
			$form_options['double-email-message'] = $popup_options['doubleoptin_message'];
			$form_options['double-message'] = $popup_options['doubleoptin_confirmation_message'];
			$form_options['double-url'] = $popup_options['doubleoptin_redirect_url'];
		}

		if ($popup_options['welcomemail_enable'] == 'on') {
			$notification = array(
				'name' => esc_html__('Welcome email', 'lepopup'),
				'enabled' => 'on',
				'action' => 'submit',
				'recipient-email' => '',
				'subject' => $popup_options['welcomemail_subject'],
				'message' => $popup_options['welcomemail_message'],
				'attachments' => array(),
				'reply-email' => '',
				'from-email' => '{{global-from-email}}',
				'from-name' => '{{global-from-name}}',
				'logic-enable' => 'off',
				'logic' => array('rules' => array())
			);
			$form_options['notifications'][] = $notification;
		}

		if ($popup_options['mail_enable'] == 'on') {
			$notification = array(
				'name' => esc_html__('Admin notification', 'lepopup'),
				'enabled' => 'on',
				'action' => 'submit',
				'recipient-email' => $popup_options['mail_email'],
				'subject' => $popup_options['mail_subject'],
				'message' => $popup_options['mail_message'],
				'attachments' => array(),
				'reply-email' => '',
				'from-email' => '{{global-from-email}}',
				'from-name' => '{{global-from-name}}',
				'logic-enable' => 'off',
				'logic' => array('rules' => array())
			);
			$form_options['notifications'][] = $notification;
		}

		$form_elements = array();
		$replaces = array();
		$content_replaces = array(
			'2013 ' => '2020 ',
			'2014 ' => '2020 ',
			'2015 ' => '2020 ',
			'2016 ' => '2020 ',
			'2017 ' => '2020 ',
			'2018 ' => '2020 ',
			' 2013' => ' 2020',
			' 2014' => ' 2020',
			' 2015' => ' 2020',
			' 2016' => ' 2020',
			' 2017' => ' 2020',
			' 2018' => ' 2020',
			'Layered Popups' => 'Green Popups',
			'<a href="#"><i class="fa fa-google-plus-square"></i></a>' => '',
			'fa fa-facebook' => 'fab fa-facebook',
			'fa fa-facebook-square' => 'fab fa-facebook',
			'fa fa-instagram' => 'fab fa-instagram',
			'fa fa-twitter-square' => 'fab fa-twitter',
			'fa fa-twitter' => 'fab fa-twitter',
			'fa fa-tumblr-square' => 'fab fa-tumblr',
			'fa fa-star' => 'fas fa-star',
			'fa fa-map-marker' => 'fas fa-map-marker-alt',
			'fa fa-bars' => 'fas fa-bars',
			'fa fa-star-o' => 'fas fa-star',
			'fa fa-dot-circle-o' => 'far fa-dot-circle',
			'fa fa-youtube' => 'fab fa-youtube',
			'fa fa-lock' => 'fas fa-lock',
			'fa fa-check' => 'fas fa-check',
			'fa fa-user' => 'far fa-user',
			'fa fa-envelope-o' => 'far fa-envelope',
			'fa fa-envelope' => 'fas fa-envelope',
			'fa fa-bar-chart' => 'fas fa-chart-bar',
			'fa fa-tumblr' => 'fab fa-tumblr',
			'ulp-inherited' => 'lepopup-inherited',
			'ulp-link-button' => 'lepopup-inherited',
			'ulp_self_close()' => 'lepopup_close()',
			'ulp_close_forever()' => 'lepopup_close(365)',
		);

		$default_element_options = $lepopup->default_form_options('html');
		$form_element = array(
			"type" => 'html',
			"_parent" => 'confirmation',
			"_seq" => 0, 
			"id" => 0,
			"position-left" => 70,
			"position-top" => 80,
			"size-width" => 280,
			"size-height" => 160,
			"animation-in" => "bounceInDown",
			"animation-out" => "fadeOutUp",
			"padding-top" => 40,
			"padding-right" => 40,
			"padding-bottom" => 40,
			"padding-left" => 40,
			"border-style-radius" => 5,
			"background-style-color" => "#5cb85c",
			"text-style-color" => "#ffffff",
			"shadow-size" => "medium",
			"shadow-color" => "#000000",
			"content" => '<h4 style="text-align: center; font-size: 18px; font-weight: bold;">Thank you!</h4><p style="text-align: center;">We will contact you soon.</p>'
		);
		$form_element = array_merge($default_element_options, $form_element);
		$form_elements[] = json_encode($form_element);
		$field_map = array();
		if (sizeof($_popup_layers) > 0) {
			foreach ($_popup_layers as $layer) {
				if (!empty($layer['title'])) {
					$layer_options = unserialize($layer['details']);
					if (is_array($layer_options)) $layer_options = array_merge($default_layer_options, $layer_options);
					else $layer_options = $default_layer_options;
					if ($layer_options['confirmation_layer'] == 'on') continue;
					if (!empty($layer_options['content'])) $layer['content'] = $layer_options['content'];
					if ($_url) {
						$layer_options['background_image'] = str_replace('ULP-UPLOADS-DIR', $_url, $layer_options['background_image']);
						$layer['content'] = str_replace('ULP-UPLOADS-DIR', $_url, $layer['content']);
					}
					$layer_options['background_image'] = str_replace('ULP-DEMO-IMAGES-URL', $lepopup->plugins_url.'/images/default', $layer_options['background_image']);
					$layer['content'] = str_replace('ULP-DEMO-IMAGES-URL', $lepopup->plugins_url.'/images/default', $layer['content']);
					if (strpos($layer['content'], '{subscription-email}') !== false) {
						$replaces['{subscription-email}'] = '{{'.$global_id.'|'.$layer['title'].'}}';
						$field_map['{subscription-email}'] = $global_id;
						$form_element = $lepopup->default_form_options('email');
						$form_element['type'] = 'email';
						$form_element['resize'] = 'both';
						$form_element['_parent'] = 1;
						$form_element['_seq'] = $layer['zindex'];
						$form_element['id'] = $global_id;
						$form_options['key-fields-primary'] = $global_id;
						
						$global_id++;
						$form_element['name'] = $layer['title'];
						$form_element['position-top'] = $layer_options['top'];
						$form_element['position-left'] = $layer_options['left'];
						$form_element['size-width'] = $layer_options['width'];
						$form_element['size-height'] = $layer_options['height'];
						if (array_key_exists($layer_options['appearance'], $lepopup->animation_effects_in)) $animation_in = $layer_options['appearance'];
						else $animation_in = 'fadeIn';
						$form_element['animation-in'] = $animation_in;
						$form_element['animation-out'] = 'fadeOut';
						$form_element['animation-duration'] = $layer_options['appearance_speed'];
						$form_element['animation-delay'] = $layer_options['appearance_delay'];
						$form_element['placeholder'] = $popup_options['email_placeholder'];
						$form_element['required'] = $popup_options['email_mandatory'];
						$form_element['icon-left-icon'] = '';
						$form_element['align'] = $layer_options['content_align'];
						$form_options['checkbox-radio-unchecked-color-color3'] = $layer_options['font_color'];
						$form_options['input-text-style-color'] = $layer_options['font_color'];
						$form_options['input-text-style-family'] = ucfirst($layer_options['font']);
						$form_options['input-text-style-size'] = $layer_options['font_size'];
						$form_options['double-email-recipient'] = $replaces['{subscription-email}'];
						if ($layer_options['font_weight'] > 400) $form_options['input-text-style-bold'] = 'on';
						if ($layer_options['box_shadow'] == 'on') {
							$form_options['input-shadow-color'] = $layer_options['box_shadow_color'];
							if ($layer_options['box_shadow_inset'] == 'on') $form_options['input-shadow-style'] = 'inset';
							else $form_options['input-shadow-style'] = 'regular';
							$form_options['input-shadow-size'] = 'medium';
						}
						if (!empty($layer_options['style'])) $form_element['css'][] = array('selector' => 'wrapper', 'css' => $layer_options['style']);
						if (!empty($popup_options['input_css'])) $form_element['css'][] = array('selector' => 'input', 'css' => $popup_options['input_css']);
						$form_elements[] = json_encode($form_element);
					} else if (strpos($layer['content'], '{subscription-name}') !== false) {
						$replaces['{subscription-name}'] = '{{'.$global_id.'|'.$layer['title'].'}}';
						$field_map['{subscription-name}'] = $global_id;
						$form_element = $lepopup->default_form_options('text');
						$form_element['type'] = 'text';
						$form_element['resize'] = 'both';
						$form_element['_parent'] = 1;
						$form_element['_seq'] = $layer['zindex'];
						$form_element['id'] = $global_id;
						$form_options['key-fields-secondary'] = $global_id;
						$global_id++;
						$form_element['name'] = $layer['title'];
						$form_element['position-top'] = $layer_options['top'];
						$form_element['position-left'] = $layer_options['left'];
						$form_element['size-width'] = $layer_options['width'];
						$form_element['size-height'] = $layer_options['height'];
						if (array_key_exists($layer_options['appearance'], $lepopup->animation_effects_in)) $animation_in = $layer_options['appearance'];
						else $animation_in = 'fadeIn';
						$form_element['animation-in'] = $animation_in;
						$form_element['animation-out'] = 'fadeOut';
						$form_element['animation-duration'] = $layer_options['appearance_speed'];
						$form_element['animation-delay'] = $layer_options['appearance_delay'];
						$form_element['placeholder'] = $popup_options['name_placeholder'];
						$form_element['required'] = $popup_options['name_mandatory'];
						$form_element['align'] = $layer_options['content_align'];
						if (!empty($layer_options['style'])) $form_element['css'][] = array('selector' => 'wrapper', 'css' => $layer_options['style']);
						if (!empty($popup_options['input_css'])) $form_element['css'][] = array('selector' => 'input', 'css' => $popup_options['input_css']);
						$form_elements[] = json_encode($form_element);
					} else if (strpos($layer['content'], '{subscription-phone}') !== false) {
						$replaces['{subscription-phone}'] = '{{'.$global_id.'|'.$layer['title'].'}}';
						$field_map['{subscription-phone}'] = $global_id;
						$form_element = $lepopup->default_form_options('text');
						$form_element['type'] = 'text';
						$form_element['resize'] = 'both';
						$form_element['_parent'] = 1;
						$form_element['_seq'] = $layer['zindex'];
						$form_element['id'] = $global_id;
						if (empty($form_options['key-fields-primary'])) $form_options['key-fields-primary'] = $global_id;
						else $form_options['key-fields-secondary'] = $global_id;
						$global_id++;
						$form_element['name'] = $layer['title'];
						$form_element['position-top'] = $layer_options['top'];
						$form_element['position-left'] = $layer_options['left'];
						$form_element['size-width'] = $layer_options['width'];
						$form_element['size-height'] = $layer_options['height'];
						if (array_key_exists($layer_options['appearance'], $lepopup->animation_effects_in)) $animation_in = $layer_options['appearance'];
						else $animation_in = 'fadeIn';
						$form_element['animation-in'] = $animation_in;
						$form_element['animation-out'] = 'fadeOut';
						$form_element['animation-duration'] = $layer_options['appearance_speed'];
						$form_element['animation-delay'] = $layer_options['appearance_delay'];
						$form_element['placeholder'] = $popup_options['phone_placeholder'];
						$form_element['required'] = $popup_options['phone_mandatory'];
						$form_element['align'] = $layer_options['content_align'];
						if ($popup_options['phone_mask'] != 'none') {
							$form_element['mask-preset'] = 'custom';
							if ($popup_options['phone_mask'] == 'custom') $form_element['mask-mask'] = $popup_options['phone_custom_mask'];
							else $form_element['mask-mask'] = $popup_options['phone_mask'];
						}
						if (!empty($layer_options['style'])) $form_element['css'][] = array('selector' => 'wrapper', 'css' => $layer_options['style']);
						if (!empty($popup_options['input_css'])) $form_element['css'][] = array('selector' => 'input', 'css' => $popup_options['input_css']);
						$form_elements[] = json_encode($form_element);
					} else if (strpos($layer['content'], '{subscription-message}') !== false) {
						$replaces['{subscription-message}'] = '{{'.$global_id.'|'.$layer['title'].'}}';
						$field_map['{subscription-message}'] = $global_id;
						$form_element = $lepopup->default_form_options('textarea');
						$form_element['type'] = 'textarea';
						$form_element['resize'] = 'both';
						$form_element['_parent'] = 1;
						$form_element['_seq'] = $layer['zindex'];
						$form_element['id'] = $global_id;
						$global_id++;
						$form_element['name'] = $layer['title'];
						$form_element['position-top'] = $layer_options['top'];
						$form_element['position-left'] = $layer_options['left'];
						$form_element['size-width'] = $layer_options['width'];
						$form_element['size-height'] = $layer_options['height'];
						if (array_key_exists($layer_options['appearance'], $lepopup->animation_effects_in)) $animation_in = $layer_options['appearance'];
						else $animation_in = 'fadeIn';
						$form_element['animation-in'] = $animation_in;
						$form_element['animation-out'] = 'fadeOut';
						$form_element['animation-duration'] = $layer_options['appearance_speed'];
						$form_element['animation-delay'] = $layer_options['appearance_delay'];
						$form_element['placeholder'] = $popup_options['message_placeholder'];
						$form_element['required'] = $popup_options['message_mandatory'];
						$form_element['align'] = $layer_options['content_align'];
						if (!empty($layer_options['style'])) $form_element['css'][] = array('selector' => 'wrapper', 'css' => $layer_options['style']);
						if (!empty($popup_options['input_css'])) $form_element['css'][] = array('selector' => 'textarea', 'css' => $popup_options['input_css']);
						$form_elements[] = json_encode($form_element);
					} else if (strpos($layer['content'], '{subscription-submit}') !== false) {
						$form_element = $lepopup->default_form_options('button');
						$form_element['type'] = 'button';
						$form_element['resize'] = 'both';
						$form_element['_parent'] = 1;
						$form_element['_seq'] = $layer['zindex'];
						$form_element['id'] = $global_id;
						$global_id++;
						$form_element['name'] = $layer['title'];
						$form_element['label'] = $popup_options['button_label'];
						$form_element['label-loading'] = $popup_options['button_label_loading'];
						if (empty($form_element['label'])) $form_element['icon-left'] = 'lepopup-if lepopup-if-check';
						$form_element['position-top'] = $layer_options['top'];
						$form_element['position-left'] = $layer_options['left'];
						$form_element['size-width'] = $layer_options['width'];
						$form_element['size-height'] = $layer_options['height'];
						if (array_key_exists($layer_options['appearance'], $lepopup->animation_effects_in)) $animation_in = $layer_options['appearance'];
						else $animation_in = 'fadeIn';
						$form_element['animation-in'] = $animation_in;
						$form_element['animation-out'] = 'fadeOut';
						$form_element['animation-duration'] = $layer_options['appearance_speed'];
						$form_element['animation-delay'] = $layer_options['appearance_delay'];
						$form_options['button-text-style-align'] = $layer_options['content_align'];
						$form_options['button-text-style-color'] = $layer_options['font_color'];
						$form_options['button-text-style-family'] = ucfirst($layer_options['font']);
						$form_options['button-text-style-size'] = $layer_options['font_size'];
						if ($layer_options['font_weight'] > 400) $form_options['button-text-style-bold'] = 'on';
						if ($layer_options['box_shadow'] == 'on') {
							$form_options['button-shadow-color'] = $layer_options['box_shadow_color'];
							if ($layer_options['box_shadow_inset'] == 'on') $form_options['button-shadow-style'] = 'inset';
							else $form_options['button-shadow-style'] = 'regular';
							$form_options['button-shadow-size'] = 'medium';
						}
						if (empty($form_options['button-background-style-color'])) {
							$color = $lepopup->get_rgb($layer_options['background_color']);
							if (!empty($color) && is_array($color)) {
								$form_options['button-background-style-color'] = 'rgba('.$color['r'].','.$color['g'].','.$color['b'].','.$layer_options['background_opacity'].')';
							} else $form_options['button-background-style-color'] = '';
							$color = $lepopup->get_rgb($layer_options['background_gradient_to']);
							if (!empty($color) && is_array($color)) {
								$form_options['button-background-style-color2'] = 'rgba('.$color['r'].','.$color['g'].','.$color['b'].','.$layer_options['background_opacity'].')';
							} else $form_options['button-background-style-color2'] = '';
							if ($layer_options['background_gradient'] == 'on') $form_options['button-background-style-gradient'] = 'horizontal';
						}
						if (intval($layer_options['border_width']) > 0 && $layer_options['border_style'] != 'none' && !empty($layer_options['border_color'])) {
							$form_options['button-border-style-width'] = $layer_options['border_width'];
							$form_options['button-border-style-style'] = $layer_options['border_style'];
							$form_options['button-border-style-color'] = $layer_options['border_color'];
						}
						if (!empty($layer_options['style'])) $form_element['css'][] = array('selector' => 'wrapper', 'css' => $layer_options['style']);
						if (!empty($popup_options['button_css'])) $form_element['css'][] = array('selector' => 'button', 'css' => $popup_options['button_css']);
						if (!empty($popup_options['button_css_hover'])) $form_element['css'][] = array('selector' => 'button-hover', 'css' => $popup_options['button_css_hover']);
						$form_elements[] = json_encode($form_element);
					} else if (strpos($layer['content'], '{custom-field-') !== false) {
						if (array_key_exists('customfields', $popup_options)) {
							$custom_fields = unserialize($popup_options['customfields']);
							if (is_array($custom_fields) && !empty($custom_fields)) {
								preg_match('/{custom-field-([^\1]*)}/i', $layer['content'], $field_id);
								if (is_array($field_id) && !empty($field_id[1]) && array_key_exists($field_id[1], $custom_fields)) {
									switch ($custom_fields[$field_id[1]]['type']) {
										case "input":
											$replaces[$field_id[0]] = '{{'.$global_id.'|'.$custom_fields[$field_id[1]]['name'].'}}';
											$field_map[$field_id[0]] = $global_id;
											$form_element = $lepopup->default_form_options('text');
											$form_element['type'] = 'text';
											$form_element['resize'] = 'both';
											$form_element['_parent'] = 1;
											$form_element['_seq'] = $layer['zindex'];
											$form_element['id'] = $global_id;
											$global_id++;
											$form_element['name'] = $custom_fields[$field_id[1]]['name'];
											$form_element['position-top'] = $layer_options['top'];
											$form_element['position-left'] = $layer_options['left'];
											$form_element['size-width'] = $layer_options['width'];
											$form_element['size-height'] = $layer_options['height'];
											if (array_key_exists($layer_options['appearance'], $lepopup->animation_effects_in)) $animation_in = $layer_options['appearance'];
											else $animation_in = 'fadeIn';
											$form_element['animation-in'] = $animation_in;
											$form_element['animation-out'] = 'fadeOut';
											$form_element['animation-duration'] = $layer_options['appearance_speed'];
											$form_element['animation-delay'] = $layer_options['appearance_delay'];
											$form_element['placeholder'] = $custom_fields[$field_id[1]]['placeholder'];
											$form_element['default'] = $custom_fields[$field_id[1]]['value'];
											$form_element['required'] = $custom_fields[$field_id[1]]['mandatory'];
											$form_element['align'] = $layer_options['content_align'];
											if (!empty($custom_fields[$field_id[1]]['mask'])) {
												$form_element['mask-preset'] = 'custom';
												$form_element['mask-mask'] = $custom_fields[$field_id[1]]['mask'];
											}
											if (!empty($layer_options['style'])) $form_element['css'][] = array('selector' => 'wrapper', 'css' => $layer_options['style']);
											if (!empty($popup_options['input_css'])) $form_element['css'][] = array('selector' => 'input', 'css' => $popup_options['input_css']);
											$form_elements[] = json_encode($form_element);
											break;
										case "textarea":
											$replaces[$field_id[0]] = '{{'.$global_id.'|'.$custom_fields[$field_id[1]]['name'].'}}';
											$field_map[$field_id[0]] = $global_id;
											$form_element = $lepopup->default_form_options('textarea');
											$form_element['type'] = 'textarea';
											$form_element['resize'] = 'both';
											$form_element['_parent'] = 1;
											$form_element['_seq'] = $layer['zindex'];
											$form_element['id'] = $global_id;
											$global_id++;
											$form_element['name'] = $custom_fields[$field_id[1]]['name'];
											$form_element['position-top'] = $layer_options['top'];
											$form_element['position-left'] = $layer_options['left'];
											$form_element['size-width'] = $layer_options['width'];
											$form_element['size-height'] = $layer_options['height'];
											if (array_key_exists($layer_options['appearance'], $lepopup->animation_effects_in)) $animation_in = $layer_options['appearance'];
											else $animation_in = 'fadeIn';
											$form_element['animation-in'] = $animation_in;
											$form_element['animation-out'] = 'fadeOut';
											$form_element['animation-duration'] = $layer_options['appearance_speed'];
											$form_element['animation-delay'] = $layer_options['appearance_delay'];
											$form_element['placeholder'] = $custom_fields[$field_id[1]]['placeholder'];
											$form_element['required'] = $custom_fields[$field_id[1]]['mandatory'];
											$form_element['default'] = $custom_fields[$field_id[1]]['value'];
											$form_element['align'] = $layer_options['content_align'];
											if (!empty($layer_options['style'])) $form_element['css'][] = array('selector' => 'wrapper', 'css' => $layer_options['style']);
											if (!empty($popup_options['input_css'])) $form_element['css'][] = array('selector' => 'textarea', 'css' => $popup_options['input_css']);
											$form_elements[] = json_encode($form_element);
											break;
										case "checkbox":
											$replaces[$field_id[0]] = '{{'.$global_id.'|'.$custom_fields[$field_id[1]]['name'].'}}';
											$field_map[$field_id[0]] = $global_id;
											$form_element = $lepopup->default_form_options('checkbox');
											$form_element['type'] = 'checkbox';
											$form_element['resize'] = 'both';
											$form_element['_parent'] = 1;
											$form_element['_seq'] = $layer['zindex'];
											$form_element['id'] = $global_id;
											$global_id++;
											$form_element['name'] = $custom_fields[$field_id[1]]['name'];
											$form_element['position-top'] = $layer_options['top'];
											$form_element['position-left'] = $layer_options['left'];
											$form_element['size-width'] = 160;
											if (array_key_exists($layer_options['appearance'], $lepopup->animation_effects_in)) $animation_in = $layer_options['appearance'];
											else $animation_in = 'fadeIn';
											$form_element['animation-in'] = $animation_in;
											$form_element['animation-out'] = 'fadeOut';
											$form_element['animation-duration'] = $layer_options['appearance_speed'];
											$form_element['animation-delay'] = $layer_options['appearance_delay'];
											$form_element['required'] = $custom_fields[$field_id[1]]['mandatory'];
											$form_element['options'] = array(array('label' => '', 'value' => 'on', 'default' => $custom_fields[$field_id[1]]['checked']));
											if (!empty($layer_options['style'])) $form_element['css'][] = array('selector' => 'wrapper', 'css' => $layer_options['style']);
											$form_elements[] = json_encode($form_element);
											break;
										case "select":
											$replaces[$field_id[0]] = '{{'.$global_id.'|'.$custom_fields[$field_id[1]]['name'].'}}';
											$field_map[$field_id[0]] = $global_id;
											$form_element = $lepopup->default_form_options('select');
											$form_element['type'] = 'select';
											$form_element['resize'] = 'both';
											$form_element['_parent'] = 1;
											$form_element['_seq'] = $layer['zindex'];
											$form_element['id'] = $global_id;
											$global_id++;
											$form_element['name'] = $custom_fields[$field_id[1]]['name'];
											$form_element['position-top'] = $layer_options['top'];
											$form_element['position-left'] = $layer_options['left'];
											$form_element['size-width'] = $layer_options['width'];
											$form_element['size-height'] = $layer_options['height'];
											if (array_key_exists($layer_options['appearance'], $lepopup->animation_effects_in)) $animation_in = $layer_options['appearance'];
											else $animation_in = 'fadeIn';
											$form_element['animation-in'] = $animation_in;
											$form_element['animation-out'] = 'fadeOut';
											$form_element['animation-duration'] = $layer_options['appearance_speed'];
											$form_element['animation-delay'] = $layer_options['appearance_delay'];
											$form_element['required'] = $custom_fields[$field_id[1]]['mandatory'];
											if (!empty($custom_fields[$field_id[1]]['placeholder'])) {
												$form_element['please-select-option'] = 'on';
												$form_element['please-select-text'] = $custom_fields[$field_id[1]]['placeholder'];
											}
											$form_element['options'] = array();
											$values = explode("\n", $custom_fields[$field_id[1]]['values']);
											foreach ($values as $value) {
												$value = trim($value);
												$form_element['options'][] = array('label' => $value, 'value' => $value, 'default' => $custom_fields[$field_id[1]]['value'] == $value ? 'on' : 'off');
											}
											if (!empty($layer_options['style'])) $form_element['css'][] = array('selector' => 'wrapper', 'css' => $layer_options['style']);
											$form_elements[] = json_encode($form_element);
											break;
										case "date":
											$replaces[$field_id[0]] = '{{'.$global_id.'|'.$custom_fields[$field_id[1]]['name'].'}}';
											$field_map[$field_id[0]] = $global_id;
											$form_element = $lepopup->default_form_options('date');
											$form_element['type'] = 'date';
											$form_element['resize'] = 'both';
											$form_element['_parent'] = 1;
											$form_element['_seq'] = $layer['zindex'];
											$form_element['id'] = $global_id;
											$global_id++;
											$form_element['name'] = $custom_fields[$field_id[1]]['name'];
											$form_element['position-top'] = $layer_options['top'];
											$form_element['position-left'] = $layer_options['left'];
											$form_element['size-width'] = $layer_options['width'];
											$form_element['size-height'] = $layer_options['height'];
											if (array_key_exists($layer_options['appearance'], $lepopup->animation_effects_in)) $animation_in = $layer_options['appearance'];
											else $animation_in = 'fadeIn';
											$form_element['animation-in'] = $animation_in;
											$form_element['animation-out'] = 'fadeOut';
											$form_element['animation-duration'] = $layer_options['appearance_speed'];
											$form_element['animation-delay'] = $layer_options['appearance_delay'];
											$form_element['placeholder'] = $custom_fields[$field_id[1]]['placeholder'];
											$form_element['default-date'] = $custom_fields[$field_id[1]]['value'];
											$form_element['required'] = $custom_fields[$field_id[1]]['mandatory'];
											$form_element['align'] = $layer_options['content_align'];
											if (!empty($layer_options['style'])) $form_element['css'][] = array('selector' => 'wrapper', 'css' => $layer_options['style']);
											if (!empty($popup_options['input_css'])) $form_element['css'][] = array('selector' => 'input', 'css' => $popup_options['input_css']);
											$form_elements[] = json_encode($form_element);
											break;
										default:
											break;
									}
								}
							}
						}
					} else {
						if (empty($layer['content'])) {
							$form_element = $lepopup->default_form_options('rectangle');
							$form_element['type'] = 'rectangle';
						} else {
							$form_element = $lepopup->default_form_options('html');
							$form_element['type'] = 'html';
							$form_element['content'] = strtr($layer['content'], $content_replaces);
							$form_element['scrollable'] = $layer_options['scrollbar'];
							$form_element['text-style-align'] = $layer_options['content_align'];
							$form_element['text-style-color'] = $layer_options['font_color'];
							$form_element['text-style-family'] = ucfirst($layer_options['font']);
							$form_element['text-style-size'] = $layer_options['font_size'];
							if ($layer_options['font_weight'] > 400) $form_element['text-style-bold'] = 'on';
							$form_element['padding-top'] = $layer_options['padding_v'];
							$form_element['padding-bottom'] = $layer_options['padding_v'];
							$form_element['padding-left'] = $layer_options['padding_h'];
							$form_element['padding-right'] = $layer_options['padding_h'];
						}
						$form_element['resize'] = 'both';
						$form_element['_parent'] = 1;
						$form_element['_seq'] = $layer['zindex'];
						$form_element['id'] = $global_id;
						$global_id++;
						$form_element['name'] = $layer['title'];
						$form_element['position-top'] = $layer_options['top'];
						$form_element['position-left'] = $layer_options['left'];
						$form_element['size-width'] = $layer_options['width'];
						$form_element['size-height'] = $layer_options['height'];
						if (array_key_exists($layer_options['appearance'], $lepopup->animation_effects_in)) $animation_in = $layer_options['appearance'];
						else $animation_in = 'fadeIn';
						$form_element['animation-in'] = $animation_in;
						$form_element['animation-out'] = 'fadeOut';
						$form_element['animation-duration'] = $layer_options['appearance_speed'];
						$form_element['animation-delay'] = $layer_options['appearance_delay'];
						$form_element['background-style-image'] = $layer_options['background_image'];
						$form_element['background-style-size'] = $layer_options['background_image_size'];
						$form_element['background-style-repeat'] = $layer_options['background_image_repeat'];
						$color = $lepopup->get_rgb($layer_options['background_color']);
						if (!empty($color) && is_array($color)) {
							$form_element['background-style-color'] = 'rgba('.$color['r'].','.$color['g'].','.$color['b'].','.$layer_options['background_opacity'].')';
						} else $form_element['background-style-color'] = '';
						$color = $lepopup->get_rgb($layer_options['background_gradient_to']);
						if (!empty($color) && is_array($color)) {
							$form_element['background-style-color2'] = 'rgba('.$color['r'].','.$color['g'].','.$color['b'].','.$layer_options['background_opacity'].')';
						} else $form_element['background-style-color2'] = '';
						if ($layer_options['background_gradient'] == 'on') $form_element['background-style-gradient'] = 'horizontal';
						$form_element['border-style-width'] = $layer_options['border_width'];
						$form_element['border-style-style'] = $layer_options['border_style'];
						$color = $lepopup->get_rgb($layer_options['border_color']);
						if (!empty($color) && is_array($color)) {
							$form_element['border-style-color'] = 'rgba('.$color['r'].','.$color['g'].','.$color['b'].','.$layer_options['background_opacity'].')';
						} else $form_element['border-style-color'] = '';
						if ($layer_options['border_radius'] < 3) $form_element['border-style-radius'] = '0';
						else if ($layer_options['border_radius'] < 5) $form_element['border-style-radius'] = '3';
						else if ($layer_options['border_radius'] < 10) $form_element['border-style-radius'] = '5';
						else if ($layer_options['border_radius'] < 18) $form_element['border-style-radius'] = '10';
						else $form_element['border-style-radius'] = 'max';
						if ($layer_options['box_shadow'] == 'on') {
							$form_element['shadow-color'] = $layer_options['box_shadow_color'];
							if ($layer_options['box_shadow_inset'] == 'on') $form_element['shadow-style'] = 'inset';
							else $form_element['shadow-style'] = 'regular';
							$form_element['shadow-size'] = 'medium';
						}
						if (!empty($layer_options['style'])) $form_element['css'][] = array('selector' => 'wrapper', 'css' => $layer_options['style']);
						$form_elements[] = json_encode($form_element);
					}
				}
			}
		}
		$replaces['{confirmation-link}'] = '{{confirmation-url}}';
		$replaces['{ip}'] = '{{ip}}';
		$replaces['{url}'] = '{{url}}';
		if (is_array($form_options['notifications'])) {
			for ($i=0; $i<sizeof($form_options['notifications']); $i++) {
				if (empty($form_options['notifications'][$i]['recipient-email']) && array_key_exists('{subscription-email}', $replaces)) {
					$form_options['notifications'][$i]['recipient-email'] = $replaces['{subscription-email}'];
				}
				$form_options['notifications'][$i]['subject'] = strtr($form_options['notifications'][$i]['subject'], $replaces);
				$form_options['notifications'][$i]['message'] = strtr($form_options['notifications'][$i]['message'], $replaces);
			}
		}
		$form_options['double-email-subject'] = strtr($form_options['double-email-subject'], $replaces);
		$form_options['double-email-message'] = strtr($form_options['double-email-message'], $replaces);
		$form_options['double-message'] = strtr($form_options['double-message'], $replaces);

		$custom_js = array(
			"js_after_open_enable" => "customjs-afterinit-enable",
			"js_after_open" => "customjs-afterinit-script",
			"js_before_submit_enable" => "customjs-beforesubmit-enable",
			"js_before_submit" => "customjs-beforesubmit-script",
			"js_after_submit_success_enable" => "customjs-aftersubmitsuccess-enable",
			"js_after_submit_success" => "customjs-aftersubmitsuccess-script",
			"js_after_close_enable" => "customjs-afterclose-enable",
			"js_after_close" => "customjs-afterclose-script"
		);
		foreach ($custom_js as $key => $param) {
			if (array_key_exists($key, $popup_options)) {
				$js_replaces = array('this.popup_id' => 'this.popup_slug');
				preg_match_all('/this.form\[[\'"]([^\'"]+)[\'"]\]/i', $popup_options[$key], $script);
				foreach ($script[1] as $idx => $var) {
					if (!empty($var)) {
						if ($var == 'name' && array_key_exists('{subscription-name}', $field_map)) $js_replaces[$script[0][$idx]] = 'this.get_field_value('.intval($field_map['{subscription-name}']).')';
						else if ($var == 'email' && array_key_exists('{subscription-email}', $field_map)) $js_replaces[$script[0][$idx]] = 'this.get_field_value('.intval($field_map['{subscription-email}']).')';
						else if ($var == 'phone' && array_key_exists('{subscription-phone}', $field_map)) $js_replaces[$script[0][$idx]] = 'this.get_field_value('.intval($field_map['{subscription-phone}']).')';
						else if ($var == 'message' && array_key_exists('{subscription-message}', $field_map)) $js_replaces[$script[0][$idx]] = 'this.get_field_value('.intval($field_map['{subscription-message}']).')';
						else if (array_key_exists('{'.$var.'}', $field_map)) $js_replaces[$script[0][$idx]] = 'this.get_field_value('.intval($field_map['{'.$var.'}']).')';
					}
				}
				$form_options[$param] = strtr($popup_options[$key], $js_replaces);
			}
		}
		$form_options['integrations'] = $this->_integrations($popup_options, $replaces);
		
		if (array_key_exists('blocked', $_popup_details) && $_popup_details['blocked'] == 0) $form_options['active'] = 'on';
		else $form_options['active'] = 'off';

		$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_forms (name,slug,options,pages,elements,cache_time,active,created,modified,deleted) VALUES (
			'".esc_sql($form_options['name'])."','".esc_sql($_slug)."','".esc_sql(json_encode($form_options))."','".esc_sql(json_encode($form_pages))."','".esc_sql(json_encode($form_elements))."','0','".esc_sql($form_options['active'] == 'on' ? 1 : 0)."','".(empty($_name) ? esc_sql($_popup_details['created']) : time())."','".(empty($_name) ? esc_sql($_popup_details['created']) : time())."','".(array_key_exists('deleted', $_popup_details) && $_popup_details['deleted'] == 1 ? 1 : 0)."')");
		$form_id = $wpdb->insert_id;

		return array('id' => $form_id, 'fields' => $field_map);
	}

	function _integrations($_popup_options, $_replaces) {
		$integrations = array();
		$integration_template = array(
			'name' => '',
			'enabled' => 'on',
			'action' => 'submit',
			'provider' => '',
			'data' => array(),
			'logic-enable' => 'off',
			'logic' => array('action' => 'show', 'operator' => 'and', 'rules' => array())
		);
// Acelle Mail		
		if (array_key_exists('acellemail_enable', $_popup_options) && $_popup_options['acellemail_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Acelle Mail';
			$integration['provider'] = 'acellemail';
			$integration['data'] = array(
				'api-url' => $_popup_options['acellemail_api_url'],
				'token' => $_popup_options['acellemail_api_key'],
				'list' => $_popup_options['acellemail_list'],
				'list-id' => $_popup_options['acellemail_list_id'],
				'fields' => array('EMAIL' => strtr('{subscription-email}', $_replaces))
			);
			if (is_array($_popup_options['acellemail_fields']) && !empty($_popup_options['acellemail_fields'])) {
				foreach ($_popup_options['acellemail_fields'] as $key => $value) {
					if (!empty($value) && $key != 'EMAIL') $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// ActiveCampaign		
		if (array_key_exists('activecampaign_enable', $_popup_options) && $_popup_options['activecampaign_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'ActiveCampaign';
			$integration['provider'] = 'activecampaign';
			$integration['data'] = array(
				'api-url' => $_popup_options['activecampaign_url'],
				'api-key' => $_popup_options['activecampaign_api_key'],
				'list' => $_popup_options['activecampaign_list'],
				'list-id' => $_popup_options['activecampaign_list_id'],
				'fields' => array(
					'email' => strtr('{subscription-email}', $_replaces),
					'first_name' => strtr($_popup_options['activecampaign_firstname'], $_replaces),
					'last_name' => strtr($_popup_options['activecampaign_lastname'], $_replaces),
					'phone' => strtr($_popup_options['activecampaign_phone'], $_replaces),
					'orgname' => strtr($_popup_options['activecampaign_orgname'], $_replaces)
				),
				'tags' => $_popup_options['activecampaign_tags']
			);
			$fields = unserialize($_popup_options['activecampaign_fields']);
			if (is_array($fields) && !empty($fields)) {
				foreach ($fields as $key => $value) {
					if (!empty($value)) $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// ActiveTrail		
		if (array_key_exists('activetrail_enable', $_popup_options) && $_popup_options['activetrail_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'ActiveTrail';
			$integration['provider'] = 'activetrail';
			$integration['data'] = array(
				'api-key' => $_popup_options['activetrail_api_key'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces)),
				'groups' => array()
			);
			$fields = unserialize($_popup_options['activetrail_fields']);
			if (is_array($fields) && !empty($fields)) {
				foreach ($fields as $key => $value) {
					if (!empty($value)) $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$groups = explode(':', $_popup_options['activetrail_groups']);
			foreach ($groups as $group) {
				if (!empty($group)) $integration['data']['groups'][$group] = 'on';
			}
			$integrations[] = $integration;
		}
// AgileCRM		
		if (array_key_exists('agilecrm_enable', $_popup_options) && $_popup_options['agilecrm_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'AgileCRM';
			$integration['provider'] = 'agilecrm';
			$integration['data'] = array(
				'url' => $_popup_options['agilecrm_url'],
				'email' => $_popup_options['agilecrm_email'],
				'api-key' => $_popup_options['agilecrm_api_key'],
				'list' => $_popup_options['agilecrm_list'],
				'list-id' => $_popup_options['agilecrm_list_id'],
				'tags' => $_popup_options['agilecrm_tags'],
				'fields' => array(),
				'custom-names' =>array(),
				'custom-values' =>array()
			);
			if (is_array($_popup_options['agilecrm_fields']) && !empty($_popup_options['agilecrm_fields'])) {
				foreach ($_popup_options['agilecrm_fields'] as $key => $value) {
					$integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			if (is_array($_popup_options['agilecrm_custom_fields']) && !empty($_popup_options['agilecrm_custom_fields'])) {
				foreach ($_popup_options['agilecrm_custom_fields'] as $key => $value) {
					$integration['data']['custom-names'][] = $key;
					$integration['data']['custom-values'][] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// AvangEmail		
		if (array_key_exists('avangemail_enable', $_popup_options) && $_popup_options['avangemail_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'AvangEmail';
			$integration['provider'] = 'avangemail';
			$integration['data'] = array(
				'public-key' => $_popup_options['avangemail_public_key'],
				'private-key' => $_popup_options['avangemail_private_key'],
				'list' => $_popup_options['avangemail_list'],
				'list-id' => $_popup_options['avangemail_list_id'],
				'fields' => array('EMAIL' => strtr('{subscription-email}', $_replaces))
			);
			if (is_array($_popup_options['avangemail_fields']) && !empty($_popup_options['avangemail_fields'])) {
				foreach ($_popup_options['avangemail_fields'] as $key => $value) {
					if (!empty($value)) $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// BirdSend		
		if (array_key_exists('birdsend_enable', $_popup_options) && $_popup_options['birdsend_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'BirdSend';
			$integration['provider'] = 'birdsend';
			$integration['data'] = array(
				'access-token' => $_popup_options['birdsend_access_token'],
				'sequence' => $_popup_options['birdsend_sequence'],
				'sequence-id' => $_popup_options['birdsend_sequence_id'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces)),
				'tags' => $_popup_options['birdsend_tags']
			);
			if (is_array($_popup_options['birdsend_fields']) && !empty($_popup_options['birdsend_fields'])) {
				foreach ($_popup_options['birdsend_fields'] as $key => $value) {
					if (!empty($value) && $key != 'email') $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// Bitrix24		
		if (array_key_exists('bitrix24_enable', $_popup_options) && $_popup_options['bitrix24_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Bitrix24';
			$integration['provider'] = 'bitrix24';
			$integration['data'] = array(
				'api-url' => $_popup_options['bitrix24_url'],
				'fields' => array('EMAIL' => strtr('{subscription-email}', $_replaces))
			);
			if (is_array($_popup_options['bitrix24_fields']) && !empty($_popup_options['bitrix24_fields'])) {
				foreach ($_popup_options['bitrix24_fields'] as $key => $value) {
					if (!empty($value)) $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// Campaign Monitor		
		if (array_key_exists('campaignmonitor_enable', $_popup_options) && $_popup_options['campaignmonitor_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Campaign Monitor';
			$integration['provider'] = 'campaignmonitor';
			$integration['data'] = array(
				'api-key' => $_popup_options['campaignmonitor_api_key'],
				'client' => $_popup_options['campaignmonitor_client'],
				'client-id' => $_popup_options['campaignmonitor_client_id'],
				'list' => $_popup_options['campaignmonitor_list'],
				'list-id' => $_popup_options['campaignmonitor_list_id'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces), 'name' => strtr('{subscription-name}', $_replaces))
			);
			$fields = unserialize($_popup_options['campaignmonitor_fields']);
			if (is_array($fields) && !empty($fields)) {
				foreach ($fields as $key => $value) {
					if (!empty($value) && $key != 'email') $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// CleverReach		
		if (array_key_exists('cleverreach_enable', $_popup_options) && $_popup_options['cleverreach_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'CleverReach';
			$integration['provider'] = 'cleverreach';
			$integration['data'] = array(
				'client-id' => $_popup_options['cleverreach_client_id'],
				'client-secret' => $_popup_options['cleverreach_client_secret'],
				'list' => $_popup_options['cleverreach_list'],
				'list-id' => $_popup_options['cleverreach_list_id'],
				'email' => strtr('{subscription-email}', $_replaces),
				'fields' => array(),
				'global-fields' => array(),
				'tags' => implode(', ', (array)$_popup_options['cleverreach_tags'])
			);
			if (is_array($_popup_options['cleverreach_fields']) && !empty($_popup_options['cleverreach_fields'])) {
				foreach ($_popup_options['cleverreach_fields'] as $key => $value) {
					if (!empty($value)) $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			if (is_array($_popup_options['cleverreach_globalfields']) && !empty($_popup_options['cleverreach_globalfields'])) {
				foreach ($_popup_options['cleverreach_globalfields'] as $key => $value) {
					if (!empty($value)) $integration['data']['global-fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// Constant Contact		
		if (array_key_exists('constantcontact_enable', $_popup_options) && $_popup_options['constantcontact_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Constant Contact';
			$integration['provider'] = 'constantcontact';
			$integration['data'] = array(
				'api-key' => $_popup_options['constantcontact_api_key'],
				'token' => $_popup_options['constantcontact_token'],
				'list' => $_popup_options['constantcontact_list'],
				'list-id' => $_popup_options['constantcontact_list_id'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces))
			);
			$fields = unserialize($_popup_options['constantcontact_fields']);
			if (is_array($fields) && !empty($fields)) {
				foreach ($fields as $key => $value) {
					$integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// Drip		
		if (array_key_exists('drip_enable', $_popup_options) && $_popup_options['drip_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Drip';
			$integration['provider'] = 'drip';
			$integration['data'] = array(
				'api-token' => $_popup_options['drip_api_token'],
				'account' => $_popup_options['drip_account'],
				'account-id' => $_popup_options['drip_account_id'],
				'campaign' => $_popup_options['drip_campaign'],
				'campaign-id' => $_popup_options['drip_campaign_id'],
				'fields' => array(),
				'custom-fields' => array(),
				'tags' => implode(', ', (array)$_popup_options['drip_tags']),
				'eu-consent' => strtr($_popup_options['drip_eu_consent'], $_replaces)
			);
			if (is_array($_popup_options['drip_fields']) && !empty($_popup_options['drip_fields'])) {
				foreach ($_popup_options['drip_fields'] as $key => $value) {
					$integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			if (is_array($_popup_options['drip_custom_fields']) && !empty($_popup_options['drip_custom_fields'])) {
				foreach ($_popup_options['drip_custom_fields'] as $key => $value) {
					if (!empty($value)) $integration['data']['custom-fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// FreshMail		
		if (array_key_exists('freshmail_enable', $_popup_options) && $_popup_options['freshmail_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'FreshMail';
			$integration['provider'] = 'freshmail';
			$integration['data'] = array(
				'api-key' => $_popup_options['freshmail_key'],
				'api-secret' => $_popup_options['freshmail_secret'],
				'list' => $_popup_options['freshmail_listid'],
				'list-id' => $_popup_options['freshmail_listid'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces))
			);
			$integrations[] = $integration;
		}
// GetResponse		
		if (array_key_exists('getresponse_enable', $_popup_options) && $_popup_options['getresponse_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'GetResponse';
			$integration['provider'] = 'getresponse';
			$integration['data'] = array(
				'api-key' => $_popup_options['getresponse_api_key'],
				'campaign' => $_popup_options['getresponse_campaign'],
				'campaign-id' => $_popup_options['getresponse_campaign_id'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces), 'name' => strtr('{subscription-name}', $_replaces))
			);
			$fields = unserialize($_popup_options['getresponse_fields']);
			if (is_array($fields) && !empty($fields)) {
				foreach ($fields as $key => $value) {
					if (!empty($value)) $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// HubSpot		
		if (array_key_exists('hubspot_enable', $_popup_options) && $_popup_options['hubspot_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'HubSpot';
			$integration['provider'] = 'hubspot';
			$integration['data'] = array(
				'api-key' => $_popup_options['hubspot_api_key'],
				'list' => $_popup_options['hubspot_list'],
				'list-id' => $_popup_options['hubspot_list_id'],
				'fields' => array()
			);
			$fields = unserialize($_popup_options['hubspot_fields']);
			if (is_array($fields) && !empty($fields)) {
				foreach ($fields as $key => $value) {
					if (!empty($value)) $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// Jetpack Subscriptions		
		if (array_key_exists('jetpack_enable', $_popup_options) && $_popup_options['jetpack_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Jetpack Subscriptions';
			$integration['provider'] = 'jetpack';
			$integration['data'] = array(
				'fields' => array('email' => strtr('{subscription-email}', $_replaces))
			);
			$integrations[] = $integration;
		}
// Klaviyo		
		if (array_key_exists('klaviyo_enable', $_popup_options) && $_popup_options['klaviyo_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Klaviyo';
			$integration['provider'] = 'klaviyo';
			$integration['data'] = array(
				'api-key' => $_popup_options['klaviyo_api_key'],
				'list' => $_popup_options['klaviyo_list'],
				'list-id' => $_popup_options['klaviyo_list_id'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces)),
				'custom-names' =>array(),
				'custom-values' =>array(),
				'double' => $_popup_options['klaviyo_double']
			);
			if (is_array($_popup_options['klaviyo_fields']) && !empty($_popup_options['klaviyo_fields'])) {
				foreach ($_popup_options['klaviyo_fields'] as $key => $value) {
					$integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			if (is_array($_popup_options['klaviyo_properties']) && !empty($_popup_options['klaviyo_properties'])) {
				foreach ($_popup_options['klaviyo_properties'] as $key => $value) {
					$integration['data']['custom-names'][] = $key;
					$integration['data']['custom-values'][] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// Mad Mimi		
		if (array_key_exists('madmimi_enable', $_popup_options) && $_popup_options['madmimi_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Mad Mimi';
			$integration['provider'] = 'madmimi';
			$integration['data'] = array(
				'username' => $_popup_options['madmimi_login'],
				'api-key' => $_popup_options['madmimi_api_key'],
				'list' => $_popup_options['madmimi_list_id'],
				'list-id' => $_popup_options['madmimi_list_id'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces), 'first_name' => strtr('{subscription-name}', $_replaces)),
				'custom-names' =>array(),
				'custom-values' =>array()
			);
			$integrations[] = $integration;
		}
// Mailautic		
		if (array_key_exists('mailautic_enable', $_popup_options) && $_popup_options['mailautic_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Mailautic';
			$integration['provider'] = 'mailautic';
			$integration['data'] = array(
				'public-key' => $_popup_options['mailautic_public_key'],
				'private-key' => $_popup_options['mailautic_private_key'],
				'list' => $_popup_options['mailautic_list'],
				'list-id' => $_popup_options['mailautic_list_id'],
				'fields' => array('EMAIL' => strtr('{subscription-email}', $_replaces), 'FNAME' => strtr('{subscription-name}', $_replaces))
			);
			if (is_array($_popup_options['mailautic_fields']) && !empty($_popup_options['mailautic_fields'])) {
				foreach ($_popup_options['mailautic_fields'] as $key => $value) {
					if (!empty($value)) $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// MailChimp		
		if (array_key_exists('mailchimp_enable', $_popup_options) && $_popup_options['mailchimp_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'MailChimp';
			$integration['provider'] = 'mailchimp';
			$integration['data'] = array(
				'api-key' => $_popup_options['mailchimp_api_key'],
				'list' => $_popup_options['mailchimp_list'],
				'list-id' => $_popup_options['mailchimp_list_id'],
				'fields' => array('EMAIL' => strtr('{subscription-email}', $_replaces)),
				'groups' => array(),
				'tags' => $_popup_options['mailchimp_tags'],
				'double' => $_popup_options['mailchimp_double']
			);
			$groups = explode(':', $_popup_options['mailchimp_groups']);
			foreach ($groups as $group) {
				if (!empty($group)) $integration['data']['groups'][$group] = 'on';
			}
			$fields = unserialize($_popup_options['mailchimp_fields']);
			if (is_array($fields) && !empty($fields)) {
				foreach ($fields as $key => $value) {
					if (!empty($value)) $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// MailerLite		
		if (array_key_exists('mailerlite_enable', $_popup_options) && $_popup_options['mailerlite_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'MailerLite';
			$integration['provider'] = 'mailerlite';
			$integration['data'] = array(
				'api-key' => $_popup_options['mailerlite_api_key'],
				'list' => $_popup_options['mailerlite_list_id'],
				'list-id' => $_popup_options['mailerlite_list_id'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces), 'name' => strtr('{subscription-name}', $_replaces))
			);
			$integrations[] = $integration;
		}
// MailFit		
		if (array_key_exists('mailfit_enable', $_popup_options) && $_popup_options['mailfit_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'MailFit';
			$integration['provider'] = 'mailfit';
			$integration['data'] = array(
				'api-url' => $_popup_options['mailfit_api_url'],
				'token' => $_popup_options['mailfit_api_key'],
				'list' => $_popup_options['mailfit_list'],
				'list-id' => $_popup_options['mailfit_list_id'],
				'fields' => array('EMAIL' => strtr('{subscription-email}', $_replaces))
			);
			if (is_array($_popup_options['mailfit_fields']) && !empty($_popup_options['mailfit_fields'])) {
				foreach ($_popup_options['mailfit_fields'] as $key => $value) {
					if (!empty($value)) $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// Mailgun		
		if (array_key_exists('mailgun_enable', $_popup_options) && $_popup_options['mailgun_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Mailgun';
			$integration['provider'] = 'mailgun';
			$integration['data'] = array(
				'api-key' => $_popup_options['mailgun_api_key'],
				'region' => $_popup_options['mailgun_region'],
				'list' => $_popup_options['mailgun_list'],
				'list-id' => $_popup_options['mailgun_list_id'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces), 'name' => strtr('{subscription-name}', $_replaces)),
				'custom-names' =>array(),
				'custom-values' =>array()
			);
			if (is_array($_popup_options['mailgun_custom_fields']) && !empty($_popup_options['mailgun_custom_fields'])) {
				foreach ($_popup_options['mailgun_custom_fields'] as $key => $value) {
					$integration['data']['custom-names'][] = $key;
					$integration['data']['custom-values'][] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// Mailjet		
		if (array_key_exists('mailjet_enable', $_popup_options) && $_popup_options['mailjet_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Mailjet';
			$integration['provider'] = 'mailjet';
			$integration['data'] = array(
				'api-key' => $_popup_options['mailjet_api_key'],
				'secret-key' => $_popup_options['mailjet_secret_key'],
				'list' => $_popup_options['mailjet_list'],
				'list-id' => $_popup_options['mailjet_list_id'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces))
			);
			$fields = unserialize($_popup_options['mailjet_fields']);
			if (is_array($fields) && !empty($fields)) {
				foreach ($fields as $key => $value) {
					if (!empty($value)) $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// MailPoet		
		if (array_key_exists('mailpoet_enable', $_popup_options) && $_popup_options['mailpoet_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'MailPoet';
			$integration['provider'] = 'mailpoet';
			$integration['data'] = array(
				'list-id' => $_popup_options['mailpoet_listid'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces))
			);
			if (is_array($_popup_options['mailpoet_fields']) && !empty($_popup_options['mailpoet_fields'])) {
				foreach ($_popup_options['mailpoet_fields'] as $key => $value) {
					if (!empty($value) && $key != 'email') $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// Mailster		
		if (array_key_exists('mymail_enable', $_popup_options) && $_popup_options['mymail_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Mailster';
			$integration['provider'] = 'mailster';
			$integration['data'] = array(
				'list-id' => $_popup_options['mymail_listid'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces), 'firstname' => strtr($_popup_options['mymail_firstname'], $_replaces), 'lastname' => strtr($_popup_options['mymail_lastname'], $_replaces)),
				'double' => $_popup_options['mymail_double']
			);
			if (is_array($_popup_options['mymail_fields']) && !empty($_popup_options['mymail_fields'])) {
				foreach ($_popup_options['mymail_fields'] as $key => $value) {
					if (!empty($value)) $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// MailWizz		
		if (array_key_exists('mailwizz_enable', $_popup_options) && $_popup_options['mailwizz_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'MailWizz';
			$integration['provider'] = 'mailwizz';
			$integration['data'] = array(
				'api-url' => $_popup_options['mailwizz_api_url'],
				'public-key' => $_popup_options['mailwizz_public_key'],
				'private-key' => $_popup_options['mailwizz_private_key'],
				'list' => $_popup_options['mailwizz_list'],
				'list-id' => $_popup_options['mailwizz_list_id'],
				'fields' => array('EMAIL' => strtr('{subscription-email}', $_replaces))
			);
			if (is_array($_popup_options['mailwizz_fields']) && !empty($_popup_options['mailwizz_fields'])) {
				foreach ($_popup_options['mailwizz_fields'] as $key => $value) {
					if (!empty($value)) $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// Mautic		
		if (array_key_exists('mautic_enable', $_popup_options) && $_popup_options['mautic_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Mautic';
			$integration['provider'] = 'mautic';
			$integration['data'] = array(
				'api-url' => $_popup_options['mautic_url'],
				'username' => $_popup_options['mautic_username'],
				'password' => $_popup_options['mautic_password'],
				'owner' => $_popup_options['mautic_owner'],
				'owner-id' => $_popup_options['mautic_owner_id'],
				'segment' => $_popup_options['mautic_segment'],
				'segment-id' => $_popup_options['mautic_segment_id'],
				'campaign' => $_popup_options['mautic_campaign'],
				'campaign-id' => $_popup_options['mautic_campaign_id'],
				'fields' => array()
			);
			if (is_array($_popup_options['mautic_fields']) && !empty($_popup_options['mautic_fields'])) {
				foreach ($_popup_options['mautic_fields'] as $key => $value) {
					if (!empty($value)) $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// Moosend		
		if (array_key_exists('moosend_enable', $_popup_options) && $_popup_options['moosend_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Moosend';
			$integration['provider'] = 'moosend';
			$integration['data'] = array(
				'api-key' => $_popup_options['moosend_api_key'],
				'list' => $_popup_options['moosend_list'],
				'list-id' => $_popup_options['moosend_list_id'],
				'email' => strtr('{subscription-email}', $_replaces),
				'name' => strtr($_popup_options['moosend_name'], $_replaces),
				'fields' => array(),
				'fieldnames' => array()
			);
			if (is_array($_popup_options['moosend_fields']) && !empty($_popup_options['moosend_fields'])) {
				foreach ($_popup_options['moosend_fields'] as $key => $value) {
					$integration['data']['fields'][$key] = strtr($value['value'], $_replaces);
					$integration['data']['fieldnames'][$key] = $value['name'];
				}
			}
			$integrations[] = $integration;
		}
// Mumara
		if (array_key_exists('mumara_enable', $_popup_options) && $_popup_options['mumara_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Mumara';
			$integration['provider'] = 'mumara';
			$integration['data'] = array(
				'api-url' => $_popup_options['mumara_api_url'],
				'api-key' => $_popup_options['mumara_api_token'],
				'list' => $_popup_options['mumara_list'],
				'list-id' => $_popup_options['mumara_list_id'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces))
			);
			if (is_array($_popup_options['mumara_fields']) && !empty($_popup_options['mumara_fields'])) {
				foreach ($_popup_options['mumara_fields'] as $key => $value) {
					if (!empty($value) && $key != 'email') $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// Omnisend		
		if (array_key_exists('omnisend_enable', $_popup_options) && $_popup_options['omnisend_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Omnisend';
			$integration['provider'] = 'omnisend';
			$integration['data'] = array(
				'api-key' => $_popup_options['omnisend_api_key'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces)),
				'custom-names' =>array(),
				'custom-values' =>array()
			);
			if (is_array($_popup_options['omnisend_fields']) && !empty($_popup_options['omnisend_fields'])) {
				foreach ($_popup_options['omnisend_fields'] as $key => $value) {
					$integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			if (is_array($_popup_options['omnisend_custom_fields']) && !empty($_popup_options['omnisend_custom_fields'])) {
				foreach ($_popup_options['omnisend_custom_fields'] as $key => $value) {
					$integration['data']['custom-names'][] = $key;
					$integration['data']['custom-values'][] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// Ontraport
		if (array_key_exists('ontraport_enable', $_popup_options) && $_popup_options['ontraport_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Ontraport';
			$integration['provider'] = 'ontraport';
			$integration['data'] = array(
				'app-id' => $_popup_options['ontraport_app_id'],
				'api-key' => $_popup_options['ontraport_api_key'],
				'tags' => $_popup_options['ontraport_tags'],
				'sequences' => $_popup_options['ontraport_sequences'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces))
			);
			if (is_array($_popup_options['ontraport_fields']) && !empty($_popup_options['ontraport_fields'])) {
				foreach ($_popup_options['ontraport_fields'] as $key => $value) {
					if (!empty($value) && $key != 'email') $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// Rapidmail		
		if (array_key_exists('rapidmail_enable', $_popup_options) && $_popup_options['rapidmail_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Rapidmail';
			$integration['provider'] = 'rapidmail';
			$integration['data'] = array(
				'api-username' => $_popup_options['rapidmail_api_username'],
				'api-password' => $_popup_options['rapidmail_api_password'],
				'list' => $_popup_options['rapidmail_list'],
				'list-id' => $_popup_options['rapidmail_list_id'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces))
			);
			if (is_array($_popup_options['rapidmail_fields']) && !empty($_popup_options['rapidmail_fields'])) {
				foreach ($_popup_options['rapidmail_fields'] as $key => $value) {
					$integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// SalesAutoPilot
		if (array_key_exists('salesautopilot_enable', $_popup_options) && $_popup_options['salesautopilot_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'SalesAutoPilot';
			$integration['provider'] = 'salesautopilot';
			$integration['data'] = array(
				'username' => $_popup_options['salesautopilot_username'],
				'password' => $_popup_options['salesautopilot_password'],
				'list' => $_popup_options['salesautopilot_list'],
				'list-id' => $_popup_options['salesautopilot_list_id'],
				'form' => $_popup_options['salesautopilot_form'],
				'form-id' => $_popup_options['salesautopilot_form_id'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces))
			);
			if (is_array($_popup_options['salesautopilot_fields']) && !empty($_popup_options['salesautopilot_fields'])) {
				foreach ($_popup_options['salesautopilot_fields'] as $key => $value) {
					if (!empty($value) && $key != 'email') $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// SendFox
		if (array_key_exists('sendfox_enable', $_popup_options) && $_popup_options['sendfox_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'SendFox';
			$integration['provider'] = 'sendfox';
			$integration['data'] = array(
				'api-token' => $_popup_options['sendfox_api_token'],
				'list' => $_popup_options['sendfox_list'],
				'list-id' => $_popup_options['sendfox_list_id'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces), 'first_name' => strtr($_popup_options['sendfox_fields']['first_name'], $_replaces), 'last_name' => strtr($_popup_options['sendfox_fields']['last_name'], $_replaces))
			);
			$integrations[] = $integration;
		}
// SendGrid
		if (array_key_exists('sendgrid_enable', $_popup_options) && $_popup_options['sendgrid_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'SendGrid';
			$integration['provider'] = 'sendgrid';
			$integration['data'] = array(
				'api-key' => $_popup_options['sendgrid_api_key'],
				'list' => $_popup_options['sendgrid_list'],
				'list-id' => $_popup_options['sendgrid_list_id'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces), 'first_name' => strtr($_popup_options['sendgrid_first_name'], $_replaces), 'last_name' => strtr($_popup_options['sendgrid_last_name'], $_replaces))
			);
			if (is_array($_popup_options['sendgrid_fields']) && !empty($_popup_options['sendgrid_fields'])) {
				foreach ($_popup_options['sendgrid_fields'] as $key => $value) {
					if (!empty($value) && !in_array($key, array('email', 'first_name', 'last_name'))) $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// SendPulse		
		if (array_key_exists('sendpulse_enable', $_popup_options) && $_popup_options['sendpulse_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'SendPulse';
			$integration['provider'] = 'sendpulse';
			$integration['data'] = array(
				'client-id' => $_popup_options['sendpulse_client_id'],
				'client-secret' => $_popup_options['sendpulse_client_secret'],
				'list' => $_popup_options['sendpulse_list'],
				'list-id' => $_popup_options['sendpulse_list_id'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces)),
				'custom-names' =>array(),
				'custom-values' =>array()
			);
			$fields = unserialize($_popup_options['sendpulse_fields']);
			if (is_array($fields) && !empty($fields)) {
				foreach ($fields as $key => $value) {
					$integration['data']['custom-names'][] = $key;
					$integration['data']['custom-values'][] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// Sendy		
		if (array_key_exists('sendy_enable', $_popup_options) && $_popup_options['sendy_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Sendy';
			$integration['provider'] = 'sendy';
			$integration['data'] = array(
				'url' => $_popup_options['sendy_url'],
				'api-key' => $_popup_options['sendy_api_key'],
				'list-id' => $_popup_options['sendy_listid'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces), 'name' => strtr('{subscription-name}', $_replaces)),
				'custom-names' =>array(),
				'custom-values' =>array()
			);
			$integrations[] = $integration;
		}
// SG Autorepondeur		
		if (array_key_exists('sgautorepondeur_enable', $_popup_options) && $_popup_options['sgautorepondeur_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'SG Autorepondeur';
			$integration['provider'] = 'sgautorepondeur';
			$integration['data'] = array(
				'member-id' => $_popup_options['sgautorepondeur_member_id'],
				'api-key' => $_popup_options['sgautorepondeur_code'],
				'list' => $_popup_options['sgautorepondeur_list'],
				'list-id' => $_popup_options['sgautorepondeur_list_id'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces))
			);
			$fields = unserialize($_popup_options['sgautorepondeur_fields']);
			if (is_array($fields) && !empty($fields)) {
				foreach ($fields as $key => $value) {
					$integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// Tribulant Newsletters		
		if (array_key_exists('tribulant_enable', $_popup_options) && $_popup_options['tribulant_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Tribulant Newsletters';
			$integration['provider'] = 'tribulant';
			$integration['data'] = array(
				'list-id' => $_popup_options['tribulant_listid'],
				'fields' => array('email' => strtr('{subscription-email}', $_replaces))
			);
			$fields = unserialize($_popup_options['tribulant_fields']);
			if (is_array($fields) && !empty($fields)) {
				foreach ($fields as $key => $value) {
					if (!empty($value)) $integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// YMLP		
		if (array_key_exists('ymlp_enable', $_popup_options) && $_popup_options['ymlp_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'Your Mailing List Provider';
			$integration['provider'] = 'ymlp';
			$integration['data'] = array(
				'api-key' => $_popup_options['ymlp_key'],
				'username' => $_popup_options['ymlp_username'],
				'list' => $_popup_options['ymlp_listid'],
				'list-id' => $_popup_options['ymlp_listid'],
				'fields' => array('EMAIL' => strtr('{subscription-email}', $_replaces))
			);
			if (!empty($_popup_options['ymlp_nameid'])) {
				$integration['data']['fields'][$_popup_options['ymlp_nameid']] = strtr('{subscription-name}', $_replaces);
			}
			$integrations[] = $integration;
		}
// WP User
		if (array_key_exists('wpuser_enable', $_popup_options) && $_popup_options['wpuser_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'WP User';
			$integration['provider'] = 'wpuser';
			$integration['data'] = array(
				'role' => $_popup_options['wpuser_role'],
				'notification' => $_popup_options['wpuser_notification_enable'],
				'allow-update' => $_popup_options['wpuser_allow_update'],
				'fields' => array()
			);
			if (is_array($_popup_options['wpuser_fields']) && !empty($_popup_options['wpuser_fields'])) {
				foreach ($_popup_options['wpuser_fields'] as $key => $value) {
					$integration['data']['fields'][$key] = strtr($value, $_replaces);
				}
			}
			$integrations[] = $integration;
		}
// HTML Form
		if (array_key_exists('htmlform_enable', $_popup_options) && $_popup_options['htmlform_enable'] == 'on') {
			$integration = $integration_template;
			$integration['name'] = 'HTML Form';
			$integration['provider'] = 'htmlform';
			$integration['data'] = array(
				'html' => $_popup_options['htmlform_html'],
				'client-side' => $_popup_options['htmlform_clientside'],
				'target' => $_popup_options['htmlform_target'] != 'blank' ? $_popup_options['htmlform_target'] : 'top',
				'action' => '',
				'method' => '',
				'field-names' => array(),
				'field-values' => array()
			);
			if (is_array($_popup_options['htmlform_parsed']) && !empty($_popup_options['htmlform_parsed'])) {
				if (!empty($_popup_options['htmlform_parsed']['action'])) {
					$integration['data']['html'] = base64_encode($_popup_options['htmlform_html']);
					$integration['data']['action'] = $_popup_options['htmlform_parsed']['action'];
					$integration['data']['method'] = $_popup_options['htmlform_parsed']['method'];
					foreach($_popup_options['htmlform_parsed']['fields'] as $field) {
						$integration['data']['field-names'][] = $field['name'];
						$integration['data']['field-values'][] = strtr($field['value'], $_replaces);
					}
				}
			}
			$integrations[] = $integration;
		}



		return $integrations;
	}

	function _popup_slug($_slug) {
		global $wpdb, $lepopup;
		$slug = $_slug;
		$check_slug = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE slug = '".esc_sql($slug)."'", ARRAY_A);
		if (!empty($check_slug)) {
			$base = $slug;
			$pos = strrpos($slug, '-');
			if ($pos !== false) {
				$last_part = substr($slug, $pos+1);
				if (strlen($last_part) == 0 || ctype_digit($last_part)) $base = substr($slug, 0, $pos);
			}
			$base .= '-';
			$rows = $wpdb->get_results("SELECT slug FROM ".$wpdb->prefix."lepopup_forms WHERE slug LIKE '".esc_sql($wpdb->esc_like($base))."%'", ARRAY_A);
			$suffix = 2;
			foreach ($rows as $row) {
				$slug_suffix = str_replace($base, '', $row['slug']);
				if (ctype_digit($slug_suffix) && $slug_suffix >= $suffix) $suffix = $slug_suffix+1;
			}
			$slug = $base.$suffix;
		}
		return $slug;
	}

	function _campaign_slug($_slug) {
		global $wpdb, $lepopup;
		$slug = $_slug;
		$check_slug = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_campaigns WHERE slug = '".esc_sql($slug)."'", ARRAY_A);
		if (!empty($check_slug)) {
			$base = $slug;
			$pos = strrpos($slug, '-');
			if ($pos !== false) {
				$last_part = substr($slug, $pos+1);
				if (strlen($last_part) == 0 || ctype_digit($last_part)) $base = substr($slug, 0, $pos);
			}
			$base .= '-';
			$rows = $wpdb->get_results("SELECT slug FROM ".$wpdb->prefix."lepopup_campaigns WHERE slug LIKE '".esc_sql($wpdb->esc_like($base))."%'", ARRAY_A);
			$suffix = 2;
			foreach ($rows as $row) {
				$slug_suffix = str_replace($base, '', $row['slug']);
				if (ctype_digit($slug_suffix) && $slug_suffix >= $suffix) $suffix = $slug_suffix+1;
			}
			$slug = $base.$suffix;
		}
		return $slug;
	}

	function _tab_slug($_slug) {
		global $wpdb, $lepopup;
		$slug = $_slug;
		$check_slug = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_tabs WHERE slug = '".esc_sql($slug)."'", ARRAY_A);
		if (!empty($check_slug)) {
			$base = $slug;
			$pos = strrpos($slug, '-');
			if ($pos !== false) {
				$last_part = substr($slug, $pos+1);
				if (strlen($last_part) == 0 || ctype_digit($last_part)) $base = substr($slug, 0, $pos);
			}
			$base .= '-';
			$rows = $wpdb->get_results("SELECT slug FROM ".$wpdb->prefix."lepopup_tabs WHERE slug LIKE '".esc_sql($wpdb->esc_like($base))."%'", ARRAY_A);
			$suffix = 2;
			foreach ($rows as $row) {
				$slug_suffix = str_replace($base, '', $row['slug']);
				if (ctype_digit($slug_suffix) && $slug_suffix >= $suffix) $suffix = $slug_suffix+1;
			}
			$slug = $base.$suffix;
		}
		return $slug;
	}
	
}
?>