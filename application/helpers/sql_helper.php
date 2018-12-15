<?php

class SQLFilterType
{
	const EQUALS		= 0;
	const LIKE			= 1;
	const ILIKE			= 2;
	const BIGGER		= 3;
	const SMALLER		= 4;
	const BIGGER_OR_EQ	= 5;
	const SMALLER_OR_EQ	= 6;
	const UNEQUALS		= 7;
	const EQUALS_ANY	= 8;
	const EQUALS_ALL	= 9;
	const UNEQUALS_ANY	= 10;
	const UNEQUALS_ALL	= 11;
	const LIKE_ANY		= 12;
	const LIKE_ALL		= 13;
	const ILIKE_ANY		= 14;
	const ILIKE_ALL		= 15;
	const BIGGER_ANY	= 16;
	const BIGGER_ALL	= 17;
	const SMALLER_ANY	= 18;
	const SMALLER_ALL	= 19;
	const BIGGER_OR_EQ_ANY = 20;
	const BIGGER_OR_EQ_ALL = 21;
	const SMALLER_OR_EQ_ANY = 22;
	const SMALLER_OR_EQ_ALL = 23;
}

class SQLConvertType
{
	const NONE			= -1;
	const DECHEX		= 0;
	const HEXDEC		= 1;
	const STRING		= 2;
	const INT			= 3;
	const FLOAT			= 4;
}

class SQLFieldType
{
	const STRING		= 0;
	const INT			= 1;
	const FLOAT			= 2;
	const ANY			= 3;
}

class SQLBoolOp
{
	const BOOL_AND		= 0;
	const BOOL_OR		= 1;
}

class SQL
{
	protected static function get_bool_concat_op( $op )
	{
		switch ( $op )
		{
			case SQLBoolOp::BOOL_AND:	return ' AND ';
			case SQLBoolOp::BOOL_OR:	return ' OR ';
		}
	}
	
	protected static function convert( $convert_type, $val )
	{
		switch ( $convert_type )
		{
			case SQLConvertType::DECHEX:	$val	= dechex( $val );	break;
			case SQLConvertType::HEXDEC:	$val	= hexdec( $val );	break;
			case SQLConvertType::STRING:	$val	= (String)$val;		break;
			case SQLConvertType::INT:		$val	= intval( $val );	break;
			case SQLConvertType::FLOAT:		$val	= floatval( $val );	break;
		}
		
		return $val;
	}
	
	protected static function cast_to_field_type( $field_type, $val, $filter_type = NULL )
	{
		switch ( $field_type )
		{
			case SQLFieldType::STRING:
			{
				$str = self::escape_string( (String)$val );
				
				if ( SQLFilterType::LIKE == $filter_type || SQLFilterType::ILIKE == $filter_type )
				{
					return $str;
				}
				else
				{
					return "'" . $str . "'";
				}
			}
			case SQLFieldType::INT:
			{
				return intval( $val );
			}
			case SQLFieldType::FLOAT:
			{
				return floatval( $val );
			}
			case SQLFieldType::ANY:
			{
				return $val;
			}
		}
	}
	
