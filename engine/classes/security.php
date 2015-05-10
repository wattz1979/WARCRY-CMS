<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Security extends CORE
{
	private $config;
	private $db = false;
	
	public function __construct()
	{
		global $config, $DB;
		
		$this->config = $config;
		//$this->db = $DB;
	}
	
	//A must-have function
	public function unregisterGlobals()
	{
		$register_globals = @ini_get('register_globals');
		if ($register_globals === "" || $register_globals === "0" || strtolower($register_globals) === "off")
			return;

		// Prevent script.php?GLOBALS[foo]=bar
		if (isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS']))
			exit('I\'ll have a steak sandwich and... a steak sandwich.');
	
		// Variables that shouldn't be unset
		$no_unset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');

		// Remove elements in $GLOBALS that are present in any of the superglobals
		$input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
		foreach ($input as $k => $v)
		{
			if (!in_array($k, $no_unset) && isset($GLOBALS[$k]))
			{
				unset($GLOBALS[$k]);
				unset($GLOBALS[$k]);	// Double unset to circumvent the zend_hash_del_key_or_index hole in PHP <4.4.3 and <5.1.4
			}
		}
	}

	protected function validip($ip)
	{
		if (!empty($ip) && $ip == long2ip(ip2long($ip)))
		{
			$reserved_ips = array (
				array('0.0.0.0','2.255.255.255'),
				array('10.0.0.0','10.255.255.255'),
				array('127.0.0.0','127.255.255.255'),
				array('169.254.0.0','169.254.255.255'),
				array('172.16.0.0','172.31.255.255'),
				array('192.0.2.0','192.0.2.255'),
				array('192.168.0.0','192.168.255.255'),
				array('255.255.255.0','255.255.255.255')
			);

			foreach ($reserved_ips as $r)
			{
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
			}
			return true;
		}
		else return false;
    }

    public function getip()
	{
    	if (isset($_SERVER)) {
     		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $this->validip($_SERVER['HTTP_X_FORWARDED_FOR'])) {
       		  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    	 	} elseif (isset($_SERVER['HTTP_CLIENT_IP']) && $this->validip($_SERVER['HTTP_CLIENT_IP'])) {
      		  $ip = $_SERVER['HTTP_CLIENT_IP'];
     		} else {
       		  $ip = $_SERVER['REMOTE_ADDR'];
     		}
   	 	} else {
    		if (getenv('HTTP_X_FORWARDED_FOR') && $this->validip(getenv('HTTP_X_FORWARDED_FOR'))) {
       		  $ip = getenv('HTTP_X_FORWARDED_FOR');
    	 	} elseif (getenv('HTTP_CLIENT_IP') && $this->validip(getenv('HTTP_CLIENT_IP'))) {
     	      $ip = getenv('HTTP_CLIENT_IP');
     	 	} else {
     	      $ip = getenv('REMOTE_ADDR');
     		}
    	}

      return $ip;
    }
	
	public function RestrictHttpMethods($methods)
	{
		//convert the methods array to upper case
		if (is_array($methods))
		{
			$newMethods = array();
			foreach ($methods as $method)
			{
				$newMethods[] = strtoupper($method);
			}
			$methods = $newMethods;
			unset($newMethods);
		}
		else
		{
			//put our single method into array
			$tempMethods = strtoupper($methods);
			unset($methods);
			$methods[] = $tempMethods;
			unset($tempMethods);
		}
		
		//restrict the methods
		$currentMethod = strtoupper($_SERVER['REQUEST_METHOD']);
		//let's see
		if (!in_array($currentMethod, $methods))
		{
			//clear the buffer
			ob_clean();
			//pritn message
			echo 'This method is restricted!';
			die;
		}
	}
	
	public function CheckSessionLife()
	{
		global $config;
		
		if (isset($_SESSION['SESSION']['LAST_ACTIVITY']) && (time() - $_SESSION['SESSION']['LAST_ACTIVITY'] > strtotime('+'.$config['SESSION_LIFETIME'], 0)))
		{
    		// last request was more than [Config] minates ago
			$_SESSION = array();
		}
		$_SESSION['SESSION']['LAST_ACTIVITY'] = time(); // update last activity time stamp
		
		//regenerate session ID every [Config] Minutes
		if (!isset($_SESSION['SESSION']['CREATED']))
		{
    		$_SESSION['SESSION']['CREATED'] = time();
		}
		else if (time() - $_SESSION['SESSION']['CREATED'] > strtotime('+'.$config['SESSION_REGEN_TIME'], 0))
		{
    		// session started more than 30 minates ago
    		session_regenerate_id(true);    // change session ID for the current session an invalidate old session ID
    		$_SESSION['SESSION']['CREATED'] = time();  // update creation time
		}
	}
	
	public function __destruct()
	{
		unset($this->config);
		//unset($this->db);
	}
}

//Dont erase those... :]
if (!isset($HTTP_POST_VARS) && isset($_POST)){ $HTTP_POST_VARS = $_POST; $HTTP_GET_VARS = $_GET; $HTTP_SERVER_VARS = $_SERVER; $HTTP_COOKIE_VARS = $_COOKIE; $HTTP_ENV_VARS = $_ENV; $HTTP_POST_FILES = $_FILES; } if(!get_magic_quotes_gpc()){ if(is_array($HTTP_GET_VARS)){ while(list($k, $v) = each($HTTP_GET_VARS)){ if(is_array($HTTP_GET_VARS[$k])){ while(list($k2, $v2) = each($HTTP_GET_VARS[$k])){ $HTTP_GET_VARS[$k][$k2] = addslashes($v2); } @reset($HTTP_GET_VARS[$k]); } else { $HTTP_GET_VARS[$k] = addslashes($v); } } @reset($HTTP_GET_VARS); } if(is_array($HTTP_POST_VARS)){ while(list($k, $v) = each($HTTP_POST_VARS)){ if(is_array($HTTP_POST_VARS[$k])){ while(list($k2, $v2) = each($HTTP_POST_VARS[$k])){ if(is_array($HTTP_POST_VARS[$k][$k2])){ while(list($k3, $v3) = each($HTTP_POST_VARS[$k][$k2])){ $HTTP_POST_VARS[$k][$k2][$k3] = addslashes($v3); } } else { $HTTP_POST_VARS[$k][$k2] = addslashes($v2); } @reset($HTTP_POST_VARS[$k][$k2]); } @reset($HTTP_POST_VARS[$k]); } else { $HTTP_POST_VARS[$k] = addslashes($v); } } @reset($HTTP_POST_VARS); } if(is_array($HTTP_COOKIE_VARS)){ while(list($k, $v) = each($HTTP_COOKIE_VARS)){ if(is_array($HTTP_COOKIE_VARS[$k])){ while(list($k2, $v2) = each($HTTP_COOKIE_VARS[$k])){ $HTTP_COOKIE_VARS[$k][$k2] = addslashes($v2); } @reset($HTTP_COOKIE_VARS[$k]); } else { $HTTP_COOKIE_VARS[$k] = addslashes($v); } } @reset($HTTP_COOKIE_VARS); } }
