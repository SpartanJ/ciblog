<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Blog extends MY_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->model('Posts_model');
		$this->load->model('Categories_model');
	}

	protected function auto_add()
	{
		parent::auto_add();
	}

	public function index($categ = 'blog')
	{
		$category				= $this->Categories_model->get_by_key( $categ );
		$data['posts']			= $this->Posts_model->get_published_by_category_key($categ, get_var('author'));
		$data['display_info']	= TRUE;
		
		if ( isset( $category ) )
		{
			$data['page_title'] 	= lang_line_category_name( $category['cat_name'] );
			$data['display_info']	= intval( $category['cat_display_info'] ) != 0;
		}
		
		$this->add_frame_view('blog/posts',$data);
	}
	
	public function show($slug)
	{
		$post = $this->Posts_model->get_slug($slug);

		if( isset( $post ) )
		{
			$data['posts']			= array($post);
			$data['display_info']	= intval( $post['cat_display_info'] ) != 0;
			$data['page_title']		= $post['post_title'];
			
			$this->add_frame_view('blog/posts',$data);
		}
		else
		{
			$this->error_404();
		}
	}
}