	protected static function get_op_symbol( $filter_type )
	{
		switch ( $filter_type )
		{
			case SQLFilterType::EQUALS:				return ' = ';
			case SQLFilterType::LIKE:				return ' LIKE ';
			case SQLFilterType::ILIKE:				return ' ILIKE ';
			case SQLFilterType::BIGGER:				return ' > ';
			case SQLFilterType::SMALLER:			return ' < ';
			case SQLFilterType::BIGGER_OR_EQ:		return ' >= ';
			case SQLFilterType::SMALLER_OR_EQ:		return ' <= ';
			case SQLFilterType::UNEQUALS:			return ' != ';
			case SQLFilterType::EQUALS_ANY:			return ' = ANY ';
			case SQLFilterType::EQUALS_ALL:			return ' = ALL ';
			case SQLFilterType::UNEQUALS_ANY:		return ' != ANY ';
			case SQLFilterType::UNEQUALS_ALL:		return ' != ALL ';
			case SQLFilterType::LIKE_ANY:			return ' LIKE ANY ';
			case SQLFilterType::LIKE_ALL:			return ' LIKE ALL ';
			case SQLFilterType::ILIKE_ANY:			return ' ILIKE ANY ';
			case SQLFilterType::ILIKE_ALL:			return ' ILIKE ALL ';
			case SQLFilterType::BIGGER_ANY:			return ' > ANY ';
			case SQLFilterType::BIGGER_ALL:			return ' > ALL ';
			case SQLFilterType::SMALLER_ANY:		return ' < ANY ';
			case SQLFilterType::SMALLER_ALL:		return ' < ALL ';
			case SQLFilterType::BIGGER_OR_EQ_ANY: 	return ' >= ANY ';
			case SQLFilterType::BIGGER_OR_EQ_ALL: 	return ' >= ALL ';
			case SQLFilterType::SMALLER_OR_EQ_ANY: 	return ' <= ANY ';
			case SQLFilterType::SMALLER_OR_EQ_ALL: 	return ' <= ALL ';
		}
	}
	
	public static function escape_string( $str )
	{
		return db()->escape_str( $str );
	}
	
	public static function get_filter( $filter_type, $field_name, $filter_val, $field_type )
	{
		switch ( $filter_type )
		{
			case SQLFilterType::EQUALS:
			case SQLFilterType::BIGGER:
			case SQLFilterType::SMALLER:
			case SQLFilterType::BIGGER_OR_EQ:
			case SQLFilterType::SMALLER_OR_EQ:
			case SQLFilterType::UNEQUALS:
			{
				return $field_name . self::get_op_symbol( $filter_type ) . self::cast_to_field_type( $field_type, $filter_val );
			}
			case SQLFilterType::LIKE:
			case SQLFilterType::ILIKE:
			{
				$like	= SQLFilterType::LIKE == $filter_type ? 'LIKE' : 'ILIKE';
				$val	= self::cast_to_field_type( $field_type, $filter_val, $filter_type );
				
				if ( !empty( $val ) )
				{
					$explicit_cast = '';
					
					if ( SQLFieldType::INT == $field_type || SQLFieldType::FLOAT == $field_type )
					{
						$explicit_cast = '::text';
					}
					
					return $field_name . $explicit_cast . " $like '%" . $val . "%'";
				}
				
				return '';
			}
			case SQLFilterType::EQUALS_ANY:
			case SQLFilterType::EQUALS_ALL:
			case SQLFilterType::UNEQUALS_ANY:
			case SQLFilterType::UNEQUALS_ALL:
			case SQLFilterType::BIGGER_ANY:
			case SQLFilterType::BIGGER_ALL:
			case SQLFilterType::SMALLER_ANY:
			case SQLFilterType::SMALLER_ALL:
			case SQLFilterType::BIGGER_OR_EQ_ANY:
			case SQLFilterType::BIGGER_OR_EQ_ALL:
			case SQLFilterType::SMALLER_OR_EQ_ANY:
			case SQLFilterType::SMALLER_OR_EQ_ALL:
			{
				return self::cast_to_field_type( $field_type, $filter_val ) . self::get_op_symbol( $filter_type ) . ' ( ' . $field_name . ' ) ';
			}
			case SQLFilterType::LIKE_ANY:
			case SQLFilterType::LIKE_ALL:
			case SQLFilterType::ILIKE_ANY:
			case SQLFilterType::ILIKE_ALL:
			{
				$val	= self::cast_to_field_type( $field_type, $filter_val, $filter_type );
				
				if ( !empty( $val ) )
				{
					return "'%" . $val . "%' " . self::get_op_symbol( $filter_type ) . ' ( ' . $field_name . ' ) ';
				}
				
				return '';
			}
		}
	}
	
