<?php
/* Advanced Targeting for Green Popups */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
define('LEPOPUP_TARGETS_POSTS_PER_PAGE', 50);
class lepopup_class_targeting {
	var $default_target = array(
		'id' => 0,
		'item' => '',
		'item-mobile' => 'same',
		'options' => array(
			'mode' => 'every-time',
			'mode-period' => 24,
			'delay' => 0,
			'close-delay' => 0,
			'offset' => 600
		),
		'post-type' => 'sitewide',
		'taxonomies' => array(),
		'posts' => array(),
		'posts-all' => true,
		'period' => 'always',
		'period-start' => '',
		'period-end' => '',
		'user-roles' => array()
	);
	var $default_target_options = array(
		'mode' => 'every-time',
		'mode-period' => 24,
		'mode-delay' => 0,
		'mode-close-delay' => 0,
		'mode-offset' => 600,
		'taxonomies' => array(),
		'posts' => array(),
		'posts-all' => 'on',
		'url-keywords' => array()
	);
	
	var $events;
	var $inline_events;
	var $content_targets = array();
	var $geoip_countries = array("AF" => "Afghanistan", "AX" => "Åland Islands", "AL" => "Albania", "DZ" => "Algeria", "AS" => "American Samoa", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AQ" => "Antarctica", "AG" => "Antigua and Barbuda", "AR" => "Argentina", "AM" => "Armenia", "AW" => "Aruba", "AU" => "Australia", "AT" => "Austria", "AZ" => "Azerbaijan", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BY" => "Belarus", "BE" => "Belgium", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BT" => "Bhutan", "BO" => "Bolivia (Plurinational State of)", "BQ" => "Bonaire, Sint Eustatius and Saba", "BA" => "Bosnia and Herzegovina", "BW" => "Botswana", "BV" => "Bouvet Island", "BR" => "Brazil", "IO" => "British Indian Ocean Territory", "BN" => "Brunei Darussalam", "BG" => "Bulgaria", "BF" => "Burkina Faso", "BI" => "Burundi", "KH" => "Cambodia", "CM" => "Cameroon", "CA" => "Canada", "CV" => "Cabo Verde", "KY" => "Cayman Islands", "CF" => "Central African Republic", "TD" => "Chad", "CL" => "Chile", "CN" => "China", "CX" => "Christmas Island", "CC" => "Cocos (Keeling) Islands", "CO" => "Colombia", "KM" => "Comoros", "CG" => "Congo", "CD" => "Congo (Democratic Republic of the)", "CK" => "Cook Islands", "CR" => "Costa Rica", "CI" => "Côte d'Ivoire", "HR" => "Croatia", "CU" => "Cuba", "CW" => "Curaçao", "CY" => "Cyprus", "CZ" => "Czech Republic", "DK" => "Denmark", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Dominican Republic", "EC" => "Ecuador", "EG" => "Egypt", "SV" => "El Salvador", "GQ" => "Equatorial Guinea", "ER" => "Eritrea", "EE" => "Estonia", "ET" => "Ethiopia", "FK" => "Falkland Islands (Malvinas)", "FO" => "Faroe Islands", "FJ" => "Fiji", "FI" => "Finland", "FR" => "France", "GF" => "French Guiana", "PF" => "French Polynesia", "TF" => "French Southern Territories", "GA" => "Gabon", "GM" => "Gambia", "GE" => "Georgia", "DE" => "Germany", "GH" => "Ghana", "GI" => "Gibraltar", "GR" => "Greece", "GL" => "Greenland", "GD" => "Grenada", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GG" => "Guernsey", "GN" => "Guinea", "GW" => "Guinea-Bissau", "GY" => "Guyana", "HT" => "Haiti", "HM" => "Heard Island and McDonald Islands", "VA" => "Holy See", "HN" => "Honduras", "HK" => "Hong Kong", "HU" => "Hungary", "IS" => "Iceland", "IN" => "India", "ID" => "Indonesia", "IR" => "Iran (Islamic Republic of)", "IQ" => "Iraq", "IE" => "Ireland", "IM" => "Isle of Man", "IL" => "Israel", "IT" => "Italy", "JM" => "Jamaica", "JP" => "Japan", "JE" => "Jersey", "JO" => "Jordan", "KZ" => "Kazakhstan", "KE" => "Kenya", "KI" => "Kiribati", "KP" => "Korea (Democratic People's Republic of)", "KR" => "Korea (Republic of)", "KW" => "Kuwait", "KG" => "Kyrgyzstan", "LA" => "Lao People's Democratic Republic", "LV" => "Latvia", "LB" => "Lebanon", "LS" => "Lesotho", "LR" => "Liberia", "LY" => "Libya", "LI" => "Liechtenstein", "LT" => "Lithuania", "LU" => "Luxembourg", "MO" => "Macao", "MK" => "Macedonia, the former Yugoslav Republic of", "MG" => "Madagascar", "MW" => "Malawi", "MY" => "Malaysia", "MV" => "Maldives", "ML" => "Mali", "MT" => "Malta", "MH" => "Marshall Islands", "MQ" => "Martinique", "MR" => "Mauritania", "MU" => "Mauritius", "YT" => "Mayotte", "MX" => "Mexico", "FM" => "Micronesia (Federated States of)", "MD" => "Moldova (Republic of)", "MC" => "Monaco", "MN" => "Mongolia", "ME" => "Montenegro", "MS" => "Montserrat", "MA" => "Morocco", "MZ" => "Mozambique", "MM" => "Myanmar", "NA" => "Namibia", "NR" => "Nauru", "NP" => "Nepal", "NL" => "Netherlands", "NC" => "New Caledonia", "NZ" => "New Zealand", "NI" => "Nicaragua", "NE" => "Niger", "NG" => "Nigeria", "NU" => "Niue", "NF" => "Norfolk Island", "MP" => "Northern Mariana Islands", "NO" => "Norway", "OM" => "Oman", "PK" => "Pakistan", "PW" => "Palau", "PS" => "Palestine, State of", "PA" => "Panama", "PG" => "Papua New Guinea", "PY" => "Paraguay", "PE" => "Peru", "PH" => "Philippines", "PN" => "Pitcairn", "PL" => "Poland", "PT" => "Portugal", "PR" => "Puerto Rico", "QA" => "Qatar", "RE" => "Réunion", "RO" => "Romania", "RU" => "Russian Federation", "RW" => "Rwanda", "BL" => "Saint Barthélemy", "SH" => "Saint Helena, Ascension and Tristan da Cunha", "KN" => "Saint Kitts and Nevis", "LC" => "Saint Lucia", "MF" => "Saint Martin (French part)", "PM" => "Saint Pierre and Miquelon", "VC" => "Saint Vincent and the Grenadines", "WS" => "Samoa", "SM" => "San Marino", "ST" => "Sao Tome and Principe", "SA" => "Saudi Arabia", "SN" => "Senegal", "RS" => "Serbia", "SC" => "Seychelles", "SL" => "Sierra Leone", "SG" => "Singapore", "SX" => "Sint Maarten (Dutch part)", "SK" => "Slovakia", "SI" => "Slovenia", "SB" => "Solomon Islands", "SO" => "Somalia", "ZA" => "South Africa", "GS" => "South Georgia and the South Sandwich Islands", "SS" => "South Sudan", "ES" => "Spain", "LK" => "Sri Lanka", "SD" => "Sudan", "SR" => "Suriname", "SJ" => "Svalbard and Jan Mayen", "SZ" => "Swaziland", "SE" => "Sweden", "CH" => "Switzerland", "SY" => "Syrian Arab Republic", "TW" => "Taiwan (Province of China)", "TJ" => "Tajikistan", "TZ" => "Tanzania, United Republic of", "TH" => "Thailand", "TL" => "Timor-Leste", "TG" => "Togo", "TK" => "Tokelau", "TO" => "Tonga", "TT" => "Trinidad and Tobago", "TN" => "Tunisia", "TR" => "Turkey", "TM" => "Turkmenistan", "TC" => "Turks and Caicos Islands", "TV" => "Tuvalu", "UG" => "Uganda", "UA" => "Ukraine", "AE" => "United Arab Emirates", "GB" => "United Kingdom of Great Britain and Northern Ireland", "US" => "United States of America", "UM" => "United States Minor Outlying Islands", "UY" => "Uruguay", "UZ" => "Uzbekistan", "VU" => "Vanuatu", "VE" => "Venezuela (Bolivarian Republic of)", "VN" => "Viet Nam", "VG" => "Virgin Islands (British)", "VI" => "Virgin Islands (U.S.)", "WF" => "Wallis and Futuna", "EH" => "Western Sahara", "YE" => "Yemen", "ZM" => "Zambia", "ZW" => "Zimbabwe");
	function __construct() {
		global $lepopup;
		$this->events = array(
			'onload' => array(
				'label' => 'OnLoad',
				'description' => esc_html__('Popups are displayed when webpage loaded.', 'lepopup')
			),
			'onscroll' => array(
				'label' => 'OnScroll',
				'description' => esc_html__('Popups are displayed when user scroll down webpage.', 'lepopup')
			),
			'onexit' => array(
				'label' => 'OnExit',
				'description' => esc_html__('Popups are displayed when user moves mouse cursor to top edge of browser window, assuming that he/she is going to leave the page.', 'lepopup')
			),
			'onidle' => array(
				'label' => 'OnInactivity',
				'description' => esc_html__('Popups are displayed when user does nothing on your website (move mouse cursor, press buttons, touch screen) for certain period of time.', 'lepopup')
			)
		);
		if ($lepopup->options['adblock-detector-enable'] == 'on') {
			$this->events['onabd'] = array(
				'label' => 'OnAdblockDetected',
				'description' => esc_html__('Popups are displayed if AdBlock (or similar) software detected.', 'lepopup')
			);
		}
		$this->inline_events = array(
			'inlinepostbegin' => array(
				'label' => 'ContentStart (inline)',
				'description' => esc_html__('Popups are embedded at the beginning of post/page/etc. content.', 'lepopup')
			),
			'inlinepostend' => array(
				'label' => 'ContentEnd (inline)',
				'description' => esc_html__('Popups are embedded at the end of post/page/etc. content.', 'lepopup')
			)
		);
	}
	
