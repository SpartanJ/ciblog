<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH.'third_party/markdown.php');

class Admin extends MY_Controller {
	protected $sess			= NULL;
	protected $thumb_width	= 500;
	protected $thumb_height	= 500;

	function __construct()
	{
		parent::__construct();
		
		$this->load->model('post_model');
	}

	public function upload()
	{
		if ( $this->session_check() )
		{
			$config['upload_path']		= 'assets/blog/';
			$config['allowed_types']	= 'gif|jpg|png';
			$config['encrypt_name']		= FALSE;
			
			$this->load->library( 'upload', $config );

			$form = $this->input->post();
			
			if ( ! $this->upload->do_upload('uploadfile') )
			{
				$ret = array('ok'=>false,'error'=>'error al enviar la imagen, chequee que sea un imagen válida');
			}
			else
			{
				$data = array( 'upload_data' => $this->upload->data() );

				$data = $this->upload->data();
				
				$this->load->library('image_lib');
				
				$thumb = 0;
				try
				{
					if ( $data['image_width'] > $this->thumb_width || $data['image_height'] > $this->thumb_height )
					{
						$thumb							= TRUE;
						$config_il['maintain_ratio']	= TRUE;
						$config_il['width']				= $this->thumb_width;
						$config_il['height']			= $this->thumb_height;
						$config_il['source_image']		= $data['full_path'];
						$file_path_real					= $config['upload_path'] . 'thumbs/' . $data['file_name'];
						$config_il['new_image']			= $file_path_real;

						$this->load->library('Images', $config);
						$this->images->resize( $data['full_path'], $file_path_real, $this->thumb_width, $this->thumb_height );

						// Creates a jpg thumnail from the png thumbnail, and it keeps the png thumbnail alive ( so you can choose )
						if ( 'png' == $data['image_type'] )
						{
							$config_il['source_image']		= $file_path_real;
							$this->image_lib->initialize($config_il);
							$this->image_lib->convert( 'jpg', FALSE, FALSE );
						}
					}
					$ret = array('ok'=>true,'thumb'=>$thumb,'filename'=>$data['file_name']);

				}
				catch(Exception $e)
				{
					$ret = array('ok'=>false,'error'=>$e->getMessage);
				}
			}
			echo  json_encode($ret);
		}
	}

	protected function auto_add()
	{
		parent::auto_add();
		$this->addjs('assets/js/jquery.filedrop.js');
		$this->addjs('assets/js/admin.js');
	}

	private function genSlug($str) {
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
			$slug = $this->genSlug($data['title']);

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

		if ( is_array( $post ) )
		{
			if( $this->create_session( $post['user'], $post['pass'] ) )
			{
				$this->kajax->redirect(base_url('/admin'));
			}
			else
			{
				$this->kajax->fadeIn('.form-error',500);
				$this->kajax->html( '.form-error', '<p>Usuario y/o contrase&ntilde;a incorrectos.</p>' );
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
		
		if ( $this->isKajaxRequest() )
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