	public static function guess_field_type( $v )
	{
		$ft	= SQLFieldType::INT;
		
		if ( isset( $v ) )
		{
			if ( is_string( $v ) && (string)intval( $v ) != $v && (string)floatval( $v ) != $v )
			{
				$ft = SQLFieldType::STRING;
			}
			else
			{
				if ( is_int( $v ) )
				{
					$ft = SQLFieldType::INT;
				}
				else if ( is_float( $v ) )
				{
					$ft = SQLFieldType::FLOAT;
				}
				else
				{
					$ft = SQLFieldType::INT;
				}
			}
		}
		
		return $ft;
	}
	
	public static function build_query_filter( $fields, $bool_concat_op = SQLBoolOp::BOOL_AND )
	{
		$qf		= '';
		$or		= '';
		$join	= '';
		$group	= '';
		
		if ( !empty( $fields ) )
		{
			$was_or	= FALSE;
			$is_or	= FALSE;
			
			foreach ( $fields as $filter )
			{
				if (
					( 
						isset( $filter['field_name'] ) && isset( $filter['filter_val'] ) && 
						( ( '' !== $filter['filter_val'] && NULL !== $filter['filter_val'] ) || is_array( $filter['filter_val'] ) )
					) ||
					( $accept_null = isset( $filter['accept_null'] ) && TRUE == $filter['accept_null'] )
				)
				{
					if ( isset( $accept_null ) && TRUE == $accept_null && NULL == $filter['filter_val'] )
					{
						if ( isset( $filter['compare_string'] ) )
						{
							$f	= $filter['field_name'] . ( SQLFilterType::UNEQUALS == $filter['filter_type'] ? ' <> \'\'' : ' = \'\'' );
						}
						else
						{
							$f	= $filter['field_name'] . ( isset( $filter['filter_type'] ) && SQLFilterType::UNEQUALS == $filter['filter_type'] ? ' IS NOT NULL' : ' IS NULL' );
						}
					}
					else
					{
						$values = is_array( $filter['filter_val'] ) ? $filter['filter_val'] : array( $filter['filter_val'] );
						$is_arr	= count( $values );
						$c		= 0;
						$f		= '';
						
						if ( $is_arr > 1 )
						{
							$f	.= '( ';
						}
						
						$field_names = is_array( $filter['field_name'] ) ? $filter['field_name'] : array( $filter['field_name'] );
						$field_names_count = count( $field_names );
						$field_names_counting = 0;
						
						foreach ( $values as $val )
						{
							if ( $field_names_count > 1 )
							{
								$f	.= '( ';
							}
							
							foreach ( $field_names as $field_name )
							{
								$v	= isset( $filter['val_convert'] ) ? self::convert( $filter['val_convert'], $val ) : $val;
								
								if ( NULL != $v || '0' === $val || ( 0 === $val && isset($filter['field_type']) && $filter['field_type'] == SQLFieldType::INT ) )
								{
									$f	.= self::get_filter(	isset( $filter['filter_type'] ) ? $filter['filter_type'] : SQLFilterType::EQUALS, 
															$field_name,
															$v,
															isset( $filter['field_type'] ) ? $filter['field_type'] : self::guess_field_type( $v )
									);
									
									$field_names_counting++;
									
									if ( $field_names_count > 1 )
									{
										if ( $field_names_counting < $field_names_count )
										{
											$f	.= ' OR ';
										}
										else
										{
											$f	.= ' )';
										}
									}
								}
							}
							
							$c++;
							
							if ( $is_arr > 1 )
							{
								if ( $c >= $is_arr )
								{
									$f	.= ' )';
								}
								else
								{
									$f	.= ' OR ';
								}
							}
						}
					}
					
					if ( '' != $f )
					{
						$is_or	= isset( $filter['concat_op'] );
						
						$qf 	.= ( $is_or ? ' ( ' : '' ) . $f . self::get_bool_concat_op( $is_or ? $filter['concat_op'] : $bool_concat_op );
						
						if ( !$is_or && $was_or )
						{
							$qf	.= ' ) ';
						}
					
						$was_or	= $is_or;
					}
				}
				else if ( isset( $filter['order_by'] ) )
				{
					$sorts		= array( 'ASC', 'DESC' );
					
					if ( is_array( $filter['order_by'] ) && ( $order_size = count( $filter['order_by'] ) ) ==  count( $filter['order_dir'] ) )
					{
						for ( $i = 0; $i < $order_size; $i++ )
						{
							if ( isset( $filter['order_fields'] ) )
							{
								$order_k				= array_search( $filter['order_by'][$i], $filter['order_fields'] );
								$order_by				= FALSE === $order_k ? NULL : $filter['order_fields'][ $order_k ];
								
								if ( !isset( $order_by ) )
								{
									continue;
								}
							}
							else
							{
								$order_by 	= $filter['order_by'][$i];
							}
							
							if ( NULL != $filter['order_dir'][$i] && '' != $filter['order_dir'][$i] )
							{
								$sort_k		= array_search( isset( $filter['order_dir'][$i] ) ? strtoupper( $filter['order_dir'][$i] ): 'ASC', $sorts );
								$order_dir	= FALSE === $sort_k ? $sorts[0] : $sorts[ $sort_k ];
							}
							else
							{
								$order_dir	= '';
							}
							
							$or 		.= $order_by . ' ' . $order_dir;
							
							if ( $i < $order_size - 1 )
							{
								$or .= ', ';
							}
						}
						
						$or	.= ( isset( $filter['null_order'] ) ? ' NULLS ' . $filter['null_order'] : '' ) . ', ';
					}
					else
					{
						if ( isset( $filter['order_fields'] ) )
						{
							$order_k			= array_search( $filter['order_by'], $filter['order_fields'] );
							$order_by			= FALSE === $order_k ? $filter['order_fields'][0] : $filter['order_fields'][ $order_k ];
							$filter['order_by']	= $order_by;
						}
						
						$sort_k		= array_search( isset( $filter['order_dir'] ) ? strtoupper( $filter['order_dir'] ): 'ASC', $sorts );
						$order_dir	= FALSE === $sort_k ? $sorts[0] : $sorts[ $sort_k ];
					
						$or 		.= $filter['order_by'] . ' ' . $order_dir . ( isset( $filter['null_order'] ) ? ' NULLS ' . $filter['null_order'] : '' ) . ', ';
					}
				}
				else if ( isset( $filter['join'] ) && isset( $filter['on'] ) )
				{
					$join_type		= isset( $filter['type'] ) ? $filter['type'] : 'INNER';
					$join_lateral	= isset( $filter['lateral'] ) && $filter['lateral'] == TRUE ? ' LATERAL ' : '';
					$join_on		= $filter['on'];
					
					$join		.= ' ' . $join_type . ' JOIN ' . $join_lateral . $filter['join'] . ' ON ' . $join_on . ' ';
				}
				else if ( isset( $filter['group_by'] ) )
				{
					$group_by	= is_array( $filter['group_by'] ) ? implode( ', ', $filter['group_by'] ) : $filter['group_by'];
					$group		= ' GROUP BY ' . $group_by . ' ';
				}
			}
			
			if ( $is_or )
			{
				$qf	.= ' ) ';
			}
			
			if ( ( $len = strlen( $qf ) ) >= 5 )
			{
				$qf = substr( $qf, 0, $len - 4 ); // I left the last space
			}
			
			if ( ( $len = strlen( $or ) ) >= 2 )
			{
				$or = substr( $or, 0, $len - 2 );
				$or = ' ORDER BY ' . $or;
			}
		}
		
		return array( 'filter' => $qf, 'order' => $or, 'join' => $join, 'group' => $group );
	}
	
