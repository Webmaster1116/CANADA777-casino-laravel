<?php

if( !defined( 'ABSPATH') ) exit();

$orders = false;
//order=asc&ot=name&type=reg
if(isset($_GET['ot']) && isset($_GET['order']) && isset($_GET['type'])){
	$order = array();
	switch($_GET['ot']){
		case 'alias':
			$order['alias'] = ($_GET['order'] == 'asc') ? 'ASC' : 'DESC';
		break;
		case 'favorite':
			$order['favorite'] = ($_GET['order'] == 'asc') ? 'ASC' : 'DESC';
		break;
		case 'name':
		default:
			$order['title'] = ($_GET['order'] == 'asc') ? 'ASC' : 'DESC';
		break;
	}

	$orders = $order;
}


$slider = new RevSlider();
$operations = new RevSliderOperations();
$arrSliders = $slider->getArrSliders($orders);
$glob_vals = $operations->getGeneralSettingsValues();

$addNewLink = self::getViewUrl(RevSliderAdmin::VIEW_SLIDER);


$fav = get_option('rev_fav_slider', array());
if($orders == false){ //sort the favs to top
	if(!empty($fav) && !empty($arrSliders)){
		$fav_sort = array();
		foreach($arrSliders as $skey => $sort_slider){
			if(in_array($sort_slider->getID(), $fav)){
				$fav_sort[] = $arrSliders[$skey];
				unset($arrSliders[$skey]);
			}
		}
		if(!empty($fav_sort)){
			//revert order of favs
			krsort($fav_sort);
			foreach($fav_sort as $fav_arr){
				array_unshift($arrSliders, $fav_arr);
			}
		}
	}
}

global $revSliderAsTheme;

$exampleID = '"slider1"';
if(!empty($arrSliders))
	$exampleID = '"'.$arrSliders[0]->getAlias().'"';

$latest_version = get_option('revslider-latest-version', RevSliderGlobals::SLIDER_REVISION);
$stable_version = get_option('revslider-stable-version', '4.1');

$cur_js = get_option('revslider-latest-version-jquery', '-');
$latest_js = get_option('revslider-latest-version-jquery', '-');


?>

