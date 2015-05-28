<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rss extends MY_Controller
{
	protected function get_web_info()
	{
		return array(	'title' => PAGE_TITLE,
						'description' => PAGE_DESCRIPTION,
						'lang' => PAGE_LANG );
	}
	
	protected function render( $web_info, $items, $image = NULL )
	{
		header('Content-type: application/rss+xml');
		
		$data['web_info']	= $web_info;
		$data['items']		= $items;
		$data['image']		= $image;
		
		$this->load->view( 'feed/rss', $data );
	}
	
	public function index()
	{
		$this->all();
	}
	
	public function all()
	{
		$this->load->model('posts_model');
		
		$winfo	= $this->get_web_info();
		$posts	= $this->posts_model->get_rss();
		$items	= array();
		$image	= array();
		
		$image['url']	= base_url('/assets/images/logo.png');
		$image['title']	= $winfo['title'];
		$image['link']	= base_url('/');
		
		if ( !empty( $posts ) )
		{
			foreach ( $posts as $post )
			{
				$item					= array();
				$item['title']			= $post['post_title'];
				$item['description']	= $post['post_body'];
				$item['link']			= base_url('blog/'.$post['post_slug']);
				$item['guid']			= $item['link'];
				$item['pubDate']		= to_blog_date($post['post_timestamp']);
				
				$items[] = $item;
			}
		}
		
		$this->render( $winfo, $items, $image );
	}
}
