var editing_row			= null;
var editing_sel			= null;

function editor_resize()
{
	var winh = $(window).height();
	var bar = parseInt( $( '.bar-top' ).outerHeight() );
	var adminbar = parseInt( $( '.bar-edit' ).outerHeight() );
	var admineditormargin = parseInt( $('.admin-editor .title').css('margin-top') ) +
							parseInt( $('.admin-editor .title').css('margin-bottom') );
	var admineditortitle = parseInt( $('.admin-editor .title').outerHeight() );
	var occupied = adminbar + admineditormargin + admineditortitle;
	var finh = winh - occupied - bar - 4;
	
	if ( screen.height <= 700 )
	{
		ckeditor_inst.resize('100%', finh * 2, false);
	}
	else
	{
		ckeditor_inst.resize('100%', finh, false);
	}
}

function editor_init( _page_url )
{
	if ( _page_url.length > 0 && $( 'textarea.body' ).length > 0 )
	{
		CKEDITOR.env.isCompatible = true;
		
		var config = {
			extraPlugins: 'codesnippet',
			codeSnippet_theme: 'obsidian',
			filebrowserBrowseUrl: _page_url + 'fm/index.html'
		};

		$( 'textarea.body' ).ckeditor( config );
		
		CKEDITOR.on('instanceLoaded', function(e)
		{
			ckeditor_inst = e.editor;
			editor_resize();
		});
	}
}

function table_row_selected_register( t )
{
	var focus_f = function()
	{
		var p = t.parent().parent();
		
		do
		{
			if ( p && p.attr('id') && p.attr('id').startsWith('row') )
			{
				break;
			}
			
			p = p.parent();
		} while( p.length > 0 );
		
		if ( p.length == 0 )
		{
			return;
		}
		
		var row_selected	= p;
		var new_editing_row = row_selected.next();
		
		if ( !row_selected.attr('id').startsWith('row') )
		{
			return;
		}
		
		while ( row_selected.attr('id').startsWith('row_extra') )
		{
			row_selected = row_selected.prev();
		}
		
		while ( new_editing_row.attr('id').startsWith('row_extra') )
		{
			new_editing_row = new_editing_row.next();
		}
		
		var new_editing_row_id = new_editing_row.attr('id');
		
		while ( !new_editing_row.attr('id').startsWith('row_hidden') )
		{
			new_editing_row	= new_editing_row.next();
		}
		
		if ( null != editing_row && editing_row.attr('id') != new_editing_row.attr('id') )
		{
			editing_sel.removeClass('selected');
			editing_row.addClass( 'hidden' );
			
			var row_extra = editing_sel.next();
			
			while ( row_extra.length > 0 && row_extra.attr('id').startsWith('row_extra') )
			{
				row_extra.addClass('hidden');
				row_extra = row_extra.next();
			}
			
			if ( 'undefined' != typeof on_table_row_unselected )
			{
				on_table_row_unselected( editing_sel );
			}
		}
		
		editing_sel = row_selected;
		editing_row = new_editing_row;
		
		editing_sel.addClass('selected');
		editing_row.removeClass('hidden');
		
		var row_next = editing_sel.next();
		
		while ( row_next.attr('id').startsWith('row_extra') )
		{
			row_next.addClass('selected');
			row_next.removeClass('hidden');
			row_next = row_next.next();
		}
		
		if ( 'undefined' != typeof on_table_row_selected )
		{
			on_table_row_selected( row_selected );
		}
	}
	
	if ( t.is(':checkbox') )
	{
		t.change( focus_f );
	}
	else
	{
		t.focus( focus_f );
	}
}

function table_row_new_register( rows_new, row_hidden_new, div_scroller, form )
{
	var row_new = null;
	var focus_f = function( row_new )
	{
		var nri = row_new.find('input[type=text]');
		
		for ( var i = 0; i < nri.length; i++ )
		{
			var el = $( nri[i] );
			
			if ( !el.prop('disabled') )
			{
				el.focus();
				
				break;
			}
		}
	}
	
	if ( $.isArray( rows_new ) )
	{
		row_new = rows_new[0];
		
		for ( var i = 0; i < rows_new.length; i++ )
		{
			rows_new[i].find('input[type=text], input[type=password], select, textarea, img').each(
				function()
				{
					table_row_selected_register( $(this) );
				}
			);
		}
	}
	else
	{
		row_new = rows_new;
		row_new.find('input[type=text], input[type=password], select, textarea, img').each(
			function()
			{
				table_row_selected_register( $(this) );
			}
		);
	}
	
	var cancel_but = row_hidden_new.find( 'button.cancel_btn' );
	cancel_but.unbind('click');
	cancel_but.bind('click', 
		function()
		{
			if ( $.isArray( rows_new ) )
			{
				for ( var i = 0; i < rows_new.length; i++ )
				{
					rows_new[i].remove();
				}
			}
			else
			{
				rows_new.remove();
			}
			
			row_hidden_new.remove();
			
			editing_row	= null;
			editing_sel	= null;
		}
	);
	
	var save_but = row_hidden_new.find('button.save_btn');
	save_but.unbind('click');
	save_but.bind('click', 
		function()
		{
			table_save_click_register( form, save_but.parent().parent().prev() );
		}
	);
	
	if ( div_scroller.scrollTop() > 0 )
	{
		div_scroller.scrollTo(
			{ top:0, left:0 },
			800,
			{
				onAfter: function()
				{
					focus_f( row_new );
				}
			}
		);
	}
	else
	{
		focus_f( row_new );
	}
}