<div class='wrap'>
	<div class="clear_both"></div>
	<div class="title_line" style="margin-bottom:10px">
		<?php
		$icon_general = '<div class="icon32" id="icon-options-general"></div>';
		echo apply_filters( 'rev_icon_general_filter', $icon_general );
		?>
		<a href="<?php echo RevSliderGlobals::LINK_HELP_SLIDERS; ?>" class="button-secondary float_right mtop_10 mleft_10" target="_blank"><?php _e("Help",'revslider'); ?></a>
	</div>

	<div class="clear_both"></div>

	<div class="title_line nobgnopd" style="height:auto; min-height:50px">
		<div class="view_title">
			<?php _e("Revolution Sliders", 'revslider'); ?>
		</div>
		<div class="slider-sortandfilter">
				<span class="slider-listviews slider-lg-views" data-type="rs-listview"><i class="eg-icon-align-justify"></i></span>
				<span class="slider-gridviews slider-lg-views active" data-type="rs-gridview"><i class="eg-icon-th"></i></span>
				<span class="slider-sort-drop"><?php _e("Sort By:",'revslider'); ?></span>
				<select id="sort-sliders" name="sort-sliders" style="max-width: 105px;" class="withlabel">
					<option value="id" selected="selected"><?php _e("By ID",'revslider'); ?></option>
					<option value="name"><?php _e("By Name",'revslider'); ?></option>
					<option value="type"><?php _e("By Type",'revslider'); ?></option>
					<option value="favorit"><?php _e("By Favorit",'revslider'); ?></option>
				</select>

				<span class="slider-filter-drop"><?php _e("Filter By:",'revslider'); ?></span>

				<select id="filter-sliders" name="filter-sliders" style="max-width: 105px;" class="withlabel">
					<option value="all" selected="selected"><?php _e("All",'revslider'); ?></option>
					<option value="gallery"><?php _e("Gallery",'revslider'); ?></option>
					<option value="vimeo"><?php _e("Vimeo",'revslider'); ?></option>
					<option value="youtube"><?php _e("YouTube",'revslider'); ?></option>
					<option value="twitter"><?php _e("Twitter",'revslider'); ?></option>
					<option value="facebook"><?php _e("Facebook",'revslider'); ?></option>
					<option value="instagram"><?php _e("Instagram",'revslider'); ?></option>
					<option value="flickr"><?php _e("Flickr",'revslider'); ?></option>
				</select>
		</div>
		<div style="width:100%;height:1px;float:none;clear:both"></div>
	</div>


	<!--
	THE INFO ABOUT EMBEDING OF THE SLIDER
	-->
    <div class="rs-dialog-embed-slider" title="<?php _e("Embed Slider",'revslider'); ?>" style="display: none;">
        <div class="revyellow" style="background: none repeat scroll 0% 0% #F1C40F; left:0px;top:55px;position:absolute;height:205px;padding:20px 10px;"><i style="color:#fff;font-size:25px" class="revicon-arrows-ccw"></i></div>
		<div style="margin:5px 0px; padding-left: 55px;">
			<div style="font-size:14px;margin-bottom:10px;"><strong><?php _e("Standard Embeding",'revslider'); ?></strong></div>
			<?php _e("To",'revslider'); ?> <b><?php _e("include slider embed library",'revslider'); ?></b> <?php _e("use this code",'revslider'); ?>:<br />
			<code><?php echo htmlentities("<?php include 'embed.php'; ?>"); ?></code>
			<div style="width:100%;height:10px"></div>
			<?php _e("To",'revslider'); ?> <b><?php _e("add CSS and JS libraries to html header",'revslider'); ?></b> <?php _e("use this code",'revslider'); ?>:<br />
			<code><?php echo htmlentities("<?php RevSliderEmbedder::headIncludes(); ?>"); ?></code>
			<div style="width:100%;height:10px"></div>
			<?php _e("To",'revslider'); ?> <b><?php _e("insert slider to your page",'revslider'); ?></b> <?php _e("use this code",'revslider'); ?>:<br />
			<code><?php echo htmlentities("<?php RevSliderEmbedder::putRevSlider('"); ?><span class="rs-example-alias">alias</span><?php echo htmlentities("'); ?>"); ?></code>
		</div>
	</div>

	<?php
	if (check_for_jquery_addon())
	{
		$no_sliders = false;
		if(empty($arrSliders)){
			?>
			<span style="display:block;margin-top:15px;margin-bottom:15px;">
			<?php  _e("No Sliders Found",'revslider'); ?>
			</span>
			<?php
			$no_sliders = true;
		}

		require self::getPathTemplate('sliders-list');

		$cur_js = get_option('revslider-js-version', '1');
	}else{
		$cur_js = '-';
	}

	?>
	<div style="width:100%;height:40px;display:block"></div>

    <?php
    $isShowDashboard = true;
    $isShowDashboard = apply_filters('revslider_overview_show_dashboard', $isShowDashboard);
    if ($isShowDashboard) :
    ?>
	<!-- DASHBOARD -->
	<div class="rs-dashboard">
		<?php
		$validated = get_option('revslider-valid', 'false');
        $temp_active = get_option('revslider-temp-active', 'false');
		$code = get_option('revslider-code', '');
        //$email = get_option('revslider-email', '');
		$latest_version = get_option('revslider-latest-version', RevSliderGlobals::SLIDER_REVISION);

		$activewidgetclass = $validated === 'true'? "rs-status-green-wrap" : "rs-status-red-wrap";
        $activewidgetclass = $temp_active === 'true' ? "rs-status-orange-wrap" : $activewidgetclass;

		get_instance()->load->view('admin/welcome_page');

		$js_validated = get_option('jquery-plugin-code-activated', 'false');

		?>

		<!--
		THE CURRENT AND NEXT VERSION
		-->
		<?php

		$latest_js = get_option('revslider-latest-version-jquery', '1.1');

		if (version_compare(RevSliderGlobals::SLIDER_REVISION, $latest_version, '<') || version_compare($cur_js, $latest_js, '<') ) {
			$updateclass = 'rs-status-orange-wrap';
		} else {
			$updateclass = 'rs-status-green-wrap';
		}
		?>
		<div class="rs-dash-widget" id="updates_dw">
			<div class="rs-dash-title-wrap <?php echo $updateclass; ?>">
				<div class="rs-dash-title"><?php _e("Plugin Updates",'revslider'); ?></div>
				<div class="rs-dash-title-button rs-status-orange"><i class="icon-update-refresh"></i><?php _e("Update Available",'revslider'); ?></div>
				<div class="rs-dash-title-button rs-status-green"><i class="icon-no-problem-found"></i><?php _e("Plugin up to date",'revslider'); ?></div>
			</div>

			<div class="rs-dash-widget-inner" style="width:233px; display:inline-block">
				<div class="rs-dash-strong-content"><?php _e("Slider Installed Version",'revslider'); ?></div>
				<div><?php echo $cur_js; ?></div>
				<div class="rs-dash-content-space"></div>
				<div class="rs-dash-strong-content"><?php _e("Slider Latest Available Version",'revslider'); ?></div>
				<div><?php echo $latest_js; ?></div>
				<div class="rs-dash-content-space"></div>
				<a class='rs-dash-invers-button' href='?page=revslider&checkforupdates=true' id="rev_check_version"><?php _e("Check for Updates",'revslider'); ?> </a>
				<?php if(!RS_DEMO){ ?>
					<div class="rs-dash-bottom-wrapper">
					<?php if ($js_validated === 'true') {
							if (version_compare($cur_js, $latest_js, '<')) {
							?>
								<a href="javascript:void(0)" id="download_addon" class="rs-dash-button"><?php _e('Update Now', 'revslider'); ?></a>
							<?php
							} else {
							?>
								<span  class="rs-dash-button-gray"><?php _e('Up to date', 'revslider'); ?></span>
							<?php
							}
						} else {
							?>
							<span class="rs-dash-button-gray"><?php _e('Register for Update', 'revslider'); ?></span>
							<?php
						}
						?>
					</div>
				<?php } ?>
			</div>


			<div class="rs-dash-widget-inner" style="width:233px; display:inline-block">
				<div class="rs-dash-strong-content"><?php _e("Editor Installed Version",'revslider'); ?></div>
				<div><?php echo RevSliderGlobals::SLIDER_REVISION; ?></div>
				<div class="rs-dash-content-space"></div>
				<div class="rs-dash-strong-content"><?php _e("Editor Latest Available Version",'revslider'); ?></div>
				<div><?php echo $latest_version; ?></div>
				<div class="rs-dash-content-space"></div>
				<a class='rs-dash-invers-button' href='?page=revslider&checkforupdates=true' id="rev_check_version"><?php _e("Check for Updates",'revslider'); ?> </a>
				<?php if(!RS_DEMO){ ?>
					<div class="rs-dash-bottom-wrapper">
					<?php if ($validated === 'true')
						{
							if (version_compare(RevSliderGlobals::SLIDER_REVISION, $latest_version, '<')) {
							?>
								<a href="<?php echo site_url('c=admin&m=update'); ?>" id="rs-check-updates" class="rs-dash-button"><?php _e('Update Now', 'revslider'); ?></a>
							<?php
							} else {
							?>
                                <span  class="rs-dash-button-gray"><?php _e('Up to date', 'revslider'); ?></span>
							<?php
							}
						} else {
						?>
                            <span class="rs-dash-button" id="regsiter-to-access-update-none"><?php _e('Update', 'revslider'); ?></span>
						<?php
						}
						?>
					</div>
				<?php } ?>
			</div>
		</div><!-- END OF VERSION INFORMATION WIDGET -->


		<!-- Requirements & Recommendations -->
		<div class="rs-dash-widget" id="system_dw">
			<?php
				$dir = wp_upload_dir();
				$mem_limit = ini_get('memory_limit');
				$mem_limit_byte = wp_convert_hr_to_bytes($mem_limit);
				$upload_max_filesize = ini_get('upload_max_filesize');
				$upload_max_filesize_byte = wp_convert_hr_to_bytes($upload_max_filesize);
				$post_max_size = ini_get('post_max_size');
				$post_max_size_byte = wp_convert_hr_to_bytes($post_max_size);

				$writeable_boolean = wp_is_writable($dir['basedir'].'/') && wp_is_writable(RS_PLUGIN_PATH . 'public') && wp_is_writable(FCPATH);
				$can_connect = get_option('revslider-connection', false);
				$mem_limit_byte_boolean = $mem_limit_byte != -1 && $mem_limit_byte<268435456;
				$upload_max_filesize_byte_boolean = ($upload_max_filesize_byte < 33554432);
				$post_max_size_byte_boolean = ($post_max_size_byte < 33554432);
				$curl_status = function_exists('curl_version');
				$dash_rr_status = ($writeable_boolean==true && $can_connect==true && $mem_limit_byte_boolean==false && $upload_max_filesize_byte_boolean==false && $post_max_size_byte_boolean==false && $curl_status==true) ? "rs-status-green-wrap" : "rs-status-red-wrap";

			?>
			<div class="rs-dash-title-wrap <?php echo $dash_rr_status; ?>">
				<div class="rs-dash-title"><?php _e("System Requirements",'revslider'); ?></div>
				<div class="rs-dash-title-button rs-status-red"><i class="icon-problem-found"></i><?php _e("Problem Found",'revslider'); ?></div>
				<div class="rs-dash-title-button rs-status-green"><i class="icon-no-problem-found"></i><?php _e("No Problems",'revslider'); ?></div>
			</div>
			<div class="rs-dash-widget-inner">
				<span class="rs-dash-label"><?php _e('Uploads folder writable', 'revslider'); ?></span>
				<?php
				//check if uploads folder can be written into

				if($writeable_boolean){
					echo '<i class="revgreenicon eg-icon-ok"></i>';
				}else{
					echo '<i class="revredicon eg-icon-cancel"></i><span style="margin-left:16px" class="rs-dash-more-info" data-title="'.__('Error with File Permissions', 'revslider').'" data-content="'.__('Please set write permission (755 or 777) to your /, /media and /revslider/public folders to make sure the Slider can save all updates and imports in the future.', 'revslider').'"><i class="eg-icon-info"></i></span>';
				}
				?>


				<div class="rs-dash-content-space-small"></div>
				<span class="rs-dash-label"><?php _e('Memory Limit', 'revslider'); ?></span>
				<?php


				if($mem_limit_byte_boolean){
					//not good
					echo '<i style="margin-right:20px" class="revredicon eg-icon-cancel"></i>';
					echo '<span class="rs-dash-red-content">';
				} else {
					echo '<i style="margin-right:20px" class="revgreenicon eg-icon-ok"></i>';
					echo '<span class="rs-dash-strong-content">';
				}

				echo __('Currently:', 'revslider').' '.($mem_limit == -1 ? __('No Limit') : $mem_limit);
				echo '</span>';
				if($mem_limit_byte_boolean){
					//not good
					echo '<span class="rs-dash-strong-content" style="margin-left:20px">'. __('(min:256M)', 'revslider').'</span>';
				}
				?>
				<div class="rs-dash-content-space-small"></div>
				<span class="rs-dash-label"><?php _e('Upload Max. Filesize', 'revslider'); ?></span>
				<?php


				if($upload_max_filesize_byte_boolean){
					//not good
					echo '<i style="margin-right:20px" class="revredicon eg-icon-cancel"></i>';
					echo '<span class="rs-dash-red-content">';
				} else {
					echo '<i style="margin-right:20px"class="revgreenicon eg-icon-ok"></i>';
					echo '<span class="rs-dash-strong-content">';
				}

				echo __('Currently:', 'revslider').' '.$upload_max_filesize;
				echo '</span>';
				if($upload_max_filesize_byte_boolean){
					echo '<span class="rs-dash-strong-content" style="margin-left:20px">'. __('(min:32M)', 'revslider').'</span>';
				}
				?>
				<div class="rs-dash-content-space-small"></div>
				<span class="rs-dash-label"><?php _e('Max. Post Size', 'revslider'); ?></span>
				<?php



				if($post_max_size_byte_boolean){
				//not good
					echo '<i style="margin-right:20px" class="revredicon eg-icon-cancel"></i>';
					echo '<span class="rs-dash-red-content">';
				} else {
					echo '<i style="margin-right:20px"class="revgreenicon eg-icon-ok"></i>';
					echo '<span class="rs-dash-strong-content">';
				}

				echo __('Currently:', 'revslider').' '.$post_max_size;
				echo '</span>';
				if($post_max_size_byte_boolean){
					echo '<span class="rs-dash-strong-content" style="margin-left:20px">'. __('(min:32M)', 'revslider').'</span>';
				}
				?>

				<div class="rs-dash-content-space-small"></div>
				<span class="rs-dash-label"><?php _e('cURL Enabled', 'revslider'); ?></span>
				<?php
				if($curl_status){
					echo '<i class="revgreenicon eg-icon-ok"></i>';
				}else{
					echo '<i class="revredicon eg-icon-cancel"></i><span style="margin-left:16px" class="rs-dash-more-info" data-title="'.__('Error with cURL library', 'revslider').'" data-content="'.__('Please install PHP cURL library to make sure the Slider can connect ThemePunch server and download updates.', 'revslider').'"><i class="eg-icon-info"></i></span>';
				}
				?>

				<div class="rs-dash-content-space-small"></div>
				<span class="rs-dash-label"><?php _e('Contact ThemePunch Server', 'revslider'); ?></span>
				<?php

				if($can_connect){
					echo '<i class="revgreenicon eg-icon-ok"></i>';
				}else{
					echo '<i class="revredicon eg-icon-cancel"></i>';
				}
				?>
				<a class='rs-dash-invers-button' href='?page=revslider&checkforupdates=true' id="rev_check_version_1" style="margin-left:16px"><?php _e("Check Now",'revslider'); ?></a>
				<?php
				if(!$can_connect){
					echo '<span class="rs-dash-more-info" data-title="'.__('Error with contacting the ThemePunch Server', 'revslider').'" data-content="'.__('Please make sure that your server can connect to updates.themepunch.tools and templates.themepunch.tools programmatically.', 'revslider').'"><i class="eg-icon-info"></i></span>';
				}
				?>
			</div>
		</div><!-- END OF Requirements & Recommendations -->


		<!--
		TEMPLATE WIDGET
		-->
		<div id="templates_dw" class="rs-dash-widget">
			<div class="templatestore-bg"></div>
			<div class="rs-dash-title-wrap" style="position:relative; z-index:1">
				<div class="rs-dash-title"><?php _e("Start Downloading Templates",'revslider'); ?></div>
			</div>

			<div class="rs-dash-widget-inner">
				<?php if ($validated === 'true') {
					?>
					<div class="rs-dash-icon rs-dash-download"></div>
					<div class="rs-dash-content-with-icon">
						<div class="rs-dash-strong-content"><?php _e("Online Slider Library",'revslider'); ?></div>
						<div><?php _e("Full examples for instant usage",'revslider'); ?></div>
					</div>
					<div class="rs-dash-content-space"></div>
					<div class="rs-dash-icon rs-dash-diamond"></div>
					<div class="rs-dash-content-with-icon">
						<div class="rs-dash-strong-content"><?php _e("Get Free Premium Sliders",'revslider'); ?></div>
						<div class=""><?php _e("Activate your plugin and profit",'revslider'); ?></div>
					</div>
				<?php }
				 else {
				 ?>
					<div class="rs-dash-icon rs-dash-notregistered"></div>
					<div class="rs-dash-content-with-icon" style="width:190px;margin-right:20px">
						<div class="rs-dash-strong-content rs-dash-deactivated"><?php _e("Online Slider Library",'revslider'); ?></div>
						<div class="rs-dash-deactivated"><?php _e("Full examples for instant usage",'revslider'); ?></div>
					</div>
                    <span class="rs-dash-more-info" data-takemeback="false" data-title="<?php _e('How to Unlock Premium Features?', 'revslider');?>" data-content="<?php echo __('If you have purchased Slider Revolution from ThemePunch directly you can find your activation code here:', 'revslider').'<div class=\'rs-dash-content-space\'></div><a target=\'_blank\' href=\'http://revolution.themepunch.com/direct-customer-benefits/#productactivation\' class=\'rs-dash-invers-button\'>'.__('Where is my Purchase Code?','revslider').'</a><div class=\'rs-dash-content-space\'></div>'.__('Dont have a license yet? Purchase a license on CodeCanyon','revslider').'<div class=\'rs-dash-content-space\'></div><a target=\'_blank\' href=\'http://codecanyon.net/item/slider-revolution-jquery-visual-editor-addon/13934907?ref=themepunch&license=regular&open_purchase_for_item_id=13934907&purchasable=source\' class=\'rs-dash-button-small\'>'.__('Buy Now from $18','revslider').'</a>'; ?>"><span class="rs-dash-invers-button-gray rs-dash-close-panel"><?php _e('Unlock Now', 'revslider'); ?></span></span>
					<div class="rs-dash-content-space"></div>
					<div class="rs-dash-icon rs-dash-notregistered"></div>
					<div class="rs-dash-content-with-icon" style="width:190px;margin-right:20px">
						<div class="rs-dash-strong-content rs-dash-deactivated"><?php _e("Get Free Premium Sliders",'revslider'); ?></div>
						<div class="rs-dash-deactivated"><?php _e("Activate your plugin and profit",'revslider'); ?></div>
					</div>
                    <span class="rs-dash-more-info" data-takemeback="false" data-title="<?php _e('How to Unlock Premium Features?', 'revslider');?>" data-content="<?php echo __('If you have purchased Slider Revolution from ThemePunch directly you can find your activation code here:', 'revslider').'<div class=\'rs-dash-content-space\'></div><a target=\'_blank\' href=\'http://revolution.themepunch.com/direct-customer-benefits/#productactivation\' class=\'rs-dash-invers-button\'>'.__('Where is my Purchase Code?','revslider').'</a><div class=\'rs-dash-content-space\'></div>'.__('Dont have a license yet? Purchase a license on CodeCanyon','revslider').'<div class=\'rs-dash-content-space\'></div><a target=\'_blank\' href=\'http://codecanyon.net/item/slider-revolution-jquery-visual-editor-addon/13934907?ref=themepunch&license=regular&open_purchase_for_item_id=13934907&purchasable=source\' class=\'rs-dash-button-small\'>'.__('Buy Now from $18','revslider').'</a>'; ?>"><span class="rs-dash-invers-button-gray rs-dash-close-panel"><?php _e('Unlock Now', 'revslider'); ?></span></span>
				 <?php
				 }
				 ?>
				<div class="rs-dash-bottom-wrapper">
					<?php if ($validated === 'true') {
					?>
						<a href="javascript:void(0)" class="rs-dash-button" id="button_import_template_slider_b"><?php _e('Open Template Store', 'revslider'); ?></a>
					<?php }
				 else {
				 	?>
                         <span class="rs-dash-button" id="regsiter-to-access-store-none" ><?php _e('Open Template Library', 'revslider'); ?></span>
				  <?php
				 }
				 ?>
				</div>
			</div>

		</div><!-- END TEMPLATE WIDGET -->

		<!--
		NEWSLETTER
		-->
		<div class="rs-dash-widget" id="newsletter_dw">
			<div class="rs-dash-title-wrap">
				<div class="rs-dash-title"><?php _e("ThemePunch Newsletter",'revslider'); ?></div>
			</div>
			<div class="newsletter-bg"></div>
			<div class="rs-dash-widget-inner">
				<div class="rs-dash-icon rs-dash-speaker"></div>
				<div class="rs-dash-content-with-icon">
					<div class="rs-dash-strong-content"><?php _e("Stay Updated",'revslider'); ?></div>
					<div><?php _e("Receive info on the latest product updates & products",'revslider'); ?></div>
				</div>
				<div class="rs-dash-content-space"></div>
				<div class="rs-dash-icon rs-dash-gift"></div>
				<div class="rs-dash-content-with-icon">
					<div class="rs-dash-strong-content"><?php _e("Free Goodies",'revslider'); ?></div>
					<div><?php _e("Learn about free stuff we offer on a regular basis",'revslider'); ?></div>
				</div>
				<div class="rs-dash-content-space"></div>
				<div class="rs-dash-icon rs-dash-smile"></div>
				<div class="rs-dash-content-with-icon">
					<div class="rs-dash-strong-content"><?php _e("Provide Feedback",'revslider'); ?></div>
					<div><?php _e("Participate in survey and help us improve constantly",'revslider'); ?></div>
				</div>

				<div class="rs-dash-bottom-wrapper">
					<span class="subscribe-newsletter-wrap"><a href="javascript:void(0);" class="rs-dash-button" id="subscribe-to-newsletter"><?php _e('Subscribe', 'revslider'); ?></a></span>
					<input class="rs-dashboard-input" style="width:220px;margin-left:10px" type="text" value="" placeholder="<?php _e('Enter your E-Mail here', 'revslider'); ?>" name="rs-email" />
				</div>
			</div>

		</div><!-- END OF NEWSLETTER  -->


		<!--
		PRODUCT SUPPORT
		-->
		<div class="rs-dash-widget" id="support_dw">
			<div class="rs-dash-title-wrap">
				<div class="rs-dash-title"><?php _e("Product Support",'revslider'); ?></div>
			</div>
			<div class="rs-dash-widget-inner">

				<div class="rs-dash-icon rs-dash-copy"></div>
				<div class="rs-dash-content-with-icon">
					<div class="rs-dash-strong-content"><?php _e("Online Documentation",'revslider'); ?></div>
					<div><?php _e("The best start for Slider Revolution beginners",'revslider'); ?></div>
				</div>
				<div class="rs-dash-content-space"></div>
				<div class="rs-dash-icon rs-dash-light"></div>
				<div class="rs-dash-content-with-icon">
					<div class="rs-dash-strong-content"><?php _e("Browse FAQ's",'revslider'); ?></div>
					<div><?php _e("Instant solutions for most problems",'revslider'); ?></div>
				</div>
				<div class="rs-dash-content-space"></div>
				<?php if ($validated === 'true') {
					?>

					<div class="rs-dash-icon rs-dash-ticket"></div>
					<div class="rs-dash-content-with-icon">
						<div class="rs-dash-strong-content"><?php _e("Ticket Support",'revslider'); ?></div>
						<div><?php _e("Direct help from our qualified support team",'revslider'); ?></div>
					</div>
				<?php }
				 else {
				 ?>
					<div class="rs-dash-icon rs-dash-notregistered"></div>
					<div class="rs-dash-content-with-icon" style="width:278px;margin-right:20px">
						<div class="rs-dash-strong-content"><?php _e("Ticket Support",'revslider'); ?></div>
						<div><?php _e("Direct help from our qualified support team",'revslider'); ?></div>
					</div>
					<span class="rs-dash-more-info" data-takemeback="false" data-title="<?php _e('How to Unlock Premium Features?', 'revslider');?>" data-content="<?php echo __('If you have purchased Slider Revolution from ThemePunch directly you can find your activation code here:', 'revslider').'<div class=\'rs-dash-content-space\'></div><a target=\'_blank\' href=\'http://revolution.themepunch.com/direct-customer-benefits/#productactivation\' class=\'rs-dash-invers-button\'>'.__('Where is my Purchase Code?','revslider').'</a><div class=\'rs-dash-content-space\'></div>'.__('Dont have a license yet? Purchase a license on CodeCanyon','revslider').'<div class=\'rs-dash-content-space\'></div><a target=\'_blank\' href=\'http://codecanyon.net/item/slider-revolution-jquery-visual-editor-addon/13934907?ref=themepunch&license=regular&open_purchase_for_item_id=13934907&purchasable=source\' class=\'rs-dash-button-small\'>'.__('Buy Now','revslider').'</a>'; ?>"><span class="rs-dash-invers-button-gray rs-dash-close-panel">Unlock Now</span></span>
				 <?php
				 }
				 ?>

				 <div class="rs-dash-bottom-wrapper">
					<a href="http://www.themepunch.com/support-center/?rev=rsb" target="_blank" class="rs-dash-button"><?php _e('Visit Support Center', 'revslider'); ?></a>
				</div>



			</div>

		</div><!-- END OF PRODUCT SUPPORT  -->

		<!-- PRIVACY POLICY -->
		<div class="rs-dash-widget">
			<div class="rs-dash-title-wrap">
				<div class="rs-dash-title"><?php _e("Privacy Policy",'revslider'); ?></div>
			</div>
			<div class="rs-dash-widget-inner">
				<?php _e('In case you’re using Google Web Fonts (default) or playing videos or sounds via YouTube or Vimeo in Slider Revolution we recommend to add the corresponding text phrase to your privacy police:'); ?>
				<div class="rs-dash-bottom-wrapper">
					<a href="#" class="rs-dash-button" id="privacy_policy"><?php _e('Read Suggested Privacy Policy', 'revslider'); ?></a>
				</div>
			</div>
		</div>
		<!-- END OF PRIVACY POLICY -->

		<div class="tp-clearfix"></div>
	</div>
    <!-- END OF RS DASHBOARD -->
    <?php endif; ?>

	<!-- THE UPDATE HISTORY OF SLIDER REVOLUTION -->
	<div style="width:100%;height:40px"></div>
	<div class="rs-update-history-wrapper">
		<div class="rs-dash-title-wrap">
			<div class="rs-dash-title"><?php _e("Update History",'revslider'); ?></div>
		</div>
		<div class="rs-update-history"><?php echo file_get_contents(RS_PLUGIN_PATH.'release_log.html'); ?></div>
	</div>
