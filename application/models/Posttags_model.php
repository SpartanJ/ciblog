<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PostTags_model extends CI_Model
{
	protected $table_name = 'post_tags';
	
	public function __construct()
	{
		parent::__construct();
		$this->table_name = $this->db->dbprefix . $this->table_name;
	}
	
	function add( $post_id, $name )
	{
		$this->db->query("INSERT INTO {$this->table_name} (ptag_post_id, ptag_name) VALUES (?,?)", array( $post_id, $name ) );
		
		return $this->db->insert_id();
	}
	
	function delete( $ptag_id )
	{
		$this->db->query("DELETE FROM {$this->table_name} WHERE ptag_id = ?", array( $ptag_id ) );
	}
	
	function exists( $post_id, $name )
	{
		return $this->db->get_var( "SELECT ptag_post_id FROM {$this->table_name} WHERE ptag_post_id = ? AND ptag_name = ? LIMIT 1", array( $post_id, $name ) );
	}
	
	function get_post_tags( $post_id )
	{
		return $this->db->get_results( "SELECT * FROM {$this->table_name} WHERE ptag_post_id = ?", ARRAY_A, array( $post_id ) );
	}
	
	function get_post_id_from_tag_id( $ptag_id )
	{
		return $this->db->get_var( "SELECT ptag_post_id FROM {$this->table_name} WHERE ptag_id = ?", array( $ptag_id ) );
	}
}
