<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('posts_model');
	}

	protected function auto_add()
	{
		parent::auto_add();
		
		$this->add_css('assets/css/admin.css');
	}
	
	public function delete($id)
	{
		if ( $this->session_check() )
		{
			$this->posts_model->delete($id);
			redirect(base_url('/admin'));
		}
	}
	
	protected function slug_create( $id, $title )
	{
		$slug = CiblogHelper::slugify( $title );
		
		// if already exists find a new one
		if ( $this->posts_model->slug_exists_and_not_me( $slug, $id ) )
		{
			$rslug = $slug;
			$c = 0;
			
			do
			{
				// try with the date
				if ( 0 == $c )
				{
					$slug_ext = '-' . date( 'Y-m-d', time() );
				}
				else
				{
					// just count
					$slug_ext = $c;
				}
					
				$slug = $rslug . $slug_ext;
				
				$c++; 
			} while ( $this->posts_model->slug_exists_and_not_me( $slug, $id ) );
		}
		
		return $slug;
	}

	public function save()
	{
		if ( $this->session_check() )
		{
			$data			=  $this->input->post();
			$data['draft']	= isset($data['draft']) && $data['draft'] ? '1' : '0';
			
			if( isset( $data['post_id'] ) )
			{
				$id			= $data['post_id'];
				$slug		= $this->slug_create( $id, $data['title'] );
				
				$this->posts_model->update($data,$slug);
			}
			else
			{
				$added		= TRUE;
				$admin_id	= $this->sess['id'];
				$id			= $this->posts_model->add($data,$slug, $admin_id);
			}
			
			if ( $this->input->is_ajax_request() )
			{
				$this->kajax->script("$('#save-success-msg').fadeIn(500).delay(500).fadeOut(500);");
				
				if ( isset( $added ) )
				{
					$bdata = $this->posts_model->get($id);
					$bdata['post_id']			= $id;
					$bdata['only_admin_bar'] 	= TRUE;
					
					$view_data = $this->load->view( 'admin/edit_post', $bdata, TRUE );
					
					$this->kajax->html( '#admin-bar', $view_data );
				}
				else
				{
					$this->kajax->href('#preview_slug', base_url('blog/'.$slug) );
				}
				
				$this->kajax->out();
			}
			else
			{
				redirect(base_url('/admin/edit/'.$id));
			}
		}
	}

	public function edit($id = null)
	{
		if ( $this->session_check() )
		{
			if($id != null)
			{
				$this->load->model('Categories_model');
				
				$data = $this->posts_model->get($id);
				$data['categories'] = $this->Categories_model->get_all();
				
				$this->add_frame_view('admin/edit_post',$data);
			}
		}
	}

	public function add()
	{
		if ( $this->session_check() )
		{
			$this->load->model('Categories_model');
			$data['categories'] = $this->Categories_model->get_all();
			
			$this->add_frame_view('admin/edit_post',$data);
		}
	}

	public function login()
	{
		$post = $this->input->post();
		
		if ( !empty( $post ) )
		{
			$admin = $this->Users_model->get_admin_user( $post['user'], $post['pass'] );
			
			if( isset( $admin ) )
			{
				$this->create_session( $post['user'], $post['pass'] );
				
				$this->kajax->redirect(base_url('/admin'));
			}
			else
			{
				$this->kajax->fadeIn('.form-error',500);
				$this->kajax->html( '.form-error', '<p>' . $this->lang->line('user_pass_incorrect') . '</p>' );
			}
			
			$this->kajax->out();
		}
		else
		{
			if ( $this->session_exists() )
			{
				redirect(base_url('/admin'));
			}
			else
			{
				$this->add_frame_view('admin/login');
			}
		}
	}
	
	public function logout()
	{
		$this->session->sess_destroy();
		
		if ( $this->is_kajax_request() )
		{
			$this->add_frame_view('admin/login');
		}
		else
		{
			redirect(base_url('/admin/login'));
		}
	}

	public function index()
	{
		if ( $this->session_exists() )
		{
			$data['drafts'] = $this->posts_model->get_drafts();
			$data['published'] = $this->posts_model->get_published();
			$this->add_frame_view('admin/list',$data);
		}
		else
		{
			redirect(base_url('/admin/login'));
		}
	}
	
	protected function session_check()
	{
		if ( $this->session_exists() )
		{
			return TRUE;
		}
		
		if ( $this->input->is_ajax_request() )
		{
			$this->kajax->redirect(base_url('/admin/login'));
			$this->kajax->out();
		}
		else
		{
			redirect(base_url('/admin/login'));
		}
	}
}
