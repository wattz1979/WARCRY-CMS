<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class UserRank
{
	private $rank;
	
	//Constructor
	public function __construct($rank)
	{
		$this->rank = $rank;
		
		return true;	
	}
	
	public function int()
	{
		return (int)$this->rank;
	}
	
	public function string()
	{
		$data = new RankStringData();
		
		return $data->get($this->int());
	}
}

class Avatar
{
	private $id;
	private $str;
	private $rank;
	private $type;
	
	public function __construct($id = 0, $str = '', $rank = 0, $type = 0)
	{
		$this->id = $id;
		$this->str = $str;
		$this->rank = $rank;
		$this->type = $type;
		
		return true;
	}
	
	public function int()
	{
		return (int)$this->id;
	}
	
	public function string()
	{
		return $this->str;
	}
	
	public function rank()
	{
		return (int)$this->rank;
	}
	
	public function type()
	{
		return (int)$this->type;
	}
}

class CURUSER extends CORE
{
	private $row;
	private $permissions = false;
	
 	//Constructor
	public function __construct()
	{
		return true;
	}
	
	public function setPermissionsObject($obj)
	{
		$this->permissions = $obj;
		
		return $this;
	}
	
	public function getPermissions()
	{
		if ($this->permissions)
			return $this->permissions;
			
		return false;
	}
	
	//function to check if the current user is logged in
	public function isOnline()
	{
		//If session logged is not set return false
		if (!isset($_SESSION['logged']) || !$_SESSION['logged'] || $_SESSION['logged'] != '1')
		{
		  return false;
		}
		else if (!isset($this->row)) //if the curuser record is not set
		{
			return false;
		}
		
	  	return true;
	}
	
	//We set the user record on startup check
	public function setrecord($array)
	{
		$this->row = $array;
	}
	
	//If the index dosent exits returns false
	public function get($key)
	{
		if (!isset($this->row[$key]))
		  	return false;
		  
	 	return $this->row[$key];
	}
	
	//Function to set variable into the curuser row
	public function setVar($key, $value)
	{
		return $this->row[$key] = $value;
	}
	
	public function setLoggedIn($id, $passhash)
	{
		$ss = new Secure();
    	$ss->cb = true;
    	$ss->cib = 2;
    	$ss->open();
    
		unset($ss);
		
    	$_SESSION['uid'] = $id;
		$_SESSION['pass'] = $passhash;
		
	  	return true;
	}
		
	public function logInfoAtLogin($acc)
	{
		global $DB, $SECURITY, $CORE;
		
		$ip = $SECURITY->getip();
		$thislogin = $CORE->getTime();
		
		//get the last login time
		$res = $DB->prepare("SELECT admin_last_login2 FROM `account_data` WHERE `id` = :account LIMIT 1;");
		$res->bindParam(':account', $acc, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() == 0)
		{
			unset($res);
			return false;
		}
		
		//fetch the data
		$row = $res->fetch();
		unset($res);
		
		$update = $DB->prepare("UPDATE `account_data` SET `admin_last_ip` = :ip, `admin_last_login` = :lastlogin, `admin_last_login2` = :lastlogin2 WHERE `id` = :account LIMIT 1;");
		$update->bindParam(':account', $acc, PDO::PARAM_INT);
		$update->bindParam(':ip', $ip, PDO::PARAM_STR);
		$update->bindParam(':lastlogin', $row['last_login2'], PDO::PARAM_STR);
		$update->bindParam(':lastlogin2', $thislogin, PDO::PARAM_STR);
		$update->execute();
		
		//return the affected rows
		$return = $update->rowCount();
		unset($update);
		
		return $return;
	}

	public function logout()
	{
    	$_SESSION = array();	
		session_unset();
		session_destroy();
	}

	public function __destruct()
	{
		unset($this->row);
		unset($this->db);
	}
	
}