</div>

<!-- Import slider dialog -->
<div id="dialog_import_slider" title="<?php _e("Import Slider",'revslider'); ?>" class="dialog_import_slider" style="display:none">
	<form action="<?php echo RevSliderBase::$url_ajax; ?>" enctype="multipart/form-data" method="post" id="form-import-slider-local">
		<br>
		<input type="hidden" name="action" value="revslider_ajax_action">
		<input type="hidden" name="client_action" value="import_slider_slidersview">
		<input type="hidden" name="nonce" value="<?php echo wp_create_nonce("revslider_actions"); ?>">
		<?php _e("Choose the import file",'revslider'); ?>:
		<br>
		<input type="file" size="60" name="import_file" class="input_import_slider">
		<br><br>
		<span style="font-weight: 700;"><?php _e("Note: styles templates will be updated if they exist!",'revslider'); ?></span><br><br>
		<table>
			<tr>
				<td><?php _e("Custom Animations:",'revslider'); ?></td>
				<td><input type="radio" name="update_animations" value="true" checked="checked"> <?php _e("Overwrite",'revslider'); ?></td>
				<td><input type="radio" name="update_animations" value="false"> <?php _e("Append",'revslider'); ?></td>
			</tr>
			<tr>
				<td><?php _e("Custom Navigations:",'revslider'); ?></td>
				<td><input type="radio" name="update_navigations" value="true" checked="checked"> <?php _e("Overwrite",'revslider'); ?></td>
				<td><input type="radio" name="update_navigations" value="false"> <?php _e("Append",'revslider'); ?></td>
			</tr>
			<?php
			$single_page_creation = RevSliderFunctions::getVal($glob_vals, "single_page_creation", "off");
			?>
			<tr style="<?php echo ($single_page_creation == 'on') ? '' : 'display: none;'; ?>">
				<td><?php _e('Create Blank Page:','revslider'); ?></td>
				<td><input type="radio" name="page-creation" value="true"> <?php _e('Yes', 'revslider'); ?></td>
				<td><input type="radio" name="page-creation" value="false" checked="checked"> <?php _e('No', 'revslider'); ?></td>
			</tr>
		</table>
		<br>
		<input type="submit" class="button-primary revblue tp-be-button rev-import-slider-button" style="display: none;" value="<?php _e("Import Slider",'revslider'); ?>">
	</form>
