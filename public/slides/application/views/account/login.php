<div class="title">
	<h1><?php echo __('Login') ?></h1>
</div>

<div class="block">

	<?php if ($error) : ?>
		<p class="error"><?php echo $error; ?></p>
	<?php endif; ?>

	<?php echo form_open(site_url('c=account&m=login_action'), array('id' => 'login_form')) ; ?>

		<dl>
			<dt>
				<label for="username"><?php echo __('Username')?></label>
			</dt>
			<dd>
				<?php echo form_input(array('name' => 'username', 'id' => 'username', 'value' => $username, 'class' => 'inputtext right', 'autofocus' => 'autofocus')) ; ?>
			</dd>
		</dl>

		<dl>
			<dt>
				<label for="password"><?php echo __('Password')?></label>
			</dt>
			<dd>
				<?php echo form_password(array('name' => 'password', 'id' => 'password', 'class' => 'inputtext right')) ; ?>
			</dd>
		</dl>
		<div class="buttons">
			<input type="submit" class="hidden" />
			<a href="javascript:void(0)" onclick="document.getElementById('login_form').submit(); return false;" class="button right"><?php echo __('Login') ?></a>
		<?php if(!RS_DEMO){ ?>
			<a href="<?php echo site_url('c=account&m=recover_password'); ?>" class="link"><?php echo __('Forgot password?') ?></a>
		<?php } ?>
		</div>
		<?php if(RS_DEMO){ ?>
		<p style="text-align: center"><?php echo __('Demo Username: demo'); ?></p>
		<p style="text-align: center"><?php echo __('Demo Password: demo'); ?></p>
		<?php } ?>
	</form>

</div>
<?php if(RS_DEMO){ ?>
	<div style="width: 620px;margin: 50px 0px 0px -110px;" class="required_plugins">
	<div style="margin-bottom: 20px"><img src="http://sharedimages.themepunch.tools/server_req.png" alt="required programs" /></div>
	
	<a href="http://codecanyon.net/item/slider-revolution-responsive-jquery-plugin/2580848?ref=themepunch&license=regular&open_purchase_for_item_id=2580848&purchasable=source" style="margin-right:10px"><img src="http://sharedimages.themepunch.tools/buy_jqueryslider.png"></a>
	<a href="http://codecanyon.net/item/slider-revolution-jquery-visual-editor-addon/13934907?ref=themepunch&license=regular&open_purchase_for_item_id=13934907&purchasable=source" style="margin-left:10px"><img src="http://sharedimages.themepunch.tools/buy_visualeditor.png"></a>
	</div>
<?php } ?>