	public static function make_insert( $table, $fields, $arr_ignore = '', $field_prefix = '', $no_slashes = FALSE )
	{
		$k		= array_keys( $fields );
		$v		= array_values( $fields );
		$q		= count($k);
		$keys	= '';
		$values	= '';
		
		for( $i = 0; $i < $q; $i++ )
		{
			$ignore = false;
			
			if ( is_array( $arr_ignore ) )
			{
				foreach( $arr_ignore as $ki )
				{
					if ( $ki == $k[$i] )
					{
						$ignore = true;
					}
				}
			}
			
			if ( !$ignore && $k[$i] != 'enviar' && $k[$i] != 'send' )
			{
				$keys		.= $field_prefix.$k[$i].', ';
				
				if ( !$no_slashes )
				{
					$v[$i]		= addslashes( $v[$i] );
				}
				
				$values		.= $v[$i].', ';
			}
		}
		
		$keys	= substr( $keys, 0, strlen( $keys ) - 2 );
		$values	= substr( $values, 0, strlen( $values ) - 2 );
		
		return 'INSERT INTO '.$table.' ( '.$keys.' ) VALUES ( '.$values.' )';
	}

	public static function make_insert_pdo( $table, $fields, $arr_ignore='', $field_prefix='' )
	{
		$a = array();
		
		if ( is_array( $fields ) )
		{
			foreach( $fields as $slot )
			{
				$a[ $slot ] = '?';
			}
		}
		
		return self::make_insert( $table, $a, $arr_ignore, $field_prefix );
	}