	static function activate() {
		global $wpdb;
		$table_name = $wpdb->prefix."lepopup_targets";
		if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
			$sql = "CREATE TABLE ".$table_name." (
				id int(11) NOT NULL auto_increment,
				event varchar(31) collate latin1_general_cs NULL,
				item varchar(255) collate latin1_general_cs NULL,
				item_mobile varchar(255) collate latin1_general_cs NULL,
				post_type varchar(255) collate utf8_unicode_ci NULL,
				period varchar(31) collate latin1_general_cs NULL,
				period_start bigint(20) NULL default '0',
				period_end bigint(20) NULL default '0',
				user_roles varchar(255) collate latin1_general_cs NULL,
				geoip_country varchar(15) collate latin1_general_ci NULL default '',
				geoip_region varchar(255) collate utf8_unicode_ci NULL default '',
				geoip_city varchar(255) collate utf8_unicode_ci NULL default '',
				geoip_zip varchar(255) collate utf8_unicode_ci NULL default '',
				language varchar(255) collate utf8_unicode_ci NULL,
				options longtext collate utf8_unicode_ci NULL,
				priority int(11) NULL default '50',
				active int(11) NULL default '0',
				created int(11) NULL,
				deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
		} else {
			if ($wpdb->get_var("SHOW COLUMNS FROM ".$wpdb->prefix."lepopup_targets LIKE 'geoip_country'") != 'geoip_country') {
				$sql = "ALTER TABLE ".$wpdb->prefix."lepopup_targets ADD geoip_country varchar(15) collate latin1_general_ci NULL default ''";
				$wpdb->query($sql);
			}
			if ($wpdb->get_var("SHOW COLUMNS FROM ".$wpdb->prefix."lepopup_targets LIKE 'geoip_region'") != 'geoip_region') {
				$sql = "ALTER TABLE ".$wpdb->prefix."lepopup_targets ADD geoip_region varchar(255) collate utf8_unicode_ci NULL default ''";
				$wpdb->query($sql);
			}
			if ($wpdb->get_var("SHOW COLUMNS FROM ".$wpdb->prefix."lepopup_targets LIKE 'geoip_city'") != 'geoip_city') {
				$sql = "ALTER TABLE ".$wpdb->prefix."lepopup_targets ADD geoip_city varchar(255) collate utf8_unicode_ci NULL default ''";
				$wpdb->query($sql);
			}
			if ($wpdb->get_var("SHOW COLUMNS FROM ".$wpdb->prefix."lepopup_targets LIKE 'geoip_zip'") != 'geoip_zip') {
				$sql = "ALTER TABLE ".$wpdb->prefix."lepopup_targets ADD geoip_zip varchar(255) collate utf8_unicode_ci NULL default ''";
				$wpdb->query($sql);
			}
		}
	}

	static function deactivate() {
		global $wpdb;
	}
	
	function admin_page() {
		global $wpdb, $lepopup;
		$post_types = get_post_types(array('public' => true), 'names');
		$static_types = array('sitewide', '__url');
		if (get_option('show_on_front') == 'posts') $static_types[] = 'homepage';
		$all_events = array_merge($this->events, $this->inline_events);
		if (array_key_exists('event', $_REQUEST) && array_key_exists($_REQUEST['event'], $all_events)) $event = $_REQUEST['event'];
		else $event = 'onload';
		$language_filter = '';
		if (defined('ICL_LANGUAGE_CODE')) {
			if (ICL_LANGUAGE_CODE != 'all') $language_filter = " AND t1.language IN ('all', '".esc_sql(ICL_LANGUAGE_CODE)."')";
		}
		$rows = $wpdb->get_results("SELECT t1.*, t2.name as form_name, t3.name as form_mobile_name, t4.name as campaign_name, t5.name as campaign_mobile_name FROM ".$wpdb->prefix."lepopup_targets t1 LEFT JOIN ".$wpdb->prefix."lepopup_forms t2 ON t2.slug = t1.item AND t2.deleted = '0' LEFT JOIN ".$wpdb->prefix."lepopup_forms t3 ON t3.slug = t1.item_mobile AND t3.deleted = '0' LEFT JOIN ".$wpdb->prefix."lepopup_campaigns t4 ON t4.slug = t1.item AND t4.deleted = '0' LEFT JOIN ".$wpdb->prefix."lepopup_campaigns t5 ON t5.slug = t1.item_mobile AND t5.deleted = '0' WHERE t1.deleted = '0' AND t1.active = '1' AND t1.event = '".esc_sql($event)."' AND t1.post_type IN ('".implode("','", $static_types)."','".implode("','", $post_types)."')".$language_filter." ORDER BY t1.priority ASC", ARRAY_A);
		echo '
<div class="wrap lepopup-admin">
	<h2>'.esc_html__('Green Popups - Targeting', 'lepopup').'
		<a class="lepopup-button-h2" href="#" onclick="lepopup_target_properties_open(\''.$event.'\', 0); return false;">'.esc_html__('Create New Target', 'lepopup').'</a>
		<a href="'.(defined('UAP_CORE') ? 'https://greenpopups.com/documentation/#standalone-script' : 'https://greenpopups.com/documentation/#wordpress-plugin').'" class="lepopup-button-h2" target="_blank">'.esc_html__('Online Documentation', 'lepopup').'</a>
	</h2>
	<div class="lepopup-targets-events">';
		foreach ($all_events as $key => $value) {
			echo '
		<a class="lepopup-targets-event-item'.($key == $event ? ' lepopup-targets-event-item-selected' : '').'" href="'.admin_url('admin.php').'?page=lepopup-targeting'.($key == 'onload' ? '' : '&event='.$key).'" title="'.esc_html($value['description']).'"><i class="far '.($key == $event ? 'fa-dot-circle' : 'fa-circle').'"></i> '.esc_html($value['label']).'</a>';
		}
		echo '
	</div>
	<div class="lepopup-options lepopup-targets-page">
		<h2>'.esc_html__('Active Targets', 'lepopup').'</h2>
		<div class="lepopup-targets-list" id="lepopup-targets-list-active">
			<div class="lepopup-targets-noitems-message" style="'.(sizeof($rows) > 0 ? ' display: none;' : ' display: block;').'">'.sprintf(esc_html__('Drop existing target here or %screate%s new one.', 'lepopup'), '<a href="#" onclick="return lepopup_target_properties_open(\''.$event.'\', 0);">', '</a>').'</div>';
		foreach($rows as $row) {
			$filter_html = $this->get_list_item_html($row);
			echo $filter_html;
		}
		echo '
		</div>
	</div>';
		$rows = $wpdb->get_results("SELECT t1.*, t2.name as form_name, t3.name as form_mobile_name, t4.name as campaign_name, t5.name as campaign_mobile_name FROM ".$wpdb->prefix."lepopup_targets t1 LEFT JOIN ".$wpdb->prefix."lepopup_forms t2 ON t2.slug = t1.item AND t2.deleted = '0' LEFT JOIN ".$wpdb->prefix."lepopup_forms t3 ON t3.slug = t1.item_mobile AND t3.deleted = '0' LEFT JOIN ".$wpdb->prefix."lepopup_campaigns t4 ON t4.slug = t1.item AND t4.deleted = '0' LEFT JOIN ".$wpdb->prefix."lepopup_campaigns t5 ON t5.slug = t1.item_mobile AND t5.deleted = '0' WHERE t1.deleted = '0' AND t1.active = '0' AND t1.event = '".esc_sql($event)."' AND t1.post_type IN ('".implode("','", $static_types)."','".implode("','", $post_types)."')".$language_filter." ORDER BY t1.created DESC", ARRAY_A);
		echo '
	<div class="lepopup-options lepopup-targets-page">
		<h2>'.esc_html__('Passive Targets', 'lepopup').'</h2>
		<div class="lepopup-targets-list" id="lepopup-targets-list-passive">
			<div class="lepopup-targets-noitems-message" style="'.(sizeof($rows) > 0 ? ' display: none;' : ' display: block;').'">'.esc_html__('Drop existing target here to disable it.', 'lepopup').'</div>';
		foreach($rows as $row) {
			$filter_html = $this->get_list_item_html($row);
			echo $filter_html;
		}
		echo '
		</div>
	</div>
</div>
<div id="lepopup-global-message"></div>';
		echo $this->_admin_dialog_html();
		echo '
<div class="lepopup-admin-popup-overlay" id="lepopup-target-properties-overlay" onclick="return lepopup_target_properties_close();"></div>
<div class="lepopup-admin-popup" id="lepopup-target-properties">
	<div class="lepopup-admin-popup-inner">
		<div class="lepopup-admin-popup-title">
			<a href="#" title="'.esc_html__('Close', 'lepopup').'" onclick="return lepopup_target_properties_close();"><i class="fas fa-times"></i></a>
			<h3><i class="fas fa-cog"></i> '.esc_html__('Target Properties', 'lepopup').'<span></span></h3>
		</div>
		<div class="lepopup-admin-popup-content">
			<div class="lepopup-admin-popup-content-form">
			</div>
		</div>
		<div class="lepopup-admin-popup-buttons">
			<a class="lepopup-admin-button" href="#" onclick="return lepopup_target_save(this);"><i class="fas fa-check"></i><label>'.esc_html__('Save Details', 'lepopup').'</label></a>
		</div>
		<div class="lepopup-admin-popup-loading"><i class="fas fa-spinner fa-spin"></i></div>
	</div>
</div>
<input type="hidden" id="lepopup-targets-event" value="'.esc_html($event).'">
<script>jQuery(document).ready(function(){lepopup_tragets_ready();});</script>';
	}
	
	function _admin_dialog_html() {
		return '
<div class="lepopup-dialog-overlay" id="lepopup-dialog-overlay"></div>
<div class="lepopup-dialog" id="lepopup-dialog">
	<div class="lepopup-dialog-inner">
		<div class="lepopup-dialog-title">
			<a href="#" title="'.esc_html__('Close', 'lepopup').'" onclick="return lepopup_dialog_close();"><i class="fas fa-times"></i></a>
			<h3><i class="fas fa-cog"></i><label></label></h3>
		</div>
		<div class="lepopup-dialog-content">
			<div class="lepopup-dialog-content-html">
			</div>
		</div>
		<div class="lepopup-dialog-buttons">
			<a class="lepopup-dialog-button lepopup-dialog-button-ok" href="#" onclick="return false;"><i class="fas fa-check"></i><label></label></a>
			<a class="lepopup-dialog-button lepopup-dialog-button-cancel" href="#" onclick="return false;"><i class="fas fa-times"></i><label></label></a>
		</div>
		<div class="lepopup-dialog-loading"><i class="fas fa-spinner fa-spin"></i></div>
	</div>
</div>';
	}

	function admin_target_properties() {
		global $wpdb, $lepopup;
		$return_data = array();
		$callback = '';
		if (array_key_exists('callback', $_REQUEST)) {
			header("Content-type: text/javascript");
			$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
		}
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$target_options = $this->default_target_options;
			$target_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_targets WHERE deleted = '0' AND id = '".intval($_REQUEST['lepopup-id'])."'", ARRAY_A);
			if ($target_details) {
				$target_options_decoded = json_decode($target_details['options'], true);
				if ($target_options_decoded) $target_options = array_merge($target_options, $target_options_decoded);
			}
			$all_events = array_merge($this->events, $this->inline_events);
			if (array_key_exists('lepopup-event', $_REQUEST) && array_key_exists($_REQUEST['lepopup-event'], $all_events)) $event = $_REQUEST['lepopup-event'];
			else $event = 'onload';
			$html = '
		<form class="lepopup-target-properties-form" enctype="multipart/form-data" method="post" style="margin: 0px" action="'.admin_url('admin.php').'">
			<input type="hidden" name="action" value="lepopup-target-save"><input type="hidden" name="lepopup-event" value="'.esc_html($event).'">'.(!empty($target_details['id']) ? '<input type="hidden" id="lepopup-id" name="lepopup-id" value="'.intval($target_details['id']).'">' : '').'
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label"><label>'.esc_html__('Popup or A/B Campaign', 'lepopup').'</label></div>
				<div class="lepopup-properties-tooltip"><i class="fas fa-question-circle lepopup-tooltip-anchor tooltipstered"></i><div class="lepopup-tooltip-content">'.esc_html__('Select popup or A/B Campaign for your target.', 'lepopup').'</div></div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-content-half">
						<select id="lepopup-item" name="lepopup-item">';
			$forms = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' ORDER BY name ASC", ARRAY_A);
			$checked = false;
			if (sizeof($forms) > 0) {
				$html .= '
							<option disabled="disabled">--------- '.esc_html__('Popups', 'lepopup').' ---------</option>';
				foreach($forms as $form) {
					if ($target_details['item'] == $form['slug']) {
						$checked = true;
						$html .= '
							<option value="'.$form['slug'].'" selected="selected">'.esc_html($form['name']).($form['active'] != 1 ? ' '.esc_html__('[inactive]', 'lepopup') : '').'</option>';
					} else {
						$html .= '
							<option value="'.$form['slug'].'">'.esc_html($form['name']).($form['active'] != 1 ? ' '.esc_html__('[inactive]', 'lepopup') : '').'</option>';
					}
				}
			}
			if (!in_array($event, array('inlinepostbegin', 'inlinepostend'))) {
				$campaigns = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_campaigns WHERE deleted = '0' ORDER BY name ASC", ARRAY_A);
				if (sizeof($campaigns) > 0) {
					$html .= '
							<option disabled="disabled">--------- '.esc_html__('A/B Campaigns', 'lepopup').' ---------</option>';
					foreach($campaigns as $campaign) {
						if ($target_details['item'] == $campaign['slug']) {
							$checked = true;
							$html .= '
							<option value="'.$campaign['slug'].'" selected="selected">'.esc_html($campaign['name']).($campaign['active'] != 1 ? ' '.esc_html__('[inactive]', 'lepopup') : '').'</option>';
						} else {
							$html .= '
							<option value="'.$campaign['slug'].'">'.esc_html($campaign['name']).($campaign['active'] != 1 ? ' '.esc_html__('[inactive]', 'lepopup') : '').'</option>';
						}
					}
				}
			}
			if (sizeof($forms) > 0 || sizeof($campaigns) > 0) {
				$html .= '
							<option disabled="disabled">------------------</option>';
			}
			$html .= '
							<option value=""'.(!$checked ? ' selected="selected"' : '').'>'.esc_html__('None (disabled)', 'lepopup').'</option>
						</select>
						<label>'.esc_html__('For desktops', 'lepopup').'</label>
					</div>';
			if ($event != 'onexit') {
				$html .= '
					<div class="lepopup-properties-content-half">
						<select id="lepopup-item-mobile" name="lepopup-item-mobile">';
				$checked = false;
				if (sizeof($forms) > 0) {
					$html .= '
								<option disabled="disabled">--------- '.esc_html__('Popups', 'lepopup').' ---------</option>';
					foreach($forms as $form) {
						if ($target_details['item_mobile'] == $form['slug']) {
							$checked = true;
							$html .= '
							<option value="'.$form['slug'].'" selected="selected">'.esc_html($form['name']).($form['active'] != 1 ? ' '.esc_html__('[inactive]', 'lepopup') : '').'</option>';
						} else {
							$html .= '
							<option value="'.$form['slug'].'">'.esc_html($form['name']).($form['active'] != 1 ? ' '.esc_html__('[inactive]', 'lepopup') : '').'</option>';
						}
					}
				}
				if (!in_array($event, array('inlinepostbegin', 'inlinepostend'))) {			
					if (sizeof($campaigns) > 0) {
						$html .= '
							<option disabled="disabled">--------- '.esc_html__('A/B Campaigns', 'lepopup').' ---------</option>';
						foreach($campaigns as $campaign) {
							if ($target_details['item_mobile'] == $campaign['slug']) {
								$checked = true;
								$html .= '
							<option value="'.$campaign['slug'].'" selected="selected">'.esc_html($campaign['name']).($campaign['active'] != 1 ? ' '.esc_html__('[inactive]', 'lepopup') : '').'</option>';
							} else {
								$html .= '
							<option value="'.$campaign['slug'].'">'.esc_html($campaign['name']).($campaign['active'] != 1 ? ' '.esc_html__('[inactive]', 'lepopup') : '').'</option>';
							}
						}
					}
				}
				if (sizeof($forms) > 0 || sizeof($campaigns) > 0) {
					$html .= '
							<option disabled="disabled">------------------</option>';
				}
				if ($target_details['item_mobile'] == 'same') {
					$checked = true;
					$html .= '
							<option value="same" selected="selected">'.esc_html__('Same as for desktops', 'lepopup').'</option>';
				} else {
					$html .= '
							<option value="same">'.esc_html__('Same as for desktops', 'lepopup').'</option>';
				}
				$html .= '
							<option value=""'.(!$checked ? ' selected="selected"' : '').'>'.esc_html__('None (disabled)', 'lepopup').'</option>
						</select>
						<label>'.esc_html__('For mobile devices', 'lepopup').'</label>
					</div>';
			}
			$html .= '
				</div>
			</div>';
			if (array_key_exists($event, $this->events)) {
				$html .= '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label"><label>'.esc_html__('Periodicity', 'lepopup').'</label></div>
				<div class="lepopup-properties-tooltip"><i class="fas fa-question-circle lepopup-tooltip-anchor tooltipstered"></i><div class="lepopup-tooltip-content">'.esc_html__('Specify how often you want to see the popup.', 'lepopup').'</div></div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-content-dime">
						<div class="lepopup-bar-selector">
							<input type="radio" value="every-time" name="lepopup-mode" id="lepopup-mode-every-time"'.($target_options['mode'] == 'every-time' ? ' checked="checked"' : '').' onchange="jQuery(\'#lepopup-properties-content-period\').fadeOut(300);"><label for="lepopup-mode-every-time">'.esc_html__('Every time', 'lepopup').'</label><input type="radio" value="once-period" name="lepopup-mode" id="lepopup-mode-once-period"'.($target_options['mode'] == 'once-period' ? ' checked="checked"' : '').' onchange="jQuery(\'#lepopup-properties-content-period\').fadeIn(300);"><label for="lepopup-mode-once-period">'.esc_html__('Once per period', 'lepopup').'</label><input type="radio" value="once-only" name="lepopup-mode" id="lepopup-mode-once-only"'.($target_options['mode'] == 'once-only' ? ' checked="checked"' : '').' onchange="jQuery(\'#lepopup-properties-content-period\').fadeOut(300);"><label for="lepopup-mode-once-only">'.esc_html__('Once only', 'lepopup').'</label>
						</div>
						<label>'.esc_html__('Periodicity mode', 'lepopup').'</label>
					</div>
					<div class="lepopup-properties-content-dime lepopup-input-units lepopup-input-hrs" id="lepopup-properties-content-period"'.($target_options['mode'] != 'once-period' ? ' style="display:none;"' : '').'>
						<div class="lepopup-number"><input type="text" class="lepopup-ta-right" name="lepopup-mode-period" id="lepopup-mode-period" value="'.esc_html($target_options['mode-period']).'" placeholder="..." /></div><label>'.esc_html__('Period (hours)', 'lepopup').'</label>
					</div>
					<div class="lepopup-properties-content-9dimes">
					</div>
				</div>
			</div>';
				switch ($event) {
					case 'onload':
						$html .= '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label"><label>'.esc_html__('Start delay', 'lepopup').'</label></div>
				<div class="lepopup-properties-tooltip"><i class="fas fa-question-circle lepopup-tooltip-anchor tooltipstered"></i><div class="lepopup-tooltip-content">'.esc_html__('Popup appears with this delay after page loaded. Set "0" for immediate start.', 'lepopup').'</div></div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-content-dime lepopup-input-units lepopup-input-sec">
						<div class="lepopup-number"><input type="text" class="lepopup-ta-right" name="lepopup-mode-delay" id="lepopup-mode-delay" value="'.esc_html($target_options['mode-delay']).'" placeholder="..." /></div><label>'.esc_html__('Seconds', 'lepopup').'</label>
					</div>
					<div class="lepopup-properties-content-9dimes">
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label"><label>'.esc_html__('Close delay', 'lepopup').'</label></div>
				<div class="lepopup-properties-tooltip"><i class="fas fa-question-circle lepopup-tooltip-anchor tooltipstered"></i><div class="lepopup-tooltip-content">'.esc_html__('Popup is automatically closed after this period of time. Set "0", if you do not need autoclosing.', 'lepopup').'</div></div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-content-dime lepopup-input-units lepopup-input-sec">
						<div class="lepopup-number"><input type="text" class="lepopup-ta-right" name="lepopup-mode-close-delay" id="lepopup-mode-close-delay" value="'.esc_html($target_options['mode-close-delay']).'" placeholder="..." /></div><label>'.esc_html__('Seconds', 'lepopup').'</label>
					</div>
					<div class="lepopup-properties-content-9dimes">
					</div>
				</div>
			</div>';
						break;
					case 'onscroll':
						$onscroll_units = 'px';
						if (strpos($target_options['mode-offset'], '%') !== false) {
							$onscroll_units = '%';
							$target_options['mode-offset'] = intval($target_options['mode-offset']);
							if ($target_options['mode-offset'] > 100) $target_options['mode-offset'] = 100;
						}
						$html .= '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label"><label>'.esc_html__('Scrolling offset', 'lepopup').'</label></div>
				<div class="lepopup-properties-tooltip"><i class="fas fa-question-circle lepopup-tooltip-anchor tooltipstered"></i><div class="lepopup-tooltip-content">'.esc_html__('Popup appears when user scroll down to this number of pixels or percents.', 'lepopup').'</div></div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-content-dime">
						<div class="lepopup-number"><input type="text" class="lepopup-ta-right" name="lepopup-mode-offset" id="lepopup-onscroll-offset" value="'.esc_html($target_options['mode-offset']).'" placeholder="..." /></div>
						<label>'.esc_html__('Offset', 'lepopup').'</label>
					</div>
					<div class="lepopup-properties-content-dime">
						<div class="lepopup-bar-selector">
							<input type="radio" value="" name="lepopup-onscroll-unit" id="lepopup-onscroll-unit-pixels"'.($onscroll_units == 'px' ? ' checked="checked"' : '').' onchange="lepopup_target_onscroll_units_changed();"><label for="lepopup-onscroll-unit-pixels">px</label><input type="radio" value="%" name="lepopup-onscroll-unit" id="lepopup-onscroll-unit-percents"'.($onscroll_units == '%' ? ' checked="checked"' : '').' onchange="lepopup_target_onscroll_units_changed();"><label for="lepopup-onscroll-unit-percents">%</label>
						</div>
						<label>'.esc_html__('Units', 'lepopup').'</label>
					</div>
					<div class="lepopup-properties-content-9dimes">
					</div>
				</div>
			</div>';
						break;
					case 'onidle':
						$html .= '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label"><label>'.esc_html__('Inactivity delay', 'lepopup').'</label></div>
				<div class="lepopup-properties-tooltip"><i class="fas fa-question-circle lepopup-tooltip-anchor tooltipstered"></i><div class="lepopup-tooltip-content">'.esc_html__('Popup appears after this period of inactivity.', 'lepopup').'</div></div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-content-dime lepopup-input-units lepopup-input-sec">
						<div class="lepopup-number"><input type="text" class="lepopup-ta-right" name="lepopup-mode-delay" id="lepopup-mode-delay" value="'.esc_html($target_options['mode-delay']).'" placeholder="..." /></div><label>'.esc_html__('Seconds', 'lepopup').'</label>
					</div>
					<div class="lepopup-properties-content-9dimes">
					</div>
				</div>
			</div>';
						break;
					default:
						break;
				}
				if ($lepopup->advanced_options['async-init'] == 'on') {
					$period = !empty($target_details) ? $target_details['period'] : 'always';
					$html .= '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label"><label>'.esc_html__('Activity', 'lepopup').'</label></div>
				<div class="lepopup-properties-tooltip"><i class="fas fa-question-circle lepopup-tooltip-anchor tooltipstered"></i><div class="lepopup-tooltip-content">'.esc_html__('Specify when you want to see the popup.', 'lepopup').'</div></div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-content-dime">
						<div class="lepopup-bar-selector">
							<input type="radio" value="always" name="lepopup-period" id="lepopup-period-always"'.($period == 'always' ? ' checked="checked"' : '').' onchange="jQuery(\'.lepopup-properties-content-date\').fadeOut(300);"><label for="lepopup-period-always">'.esc_html__('Always', 'lepopup').'</label><input type="radio" value="period" name="lepopup-period" id="lepopup-period-period"'.($period == 'period' ? ' checked="checked"' : '').' onchange="jQuery(\'.lepopup-properties-content-date\').fadeIn(300);"><label for="lepopup-period-period">'.esc_html__('Certain period', 'lepopup').'</label>
						</div>
						<label>'.esc_html__('Activity', 'lepopup').'</label>
					</div>
					<div class="lepopup-properties-content-third lepopup-properties-content-date lepopup-date"'.($period == 'always' ? ' style="display:none;"' : '').'>
						<input type="text" class="lepopup-target-period-date" name="lepopup-period-start" id="lepopup-period-start" value="'.(!empty($target_details) ? esc_html($lepopup->datetime_string($target_details['period_start'])) : '').'" /><label>'.esc_html__('From', 'lepopup').'</label><span><i class="far fa-calendar-alt"></i></span>
					</div>
					<div class="lepopup-properties-content-third lepopup-properties-content-date lepopup-date"'.($period == 'always' ? ' style="display:none;"' : '').'>
						<input type="text" class="lepopup-target-period-date" name="lepopup-period-end" id="lepopup-period-end" value="'.(!empty($target_details) ? esc_html($lepopup->datetime_string($target_details['period_end'])) : '').'" /><label>'.esc_html__('To', 'lepopup').'</label><span><i class="far fa-calendar-alt"></i></span>
					</div>
					<div class="lepopup-properties-content-third">
					</div>
				</div>
			</div>';
					$roles = get_editable_roles();
					$keys = array_keys($roles);
					if ($target_details) {
						$tmp = trim($target_details['user_roles'], '{}');
						$user_roles = explode('}{', $tmp);
					} else $user_roles = array();
					$selected_roles = array_intersect($user_roles, $keys);
					$visitor_selected = in_array('visitor', $user_roles);
					$html .= '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label"><label>'.esc_html__('User roles', 'lepopup').'</label></div>
				<div class="lepopup-properties-tooltip"><i class="fas fa-question-circle lepopup-tooltip-anchor tooltipstered"></i><div class="lepopup-tooltip-content">'.esc_html__('Popup appears for selected user roles.', 'lepopup').'</div></div>
				<div class="lepopup-properties-content">
					<input oninput="if(jQuery(this).is(\':checked\')){jQuery(\'.lepopup-target-userrole\').prop(\'checked\', false).prop(\'disabled\', true);}else{jQuery(\'.lepopup-target-userrole\').prop(\'disabled\', false);}" type="checkbox" class="lepopup-target-checkbox" id="lepopup-userrole-all" name="lepopup-userroles[]" value="all"'.(!$visitor_selected && empty($selected_roles) ? ' checked="checked"' : '').' />
					<label for="lepopup-userrole-all">'.esc_html__('All User Roles', 'lepopup').'</label>
					<input type="checkbox" class="lepopup-target-checkbox lepopup-target-userrole" id="lepopup-userrole-visitor" name="lepopup-userroles[]" value="visitor"'.($visitor_selected ? ' checked="checked"' : '').(!$visitor_selected && empty($selected_roles) ? ' disabled="disabled"' : '').' />
					<label for="lepopup-userrole-visitor">'.esc_html__('Non-registered Visitor', 'lepopup').'</label>';
					foreach ($roles as $key => $value) {
						$html .= '
					<input type="checkbox" class="lepopup-target-checkbox lepopup-target-userrole" id="lepopup-userrole-'.esc_html($key).'" name="lepopup-userroles[]" value="'.esc_html($key).'"'.(in_array($key, $selected_roles) ? ' checked="checked"' : '').(!$visitor_selected && empty($selected_roles) ? ' disabled="disabled"' : '').' />
					<label for="lepopup-userrole-'.esc_html($key).'">'.esc_html($value['name']).'</label>';
					}
					$html .= '
				</div>
			</div>';
					$geoip_params = apply_filters('lepopup_geoip_params_'.$lepopup->options['geoip-service'], array());
					if (!empty($geoip_params)) {
						$country_options = '<option value=""'.(empty($target_details) || $target_details['geoip_country'] == '' ? ' selected="selected"' : '').'>'.esc_html__('All countries', 'lepopup').'</option>';
						foreach ($this->geoip_countries as $key => $name) {
							$country_options .= '<option value="'.esc_html($key).'"'.(!empty($target_details) && $target_details['geoip_country'] == $key ? ' selected="selected"' : '').'>'.esc_html($name).'</option>';
						}
						$html .= '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label"><label>'.esc_html__('GeoIP filter', 'lepopup').'</label></div>
				<div class="lepopup-properties-tooltip"><i class="fas fa-question-circle lepopup-tooltip-anchor tooltipstered"></i><div class="lepopup-tooltip-content">'.esc_html__('Specify GeoIP filter for this target. Use latin alphabet.', 'lepopup').'</div></div>
				<div class="lepopup-properties-content">
					'.(in_array('country', $geoip_params) ? '<div class="lepopup-properties-content-quarter">
						<select class="lepopup-target-input" name="lepopup-geoip-country" id="lepopup-geoip-country">'.$country_options.'</select>
						<label>'.esc_html__('Country', 'lepopup').'</label>
					</div>
					' : '').(in_array('region', $geoip_params) ? '<div class="lepopup-properties-content-quarter">
						<input type="text" class="lepopup-target-input" name="lepopup-geoip-region" id="lepopup-geoip-region" value="'.(!empty($target_details) ? esc_html($target_details['geoip_region']) : '').'" />
						<label>'.esc_html__('Region', 'lepopup').'</label>
					</div>
					' : '').(in_array('city', $geoip_params) ? '<div class="lepopup-properties-content-quarter">
						<input type="text" class="lepopup-target-input" name="lepopup-geoip-city" id="lepopup-geoip-city" value="'.(!empty($target_details) ? esc_html($target_details['geoip_city']) : '').'" />
						<label>'.esc_html__('City', 'lepopup').'</label>
					</div>
					' : '').(in_array('zip', $geoip_params) ? '<div class="lepopup-properties-content-quarter">
						<input type="text" class="lepopup-target-input" name="lepopup-geoip-zip" id="lepopup-geoip-zip" value="'.(!empty($target_details) ? esc_html($target_details['geoip_zip']) : '').'" />
						<label>'.esc_html__('ZIP / postal code', 'lepopup').'</label>
					</div>' : '').'
				</div>
			</div>';
					}
				}
				if (!empty($target_details)) $post_type = $target_details['post_type'];
				else $post_type = 'sitewide';
			} else {
				if (!empty($target_details)) $post_type = $target_details['post_type'];
				else $post_type = 'post';
			}
			$html .= '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label"><label>'.esc_html__('Site area', 'lepopup').'</label></div>
				<div class="lepopup-properties-tooltip"><i class="fas fa-question-circle lepopup-tooltip-anchor tooltipstered"></i><div class="lepopup-tooltip-content">'.esc_html__('Specify the site area where to display the popup.', 'lepopup').'</div></div>
				<div class="lepopup-properties-content" id="lepopup-target-post-types">';
			$post_types = get_post_types(array('public' => true), 'objects');
			if (!array_key_exists($event, $this->inline_events)) {
				$html .= '
					<input type="radio" class="lepopup-target-radio lepopup-target-post-type" id="lepopup-post-type-sitewide" name="lepopup-post-type" value="sitewide"'.($post_type == 'sitewide' ? ' checked="checked"' : '').' oninput="return lepopup_target_post_type_selected(this);" />
					<label for="lepopup-post-type-sitewide">'.esc_html__('Sitewide', 'lepopup').'</label>';
			}
			if (get_option('show_on_front') == 'posts' && !array_key_exists($event, $this->inline_events)) {
				$html .= '
					<input type="radio" class="lepopup-target-radio lepopup-target-post-type" id="lepopup-post-type-homepage" name="lepopup-post-type" value="homepage"'.($post_type == 'homepage' ? ' checked="checked"' : '').' oninput="return lepopup_target_post_type_selected(this);" />
					<label for="lepopup-post-type-homepage">'.esc_html__('Homepage', 'lepopup').'</label>';
			}
			foreach ($post_types as $key => $p_type) {
				if ($key != 'attachment') {
					$html .= '
					<input type="radio" class="lepopup-target-radio lepopup-target-post-type" id="lepopup-post-type-'.esc_html($key).'" name="lepopup-post-type" value="'.esc_html($key).'"'.($post_type == $key ? ' checked="checked"' : '').' oninput="return lepopup_target_post_type_selected(this);" />
					<label for="lepopup-post-type-'.esc_html($key).'">'.esc_html($p_type->label).'</label>';
				}
			}
			if (!array_key_exists($event, $this->inline_events)) {
				$html .= '
					<input type="radio" class="lepopup-target-radio lepopup-target-post-type" id="lepopup-post-type-__url" name="lepopup-post-type" value="__url"'.($post_type == '__url' ? ' checked="checked"' : '').' oninput="return lepopup_target_post_type_selected(this);" />
					<label for="lepopup-post-type-__url">'.esc_html__('URL', 'lepopup').'</label>';
			}
			$html .= '
				</div>
			</div>';

			if (!array_key_exists($event, $this->inline_events)) {
				$url_keywords = array_map('esc_html', $target_options['url-keywords']);
				$html .= '
			<div id="lepopup-target-content-url-keywords"'.($post_type == '__url' ? '' : ' style="display:none;"').'>
				<div class="lepopup-properties-item">
					<div class="lepopup-properties-label"><label>'.esc_html__('URL keywords', 'lepopup').'</label></div>
					<div class="lepopup-properties-tooltip"><i class="fas fa-question-circle lepopup-tooltip-anchor tooltipstered"></i><div class="lepopup-tooltip-content">'.esc_html__('Set the list of URL-encoded keywords (one per line). If URL contains these keywords, the target is activated.', 'lepopup').'</div></div>
					<div class="lepopup-properties-content">
						<textarea class="lepopup-target-input" name="lepopup-url-keywords" id="lepopup-url-keywords">'.implode("\r\n", $url_keywords).'</textarea></div>
					</div>
				</div>
			</div>';
			}
			$taxonomies_html = $this->get_taxonomies_html($event, $post_type, $target_options);
			
			$html .= '
			<div id="lepopup-target-content-taxonomies">'.$taxonomies_html.'</div>
			<div id="lepopup-target-content-loading"><i class="fas fa-spinner fa-spin"></i></div>
			<div id="lepopup-target-content-errors"></div>
		</form>';
			
			$return_data = array(
				'status' => 'OK',
				'html' => $html
			);
		} else {
			$return_data = array(
				'status' => 'ERROR',
				'message' => esc_html__('You do not have enough priveleges to perform this action.', 'lepopup')
			);
		}
		if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
		else echo json_encode($return_data);
		exit;
	}
	
	function admin_target_taxonomies() {
		global $wpdb, $lepopup;
		$return_data = array();
		$callback = '';
		if (array_key_exists('callback', $_REQUEST)) {
			header("Content-type: text/javascript");
			$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
		}
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$target_options = $this->default_target_options;
			$post_types = get_post_types(array('public' => true));
			$static_types = array('sitewide', '__url');
			if (get_option('show_on_front') == 'posts') $static_types[] = 'homepage';
			if (array_key_exists('lepopup-post-type', $_REQUEST) && (in_array($_REQUEST['lepopup-post-type'], $post_types) || in_array($_REQUEST['lepopup-post-type'], $static_types))) $post_type = $_REQUEST['lepopup-post-type'];
			else $post_type = 'sitewide';

			$all_events = array_merge($this->events, $this->inline_events);
			if (array_key_exists('lepopup-event', $_REQUEST) && array_key_exists($_REQUEST['lepopup-event'], $all_events)) $event = $_REQUEST['lepopup-event'];
			else $event = 'onload';
			
			$html = $this->get_taxonomies_html($event, $post_type, $target_options);
			
			$return_data = array(
				'status' => 'OK',
				'html' => $html
			);
		} else {
			$return_data = array(
				'status' => 'ERROR',
				'message' => esc_html__('You do not have enough priveleges to perform this action.', 'lepopup')
			);
		}
		if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
		else echo json_encode($return_data);
		exit;
	}
	function get_taxonomies_html($_event, $_post_type, $_target_options) {
		global $wpdb, $lepopup;
		$html = '';
		$target_options = array_merge($this->default_target_options, $_target_options);
		if ($_post_type == 'sitewide' || $_post_type == 'homepage' || $_post_type == '__url') return '';
		
		$taxonomies = get_object_taxonomies($_post_type, 'object');
		foreach ($taxonomies as $key => $taxonomy) {
			if (!$taxonomy->public) continue;
			if ($key == 'post_format') continue;
			$selected_terms = array();
			if (array_key_exists($key, $target_options['taxonomies']) && is_array($target_options['taxonomies'][$key])) $selected_terms = $target_options['taxonomies'][$key];
			$selected = false;
			$terms = get_terms($key, array('hide_empty' => false));
			if (!empty($selected_terms)) {
				foreach ($terms as $term) {
					if (in_array($term->slug, $selected_terms)) {
						$selected = true;
						break;
					}
				}
			}
			$html .= '
			<div class="lepopup-properties-item lepopup-target-taxonomies'.(!$selected ? ' lepopup-target-disabled' : '').'">
				<div class="lepopup-properties-label"><label>'.esc_html($taxonomy->label).'</label></div>
				<div class="lepopup-properties-tooltip"><i class="fas fa-question-circle lepopup-tooltip-anchor tooltipstered"></i><div class="lepopup-tooltip-content">'.esc_html__('Taxonomy:', 'lepopup').' '.esc_html($taxonomy->label).'</div></div>
				<div class="lepopup-properties-content">
					<input oninput="if(jQuery(this).is(\':checked\')){jQuery(\'.lepopup-target-taxonomy-'.esc_html($key).'\').prop(\'checked\', false).prop(\'disabled\', true);}else{jQuery(\'.lepopup-target-taxonomy-'.esc_html($key).'\').prop(\'disabled\', false);}lepopup_target_taxonomy_selected(this, \''.esc_html($key).'\');" type="checkbox" class="lepopup-target-checkbox" id="lepopup-target-taxonomy-'.esc_html($key).'-all" name="lepopup-taxonomy-'.esc_html($key).'[]" value="all"'.(!$selected ? ' checked="checked"' : '').' />
					<label for="lepopup-target-taxonomy-'.esc_html($key).'-all">'.esc_html($taxonomy->labels->all_items).'</label>';
			$i = 0;
			foreach ($terms as $term) {
				$html .= '
					<input oninput="lepopup_target_taxonomy_selected(this, \''.esc_html($key).'\');" type="checkbox" class="lepopup-target-checkbox lepopup-target-taxonomy-'.esc_html($key).'" id="lepopup-target-taxonomy-'.esc_html($key).'-'.$i.'" name="lepopup-taxonomy-'.esc_html($key).'[]" value="'.esc_html($term->slug).'"'.(in_array($term->slug, $selected_terms) ? ' checked="checked"' : '').(!$selected ? ' disabled="disabled"' : '').' />
					<label for="lepopup-target-taxonomy-'.esc_html($key).'-'.$i.'">'.esc_html($term->name).'</label>';
				$i++;
			}
				
			$html .= '
				</div>
			</div>';
			if (!array_key_exists($_event, $this->inline_events)) {
				$archive_enable = array_key_exists('archive-enable-'.$key, $target_options['taxonomies']) ? $target_options['taxonomies']['archive-enable-'.$key] : 'off';
				$html .= '
			<div class="lepopup-properties-item lepopup-target-taxonomies'.(!$selected ? ' lepopup-target-disabled' : '').'">
				<div class="lepopup-properties-label"></div>
				<div class="lepopup-properties-tooltip"></div>
				<div class="lepopup-properties-content">
					<input type="checkbox" class="lepopup-target-checkbox3" id="lepopup-target-taxonomy-archive-enable-'.esc_html($key).'" name="lepopup-taxonomy-archive-enable-'.esc_html($key).'" value="on"'.($archive_enable == 'on' ? ' checked="checked"' : '').' /><label for="lepopup-target-taxonomy-archive-enable-'.esc_html($key).'"></label>
					<label for="lepopup-target-taxonomy-'.esc_html($key).'-archive-enable">'.esc_html__('Enable popup for archive pages.', 'lepopup').'</label>
				</div>
			</div>';
			}
		}
		$post_type = get_post_type_object($_post_type);
		$posts_data = $this->get_posts_html($_post_type, $target_options, 0);
		$html .= '
			<input type="hidden" id="lepopup-target-next-offset" value="'.$posts_data['next-offset'].'">
			<div class="lepopup-properties-item lepopup-target-taxonomies lepopup-target-posts">
				<div class="lepopup-properties-label"><label>'.esc_html($post_type->labels->name).'</label></div>
				<div class="lepopup-properties-tooltip"><i class="fas fa-question-circle lepopup-tooltip-anchor tooltipstered"></i><div class="lepopup-tooltip-content">'.esc_html($post_type->labels->name).'</div></div>
				<div class="lepopup-properties-content">
					<input oninput="if(jQuery(this).is(\':checked\')){jQuery(\'.lepopup-target-post\').prop(\'checked\', false).prop(\'disabled\', true);}else{jQuery(\'.lepopup-target-post\').prop(\'disabled\', false);}" type="checkbox" class="lepopup-target-checkbox" id="lepopup-target-post-all" name="lepopup-posts-all" value="on"'.($target_options['posts-all'] == 'on' ? ' checked="checked"' : '').' />
					<label for="lepopup-target-post-all">'.sprintf(esc_html__('All %s', 'lepopup'), (function_exists('mb_strtolower') ? mb_strtolower($post_type->labels->name) : strtolower($post_type->labels->name))).'</label>
					<div id="lepopup-target-posts-container">
						<div id="lepopup-target-content-posts">'.$posts_data['html'].'</div>
					</div>
				</div>
			</div>';
		return $html;
	}
	function admin_target_posts() {
		global $wpdb, $lepopup;
		$return_data = array();
		$callback = '';
		if (array_key_exists('callback', $_REQUEST)) {
			header("Content-type: text/javascript");
			$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
		}
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			if (array_key_exists('lepopup-offset', $_REQUEST)) $offset = intval($_REQUEST['lepopup-offset']);
			else $offset = 0;
			if ($offset < 0) {
				$return_data = array('status' => 'OK', 'html' => '');
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			$target_options = $this->default_target_options;
			if (array_key_exists('lepopup-id', $_REQUEST)) {
				$target_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_targets WHERE deleted = '0' AND id = '".intval($_REQUEST['lepopup-id'])."'", ARRAY_A);
				if ($target_details) {
					$target_options_decoded = json_decode($target_details['options'], true);
					if ($target_options_decoded) $target_options = array_merge($target_options, $target_options_decoded);
				}
			}
			$post_types = get_post_types(array('public' => true));
			$static_types = array('sitewide', '__url');
			if (get_option('show_on_front') == 'posts') $static_types[] = 'homepage';
			if (array_key_exists('lepopup-post-type', $_REQUEST) && (in_array($_REQUEST['lepopup-post-type'], $post_types) || in_array($_REQUEST['lepopup-post-type'], $static_types))) $post_type = $_REQUEST['lepopup-post-type'];
			else $post_type = 'sitewide';
			$target_options['taxonomies'] = array();
			$taxonomies = get_object_taxonomies($post_type, 'object');
			foreach ($taxonomies as $key => $taxonomy) {
				if (!$taxonomy->public) continue;
				if ($key == 'post_format') continue;
				$terms = get_terms($key, array('hide_empty' => false));
				foreach ($terms as $term) {
					if (array_key_exists('lepopup-taxonomy-'.$key, $_REQUEST) && is_array($_REQUEST['lepopup-taxonomy-'.$key]) && in_array($term->slug, $_REQUEST['lepopup-taxonomy-'.$key])) {
						$target_options['taxonomies'][$key][] = $term->slug;
					}
				}
			}
			if (array_key_exists('lepopup-posts-all', $_REQUEST) && $_REQUEST['lepopup-posts-all'] == 'on') $target_options['posts-all'] = 'on';
			else $target_options['posts-all'] = 'off';
			
			$posts_data = $this->get_posts_html($post_type, $target_options, $offset);
			
			$return_data = array(
				'status' => 'OK',
				'html' => $posts_data['html'],
				'next_offset' => $posts_data['next-offset']
			);
		} else {
			$return_data = array(
				'status' => 'ERROR',
				'message' => esc_html__('You do not have enough priveleges to perform this action.', 'lepopup')
			);
		}
		if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
		else echo json_encode($return_data);
		exit;
	}
	
	function get_posts_html($_post_type, $_target_options, $_offset = 0) {
		global $wpdb, $lepopup;
		$html = '';
		$target_options = array_merge($this->default_target_options, $_target_options);
		if ($_post_type == 'sitewide' || $_post_type == 'homepage' || $_post_type == '__url') return '';
		$args = array(
			'post_type' => $_post_type,
			'order' => 'DESC',
			'orderby' => 'date'
		);
		$taxonomies = get_object_taxonomies($_post_type, 'object');
		foreach ($taxonomies as $key => $taxonomy) {
			if (!$taxonomy->public) continue;
			if ($key == 'post_format') continue;
			$tax_query = array(
				'taxonomy' => $key,
				'field' => 'slug',
				'terms' => array()
			);
			$terms = get_terms($key, array('hide_empty' => false));
			foreach ($terms as $term) {
				if  (array_key_exists($key, $target_options['taxonomies']) && is_array($target_options['taxonomies'][$key]) && in_array($term->slug, $target_options['taxonomies'][$key])) {
					$tax_query['terms'][] = $term->slug;
				}
			}
			if (sizeof($tax_query['terms']) > 0) $args['tax_query'][] = $tax_query;
		}
		$posts_found = false;
		$next_offset = -1;
		if ($_offset == 0) {
			if (sizeof($target_options['posts']) > 0) {
				$args['post__in'] = $target_options['posts'];
				$args['nopaging'] = true;
				$query = new WP_Query($args);
				foreach ($query->posts as $post) {
					$html .= '
			<div class="lepopup-target-posts-item">
				<input type="checkbox" class="lepopup-target-checkbox2 lepopup-target-post" id="lepopup-target-post-'.esc_html($post->ID).'" name="lepopup-posts[]" value="'.esc_html($post->ID).'" checked="checked"'.($target_options['posts-all'] == 'on' ? ' disabled="disabled"' : '').' />
				<label for="lepopup-target-post-'.esc_html($post->ID).'">'.(empty($post->post_title) ? esc_html__('No title', 'lepopup') : esc_html($post->post_title)).' (ID: '.$post->ID.', Status: '.ucfirst($post->post_status).')</label>
			</div>';
				}
				if ($query->found_posts > 0) $posts_found = true;
				unset($args['post__in']);
			}
		}
		$args['nopaging'] = false;
		$args['posts_per_page'] = LEPOPUP_TARGETS_POSTS_PER_PAGE;
		$args['offset'] = $_offset;
		if (sizeof($target_options['posts']) > 0) {
			$args['post__not_in'] = $target_options['posts'];
		}
		$query = new WP_Query($args);
		foreach ($query->posts as $post) {
			$html .= '
			<div class="lepopup-target-posts-item">
				<input type="checkbox" class="lepopup-target-checkbox2 lepopup-target-post" id="lepopup-target-post-'.esc_html($post->ID).'" name="lepopup-posts[]" value="'.esc_html($post->ID).'"'.($target_options['posts-all'] == 'on' ? ' disabled="disabled"' : '').' />
				<label for="lepopup-target-post-'.esc_html($post->ID).'">'.(empty($post->post_title) ? esc_html__('No title', 'lepopup') : esc_html($post->post_title)).' (ID: '.$post->ID.', Status: '.ucfirst($post->post_status).')</label>
			</div>';
		}
		if ($query->found_posts > 0) $posts_found = true;
		if (!$posts_found) {
			if ($_offset == 0) {
				$html = '<div id="lepopup-target-noposts">'.esc_html__('Nothing found.', 'lepopup').'</div>';
			} else {
				$html .= '';
			}
		} else {
			if ($query->query_vars['offset'] + $query->post_count < $query->found_posts) $next_offset = $query->query_vars['offset'] + $query->post_count;
		}
		return array('html' => $html, 'next-offset' => $next_offset);
	}
	function admin_save() {
		global $wpdb, $lepopup;
		$callback = '';
		$errors = array();
		if (array_key_exists('callback', $_REQUEST)) {
			header("Content-type: text/javascript");
			$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
		}
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$target_options = $this->default_target_options;
			$target_id = null;
			if (array_key_exists('lepopup-id', $_REQUEST)) {
				$target_id = intval($_REQUEST['lepopup-id']);
				$target_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_targets WHERE deleted = '0' AND id = '".esc_sql($target_id)."'", ARRAY_A);
				if ($target_details) {
					$target_options_decoded = json_decode($target_details['options'], true);
					if ($target_options_decoded) $target_options = array_merge($target_options, $target_options_decoded);
				} else $target_id = null;
			}
			$event = preg_replace('/[^a-zA-Z0-9_-]/', '', $_REQUEST['lepopup-event']);
			$target_details['item'] = preg_replace('/[^a-zA-Z0-9-]/', '', $_REQUEST['lepopup-item']);
			if (array_key_exists('lepopup-item-mobile', $_REQUEST)) $target_details['item_mobile'] = preg_replace('/[^a-zA-Z0-9-]/', '', $_REQUEST['lepopup-item-mobile']);
			else $target_details['item_mobile'] = $_REQUEST['lepopup-item'];
			if (array_key_exists('lepopup-mode', $_REQUEST)) $target_options['mode'] = $_REQUEST['lepopup-mode'];
			else $target_options['mode'] = 'every-time';
			$target_details['post_type'] = $_REQUEST['lepopup-post-type'];
			$target_options['taxonomies'] = array();
			$target_options['posts'] = array();
			$target_options['url-keywords'] = array();
			if ($target_details['post_type'] != 'sitewide' && $target_details['post_type'] != 'homepage' && $target_details['post_type'] != '__url') {
				$taxonomies = get_object_taxonomies($target_details['post_type'], 'object');
				foreach ($taxonomies as $key => $taxonomy) {
					if (!$taxonomy->public) continue;
					if ($key == 'post_format') continue;
					if (array_key_exists('lepopup-taxonomy-'.$key, $_REQUEST) && is_array($_REQUEST['lepopup-taxonomy-'.$key])) {
						if (in_array('all', $_REQUEST['lepopup-taxonomy-'.$key]) || empty($_REQUEST['lepopup-taxonomy-'.$key])) {
							$target_options['taxonomies'][$key] = 'all';
						} else {
							$terms = get_terms($key, array('hide_empty' => false));
							foreach ($terms as $term) {
								if (in_array($term->slug, $_REQUEST['lepopup-taxonomy-'.$key])) {
									$target_options['taxonomies'][$key][] = $term->slug;
								}
							}
							if (!array_key_exists($key, $target_options['taxonomies']) || empty($target_options['taxonomies'][$key])) $target_options['taxonomies'][$key] = 'all';
						}
					} else {
						$target_options['taxonomies'][$key] = 'all';
					}
					if (array_key_exists('lepopup-taxonomy-archive-enable-'.$key, $_REQUEST)) $target_options['taxonomies']['archive-enable-'.$key] = 'on';
					else $target_options['taxonomies']['archive-enable-'.$key] = 'off';
				}
				$target_options['posts-all'] = (array_key_exists('lepopup-posts-all', $_REQUEST) && $_REQUEST['lepopup-posts-all'] == 'on') ? 'on' : 'off';
				if ($target_options['posts-all'] == 'off') {
					if (array_key_exists('lepopup-posts', $_REQUEST) && is_array($_REQUEST['lepopup-posts'])) {
						foreach ($_REQUEST['lepopup-posts'] as $post_id) {
							$target_options['posts'][] = $post_id;
						}
					}
				}
			} else if ($target_details['post_type'] == '__url' && array_key_exists('lepopup-url-keywords', $_REQUEST)) {
				$url_keywords = explode("\n", $_REQUEST['lepopup-url-keywords']);
				foreach ($url_keywords as $url_keyword) {
					$url_keyword = trim($url_keyword);
					if (!empty($url_keyword)) $target_options['url-keywords'][] = $url_keyword;
				}
				if (empty($target_options['url-keywords'])) $errors[] = esc_html__('Enter at least one URL keyword.', 'lepopup');
			}
			
			$target_details['user_roles'] = array();
			if (array_key_exists('lepopup-userroles', $_REQUEST) && is_array($_REQUEST['lepopup-userroles']) && !in_array('all', $_REQUEST['lepopup-userroles'])) {
				if (in_array('visitor', $_REQUEST['lepopup-userroles'])) $target_details['user_roles'][] = 'visitor';
				$roles = get_editable_roles();
				foreach ($roles as $key => $value) {
					if (in_array($key, $_REQUEST['lepopup-userroles'])) $target_details['user_roles'][] = esc_sql($key);
				}
			}
			$target_details['geoip_country'] = '';
			if (array_key_exists('lepopup-geoip-country', $_REQUEST)) $target_details['geoip_country'] = $_REQUEST['lepopup-geoip-country'];
			$target_details['geoip_region'] = '';
			if (array_key_exists('lepopup-geoip-region', $_REQUEST)) $target_details['geoip_region'] = $_REQUEST['lepopup-geoip-region'];
			$target_details['geoip_city'] = '';
			if (array_key_exists('lepopup-geoip-city', $_REQUEST)) $target_details['geoip_city'] = $_REQUEST['lepopup-geoip-city'];
			$target_details['geoip_zip'] = '';
			if (array_key_exists('lepopup-geoip-zip', $_REQUEST)) $target_details['geoip_zip'] = $_REQUEST['lepopup-geoip-zip'];
			switch($event) {
				case 'onload':
					if (array_key_exists('lepopup-mode-delay', $_REQUEST)) $target_options['mode-delay'] = intval($_REQUEST['lepopup-mode-delay']);
					if (array_key_exists('lepopup-mode-close-delay', $_REQUEST)) $target_options['mode-close-delay'] = intval($_REQUEST['lepopup-mode-close-delay']);
					break;
				case 'onscroll':
					if (array_key_exists('lepopup-mode-offset', $_REQUEST)) $target_options['mode-offset'] = intval($_REQUEST['lepopup-mode-offset']);
					if (array_key_exists('lepopup-onscroll-unit', $_REQUEST) && $_REQUEST["lepopup-onscroll-unit"] == '%') {
						if ($target_options['mode-offset'] > 100) $target_options['mode-offset'] = '100';
						$target_options['mode-offset'] .= '%';
					}
					break;
				case 'onidle':
					if (array_key_exists('lepopup-mode-delay', $_REQUEST)) $target_options['mode-delay'] = intval($_REQUEST['lepopup-mode-delay']);
					break;
				default:
					break;
			}
			if (array_key_exists('lepopup-mode-period', $_REQUEST)) $target_options['mode-period'] = intval($_REQUEST['lepopup-mode-period']);
			$period_start = 0;
			$period_end = 0;
			if (array_key_exists('lepopup-period', $_REQUEST)) $target_details['period'] = $_REQUEST['lepopup-period'];
			else $target_details['period'] = 'always';
			if ($target_details['period'] == 'period') {
				$period_start = preg_replace('/[^0-9]/', '', $_REQUEST['lepopup-period-start']);
				if (strlen($period_start) != 12) $period_start = 0;
				else {
					$time_start = mktime(substr($period_start,8,2), substr($period_start,10,2), "00", substr($period_start,4,2), substr($period_start,6,2), substr($period_start,0,4));
					if ($time_start === false || $time_start < 1) $period_start = 0;
				}
				if ($period_start == 0) $errors[] = esc_html__('Invalid start time/date of the period.', 'lepopup');
					
				$period_end = preg_replace('/[^0-9]/', '', $_REQUEST['lepopup-period-end']);
				if (strlen($period_end) != 12) $period_end = 0;
				else {
					$time_end = mktime(substr($period_end,8,2), substr($period_end,10,2), "00", substr($period_end,4,2), substr($period_end,6,2), substr($period_end,0,4));
					if ($time_end === false || $time_end < 1) $period_end = 0;
				}
				if ($period_end == 0) $errors[] = esc_html__('Invalid end time/date of the period.', 'lepopup');
				
				if ($period_start > 0 && $period_end > 0) {
					if ($time_end < $time_start) $errors[] = esc_html__('End of the period can not be earlier then start of the period.', 'lepopup');
				}
			}
			if (!empty($errors)) {
				$return_data = array(
					'status' => 'ERROR',
					'message' => implode('<br />', $errors)
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			
			if (!empty($target_id)) {
				$sql = "UPDATE ".$wpdb->prefix."lepopup_targets SET
					item = '".esc_sql($target_details['item'])."',
					item_mobile = '".esc_sql($target_details['item_mobile'])."',
					period = '".esc_sql($target_details['period'])."',
					period_start = '".esc_sql($period_start)."',
					period_end = '".esc_sql($period_end)."',
					user_roles = '".(!empty($target_details['user_roles']) ? '{'.implode('}{', $target_details['user_roles']).'}' : '')."',
					geoip_country = '".esc_sql($target_details['geoip_country'])."',
					geoip_region = '".esc_sql($target_details['geoip_region'])."',
					geoip_city = '".esc_sql($target_details['geoip_city'])."',
					geoip_zip = '".esc_sql($target_details['geoip_zip'])."',
					post_type = '".esc_sql($target_details['post_type'])."',
					options = '".esc_sql(json_encode($target_options))."'
					WHERE id = '".esc_sql($target_id)."'";
				$message = esc_html__('Target successfully updated.', 'lepopup');
				$wpdb->query($sql);
				$action = 'update';
			} else {
				if (defined('ICL_LANGUAGE_CODE')) $language = ICL_LANGUAGE_CODE;
				else $language = 'all';
				$sql = "INSERT INTO ".$wpdb->prefix."lepopup_targets (
					event, item, item_mobile, post_type, period, period_start, period_end, user_roles, geoip_country, geoip_region, geoip_city, geoip_zip, language, options, priority, active, created, deleted) VALUES (
					'".esc_sql($event)."',
					'".esc_sql($target_details['item'])."',
					'".esc_sql($target_details['item_mobile'])."',
					'".esc_sql($target_details['post_type'])."',
					'".esc_sql($target_details['period'])."',
					'".esc_sql($period_start)."',
					'".esc_sql($period_end)."',
					'".(!empty($target_details['user_roles']) ? '{'.implode('}{', $target_details['user_roles']).'}' : '')."',
					'".esc_sql($target_details['geoip_country'])."',
					'".esc_sql($target_details['geoip_region'])."',
					'".esc_sql($target_details['geoip_city'])."',
					'".esc_sql($target_details['geoip_zip'])."',
					'".esc_sql($language)."',
					'".esc_sql(json_encode($target_options))."',
					'50', '1', '".time()."', '0')";
				$wpdb->query($sql);
				$message = esc_html__('New target successfully created.', 'lepopup');
				$target_id = $wpdb->insert_id;
				$action = 'insert';
			}
			$target_details = $wpdb->get_row("SELECT t1.*, t2.name as form_name, t3.name as form_mobile_name, t4.name as campaign_name, t5.name as campaign_mobile_name FROM ".$wpdb->prefix."lepopup_targets t1 LEFT JOIN ".$wpdb->prefix."lepopup_forms t2 ON t2.slug = t1.item AND t2.deleted = '0' LEFT JOIN ".$wpdb->prefix."lepopup_forms t3 ON t3.slug = t1.item_mobile AND t3.deleted = '0' LEFT JOIN ".$wpdb->prefix."lepopup_campaigns t4 ON t4.slug = t1.item AND t4.deleted = '0' LEFT JOIN ".$wpdb->prefix."lepopup_campaigns t5 ON t5.slug = t1.item_mobile AND t5.deleted = '0' WHERE t1.id = '".esc_sql($target_id)."'", ARRAY_A);			
			$html = $this->get_list_item_html($target_details);
			
			$return_data = array(
				'status' => 'OK',
				'action' => $action,
				'id' => $target_id,
				'message' => $message,
				'html' => $html
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
			exit;
		}
	}
	function admin_save_list() {
		global $wpdb, $lepopup;
		$callback = '';
		if (array_key_exists('callback', $_REQUEST)) {
			header("Content-type: text/javascript");
			$callback = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['callback']);
		}
		if (current_user_can('manage_options') || $lepopup->demo_mode) {
			$all_events = array_merge($this->events, $this->inline_events);
			if (array_key_exists('lepopup-event', $_REQUEST) && array_key_exists($_REQUEST['lepopup-event'], $all_events)) $event = $_REQUEST['lepopup-event'];
			else {
				$return_data = array(
					'status' => 'ERROR',
					'message' => esc_html__('No event found.', 'lepopup')
				);
				if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
				else echo json_encode($return_data);
				exit;
			}
			if (array_key_exists('lepopup-targets-deleted', $_REQUEST)) {
				$deleted = array();
				$deleted_raw = explode(',', $_REQUEST['lepopup-targets-deleted']);
				foreach ($deleted_raw as $value) {
					if ($value == intval($value)) $deleted[] = intval($value);
				}
				if (!empty($deleted)) {
					$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_targets SET deleted = '1' WHERE event = '".esc_sql($event)."' AND id IN ('".implode("','", $deleted)."')");
				}
			}
			$language_filter = '';
			if (defined('ICL_LANGUAGE_CODE')) {
				if (ICL_LANGUAGE_CODE != 'all') $language_filter = " AND language IN ('all', '".esc_sql(ICL_LANGUAGE_CODE)."')";
			}
			
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_targets SET active = '0' WHERE event = '".$event."' AND deleted = '0'".$language_filter);
			if (array_key_exists('lepopup-targets-active', $_REQUEST)) {
				$active = array();
				$active_raw = explode(',', $_REQUEST['lepopup-targets-active']);
				foreach ($active_raw as $value) {
					if ($value == intval($value)) $active[] = intval($value);
				}
				if (!empty($active)) {
					for ($i=0; $i<sizeof($active); $i++) {
						$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_targets SET active = '1', priority = '".$i."' WHERE event = '".$event."' AND id = '".$active[$i]."'");
					}
				}
			}
			$return_data = array(
				'status' => 'OK',
				'message' => esc_html__('Targets list successfully saved.', 'lepopup')
			);
			if (!empty($callback)) echo $callback.'('.json_encode($return_data).')';
			else echo json_encode($return_data);
			exit;
		}
	}
 
	function get_list_item_html($_target_details) {
		global $lepopup;
		$filter_html = '';
		$target_options = $this->default_target_options;
		if ($_target_details) {
			$target_options_decoded = json_decode($_target_details['options'], true);
			if ($target_options_decoded) $target_options = array_merge($target_options, $target_options_decoded);
		}
		if ($_target_details['post_type'] == 'sitewide') {
			$filter_html .= '<span>'.esc_html__('Sitewide', 'lepopup').'</span>';
		} else if ($_target_details['post_type'] == 'homepage') {
			$filter_html .= '<span>'.esc_html__('Homepage', 'lepopup').'</span>';
		} else if ($_target_details['post_type'] == '__url') {
			$filter_html .= '<span><label>'.esc_html__('URL keywords', 'lepopup').':</label> '.implode(', ', array_map('esc_html', $target_options['url-keywords'])).'</span>';
		} else if ($target_options['posts-all'] == 'off' && !empty($target_options['posts'])) {
			$args = array(
				'post_type' => $_target_details['post_type'],
				'order' => 'DESC',
				'orderby' => 'date',
				'post__in' => $target_options['posts'],
				'nopaging' => true
			);
			$args['post__in'] = $target_options['posts'];
			$args['nopaging'] = true;
			$query = new WP_Query($args);
			if ($query->found_posts > 0) {
				$post_type = get_post_type_object($_target_details['post_type']);
				$filter_html .= '<span><label>'.esc_html($post_type->labels->name).':</label>';
				$posts = array();
				foreach ($query->posts as $post) {
					$posts[] = (empty($post->post_title) ? esc_html__('No title', 'lepopup') : esc_html($post->post_title)).' (ID: '.$post->ID.', Status: '.ucfirst($post->post_status).')';
				}
				$filter_html .= implode(', ', $posts).'</span>';
			}
		} else {
			$taxonomies = get_object_taxonomies($_target_details['post_type'], 'object');
			$skip = false;
			foreach ($taxonomies as $key => $taxonomy) {
				if (!$taxonomy->public) continue;
				if ($key == 'post_format') continue;
				if (array_key_exists($key, $target_options['taxonomies'])) {
					if ($target_options['taxonomies'][$key] == 'all') $filter_html .= '<span><label>'.esc_html($taxonomy->label).':</label>All</span>';
					else if (is_array($target_options['taxonomies'][$key])) {
						$terms = get_terms($key, array('hide_empty' => false));
						$selected = array();
						foreach ($terms as $term) {
							if (in_array($term->slug, $target_options['taxonomies'][$key])) $selected[] = esc_html($term->name);
						}
						if (sizeof($selected) > 0) {
							$filter_html .= '<span><label>'.esc_html($taxonomy->label).':</label>'.implode(', ', $selected).'</span>';
						} else {
							$skip = true;
							break;
						}
					} else {
						$skip = true;
						break;
					}
				}
			}
			$post_type = get_post_type_object($_target_details['post_type']);
			$filter_html .= '<span><label>'.esc_html($post_type->labels->name).':</label>'.($target_options['posts-all'] == 'on' ? 'All' : 'None').'</span>';
			if ($skip) return '';
		}
		if (empty($_target_details['form_name']) && array_key_exists('campaign_name', $_target_details) && !empty($_target_details['campaign_name'])) $_target_details['form_name'] = $_target_details['campaign_name'];
		if (empty($_target_details['form_mobile_name']) && array_key_exists('campaign_mobile_name', $_target_details) && !empty($_target_details['campaign_mobile_name'])) $_target_details['form_mobile_name'] = $_target_details['campaign_mobile_name'];
		if ($lepopup->advanced_options['async-init'] == 'on') {
			if ($_target_details['period'] == 'period') {
				$filter_html .= '<span><label>'.esc_html__('Active period', 'lepopup').':</label>'.esc_html($lepopup->datetime_string($_target_details['period_start']).' ... '.$lepopup->datetime_string($_target_details['period_end'])).'</span>';
			}
			$roles = get_editable_roles();
			$keys = array_keys($roles);
			$tmp = trim($_target_details['user_roles'], '{}');
			$user_roles = explode('}{', $tmp);
			$selected_roles = array_intersect($user_roles, $keys);
			$visitor_selected = in_array('visitor', $user_roles);
			if ($visitor_selected || !empty($selected_roles)) {
				$role_labels = array();
				if ($visitor_selected) $role_labels[] = esc_html__('Non-registered Visitor', 'lepopup');
				foreach ($selected_roles as $key) {
					$role_labels[] = $roles[$key]['name'];
				}
				$filter_html .= '<span><label>'.esc_html__('Active user roles', 'lepopup').':</label>'.esc_html(implode(', ', $role_labels)).'</span>';
			}
			$geoip_params = apply_filters('lepopup_geoip_params_'.$lepopup->options['geoip-service'], array());
			if (!empty($geoip_params)) {
				if (in_array('country', $geoip_params) && !empty($_target_details['geoip_country'])) $filter_html .= '<span><label>'.esc_html__('Country', 'lepopup').':</label>'.(array_key_exists($_target_details['geoip_country'], $this->geoip_countries) ? esc_html($this->geoip_countries[$_target_details['geoip_country']]) : esc_html__('Unknown', 'lepopup')).'</span>';
				if (in_array('region', $geoip_params) && !empty($_target_details['geoip_region'])) $filter_html .= '<span><label>'.esc_html__('Region', 'lepopup').':</label>'.esc_html($_target_details['geoip_region']).'</span>';
				if (in_array('city', $geoip_params) && !empty($_target_details['geoip_city'])) $filter_html .= '<span><label>'.esc_html__('City', 'lepopup').':</label>'.esc_html($_target_details['geoip_city']).'</span>';
				if (in_array('zip', $geoip_params) && !empty($_target_details['geoip_zip'])) $filter_html .= '<span><label>'.esc_html__('ZIP', 'lepopup').':</label>'.esc_html($_target_details['geoip_zip']).'</span>';
			}
		}
		$item_html = '
				<div class="lepopup-targets-list-item" id="lepopup-targets-list-item-'.esc_html($_target_details['id']).'" data-id="'.esc_html($_target_details['id']).'">
					<div class="lepopup-targets-list-item-content">
						<h4>'.(empty($_target_details['form_name']) ? 'None (disabled)' : esc_html($_target_details['form_name'])).' / '.(empty($_target_details['form_mobile_name']) ? ($_target_details['item_mobile'] == 'same' ? (empty($_target_details['item']) ? 'None (disabled)' : esc_html($_target_details['form_name'])) : 'None (disabled)') : esc_html($_target_details['form_mobile_name'])).'</h4>
						'.$filter_html.'
						<div class="lepopup-targets-list-item-buttons">
							<a href="#" onclick="return lepopup_target_properties_open(\''.esc_html($_target_details['event']).'\', '.esc_html($_target_details['id']).');"><i class="fas fa-pencil-alt"></i> '.esc_html__('Edit', 'lepopup').'</a>
							<a href="#" onclick="return lepopup_target_delete(\''.esc_html($_target_details['event']).'\', '.esc_html($_target_details['id']).');"><i class="fas fa-times"></i> '.esc_html__('Remove', 'lepopup').'</a>
						</div>
					</div>
				</div>';
		return $item_html;
	}

	function get_events_data($_raw_post_id, $_url = '') {
		global $lepopup;
		$post_id = 0;
		$raw_post_id = trim(stripslashes($_raw_post_id), '{}');
		if ($raw_post_id == preg_replace('/[^0-9]/', '', $raw_post_id)) {
			$post_id = $raw_post_id;
		} else {
			$tm = explode('}{', $raw_post_id);
			if (sizeof($tm) == 2) {
				if ($tm[0] == preg_replace('/[^0-9]/', '', $tm[0]) && taxonomy_exists($tm[1])) {
					unset($post_id);
					$post_id = array('term' => $tm[0], 'taxonomy' => $tm[1]);
				}
			}
		}

		$event_items = array();
		$javascript_vars = array();

		$targets = $this->get_targets($this->events, $post_id, $_url);
		foreach ($targets as $event => $target_details) {
			$target_options = $this->default_target_options;
			$target_options_decoded = json_decode($target_details['options'], true);
			if ($target_options_decoded) $target_options = array_merge($target_options, $target_options_decoded);
			$popup = $target_details['item'];
			if ($target_details['item_mobile'] != 'same' && (!empty($target_details['item']) || !empty($target_details['item_mobile']))) $popup .= '*'.$target_details['item_mobile'];
			if (!empty($target_details['item'])) $event_items[] = $target_details['item'];
			if (!empty($target_details['item_mobile']) && $target_details['item_mobile'] != 'same') $event_items[] = $target_details['item_mobile'];
			$javascript_vars[$event.'-item'] = $popup;
			$javascript_vars[$event.'-mode'] = $target_options['mode'];
			$javascript_vars[$event.'-mode-period'] = intval($target_options['mode-period']);
			switch ($event) {
				case 'onload':
					$javascript_vars[$event.'-mode-delay'] = intval($target_options['mode-delay']);
					$javascript_vars[$event.'-mode-close-delay'] = intval($target_options['mode-close-delay']);
					break;
				case 'onidle':
					$javascript_vars[$event.'-mode-delay'] = intval($target_options['mode-delay']);
					break;
				case 'onscroll':
					$javascript_vars[$event.'-mode-offset'] = intval($target_options['mode-offset']).(strpos($target_options['mode-offset'], '%') !== false ? '%' : '');
					break;
				default:
					break;
			}
		}
		return array('events-data' => $javascript_vars, 'event-items' => $event_items);
	}	

	function get_targets($_events, $_post_id, $_url = '') {
		global $wpdb, $post, $current_user, $lepopup;
		$post_types = array('sitewide', '__url');
		if (get_option('show_on_front') == 'posts') $post_types[] = 'homepage';
		if (is_array($_post_id)) {
			$taxonomy = get_taxonomy($_post_id['taxonomy']);
			if (is_object($taxonomy) && property_exists($taxonomy, 'object_type') && is_array($taxonomy->object_type))
			$post_types = array_merge($post_types, $taxonomy->object_type);
		} else if (preg_replace('/[^0-9]/', '', $_post_id) == $_post_id && $_post_id > 0) {
			$post_type = get_post_type($_post_id);
			if ($post_type !== false) {
				$post_types[] = $post_type;
			}
		}
		$language_filter = '';
		if (is_array($_REQUEST) && array_key_exists('wpml-language', $_REQUEST)) {
			if ($_REQUEST['wpml-language'] != 'all') $language_filter = " AND language IN ('all', '".esc_sql($_REQUEST['wpml-language'])."')";
		} else if (defined('ICL_LANGUAGE_CODE')) {
			if (ICL_LANGUAGE_CODE != 'all') $language_filter = " AND language IN ('all', '".esc_sql(ICL_LANGUAGE_CODE)."')";
		}
		$targets = array();
		foreach ($_events as $key => $value) {
			$target_details = array();
			$extra_filter = '';
			if ($lepopup->advanced_options['async-init'] == 'on') {
				$user_roles = array();
				$extra_filter = " AND (user_roles IS NULL OR user_roles = ''";
				if (is_object($current_user)) {
					if (!empty($current_user->roles) && is_array($current_user->roles)) $user_roles = $current_user->roles;
					else $user_roles[] = 'visitor';
				} else $user_roles[] = 'visitor';
				foreach ($user_roles as $user_role) {
					$extra_filter .= " OR user_roles LIKE '%{".esc_sql($user_role)."}%'";
				}
				$period_current = date('YmdHi');
				$extra_filter .= ") AND (period = 'always' OR (period = 'period' AND period_start <= '".$period_current."' AND period_end >= '".$period_current."'))";
				$geoip_data = apply_filters('lepopup_geoip_data_'.$lepopup->options['geoip-service'], array(), $_SERVER['REMOTE_ADDR']);
				if (!empty($geoip_data)) {
					if (array_key_exists('country', $geoip_data)) $extra_filter .= " AND (geoip_country = '' OR geoip_country = '".esc_sql($geoip_data['country'])."')";
					if (array_key_exists('region', $geoip_data)) $extra_filter .= " AND (geoip_region = '' OR geoip_region = '".esc_sql($geoip_data['region'])."')";
					if (array_key_exists('city', $geoip_data)) $extra_filter .= " AND (geoip_city = '' OR geoip_city = '".esc_sql($geoip_data['city'])."')";
					if (array_key_exists('zip', $geoip_data)) $extra_filter .= " AND (geoip_zip = '' OR geoip_zip = '".esc_sql($geoip_data['zip'])."')";
				}
			}
			$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_targets WHERE deleted = '0' AND active = '1' AND event = '".esc_sql($key)."' AND post_type IN ('".implode("','", $post_types)."')".$extra_filter.$language_filter." ORDER BY priority ASC", ARRAY_A);
			foreach ($rows as $row) {
				$target_options = $this->default_target_options;
				$target_options_decoded = json_decode($row['options'], true);
				if ($target_options_decoded) $target_options = array_merge($target_options, $target_options_decoded);
				if ($row['post_type'] == 'sitewide') {
					$target_details = $row;
					break;
				} else if ($row['post_type'] == 'homepage') {
					if ($_post_id == 'homepage') {
						$target_details = $row;
						break;
					} else continue;
				} else if ($row['post_type'] == '__url') {
					if (!empty($target_options['url-keywords']) && !empty($_url)) {
						if ($_url != str_replace($target_options['url-keywords'], '', $_url)) {
							$target_details = $row;
							break;
						} else continue;
					} else continue;
				} else if (preg_replace('/[^0-9]/', '', $_post_id) == $_post_id && $target_options['posts-all'] == 'off') {
					if (is_array($target_options['posts']) && in_array($_post_id, $target_options['posts'])) {
						$target_details = $row;
						break;
					}
				} else {
					if (is_array($target_options['taxonomies'])) {
						if (is_array($_post_id)) {
							if (array_key_exists('archive-enable-'.$_post_id['taxonomy'], $target_options['taxonomies']) && $target_options['taxonomies']['archive-enable-'.$_post_id['taxonomy']] == 'on') {
								if (array_key_exists($_post_id['taxonomy'], $target_options['taxonomies'])) {
									if (is_array($target_options['taxonomies'][$_post_id['taxonomy']])) {
										$term = get_term_by('id', $_post_id['term'], $_post_id['taxonomy'], ARRAY_A);
										if ($term) {
											if (in_array($term['slug'], $target_options['taxonomies'][$_post_id['taxonomy']])) {
												$target_details = $row;
												break;
											}
										}
									} else {
										$target_details = $row;
										break;
									}
								} else {
									$target_details = $row;
									break;
								}
							}
						} else {
							$match = true;
							foreach ($target_options['taxonomies'] as $slug => $terms) {
								if (is_array($terms)) {
									if (empty($terms)) continue;
									else {
										$post_term_objects = wp_get_object_terms($_post_id, $slug);
										if (is_array($post_term_objects)) {
											$post_terms = array();
											foreach ($post_term_objects as $post_term_object) {
												$post_terms[] = $post_term_object->slug;
											}
											$common_terms = array_intersect($post_terms, $terms);
											if (empty($common_terms)) {
												$match = false;
												break;
											}
										} else continue;
									}
								}
							}
							if ($match) {
								$target_details = $row;
								break;
							}
						}
					}
				}
			}
			if (!empty($target_details)) {
				$targets[$key] = $target_details;
			}
		}
		return $targets;
	}
	function front_init_inline($_post_id) {
		global $wpdb, $post, $current_user, $lepopup;
		add_filter('the_content', array(&$this, 'the_content'));
		$targets = $this->get_targets($this->inline_events, $_post_id);
		foreach ($this->inline_events as $key => $value) {
			if (array_key_exists($key, $targets)) $this->content_targets[$key] = $targets[$key];
		}
		return $targets;
	}
	function the_content($_content) {
		global $wpdb, $post, $lepopup;
		$prefix = '';
		if (array_key_exists('inlinepostbegin', $this->content_targets) && !empty($this->content_targets['inlinepostbegin']) && class_exists('lepopup_front_class')) {
			$item = $this->content_targets['inlinepostbegin']['item'];
			if ($this->content_targets['inlinepostbegin']['item_mobile'] != 'same' && (!empty($this->content_targets['inlinepostbegin']['item']) || !empty($this->content_targets['inlinepostbegin']['item_mobile']))) $item .= '*'.$this->content_targets['inlinepostbegin']['item_mobile'];
			$prefix = lepopup_front_class::shortcode_handler(array('slug' => $item));
		}
		$suffix = '';
		if (array_key_exists('inlinepostend', $this->content_targets) && !empty($this->content_targets['inlinepostend']) && class_exists('lepopup_front_class')) {
			$item = $this->content_targets['inlinepostend']['item'];
			if ($this->content_targets['inlinepostend']['item_mobile'] != 'same' && (!empty($this->content_targets['inlinepostend']['item']) || !empty($this->content_targets['inlinepostend']['item_mobile']))) $popup .= '*'.$this->content_targets['inlinepostend']['item_mobile'];
			$suffix = lepopup_front_class::shortcode_handler(array('slug' => $item));
		}
		$content = $prefix.$_content.$suffix;
		return $content;
	}
}
?>