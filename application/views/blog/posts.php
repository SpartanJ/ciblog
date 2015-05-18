<?
if(count($posts)>1)
{

	$total = 0;
	$len_arr = array();

	foreach($posts as $r){
		$len = strlen($r['body']);;
		$total += $len;
		$len_arr[$r['post_id']] = $len;
	}


?>

<ul id="minimap">
	<?foreach($posts as $r){
		$height = round( ($len_arr[$r['post_id']]/$total)*100 );

	?>
	<li style="height:<?=$height?>%;">
		<a href="#<?=$r['slug']?>"><?=$r['title']?><span class="mark"></span></a>
	</li>
	<?}?>

</ul>

<?
	}
?>

<?
	$i=0;
	foreach($posts as $r)
	{
?>

<div class="blog_post" id="<?=$r['slug']?>">
	<?if($i==0 && $show_date){?>
	<div class="date">PUBLICADO el <?=toBlogDate($r['timestamp'])?></div>
	<?}?>

		<h1>
			<a name="<?=$r['slug']?>" href="<?=base_url('blog/'.$r['slug'])?>">
			<?=$r['title']?>
			</a>
		</h1>
	<div class="markdown"><?=$r['body']?></div>
</div>

<?
		$i++;
}?>



<script>
	$( function(){
		highlight_init();
		minimap_init();
	});
</script>
