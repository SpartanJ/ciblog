<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Blog extends MY_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->model('posts_model');
		$this->load->model('Categories_model');
	}

	protected function auto_add()
	{
		parent::auto_add();
	}

	public function index($categ = 'blog')
	{
		$category			= $this->Categories_model->get_by_key( $categ );
		$data['posts']		= $this->posts_model->get_published_by_category_key($categ);
		$data['show_date']	= true;
		
		if ( isset( $category ) )
		{
			$data['page_title'] = lang_line_category_name( $category['cat_name'] );
			$data['show_date']	= intval( $category['cat_show_dates'] ) != 0;
		}
		
		$this->add_frame_view('blog/posts',$data);
	}
	
	public function show($slug)
	{
		$post = $this->posts_model->get_slug($slug);

		if( isset( $post ) )
		{
			$data['posts']		= array($post);
			$data['show_date']	= intval( $post['cat_show_dates'] ) != 0;
			$data['page_title']	= $post['post_title'];
			
			$this->add_frame_view('blog/posts',$data);
		}
		else
		{
			$this->error_404();
		}
	}
}
