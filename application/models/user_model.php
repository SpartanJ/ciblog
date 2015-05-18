<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model
{
	public function by_id( $id )
	{
		return $this->db->query( 'SELECT * FROM admin WHERE id = ?', array( $id ) )->row();
	}
	
	public function by_user( $user, $pass )
	{
		return $this->db->query( 'SELECT * FROM admin WHERE name = ? AND password = ?', array( strtolower( $user ), ( ( '' != $pass ) ? hash( 'md5', $pass ) : '' ) ) )->row();
	}
}