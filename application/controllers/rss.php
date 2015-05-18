<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'third_party/markdown.php');

class Rss extends MY_Controller {
	protected function get_web_info()
	{
		return array(	'title' => 'ensoft',
						'description' => 'Sitio de ensoft',
						'lang' => 'es' );
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
		$this->load->model('post_model');
		
		$winfo	= $this->get_web_info();
		$posts	= $this->post_model->get_rss();
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
				$item['title']			= $post['title'];
				$item['description']	= $post['body'];
				$item['link']			= base_url('blog/'.$post['slug']);
				$item['guid']			= $item['link'];
				$item['pubDate']		= toBlogDate($post['timestamp']);
				
				$items[] = $item;
			}
		}
		
		$this->render( $winfo, $items, $image );
	}
}
