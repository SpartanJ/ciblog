<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class SESSION_Controller extends MY_Controller
{
	protected $sess			= NULL;
	protected $user 		= NULL;
	
	function __construct( $load_session = TRUE )
	{
		parent::__construct();
		
		$this->load->model('Users_model');
		
		if ( $load_session )
		{
			$this->load_session();
		}
	}
	
	protected function auto_add()
	{
		parent::auto_add();
		
		//$this->add_js('assets/js/admin.js');
		
		$this->add_css('assets/css/admin.css');
	}
	
	protected function load_session( $expire_on_close = TRUE )
	{
		$config['sess_cookie_name']		= 'ciblog_user_session';
		$config['sess_expiration']		= 0;
		$config['sess_expire_on_close']	= $expire_on_close;
		$config['sess_encrypt_cookie']	= TRUE;
		$config['sess_use_database']	= FALSE;
		$config['sess_table_name']		= 'ci_sessions';
		$config['sess_match_ip']		= FALSE;
		$config['sess_match_useragent']	= FALSE;
		$config['sess_time_to_update']	= 300;
		
		$this->load->library( 'session', $config, 'session_user' );
		
		$this->sess = $this->session_user->all_userdata();
		
		$this->load_user();
	}
	
	protected function load_user()
	{
		if ( $this->session_exists() )
		{
			$this->user = $this->Users_model->by_id( $this->sess['id'] );
		}
	}
	
	protected function session_exists()
	{
		return isset( $this->sess ) && isset( $this->sess['id'] );
	}
	
	public function destroy_session()
	{
		$this->session_user->sess_destroy();
	}
	
	protected function create_session( $user, $pass )
	{
		$this->load->model( 'Users_model' );
		
		$user = $this->Users_model->by_user( $user, $pass );
		
		if ( isset( $user ) )
		{
			$data = array(
				'id' => $user->user_id,
				'name' => $user->user_name,
				'token' => hash( 'sha256', time() + $user->user_id )
			);
			
			$this->session->set_userdata( $data );
			
			$this->sess = $data;
			$this->user = $user;
			
			return TRUE;
		}
		
		return FALSE;
	}
}
