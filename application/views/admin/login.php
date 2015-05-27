<div class="styled-box">

<form class="ajax" action="<?=base_url('/admin/login')?>"  method="post" accept-charset="utf-8">
	<ul class="stylish-form">
		<li><h1 class="robot_icon"><?=lang_line('admin_login')?></h1>
			<p><?=lang_line('admin_login_msg')?></p>
		</li>
		<li><input name="user" type="text" placeholder="<?=lang_line_ucwords('username')?>"/></li>
		<li><input name="pass" type="password" placeholder="<?=lang_line_ucwords('password')?>"/></li>
		<li class="form-error"></li>
		<li><input type="submit" value="<?=lang_line('admin_login_but')?>"/></li>
	</ul>
</form>

</div>
