<div class="admin-content">

<? if ( !isset( $only_admin_bar ) ) { ?>
<form class="ajax" method="post" action="<?=base_url('/admin/save')?>">

<div class="admin-editor">
	<input type="text" name="title" class="title" placeholder="<?=lang_line_ucwords('title')?>" value="<?=isset($post_title)?$post_title:''?>">
	<textarea name="body" class="body" placeholder="<?=lang_line('write_something')?>"><?=isset($post_body)?htmlspecialchars($post_body):''?></textarea>
</div>

<div id="save-success-msg"><?=lang_line_ucwords('saved')?></div>

<div class="admin-bar bar-edit">
<? } ?>
<?
	$selected='selected="selected"';
	$draft_checked='checked="checked"';

	if(isset($post_draft) && $post_draft == '0')
	{
			$draft_checked='';
	}
?>
	<?if(isset($post_id)){?>
		<input type="hidden" name="post_id" value="<?=$post_id?>"/>
	<?}?>
	<div class="container">
		<div class="left">
			<a class="ajax-link" href="<?=base_url('/admin')?>">« <?=lang_line_ucwords('admin')?></a>
			<?if(isset($post_id)){?>
				<a class="ajax-fancy-confirm-link" data-text="<?=lang_line('admin_confirm_delete_article')?>" href="<?=base_url('/admin/delete/'.$post_id)?>">
					<?=lang_line_ucwords('delete')?>
				</a>
			<?}?>
		</div>
		<div class="right">
				<span>
					<label for="category"><?=lang_line_ucwords('category')?></label>
					<select id="category" name="category">
					<?foreach ( $categories as $cat ){ ?>
						<option <?=isset($post_category) && $post_category == $cat['cat_id']?$selected:''?> value="<?=$cat['cat_id']?>"><?=lang_line_category_name_ucwords($cat['cat_name'])?></option>
					<?}?>
					</select>
				</span>
				<span class="draft_check">
					<input <?=$draft_checked?> id="draft" name="draft" type="checkbox"/>
					<label for="draft"><?=lang_line_ucwords('draft')?></label>
				</span>
			<?if(isset($post_id)){?>
				<a id="preview_slug" target="_blank" href="<?=base_url('/blog/'.$post_slug)?>"><?=lang_line_ucwords('preview')?></a>
			<?}?>
			
			<input type="submit" value="<?=lang_line_ucwords('save')?>"></input>
		</div>
	</div>
	<? if ( !isset( $only_admin_bar ) ) { ?>
</div>

</form>
<? } ?>
</div>

<script>
	$(function()
	{
		editor_init("<?=base_url()?>");
	});
</script>
