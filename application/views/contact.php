<div id="contact-box" class="styled-box">
<script src='https://www.google.com/recaptcha/api.js'></script>

<form class="ajax" id="form-contact" action="<?=base_url('/contact/mail')?>" method="post" accept-charset="utf-8">
<ul class="stylish-form">
	<li>
		<div>
			<h1 class="mail_icon"><?=lang_line_upper('contact_us')?></h1>
		</div>
		<p><?=lang_line('contact_form_txt')?></p>
	</li>
	<li><input name="name" type="text" placeholder="<?=lang_line_ucwords('name')?>"/></li>
	<li><input name="mail" type="text" placeholder="<?=lang_line_ucwords('email')?>"/></li>
	<li><textarea name="message" class="autoresize" placeholder="<?=lang_line_ucwords('message')?>"></textarea></li>
	<li><div class="g-recaptcha" data-sitekey="<?=CIBLOG_RECAPTCHA_SITE_KEY?>"></div></li>
	
	<li class="form-error"></li>
	
	<li><input type="submit" value="<?=lang_line_upper('send_email')?>"/></li>
</ul>
</form>

</div>
