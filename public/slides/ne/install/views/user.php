<div class="title">
	<h1><?php echo __('Admin User Account') ?></h1>
</div>
<div class="nav">
	<ul>
		<li><a class="done system" href="?step=checkconfig&lang=<?php echo $lang ?>"><?php echo __('System Check') ?></a></li>
		<li><a class="done database" href="?step=database&lang=<?php echo $lang ?>"><?php echo __('Database') ?></a></li>
		<li><a class="active admin" href="?step=settings&lang=<?php echo $lang ?>"><?php echo __('Admin User') ?></a></a></li>
	</ul>
</div>

<div class="block">
	<div class="icon admin"><i></i></div>
	<div class="content">

		<p><?php echo __('This will be default admin user for admin panel.') ?></p>

		<!-- User message -->
		<?php if(isset($message)) :?>

			<p class="<?php echo $message_type ?>"><?php echo $message ?></p>

		<?php endif ;?>


		<?php echo form_open('step=user&lang=' . $lang, array('id' => 'user_form')) ; ?>

			<?php echo form_hidden('action', 'save') ; ?>

			<!-- User login -->
			<dl>
				<dt>
					<label for="username"><?php echo __('Username')?></label>
				</dt>
				<dd>
					<?php echo form_input(array('name' => 'username', 'id' => 'username', 'value' => $username, 'class' => 'inputtext')) ; ?>
				</dd>
			</dl>

			<dl>
				<dt>
					<label for="email"><?php echo __('Email')?></label>
				</dt>
				<dd>
					<?php echo form_input(array('name' => 'email', 'id' => 'email', 'value' => $email, 'class' => 'inputtext')) ; ?>
				</dd>
			</dl>

			<dl>
				<dt>
					<label for="password"><?php echo __('Password')?></label>
				</dt>
				<dd>
					<?php echo form_password(array('name' => 'password', 'id' => 'password', 'class' => 'inputtext')) ; ?>
				</dd>
			</dl>

			<dl>
				<dt>
					<label for="password2"><?php echo __('Confirm password')?></label>
				</dt>
				<dd>
					<?php echo form_password(array('name' => 'password2', 'id' => 'password2', 'class' => 'inputtext')) ; ?>
				</dd>
			</dl>

			<div class="buttons">
				<input type="submit" style="display:none" />
				<a href="javascript:void(0)" onclick="document.getElementById('user_form').submit(); return false;" class="button yes right"><?php echo __('Create Admin & Finish') ?></a>
			</div>
		</form>

	</div>
</div>