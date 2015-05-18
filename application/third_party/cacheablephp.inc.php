<?php
require_once('cacheable.inc.php');

abstract class CacheablePHP extends Cacheable
{

	protected function readfile($filein)
	{
		ob_start();
		include $filein;
		return ob_get_clean();
	}
}
