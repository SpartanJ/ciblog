<?
function print_post($p)
{?>
	<li>
		<a class="ajax-link" href="<?=base_url('/admin/edit/'.$p['post_id'])?>"><?=$p['title']?></a>
		<em><?=$p["category"]?></em>
		<span>
			<a target="_blank" href="<?=base_url('/blog/'.$p['slug'])?>">VIEW</a>
			<a onclick="return confirm('¿Are you sure you wan't to delete the article?');" href="<?=base_url('/admin/delete/'.$p['post_id'])?>">X</a>
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
		<h1>DRAFT</h1>
		
		<a class="ajax-link button square-button" href="<?=base_url('/admin/add')?>">NEW</a>
		
		<ul>
		<?foreach($drafts as $p){
			print_post($p);
		}?>
		</ul>
	</div>

	<div id="published">
		<h1>PUBLISHED</h1>
		
		<a class="ajax-link button square-button" href="<?=base_url('/blog')?>">BLOG</a>
		
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
