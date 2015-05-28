<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('Blog.php');

class Error extends Blog
{
	public function index($empty='')
	{
		$this->show($this->uri->segment(1));
	}
}
