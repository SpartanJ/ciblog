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
	
	protected function build_filters( $category, $tag = NULL )
	{
		$filter = array(
			array(
				'field_name'	=> 'cat_key',
				'filter_val'	=> $category
			),
			array(
				'field_name'	=> 'post_author',
				'filter_val'	=> get_var( 'author' )
			),
			array(
				'field_name'	=> 'post_draft',
				'filter_val'	=> 0,
				'field_type'	=> SQLFieldType::INT
			),
			array(
				'order_by'		=> get_var_def( 'order_by', 'post_created' ),
				'order_fields'	=> array( 'post_id', 'post_created', 'post_updated', 'cat_key' ),
				'order_dir'		=> get_var_def( 'order_dir', 'DESC' )
			)
		);
		
		if ( NULL != $tag )
		{
			$join = array(
				array(
					'field_name'	=> 'ptag_name',
					'filter_val'	=> $tag
				), 
				array(
					'join'			=> 'post_tags',
					'on'			=> 'ptag_post_id = post_id'
				)
			);
			
			$filter = array_merge( $join, $filter );
		}
		
		return SQL::build_query_filter( $filter );
	}

	public function index($categ = 'blog')
	{
		$this->load->library('pagination');
		
		$page						= get_var_def( 'page_num', 1 );
		$config						= pagination_config( 5, TRUE );
		$query_filter				= $this->build_filters( $categ );
		$config['total_rows']		= $data['posts_count']	= $this->Posts_model->count( NULL, $query_filter );
		$data['posts']				= $this->Posts_model->get_all(NULL, $query_filter, $config['per_page'], $page, '*');
		$config['base_url']			= base_url( '/' . $categ . '/?' . http_build_query_pagination() );
		$data['display_info']		= TRUE;
		$category					= $this->Categories_model->get_by_key( $categ );
		
		if ( isset( $category ) )
		{
			$data['page_title'] 	= lang_line_category_name( $category['cat_name'] );
			$data['display_info']	= intval( $category['cat_display_info'] ) != 0;
		}
		
		$this->pagination->initialize($config);
		
		$data['pagination']		= $this->pagination->create_links();
		
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
	
	public function tag($tag)
	{
		$this->load->library('pagination');
		
		$page						= get_var_def( 'page_num', 1 );
		$config						= pagination_config( 5, TRUE );
		$query_filter				= $this->build_filters( NULL, urldecode( $tag ) );
		$config['total_rows']		= $data['posts_count']	= $this->Posts_model->count( NULL, $query_filter );
		$data['posts']				= $this->Posts_model->get_all(NULL, $query_filter, $config['per_page'], $page, '*');
		$config['base_url']			= base_url( '/tag/' . $tag . '/?' . http_build_query_pagination() );
		$data['display_info']		= TRUE;
		
		$this->pagination->initialize($config);
		
		$data['pagination']		= $this->pagination->create_links();
		
		$this->add_frame_view('blog/posts',$data);
	}
}
