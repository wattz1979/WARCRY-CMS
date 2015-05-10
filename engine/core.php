<?php

//Set the error reporting
error_reporting(E_ALL);

define('init_engine', true);

###################################################################################
## FILE INCLUSION #################################################################
###################################################################################

//constants
include_once './engine/constants.php';

//General Classes
require_once './configuration/config.php';
include_once $config['RootPath'] . '/engine/classes/cache.php';
include_once $config['RootPath'] . '/engine/classes/template.php';
include_once $config['RootPath'] . '/engine/classes/multipleError_handler.php';
include_once $config['RootPath'] . '/engine/classes/sessions.secure.php';
include_once $config['RootPath'] . '/engine/classes/accounts.curuser.php';
include_once $config['RootPath'] . '/engine/classes/security.php';
include_once $config['RootPath'] . '/engine/classes/chmod.calc.php';
include_once $config['RootPath'] . '/engine/shutdown.php';
include_once $config['RootPath'] . '/engine/classes/notifications.php';

//storage variables
include_once $config['RootPath'] . '/engine/storages/boosts.php';
include_once $config['RootPath'] . '/engine/storages/rank_strings.php';
include_once $config['RootPath'] . '/engine/storages/avatars.php';
include_once $config['RootPath'] . '/engine/storages/countries.php';
include_once $config['RootPath'] . '/engine/storages/secret_questions.php';
include_once $config['RootPath'] . '/engine/storages/voteSites.php';
include_once $config['RootPath'] . '/engine/storages/pStore_levels.php';
include_once $config['RootPath'] . '/engine/storages/tp.mapsInfo.php';
include_once $config['RootPath'] . '/engine/storages/tp.points.php';
include_once $config['RootPath'] . '/engine/storages/bt.categories.php';

//We'll have some alternatives for the sessions
if (isset($config['SESSION_HANDLER']))
{
	if ($config['SESSION_HANDLER'] == 'MCRYPT')
	{
		include_once $config['RootPath'] . '/engine/classes/sessions.filesystem.php';
	}
	else
	{
		include_once $config['RootPath'] . '/engine/classes/sessions.none.php';
	}
}
else
{
	include_once $config['RootPath'] . '/engine/classes/sessions.none.php';
}

###################################################################################
## PHP CLASS CORE #################################################################
###################################################################################

class CORE
{
	private $config;
	private $db = false;
	private $auth_db = false;
	private $Modules;
	private $LoadedModules;
	private $RealmConnections;
	
	public function __construct()
	{
		global $config;
		
		$this->config = $config;
		
		//prepare the Modules variable
		$this->Modules['CORE'] = array();
		$this->Modules['SERVER'] = array();
	}
	
	public function DatabaseConnection()
	{
		global $PDO_config;
		
		if (!$this->db)
		{
			try 
			{
				//Construct PDO
				$obj = new PDO('mysql:dbname='.$this->config['DatabaseName'].'; host='.$this->config['DatabaseHost'].';', $this->config['DatabaseUser'], $this->config['DatabasePass'], NULL);
				
				//set error handler exception
				$obj->setAttribute(PDO::ATTR_ERRMODE, $PDO_config['errorHandler']);
				
				//set default fetch method
				$obj->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $PDO_config['fetch']);
				
				//set encoding
				$obj->query("SET NAMES '".$this->config['DatabaseEncoding']."'");
			}
			catch (PDOException $e)
			{
				echo '<strong>Database Connection failed:</strong> Unable to connect to the Web Database.<br><br>';
				
				if ($this->config['DEBUG'])
				{
					echo $e->getMessage();
				}
				die;
			}
			
			//save the database connection because we wont be able to open new one
			$this->db = $obj;
			
			//unset the config variables
			unset($this->config['DatabaseName'], $this->config['DatabaseHost'], $this->config['DatabaseUser'], $this->config['DatabasePass']);
		}
		
