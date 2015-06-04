<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_Model
{
	protected $table_name = 'users';
	protected $get_all_basic_fields = '
		user_id,
		user_name,
		user_email,
		user_url,
		user_nickname,
		user_display_name,
		user_firstname,
		user_lastname,
		user_registered,
		user_lastlogin,
		user_level,
		user_status,
		user_session_token
	';
	
	public function __construct()
	{
		parent::__construct();
		$this->table_name = $this->db->dbprefix . $this->table_name;
	}
	
	function exists( $id )
	{
		return $this->db->get_var( "SELECT 1 FROM {$this->table_name} WHERE user_id = ? LIMIT 1", array( $id ) );
	}
	
	function exists_username( $name )
	{
		return $this->db->get_var( "SELECT 1 FROM {$this->table_name} WHERE user_name = ? LIMIT 1", array( $name ) );
	}
	
	public function by_id( $id, $output = OBJECT )
	{
		return $this->db->get_row( "SELECT * FROM {$this->table_name} WHERE user_id = ? LIMIT 1", $output, array( $id ) );
	}
	
	public function by_user( $user, $pass )
	{
		return $this->db->query( "SELECT * FROM {$this->table_name} WHERE user_name = ? AND user_password = ? LIMIT 1", array( strtolower( $user ), CiblogHelper::password_hash( $pass ) ) )->row();
	}
	
	public function by_session_token( $token )
	{
		return $this->db->query( "SELECT * FROM {$this->table_name} WHERE user_session_token = ? LIMIT 1", array( $token ) )->row();
	}
	
	public function get_user( $user, $pass )
	{
		return $this->db->query( "SELECT * FROM {$this->table_name} WHERE user_name = ? AND user_password = ? LIMIT 1", array( strtolower( $user ), CiblogHelper::password_hash( $pass ) ) )->row();
	}
	
	public function update_session_token( $id, $token )
	{
		$this->db->query( "UPDATE {$this->table_name} SET user_session_token = ? WHERE user_id = ?", array( $token, $id ) );
	}
	
	public function update_last_login( $id )
	{
		$this->db->query( "UPDATE {$this->table_name} SET user_lastlogin = NOW() WHERE user_id = ?", array( $id ) );
	}
	
	public function get_all( $filter = NULL, $per_page = NULL, $page_num = 1, $fields_get = NULL )
	{
		$fields_get = NULL == $fields_get ? $this->get_all_basic_fields : $fields_get;
		$where		= '';
		$is_count	= -1 != str_starts_with( 'COUNT', $fields_get );
		
		SQL::prepare_filter( $where, $filter, $is_count );
		
		$sql = 'SELECT ' . $fields_get . ' FROM ' . $this->table_name . ' ' . $where;
		
		if ( NULL != $per_page )
		{
			$sql .= ' LIMIT '. (string)intval( $per_page ) .
					' OFFSET '.(string)intval( ($page_num-1)*$per_page );
		}
		
		if ( $is_count )
		{
			return $this->db->get_var( $sql );
		}
		else
		{
			return $this->db->get_results( $sql, ARRAY_A );
		}
	}
	
	public function count( $filter = '' )
	{
		return $this->get_all( $filter, NULL, 1, 'COUNT(user_id)' );
	}
	
	public function get_counts()
	{
		$sql = "SELECT 
					( SELECT COUNT(user_id) FROM {$this->table_name} ) AS users_count,
					( SELECT COUNT(user_id) FROM {$this->table_name} WHERE user_level >= " . CIBLOG_ADMIN_LEVEL . " ) AS administrator_count,
					( SELECT COUNT(user_id) FROM {$this->table_name} WHERE user_level >= " . CIBLOG_EDITOR_LEVEL . " AND user_level < " . CIBLOG_ADMIN_LEVEL . " ) AS editor_count,
					( SELECT COUNT(user_id) FROM {$this->table_name} WHERE user_level >= " . CIBLOG_AUTHOR_LEVEL . " AND user_level < " . CIBLOG_EDITOR_LEVEL . " ) AS author_count,
					( SELECT COUNT(user_id) FROM {$this->table_name} WHERE user_level = " . CIBLOG_SUSCRIBER_LEVEL . " ) AS suscriber_count";
		
		return $this->db->query($sql)->row_array();
	}
	
	public function add_from_post( $post )
	{
		$post['password']		= CiblogHelper::password_hash( $post['password'] );
		$post['display_name']	= $post['nickname'] = $post['name'];
		
		$fields = array( 'name', 'email', 'firstname', 'lastname', 'url', 'password', 'level', 'display_name', 'nickname' );
		$values = array_values_keys( $post, $fields );
		
		$this->db->query( SQL::make_insert_qb( $this->table_name, $fields, 'user_' ), $values );
	}
	
	public function update_from_post( $post, $allow_change_role = TRUE )
	{
		$fields = array( 'firstname', 'lastname', 'nickname', 'display_name', 'email', 'url', 'bio' );
		
		if ( TRUE == $allow_change_role )
		{
			$fields[] = 'level';
		}
		
		if ( isset( $post['password'] ) && !empty( $post['password'] ) )
		{
			$fields[]			= 'password';
			$post['password']	= CiblogHelper::password_hash( $post['password'] );
		}
		
		$fields[] = 'id';
		
		$values = array_values_keys( $post, $fields );
		$this->db->query( SQL::make_update_qb( $this->table_name, $fields, 'id', 'user_' ), $values );
	}
}
