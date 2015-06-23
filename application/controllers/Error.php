<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('Blog.php');

class Error extends Blog
{
	public function index($empty='')
	{
		$this->load->model('Posts_model');
		$this->load->model('Categories_model');
		
		$slug = $this->uri->segment(1);
		
		if ( $this->Posts_model->slug_exists($slug) )
		{
			$this->show($slug);
		}
		else if ( $this->Categories_model->name_exists($slug) )
		{
			
			parent::index($slug);
		}
		else
		{
			$this->error_404();
		}
	}
}
