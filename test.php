<?php

require_once(__DIR__.'/Libs/Crypto.class.php');

# Initializing Crypto class
$crypto = new Crypto();

$plaintext = "This is example text";
echo "Plain Text = ".$plaintext;


echo "<br/><br/>";


# Encrypt some words (default cipher)
$encrypted1 = $crypto->encrypt($plaintext);
echo "# Encryption with default cipher (".$crypto->getCipher().")<br/>";
echo $encrypted1;
if( !$encrypted1 )
	echo $crypto->debug(true);


echo "<br/><br/>";


# Encrypt some words (custom cipher : AES-128-CBC)
$encrypted2 = $crypto->encrypt($plaintext, "AES-128-CBC");
echo "# Encryption with custom cipher (".$crypto->getCipher().")<br/>";
echo $encrypted2;
if( !$encrypted2 )
	echo $crypto->debug(true);


echo "<br/><br/>";


# Decrypt the encrypted text
$decrypted = $crypto->decrypt($encrypted1);
echo "# Decrypt<br/>";
echo $decrypted."<br/>";
if( !$decrypted )
	echo $crypto->debug(true);

# Done ;-)

?>