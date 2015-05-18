<?php
include('leafo-lessphp/lessc.inc.php');
require_once('cacheablephp.inc.php');

class Lesscss extends CacheablePHP
{
	private $import_dir = '';
	
	public function __construct($less_file, $cache_dir)
	{ 
		$pi = pathinfo($less_file);
		$this->import_dir = $pi['dirname'].'/';
		parent::__construct($less_file, '.css', $cache_dir);
	}


	protected function process($buf_in)
	{
		try {
				$less = new lessc();
				$less->importDir = $this->import_dir;
				$less_body = $less->parse($buf_in);
			} catch (exception $ex) {
				exit('lessc fatal error:<br />'.$ex->getMessage());
			}
		return $less_body;
	}
}