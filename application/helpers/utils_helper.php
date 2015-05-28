<?php

function datetime_noms( $datetime )
{
	$date			= explode( '.', $datetime );
	return $date[0];
}

function timestamp_to_str( $timestamp, $def_val = '', $include_seconds = FALSE )
{
	if ( NULL == $timestamp )
	{
		return $def_val;
	}
	
	return strftime ('%d/%m/%Y %H:%M' . ( $include_seconds ? ':%S' : '' ), $timestamp );
}

function get_size($path)
{
	$bytes = array('B','KB','MB','GB','TB');
	
	if (file_exists($path))
		$size = filesize($path);
	else
		return '';	
	
	foreach($bytes as $val)
	{
		if($size > 1024){
			$size = $size / 1024;
		} else break;
	}
	return round($size, 2)." ".$val;
}

function get_ext($file)
{
	$arr = explode('.', $file);
	return strtolower($arr[count($arr)-1]);
}

function trace( $err )
{
	$file = @fopen ( ROOTPATH . '/traced_errors', 'a' );
	@fputs ( $file, $err."\n" );
	@fclose ($file);
}

function add_include_path($path)
{
	set_include_path( get_include_path() . PATH_SEPARATOR . $path );
}

/** pretty-prints json data, for human readability */
function json_pp( $json, $html = false )
{
	$nl = $html ? "<br/>\n" : "\n";
	$tab = "  ";
	$new_json = "";
	$indent_level = 0;
	$in_string = false;

	$len = strlen($json);

	for($c = 0; $c < $len; $c++)
	{
		$char = $json[$c];
		switch($char)
		{
			case '{':
			case '[':
				if(!$in_string)
				{
					$new_json .= $char . $nl . str_repeat($tab, $indent_level+1);
					$indent_level++;
				}
				else
				{
					$new_json .= $char;
				}
				break;
			case '}':
			case ']':
				if(!$in_string)
				{
					$indent_level--;
					$new_json .= $nl . str_repeat($tab, $indent_level) . $char;
				}
				else
				{
					$new_json .= $char;
				}
				break;
			case ',':
				if(!$in_string)
				{
					$new_json .= "," . $nl . str_repeat($tab, $indent_level);
				}
				else
				{
					$new_json .= $char;
				}
				break;
			case ':':
				if(!$in_string)
				{
					$new_json .= ": ";
				}
				else
				{
					$new_json .= $char;
				}
				break;
			case '"':
				if($c > 0 && $json[$c-1] != '\\')
				{
					$in_string = !$in_string;
				}
			default:
				$new_json .= $char;
				break;
		}
	}

	return $new_json;
}

function json_enc( $data, $html = false )
{
	return json_pp( json_encode( $data ), $html );
}

function json_enc_obj( $obj, $html = false )
{
	return json_pp( json_encode( object_to_array( $obj ) ), $html );
}

function now_date()
{
	return time_to_date();
}

function time_to_date( $time = null, $set_time_zone = false )
{
	if ( $set_time_zone )
		date_default_timezone_set( 'UTC' );
	
	if ( is_null( $time ) )
		return date( 'Y-m-d H:i:s', time() );
	
	return date( 'Y-m-d H:i:s', $time );
}

function get_var( $name, $is_post = FALSE )
{
	$ret = null;
	
	if ( TRUE == DEBUG )
	{
		if ( isset( $_REQUEST[ $name ] ) )
		{
			$ret = $_REQUEST[ $name ];
		}
		
	}
	
	if ( $is_post )
	{
		if ( isset( $_POST[ $name ] ) )
		{
			$ret = $_POST[ $name ];
		}
	}
	
	if ( isset( $_GET[ $name ] ) )
	{
		$ret = $_GET[ $name ];
	}
	
	/* We strip slashes if magic quotes is on to keep things consistent

	   NOTE: In PHP 5.4 get_magic_quotes_gpc() will always return 0 and
		 it will probably not exist in future versions at all.
	*/
	if ( ! is_php('5.4') && get_magic_quotes_gpc())
	{
		$ret = stripslashes($ret);
	}

	return $ret;
}

function get_post( $name )
{
	return get_var( $name, true );
}

function get_var_def( $name, $default, $is_post = FALSE )
{
	$res = get_var( $name, $is_post );
	
	return NULL != $res ? $res : $default;
}

function get_post_def( $name, $default )
{
	return get_var_def( $name, $default, TRUE );
}

function getlocale( $category = LC_ALL )
{
	return setlocale( $category, NULL );
}

function simple_mail( $from, $to, $subject, $msg, $reply_to = '' )
{
	if ( '' == $to ) { return; }

	CI()->load->library('email');
	CI()->email->from( $from );
	CI()->email->to($to); 
	CI()->email->subject($subject);
	CI()->email->message($msg);
	
	if ( '' != $reply_to )
	{
		CI()->email->reply_to( $reply_to );
	}
	
	CI()->email->send();
}

