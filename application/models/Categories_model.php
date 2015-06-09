<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Categories_model extends CI_Model
{
	protected $table_name = 'categories';
	
	public function __construct()
	{
		parent::__construct();
		$this->table_name = $this->db->dbprefix . $this->table_name;
	}
	
	function add( $key, $name, $display_info, $in_menu, $order )
	{
		$this->db->query("INSERT INTO {$this->table_name} (cat_key, cat_name, cat_display_info, cat_in_menu, cat_order) VALUES (?,?,?,?,?)", array( $key, $name, $display_info, $in_menu, $order ) );
		
		return $this->db->insert_id();
	}
	
	function update( $id, $key, $name, $display_info, $in_menu, $order )
	{
		$this->db->query("UPDATE {$this->table_name} SET cat_key = ?, cat_name = ?, cat_display_info = ?, cat_in_menu = ?, cat_order = ? WHERE cat_id = ?", array( $key, $name, $display_info, $in_menu, $order, $id ) );
	}
	
	function can_delete( $id )
	{
		return 0 == intval( $this->db->get_var( "SELECT COUNT(*) FROM {$this->db->dbprefix}posts WHERE post_category = ?", array( $id ) ) );
	}
	
	function delete( $id )
	{
		if ( $this->can_delete( $id ) )
		{
			$this->db->query( "DELETE FROM {$this->table_name} WHERE cat_id = ?", array( $id ) );
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	function exists( $id )
	{
		return $this->db->get_var( "SELECT 1 FROM {$this->table_name} WHERE cat_id = ? LIMIT 1", array( $id ) );
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
	
	function get_menu_sections( $output = ARRAY_A )
	{
		$sql = "
			(
				SELECT 
					post_id AS id, 
					COALESCE(post_menu_title,post_title) AS title, 
					post_order AS `order`, 
					post_slug AS slug, 
					0 AS is_category 
				FROM {$this->db->dbprefix}posts 
				WHERE post_in_menu = 1 
			)
			UNION
			( 
				SELECT 
					cat_id AS id, 
					cat_name AS title, 
					cat_order AS `order`, 
					cat_key AS slug, 
					1 AS is_category 
				FROM {$this->table_name} 
				WHERE 
					cat_in_menu = 1 
			)
			ORDER BY `order` ASC";
			
		return $this->db->get_results( $sql, $output );
	}
}
