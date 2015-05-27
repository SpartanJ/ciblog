<? if ( !isset( $only_admin_bar ) ) { ?>
<form class="ajax" method="post" action="<?=base_url('/admin/save')?>">


<div class="admin-editor">
		<textarea name="title" class="title" placeholder="Título"><?=isset($title)?$title:''?></textarea>
		<textarea name="body" class="body autoresize uploadarea" placeholder="Escribir algo"><?=isset($body)?htmlspecialchars($body):''?></textarea>
</div>

<div id="save-success-msg">Guardado</div>

<div id="admin-bar">
	<? } ?>
<?
	$selected='selected="selected"';
	$draft_checked='checked="checked"';

	if(isset($draft) && $draft == '0')
	{
			$draft_checked='';
	}
?>
	<?if(isset($post_id)){?>
		<input type="hidden" name="post_id" value="<?=$post_id?>"/>
	<?}?>
	<div class="container">
		<div class="left">
			<a class="ajax-link" href="<?=base_url('/admin')?>">« Admin</a>
			<?if(isset($post_id)){?>
				<a onclick="return confirm('¿Seguro que desea borrar el artículo?');" href="<?=base_url('/admin/delete/'.$post_id)?>">Borrar</a></span>
			<?}?>
		</div>
		<div class="right">
				<span>
					<label for="category">Categoría</label>
					<select id="category" name="category">
						<option <?=isset($category) && $category == 'BLOG' ?$selected:''?> value="BLOG">Blog</option>
						<option <?=isset($category) && $category == 'PORTFOLIO' ?$selected:''?> value="PORTFOLIO">Portfolio</option>
						<option <?=isset($category) && $category == 'START' ?$selected:''?> value="START">Inicio</option>
						<option <?=isset($category) && $category == 'STANDALONE' ?$selected:''?> value="STANDALONE">Standalone</option>
					</select>
				</span>
				<span>
					<label for="draft">Borrador</label> <input  <?=$draft_checked?>  id="draft" name="draft" type="checkbox"/>
				</span>
			<?if(isset($post_id)){?>
				<a target="_blank" href="<?=base_url('/blog/'.$slug)?>">Preview</a>
			<?}?>
			
			<input type="submit" value="Guardar"></input>
		</div>
	</div>
	<? if ( !isset( $only_admin_bar ) ) { ?>
</div>

</form>
<? } ?>

<script>
	$(function()
	{
		editor_init();
	});
</script>
