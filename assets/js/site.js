var page_url		= '';
var is_mobile		= screen.width < 1000;
var ckeditor_inst	= null;

function site_init( base_url )
{
	page_url = base_url;
	
	placeholder_init();
}

$(window).resize(function()
{
	content_update();
});

function content_update()
{
	if ( $( 'textarea.body' ).length > 0 && 'function' == typeof editor_resize )
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

function placeholder_init()
{
	/*
	fix placeholder for ie
	requires jquery.placeholder.min.js
	*/
	$('input[placeholder], textarea[placeholder]').placeholder();
}

function minimap_init()
{
	if( is_mobile )
	{
		return;
	}

	var minimap = $('#minimap');
	
	/*hides the minimap if window is too small*/
	$(window).resize(function()
	{
		var w = $(window).width();
		var min = 980;
		var delta_right = 150;
		var delta_width = 100;

		var r = (w/(min)-1);

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

	minimap.mouseenter(function()
	{
		mouse_over = true;
		
		minimap.stop().animate({opacity:full_opacity}, anim_speed);
		
		if(timeout_id != -1)
		{
			clearTimeout(timeout_id);
			timeout_id = -1;
		}
	});

	minimap.mouseleave(function()
	{
		mouse_over = false;
		
		minimap.stop().animate({opacity:min_opacity}, anim_speed)}
	);

	/*marks the minimap tags if the div is visible*/
	$(window).scroll(function()
	{
		var oneSelected = false;
		
		$(".blog_post").each( function()
		{
			name = $(this).attr('id');
			maptag = $('a[href=#'+name+']');
			
			if(scrolled_into_view(this) && !oneSelected)
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
						{
							clearTimeout(timeout_id);
						}
						
						timeout_id = setTimeout(function(){ minimap.stop().animate({opacity:min_opacity}, anim_speed);},fadeout_time);
					}

				}
			}
			else
			{
				$('.mark',maptag).hide();
				
				maptag.removeClass('selected');
			}
		})
	});
	
	$("#minimap").show();
	
	$(window).resize();
}

function scrolled_into_view(elem)
{
	var dT = $(window).scrollTop();
	var dB = dT + $(window).height();

	var eT = $(elem).offset().top;
	var eB = eT + $(elem).height();

	var adj = 200;

	return !( eT+adj>=dB  || eB-adj <=dT );
}
