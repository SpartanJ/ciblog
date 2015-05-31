<?php
$sel = 'selected="selected"';
function posts_build_link( $query = array() )
{
	return base_url( '/admin/posts/?' ) . http_build_query_merge( $query, TRUE );
}

function print_post($p)
{?>
	<li id="post_<?=$p['post_id']?>" class="<?=$p['post_draft']==1?'draft':'published'?>">
		<a class="ajax-link" href="<?=base_url('/admin/edit/'.$p['post_id'])?>"><?=$p['post_title']?></a>
		<em class="date"><?=CiblogHelper::to_blog_date($p["post_created"])?> <?=lang_line('by')?> 
			<a class="ajax-link" href="<?=base_url('/admin/posts?user_id='.$p['user_id'])?>"><?=( ( NULL != $p['user_display_name'] ) ? $p['user_display_name'] : $p['user_name'] )?></a>
		</em>
		<em>
			<a class="ajax-link" href="<?=base_url('/admin/posts/?cat_id='.$p['cat_id'])?>"><?=lang_line_category_name_upper($p['cat_name'])?></a>
		</em>
		<span>
			<a target="_blank" href="<?=base_url('/blog/'.$p['post_slug'])?>"><?=lang_line_upper('view')?></a>
			
			<? if ( 1==$p['post_draft'] ) { ?>
			<a class="ajax-eval-fancy-confirm-link" data-text="<?=lang_line('admin_confirm_publish_article')?>" href="<?=base_url('/admin/publish_it/'.$p['post_id'])?>"><?=lang_line_upper('publish')?></a>
			<? } else { ?>
			<a class="ajax-eval-fancy-confirm-link" data-text="<?=lang_line('admin_confirm_draft_article')?>" href="<?=base_url('/admin/draft_it/'.$p['post_id'])?>"><?=lang_line_upper('draft')?></a>
			<? } ?>
			
			<a class="ajax-eval-fancy-confirm-link" data-text="<?=lang_line('admin_confirm_delete_article')?>" href="<?=base_url('/admin/delete/'.$p['post_id'])?>">X</a>
		</span>
	</li>
<?}?>

<div class="admin-posts ajax-paging">
	<div class="logout">
		<a class="ajax-link" href="<?=base_url('/admin/logout')?>">
			<i class="fa fa-sign-out"></i>
		</a>
	</div>

	<div class="posts">
		<h1><a class="ajax-link" href="<?=base_url('/admin/posts')?>"><?=lang_line_upper('posts')?></a></h1>
		
		<a class="ajax-link button square-button" href="<?=base_url('/admin/add')?>"><?=lang_line_upper('new')?></a>
		
		<a class="button square-button" target="_blank" href="<?=base_url('/blog')?>"><?=lang_line_upper('blog')?></a>
		
		<div class="posts_filter">
			<div class="status_filter">
				<a class="ajax-link<?=$post_draft===NULL?' active':''?>" href="<?=posts_build_link( array( 'post_draft' => NULL ) )?>">
					<?=lang_line_ucwords('all')?> <span>(<?=$stats['posts_count']?>)</span>
				</a> | 
				<a class="ajax-link<?=$post_draft==='0'?' active':''?>" href="<?=posts_build_link( array( 'post_draft' => 0 ) )?>">
					<?=lang_line_ucwords('published')?> <span>(<?=$stats['posts_published']?>)</span>
				</a> | 
				<a class="ajax-link<?=$post_draft==='1'?' active':''?>" href="<?=posts_build_link( array( 'post_draft' => 1 ) )?>">
					<?=lang_line_ucwords('draft')?> <span>(<?=$stats['posts_draft']?>)</span>
				</a>
			</div>
		</div>
		
		<ul>
		<?if(isset($posts)){foreach($posts as $p){
			print_post($p);
		}}?>
		</ul>
		
		<?=$pagination?>
	</div>
</div>
