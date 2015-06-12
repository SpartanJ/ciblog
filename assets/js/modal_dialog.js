var _modal_dialog_callbacks_open	= [];
var _modal_dialog_callbacks_close	= [];
var _modal_dialog_callbacks_update	= [];
var _modal_dialog_size_width		= null;
var _modal_dialog_size_height		= null;
var _modal_inline_content			= null;
var _modal_is_open					= false;
var modal_dialog_header_name		= '#header';
var modal_dialog_footer_name		= '#footer';

function _modal_dialog_callbacks_open_fire()
{
	for ( var i = 0; i < _modal_dialog_callbacks_open.length; i++ )
	{
		_modal_dialog_callbacks_open[i]();
	}
	
	_modal_dialog_callbacks_open = [];
}

function _modal_dialog_callbacks_close_fire()
{
	for ( var i = 0; i < _modal_dialog_callbacks_close.length; i++ )
	{
		_modal_dialog_callbacks_close[i]();
	}
	
	_modal_dialog_callbacks_close	= [];
	_modal_dialog_callbacks_update	= [];
}

function _modal_dialog_callbacks_update_fire()
{
	for ( var i = 0; i < _modal_dialog_callbacks_update.length; i++ )
	{
		_modal_dialog_callbacks_update[i]();
	}
}

function modal_dialog_register_open_callback( cb )
{
	return _modal_dialog_callbacks_open.push( cb ) - 1;
}

function modal_dialog_register_close_callback( cb )
{
	return _modal_dialog_callbacks_close.push( cb ) - 1;
}

function modal_dialog_register_update_callback( cb )
{
	return _modal_dialog_callbacks_update.push( cb ) - 1;
}

function modal_dialog_update()
{
	var md = $('.modal_dialog');
	
	if ( md.length > 0 )
	{
		_modal_dialog_callbacks_update_fire();
		
		var v					= modal_dialog_viewport_get();
		var modal_div			= md.find(' > div');
		var header_height		= !$(modal_dialog_header_name).hasClass('ignore') ? $(modal_dialog_header_name).outerHeight() : 0;
		var footer_height		= !$(modal_dialog_footer_name).hasClass('ignore') ? $(modal_dialog_footer_name).outerHeight() : 0;
		var modal_div_height	= modal_div.outerHeight();
		
		var modal_mt	= ( v.height - header_height - footer_height - modal_div_height ) / 2;
		
		md.css('top', header_height );
		md.css('bottom', footer_height );
		
		modal_div.css('margin-top', modal_mt );
	}
	
	return md;
}

function modal_dialog_get()
{
	return $('.modal_dialog');
}

function modal_dialog_is_visible()
{
	var md = $('.modal_dialog');
	
	if ( md.length > 0 )
	{
		if ( md.is(':visible') )
		{
			return true;
		}
	}
	
	return false;
}

function modal_dialog_is_open()
{
	return _modal_is_open;
}

function modal_dialog_size( width, height )
{
	var md = $('.modal_dialog');
	
	if ( md.length > 0 )
	{
		if ( 'undefined' != typeof width && null != width )
		{
			md.find('> div').css('width', width );
		}
		else
		{
			md.find('> div').css('width', '');
		}
		
		if ( 'undefined' != typeof height && null != height )
		{
			md.find('> div').css('height', height );
		}
		else
		{
			md.find('> div').css('height', '');
		}
		
		setTimeout(function()
		{
			modal_dialog_update();
		},0);
	}
}

function modal_dialog_opts_apply( modal_dialog_opts )
{
	if ( 'undefined' != typeof modal_dialog_opts && null != modal_dialog_opts )
	{
		if ( 'header' in modal_dialog_opts )
		{
			modal_dialog_header_name = modal_dialog_opts.header;
		}
		
		if ( 'footer' in modal_dialog_opts )
		{
			modal_dialog_footer_name = modal_dialog_opts.footer;
		}		
		
		if ( 'extraClass' in modal_dialog_opts )
		{
			modal_dialog_add_class( modal_dialog_opts.extraClass );
		}
		
		if ( 'onClosed' in modal_dialog_opts )
		{
			modal_dialog_register_close_callback( modal_dialog_opts.onClosed );
		}
		
		if ( 'onOpened' in modal_dialog_opts )
		{
			modal_dialog_register_open_callback( modal_dialog_opts.onOpened );
		}
		
		if ( 'onUpdate' in modal_dialog_opts )
		{
			modal_dialog_register_update_callback( modal_dialog_opts.onUpdate );
		}
		
		if ( 'width' in modal_dialog_opts )
		{
			_modal_dialog_size_width = modal_dialog_opts.width;
		}
		
		if ( 'height' in modal_dialog_opts )
		{
			_modal_dialog_size_height = modal_dialog_opts.height;
		}
		
		modal_dialog_size( _modal_dialog_size_width, _modal_dialog_size_height );
		
		_modal_dialog_size_width 	= null;
		_modal_dialog_size_height	= null;
	}
}

