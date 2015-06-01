

function editor_resize()
{
	var winh = $(window).height();
	var bar = parseInt( $( '#bar' ).outerHeight() );
	var adminbar = parseInt( $( '.admin-bar' ).outerHeight() );
	var admineditormargin = parseInt( $('.admin-editor').css('margin-top') );
	var admineditortitle = parseInt( $('.admin-editor .title').outerHeight() );
	var occupied = adminbar + admineditormargin + admineditortitle;
	var finh = winh - occupied - bar;
	
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