	public static function make_update( $table, $fields, $field_id_name, $arr_ignore='', $field_prefix = '' )
	{
		if ( !is_array( $fields ) )
		{
			return '';
		}
		
		$k				= array_keys( $fields );
		$v				= array_values( $fields );
		$c				= count($k);
		$fieldsr		= '';
		$field_id_value	= null;

		for( $i=0; $i < $c; $i++ )
		{
			$ignore = false;
			
			if ( is_array( $arr_ignore ) )
			{
				foreach($arr_ignore as $ki)
				{
					if ( $ki==$k[$i] )
					{
						$ignore = true;
					}
				}
			}
			
			if ( !$ignore )
			{
				if ( $field_id_name == $field_prefix.$k[$i] || $field_id_name == $k[$i] )
				{
					$field_id_value = $v[$i];
					
					if ( $field_id_name != $field_prefix.$k[$i] )
					{
						$field_id_name = $field_prefix.$k[$i];
					}
					
					if ( !isset( $field_id_value ) )
					{
						$field_id_value = '?';
					}
				}
				else if ( $k[$i] != 'enviar' && $k[$i] != 'send' )
				{ // Filter form data
					$v[$i]		= addslashes( $v[$i] );
					$fieldsr	.= $field_prefix.$k[ $i ] . ' = ' . $v[$i] . ', ';
				}
			}
		}
		
		$fieldsr = substr( $fieldsr, 0, strlen($fieldsr) - 2 );
		
		if ( isset( $field_id_value ) )
		{
			return 'UPDATE '.$table.' SET '.$fieldsr.' WHERE '.$field_id_name.' = '.$field_id_value;
		}
		else
		{
			return '';
		}
	}

	public static function make_update_pdo( $table, $fields, $field_id_name, $arr_ignore='', $field_prefix='' )
	{
		$a = array();
		
		if ( is_array( $fields ) )
		{
			foreach( $fields as $slot )
			{
				$a[ $slot ] = '?';
			}
		}
		
		return self::make_update( $table, $a, $field_id_name, $arr_ignore, $field_prefix );
	}

	public static function make_delete( $table, $field_id_name, $field_id_value )
	{
		return 'DELETE FROM '.$table.' WHERE '.$field_id_name.' = '.$field_id_value;
	}

	public static function make_delete_pdo( $table, $field_id_name )
	{
		return self::make_delete( $table, $field_id_name, '?' );
	}
	
	public static function from_unix_time( $time )
	{
		if ( defined( 'USE_POSTGRES' ) )
		{
			return 'to_timestamp('.$time.')::TIMESTAMP';
		}
		
		return 'FROM_UNIXTIME('	. $time . ')';
	}

