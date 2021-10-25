<div class="title">
	<h1><?php echo __('Database Settings') ?></h1>
</div>
<div class="nav">
	<ul>
		<li><a class="done system" href="?step=checkconfig&lang=<?php echo $lang ?>"><?php echo __('System Check') ?></a></li>
		<li><a class="active database" href="?step=database&lang=<?php echo $lang ?>"><?php echo __('Database') ?></a></li>
		<li><span class="inactive admin"><?php echo __('Admin User') ?></span></li>
	</ul>
</div>

<div class="block">
	<div class="icon database"><i></i></div>
	<div class="content">

		<?php if(isset($message)) :?>
			<p class="<?php echo $message_type ?>"><?php echo $message ?></p>
		<?php endif ;?>

		<p><?php  echo __('Please fill your database settings.') ?></p>

		<?php echo form_open('step=database&lang=' . $lang, array('id' => 'db_form')) ; ?>

			<?php echo form_hidden('action', 'save') ; ?>
			<?php echo form_hidden('db_driver', function_exists('mysqli_connect') ? 'mysqli' : 'mysql') ; ?>

			<dl>
				<dt>
					<label for="db_hostname"><?php echo __('Hostname')?></label>
				</dt>
				<dd>
					<?php echo form_input(array('name' => 'db_hostname', 'id' => 'db_hostname', 'value' => $db_hostname != '' ? $db_hostname : 'localhost', 'class' => 'inputtext')) ; ?>
				</dd>
			</dl>

			<dl>
				<dt>
					<label for="db_name"><?php echo __('Database')?></label>
				</dt>
				<dd>
					<?php echo form_input(array('name' => 'db_name', 'id' => 'db_name', 'value' => $db_name, 'class' => 'inputtext')) ; ?>
				</dd>
			</dl>

			<dl>
				<dt>
					<label for="db_username"><?php echo __('User')?></label>
				</dt>
				<dd>
					<?php echo form_input(array('name' => 'db_username', 'id' => 'db_username', 'value' => $db_username, 'class' => 'inputtext')) ; ?>
				</dd>
			</dl>

			<dl>
				<dt>
					<label for="db_password"><?php echo __('Password')?></label>
				</dt>
				<dd>
					<?php echo form_input(array('name' => 'db_password', 'id' => 'db_password', 'value' => '', 'class' => 'inputtext')) ; ?>
				</dd>
			</dl>

			<dl>
				<dt>
					<label for="db_prefix"><?php echo __('Prefix')?></label>
				</dt>
				<dd>
					<?php echo form_input(array('name' => 'db_prefix', 'id' => 'db_prefix', 'value' => $db_prefix != '' ? $db_prefix : 'revslider_', 'class' => 'inputtext')) ; ?>
				</dd>
			</dl>

			<div class="buttons">
				<a href="javascript:void(0)" onclick="document.getElementById('db_form').submit(); return false;" class="button yes right"><?php echo __('Save & Go to Next step') ?></a>
			</div>

		</form>

	</div>
</div>