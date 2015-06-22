<div class="admin-content">

<? if ( !isset( $only_admin_bar ) ) { ?>
<form class="ajax" method="post" action="<?=base_url('/admin/save')?>">

<div class="hidden">
	<div class="tag tag_base"><span></span> <a href="#" class="ajax-eval-link"><i class="fa fa-times-circle"></i></a></div>
	
	<div class="post_advanced">
		<h4><?=lang_line_ucwords('post_options')?></h4>
		
		<div class="inner inner-form">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="user_email"><?=lang_line_ucwords('in_menu')?></label></th>
						<td>
							<input id="post_in_menu" type="checkbox" name="in_menu" <?=isset($post_in_menu)&&intval($post_in_menu!=0)?' checked="checked"':''?>>
							<label for="post_in_menu">&nbsp;</label>
						</td>
					</tr>
					<tr>
						<th><label for="post_order"><?=lang_line_ucwords('order')?></label></th>
						<td><input type="text" name="order" id="post_order" value="<?=$post_order?>" /></td>
					</tr>
					<tr>
						<th><label for="post_order"><?=lang_line_ucwords('menu_title')?></label></th>
						<td><input type="text" name="menu_title" id="post_menu_title" value="<?=$post_menu_title?>" /></td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div class="inner post_tags">
			<h3><?=lang_line_ucwords('tags')?></h3>
			
			<input type="text" id="tags_input" name="tags" />
			
			<input id="tag_add_button" type="button" value="<?=lang_line_ucwords('add')?>" />
			
			<p><?=lang_line('separate_tags')?></p>
			
			<div class="tags">
			<? if ( isset( $tags ) ) { foreach ( $tags as $tag ) { ?>
			<div class="tag" id="ptag_id_<?=$tag['ptag_id']?>"><span><?=$tag['ptag_name']?></span> <a href="<?=base_url('/admin/post_tag_delete/'.$tag['ptag_id'])?>" class="ajax-eval-link"><i class="fa fa-times-circle"></i></a></div>
			<? }} ?>
			</div>
		</div>
	</div>
</div>

<div class="admin-editor">
	<input type="text" name="title" class="title" placeholder="<?=lang_line_ucwords('title')?>" value="<?=isset($post_title)?$post_title:''?>">
	<textarea name="body" class="body" placeholder="<?=lang_line('write_something')?>"><?=isset($post_body)?htmlspecialchars($post_body):''?></textarea>
</div>

<div class="save-success-msg"><?=lang_line_ucwords('saved')?></div>

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
		<input type="hidden" id="post_id" name="post_id" value="<?=$post_id?>"/>
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
			
			<input type="button" value="<?=lang_line_ucwords('advanced')?>" onclick="post_advanced_dialog();"></input>
			
			<input type="submit" value="<?=lang_line_ucwords('save')?>"></input>
		</div>
	</div>
	<? if ( !isset( $only_admin_bar ) ) { ?>
</div>

</form>
<? } ?>
</div>

<script>
	function post_advanced_dialog()
	{
		if ( !modal_dialog_is_open() )
		{
			modal_dialog_inline( $('.post_advanced'), {
				width: '80%',
				height: '90%',
				header: '',
				footer: ''
			});
		}
	}
	
	function tag_add_from_input()
	{
		var tagstxt = $('#tags_input').val();
		
		kajax_eval( '<?=base_url('/admin/post_tag_add')?>', 
			{
				post_id: <?=$post_id?>, 
				tags: tagstxt
			},
			function(res)
			{
				$('#tags_input').val('');
			}
		);
	}
	
	$(function()
	{
		editor_init("<?=base_url()?>");
		
		$('#tag_add_button').unbind('click').bind('click', function()
		{
			tag_add_from_input();
		});
		
		$('#tags_input').keypress(function(event)
		{
			if (event.keyCode == 13)
			{
				tag_add_from_input();
			}
		});
	});
</script>
