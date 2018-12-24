<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once SYSDIR . '/libraries/Session/Session.php';

class CIBLOG_Session extends CI_Session {
	public function __construct(array $params = array())
	{
		// No sessions under CLI
		if (is_cli())
		{
			log_message('debug', 'Session: Initialization under CLI aborted.');
			return;
		}
		elseif ((bool) ini_get('session.auto_start'))
		{
			log_message('error', 'Session: session.auto_start is enabled in php.ini. Aborting.');
			return;
		}
		elseif ( ! empty($params['driver']))
		{
			$this->_driver = $params['driver'];
			unset($params['driver']);
		}
		elseif ($driver = config_item('sess_driver'))
		{
			$this->_driver = $driver;
		}
		// Note: BC workaround
		elseif (config_item('sess_use_database'))
		{
			$this->_driver = 'database';
		}

		$class = $this->_ci_load_classes($this->_driver);

		// Configuration ...
		$this->_configure($params);

		$class = new $class($this->_config);
		if ($class instanceof SessionHandlerInterface)
		{
			if (is_php('5.4'))
			{
				session_set_save_handler($class, TRUE);
			}
			else
			{
				session_set_save_handler(
					array($class, 'open'),
					array($class, 'close'),
					array($class, 'read'),
					array($class, 'write'),
					array($class, 'destroy'),
					array($class, 'gc')
				);

				register_shutdown_function('session_write_close');
			}
		}
		else
		{
			log_message('error', "Session: Driver '".$this->_driver."' doesn't implement SessionHandlerInterface. Aborting.");
			return;
		}

		// Sanitize the cookie, because apparently PHP doesn't do that for userspace handlers
		/*if (isset($_COOKIE[$this->_config['cookie_name']])
			&& (
				! is_string($_COOKIE[$this->_config['cookie_name']])
				OR ! preg_match('/^[0-9a-f]{40}$/', $_COOKIE[$this->_config['cookie_name']])
			)
		)
		{
			log_message('error', 'unseting cookie: ' . $_COOKIE[$this->_config['cookie_name']] );
			log_message('error', json_enc( $_COOKIE ) );
			
			unset($_COOKIE[$this->_config['cookie_name']]);
		}*/

		session_start();

		// Is session ID auto-regeneration configured? (ignoring ajax requests)
		if ((empty($_SERVER['HTTP_X_REQUESTED_WITH']) OR strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')
			&& ($regenerate_time = config_item('sess_time_to_update')) > 0
		)
		{
			log_message('debug', 'regenerated cookies __ci_last_regenerate');
			
			if ( ! isset($_SESSION['__ci_last_regenerate']))
			{
				$_SESSION['__ci_last_regenerate'] = time();
				
				log_debug( 'session updated __ci_last_regenerate' );
			}
			elseif ($_SESSION['__ci_last_regenerate'] < (time() - $regenerate_time))
			{
				$this->sess_regenerate((bool) config_item('sess_regenerate_destroy'));
				
				log_message('debug', 'session regenerated the session id with ' . session_id() );
			}
		}

		$this->_ci_init_vars();

		log_message('info', "Session: Class initialized using '".$this->_driver."' driver.");
	}
}
