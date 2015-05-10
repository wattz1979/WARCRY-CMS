<?php
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Load the Tokens Module
$CORE->load_CoreModule('tokens');

//setup new instance of multiple errors
$ERRORS->NewInstance('password_recovery');

//Define the variables
$password = isset($_POST['password']) ? $_POST['password'] : false;
$password2 = isset($_POST['password2']) ? $_POST['password2'] : false;
$key = isset($_SESSION['P_Recovery_Token']) ? $_SESSION['P_Recovery_Token'] : false;

//construct
$token = new Tokens();
//Set the application string so the token is only valid for this app
$token->setApplication('PRECOVER');
						
######################################
###### NEW PASSWORD CHECK ############
	if (!$password)
	{
		//no new password
		$ERRORS->Add('Please enter new password.');
	}
	else if (!$password2)
	{
		//password not confirmed
		$ERRORS->Add('Please confirm your new password.');
	}
	else if ($password != $password2)
	{
		//password do not match
		$ERRORS->Add('You\'ve failed to confirm your new password.');
	}
	else if (strlen($password) > 64)
	{
		//password too long
		$ERRORS->Add('The new password is too long, maximum length 64.');
	}
	else if (strlen($password) < 6)
	{
		//password too short
		$ERRORS->Add('The new password is too short, minimum length 6.');
	}
	//Check if the key is set and valid
	if (!$key or $token->set_decodedToken($key) !== true)
	{
		//Setup our notification
		$NOTIFICATIONS->SetTitle('Notification');
		$NOTIFICATIONS->SetHeadline('Error!');
		$NOTIFICATIONS->SetText('Invalid security token.<br>Please open your your e-mail and follow the instruction we have sent you.');
		$NOTIFICATIONS->SetTextAlign('center');
		//$NOTIFICATIONS->SetAutoContinue(true);
		//$NOTIFICATIONS->SetContinueDelay(5);
		$NOTIFICATIONS->Apply();
		
		header("Location: ".$config['BaseURL']."/index.php?page=password_recovery");
		die;
	}
	
$password = trim($password);

//Check for errors
$ERRORS->Check('/index.php?page=password_recovery&verify=1&key='.$key);

##################################################
############## The actual script #################
	
	//Get the external data for the token
	$row = $token->get_enternalData();
	
	//Destroy this token
	$token->destroyToken();
	
	//unset the class
	unset($token);
	
	//get the column names for table accounts
	$columns = CORE_COLUMNS::get('accounts');
	
	//make our new pass hash
	$shapasshash = server_Account::makeHash($row[$columns['username']], $password);
	
	//Apply the new hash to the account
	$update = $AUTH_DB->prepare("UPDATE `".$columns['self']."` SET `".$columns['shapasshash']."` = :hash, `".$columns['sessionkey']."` = '', `".$columns['v']."` = '', `".$columns['s']."` = '' WHERE `".$columns['id']."` = :acc LIMIT 1;");
	$update->bindParam(':hash', $shapasshash, PDO::PARAM_STR);
	$update->bindParam(':acc', $row[$columns['id']], PDO::PARAM_INT);
	$update->execute();
		
	//check if the account was affected
	if ($update->rowCount() > 0)
	{
		//update the account event
		$update = $DB->prepare("UPDATE `account_data` SET `event` = '' WHERE `id` = :id LIMIT 1;");
		$update->bindParam(':id', $row[$columns['id']], PDO::PARAM_INT);
		$update->execute();
		unset($update);
		
		//Setup our notification
		$NOTIFICATIONS->SetTitle('Password Recovery');
		$NOTIFICATIONS->SetHeadline('Congratulations!');
		$NOTIFICATIONS->SetText('Your account password has been updated.<br>Please enjoy your stay.');
		$NOTIFICATIONS->SetTextAlign('center');
		//$NOTIFICATIONS->SetAutoContinue(true);
		//$NOTIFICATIONS->SetContinueDelay(5);
		$NOTIFICATIONS->Apply();
		
		######################################
		############# LOGIN ##################
		$CURUSER->setLoggedIn($row[$columns['id']], $shapasshash);

		unset($row);
		
		######################################
		########## Redirect ##################	
		header("Location: ".$config['BaseURL']."/index.php?page=home");
	}
	else
	{
		$ERRORS->Add('The website failed to change your account password. Please contact the administration.');
	}

$ERRORS->Check('/index.php?page=changepass');

exit;