function rand_string( $length, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' )
{
	$str	= '';
	$count	= strlen($charset);
	
	while ($length--)
	{
		$str .= $charset[ mt_rand( 0, $count - 1 ) ];
	}
	
	return $str;
}

function str_starts_with( $start, $str )
{
	$pos = -1;
	$start_size = strlen( $start );
	if ( strlen( $str ) >= $start_size )
	{
		for ( $i = 0; $i < $start_size; $i++ )
		{
			if ( $start[$i] == $str[$i] )
			{
				$pos = $i;
			} else {
				$pos = -1;
				break;
			}
		}
	}

	return $pos;
}

function object_to_array($Class)
{
	# Typecast to (array) automatically converts stdClass -> array.
	$Class = (array)$Class;
	
	# Iterate through the former properties looking for any stdClass properties.
	# Recursively apply (array).
	foreach($Class as $key => $value)
	{
		if(is_object($value)&&get_class($value)==='stdClass')
		{
			$Class[$key] = object_to_array($value);
		}
	}
	return $Class;
}

# Convert an Array to stdClass.
function array_to_object(array $array)
{
	# Iterate through our array looking for array values.
	# If found recurvisely call itself.
	foreach($array as $key => $value)
	{
		if(is_array($value))
		{
			$array[$key] = array_to_object($value);
		}
	}
	
	# Typecast to (object) will automatically convert array -> stdClass
	return (object)$array;
}

/**
* Handles multidimentional array sorting by a key (not recursive)
*
* @author Oliwier Ptak <aleczapka at gmx dot net>
*/
class array_sorter
{
	var $skey = false;
	var $sarray = false;
	var $sasc = true;

	/**
	* Constructor
	*
	* @access public
	* @param mixed $array array to sort
	* @param string $key array key to sort by
	* @param boolean $asc sort order (ascending or descending)
	*/
	function array_sorter(&$array, $key, $asc=true)
	{
		$this->sarray = $array;
		$this->skey = $key;
		$this->sasc = $asc;
	}

	/**
	* Sort method
	*
	* @access public
	* @param boolean $remap if true reindex the array to rewrite indexes
	*/
	function sortit($remap=true)
	{
		$array = &$this->sarray;
		uksort($array, array($this, "_as_cmp"));
		if ($remap)
		{
			$tmp = array();
			while (list($id, $data) = each($array))
				$tmp[] = $data;
			return $tmp;
		}
		return $array;
	}

	/**
	* Custom sort function
	*
	* @access private
	* @param mixed $a an array entry
	* @param mixed $b an array entry
	*/
	function _as_cmp($a, $b)
	{
		//since uksort will pass here only indexes get real values from our array
		if (!is_array($a) && !is_array($b))
		{
			$a = $this->sarray[$a][$this->skey];
			$b = $this->sarray[$b][$this->skey];
		}

		//if string - use string comparision
		if (!ctype_digit($a) && !ctype_digit($b))
		{
			if ($this->sasc)
				return strcasecmp($a, $b);
			else 
				return strcasecmp($b, $a);
		}
		else
		{
			if (intval($a) == intval($b)) 
				return 0;

			if ($this->sasc)
				return (intval($a) > intval($b)) ? -1 : 1;
			else
				return (intval($a) > intval($b)) ? 1 : -1;
		}
	}
}//end of class

function multi_sort(&$array, $key, $asc=true)
{
	$sorter = new array_sorter( $array, $key, $asc );
	return $sorter->sortit();
}

function format_date( $date )
{
	if ( !$date )	return null;

	$exp	= explode( ' ', $date );
	$dat	= explode( '/', $exp[0] );

	if ( count( $dat ) != 3 )
	{
		$dat = explode( '-', $exp[0] );
	}

	return $dat[2] . '-' . $dat[1] . '-' . $dat[0];
}

function unformat_date( $date )
{
	if ( !$date )	return null;

	$exp	= explode( ' ', $date );
	$dat	= explode( '-', $exp[0] );

	return $dat[2] . '/' . $dat[1] . '/' . $dat[0];
}

function format_date_time( $date )
{
	if ( !$date )	return null;

	
	$exp	= explode( ' ', $date );
	$dat	= explode( '/', $exp[0] );
	
	$rdat	= $dat[2] . '-' . $dat[1] . '-' . $dat[0];
	$rtim	= $exp[1] . ':00';
	
	return $rdat . ' ' . $rtim;
}

function is_mobile_browser()
{
	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) )
	{
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		
		return (preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|meego.+mobile|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)));
	}
	
	return false;
}

