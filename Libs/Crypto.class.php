<?php

/**
 * Minimum PHP 7.1.0+
 *
 */

require_once(dirname(__DIR__).'/Exceptions/CryptoException.php');

class Crypto
{
	// Set the default cipher
    protected $cipher = "AES-256-CBC";
    
    protected $options = 0;
	
	// Set the encryption key
	protected $key = "your_secret_key_here";

    private $errors = [];

    private $lastCipher = null;
    
    public function encrypt($plaintext, $cipher = null)
    {
        $cipher = is_null($cipher) ? $this->cipher : $cipher;

        $this->lastCipher = $cipher;

        try
        {
            /**
             * first, we need to check if selected cipher is available
             */
             
            if( !in_array($cipher, openssl_get_cipher_methods()) )
                throw new CryptoException("Selected cipher ({$cipher}) isn't available");
            
            /**
             * generate iv random string and encrypt $plaintext
             */
                
        	$ivlen = openssl_cipher_iv_length($cipher);
        	$iv = openssl_random_pseudo_bytes($ivlen);
    	    $encrypted = openssl_encrypt($plaintext, $cipher, $this->key, $this->options, $iv);
        	if( $encrypted === false )
                throw new CryptoException("Failed to encrypt string");

        	/**
             * encryption is successful
        	 * then set output structures
        	 * here we use base64_encode, json_encode, and bin2hex encoding
        	 */
        			 
        	$result = array(
        		'd'		=> $encrypted,
        		'i'		=> base64_encode($iv),
        		'c'		=> $cipher
        		);

        	return base64_encode(json_encode($result));
        }
        catch(CryptoException $ce)
        {
            $this->addError($ce->getMessage());

            return false;
        }
    }
    
    public function decrypt($encrypted)
    {
        try
        {
            /**
             * first decode the base64
             * then decode the json
             */
            
        	$json = base64_decode($encrypted);
            $o = json_decode($json);
            if( json_last_error() !== JSON_ERROR_NONE )
                throw new CryptoException("Failed to parsing JSON 1");
            
            /**
            * d for main encrypted data
            * i for iv (must base64 decoded first)
            * c for cipher mode (must hex2bin decoded first)
            * t for tag (must base64 decoded first)
            */

            $_data = $o->d;
            $_iv = base64_decode($o->i);
            $_cipher = $o->c;

            if( json_last_error() !== JSON_ERROR_NONE )
                throw new CryptoException("Failed to parsing JSON 2");

            $this->lastCipher = $_cipher;
            	
            /**
            * check if the selected cipher is available
            */

            if( !in_array($_cipher, openssl_get_cipher_methods()) )
                throw new CryptoException("Failed to decrypt text, cipher ({$cipher}) isn't supported");
            
            /**
            * show the result of the decryption!
            * decrypted data on success and false on failure
            */
            	     
            return openssl_decrypt($_data, $_cipher, $this->key, $this->options, $_iv);
        }
        catch(CryptoException $ce)
        {
            $this->addError($ce->getMessage());

            return false;
        }
	}

    public function getCipher()
    {
        return $this->lastCipher;
    }

    private function addError($message)
    {
        $this->errors[] = $message;
    }

    public function debug($print = false)
    {
        $error = implode(PHP_EOL, $this->errors);

        if( $print):
            print_r($error);
        else:
            return $error;
        endif;
    }
}

?>