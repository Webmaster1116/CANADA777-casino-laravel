<div id="edit_account_dialog" data-title="<?php _e('Edit Account'); ?>">
	<form name="form_edit_account" id="form_edit_account">
		<input type="hidden" id="user_id" name="user_id" value="<?php echo $user['id']; ?>" />
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<?php _e('Username:'); ?>
				</th>
				<td>
					<input id="username" name="username" type="text" class="regular-text" value="<?php echo $user['username']; ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Email:'); ?>
				</th>
				<td>
					<input id="email" name="email" type="email" class="regular-text" value="<?php echo $user['email']; ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Change password:'); ?>
				</th>
				<td>
					<input id="password" name="password" type="password" class="regular-text" value="" autocomplete="off" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Confirm new password:'); ?>
				</th>
				<td>
					<input id="confirm_password" name="confirm_password" type="password" class="regular-text" value="" autocomplete="off" />
				</td>
			</tr>
		</table>
	</form>
	<div class="alignright">
		<a id="button_save_account" class="button-primary revblue"><?php _e("Update"); ?></a>
	</div>
</div>