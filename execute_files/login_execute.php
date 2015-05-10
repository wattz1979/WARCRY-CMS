<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//if the user is already logged in return him to index
if ($CURUSER->isOnline())
{
   header("Refresh: 0; url=".$config['BaseURL']."/index.php");
   exit();
}

//prepare multi errors
$ERRORS->NewInstance('login');
	
$username = (isset($_POST['username']) ? $_POST['username'] : false);
$password = (isset($_POST['password']) ? $_POST['password'] : false);
$rememberme = (isset($_POST['rememberme']) ? true : false);

if (isset($_POST['url_bl']))
{
	//check if it is valid URL
	if($CORE->ValidateURLBeforeLogin($_POST['url_bl']))
	{
		$_SESSION['url_bl'] = $_POST['url_bl'];
	}
	unset($_POST['url_bl']);
}

if (!$username)
{
	$ERRORS->Add("Please enter account name.");
}
if (!$password)
{
	$ERRORS->Add("Please enter account password.");
}

$ERRORS->Check('/index.php?page=login');

####################################################################
## The actual Login script begins here
	
	//get the column names for table accounts
	$columns = CORE_COLUMNS::get('accounts');

	//make the query to the logon database
	$res = $AUTH_DB->prepare("SELECT ".$columns['id'].", ".$columns['username'].", ".$columns['shapasshash'].", ".$columns['email'].", ".$columns['flags']." FROM `".$columns['self']."` WHERE ".$columns['username']." = :username LIMIT 1");
	
	//bind some parameters
	$res->bindParam(':username', $username, PDO::PARAM_STR);
	
	//bind the columns for easy usage
	$res->bindColumn(1, $accid, PDO::PARAM_INT);
	$res->bindColumn(2, $accusername, PDO::PARAM_STR);
	$res->bindColumn(3, $accpasshash, PDO::PARAM_STR);
	$res->bindColumn(4, $accemail, PDO::PARAM_STR);
	$res->bindColumn(5, $accflags, PDO::PARAM_INT);
	
	//run the query
	$res->execute();
		
	//check if we have found the record
	if ($res->rowCount() > 0)
	{
		//fetch the record
		$row = $res->fetch(PDO::FETCH_NUM);
		
		//make new pass hash
		$passcheck = server_Account::makehash($username, $password);

		//compare the new pass hash with the one in the record
		if ($accpasshash == $passcheck)
		{ 
			$continue = false;
			
			//make some logging
			if (!$CURUSER->logInfoAtLogin($accid))
			{
				//try creating new record for this acc
				if ($CURUSER->handle_MissingRecord($accid))
				{
					$continue = true;
				}
				else
				{
					$ERRORS->Add("The account you are trying to access is broken. Please contact the administration.");
				}
			}
			else
			{
				$continue = true;
			}
			
			//if the account is good to be logged in
			if ($continue)
			{
				//Login the user
				$CURUSER->setLoggedIn($accid, $passcheck);
				
				//needed for the loginb page
				$_SESSION['JustLoggedIn'] = true;
				
				
				//Remember me
				if ($rememberme)
				{
					//Generate random salt
					$salt = uniqid(mt_rand(), true);
					
					//store the salt
					$update = $DB->prepare("UPDATE `account_data` SET `salt` = :salt WHERE `id` = :acc LIMIT 1;");
					$update->bindParam(':acc', $accid, PDO::PARAM_INT);
					$update->bindParam(':salt', $salt, PDO::PARAM_STR);
					$update->execute();
					
					//make the hash for the cookie
					$newHash = sha1($accpasshash . $salt);
					
					//Remember the user for a month
					$expire = strtotime('+1 month', time());
					
					//prepare the cookie value
					$value = $username . '-' . $newHash;
					
					//set the cookie
					$CORE->setCookie('rmm', $value, $expire);

					//mem
					unset($newHash, $expire, $value, $salt);
				}
						
				//redirect
	  			header("Location: " . $config['BaseURL'] . "/index.php?page=loginb");
				exit;
			}
		}
		else
		{
			$ERRORS->Add("You've entered wrong password.");
		}
	}
	else
	{
		$ERRORS->Add("The account you are trying to access does not exist.");
	}
	unset($res);

####################################################################

$ERRORS->Check('/index.php?page=login');

exit;