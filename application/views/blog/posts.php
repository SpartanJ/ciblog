<?
if(isset($posts)&&count($posts)>1)
{
	$total = 0;
	$len_arr = array();

	foreach($posts as $r)
	{
		$len = strlen($r['post_body']);;
		$total += $len;
		$len_arr[$r['post_id']] = $len;
	}
?>
<div class="ajax-paging">

<ul id="minimap">
	<?foreach($posts as $r) {
		$height = round( ($len_arr[$r['post_id']]/$total)*100 );
	?>
	<li style="height:<?=$height?>%;">
		<a href="#<?=$r['post_slug']?>"><?=$r['post_title']?><span class="mark"></span></a>
	</li>
	<?}?>

</ul>

<?
}

$i=0;
if(isset($posts)){foreach($posts as $r){
?>
	<div class="blog_post <?=count($posts)==1?'blog_post_lonely':''?>" id="<?=$r['post_slug']?>">
		<h1><a name="<?=$r['post_slug']?>" href="<?=base_url('blog/'.$r['post_slug'])?>"><?=$r['post_title']?></a></h1>
		
		<?if($display_info){?>
		<div class="date"><?=CiblogHelper::to_blog_date($r['post_created'])?> <?=lang_line('by')?> <a class="ajax-link" href="<?=base_url('blog?author='.$r['post_author'])?>"><?=$r['user_display_name']?></a></div>
		<?}?>

		<div class="markdown"><?=$r['post_body']?></div>
	</div>
<?
	$i++;
}}?>

<?=isset($pagination)?$pagination:''?>

<script>
	$(function()
	{
		highlight_init();
		
		minimap_init();
	});
</script>

</div>