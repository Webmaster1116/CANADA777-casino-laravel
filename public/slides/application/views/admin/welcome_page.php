<a name="activateaddon"></a>

<?php 
$validated = get_option('revslider-valid', 'false');
$temp_active = get_option('revslider-temp-active', 'false');
$code = get_option('revslider-code', '');
//$email = get_option('revslider-email', '');
$latest_version = get_option('revslider-latest-version', RevSliderGlobals::SLIDER_REVISION);
$activewidgetclass = $validated === 'true'? "rs-status-green-wrap" : "rs-status-red-wrap";
$activewidgetclass = $temp_active === 'true' ? "rs-status-orange-wrap" : $activewidgetclass;
?>
<!-- VALIDATION WIDGET -->
<div class="rs-dash-widget rs-dash-doublewidget">
	
	<div class="rs-dash-title-wrap">
		<div class="rs-dash-title <?php echo (check_for_jquery_addon() ? 'rs-green' : 'rs-red'); ?>"><?php _e("Visual Editor Addon Enabling & Activation",'revslider'); ?></div>
		<div class="rs-dash-more-buttons-wrapper">
			<?php if ($temp_active == 'true') { ?>
                <div class="rs-dash-title-button rs-status-orange"><i class="icon-no-problem-found"></i><?php _e("Editor Temporarily Activated",'revslider'); ?></div>
			<?php } elseif ($validated==='true') { ?>
				<div  class="rs-dash-title-button rs-green"><i class="icon-no-problem-found"></i><?php _e("Editor Activated",'revslider'); ?></div>			
			<?php } else { ?>		
				<div  class="rs-dash-title-button rs-red"><i class="icon-not-registered"></i><?php _e("Editor Not Activated",'revslider'); ?></div>
			<?php } ?>
			<?php if (!check_for_jquery_addon()) { ?>	
				<div  class="rs-dash-title-button rs-red"><i class="icon-not-registered"></i><?php _e("Slider Not Installed",'revslider'); ?></div>
			<?php } else { ?>
				<?php if (is_jquery_addon_temp_activated()){ ?>
					<div  class="rs-dash-title-button rs-status-orange"><i class="icon-no-problem-found"></i><?php _e("Slider Temporarily Activated",'revslider'); ?></div>
				<?php } elseif (is_jquery_addon_activated()) { ?>
					<div  class="rs-dash-title-button rs-green"><i class="icon-no-problem-found"></i><?php _e("Slider Activated",'revslider'); ?></div>
				<?php } else { ?>
					<div  class="rs-dash-title-button rs-red"><i class="icon-not-registered"></i><?php _e("Slider Not Activated",'revslider'); ?></div>
				<?php  } ?> 				
			<?php } ?>			
		</div>
	</div>
	
	<div class="rs-dash-widget-default-view-wrap">
		<div class="rs-dash-widget-inner">
			<?php if (!check_for_jquery_addon()) { ?>
				<div class="rs-dash-icon rs-dash-notregistered"></div>
				<div class="rs-dash-content-with-icon">
					<div class="rs-dash-strong-content"><?php echo get_jquery_addon_message(); ?></div>
					<div><?php _e("This is requires in order to use the Visual Editor Addon", 'revslider'); ?></div>
				</div>
			<?php } else { ?>
				<div class="rs-dash-icon rs-dash-refresh"></div>
				<div class="rs-dash-content-with-icon">
					<div class="rs-dash-strong-content"><?php _e('Visual Editor Addon Installed Correctly', 'revslider'); ?></div>
					<div><?php _e("You are ready to use the addon", 'revslider'); ?></div>
				</div>
			<?php } ?>
			
			<?php if(!RS_DEMO){ ?>
				<div class="rs-dash-bottom-wrapper">
					<div><?php _e("If you don't have a Slider Revolution jQuery Purchase Code:", 'revslider'); ?></div>
					<div class="rs-dash-content-space"></div>
					<span id="show-manual-installation" class="rs-dash-button"><?php _e('Manual Installation','revslider'); ?></span>
                    <?php if ($validated !== 'true') : ?>
                        <a class="rs-dash-button" href="https://themepunch.com/purchase-code-deactivation/" target="_blank"><?php _e('Deregister Domain','revslider'); ?></a>
                    <?php endif; ?>
                    <?php
                    $temp_active = get_option('revslider-temp-active', 'false');
                    if($temp_active == 'true'){
                        ?>
                        <a href="?page=revslider&checktempactivate=true" id="rs-validation-full-activate" class="rs-dash-button"><?php _e('Complete Activation','revslider'); ?></a>
                        <span class="rs-dash-more-info" data-takemeback="false" data-title="<?php echo _e('What does \'Temporary Activated\' mean?', 'revslider');?>" data-content="<?php echo __('The Envato API was unavailable at the activation process:', 'revslider').'<div class=\'rs-dash-content-space\'></div>'.__('The Slider is temporary activated until the Envato API can be reached again by the ThemePunch servers.','revslider').'<div class=\'rs-dash-content-space\'></div>'.__('The plugin will be fully activated as soon as the Envato API is available again.','revslider').''; ?>"><span class="rs-dash-invers-button-gray rs-dash-close-panel"><?php _e('Why?', 'revslider'); ?></span></span>
                        <?php
                    }
                    ?>
				</div>
			<?php } ?>
		</div>


		<div class="rs-dash-widget-inner">
		
			<div class="rs-dash-icon rs-dash-refresh"></div>
			<div class="rs-dash-content-with-icon">
				<div class="rs-dash-strong-content"><?php _e("Slider Revolution jQuery Purchase Code",'revslider'); ?></div>
				<div><?php _e("You can learn how to find your purchase key <a target='_blank' href='http://www.themepunch.com/faq/where-to-find-the-purchase-code/'>here</a>",'revslider'); ?></div>
			</div>
			<div class="rs-dash-content-space"></div>				
			<?php if(!RS_DEMO){ ?>
				<input type="text" value="<?php echo get_option('jquery-plugin-code'); ?>" name="addon_purchase_token" class="rs-dashboard-input" <?php echo is_jquery_addon_activated() ? 'readonly="readonly"' : ''; ?> style="width:<?php echo is_jquery_addon_activated()=='true' ? '322px' : '273px'; ?>;margin-right:10px" />			
				<span style="display:none" id="rs_purchase_validation" class="loader_round"><?php _e('Please Wait...', 'revslider'); ?></span>					
				<?php
				if (is_jquery_addon_activated()){ 
					?>
					<a href="javascript:void(0);" id="deactivate_addon" class="rs-dash-button"><?php _e('Deregister','revslider'); ?></a>			
				<?php }else{ ?>
					<a href="javascript:void(0);" id="download_addon" class="rs-dash-button"><?php _e('Register & Install','revslider'); ?></a>							
				<?php } ?>
			<?php } ?>
			<div class="rs-dash-content-space"></div>	

			<div class="rs-dash-icon rs-dash-buket"></div>
			<div class="rs-dash-content-with-icon">
				<div class="rs-dash-strong-content"><?php _e("Visual Editor Addon Purchase Code",'revslider'); ?></div>
				<div><?php _e("You can learn how to find your purchase key <a target='_blank' href='http://www.themepunch.com/faq/where-to-find-the-purchase-code/'>here</a>",'revslider'); ?></div>
			</div>
			<div class="rs-dash-content-space"></div>				
			<?php if(!RS_DEMO){ ?>
				<input type="text" name="rs-validation-token" class="rs-dashboard-input" value="<?php echo $code; ?>" <?php echo ($validated === 'true') ? ' readonly="readonly"' : ''; ?> style="width: <?php echo ($validated !== 'true') ? '330px' : '322px' ?>;margin-right:10px" />				
				<span style="display:none" id="rs_purchase_validation" class="loader_round"><?php _e('Please Wait...', 'revslider'); ?></span>					
				<a href="javascript:void(0);" <?php echo ($validated !== 'true') ? '' : 'style="display: none;"'; ?> id="rs-validation-activate" class="rs-dash-button"><?php _e('Register','revslider'); ?></a>				
				<a href="javascript:void(0);" <?php echo ($validated === 'true') ? '' : 'style="display: none;"'; ?> id="rs-validation-deactivate" class="rs-dash-button"><?php _e('Deregister','revslider'); ?></a>
			<?php } ?>
		</div>		
	</div>
	
	<?php if(!RS_DEMO){ ?>
		<div class="rs-dash-widget-extra-view-wrap">
			<div class="rs-dash-widget-inner">
				
				<div class="rs-dash-icon rs-dash-light"></div>
				<div class="rs-dash-content-with-icon">
					<div class="rs-dash-strong-content"><?php echo "Manual Installation"; ?></div>

					<div><?php _e('You can find the addon archive file "visual-editor-extension.zip" <br>inside of the "addon-visual-editor" folder of your zip file:<br>', 'revslider');?><a href="//codecanyon.net/item/slider-revolution-responsive-jquery-plugin/2580848" target="_blank"><?php _e("Slider Revolution Responsive jQuery Plugin", 'revslider'); ?></a></div>
				</div>
			</div>


			<div class="rs-dash-widget-inner">
				<form action="<?php echo site_url('c=admin&m=upload_addon', 'revslider') ?>" enctype="multipart/form-data" method="post">
					<div class="rs-dash-icon rs-dash-download"></div>
					<div class="rs-dash-content-with-icon">
						<div class="rs-dash-strong-content"><?php _e("Select the visual-editor-extension.zip file",'revslider'); ?></div>				
						
						<input type="file" style="width: 350px;" value="" name="addon_file" class="input_import_slider">					
					</div>
					<div class="rs-dash-content-space"></div>	
					<div></div>
					
					<div class="rs-dash-bottom-wrapper">
						<input type="submit" class="rs-dash-button rev-import-slider-button" value="<?php _e("Upload",'revslider'); ?>">
						<span id="hide-manual-installation" class="rs-dash-button"><?php _e('Back to Code Registration','revslider'); ?></span>
					</div>
				</form>					
			</div>
		</div>

		<script>
			jQuery(document).ready(function() {			

				jQuery('#show-manual-installation').click(function() {				
					punchgs.TweenLite.to(jQuery('.rs-dash-widget-default-view-wrap'),0.5,{opacity:1,x:-960,ease:punchgs.Power3.easeInOut});
					punchgs.TweenLite.fromTo(jQuery('.rs-dash-widget-extra-view-wrap'),0.5,{display:"block",autoAlpha:0,x:960},{autoAlpha:1,x:0,ease:punchgs.Power3.easeInOut});
				});
				jQuery('#hide-manual-installation').click(function() {				
					punchgs.TweenLite.to(jQuery('.rs-dash-widget-default-view-wrap'),0.5,{autoAlpha:1,x:0,ease:punchgs.Power3.easeInOut});
					punchgs.TweenLite.to(jQuery('.rs-dash-widget-extra-view-wrap'),0.5,{autoAlpha:0,x:960,ease:punchgs.Power3.easeInOut});
				})
			});
		</script>
	<?php } ?>
	
</div><!-- END OF VALIDATION WIDGET -->
