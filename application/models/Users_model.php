<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_Model
{
	protected $table_name = 'users';
	
	public function __construct()
	{
		parent::__construct();
		$this->table_name = $this->db->dbprefix . $this->table_name;
	}
	
	public function by_id( $id )
	{
		return $this->db->query( "SELECT * FROM {$this->table_name} WHERE user_id = ? LIMIT 1", array( $id ) )->row();
	}
	
	public function by_user( $user, $pass )
	{
		return $this->db->query( "SELECT * FROM {$this->table_name} WHERE user_name = ? AND user_password = ? LIMIT 1", array( strtolower( $user ), CiblogHelper::password_hash( $pass ) ) )->row();
	}
	
	public function by_session_token( $token )
	{
		return $this->db->query( "SELECT * FROM {$this->table_name} WHERE user_session_token = ? LIMIT 1", array( $token ) )->row();
	}
	
	public function get_admin_user( $user, $pass )
	{
		return $this->db->query( "SELECT * FROM {$this->table_name} WHERE user_name = ? AND user_password = ? AND user_level >= 1000 LIMIT 1", array( strtolower( $user ), CiblogHelper::password_hash( $pass ) ) )->row();
	}
	
	public function update_session_token( $id, $token )
	{
		$this->db->query( "UPDATE {$this->table_name} SET user_session_token = ? WHERE user_id = ?", array( $token, $id ) );
	}
}
