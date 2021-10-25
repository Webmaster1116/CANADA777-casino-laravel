<?php
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_admin_class {
	var $list_table;
	var $success_message = '';
	var $error_message = '';
	function __construct() {
		global $lepopup;
		if (is_admin()) {
			$version = get_option('lepopup-version', LEPOPUP_VERSION);
			$webfonts_version = get_option('lepopup-webfonts-version', 0);
			if (($version && $version < LEPOPUP_VERSION) || $webfonts_version < LEPOPUP_WEBFONTS_VERSION) {
				lepopup_class::activate();
				//add_action('admin_notices', array(&$this, 'admin_warning'));
			}
			
			add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
			add_action('admin_head', array(&$this, 'admin_head'));
			add_action('admin_menu', array(&$this, 'admin_menu'), 99);
			add_action('admin_init', array(&$this, 'admin_init'));
			/* Personal Data - 2020-12-09 - begin */
			include_once(dirname(__FILE__).'/core-personal.php');
			$lepopup_personal = new lepopup_personal_data_class();
			/* Personal Data - 2020-12-09 - end */
		}
	}
	
	function admin_warning() {
		echo '
		<div class="error lepopup-error lepopup-error-animated"><p>'.sprintf(esc_html__('IMPORTANT! Please deactivate and activate "Green Popups" plugin %shere%s! It is necessary to sync database for additional functionality', 'lepopup'), '<a href="'.admin_url('plugins.php').'">', '</a>').'</p></div>';
	}

	function admin_enqueue_scripts() {
		global $lepopup;
		wp_enqueue_script("jquery");
		wp_enqueue_style('lepopup', $lepopup->plugins_url.'/css/admin.css', array(), LEPOPUP_VERSION);
		wp_enqueue_script('lepopup', $lepopup->plugins_url.'/js/admin.js', array('jquery'), LEPOPUP_VERSION);
		wp_enqueue_style('lepopup-front', $lepopup->plugins_url.'/css/style.css', array(), LEPOPUP_VERSION);
		wp_enqueue_style('tooltipster', $lepopup->plugins_url.'/css/tooltipster.bundle.min.css', array(), LEPOPUP_VERSION);
		wp_enqueue_script('tooltipster', $lepopup->plugins_url.'/js/tooltipster.bundle.min.js', array('jquery'), LEPOPUP_VERSION);
		if (array_key_exists('page', $_GET) && ($_GET['page'] == 'lepopup' || substr($_GET['page'], 0, strlen('lepopup-')) == 'lepopup-')) {
			wp_enqueue_style('lepopup-if', $lepopup->plugins_url.'/css/lepopup-if.css', array(), LEPOPUP_VERSION);
			wp_enqueue_style('font-awesome-5.7.2', $lepopup->plugins_url.'/css/fontawesome-all.min.css', array(), LEPOPUP_VERSION);
			wp_enqueue_style('material-icons-3.0.1', $lepopup->plugins_url.'/css/material-icons.css', array(), LEPOPUP_VERSION);
			wp_enqueue_style('airdatepicker', $lepopup->plugins_url.'/css/airdatepicker.css', array(), LEPOPUP_VERSION);
			wp_enqueue_style('jquery-ui', $lepopup->plugins_url.'/css/jquery-ui/jquery-ui.min.css', array(), LEPOPUP_VERSION);
			wp_enqueue_script('airdatepicker', $lepopup->plugins_url.'/js/airdatepicker.js', array('jquery'), LEPOPUP_VERSION);
			wp_enqueue_script('chart', $lepopup->plugins_url.'/js/chart.min.js', array(), LEPOPUP_VERSION);
			wp_enqueue_script('jquery.mask', $lepopup->plugins_url.'/js/jquery.mask.min.js', array('jquery'), LEPOPUP_VERSION);
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-draggable');
			wp_enqueue_script('jquery-ui-resizable');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-slider');
			wp_enqueue_style('minicolors', $lepopup->plugins_url.'/css/jquery.minicolors.css', array(), LEPOPUP_VERSION);
			wp_enqueue_script('minicolors', $lepopup->plugins_url.'/js/jquery.minicolors.js', array('jquery'), LEPOPUP_VERSION, true);
			if ($lepopup->options['range-slider-enable'] == 'on') {
				wp_enqueue_style('ion.rangeSlider', $lepopup->plugins_url.'/css/ion.rangeSlider.css', array(), LEPOPUP_VERSION);
				wp_enqueue_script('ion.rangeSlider', $lepopup->plugins_url.'/js/ion.rangeSlider.js', array('jquery'), LEPOPUP_VERSION, true);
			}
			wp_enqueue_media();
			//wp_enqueue_editor();
		}
	}
	
	function admin_head() {
		global $lepopup, $wpdb;
		$sql = "SELECT id, name, active FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' ORDER BY id DESC";
		$rows = $wpdb->get_results($sql, ARRAY_A);
		
		$gettingstarted_steps = array(
			"create-form" => array(
				array(
					"selector"	=> ".lepopup-toolbar",
					"class"		=> "bottom-left",
					"style"		=> "margin-top:10px;margin-left:50px;min-width:530px;",
					"text"		=> esc_html__('Add input field or any other element to the popup by clicking relevant button on Elements Toolbar.', 'lepopup')
				),
				array (
					"selector"	=> ".lepopup-header-settings",
					"class"		=> "left",
					"style"		=> "top:-25px;left:60px;min-width:530px;",
					"text"		=> esc_html__('Click this button to check and adjust popup settings (styles, notifications, integrations, payments, etc.).', 'lepopup')
				),
				array (
					"selector"	=> ".lepopup-pages-bar",
					"class"		=> "bottom-left",
					"style"		=> "margin-top:10px;margin-left:60px;",
					"text"		=> esc_html__('Add pages (steps) to create multi-steps popup.', 'lepopup')
				),
				array (
					"selector"	=> ".lepopup-header-slug",
					"class"		=> "bottom-left",
					"style"		=> "margin-top:10px;margin-left:60px;",
					"text"		=> esc_html__('Set the unique slug.', 'lepopup')
				),
				array (
					"selector"	=> ".lepopup-header-save",
					"class"		=> "bottom-right",
					"style"		=> "margin-top:10px;margin-right:50px;min-width:400px;",
					"text"		=> esc_html__('Save changes by clicking this button.', 'lepopup')
				)
			),
			"form-saved" => array(
				array (
					"selector"	=> ".lepopup-header-using",
					"class"		=> "bottom-right",
					"style"		=> "margin-top:0px;margin-right:20px;min-width:400px;",
					"text"		=> esc_html__('Click this button and check how to use or embed the popup.', 'lepopup')
				)
			),
			"element-properties" => array(
				array(
					"selector"	=> ".lepopup-element-1",
					"class"		=> "bottom-left",
					"style"		=> "margin-top:10px;margin-left:20px;min-width:400px;",
					"text"		=> esc_html__('Click mouse right button over element to access its properties.', 'lepopup')
				),
				array (
					"selector"	=> ".lepopup-layers",
					"class"		=> "bottom-right",
					"style"		=> "margin-top:5px;margin-right:120px;min-width:400px;",
					"text"		=> esc_html__('This is a list of layers. Sort order to change layer priority (z-index).', 'lepopup')
				)
			)
		);
		echo '
<script>
	'.(defined('UAP_CORE') ? 'var lepopup_uap_core = true; ' : '').'var lepopup_ajax_handler = "'.admin_url('admin-ajax.php').'";var lepopup_plugin_url = "'.$lepopup->plugins_url.'"; var lepopup_forms_encoded = "'.base64_encode(json_encode($rows)).'"; var lepopup_gettingstarted_enable = "'.$lepopup->options['gettingstarted-enable'].'"; var lepopup_gettingsstarted_encoded = "'.base64_encode(json_encode($gettingstarted_steps)).'";
	lepopup_gettingstarted_steps = JSON.parse(lepopup_decode64(lepopup_gettingsstarted_encoded));
</script>';
	}

	function admin_menu() {
		global $lepopup;
		if ($lepopup->demo_mode) {
			$cap = "read";
		} else $cap = "manage_options";
		add_menu_page(
			"Green Popups"
			, "Green Popups"
			, $cap
			, "lepopup"
			, array(&$this, 'admin_forms')
			, 'none'
			, 56
		);
		add_submenu_page(
			"lepopup"
			, esc_html__('Popups', 'lepopup')
			, esc_html__('Popups', 'lepopup')
			, $cap
			, "lepopup"
			, array(&$this, 'admin_forms')
		);
		add_submenu_page(
			"lepopup"
			, esc_html__('Create Popup', 'lepopup')
			, esc_html__('Create Popup', 'lepopup')
			, $cap
			, "lepopup-add"
			, array(&$this, 'admin_add_form')
		);
		add_submenu_page(
			"lepopup"
			, esc_html__('A/B Campaigns', 'lepopup')
			, esc_html__('A/B Campaigns', 'lepopup')
			, $cap
			, "lepopup-campaigns"
			, array(&$this, 'admin_campaigns')
		);
		if (!defined('UAP_CORE')) {
			add_submenu_page(
				"lepopup"
				, esc_html__('Targeting', 'lepopup')
				, esc_html__('Targeting', 'lepopup')
				, $cap
				, "lepopup-targeting"
				, array(&$this, 'admin_targeting')
			);
		}
		add_submenu_page(
			"lepopup"
			, esc_html__('Log', 'lepopup')
			, esc_html__('Log', 'lepopup')
			, $cap
			, "lepopup-log"
			, array(&$this, 'admin_records')
		);
		if ($lepopup->advanced_options['admin-menu-stats'] != 'off') {
			add_submenu_page(
				"lepopup"
				, esc_html__('Stats', 'lepopup')
				, esc_html__('Stats', 'lepopup')
				, $cap
				, "lepopup-stats"
				, array(&$this, 'admin_stats')
			);
		}
		if ($lepopup->advanced_options['admin-menu-analytics'] != 'off') {
			add_submenu_page(
				"lepopup"
				, esc_html__('Field Analytics', 'lepopup')
				, esc_html__('Field Analytics', 'lepopup')
				, $cap
				, "lepopup-field-analytics"
				, array(&$this, 'admin_field_analytics')
			);
		}
		if ($lepopup->advanced_options['admin-menu-transactions'] != 'off') {
			add_submenu_page(
				"lepopup"
				, esc_html__('Transactions', 'lepopup')
				, esc_html__('Transactions', 'lepopup')
				, $cap
				, "lepopup-transactions"
				, array(&$this, 'admin_transactions')
			);
		}
		add_submenu_page(
			"lepopup"
			, __('Popups Library', 'lepopup')
			, __('Popups Library', 'lepopup')
			, $cap
			, "lepopup-library"
			, array(&$this, 'admin_library')
		);
		
		do_action('lepopup_admin_menu');
		add_submenu_page(
			"lepopup"
			, esc_html__('Settings', 'lepopup')
			, esc_html__('Settings', 'lepopup')
			, $cap
			, "lepopup-settings"
			, array(&$this, 'admin_settings')
		);
		if (defined('UAP_CORE')) {
			add_submenu_page(
				"lepopup"
				, esc_html__('How To Use', 'lepopup')
				, esc_html__('How To Use', 'lepopup')
				, $cap
				, "lepopup-using"
				, array(&$this, 'admin_using')
			);
		}
		if (defined('ULP_VERSION') && ULP_VERSION > 6) {
			add_submenu_page(
				"lepopup"
				, esc_html__('Migrating', 'lepopup')
				, esc_html__('Migrating', 'lepopup')
				, $cap
				, "lepopup-migrating"
				, array(&$this, 'admin_migrating')
			);
		}
	}

	function admin_using() {
		global $wpdb, $lepopup;
		echo '
		<div class="wrap lepopup-admin lepopup">
			<h2>'.esc_html__('Green Popups - How To Use', 'lepopup').'
				<a href="'.(defined('UAP_CORE') ? 'https://greenpopups.com/documentation/#standalone-script' : 'https://greenpopups.com/documentation/#wordpress-plugin').'" class="lepopup-button-h2" target="_blank">'.esc_html__('Online Documentation', 'lepopup').'</a>
			</h2>
			<div class="lepopup-settings lepopup-using-page">
				<h3>'.esc_html__('Embedding Green Popups', 'lepopup').'</h3>
				<p>'.esc_html__('To embed Green Popups into any website you need perform the following steps:', 'lepopup').'</p>
				<ol>
					<li>
						<span>'.sprintf(esc_html__('Make sure that website has %sDOCTYPE%s. If not, add the following line as a first line of HTML-document:', 'lepopup'), '<code>', '</code>').'</span>
						<input type="text" readonly="readonly" value="'.esc_html('<!DOCTYPE html>').'" onclick="this.focus();this.select();" />
					</li>
					<li>
						<span>'.sprintf(esc_html__('Make sure that website loads jQuery version 1.9 or higher. If not, add the following line into %shead%s section of HTML-document:', 'lepopup'), '<code>', '</code>').'</span>
						<input type="text" readonly="readonly" value="'.esc_html('<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>').'" onclick="this.focus();this.select();" />
					</li>
					<li>
						<span>'.sprintf(esc_html__('Copy the following JS-snippet and paste it into website code. You need paste it at the end of %sbody%s section (above closing %s</body>%s tag).', 'lepopup'), '<code>', '</code>', '<code>', '</code>').'</span>
						<input type="text" readonly="readonly" value="'.esc_html('<script id="lepopup-remote" src="'.$lepopup->plugins_url.'/js/lepopup'.($lepopup->advanced_options['minified-sources'] == 'on' ? '.min' : '').'.js?ver='.LEPOPUP_VERSION.'" data-handler="'.admin_url('admin-ajax.php').'"></script>').'" onclick="this.focus();this.select();" />
					</li>
					<li>
						<span>'.esc_html__('Integration finished.', 'lepopup').'</span>
					</li>
				</ol>
				<p>'.sprintf(esc_html__('Read %sdocumentation%s how to use popups.', 'lepopup'), '<a href="https://greenpopups.com/documentation/#chapter-using-popups" target="_blank">', '</a>').'
			</div>';
		do_action('lepopup_using');
		echo '
		</div>';
	}
	
	function admin_settings() {
		global $wpdb, $lepopup;
		if (isset($_GET['subpage']) && $_GET['subpage'] == 'advanced') {
			$this->admin_advanced_settings();
			return;
		}
		$custom_fonts = array();
		foreach ((array)$lepopup->options['custom-fonts'] as $custom_font) {
			if (!empty($custom_font)) $custom_fonts[] = $custom_font;
		}
		sort($custom_fonts);
		
		echo '
<div class="wrap lepopup-admin lepopup">
	<h2>'.esc_html__('Green Popups - General Settings', 'lepopup').'
		<a href="'.(defined('UAP_CORE') ? 'https://greenpopups.com/documentation/#standalone-script' : 'https://greenpopups.com/documentation/#wordpress-plugin').'" class="lepopup-button-h2" target="_blank">'.esc_html__('Online Documentation', 'lepopup').'</a>
	</h2>
	<div class="lepopup-tabs lepopup-tabs-main">
		<a class="lepopup-tab lepopup-tab-active" href="'.admin_url('admin.php').'?page=lepopup-settings">'.esc_html__('General', 'lepopup').'</a>
		<a class="lepopup-tab" href="'.admin_url('admin.php').'?page=lepopup-settings&subpage=advanced">'.esc_html__('Advanced', 'lepopup').'</a>
	</div>
	<form class="lepopup-settings-form" enctype="multipart/form-data" method="post" style="margin: 0px" action="'.admin_url('admin.php').'">
		<div class="lepopup-settings" style="position: relative;">
			<h3>'.esc_html__('Mailing Settings', 'lepopup').'</h3>
			<table class="lepopup-useroptions">
				<tr>
					<th>'.esc_html__('Sender name', 'lepopup').':</th>
					<td>
						<input type="text" id="lepopup-from-name" name="lepopup-from-name" value="'.esc_html($lepopup->options['from-name']).'" class="widefat" />
						<br /><em>'.esc_html__('Please enter sender name. All messages from plugin are sent using this name as "FROM:" header value.', 'lepopup').'</a></em>.
					</td>
				</tr>
				<tr>
					<th>'.esc_html__('Sender email', 'lepopup').':</th>
					<td>
						<input type="text" id="lepopup-from-email" name="lepopup-from-email" value="'.esc_html($lepopup->options['from-email']).'" class="widefat" />
						<br /><em>'.esc_html__('Please enter sender e-mail. All messages from plugin are sent using this e-mail as "FROM:" header value.', 'lepopup').'</a></em>.
					</td>
				</tr>
			</table>
			<h3>'.esc_html__('Miscellaneous', 'lepopup').'</h3>
			<table class="lepopup-useroptions">';
		if (!defined('UAP_CORE')) {
			echo '
					<tr>
						<th>'.esc_html__('Pre-load popups', 'lepopup').':</th>
						<td>
							<input type="checkbox" id="lepopup-preload" name="lepopup-preload" '.($lepopup->options['preload'] == "on" ? 'checked="checked"' : '').' oninput="if(jQuery(this).is(\':checked\')){jQuery(\'.lepopup-row-preload-event-popups\').slideUp(300);}else{jQuery(\'.lepopup-row-preload-event-popups\').slideDown(300);}"><label for="lepopup-preload"></label><span>'.esc_html__('Pre-load popups', 'lepopup').'</span>
							<br /><em>'.esc_html__('Tick checkbox to pre-load popups (not recommended). If disabled, popups are pulled on demand using AJAX.', 'lepopup').'</em>
						</td>
					</tr>
					<tr class="lepopup-row-preload-event-popups"'.($lepopup->options['preload'] == "on" ? ' style="display: none;"' : '').'>
						<th></th>
						<td>
							<input type="checkbox" id="lepopup-preload-event-popups" name="lepopup-preload-event-popups" '.($lepopup->options['preload-event-popups'] == "on" ? 'checked="checked"' : '').'><label for="lepopup-preload-event-popups"></label><span>'.esc_html__('Pre-load event popups', 'lepopup').'</span>
							<br /><em>'.esc_html__('If enabled, only event popups (OnLoad, OnExit, etc.) are loaded together with website. All other popups are pulled on demand using AJAX.', 'lepopup').'</em>
						</td>
					</tr>';
		}
		echo '
				<tr>
					<th>'.esc_html__('GA tracking', 'lepopup').':</th>
					<td>
						<input type="checkbox" id="lepopup-ga-tracking" name="lepopup-ga-tracking" value="on" '.($lepopup->options['ga-tracking'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-ga-tracking"></label><span>'.esc_html__('Enable Google Analytics tracking', 'lepopup').'</span>
						<br /><em>'.esc_html__('Send form submission event to Google Analytics. GA must be installed on your website. If you use GTM, please configure it properly to accept events.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th>'.esc_html__('Font Awesome', 'lepopup').':</th>
					<td>
						<input type="checkbox" id="lepopup-fa-enable" name="lepopup-fa-enable" value="on" '.($lepopup->options['fa-enable'] == "on" ? 'checked="checked"' : '').' onchange="if(jQuery(\'#lepopup-fa-enable\').is(\':checked\')){jQuery(\'.lepopup-fa-extra\').fadeIn(300);}else{jQuery(\'.lepopup-fa-extra\').fadeOut(300);}" /><label for="lepopup-fa-enable"></label><span>'.esc_html__('Enable Font Awesome icons', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this feature on if you want to use full set of Font Awesome icons.', 'lepopup').'</em>
					</td>
				</tr>
				<tr class="lepopup-fa-extra"'.($lepopup->options['fa-enable'] == "on" ? '' : ' style="display:none;"').'>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-fa-solid-enable" name="lepopup-fa-solid-enable" value="on" '.($lepopup->options['fa-solid-enable'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-fa-solid-enable"></label><span>'.esc_html__('Enable Solid Icons', 'lepopup').'</span>
						<br /><em>'.esc_html__('Enable Font Awesome Solid Icons, you can turn it off you do not need Solid Icons pack. More details:', 'lepopup').' <a href="https://fontawesome.com/cheatsheet" target="_blank">https://fontawesome.com/cheatsheet</a>.</em>
					</td>
				</tr>
				<tr class="lepopup-fa-extra"'.($lepopup->options['fa-enable'] == "on" ? '' : ' style="display:none;"').'>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-fa-regular-enable" name="lepopup-fa-regular-enable" value="on" '.($lepopup->options['fa-regular-enable'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-fa-regular-enable"></label><span>'.esc_html__('Enable Regular Icons', 'lepopup').'</span>
						<br /><em>'.esc_html__('Enable Font Awesome Regular Icons, you can turn it off you do not need Regular Icons pack. More details:', 'lepopup').' <a href="https://fontawesome.com/cheatsheet" target="_blank">https://fontawesome.com/cheatsheet</a>.</em>
					</td>
				</tr>
				<tr class="lepopup-fa-extra"'.($lepopup->options['fa-enable'] == "on" ? '' : ' style="display:none;"').'>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-fa-brands-enable" name="lepopup-fa-brands-enable" value="on" '.($lepopup->options['fa-brands-enable'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-fa-brands-enable"></label><span>'.esc_html__('Enable Brand Icons', 'lepopup').'</span>
						<br /><em>'.esc_html__('Enable Font Awesome Brand Icons, you can turn it off you do not need Brand Icons pack. More details:', 'lepopup').' <a href="https://fontawesome.com/cheatsheet" target="_blank">https://fontawesome.com/cheatsheet</a>.</em>
					</td>
				</tr>
				<tr class="lepopup-fa-extra"'.($lepopup->options['fa-enable'] == "on" ? '' : ' style="display:none;"').'>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-fa-css-disable" name="lepopup-fa-css-disable" value="on" '.($lepopup->options['fa-css-disable'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-fa-css-disable"></label><span>'.esc_html__('Do not load Font Awesome library', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this feature on if your theme or another plugin already loads Font Awesome library.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th>'.esc_html__('Air Datepicker plugin', 'lepopup').':</th>
					<td>
						<input type="checkbox" id="lepopup-airdatepicker-enable" name="lepopup-airdatepicker-enable" value="on" '.($lepopup->options['airdatepicker-enable'] == "on" ? 'checked="checked"' : '').' onchange="if(jQuery(\'#lepopup-airdatepicker-enable\').is(\':checked\')){jQuery(\'.lepopup-airdatepicker-extra\').fadeIn(300);}else{jQuery(\'.lepopup-airdatepicker-extra\').fadeOut(300);}" /><label for="lepopup-airdatepicker-enable"></label><span>'.esc_html__('Enable Air Datepicker plugin', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this feature on if you want to use nice datepicker with forms.', 'lepopup').'</em>
					</td>
				</tr>
				<tr class="lepopup-airdatepicker-extra"'.($lepopup->options['airdatepicker-enable'] == "on" ? '' : ' style="display:none;"').'>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-airdatepicker-js-disable" name="lepopup-airdatepicker-js-disable" value="on" '.($lepopup->options['airdatepicker-js-disable'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-airdatepicker-js-disable"></label><span>'.esc_html__('Do not load Air Datepicker plugin', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this feature on if your theme or another plugin already loads Air Datepicker plugin.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th>'.esc_html__('jQuery Mask plugin', 'lepopup').':</th>
					<td>
						<input type="checkbox" id="lepopup-mask-enable" name="lepopup-mask-enable" value="on" '.($lepopup->options['mask-enable'] == "on" ? 'checked="checked"' : '').' onchange="if(jQuery(\'#lepopup-mask-enable\').is(\':checked\')){jQuery(\'.lepopup-mask-extra\').fadeIn(300);}else{jQuery(\'.lepopup-mask-extra\').fadeOut(300);}" /><label for="lepopup-mask-enable"></label><span>'.esc_html__('Enable jQuery Mask plugin', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this feature on if you want to specify input masks for text fields.', 'lepopup').'</em>
					</td>
				</tr>
				<tr class="lepopup-mask-extra"'.($lepopup->options['mask-enable'] == "on" ? '' : ' style="display:none;"').'>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-mask-js-disable" name="lepopup-mask-js-disable" value="on" '.($lepopup->options['mask-js-disable'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-mask-js-disable"></label><span>'.esc_html__('Do not load jQuery Mask plugin', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this feature on if your theme or another plugin already loads jQuery Mask plugin.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th>'.esc_html__('JavaScript Expression Parser', 'lepopup').':</th>
					<td>
						<input type="checkbox" id="lepopup-jsep-enable" name="lepopup-jsep-enable" value="on" '.($lepopup->options['jsep-enable'] == "on" ? 'checked="checked"' : '').' onchange="if(jQuery(\'#lepopup-jsep-enable\').is(\':checked\')){jQuery(\'.lepopup-jsep-extra\').fadeIn(300);}else{jQuery(\'.lepopup-jsep-extra\').fadeOut(300);}" /><label for="lepopup-jsep-enable"></label><span>'.esc_html__('Enable JavaScript Expression Parser plugin', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this feature on if you want to use math expressions and show results on front-end side.', 'lepopup').'</em>
					</td>
				</tr>
				<tr class="lepopup-jsep-extra"'.($lepopup->options['jsep-enable'] == "on" ? '' : ' style="display:none;"').'>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-jsep-js-disable" name="lepopup-jsep-js-disable" value="on" '.($lepopup->options['jsep-js-disable'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-jsep-js-disable"></label><span>'.esc_html__('Do not load JavaScript Expression Parser plugin', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this feature on if your theme or another plugin already loads JavaScript Expression Parser plugin.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th>'.esc_html__('Signature Pad plugin', 'lepopup').':</th>
					<td>
						<input type="checkbox" id="lepopup-signature-enable" name="lepopup-signature-enable" value="on" '.($lepopup->options['signature-enable'] == "on" ? 'checked="checked"' : '').' onchange="if(jQuery(this).is(\':checked\')){jQuery(\'.lepopup-signature-extra\').fadeIn(300);}else{jQuery(\'.lepopup-signature-extra\').fadeOut(300);}" /><label for="lepopup-signature-enable"></label><span>'.esc_html__('Enable Signature Pad plugin', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this feature on if you want to use signature pad with forms.', 'lepopup').'</em>
					</td>
				</tr>
				<tr class="lepopup-signature-extra"'.($lepopup->options['signature-enable'] == "on" ? '' : ' style="display:none;"').'>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-signature-js-disable" name="lepopup-signature-js-disable" value="on" '.($lepopup->options['signature-js-disable'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-signature-js-disable"></label><span>'.esc_html__('Do not load Signature Pad plugin', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this feature on if your theme or another plugin already loads Signature Pad plugin.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th>'.esc_html__('Ion.RangeSlider plugin', 'lepopup').':</th>
					<td>
						<input type="checkbox" id="lepopup-range-slider-enable" name="lepopup-range-slider-enable" value="on" '.($lepopup->options['range-slider-enable'] == "on" ? 'checked="checked"' : '').' onchange="if(jQuery(this).is(\':checked\')){jQuery(\'.lepopup-range-slider-extra\').fadeIn(300);}else{jQuery(\'.lepopup-range-slider-extra\').fadeOut(300);}" /><label for="lepopup-range-slider-enable"></label><span>'.esc_html__('Enable Ion.RangeSlider plugin', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this feature on if you want to use range slider with forms.', 'lepopup').'</em>
					</td>
				</tr>
				<tr class="lepopup-range-slider-extra"'.($lepopup->options['range-slider-enable'] == "on" ? '' : ' style="display:none;"').'>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-range-slider-js-disable" name="lepopup-range-slider-js-disable" value="on" '.($lepopup->options['range-slider-js-disable'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-range-slider-js-disable"></label><span>'.esc_html__('Do not load Ion.RangeSlider plugin', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this feature on if your theme or another plugin already loads Ion.RangeSlider plugin.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th>'.esc_html__('AdBlock detetctor', 'lepopup').':</th>
					<td>
						<input type="checkbox" id="lepopup-adblock-detector-enable" name="lepopup-adblock-detector-enable" value="on" '.($lepopup->options['adblock-detector-enable'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-adblock-detector-enable"></label><span>'.esc_html__('Enable AdBlock detector', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this feature on if you want to use OnAdblockDetected popups.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th>'.esc_html__('CSV column separator', 'lepopup').':</th>
					<td>
						<select id="lepopup-csv-separator" name="lepopup-csv-separator">
							<option value=";"'.($lepopup->options['csv-separator'] == ';' ? ' selected="selected"' : '').'>'.esc_html__('Semicolon - ";"', 'lepopup').'</option>
							<option value=","'.($lepopup->options['csv-separator'] == ',' ? ' selected="selected"' : '').'>'.esc_html__('Comma - ","', 'lepopup').'</option>
							<option value="tab"'.($lepopup->options['csv-separator'] == 'tab' ? ' selected="selected"' : '').'>'.esc_html__('Tab', 'lepopup').'</option>
						</select>
						<br /><em>'.esc_html__('Please select CSV column separator.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th>'.esc_html__('Custom local fonts', 'lepopup').':</th>
					<td>
						<textarea id="lepopup-custom-fonts" name="lepopup-custom-font-options" placeholder="..." class="widefat" style="height: 120px;">'.implode("\r\n", $custom_fonts).'</textarea>
						<br /><em>'.esc_html__('Set custom local font names (one name per line). Font name must be exactly the same as it goes in your CSS-files, without quotes.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th>'.esc_html__('Email validation', 'lepopup').':</th>
					<td>
						<select id="lepopup-email-validator" name="lepopup-email-validator" onchange="lepopup_email_validator_changed(this);">';
			foreach ($lepopup->email_validators as $key => $label) {
				echo '<option value="'.esc_html($key).'"'.($lepopup->options['email-validator'] == $key ? ' selected="selected"' : '').'>'.esc_html($label).'</option>';
			}
			echo '
						</select>
						<br /><em>'.esc_html__('Please select the type of email validation.', 'lepopup').'</em>
					</td>
				</tr>';
			do_action('lepopup_email_validator_options_show', $lepopup->options['email-validator']);
			if (!defined('UAP_CORE')) {
				echo '
				<tr>
					<th>'.esc_html__('GeoIP service', 'lepopup').':</th>
					<td>
						<select id="lepopup-geoip-service" name="lepopup-geoip-service" onchange="lepopup_geoip_service_changed(this);">';
				foreach ($lepopup->geoip_services as $key => $label) {
					echo '<option value="'.esc_html($key).'"'.($lepopup->options['geoip-service'] == $key ? ' selected="selected"' : '').'>'.esc_html($label).'</option>';
				}
				echo '
						</select>
						<br /><em>'.esc_html__('Please select the GeoIP service.', 'lepopup').'</em>
					</td>
				</tr>';
				do_action('lepopup_geoip_service_options_show', $lepopup->options['geoip-service']);
			}
			echo '
				<tr>
					<th>'.esc_html__('User uploads', 'lepopup').':</th>
					<td>
						<select id="lepopup-file-autodelete" name="lepopup-file-autodelete">';
			foreach ($lepopup->file_autodelete_options as $key => $label) {
				echo '<option value="'.esc_html($key).'"'.($lepopup->options['file-autodelete'] == $key ? ' selected="selected"' : '').'>'.esc_html($label).'</option>';
			}
			echo '
						</select>
						<br /><em>'.esc_html__('Please select how long to keep user uploads on server.', 'lepopup').'</em>
					</td>
				</tr>';
			do_action('lepopup_misc_options_show');
			echo '
				<tr>
					<th>'.esc_html__('Reset cookie', 'lepopup').':</th>
					<td>
						<a class="lepopup-button lepopup-button-small" onclick="return lepopup_cookies_reset(this);"><i class="fas fa-times"></i><label>'.esc_html__('Reset Cookies', 'lepopup').'</label></a>
						<br /><em>'.esc_html__('Click the button to reset cookie. Popup will appear for all users. Do this operation if you changed content in popup and want to display it for returning visitors.', 'lepopup').'</em>
					</td>
				</tr>
			</table>';
		do_action('lepopup_options_show');
		echo '
			<h3>'.esc_html__('Item Purchase Code', 'lepopup').'</h3>
			<table class="lepopup-useroptions">
				<tr>
					<th>'.esc_html__('Item Purchase Code', 'lepopup').':</th>
					<td>
						<input type="text" id="lepopup-purchase-code" name="lepopup-purchase-code" value="'.(!$lepopup->demo_mode ? esc_html($lepopup->options['purchase-code']) : '').'" class="widefat" />
						<br /><em>'.esc_html__('To activate your license please enter Item Purchase Code.', 'lepopup').' <a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600">'.esc_html__('Where can I find my Purchase Code?', 'lepopup').'</a></em>
					</td>
				</tr>
				</tr>
			</table>
			<hr>
			<div class="lepopup-button-container">
				<input type="hidden" id="lepopup-gettingstarted-enable" name="lepopup-gettingstarted-enable" value="'.esc_html($lepopup->options['gettingstarted-enable']).'" class="widefat" />
				<input type="hidden" name="action" value="lepopup-settings-save" />
				<a class="lepopup-button" onclick="return lepopup_settings_save(this);"><i class="fas fa-check"></i><label>'.esc_html__('Save Settings', 'lepopup').'</label></a>
			</div>
		</div>
	</form>
</div>
<div id="lepopup-global-message"></div>';
	}

	function admin_advanced_settings() {
		global $wpdb, $lepopup;
		
		if (!in_array('curl', get_loaded_extensions())) {
			$is_curl = false;
			$message .= '<div class="error"><p>'.esc_html__('cURL is not installed! Some modules are not available.', 'lepopup').'</p></div>';
		
		} else $is_curl = true;
		echo '
<div class="wrap lepopup-admin lepopup">
	<h2>'.esc_html__('Green Popups - Advanced Settings', 'lepopup').'
		<a href="'.(defined('UAP_CORE') ? 'https://greenpopups.com/documentation/#standalone-script' : 'https://greenpopups.com/documentation/#wordpress-plugin').'" class="lepopup-button-h2" target="_blank">'.esc_html__('Online Documentation', 'lepopup').'</a>
	</h2>
	<div class="lepopup-tabs lepopup-tabs-main">
		<a class="lepopup-tab" href="'.admin_url('admin.php').'?page=lepopup-settings">'.esc_html__('General', 'lepopup').'</a>
		<a class="lepopup-tab lepopup-tab-active" href="'.admin_url('admin.php').'?page=lepopup-settings&subpage=advanced">'.esc_html__('Advanced', 'lepopup').'</a>
	</div>
	<form class="lepopup-settings-form" enctype="multipart/form-data" method="post" style="margin: 0px" action="'.admin_url('admin.php').'">
		<div class="lepopup-settings" style="position: relative;">
			<h3>'.esc_html__('Plugin Modules', 'lepopup').'</h3>
			<table class="lepopup-useroptions">
				<tr>
					<th>'.esc_html__('Basic', 'lepopup').':</th>
					<td>
						<input type="checkbox" id="lepopup-advanced-enable-custom-js" name="lepopup-advanced-enable-custom-js" value="on" '.($lepopup->advanced_options['enable-custom-js'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-custom-js"></label><span>'.esc_html__('Activate Custom JavaScript Handlers module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to use custom javascript event handlers for forms. Configure them on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-htmlform" name="lepopup-advanced-enable-htmlform" value="on" '.($lepopup->advanced_options['enable-htmlform'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-htmlform"></label><span>'.esc_html__('Activate HTML Form Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to re-submit popup data as a part of 3rd party HTML form. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-post" name="lepopup-advanced-enable-post" value="on" '.($lepopup->advanced_options['enable-post'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-post"></label><span>'.esc_html__('Activate Custom GET/POST Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to re-submit popup data to 3rd party URL using GET or POST request. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-mysql" name="lepopup-advanced-enable-mysql" value="on" '.($lepopup->advanced_options['enable-mysql'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-mysql"></label><span>'.esc_html__('Activate 3rd party MySQL Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to insert form data into 3rd party MySQL table. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>';
		if (!defined('UAP_CORE')) {
			echo '
				<tr>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-advanced-enable-wpuser" name="lepopup-advanced-enable-wpuser" value="on" '.($lepopup->advanced_options['enable-wpuser'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-wpuser"></label><span>'.esc_html__('Activate WP User Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to create new WordPress user when form submitted. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>';
		}
		echo '
				<tr><td colspan="2"><hr /></td></tr>
				<tr>
					<th>'.esc_html__('Marketing Systems, Newsletters and CRM', 'lepopup').':</th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-acellemail" name="lepopup-advanced-enable-acellemail" value="on" '.($lepopup->advanced_options['enable-acellemail'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-acellemail"></label><span>'.esc_html__('Activate Acelle Mail Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Acelle Mail. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-activecampaign" name="lepopup-advanced-enable-activecampaign" value="on" '.($lepopup->advanced_options['enable-activecampaign'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-activecampaign"></label><span>'.esc_html__('Activate ActiveCampaign Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to ActiveCampaign. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-activetrail" name="lepopup-advanced-enable-activetrail" value="on" '.($lepopup->advanced_options['enable-activetrail'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-activetrail"></label><span>'.esc_html__('Activate ActiveTrail Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to ActiveTrail. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-agilecrm" name="lepopup-advanced-enable-agilecrm" value="on" '.($lepopup->advanced_options['enable-agilecrm'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-agilecrm"></label><span>'.esc_html__('Activate AgileCRM Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to AgileCRM. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-automizy" name="lepopup-advanced-enable-automizy" value="on" '.($lepopup->advanced_options['enable-automizy'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-automizy"></label><span>'.esc_html__('Activate Automizy Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Automizy. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-avangemail" name="lepopup-advanced-enable-avangemail" value="on" '.($lepopup->advanced_options['enable-avangemail'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-avangemail"></label><span>'.esc_html__('Activate AvangEmail Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to AvangEmail. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-aweber" name="lepopup-advanced-enable-aweber" value="on" '.($lepopup->advanced_options['enable-aweber'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-aweber"></label><span>'.esc_html__('Activate AWeber Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to AWeber. Connect to AWeber on General Settings page and configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-birdsend" name="lepopup-advanced-enable-birdsend" value="on" '.($lepopup->advanced_options['enable-birdsend'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-birdsend"></label><span>'.esc_html__('Activate BirdSend Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to BirdSend. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-bitrix24" name="lepopup-advanced-enable-bitrix24" value="on" '.($lepopup->advanced_options['enable-bitrix24'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-bitrix24"></label><span>'.esc_html__('Activate Bitrix24 Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Bitrix24. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-campaignmonitor" name="lepopup-advanced-enable-campaignmonitor" value="on" '.($lepopup->advanced_options['enable-campaignmonitor'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-campaignmonitor"></label><span>'.esc_html__('Activate Campaign Monitor Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Campaign Monitor. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-cleverreach" name="lepopup-advanced-enable-cleverreach" value="on" '.($lepopup->advanced_options['enable-cleverreach'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-cleverreach"></label><span>'.esc_html__('Activate CleverReach Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to CleverReach. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-constantcontact" name="lepopup-advanced-enable-constantcontact" value="on" '.($lepopup->advanced_options['enable-constantcontact'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-constantcontact"></label><span>'.esc_html__('Activate Constant Contact Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Constant Contact. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-conversio" name="lepopup-advanced-enable-conversio" value="on" '.($lepopup->advanced_options['enable-conversio'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-conversio"></label><span>'.esc_html__('Activate Conversio Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Conversio. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-convertkit" name="lepopup-advanced-enable-convertkit" value="on" '.($lepopup->advanced_options['enable-convertkit'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-convertkit"></label><span>'.esc_html__('Activate ConvertKit Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to ConvertKit. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-drip" name="lepopup-advanced-enable-drip" value="on" '.($lepopup->advanced_options['enable-drip'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-drip"></label><span>'.esc_html__('Activate Drip Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Drip. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-egoi" name="lepopup-advanced-enable-egoi" value="on" '.($lepopup->advanced_options['enable-egoi'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-egoi"></label><span>'.esc_html__('Activate E-goi Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to E-goi. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-emailoctopus" name="lepopup-advanced-enable-emailoctopus" value="on" '.($lepopup->advanced_options['enable-emailoctopus'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-emailoctopus"></label><span>'.esc_html__('Activate EmailOctopus Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to EmailOctopus. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>';
		if (!defined('UAP_CORE')) {
			echo '
				<tr>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-advanced-enable-fluentcrm" name="lepopup-advanced-enable-fluentcrm" value="on" '.($lepopup->advanced_options['enable-fluentcrm'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-fluentcrm"></label><span>'.esc_html__('Activate FluentCRM Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to FluentCRM. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>';
		}
		echo '
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-freshmail" name="lepopup-advanced-enable-freshmail" value="on" '.($lepopup->advanced_options['enable-freshmail'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-freshmail"></label><span>'.esc_html__('Activate FreshMail Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to FreshMail. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-getresponse" name="lepopup-advanced-enable-getresponse" value="on" '.($lepopup->advanced_options['enable-getresponse'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-getresponse"></label><span>'.esc_html__('Activate GetResponse Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to GetResponse. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-gist" name="lepopup-advanced-enable-gist" value="on" '.($lepopup->advanced_options['enable-gist'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-gist"></label><span>'.esc_html__('Activate Gist Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Gist. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>';
		if (!defined('UAP_CORE')) {
			echo '
				<tr>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-advanced-enable-groundhogg" name="lepopup-advanced-enable-groundhogg" value="on" '.($lepopup->advanced_options['enable-groundhogg'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-groundhogg"></label><span>'.esc_html__('Activate Groundhogg Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Groundhogg. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>';
		}
		echo '
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-hubspot" name="lepopup-advanced-enable-hubspot" value="on" '.($lepopup->advanced_options['enable-hubspot'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-hubspot"></label><span>'.esc_html__('Activate HubSpot Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to HubSpot. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-inbox" name="lepopup-advanced-enable-inbox" value="on" '.($lepopup->advanced_options['enable-inbox'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-inbox"></label><span>'.esc_html__('Activate INBOX Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to INBOX. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-infomaniak" name="lepopup-advanced-enable-infomaniak" value="on" '.($lepopup->advanced_options['enable-infomaniak'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-infomaniak"></label><span>'.esc_html__('Activate Infomaniak Newsletter Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Infomaniak Newsletter. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-intercom" name="lepopup-advanced-enable-intercom" value="on" '.($lepopup->advanced_options['enable-intercom'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-intercom"></label><span>'.esc_html__('Activate Intercom Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Intercom. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>';
		if (!defined('UAP_CORE')) {
			echo '
				<tr>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-advanced-enable-jetpack" name="lepopup-advanced-enable-jetpack" value="on" '.($lepopup->advanced_options['enable-jetpack'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-jetpack"></label><span>'.esc_html__('Activate Jetpack Subscriptions Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Jetpack Subscriptions. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>';
		}
		echo '
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-klaviyo" name="lepopup-advanced-enable-klaviyo" value="on" '.($lepopup->advanced_options['enable-klaviyo'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-klaviyo"></label><span>'.esc_html__('Activate Klaviyo Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Klaviyo. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-madmimi" name="lepopup-advanced-enable-madmimi" value="on" '.($lepopup->advanced_options['enable-madmimi'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-madmimi"></label><span>'.esc_html__('Activate Mad Mimi Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Mad Mimi. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-mailautic" name="lepopup-advanced-enable-mailautic" value="on" '.($lepopup->advanced_options['enable-mailautic'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-mailautic"></label><span>'.esc_html__('Activate Mailautic Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Mailautic. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-mailchimp" name="lepopup-advanced-enable-mailchimp" value="on" '.($lepopup->advanced_options['enable-mailchimp'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-mailchimp"></label><span>'.esc_html__('Activate MailChimp Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to MailChimp. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-mailerlite" name="lepopup-advanced-enable-mailerlite" value="on" '.($lepopup->advanced_options['enable-mailerlite'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-mailerlite"></label><span>'.esc_html__('Activate MailerLite Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to MailerLite. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-mailfit" name="lepopup-advanced-enable-mailfit" value="on" '.($lepopup->advanced_options['enable-mailfit'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-mailfit"></label><span>'.esc_html__('Activate MailFit Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to MailFit. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-mailgun" name="lepopup-advanced-enable-mailgun" value="on" '.($lepopup->advanced_options['enable-mailgun'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-mailgun"></label><span>'.esc_html__('Activate Mailgun Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Mailgun. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-mailjet" name="lepopup-advanced-enable-mailjet" value="on" '.($lepopup->advanced_options['enable-mailjet'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-mailjet"></label><span>'.esc_html__('Activate Mailjet Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Mailjet. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>';
		if (!defined('UAP_CORE')) {
			echo '
				<tr>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-advanced-enable-mailpoet" name="lepopup-advanced-enable-mailpoet" value="on" '.($lepopup->advanced_options['enable-mailpoet'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-mailpoet"></label><span>'.esc_html__('Activate MailPoet Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to MailPoet. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>';
		}
		echo '
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-mailrelay" name="lepopup-advanced-enable-mailrelay" value="on" '.($lepopup->advanced_options['enable-mailrelay'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-mailrelay"></label><span>'.esc_html__('Activate Mailrelay Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Mailrelay. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>';
		if (!defined('UAP_CORE')) {
			echo '
				<tr>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-advanced-enable-mailster" name="lepopup-advanced-enable-mailster" value="on" '.($lepopup->advanced_options['enable-mailster'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-mailster"></label><span>'.esc_html__('Activate Mailster Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Mailster. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>';
		}
		echo '
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-mailwizz" name="lepopup-advanced-enable-mailwizz" value="on" '.($lepopup->advanced_options['enable-mailwizz'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-mailwizz"></label><span>'.esc_html__('Activate MailWizz Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to MailWizz. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-mautic" name="lepopup-advanced-enable-mautic" value="on" '.($lepopup->advanced_options['enable-mautic'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-mautic"></label><span>'.esc_html__('Activate Mautic Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Mautic. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-moosend" name="lepopup-advanced-enable-moosend" value="on" '.($lepopup->advanced_options['enable-moosend'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-moosend"></label><span>'.esc_html__('Activate Moosend Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Moosend. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-mumara" name="lepopup-advanced-enable-mumara" value="on" '.($lepopup->advanced_options['enable-mumara'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-mumara"></label><span>'.esc_html__('Activate Mumara Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Mumara. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-newsman" name="lepopup-advanced-enable-newsman" value="on" '.($lepopup->advanced_options['enable-newsman'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-newsman"></label><span>'.esc_html__('Activate Newsman Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Newsman. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-omnisend" name="lepopup-advanced-enable-omnisend" value="on" '.($lepopup->advanced_options['enable-omnisend'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-omnisend"></label><span>'.esc_html__('Activate Omnisend Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Omnisend. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-ontraport" name="lepopup-advanced-enable-ontraport" value="on" '.($lepopup->advanced_options['enable-ontraport'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-ontraport"></label><span>'.esc_html__('Activate Ontraport Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Ontraport. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-pipedrive" name="lepopup-advanced-enable-pipedrive" value="on" '.($lepopup->advanced_options['enable-pipedrive'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-pipedrive"></label><span>'.esc_html__('Activate Pipedrive Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Pipedrive. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-rapidmail" name="lepopup-advanced-enable-rapidmail" value="on" '.($lepopup->advanced_options['enable-rapidmail'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-rapidmail"></label><span>'.esc_html__('Activate Rapidmail Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Rapidmail. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-salesflare" name="lepopup-advanced-enable-salesflare" value="on" '.($lepopup->advanced_options['enable-salesflare'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-salesflare"></label><span>'.esc_html__('Activate Salesflare Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Salesflare. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-salesautopilot" name="lepopup-advanced-enable-salesautopilot" value="on" '.($lepopup->advanced_options['enable-salesautopilot'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-salesautopilot"></label><span>'.esc_html__('Activate SalesAutoPilot Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to SalesAutoPilot. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-sendfox" name="lepopup-advanced-enable-sendfox" value="on" '.($lepopup->advanced_options['enable-sendfox'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-sendfox"></label><span>'.esc_html__('Activate SendFox Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to SendFox. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-sendgrid" name="lepopup-advanced-enable-sendgrid" value="on" '.($lepopup->advanced_options['enable-sendgrid'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-sendgrid"></label><span>'.esc_html__('Activate SendGrid Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to SendGrid. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-sendinblue" name="lepopup-advanced-enable-sendinblue" value="on" '.($lepopup->advanced_options['enable-sendinblue'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-sendinblue"></label><span>'.esc_html__('Activate SendinBlue Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to SendinBlue. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-sendpulse" name="lepopup-advanced-enable-sendpulse" value="on" '.($lepopup->advanced_options['enable-sendpulse'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-sendpulse"></label><span>'.esc_html__('Activate SendPulse Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to SendPulse. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-sendy" name="lepopup-advanced-enable-sendy" value="on" '.($lepopup->advanced_options['enable-sendy'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-sendy"></label><span>'.esc_html__('Activate Sendy Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Sendy. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-sgautorepondeur" name="lepopup-advanced-enable-sgautorepondeur" value="on" '.($lepopup->advanced_options['enable-sgautorepondeur'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-sgautorepondeur"></label><span>'.esc_html__('Activate SG Autorepondeur Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to SG Autorepondeur. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-socketlabs" name="lepopup-advanced-enable-socketlabs" value="on" '.($lepopup->advanced_options['enable-socketlabs'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-socketlabs"></label><span>'.esc_html__('Activate SocketLabs Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to SocketLabs. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>';
		if (!defined('UAP_CORE')) {
			echo '
				<tr>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-advanced-enable-thenewsletterplugin" name="lepopup-advanced-enable-thenewsletterplugin" value="on" '.($lepopup->advanced_options['enable-thenewsletterplugin'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-thenewsletterplugin"></label><span>'.esc_html__('Activate The Newsletter Plugin Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to The Newsletter Plugin. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-advanced-enable-tribulant" name="lepopup-advanced-enable-tribulant" value="on" '.($lepopup->advanced_options['enable-tribulant'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-tribulant"></label><span>'.esc_html__('Activate Tribulant Newsletters Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Tribulant Newsletters. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>';
		}
		echo '
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-verticalresponse" name="lepopup-advanced-enable-verticalresponse" value="on" '.($lepopup->advanced_options['enable-verticalresponse'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-verticalresponse"></label><span>'.esc_html__('Activate VerticalResponse Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to VerticalResponse. Connect to VerticalResponse on General Settings page and configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-ymlp" name="lepopup-advanced-enable-ymlp" value="on" '.($lepopup->advanced_options['enable-ymlp'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-ymlp"></label><span>'.esc_html__('Activate Your Mailing List Provider Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Your Mailing List Provider. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-zapier" name="lepopup-advanced-enable-zapier" value="on" '.($lepopup->advanced_options['enable-zapier'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-zapier"></label><span>'.esc_html__('Activate Zapier Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Zapier. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-zohocrm" name="lepopup-advanced-enable-zohocrm" value="on" '.($lepopup->advanced_options['enable-zohocrm'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-zohocrm"></label><span>'.esc_html__('Activate Zoho CRM Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to submit popup data to Zoho CRM. Connect to Zoho CRM on General Settings page and configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr><td colspan="2"><hr /></td></tr>
				<tr>
					<th>'.esc_html__('Payment Providers', 'lepopup').':</th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-authorizenet" name="lepopup-advanced-enable-authorizenet" value="on" '.($lepopup->advanced_options['enable-authorizenet'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-authorizenet"></label><span>'.esc_html__('Activate Authorize.Net Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to accept payments via Authorize.Net. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-blockchain" name="lepopup-advanced-enable-blockchain" value="on" '.($lepopup->advanced_options['enable-blockchain'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-blockchain"></label><span>'.esc_html__('Activate Blockchain Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to accept payments via Blockchain. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-instamojo" name="lepopup-advanced-enable-instamojo" value="on" '.($lepopup->advanced_options['enable-instamojo'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-instamojo"></label><span>'.esc_html__('Activate Instamojo Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to accept payments via Instamojo. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-advanced-enable-interkassa" name="lepopup-advanced-enable-interkassa" value="on" '.($lepopup->advanced_options['enable-interkassa'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-interkassa"></label><span>'.esc_html__('Activate InterKassa Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to accept payments via InterKassa. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-mollie" name="lepopup-advanced-enable-mollie" value="on" '.($lepopup->advanced_options['enable-mollie'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-mollie"></label><span>'.esc_html__('Activate Mollie Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to accept payments via Mollie. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-payfast" name="lepopup-advanced-enable-payfast" value="on" '.($lepopup->advanced_options['enable-payfast'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-payfast"></label><span>'.esc_html__('Activate PayFast Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to accept payments via PayFast. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-paypal" name="lepopup-advanced-enable-paypal" value="on" '.($lepopup->advanced_options['enable-paypal'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-paypal"></label><span>'.esc_html__('Activate PayPal Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to accept payments via PayPal. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-paystack" name="lepopup-advanced-enable-paystack" value="on" '.($lepopup->advanced_options['enable-paystack'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-paystack"></label><span>'.esc_html__('Activate Paystack Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to accept payments via Paystack (accept NGN only). Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-payumoney" name="lepopup-advanced-enable-payumoney" value="on" '.($lepopup->advanced_options['enable-payumoney'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-payumoney"></label><span>'.esc_html__('Activate PayUmoney Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to accept payments via PayUmoney. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-advanced-enable-perfectmoney" name="lepopup-advanced-enable-perfectmoney" value="on" '.($lepopup->advanced_options['enable-perfectmoney'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-perfectmoney"></label><span>'.esc_html__('Activate Perfect Money Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to accept payments via Perfect Money. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-razorpay" name="lepopup-advanced-enable-razorpay" value="on" '.($lepopup->advanced_options['enable-razorpay'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-razorpay"></label><span>'.esc_html__('Activate Razorpay Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to accept payments via Razorpay. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-skrill" name="lepopup-advanced-enable-skrill" value="on" '.($lepopup->advanced_options['enable-skrill'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-skrill"></label><span>'.esc_html__('Activate Skrill Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to accept payments via Skrill. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-stripe" name="lepopup-advanced-enable-stripe" value="on" '.($lepopup->advanced_options['enable-stripe'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-stripe"></label><span>'.esc_html__('Activate Stripe Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to accept payments via Stripe. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-wepay" name="lepopup-advanced-enable-wepay" value="on" '.($lepopup->advanced_options['enable-wepay'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-wepay"></label><span>'.esc_html__('Activate WePay Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to accept payments via WePay. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-advanced-enable-yandexmoney" name="lepopup-advanced-enable-yandexmoney" value="on" '.($lepopup->advanced_options['enable-yandexmoney'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-yandexmoney"></label><span>'.esc_html__('Activate Yandex.Money Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to accept payments via Yandex.Money. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr><td colspan="2"><hr /></td></tr>
				<tr>
					<th>'.esc_html__('SMS Gateways', 'lepopup').':</th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-bulkgate" name="lepopup-advanced-enable-bulkgate" value="on" '.($lepopup->advanced_options['enable-bulkgate'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-bulkgate"></label><span>'.esc_html__('Activate BulkGate Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to use BulkGate gateway. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-gatewayapi" name="lepopup-advanced-enable-gatewayapi" value="on" '.($lepopup->advanced_options['enable-gatewayapi'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-gatewayapi"></label><span>'.esc_html__('Activate GatewayAPI Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to use GatewayAPI gateway. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-nexmo" name="lepopup-advanced-enable-nexmo" value="on" '.($lepopup->advanced_options['enable-nexmo'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-nexmo"></label><span>'.esc_html__('Activate Nexmo Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to use Nexmo gateway. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-twilio" name="lepopup-advanced-enable-twilio" value="on" '.($lepopup->advanced_options['enable-twilio'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-twilio"></label><span>'.esc_html__('Activate Twilio Integration module', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this module on if you want to use Twilio gateway. Configure integration on popup editor.', 'lepopup').'</em>
					</td>
				</tr>
			</table>';
		if (!defined('UAP_CORE')) {
			echo '
			<table class="lepopup-useroptions">
				<tr><td colspan="2"><hr /></td></tr>
				<tr>
					<th>'.esc_html__('GeoIP services', 'lepopup').':</th>
					<td>
						<input type="checkbox" id="lepopup-advanced-enable-geoipdetect" name="lepopup-advanced-enable-geoipdetect" value="on" '.($lepopup->advanced_options['enable-geoipdetect'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-geoipdetect"></label><span>'.esc_html__('Activate Geolocation IP Detection Plugin Integration module', 'lepopup').'</span>
						<br /><em>'.sprintf(esc_html__('Turn this module on if you want to use %sGeolocation IP Detection Plugin%s to handle GeoIP targets.', 'lepopup'), '<a href="https://wordpress.org/plugins/geoip-detect/" target="_blank">', '</a>').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-advanced-enable-ipstack" name="lepopup-advanced-enable-ipstack" value="on" '.($lepopup->advanced_options['enable-ipstack'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-ipstack"></label><span>'.esc_html__('Activate ipstack Integration module', 'lepopup').'</span>
						<br /><em>'.sprintf(esc_html__('Turn this module on if you want to use %sipstack%s service to handle GeoIP targets.', 'lepopup'), '<a href="https://ipstack.com/" target="_blank">', '</a>').'</em>
					</td>
				</tr>
			</table>';
		}
		echo '
			</table>
			<table class="lepopup-useroptions">
				<tr><td colspan="2"><hr /></td></tr>
				<tr>
					<th>'.esc_html__('Extended email validation', 'lepopup').':</th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-clearout" name="lepopup-advanced-enable-clearout" value="on" '.($lepopup->advanced_options['enable-clearout'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-clearout"></label><span>'.esc_html__('Activate Clearout Integration module', 'lepopup').'</span>
						<br /><em>'.sprintf(esc_html__('Turn this module on if you want to use %sClearout%s to validate email addresses. Configure integration on General Settings page.', 'lepopup'), '<a href="https://clearout.io/" target="_blank">', '</a>').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-emaillistvalidation" name="lepopup-advanced-enable-emaillistvalidation" value="on" '.($lepopup->advanced_options['enable-emaillistvalidation'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-emaillistvalidation"></label><span>'.esc_html__('Activate Email List Validation Integration module', 'lepopup').'</span>
						<br /><em>'.sprintf(esc_html__('Turn this module on if you want to use %sEmail List Validation%s to validate email addresses. Configure integration on General Settings page.', 'lepopup'), '<a href="https://emaillistvalidation.com/" target="_blank">', '</a>').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-emaillistverify" name="lepopup-advanced-enable-emaillistverify" value="on" '.($lepopup->advanced_options['enable-emaillistverify'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-emaillistverify"></label><span>'.esc_html__('Activate Email List Verify Integration module', 'lepopup').'</span>
						<br /><em>'.sprintf(esc_html__('Turn this module on if you want to use %sEmail List Verify%s to validate email addresses. Configure integration on General Settings page.', 'lepopup'), '<a href="https://www.emaillistverify.com/" target="_blank">', '</a>').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-kickbox" name="lepopup-advanced-enable-kickbox" value="on" '.($lepopup->advanced_options['enable-kickbox'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-kickbox"></label><span>'.esc_html__('Activate Kickbox Integration module', 'lepopup').'</span>
						<br /><em>'.sprintf(esc_html__('Turn this module on if you want to use %sKickbox%s to validate email addresses. Configure integration on General Settings page.', 'lepopup'), '<a href="https://kickbox.com/" target="_blank">', '</a>').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-thechecker" name="lepopup-advanced-enable-thechecker" value="on" '.($lepopup->advanced_options['enable-thechecker'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-thechecker"></label><span>'.esc_html__('Activate TheChecker Integration module', 'lepopup').'</span>
						<br /><em>'.sprintf(esc_html__('Turn this module on if you want to use %sTheChecker%s to validate email addresses. Configure integration on General Settings page.', 'lepopup'), '<a href="https://thechecker.co/" target="_blank">', '</a>').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input '.($is_curl ? '' : 'disabled="disabled" ').'type="checkbox" id="lepopup-advanced-enable-truemail" name="lepopup-advanced-enable-truemail" value="on" '.($lepopup->advanced_options['enable-truemail'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-enable-truemail"></label><span>'.esc_html__('Activate TrueMail Integration module', 'lepopup').'</span>
						<br /><em>'.sprintf(esc_html__('Turn this module on if you want to use %sTrueMail%s to validate email addresses. Configure integration on General Settings page.', 'lepopup'), '<a href="https://truemail.io/" target="_blank">', '</a>').'</em>
					</td>
				</tr>
			</table>
			<h3>'.esc_html__('Translations', 'lepopup').'</h3>
			<table class="lepopup-useroptions">
				<tr>
					<th>'.esc_html__('Detail labels', 'lepopup').':</th>
					<td>
						<input type="text" id="lepopup-advanced-label-form-values" name="lepopup-advanced-label-form-values" value="'.esc_html($lepopup->advanced_options['label-form-values']).'" class="widefat" />
						<br /><em>'.esc_html__('Form Values', 'lepopup').'</a></em>.
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="text" id="lepopup-advanced-label-payment" name="lepopup-advanced-label-payment" value="'.esc_html($lepopup->advanced_options['label-payment']).'" class="widefat" />
						<br /><em>'.esc_html__('Payment', 'lepopup').'</a></em>.
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="text" id="lepopup-advanced-label-general-info" name="lepopup-advanced-label-general-info" value="'.esc_html($lepopup->advanced_options['label-general-info']).'" class="widefat" />
						<br /><em>'.esc_html__('General Info', 'lepopup').'</a></em>.
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="text" id="lepopup-advanced-label-raw-details" name="lepopup-advanced-label-raw-details" value="'.esc_html($lepopup->advanced_options['label-raw-details']).'" class="widefat" />
						<br /><em>'.esc_html__('Raw Details', 'lepopup').'</a></em>.
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="text" id="lepopup-advanced-label-technical-info" name="lepopup-advanced-label-technical-info" value="'.esc_html($lepopup->advanced_options['label-technical-info']).'" class="widefat" />
						<br /><em>'.esc_html__('Technical Info', 'lepopup').'</a></em>.
					</td>
				</tr>
			</table>
			<h3>'.esc_html__('Miscellaneous', 'lepopup').'</h3>
			<table class="lepopup-useroptions">
				<tr>
					<th>'.esc_html__('Admin menu items', 'lepopup').':</th>
					<td>
						<input type="checkbox" id="lepopup-advanced-admin-menu-stats" name="lepopup-advanced-admin-menu-stats" value="on" '.($lepopup->advanced_options['admin-menu-stats'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-admin-menu-stats"></label><span>'.esc_html__('Enable "Stats" menu item', 'lepopup').'</span>
						<br /><em>'.esc_html__('Show or hide menu item "Stats" in Left Side admin menu.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-advanced-admin-menu-analytics" name="lepopup-advanced-admin-menu-analytics" value="on" '.($lepopup->advanced_options['admin-menu-analytics'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-admin-menu-analytics"></label><span>'.esc_html__('Enable "Field Analytics" menu item', 'lepopup').'</span>
						<br /><em>'.esc_html__('Show or hide menu item "Field Analytics" in Left Side admin menu.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="checkbox" id="lepopup-advanced-admin-menu-transactions" name="lepopup-advanced-admin-menu-transactions" value="on" '.($lepopup->advanced_options['admin-menu-transactions'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-admin-menu-transactions"></label><span>'.esc_html__('Enable "Transactions" menu item', 'lepopup').'</span>
						<br /><em>'.esc_html__('Show or hide menu item "Transactions" in Left Side admin menu.', 'lepopup').'</em>
					</td>
				</tr>';
		if (!defined('UAP_CORE')) {
			echo '
				<tr>
					<th>'.esc_html__('Async Initialization', 'lepopup').':</th>
					<td>
						<input type="checkbox" id="lepopup-advanced-async-init" name="lepopup-advanced-async-init" '.($lepopup->advanced_options['async-init'] == "on" ? 'checked="checked"' : '').'"><label for="lepopup-advanced-async-init"></label><span>'.esc_html__('Enable async initialization of event popups', 'lepopup').'</span>
						<br /><em>'.esc_html__('Tick checkbox to enable initilaization of event popups asynchronously (recommended for best front-end performance).', 'lepopup').'</em>
					</td>
				</tr>';
		}
		echo '
				<tr>
					<th>'.esc_html__('Enable PHP Session', 'lepopup').':</th>
					<td>
						<input type="checkbox" id="lepopup-advanced-enable-php-session" name="lepopup-advanced-enable-php-session" '.($lepopup->advanced_options['enable-php-session'] == "on" ? 'checked="checked"' : '').'"><label for="lepopup-advanced-enable-php-session"></label><span>'.esc_html__('Enable PHP Session', 'lepopup').'</span>
						<br /><em>'.esc_html__('Tick checkbox to enable to enable PHP Session (for better file uploading functionality).', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th>'.esc_html__('!Important CSS', 'lepopup').':</th>
					<td>
						<input type="checkbox" id="lepopup-advanced-important-enable" name="lepopup-advanced-important-enable" value="on" '.($lepopup->advanced_options['important-enable'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-important-enable"></label><span>'.esc_html__('Add suffix "!important" to styles', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this feature on of you have some CSS-conflicts with existing stylesheet.', 'lepopup').'</em>
					</td>
				</tr>
				<tr>
					<th>'.esc_html__('Minification', 'lepopup').':</th>
					<td>
						<input type="checkbox" id="lepopup-advanced-minified-sources" name="lepopup-advanced-minified-sources" value="on" '.($lepopup->advanced_options['minified-sources'] == "on" ? 'checked="checked"' : '').' /><label for="lepopup-advanced-minified-sources"></label><span>'.esc_html__('Use minified JS and CSS files', 'lepopup').'</span>
						<br /><em>'.esc_html__('Turn this feature on to use minified JS and CSS files.', 'lepopup').'</em>
					</td>
				</tr>
			</table>
			<h3>'.esc_html__('Remote use', 'lepopup').'</h3>
			<table class="lepopup-useroptions">
				<tr>
					<th>'.esc_html__('Green Popups', 'lepopup').':</th>
					<td>
						<input type="text" class="lepopup-input-code widefat" readonly="readonly" value="'.esc_html('<script id="lepopup-remote" src="'.$lepopup->plugins_url.'/js/lepopup'.($lepopup->advanced_options['minified-sources'] == 'on' ? '.min' : '').'.js?ver='.LEPOPUP_VERSION.'" data-handler="'.admin_url('admin-ajax.php').'"></script>').'" onclick="this.focus();this.select();" />
						<br /><em>'.sprintf(esc_html__('Embed Green Popups into 3rd party page, to use popups remotely. Please read %sDocumentation%s for more details.', 'lepopup'), '<a target="_blank" href="https://greenpopups.com/documentation/?wordpress#chapter-using-popups">', '</a>').'</em>
					</td>
				</tr>';
		do_action('lepopup_remote_use_settings');
		echo '
			</table>
			<hr>
			<div class="lepopup-button-container">
				<input type="hidden" name="action" value="lepopup-advanced-settings-save" />
				<a class="lepopup-button" onclick="return lepopup_settings_save(this);"><i class="fas fa-check"></i><label>'.esc_html__('Save Settings', 'lepopup').'</label></a>
			</div>
		</div>
	</form>
</div>
<div id="lepopup-global-message"></div>';
	}

	function admin_forms() {
		global $wpdb, $lepopup;
		
		if (isset($_GET["s"])) $search_query = trim(stripslashes($_GET["s"]));
		else $search_query = "";
		
		$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0'".((strlen($search_query) > 0) ? " AND name LIKE '%".esc_sql($wpdb->esc_like($search_query))."%'" : ""), ARRAY_A);
		$total = $tmp["total"];
		$totalpages = ceil($total/LEPOPUP_RECORDS_PER_PAGE);
		if ($totalpages == 0) $totalpages = 1;
		if (isset($_GET["p"])) $page = intval($_GET["p"]);
		else $page = 1;
		if ($page < 1 || $page > $totalpages) $page = 1;
		$switcher = $lepopup->page_switcher(admin_url('admin.php').'?page=lepopup'.((strlen($search_query) > 0) ? '&s='.rawurlencode($search_query) : ''), $page, $totalpages);

		if (isset($_GET['o'])) {
			$sort = $_GET['o'];
			if (in_array($sort, $lepopup->sort_methods)) {
				if ($sort != $lepopup->options['sort-forms']) {
					update_option('lepopup-sort-forms', $sort);
					$lepopup->options['sort-forms'] = $sort;
				}
			} else $sort = $lepopup->options['sort-forms'];
		} else $sort = $lepopup->options['sort-forms'];
		$orderby = 't1.created DESC';
		switch ($sort) {
			case 'name-az':
				$orderby = 't1.name ASC';
				break;
			case 'name-za':
				$orderby = 't1.name DESC';
				break;
			case 'date-az':
				$orderby = 't1.created ASC';
				break;
			default:
				$orderby = 't1.created DESC';
				break;
		}
		
		$sql = "SELECT t1.*, t2.entries FROM ".$wpdb->prefix."lepopup_forms t1 LEFT JOIN (SELECT COUNT(*) AS entries, form_id FROM ".$wpdb->prefix."lepopup_records WHERE deleted = '0' GROUP BY form_id) t2 ON t2.form_id = t1.id WHERE t1.deleted = '0'".((strlen($search_query) > 0) ? " AND t1.name LIKE '%".esc_sql($wpdb->esc_like($search_query))."%'" : "")." ORDER BY ".$orderby.", id DESC LIMIT ".esc_sql(($page-1)*LEPOPUP_RECORDS_PER_PAGE).", ".esc_sql(LEPOPUP_RECORDS_PER_PAGE);
		$rows = $wpdb->get_results($sql, ARRAY_A);
		
		echo '
<div class="wrap lepopup-admin">
	<h2>'.esc_html__('Green Popups - Popups', 'lepopup').'
		<a class="lepopup-button-h2" href="'.admin_url('admin.php').'?page=lepopup-add">'.esc_html__('Create New Popup', 'lepopup').'</a>
		<a href="'.(defined('UAP_CORE') ? 'https://greenpopups.com/documentation/#standalone-script' : 'https://greenpopups.com/documentation/#wordpress-plugin').'" class="lepopup-button-h2" target="_blank">'.esc_html__('Online Documentation', 'lepopup').'</a>
	</h2>
	<div class="lepopup-top-forms">
		<div class="lepopup-top-form-left">
			<form action="'.admin_url('admin.php').'" method="get" class="uap-filter-form lepopup-filter-form">
				<input type="hidden" name="page" value="lepopup" />
				<label>'.esc_html__('Search:', 'lepopup').'</label>
				<input type="text" name="s" style="width: 200px;" class="form-control" value="'.esc_html($search_query).'">
				<input type="submit" class="lepopup-button-search" value="'.esc_html__('Search', 'lepopup').'" />
				'.((strlen($search_query) > 0) ? '<input type="button" class="lepopup-button-search" value="'.esc_html__('Reset search results', 'lepopup').'" onclick="window.location.href=\''.admin_url('admin.php').'?page=lepopup\';" />' : '').'
			</form>
		</div>
		<div class="lepopup-top-form-right">
			<form id="lepopup-sorting-form" action="'.admin_url('admin.php').'" method="get" class="uap-filter-form lepopup-filter-form">
			<input type="hidden" name="page" value="lepopup" />
			<label>'.esc_html__('Sort:', 'lepopup').'</label>
			'.((strlen($search_query) > 0) ? '<input type="hidden" name="s" value="'.esc_html($search_query).'">' : '').'
			'.(($page > 1) ? '<input type="hidden" name="p" value="'.esc_html($page).'">' : '').'
			<select name="o" onchange="jQuery(\'#lepopup-sorting-form\').submit();" style="width: 150px;" class="form-control">
				<option value="name-az"'.($sort == 'name-az' ? ' selected="selected"' : '').'>'.esc_html__('Alphabetically', 'lepopup').' ▲</option>
				<option value="name-za"'.($sort == 'name-za' ? ' selected="selected"' : '').'>'.esc_html__('Alphabetically', 'lepopup').' ▼</option>
				<option value="date-az"'.($sort == 'date-az' ? ' selected="selected"' : '').'>'.esc_html__('Created', 'lepopup').' ▲</option>
				<option value="date-za"'.($sort == 'date-za' ? ' selected="selected"' : '').'>'.esc_html__('Created', 'lepopup').' ▼</option>
			</select>
			</form>
		</div>
	</div>
	<div class="lepopup-table-list-buttons"><a href="'.admin_url('admin.php').'?page=lepopup-add" class="lepopup-button lepopup-button-small"><i class="fas fa-plus"></i><label>'.esc_html__('Create New Popup', 'lepopup').'</label></a></div>
	<div class="lepopup-pageswitcher">'.$switcher.'</div>
	<table class="lepopup-table-list widefat">
		<tr>
			<th>'.esc_html__('Name', 'lepopup').'</th>
			<th style="width: 35px;"></th>
			<th style="width: 360px;">'.esc_html__('Slug', 'lepopup').'</th>
			<th style="width: 60px;">'.esc_html__('Entries', 'lepopup').'</th>
			<th style="width: 35px;"></th>
		</tr>';
		if (sizeof($rows) > 0) {
			foreach ($rows as $row) {
				if (!defined('UAP_CORE')) $preview_url = get_bloginfo('url').'?lepopup='.$row['slug'].'&ac={ANTICACHE}#lepopup-'.$row['slug'];
				else $preview_url = $lepopup->plugins_url.'/index.html?lepopup='.$row['slug'].'&ac={ANTICACHE}#lepopup-'.$row['slug'];
				
				echo '
				<tr>
					<td><a href="'.admin_url('admin.php').'?page=lepopup-add&id='.esc_html($row['id']).'"><strong>'.esc_html($row['name']).'</strong></a><span class="lepopup-table-list-badge-status">'.($row['active'] < 1 ? '<span class="lepopup-badge lepopup-badge-danger">'.esc_html__('Inactive', 'lepopup').'</span>' : '').'</span><label class="lepopup-table-list-created">'.esc_html__('Created', 'lepopup').': '.$lepopup->unixtime_string($row['created']).'</label></td>
					<td class="lepopup-table-list-column-preview"><a href="'.esc_html(str_replace('{ANTICACHE}', $lepopup->random_string(16), $preview_url)).'" target="_blank" data-href="'.esc_html($preview_url).'" class="lepopup-preview" title="'.esc_html__('Click the icon to preview popup on live site.', 'lepopup').'" onclick="jQuery(this).attr(\'href\', jQuery(this).attr(\'data-href\').replace(\'{ANTICACHE}\', (new Date).getTime()));"><i class="fas fa-eye"></i></a></td>
					<td class="lepopup-table-list-column-shortcode"><span class="lepopup-more-using" data-id="'.esc_html($row['id']).'" data-mode="form" title="'.esc_html__('Click the icon for info about using the popup.', 'lepopup').'" onclick="lepopup_more_using_open(this);"><i class="fas fa-code"></i></span><div><input type="text" value="'.esc_html($row['slug']).'" readonly="readonly" style="" onclick="this.focus();this.select();"></div></td>
					<td><a href="'.admin_url('admin.php').'?page=lepopup-log&form='.esc_html($row['id']).'">'.intval($row['entries']).'</a></td>
					<td>
						<div class="lepopup-table-list-actions">
							<span><i class="fas fa-ellipsis-v"></i></span>
							<div class="lepopup-table-list-menu">
								<ul>
									<li><a href="'.admin_url('admin.php').'?page=lepopup-add&id='.esc_html($row['id']).'">'.esc_html__('Edit', 'lepopup').'</a></li>
									<li><a href="#" data-status="'.($row['active'] > 0 ? 'active' : 'inactive').'" data-id="'.esc_html($row['id']).'" data-doing="'.($row['active'] > 0 ? esc_html__('Deactivating...', 'lepopup') : esc_html__('Activating...', 'lepopup')).'" onclick="return lepopup_forms_status_toggle(this);">'.($row['active'] > 0 ? esc_html__('Deactivate', 'lepopup') : esc_html__('Activate', 'lepopup')).'</a></li>
									<li><a href="#" data-id="'.esc_html($row['id']).'" data-doing="'.esc_html__('Duplicating...', 'lepopup').'" onclick="return lepopup_forms_duplicate(this);">'.esc_html__('Duplicate', 'lepopup').'</a></li>
									<li><a href="'.admin_url('admin.php').'?page=lepopup&lepopup-action=export&id='.esc_html($row['id']).'">'.esc_html__('Export popup definition', 'lepopup').'</a></li>
									<li><a href="'.admin_url('admin.php').'?page=lepopup&lepopup-action=export-csv&id='.esc_html($row['id']).'">'.esc_html__('Export all records as CSV', 'lepopup').'</a></li>
									'.($lepopup->advanced_options['admin-menu-stats'] != 'off' ? '<li><a href="'.admin_url('admin.php').'?page=lepopup-stats&form='.esc_html($row['id']).'">'.esc_html__('Statistics', 'lepopup').'</a></li>' : '').'
									'.($lepopup->advanced_options['admin-menu-stats'] != 'off' ? '<li><a href="#" data-id="'.esc_html($row['id']).'" data-doing="'.esc_html__('Reseting...', 'lepopup').'" onclick="return lepopup_stats_reset(this);">'.esc_html__('Reset Statistics', 'lepopup').'</a></li>' : '').'
									'.($lepopup->advanced_options['admin-menu-analytics'] != 'off' ? '<li><a href="'.admin_url('admin.php').'?page=lepopup-field-analytics&form='.esc_html($row['id']).'">'.esc_html__('Field Analytics', 'lepopup').'</a></li>' : '').'
									<li class="lepopup-table-list-menu-line"></li>
									<li><a href="#" data-id="'.esc_html($row['id']).'" data-doing="'.esc_html__('Deleting...', 'lepopup').'" onclick="return lepopup_forms_delete(this);">'.esc_html__('Delete', 'lepopup').'</a></li>
								</ul>
							</div>
						</div>
					</td>
				</tr>';
			}
		} else {
			echo '
				<tr><td colspan="5" class="lepopup-table-list-empty">'.((strlen($search_query) > 0) ? esc_html__('No results found for', 'lepopup').' "<strong>'.esc_html($search_query).'</strong>"' : esc_html__('List is empty.', 'lepopup')).'</td></tr>';
		}
		echo '
	</table>
	<div class="lepopup-pageswitcher">'.$switcher.'</div>
	<div class="lepopup-table-list-buttons">
		<form id="lepopup-import-form" enctype="multipart/form-data" method="post" action="'.admin_url('admin.php').'?page=lepopup&lepopup-action=import">
			<input id="lepopup-import-form-file" type="file" accept=".txt, .zip" name="lepopup-file" onchange="jQuery(\'#lepopup-import-form\').submit();">
		</form>
		<a class="lepopup-button lepopup-button-small" onclick="jQuery(\'#lepopup-import-form-file\').click(); return false;"><i class="fas fa-upload"></i><label>'.esc_html__('Import Popup', 'lepopup').'</label></a>
		<a href="'.admin_url('admin.php').'?page=lepopup-add" class="lepopup-button lepopup-button-small"><i class="fas fa-plus"></i><label>'.esc_html__('Create New Popup', 'lepopup').'</label></a>
	</div>
</div>
<div id="lepopup-global-message"></div>';
		echo $this->admin_dialog_html();
		if (!empty($this->error_message)) {
			echo '
<script>jQuery(document).ready(function(){lepopup_global_message_show("danger", "'.esc_html($this->error_message).'");});</script>';
		} else if (!empty($this->success_message)) {
			echo '
<script>jQuery(document).ready(function(){lepopup_global_message_show("success", "'.esc_html($this->success_message).'");});</script>';
		}
		echo '
<div class="lepopup-admin-popup-overlay" id="lepopup-more-using-overlay"></div>
<div class="lepopup-admin-popup" id="lepopup-more-using">
	<div class="lepopup-admin-popup-inner">
		<div class="lepopup-admin-popup-title">
			<a href="#" title="'.esc_html__('Close', 'lepopup').'" onclick="return lepopup_more_using_close();"><i class="fas fa-times"></i></a>
			<h3><i class="fas fa-code"></i> '.esc_html__('How To Use', 'lepopup').'<span></span></h3>
		</div>
		<div class="lepopup-admin-popup-content">
			<div class="lepopup-admin-popup-content-form">
			</div>
		</div>
		<div class="lepopup-admin-popup-loading"><i class="fas fa-spinner fa-spin"></i></div>
	</div>
</div>
<script>jQuery(document).ready(function(){lepopup_forms_ready();});</script>';
	}
	
	function admin_add_form() {
		global $wpdb, $lepopup;
		$predefined_options = array(
			'countries' => array(
				'label' => esc_html__('Countries', 'lepopup'),
				'options' => array(esc_html__("Afghanistan", "lepopup"),esc_html__("Albania", "lepopup"),esc_html__("Algeria", "lepopup"),esc_html__("American Samoa", "lepopup"),esc_html__("Andorra", "lepopup"),esc_html__("Angola", "lepopup"),esc_html__("Anguilla", "lepopup"),esc_html__("Antarctica", "lepopup"),esc_html__("Antigua And Barbuda", "lepopup"),esc_html__("Argentina", "lepopup"),esc_html__("Armenia", "lepopup"),esc_html__("Aruba", "lepopup"),esc_html__("Australia", "lepopup"),esc_html__("Austria", "lepopup"),esc_html__("Azerbaijan", "lepopup"),esc_html__("Bahamas", "lepopup"),esc_html__("Bahrain", "lepopup"),esc_html__("Bangladesh", "lepopup"),esc_html__("Barbados", "lepopup"),esc_html__("Belarus", "lepopup"),esc_html__("Belgium", "lepopup"),esc_html__("Belize", "lepopup"),esc_html__("Benin", "lepopup"),esc_html__("Bermuda", "lepopup"),esc_html__("Bhutan", "lepopup"),esc_html__("Bolivia", "lepopup"),esc_html__("Bosnia And Herzegovina", "lepopup"),esc_html__("Botswana", "lepopup"),esc_html__("Bouvet Island", "lepopup"),esc_html__("Brazil", "lepopup"),esc_html__("British Indian Ocean Territory", "lepopup"),esc_html__("Brunei Darussalam", "lepopup"),esc_html__("Bulgaria", "lepopup"),esc_html__("Burkina Faso", "lepopup"),esc_html__("Burundi", "lepopup"),esc_html__("Cambodia", "lepopup"),esc_html__("Cameroon", "lepopup"),esc_html__("Canada", "lepopup"),esc_html__("Cape Verde", "lepopup"),esc_html__("Cayman Islands", "lepopup"),esc_html__("Central African Republic", "lepopup"),esc_html__("Chad", "lepopup"),esc_html__("Chile", "lepopup"),esc_html__("China", "lepopup"),esc_html__("Christmas Island", "lepopup"),esc_html__("Cocos (Keeling) Islands", "lepopup"),esc_html__("Colombia", "lepopup"),esc_html__("Comoros", "lepopup"),esc_html__("Congo", "lepopup"),esc_html__("Congo, The Democratic Republic Of The", "lepopup"),esc_html__("Cook Islands", "lepopup"),esc_html__("Costa Rica", "lepopup"),esc_html__("Cote D'Ivoire", "lepopup"),esc_html__("Croatia (Local Name: Hrvatska)", "lepopup"),esc_html__("Cuba", "lepopup"),esc_html__("Cyprus", "lepopup"),esc_html__("Czech Republic", "lepopup"),esc_html__("Denmark", "lepopup"),esc_html__("Djibouti", "lepopup"),esc_html__("Dominica", "lepopup"),esc_html__("Dominican Republic", "lepopup"),esc_html__("East Timor", "lepopup"),esc_html__("Ecuador", "lepopup"),esc_html__("Egypt", "lepopup"),esc_html__("El Salvador", "lepopup"),esc_html__("Equatorial Guinea", "lepopup"),esc_html__("Eritrea", "lepopup"),esc_html__("Estonia", "lepopup"),esc_html__("Ethiopia", "lepopup"),esc_html__("Falkland Islands (Malvinas)", "lepopup"),esc_html__("Faroe Islands", "lepopup"),esc_html__("Fiji", "lepopup"),esc_html__("Finland", "lepopup"),esc_html__("France", "lepopup"),esc_html__("France, Metropolitan", "lepopup"),esc_html__("French Guiana", "lepopup"),esc_html__("French Polynesia", "lepopup"),esc_html__("French Southern Territories", "lepopup"),esc_html__("Gabon", "lepopup"),esc_html__("Gambia", "lepopup"),esc_html__("Georgia", "lepopup"),esc_html__("Germany", "lepopup"),esc_html__("Ghana", "lepopup"),esc_html__("Gibraltar", "lepopup"),esc_html__("Greece", "lepopup"),esc_html__("Greenland", "lepopup"),esc_html__("Grenada", "lepopup"),esc_html__("Guadeloupe", "lepopup"),esc_html__("Guam", "lepopup"),esc_html__("Guatemala", "lepopup"),esc_html__("Guinea", "lepopup"),esc_html__("Guinea-Bissau", "lepopup"),esc_html__("Guyana", "lepopup"),esc_html__("Haiti", "lepopup"),esc_html__("Heard And Mc Donald Islands", "lepopup"),esc_html__("Holy See (Vatican City State)", "lepopup"),esc_html__("Honduras", "lepopup"),esc_html__("Hong Kong", "lepopup"),esc_html__("Hungary", "lepopup"),esc_html__("Iceland", "lepopup"),esc_html__("India", "lepopup"),esc_html__("Indonesia", "lepopup"),esc_html__("Iran (Islamic Republic Of)", "lepopup"),esc_html__("Iraq", "lepopup"),esc_html__("Ireland", "lepopup"),esc_html__("Israel", "lepopup"),esc_html__("Italy", "lepopup"),esc_html__("Jamaica", "lepopup"),esc_html__("Japan", "lepopup"),esc_html__("Jordan", "lepopup"),esc_html__("Kazakhstan", "lepopup"),esc_html__("Kenya", "lepopup"),esc_html__("Kiribati", "lepopup"),esc_html__("Korea, Democratic People's Republic Of", "lepopup"),esc_html__("Korea, Republic Of", "lepopup"),esc_html__("Kuwait", "lepopup"),esc_html__("Kyrgyzstan", "lepopup"),esc_html__("Lao People's Democratic Republic", "lepopup"),esc_html__("Latvia", "lepopup"),esc_html__("Lebanon", "lepopup"),esc_html__("Lesotho", "lepopup"),esc_html__("Liberia", "lepopup"),esc_html__("Libyan Arab Jamahiriya", "lepopup"),esc_html__("Liechtenstein", "lepopup"),esc_html__("Lithuania", "lepopup"),esc_html__("Luxembourg", "lepopup"),esc_html__("Macau", "lepopup"),esc_html__("Macedonia, Former Yugoslav Republic Of", "lepopup"),esc_html__("Madagascar", "lepopup"),esc_html__("Malawi", "lepopup"),esc_html__("Malaysia", "lepopup"),esc_html__("Maldives", "lepopup"),esc_html__("Mali", "lepopup"),esc_html__("Malta", "lepopup"),esc_html__("Marshall Islands", "lepopup"),esc_html__("Martinique", "lepopup"),esc_html__("Mauritania", "lepopup"),esc_html__("Mauritius", "lepopup"),esc_html__("Mayotte", "lepopup"),esc_html__("Mexico", "lepopup"),esc_html__("Micronesia, Federated States Of", "lepopup"),esc_html__("Moldova, Republic Of", "lepopup"),esc_html__("Monaco", "lepopup"),esc_html__("Mongolia", "lepopup"),esc_html__("Montserrat", "lepopup"),esc_html__("Morocco", "lepopup"),esc_html__("Mozambique", "lepopup"),esc_html__("Myanmar", "lepopup"),esc_html__("Namibia", "lepopup"),esc_html__("Nauru", "lepopup"),esc_html__("Nepal", "lepopup"),esc_html__("Netherlands", "lepopup"),esc_html__("Netherlands Antilles", "lepopup"),esc_html__("New Calepopupia", "lepopup"),esc_html__("New Zealand", "lepopup"),esc_html__("Nicaragua", "lepopup"),esc_html__("Niger", "lepopup"),esc_html__("Nigeria", "lepopup"),esc_html__("Niue", "lepopup"),esc_html__("Norfolk Island", "lepopup"),esc_html__("Northern Mariana Islands", "lepopup"),esc_html__("Norway", "lepopup"),esc_html__("Oman", "lepopup"),esc_html__("Pakistan", "lepopup"),esc_html__("Palau", "lepopup"),esc_html__("Panama", "lepopup"),esc_html__("Papua New Guinea", "lepopup"),esc_html__("Paraguay", "lepopup"),esc_html__("Peru", "lepopup"),esc_html__("Philippines", "lepopup"),esc_html__("Pitcairn", "lepopup"),esc_html__("Poland", "lepopup"),esc_html__("Portugal", "lepopup"),esc_html__("Puerto Rico", "lepopup"),esc_html__("Qatar", "lepopup"),esc_html__("Reunion", "lepopup"),esc_html__("Romania", "lepopup"),esc_html__("Russian Federation", "lepopup"),esc_html__("Rwanda", "lepopup"),esc_html__("Saint Kitts And Nevis", "lepopup"),esc_html__("Saint Lucia", "lepopup"),esc_html__("Saint Vincent And The Grenadines", "lepopup"),esc_html__("Samoa", "lepopup"),esc_html__("San Marino", "lepopup"),esc_html__("Sao Tome And Principe", "lepopup"),esc_html__("Saudi Arabia", "lepopup"),esc_html__("Senegal", "lepopup"),esc_html__("Seychelles", "lepopup"),esc_html__("Sierra Leone", "lepopup"),esc_html__("Singapore", "lepopup"),esc_html__("Slovakia (Slovak Republic)", "lepopup"),esc_html__("Slovenia", "lepopup"),esc_html__("Solomon Islands", "lepopup"),esc_html__("Somalia", "lepopup"),esc_html__("South Africa", "lepopup"),esc_html__("South Georgia, South Sandwich Islands", "lepopup"),esc_html__("Spain", "lepopup"),esc_html__("Sri Lanka", "lepopup"),esc_html__("St. Helena", "lepopup"),esc_html__("St. Pierre And Miquelon", "lepopup"),esc_html__("Sudan", "lepopup"),esc_html__("Suriname", "lepopup"),esc_html__("Svalbard And Jan Mayen Islands", "lepopup"),esc_html__("Swaziland", "lepopup"),esc_html__("Sweden", "lepopup"),esc_html__("Switzerland", "lepopup"),esc_html__("Syrian Arab Republic", "lepopup"),esc_html__("Taiwan", "lepopup"),esc_html__("Tajikistan", "lepopup"),esc_html__("Tanzania, United Republic Of", "lepopup"),esc_html__("Thailand", "lepopup"),esc_html__("Togo", "lepopup"),esc_html__("Tokelau", "lepopup"),esc_html__("Tonga", "lepopup"),esc_html__("Trinidad And Tobago", "lepopup"),esc_html__("Tunisia", "lepopup"),esc_html__("Turkey", "lepopup"),esc_html__("Turkmenistan", "lepopup"),esc_html__("Turks And Caicos Islands", "lepopup"),esc_html__("Tuvalu", "lepopup"),esc_html__("Uganda", "lepopup"),esc_html__("Ukraine", "lepopup"),esc_html__("United Arab Emirates", "lepopup"),esc_html__("United Kingdom", "lepopup"),esc_html__("United States", "lepopup"),esc_html__("United States Minor Outlying Islands", "lepopup"),esc_html__("Uruguay", "lepopup"),esc_html__("Uzbekistan", "lepopup"),esc_html__("Vanuatu", "lepopup"),esc_html__("Venezuela", "lepopup"),esc_html__("Vietnam", "lepopup"),esc_html__("Virgin Islands (British)", "lepopup"),esc_html__("Virgin Islands (U.S.)", "lepopup"),esc_html__("Wallis And Futuna Islands", "lepopup"),esc_html__("Western Sahara", "lepopup"),esc_html__("Yemen", "lepopup"),esc_html__("Yugoslavia", "lepopup"),esc_html__("Zambia", "lepopup"),esc_html__("Zimbabwe", "lepopup"))
			),
			'us-states' => array(
				'label' => esc_html__('U.S. States', 'lepopup'),
				'options' => array(esc_html__("Alabama", "lepopup"),esc_html__("Alaska", "lepopup"),esc_html__("Arizona", "lepopup"),esc_html__("Arkansas", "lepopup"),esc_html__("California", "lepopup"),esc_html__("Colorado", "lepopup"),esc_html__("Connecticut", "lepopup"),esc_html__("Delaware", "lepopup"),esc_html__("District Of Columbia", "lepopup"),esc_html__("Florida", "lepopup"),esc_html__("Georgia", "lepopup"),esc_html__("Hawaii", "lepopup"),esc_html__("Idaho", "lepopup"),esc_html__("Illinois", "lepopup"),esc_html__("Indiana", "lepopup"),esc_html__("Iowa", "lepopup"),esc_html__("Kansas", "lepopup"),esc_html__("Kentucky", "lepopup"),esc_html__("Louisiana", "lepopup"),esc_html__("Maine", "lepopup"),esc_html__("Maryland", "lepopup"),esc_html__("Massachusetts", "lepopup"),esc_html__("Michigan", "lepopup"),esc_html__("Minnesota", "lepopup"),esc_html__("Mississippi", "lepopup"),esc_html__("Missouri", "lepopup"),esc_html__("Montana", "lepopup"),esc_html__("Nebraska", "lepopup"),esc_html__("Nevada", "lepopup"),esc_html__("New Hampshire", "lepopup"),esc_html__("New Jersey", "lepopup"),esc_html__("New Mexico", "lepopup"),esc_html__("New York", "lepopup"),esc_html__("North Carolina", "lepopup"),esc_html__("North Dakota", "lepopup"),esc_html__("Ohio", "lepopup"),esc_html__("Oklahoma", "lepopup"),esc_html__("Oregon", "lepopup"),esc_html__("Pennsylvania", "lepopup"),esc_html__("Rhode Island", "lepopup"),esc_html__("South Carolina", "lepopup"),esc_html__("South Dakota", "lepopup"),esc_html__("Tennessee", "lepopup"),esc_html__("Texas", "lepopup"),esc_html__("Utah", "lepopup"),esc_html__("Vermont", "lepopup"),esc_html__("Virginia", "lepopup"),esc_html__("Washington", "lepopup"),esc_html__("West Virginia", "lepopup"),esc_html__("Wisconsin", "lepopup"),esc_html__("Wyoming", "lepopup"))
			),
			'canadian-provinces' => array(
				'label' => esc_html__('Canadian Provinces', 'lepopup'),
				'options' => array(esc_html__("Alberta", "lepopup"),esc_html__("British Columbia", "lepopup"),esc_html__("Manitoba", "lepopup"),esc_html__("New Brunswick", "lepopup"),esc_html__("Newfoundland & Labrador", "lepopup"),esc_html__("Northwest Territories", "lepopup"),esc_html__("Nova Scotia", "lepopup"),esc_html__("Nunavut", "lepopup"),esc_html__("Ontario", "lepopup"),esc_html__("Prince Edward Island", "lepopup"),esc_html__("Quebec", "lepopup"),esc_html__("Saskatchewan", "lepopup"),esc_html__("Yukon", "lepopup"))
			),
			'uk-counties' => array(
				'label' => esc_html__('UK Counties', 'lepopup'),
				'options' => array(esc_html__("Aberdeen City", "lepopup"),esc_html__("Aberdeenshire", "lepopup"),esc_html__("Angus", "lepopup"),esc_html__("Antrim", "lepopup"),esc_html__("Argyll and Bute", "lepopup"),esc_html__("Armagh", "lepopup"),esc_html__("Avon", "lepopup"),esc_html__("Banffshire", "lepopup"),esc_html__("Bedfordshire", "lepopup"),esc_html__("Berkshire", "lepopup"),esc_html__("Blaenau Gwent", "lepopup"),esc_html__("Borders", "lepopup"),esc_html__("Bridgend", "lepopup"),esc_html__("Bristol", "lepopup"),esc_html__("Buckinghamshire", "lepopup"),esc_html__("Caerphilly", "lepopup"),esc_html__("Cambridgeshire", "lepopup"),esc_html__("Cardiff", "lepopup"),esc_html__("Carmarthenshire", "lepopup"),esc_html__("Ceredigion", "lepopup"),esc_html__("Channel Islands", "lepopup"),esc_html__("Cheshire", "lepopup"),esc_html__("Clackmannan", "lepopup"),esc_html__("Cleveland", "lepopup"),esc_html__("Conwy", "lepopup"),esc_html__("Cornwall", "lepopup"),esc_html__("Cumbria", "lepopup"),esc_html__("Denbighshire", "lepopup"),esc_html__("Derbyshire", "lepopup"),esc_html__("Devon", "lepopup"),esc_html__("Dorset", "lepopup"),esc_html__("Down", "lepopup"),esc_html__("Dumfries and Galloway", "lepopup"),esc_html__("Durham", "lepopup"),esc_html__("East Ayrshire", "lepopup"),esc_html__("East Dunbartonshire", "lepopup"),esc_html__("East Lothian", "lepopup"),esc_html__("East Renfrewshire", "lepopup"),esc_html__("East Riding of Yorkshire", "lepopup"),esc_html__("East Sussex", "lepopup"),esc_html__("Edinburgh City", "lepopup"),esc_html__("Essex", "lepopup"),esc_html__("Falkirk", "lepopup"),esc_html__("Fermanagh", "lepopup"),esc_html__("Fife", "lepopup"),esc_html__("Flintshire", "lepopup"),esc_html__("Glasgow (City of)", "lepopup"),esc_html__("Gloucestershire", "lepopup"),esc_html__("Greater Manchester", "lepopup"),esc_html__("Gwynedd", "lepopup"),esc_html__("Hampshire", "lepopup"),esc_html__("Herefordshire", "lepopup"),esc_html__("Hertfordshire", "lepopup"),esc_html__("Highland", "lepopup"),esc_html__("Humberside", "lepopup"),esc_html__("Inverclyde", "lepopup"),esc_html__("Isle of Anglesey", "lepopup"),esc_html__("Isle of Man", "lepopup"),esc_html__("Isle of Wight", "lepopup"),esc_html__("Isles of Scilly", "lepopup"),esc_html__("Kent", "lepopup"),esc_html__("Lancashire", "lepopup"),esc_html__("Leicestershire", "lepopup"),esc_html__("Lincolnshire", "lepopup"),esc_html__("London", "lepopup"),esc_html__("Londonderry", "lepopup"),esc_html__("Merseyside", "lepopup"),esc_html__("Merthyr Tydfil", "lepopup"),esc_html__("Middlesex", "lepopup"),esc_html__("Midlothian", "lepopup"),esc_html__("Monmouthshire", "lepopup"),esc_html__("Moray", "lepopup"),esc_html__("Neath Port Talbot", "lepopup"),esc_html__("Newport", "lepopup"),esc_html__("Norfolk", "lepopup"),esc_html__("North Ayrshire", "lepopup"),esc_html__("North East Lincolnshire", "lepopup"),esc_html__("North Lanarkshire", "lepopup"),esc_html__("North Yorkshire", "lepopup"),esc_html__("Northamptonshire", "lepopup"),esc_html__("Northumberland", "lepopup"),esc_html__("Nottinghamshire", "lepopup"),esc_html__("Orkney", "lepopup"),esc_html__("Oxfordshire", "lepopup"),esc_html__("Pembrokeshire", "lepopup"),esc_html__("Perthshire and Kinross", "lepopup"),esc_html__("Powys", "lepopup"),esc_html__("Renfrewshire", "lepopup"),esc_html__("Rhondda Cynon Taff", "lepopup"),esc_html__("Roxburghshire", "lepopup"),esc_html__("Rutland", "lepopup"),esc_html__("Shetland", "lepopup"),esc_html__("Shropshire", "lepopup"),esc_html__("Somerset", "lepopup"),esc_html__("South Ayrshire", "lepopup"),esc_html__("South Lanarkshire", "lepopup"),esc_html__("South Yorkshire", "lepopup"),esc_html__("Staffordshire", "lepopup"),esc_html__("Stirling", "lepopup"),esc_html__("Suffolk", "lepopup"),esc_html__("Surrey", "lepopup"),esc_html__("Swansea", "lepopup"),esc_html__("The Vale of Glamorgan", "lepopup"),esc_html__("Torfaen", "lepopup"),esc_html__("Tyne and Wear", "lepopup"),esc_html__("Tyrone", "lepopup"),esc_html__("Warwickshire", "lepopup"),esc_html__("West Dunbartonshire", "lepopup"),esc_html__("West Lothian", "lepopup"),esc_html__("West Midlands", "lepopup"),esc_html__("West Sussex", "lepopup"),esc_html__("West Yorkshire", "lepopup"),esc_html__("Western Isles", "lepopup"),esc_html__("Wiltshire", "lepopup"),esc_html__("Worcestershire", "lepopup"),esc_html__("Wrexham", "lepopup"))
			),
			'german-states' => array(
				'label' => esc_html__('German States', 'lepopup'),
				'options' => array(esc_html__("Baden-Wurttemberg", "lepopup"),esc_html__("Bavaria", "lepopup"),esc_html__("Berlin", "lepopup"),esc_html__("Brandenburg", "lepopup"),esc_html__("Bremen", "lepopup"),esc_html__("Hamburg", "lepopup"),esc_html__("Hesse", "lepopup"),esc_html__("Lower Saxony", "lepopup"),esc_html__("Mecklenburg-West Pomerania", "lepopup"),esc_html__("North Rhine-Westphalia", "lepopup"),esc_html__("Rhineland-Palatinate", "lepopup"),esc_html__("Saarland", "lepopup"),esc_html__("Saxony", "lepopup"),esc_html__("Saxony-Anhalt", "lepopup"),esc_html__("Schleswig-Holstein", "lepopup"),esc_html__("Thuringia", "lepopup"))
			),
			'dutch-provinces' => array(
				'label' => esc_html__('Dutch Provinces', 'lepopup'),
				'options' => array(esc_html__("Drente", "lepopup"),esc_html__("Flevoland", "lepopup"),esc_html__("Friesland", "lepopup"),esc_html__("Gelderland", "lepopup"),esc_html__("Groningen", "lepopup"),esc_html__("Limburg", "lepopup"),esc_html__("Noord-Brabant", "lepopup"),esc_html__("Noord-Holland", "lepopup"),esc_html__("Overijssel", "lepopup"),esc_html__("Utrecht", "lepopup"),esc_html__("Zeeland", "lepopup"),esc_html__("Zuid-Holland", "lepopup"))
			),
			'australian-states' => array(
				'label' => esc_html__('Australian States', 'lepopup'),
				'options' => array(esc_html__("Australian Capital Territory", "lepopup"),esc_html__("New South Wales", "lepopup"),esc_html__("Northern Territory", "lepopup"),esc_html__("Queensland", "lepopup"),esc_html__("South Australia", "lepopup"),esc_html__("Tasmania", "lepopup"),esc_html__("Victoria", "lepopup"),esc_html__("Western Australia", "lepopup"))
			),
			'continents' => array(
				'label' => esc_html__('Continents', 'lepopup'),
				'options' => array(esc_html__("Africa", "lepopup"),esc_html__("Antarctica", "lepopup"),esc_html__("Asia", "lepopup"),esc_html__("Australia", "lepopup"),esc_html__("Europe", "lepopup"),esc_html__("North America", "lepopup"),esc_html__("South America", "lepopup"))
			),
			'days' => array(
				'label' => esc_html__('Days', 'lepopup'),
				'options' => array(esc_html__("Monday", "lepopup"),esc_html__("Tuesday", "lepopup"),esc_html__("Wednesday", "lepopup"),esc_html__("Thursday", "lepopup"),esc_html__("Friday", "lepopup"),esc_html__("Saturday", "lepopup"),esc_html__("Sunday", "lepopup"))
			),
			'months' => array(
				'label' => esc_html__('Months', 'lepopup'),
				'options' => array(esc_html__("January", "lepopup"),esc_html__("February", "lepopup"),esc_html__("March", "lepopup"),esc_html__("April", "lepopup"),esc_html__("May", "lepopup"),esc_html__("June", "lepopup"),esc_html__("July", "lepopup"),esc_html__("August", "lepopup"),esc_html__("September", "lepopup"),esc_html__("October", "lepopup"),esc_html__("November", "lepopup"),esc_html__("December", "lepopup"))
			)
		);
		if (!defined("UAP_CORE")) wp_deregister_script('wp-color-picker-alpha');
		$default_form_options = $lepopup->default_form_options();
		$form_id = null;
		$form_options = null;
		$form_details = array();
		$form_elements = array();
		$slug = 'popup-'.date("Y-m-d-h-i-s");
		if (array_key_exists('id', $_REQUEST)) {
			$form_id = intval($_REQUEST['id']);
			$form_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND id = '".esc_sql($form_id)."'", ARRAY_A);
			if (!empty($form_details)) $form_options = json_decode($form_details['options'], true);
			else $form_id = null;
		}
		if (!empty($form_options)) $form_options = array_merge($default_form_options, $form_options);
		else $form_options = $default_form_options;
		$default_page_options = $lepopup->default_form_options('page');
		$default_page_confirmation_options = $lepopup->default_form_options('page-confirmation');
		$form_pages = array(array_merge($default_page_options, array('id' => 1, 'type' => 'page')));
		
		if (!empty($form_details)) {
			$slug = $form_details['slug'];
			$form_options['name'] = $form_details['name'];
			$form_options['active'] = $form_details['active'] > 0 ? 'on' : 'off';

			$form_pages = json_decode($form_details['pages'], true);
			if (is_array($form_pages)) {
				foreach($form_pages as $key => $page_options) {
					if (is_array($page_options)) {
						if ($page_options['type'] == 'page') $page_options = array_merge($default_page_options, $page_options);
						else $page_options = array_merge($default_page_confirmation_options, $page_options);
						$form_pages[$key] = $page_options;
					} else unset($form_pages[$key]);
				}
				$form_pages = array_values($form_pages);
			} else $form_pages = array(array_merge($default_page_options, array('id' => 1)));
			
			$form_elements = json_decode($form_details['elements'], true);
			if (is_array($form_elements)) {
				foreach($form_elements as $key => $form_element_raw) {
					$element_options = json_decode($form_element_raw, true);
					if (is_array($element_options) && array_key_exists('type', $element_options)) {
						$default_element_options = $lepopup->default_form_options($element_options['type']);
						$element_options = array_merge($default_element_options, $element_options);
						$form_elements[$key] = json_encode($element_options);
					} else unset($form_elements[$key]);
				}
				$form_elements = array_values($form_elements);
			} else $form_elements = array();
		}
		$confirmation_found = false;
		foreach($form_pages as $form_page) {
			if ($form_page['id'] == 'confirmation') {
				$confirmation_found = true;
				break;
			}
		}
		if (!$confirmation_found) {
			$form_pages[] = array_merge($default_page_confirmation_options, array('id' => 'confirmation', 'type' => 'page-confirmation', 'name' => esc_html__('Confirmation', 'lepopup')));
			$default_element_options = $lepopup->default_form_options('html');
			$element_options = array(
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
			$element_options = array_merge($default_element_options, $element_options);
			$form_elements[] = json_encode($element_options);
		}
		echo (!empty($form_id) ? '<script>lepopup_gettingstarted_enable = "off";</script>' : '').'
<style>body {position: absolute; width: 100%;}
.mce-menu .mce-menu-item-normal.mce-active, .mce-menu .mce-menu-item-preview.mce-active, .mce-menu .mce-menu-item.mce-selected, .mce-menu .mce-menu-item:focus, .mce-menu .mce-menu-item:hover {background: #bd4070; color: #fff !important;}
.mce-menu .mce-menu-item-normal.mce-active span, .mce-menu .mce-menu-item-preview.mce-active span, .mce-menu .mce-menu-item.mce-selected span, .mce-menu .mce-menu-item:focus span, .mce-menu .mce-menu-item:hover span {color: #fff !important;}
</style>
<div class="wrap lepopup-admin lepopup-admin-editor">
	<h2>'.esc_html__('Green Popups - Edit Popup', 'lepopup').'
		<a class="lepopup-button-h2" href="'.admin_url('admin.php').'?page=lepopup-add">'.esc_html__('Create New Popup', 'lepopup').'</a>
		<a href="'.(defined('UAP_CORE') ? 'https://greenpopups.com/documentation/#standalone-script' : 'https://greenpopups.com/documentation/#wordpress-plugin').'" class="lepopup-button-h2" target="_blank">'.esc_html__('Online Documentation', 'lepopup').'</a>
	</h2>
<div class="lepopup-form-editor">
	<div class="lepopup-toolbars">
		<div class="lepopup-header">
			<div class="lepopup-header-settings"><span data-type="settings" onclick="return lepopup_properties_open(this);"><i class="fas fa-cogs"></i></span></div>
			<div class="lepopup-header-slug"><input type="text" id="lepopup-slug" placeholder="'.esc_html__('Unique slug...', 'lepopup').'" title="'.esc_html__('Unique slug/ID of the popup.', 'lepopup').'" value="'.esc_html($slug).'" /></div>
			<div class="lepopup-header-save"><span onclick="return lepopup_save(this);"><i class="far fa-save"></i>'.esc_html__('Save', 'lepopup').'</span></div>
			<div class="lepopup-header-using"><span'.(!empty($form_id) ? ' data-id="'.esc_html($form_id).'"' : ' style="display: none;"').' data-mode="form" onclick="lepopup_more_using_open(this);"><i class="fas fa-code"></i></span></div>
		</div>
		<div class="lepopup-pages-bar">
			<ul class="lepopup-pages-bar-items">';
			foreach ($form_pages as $form_page) {
				if ($form_page['id'] == 'confirmation') {
					echo '
				<li class="lepopup-pages-bar-item-confirmation" data-id="'.esc_html($form_page['id']).'" data-name="'.esc_html($form_page['name']).'"><label onclick="return lepopup_pages_activate(this);">'.esc_html($form_page['name']).'</label><span><a href="#" data-type="page-confirmation" onclick="return lepopup_properties_open(this);"><i class="fas fa-cog"></i></a></span></li>';
				} else {
					echo '
				<li class="lepopup-pages-bar-item" data-id="'.esc_html($form_page['id']).'" data-name="'.esc_html($form_page['name']).'"><label onclick="return lepopup_pages_activate(this);">'.esc_html($form_page['name']).'</label><span><a href="#" data-type="page" onclick="return lepopup_properties_open(this);"><i class="fas fa-cog"></i></a><a href="#" class="lepopup-pages-bar-item-delete'.(sizeof($form_pages) <= 1 ? ' lepopup-pages-bar-item-delete-disabled' : '').'" onclick="return lepopup_pages_delete(this);"><i class="fas fa-trash-alt"></i></a></span></li>';
				}
			}
			echo '
				<li class="lepopup-pages-add" onclick="return lepopup_pages_add();"><label><i class="fas fa-plus"></i> '.esc_html__('Add Page', 'lepopup').'</label></li>
			</ul>
		</div>
		<div class="lepopup-toolbar">
			<ul class="lepopup-toolbar-list">';
			foreach ($lepopup->toolbar_tools as $key => $value) {
				if (array_key_exists('options', $value)) {
					echo '
				<li class="lepopup-toolbar-tool-'.esc_html($value['type']).'" class="lepopup-toolbar-list-options" data-type="'.esc_html($key).'" data-option="2"><a href="#" title="'.esc_html($value['title']).'"><i class="'.esc_html($value['icon']).'"></i></a><ul>';
					foreach ($value['options'] as $option_key => $option_value) {
						echo '<li data-type="'.esc_html($key).'" data-option="'.esc_html($option_key).'" title=""><a href="#" title="'.esc_html($value['title']).'">'.esc_html($option_value).'</a></li>';
					}
					echo '</ul></li>';
					
				} else {
					echo '
				<li class="lepopup-toolbar-tool-'.esc_html($value['type']).'" data-type="'.esc_html($key).'"><a href="#" title="'.esc_html($value['title']).'"><i class="'.esc_html($value['icon']).'"></i></a></li>';
				}
			}
			echo '
			</ul>
		</div>
	</div>
	<div class="lepopup-builder"><div class="lepopup-form-global-style"></div>';
		foreach ($form_pages as $form_page) {
			echo '
			<div id="lepopup-form-'.esc_html($form_page['id']).'" class="lepopup-form" _data-parent="'.esc_html($form_page['id']).'">
				<div class="lepopup-basic-frame"><div class="lepopup-elements"></div></div><div class="lepopup-hidden-elements"></div>
			</div>';
		}
		echo '
	</div>
	<div class="lepopup-layers">
		<h4><i class="fas fa-arrows-alt"></i>'.esc_html__('Layers', 'lepopup').'</h4>
		<ul class="lepopup-layers-list"></ul>
		<span>'.esc_html__('1. Click any button on elements toolbar to add new layer. 2. Sort layers to change z-index.', 'lepopup').'</span>
	</div>
</div>
<span class="lepopup-properties-panel-close" title="'.esc_html__('Close', 'lepopup').'" onclick="return lepopup_properties_panel_close();"><i class="fas fa-times"></i></span>
<div class="lepopup-properties-panel">
	<div class="lepopup-properties-panel-inner">
		<div class="lepopup-admin-popup-content-form">
		</div>
		
	</div>
	<div class="lepopup-properties-panel-loading"><i class="fas fa-spinner fa-spin"></i></div>
</div>
<div class="lepopup-admin-popup-overlay" id="lepopup-element-properties-overlay"></div>
<div class="lepopup-admin-popup" id="lepopup-element-properties">
	<div class="lepopup-admin-popup-inner">
		<div class="lepopup-admin-popup-title">
			<a href="#" title="'.esc_html__('Close', 'lepopup').'" onclick="return lepopup_properties_close();"><i class="fas fa-times"></i></a>
			<h3><i class="fas fa-cog"></i> '.esc_html__('Element Properties', 'lepopup').'</h3>
		</div>
		<div class="lepopup-admin-popup-content">
			<div class="lepopup-admin-popup-content-form">
			</div>
		</div>
		<div class="lepopup-admin-popup-buttons">
			<a class="lepopup-admin-button" href="#" onclick="return lepopup_properties_save();"><i class="fas fa-check"></i><label>'.esc_html__('Save Details', 'lepopup').'</label></a>
		</div>
		<div class="lepopup-admin-popup-loading"><i class="fas fa-spinner fa-spin"></i></div>
	</div>
</div>
<div class="lepopup-fa-selector-overlay"></div>
<div class="lepopup-fa-selector">
	<div class="lepopup-fa-selector-inner">
		<div class="lepopup-fa-selector-header">
			<a href="#" title="'.esc_html__('Close', 'lepopup').'" onclick="return lepopup_fa_selector_close();"><i class="fas fa-times"></i></a>
			<input type="text" placeholder="'.esc_html__('Search...', 'lepopup').'">
		</div>
		<div class="lepopup-fa-selector-content">
			<span title="No icon" onclick="lepopup_fa_selector_set(this);"><i class=""></i></span>';
		if ($lepopup->options['fa-enable'] == 'on') {
			if ($lepopup->options['fa-solid-enable'] == 'on') {
				foreach ($lepopup->fa_solid as $value) {
					echo '<span title="'.esc_html(ucwords(str_replace(array("-"), array(" "), $value))).'" onclick="lepopup_fa_selector_set(this);"><i class="fas fa-'.esc_html($value).'"></i></span>';
				}
			}
			if ($lepopup->options['fa-regular-enable'] == 'on') {
				foreach ($lepopup->fa_regular as $value) {
					echo '<span title="'.esc_html(ucwords(str_replace(array("-"), array(" "), $value))).'" onclick="lepopup_fa_selector_set(this);"><i class="far fa-'.esc_html($value).'"></i></span>';
				}
			}
			if ($lepopup->options['fa-brands-enable'] == 'on') {
				foreach ($lepopup->fa_brands as $value) {
					echo '<span title="'.esc_html(ucwords(str_replace(array("-"), array(" "), $value))).'" onclick="lepopup_fa_selector_set(this);"><i class="fab fa-'.esc_html($value).'"></i></span>';
				}
			}
		} else {
			foreach ($lepopup->font_awesome_basic as $value) {
				echo '<span title="'.esc_html(ucwords(str_replace(array("-"), array(" "), $value))).'" onclick="lepopup_fa_selector_set(this);"><i class="lepopup-fa lepopup-fa-'.esc_html($value).'"></i></span>';
			}
		}
		echo '
		</div>
	</div>
</div>
<div class="lepopup-admin-popup-overlay" id="lepopup-bulk-options-overlay"></div>
<div class="lepopup-admin-popup" id="lepopup-bulk-options">
	<div class="lepopup-admin-popup-inner">
		<div class="lepopup-admin-popup-title">
			<a href="#" title="'.esc_html__('Close', 'lepopup').'" onclick="return lepopup_bulk_options_close();"><i class="fas fa-times"></i></a>
			<h3><i class="fas fa-list-ul"></i> '.esc_html__('Add Bulk Options', 'lepopup').'</h3>
		</div>
		<div class="lepopup-admin-popup-content">
			<div class="lepopup-admin-popup-content-form">
				<div class="lepopup-bulk-options-text">'.esc_html__('Click a category on the left side to insert predefined options. You can edit the options on the right side or enter your own options. One option per line!', 'lepopup').'</div>
				<div class="lepopup-bulk-options-container">
					<div class="lepopup-bulk-categories">
						<ul>
							<li data-category="existing" onclick="return lepopup_bulk_category_add(this);"><i class="fas fa-plus"></i> '.esc_html__('Existing Options', 'lepopup').'</li>';
		foreach($predefined_options as $key => $value) {
			echo '
							<li data-category="'.esc_html($key).'" onclick="return lepopup_bulk_category_add(this);"><i class="fas fa-plus"></i> '.esc_html($value['label']).'</li>';
		}
		echo '
						</ul>
					</div>
					<div class="lepopup-bulk-editor">
						<textarea></textarea>
					</div>
				</div>
				<div class="lepopup-bulk-options-text"><input class="lepopup-checkbox-toggle" type="checkbox" id="lepopup-bulk-options-overwrite"><label for="lepopup-bulk-options-overwrite"></label> '.esc_html__('Overwrite existing options', 'lepopup').'</div>
			</div>
		</div>
		<div class="lepopup-admin-popup-buttons">
			<a class="lepopup-admin-button" href="#" onclick="return lepopup_bulk_options_add();"><i class="fas fa-plus"></i><label>'.esc_html__('Add Options', 'lepopup').'</label></a>
		</div>
	</div>
</div>
<div class="lepopup-admin-popup-overlay" id="lepopup-more-using-overlay"></div>
<div class="lepopup-admin-popup" id="lepopup-more-using">
	<div class="lepopup-admin-popup-inner">
		<div class="lepopup-admin-popup-title">
			<a href="#" title="'.esc_html__('Close', 'lepopup').'" onclick="return lepopup_more_using_close();"><i class="fas fa-times"></i></a>
			<h3><i class="fas fa-code"></i> '.esc_html__('How To Use', 'lepopup').'<span></span></h3>
		</div>
		<div class="lepopup-admin-popup-content">
			<div class="lepopup-admin-popup-content-form">
			</div>
		</div>
		<div class="lepopup-admin-popup-loading"><i class="fas fa-spinner fa-spin"></i></div>
	</div>
</div>
<div id="lepopup-global-message"></div>';
		if (empty($form_id)) {
			echo '
<div class="lepopup-admin-create-overlay">
	<div class="lepopup-admin-create">
		<div class="lepopup-admin-create-content">
			<div>
				<input type="text" id="lepopup-create-name" value="" placeholder="'.esc_html__('Please enter the popup name...', 'lepopup').'" />
			</div>
			<div class="lepopup-admin-buttons-create">
				<a class="lepopup-admin-button lepopup-admin-button-create" onclick="return lepopup_create();"><i class="fas fa-check"></i> '.esc_html__('Create New Popup', 'lepopup').'</a>
			</div>
		</div>
	</div>
</div>';
		}
		echo $this->admin_dialog_html();
		$webfonts_array = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_webfonts WHERE deleted = '0' ORDER BY family", ARRAY_A);
		$webfonts = array();
		foreach ($webfonts_array as $webfont) {
			$webfonts[] = $webfont['family'];
		}
		$providers = array();
		$providers = apply_filters('lepopup_providers', $providers);
		$payment_providers = array();
		$payment_providers = apply_filters('lepopup_payment_providers', $payment_providers);

		$forms = $wpdb->get_results("SELECT slug, name FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND id != '".intval($form_id)."' ORDER BY name ASC", ARRAY_A);
		
		echo '
<input type="hidden" id="lepopup-id" value="'.intval($form_id).'" />
<script>
	var lepopup_webfonts = '.json_encode($webfonts).';
	var lepopup_animations_in = '.json_encode($lepopup->animation_effects_in).';
	var lepopup_animations_out = '.json_encode($lepopup->animation_effects_out).';
	var lepopup_localfonts = '.json_encode($lepopup->local_fonts).';
	var lepopup_font_weights = '.json_encode($lepopup->font_weights).';
	var lepopup_toolbar_tools = '.json_encode($lepopup->toolbar_tools).';
	var lepopup_meta = '.json_encode($lepopup->element_properties_meta).';
	var lepopup_validators = '.json_encode($lepopup->validators_meta).';
	var lepopup_filters = '.json_encode($lepopup->filters_meta).';
	var lepopup_confirmations = '.json_encode($lepopup->confirmations_meta).';
	var lepopup_notifications = '.json_encode($lepopup->notifications_meta).';
	var lepopup_integrations = '.json_encode($lepopup->integrations_meta).';
	var lepopup_payment_gateway = '.json_encode($lepopup->payment_gateways_meta).';
	var lepopup_math_expressions_meta = '.json_encode($lepopup->math_meta).';
	var lepopup_logic_rules = '.json_encode($lepopup->logic_rules).';
	var lepopup_predefined_options = '.json_encode($predefined_options).';
	var lepopup_form_options = '.json_encode($form_options).';
	var lepopup_form_pages_raw = '.json_encode($form_pages).';
	var lepopup_form_elements_raw = '.json_encode($form_elements).';
	var lepopup_integration_providers = '.json_encode($providers).';
	var lepopup_payment_providers = '.json_encode($payment_providers).';
	var lepopup_forms = '.json_encode($forms).';
	jQuery(document).ready(function(){lepopup_form_ready();});
</script>
</div>';
	}

	function admin_dialog_html() {
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

	function admin_campaigns() {
		global $wpdb, $lepopup;
		
		if (isset($_GET["s"])) $search_query = trim(stripslashes($_GET["s"]));
		else $search_query = "";
		
		$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."lepopup_campaigns WHERE deleted = '0'".((strlen($search_query) > 0) ? " AND name LIKE '%".esc_sql($wpdb->esc_like($search_query))."%'" : ""), ARRAY_A);
		$total = $tmp["total"];
		$totalpages = ceil($total/LEPOPUP_RECORDS_PER_PAGE);
		if ($totalpages == 0) $totalpages = 1;
		if (isset($_GET["p"])) $page = intval($_GET["p"]);
		else $page = 1;
		if ($page < 1 || $page > $totalpages) $page = 1;
		$switcher = $lepopup->page_switcher(admin_url('admin.php').'?page=lepopup'.((strlen($search_query) > 0) ? '&s='.rawurlencode($search_query) : ''), $page, $totalpages);

		if (isset($_GET['o'])) {
			$sort = $_GET['o'];
			if (in_array($sort, $lepopup->sort_methods)) {
				if ($sort != $lepopup->options['sort-campaigns']) {
					update_option('lepopup-sort-campaigns', $sort);
					$lepopup->options['sort-campaigns'] = $sort;
				}
			} else $sort = $lepopup->options['sort-campaigns'];
		} else $sort = $lepopup->options['sort-campaigns'];
		$orderby = 't1.created DESC';
		switch ($sort) {
			case 'name-az':
				$orderby = 't1.name ASC';
				break;
			case 'name-za':
				$orderby = 't1.name DESC';
				break;
			case 'date-az':
				$orderby = 't1.created ASC';
				break;
			default:
				$orderby = 't1.created DESC';
				break;
		}
		
		$sql = "SELECT t1.*, t2.forms, t2.submits, t2.impressions FROM ".$wpdb->prefix."lepopup_campaigns t1 LEFT JOIN (SELECT COUNT(*) AS forms, SUM(tt1.submits) AS submits, SUM(tt1.impressions) AS impressions, tt1.campaign_id FROM ".$wpdb->prefix."lepopup_campaign_items tt1 JOIN ".$wpdb->prefix."lepopup_forms tt2 ON tt2.id = tt1.form_id WHERE tt1.deleted = '0' AND tt2.deleted = '0' GROUP BY tt1.campaign_id) t2 ON t2.campaign_id = t1.id WHERE t1.deleted = '0'".((strlen($search_query) > 0) ? " AND t1.name LIKE '%".addslashes($search_query)."%'" : "")." ORDER BY ".$orderby." LIMIT ".(($page-1)*LEPOPUP_RECORDS_PER_PAGE).", ".LEPOPUP_RECORDS_PER_PAGE;
		$rows = $wpdb->get_results($sql, ARRAY_A);
		
		echo '
<div class="wrap lepopup-admin">
	<h2>'.esc_html__('Green Popups - A/B Campaigns', 'lepopup').'
		<a class="lepopup-button-h2" href="#" onclick="lepopup_campaign_properties_open(0); return false;">'.esc_html__('Create New Campaign', 'lepopup').'</a>
		<a href="'.(defined('UAP_CORE') ? 'https://greenpopups.com/documentation/#standalone-script' : 'https://greenpopups.com/documentation/#wordpress-plugin').'" class="lepopup-button-h2" target="_blank">'.esc_html__('Online Documentation', 'lepopup').'</a>
	</h2>
	<div class="lepopup-top-forms">
		<div class="lepopup-top-form-left">
			<form action="'.admin_url('admin.php').'" method="get" class="uap-filter-form lepopup-filter-form">
				<input type="hidden" name="page" value="lepopup-campaigns" />
				<label>'.esc_html__('Search:', 'lepopup').'</label>
				<input type="text" name="s" style="width: 200px;" class="form-control" value="'.esc_html($search_query).'">
				<input type="submit" class="lepopup-button-search" value="'.esc_html__('Search', 'lepopup').'" />
				'.((strlen($search_query) > 0) ? '<input type="button" class="lepopup-button-search" value="'.esc_html__('Reset search results', 'lepopup').'" onclick="window.location.href=\''.admin_url('admin.php').'?page=lepopup-campaigns\';" />' : '').'
			</form>
		</div>
		<div class="lepopup-top-form-right">
			<form id="lepopup-sorting-form" action="'.admin_url('admin.php').'" method="get" class="uap-filter-form lepopup-filter-form">
			<input type="hidden" name="page" value="lepopup-campaigns" />
			<label>'.esc_html__('Sort:', 'lepopup').'</label>
			'.((strlen($search_query) > 0) ? '<input type="hidden" name="s" value="'.esc_html($search_query).'">' : '').'
			'.(($page > 1) ? '<input type="hidden" name="p" value="'.esc_html($page).'">' : '').'
			<select name="o" onchange="jQuery(\'#lepopup-sorting-form\').submit();" style="width: 150px;" class="form-control">
				<option value="name-az"'.($sort == 'name-az' ? ' selected="selected"' : '').'>'.esc_html__('Alphabetically', 'lepopup').' ▲</option>
				<option value="name-za"'.($sort == 'name-za' ? ' selected="selected"' : '').'>'.esc_html__('Alphabetically', 'lepopup').' ▼</option>
				<option value="date-az"'.($sort == 'date-az' ? ' selected="selected"' : '').'>'.esc_html__('Created', 'lepopup').' ▲</option>
				<option value="date-za"'.($sort == 'date-za' ? ' selected="selected"' : '').'>'.esc_html__('Created', 'lepopup').' ▼</option>
			</select>
			</form>
		</div>
	</div>
	<div class="lepopup-table-list-buttons"><a href="#" onclick="lepopup_campaign_properties_open(0); return false;" class="lepopup-button lepopup-button-small"><i class="fas fa-plus"></i><label>'.esc_html__('Create New Campaign', 'lepopup').'</label></a></div>
	<div class="lepopup-pageswitcher">'.$switcher.'</div>
	<table class="lepopup-table-list widefat">
		<tr>
			<th>'.esc_html__('Name', 'lepopup').'</th>
			<th style="width: 360px;">'.esc_html__('Slug', 'lepopup').'</th>
			<th style="width: 60px;">'.esc_html__('Popups', 'lepopup').'</th>
			<th style="width: 35px;"></th>
		</tr>';
		if (sizeof($rows) > 0) {
			foreach ($rows as $row) {
				echo '
				<tr>
					<td><a href="#" onclick="lepopup_campaign_properties_open('.$row['id'].'); return false;"><strong>'.esc_html($row['name']).'</strong></a><span class="lepopup-table-list-badge-status">'.($row['active'] < 1 ? '<span class="lepopup-badge lepopup-badge-danger">'.esc_html__('Inactive', 'lepopup').'</span>' : '').'</span><label class="lepopup-table-list-created">'.esc_html__('Created', 'lepopup').': '.$lepopup->unixtime_string($row['created']).'</label></td>
					<td class="lepopup-table-list-column-shortcode"><span class="lepopup-more-using" data-id="'.esc_html($row['id']).'" data-mode="campaign" title="'.esc_html__('Click the icon for info about using the popup.', 'lepopup').'" onclick="lepopup_more_using_open(this);"><i class="fas fa-code"></i></span><div><input type="text" value="'.esc_html($row['slug']).'" readonly="readonly" style="" onclick="this.focus();this.select();"></div></td>
					<td>'.intval($row['forms']).'</td>
					<td>
						<div class="lepopup-table-list-actions">
							<span><i class="fas fa-ellipsis-v"></i></span>
							<div class="lepopup-table-list-menu">
								<ul>
									<li><a href="#" onclick="lepopup_campaign_properties_open('.$row['id'].'); return false;">'.esc_html__('Edit', 'lepopup').'</a></li>
									<li><a href="#" data-status="'.($row['active'] > 0 ? 'active' : 'inactive').'" data-id="'.esc_html($row['id']).'" data-doing="'.($row['active'] > 0 ? esc_html__('Deactivating...', 'lepopup') : esc_html__('Activating...', 'lepopup')).'" onclick="return lepopup_campaigns_status_toggle(this);">'.($row['active'] > 0 ? esc_html__('Deactivate', 'lepopup') : esc_html__('Activate', 'lepopup')).'</a></li>
									<li><a href="#" onclick="lepopup_campaign_stats_open('.$row['id'].'); return false;">'.esc_html__('Statistics', 'lepopup').'</a></li>
									<li><a href="#" data-id="'.esc_html($row['id']).'" data-doing="'.esc_html__('Reseting...', 'lepopup').'" onclick="return lepopup_campaigns_stats_reset(this);">'.esc_html__('Reset Statistics', 'lepopup').'</a></li>
									<li class="lepopup-table-list-menu-line"></li>
									<li><a href="#" data-id="'.esc_html($row['id']).'" data-doing="'.esc_html__('Deleting...', 'lepopup').'" onclick="return lepopup_campaigns_delete(this);">'.esc_html__('Delete', 'lepopup').'</a></li>
								</ul>
							</div>
						</div>
					</td>
				</tr>';
			}
		} else {
			echo '
				<tr><td colspan="4" class="lepopup-table-list-empty">'.((strlen($search_query) > 0) ? esc_html__('No results found for', 'lepopup').' "<strong>'.esc_html($search_query).'</strong>"' : esc_html__('List is empty.', 'lepopup')).'</td></tr>';
		}
		echo '
	</table>
	<div class="lepopup-pageswitcher">'.$switcher.'</div>
	<div class="lepopup-table-list-buttons">
		<a href="#" onclick="lepopup_campaign_properties_open(0); return false;" class="lepopup-button lepopup-button-small"><i class="fas fa-plus"></i><label>'.esc_html__('Create New Campaign', 'lepopup').'</label></a>
	</div>
</div>
<div id="lepopup-global-message"></div>';
		echo $this->admin_dialog_html();
		if (!empty($this->error_message)) {
			echo '
<script>jQuery(document).ready(function(){lepopup_global_message_show("danger", "'.esc_html($this->error_message).'");});</script>';
		} else if (!empty($this->success_message)) {
			echo '
<script>jQuery(document).ready(function(){lepopup_global_message_show("success", "'.esc_html($this->success_message).'");});</script>';
		}
		echo '
<div class="lepopup-admin-popup-overlay" id="lepopup-campaign-properties-overlay" onclick="return lepopup_campaign_properties_close();"></div>
<div class="lepopup-admin-popup" id="lepopup-campaign-properties">
	<div class="lepopup-admin-popup-inner">
		<div class="lepopup-admin-popup-title">
			<a href="#" title="'.esc_html__('Close', 'lepopup').'" onclick="return lepopup_campaign_properties_close();"><i class="fas fa-times"></i></a>
			<h3><i class="fas fa-cog"></i> '.esc_html__('Campaign Properties', 'lepopup').'<span></span></h3>
		</div>
		<div class="lepopup-admin-popup-content">
			<div class="lepopup-admin-popup-content-form">
			</div>
		</div>
		<div class="lepopup-admin-popup-buttons">
			<a class="lepopup-admin-button" href="#" onclick="return lepopup_campaign_save(this);"><i class="fas fa-check"></i><label>'.esc_html__('Save Details', 'lepopup').'</label></a>
		</div>
		<div class="lepopup-admin-popup-loading"><i class="fas fa-spinner fa-spin"></i></div>
	</div>
</div>
<div class="lepopup-admin-popup-overlay" id="lepopup-campaign-stats-overlay" onclick="return lepopup_campaign_stats_close();"></div>
<div class="lepopup-admin-popup" id="lepopup-campaign-stats">
	<div class="lepopup-admin-popup-inner">
		<div class="lepopup-admin-popup-title">
			<a href="#" title="'.esc_html__('Close', 'lepopup').'" onclick="return lepopup_campaign_stats_close();"><i class="fas fa-times"></i></a>
			<h3><i class="fas fa-chart-bar"></i> '.esc_html__('Statistics', 'lepopup').'<span></span></h3>
		</div>
		<div class="lepopup-admin-popup-content">
			<div class="lepopup-admin-popup-content-form" style="margin-top:30px;">
				<canvas id="lepopup-stats"></canvas>
			</div>
		</div>
		<div class="lepopup-admin-popup-loading"><i class="fas fa-spinner fa-spin"></i></div>
	</div>
</div>
<div class="lepopup-admin-popup-overlay" id="lepopup-more-using-overlay" onclick="return lepopup_more_using_close();"></div>
<div class="lepopup-admin-popup" id="lepopup-more-using">
	<div class="lepopup-admin-popup-inner">
		<div class="lepopup-admin-popup-title">
			<a href="#" title="'.esc_html__('Close', 'lepopup').'" onclick="return lepopup_more_using_close();"><i class="fas fa-times"></i></a>
			<h3><i class="fas fa-code"></i> '.esc_html__('How To Use', 'lepopup').'<span></span></h3>
		</div>
		<div class="lepopup-admin-popup-content">
			<div class="lepopup-admin-popup-content-form">
			</div>
		</div>
		<div class="lepopup-admin-popup-loading"><i class="fas fa-spinner fa-spin"></i></div>
	</div>
</div>
<script>jQuery(document).ready(function(){lepopup_campaigns_ready();});</script>';
	}

	function admin_targeting() {
		global $wpdb;
		include_once(dirname(__FILE__).'/core-targeting.php');
		$targeting = new lepopup_class_targeting();
		$targeting->admin_page();
	}

	function admin_records() {
		global $wpdb, $lepopup;

		if (isset($_GET["s"])) $search_query = trim(stripslashes($_GET["s"]));
		else $search_query = "";
		
		if (isset($_GET["form"])) $form_id = intval(stripslashes($_GET["form"]));
		else $form_id = 0;
		$forms = $wpdb->get_results("SELECT DISTINCT t1.form_id, t2.deleted, t2.name AS form_name FROM ".$wpdb->prefix."lepopup_records t1 LEFT JOIN ".$wpdb->prefix."lepopup_forms t2 ON t2.id = t1.form_id WHERE t1.deleted = '0' ORDER BY t2.name ASC", ARRAY_A);

		$filter = '';
		if ($form_id > 0) $filter = " AND t1.form_id = '".esc_sql($form_id)."'";
		if (!empty($search_query)) $filter .= " AND (t1.fields LIKE '%".esc_sql($wpdb->esc_like($search_query))."%' OR t1.id LIKE '%".esc_sql($wpdb->esc_like($search_query))."%')";
		
		$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."lepopup_records t1 WHERE t1.deleted = '0'".$filter, ARRAY_A);
		$total = $tmp["total"];
		$totalpages = ceil($total/LEPOPUP_RECORDS_PER_PAGE);
		if ($totalpages == 0) $totalpages = 1;
		if (isset($_GET["p"])) $page = intval($_GET["p"]);
		else $page = 1;
		if ($page < 1 || $page > $totalpages) $page = 1;
		$switcher = $lepopup->page_switcher(admin_url('admin.php').'?page=lepopup-log'.($form_id > 0 ? '&form='.rawurlencode($form_id) : '').((strlen($search_query) > 0) ? '&s='.rawurlencode($search_query) : ''), $page, $totalpages);
		
		$sql = "SELECT t1.*, t2.name AS form_name, t2.options AS form_options, t2.deleted AS form_deleted FROM ".$wpdb->prefix."lepopup_records t1 LEFT JOIN ".$wpdb->prefix."lepopup_forms t2 ON t2.id = t1.form_id WHERE t1.deleted = '0'".$filter." ORDER BY t1.created DESC LIMIT ".esc_sql(($page-1)*LEPOPUP_RECORDS_PER_PAGE).", ".esc_sql(LEPOPUP_RECORDS_PER_PAGE);
		$rows = $wpdb->get_results($sql, ARRAY_A);

		echo '
<div class="wrap lepopup-admin">
	<h2>'.esc_html__('Green Popups - Log', 'lepopup').'
		<a href="'.(defined('UAP_CORE') ? 'https://greenpopups.com/documentation/#standalone-script' : 'https://greenpopups.com/documentation/#wordpress-plugin').'" class="lepopup-button-h2" target="_blank">'.esc_html__('Online Documentation', 'lepopup').'</a>
	</h2>

	<div class="lepopup-top-forms">
		<div class="lepopup-top-form-left">
			<form action="'.admin_url('admin.php').'" method="get"  class="uap-filter-form lepopup-filter-form">
			<input type="hidden" name="page" value="lepopup-log" />
			'.($form_id > 0 ? '<input type="hidden" name="form" value="'.esc_html($form_id).'" />' : '').'
			<label>'.esc_html__('Search', 'lepopup').':</label>
			<input type="text" name="s" class="form-control" style="width: 200px;" value="'.esc_html($search_query).'">
			<input type="submit" class="lepopup-button-search" value="'.esc_html__('Search', 'lepopup').'" />
			'.((strlen($search_query) > 0) ? '<input type="button" class="lepopup-button-search" value="'.esc_html__('Reset search results', 'lepopup').'" onclick="window.location.href=\''.admin_url('admin.php').'?page=lepopup-log'.($form_id > 0 ? '&form='.rawurlencode($form_id) : '').'\';" />' : '').'
			</form>
		</div>
		<div class="lepopup-top-form-right">
			<form id="lepopup-filter-form" action="'.admin_url('admin.php').'" method="get"  class="uap-filter-form lepopup-filter-form">
			<input type="hidden" name="page" value="lepopup-log" />
			<label>'.esc_html__('Filter:', 'lepopup').'</label>
			<select name="form" class="form-control" style="width: 150px;" onchange="jQuery(\'#lepopup-filter-form\').submit();">
				<option value="">'.esc_html__('All Popups', 'lepopup').'</option>';
			foreach ($forms as $form) {
				echo '
				<option value="'.esc_html($form['form_id']).'"'.($form['form_id'] == $form_id ? ' selected="selected"' : '').'>'.esc_html($form['form_name']).($form['deleted'] == 1 ? ' [deleted]': '').'</option>';
			}
			echo '
			</select>
			</form>
		</div>
	</div>
	<div class="lepopup-table-list-buttons">
		<div class="lepopup-column-settings">
			<span><i class="fas fa-wrench"></i></span>
			<div class="lepopup-column-menu">
				<ul class="lepopup-log-columns" data-id="log">
					<li><input class="lepopup-checkbox lepopup-checkbox-tgl lepopup-checkbox-small" id="lepopup-column-id" data-id="id" type="checkbox" checked="checked" onchange="lepopup_columns_toggle(this);" /><label for="lepopup-column-id"></label><label for="lepopup-column-id">'.esc_html__('ID', 'lepopup').'</label></li>
					<li><input class="lepopup-checkbox lepopup-checkbox-tgl lepopup-checkbox-small" id="lepopup-column-primary" data-id="primary" type="checkbox" checked="checked" onchange="lepopup_columns_toggle(this);" /><label for="lepopup-column-primary"></label><label for="lepopup-column-primary">'.esc_html__('Primary Field', 'lepopup').'</label></li>
					<li><input class="lepopup-checkbox lepopup-checkbox-tgl lepopup-checkbox-small" id="lepopup-column-secondary" data-id="secondary" type="checkbox" checked="checked" onchange="lepopup_columns_toggle(this);" /><label for="lepopup-column-secondary"></label><label for="lepopup-column-secondary">'.esc_html__('Secondray Field', 'lepopup').'</label></li>
					<li><input class="lepopup-checkbox lepopup-checkbox-tgl lepopup-checkbox-small" id="lepopup-column-form" data-id="form" type="checkbox" checked="checked" onchange="lepopup_columns_toggle(this);" /><label for="lepopup-column-form"></label><label for="lepopup-column-form">'.esc_html__('Popup', 'lepopup').'</label></li>
					<li><input class="lepopup-checkbox lepopup-checkbox-tgl lepopup-checkbox-small" id="lepopup-column-amount" data-id="amount" type="checkbox" checked="checked" onchange="lepopup_columns_toggle(this);" /><label for="lepopup-column-amount"></label><label for="lepopup-column-amount">'.esc_html__('Amount', 'lepopup').'</label></li>
					<li><input class="lepopup-checkbox lepopup-checkbox-tgl lepopup-checkbox-small" id="lepopup-column-created" data-id="created" type="checkbox" checked="checked" onchange="lepopup_columns_toggle(this);" /><label for="lepopup-column-created"></label><label for="lepopup-column-created">'.esc_html__('Created', 'lepopup').'</label></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="lepopup-pageswitcher">'.$switcher.'</div>
	<table id="lepopup-table-log" class="lepopup-table-list widefat" style="display:none;">
		<tr>
			<th class="lepopup-column lepopup-column-checkbox" style="width: 35px;"><input type="hidden" name="action" value="lepopup-bulk-records-delete" /></th>
			<th class="lepopup-column lepopup-column-id">'.esc_html__('ID', 'lepopup').'</th>
			<th class="lepopup-column lepopup-column-primary">'.esc_html__('Primary Field', 'lepopup').'</th>
			<th class="lepopup-column lepopup-column-secondary">'.esc_html__('Secondray Field', 'lepopup').'</th>
			<th class="lepopup-column lepopup-column-form">'.esc_html__('Popup', 'lepopup').'</th>
			<th class="lepopup-column lepopup-column-amount" style="width: 100px;">'.esc_html__('Amount', 'lepopup').'</th>
			<th class="lepopup-column lepopup-column-created" style="width: 130px;">'.esc_html__('Created', 'lepopup').'</th>
			<th class="lepopup-column lepopup-column-actions" style="width: 35px;"></th>
		</tr>';
		if (sizeof($rows) > 0) {
			foreach ($rows as $row) {
				$primary_field = '<a href="#" onclick="return lepopup_record_details_open(this);" data-id="'.esc_html($row['id']).'"><strong>-</strong></a>';
				$secondary_field = '-';
				$form_options = json_decode($row['form_options'], true);
				if (!empty($form_options) && is_array($form_options)) {
					$fields = json_decode($row['fields'], true);
					if (!empty($fields) && is_array($fields)) {
						if (array_key_exists($form_options['key-fields-primary'], $fields) && !empty($fields[$form_options['key-fields-primary']])) {
							$primary_field = '<a href="#" onclick="return lepopup_record_details_open(this);" data-id="'.esc_html($row['id']).'"><strong>'.esc_html($fields[$form_options['key-fields-primary']]).'</strong></a>';
						}
						if (array_key_exists($form_options['key-fields-secondary'], $fields) && !empty($fields[$form_options['key-fields-secondary']])) {
							$secondary_field = esc_html($fields[$form_options['key-fields-secondary']]);
						}
					}
				}
				if ($row['status'] == LEPOPUP_RECORD_STATUS_UNCONFIRMED) $primary_field .= '<span class="lepopup-badge lepopup-badge-danger">'.esc_html__('Unconfirmed', 'lepopup').'</span>';
				else if ($row['status'] == LEPOPUP_RECORD_STATUS_CONFIRMED) $primary_field .= '<span class="lepopup-badge lepopup-badge-success">'.esc_html__('Confirmed', 'lepopup').'</span>';
				echo '
				<tr>
					<td class="lepopup-column lepopup-column-checkbox"><div class="lepopup-cr-box"><input class="lepopup-checkbox lepopup-checkbox-fa-check lepopup-checkbox-small" type="checkbox" name="records[]" id="lepopup-record-'.esc_html($row['id']).'" value="'.esc_html($row['id']).'"><label for="lepopup-record-'.esc_html($row['id']).'"></label></div></td>
					<td class="lepopup-column lepopup-column-id">'.esc_html($row['id']).'</td>
					<td class="lepopup-column lepopup-column-primary">'.$primary_field.'</td>
					<td class="lepopup-column lepopup-column-secondary">'.$secondary_field.'</td>
					<td class="lepopup-column lepopup-column-form">'.($row['form_deleted'] == 0 ? '<a href="'.admin_url('admin.php').'?page=lepopup-add&id='.esc_html($row['form_id']).'">'.esc_html($row['form_name']).'</a>' : esc_html($row['form_name']).' ('.esc_html__('deleted', 'lepopup').')').'</td>
					<td class="lepopup-column lepopup-column-amount">'.($row['amount'] > 0 ? '<a href="'.admin_url('admin.php').'?page=lepopup-transactions&record='.rawurlencode($row['id']).'">'.($row['currency'] != 'BTC' ? number_format($row['amount'], 2, '.', '') : number_format($row['amount'], 8, '.', '')).' '.esc_html($row['currency']).'</a>'.($row['status'] == LEPOPUP_RECORD_STATUS_PAID ? '<span class="lepopup-badge lepopup-badge-success">'.esc_html__('Paid', 'lepopup').'</span>' : '<span class="lepopup-badge lepopup-badge-danger">'.esc_html__('Unpaid', 'lepopup').'</span>') : '-').'</td>
					<td class="lepopup-column lepopup-column-created">'.$lepopup->unixtime_string($row['created']).'</td>
					<td class="lepopup-column lepopup-column-actions">
						<div class="lepopup-table-list-actions">
							<span><i class="fas fa-ellipsis-v"></i></span>
							<div class="lepopup-table-list-menu">
								<ul>
									<li><a href="#" onclick="return lepopup_record_details_open(this);" data-id="'.esc_html($row['id']).'">'.esc_html__('Details', 'lepopup').'</a></li>
									'.($lepopup->advanced_options['admin-menu-transactions'] != 'off' ? '<li><a href="'.admin_url('admin.php').'?page=lepopup-transactions&record='.esc_html($row['id']).'">'.esc_html__('Transactions', 'lepopup').'</a></li>' : '').'
									<li><a href="#" data-id="'.esc_html($row['id']).'" data-doing="'.esc_html__('Deleting...', 'lepopup').'" onclick="return lepopup_records_delete(this);">'.esc_html__('Delete', 'lepopup').'</a></li>
								</ul>
							</div>
						</div>
					</td>
				</tr>';
			}
		} else {
			echo '
				<tr><td colspan="8" class="lepopup-table-list-empty">'.((strlen($search_query) > 0) ? esc_html__('No results found for', 'lepopup').' "<strong>'.esc_html($search_query).'</strong>"' : esc_html__('List is empty.', 'lepopup')).'</td></tr>';
		}
		echo '
	</table>
	<script>lepopup_columns_toggle("log");jQuery("#lepopup-table-log").show();</script>
	<div class="lepopup-pageswitcher">'.$switcher.'</div>
	<div class="lepopup-table-list-buttons">
		<a href="#" class="lepopup-button lepopup-button-small" onclick="return lepopup_bulk_records_delete(this);"><i class="fas fa-trash"></i><label>'.esc_html__('Delete Selected', 'lepopup').'</label></a>
	</div>
</div>
<div id="lepopup-global-message"></div>';
		echo $this->admin_dialog_html();
		if (!empty($this->error_message)) {
			echo '
<script>jQuery(document).ready(function(){lepopup_global_message_show("danger", "'.esc_html($this->error_message).'");});</script>';
		} else if (!empty($this->success_message)) {
			echo '
<script>jQuery(document).ready(function(){lepopup_global_message_show("success", "'.esc_html($this->success_message).'");});</script>';
		}
		echo '
<div class="lepopup-admin-popup-overlay" id="lepopup-record-details-overlay"></div>
<div class="lepopup-admin-popup" id="lepopup-record-details">
	<div class="lepopup-admin-popup-inner">
		<div class="lepopup-admin-popup-title">
			<a href="#" title="'.esc_html__('Close', 'lepopup').'" onclick="return lepopup_record_details_close();"><i class="fas fa-times"></i></a>
			<span class="lepopup-export-pdf" data-url="'.admin_url('admin.php').'?page=lepopup&lepopup-action=log-record-pdf&id={ID}"><a target="_blank" href="#"><i class="fas fa-file-pdf"></i></a></span>
			<span class="lepopup-print" data-url="'.admin_url('admin.php').'?page=lepopup&lepopup-action=log-record-print&id={ID}"><a target="_blank" href="#"><i class="fas fa-print"></i></a></span>
			<h3><i class="fas fa-cog"></i> '.esc_html__('Record Details', 'lepopup').'<span></span></h3>
		</div>
		<div class="lepopup-admin-popup-content">
			<div class="lepopup-admin-popup-content-form">
			</div>
		</div>
		<div class="lepopup-admin-popup-loading"><i class="fas fa-spinner fa-spin"></i></div>
	</div>
</div>
<script>jQuery(document).ready(function(){lepopup_log_ready();});</script>';
 	}

	function admin_stats() {
		global $wpdb, $lepopup;

		if (array_key_exists('form', $_REQUEST) && $_REQUEST['form'] > 0) $form_id = intval(stripslashes($_REQUEST["form"]));
		else $form_id = null;
		$forms = $wpdb->get_results("SELECT DISTINCT t1.form_id, t2.deleted, t2.name AS form_name FROM ".$wpdb->prefix."lepopup_stats t1 LEFT JOIN ".$wpdb->prefix."lepopup_forms t2 ON t2.id = t1.form_id WHERE t1.deleted = '0' AND t2.deleted = '0' ORDER BY t2.name ASC", ARRAY_A);

		$start_date = new DateTime(date("Y-m-01", time()+3600*$lepopup->gmt_offset));
		$end_date = new DateTime(date("Y-m-t", time()+3600*$lepopup->gmt_offset));
		
		$output = $lepopup->stats_array($form_id, $start_date, $end_date);
		
		echo '
<div class="wrap lepopup-admin">
	<h2>'.esc_html__('Green Popups - Stats', 'lepopup').'
		<a href="'.(defined('UAP_CORE') ? 'https://greenpopups.com/documentation/#standalone-script' : 'https://greenpopups.com/documentation/#wordpress-plugin').'" class="lepopup-button-h2" target="_blank">'.esc_html__('Online Documentation', 'lepopup').'</a>
	</h2>

	<div class="lepopup-top-forms">
		<div class="lepopup-top-form-left">
			<div class="lepopup-stats-filter">
				<div class="lepopup-stats-select-container">
					<select id="lepopup-stats-form" onchange="">
						<option value="0">'.esc_html__('All Popups', 'lepopup').'</option>';
				foreach ($forms as $form) {
					echo '
						<option value="'.esc_html($form['form_id']).'"'.($form['form_id'] == $form_id ? ' selected="selected"' : '').'>'.esc_html($form['form_name']).($form['deleted'] == 1 ? ' [deleted]': '').'</option>';
				}
				echo '
					</select>
					<label>'.esc_html__('Popup', 'lepopup').'</label>
				</div>
				<div class="lepopup-stats-input-container">
					<input type="text" id="lepopup-stats-date-start" class="lepopup-stats-date" value="'.date('Y-m-01', time()+3600*$lepopup->gmt_offset).'" />
					<label>'.esc_html__('Start date', 'lepopup').'</label>
				</div>
				<div class="lepopup-stats-input-container">
					<input type="text" id="lepopup-stats-date-end" class="lepopup-stats-date" value="'.date('Y-m-t', time()+3600*$lepopup->gmt_offset).'" />
					<label>'.esc_html__('End date', 'lepopup').'</label>
				</div>
				<a class="lepopup-stats-button" onclick="return lepopup_stats_load(this);"><i class="fas fa-check"></i><label>'.esc_html__('Apply', 'lepopup').'</label></a>
			</div>
		</div>
	</div>
	<canvas id="lepopup-stats"></canvas>
</div>
<input type="hidden" id="lepopup-stats-initial-data" value="'.esc_html(json_encode($output)).'" />
<script>jQuery(document).ready(function(){lepopup_stats_ready();});</script>
<div id="lepopup-global-message"></div>';
		echo $this->admin_dialog_html();
		if (!empty($this->error_message)) {
			echo '
<script>jQuery(document).ready(function(){lepopup_global_message_show("danger", "'.esc_html($this->error_message).'");});</script>';
		} else if (!empty($this->success_message)) {
			echo '
<script>jQuery(document).ready(function(){lepopup_global_message_show("success", "'.esc_html($this->success_message).'");});</script>';
		}
 	}

	function admin_field_analytics() {
		global $wpdb, $lepopup;

		if (array_key_exists('form', $_REQUEST) && $_REQUEST['form'] > 0) $form_id = intval(stripslashes($_REQUEST["form"]));
		else $form_id = null;
		$forms = $wpdb->get_results("SELECT DISTINCT t1.form_id, t2.deleted, t2.name AS form_name FROM ".$wpdb->prefix."lepopup_stats t1 LEFT JOIN ".$wpdb->prefix."lepopup_forms t2 ON t2.id = t1.form_id WHERE t1.deleted = '0' && t2.deleted = '0' ORDER BY t2.name ASC", ARRAY_A);

		$start_date = new DateTime('2000-01-01');
		$end_date = new DateTime('2030-12-31');
		
		$output = array();
		if ($form_id > 0) {
			if (!class_exists("lepopup_form")) include_once(dirname(__FILE__).'/core-form.php');
			$form_object = new lepopup_form(intval($form_id));
			if (!empty($form_object->id)) {
				$output = $form_object->field_analytics_array($start_date, $end_date);
			} else $form_id = null;
		}
		echo '
<div class="wrap lepopup-admin">
	<h2>'.esc_html__('Green Popups - Field Analytics', 'lepopup').'
		<a href="'.(defined('UAP_CORE') ? 'https://greenpopups.com/documentation/#standalone-script' : 'https://greenpopups.com/documentation/#wordpress-plugin').'" class="lepopup-button-h2" target="_blank">'.esc_html__('Online Documentation', 'lepopup').'</a>
	</h2>

	<div class="lepopup-top-forms">
		<div class="lepopup-top-form-left">
			<div class="lepopup-stats-filter">
				<div class="lepopup-stats-select-container">
					<select id="lepopup-stats-form" onchange="">
						<option value="0">'.esc_html__('Select the form', 'lepopup').'</option>';
				foreach ($forms as $form) {
					echo '
						<option value="'.esc_html($form['form_id']).'"'.($form['form_id'] == $form_id ? ' selected="selected"' : '').'>'.esc_html($form['form_name']).($form['deleted'] == 1 ? ' [deleted]': '').'</option>';
				}
				echo '
					</select>
					<label>'.esc_html__('Popup', 'lepopup').'</label>
				</div>
				<div class="lepopup-stats-radio-container">
					<div class="lepopup-stats-radio-toggle-container">
						<input class="lepopup-checkbox-toggle" type="checkbox" value="off" id="lepopup-stats-period" /><label for="lepopup-stats-period"></label>
					</div>
					<label>'.esc_html__('Period', 'lepopup').'</label>
				</div>
				<div class="lepopup-stats-input-container" style="display:none;">
					<input type="text" id="lepopup-stats-date-start" class="lepopup-stats-date" value="'.date('Y-m-01', time()+3600*$lepopup->gmt_offset).'" />
					<label>'.esc_html__('Start date', 'lepopup').'</label>
				</div>
				<div class="lepopup-stats-input-container" style="display:none;">
					<input type="text" id="lepopup-stats-date-end" class="lepopup-stats-date" value="'.date('Y-m-t', time()+3600*$lepopup->gmt_offset).'" />
					<label>'.esc_html__('End date', 'lepopup').'</label>
				</div>
				<a class="lepopup-stats-button" onclick="return lepopup_field_analytics_load(this);"><i class="fas fa-check"></i><label>'.esc_html__('Apply', 'lepopup').'</label></a>
			</div>
		</div>
	</div>
	<div class="lepopup-field-analytics-container">
		'.(empty($form_id) ? '<div class="lepopup-field-analytics-noform">'.esc_html__('No form selected.', 'lepopup').'</div>' : '').'
	</div>
</div>
<input type="hidden" id="lepopup-field-analytics-initial-data" value="'.esc_html(json_encode($output)).'" />
<script>jQuery(document).ready(function(){lepopup_field_analytics_ready();});</script>
<div id="lepopup-global-message"></div>';
		echo $this->admin_dialog_html();
		if (!empty($this->error_message)) {
			echo '
<script>jQuery(document).ready(function(){lepopup_global_message_show("danger", "'.esc_html($this->error_message).'");});</script>';
		} else if (!empty($this->success_message)) {
			echo '
<script>jQuery(document).ready(function(){lepopup_global_message_show("success", "'.esc_html($this->success_message).'");});</script>';
		}
 	}

	function admin_transactions() {
		global $wpdb, $lepopup;

		if (isset($_GET["s"])) $search_query = trim(stripslashes($_GET["s"]));
		else $search_query = "";
		if (isset($_GET["record"])) $record_id = intval(stripslashes($_GET["record"]));
		else $record_id = 0;
		
		$filter = '';
		if ($record_id > 0) $filter = " AND t1.record_id = '".esc_sql($record_id)."'";
		if (!empty($search_query)) $filter .= " AND (t1.payer_name LIKE '%".esc_sql($wpdb->esc_like($search_query))."%' OR t1.payer_email LIKE '%".esc_sql($wpdb->esc_like($search_query))."%' OR t1.details LIKE '%".esc_sql($wpdb->esc_like($search_query))."%')";
		
		$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."lepopup_transactions t1 WHERE t1.deleted = '0'".$filter, ARRAY_A);
		$total = $tmp["total"];
		$totalpages = ceil($total/LEPOPUP_RECORDS_PER_PAGE);
		if ($totalpages == 0) $totalpages = 1;
		if (isset($_GET["p"])) $page = intval($_GET["p"]);
		else $page = 1;
		if ($page < 1 || $page > $totalpages) $page = 1;
		$switcher = $lepopup->page_switcher(admin_url('admin.php').'?page=lepopup-transactions'.($record_id > 0 ? '&record='.rawurlencode($record_id) : '').((strlen($search_query) > 0) ? '&s='.rawurlencode($search_query) : ''), $page, $totalpages);
		
		$sql = "SELECT t1.*, t2.form_id AS form_id FROM ".$wpdb->prefix."lepopup_transactions t1 LEFT JOIN ".$wpdb->prefix."lepopup_records t2 ON t2.id = t1.record_id WHERE t1.deleted = '0'".$filter." ORDER BY t1.created DESC LIMIT ".esc_sql(($page-1)*LEPOPUP_RECORDS_PER_PAGE).", ".esc_sql(LEPOPUP_RECORDS_PER_PAGE);
		$rows = $wpdb->get_results($sql, ARRAY_A);
		
		echo '
<div class="wrap lepopup-admin">
	<h2>'.esc_html__('Green Popups - Transactions', 'lepopup').'
		<a href="'.(defined('UAP_CORE') ? 'https://greenpopups.com/documentation/#standalone-script' : 'https://greenpopups.com/documentation/#wordpress-plugin').'" class="lepopup-button-h2" target="_blank">'.esc_html__('Online Documentation', 'lepopup').'</a>
	</h2>

	<div class="lepopup-top-forms">
		<div class="lepopup-top-form-left">
			<form action="'.admin_url('admin.php').'" method="get"  class="uap-filter-form lepopup-filter-form">
			<input type="hidden" name="page" value="lepopup-transactions" />
			'.($record_id > 0 ? '<input type="hidden" name="record" value="'.esc_html($record_id).'" />' : '').'
			<label>'.esc_html__('Search', 'lepopup').':</label>
			<input type="text" name="s" class="form-control" style="width: 200px;" value="'.esc_html($search_query).'">
			<input type="submit" class="lepopup-button-search" value="'.esc_html__('Search', 'lepopup').'" />
			'.((strlen($search_query) > 0) ? '<input type="button" class="lepopup-button-search" value="'.esc_html__('Reset search results', 'lepopup').'" onclick="window.location.href=\''.admin_url('admin.php').'?page=lepopup-transactions'.($record_id > 0 ? '&record='.rawurlencode($record_id) : '').'\';" />' : '').'
			</form>
		</div>
		<div class="lepopup-top-form-right">
		</div>
	</div>
	<div class="lepopup-table-list-buttons"></div>
	<div class="lepopup-pageswitcher">'.$switcher.'</div>
	<table id="lepopup-table-transactions" class="lepopup-table-list widefat">
		<tr>
			<th style="width: 35px;" class="lepopup-column lepopup-column-checkbox"><input type="hidden" name="action" value="lepopup-bulk-transactions-delete" /></th>
			<th>'.esc_html__('Payer', 'lepopup').'</th>
			<th>'.esc_html__('Status', 'lepopup').'</th>
			<th style="width: 120px;">'.esc_html__('Amount', 'lepopup').'</th>
			<th style="width: 130px;">'.esc_html__('Created', 'lepopup').'</th>
			<th style="width: 35px;"></th>
		</tr>';
		if (sizeof($rows) > 0) {
			foreach ($rows as $row) {
				echo '
				<tr>
					<td class="lepopup-column lepopup-column-checkbox"><div class="lepopup-cr-box"><input class="lepopup-checkbox lepopup-checkbox-fa-check lepopup-checkbox-small" type="checkbox" name="records[]" id="lepopup-record-'.esc_html($row['id']).'" value="'.esc_html($row['id']).'"><label for="lepopup-record-'.esc_html($row['id']).'"></label></div></td>
					<td><a href="#" onclick="return lepopup_transaction_details_open(this);" data-id="'.esc_html($row['id']).'"><strong>'.esc_html($row['payer_name']).'</strong><label class="lepopup-table-list-created">'.esc_html($row['payer_email']).'</label></a></td>
					<td><a href="#" onclick="return lepopup_transaction_details_open(this);" data-id="'.esc_html($row['id']).'">'.esc_html($row["payment_status"]).'</a><label class="lepopup-table-list-created">'.esc_html($row["transaction_type"]).'</label></td>
					<td>'.($row['currency'] == 'BTC' ? number_format($row['gross'], 8, ".", "") : number_format($row['gross'], 2, ".", "")).' '.$row['currency'].'</td>
					<td>'.$lepopup->unixtime_string($row['created']).'</td>
					<td>
						<div class="lepopup-table-list-actions">
							<span><i class="fas fa-ellipsis-v"></i></span>
							<div class="lepopup-table-list-menu">
								<ul>
									<li><a href="#" onclick="return lepopup_transaction_details_open(this);" data-id="'.esc_html($row['id']).'">'.esc_html__('Details', 'lepopup').'</a></li>
									<li><a href="#" data-id="'.esc_html($row['id']).'" data-doing="'.esc_html__('Deleting...', 'lepopup').'" onclick="return lepopup_transactions_delete(this);">'.esc_html__('Delete', 'lepopup').'</a></li>
								</ul>
							</div>
						</div>
					</td>
				</tr>';
			}
		} else {
			echo '
				<tr><td colspan="6" class="lepopup-table-list-empty">'.((strlen($search_query) > 0) ? esc_html__('No results found for', 'lepopup').' "<strong>'.esc_html($search_query).'</strong>"' : esc_html__('List is empty.', 'lepopup')).'</td></tr>';
		}
		echo '
	</table>
	<div class="lepopup-pageswitcher">'.$switcher.'</div>
	<div class="lepopup-table-list-buttons">
		<a href="#" class="lepopup-button lepopup-button-small" onclick="return lepopup_bulk_transactions_delete(this);"><i class="fas fa-trash"></i><label>'.esc_html__('Delete Selected', 'lepopup').'</label></a>
	</div>
</div>
<div id="lepopup-global-message"></div>';
		echo $this->admin_dialog_html();
		if (!empty($this->error_message)) {
			echo '
<script>jQuery(document).ready(function(){lepopup_global_message_show("danger", "'.esc_html($this->error_message).'");});</script>';
		} else if (!empty($this->success_message)) {
			echo '
<script>jQuery(document).ready(function(){lepopup_global_message_show("success", "'.esc_html($this->success_message).'");});</script>';
		}
		echo '
<div class="lepopup-admin-popup-overlay" id="lepopup-record-details-overlay"></div>
<div class="lepopup-admin-popup" id="lepopup-record-details">
	<div class="lepopup-admin-popup-inner">
		<div class="lepopup-admin-popup-title">
			<a href="#" title="'.esc_html__('Close', 'lepopup').'" onclick="return lepopup_record_details_close();"><i class="fas fa-times"></i></a>
			<span class="lepopup-export-pdf" data-url="'.admin_url('admin.php').'?page=lepopup&lepopup-action=transaction-pdf&id={ID}"><a target="_blank" href="#"><i class="fas fa-file-pdf"></i></a></span>
			<span class="lepopup-print" data-url="'.admin_url('admin.php').'?page=lepopup&lepopup-action=transaction-print&id={ID}"><a target="_blank" href="#"><i class="fas fa-print"></i></a></span>
			<h3><i class="fas fa-cog"></i> '.esc_html__('Transaction Details', 'lepopup').'<span></span></h3>
		</div>
		<div class="lepopup-admin-popup-content">
			<div class="lepopup-admin-popup-content-form">
			</div>
		</div>
		<div class="lepopup-admin-popup-loading"><i class="fas fa-spinner fa-spin"></i></div>
	</div>
</div>
<script>jQuery(document).ready(function(){lepopup_log_ready();});</script>';
 	}

	function admin_library() {
		global $wpdb, $lepopup;
		$url = trailingslashit(LEPOPUP_LIBRARY_URL).'get-items/';
		$items = array();
		$upload_dir = wp_upload_dir();
		$cache_file = $upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/temp/cache-'.md5($url).'.txt';
		if (file_exists($cache_file)) {
			if (filemtime($cache_file)+3600*12 > time()) {
				$cached = file_get_contents($cache_file);
				$items_tmp = unserialize($cached);
				if ($items_tmp === false) unlink($cache_file);
				else $items = $items_tmp;
			}
		}
		if (empty($items)) {
			try {
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_TIMEOUT, 10);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36');
				//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
				$response = curl_exec($curl);
				curl_close($curl);
													
				$result = json_decode($response, true);
				if($result && is_array($result) && !empty($result)) {
					$items = $result;
					file_put_contents($cache_file, serialize($items));
				}
			} catch (Exception $e) {
			}
		}
		if (!empty($lepopup->error)) $message = "<div class='error'><p>".$lepopup->error."</p></div>";
		else if (!empty($lepopup->info)) $message = "<div class='updated'><p>".$lepopup->info."</p></div>";
		else $message = '';
		echo '
<div class="wrap lepopup-admin">
	<h2>'.esc_html__('Green Popups - Library', 'lepopup').'
		<a href="'.admin_url('admin.php').'?page=lepopup-library&lepopup-action=clear-library-cache" class="lepopup-button-h2">'.esc_html__('Clear Library Cache', 'lepopup').'</a>
		<a href="'.(defined('UAP_CORE') ? 'https://greenpopups.com/documentation/#standalone-script' : 'https://greenpopups.com/documentation/#wordpress-plugin').'" class="lepopup-button-h2" target="_blank">'.esc_html__('Online Documentation', 'lepopup').'</a>
	</h2>
	<div class="lepopup-library-container">';
		if (empty($items)) {
			echo '
		<div class="lepopup-library-error">'.esc_html__('The library is not available. Please try again later.', 'lepopup').'</div>';
		} else {
			foreach($items as $item) {
				echo '
		<div class="lepopup-library-item-box">
			<img class="item-thumbnail" src="'.$item['image'].'" alt="#'.$item['id'].'" />
			<a href="'.admin_url('admin.php').'?page=lepopup&lepopup-action=import-library&id='.$item['id'].'&key='.$item['key'].'" class="lepopup-library-item-box-hover">'.__('Download and import popup', 'lepopup').'</a>
			<div class="lepopup-library-label"># '.$item['id'].'</div>
		</div>';
			}
		}
		echo '
	</div>
</div>
<div id="lepopup-global-message"></div>';
		if (!empty($this->error_message)) {
			echo '
<script>jQuery(document).ready(function(){lepopup_global_message_show("danger", "'.esc_html($this->error_message).'");});</script>';
		} else if (!empty($this->success_message)) {
			echo '
<script>jQuery(document).ready(function(){lepopup_global_message_show("success", "'.esc_html($this->success_message).'");});</script>';
		}

	}

	function admin_migrating() {
		global $wpdb, $lepopup;
		include_once(dirname(__FILE__).'/core-legacy.php');
		$legacy = new lepopup_legacy_class();
		$status = $legacy->status();

		$finished = false;
		if ($status['settings'] == 'done' && $status['popups']['total'] == $status['popups']['done'] && $status['campaigns']['total'] == $status['campaigns']['done'] && $status['targets']['total'] == $status['targets']['done'] && $status['records']['total'] == $status['records']['done']) $finished = true;
		if (class_exists('ulptabs_class') && class_exists('lepopuptab_class')) {
			if ($status['tabs']['total'] != $status['tabs']['done']) $finished = false;
		}
		if (class_exists('ulpdownload_class') && class_exists('lepopupdownload_class')) {
			if ($status['downloads']['total'] != $status['downloads']['done']) $finished = false;
		}
		
		echo '
<div class="wrap lepopup-admin lepopup">
	<div class="lepopup-intro">
		<div class="lepopup-intro-logo-container">
			<img src="'.$lepopup->plugins_url.'/images/icon.png" alt="'.esc_html__('Welcome to Green Popups', 'lepopup').'" />
		</div>
		<h1>'.esc_html__('Welcome to Migration Tool', 'lepopup').'</h1>
		<h2>'.esc_html__('for Green Popups (formerly Layered Popups)', 'lepopup').'</h2>
		'.(!empty($status['warning-message']) ? '<div class="lepopup-intro-danger">'.$status['warning-message'].'</div>' : '').'
		'.(!empty($status['info-message']) ? '<div class="lepopup-intro-info">'.$status['info-message'].'</div>' : '').'
		<div class="lepopup-intro-buttons">
			<span id="lepopup-migrate-button"'.($finished ? 'class="lepopup-intro-button-disabled lepopup-intro-button-finished"' : '').' data-label="'.esc_html__('Migrate from Layered Popups', 'lepopup').'" data-done="'.esc_html__('Completed!', 'lepopup').'" data-loading="'.esc_html__('Processing...', 'lepopup').'" onclick="return lepopup_migrate();">'.($finished ? esc_html__('Completed!', 'lepopup') : esc_html__('Migrate from Layered Popups', 'lepopup')).'</span>
		</div>
		<table id="lepopup-intro-status">
			<tr><th>'.esc_html__('Settings', 'lepopup').'</th><td id="lepopup-migrate-status-settings">'.($status['settings'] == 'done' ? '100' : '0').'%</td></tr>
			<tr><th>'.esc_html__('Popups', 'lepopup').'</th><td id="lepopup-migrate-status-popups">'.($status['popups']['total'] > 0 ? intval(floor(100*intval($status['popups']['done'])/$status['popups']['total'])) : 0).'%</td></tr>
			<tr><th>'.esc_html__('Campaigns', 'lepopup').'</th><td id="lepopup-migrate-status-campaigns">'.($status['campaigns']['total'] > 0 ? intval(floor(100*intval($status['campaigns']['done'])/$status['campaigns']['total'])) : 0).'%</td></tr>
			'.(!defined('UAP_CORE') ? '<tr><th>'.esc_html__('Targets', 'lepopup').'</th><td id="lepopup-migrate-status-targets">'.($status['targets']['total'] > 0 ? intval(floor(100*intval($status['targets']['done'])/$status['targets']['total'])) : 0).'%</td></tr>' : '').'
			<tr><th>'.esc_html__('Log Records', 'lepopup').'</th><td id="lepopup-migrate-status-records">'.($status['records']['total'] > 0 ? intval(floor(100*intval($status['records']['done'])/$status['records']['total'])) : 0).'%</td></tr>';
		if (class_exists('ulptabs_class') && class_exists('lepopuptab_class')) {
			echo '
			<tr><th>'.esc_html__('Side Tabs', 'lepopup').'</th><td id="lepopup-migrate-status-tabs">'.($status['tabs']['total'] > 0 ? intval(floor(100*intval($status['tabs']['done'])/$status['tabs']['total'])) : 0).'%</td></tr>';
		}
		if (class_exists('ulpdownload_class') && class_exists('lepopupdownload_class')) {
			echo '
			<tr><th>'.esc_html__('Downloads', 'lepopup').'</th><td id="lepopup-migrate-status-downloads">'.($status['downloads']['total'] > 0 ? intval(floor(100*intval($status['downloads']['done'])/$status['downloads']['total'])) : 0).'%</td></tr>';
		}
		echo '
		</table>
	</div>
</div>
<div id="lepopup-global-message"></div>';
	}
	
	function admin_init() {
		global $wpdb, $lepopup;
		if (!current_user_can('manage_options') && !$lepopup->demo_mode) return;
		if (array_key_exists('lepopup-action', $_REQUEST)) {
			switch ($_REQUEST['lepopup-action']) {
				case 'download':
					if ($lepopup->demo_mode) {
						$this->error_message = esc_html__('This operation disabled in DEMO mode.', 'lepopup');
						return;
					}
					$upload_id = intval($_REQUEST["id"]);
					$upload_dir = wp_upload_dir();
					$upload_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_uploads WHERE deleted = '0' AND id = '".esc_sql($upload_id)."'", ARRAY_A);
					if (!empty($upload_details)) {
						$filename = $upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$upload_details['form_id'].'/'.$upload_details['filename'];
						if (file_exists($filename) && is_file($filename)) {
							error_reporting(0);
							ob_start();
							if(!ini_get('safe_mode')) set_time_limit(0);
							ob_end_clean();
							$length = filesize($filename);
							if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE")) {
								header("Pragma: public");
								header("Expires: 0");
								header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
								header("Content-type: application-download");
								header("Content-Length: ".$length);
								header("Content-Disposition: attachment; filename=\"".$upload_details['filename_original']."\"");
								header("Content-Transfer-Encoding: binary");
							} else {
								header("Content-type: application-download");
								header("Content-Length: ".$length);
								header("Content-Disposition: attachment; filename=\"".$upload_details['filename_original']."\"");
							}

							$handle_read = fopen($filename, "rb");
							while (!feof($handle_read) && $length > 0) {
								$content = fread($handle_read, 1024);
								echo substr($content, 0, min($length, 1024));
								flush();
								ob_flush();
								$length = $length - strlen($content);
								if ($length < 0) $length = 0;
							}
							fclose($handle_read);
							exit;
						}
					}
					$this->error_message = esc_html__('Requested file not found.', 'lepopup');
					return;
					break;
				case 'log-record-print':
					$record_id = null;
					if (array_key_exists('id', $_REQUEST)) $record_id = intval($_REQUEST['id']);
					$return_data = $lepopup->log_record_details_html($record_id, false);
					if ($return_data['status'] != 'OK') {
						$this->error_message = $return_data['message'];
						return;
					}
					echo '
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
	<title>'.$return_data['form_name'].' - '.esc_html__('Record', 'lepopup').' '.$return_data['record-id'].'</title>
	<style>body{font-family:arial;font-size:15px;}body::-webkit-scrollbar{width: 5px;}body::-webkit-scrollbar-track{box-shadow:inset 0 0 6px rgba(0,0,0,0.1);}body::-webkit-scrollbar-thumb{background-color:#bd4070;}div.not-found{margin: 40px;text-align:center;}</style>
	<link rel="stylesheet" href="'.$lepopup->plugins_url.'/css/admin.css?ver='.LEPOPUP_VERSION.'" type="text/css" media="all" />
</head>
<body>
	'.$return_data['html'].'
	<script type="text/javascript">window.print();</script>
</body>
</html>';
					exit;
					break;
				case 'log-record-pdf':
					$record_id = null;
					if (array_key_exists('id', $_REQUEST)) $record_id = intval($_REQUEST['id']);
					$return_data = $lepopup->log_record_details_html($record_id, true);
					if ($return_data['status'] != 'OK') {
						$this->error_message = $return_data['message'];
						return;
					}
					if (!class_exists("TCPDF")) require_once(dirname(dirname(__FILE__)).'/libs/tcpdf/tcpdf.php');
					$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
					$pdf->SetCreator(PDF_CREATOR);
					$pdf->SetAuthor('Green Popups');
					$pdf->SetTitle($return_data['form_name'].' - '.esc_html__('Record', 'lepopup').' '.$return_data['record-id']);
					$pdf->SetSubject(esc_html__('Record Details', 'lepopup'));
					$pdf->SetKeywords('Green Popups, Record Details');

					//$pdf->SetHeaderData(null, null, $return_data['form_name'].': '.esc_html__('Record', 'lepopup').' '.$return_data['record-id'], "by Green Popups", array(0,64,255), array(0,64,128));
					//$pdf->setFooterData(array(0,64,0), array(0,64,128));

					//$pdf->setHeaderFont(Array('freesans', '', 10));
					//$pdf->setFooterFont(Array('freesans', '', 8));
					
					$pdf->setPrintHeader(false);
					$pdf->setPrintFooter(false);

					$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

					$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);
					$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
					$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

					$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

					$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

					$pdf->setFontSubsetting(true);

					$pdf->SetFont('freesans', '', 11, '', true);

					$pdf->AddPage();

					$pdf->writeHTMLCell(0, 0, '', '', $return_data['html'], 0, 1, 0, true, '', true);
					$pdf->Output('record-'.$record_id.'.pdf', 'I');
					exit;
					break;
				case 'transaction-print':
					$record_id = null;
					if (array_key_exists('id', $_REQUEST)) $record_id = intval($_REQUEST['id']);
					$return_data = $lepopup->transaction_details_html($record_id, true);
					if ($return_data['status'] != 'OK') {
						$this->error_message = $return_data['message'];
						return;
					}
					echo '
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
	<title>'.esc_html__('Transaction', 'lepopup').': '.esc_html($return_data['txn_id']).'</title>
	<style>body{font-family:arial;font-size:15px;}body::-webkit-scrollbar{width: 5px;}body::-webkit-scrollbar-track{box-shadow:inset 0 0 6px rgba(0,0,0,0.1);}body::-webkit-scrollbar-thumb{background-color:#bd4070;}div.not-found{margin: 40px;text-align:center;}</style>
	<link rel="stylesheet" href="'.$lepopup->plugins_url.'/css/admin.css?ver='.LEPOPUP_VERSION.'" type="text/css" media="all" />
</head>
<body>
	'.$return_data['html'].'
	<script type="text/javascript">window.print();</script>
</body>
</html>';
					exit;
					break;
				case 'transaction-pdf':
					$record_id = null;
					if (array_key_exists('id', $_REQUEST)) $record_id = intval($_REQUEST['id']);
					$return_data = $lepopup->transaction_details_html($record_id, true);
					if ($return_data['status'] != 'OK') {
						$this->error_message = $return_data['message'];
						return;
					}
					if (!class_exists("TCPDF")) require_once(dirname(dirname(__FILE__)).'/libs/tcpdf/tcpdf.php');
					$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
					$pdf->SetCreator(PDF_CREATOR);
					$pdf->SetAuthor('Green Popups');
					$pdf->SetTitle(esc_html__('Transaction', 'lepopup').': '.esc_html($return_data['txn_id']));
					$pdf->SetSubject(esc_html__('Record Details', 'lepopup'));
					$pdf->SetKeywords('Green Popups, Transaction');

					//$pdf->SetHeaderData(null, null, esc_html__('Transaction', 'lepopup').': '.esc_html($return_data['txn_id']), "by Green Popups", array(0,64,255), array(0,64,128));
					//$pdf->setFooterData(array(0,64,0), array(0,64,128));

					//$pdf->setHeaderFont(Array('freesans', '', 10));
					//$pdf->setFooterFont(Array('freesans', '', 8));

					$pdf->setPrintHeader(false);
					$pdf->setPrintFooter(false);

					$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

					$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);
					$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
					$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

					$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

					$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

					$pdf->setFontSubsetting(true);

					$pdf->SetFont('freesans', '', 11, '', true);

					$pdf->AddPage();

					$pdf->writeHTMLCell(0, 0, '', '', $return_data['html'], 0, 1, 0, true, '', true);
					$pdf->Output('transaction-'.$record_id.'.pdf', 'I');
					exit;
					break;
				case 'export':
//					if ($lepopup->demo_mode) {
//						$this->error_message = esc_html__('This operation disabled in DEMO mode.', 'lepopup');
//						return;
//					}
					$form_id = intval($_REQUEST["id"]);
					$export = $lepopup->export($form_id);
					$this->error_message = esc_html__('Requested form not found.', 'lepopup');
					return;
					break;
				case 'export-style':
					$style_id = intval($_REQUEST["id"]);
					$export = $lepopup->export_style($style_id);
					$this->error_message = esc_html__('Requested style not found.', 'lepopup');
					return;
					break;
				case 'import':
					if ($lepopup->demo_mode) {
						$this->error_message = esc_html__('This operation disabled in DEMO mode.', 'lepopup');
						return;
					}
					if (!array_key_exists('lepopup-file', $_FILES)) return;
					if (is_uploaded_file($_FILES["lepopup-file"]["tmp_name"])) {
						$dot_pos = strrpos($_FILES["lepopup-file"]["name"], '.');
						if ($dot_pos === false) {
							$this->error_message = esc_html__('Invalid form file.', 'lepopup');
							return;
						}
						$ext = strtolower(substr($_FILES["lepopup-file"]["name"], $dot_pos));
						if ($ext == '.txt') {
							$this->_import_form($_FILES["lepopup-file"]["tmp_name"], str_replace('http://', '//', $lepopup->plugins_url.'/images/default'));
							if (!empty($this->error_message)) return;
							$this->success_message = esc_html__('The new form successfully imported.', 'lepopup');
							return;
						} else if ($ext == '.zip') {
							$import_status = $this->import_zip($_FILES["lepopup-file"]["tmp_name"]);
							if ($import_status === true) $this->success_message = esc_html__('The new popup successfully imported.', 'lepopup');
							else $this->error_message = $import_status;
							return;
						} else {
							$this->error_message = esc_html__('Invalid popup file.', 'lepopup');
							return;
						}
					}
					$this->error_message = esc_html__('Popup file was not uploaded.', 'lepopup');
					return;
					break;
				case 'export-csv':
					if ($lepopup->demo_mode) {
						$this->error_message = esc_html__('This operation disabled in DEMO mode.', 'lepopup');
						return;
					}
					$form_id = intval($_REQUEST["id"]);
					if (!class_exists("lepopup_form")) include_once(dirname(__FILE__).'/core-form.php');
					$form_object = new lepopup_form($form_id);
					$form_full = array();
					if (!empty($form_object->id)) {
						$form_object->export_records();
					} else $this->error_message = esc_html__('Requested form not found.', 'lepopup');
					return;
					break;
				case 'clear-library-cache':
					$upload_dir = wp_upload_dir();
					$cache_dir = $upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/temp';
					$files = array_diff(scandir($cache_dir), array('.','..')); 
					foreach ($files as $file) { 
						if (is_file($cache_dir.'/'.$file) && substr($file, 0, 6) == 'cache-') {
							unlink($cache_dir.'/'.$file);
						}
					}
					$this->success_message = esc_html__('Library cache successfully cleared.', 'lepopup');
					return;
					break;
				case 'import-library':
					if (!array_key_exists('id', $_REQUEST) || !array_key_exists('key', $_REQUEST)) {
						$this->error_message = esc_html__('Invalid URL.', 'lepopup');
						return;
					}
					$purchase_code = preg_replace('#([a-z0-9]{8})-?([a-z0-9]{4})-?([a-z0-9]{4})-?([a-z0-9]{4})-?([a-z0-9]{12})#', '$1-$2-$3-$4-$5', strtolower($lepopup->options['purchase-code']));
					if (strlen($purchase_code) != 36) {
						$this->error_message = esc_html__('Invalid Item Purchase Code. Please make sure that you set correct Item Purchse Code on Settings page.', 'lepopup');
						return;
					}
					$key = preg_replace('/[^a-zA-Z0-9-]/', '', $_REQUEST['key']);
					$human_id = preg_replace('/[^a-zA-Z0-9-]/', '', $_REQUEST['id']);
					$upload_dir = wp_upload_dir();
					$zip_file = $upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/temp/cache-'.$key.'.zip';
					$use_cache = false;
					if (file_exists($zip_file)) {
						if (filemtime($zip_file)+3600*24 > time()) {
							$use_cache = true;
						}
					}
					if (!$use_cache) {
						$request = array(
							'purchase_code' => $lepopup->options['purchase-code'],
							'website' => get_bloginfo('wpurl'),
							'item' => $key
						);
						try {
							$url = trailingslashit(LEPOPUP_LIBRARY_URL).'download/';
							$curl = curl_init($url);
							curl_setopt($curl, CURLOPT_TIMEOUT, 20);
							curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
							curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
							curl_setopt($curl, CURLOPT_POST, true);
							curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
							curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
							curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
							curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36');
							//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
							$response = curl_exec($curl);
							if (curl_error($curl)) {
								$this->error_message = curl_error($curl);
								curl_close($curl);
								return;
							}
							$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
							curl_close($curl);
							if ($http_code != '200') {
								$result = json_decode($response, true);
								if($result && is_array($result) && !empty($result)) {
									$this->error_message = $result['message'];
								} else {
									$this->error_message = esc_html__('Can not download desired item.', 'lepopup');
								}
								return;
							}
							$result = file_put_contents($zip_file, $response);
							if ($result === false) {
								$this->error_message = sprintf(esc_html__('Can not save</strong> file in temp directory. Please re-activate the plugin and make sure that the following directory exists and writable: %s', 'lepopup'), $upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/temp/.');
								return;
							}
						} catch (Exception $e) {
						}
					}
					$import_status = $this->import_zip($zip_file, 'Popup # '.$human_id);
					if ($import_status !== true) {
						$this->error_message = $import_status;
						return;
					}
					$this->success_message = sprintf(esc_html__('Popup # %s successfully imported from library.', 'lepopup'), $human_id);
					return;
					break;
				default:
					break;
			}
		} else if (array_key_exists('lepopup-gettingstarted', $_REQUEST)) {
			if ($_REQUEST['lepopup-gettingstarted'] == 'on') {
				$lepopup->options['gettingstarted-enable'] = 'on';
				$lepopup->update_options();
			} else {
				$lepopup->options['gettingstarted-enable'] = 'off';
				$lepopup->update_options();
			}
		}
		if ($lepopup->options['file-autodelete'] > 0 && array_key_exists($lepopup->options['file-autodelete'], $lepopup->file_autodelete_options)) {
			$ids = array();
			$upload_dir = wp_upload_dir();
			$uploads = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_uploads WHERE file_deleted != '1' AND created < '".(time()-intval($lepopup->options['file-autodelete'])*24*3600)."' LIMIT 0, 32", ARRAY_A);
			foreach($uploads as $upload_details) {
				if (file_exists($upload_dir["basedir"].DIRECTORY_SEPARATOR.LEPOPUP_UPLOADS_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$upload_details['form_id'].DIRECTORY_SEPARATOR.$upload_details['filename']) && is_file($upload_dir["basedir"].DIRECTORY_SEPARATOR.LEPOPUP_UPLOADS_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$upload_details['form_id'].DIRECTORY_SEPARATOR.$upload_details['filename'])) {
					$deleted = unlink($upload_dir["basedir"].DIRECTORY_SEPARATOR.LEPOPUP_UPLOADS_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$upload_details['form_id'].DIRECTORY_SEPARATOR.$upload_details['filename']);
					if ($deleted) $ids[] = $upload_details['id'];
				} else $ids[] = $upload_details['id'];
			}
			if (!empty($ids)) {
				$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_uploads SET file_deleted = '1' WHERE id IN ('".implode("', '", $ids)."')");
			}
		}
	}

	function import_zip($_file, $_name = null) {
		global $lepopup;
		$str_id = $lepopup->random_string(16);
		$upload_dir = wp_upload_dir();
		$temp_dir = $upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/'.$str_id;
		if (!file_exists($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/temp') || !wp_mkdir_p($temp_dir)) {
			return esc_html__('Make sure that "temp" folder has write permissions.', 'lepopup');
		}
		if (!defined('UAP_CORE')) {
			require_once(ABSPATH.'wp-admin/includes/file.php');
			WP_Filesystem();
			$result = unzip_file($_file, $temp_dir);
		} else {
			$result = new WP_Error();
		}
		if (is_wp_error($result)) {
			try {
				$zip = new ZipArchive;
			} catch(Exception $e){
				return esc_html__('This operation requires ZipArchive module. It is not found.', 'lepopup');
			}
			if ($zip->open($_file) === true) {
				$zip->extractTo($temp_dir);
				$zip->close();
			} else {
				return esc_html__('Can not unzip archive into folder.', 'lepopup');
			}
		}
		$upload_url = trailingslashit($upload_dir['baseurl']).LEPOPUP_UPLOADS_DIR.'/'.$str_id;
		if (strtolower(substr($upload_url, 0, 7)) == 'http://') $upload_url = substr($upload_url, 5);
		else if (strtolower(substr($upload_url, 0, 8)) == 'https://') $upload_url = substr($upload_url, 6);

		$import_status = $this->_import_form($temp_dir.'/popup.txt', $upload_url, $_name);

		unlink($temp_dir.'/popup.txt');

		if ($import_status !== true) {
			$this->_remove_dir($temp_dir);
			return $import_status;
		}
		return true;
	}
	function _import_form($_file, $_url = null, $_name = null) {
		global $lepopup, $wpdb;
		if (empty($lepopup) || !is_a($lepopup, "lepopup_class")) {
			$lepopup = new lepopup_class();
		}
		if (!file_exists($_file)) {
			return esc_html__('Please make sure that you uploaded a valid popup file.', 'lepopup');
		}
		$lines = file($_file);
		$version = intval(trim($lines[0]));
		if ($version == 1) {
			include_once(dirname(__FILE__).'/core-legacy.php');
			$legacy = new lepopup_legacy_class();
			$import_status = $legacy->import_layered_popup($_file, $_url, $_name);
			return $import_status;
		} else if ($version > intval(LEPOPUP_EXPORT_VERSION)) {
			return esc_html__('Version of the popup file is not supported.', 'lepopup');
		}
		if (sizeof($lines) != 4) {
			return esc_html__('Invalid popup file.', 'lepopup');
		}
		$slug = trim($lines[1]);
		$md5_hash = trim($lines[2]);
		$form_data = trim($lines[3]);
		$form_data = base64_decode($form_data);
		if (!$form_data || md5($form_data) != $md5_hash) {
			return esc_html__('Popup file corrupted.', 'lepopup');
		}
		$form_details = json_decode($form_data, true);
		if (!$form_details || !is_array($form_details) || !array_key_exists('name', $form_details) || !array_key_exists('options', $form_details) || !array_key_exists('pages', $form_details) || !array_key_exists('elements', $form_details)) {
			return esc_html__('Popup file corrupted.', 'lepopup');
		}

		$default_form_options = $lepopup->default_form_options();
		$form_options = array_merge($default_form_options, $form_details['options']);
		if (!empty($_url)) {
			foreach ($lepopup->element_properties_meta['settings'] as $key => $element) {
				if (array_key_exists('type', $element) && $element['type'] == 'background-style') {
					if (array_key_exists($key.'-image', $form_options)) {
						$form_options[$key.'-image'] = str_replace('LEPOPUP-FORM-DIR', $_url, $form_options[$key.'-image']);
					}
				}
			}
//			if (array_key_exists('confirmations', $form_options) && !empty($form_options['confirmations'])) {
//				foreach ($form_options['confirmations'] as $key => $confirmation) {
//					$form_options['confirmations'][$key]['message'] = str_replace('LEPOPUP-FORM-DIR', $_url, $form_options['confirmations'][$key]['message']);
//				}
//			}
			if (array_key_exists('double-email-message', $form_options) && !empty($form_options['double-email-message'])) {
				$form_options['double-email-message'] = str_replace('LEPOPUP-FORM-DIR', $_url, $form_options['double-email-message']);
			}
			if (array_key_exists('double-message', $form_options) && !empty($form_options['double-message'])) {
				$form_options['double-message'] = str_replace('LEPOPUP-FORM-DIR', $_url, $form_options['double-message']);
			}
			if (array_key_exists('notifications', $form_options) && !empty($form_options['notifications'])) {
				foreach ($form_options['notifications'] as $key => $notification) {
					$form_options['notifications'][$key]['message'] = str_replace('LEPOPUP-FORM-DIR', $_url, $form_options['notifications'][$key]['message']);
				}
			}
		}

		$form_pages = array();
		$default_page_options = $lepopup->default_form_options("page");
		$default_page_confirmation_options = $lepopup->default_form_options("page-confirmation");
		foreach ($form_details['pages'] as $page_options) {
			if (is_array($page_options)) {
				if ($page_options['type'] == 'page') $page_options = array_merge($default_page_options, $page_options);
				else $page_options = array_merge($default_page_confirmation_options, $page_options);
				$form_pages[] = $page_options;
			}
		}

		$form_elements = array();
		foreach($form_details['elements'] as $element_options) {
			if (is_array($element_options) && array_key_exists('type', $element_options)) {
				$default_element_options = $lepopup->default_form_options($element_options['type']);
				$element_options = array_merge($default_element_options, $element_options);
				if (!empty($_url)) {
					if ($element_options['type'] == 'html') {
						$element_options['content'] = str_replace('LEPOPUP-FORM-DIR', $_url, $element_options['content']);
						if (array_key_exists('background-style-image', $element_options)) {
							$element_options['background-style-image'] = str_replace('LEPOPUP-FORM-DIR', $_url, $element_options['background-style-image']);
						}
					} else if ($element_options['type'] == 'rectangle') {
						if (array_key_exists('background-style-image', $element_options)) {
							$element_options['background-style-image'] = str_replace('LEPOPUP-FORM-DIR', $_url, $element_options['background-style-image']);
						}
					} else if ($element_options['type'] == 'imageselect') {
						foreach($element_options['options'] as $option_key => $option) {
							$element_options['options'][$option_key]['image'] = str_replace('LEPOPUP-FORM-DIR', $_url, $element_options['options'][$option_key]['image']);
						}
					}
				}
				$form_elements[] = json_encode($element_options);
			}
		}
		
		$check_slug = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND slug = '".esc_sql($slug)."'", ARRAY_A);
		if (!empty($check_slug)) {
			$base = $slug;
			$pos = strrpos($slug, '-');
			if ($pos !== false) {
				$last_part = substr($slug, $pos+1);
				if (strlen($last_part) == 0 || ctype_digit($last_part)) $base = substr($slug, 0, $pos);
			}
			$base .= '-';
			$rows = $wpdb->get_results("SELECT slug FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND slug LIKE '".esc_sql($wpdb->esc_like($base))."%'", ARRAY_A);
			$suffix = 2;
			foreach ($rows as $row) {
				$slug_suffix = str_replace($base, '', $row['slug']);
				if (ctype_digit($slug_suffix) && $slug_suffix >= $suffix) $suffix = $slug_suffix+1;
			}
			$slug = $base.$suffix;
		}
		
		$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_forms (name,slug,options,pages,elements,active,created,modified,deleted) VALUES (
			'".esc_sql($form_details['name'])."','".esc_sql($slug)."','".esc_sql(json_encode($form_options))."','".esc_sql(json_encode($form_pages))."','".esc_sql(json_encode($form_elements))."','0','".esc_sql(time())."','".esc_sql(time())."','0')");
		return true;
	}

	function _remove_dir($_dir) { 
		$files = array_diff(scandir($_dir), array('.','..')); 
		foreach ($files as $file) { 
			if (is_dir($_dir.'/'.$file)) {
				$this->_remove_dir($_dir.'/'.$file);
			} else {
				unlink($_dir.'/'.$file); 
			}
		}
		return rmdir($_dir);
	}
}
?>