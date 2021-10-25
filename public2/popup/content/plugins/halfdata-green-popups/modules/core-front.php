<?php
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_front_class {
	function __construct() {
		global $lepopup;
		if (is_admin()) {
		} else {
			add_action('init', array(&$this, 'init'), 15);
			add_action('wp', array(&$this, 'front_init'), 15);
			add_shortcode('lepopup', array(&$this, "shortcode_handler"));
			add_shortcode('lepopuplinklocker', array(&$this, "shortcode_linklocker_handler"));			
			// Compatibilty with posts handled by BuddyPress, 2020-07-16, begin
			if (function_exists('buddypress')) {
				add_filter('the_content', array(&$this, "bp_the_content"));
			}
			// Compatibilty with posts handled by BuddyPress, 2020-07-16, end
		}
	}

	// Compatibilty with posts handled by BuddyPress, 2020-07-16, begin
	function bp_the_content($_content) {
		global $post, $lepopup;
		$component = null;
		if (function_exists('bp_current_component')) $component = bp_current_component();
		if ($lepopup->advanced_options['async-init'] == 'on' && $component == 'blog' && is_single() && !empty($post) && !empty($post->ID)) $lepopup->front_footer .= '<script>lepopup_content_id="'.esc_html($post->ID).'";</script>';
		return $_content;
	}
	// Compatibilty with posts handled by BuddyPress, 2020-07-16, end

	function front_init() {
		global $wpdb, $post, $current_user, $lepopup, $porto_settings, $w2dc_instance;

		$post_id = 0;
		$posts_page_id = get_option('page_for_posts');
		if (is_home() && !empty($posts_page_id)) {
			$post_id = $posts_page_id;
		} else if (function_exists('is_product') && is_product()) {
			if (!empty($post)) $post_id = $post->ID;
			else $post_id = 0;
		} else if (function_exists('is_shop') && is_shop() && (function_exists('woocommerce_get_page_id') || function_exists('wc_get_page_id'))) {
			if (function_exists('wc_get_page_id')) $post_id = wc_get_page_id('shop');
			else $post_id = woocommerce_get_page_id('shop');
		} else if (is_singular()) {
			if (!empty($post)) $post_id = $post->ID;
			else $post_id = 0;
		} else if (defined('porto_version') && is_post_type_archive('portfolio')) {
			$post_id = $porto_settings['portfolio-archive-page'];
		} else if (defined('porto_version') && is_post_type_archive('event')) {
			$post_id = $porto_settings['event-archive-page'];
		} else if (defined('porto_version') && is_post_type_archive('member')) {
			$post_id = $porto_settings['member-archive-page'];
		} else if (defined('porto_version') && is_post_type_archive('faq')) {
			$post_id = $porto_settings['faq-archive-page'];
		}
		if (class_exists("w2dc_plugin") && is_object($w2dc_instance)) {
			if (property_exists($w2dc_instance, "frontend_controllers") && array_key_exists('webdirectory-listing', $w2dc_instance->frontend_controllers) && is_array($w2dc_instance->frontend_controllers['webdirectory-listing'])) {
				$w2dc_controller = $w2dc_instance->frontend_controllers['webdirectory-listing'][0];
				if (is_object($w2dc_controller) && $w2dc_controller->is_single && property_exists($w2dc_controller, "listing")) {
					$post_id = $w2dc_controller->listing->post->ID;
				}
			}
		}
		
		if ($post_id == 0 && (is_tax() || is_tag() || is_category())) {
			$queried_object = get_queried_object();
			if (is_a($queried_object, 'WP_Term')) {
				if (property_exists($queried_object, 'term_id') && property_exists($queried_object, 'taxonomy')) {
					unset($post_id);
					$post_id = '{'.$queried_object->term_id.'}{'.$queried_object->taxonomy.'}';
				}
			}
		}
		if ($post_id == 0 && is_home()) $post_id = 'homepage';
		else if (function_exists("avia_get_option") && $post_id == avia_get_option('frontpage') && get_option('show_on_front') == 'posts') $post_id = 'homepage';
		include_once(dirname(__FILE__).'/core-targeting.php');
		$targeting = new lepopup_class_targeting();
		$targets = $targeting->front_init_inline($post_id);

		$forced_form_id = null;
		if (array_key_exists('lepopup', $_GET)) {
			$forced_slug = preg_replace('/[^a-zA-Z0-9-]/', '', $_GET['lepopup']);
			$forced_form_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND slug = '".esc_sql($forced_slug)."'", ARRAY_A);
			if ($forced_form_details) $forced_form_id = $forced_form_details['id'];
		}
		
		$javascript_vars_html = '';
		$preloaded_items = '';
		
		if ($lepopup->advanced_options['async-init'] == 'on') {
			$javascript_vars_html = 'var lepopup_events_data={};';
		} else {
			$event_data = $targeting->get_events_data($post_id, (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ? 'http://' : 'https://').$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
			$tmp = array();
			foreach ($event_data['events-data'] as $key => $value) {
				$tmp[] = "'".esc_html($key)."':'".esc_html($value)."'";
			}
			$javascript_vars_html = 'var lepopup_events_data={'.implode(',',$tmp).'};';
			$forms = array();
			if ($lepopup->options['preload'] == 'on') {
				$forms = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND active = '1'", ARRAY_A);
			} else if ($lepopup->options['preload-event-popups'] == 'on') {
				if (is_array($event_data['event-items']) && !empty($event_data['event-items'])) {
					$form_ids = array();
					$rows = $wpdb->get_results("SELECT t1.*, t2.slug FROM ".$wpdb->prefix."lepopup_campaign_items t1 LEFT JOIN ".$wpdb->prefix."lepopup_campaigns t2 ON t2.id = t1.campaign_id WHERE t1.deleted = '0' AND t2.deleted = '0' AND t2.active = '1' AND t2.slug IN ('".implode("','", $event_data['event-items'])."')", ARRAY_A);
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
			if (!empty($forms)) {
				if (!class_exists("lepopup_form")) include_once(dirname(__FILE__).'/core-form.php');
				foreach ($forms as $form) {
					$form_object = new lepopup_form($form['id'], true);
					if (!empty($form_object->id)) {
						$form = $form_object->get_form_html();
						if (is_array($form) && array_key_exists('style', $form) && array_key_exists('html', $form)) {
							$html = '<div class="lepopup-popup-container" id="lepopup-popup-'.esc_html($form_object->id).'" onclick="jQuery(\'#lepopup-popup-'.esc_html($form_object->id).'-overlay\').click();">'.$form['html'].'</div>';
							$preloaded_items .= $form['style'].$html;
						}
					}
				}
			}
		}
		$lepopup->front_header .= '
		<script>'.(defined('ULP_VERSION') ? 'var lepopup_ulp="on";' : '').(!empty($forced_form_id) ? 'var lepopup_preview="on";' : '').'var lepopup_customjs_handlers={};var lepopup_cookie_value="'.esc_html($lepopup->options['cookie-value']).'";'.$javascript_vars_html;
		if ($lepopup->advanced_options['async-init'] == 'on') {
			$lepopup->front_header .= 'var lepopup_content_id="'.esc_html($post_id).'";';
			if (defined('ICL_LANGUAGE_CODE')) {
				if (ICL_LANGUAGE_CODE != 'all') $lepopup->front_header .= 'var lepopup_icl_language="'.esc_html(ICL_LANGUAGE_CODE).'";';
			}
		}
		$lepopup->front_header .= '</script>';
		$lepopup->front_header .= '<script>function lepopup_add_event(_event,_data){if(typeof _lepopup_add_event == typeof undefined){jQuery(document).ready(function(){_lepopup_add_event(_event,_data);});}else{_lepopup_add_event(_event,_data);}}</script>';
		
		$forms = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND (active = '1'".(!empty($forced_form_id) ? " OR id = '".esc_sql($forced_form_id)."'" : "").")", ARRAY_A);
		$default_form_options = $lepopup->default_form_options();
		$overlays_array = array();
		foreach ($forms as $form_details) {
			$form_options = json_decode($form_details['options'], true);
			if (!empty($form_options) && is_array($form_options)) $form_options = array_merge($default_form_options, $form_options);
			else $form_options = $default_form_options;
			$overlays_array[] = '"'.$form_details['slug'].'":["'.$form_details['id'].'","'.$form_options['position'].'","'.$form_options['overlay-enable'].'","'.(empty($form_options['overlay-color']) ? 'transparent' : $form_options['overlay-color']).'","'.$form_options['overlay-click'].'","'.$form_options['overlay-animation'].'","'.(empty($form_options["spinner-color-color1"]) ? '#FF5722' : $form_options["spinner-color-color1"]).'","'.(empty($form_options["spinner-color-color2"]) ? '#FF9800' : $form_options["spinner-color-color2"]).'","'.(empty($form_options["spinner-color-color3"]) ? '#FFC107' : $form_options["spinner-color-color3"]).'","'.(array_key_exists('cookie-lifetime', $form_options) ? intval($form_options['cookie-lifetime']) : '365').'"]';
		}
		$campaigns = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_campaigns WHERE deleted = '0' AND active = '1'", ARRAY_A);
		$campaigns_array = array();
		foreach ($campaigns as $campaigns_details) {
			$form_slugs = $wpdb->get_results("SELECT t2.slug FROM ".$wpdb->prefix."lepopup_campaign_items t1 LEFT JOIN ".$wpdb->prefix."lepopup_forms t2 ON t2.id = t1.form_id WHERE t1.deleted = '0' AND t2.deleted = '0' AND t2.active = '1' AND t1.campaign_id = '".esc_sql($campaigns_details['id'])."'", ARRAY_A);
			$forms_array = array();
			foreach($form_slugs as $form_slug) {
				if (!empty($form_slug['slug'])) $forms_array[] = $form_slug['slug'];
			}
			if (!empty($forms_array)) $campaigns_array[] = '"'.$campaigns_details['slug'].'":["'.implode('","', $forms_array).'"]';
		}
		$lepopup->front_footer .= $preloaded_items.'
<script>
	var lepopup_ajax_url = "'.admin_url('admin-ajax.php').'";
	var lepopup_ga_tracking = "'.$lepopup->options['ga-tracking'].'";
	var lepopup_abd_enabled = "'.$lepopup->options['adblock-detector-enable'].'";
	var lepopup_async_init = "'.$lepopup->advanced_options['async-init'].'";
	var lepopup_preload = "'.$lepopup->options['preload'].'";
	var lepopup_overlays = {'.implode(',', $overlays_array).'};
	var lepopup_campaigns = {'.implode(',', $campaigns_array).'};
</script>'.($lepopup->options['adblock-detector-enable'] == 'on' ? '<script src="https://static.doubleclick.net/instream/ad_status.js"></script>' : '');

		add_action('wp_enqueue_scripts', array(&$this, 'front_enqueue_scripts'), 99);
		add_action('wp_head', array(&$this, 'front_header'), 15);
		add_action('wp_footer', array(&$this, 'front_footer'), 999);
		if (!defined('ULP_VERSION')) {
			add_shortcode('ulp', array(&$this, "shortcode_handler"));
			add_shortcode('ulplinklocker', array(&$this, "shortcode_linklocker_handler"));			
		}
	}

	function front_enqueue_scripts() {
		global $lepopup;
		if ($lepopup->advanced_options['minified-sources'] == 'on') $min = '.min';
		else $min = '';
		wp_enqueue_script("jquery");
		wp_enqueue_style('lepopup-style', $lepopup->plugins_url.'/css/style'.$min.'.css', array(), LEPOPUP_VERSION);
		wp_enqueue_script('lepopup', $lepopup->plugins_url.'/js/lepopup'.$min.'.js', array('jquery'), LEPOPUP_VERSION, true);
		if ($lepopup->options['fa-enable'] == 'on') {
			if ($lepopup->options['fa-css-disable'] != 'on') {
				if ($lepopup->options['fa-solid-enable'] == 'on' && $lepopup->options['fa-regular-enable'] == 'on' && $lepopup->options['fa-brands-enable'] == 'on') wp_enqueue_style('lepopup-font-awesome-all', $lepopup->plugins_url.'/css/fontawesome-all'.$min.'.css', array(), LEPOPUP_VERSION);
				else {
					wp_enqueue_style('lepopup-font-awesome-all', $lepopup->plugins_url.'/css/fontawesome'.$min.'.css', array(), LEPOPUP_VERSION);
					if ($lepopup->options['fa-solid-enable'] == 'on') wp_enqueue_style('lepopup-font-awesome-solid', $lepopup->plugins_url.'/css/fontawesome-solid'.$min.'.css', array(), LEPOPUP_VERSION);
					if ($lepopup->options['fa-regular-enable'] == 'on') wp_enqueue_style('lepopup-font-awesome-regular', $lepopup->plugins_url.'/css/fontawesome-regular'.$min.'.css', array(), LEPOPUP_VERSION);
					if ($lepopup->options['fa-brands-enable'] == 'on') wp_enqueue_style('lepopup-font-awesome-brands', $lepopup->plugins_url.'/css/fontawesome-brands'.$min.'.css', array(), LEPOPUP_VERSION);
				}
			}
		}
		if ($lepopup->options['airdatepicker-enable'] == 'on') {
			if ($lepopup->options['airdatepicker-js-disable'] != 'on') {
				wp_enqueue_style('airdatepicker', $lepopup->plugins_url.'/css/airdatepicker'.$min.'.css', array(), LEPOPUP_VERSION);
				wp_enqueue_script('airdatepicker', $lepopup->plugins_url.'/js/airdatepicker'.$min.'.js', array('jquery'), LEPOPUP_VERSION, true);
			}
		}
		if ($lepopup->options['jsep-enable'] == 'on') {
			if ($lepopup->options['jsep-js-disable'] != 'on') {
				wp_enqueue_script('jsep', $lepopup->plugins_url.'/js/jsep'.$min.'.js', array('lepopup'), LEPOPUP_VERSION, true);
			}
		}
		if ($lepopup->options['signature-enable'] == 'on') {
			if ($lepopup->options['signature-js-disable'] != 'on') {
				wp_enqueue_script('signature', $lepopup->plugins_url.'/js/signature_pad'.$min.'.js', array('lepopup'), LEPOPUP_VERSION, true);
			}
		}
		if ($lepopup->options['range-slider-enable'] == 'on') {
			if ($lepopup->options['range-slider-js-disable'] != 'on') {
				wp_enqueue_style('Ion.RangeSlider', $lepopup->plugins_url.'/css/ion.rangeSlider'.$min.'.css', array(), LEPOPUP_VERSION);
				wp_enqueue_script('Ion.RangeSlider', $lepopup->plugins_url.'/js/ion.rangeSlider'.$min.'.js', array('jquery'), LEPOPUP_VERSION, true);
			}
		}
		if ($lepopup->options['mask-enable'] == 'on') {
			if ($lepopup->options['mask-js-disable'] != 'on') {
				wp_enqueue_script('jquery.mask', $lepopup->plugins_url.'/js/jquery.mask'.$min.'.js', array('jquery'), LEPOPUP_VERSION, true);
			}
		}		
		do_action('lepopup_wp_enqueue_scripts');
	}

	function init() {
		global $wpdb, $lepopup;
		if (array_key_exists('lepopup-confirm', $_REQUEST)) {
			$confirmation_id = preg_replace('/[^a-zA-Z0-9-]/', '', $_REQUEST['lepopup-confirm']);
			$message = esc_html__('Invalid confirmation URL.', 'lepopup');
			$forms = '';
			if (!empty($confirmation_id)) {
				$record_details = $wpdb->get_row("SELECT t1.*, t2.name AS form_name, t2.options AS form_options, t2.elements AS form_elements FROM ".$wpdb->prefix."lepopup_records t1 LEFT JOIN ".$wpdb->prefix."lepopup_forms t2 ON t2.id = t1.form_id WHERE t1.deleted = '0' AND t1.str_id = '".esc_sql($confirmation_id)."'", ARRAY_A);
				if ($record_details) {
					if ($record_details['status'] == LEPOPUP_RECORD_STATUS_CONFIRMED) {
						$message = esc_html__('Email address already confirmed.', 'lepopup');
					} else {
						if (!class_exists("lepopup_form")) include_once(dirname(__FILE__).'/core-form.php');
						$form_object = new lepopup_form(intval($record_details['form_id']));
						if (empty($form_object->id)) {
							$message = esc_html__('Relevant form not found.', 'lepopup');
						} else {
							$form_object->form_data = json_decode($record_details['fields'], true);
							$form_object->form_info = json_decode($record_details['info'], true);
							$form_object->form_extra = json_decode($record_details['extra'], true);
							$form_object->record_id = $record_details['id'];
							
							do_action("lepopup_populate_form_extra", $form_object->form_extra);
							do_action("lepopup_populate_record_id", $form_object->record_id);
							
							$datestamp = date('Ymd', time()+3600*$lepopup->gmt_offset);
							$timestamp = date('h', time()+3600*$lepopup->gmt_offset);
							$stats_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_stats WHERE form_id = '".esc_sql($form_object->id)."' AND datestamp = '".esc_sql($datestamp)."' AND timestamp = '".esc_sql($timestamp)."'", ARRAY_A);
							if (!empty($stats_details)) {
								$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_stats SET confirmed = confirmed + 1 WHERE id = '".esc_sql($stats_details['id'])."'");
							} else {
								$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_stats (form_id, impressions, submits, confirmed, payments, datestamp, timestamp, deleted) VALUES ('".esc_sql($form_object->id)."', '0', '0', '1', '0', '".esc_sql($datestamp)."', '".esc_sql($timestamp)."', '0')");
							}
							$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_records SET status = '".esc_sql(LEPOPUP_RECORD_STATUS_CONFIRMED)."' WHERE id = '".esc_sql($record_details['id'])."'");

							$form_object->do_notifications("confirm");
							$integrations_data = $form_object->do_integrations("confirm");
							if (array_key_exists('forms', $integrations_data)) $forms = implode('', $integrations_data['forms']);
							do_action('lepopup_confirmed', $form_object);
							
							$message = $form_object->replace_shortcodes($form_object->form_options['double-message'], array(), false, true); // UF-checked
							if (!empty($form_object->form_options['double-url'])) {
								$url = $form_object->replace_shortcodes($form_object->form_options['double-url'], array(), true); // UF-checked
								$form_object->update_extra();
								header('Location: '.$url);
								exit;
							} else $form_object->update_extra();
						}
					}
				}
			}
			echo '<!DOCTYPE html>
<html>
<head>
	<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>'.esc_html__('Confirmation', 'lepopup').'</title>
	<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300&subset=latin,cyrillic-ext,greek-ext,latin-ext,cyrillic,greek,vietnamese" rel="stylesheet" type="text/css">
	<link href="'.$lepopup->plugins_url.'/css/tiny-content.css" rel="stylesheet" type="text/css">
	<style>
	body {font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif; font-weight: 100; color: #444; background-color: #fff; line-height: 1.475;}
	.front-container {position: absolute;top: 0;right: 0;bottom: 0;left: 0;min-width: 240px;height: 100%;display: table;width: 100%;}
	.front-content {max-width: 1024px;margin: 0px auto;padding: 20px 0;position: relative;display: table-cell;text-align: center;vertical-align: middle;}
	</style>
</head>
<body>
	<div class="front-container">
		<div class="front-content">
			'.$message.'
		</div>
	</div>'.(empty($forms) ? '' : $forms.'
	<script>
		var buttons = document.getElementsByClassName("lepopup-send");
		for (var i=0; i<buttons.length; i++) {
			buttons[i].click();
		}
	</script>').'
</body>
</html>';
			exit;
		} else if (array_key_exists('lepopup-dl', $_REQUEST) && defined('LEPOPUP_ALLOW_FORM_EXPORT') && LEPOPUP_ALLOW_FORM_EXPORT === true) {
			$form_id = intval($_REQUEST["lepopup-dl"]);
			$export = $lepopup->export($form_id);
		}
	}

	function front_header() {
		global $wpdb, $lepopup;
		echo $lepopup->front_header;
	}

	static function front_footer() {
		global $wpdb, $lepopup;
		echo $lepopup->front_footer;
	}

	static function shortcode_handler($_atts) {
		global $post, $wpdb, $lepopup;
		$html = '';
		if (is_feed()) {
			if (isset($_atts['feed'])) {
				return '<div>'.$_atts['feed'].'</div>';
			} else return '';
		}
		$id_slug = null;
		if (array_key_exists('id', $_atts) && !empty($_atts['id'])) $id_slug = $_atts['id'];
		else if (array_key_exists('slug', $_atts) && !empty($_atts['slug'])) $id_slug = $_atts['slug'];
		if (empty($id_slug)) return '';
		if (!class_exists("lepopup_form")) include_once(dirname(__FILE__).'/core-form.php');
		$popups = explode('*', $id_slug);
		if (sizeof($popups) > 1 && $popups[0] == $popups[1]) $popups = array($popups[0]);
		$form_object = new lepopup_form($popups[0]);
		if (!empty($form_object->id)) {
			if (array_key_exists('xd', $_atts) && $_atts['xd'] === true && $form_object->form_options['cross-domain'] != 'on') {
				$html .= '<div class="lepopup-inline"'.(sizeof($popups) > 1 ? ' data-device="desktop"' : '').'><div class="lepopup-xd-forbidden">'.esc_html__('Cross-domain calls are not allowed for this popup.', 'lepopup').'</div></div>';
			}
			$form = $form_object->get_form_html();
			if (!empty($form)) {
				$html .= $form['style'].'<div class="lepopup-inline"'.(sizeof($popups) > 1 ? ' data-device="desktop"' : '').' style="margin: 0 auto;">'.$form['html'].'</div>';
			}
		}
		if (sizeof($popups) > 1) {
			$form_object = new lepopup_form($popups[1]);
			if (!empty($form_object->id)) {
				if (array_key_exists('xd', $_atts) && $_atts['xd'] === true && $form_object->form_options['cross-domain'] != 'on') {
					$html .= '<div class="lepopup-inline" data-device="mobile"><div class="lepopup-xd-forbidden">'.esc_html__('Cross-domain calls are not allowed for this popup.', 'lepopup').'</div></div>';
				}
				$form = $form_object->get_form_html();
				if (!empty($form)) {
					$html .= $form['style'].'<div class="lepopup-inline" data-device="mobile" style="margin: 0 auto;">'.$form['html'].'</div>';
				}
			}
		}
		return $html;
	}
	
	function shortcode_linklocker_handler($_atts, $_content = null) {
		global $wpdb;
		if (is_feed()) {
			if (isset($_atts['feed'])) {
				return '<div>'.$_atts['feed'].'</div>';
			} else return $_content;
		}
		$content = $_content;
		$id_slug = null;
		if (array_key_exists('id', $_atts) && !empty($_atts['id'])) $id_slug = $_atts['id'];
		else if (array_key_exists('slug', $_atts) && !empty($_atts['slug'])) $id_slug = $_atts['slug'];
		if (empty($id_slug)) return $content;
		
		$regexp = "<a\s[^>]*href=(\"|'??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		$original_links = array();
		$new_links = array();
		if (preg_match_all("/".$regexp."/siU", $_content, $matches)) {
			foreach ($matches[0] as $key => $value) {
				if (substr($matches[2][$key], 0, 1) != '#' && !empty($matches[2][$key])) {
					$original_links[] = $value;
					$new_links[] = preg_replace('/'.preg_quote($matches[2][$key], '/').'/', '#lepopup-'.$id_slug.':'.base64_encode($matches[2][$key]), $value, 1);
				}
			}
			$content = str_replace($original_links, $new_links, $_content);
		}
		return $content;
	}
	
	function widgets_init() {
		include_once(dirname(dirname(__FILE__)).'/widget.php');
		register_widget('lepopup_widget');
	}
}
?>