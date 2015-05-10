<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//if the user is already logged in return him to index
if ($CURUSER->isOnline())
{
   header("Refresh: 0; url=".$config['BaseURL']."/admin/index.php");
   exit();
}

$ERRORS->Add("Incorrect username or password.");
//prepare multi errors
$ERRORS->NewInstance('login');
	
$username = (isset($_POST['username']) ? $_POST['username'] : false);
$password = (isset($_POST['password']) ? $_POST['password'] : false);

if (isset($_POST['url_bl']))
{
	$_SESSION['url_bl'] = $_POST['url_bl'];
}

if (!$username)
{
	$ERRORS->Add("Please enter account name.");
}
if (!$password)
{
	$ERRORS->Add("Please enter account password.");
}

$ERRORS->Check('/login.php');

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
			//check if the account is allowed to login into the admin panel
			$perms = new Permissions($accid);
			
			if ($perms->IsAllowedToUseACP())
			{
				//make some logging
				$CURUSER->logInfoAtLogin($accid);
				
				//Login the user
				$CURUSER->setLoggedIn($accid, $passcheck);
				
				//check if we have URL the user wanted to access before we ask to login
				if (isset($_SESSION['url_bl']))
				{ 
					$url = trim($_SESSION['url_bl']);
					unset($_SESSION['url_bl']);
				}
				elseif (isset($_POST['url_bl']))
				{
					$url = trim($_POST['url_bl']);
				}
				else
				{
		  			$url = $config['BaseURL'] . '/admin/index.php?code=login_success';
				}
					
				//redirect
		  		header("Location: " . $url);
				exit;
			}
			else
			{
				//print message even if the account exist
				$ERRORS->Add("Incorrect username or password.");
			}
		}
		else
		{
			$ERRORS->Add("Incorrect username or password.");
		}
	}
	else
	{
		$ERRORS->Add("Incorrect username or password.");
	}
	unset($res);

####################################################################

$ERRORS->Check('/login.php');

exit;