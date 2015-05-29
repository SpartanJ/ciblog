<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Posts_model extends CI_Model
{
	protected $table_name = 'posts';
	
	public function __construct()
	{
		parent::__construct();
		$this->table_name = $this->db->dbprefix . $this->table_name;
	}
	
	function add( $data,$slug, $admin_id )
	{
		$this->db->query("INSERT INTO {$this->table_name} (post_title, post_body, post_category, post_draft, post_slug, post_admin_id, post_created) VALUES (?,?,?,?,?,?, now())", array( $data['title'], $data['body'], $data['category'], $data['draft'], $slug, $admin_id ) );
		
		return $this->db->insert_id();
	}
	
	function update( $data, $slug, $update_timestamp = FALSE )
	{
		$this->db->query("UPDATE {$this->table_name} SET post_title = ?, post_body = ?, post_category = ?, post_draft = ?, post_slug = ?, post_updated = NOW() WHERE post_id = ?", array( $data['title'], $data['body'], $data['category'], $data['draft'], $slug, $data['post_id'] ) );
	}
	
	function get_drafts()
	{
		return $this->db->query("SELECT * FROM {$this->table_name} INNER JOIN {$this->db->dbprefix}categories ON post_category = cat_id WHERE post_draft = 1 ORDER BY post_created DESC")->result_array();
	}

	function get_published( $category = NULL )
	{
		$and = '';
		
		if( isset( $category ) )
		{
			$and = "AND post_category = ?";
		}
		
		return $this->db->query("SELECT * FROM {$this->table_name} INNER JOIN {$this->db->dbprefix}categories ON post_category = cat_id WHERE post_draft = 0 " . $and . " ORDER BY post_created DESC", array( $category ) )->result_array();
	}
	
	function get_published_by_category_key( $cat_key = NULL )
	{
		$and = '';
		
		if( isset( $cat_key ) )
		{
			$and = "AND cat_key = ?";
		}
		
		return $this->db->query("SELECT * FROM {$this->table_name} INNER JOIN {$this->db->dbprefix}categories ON post_category = cat_id WHERE post_draft = 0 " . $and . " ORDER BY post_created DESC", array( $cat_key ) )->result_array();
	}
	
	function get_rss( $category = NULL )
	{
		if( isset( $category ) )
		{
			$and = "AND post_category = ?";
		}
		
		return $this->db->query("SELECT * FROM {$this->table_name} INNER JOIN {$this->db->dbprefix}categories ON post_category = cat_id WHERE post_draft = 0 " . $and . " ORDER BY post_created DESC", array( $category ) )->result_array();
	}
	
	function get( $post_id )
	{
		return $this->db->query("SELECT * FROM {$this->table_name} INNER JOIN {$this->db->dbprefix}categories ON post_category = cat_id WHERE post_id = ?", array( $post_id ) )->row_array();
	}

	function delete( $post_id )
	{
		$this->db->query("DELETE FROM {$this->table_name} WHERE post_id = ?", array( $post_id ) );
	}

	function get_slug($slug)
	{
		return $this->db->query("SELECT * FROM {$this->table_name} INNER JOIN {$this->db->dbprefix}categories ON post_category = cat_id WHERE post_slug = ?", array( $slug ) )->row_array();
	}
	
	function slug_exists_and_not_me( $slug, $post_id )
	{
		return NULL != $this->db->query("SELECT 1 FROM {$this->table_name} WHERE post_slug = ? AND post_id != ? LIMIT 1", array( $slug, $post_id ) )->row_array();
	}
	
	function slug_exists( $slug )
	{
		return NULL != $this->db->query("SELECT 1 FROM {$this->table_name} WHERE post_slug = ? LIMIT 1", array( $slug ) )->row_array();
	}
}
