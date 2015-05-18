KAJAX_DEBUG				= true;
KAJAX_POPING_STATE		= false;
KAJAX_LOADING_CLASS		= 'kajax-loading';
KAJAX_LOADING_QCLASS	= '.' + KAJAX_LOADING_CLASS;
KAJAX_LOADING_DIV		= '<div class="' + KAJAX_LOADING_CLASS + '"><div class="kajax-loading-image"></div></div>';
KAJAX_TARGET			= '#content';
kajax_load_callbacks	= [];
kajax_unload_callbacks	= [];

$(window).on('load', function ()
{
	setTimeout(function ()
	{
		kajax_push_state_init();
		kajax_init($('body'));
	}, 0);
});

function kajax_push_state_init()
{
	if(history.pushState)
	{
		// needed to recreate the 'first' page with AJAX
		history.replaceState({ path: window.location.href }, null);

		$(window).bind('popstate', function(event) {
			// if the event has our history data on it, load the page fragment with AJAX
			var state = event.originalEvent.state;
			
			if (state)
			{
				KAJAX_POPING_STATE = true;
				kajax_load_target(state.path);
				KAJAX_POPING_STATE = false;
			}
		});
	}
}

function kajax_bind_click(el, func)
{
	el = $(el);

	el.unbind('click'); //removes any previous event
	
	el.click(function(event)
	{
		if(event.which == 1)
		{
			func(el,event.which);
			
			return false;
		}
		else if (event.which == 2)
		{
			var fn = func(el,event.which);
			
			if ( 'string' == typeof fn )
			{
				window.open( fn, '_blank');
				
				return false;
			}
		}
	});
}

function kajax_init( parent )
{
	$('.ajax-form, .ajax', parent).each(
		function()
		{
			$(this).unbind('submit'); //removes any previous event
			$(this).bind('submit',kajax_form_submit); //avoids sending the form
		}
	);
	
	$('.ajax-append-form', parent).each(
		function()
		{
			$(this).unbind('submit'); //removes any previous event
			$(this).bind('submit',kajax_form_append_submit); //avoids sending the form
		}
	);
	
	$('.ajax-get-form', parent).each(
		function()
		{
			$(this).unbind('submit'); //removes any previous event
			$(this).bind('submit',kajax_get_form_submit); //avoids sending the form
		}
	);
	
	$('.ajax-link', parent).each(
		function()
		{
			kajax_bind_click(this,kajax_click);
		}
	);
	
	$('.ajax-el-link', parent).each(
		function()
		{
			kajax_bind_click(this,kajax_el_click);
		}
	);
	
	$('.ajax-paging-link', parent).each(
		function()
		{
			kajax_bind_click(this,kajax_paging_click);
		}
	);

	$('.ajax-confirm-link', parent).each(
		function()
		{
		   kajax_bind_click(this,kajax_confirm_click);
		}
	);
	
	$('.ajax-fancy-confirm-link', parent).each(
		function()
		{
		   kajax_bind_click(this,kajax_fancy_confirm_click);
		}
	);
	
	$('.ajax-eval-link', parent).each(
		function()
		{
		   kajax_bind_click(this,kajax_eval_click);
		}
	);
	
	$('.ajax-eval-confirm-link', parent).each(
		function()
		{
			kajax_bind_click(this,kajax_eval_confirm_click);
		}
	);
	
	$('.ajax-eval-fancy-confirm-link', parent).each(
		function()
		{
			kajax_bind_click(this,kajax_eval_fancy_confirm_click);
		}
	);
	
	$('.ajax-append', parent).each(
		function()
		{
			kajax_bind_click(this,kajax_append_click);
		}
	);
}

function kajax_on_loaded_event( where, load_url )
{
	kajax_init( where );
	kajax_fire_load_callbacks( where, load_url );
	$(KAJAX_LOADING_QCLASS, where).remove();
}

function kajax_load( where, load_url, push_state )
{
	if ( '#' == load_url )
	{
		return;
	}
	
	if ( typeof push_state != 'undefined' && false == push_state )
	{
		kajax_load_clean( where, load_url );
		
		return;
	}
	
	if( history.pushState )
	{
		where = $(where);
	
		if ( where.length == 0 )
		{
			return;
		}

		if ( !KAJAX_POPING_STATE )
		{
			history.pushState({path:load_url}, null, load_url);
		}
	
		where.empty();
		
		kajax_fire_unload_callbacks( where, load_url );
		
		where.append(KAJAX_LOADING_DIV);

		where.load( load_url, {'kajax':true} ,
			function()
			{
				kajax_on_loaded_event( where, load_url );
			}
		);
	}
	else
	{
		window.location = load_url;
	}
}

