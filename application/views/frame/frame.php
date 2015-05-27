<!DOCTYPE html>
<html xml:lang="<?=PAGE_LANG?>" lang="<?=PAGE_LANG?>">
<head>
	<title><?=PAGE_TITLE?><?=$page_title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="viewport" content="width=480, user-scalable=no">
	<link rel="shortcut icon" href="<?=base_url('assets/images/favicon.ico')?>" />
	<link rel="icon" type="image/x-icon" href="<?=base_url('assets/images/favicon.ico')?>" />
	<link rel="icon" type="image/png" href="<?=base_url('assets/images/favicon-32x32.png')?>" sizes="32x32">
	<link rel="icon" type="image/png" href="<?=base_url('assets/images/favicon-48x48.png')?>" sizes="48x48">
	<link rel="icon" type="image/png" href="<?=base_url('assets/images/favicon-64x64.png')?>" sizes="64x64">
	<script type='text/javascript'>window.q=[];window.$=function(f){q.push(f)};</script>
<?/*MAGIC #1*/?>
<?=$rss?>
<?=$og?>
<?=$css?>
</head>
<body>
<?if (isset($header)){echo $header;}?>

	<div id="content"><?=$content?></div>

<?if (isset($footer)){echo $footer;}?>

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
