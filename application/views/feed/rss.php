<? echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:georss="http://www.georss.org/georss">
<channel>
	<title><?=(isset($web_info['title']))?$web_info['title']:''?></title>
	<description><?=(isset($web_info['description']))?$web_info['description']:''?></description>
	<link><?=base_url('/')?></link>
	<language><?=(isset($web_info['lang']))?$web_info['lang']:'es'?></language>
	<generator></generator>
	<atom:link href="<?=current_url()?>" rel="self" type="application/rss+xml" />
<? if ( isset( $image ) ) { ?>
<image>
	<?=(isset($image['url']))?'<url>'.$image['url'].'</url>'."\n":''?>
	<?=(isset($image['title']))?'<title>'.$image['title'].'</title>'."\n":''?>
	<?=(isset($image['link']))?'<link>'.$image['link'].'</link>'."\n":''?>
	<?=(isset($image['width']))?'<width>'.$image['width'].'</width>'."\n":''?>
	<?=(isset($image['height']))?'<height>'.$image['height'].'</height>'."\n":''?>
</image>	
<? } ?>
<? foreach( $items as $item ) { ?>
	<item>
		<title><?=$item['title']?></title>
		<description><![CDATA[<?=$item['description']?>]]></description>
		<link><?=$item['link']?></link>
		<guid><?=$item['guid']?></guid>
		<?=(isset($item['author']))?'<author>'.$item['author'].'</author>':''?>
		<?=(isset($item['pubDate']))?'<pubDate>'.$item['pubDate'].'</pubDate>':''?>
	</item>
<? } ?>
</channel>
</rss>
