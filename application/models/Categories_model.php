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
	
	function get_all( $filter = NULL, $per_page = NULL, $page_num = 1, $fields_get = '*' )
	{
		$where		= '';
		$is_count	= -1 != str_starts_with( 'COUNT', $fields_get );
		
		SQL::prepare_filter( $where, $filter, $is_count );
		
		$sql = "SELECT {$fields_get} FROM {$this->table_name} {$where}";
		
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
		
		return $this->db->query()->result_array();
	}
	
	public function count( $filter = '' )
	{
		return $this->get_all( $filter, NULL, 1, 'COUNT(cat_id)' );
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