	public static function time_diff( $start, $end )
	{
		if ( defined( 'USE_POSTGRES' ) )
		{
			return '( ' . $start . ' - ' . $end . ' )';
		}
		
		return 'TIMEDIFF('.$start.','.$end.')';
	}

	public static function time_to_sec( $time )
	{
		if ( defined( 'USE_POSTGRES' ) )
		{
			return ' EXTRACT( EPOCH FROM ' . $time . ' )::bigint';
		}
		
		return ' TIME_TO_SEC(' . $time . ') ';
	}

	public static function secs_from( $from, $ref_time = 'CURRENT_TIMESTAMP' )
	{
		return self::time_to_sec( self::time_diff( $ref_time, self::from_unix_time( $from ) ) );
	}

	public static function secs_from_ts( $from, $ref_time = 'CURRENT_TIMESTAMP' )
	{
		return self::time_to_sec( self::time_diff( $ref_time, $from ) );
	}

	public static function unix_timestamp( $str = 'now()' )
	{	
		if ( defined( 'USE_POSTGRES' ) )
		{
			return "($str)::abstime::int4";
		}
		
		return "UNIX_TIMESTAMP($str)";
	}

	public static function get_days_back( $days = 31 )
	{
		if ( defined( 'USE_POSTGRES' ) )
		{
			return 'NOW() - INTERVAL \''. ( (string) $days ) .' day\'';
		}
		
		return 'DATE_SUB(NOW(), INTERVAL '. ( (string) $days ) .' DAY)';
	}
	
	public static function get_days_future( $days = 31 )
	{
		if ( defined( 'USE_POSTGRES' ) )
		{
			return 'NOW() + INTERVAL \''. ( (string) $days ) .' day\'';
		}
		
		return 'DATE_ADD(NOW(), INTERVAL '. ( (string) $days ) .' DAY)';
	}

	public static function get_seconds_back( $seconds = 30 )
	{
		if ( defined( 'USE_POSTGRES' ) )
		{
			return 'NOW() - INTERVAL \''. ( (string) $seconds ) .' SECOND\'';
		}
		
		return 'DATE_SUB(NOW(), INTERVAL '. ( (string) $seconds ) .' SECOND)';
	}
	
	public static function get_seconds_future( $seconds = 30 )
	{
		if ( defined( 'USE_POSTGRES' ) )
		{
			return 'NOW() + INTERVAL \''. ( (string) $seconds ) .' SECOND\'';
		}
		
		return 'DATE_ADD(NOW(), INTERVAL '. ( (string) $seconds ) .' SECOND)';
	}

	public static function get_31_days_back()
	{
		return self::get_days_back( 31 );
	}
	
	public static function format_timestamp( $field )
	{
		if ( defined('USE_POSTGRES' ) )
		{
			return "to_char($field,'YYYY-MM-DD HH24:MI:SS')";
		}
		
		return $field;
	}
	
	public static function get_op_filter( $filter, $field_name, $convert_type = SQLConvertType::NONE, $op = ' OR ' )
	{
		$where = '';
		
		if ( NULL != $filter && !empty( $filter ) )
		{
			if ( is_array( $filter ) )
			{
				foreach ( $filter AS $field_id )
				{
					$v	= self::convert( $convert_type, $field_id );
					$f	= self::get_filter( SQLFilterType::EQUALS, $field_name, $v, self::guess_field_type( $v ) );
					
					$where .= $f . $op;
				}
				
				$where = substr( $where, 0, strlen($where) - 4 );
				
				$where = ' ( ' . $where . ' ) ';
			}
			else
			{
				$v	= self::convert( $convert_type, $filter );
				
				if ( 0 != intval( $v ) )
				{
					$f	= self::get_filter( SQLFilterType::EQUALS, $field_name, $v, self::guess_field_type( $v ) );
					$where = $f . ' ';
				}
			}
		}
		
		return $where;
	}
	
