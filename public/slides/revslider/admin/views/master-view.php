<?php
if( !defined( 'ABSPATH') ) exit();

global $revSliderVersion;

$wrapperClass = apply_filters( 'rev_overview_wrapper_class_filter', RevSliderGlobals::$isNewVersion == false ? " oldwp" : "");
$nonce = wp_create_nonce("revslider_actions");
$waitstyle = isset($_REQUEST['update_shop']) ? 'display:block' : '';

$operations = new RevSliderOperations();
$glval = $operations->getGeneralSettingsValues();
$pack_page_creation = RevSliderFunctions::getVal($glval, "pack_page_creation", "on");
$single_page_creation = RevSliderFunctions::getVal($glval, "single_page_creation", "off");
$tp_color_picker_presets = TPColorpicker::get_color_presets();

$template = new RevSliderTemplate();
?>

<div id="waitaminute" style="<?php echo $waitstyle; ?>">
    <div class="waitaminute-message"><i class="eg-icon-emo-coffee"></i><br><?php _e("Please Wait...", 'revslider'); ?></div>
</div>

<div id="rs-premium-benefits-dialog" style="display: none;">
	<div class="rs-premium-benefits-dialogtitles" id="rs-wrong-purchase-code-title">
		<span class="oppps-icon"></span>
		<span class="benefits-title-right">
            <span class="rs-premium-benefits-dialogtitle"><?php _e('Ooops... Wrong Purchase Code!', 'revslider'); ?></span>
            <span class="rs-premium-benefits-dialogsubtitle"><?php _e('Maybe just a typo? (Click <a target="_blank" href="https://revolution.themepunch.com/direct-customer-benefits/#productactivation">here</a> to find out how to locate your Slider Revolution purchase code.)', 'revslider'); ?></span>
        </span>
	</div>
	<div style="display:none" class="rs-premium-benefits-dialogtitles" id="rs-plugin-cachewarning-title">
		<span class="oppps-icon"></span>
		<span class="benefits-title-right">
            <span class="rs-premium-benefits-dialogtitle"><?php _e('Make sure to clear all caches!', 'revslider'); ?></span>
            <span class="rs-premium-benefits-dialogsubtitle"><?php _e('Here are the most common cache issues explained.', 'revslider'); ?></span>
        </span>
	</div>
	<div style="display:none" class="rs-premium-benefits-dialogtitles" id="rs-plugin-update-feedback-title">
		<span class="oppps-icon-red"></span>
		<span class="benefits-title-right">
            <span class="rs-premium-benefits-dialogtitle"><?php _e('Plugin Activation Required'); ?></span>
            <span class="rs-premium-benefits-dialogsubtitle"><?php _e('In order to download the <a target="_blank" href="http://codecanyon.net/item/slider-revolution-jquery-visual-editor-addon/13934907?ref=themepunch">latest update</a> instantly', 'revslider'); ?></span>
        </span>
	</div>
	<div style="display:none" class="rs-premium-benefits-dialogtitles" id="rs-plugin-object-library-feedback-title">
		<span class="oppps-icon"></span>
		<span class="benefits-title-right">
            <span class="rs-premium-benefits-dialogtitle"><?php _e('Plugin Activation Required'); ?></span>
            <span class="rs-premium-benefits-dialogsubtitle"><?php _e('In order to download from the <a target="_blank" href="https://revolution.themepunch.com/object-library/">Object Library</a> instantly', 'revslider'); ?></span>
        </span>
	</div>
	<div style="display:none" class="rs-premium-benefits-dialogtitles" id="rs-plugin-download-template-feedback-title">
		<span class="oppps-icon"></span>
		<span class="benefits-title-right">
            <span class="rs-premium-benefits-dialogtitle"><?php _e('Plugin Activation Required'); ?></span>
            <span class="rs-premium-benefits-dialogsubtitle"><?php _e('In order to gain instant access to the entire <a target="_blank" href="https://revolution.themepunch.com/examples/">Template Library</a>', 'revslider'); ?></span>
        </span>
	</div>

	<div style="display:none" class="rs-premium-benefits-dialogtitles" id="rs-library-license-info-dialogtitle">
		<span class="oppps-icon"></span>
		<span class="benefits-title-right">
            <span class="rs-premium-benefits-dialogtitle"><?php _e('Object Library License Information'); ?></span>
            <span class="rs-premium-benefits-dialogsubtitle"><?php _e('What you need to know for using Slider Revolution elements in your projects', 'revslider'); ?></span>
        </span>
	</div>

	<div id="basic_premium_benefits_block">
		<div class="rs-premium-benefits-block rspb-withborder">
			<h3><i class="big_present"></i><?php _e('If you purchased a theme that bundled Slider Revolution', 'revslider'); ?></h3>
			<ul>
				<li><?php _e('No activation needed to use to use / create sliders with Slider Revolution', 'revslider'); ?></li>
				<li><?php _e('Update manually through your theme', 'revslider'); ?></li>
				<li><?php _e('Access our <a target="_blank" class="rspb_darklink" href="https://www.themepunch.com/support-center/#support">FAQ database</a> and <a target="_blank" class="rspb_darklink" href="https://www.youtube.com/playlist?list=PLSCdqDWVMJPPDcH_57CNZvLckoB8cimJF">video tutorials</a> for help', 'revslider'); ?></li>
			</ul>
		</div>
		<div class="rs-premium-benefits-block">
			<h3><i class="big_diamond"></i><?php _e('Activate Slider Revolution for', 'revslider'); ?> <span class="instant_access"></span> <?php _e('to ...', 'revslider'); ?></h3>
			<ul>
				<li><?php _e('<a target="_blank" href="https://revolution.themepunch.com/direct-customer-benefits/#liveupdates">Update</a> to the latest version directly from your dashboard', 'revslider'); ?></li>
				<li><?php _e('<a target="_blank" href="https://themepunch.ticksy.com/submit/">Support</a> ThemePunch ticket desk', 'revslider'); ?></li>
				<li><?php _e('<a target="_blank" href="https://revolution.themepunch.com/direct-customer-benefits/#addons">Add-Ons</a> for Slider Revolution', 'revslider'); ?></li>
				<li><?php _e('<a target="_blank" href="https://revolution.themepunch.com/examples/">Library</a> with tons of free premium slider templates', 'revslider'); ?></li>
				<li><?php _e('<a target="_blank" href="https://revolution.themepunch.com/direct-customer-benefits/#objectlibrary">Object Library</a> with tons of images ready to be used', 'revslider'); ?></li>
			</ul>
		</div>
		<a target="_blank" class="get_purchase_code" href="http://codecanyon.net/item/slider-revolution-jquery-visual-editor-addon/13934907?ref=themepunch&license=regular&open_purchase_for_item_id=13934907&purchasable=source"><?php _e('GET A PURCHASE CODE', 'revslider'); ?></a>
	</div>

	<!-- WARNING BLOCK -->
	<div id="cachewarning_block" style="display:none">
		<div class="rs-premium-benefits-block">
			<h3><i class="big_light"></i><?php _e('Click on the following to learn how to fix issues', 'revslider'); ?></h3>
			<ul>
				<li><a target="_blank" href="https://www.themepunch.com/faq/updating-make-sure-clear-caches#hosting"><?php _e('Hosting Cache', 'revslider'); ?></a></li>
				<li><a target="_blank" href="https://www.themepunch.com/faq/updating-make-sure-clear-caches#plugins"><?php _e('Caching Plugins', 'revslider'); ?></a></li>
				<li><a target="_blank" href="https://www.themepunch.com/faq/updating-make-sure-clear-caches#cdn"><?php _e('CDNs (Content Delivery Networks)', 'revslider'); ?></a></li>
                <li><a target="_blank" href="https://www.themepunch.com/faq/updating-make-sure-clear-caches#browser"><?php _e('Browser Cache', 'revslider'); ?></a></li>
			</ul>
		</div>
		<div class="rs-premium-benefits-block" style="border:none;">
			<h3><i class="big_diamond"></i><?php _e('Cache Plugins Installed on your Server', 'revslider'); ?></h3>
			<ul>
				<?php
				$cache_plugins = $operations->get_installed_cache_plugins();
				if(!empty($cache_plugins)){
					foreach($cache_plugins as $name => $url){
						?>
						<li><a href="<?php echo esc_url($url); ?>" target="_blank"><?php echo esc_html($name); ?></a></li><?php
					}
				}
				?>
			</ul>
		</div>

	</div>

	<div id="basic_objectlibrary_license_block">
		<div id="license_object_library_type_list_new">
			<span data-id="svg_license_content" class="license_obj_library_cats_filter">SVG</span>
			<span data-id="ico_license_content" class="license_obj_library_cats_filter">ICON</span>
			<span data-id="png_license_content" class="license_obj_library_cats_filter selected">PNG</span>
			<span data-id="jpg_license_content" class="license_obj_library_cats_filter">JPG</span>
		</div>

		<div class="license_deep_content" id="svg_license_content" style="display:none">
			<h3><i class="pe-7s-folder"></i><?php _e('Terms of Using SVG Objects from the Object Library', 'revslider'); ?></h3>
			<ul>
				<li><?php _e('Usage only allowed within Slider Revolution Plugin', 'revslider'); ?></li>
				<li><?php _e('A variety of sizes and densities can be also downloaded from the <br> <a target="_blank" class="rspb_darklink" href="https://github.com/google/material-design-icons">git repository</a>, making it even easier for developers to customize, share,<br> and re-use outside of Slider Revolution.', 'revslider'); ?></li>
				<li><?php _e('Licenses via Apache License. Read More at <a target="_blank" class="rspb_darklink" href="https://github.com/google/material-design-icons/blob/master/LICENSE">Google Material Design Icons</a>', 'revslider'); ?></li>

			</ul>
		</div>

		<div class="license_deep_content" id="ico_license_content" style="display:none">
			<h3><i class="pe-7s-folder"></i><?php _e('Terms of Using ICON Objects from the Object Library', 'revslider'); ?></h3>
			<ul>
				<li><?php _e('Please check the listed license files for details about how you can use the "FontAwesome" and "Stroke 7 Icon" font sets for commercial projects, open source projects, or really just about whatever you want.', 'revslider'); ?></li>
				<li><?php _e('Please respect all other icon fonts licenses for fonts not included directly into Slider Revolution.', 'revslider'); ?></li>

			</ul>
			<h3><i class="fa-icon-list-alt"></i><?php _e('Further License Informations', 'revslider'); ?></h3>
			<ul>
				<li><strong>Font Awesome 4.6.3</strong> by @davegandy - http://fontawesome.io - @fontawesome<br>
					License - <a target="_blank" href="http://fontawesome.io/license">http://fontawesome.io/license</a> (Font: SIL OFL 1.1, CSS: MIT License)</li>
				<li><strong>Stroke 7 Icon Font Set</strong> by www.pixeden.com<br>
					Get your Freebie Iconset at <a href="http://www.pixeden.com/icon-fonts/stroke-7-icon-font-set" target="_blank">http://www.pixeden.com/icon-fonts/stroke-7-icon-font-set</a></li>
			</ul>
		</div>

		<div class="license_deep_content" id="png_license_content">
			<h3><i class="pe-7s-folder"></i><?php _e('Terms of Using PNG Objects from the Object Library', 'revslider'); ?></h3>
			<ul>
				<li><?php _e('Usage only allowed within Slider Revolution Plugin', 'revslider'); ?></li>
				<li><?php _e('Licenses via extended license and cooperation with<br>author <a target="_blank" class="rspb_darklink" href="https://creativemarket.com/Qeaql">Qeaql</a>', 'revslider'); ?></li>
                <li><?php _e('If you need .psd files for objects, you can purchase it from the original<br>author <a target="_blank" class="rspb_darklink" href="https://creativemarket.com/Qeaql/126175-Scene-creator-Top-view">here</a>', 'revslider'); ?></li>
            </ul>
		</div>

		<div class="license_deep_content" id="jpg_license_content" style="display:none">
			<h3><i class="pe-7s-folder"></i><?php _e('Terms of Using JPG Objects from the Object Library', 'revslider'); ?></h3>
			<ul>
				<li><?php _e('The pictures are free for personal and even for commercial use.', 'revslider'); ?></li>
				<li><?php _e('You can modify, copy and distribute the photos. All without asking for permission or setting a link to the source. So, attribution is not required.', 'revslider'); ?></li>
				<li><?php _e('The only restriction is that identifiable people may not appear in a bad light or in a way that they may find offensive, unless they give their consent.', 'revslider'); ?></li>
				<li><?php _e('The CC0 license was released by the non-profit organization Creative Commons (CC). Get more information about Creative Commons images and the license on the official license page.', 'revslider'); ?></li>
				<li><?php _e('<a target="_blank" class="rspb_darklink" href="http://allthefreestock.com/">All The Freestock</a> under the License <a target="_blank" class="rspb_darklink" href="https://creativecommons.org/publicdomain/zero/1.0/">CC0 Creative Commons</a>', 'revslider'); ?></li>
			</ul>
		</div>
	</div>

	<!-- PRIVACY POLICY DIALOG TITLE -->
	<div style="display:none" class="rs-premium-benefits-dialogtitles privacy_policy_dialog_element">
		<span class="oppps-icon"></span>
		<span class="benefits-title-right">
			<span class="rs-premium-benefits-dialogtitle"><?php _e('Suggested Privacy Policy'); ?></span>
			<span class="rs-premium-benefits-dialogsubtitle"><?php _e('Recommended to add to your privacy policy'); ?></span>
		</span>
	</div>
	<!-- END OF PRIVACY POLICY DIALOG TITLE -->

	<!-- PRIVACY POLICY DIALOG -->
	<div class="privacy_policy_dialog_element">
		<div class="rs-premium-benefits-block rspb-withborder">
			<?php _e('<p>In case you’re using Google Web Fonts (default) or playing videos or sounds via YouTube or Vimeo in Slider Revolution we recommend to add the corresponding text phrase to your privacy policy:</p>','revslider'); ?>
		</div>
		<div class="rs-premium-benefits-block">
			<?php _e('<h3>YouTube</h3> <p>Our website uses plugins from YouTube, which is operated by Google. The operator of the pages is YouTube LLC, 901 Cherry Ave., San Bruno, CA 94066, USA.</p> <p>If you visit one of our pages featuring a YouTube plugin, a connection to the YouTube servers is established. Here the YouTube server is informed about which of our pages you have visited.</p> <p>If you\'re logged in to your YouTube account, YouTube allows you to associate your browsing behavior directly with your personal profile. You can prevent this by logging out of your YouTube account.</p> <p>YouTube is used to help make our website appealing. This constitutes a justified interest pursuant to Art. 6 (1) (f) DSGVO.</p> <p>Further information about handling user data, can be found in the data protection declaration of YouTube under <a href="https://www.google.de/intl/de/policies/privacy" target="_blank">https://www.google.de/intl/de/policies/privacy</a>.</p>
			<h3>Vimeo</h3> <p>Our website uses features provided by the Vimeo video portal. This service is provided by Vimeo Inc., 555 West 18th Street, New York, New York 10011, USA.</p> <p>If you visit one of our pages featuring a Vimeo plugin, a connection to the Vimeo servers is established. Here the Vimeo server is informed about which of our pages you have visited. In addition, Vimeo will receive your IP address. This also applies if you are not logged in to Vimeo when you visit our plugin or do not have a Vimeo account. The information is transmitted to a Vimeo server in the US, where it is stored.</p> <p>If you are logged in to your Vimeo account, Vimeo allows you to associate your browsing behavior directly with your personal profile. You can prevent this by logging out of your Vimeo account.</p> <p>For more information on how to handle user data, please refer to the Vimeo Privacy Policy at <a href="https://vimeo.com/privacy" target="_blank">https://vimeo.com/privacy</a>.</p>
			<h3>Google Web Fonts</h3> <p>For uniform representation of fonts, this page uses web fonts provided by Google. When you open a page, your browser loads the required web fonts into your browser cache to display texts and fonts correctly.</p> <p>For this purpose your browser has to establish a direct connection to Google servers. Google thus becomes aware that our web page was accessed via your IP address. The use of Google Web fonts is done in the interest of a uniform and attractive presentation of our plugin. This constitutes a justified interest pursuant to Art. 6 (1) (f) DSGVO.</p> <p>If your browser does not support web fonts, a standard font is used by your computer.</p> <p>Further information about handling user data, can be found at <a href="https://developers.google.com/fonts/faq" target="_blank">https://developers.google.com/fonts/faq</a> and in Google\'s privacy policy at <a href="https://www.google.com/policies/privacy/" target="_blank">https://www.google.com/policies/privacy/</a>.</p>
			<h3>SoundCloud</h3><p>On our pages, plugins of the SoundCloud social network (SoundCloud Limited, Berners House, 47-48 Berners Street, London W1T 3NF, UK) may be integrated. The SoundCloud plugins can be recognized by the SoundCloud logo on our site.</p>
			<p>When you visit our site, a direct connection between your browser and the SoundCloud server is established via the plugin. This enables SoundCloud to receive information that you have visited our site from your IP address. If you click on the “Like” or “Share” buttons while you are logged into your SoundCloud account, you can link the content of our pages to your SoundCloud profile. This means that SoundCloud can associate visits to our pages with your user account. We would like to point out that, as the provider of these pages, we have no knowledge of the content of the data transmitted or how it will be used by SoundCloud. For more information on SoundCloud’s privacy policy, please go to https://soundcloud.com/pages/privacy.</p><p>If you do not want SoundCloud to associate your visit to our site with your SoundCloud account, please log out of your SoundCloud account.</p>','revslider'); ?>
		</div>
	</div>
	<!-- END OF PRIVACY POLICY DIALOG -->

</div>


<script>

	var show_premium_dialog = function(clicked) {
		jQuery('#rs-premium-benefits-dialog').dialog({
			width:830,
			height:750,
			modal:true,
			resizable:false,
			open:function(ui) {
				var dialog = jQuery(ui.target).parent(),
					dialogtitle = dialog.find('.ui-dialog-title');

				dialog.addClass("rs-open-premium-benefits-dialog-container");
				if (!dialogtitle.hasClass("titlechanged")) {
					dialogtitle.html("");
					dialogtitle.append(jQuery('#rs-premium-benefits-dialog .rs-premium-benefits-dialogtitles'));
					dialogtitle.addClass("titlechanged");
				}

				//HIDE TITLE
				jQuery('#rs-library-license-info-dialogtitle, #rs-plugin-object-library-feedback-title, #rs-wrong-purchase-code-title, #rs-plugin-update-feedback-title, #rs-plugin-download-template-feedback-title,.privacy_policy_dialog_element').hide();
				jQuery('#rs-premium-benefits-dialog').removeClass("nomainbg")
				//HIDE CONTENT
				jQuery('#basic_objectlibrary_license_block, #basic_premium_benefits_block,.privacy_policy_dialog_element').hide();

				switch (clicked) {
					case "regsiter-to-access-update-none":
						jQuery('#rs-plugin-update-feedback-title').show();
						jQuery('#basic_premium_benefits_block').show();
						break;
					case "regsiter-to-access-store-none":
						jQuery('#rs-plugin-download-template-feedback-title').show();
						jQuery('#basic_premium_benefits_block').show();
						break;
					case "register-wrong-purchase-code":
						jQuery('#rs-wrong-purchase-code-title').show();
						jQuery('#basic_premium_benefits_block').show();
						break;
					case "register-to-acess-object-library":
						jQuery('#rs-plugin-object-library-feedback-title').show();
						jQuery('#basic_premium_benefits_block').show();
						break;
					case "licence_obect_library":
						jQuery('#basic_objectlibrary_license_block').show();
						jQuery('#rs-library-license-info-dialogtitle').show();
						jQuery('#rs-premium-benefits-dialog').addClass("nomainbg");
						break;
					case "cahce-warning-dialog":
						jQuery('#rs-plugin-cachewarning-title').show();
						jQuery('#cachewarning_block').show();
						jQuery('#rs-premium-benefits-dialog').addClass("cachbg");
						jQuery('#rs-premium-benefits-dialog').height(600)
						break;
					case "privacy_policy" :
						jQuery('.privacy_policy_dialog_element').show();
						jQuery('#rs-premium-benefits-dialog').addClass("nomainbg");
						break;
				}
			}
		});
	}

</script>

<script type="text/javascript">
	var g_urlContent = "<?php echo str_replace(array("\n", "\r", chr(10), chr(13)), array(''), content_url())."/"; ?>";
	var g_urlAjaxShowImage = "<?php echo RevSliderBase::$url_ajax_showimage; ?>";
	var g_urlAjaxActions = "<?php echo RevSliderBase::$url_ajax_actions; ?>";
	var g_revslider_url = "<?php echo RS_PLUGIN_URL; ?>";
	var g_settingsObj = {};
	var rs_pack_page_creation = <?php echo ($pack_page_creation == 'on') ? 'true' : 'false'; ?>;
	var rs_single_page_creation = <?php echo ($single_page_creation == 'on') ? 'true' : 'false'; ?>;
	var tp_color_picker_presets = jQuery.parseJSON(<?php echo RevSliderFunctions::jsonEncodeForClientSide($tp_color_picker_presets); ?>);

	var global_grid_sizes = {
		'desktop': '<?php echo RevSliderBase::getVar($glval, 'width', 1230); ?>',
		'notebook': '<?php echo RevSliderBase::getVar($glval, 'width_notebook', 1230); ?>',
		'tablet': '<?php echo RevSliderBase::getVar($glval, 'width_tablet', 992); ?>',
		'mobile': '<?php echo RevSliderBase::getVar($glval, 'width_mobile', 480); ?>'
	};

    var RS_DEMO = <?php echo (RS_DEMO) ? 'true' : 'false'; ?>;
</script>

<div id="div_debug"></div>

<div class='unite_error_message' id="error_message" style="display:none;"></div>

<div class='unite_success_message' id="success_message" style="display:none;"></div>

<div id="viewWrapper" class="view_wrapper<?php echo $wrapperClass; ?>">
	<?php self::requireView($view); ?>
</div>

<div id="divColorPicker" style="display:none;"></div>

<?php self::requireView("system/dialog-video"); ?>

<div class="tp-plugin-version">
	<span style="margin-right:15px">&copy; All rights reserved, <a href="http://www.themepunch.com" target="_blank">ThemePunch</a>  ver. <?php echo $revSliderVersion; ?></span>
</div>

<div id="rs-shop-overview">
	<?php
	$shop_data = get_option('tp-shop');
	?>
</div>

<div id="rs-preview-wrapper" style="display: none;">
	<div id="rs-preview-wrapper-inner">
		<div id="rs-preview-info">
			<div class="rs-preview-toolbar">
				<a class="rs-close-preview"><i class="eg-icon-cancel"></i></a>
			</div>

			<div data-type="desktop" class="rs-preview-device_selector_prev rs-preview-ds-desktop selected"></div>
			<div data-type="notebook" class="rs-preview-device_selector_prev rs-preview-ds-notebook"></div>
			<div data-type="tablet" class="rs-preview-device_selector_prev rs-preview-ds-tablet"></div>
			<div data-type="mobile" class="rs-preview-device_selector_prev rs-preview-ds-mobile"></div>

		</div>
		<div class="rs-frame-preview-wrapper">
			<iframe id="rs-frame-preview" name="rs-frame-preview"></iframe>
		</div>
	</div>
</div>
<form id="rs-preview-form" name="rs-preview-form" action="<?php echo RevSliderBase::$url_ajax_actions; ?>" target="rs-frame-preview" method="post">
	<input type="hidden" id="rs-client-action" name="client_action" value="">
	<input type="hidden" id="rs-nonce" name="rs-nonce" value="<?php echo $nonce; ?>">

	<!-- SPECIFIC FOR SLIDE PREVIEW -->
	<input type="hidden" name="data" value="" id="preview-slide-data">

	<!-- SPECIFIC FOR SLIDER PREVIEW -->
	<input type="hidden" id="preview_sliderid" name="sliderid" value="">
	<input type="hidden" id="preview_slider_markup" name="only_markup" value="">
</form>


<div id="dialog_preview_sliders" class="dialog_preview_sliders" title="Preview Slider" style="display:none;">
	<iframe id="frame_preview_slider" name="frame_preview_slider" style="width: 100%;"></iframe>
</div>

<script type="text/javascript">
    <?php
	$validated = get_option('revslider-valid', 'false');
	?>
	rs_plugin_validated = <?php echo ($validated == 'true') ? 'true' : 'false'; ?>;

    jQuery('body').on('click','.rs-preview-device_selector_prev', function() {
    	var btn = jQuery(this);
    	jQuery('.rs-preview-device_selector_prev.selected').removeClass("selected");
    	btn.addClass("selected");

    	var w = parseInt(global_grid_sizes[btn.data("type")],0);
    	if (w>1450) w = 1450;
    	jQuery('#rs-preview-wrapper-inner').css({maxWidth:w+"px"});

    });

    jQuery(window).resize(function() {
    	var ww = jQuery(window).width();
    	if (global_grid_sizes)
	    	jQuery.each(global_grid_sizes,function(key,val) {
	    		if (ww<=parseInt(val,0)) {
	    			jQuery('.rs-preview-device_selector_prev.selected').removeClass("selected");
	    			jQuery('.rs-preview-device_selector_prev[data-type="'+key+'"]').addClass("selected");
	    		}
	    	})
    })

	/* SHOW A WAIT FOR PROGRESS */
	function showWaitAMinute(obj) {
        var wm = jQuery('#waitaminute');

        // CHANGE TEXT
        if (obj.text != undefined) {
            switch (obj.text) {
                case "progress1":

                    break;
                default:

                    wm.html('<div class="waitaminute-message"><i class="eg-icon-emo-coffee"></i><br>'+obj.text+'</div>');
                    break;
            }
        }


        if (obj.delay!=undefined) {
			punchgs.TweenLite.to(wm,0.3,{autoAlpha:1,ease:punchgs.Power3.easeInOut});
			punchgs.TweenLite.set(wm,{display:"block"});

			setTimeout(function() {
				punchgs.TweenLite.to(wm,0.3,{autoAlpha:0,ease:punchgs.Power3.easeInOut,onComplete:function() {
					punchgs.TweenLite.set(wm,{display:"block"});
				}});
			},obj.delay)
		}

		// SHOW IT
        if (obj.fadeIn !== undefined) {
			punchgs.TweenLite.to(wm,obj.fadeIn/1000,{autoAlpha:1,ease:punchgs.Power3.easeInOut});
            punchgs.TweenLite.set(wm,{display:"block"});
		}

		// HIDE IT
        if (obj.fadeOut !== undefined) {
			punchgs.TweenLite.to(wm,obj.fadeOut/1000,{autoAlpha:0,ease:punchgs.Power3.easeInOut,onComplete:function() {
					punchgs.TweenLite.set(wm,{display:"block"});
				}});
		}

    }


    jQuery(document).ready(function(){
        jQuery('#licence_obect_library, #regsiter-to-access-update-none, #regsiter-to-access-store-none, #register-wrong-purchase-code,#privacy_policy').click(function(e){
			e.preventDefault();
            var clicked = jQuery(this).attr('id');
            show_premium_dialog(clicked);
        });

        jQuery('.license_obj_library_cats_filter').click(function() {
            var t = jQuery(this);
            jQuery('.license_obj_library_cats_filter').removeClass("selected");
            t.addClass("selected");
            jQuery('.license_deep_content').hide();
            jQuery("#"+t.data('id')).show();
        });

    });

</script>