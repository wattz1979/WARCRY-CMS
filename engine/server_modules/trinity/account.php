<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class server_Account
{
	static public function userCheck($ACP = false)
	{
 		global $CURUSER, $AUTH_DB, $DB, $CORE;
		
		//If we are not logged in empty the session meaning logout	
    	if (!isset($_SESSION['uid']) || !isset($_SESSION['pass']))
		{
    		return;
		}
	
		//get the user id if set
    	$id = 0 + (int)$_SESSION['uid'];
	
		//empty session if there is no id or the passhash is incorrect length
    	if (!$id || strlen($_SESSION['pass']) != 40)
		{
    		return;
		}
		
		//get the column names for table accounts
		$columns = CORE_COLUMNS::get('accounts');	
		
		//Select accounts_more
		$res = $AUTH_DB->prepare("SELECT * FROM `".$columns['self']."` WHERE `".$columns['id']."` = :id LIMIT 1");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
		$row = $res->fetch();
    	unset($res);

		//If user with that ID actually exists else empty session
    	if (!$row)
		{
			$_SESSION = array();
    		return;
		}
	
		//check user pass 
    	if (strtolower($_SESSION['pass']) !== strtolower($row['sha_pass_hash']))
		{
			$_SESSION = array();
    		return;
		}

		//if this is check for the admin panel
		if ($ACP)
		{
			$perms = new Permissions($row[$columns['id']]);
			
			//check if the account is allowed
			if (!$perms->IsAllowedToUseACP())
			{
				$_SESSION = array();
    			return;
			}
			
			//save the permission object
			$CURUSER->setPermissionsObject($perms);
		}
		
		//let's add some security to the session
    	$ss = new Secure();
    	$ss->cb = true;
    	$ss->cib = 2;
    
		//if the session is stolen we empty it
    	if (!$ss->check())
		{
			unset($ss);
		
			$_SESSION = array();
			return;
		}
    	unset($ss);
		
		//find the webiste record
		$res = $DB->prepare("SELECT * FROM `account_data` WHERE `id` = :id LIMIT 1");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
		$webRow = $res->fetch(PDO::FETCH_ASSOC);
		unset($res);
		
		//create new translated row
		$newRow['id'] = $row[$columns['id']];
		$newRow['username'] = $row[$columns['username']];
		$newRow['shapasshash'] = $row[$columns['shapasshash']];
		$newRow['lastip'] = $row[$columns['lastip']];
		$newRow['lastlogin'] = $row[$columns['lastlogin']];
		$newRow['flags'] = $row[$columns['flags']];
		$newRow['email'] = $row[$columns['email']];
		$newRow['joindate'] = $row[$columns['joindate']];
		$newRow['recruiter'] = $row[$columns['recruiter']];
		
		//merge the website row with the newly made auth row
		if ($webRow)
		{
			$newRow = array_merge($newRow, $webRow);
		}
				
		//set the CMS database accounts_more record of this user		
		$CURUSER->setrecord($newRow);
	
		//free the result and unset the row	 
		unset($row);
		unset($newRow); 

		//if the session is not tagged as logged we do so
		if (!isset($_SESSION['logged']))
		{
    		$_SESSION['logged'] = '1';
		}
	}
	
	//function for hashing
	static public function makeHash($user, $pass)
	{   
		$user = trim($user);
		$pass = trim($pass);
		
		$hashed = sha1(strtoupper($user) . ":" . strtoupper($pass));
		
 	  return $hashed;
	}
	
	static public function RememberMeCheck()
	{
		global $AUTH_DB, $DB, $CURUSER;
		
		$rememberMeCookie = isset($_COOKIE['rmm_wcw']) ? $_COOKIE['rmm_wcw'] : false;

		if ($rememberMeCookie && !$CURUSER->isOnline())
		{
			$cookieData = explode("-", $rememberMeCookie);
		
			//-do cookie login values
			$cookieUser = strtoupper($cookieData[0]);
			$cookieHash = $cookieData[1];
			
			unset($cookieData);
			
			//get the column names for table accounts
			$columns = CORE_COLUMNS::get('accounts');	
			
			//Get the user account hash
			$res = $AUTH_DB->prepare("SELECT `".$columns['id']."` AS id, `".$columns['shapasshash']."` AS hash FROM `".$columns['self']."` WHERE `".$columns['username']."` = :username;");
			$res->bindParam(':username', $cookieUser, PDO::PARAM_STR);
			$res->execute();

			//Make sure we have both the web record and account record
			if ($res->rowCount() > 0)
			{
				$acc = $res->fetch(PDO::FETCH_ASSOC);
				
				//Get the user account salt
				$saltRes = $DB->prepare("SELECT `salt` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
				$saltRes->bindParam(':acc', $acc['id'], PDO::PARAM_INT);
				$saltRes->execute();
				
				if ($saltRes->rowCount() > 0)
				{
					$web = $saltRes->fetch(PDO::FETCH_ASSOC);
					
					if ($web['salt'] != '')
					{
						//match the cookie hash
						$hashCheck = sha1($acc['hash'] . $web['salt']);
						
						if ($hashCheck === $cookieHash)
						{
							//Login the user
							$CURUSER->setLoggedIn($acc['id'], $acc['hash']);
						}
					}
					unset($web, $acc, $hashCheck);
				}
			}
			unset($res, $cookieUser, $cookieHash);
		}
		unset($rememberMeCookie);
	}
	
	static public function register($username, $password, $email, $expansion = 2, $recruiter = 0)
	{
		global $AUTH_DB, $CORE, $SECURITY;
		
		//get the column names for table accounts
		$columns = CORE_COLUMNS::get('accounts');
		
		//make the user pass hash
		$shapasshash = self::makeHash($username, $password);
		
		//get the time for the joindate
		$dateTime = $CORE->getTime(true);
		$joindate = $dateTime->format("Y-m-d H:i:s");
		unset($dateTime);
		//get the visitor IP Address
		$lastip = $SECURITY->getip();
		
		$insert = $AUTH_DB->prepare("INSERT INTO `".$columns['self']."` (".$columns['username'].", ".$columns['shapasshash'].", ".$columns['email'].",  ".$columns['joindate'].", ".$columns['lastip'].", ".$columns['flags'].", ".$columns['recruiter'].") VALUES (:username, :passhash, :email, :joindate, :lastip, :flags, :recruiter);");
		$insert->bindParam(':username', $username, PDO::PARAM_STR);
		$insert->bindParam(':passhash', $shapasshash, PDO::PARAM_STR);
		$insert->bindParam(':email', $email, PDO::PARAM_STR);
		$insert->bindParam(':joindate', $joindate, PDO::PARAM_STR);
		$insert->bindParam(':lastip', $lastip, PDO::PARAM_STR);
		$insert->bindParam(':flags', $expansion, PDO::PARAM_INT);
		$insert->bindParam(':recruiter', $recruiter, PDO::PARAM_INT);
				
		//make sure the query was executed without errors
		if ($insert->execute())
		{
			$return = $AUTH_DB->lastInsertId();
		}
		else
		{
			$return = false;
		}
		unset($insert);
		unset($columns);
		
		return $return;
	}
}