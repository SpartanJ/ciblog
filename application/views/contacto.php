<div id="contact-box" class="styled-box">

<form class="ajax" id="form-contact" action="<?=base_url('/contacto/mail')?>" method="post" accept-charset="utf-8">
<ul class="stylish-form">
	<li>
		<h1 class="mail_icon">CONTACTANOS</h1>
		<p>Escribe tu consulta a continuación, te contactaremos antes de lo que esperas.</p>
	</li>
	<li><input name="name" type="text" placeholder="Nombre"/></li>
	<li><input name="mail" class="check-mail" type="text" placeholder="Email"/>
		<p id="check-mail-target"></p>
	</li>
	<li><textarea name="message" class="autoresize" placeholder="Escribí tu consulta acá"></textarea></li>
	
	<li class="form-error"></li>
	
	<li><input type="submit" value="ENVIAR CONSULTA"/></li>
</ul>
</form>

</div>


<script>
	$( function(){
		mailcheck_init();
	});
</script>
