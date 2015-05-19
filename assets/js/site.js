var is_mobile =  screen.width < 1000;
var ckeditor_inst = null;

$(function ()
{
	init_site();
});

function init_site()
{
	autoresize_init();
	
	placeholder_init();

	mailcheck_init();

	mobile_init();
}

function editor_resize()
{
	var winh = $(window).height();
	var adminbar = parseInt( $( '#admin-bar' ).outerHeight() );
	var admineditormargin = parseInt( $('.admin-editor').css('margin-top') );
	var admineditortitle = parseInt( $('.admin-editor .title').outerHeight() );
	var occupied = adminbar + admineditormargin + admineditortitle;
	var finh = winh - occupied - 24;
	
	ckeditor_inst.resize('100%', finh, false);
}

function editor_init()
{
	var config = {
		extraPlugins: 'codesnippet',
		codeSnippet_theme: 'obsidian'
	};

	$( 'textarea.body' ).ckeditor( config );
	
	CKEDITOR.on('instanceLoaded', function(e)
	{
		ckeditor_inst = e.editor;
		editor_resize();
	});
	
}

$(window).resize(function()
{
	content_update();
});

function content_update()
{
	if ( $( 'textarea.body' ).length > 0 )
	{
		editor_resize();
	}
}

function highlight_init()
{
	$('pre code').each(function(i, block)
	{
		hljs.highlightBlock(block);
	});
}

function mobile_init()
{
	if(is_mobile)
	{
		$("#bar").addClass("smallscreen");
		$("#content").addClass("smallscreen");
	}
}

function autoresize_init()
{
	/*
	makes textareas autoresize when typed inside
	requires autoresize.jquery.js*/
	
	$('.autoresize').autosize();
}

function placeholder_init()
{
	/*
	fix placeholder for ie
	requires jquery.placeholder.min.js
	*/
	$('input[placeholder], textarea[placeholder]').placeholder();
}

function mailcheck_init()
{
	/*suggests fixes for common mail typos
	 requires jquery.mailcheck.min.js*/
	var domains = ['hotmail.com', 'gmail.com', 'hotmail.com.ar', 'yahoo.com', 'yahoo.com.ar', 'copetel.com.ar', 'speedy.com.ar'];
	$('.check-mail').change( function(){
		$(this).mailcheck(domains, {
			suggested: function(element, suggestion) {
				$('#check-mail-target').html('Tal vez quizo decir: <a onclick="fixmail('+"'"+suggestion.full+"'"+');" href="#">'+suggestion.full+'</a>');
				$('#check-mail-target').fadeIn();
			},
			empty: function(element) {
				$('#check-mail-target').fadeOut();
			}
		})
	});
}

function minimap_init()
{
	if(is_mobile)
	{
		return;
	}

	var minimap = $('#minimap');
	/*hides the minimap if window is too small*/
	$(window).resize(function() {

		var w = $(window).width();
		var min = 980;
		var delta_right = 150;
		var delta_width = 100;

		var r = (w/(min)-1);


		//$('#minimap').css('right',(r*delta_right)+'px');

		minimap.css('width',(Math.min(110+r*delta_width,170))+'px');

		if(w < min)
		{
			minimap.fadeOut();
		}
		else
		{
			minimap.fadeIn();
		}
	});

	var timeout_id = -1;
	var mouse_over = false;
	var min_opacity = 0.5;
	var change_opacity = 0.7;
	var full_opacity = 1.0;
	var anim_speed = 500;
	var fadeout_time = 2500;

	minimap.mouseenter( function()
	{
		mouse_over = true;
		minimap.stop().animate({opacity:full_opacity}, anim_speed)
		if(timeout_id != -1)
		{
			clearTimeout(timeout_id);
			timeout_id = -1;
		}
	});

	minimap.mouseleave( function()
		{
			mouse_over = false;
			minimap.stop().animate({opacity:min_opacity}, anim_speed)}
	);

	/*marks the minimap tags if the div is visible*/
	$(window).scroll(function() {


		var oneSelected = false;
		$(".blog_post").each( function() {
			name = $(this).attr('id');
			maptag = $('a[href=#'+name+']');
			if(isScrolledIntoView(this) && !oneSelected)
			{
				oneSelected = true;
				if(!maptag.hasClass('selected'))
				{
					$('.mark',maptag).show();
					maptag.addClass('selected');
					if(!mouse_over)
					{
						minimap.stop().animate({opacity:change_opacity}, anim_speed);
						if(timeout_id != -1)
						{ clearTimeout(timeout_id);	}
						timeout_id = setTimeout(function(){ minimap.stop().animate({opacity:min_opacity}, anim_speed);},fadeout_time);
					}

				}
			}
			else{
				$('.mark',maptag).hide();
				maptag.removeClass('selected');
			}
		})
	});


	$("#minimap").show();
	
	$(window).resize();
}

function fixmail(newmail)
{
	$('.check-mail').val(newmail);
	$('#check-mail-target').fadeOut();
}

function isScrolledIntoView(elem)
{
	var dT = $(window).scrollTop();
	var dB = dT + $(window).height();

	var eT = $(elem).offset().top;
	var eB = eT + $(elem).height();

	var adj = 200;

	return !( eT+adj>=dB  || eB-adj <=dT );
}
