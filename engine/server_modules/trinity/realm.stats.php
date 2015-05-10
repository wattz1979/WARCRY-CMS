<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class server_RealmStats
{
	private $realm = 0;
	private $realm_config;
	private $REALM_DB;
	private $uptimeRow;
	
	//constructor
	public function __construct()
	{
		return true;
	}
	
	//returns true if everything went successful while setting up the realm
	public function setRealm($id)
	{
		global $realms_config, $CORE;
		
		if (isset($realms_config[$id]))
		{
			//try to connect to the database
			if ($this->REALM_DB = $CORE->RealmDatabaseConnection($id))
			{
				//set some variables
				$this->realm = $id;
				$this->realm_config = $realms_config[$id];
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	public function prepareUptimeRow()
	{
		global $AUTH_DB;
			
		$res = $AUTH_DB->prepare("SELECT starttime, uptime FROM `uptime` WHERE `realmid` = :id ORDER BY starttime DESC LIMIT 1;");
		$res->bindParam(':id', $this->realm, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$this->uptimeRow = $res->fetch();
		}
		else
		{
			$this->uptimeRow = false;
		}
		unset($res);
		
		return true;
	}
	
    public function getStatus()
	{
	 	global $chars_config;
	 		 
	  	if (!$this->uptimeRow)
		{	   		
	    	return 'offline';
		}
	   	else
		{
			//check if it's set in the config
			if (isset($this->realm_config['UPDATE_TIME']))
			{
				$confVal = $this->realm_config['UPDATE_TIME'];
				
				//find out how did we pass the value
				if (is_int($confVal))
				{
					//the value is int
					$updateTime = $confVal;
				}
				else if (ctype_digit($confVal))
				{
					//the value consists of all digits
					$updateTime = $confVal;
				}
				else
				{
					//convert string to time
					$updateTime = strtotime($confVal, 0);
				}
			}
			else
			{
				//default 10 minutes in seconds
				$updateTime = 600;
			}
			
			//get the time which should be equal or greater than now if the server is online
	 		$time = $this->uptimeRow['starttime'] + $this->uptimeRow['uptime'] + $updateTime;

	   		if ($time < time())
			{ 
	     		return 'offline';
			}
	    	else
			{
				return 'online';
	   		}
	  	}
		
		return false;
    }
    	
    public function getUptime()
    {	 
	 	$num = $this->uptimeRow['uptime'];
	 
      	$day = floor($num/86400);
      	$hours = floor(($num - $day*86400)/3600);
      	$minutes = floor(($num - $day*86400 - $hours*3600)/60);
	   
	  	if ($day <= 0 and $hours <= 0)
		{
       		$return = $minutes . ($minutes > 1 ? ' minutes' : ' minute');
		}
	  	else if ($day <= 0)
		{
       		$return = $hours . ($hours > 1 ? ' hours' : ' hour') . ' and ' . $minutes . ($minutes > 1 ? ' minutes' : ' minute');
		}
	  	else
		{
       		$return = $day . ($day > 1 ? ' days ' : ' day ') . $hours . ($hours > 1 ? ' hours' : ' hour') . ' and ' . $minutes.' min';
		}

     	return $return;
    }

	public function getOnline()
	{
		$columns = CORE_COLUMNS::get('characters');
					
		//count the Alliance
		$res = $this->REALM_DB->prepare("SELECT COUNT(".$columns['guid'].") AS a FROM `".$columns['self']."` WHERE `".$columns['online']."` = '1' AND `".$columns['race']."` IN (1, 3, 4, 7, 11, 22)");
		$res->execute();
		$allyRes = $res->fetch(PDO::FETCH_ASSOC);
		unset($res);
	
		//Count the Horde
		$res = $this->REALM_DB->prepare("SELECT COUNT(".$columns['guid'].") AS h FROM `".$columns['self']."` WHERE `".$columns['online']."` = '1' AND `".$columns['race']."` IN (2, 5, 6, 8, 9, 10)");
		$res->execute();
		$hordeRes = $res->fetch(PDO::FETCH_ASSOC);
		unset($res);

		//get the count
		$allyCount = $allyRes['a'];
		$hordeCount = $hordeRes['h'];
		$totalCount = $allyCount + $hordeCount;
		
		return array('total' => $totalCount, 'alliance' => $allyCount, 'horde' => $hordeCount);
	}
	
	public function GetRealmDetails()
	{
		global $DB;
		
		//Get the realm details
		$res = $DB->prepare("SELECT * FROM `realm_stats` WHERE `RealmID` = :id LIMIT 1;");
		$res->bindParam(':id', $this->realm, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() == 0)
			return false;
		
		$row = $res->fetch();
		unset($res);
		
		return (object)$row;
	}
	
	public function __destruct()
	{
		unset($this->realm);
		unset($this->realm_config);
		$this->REALM_DB = NULL;
		unset($this->REALM_DB);		
	}
}