<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Post_model extends CI_Model
{
	function add($data,$slug, $admin_id)
	{
		$this->db->query("INSERT INTO blog_posts (title, body, category, draft, slug, admin_id) VALUES (?,?,?,?,?,?,?)", array($data['title'], $data['body'], $data['category'], $data['draft'], $slug, $admin_id));
		return $this->db->insert_id();
	}

	function update($data,$slug,$update_timestamp = false)
	{
		//updates timestamp if draft flag changed
		$this->db->query("UPDATE blog_posts SET timestamp = NOW() WHERE draft != ? and post_id = ?", array( $data['draft'], $data['post_id']));
		$this->db->query("UPDATE blog_posts SET title = ?, body = ?, category = ?, draft = ?, slug =  ? WHERE post_id = ?", array($data['title'], $data['body'], $data['category'], $data['draft'], $slug, $data['post_id']));
	}

	function get_drafts()
	{
		return $this->db->query("SELECT * FROM blog_posts WHERE draft = 1 ORDER BY timestamp DESC")->result_array();
	}

	function get_published($category = null)
	{
		$and = '';
		if($category != null)
		{
			$and = "AND category = ?";
		}
		return $this->db->query("SELECT * FROM blog_posts WHERE draft = 0 ".$and." ORDER BY timestamp DESC", array($category))->result_array();
	}

	function get_rss($category = null)
	{
		return $this->db->query("SELECT * FROM blog_posts WHERE draft = 0 AND category != 'STANDALONE' ORDER BY timestamp DESC", array($category))->result_array();
	}

	function get($post_id)
	{
		return $this->db->query("SELECT * FROM blog_posts WHERE post_id = ?",array($post_id))->row_array();
	}

	function delete($post_id)
	{
		$this->db->query("DELETE FROM blog_posts WHERE post_id = ?",array($post_id));
	}

	function get_slug($slug)
	{
		return $this->db->query("SELECT * FROM blog_posts WHERE slug = ?",array($slug))->row_array();
	}
}
