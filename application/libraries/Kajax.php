<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*------------UTILITIES--------------*/

/*creates a jquery element selector*/
function jelem($target)
{
	return "$('" . $target . "')";
}

/*creates a jquery method call*/
function jmethod($target,$func_name,$paramstr)
{
	return jelem($target).".".$func_name."(".$paramstr.");\n";
}

/*creates a javascritp string*/
function jstr($str)
{
	$str = str_replace( "\r\n", '', $str );
	$str = str_replace( "\n", '', $str );
	$str = str_replace( '"', "\\\"", $str );
	
	return "\"".$str."\"";
}

/*---------------KAJAX----------------*/
class Kajax
{
	protected $buffer		= '';

	public function __construct()
	{
	}

	/** adds manual script */
	public function script( $js )
	{
		$this->buffer .= $js;
	}
	
	public function call( $func )
	{
		$this->buffer .= $func . ";\n";
	}

	public function alert( $text )
	{
		$this->buffer .= sprintf("alert(\"%s\");", $text);
	}

	public function olog( $obj )
	{
		$this->buffer .=  $this->log( json_encode($obj) );
	}

	public function log( $text )
	{
		$this->buffer .=  sprintf('console.log("%s");', $text);
	}

	public function html( $target, $content, $init_ajax=TRUE )
	{
		$this->buffer .= jmethod($target,'html',jstr($content));
		
		if ( TRUE == $init_ajax )
		{
			$this->call( "kajax_on_loaded_event($('".$target."'), '".current_url()."')" );
		}
	}
	
	public function text( $target, $content )
	{
		$this->buffer .= jmethod($target,'text',jstr($content));
	}
	
	public function prop( $target, $prop, $value )
	{
		$this->buffer .= jmethod($target,'prop', "'" . $prop . "', " . $value );
	}
	
	public function data( $target, $data, $value )
	{
		$this->buffer .= jmethod($target,'data', jstr($data).','.jstr($value) );
	}

	public function bind( $target, $func, $value )
	{
		$this->buffer .= jmethod($target,'bind', jstr($func). ',' . $value );
	}
	
	public function unbind( $target, $func )
	{
		$this->buffer .= jmethod($target,'unbind', jstr($func) );
	}
	
	public function append($target, $content)
	{
		$this->buffer .= jmethod($target,'append',jstr($content));
	}

	public function prepend($target, $content)
	{
		$this->buffer .= jmethod($target,'prepend',jstr($content));
	}

	public function remove($target)
	{
		$this->buffer .= jmethod($target,'remove','');
	}

	public function empty_elem($target)
	{
		$this->buffer .= jmethod($target,'empty','');
	}

	public function toggle($target)
	{
		$this->buffer .= jmethod($target,'toggle','');
	}
	
	public function show($target)
	{
		$this->buffer .= jmethod($target,'show','');
	}
	
	public function hide($target)
	{
		$this->buffer .= jmethod($target,'hide','');
	}

	public function attr($target, $attr, $value)
	{
		$this->buffer .= jmethod($target,'attr', jstr($attr).','.jstr($value) );
	}
	
	public function removeAttr($target, $attr)
	{
		$this->buffer .= jmethod($target,'removeAttr', jstr($attr) );
	}

	public function href($target, $value)
	{
		$this->attr($target,'href',$value);
	}

	public function rel($target, $value)
	{
		$this->attr($target,'rel',$value);
	}
	
	public function src($target, $value)
	{
		$this->attr($target,'src',$value);
	}
	
	public function css($target, $prop, $value)
	{
		$this->buffer .= jmethod($target,'css', jstr($prop).','.jstr($value) );
	}

	public function addClass($target, $class)
	{
		$this->buffer .= jmethod($target,'addClass', jstr($class));
	}

	public function removeClass($target, $class)
	{
		$this->buffer .= jmethod($target,'removeClass', jstr($class));
	}

	public function toggleClass($target, $class)
	{
		$this->buffer .= jmethod($target,'toggleClass', jstr($class));
	}

	public function val($target, $val)
	{
		$this->buffer .= jmethod($target,'val', jstr($val));
	}
	
	public function reload()
	{
		$this->buffer .= 'window.location = window.location.href;';
	}

	public function redirect($url)
	{
		$this->buffer .= 'window.location = "'.$url.'"';
	}
	
	public function load_target( $url )
	{
		$this->call( "kajax_load_target( '$url' )" );
	}
	
	public function reload_target()
	{
		$this->call( "kajax_reload_target()" );
	}
	
	public function load( $target, $url, $clean = FALSE )
	{
		$this->call( "kajax_load" . ( $clean ? '_clean' : '' ) . "( '$target', '$url' )" );
	}
	
	public function fadeIn($target, $duration)
	{
		$this->buffer .= jmethod($target,'fadeIn',jstr($duration));
	}

	public function fadeOut($target, $duration)
	{
		$this->buffer .= jmethod($target,'fadeIn',jstr($duration));
	}

	public function fancy_alert( $text )
	{
		$this->call( "alertify.alert( " . jstr( $text ) . " ) " );
	}
	
	public function fancy_log( $text )
	{
		$this->call( "alertify.log( " . jstr( $text ) . " ) " );
	}

	public function fancy_log_success( $text )
	{
		$this->call( "alertify.success( " . jstr( $text ) . " ) " );
	}

	public function fancy_log_error( $text )
	{
		$this->call( "alertify.error( " . jstr( $text ) . " ) " );
	}

	public function out($print_tags = FALSE, $return = FALSE)
	{
		$out = $print_tags ? "<script type=\"text/javascript\">\n$(function(){\n"  . $this->buffer . "\n})</script>\n" : $this->buffer;
		
		if ( !$return )
		{
			echo $out;
		}
		else
		{
			return $out;
		}
	}
	
	public function buffer_clear()
	{
		$this->buffer = '';
	}
	
	public function buffer_get()
	{
		return $this->buffer;
	}
	
	public function buffer_is_empty()
	{
		return empty( $this->buffer );
	}
}
