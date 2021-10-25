<div class="title">
	<h1><?php echo __('Recover Password') ?></h1>
</div>

<div class="block">

	<?php if ($error) : ?>
		<p class="error"><?php echo $error; ?></p>
	<?php endif; ?>

	<?php echo form_open(site_url('c=account&m=recover_password_action'), array('id' => 'recover_form')) ; ?>

		<dl>
			<dt>
				<label for="email"><?php echo __('Email address')?></label>
			</dt>
			<dd>
				<?php echo form_input(array('name' => 'email', 'id' => 'email', 'value' => $email, 'class' => 'inputtext right')) ; ?>
			</dd>
		</dl>

		<div class="buttons">
			<input type="submit" class="hidden" />
			<a href="javascript:void(0)" onclick="document.getElementById('recover_form').submit(); return false;" class="button right"><?php echo __('Recover Password') ?></a>
			<a href="<?php echo site_url('c=account&m=login'); ?>" class="link"><?php echo __('Login') ?></a>
		</div>

	</form>

</div>