</div>

<div id="dialog_duplicate_slider" class="dialog_duplicate_layer" title="<?php _e('Duplicate', 'revslider'); ?>" style="display:none;">
	<div style="margin-top:14px">
		<span style="margin-right:15px"><?php _e('Title:', 'revslider'); ?></span><input id="rs-duplicate-animation" type="text" name="rs-duplicate-animation" value="" />
	</div>
</div>

<div id="dialog_duplicate_slider_package" class="dialog_duplicate_layer" title="<?php _e('Duplicate', 'revslider'); ?>" style="display:none;">
    <div style="margin-top:14px">
        <span style="margin-right:15px"><?php _e('Prefix:', 'revslider'); ?></span><input id="rs-duplicate-prefix" type="text" name="rs-duplicate-prefix" value="" />
    </div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function(){
		RevSliderAdmin.initSlidersListView();
		RevSliderAdmin.initNewsletterRoutine();

		jQuery('#benefitsbutton').hover(function() {
			jQuery('#benefitscontent').slideDown(200);
		}, function() {
			jQuery('#benefitscontent').slideUp(200);
		});

		jQuery('#why-subscribe').hover(function() {
			jQuery('#why-subscribe-wrapper').slideDown(200);
		}, function() {
			jQuery('#why-subscribe-wrapper').slideUp(200);
		});

		jQuery('#tp-validation-box').click(function() {
			jQuery(this).css({cursor:"default"});
			if (jQuery('#rs-validation-wrapper').css('display')=="none") {
				jQuery('#tp-before-validation').hide();
				jQuery('#rs-validation-wrapper').slideDown(200);
			}
		});
	});

	jQuery('body').on('click','.rs-dash-more-info',function() {
		var btn = jQuery(this),
			p = btn.closest('.rs-dash-widget-inner'),
			tmb = btn.data('takemeback'),
			btxt = '';

		btxt = btxt + '<div class="rs-dash-widget-warning-panel">';
		btxt = btxt + '	<i class="eg-icon-cancel rs-dash-widget-wp-cancel"></i>';
		btxt = btxt + '	<div class="rs-dash-strong-content">'+ btn.data("title")+'</div>';
		btxt = btxt + '	<div class="rs-dash-content-space"></div>';
		btxt = btxt + '	<div>'+btn.data("content")+'</div>';

		if (tmb!=="false" && tmb!==false) {
			btxt = btxt + '	<div class="rs-dash-content-space"></div>';
			btxt = btxt + '	<span class="rs-dash-invers-button-gray rs-dash-close-panel">Thanks! Take me back</span>';
		}
		btxt = btxt + '</div>';

		p.append(btxt);
		var panel = p.find('.rs-dash-widget-warning-panel');

		punchgs.TweenLite.fromTo(panel,0.3,{y:-10,autoAlpha:0},{autoAlpha:1,y:0,ease:punchgs.Power3.easeInOut});
		panel.find('.rs-dash-widget-wp-cancel, .rs-dash-close-panel').click(function() {
			punchgs.TweenLite.to(panel,0.3,{y:-10,autoAlpha:0,ease:punchgs.Power3.easeInOut});
			setTimeout(function() {
				panel.remove();
			},300)
		})
	});
</script>
<?php
require self::getPathTemplate('template-slider-selector');
?>
<div style="visibility: none;" id="register-wrong-purchase-code"></div>