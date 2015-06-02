<?php
function users_build_link( $order_by )
{
	return base_url( '/admin/users/?' ) . http_build_query_merge_auto( array( 'order_by' => $order_by ) );
}

function user_build_link_query( $query = array() )
{
	return base_url( '/admin/users/?' ) . http_build_query_merge( $query, TRUE );
}

?>

<div class="admin-content ajax-paging">
	<div class="inner">
		<h1><a class="ajax-link" href="<?=base_url('/admin/users')?>"><?=lang_line_upper('users')?></a></h1>
		
		<a class="ajax-link button square-button" href="<?=base_url('/admin/user_add')?>"><?=lang_line_upper('add_new')?></a>
		
		<div class="posts_filter">
			<div class="status_filter">
				<a class="ajax-link<?=$user_level===NULL?' active':''?>" href="<?=user_build_link_query( array( 'user_level' => NULL ) )?>">
					<?=lang_line_ucwords('all')?> <span>(<?=$stats['users_count']?>)</span>
				</a> | 
				<a class="ajax-link<?=intval($user_level)===CIBLOG_ADMIN_LEVEL?' active':''?>" href="<?=user_build_link_query( array( 'user_level' => CIBLOG_ADMIN_LEVEL ) )?>">
					<?=CiblogHelper::get_user_role_name(CIBLOG_ADMIN_LEVEL)?> <span>(<?=$stats[ strtolower(CiblogHelper::get_user_role_name(CIBLOG_ADMIN_LEVEL)) . '_count']?>)</span>
				</a> | 
				<a class="ajax-link<?=intval($user_level)===CIBLOG_EDITOR_LEVEL?' active':''?>" href="<?=user_build_link_query( array( 'user_level' => CIBLOG_EDITOR_LEVEL ) )?>">
					<?=CiblogHelper::get_user_role_name(CIBLOG_EDITOR_LEVEL)?> <span>(<?=$stats[ strtolower(CiblogHelper::get_user_role_name(CIBLOG_EDITOR_LEVEL)) . '_count']?>)</span>
				</a> | 
				<a class="ajax-link<?=intval($user_level)===CIBLOG_AUTHOR_LEVEL?' active':''?>" href="<?=user_build_link_query( array( 'user_level' => CIBLOG_AUTHOR_LEVEL ) )?>">
					<?=CiblogHelper::get_user_role_name(CIBLOG_AUTHOR_LEVEL)?> <span>(<?=$stats[ strtolower(CiblogHelper::get_user_role_name(CIBLOG_AUTHOR_LEVEL)) . '_count']?>)</span>
				</a> | 
				<a class="ajax-link<?=$user_level!==NULL&&intval($user_level)===CIBLOG_SUSCRIBER_LEVEL?' active':''?>" href="<?=user_build_link_query( array( 'user_level' => CIBLOG_SUSCRIBER_LEVEL ) )?>">
					<?=CiblogHelper::get_user_role_name(CIBLOG_SUSCRIBER_LEVEL)?> <span>(<?=$stats[ strtolower(CiblogHelper::get_user_role_name(CIBLOG_SUSCRIBER_LEVEL)) . '_count']?>)</span>
				</a>
			</div>
			
			<div class="general_filter">
				<form class="ajax-get-form">
				
				<input type="text" name="search_name" value="<?=$search_name?>" />
				
				<input type="submit" value="<?=lang_line_upper('search_users')?>" />
				
				</form>
			</div>
		</div>
		
		<div class="resize_informer">
			<table id="users_list_table" class="default_table expanded_table" align="center" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th class="ajax-el-link" data-href="<?=users_build_link( 'user_name' )?>"><?=lang_line_ucwords('username')?></th>
						<th class="ajax-el-link" data-href="<?=users_build_link( 'user_display_name' )?>"><?=lang_line_ucwords('name')?></th>
						<th class="ajax-el-link" data-href="<?=users_build_link( 'user_email' )?>"><?=lang_line_ucwords('email')?></th>
						<th class="ajax-el-link" data-href="<?=users_build_link( 'user_level' )?>"><?=lang_line_ucwords('role')?></th>
					</tr>
				</thead>
				<tbody>
					<? if ( isset( $users ) ) { foreach ( $users as $user ) { ?>
					<tr id="row_<?=$user['user_id']?>">
						<td class="ajax-el-link" href="<?=base_url('/admin/user_edit/'.$user['user_id'])?>">
							<a class="ajax-link" href="<?=base_url('/admin/user_edit/'.$user['user_id'])?>">
								<?=$user['user_name']?>
							</a>
						</td>
						<td>
							<?=$user['user_display_name']?>
						</td>
						<td>
							<a href="mailto:<?=$user['user_email']?>"><?=$user['user_email']?></a>
						</td>
						<td>
							<?=CiblogHelper::get_user_role_name( $user['user_level'] )?>
						</td>
					</tr>
					<? }} ?>
				</tbody>
			</table>
			
			<?=$pagination?>
		</div>

	</div>
</div> 
