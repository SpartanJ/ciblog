<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends SESSION_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Posts_model');
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
			if ( $this->Posts_model->slug_exists_and_not_me( $slug, $id ) )
			{
				do
				{
					$slug = $this->slug_get_temp( $rslug, $c );
				} while ( $this->Posts_model->slug_exists_and_not_me( $slug, $id ) );
			}
		}
		else
		{
			if ( $this->Posts_model->slug_exists( $slug ) )
			{
				do
				{
					$slug = $this->slug_get_temp( $rslug, $c );
				} while ( $this->Posts_model->slug_exists( $slug ) );
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
			
			$this->Posts_model->update( $data, $slug );
		}
		else
		{
			$added		= TRUE;
			$slug		= $this->slug_create( $data['title'] );
			$id			= $this->Posts_model->add( $data, $slug, $this->user->user_id );
		}
		
		if ( $this->is_kajax_request() )
		{
			$this->kajax->script("$('#save-success-msg').fadeIn(500).delay(500).fadeOut(500);");
			
			if ( isset( $added ) )
			{
				$bdata = $this->Posts_model->get($id);
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
			
			$data				= $this->Posts_model->get($id);
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
	
		$this->Posts_model->delete($id);
		
		$this->kajax->load_target(base_url('/admin'));
		$this->kajax->out();
	}
	
	public function draft_it( $id )
	{
		$this->admin_session_restrict();
		
		$this->Posts_model->draft_it($id);
		
		$this->kajax->load_target(base_url('/admin'));
		$this->kajax->out();
	}
	
	public function publish_it( $id )
	{
		$this->admin_session_restrict();
	
		$this->Posts_model->publish_it($id);
		
		$this->kajax->load_target(base_url('/admin'));
		$this->kajax->out();
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
				
				$this->Users_model->update_last_login( $admin->user_id );
				
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
	
	protected function build_filters()
	{
		$filter = array(
			array(
				'field_name'	=>	'cat_id',
				'filter_val'	=>	get_var( 'cat_id' )
			),
			array(
				'field_name'	=>	'user_id',
				'filter_val'	=>	get_var( 'user_id' )
			),
			array(
				'field_name'	=>	'post_title',
				'filter_val'	=>	get_var( 'post_title' ),
				'filter_type'	=>	SQLFilterType::ILIKE
			),
			array(
				'field_name'	=>	'post_draft',
				'filter_val'	=>	get_var( 'post_draft' )
			),
			array(
				'order_by'		=> get_var_def( 'order_by', 'post_created' ),
				'order_fields'	=> array( 'post_id', 'post_created', 'post_updated', 'cat_key' ),
				'order_dir'		=> get_var_def( 'order_dir', 'DESC' )
			)
		);
		
		return SQL::build_query_filter( $filter );
	}
	
	public function posts()
	{
		$this->admin_session_restrict();

		$this->load->library('pagination');
		$this->load->model('Categories_model');
		
		$page					= get_var_def( 'page_num', 1 );
		$config					= pagination_config();
		$query_filter			= $this->build_filters();
		$config['total_rows']	= $data['posts_count']	= $this->Posts_model->count( NULL, $query_filter );
		$data['posts']			= $this->Posts_model->get_all( NULL, $query_filter, $config['per_page'], $page );
		$config['base_url']		= base_url( '/admin/posts/?' . http_build_query_pagination() );
		$data['stats']			= $this->Posts_model->get_counts();
		$data['categories']		= $this->Categories_model->get_all();
		$data['post_draft']		= get_var('post_draft');
		$data['user_id']		= get_var('user_id');
		$data['cat_id']			= get_var('cat_id');
		
		$this->pagination->initialize($config);
		
		$data['pagination']		= $this->pagination->create_links();
		
		$this->add_frame_view( 'admin/list', $data );
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
