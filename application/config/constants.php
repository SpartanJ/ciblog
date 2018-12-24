<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');
define('EXT', '.php');

define( 'ROOTPATH'			, dirname( realpath( __FILE__ ) ) . '/../../'  );
define( 'SERVER_NAME'		, php_uname( 'n' ) );
define( 'PAGE_HOST'			, isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '' );
define( 'DEBUG'				, TRUE );

define( 'CIBLOG_DB_PASSWORD_SALT'	, 'ciblog_salt' );

define( 'CIBLOG_ADMIN_LEVEL'		, 1000  );
define( 'CIBLOG_EDITOR_LEVEL'		, 800	);
define( 'CIBLOG_AUTHOR_LEVEL'		, 600	);
define( 'CIBLOG_SUSCRIBER_LEVEL'	, 0		);
define( 'CIBLOG_RECAPTCHA_SITE_KEY'	, '6LcZJRUUAAAAAFODJ5M-c8rpGJNmp5hOdLgPcJ6F' );
define( 'CIBLOG_RECAPTCHA_SECRET'	, '6LcZJRUUAAAAAOrm6qa2FJDQO3hW1LJiGNku-oX8' );

define( 'PAGE_TITLE'				, 'ciblog' );
define( 'PAGE_DESCRIPTION'			, 'ciblog description' );
define( 'PAGE_LANG'					, 'en' );

/* End of file constants.php */

/* Location: ./application/config/constants.php */
