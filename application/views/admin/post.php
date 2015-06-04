	<li id="post_<?=$p['post_id']?>" class="<?=$p['post_draft']==1?'draft':'published'?>">
		<? if ( $user->user_level >= CIBLOG_EDITOR_LEVEL || ( $user->user_level >= CIBLOG_AUTHOR_LEVEL && $p['user_id'] == $user->user_id ) ) { ?>
		<a class="ajax-link" href="<?=base_url('/admin/edit/'.$p['post_id'])?>"><?=$p['post_title']?></a>
		<? } else { ?>
		<a><?=$p['post_title']?></a>
		<? } ?>
		
		<em class="date">
			<?=CiblogHelper::to_blog_date($p["post_created"])?> <?=lang_line('by')?><a class="ajax-link" href="<?=base_url('/admin/posts?user_id='.$p['user_id'])?>"><?=$p['user_display_name']?></a>
		</em>
		<em>
			<a class="ajax-link" href="<?=base_url('/admin/posts/?cat_id='.$p['cat_id'])?>"><?=lang_line_category_name_upper($p['cat_name'])?></a>
		</em>
		<span>
			<a target="_blank" href="<?=base_url('/blog/'.$p['post_slug'])?>"><?=lang_line_upper('view')?></a>
			
		<? if ( $user->user_level >= CIBLOG_EDITOR_LEVEL || ( $user->user_level >= CIBLOG_AUTHOR_LEVEL && $p['user_id'] == $user->user_id ) ) { ?>
			<? if ( 1==$p['post_draft'] ) { ?>
			<a class="ajax-eval-fancy-confirm-link" data-text="<?=lang_line('admin_confirm_publish_article')?>" href="<?=base_url('/admin/publish_it/'.$p['post_id'])?>"><?=lang_line_upper('publish')?></a>
			<? } else { ?>
			<a class="ajax-eval-fancy-confirm-link" data-text="<?=lang_line('admin_confirm_draft_article')?>" href="<?=base_url('/admin/draft_it/'.$p['post_id'])?>"><?=lang_line_upper('draft')?></a>
			<? } ?>
		<? } ?>
			
			<a class="ajax-eval-fancy-confirm-link" data-text="<?=lang_line('admin_confirm_delete_article')?>" href="<?=base_url('/admin/delete/'.$p['post_id'])?>">X</a>
		</span>
	</li> 
