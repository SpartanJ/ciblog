
<div class="menu">

<ul	id="links">
	<li><a class="ajax-link" href="<?=base_url('/portfolio')?>">portfolio</a></li>
	<li><a class="ajax-link" href="<?=base_url('/nosotros')?>">nosotros</a></li>
	<!--<li><a class="ajax-link" href="<?=base_url('/blog')?>">blog</a></li>-->
	<li><a class="ajax-link" href="<?=base_url('/contacto')?>">contacto</a></li>
	<? if ( isset( $is_admin ) ) { ?>
	<li><a class="ajax-link" href="<?=base_url('/admin')?>">admin</a></li>
	<? } ?>

</ul>

</div>
<!--
<div id="rss"><a href="<?=base_url('/rss')?>">RSS</a></div>
-->
