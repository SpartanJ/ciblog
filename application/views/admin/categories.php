<?php
function categories_build_link( $order_by )
{
	return base_url( '/admin/categories/?' ) . http_build_query_merge_auto( array( 'order_by' => $order_by ) );
}
?>
<div class="hidden">
	<table>
		<tbody>
			<tr class="row_new">
				<td>
					<input type="hidden" name="id" value="0" />
					<input type="text" name="key" />
				</td>
				<td>
					<input type="text" name="name" />
				</td>
			</tr>
			<tr class="form_buttons hidden" class="row_hidden_new">
				<td colspan="2" align="center">
					<button class="submit_btn_table cancel_btn" value="cancel"><span><?=lang_line_upper('cancel')?></span></button>
					
					<button class="submit_btn_table save_btn" value="submit"><span><?=lang_line_upper('save')?></span></button>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div class="admin-content ajax-paging">
	<div class="inner">
		<h1><a class="ajax-link" href="<?=base_url('/admin/categories')?>"><?=lang_line_upper('categories')?></a></h1>
		
		<a id="add_new_but" class="button square-button"><?=lang_line_upper('new')?></a>
				
		<div class="resize_informer">
			<form id="table-form" action="<?=base_url('/admin/category_update')?>" method="post" style="display: none;"></form>
			
			<table id="categories_list_table" class="default_table" align="center" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th class="ajax-el-link" data-href="<?=categories_build_link( 'cat_key' )?>"><?=lang_line_ucwords('key')?></th>
						<th class="ajax-el-link" data-href="<?=categories_build_link( 'cat_name' )?>"><?=lang_line_ucwords('name')?></th>
					</tr>
				</thead>
				<tbody>
					<? if ( isset( $categories ) ) { foreach ( $categories as $cat ) { ?>
					<tr id="row_<?=$cat['cat_id']?>">
						<td>
							<input type="hidden" name="id" value="<?=$cat['cat_id']?>" />
							<input type="text" name="key" value="<?=$cat['cat_key']?>" />
						</td>
						<td>
							<input type="text" name="name" value="<?=$cat['cat_name']?>" />
						</td>
					</tr>
					<tr class="form_buttons hidden" id="row_hidden_<?=$cat['cat_id']?>">
						<td colspan="2" align="center">
							<button class="submit_btn_table cancel_btn" value="cancel"><span><?=lang_line_upper('cancel')?></span></button>
							
							<button class="submit_btn_table save_btn" value="submit"><span><?=lang_line_upper('save')?></span></button>
						</td>
					</tr>
					<? }} ?>
				</tbody>
			</table>
		</div>

	</div>
</div> 

<script type="text/javascript">
	var form_table	= null;

	function row_new_add_click( el )
	{
		if ( $('#row_new').length == 0 )
		{
			var trs = $('.hidden table tbody tr').clone();
			trs.first().attr('id','row_new').removeClass('row_new');
			trs.last().attr('id','row_hidden_new').removeClass('row_hidden_new');
			
			$('#categories_list_table').prepend( trs );
			
			table_row_new_register( $('#row_new'), $('#row_hidden_new'), $('#content'), $('#table-form') );
		}
	}
	
	$(function()
	{
		form_table	= $('#table-form');
		
		table_register( form_table );
		
		$('#add_new_but').unbind('click').bind('click',function()
		{
			row_new_add_click();
		});
	})
</script>