function pp_date( $date )
{
	return strftime( '%Y-%m-%d %H:%M:%S', $date );
}

function pp_date_now()
{
	return pp_date( time() );
}

function add_date( $givendate, $day=0, $mth=0, $yr=0 )
{
	$cd = strtotime( $givendate );
	$newdate = date('Y-m-d H:i:s', mktime( date('H',$cd), date('i',$cd), date('s',$cd), date('m',$cd) + $mth, date('d',$cd) + $day, date('Y',$cd) + $yr ) );
	return $newdate;
}

function sub_date( $givendate, $day=0, $mth=0, $yr=0 )
{
	$cd = strtotime( $givendate );
	$newdate = date('Y-m-d H:i:s', mktime( date('H',$cd), date('i',$cd), date('s',$cd), date('m',$cd) - $mth, date('d',$cd) - $day, date('Y',$cd) - $yr ) );
	return $newdate;
}

function days_diff( $date_start, $date_end )
{
	return floor(($date_end - $date_start)/3600/24);
}

function uri_starts_with( $uri )
{
	return -1 != str_starts_with( $uri, $_SERVER['REQUEST_URI'] );
}

function trace_json( $arr )
{
	trace( json_enc( $arr ) );
}

function get_field_values_from_form( $fields, $form )
{
	$values = array();
	
	foreach ( $fields as $f )
	{
		array_push( $values, $form[ $f ] );
	}
	
	return $values;
}

function& CI()
{
	return get_instance();
}

function& db_get()
{
	return CI()->db;
}

function& load_model($model_name)
{
	$CI =& get_instance();
	$CI->load->model($model_name);
	return $CI->$model_name;
}

function& load_view( $view, $vars = array(), $return = FALSE )
{
	$CI =& get_instance();
	$CI->load->view( $view, $vars, $return );
	return $CI->$view;
}

function load_library($library = '', $params = NULL, $object_name = NULL)
{
	$CI =& get_instance();
	$CI->load->library( $library, $params, $object_name );
}

function lang_line($line, $log_errors = TRUE)
{
	$CI =& get_instance();
	return $CI->lang->line( $line, $log_errors );
}

function lang_line_upper($line, $log_errors = TRUE)
{
	return strtoupper( lang_line( $line, $log_errors ) );
}

function lang_line_lower($line, $log_errors = TRUE)
{
	return strtolower( lang_line( $line, $log_errors ) );
}

function lang_line_ucwords($line, $log_errors = TRUE)
{
	return ucwords( lang_line( $line, $log_errors ) );
}

function lang_line_category_name( $name )
{
	$cat_name = lang_line( $name, FALSE );
	
	return isset( $cat_name ) ? $cat_name : $name;
}

function lang_line_category_name_upper( $name )
{
	return strtoupper( lang_line_category_name( $name ) );
}

function lang_line_category_name_ucwords( $name )
{
	return ucwords( lang_line_category_name( $name ) );
}

function pipe_exec( $cmd, $async = FALSE, $input='' )
{
	$cmd  = $cmd . ( TRUE == $async ? ' > /dev/null 2>/dev/null &' : '' );
	$proc = proc_open($cmd, array(array('pipe', 'r'),
								  array('pipe', 'w'),
								  array('pipe', 'w')), $pipes);
	fwrite($pipes[0], $input);
	fclose($pipes[0]);

	$stdout = stream_get_contents($pipes[1]);
	fclose($pipes[1]);

	$stderr = stream_get_contents($pipes[2]);
	fclose($pipes[2]);

	$return_code = (int)proc_close($proc);

	return array($return_code, $stdout, $stderr);
}

function http_remove( $url )
{
	$disallowed = array('http://', 'https://');
	
	foreach( $disallowed as $d )
	{
		if( strpos($url, $d) === 0 )
		{
			return str_replace($d, '', $url);
		}
	}
	
	return $url;
}

function http_generate_get_post_string( $fields )
{
	$vars	= '';
	$sep	= '';
	
	if ( NULL != $fields && !empty( $fields ) )
	{
		foreach( $fields as $key=>$value )
		{
			if ( is_string( $key ) && is_string( $value ) )
			{
				$vars.= $sep . urlencode( $key ) . '=' . urlencode( $value );
				$sep  ='&';
			}
			else
			{
				trace( "key found as none-string: " );
				trace( json_enc( $key ) );
				trace( json_enc( $value ) );
			}
		}
	}
	
	return $vars;
}

function http_request_redirect( $to_host )
{
	$post 	= '';
	$host	= PAGE_PROTOCOL . http_remove( $to_host ) . $_SERVER['REQUEST_URI'];

	if ( isset( $_POST ) && !empty( $_POST ) )
	{
		$post	= http_generate_get_post_string( $_POST );
	}

	$cmd	= "curl --insecure --data \"$post\" $host";
	
	if ( '' != $cmd )
	{
		$res = pipe_exec( $cmd, TRUE );
	}
}

