<div id="navigation">
	<ul id="user">
		<li class="slider-overview"><?php _e('Hello'); ?>, <?php echo $user['username']; ?>!</li>
        <li>
            <i class="fa-icon-file-image-o"></i>
            <a href="?view=sliders"><?php _e('Slider Overview'); ?></a>
        </li>
        <li class="navigation-editor">
            <i class="fa-icon-keyboard-o"></i>
            <a href="?view=navigation-editor"><?php _e('Navigation Editor'); ?></a>
        </li>
        <li class="rev-addon">
            <i class="fa-icon-cubes"></i>
            <a href="?page=rev_addon"><?php _e('Add-Ons'); ?></a>
        </li>
		<?php if(!RS_DEMO){ ?>
            <li class="global-settings">
                <i class="fa-icon-gear"></i>
                <a href="?view=global-settings"><?php _e('Global Settings'); ?></a>
            </li>
            <li class="edit-account">
                <i class="fa-icon-user"></i>
                <a href="#" id="edit_account_link"><?php _e('Edit Account'); ?></a>
            </li>
		<?php } ?>
		<li class="logout">
			<i class="fa-icon-sign-out"></i>
			<a href="<?php echo site_url('c=account&m=logout_action'); ?>"><?php _e('Logout'); ?></a>
		</li>
		<li class="languages">
			<i class="fa-icon-globe"></i>
			<select id="language">
				<?php foreach ($this->config->item('available_languages') as $_code => $_name) : ?>
					<option value="<?php echo $_code; ?>" <?php echo selected(get_language(), $_code); ?>><?php echo $_name; ?></option>
				<?php endforeach; ?>
			</select>
		</li>
	</ul>
	<?php if(!RS_DEMO){ ?>
		<?php if ($export_button) : ?>
            <span id="nav_export_wrapper">
			    <a id="nav_button_export_slider" href="javascript:void(0)" class="button-primary revblue"><?php _e('Export this Slider'); ?><i class="eg-icon-export"></i></a>
                <a id="nav_button_export_html" href="javascript:void(0)" class="button-primary revblue"><?php _e('Export to HTML'); ?><i class="eg-icon-export"></i></a>
            </span>
		<?php endif; ?>
	<?php } ?>
	<?php if(RS_DEMO){ ?>
			<a id="" href="javascript:void(0)" class="button-primary revblue"><?php _e('DEMO MODE ACTIVE'); ?></a>
	<?php } ?>
</div>
<?php do_action('admin_notices'); ?>