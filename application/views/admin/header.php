
<div id="header" class="admin-bar bar-top">
	<div class="menu">
		<ul	class="links">
			<li><a class="ajax-link" href="<?=base_url('/admin/posts')?>"><?=lang_line('posts')?></a></li>
			
			<? if ( isset( $user ) && $user->user_level >= CIBLOG_ADMIN_LEVEL ) { ?>
			<li><a class="ajax-link" href="<?=base_url('/admin/categories')?>"><?=lang_line('categories')?></a></li>
			<li><a class="ajax-link" href="<?=base_url('/admin/users')?>"><?=lang_line('users')?></a></li>
			<? } ?>
			
			<li class="mobile-logout"><a class="ajax-link" href="<?=base_url('/admin/logout')?>"><i class="fa fa-sign-out"></i></a></li>
		</ul>
	</div>
	
	<? if ( isset( $user ) ) { ?>
	<div class="user">
		<img src="<?=CiblogHelper::get_gravatar( $user->user_email, 32 )?>" />
		
		<div class="menu">
			<div class="avatar">
				<a class="ajax-link" href="<?=base_url('/admin/profile')?>"><img src="<?=CiblogHelper::get_gravatar( $user->user_email, 64 )?>" /></a>
			</div>
			<div class="options">
				<ul>
					<li><a class="ajax-link" href="<?=base_url('/admin/profile')?>"><?=$user->user_name?></a></li>
					
					<li><a class="ajax-link" href="<?=base_url('/admin/profile')?>"><?=lang_line_ucwords('edit_my_profile')?></a></li>
					
					<li><a class="ajax-link" href="<?=base_url('/admin/logout')?>"><?=lang_line_ucwords('log_out')?></a></li>
				</ul>
			</div>
		</div>
	</div>
	<? } ?>
</div>
