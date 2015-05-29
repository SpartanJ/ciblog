<?
function print_post($p)
{?>
	<li>
		<a class="ajax-link" href="<?=base_url('/admin/edit/'.$p['post_id'])?>"><?=$p['post_title']?></a>
		<em><?=lang_line_category_name_upper($p['cat_name'])?></em>
		<em class="date"><?=CiblogHelper::to_blog_date($p["post_timestamp"])?></em>
		<span>
			<a target="_blank" href="<?=base_url('/blog/'.$p['post_slug'])?>"><?=lang_line_upper('view')?></a>
			<a class="ajax-fancy-confirm-link" data-text="<?=lang_line('admin_confirm_delete_article')?>" href="<?=base_url('/admin/delete/'.$p['post_id'])?>">X</a>
		</span>
	</li>
<?}?>

<div id="admin-posts">
	<div id="logout">
		<a class="ajax-link" href="<?=base_url('/admin/logout')?>">
			<i class="fa fa-sign-out"></i>
		</a>
	</div>

	<div id="drafts">
		<h1><?=lang_line_upper('draft')?></h1>
		
		<a class="ajax-link button square-button" href="<?=base_url('/admin/add')?>"><?=lang_line_upper('new')?></a>
		
		<ul>
		<?foreach($drafts as $p){
			print_post($p);
		}?>
		</ul>
	</div>

	<div id="published">
		<h1><?=lang_line_upper('published')?></h1>
		
		<a class="ajax-link button square-button" href="<?=base_url('/blog')?>"><?=lang_line_upper('blog')?></a>
		
		<ul>
		<?foreach($published as $p){
			print_post($p);
		}?>
		</ul>
	</div>
</div>