function table_cancel_click_register( t )
{
	var row = t.parent().parent().prev();
	
	while ( row.attr('id').startsWith('row_extra') )
	{
		row.removeClass('selected');
		row.addClass('hidden');
		row = row.prev();
	}
	
	row.removeClass('selected');
	t.parent().parent().addClass('hidden');

	var row_extra = row.next();
	
	while ( row_extra.attr('id').startsWith('row_extra') )
	{
		row_extra.addClass('hidden');
		row_extra = row_extra.next();
	}

	if ( 'undefined' != typeof on_table_row_unselected )
	{
		on_table_row_unselected( row );
	}
}

function table_row_new_convert_to_id( id, id_field_name, delete_url, delete_val, delete_text )
{
	var row_new		= $('#row_new');
	var row			= ( row_new.length > 0 ) ? row_new : $('#row_'+id);
	var row_hidden	= row.next();

	while ( !row_hidden.attr('id').startsWith('row_hidden') )
	{
		row_hidden	= row_hidden.next();
	}

	row.find('input[name="' + id_field_name + '"]').val(id);
	
	var cancel_but = row_hidden.find( 'button.cancel_btn' );
	cancel_but.unbind('click');
	cancel_but.bind('click', function() { table_cancel_click_register( cancel_but ); } );
	
	if ( 'undefined' != typeof delete_url && '' != delete_url )
	{
		row_hidden.find('td').prepend( '<button data-href="' + delete_url + '" class="submit_btn_table delete_btn" data-text="' + delete_val + '"><span>' + delete_text + '</span></button>' );
		
		var delete_but = row_hidden.find('button.delete_btn');
		delete_but.unbind('click');
		delete_but.bind('click', 
			function()
			{
				kajax_fancy_confirm( delete_but.data('text'), function() { kajax_eval_click( delete_but ); } );
			}
		);
	}
	
	if ( 'undefined' != typeof table_row_new_fix_current )
	{
		table_row_new_fix_current( id );
	}
}

function table_save_click_register( form, row_clone )
{
	while ( row_clone.attr('id').startsWith('row_extra') )
	{
		row_clone = row_clone.prev();
	}
	
	var row			= [ row_clone ];
	var row_extra	= row_clone.next();
	
	while ( row_extra.attr('id').startsWith('row_extra') )
	{
		row.push( row_extra );
		
		row_extra	= row_extra.next();
	}
	
	kajax_table_row_form_submit( form, row );
}

function table_delete_click_register( btn )
{
	btn.unbind('click');
	btn.bind('click', 
		function()
		{
			kajax_fancy_confirm( btn.data('text'), function() { kajax_eval_click( btn ); } );
		}
	);
}

function table_register( form_table )
{
	$('table tr td input[type=text], table tr td input[type=password], table tr td select, table tr td textarea, table tr td img, table tr td input[type=checkbox]').each(
		function()
		{
			table_row_selected_register( $(this) );
		}
	);
	
	$('table tr td button.cancel_btn').each(
		function()
		{
			var t = $(this);
			
			t.unbind('click');
			t.bind('click', 
				function()
				{
					table_cancel_click_register( t );
				}
			);
		}
	);
	
	$('table tr td button.save_btn').each(
		function()
		{
			var f = form_table;
			var t = $(this);
			
			t.unbind('click');
			t.bind('click', 
				function()
				{
					table_save_click_register( f, t.parent().parent().prev() );
				}
			);
		}
	);
	
	$('table tr td button.view_btn').each(
		function()
		{
			var t = $(this);
			
			t.unbind('click');
			t.bind('click',
				function()
				{
					window.location.href = t.data('href');
				}
			);
		}
	);
	
	$('table tr td button.modal_ajax_btn').each(
		function()
		{
			var t = $(this);
			
			t.unbind('click');
			t.bind('click',
				function()
				{
					modal_dialog_ajax_get( t.data('href'), null, t.data('options') );
				}
			);
		}
	);
	
	$('table tr td button.delete_btn, table tr td button.suspend_btn').each(
		function()
		{
			table_delete_click_register( $(this) );
		}
	);
}