function modal_dialog_add_class( cls )
{
	modal_dialog_get().addClass( cls );
}

function modal_dialog_create( modal_dialog_opts )
{
	var md = $('.modal_dialog');

	if ( md.length == 0 )
	{
		$('body').append( '<div class="modal_dialog"><div><div class="cont"></div><div class="close"></div></div></div>' );

		md = $('.modal_dialog');
	}
	
	md.attr('class','modal_dialog');
	
	modal_dialog_opts_apply( modal_dialog_opts );
	
	md.on('click', function(e)
	{
		if( e.target !== this )
		{
			return;
		}
		
		modal_dialog_close();
	});
	
	md.find('.close').unbind('click').bind('click',function()
	{
		modal_dialog_close();
	});
	
	$(document).one("keydown.modal_dialog", function(e) {
		// escape key
		if ( e.keyCode == 27 )
		{
			e.preventDefault();
			
			modal_dialog_close();
		}
	});
	
	return md;
}

function modal_dialog_get_transition_duration()
{
	var tdparent	= $('.modal_dialog').css('transition-duration');
	var tdchild		= $('.modal_dialog > div').css('transition-duration');
	return parseInt( parseFloat( tdparent > tdchild ? tdparent : tdchild ) * 1000 );
}

function modal_dialog_open( modal_dialog_opts )
{
	var md = modal_dialog_create( modal_dialog_opts );
	
	$('.modal_dialog, .modal_dialog > div').each(function(){ $(this).width(); });
	
	md.addClass('modal_dialog_visible');
	
	setTimeout(function()
	{
		md.addClass('modal_dialog_ready');
	}, modal_dialog_get_transition_duration() );
	
	_modal_is_open = true;
	
	setTimeout(function()
	{
		modal_dialog_update();
	},0);
	
	_modal_dialog_callbacks_open_fire();
	
	return md;
}

function modal_dialog_close()
{
	var md = $('.modal_dialog');
	
	if ( md.length > 0 )
	{
		md.removeClass('modal_dialog_ready');
		
		md.removeClass('modal_dialog_visible');
		
		_modal_is_open = false;

		setTimeout(function()
		{
			_modal_dialog_callbacks_close_fire();
			
			md.find('.cont').empty();
		}, modal_dialog_get_transition_duration() );

		$(document).unbind("keydown.modal_dialog");
	}
}

function modal_dialog_close_timeout( time )
{
	setTimeout(function()
	{
		var md = $('.modal_dialog');
		
		if ( md.length > 0 && md.css("display") == "block" )
		{
			modal_dialog_close();
		}
	}, time);
}

function modal_dialog_ajax_get( uri, data, modal_dialog_opts )
{
	_modal_dialog_callbacks_close_fire();
	
	var md = modal_dialog_create( modal_dialog_opts );
	
	if ( !uri.startsWith( 'http' ) )
	{
		uri = page_url + uri;
	}
	
	$.get( uri, data, function( res )
	{
		if ( res )
		{
			var cont = md.find('.cont');
			
			cont.empty();
			
			cont.html( res );
			
			modal_dialog_open( modal_dialog_opts );
			
			if ( 'undefined' != typeof modal_dialog_opts )
			{
				if ( 'onComplete' in modal_dialog_opts )
				{
					modal_dialog_opts.onComplete( md, res );
				}
			}
		}
	});
}

function modal_dialog_inline( target, modal_dialog_opts )
{
	_modal_dialog_callbacks_close_fire();
	
	if ( 'string' == typeof target )
	{
		target = $(target);
	}
	
	var md = modal_dialog_create( modal_dialog_opts );
	
	if ( target.length > 0 )
	{
		var cont = md.find('.cont');
		
		cont.empty();
		
		_modal_inline_content = $('<div>').hide().insertBefore(target);

		modal_dialog_register_close_callback(function()
		{
			_modal_inline_content.replaceWith(target);
			_modal_inline_content = null;
		});
		
		cont.append(target);
		
		modal_dialog_open( modal_dialog_opts );
	}
}

function modal_dialog_viewport_get()
{
	var e = window, a = 'inner';

	if ( !('innerWidth' in window ) )
	{
		a = 'client';
		e = document.documentElement || document.body;
	}

	return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
}