function csv_create_header( $filename )
{
	header('Pragma: public');
	header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0, max-age=0');
	header('Content-Type: application/force-download');
	header('Content-Type: application/octet-stream', false);
	header('Content-Type: application/download', false);
	header('Content-Type: text/csv', false);
	header('Content-Disposition: attachment; filename="'.$filename.'.csv"');
	header('Content-Transfer-Encoding: binary');
}

function text_plain_create_header( $filename )
{
	header('Pragma: public');
	header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0, max-age=0');
	header('Content-Type: application/force-download');
	header('Content-Type: application/octet-stream', false);
	header('Content-Type: application/download', false);
	header('Content-Type: text/plain', false);
	header('Content-Disposition: attachment; filename="'.$filename.'.txt"');
	header('Content-Transfer-Encoding: binary');
}

function pagination_config()
{
	$config							= array();
	$config['full_tag_open']		= '<div class="table_pager">';
	$config['full_tag_close']		= '</div>';
	$config['num_links']			= 5;
	$config['per_page']				= defined('LIGHTWEIGHT_RESULTS') && TRUE == LIGHTWEIGHT_RESULTS ? 50 : 500;
	$config['use_page_numbers'] 	= TRUE;
	$config['anchor_class']			= 'class="ajax-link" ';
	$config['first_link']			= '&lt;&lt;';
	$config['first_tag_open']		= '<div class="first">';
	$config['first_tag_close']		= '</div>';
	$config['last_link']			= '&gt;&gt;';
	$config['last_tag_open']		= '<div class="last">';
	$config['last_tag_close']		= '</div>';
	$config['next_link']			= '&gt;';
	$config['next_tag_open']		= '<div class="next">';
	$config['next_tag_close']		= '</div>';
	$config['prev_link']			= '&lt;';
	$config['prev_tag_open']		= '<div class="prev">';
	$config['prev_tag_close']		= '</div>';
	$config['cur_tag_open']			= '<span>';
	$config['cur_tag_close']		= '</span>';
	$config['num_tag_open']			= '<div class="digit">';
	$config['num_tag_close']		= '</div>';
	$config['page_query_string']	= TRUE;
	$config['query_string_segment']	= 'page_num';
	return $config;
}

function http_build_query_merge( $query_strings )
{
	return http_build_query( array_merge( $_GET, $query_strings ) );
}

function http_build_query_current()
{
	return http_build_query( $_GET );
}

function http_build_query_pagination()
{
	$current = $_GET;
	
	if ( isset( $current['page_num'] ) )
	{
		unset( $current['page_num'] );
	}
	
	return http_build_query( $current );
}

function order_dir_invert()
{
	return array( 'order_dir' => ( strtoupper( get_var_def( 'order_dir', 'ASC' ) ) == 'ASC' ? 'DESC' : 'ASC' ) );
}

function http_build_query_merge_auto( $query_strings )
{
	return http_build_query_merge( array_merge( $query_strings, order_dir_invert() ) );
}

function http_header_json_no_cache()
{
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json; charset=utf-8');
}

function array_merge_recursive_distinct(array &$array1, array &$array2)
{
	$merged = $array1;

	foreach($array2 as $key => &$value)
	{
		if( is_array( $value ) && isset( $merged[$key] ) && is_array( $merged[$key] ) )
		{
			$merged[$key] = array_merge_recursive_distinct( $merged[$key], $value );
		}
		else
		{
			$merged[$key] = $value;
		}
	}

	return $merged;
}

function is_valid_domain_name($domain_name)
{
	return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
			&& preg_match("/^.{1,253}$/", $domain_name) //overall length check
			&& preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
}

function unichr($u)
{
	return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
}

function array_remove_value( &$arr, $del_val )
{
	if ( ( $key = array_search( $del_val, $arr ) ) !== FALSE )
	{
		unset( $arr[$key] );
	}
}

class CiblogHelper
{
	public static function password_hash( $password )
	{
		return hash( 'sha256', CIBLOG_DB_PASSWORD_SALT . $password );
	}
	
	public static function slugify( $text )
	{ 
		// replace non letter or digits by -
		$text = preg_replace('~[^\\pL\d]+~u', '-', $text);

		// trim
		$text = trim($text, '-');

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// lowercase
		$text = strtolower($text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		if (empty($text))
		{
		return 'n-a';
		}

		return $text;
	}
	
	public static function to_blog_date($timestamp)
	{
		return unix_to_human(strtotime($timestamp), FALSE, 'eu');
	}
}
