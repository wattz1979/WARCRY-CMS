<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Define the reward amount (Silver Coins)
$RewardAmount = 5;

$app = ((isset($_POST['app'])) ? (int)$_POST['app'] : false);
$status = ((isset($_POST['status'])) ? (int)$_POST['status'] : false);

###############################################################
########## Check if the values are correct ####################

if (!$CURUSER->isOnline())
{
	//no user no need
	exit;
}

$allowedApps = array(APP_FACEBOOK, APP_TWITTER);
if (!in_array($app, $allowedApps))
{
	//idk what else could i do...
	exit;
}
unset($allowedApps);

$allowedStats = array(STATUS_NEGATIVE, STATUS_POSITIVE);
if (!in_array($status, $allowedStats))
{
	//idk what else could i do...
	exit;
}
unset($allowedStats);

//check if it needs updating
if ($CURUSER->getSocial($app) === $status)
{
	//already set
	exit;
}

###############################################################
############### Update the Social Status ######################

	//Im going to limit the status update to positive only because
	//we are going to reward users who give us positives
	if ($CURUSER->getSocial($app) === STATUS_POSITIVE)
	{
		exit;
	}
	
	$CORE->load_CoreModule('purchaseLog');
	//prepare the log
	$logs = new purchaseLog();
	//start logging
	$logs->add('SOCIAL_BUTTONS', 'Starting log session. Application: '.$app.', status variable: '.$status.'.', 'pending');
	
	//Update the user social button status
	if ($CURUSER->setSocial($app, $status))
	{
		//If the status was update reward the user
		//log successful status update
		$logs->update(false, 'The user status was update.', 'pending');
		
		//Load the most important module
		$CORE->load_CoreModule('accounts.finances');
		
		//Setup the finances class
		$finance = new AccountFinances();
				
		//Set the account id
		$finance->SetAccount($CURUSER->get('id'));
		//Set the currency to gold
		$finance->SetCurrency(CURRENCY_SILVER);
		//Set the amount we are Giving
		$finance->SetAmount($RewardAmount);
		
		//Resolve the source [facebook, twitter etc...]
		switch ($app)
		{
			case APP_FACEBOOK:
				$CA_Source = 'You liked us on Facebook';
				break;
			case APP_TWITTER:
				$CA_Source = 'You followed us on Twitter';
				break;
			default:
				$CA_Source = 'Unknown action';
				break;
		}
		
		//Finally reward the user
		$Reward = $finance->Reward($CA_Source, CA_SOURCE_TYPE_REWARD);
		
		//Make sure the reward was given, otherwise log this failure
		if ($Reward !== true)
		{
			$logs->update(false, 'The website failed to reward the user.', 'error');
		}
		else
		{
			$logs->update(false, 'The user was rewarded.', 'ok');
		}
		
		unset($CA_Source, $Reward, $finance);
	}
	
	//free mem
	unset($app, $status, $RewardAmount, $logs);

exit;