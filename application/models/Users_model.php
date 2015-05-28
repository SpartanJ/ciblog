<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_Model
{
	public function by_id( $id )
	{
		return $this->db->query( 'SELECT * FROM users WHERE user_id = ? LIMIT 1', array( $id ) )->row();
	}
	
	public function by_user( $user, $pass )
	{
		return $this->db->query( 'SELECT * FROM users WHERE user_name = ? AND user_password = ? LIMIT 1', array( strtolower( $user ), CiblogHelper::password_hash( $pass ) ) )->row();
	}
	
	public function get_admin_user( $user, $pass )
	{
		return $this->db->query( 'SELECT * FROM users WHERE user_name = ? AND user_password = ? AND user_level >= 1000 LIMIT 1', array( strtolower( $user ), CiblogHelper::password_hash( $pass ) ) )->row();
	}
	
	public function update_token( $id, $token )
	{
		
	}
}
