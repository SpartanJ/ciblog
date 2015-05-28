<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class SESSION_Controller extends MY_Controller
{
	protected $sess			= NULL;
	protected $user 		= NULL;
	
	function __construct( $session_recover = TRUE )
	{
		parent::__construct();
		
		$this->load->model('Users_model');
		
		if ( $session_recover )
		{
			$this->session_recover();
		}
	}
	
	protected function auto_add()
	{
		parent::auto_add();
		
		//$this->add_js('assets/js/admin.js');
		
		$this->add_css('assets/css/admin.css');
	}
	
	protected function session_recover( $expire_on_close = TRUE )
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
			$this->user = $this->Users_model->by_session_token( $this->sess['token'] );
		}
		else
		{
			$this->session_destroy();
		}
	}
	
	protected function session_exists()
	{
		return isset( $this->sess ) && isset( $this->sess['id'] ) && isset( $this->sess['token'] ) && '' != $this->sess['token'];
	}
	
	public function session_destroy()
	{
		$this->session_user->sess_destroy();
	}
	
	protected function session_create( $user, $pass )
	{
		$this->load->model( 'Users_model' );
		
		$user = $this->Users_model->by_user( $user, $pass );
		
		if ( isset( $user ) )
		{
			$session_token = hash( 'sha256', time() + $user->user_id );
			
			$data = array(
				'id' => $user->user_id,
				'name' => $user->user_name,
				'token' => $session_token
			);
			
			$this->session_user->set_userdata( $data );
			
			$this->sess = $data;
			$this->user = $user;
			
			$this->Users_model->update_session_token( $user->user_id, $session_token );
			$this->user->user_session_token = $session_token;
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function admin_session_exists()
	{
		return 	$this->session_exists() && 
				isset( $this->user ) && 
				isset( $this->user_session_token ) &&
				isset( $this->sess['token'] ) &&
				$this->user_session_token == $this->sess['token'] &&
				isset( $this->user->user_level ) && 
				$this->user->user_level >= CIBLOG_ADMIN_LEVEL;
	}
}
