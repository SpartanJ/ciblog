<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<title>ensoft<?=$page_title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="viewport" content="width=480, user-scalable=no">
	<link rel="shortcut icon" href="<?=base_url('assets/images/favicon.ico')?>" />
	<link rel="icon" type="image/x-icon" href="<?=base_url('assets/images/favicon.ico')?>" />
	<link rel="icon" type="image/png" href="<?=base_url('assets/images/favicon-32x32.png')?>" sizes="32x32">
	<link rel="icon" type="image/png" href="<?=base_url('assets/images/favicon-48x48.png')?>" sizes="48x48">
	<link rel="icon" type="image/png" href="<?=base_url('assets/images/favicon-64x64.png')?>" sizes="64x64">
	<script type='text/javascript'>window.q=[];window.$=function(f){q.push(f)};</script>
<?=$rss?>
<?/*MAGIC #1*/?>
<?=$css?>
</head>
<body>
	<div id="content"><?=$content?></div>
	<div id="bar"><?=$bar?></div>
<?=$js /*newest optimizationr recomendations, states that is best to load js at the end, so we do.*/?>
<script type="text/javascript">$.each(q,function(i,f){$(f)});</script><?/*MAGIC #2*/?>
<?/*
    About magic JS:

    #1 captures all the references to Jquery domready "$( function(){ ... } )" and pushes them to an array.
    #2 then executes all functions, after jquery is loaded

    this hack allows late jquery loading, and faster DOM loading.

    ref: http://samsaffron.com/archive/2012/02/17/stop-paying-your-jquery-tax
*/?>

<script type='text/javascript'>
	$(function()
	{
		if ( typeof site_init == 'function' )
			site_init( '<?=base_url()?>' );
	});
</script>
</body>
</html>
