<?php

class Crypto
{
	// Set the method
    protected static $cipher = "AES-256-CBC";
    
    protected static $options = 0;
	
	// Set the encryption key
	protected static $key = "6818f23eef19d38dad1d2729991f6368";
    
    public static function encrypt($plaintext)
    {
        /**
         * first, we need to check if selected cipher is available
         */
         
        if( in_array(self::$cipher, openssl_get_cipher_methods()) )
        {
            /**
             * generate iv random string and encrypt $plaintext
             */
            
    		$ivlen = openssl_cipher_iv_length(self::$cipher);
    		$iv = openssl_random_pseudo_bytes($ivlen);
	    	$encrypted = openssl_encrypt($plaintext, self::$cipher, self::$key, self::$options, $iv);
    		if( $encrypted !== false )
    		{
    			/**
    			 * encryption is successful
    			 * then set output structures
    			 * here we use base64_encode, json_encode, and bin2hex encoding
    			 */
    			 
    			$result = array(
    				'd'		=> $encrypted,
    				'i'		=> base64_encode($iv),
    				'c'		=> self::$cipher
    				);

    			return base64_encode(json_encode($result));
    		}
    	}

    	return false;
    }
    
    public static function decrypt($encrypted)
    {
        /**
         * first decode the base64
         * then decode the json
         */
        
    	$json = base64_decode($encrypted);
        $o = json_decode($json);
        if( is_object($o) )
        {
            /**
             * d for main encrypted data
             * i for iv (must base64 decoded first)
             * c for cipher mode (must hex2bin decoded first)
             * t for tag (must base64 decoded first)
             */

        	$_data = $o->d;
        	$_iv = base64_decode($o->i);
        	$_cipher = $o->c;
        	
        	/**
        	 * check if the selected cipher is available
        	 */

        	if( in_array($_cipher, openssl_get_cipher_methods()) )
        	{
        	    /**
        	     * show the result of the decryption!
        	     * decrypted data on success and false on failure
        	     */
        	     
	        	return openssl_decrypt($_data, $_cipher, self::$key, self::$options, $_iv);
	        }
        }

        return false;
	}
}

?>