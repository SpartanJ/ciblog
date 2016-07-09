<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Posts_model extends CI_Model
{
	protected $table_name = 'posts';
	protected $get_all_basic_fields = '
		post_id,
		post_author,
		post_title,
		post_slug,
		post_created,
		post_updated,
		post_category,
		post_draft,
		user_id,
		user_name,
		user_email,
		user_display_name,
		cat_id,
		cat_key,
		cat_name
	';
	
	public function __construct()
	{
		parent::__construct();
		$this->table_name = $this->db->dbprefix . $this->table_name;
	}
	
	function add( $data, $slug, $author )
	{
		$in_menu = isset($data['in_menu'])?1:0;
		
		$this->db->query("INSERT INTO {$this->table_name} 
							(post_title, post_body, post_category, post_draft, post_slug, post_author, post_in_menu, post_order, post_menu_title, post_created) 
							VALUES (?,?,?,?,?,?,?,?,?, now())", 
							array( $data['title'], $data['body'], $data['category'], $data['draft'], $slug, $author, $in_menu, $data['order'], $data['menu_title'] )
		);
		
		return $this->db->insert_id();
	}
	
	function update( $data, $slug, $update_timestamp = FALSE )
	{
		$in_menu = isset($data['in_menu'])?1:0;
		
		$this->db->query("UPDATE {$this->table_name} 
							SET post_title = ?, post_body = ?, post_category = ?, post_draft = ?, 
								post_slug = ?, post_in_menu = ?, post_order = ?, post_menu_title = ?, post_updated = NOW() WHERE post_id = ?", 
						array( $data['title'], $data['body'], $data['category'], $data['draft'], $slug, $in_menu, $data['order'], $data['menu_title'], $data['post_id'] ) );
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
	
	function get_published_by_category_key( $cat_key = NULL, $author = NULL, $per_page = NULL, $page_num = 1 )
	{
		$and = '';
		$params = array();
		
		if( isset( $cat_key ) )
		{
			$and .= "AND cat_key = ?";
			$params[] = $cat_key;
		}
		
		if ( isset( $author ) )
		{
			$and .= "AND post_author = ?";
			$params[] = $author;
		}
		
		return $this->db->query("SELECT * FROM {$this->table_name} 
									INNER JOIN {$this->db->dbprefix}categories ON post_category = cat_id  
									INNER JOIN {$this->db->dbprefix}users ON post_author = user_id 
								 WHERE post_draft = 0 "
								 . $and . " ORDER BY post_created DESC", $params )->result_array();
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
		return $this->db->query("SELECT * FROM {$this->table_name} 
									INNER JOIN {$this->db->dbprefix}categories ON post_category = cat_id  
									INNER JOIN {$this->db->dbprefix}users ON post_author = user_id
								WHERE post_slug = ?", array( $slug ) )->row_array();
	}
	
	function slug_exists_and_not_me( $slug, $post_id )
	{
		return NULL != $this->db->query("SELECT 1 FROM {$this->table_name} WHERE post_slug = ? AND post_id != ? LIMIT 1", array( $slug, $post_id ) )->row_array();
	}
	
	function slug_exists( $slug )
	{
		return NULL != $this->db->query("SELECT 1 FROM {$this->table_name} WHERE post_slug = ? LIMIT 1", array( $slug ) )->row_array();
	}
	
	function draft_it( $id )
	{
		$this->db->query("UPDATE {$this->table_name} SET post_draft = 1 WHERE post_id = ?", array( $id ) );
	}
	
	function publish_it( $id )
	{
		$this->db->query("UPDATE {$this->table_name} SET post_draft = 0 WHERE post_id = ?", array( $id ) );
	}
	
	/** could be an array of categories or just a cat_id */
	public static function get_category_filter( $filter_categories = NULL, $field_name = 'post_category' )
	{
		return SQL::get_or_filter( $filter_categories, $field_name );
	}
	
	public function get_all( $filter_category = NULL, $filter = NULL, $per_page = NULL, $page_num = 1, $fields_get = NULL )
	{
		$fields_get = NULL == $fields_get ? $this->get_all_basic_fields : $fields_get;
		$where		= self::get_category_filter( $filter_category );
		$is_count	= -1 != str_starts_with( 'COUNT', $fields_get );
		
		SQL::prepare_filter( $where, $filter, $is_count );
		
		$sql = 'SELECT ' . $fields_get . ' 
				FROM ' . $this->table_name . ' 
					INNER JOIN ' . $this->db->dbprefix . 'users ON user_id = post_author 
					INNER JOIN ' . $this->db->dbprefix . 'categories ON cat_id = post_category ' . 
				$where;
		
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
	
	public function count( $filter_category, $filter = '' )
	{
		return $this->get_all( $filter_category, $filter, NULL, 1, 'COUNT(post_id)' );
	}
	
	public function get_counts()
	{
		$sql = "SELECT 
					( SELECT COUNT(post_id) FROM {$this->table_name} ) AS posts_count,
					( SELECT COUNT(post_id) FROM {$this->table_name} WHERE post_draft = 0 ) AS posts_published,
					( SELECT COUNT(post_id) FROM {$this->table_name} WHERE post_draft = 1 ) AS posts_draft";
			
		return $this->db->query($sql)->row_array();
	}
}
