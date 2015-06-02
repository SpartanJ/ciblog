 <?php $GLOBALS['cur_user'] = $user; function ud( $field ) { $user = $GLOBALS['cur_user']; return ( isset( $user[$field] ) ) ? $user[$field] : ''; }?>

<div class="admin-content ajax-paging">
	<div class="inner">
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
				<tr>
					<th><label for="display_name"><?=lang_line('display_name_as')?></label></th>
					<td>
						<select name="display_name" id="display_name">
						<?	$dn = CiblogHelper::get_display_names( $user );
							foreach ( $dn as $name ) {
						?>
							<option value="<?=$name?>"<?=$user_display_name==$name?'selected="selected"':''?>><?=$name?></option>
						<? } ?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		
		<h3><?=lang_line_ucwords('contact_info')?></h3>
		
		
	</div>
</div> 
