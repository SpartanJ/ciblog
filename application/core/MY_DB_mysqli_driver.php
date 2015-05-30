<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

define('OBJECT','OBJECT',true);
define('ARRAY_A','ARRAY_A',true);

class MY_DB_mysqli_driver extends CI_DB_mysqli_driver
{
	public $query_data	= FALSE;
	public $num_rows	= 0;
	
	function set_query_data( $data )
	{
		$this->query_data = null != $data ? ( is_array( $data ) ? $data : func_get_args() ) : null;
	}

	function set_qd( $data )
	{
		$this->query_data = null != $data ? ( is_array( $data ) ? $data : func_get_args() ) : null;
	}
	
	function query( $sql, $binds = FALSE, $return_object = TRUE )
	{
		$q = parent::query( $sql, $binds != FALSE ? $binds : $this->query_data, $return_object );
		
		$this->query_data	= FALSE;
		$this->num_rows		= is_object( $q->result_id ) ? $q->num_rows() : 0;
		
		return $q;
	}
	
	function get_var( $query, $params = FALSE, $x = 0 )
	{
		$r	= NULL;
		$q	= $this->query( $query, $params != FALSE ? $params : $this->query_data );
		
		if ( $q->num_rows() > 0 )
		{
			$arr	= array_values( $q->row_array() );
			$r		= $arr[$x];
		}
		
		return $r;
	}

	function get_row( $query, $output = OBJECT, $params = FALSE )
	{
		$r	= NULL;
		$q	= $this->query( $query, $params != FALSE ? $params : $this->query_data );
		
		if ( $q->num_rows() > 0 )
		{
			switch ( $output )
			{
				case OBJECT:
				{
					$r = $q->row_object();
					
					break;
				}
				case ARRAY_A:
				{
					$r = $q->row_array();
				}
			}
		}
		
		return $r;
	}

	function get_results( $query, $output = OBJECT, $params = FALSE )
	{
		$r	= NULL;
		$q	= $this->query( $query, $params != FALSE ? $params : $this->query_data );
		
		if ( $q->num_rows() > 0 )
		{
			switch ( $output )
			{
				case OBJECT:
				{
					$r = $q->result_object();
					
					break;
				}
				case ARRAY_A:
				{
					$r = $q->result_array();
					
					break;
				}
			}
		}
		
		return $r;
	}

	function last_insert_id()
	{
		return $this->insert_id();
	}
}