function kajax_load_clean( where, load_url )
{
	where = $(where);
	
	if ( where.length == 0 )
	{
		return;
	}

	where.append(KAJAX_LOADING_DIV);

	where.load(load_url,{'kajax':true},
		function()
		{
			kajax_on_loaded_event( where, load_url );
		}
	);
}

function kajax_load_target( load_url, push_state )
{
	kajax_load( KAJAX_TARGET, load_url, push_state );
}

function kajax_reload_target( push_state )
{
	kajax_load_target( window.location.href, push_state );
}

function kajax_load_paging( load_url, push_state )
{
	kajax_load( '#ajax-paging', load_url, push_state );
}

function kajax_confirm_click(el,button_id)
{
	var title = el.data('text');
	
	if( confirm(title) )
	{
		kajax_click(el,button_id);
	}
}

function kajax_fancy_confirm_click(el,button_id)
{
	var title = el.data('text');
	
	kajax_fancy_confirm(title, function() { kajax_click(el,button_id); } );
}

function kajax_click(el,button_id)
{
	var load_url = el.attr('href') || el.data('href');
	var push_state = !el.hasClass('ajax-clean');
	
	if ( 1 == button_id )
	{
		kajax_load_target(load_url, push_state);
	}
}

function kajax_el_click(el,button_id)
{
	var load_url = el.data('href');
	
	if ( 1 == button_id )
	{
		var push_state = !el.hasClass('ajax-clean');
	
		kajax_load_target(load_url, push_state);
	}
	
	return load_url;
}

function kajax_paging_click(el,button_id)
{
	var load_url = el.attr('href') || el.data('href');
	var push_state = !el.hasClass('ajax-clean');

	if ( 1 == button_id )
	{
		kajax_load_paging(load_url, push_state);
	}
}

/*raises a javascript overlay on the page, to show an unrecoverable exception*/
function kajax_raise_overlay(content)
{
	if(KAJAX_DEBUG)
	{
		var overlay = $('#kajax-overlay');
		
		if ( overlay.length )
		{
			overlay.remove();
		}
		
		$("body").append(
			$("<div id='kajax-overlay'><pre id='kajax-debug-content'></pre></div>")
		);
		
		$("#kajax-debug-content").html(content);
		
		$("#kajax-overlay").click( function()
		{
			$("#kajax-overlay").remove();
		});
	}
}

function kajax_text_to_html(string)
{
	return $('<span>').text(string).html()
};

function kajax_error_to_html(errobj)
{
	var s = '';
	$.each(
		errobj, function(k,v)
		{
			if(k!='')
			{
				s += '\n---------------\n';
				s += '<span style="color:red">'+ k + '</span>' + v;
			}
			else{
				s += '<span style="color:red; font-size:2.0em;">'+v+ '</span>';
			}
		}
	)
	return s;
}

function kajax_error_to_text(errobj)
{
	var s = '';
	$.each(
		errobj, function(k,v)
		{
			s += '\n---------------\n';
			s += k + v;
		}
	)
	return s;
}

/*catches all ajax errors*/
$(document).ajaxError(function(event, xhr, ajaxOptions, errorThrown)
{
	msg = {
		'':'KAJAX Error while executing ajax request',
		'URL: ':ajaxOptions.url,
		'Status: ':xhr.status+' ('+errorThrown+')',
		'Response Text: ':xhr.responseText
	}
	
	if ( xhr.status != 0 )
	{
		kajax_raise_overlay(kajax_error_to_html(msg));
	}
	
	$(KAJAX_LOADING_QCLASS).remove();
	
	throw kajax_error_to_text(msg);
});

function kajax_eval(url, data /*this can be null*/, on_success /*this can be null*/)
{
	if ( url == '#' )	return;
	
	$.post( url, data,
		function(res)
		{
			try
			{
				eval(res);
			}
			catch(err)
			{
				msg = {
					'':'KAJAX Exception while evaluating Form javascript response',
					'Url: ':url,
					'Sent Data: ':decodeURIComponent(data),
					'Error Type: ':err.name,
					'Debug message: ':err.message,
					'Received Script: \n\n':res
				};

				kajax_raise_overlay( kajax_error_to_html(msg) );
				
				$(KAJAX_LOADING_QCLASS).remove();
				
				throw kajax_error_to_text( msg );
			}
			
			if(on_success)
			{
				on_success(res);
			}
		}
	);
}

function kajax_append_click(el)
{
	var url		= el.attr('href') || el.data('href');
	var data	= el.data('data');
	var cb		= el.data('cb');
	var where	= el.data('where');
	
	if ( 'string' == typeof data )
	{
		data = $.parseJSON( data );
	}
	
	kajax_append(el, url, data, where, cb);
}

function kajax_append_after(el, url, data, on_success)
{
	kajax_append(el, url, data, 'after', on_success);
}

