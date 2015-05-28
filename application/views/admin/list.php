<?
function print_post($p)
{?>
	<li>
		<a class="ajax-link" href="<?=base_url('/admin/edit/'.$p['post_id'])?>"><?=$p['title']?></a>
		<em><?=$p["category"]?></em>
		<em class="date"><?=to_blog_date($p["timestamp"])?></em>
		<span>
			<a target="_blank" href="<?=base_url('/blog/'.$p['slug'])?>"><?=lang_line_upper('view')?></a>
			<a onclick="return confirm('<?=lang_line('admin_confirm_delete_article')?>');" href="<?=base_url('/admin/delete/'.$p['post_id'])?>">X</a>
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

<script>
	$(function()
	{
		$('#admin-posts li').mouseenter(function()
		{
			$('span',this).show();
		});

		$('#admin-posts li').mouseleave(function()
		{
			$('span',this).hide();
		});
	});
</script>
