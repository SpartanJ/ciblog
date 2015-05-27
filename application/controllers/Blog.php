<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Blog extends MY_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->model('post_model');
	}

	protected function auto_add()
	{
		parent::auto_add();
	}

	public function index($categ = 'BLOG')
	{
		$data['posts'] = $this->post_model->get_published($categ);
		$data['show_date'] = false;

		switch($categ)
		{
			case 'START':		$data['page_title'] = lang_line('about_us');	break;
			case 'BLOG': 		$data['page_title'] = lang_line('blog');		break;
			case 'PORTFOLIO': 	$data['page_title'] = lang_line('portfolio');	break;
		}
		
		$this->add_frame_view('blog/posts',$data);
	}
	
	public function portfolio()
	{
		$this->index('PORTFOLIO');
	}

	public function show($slug)
	{
		$post = $this->post_model->get_slug($slug);

		if($post)
		{
			$data['posts'] = array($post);
			$data['show_date'] = true;
			$data['page_title'] = $post['title'];
			
			$this->add_frame_view('blog/posts',$data);
		}
		else
		{
			$this->error_404();
		}
	}
}