	public static function get_or_filter( $filter, $field_name, $convert_type = SQLConvertType::NONE )
	{
		return self::get_op_filter( $filter, $field_name, $convert_type, ' OR ' );
	}
	
	public static function get_and_filter( $filter, $field_name, $convert_type = SQLConvertType::NONE )
	{
		return self::get_op_filter( $filter, $field_name, $convert_type, ' AND ' );
	}
	
	public static function prepare_filter( &$where, $filter = NULL, $is_count = FALSE )
	{
		$owhere		= $where;
		$join		= isset( $filter['join'] ) ? $filter['join'] : '';
		$has_where	= ( isset( $where ) && '' != $where ) || ( isset( $filter['filter'] ) && '' != $filter['filter'] );
		
		if ( $has_where && -1 == str_starts_with( 'WHERE', $where ) )
		{
			$where = 'WHERE ' . $where;
		}
		
		$where = $join . $where;
		
		if ( isset( $filter['filter'] ) && '' != $filter['filter'] )
		{
			if ( '' != $owhere )
			{
				$where .= ' AND ';
			}
		}
		
		$where .= ( isset( $filter['filter'] ) ? $filter['filter'] : '' ) .	( isset( $filter['group'] ) ? $filter['group'] : '' ) . 
																			(  $is_count ? '' : isset( $filter['order'] ) ? $filter['order'] : '' );
	}
	
	public static function build_filter( $filter, $where = '', $is_count = FALSE, $bool_concat_op = SQLBoolOp::BOOL_AND )
	{
		$query_filter = SQL::build_query_filter( $filter, $bool_concat_op );
		
		$twhere = $where;
		
		SQL::prepare_filter( $twhere, $query_filter, $is_count );
		
		return $twhere;
	}
	
	public static function get_or_filter_exploded( $filter, $field_name, $explode_char = ',', $convert_type = SQLConvertType::NONE )
	{
		return self::get_or_filter( explode( $explode_char, $filter ), $field_name, $convert_type );
	}
	
	public static function get_and_filter_exploded( $filter, $field_name, $explode_char = ',', $convert_type = SQLConvertType::NONE )
	{
		return self::get_and_filter( explode( $explode_char, $filter ), $field_name, $convert_type );
	}
	
	public static function arr_to_ints( $arr )
	{
		foreach( $arr as &$v )
		{
			$v = intval( $v );
		}
		
		return $arr;
	}
	
	public static function data_from_prefix( $array, $prefix )
	{
		$ret = array();
		
		if ( isset( $array ) )
		{
			if ( !is_array( $array ) )
			{
				$array = get_object_vars( $array );
			}
			
			if ( is_array( $array ) )
			{
				foreach ( $array as $key=>$val )
				{
					if ( -1 != str_starts_with( $prefix, $key ) )
					{
						$ret[$key] = $val;
					}
				}
			}
		}
		
		return !empty( $ret ) ? $ret : NULL;
	}
	
	public static function arr_to_str( $array )
	{
		return str_replace( array( '{', '}' ), array( '', '' ), $array );
	}
	
	public static function timestamp_to_db_format( $epoch )
	{
		$date = new DateTime();
		$date->setTimestamp( $epoch );
		return $date->format('Y-m-d H:i:s');
	}
	
	public static function byte_array_to_bytea( $barray )
	{
		return '\\x' . byte_array_to_hexa( $barray );
	}
	
	public static function bytea_to_byte_array( $bytea )
	{
		$raw = array();
		$count = strlen( $bytea );
		
		if ( $count > 2 )
		{
			$bytea = substr( $bytea, 2 );
			$count -= 2;
			
			if ( $count % 2 == 0 )
			{
				for ( $i = 0; $i < $count; $i+=2 )
				{
					$raw[] = hexdec( substr( $bytea, $i, 2 ) );
				}
			}
		}
		
		return $raw;
	}
}
