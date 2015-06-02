 <?php
 if ( isset( $user ) ) { $GLOBALS['cur_user'] = $user; } 
 function ud( $field ) { 
	if ( isset( $GLOBALS['cur_user'] ) )
	{
		$user = $GLOBALS['cur_user']; 
		return ( isset( $user[$field] ) ) ? $user[$field] : '';
	}
	return '';
}?>

<div class="admin-content ajax-paging">
	<div class="inner inner-form">
		<form class="ajax" method="post" action="<?=base_url('/admin/user_update')?>">
		
		<h1><?=isset($user_id)?lang_line_upper('user_edit'):lang_line_upper('user_add')?></h1>
		
		<h3><?=lang_line_ucwords('name')?></h3>
		
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="user_name"><?=lang_line_ucwords('username')?></label></th>
					<td><input type="text" name="user_name" id="user_name" value="<?=ud('user_name')?>" <?=isset($user_id)?'disabled="disabled"':''?> /> 
						<? if ( isset($user_id) ) { ?>
						<span class="description"><?=lang_line('usernames_cannot_change')?></span>
						<? } ?>
					</td>
				</tr>
				<tr>
					<th><label for="firstname"><?=lang_line_ucwords('first_name')?></label></th>
					<td><input type="text" name="firstname" id="firstname" value="<?=ud('user_firstname')?>" /></td>
				</tr>
				<tr>
					<th><label for="lastname"><?=lang_line_ucwords('last_name')?></label></th>
					<td><input type="text" name="lastname" id="lastname" value="<?=ud('user_lastname')?>" /></td>
				</tr>
				<tr>
					<th><label for="nickname"><?=lang_line_ucwords('nickname')?> <span class="description">(<?=lang_line('required')?>)</span></label></th>
					<td><input type="text" name="nickname" id="nickname" value="<?=ud('user_nickname')?>" /></td>
				</tr>
				<? if ( isset( $user_id ) ) { ?>
				<tr>
					<th><label for="display_name"><?=lang_line('display_name_as')?></label></th>
					<td>
						<select name="display_name" id="display_name">
						<?	$dn = CiblogHelper::get_display_names( $user );
							foreach ( $dn as $name ) {
						?>
							<option value="<?=$name?>"<?=isset($user_display_name)&&$user_display_name==$name?'selected="selected"':''?>><?=$name?></option>
						<? } ?>
						</select>
					</td>
				</tr>
				<? } ?>
			</tbody>
		</table>
		
		<h3><?=lang_line_ucwords('contact_info')?></h3>
		
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="user_email"><?=lang_line_ucwords('email')?> <span class="description">(<?=lang_line('required')?>)</span></label></th>
					<td><input type="text" name="email" id="user_email" value="<?=ud('user_email')?>" /></td>
				</tr>
				<tr>
					<th><label for="user_url"><?=lang_line_ucwords('website')?></label></th>
					<td><input type="text" name="url" id="user_url" value="<?=ud('user_url')?>" /></td>
				</tr>
			</tbody>
		</table>
		
		<h3><?=lang_line_ucwords('about_yourself')?></h3>
		
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="user_bio"><?=lang_line_ucwords('biographical_info')?></label></th>
					<td>
						<textarea name="bio" id="user_bio" rows="5" cols="30"></textarea>
						<p class="description"><?=lang_line('biographical_info_desc')?></p>
					</td>
				</tr>
				<tr>
					<th><label for="user_password"><?=lang_line_ucwords('new_password')?></label></th>
					<td>
						<input type="password" name="password" id="user_password" />
						<p class="description"><?=lang_line('new_password_desc')?></p>
					</td>
				</tr>
				<tr>
					<th><label for="new_password_repeat"><?=lang_line_ucwords('new_password_repeat')?></label></th>
					<td>
						<input type="password" name="password_repeat" id="new_password_repeat" />
						<p class="description"><?=lang_line('new_password_repeat_desc')?></p>
					</td>
				</tr>
			</tbody>
		</table>
		
		<p class="submit">
		<? if ( isset( $user_id ) ) { ?>
			<input type="hidden" name="id" id="user_id" value="<?=$user_id?>" />
			<input type="hidden" name="username" id="username" value="<?=$user_name?>" />
			
			<input type="submit" name="submit" class="button button-primary" value="<?=lang_line_ucwords('update_profile')?>">
		<? } else { ?>
			<input type="submit" name="submit" class="button button-primary" value="<?=lang_line_ucwords('add_new_user')?>">
		<? } ?>
		</p>
		
		</form>
	</div>
</div> 
