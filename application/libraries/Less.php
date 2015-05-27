<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once(APPPATH.'third_party/lesscss.inc.php');


class Less
{
	public function parse($file)
	{
		$less = new Lesscss($file,'assets/cache');
		return $less->get_url();
	}
}