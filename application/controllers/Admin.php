<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('post_model');
	}

	protected function auto_add()
	{
		parent::auto_add();
		
		$this->add_css('assets/css/admin.css');
	}
	
	private function get_slug($str) {
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
		return $clean;
	}

	public function delete($id)
	{
		if ( $this->session_check() )
		{
			$this->post_model->delete($id);
			redirect(base_url('/admin'));
		}
	}

	public function save()
	{
		if ( $this->session_check() )
		{
			$data =  $this->input->post();
			$data['draft'] = isset($data['draft']) && $data['draft'] ? '1' : '0';
			$slug = $this->get_slug($data['title']);

			if(isset($data['post_id']))
			{
				$id = $data['post_id'];
				$this->post_model->update($data,$slug);
			}
			else
			{
				$added		= TRUE;
				$admin_id	= $this->sess['id'];
				$id			= $this->post_model->add($data,$slug, $admin_id);
			}
			
			if ( $this->input->is_ajax_request() )
			{
				$this->kajax->script("$('#save-success-msg').fadeIn(500).delay(500).fadeOut(500);");
				
				if ( isset( $added ) )
				{
					$bdata = $this->post_model->get($id);
					$bdata['post_id']			= $id;
					$bdata['only_admin_bar'] 	= TRUE;
					
					$view_data = $this->load->view( 'admin/edit_post', $bdata, TRUE );
					
					$this->kajax->html_safe( '#admin-bar', $view_data );
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
				$data = $this->post_model->get($id);
				$this->add_frame_view('admin/edit_post',$data);
			}
		}
	}

	public function add()
	{
		if ( $this->session_check() )
		{
			$this->add_frame_view('admin/edit_post');
		}
	}

	public function login()
	{
		$post = $this->input->post();
		
		if ( !empty( $post ) )
		{
			if( $this->create_session( $post['user'], $post['pass'] ) )
			{
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
			$data['drafts'] = $this->post_model->get_drafts();
			$data['published'] = $this->post_model->get_published();
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
