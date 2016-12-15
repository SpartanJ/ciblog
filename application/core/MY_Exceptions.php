<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions {
	public function log_exception($severity, $message, $filepath, $line)
	{
		$severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;
		log_message('error', 'Severity: '.$severity.' --> '.$message.' '.$filepath.' '.$line);
		log_message('error', 'Backtrace:' );
		
		$backtrace = debug_backtrace();
		
		if ( isset( $backtrace ) && !empty( $backtrace ) )
		{
			foreach ( $backtrace as $t )
			{
				if ( isset($t['file']) )
				{
					log_message('error', "file: " . $t['file'] . ":" . $t['line'] . " func: " . $t['function'] );
				}
				else
				{
					log_message('error', "file: ?:? func: " . $t['function'] );
				}
			}
		}
	}
}
