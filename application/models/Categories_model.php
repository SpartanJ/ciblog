<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Categories_model extends CI_Model
{
	function add( $key, $name )
	{
		$this->db->query("INSERT INTO categories (cat_key, cat_name) VALUES (?,?)", array( $key, $name ) );
		
		return $this->db->insert_id();
	}
	
	function update( $key, $name, $id )
	{
		$this->db->query("UPDATE categories SET cat_key = ?, cat_name = ? WHERE cat_id = ?", array( $key, $name, $id ) );
	}
	
	function get_all()
	{
		return $this->db->query("SELECT * FROM categories ORDER BY cat_name ASC")->result_array();
	}
	
	function get( $id )
	{
		return $this->db->query("SELECT * FROM categories WHERE cat_id = ? LIMIT 1", array( $id ) )->row_array();
	}
	
	function get_by_key( $key )
	{
		return $this->db->query("SELECT * FROM categories WHERE cat_key = ? LIMIT 1", array( $key ) )->row_array();
	}
}
