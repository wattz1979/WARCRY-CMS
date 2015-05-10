<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Session
{
	protected $_state;
	protected $_key;
	protected $_path;
	protected $_name;
	protected $_ivSize;
	protected $_keyName;
		
	public function __construct()
	{		
		// set default sessions save handler
		ini_set('session.save_handler', 'files');
        
	}

	protected function _start()
	{
	  global $config;
	  
		session_name($config['AuthCookieName'].'_hash');

		session_start();
		
        $this->_state =	'active';
		
		return true;
	}

	public function register()
	{
		session_set_save_handler(array($this, '_open'),
	                             array($this, '_close'),
						         array($this, '_read'),
						         array($this, '_write'),
						         array($this, '_destroy'),
						         array($this, '_clean')
						        );
								
		//Start the session if needed
		if(!isset($_SESSION))
		{
		  $this->_start();
		}

	}
			
	protected function _randomKey($length=32) {
	     if(function_exists('openssl_random_pseudo_bytes')) {
    	      $rnd = openssl_random_pseudo_bytes($length, $strong);
        	  if($strong === TRUE) 
        	       return $rnd;
	     }
	     for ($i=0; $i<$length; $i++) {
	      $sha = sha1(mt_rand());
		  $char = mt_rand(0,30);
		  $rnd = chr(hexdec($sha[$char].$sha[$char+1]));
	     }	
	     return $rnd;
	}

    public function _open($save_path, $session_name)
	{
      	$this->_path = $save_path . '/';	
	  	$this->_name = $session_name;
	  	$this->_keyName = "KEY_" . $session_name;
	  	$this->_ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
 
		if (empty($_COOKIE[$this->_keyName])) {
		    
	     	$keyLength = mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
		 
	     	$this->_key = $this->_randomKey($keyLength);
		 
	     	$cookie_param = session_get_cookie_params();
         	setcookie(
                  $this->_keyName,
                  base64_encode($this->_key),
                  $cookie_param['lifetime'],
                  $cookie_param['path'],
                  $cookie_param['domain'],
                  $cookie_param['secure'],
                  $cookie_param['httponly']
         	);
		 
		} else {
	     	$this->_key = base64_decode($_COOKIE[$this->_keyName]);
		} 
	
	  return true;
    }

    public function _close() {
        return true;
    }

    public function _read($id)
	{
        $sess_file = $this->_path . $this->_name . "_" . $id;
  		$data = @file_get_contents($sess_file);
		
  		if (empty($data)) {
  			return false;
  		}
		
  		$iv = substr($data, 0, $this->_ivSize);
  		$encrypted = substr($data, $this->_ivSize);
  	    $decrypt = mcrypt_decrypt(
             MCRYPT_RIJNDAEL_256,
             $this->_key,
             $encrypted,
             MCRYPT_MODE_CBC,
             $iv
        );
		
      return rtrim($decrypt, "\0"); 
    }

    public function _write($id, $data)
	{
        $sess_file = $this->_path . $this->_name . "_" . $id;
		$iv = mcrypt_create_iv($this->_ivSize, MCRYPT_RAND);
		
		if ($fp = @fopen($sess_file, "w")) {
			
	     	$encrypted = mcrypt_encrypt(
                  MCRYPT_RIJNDAEL_256,
                  $this->_key,
                  $data,
                  MCRYPT_MODE_CBC,
                  $iv
            );
			
	      	$return = fwrite($fp, $iv.$encrypted);
			
	      	fclose($fp);
			
	      return $return;
		} else {
	      return false;
	 	}
    }

    public function _destroy($id)
	{
		//Session was already destroyed
		if ($this->_state === 'destroyed') {
			return true;
		}
		
        $sess_file = $this->_path . $this->_name . "_" . $id;
		
		$CookieInfo = session_get_cookie_params();
		
        setcookie($this->_keyName, '', time()-3600, $CookieInfo['path'], $CookieInfo['domain']);

		//the session cookie must be deleted.
		if (isset($_COOKIE[session_name()]))
		{
			setcookie(session_name(), '', time()-3600, $CookieInfo['path'], $CookieInfo['domain']);
		}
		
		$this->_state = 'destroyed';
		
	  return(@unlink($sess_file));
    }

    public function _clean($max)
	{
    	foreach (glob($this->_path . $this->_name . '_*') as $filename) {
    	     if (filemtime($filename) + $max < time()) {
      	          @unlink($filename);
    	     }
  		}
		
  	  return true;
    }			
}