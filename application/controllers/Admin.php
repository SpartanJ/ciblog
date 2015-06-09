<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends USER_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Posts_model');
	}
	
	protected function add_base_css()
	{
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
		$this->session_restrict( CIBLOG_AUTHOR_LEVEL );
		
		$data			=  $this->input->post();
		$data['draft']	= isset($data['draft']) && $data['draft'] ? '1' : '0';
		
		if( isset( $data['post_id'] ) )
		{
			$post = $this->Posts_model->get( $data['post_id'] );
			
			if ( $this->user->user_level >= CIBLOG_EDITOR_LEVEL || ( $this->user->user_level >= CIBLOG_AUTHOR_LEVEL && $post['post_author'] == $this->user->user_id ) )
			{
				$id			= $data['post_id'];
				$slug		= $this->slug_create( $data['title'], $id );
				
				$this->Posts_model->update( $data, $slug );
			}
			else
			{
				return;
			}
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
				
				$view_data = $this->load->view( 'admin/post_edit', $bdata, TRUE );
				
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
		$this->session_restrict( CIBLOG_AUTHOR_LEVEL );
		
		if( $id != NULL )
		{
			$this->load->model('Categories_model');
			
			$data				= $this->Posts_model->get($id);
			
			if ( $this->user->user_level >= CIBLOG_EDITOR_LEVEL || ( $this->user->user_level >= CIBLOG_AUTHOR_LEVEL && $data['post_author'] == $this->user->user_id ) )
			{
				$data['categories']	= $this->Categories_model->get_all();
				
				$this->add_frame_view('admin/post_edit',$data);
			}
		}
	}

	public function add()
	{
		$this->session_restrict( CIBLOG_AUTHOR_LEVEL );

		$this->load->model('Categories_model');
		
		$data['categories'] = $this->Categories_model->get_all();
		
		$this->add_frame_view('admin/post_edit',$data);
	}
	
	public function delete($id)
	{
		$this->session_restrict( CIBLOG_AUTHOR_LEVEL );
		
		$post = $this->Posts_model->get( $id );
		
		if ( $this->user->user_level >= CIBLOG_EDITOR_LEVEL || ( $this->user->user_level >= CIBLOG_AUTHOR_LEVEL && $post['post_author'] == $this->user->user_id ) )
		{
			$this->Posts_model->delete($id);
		
			$this->kajax->load_target(base_url('/admin/posts'));
			$this->kajax->out();
		}
	}
	
	public function draft_it( $id )
	{
		$this->session_restrict( CIBLOG_AUTHOR_LEVEL );
		
		$post = $this->Posts_model->get( $id );
		
		if ( $this->user->user_level >= CIBLOG_EDITOR_LEVEL || ( $this->user->user_level >= CIBLOG_AUTHOR_LEVEL && $post['post_author'] == $this->user->user_id ) )
		{
			$this->Posts_model->draft_it($id);
			
			$this->kajax->load_target(base_url('/admin/posts'));
			$this->kajax->out();
		}
	}
	
	public function publish_it( $id )
	{
		$this->session_restrict( CIBLOG_AUTHOR_LEVEL );
		
		$post = $this->Posts_model->get( $id );
		
		if ( $this->user->user_level >= CIBLOG_EDITOR_LEVEL || ( $this->user->user_level >= CIBLOG_AUTHOR_LEVEL && $post['post_author'] == $this->user->user_id ) )
		{
			$this->Posts_model->publish_it($id);
		
			$this->kajax->load_target(base_url('/admin/posts'));
			$this->kajax->out();
		}
	}
	
	public function logout()
	{
		$this->session_destroy();
		
		$this->redirect(base_url('/admin/login'));
	}
	
	protected function build_posts_filters()
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
		$this->session_restrict( CIBLOG_AUTHOR_LEVEL );

		$this->load->library('pagination');
		$this->load->model('Categories_model');
		
		$page						= get_var_def( 'page_num', 1 );
		$config						= pagination_config();
		$query_filter				= $this->build_posts_filters();
		$config['total_rows']		= $data['posts_count']	= $this->Posts_model->count( NULL, $query_filter );
		$data['posts']				= $this->Posts_model->get_all( NULL, $query_filter, $config['per_page'], $page );
		$config['base_url']			= base_url( '/admin/posts/?' . http_build_query_pagination() );
		$data['stats']				= $this->Posts_model->get_counts();
		$data['categories']			= $this->Categories_model->get_all();
		$data['post_draft']			= get_var('post_draft');
		$data['user_id']			= get_var('user_id');
		$data['cat_id']				= get_var('cat_id');
		$data['post_title']			= get_var('post_title');
		$data['user']				= $this->user;
		
		$this->pagination->initialize($config);
		
		$data['pagination']		= $this->pagination->create_links();
		
		$this->add_frame_view( 'admin/posts', $data );
	}
	
	protected function build_categories_filters()
	{
		$filter = array(
			array(
				'field_name'	=>	'cat_key',
				'filter_val'	=>	get_var( 'cat_key' )
			),
			array(
				'order_by'		=> get_var_def( 'order_by', 'cat_name' ),
				'order_fields'	=> array( 'cat_id', 'cat_key', 'cat_name' ),
				'order_dir'		=> get_var_def( 'order_dir', 'ASC' )
			)
		);
		
		return SQL::build_query_filter( $filter );
	}
	
	public function categories()
	{
		$this->admin_session_restrict();
		$this->load->library('pagination');
		$this->load->model('Categories_model');
		
		$page						= get_var_def( 'page_num', 1 );
		$config						= pagination_config();
		$query_filter				= $this->build_categories_filters();
		$config['total_rows']		= $data['categories_count']	= $this->Categories_model->count( $query_filter );
		$config['base_url']			= base_url( '/admin/categories/?' . http_build_query_pagination() );
		$data['categories']			= $this->Categories_model->get_all( $query_filter, $config['per_page'], $page );

		$this->pagination->initialize($config);
		
		$data['pagination']		= $this->pagination->create_links();
		
		$this->add_frame_view( 'admin/categories', $data );
	}
	
	protected function build_users_filters()
	{
		$filter = array(
			array(
				'field_name'	=>	array( 'user_display_name', 'user_name', 'user_firstname', 'user_lastname' ),
				'filter_val'	=>	get_var( 'search_name' ),
				'filter_type'	=>	SQLFilterType::ILIKE
			),
			array(
				'field_name'	=>	'user_level',
				'filter_val'	=>	get_var( 'user_level' )
			),
			array(
				'order_by'		=> get_var_def( 'order_by', 'user_name' ),
				'order_fields'	=> array( 'user_name', 'user_display_name', 'user_email', 'user_level' ),
				'order_dir'		=> get_var_def( 'order_dir', 'ASC' )
			)
		);
		
		return SQL::build_query_filter( $filter );
	}
	
	public function users()
	{
		$this->admin_session_restrict();
		$this->load->library('pagination');
		$this->load->model('Users_model');
		
		$page						= get_var_def( 'page_num', 1 );
		$config						= pagination_config();
		$query_filter				= $this->build_users_filters();
		$config['total_rows']		= $data['users_count']	= $this->Users_model->count( $query_filter );
		$config['base_url']			= base_url( '/admin/users/?' . http_build_query_pagination() );
		$data['users']				= $this->Users_model->get_all( $query_filter, $config['per_page'], $page );
		$data['user_level']			= get_var('user_level');
		$data['search_name']		= get_var('search_name');
		$data['stats']				= $this->Users_model->get_counts();
		
		$this->pagination->initialize($config);
		
		$data['pagination']		= $this->pagination->create_links();
		
		$this->add_frame_view( 'admin/users', $data );
	}
	
	public function user_add()
	{
		$this->admin_session_restrict();
		
		$data['roles'] = CiblogHelper::get_user_roles();
		
		$this->add_frame_view('admin/user_add_form', $data );
	}
	
	public function user_edit($id)
	{
		$this->admin_session_restrict();
		
		$this->load->model('Users_model');
		
		$data = $this->Users_model->by_id($id, ARRAY_A);
		$data['user'] = $data;
		$data['roles'] = CiblogHelper::get_user_roles();
		$data['is_admin'] = TRUE;
		
		$this->add_frame_view('admin/user_form', $data );
	}
	
	public function val_username( $str )
	{
		$this->load->model('Users_model');
		
		if ( !$this->Users_model->exists_username( $str ) )
		{
			return TRUE;
		}
		
		$this->form_validation->set_message( 'val_username', lang_line('username_in_use') );
		
		return FALSE;
	}
	
	public function user_insert()
	{
		$this->admin_session_restrict();
		
		$post = $this->input->post();
		
		$this->load->model('Users_model');
		$this->load->library('form_validation');
		
		$rules	= array(
			array(
				'field'   => 'name', 
				'label'   => lang_line_ucwords('username'), 
				'rules'   => 'trim|required|min_length[4]|max_length[64]|callback_val_username'
			),
			array(
				'field'   => 'email',
				'label'   => lang_line_ucwords('email'), 
				'rules'   => 'trim|required|valid_email|max_length[64]'
			),
			array(
				'field'   => 'firstname', 
				'label'   => lang_line_ucwords('first_name'), 
				'rules'   => 'trim|max_length[64]'
			),
			array(
				'field'   => 'lastname',
				'label'   => lang_line_ucwords('last_name'), 
				'rules'   => 'trim|max_length[64]'
			),
			array(
				'field'   => 'url',
				'label'   => lang_line_ucwords('biographical_info'), 
				'rules'   => 'trim|valid_url|max_length[128]'
			),
			array(
				'field'   => 'password',
				'label'   => lang_line_ucwords('password'), 
				'rules'   => 'required|min_length[4]'
			),
			array(
				'field'   => 'password_repeat',
				'label'   => lang_line_ucwords('new_password_repeat'), 
				'rules'   => 'required|matches[password]'
			)
		);
		
		$id = isset( $post['id'] ) ? intval( $post['id'] ) : 0;
		
		$this->form_validation->set_rules( $rules );
		
		$form_inputs = '.form-table input, .form-table textarea, .form-table select';
		$this->kajax->removeClass( $form_inputs, 'error' );
		$this->kajax->resetAnim( $form_inputs );
		
		if ( $this->form_validation->run() != FALSE )
		{
			$this->Users_model->add_from_post( $post );
			
			$this->kajax->fancy_log_success( lang_line_ucwords('user') . " '" . $post['name'] . "' " . lang_line('added_successfuly') . '.' );
			
			$this->kajax->load_target(base_url('/admin/users'));
		}
		else
		{
			$this->kajax->fancy_log_error( validation_errors() );
		}
		
		$this->kajax_validate_inputs( $rules );
		
		$this->kajax->out();
	}
	
	public function user_update()
	{
		$this->admin_session_restrict();
		
		$this->user_update_data();
	}
	
	public function user_delete()
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
	
	public function category_update()
	{
		$this->admin_session_restrict();
		
		$post = $this->input->post();
		
		$this->load->model('Categories_model');
		$this->load->library('form_validation');
		
		$rules	= array(
			array(
				'field'   => 'key', 
				'label'   => lang_line_ucwords('key'), 
				'rules'   => 'trim|required|max_length[64]'
			),
			array(
				'field'   => 'name',
				'label'   => lang_line_ucwords('name'), 
				'rules'   => 'trim|required|max_length[64]'
			),
			array(
				'field'   => 'order',
				'label'   => lang_line_ucwords('order'), 
				'rules'   => 'trim|required|integer'
			)
		);
		
		$this->form_validation->set_rules( $rules );
		
		$id = isset( $post['id'] ) ? intval( $post['id'] ) : 0;
		
		if ( $this->form_validation->run() != FALSE )
		{
			if ( $id == 0 )
			{
				$id = $this->Categories_model->add( $post['key'], $post['name'], isset($post['display_info']) ? 1 : 0, isset($post['in_menu']) ? 1 : 0, $post['order'] );
				
				$this->kajax_replace_new_ids( $id, array( 'row_', 'row_hidden_', 'row_display_info_', 'row_in_menu_' ) );
				
				$this->kajax->call( 'table_row_new_convert_to_id( ' . $id . ', "id", "' . 
									base_url('/admin/category_delete/'.$id) . '", "' . 
									lang_line('admin_confirm_delete_category') . '", "' . 
									lang_line_upper('delete') . '" )'
				);
				
				$this->kajax->fancy_log_success( lang_line_ucwords('category') . " '" . $post['name'] . "' " . lang_line('added_successfuly') . '.' );
			}
			else
			{
				if ( $this->Categories_model->exists( $id )  )
				{
					$this->Categories_model->update( $id, $post['key'], $post['name'], isset($post['display_info']) ? 1 : 0, isset($post['in_menu']) ? 1 : 0, $post['order'] );
					
					$this->kajax->fancy_log_success( lang_line_ucwords('category') . " '" . $post['name'] . "' " . lang_line('saved') . '.' );
				}
				else
				{
					$this->kajax->fancy_log_error( lang_line_ucwords('category') . " '" . $post['name'] . "' " . lang_line('doesnt_exists') . '.' );
				}
			}
		}
		else
		{
			$this->kajax->fancy_log_error( validation_errors() );
		}
		
		$this->kajax_validation_set_input_states( $rules, $post['id'] );
		
		$this->kajax->out();
	}
	
	public function category_delete( $id )
	{
		$this->admin_session_restrict();
		
		$this->load->model('Categories_model');
		
		if ( $this->Categories_model->delete( $id ) )
		{
			$this->kajax->fancy_log_success( lang_line_ucwords('category') . ' ' . lang_line('deleted_successfuly') . '.' );
			
			$this->kajax->remove( '#row_hidden_' . $id );
			$this->kajax->remove( '#row_' . $id );
		}
		else
		{
			$this->kajax->fancy_log_error( lang_line('cant_delete_category') );
		}
		
		$this->kajax->out();
	}
	
	public function filemanager()
	{
		require_once( ROOTPATH . 'fm/connectors/php/filemanager.class.php');
		
		$_SERVER['DOCUMENT_ROOT'] = substr( ROOTPATH, 0, strlen( ROOTPATH ) - 1 );
		
		$fm = new Filemanager();
		
		$response = '';

		if(!$this->user_is_logged() || $this->user->user_level < CIBLOG_AUTHOR_LEVEL) {
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
