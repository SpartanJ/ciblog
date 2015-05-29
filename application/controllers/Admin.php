<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends SESSION_Controller
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

	protected function slug_get_temp( $original_slug, &$c )
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
		
		$c++;
		
		return $original_slug . $slug_ext;
	}
	
	protected function slug_create( $title, $id = NULL )
	{
		$slug	= CiblogHelper::slugify( $title );
		$rslug	= $slug;
		$c		= 0;
		
		// if already exists find a new one
		if ( NULL != $id )
		{
			if ( $this->posts_model->slug_exists_and_not_me( $slug, $id ) )
			{
				do
				{
					$slug = $this->slug_get_temp( $rslug, $c );
				} while ( $this->posts_model->slug_exists_and_not_me( $slug, $id ) );
			}
		}
		else
		{
			if ( $this->posts_model->slug_exists( $slug ) )
			{
				do
				{
					$slug = $this->slug_get_temp( $rslug, $c );
				} while ( $this->posts_model->slug_exists( $slug ) );
			}
		}
		
		return $slug;
	}

	public function save()
	{
		$this->admin_session_restrict();
		
		$data			=  $this->input->post();
		$data['draft']	= isset($data['draft']) && $data['draft'] ? '1' : '0';
		
		if( isset( $data['post_id'] ) )
		{
			$id			= $data['post_id'];
			$slug		= $this->slug_create( $data['title'], $id );
			
			$this->posts_model->update( $data, $slug );
		}
		else
		{
			$added		= TRUE;
			$slug		= $this->slug_create( $data['title'] );
			$id			= $this->posts_model->add( $data, $slug, $this->user->user_id );
		}
		
		if ( $this->is_kajax_request() )
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

	public function edit( $id = NULL )
	{
		$this->admin_session_restrict();
		
		if( $id != NULL )
		{
			$this->load->model('Categories_model');
			
			$data				= $this->posts_model->get($id);
			$data['categories']	= $this->Categories_model->get_all();
			
			$this->add_frame_view('admin/edit_post',$data);
		}
	}

	public function add()
	{
		$this->admin_session_restrict();

		$this->load->model('Categories_model');
		
		$data['categories'] = $this->Categories_model->get_all();
		
		$this->add_frame_view('admin/edit_post',$data);
	}
	
	public function delete($id)
	{
		$this->admin_session_restrict();
	
		$this->posts_model->delete($id);
		
		redirect(base_url('/admin'));
	}
	
	public function login()
	{
		$post = $this->input->post();
		
		if ( !empty( $post ) && isset( $post['user'] ) )
		{
			$admin = $this->Users_model->get_admin_user( $post['user'], $post['pass'] );
			
			if( isset( $admin ) )
			{
				$this->session_create( $post['user'], $post['pass'], isset( $post['remember_me'] ) );
				
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
			if ( $this->admin_is_logged() )
			{
				redirect(base_url('/admin'));
			}
			else
			{
				$this->add_frame_view('admin/login', NULL, FALSE);
			}
		}
	}
	
	public function logout()
	{
		$this->session_destroy();
		
		$this->redirect(base_url('/admin/login'));
	}
	
	protected function admin_posts()
	{
		$data['drafts']		= $this->posts_model->get_drafts();
		
		$data['published']	= $this->posts_model->get_published();
		
		$this->add_frame_view('admin/list',$data);
	}
	
	public function posts()
	{
		$this->admin_session_restrict();

		$this->admin_posts();
	}
	
	public function categories()
	{
		$this->admin_session_restrict();
	}
	
	public function users()
	{
		$this->admin_session_restrict();
	}

	public function index()
	{
		$this->posts();
	}
	
	protected function add_frame_view( $content_view, $data = array(), $show_header = TRUE, $show_footer = TRUE, $return = FALSE, $hf_folder = 'admin' )
	{
		parent::add_frame_view( $content_view, $data, $show_header, $show_footer, $return, $hf_folder );
	}
	
	public function filemanager()
	{
		require_once( ROOTPATH . 'fm/connectors/php/filemanager.class.php');
		
		$_SERVER['DOCUMENT_ROOT'] = substr( ROOTPATH, 0, strlen( ROOTPATH ) - 1 );
		
		$fm = new Filemanager();
		
		$response = '';

		if(!$this->admin_is_logged()) {
		  $fm->error($fm->lang('AUTHORIZATION_REQUIRED'));
		}

		if(!isset($_GET)) {
		  $fm->error($fm->lang('INVALID_ACTION'));
		} else {

		  if(isset($_GET['mode']) && $_GET['mode']!='') {

			switch($_GET['mode']) {
				
			  default:

				$fm->error($fm->lang('MODE_ERROR'));
				break;

			  case 'getinfo':

				if($fm->getvar('path')) {
				  $response = $fm->getinfo();
				}
				break;

			  case 'getfolder':
					
				if($fm->getvar('path')) {
				  $response = $fm->getfolder();
				}
				break;

			  case 'rename':

				if($fm->getvar('old') && $fm->getvar('new')) {
				  $response = $fm->rename();
				}
				break;

			  case 'move':
				// allow "../"
				if($fm->getvar('old') && $fm->getvar('new') && $fm->getvar('root')) {
				  $response = $fm->move();
				}
				break;

			  case 'editfile':
					 
				if($fm->getvar('path')) {
					$response = $fm->editfile();
				}
				break;
				
			  case 'delete':

				if($fm->getvar('path')) {
				  $response = $fm->delete();
				}
				break;

			  case 'addfolder':

				if($fm->getvar('path') && $fm->getvar('name')) {
				  $response = $fm->addfolder();
				}
				break;

			  case 'download':
				if($fm->getvar('path')) {
				  $fm->download();
				}
				break;
				
			  case 'preview':
				if($fm->getvar('path')) {
					if(isset($_GET['thumbnail'])) {
						$thumbnail = true;
					} else {
						$thumbnail = false;
					}
				  $fm->preview($thumbnail);
				}
				break;
			}

		  } else if(isset($_POST['mode']) && $_POST['mode']!='') {

			switch($_POST['mode']) {
				
			  default:

				$fm->error($fm->lang('MODE_ERROR'));
				break;
					
			  case 'add':

				if($fm->postvar('currentpath')) {
				  $fm->add();
				}
				break;

				case 'replace':
			
					if($fm->postvar('newfilepath')) {
						$fm->replace();
					}
					break;
			
				case 'savefile':
					
					if($fm->postvar('content', false) && $fm->postvar('path')) {
						$response = $fm->savefile();
					}
					break;
			}

		  }
		}

		echo json_encode($response);
		die();
	}
}
