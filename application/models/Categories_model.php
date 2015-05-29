<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Categories_model extends CI_Model
{
	protected $table_name = 'categories';
	
	public function __construct()
	{
		parent::__construct();
		$this->table_name = $this->db->dbprefix . $this->table_name;
	}
	
	function add( $key, $name )
	{
		$this->db->query("INSERT INTO {$this->table_name} (cat_key, cat_name) VALUES (?,?)", array( $key, $name ) );
		
		return $this->db->insert_id();
	}
	
	function update( $key, $name, $id )
	{
		$this->db->query("UPDATE {$this->table_name} SET cat_key = ?, cat_name = ? WHERE cat_id = ?", array( $key, $name, $id ) );
	}
	
	function get_all()
	{
		return $this->db->query("SELECT * FROM {$this->table_name} ORDER BY cat_name ASC")->result_array();
	}
	
	function get( $id )
	{
		return $this->db->query("SELECT * FROM {$this->table_name} WHERE cat_id = ? LIMIT 1", array( $id ) )->row_array();
	}
	
	function get_by_key( $key )
	{
		return $this->db->query("SELECT * FROM {$this->table_name} WHERE cat_key = ? LIMIT 1", array( $key ) )->row_array();
	}
}
