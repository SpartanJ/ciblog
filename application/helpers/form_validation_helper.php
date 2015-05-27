<?php
define( "REGEXP_PHONE", "/^[\(]?(\d{0,5})[\)]?[\s]?[\-]?(\d{3,4})[\s]?[\-]?(\d{4})[\s]?[x]?(\d*)$/");
define( "REGEXP_EMAIL", "/^[A-z0-9][\w.-]*@[A-z0-9][\w\-\.]+\.[A-z0-9]{2,6}$/");
define( "REGEXP_WEB","/^[A-Za-z0-9][A-Za-z0-9-]*[A-Za-z0-9](.[A-Za-z0-9][A-Za-z0-9-]*[A-Za-z0-9])+$/");
define( "REGEXP_DATE", "/^(0?[1-9]|[12][0-9]|3[01])[-\/](0?[1-9]|1[012])[-\/](19|20)[0-9][0-9]$/" ); //dd-mm-yyyy
define( "REGEXP_DNI", "/^(\d{1,3})[.]?(\d{3})[.]?(\d{3})$/");
define( "REGEXP_PASS", "/^.{5,}$/");
define( "REGEXP_MINCHARS", "/^.{3,}$/");
define( "REGEXP_URL", "/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/");
define( "REGEXP_HOUR", "/^((?:[01]\d)|(?:2[0-3])):([0-5]\d)$/");
define( "REGEXP_FLOAT", "/^[-+]?[0-9]*\.?[0-9]+/" );

define( "LS_GF_REQFIELD", "Campo Requerido" );
define( "LS_GF_OKFIELD", "OK" );
define( "LS_GF_PASSNC", "Las claves deben coincidir." );
define( "LS_GF_ERRSEND", "No se puede envíar el formulario porque contiene errores." );
define( "LS_GF_OKSEND", "El formulario se envio correctamente" );

function validate_field( $value, $type = 'any', $req = FALSE, $passopt = '' )
{
	static $validation_array;
	
	if ( !isset( $validation_array ) )
	{
		$validation_array = array (
			'any'   => array( 'regex' => '/^$/', 'emsg' => ''),
			'min'   => array( 'regex' => REGEXP_MINCHARS, 'emsg' => 'Debe ingresar como mínimo 3 caracteres'),
			'num'   => array( 'regex' => '/^\d+$/', 'emsg' => 'Ingrese solo números'),
			'alpha' => array( 'regex' => '/^[a-z\sA-Z|á|é|í|ó|ú|ñ]+$/', 'emsg' => 'Ingrese solo letras'),
			'address' => array( 'regex' => '/^([a-z\sA-Z0-9\.\,\°\(\)])+$/', 'emsg' => 'Solo números y letras'),
			'alnum' => array( 'regex' => '/^[a-z\sA-Z0-9]+$/', 'emsg' => 'Solo números y letras'),
			'alphanum' => array( 'regex' => '/^[a-z\sA-Z0-9\.\,\°\(\)|á|é|í|ó|ú|ñ]+$/', 'emsg' => 'Caracteres inválidos'),
			'email' => array( 'regex' => REGEXP_EMAIL, 'emsg' => 'Ingrese un email válido'),
			'web' => array( 'regex' => REGEXP_WEB, 'emsg' => 'Dirección web inválida'),
			'url' => array( 'regex' => REGEXP_URL, 'emsg' => 'Dirección web inválida'),
			'date' => array( 'regex' => REGEXP_DATE, 'emsg' => 'Fecha inválida: DD/MM/YYYY'),
			'phone' => array( 'regex' => REGEXP_PHONE, 'emsg' => 'Telefono inválido'),
			'pass' => array( 'regex' => REGEXP_PASS, 'emsg' => 'Debe ingresar más de 5 caracteres'),
			'dni' => array( 'regex' => REGEXP_DNI, 'emsg' => 'Ingrese un DNI válido'),
			'float' => array( 'regex' => REGEXP_FLOAT, 'emsg' => 'Valor inválido' ),
			'textarea' => array( 'regex' => '/^\w+$/', 'emsg' => ''),
			'select' => array( 'regex' => '/^\w+$/', 'emsg' => ''),
			'hour' => array ( 'regex' => REGEXP_HOUR, 'emsg' => 'Hora inválida.'),
			'hexa1' => array ( 'regex' => '/^[a-fA-F0-9]{1}$/'	, 'emsg' => 'Fuera de Rango' ),
			'hexa2' => array ( 'regex' => '/^[a-fA-F0-9]{1,2}$/', 'emsg' => 'Fuera de Rango' ),
			'hexa3' => array ( 'regex' => '/^[a-fA-F0-9]{1,3}$/', 'emsg' => 'Fuera de Rango' ),
			'hexa4' => array ( 'regex' => '/^[a-fA-F0-9]{1,4}$/', 'emsg' => 'Fuera de Rango' ),
			'hexa' => array ( 'regex' => '/^[a-fA-F0-9]+$/', 'emsg' => 'Fuera de Rango' )
		);
	}

	$a		= $validation_array[ $type ];
	$empty	= trim( $value ) == '';
	
	if( $empty )
	{
		if( $req )
		{
			return LS_GF_REQFIELD;
		}
		else
		{
			return ''; 				# OK
		}
	}
	
	if( $type == 'passconf' )
	{
		if( $value != $passopt ) 		# password no igual al original
		{
			return LS_GF_PASSNC;
		}
		else
		{
			return '';
		}
	}
	
	if( $type != 'text' && !preg_match( $a['regex'], $value ) ) # error, no cumple con el regex
	{
		return $a['emsg'];
	}
	else
	{
		return ''; 					# OK
	}
}

function get_mails_from_string( $str )
{
	$sup = '';

	if ( '' != $str )
	{
		$mails = explode( ',', $str );

		foreach ( $mails as $mail )
		{
			$mail = trim( $mail );

			if ( strstr($mail, '@' ) )
			{
				if ( '' == validate_field( $mail, 'email', false ) ) {
					$sup .= $mail . ',';
				}
			}
		}

		if ( '' != $sup )
		{
			$sup = substr( $sup, 0, strlen( $sup ) - 1 );
		}
	}

	return $sup;
}
