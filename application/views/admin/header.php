
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
	
	<div class="logout">
		<a class="ajax-link" href="<?=base_url('/admin/logout')?>"><i class="fa fa-sign-out"></i></a>
	</div>
</div>
