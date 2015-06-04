<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends USER_Controller
{
	public function logout()
	{
		$this->session_destroy();
		
		$this->redirect(base_url('/'));
	}
}
