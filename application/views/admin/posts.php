<?php
$sel = ' selected="selected"';
function posts_build_link( $query = array() )
{
	return base_url( '/admin/posts/?' ) . http_build_query_merge( $query, TRUE );
}

function print_post($p,$user)
{
	include('post.php');
}?>

<div class="admin-content ajax-paging">
	<div class="inner">
		<h1><a class="ajax-link" href="<?=base_url('/admin/posts')?>"><?=lang_line_upper('posts')?></a></h1>
		
		<a class="ajax-link button square-button" href="<?=base_url('/admin/add')?>"><?=lang_line_upper('add_new')?></a>
		
<!--	<a class="button square-button" target="_blank" href="<?=base_url('/blog')?>"><?=lang_line_upper('blog')?></a> -->
		
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
			
			<div class="general_filter">
				<form class="ajax-get-form">
				
				<select name="cat_id">
					<option value=""><?=lang_line_ucwords('filter_categories')?></option>
					<? if(isset($categories)){foreach($categories as $cat) {?>
						<option value="<?=$cat['cat_id']?>"<?=$cat_id==$cat['cat_id']?$sel:''?>><?=lang_line_ucwords($cat['cat_name'])?></option>
					<? }} ?>
				</select>
				
				<input type="text" name="post_title" placeholder="<?=lang_line_ucwords('search_title')?>" value="<?=$post_title?>" />
				
				<input type="submit" value="<?=lang_line_upper('filter')?>" />
				
				</form>
			</div>
		</div>
		
		<ul>
		<?if(isset($posts)){foreach($posts as $p){
			print_post($p,$user);
		}}?>
		</ul>
		
		<?=$pagination?>
	</div>
</div>