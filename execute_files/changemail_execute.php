<?php
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

$CORE->load_CoreModule('email.reservation');

//setup new instance of multiple errors
$ERRORS->NewInstance('changemail');
//bind the onsuccess message
$ERRORS->onSuccess('Your E-mail Address was successfuly changed.', '/index.php?page=changemail');

//Define the variables
$email = isset($_POST['email']) ? $_POST['email'] : false;
$secretQuestion = isset($_POST['secretQuestion']) ? (int)$_POST['secretQuestion'] : false;
$secretAnswer = isset($_POST['secretAnswer']) ? trim($_POST['secretAnswer']) : false;

######################################
###### SECRET ANSWER CHECK ###########
	
	//hash the secret answer
	$aHash = sha1($secretQuestion . ':' . strtolower($secretAnswer));
	
	if (!$secretAnswer)
	{
		//The current password is not defined
		$ERRORS->Add('Please enter answer to your Secret Question.');
	}
	else if ($aHash != $CURUSER->get('secretAnswer'))
	{
		//check if the password is valid
		$ERRORS->Add('You\'ve entered wrong Secret Question or Secret Answer.');
	}
	
######################################
######## NEW EMAIL CHECK #############
	if (!$email)
	{
		//no new password
		$ERRORS->Add('Please enter your new E-mail Address.');
	}
	else
	{
		//check for reservation
		if (EmailReservations::IsReserved(array('email' => $email)) === true)
		{
			$ERRORS->Add('The e-mail address is reserved.');
		}
	}

//Check for errors
$ERRORS->Check('/index.php?page=changemail');

##################################################
######## REGISTER SERVER ACCOUNT #################

	//get the column names for table accounts
	$columns = CORE_COLUMNS::get('accounts');
	
	//make our new pass hash
	$shapasshash = server_Account::makeHash($CURUSER->get('username'), $newpassword);
	
	//Apply the new hash to the account
	$update = $AUTH_DB->prepare("UPDATE `".$columns['self']."` SET `".$columns['email']."` = :email WHERE `".$columns['id']."` = :acc LIMIT 1;");
	$update->bindParam(':email', $email, PDO::PARAM_STR);
	$update->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
	$update->execute();
		
	//check if the account was affected
	if ($update->rowCount() > 0)
	{
		######################################
		########## Redirect ##################	
		$ERRORS->triggerSuccess();
	}
	else
	{
		$ERRORS->Add('The website failed to change your E-mail Address. Please contact the administration.');
	}

$ERRORS->Check('/index.php?page=changemail');

exit;