  	 	return $this->db;
	}
	
	public function AuthDatabaseConnection()
	{
		global $auth_config, $PDO_config;
		
		if (!$this->auth_db)
		{
			try 
			{
				//Construct PDO
				$obj = new PDO('mysql:dbname='.$auth_config['DatabaseName'].'; host='.$auth_config['DatabaseHost'].';', $auth_config['DatabaseUser'], $auth_config['DatabasePass'], NULL);
				
				//set error handler exception
				$obj->setAttribute(PDO::ATTR_ERRMODE, $PDO_config['errorHandler']);
				
				//set default fetch method
				$obj->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $PDO_config['fetch']);
				
				//set encoding
				$obj->query("SET NAMES '".$auth_config['DatabaseEncoding']."'");
			}
			catch (PDOException $e)
			{
				echo '<strong>Database Connection failed:</strong> Unable to connect to the Authentication Database.<br><br>';
				
				if ($this->config['DEBUG'])
				{
					echo $e->getMessage();
				}
				die;
			}
			
			//save the database connection because we wont be able to open new one
			$this->auth_db = $obj;
		}
				
  	  return $this->auth_db;
	}

	public function RealmDatabaseConnection($id)
	{
		global $realms_config, $PDO_config;
		
		$error = false;
		
		//check if we have the connection stored
		if (!isset($this->RealmConnections[$id]))
		{
			$config = $realms_config[$id]['Database'];
			try 
			{
				//Construct PDO
				$obj = new PDO('mysql:dbname='.$config['name'].'; host='.$config['host'].';', $config['user'], $config['pass'], NULL);
			
				//set error handler exception
				$obj->setAttribute(PDO::ATTR_ERRMODE, $PDO_config['errorHandler']);
			
				//set default fetch method
				$obj->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $PDO_config['fetch']);
			
				//set encoding
				$obj->query("SET NAMES '".$config['encoding']."'");
			}
			catch (PDOException $e)
			{
				$error = '<strong>Database Connection failed:</strong> Unable to connect to a Realm specific Database.';
			}
			
			if (!$error)
			{
				//store the newly made connection and return it
				$this->RealmConnections[$id] = $obj;
				//the connection is made, unset the config
				unset($config, $GLOBALS['realms_config'][$id]['Database']);
				
				return $obj;
			}
			else
			{
				return false;
			}
		}
		else
		{
			//return the stored connection
			return $this->RealmConnections[$id];
		}
		
		return false;		
	}
	
	public function register_Module($name, $category = 'CORE', $serverType = NULL)
	{
		if ($category == 'SERVER')
		{
			if ($serverType == NULL)
			{
				throw new Exception('Cannot register Module with undefined server type.');
			}
			else
			{
				$this->Modules['SERVER'][$serverType][$name] = $this->config['RootPath'] . '/engine/server_modules/' . $serverType . '/' . $name . '.php';
			}
		}
		else if ($category == 'CORE')
		{
			$this->Modules['CORE'][$name] = $this->config['RootPath'] . '/engine/core_modules/' . $name . '.php';
		}
		else
		{
			throw new Exception('Cannot register Module with undefined category.');
		}
	}
	
	public function isRegistred_Module($name, $category = 'CORE', $serverType = NULL)
	{
		if ($category == 'SERVER')
		{
			if ($serverType == NULL)
			{
				return false;
			}
			else
			{
				return isset($this->Modules['SERVER'][$serverType][$name]);
			}
		}
		else if ($category == 'CORE')
		{
			return isset($this->Modules['CORE'][$name]);
		}
		else
		{
			return false;
		}
	}
	
	public function load_CoreModule($name)
	{
		global $config, $CORE;
		
		if ($this->isRegistred_Module($name, 'CORE'))
		{
			if (file_exists($this->Modules['CORE'][$name]))
			{
				//include the PHP file
				include_once $this->Modules['CORE'][$name];
				//define that this modules is loaded
				$this->LoadedModules['CORE'][$name] = true;
			}
			else
			{
				throw new Exception('Loading Core Module "'. $name .'" failed.');
			}
		}
		else
		{
			throw new Exception('Cannot load Unregistred Core Module - ' . $name . '.');
		}
	}

	public function isLoaded_CoreModule($name)
	{
		if (isset($this->LoadedModules['CORE'][$name]))
		{
			return $this->LoadedModules['CORE'][$name];
		}
		else
		{
			return false;
		}
	}
	
	public function load_ServerModule($name, $serverType = false)
	{
		global $server_config;
		
		if (!$serverType)
		{
			$serverType = $server_config['CORE'];
		}
		
		if ($this->isRegistred_Module($name, 'SERVER', $serverType))
		{
			if (file_exists($this->Modules['SERVER'][$serverType][$name]))
			{
				//include the PHP file
				include_once $this->Modules['SERVER'][$serverType][$name];
				//define that this modules is loaded
				$this->LoadedModules['SERVER'][$serverType][$name] = true;
			}
			else
			{
				throw new Exception('Loading Server Module "'. $name .'['.$serverType.']" failed.');
			}
		}
		else
		{
			throw new Exception('Cannot load Unregistred Core Module - ' . $name . '['.$serverType.'].');
		}
	}

	public function isLoaded_ServerModule($name, $serverType = false)
	{
		global $server_config;
		
		if (!$serverType)
		{
			$serverType = $server_config['CORE'];
		}
		
		if (isset($this->LoadedModules['SERVER'][$serverType][$name]))
		{
			return $this->LoadedModules['SERVER'][$serverType][$name];
		}
		else
		{
			return false;
		}
	}
	
	//Check if the visitor is a bot
	public function isCrawler($userAgent)
	{
		$crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|' .
		'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|' .
		'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby';
		
		$isCrawler = (preg_match("/$crawlers/", $userAgent) > 0);
		
		return $isCrawler;
	}
	
	public function cookiesEnabled()
	{
		//Try setting a cookie	
		setcookie('__Test', 1, time()+60*5);
		
		if(!isset($_COOKIE))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	//Using that to store the page URL before login is requested
	public function getPageURL()
	{
		$isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
		$port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
		$port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
		$url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port.$_SERVER["REQUEST_URI"];
	
  	  return $url;
	}
	
	public function ValidateURLBeforeLogin($url)
	{
		//if we are on localhost return true no matter
		if ($_SERVER['HTTP_HOST'] == 'localhost')
		{
			return true;
		}
		
		//check if it is valid URL
		if(!preg_match("/^[a-zA-Z]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+$/i", $url))
		{
			return false;
		}
		
		//check if the redirection is to our host dont allow outside of the host
		$URL_Host = parse_url($url, PHP_URL_HOST);
		
		if ($URL_Host != $_SERVER['HTTP_HOST'])
		{
			return false;
		}
		
		return true;
	}
	
	public function loggedInOrReturn()
	{
    	global $CURUSER;
		
    	if (!$CURUSER->isOnline())
		{   
	    	$_SESSION['url_bl'] = $this->getPageURL();
        	header("Location: ".$this->config['BaseURL']."/index.php?page=login");
        	die;
    	}
	}
	
	public function currency_StringToSymbol($str)
	{
		switch($str)
		{
			case "EUR":
				$symbol = "&euro;";
			break;
			case "USD":
				$symbol = "$";
			break;
			case "BGN":
				$symbol = "лв.";
			break;
			default:
				$symbol = "&euro;";
			break;
		}
		
	  return $symbol;
	}
	
	public function getTime($obj = false, $timestamp = false)
	{
	  global $config;
	  	
		if (isset($config['TimeZone']))
		{
			$timeZone = new DateTimeZone($config['TimeZone']);
		}
		else
		{
			$timeZone = NULL;
		}
		
		//construct the DateTime Object, with DateTimeZone Object if possible
		if ($timestamp)
		{
			$time = new DateTime($timestamp, $timeZone);
		}
		else
		{	
			$time = new DateTime(NULL, $timeZone);
		}
		
		//if we want to return the DateTime Object
		if ($obj)
		{
			return $time;
		}
		else
		{
			return $time->format('Y-m-d H:i:s');
		}
	}
	
	public function getWeekStartEnd($date = false)
	{
		if (!$date)
			$date = $this->getTime();
			
       	$ts = strtotime($date);
		
		//check if today is monday
		if (date('w', $ts) == 1)
		{
			$start = strtotime('today', $ts);
		}
		else
		{
			$start = strtotime('this week last monday', $ts);
		}
		//Check if today is sunday
		if (date('w', $ts) == 0)
		{
			$end = strtotime('today', $ts);
		}
		else
		{
			$end = strtotime('this week next sunday', $ts);
		}
		
		//Add 23 hours and 59 minutes to the END date
		return array(date('Y-m-d H:i', $start), date('Y-m-d H:i', $end + 60*60*23 + 60*59));
	}
	
	public function convertDataTime($timestamp)
	{
	  	global $CORE;
	  		
		//setup DateTime Object with the timestamp from the parameter
		$dateTime = $CORE->getTime(true, $timestamp);
		//setup new DateTime Object from the time ATM
		$now = $CORE->getTime(true);
		
		//get time diference
		$hours = $dateTime->diff($now)->h;
		$minutes = $dateTime->diff($now)->i;
		$seconds = $dateTime->diff($now)->s;
				
		//if the hours are less then 24
		if ($hours < 24)
		{
			if ($hours > 0)
			{
				$return_time = $hours . ' hours  ago';
			}
			else if ($minutes > 0)
			{
				$return_time = $minutes . ' minutes ago';
			}
			else
			{
				$return_time = $seconds . ' seconds ago';
			}
		}
		
		//create time string for days before today
		$time_else = $dateTime->format('g:i:s A');
		
		$nowIs = $now->format('Y-m-d');
		$dayAgo = $now->modify('-1 day')->format('Y-m-d');
		$twoAgo = $now->modify('-1 day')->format('Y-m-d');
		$threeAgo = $now->modify('-1 day')->format('Y-m-d');
		
		//Try Today or Yestarday
    	if ($dateTime->format('Y-m-d') == $nowIs)
		{
        	$return_date_time = 'Today, ' . $return_time;
    	}
    	else if ($dateTime->format('Y-m-d') == $dayAgo)
		{
       		$return_date_time = 'Yesterday, at ' . $time_else;
    	}
		else if ($dateTime->format('Y-m-d') == $twoAgo)
		{
       		$return_date_time = '2 days ago, at ' . $time_else;
		}
		else if ($dateTime->format('Y-m-d') == $threeAgo)
		{
       		$return_date_time = '3 days ago, at ' . $time_else;
		}
		else
		{
			$return_date_time = $dateTime->format('d.m.Y, g:i:s A');
		}
							
      return $return_date_time;
	}
	
	public function singleMeasureTimeLeft($timestamp)
	{
	  	global $CORE;
	  		
		//setup DateTime Object with the timestamp from the parameter
		$dateTime = $CORE->getTime(true);
		$dateTime->setTimestamp($timestamp);
		//setup new DateTime Object from the time ATM
		$now = $CORE->getTime(true);
		
		//get time diference
		$days = $dateTime->diff($now)->d;
		$hours = $dateTime->diff($now)->h;
		$minutes = $dateTime->diff($now)->i;
		$seconds = $dateTime->diff($now)->s;
				
		if ($days >= 30)
		{
			return '<b>1</b> month';
		}
		else if ($days > 0)
		{
			return '<b>' . $days . '</b> day' . ($days == 1 ? '' : 's');
		}
		else if ($hours > 0)
		{
			return '<b>' . $hours . '</b> hour' . ($hours == 1 ? '' : 's');
		}
		else if ($minutes > 0)
		{
			return '<b>' . $minutes . '</b> minute' . ($minutes == 1 ? '' : 's');
		}
		else if ($seconds > 0)
		{
			return '<b>' . $seconds . '</b> second' . ($seconds == 1 ? '' : 's');
		}
		
		return false;
	}
	
	public function convertCooldown($timestamp)
	{
	  global $CORE;
	  		
		//get the diference in int
		$difference = $timestamp - time();
		
		//check if we have cooldown at all
		if ($difference < 0)
		{
			return false;
		}
		
		//get the seconds, minutes, hours and days
		$seconds = $difference % 60;
		$minutes = ($difference / 60) % 60;
		$hours = ($difference / (60*60)) % 24;
		$days = ($difference / (24*60*60)) % 30;
		
		return array('seconds' => $seconds, 'minutes' => $minutes, 'hours' => $hours, 'days' => $days, 'int' => $difference, 'timestamp' => $timestamp);
	}

	public function percent($num_amount, $num_total)
	{
		$count1 = $num_amount / $num_total;
		$count2 = $count1 * 100;
		$count = round($count2);
		
		return $count;
	}
	
	public function convertBytes($size)
	{
		$unit=array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
	
	public function ExecuteSoapCommand($command, $realmid)
	{
		global $realms_config;
	
	    try //Try to execute function
	    {
	    	$cliente = new SoapClient(NULL,
	    		array(
	    			"location" 	=> "".$realms_config[$realmid]['soap_protocol']."://".$realms_config[$realmid]['soap_address'].":".$realms_config[$realmid]['soap_port']."/",
	    			"uri"   	=> "urn:TC",
	    			"style" 	=> SOAP_RPC,
	    			"login" 	=> $realms_config[$realmid]['soap_user'],
	    			"password" 	=> $realms_config[$realmid]['soap_pass']
				)
	    	);
	
	   	 	$result = $cliente->executeCommand(new SoapParam($command, "command"));
	    
	    }
		catch(Exception $e)
	    {
	        return array('sent' => false, 'message' => $e->getMessage());
	    }
		 
	    return array('sent' => true, 'message' => $result);
	}
	
	public function getRemotePage($parameters)
	{
		if (!isset($parameters['page']))
			$parameters['page'] = '/';
		
		if (!isset($parameters['timeout']))
			$parameters['timeout'] = 0.5;
		
		//Return array
		$return = array();
		
		$fp = fsockopen($parameters['host'], $parameters['port'], $errno, $errdesc, $parameters['timeout']);
		if (!$fp)
		{
			$return['error'] = 'Couldn\'t connect to '.$parameters['host'].':\nError: '.$errno.'\nDesc: '.$errdesc.'\n';
		}
			
		$request = "GET ".$parameters['page']." HTTP/1.0\r\n";
		$request .= "Host: ".$parameters['host']."\r\n";
		$request .= "Referer: http://www.warcry-wow.com\r\n";
		$request .= "User-Agent: Warcry WoW PHP Client\r\n\r\n";
		
		//Prepare the page
		$return['headers'] = '';
		$return['body'] = '';
		
		fputs($fp, $request);

		// put the header in variable
		do // loop until the end of the header
		{
			$return['headers'] .= fgets($fp, 128);

		} while(strpos($return['headers'], "\r\n\r\n") === false);

		// now put the body in variable
		while (!feof($fp))
		{
			$return['body'] .= fgets ($fp, 128);
		}
	
		fclose($fp);
		
		return $return;
	}
	
	public function setCookie($key, $value, $expire, $path = '/', $domain = '')
	{
		setcookie($key . '_wcw', $value, $expire, $path, $domain, isset($_SERVER["HTTPS"]), true);
	}
	
	public function removeCookie($key, $path = '/', $domain = '')
	{
		if (isset($_COOKIE[$key . '_wcw']))
			setcookie($key . '_wcw', "", time()-3600, $path, $domain);
	}
	
	public function getItemQualityString($id)
	{
		switch($id)
		{
			case 0:
				return 'Poor';
				break;
			case 1:
				return 'Common';
				break;
			case 2:
				return 'Uncommon';
				break;
			case 3:
				return 'Rare';
				break;
			case 4:
				return 'Epic';
				break;
			case 5:
				return 'Legendary';
				break;
			case 6:
				return 'Artifact';
				break;
			case 7:
				return 'Heirloom';
				break;
			default:
				return 'Poor';
				break;
		}
		
		return false;
	}
	
	public function getClassString($id)
	{
		switch($id)
		{
			case 1:
				return 'Warrior';
				break;
			case 2:
				return 'Paladin';
				break;
			case 3:
				return 'Hunter';
				break;
			case 4:
				return 'Rogue';
				break;
			case 5:
				return 'Priest';
				break;
			case 6:
				return 'Death Knight';
				break;
			case 7:
				return 'Shaman';
				break;
			case 8:
				return 'Mage';
				break;
			case 9:
				return 'Warlock';
				break;
			case 11:
				return 'Druid';
				break;
		}
		
		return false;
	}
	
	public function ChmodWritable($path)
	{
		//check if the path is directory
		if (is_dir($path))
		{
			$chmod = new ChmodCalc();
			$chmod->setOwnermodes(true,true,true);
			$chmod->setGroupmodes(true,true,false);
			$chmod->setPublicmodes(true,true,false);			
			$ChmodPermissions = $chmod->getMode();
			//chmod it
			chmod($path, $ChmodPermissions);
		}
		else
		{
			$chmod = new ChmodCalc();
			$chmod->setOwnermodes(true,true,false);
			$chmod->setGroupmodes(true,true,false);
			$chmod->setPublicmodes(true,true,false);			
			$ChmodPermissions = $chmod->getMode();
			//chmod it
			chmod($path, $ChmodPermissions);
		}
		
		return false;
	}

	public function ChmodReadonly($path)
	{
		//check if the path is directory
		if (is_dir($path))
		{
			$chmod = new ChmodCalc();
			$chmod->setOwnermodes(true,true,true);
			$chmod->setGroupmodes(true,false,false);
			$chmod->setPublicmodes(true,false,false);
			$ChmodPermissions = $chmod->getMode();
			//chmod it
			chmod($path, $ChmodPermissions);
		}
		else
		{
			$chmod = new ChmodCalc();
			$chmod->setOwnermodes(true,true,false);
			$chmod->setGroupmodes(true,false,false);
			$chmod->setPublicmodes(true,false,false);
			$ChmodPermissions = $chmod->getMode();
			//chmod it
			chmod($path, $ChmodPermissions);
		}
		
		return false;
	}
	
	public function hasFlag($flags, $flag)
	{
        return ($flags & $flag) != 0;
    }

    public function setFlag(&$flags, $flag) 
	{
		 $flags |= $flag;
	}
	
	public function removeFlag(&$flags, $flag)
	{
		$flags &= ~$flag;
	}
	
	public function __destruct()
	{
		//kill and unset the website DB
		unset($this->db);
		//kill and unset the auth DB
		unset($this->auth_db);
		//unset the modules variable
		unset($this->Modules);
		//close the stored realm connections
		if (is_array($this->RealmConnections))
		{
			foreach ($this->RealmConnections as $id => $PDO)
			{
				$this->RealmConnections[$id] = NULL;
			}
			unset($this->RealmConnections);
		}
		//unset the config
		unset($this->config);
	}
}