function kajax_append_before(el, url, data, on_success)
{
	kajax_append(el, url, data, 'before', on_success);
}

function kajax_append(el, url, data /*this can be null*/, where /* inside/after/before */, on_success /*this can be null*/)
{
	if ( url == '#' )	return;
	
	$.post( url, data,
		function(res)
		{
			try
			{
				if ( 'undefined' != typeof where )
				{
					if ( 'after' == where )
					{
						el.after(res);
					}
					else if ( 'before' == where )
					{
						el.before(res);
					}
					else
					{
						el.append(res);
					}
				}
				else
				{
					el.append(res);
				}
			}
			catch(err)
			{
				msg = {
					'':'KAJAX Exception while evaluating Form javascript response',
					'Url: ':url,
					'Sent Data: ':decodeURIComponent(data),
					'Error Type: ':err.name,
					'Debug message: ':err.message,
					'Received Script: \n\n':res
				};

				kajax_raise_overlay( kajax_error_to_html(msg) );
				
				$(KAJAX_LOADING_QCLASS).remove();
				
				throw kajax_error_to_text( msg );
			}
			
			if(on_success)
			{
				on_success(res);
			}
		}
	);
}

function kajax_eval_confirm_click(el)
{
	var title = el.data('text');
	
	if( confirm(title) )
	{
		kajax_eval_click(el);
	}
}

function kajax_eval_fancy_confirm_click(el)
{
	var title = el.data('text');
	
	kajax_fancy_confirm( title, function() { kajax_eval_click(el); } );
}

function kajax_eval_click(el)
{
	var load_url = el.attr('href') || el.data('href');
	
	kajax_eval(load_url);
}

function kajax_el_eval_click(el)
{
	var load_url = el.data('href');
	
	kajax_eval(load_url);
}

function kajax_form_submit()
{
	var f = $(this);
	data = "kajax=true&" + f.serialize();
	url = f.attr('action');
	$('>ul',f).append(KAJAX_LOADING_DIV);

	$('.kajax-form-error').fadeOut(500);

	kajax_eval(url,data, function()
	{
		$(KAJAX_LOADING_QCLASS, f).remove();
	});

	return false;
}

function kajax_form_append_submit()
{
	var f		= $(this);
	var data	= "kajax=true&" + f.serialize();
	var url		= f.attr('action');
	var el		= $(f.data('to'));
	var where	= f.data('where');
	var cb		= f.data('cb');
	
	$('>ul',f).append(KAJAX_LOADING_DIV);

	$('.kajax-form-error').fadeOut(500);

	kajax_append(el,url,data,where, function(res)
	{
		$(KAJAX_LOADING_QCLASS, f).remove();
		
		if ( cb )
		{
			var fn = window[cb];
			if (typeof fn === "function") fn(res);
		}
	});

	return false;
}

function kajax_get_form_url(f,_url)
{
	var f = 'currentTarget' in f ? $(f.currentTarget) : f;
	
	url = 'undefined' != typeof _url ? _url : f.attr('action');

	if ( 'undefined' == typeof url )
	{
		// TODO: This should merge query strings and create a new url.
		url = document.location.origin + document.location.pathname;
		
		if ( !url.endsWith( '/') )
		{
			url += '/';
		}
	}
	else if ( 'undefined' != typeof _url && !url.contains('?') && !url.endsWith('/') )
	{
		url += '/';
	}
	
	data = ( !url.contains("?") ? '?' : '&' ) + f.serialize();
	
	return url+data;
}

function kajax_get_form_submit(f,_url)
{
	var f = 'currentTarget' in f ? $(f.currentTarget) : f;
	
	url = kajax_get_form_url(f,_url);
	
	where = f.data('target');
	
	push_state = !f.hasClass('ajax-clean');
	
	if ( 'undefined' == typeof where )
	{
		where = KAJAX_TARGET;
	}
	
	var cb = f.data('cbstart');
	if ( cb )
	{
		var fn = window[cb];
		if (typeof fn === "function") fn();
	}
	
	kajax_load(where, url, push_state);

	return false;
}

/** load callbacks */
function kajax_fire_load_callbacks( where, load_url )
{
	for ( var i = 0; i < kajax_load_callbacks.length; i++ )
	{
		kajax_load_callbacks[i]( where, load_url );
	}
}

function kajax_register_load_callback( cb )
{
	kajax_load_callbacks.push( cb );
}
/** load callbacks */

/** unload callbacks */
function kajax_fire_unload_callbacks( where, load_url )
{
	for ( var i = 0; i < kajax_unload_callbacks.length; i++ )
	{
		if ( kajax_unload_callbacks[i]( where, load_url ) )
		{
			kajax_obj_remove_item( kajax_unload_callbacks, i );
		}
	}
}

