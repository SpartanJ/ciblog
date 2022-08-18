<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Encrypt extends CI_Encrypt
{
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->_mcrypt_exists = ( ! function_exists('openssl_encrypt')) ? FALSE : TRUE;

		if ($this->_mcrypt_exists === FALSE)
		{
			show_error('The Encrypt library requires the OpenSSL extension.');
		}

		log_message('debug', "MY_Encrypt Class Initialized");
	}
	
	function mcrypt_encode($data, $key)
	{
		$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
		return base64_encode( $iv.$hmac.$ciphertext_raw );
	}

	function mcrypt_decode($data, $key)
	{
		$c = base64_decode($data);
		$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv = substr($c, 0, $ivlen);
		$hmac = substr($c, $ivlen, $sha2len=32);
		$ciphertext_raw = substr($c, $ivlen+$sha2len);
		$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
		$calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
		return !is_bool($calcmac) && !is_bool($hmac) ? ( hash_equals($hmac, $calcmac) ? $original_plaintext : NULL ) : NULL;
	}
}
