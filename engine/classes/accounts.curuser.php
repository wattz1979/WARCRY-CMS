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
	
 	//Constructor
	public function __construct()
	{
		return true;	
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
		if(!isset($this->row[$key]))
		  return false;
		  
	 return $this->row[$key];
	}
	
	//Function to set variable into the curuser row
	public function setVar($key, $value)
	{
		return $this->row[$key] = $value;
	}
	
	public function setRecruiterLinkState($status)
	{
		$_SESSION['CU_RAF_LINK_STATE'] = $status;
	}
	
	public function getRecruiterLinkState()
	{
		//check if the sessions was set
		if (isset($_SESSION['CU_RAF_LINK_STATE']))
		{
			return $_SESSION['CU_RAF_LINK_STATE'];
		}
		
		return RAF_LINK_PENDING;
	}

	public function setLastVoteTime($time)
	{
		$_SESSION['LastVoteTime'] = $time;
	}
	
	public function getLastVoteTime()
	{
		//check if the sessions was set
		if (isset($_SESSION['LastVoteTime']))
		{
			return $_SESSION['LastVoteTime'];
		}
		
		return false;
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
	
	public function getRank()
	{
		return new UserRank($this->get('rank'));
	}
	
	public function getAvatar()
	{
		if ((int)$this->get('avatarType') == AVATAR_TYPE_GALLERY)
		{
			$gallery = new AvatarGallery();

			return $gallery->get($this->get('avatar'));
		}
		else if ((int)$this->get('avatarType') == AVATAR_TYPE_UPLOAD)
		{
			return new Avatar(0, $this->get('avatar'), 0, AVATAR_TYPE_UPLOAD);
		}
		
		return false;
	}
	
	private function GetFirstRealm()
	{
		global $realms_config;
		
		if (isset($realms_config) && is_array($realms_config))
		{
			foreach ($realms_config as $id => $data)
			{
				return $id;
			}
		}
		
		return 1;
	}
	
	private function isValidRealm($id)
	{
		global $realms_config;
		
		return isset($realms_config[$id]) ? true : false;
	}
	
	public function GetRealm()
	{
		//If for some reason this is called with no user
		if (!$this->isOnline())
		{
			return $this->GetFirstRealm();
		}
		
		//Check if the user has selected realm
		if (isset($this->row['selected_realm']) && $this->row['selected_realm'] != '')
		{
			//is valid realm
			if ($this->isValidRealm($this->row['selected_realm']))
			{
				return (int)$this->row['selected_realm'];
			}
		}
		
		//not set
		return $this->GetFirstRealm();
	}
	
	public function Update($array)
	{
		global $DB;
		
		if (!is_array($array))
		{
			return false;
		}
		else if (count($array) == 0)
		{
			return false;
		}
		
		//prepare the query
		foreach ($array as $key => $value)
		{
			$updateset[] = "`".$key."` = :".strtolower($key);
		}

		$update = $DB->prepare("UPDATE `account_data` SET ".implode(', ', $updateset)." WHERE `id` = :account LIMIT 1;");
		$update->bindParam(':account', $this->get('id'), PDO::PARAM_INT);
		//prepare the values
		foreach ($array as $key => $value)
		{
			$update->bindParam(':'.strtolower($key), $value, (is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR));
		}		
		$update->execute();

		if ($update->rowCount() > 0)
		{
			unset($update);
			return true;
		}
		else
		{
			unset($update);
			return false;
		}
	}
	
	public function handle_MissingRecord($acc)
	{
		global $DB, $SECURITY, $CORE;
		
		$ip = $SECURITY->getip();
		$thislogin = $CORE->getTime();
		
		$insert = $DB->prepare("INSERT INTO `account_data` (id, last_ip, reg_ip, last_login2, status) VALUES (:account, :lastip, :regip, :lastlogin2, :status);");
		$insert->bindParam(':account', $acc, PDO::PARAM_INT);
		$insert->bindParam(':lastip', $ip, PDO::PARAM_STR);
		$insert->bindParam(':regip', $ip, PDO::PARAM_STR);
		$insert->bindParam(':lastlogin2', $thislogin, PDO::PARAM_STR);
		$insert->bindValue(':status', 'active', PDO::PARAM_STR);
		$insert->execute();
		
		$return = $insert->rowCount();
		unset($insert);
		
		return $return;
	}
	
	public function logInfoAtLogin($acc)
	{
		global $DB, $SECURITY, $CORE;
		
		$ip = $SECURITY->getip();
		$thislogin = $CORE->getTime();
		
		//get the last login time
		$res = $DB->prepare("SELECT last_login2 FROM `account_data` WHERE `id` = :account LIMIT 1;");
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
		
		$update = $DB->prepare("UPDATE `account_data` SET `last_ip` = :ip, `last_login` = :lastlogin, `last_login2` = :lastlogin2 WHERE `id` = :account LIMIT 1;");
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
		global $CORE;
		
    	$_SESSION = array();	
		session_unset();
		session_destroy();
	}

	public function getCooldown($key)
	{
		//check if the current user is online
		if (!$this->isOnline())
		{
			return false;
		}

		//get the cooldowns string from the users record
		$string = $this->get('cooldowns');

		//eval the string as PHP code, parsing to array in this case
		if (!$string or $string == '')
		{
			$cooldowns = array();
		}
		else
		{
			$cooldowns = eval("return $string");
		}
		
		if (isset($cooldowns[$key]))
		{
			return $cooldowns[$key];
		}
		
	  return false;
	}
	
	public function getVoteIPCooldown($siteid)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT `account` FROM `vote_data` WHERE `ip` = :ip AND `siteid` = :siteid ORDER BY `id` DESC LIMIT 1");
		$res->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
		$res->bindParam(':siteid', $siteid, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() == 0)
		{

			unset($res);
			return false;
		}
		//fetch the data
		$row = $res->fetch();
		unset($res);
		
		$res = $DB->prepare("SELECT `cooldowns` FROM `account_data` WHERE `id` = :account");
		$res->bindParam(':account', $row['account'], PDO::PARAM_INT);
		$res->execute();
		
		unset($row);
		
		if ($res->rowCount() == 0)
		{
			unset($res);
			return false;
		}
		//fetch the data
		$row = $res->fetch();
		unset($res);
		
		$string = $row['cooldowns'];
		
		
		//eval the string as PHP code, parsing to array in this case
		if (!$string or $string == '')
		{
			$cooldowns = array();
		}
		else
		{
			$cooldowns = eval("return $string");
		}
		
		if (isset($cooldowns['votingsite' . $siteid]))
		{
			return $cooldowns['votingsite' . $siteid];
		}
		
		return false;
	}
	
	public function setCooldown($key, $value)
	{
		global $DB;
		
		if (!isset($value) or $value == '')
		{
			return false;
		}
		
		//check if the current user is online
		if (!$this->isOnline())
		{
			return false;
		}
		
		//get the cooldowns string from the users record
		$string = $this->get('cooldowns');

		//eval the string as PHP code, parsing to array in this case
		if (!$string or $string == '')
		{
			$cooldowns = array();
		}
		else
		{
			$cooldowns = eval("return $string");
		}
		
		//set the cooldown
		$cooldowns[$key] = $value;
		
		$string = 'array(';
		foreach ($cooldowns as $key => $value)
		{
			$string .= "'$key' => '$value', ";
		}
		$string .= ');';

		unset($cooldowns, $key, $value);
				
		$res = $DB->prepare("UPDATE `account_data` SET `cooldowns` = :string WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':string', $string, PDO::PARAM_STR);
		$res->bindParam(':id', $this->get('id'), PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$return = true;
		}
		else
		{
			$return = false;
		}
		unset($res);
		
	  return $return;
	}
	
	public function unsetCooldown($key)
	{
		global $DB;
		
		//check if the current user is online
		if (!$this->isOnline())
		{
			return false;
		}

		//get the cooldowns string from the users record
		$string = $this->get('cooldowns');

		//eval the string as PHP code, parsing to array in this case
		if (!$string or $string == '')
		{
			$cooldowns = array();
		}
		else
		{
			$cooldowns = eval("return $string");
		}
		
		//unset the cooldown
		unset($cooldowns[$key]);
		
		$string = 'array(';
		foreach ($cooldowns as $key => $value)
		{
			$string .= "'$key' => '$value', ";
		}
		$string .= ');';
		
		unset($cooldowns, $key, $value);

		$res = $DB->prepare("UPDATE `account_data` SET `cooldowns` = :string WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':string', $string, PDO::PARAM_STR);
		$res->bindParam(':id', $this->get('id'), PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$return = true;
		}
		else
		{
			$return = false;
		}
		unset($res);
		
	  return $return;
	}
	
	public function getSocial($app)
	{
		//check if the current user is online
		if (!$this->isOnline())
		{
			return false;
		}

		//get the data string from the users record
		$string = $this->get('socialData');

		//eval the string as PHP code, parsing to array in this case
		if (!$string or $string == '')
		{
			$socialData = array();
		}
		else
		{
			$socialData = eval("return $string");
		}
		
		if (isset($socialData[$app]))
		{
			return $socialData[$app];
		}
		
		//We should return negative by default
	 	return STATUS_NEGATIVE;
	}

	public function setSocial($app, $status)
	{
		global $DB;
		
		if (!isset($status) or $status == '')
		{
			return false;
		}
		
		//check if the current user is online
		if (!$this->isOnline())
		{
			return false;
		}
		
		//get the cooldowns string from the users record
		$string = $this->get('socialData');

		//eval the string as PHP code, parsing to array in this case
		if (!$string or $string == '')
		{
			$socialData = array();
		}
		else
		{
			$socialData = eval("return $string");
		}
		
		//set the cooldown
		$socialData[$app] = $status;
		
		$string = 'array(';
		foreach ($socialData as $key => $value)
		{
			$string .= "'$key' => '$value', ";
		}
		$string .= ');';
		
		unset($socialData, $key, $value);
		
		$res = $DB->prepare("UPDATE `account_data` SET `socialData` = :string WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':string', $string, PDO::PARAM_STR);
		$res->bindParam(':id', $this->get('id'), PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$return = true;
		}
		else
		{
			$return = false;
		}
		unset($res);
		
	  return $return;
	}

	public function __destruct()
	{
		unset($this->row);
		unset($this->db);
	}
}