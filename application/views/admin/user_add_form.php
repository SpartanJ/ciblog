
<div class="admin-content ajax-paging">
	<div class="inner inner-form">
		<form class="ajax" method="post" action="<?=base_url('/admin/user_insert')?>">
		
		<h1><?=isset($user_id)?lang_line_upper('user_edit'):lang_line_upper('user_add')?></h1>
		
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="name"><?=lang_line_ucwords('username')?> <span class="description">(<?=lang_line('required')?>)</span></label></th>
					<td><input type="text" name="name" id="name" /> </td>
				</tr>
				<tr>
					<th><label for="email"><?=lang_line_ucwords('email')?> <span class="description">(<?=lang_line('required')?>)</span></label></th>
					<td><input type="text" name="email" id="email" /></td>
				</tr>
				<tr>
					<th><label for="firstname"><?=lang_line_ucwords('first_name')?></label></th>
					<td><input type="text" name="firstname" id="firstname" /></td>
				</tr>
				<tr>
					<th><label for="lastname"><?=lang_line_ucwords('last_name')?></label></th>
					<td><input type="text" name="lastname" id="lastname" /></td>
				</tr>
				<tr>
					<th><label for="url"><?=lang_line_ucwords('website')?></label></th>
					<td><input type="text" name="url" id="url" /></td>
				</tr>
				<tr>
					<th><label for="user_password"><?=lang_line_ucwords('new_password')?> <span class="description">(<?=lang_line('required')?>)</span></label></th>
					<td><input type="password" name="password" id="user_password" /></td>
				</tr>
				<tr>
					<th><label for="password_repeat"><?=lang_line_ucwords('new_password_repeat')?> <span class="description">(<?=lang_line('required')?>)</span></label></th>
					<td><input type="password" name="password_repeat" id="password_repeat" /></td>
				</tr>
				<tr>
					<th><label for="role"><?=lang_line_ucwords('role')?></label></th>
					<td>
						<select name="level" id="level">
						<? if ( isset( $roles ) ) { foreach( $roles as $role=>$level ) { ?>
							<option value="<?=$level?>"><?=lang_line_ucwords($role)?></option>
						<? } } ?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		
		<p class="submit">
			<input type="submit" name="submit" class="button button-primary" value="<?=lang_line_ucwords('add_new_user')?>">
		</p>
		
		</form>
	</div>
</div>
