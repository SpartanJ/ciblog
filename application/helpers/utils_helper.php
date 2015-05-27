<?php

function recover_db_date( $date )
{
	$exp	= explode( ' ', $date );
	$dat	= explode( '-', $exp[0] );
	$tim	= explode( ':', $exp[1] );
	
	$rdat	= $dat[2] . '/' . $dat[1] . '/' . $dat[0];
	$rtim	= $tim[0] . ':' . $tim[1];
	
	return $rdat . ' ' . $rtim;
}

function recover_db_time( $time )
{
	$dat	= explode( '-', $time );
	
	$rdat	= $dat[2] . '/' . $dat[1] . '/' . $dat[0];
	
	return $rdat;
}

function get_pic_url( $pic )
{
	return base_url('assets/uploads/'.$pic);
}

function to_db_datetime( $date )
{
	return get_instance()->db->query("SELECT STR_TO_DATE( '" . get_instance()->db->escape_str( $date ) . "' , '%d/%m/%Y %H:%i') as date")->row()->date;
}

function to_db_date( $date )
{
	return get_instance()->db->query("SELECT STR_TO_DATE( '" . get_instance()->db->escape_str( $date ) . "' , '%d/%m/%Y') as date")->row()->date;
}

function format_date_time( $date )
{
	if ( !$date )
	{
		return null;
	}
	
	$exp	= explode( ' ', $date );
	$dat	= explode( '/', $exp[0] );
	
	$rdat	= $dat[2] . '-' . $dat[1] . '-' . $dat[0];
	$rtim	= $exp[1] . ':00';
	
	return $rdat . ' ' . $rtim;
}

function format_dateh_time( $date, $hour )
{
	return format_date_time( $date . ' ' . $hour );
}

function rand_string( $length, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' )
{
	$str = '';
	$count = strlen($charset);
	
	while ($length--) {
		$str .= $charset[ mt_rand( 0, $count - 1 ) ];
	}
	
	return $str;
}

function get_password_hash( $pass )
{
	return hash( 'sha256', $pass );
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
			}
			else
			{
				$pos = -1;
				break;
			}
		}
	}

	return $pos;
}

/** pretty-prints json data, for human readability */
function json_pp($json) {
	$tab = "  ";
	$new_json = "";
	$indent_level = 0;
	$in_string = false;

	$json_obj = json_decode($json);

	if($json_obj === false)
	return false;

	$json = json_encode($json_obj);
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
					$new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
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
					$new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
				}
				else
				{
					$new_json .= $char;
				}
				break;
			case ',':
				if(!$in_string)
				{
					$new_json .= ",\n" . str_repeat($tab, $indent_level);
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

	echo $new_json;
}

function json_enc( $data ) {
	return json_pp( json_encode( $data ) );
}

function to_blog_date($time)
{
	$months = array ("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
	$t = strtotime($time);
	$m = date('n',$t);
	$d = date('d',$t);
	$y = date('Y',$t);
	return $d.' de '.$months[$m].' del '.$y;
}
