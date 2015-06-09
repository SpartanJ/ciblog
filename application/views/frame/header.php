
<div id="header" class="bar bar-top">
	<div class="menu">
		<ul	class="links">
			<? if ( isset( $sections ) ) { foreach( $sections as $s ) { ?>
			<li><a class="ajax-link" href="<?=base_url('/' . $s['slug'] )?>"><?=lang_line( $s['title'] )?></a></li>
			<? }} ?>
			
			<li><a class="ajax-link" href="<?=base_url('/contact')?>"><?=lang_line('contact')?></a></li>
		</ul>
	</div>
</div>
