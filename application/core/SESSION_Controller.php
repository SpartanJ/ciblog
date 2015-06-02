<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class SESSION_Controller extends MY_Controller
{
	protected $sess			= NULL;
	protected $user 		= NULL;
	protected $_config		= array(
		'driver'				=> 'files',
		'cookie_name'			=> 'ciblog_user_session',
		'cookie_lifetime' 		=> 0, //60*60*24*365; // 0 expires the cookie until the session is closed ( seconds until expires )
		'cookie_path'			=> '/',
		'cookie_prefix'			=> '',
		'cookie_domain'			=> '',
		'cookie_secure'			=> FALSE,
		'expiration'			=> 0, // 0 until the browser is closed the session lives
		'match_ip'				=> FALSE,
		'sess_time_to_update' 	=> 0
	);
	
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
		
		$this->add_js('assets/js/admin.js');
		
		$this->add_css('assets/css/admin.css');
	}
	
	protected function session_recover( $expire_on_close = TRUE )
	{
		$this->load->library( 'CIBLOG_Session', $this->_config, 'session_user' );
		
		$this->sess = $this->session_user->all_userdata();
		
		$this->user_load();
	}
	
	protected function user_load()
	{
		if ( $this->session_exists() )
		{
			$this->user = $this->Users_model->by_session_token( $this->sess['token'] );
		}
	}
	
	protected function session_exists()
	{
		return isset( $this->sess ) && isset( $this->sess['id'] ) && isset( $this->sess['token'] ) && '' != $this->sess['token'];
	}
	
	protected function session_destroy()
	{
		$this->session_user->sess_destroy();
	}
	
	protected function session_create( $user, $pass, $remember_me = FALSE )
	{
		$user = $this->Users_model->by_user( $user, $pass, $cookie_lifetime = 0 );
		
		if ( isset( $user ) )
		{
			$session_token = hash( 'sha256', time() + $user->user_id );
			
			$data = array(
				'id'	=> $user->user_id,
				'name'	=> $user->user_name,
				'token'	=> $session_token
			);
			
			$this->session_user->set_userdata( $data );
			
			$this->sess = $data;
			$this->user = $user;
			
			$this->Users_model->update_session_token( $user->user_id, $session_token );
			$this->user->user_session_token = $session_token;
			
			// update the cookie expiration time
			if ( $remember_me )
			{
				setcookie(
					$this->_config['cookie_name'],
					session_id(),
					time() + 60*60*24*365, // expire in a year
					config_item('cookie_path'),
					config_item('cookie_domain'),
					config_item('cookie_secure'),
					TRUE
				);
			}
			
			session_write_close();
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	protected function user_is_logged()
	{
		return	$this->session_exists() && 
				isset( $this->user ) && 
				isset( $this->user->user_session_token ) &&
				isset( $this->sess['token'] ) &&
				$this->user->user_session_token == $this->sess['token'];
	}
	
	protected function admin_is_logged()
	{
		return	$this->user_is_logged() &&
				isset( $this->user->user_level ) && 
				$this->user->user_level >= CIBLOG_ADMIN_LEVEL;
	}
	
	protected function admin_session_restrict( $min_level = CIBLOG_ADMIN_LEVEL )
	{
		if ( $this->admin_is_logged() && $this->user->user_level >= $min_level )
		{
			return TRUE;
		}
		
		$this->session_destroy();
		
		$this->redirect(base_url('/admin/login'));
		
		return FALSE;
	}
}
