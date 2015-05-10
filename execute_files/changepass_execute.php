<?php
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//setup new instance of multiple errors
$ERRORS->NewInstance('changepass');
//bind the onsuccess message
$ERRORS->onSuccess('Your password has been successfully changed.', '/index.php?page=changepass');

//Define the variables
$password = isset($_POST['password']) ? $_POST['password'] : false;
$newpassword = isset($_POST['newPassword']) ? $_POST['newPassword'] : false;
$newpassword2 = isset($_POST['newPassword2']) ? $_POST['newPassword2'] : false;

######################################
######## PASSWORD CHECK ##############
	
	//make the hash to check current pass
	$current_shapasshash = server_Account::makeHash($CURUSER->get('username'), $password);
	
	if (!$password)
	{
		//The current password is not defined
		$ERRORS->Add('Please enter your current password.');
	}
	else if ($current_shapasshash != $CURUSER->get('shapasshash'))
	{
		//check if the password is valid
		$ERRORS->Add('You\'ve entered wrong account password.');
	}
		
######################################
###### NEW PASSWORD CHECK ############
	if (!$newpassword)
	{
		//no new password
		$ERRORS->Add('Please enter new password.');
	}
	else if (!$newpassword2)
	{
		//password not confirmed
		$ERRORS->Add('Please confirm your new password.');
	}
	else if ($newpassword == $password)
	{
		//if the new pass and old pass are the same
		$ERRORS->Add('Your new password is exactly the same as your old one.');
	}
	else if ($newpassword != $newpassword2)
	{
		//password do not match
		$ERRORS->Add('You\'ve failed to confirm your new password.');
	}
	else if (strlen($newpassword) > 64)
	{
		//password too long
		$ERRORS->Add('The new password is too long, maximum length 64.');
	}
	else if (strlen($newpassword) < 6)
	{
		//password too short
		$ERRORS->Add('The new password is too short, minimum length 6.');
	}
	
$newpassword = trim($newpassword);

//Check for errors
$ERRORS->Check('/index.php?page=changepass');

##################################################
######## REGISTER SERVER ACCOUNT #################

	//get the column names for table accounts
	$columns = CORE_COLUMNS::get('accounts');
	
	//make our new pass hash
	$shapasshash = server_Account::makeHash($CURUSER->get('username'), $newpassword);
	
	//Apply the new hash to the account
	$update = $AUTH_DB->prepare("UPDATE `".$columns['self']."` SET `".$columns['shapasshash']."` = :hash, `".$columns['sessionkey']."` = '', `".$columns['v']."` = '', `".$columns['s']."` = '' WHERE `".$columns['id']."` = :acc LIMIT 1;");
	$update->bindParam(':hash', $shapasshash, PDO::PARAM_STR);
	$update->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
	$update->execute();
		
	//check if the account was affected
	if ($update->rowCount() > 0)
	{
		######################################
		############# LOGIN ##################
		$CURUSER->setLoggedIn($CURUSER->get('id'), $shapasshash);
	
		######################################
		########## Redirect ##################	
		$ERRORS->triggerSuccess();
	}
	else
	{
		$ERRORS->Add('The website failed to change your account password. Please contact the administration.');
	}

$ERRORS->Check('/index.php?page=changepass');

exit;