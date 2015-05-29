<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Posts_model extends CI_Model
{
	function add( $data,$slug, $admin_id )
	{
		$this->db->query("INSERT INTO posts (post_title, post_body, post_category, post_draft, post_slug, post_admin_id, post_created) VALUES (?,?,?,?,?,?, now())", array( $data['title'], $data['body'], $data['category'], $data['draft'], $slug, $admin_id ) );
		
		return $this->db->insert_id();
	}
	
	function update( $data, $slug, $update_timestamp = FALSE )
	{
		//updates timestamp if draft flag changed
		$this->db->query("UPDATE posts SET post_timestamp = NOW() WHERE post_draft != ? and post_id = ?", array( $data['draft'], $data['post_id'] ) );
		
		$this->db->query("UPDATE posts SET post_title = ?, post_body = ?, post_category = ?, post_draft = ?, post_slug = ? WHERE post_id = ?", array( $data['title'], $data['body'], $data['category'], $data['draft'], $slug, $data['post_id'] ) );
	}
	
	function get_drafts()
	{
		return $this->db->query("SELECT * FROM posts INNER JOIN categories ON post_category = cat_id WHERE post_draft = 1 ORDER BY post_timestamp DESC")->result_array();
	}

	function get_published( $category = NULL )
	{
		$and = '';
		
		if( isset( $category ) )
		{
			$and = "AND post_category = ?";
		}
		
		return $this->db->query("SELECT * FROM posts INNER JOIN categories ON post_category = cat_id WHERE post_draft = 0 " . $and . " ORDER BY post_timestamp DESC", array( $category ) )->result_array();
	}
	
	function get_published_by_category_key( $cat_key = NULL )
	{
		$and = '';
		
		if( isset( $cat_key ) )
		{
			$and = "AND cat_key = ?";
		}
		
		return $this->db->query("SELECT * FROM posts INNER JOIN categories ON post_category = cat_id WHERE post_draft = 0 " . $and . " ORDER BY post_timestamp DESC", array( $cat_key ) )->result_array();
	}
	
	function get_rss( $category = NULL )
	{
		if( isset( $category ) )
		{
			$and = "AND post_category = ?";
		}
		
		return $this->db->query("SELECT * FROM posts INNER JOIN categories ON post_category = cat_id WHERE post_draft = 0 " . $and . " ORDER BY post_timestamp DESC", array( $category ) )->result_array();
	}
	
	function get( $post_id )
	{
		return $this->db->query("SELECT * FROM posts INNER JOIN categories ON post_category = cat_id WHERE post_id = ?", array( $post_id ) )->row_array();
	}

	function delete( $post_id )
	{
		$this->db->query("DELETE FROM posts WHERE post_id = ?", array( $post_id ) );
	}

	function get_slug($slug)
	{
		return $this->db->query("SELECT * FROM posts INNER JOIN categories ON post_category = cat_id WHERE post_slug = ?", array( $slug ) )->row_array();
	}
	
	function slug_exists_and_not_me( $slug, $post_id )
	{
		return NULL != $this->db->query("SELECT 1 FROM posts WHERE post_slug = ? AND post_id != ? LIMIT 1", array( $slug, $post_id ) )->row_array();
	}
	
	function slug_exists( $slug )
	{
		return NULL != $this->db->query("SELECT 1 FROM posts WHERE post_slug = ? LIMIT 1", array( $slug ) )->row_array();
	}
}
