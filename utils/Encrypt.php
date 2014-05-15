<?php


/**
 * Very quick encryption class
 */


namespace fieldwork\utils;


class Encrypt {
	
	//	Replace this with an application specific key
	private $_key = 'K$CPl7fyn4htp0=F(IW[Lj3V`yF5^Q0+F;skk"15vJb,GZh5Ipj%:l|,GmAQU5e';
	
	public function __construct($key = false) {
		if ($key) {
			$this->_key = $key;
		}
	}

	public function encrypt($string) {
		return base64_encode(
			mcrypt_encrypt(
				MCRYPT_RIJNDAEL_256, 
				md5($this->_key), 
				$string, 
				MCRYPT_MODE_CBC, 
				md5(md5($this->_key))
			)
		);
	}
	
	public function decrypt($encrypted) {
		return rtrim(
			mcrypt_decrypt(
				MCRYPT_RIJNDAEL_256, 
				md5($this->_key), 
				base64_decode($encrypted), 
				MCRYPT_MODE_CBC, 
				md5(md5($this->_key))
			), 
			"\0"
		);
	}

}



?>