function kajax_register_unload_callback( cb )
{
	kajax_unload_callbacks.push( cb );
}
/** unload callbacks */

function kajax_table_row_form_submit( form, table_row )
{
	form.empty();
	
	if ( 'undefined' != typeof table_row && null != table_row )
	{
		if( $.isArray( table_row ) )
		{
			for ( var i = 0; i < table_row.length; i++ )
			{
				form.append( table_row[i].find( 'input, select, textearea' ).clone() );
			}
		}
		else
		{
			form.append( table_row.find( 'input, select, textearea' ).clone() );
		}
	}
	
	data = "kajax=true&" + form.serialize();
	form.empty();

	url = form.attr('action');
	$('>ul',form).append(KAJAX_LOADING_DIV);

	$('.kajax-form-error').fadeOut(500);

	kajax_eval(url,data, function()
	{
		$(KAJAX_LOADING_QCLASS, form).remove();
	});

	return false;
}

function kajax_fancy_confirm( msg, cb )
{
	alertify.confirm( msg, function (e)
	{
		if ( e )
		{
			cb();
		}
	});
}

// Textarea and select clone() bug workaround | Spencer Tipping
// Licensed under the terms of the MIT source code license

// Motivation.
// jQuery's clone() method works in most cases, but it fails to copy the value of textareas and select elements. This patch replaces jQuery's clone() method with a wrapper that fills in the
// values after the fact.

// An interesting error case submitted by Piotr Przybyl: If two <select> options had the same value, the clone() method would select the wrong one in the cloned box. The fix, suggested by Piotr
// and implemented here, is to use the selectedIndex property on the <select> box itself rather than relying on jQuery's value-based val().
(function (original) {
	jQuery.fn.clone = function () {
	var result           = original.apply(this, arguments),
		my_textareas     = this.find('textarea').add(this.filter('textarea')),
		result_textareas = result.find('textarea').add(result.filter('textarea')),
		my_selects       = this.find('select').add(this.filter('select')),
		result_selects   = result.find('select').add(result.filter('select'));

	for (var i = 0, l = my_textareas.length; i < l; ++i) $(result_textareas[i]).val($(my_textareas[i]).val());
	for (var i = 0, l = my_selects.length;   i < l; ++i) {
	  for (var j = 0, m = my_selects[i].options.length; j < m; ++j) {
		if (my_selects[i].options[j].selected === true) {
		  result_selects[i].options[j].selected = true;
		}
	  }
	}
	return result;
	};
}) (jQuery.fn.clone);

/** polyfills */
(function()
{
	if (!Object.defineProperty || !(function () { try { Object.defineProperty({}, 'x', {}); return true; } catch (e) { return false; } } ()) )
	{
		var orig = Object.defineProperty;
		
		Object.defineProperty = function (o, prop, desc)
		{
			// In IE8 try built-in implementation for defining properties on DOM prototypes.
			if (orig) { try { return orig(o, prop, desc); } catch (e) {} }
			
			if (o !== Object(o)) { throw TypeError("Object.defineProperty called on non-object"); }
			
			if (Object.prototype.__defineGetter__ && ('get' in desc))
			{
				Object.prototype.__defineGetter__.call(o, prop, desc.get);
			}
			
			if (Object.prototype.__defineSetter__ && ('set' in desc))
			{
				Object.prototype.__defineSetter__.call(o, prop, desc.set);
			}
			
			if ('value' in desc)
			{
				o[prop] = desc.value;
			}
			
			return o;
		};
	}
}());

if ( !String.prototype.endsWith )
{
	Object.defineProperty(String.prototype, 'endsWith',
	{
		value: function (searchString, position)
		{
			var subjectString = this.toString();
			
			if (position === undefined || position > subjectString.length)
			{
				position = subjectString.length;
			}
			
			position -= searchString.length;
			var lastIndex = subjectString.indexOf(searchString, position);
			
			return lastIndex !== -1 && lastIndex === position;
		}
	});
}

if ( !String.prototype.contains )
{
	String.prototype.contains = function()
	{
		return String.prototype.indexOf.apply( this, arguments ) !== -1;
	};
}

if ( !String.prototype.startsWith )
{
	Object.defineProperty(String.prototype, 'startsWith',
	{
		enumerable: false,
		configurable: false,
		writable: false,
		value: function (searchString, position)
		{
			position = position || 0;
			return this.lastIndexOf(searchString, position) === position;
		}
	});
}

function kajax_obj_remove_item(obj,key)
{
	if ( !obj.hasOwnProperty( key ) )
	{
		return;
	}
	
	if ( isNaN( parseInt( key ) ) || !( obj instanceof Array ) )
	{
		delete obj[key];
	}
	else
	{
		obj.splice(key, 1);
	}
}
