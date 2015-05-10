<?php
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

//Load the Tokens Module
$CORE->load_CoreModule('tokens');
//Load the Text captha Module
$CORE->load_CoreModule('text.captcha');

$captcha = new TextCaptcha();

//define the email reward token lifetime
$emailTokenLifetime = '24 hours';
//the event we should apply to the account
$event = 'PASSWORD_RECOVERY_PENDING';

//Get variables
$email = isset($_POST['email']) ? trim($_POST['email']) : false;

//setup new instance of multiple errors
$ERRORS->NewInstance('password_recovery');

//missing inputs check
if (!$email)
{
	$ERRORS->Add('Please enter your E-mail Address.');
}

########################################################################################################
######### Text Captcha Check 
	
	/*
	if ($CaptchaResponseField = $captcha->GetResponseFieldName())
	{
		$CaptchaResponse = isset($_POST[$CaptchaResponseField]) ? $_POST[$CaptchaResponseField] : false;
		//check if it was filled in
		if (!$CaptchaResponse)
		{
			$ERRORS->Add('Please answer the Human Test question.');
		}
		else if (!$captcha->CheckAnswer($CaptchaResponse))
		{
			$ERRORS->Add('You have failed to answer the Human Test question.');
		}
	}
	else
	{
		$ERRORS->Add('There was a problem with the Human Test.');
	}
	//kill the captcha session
	$captcha->Kill();
	//free up some mem
	unset($CaptchaResponseField, $CaptchaResponse, $captcha);
	*/
	
########################################################################################################

//get the column names for table accounts
$columns = CORE_COLUMNS::get('accounts');

$res = $AUTH_DB->prepare("SELECT ".$columns['id'].", ".$columns['email'].", ".$columns['username']." FROM `".$columns['self']."` WHERE `".$columns['email']."` = :email LIMIT 1;");
$res->bindParam(':email', $email, PDO::PARAM_STR);
$res->execute();

if ($res->rowCount() > 0)
{
	$row = $res->fetch(PDO::FETCH_ASSOC);
}
else
{
	if ($email)
		$ERRORS->Add('Incorrent E-Mail address. Please make sure you enter the correct E-Mail address of the account.');
}
unset($res);

//Check for errors
$ERRORS->Check('/index.php?page=password_recovery');

####################################################################
## The actual script begins here

//assume failure
$success = false;

//Let's setup our token
$token = new Tokens();

//Set the account ID to be included as salt
$token->setIdentifier($row[$columns['id']]);
//Set the application string so the token is only valid for this app
$token->setApplication('PRECOVER');
//Set the token expiration time
$token->setExpiration($emailTokenLifetime);
//Save the user ID under the token
$token->setExternalData($row);
//Generate a key for the token
$token->generateKey();
//get the encoded key
$key = $token->get_encodedToken();
//register the token
$tokenReg = $token->registerToken();

//continue only if the key was successfully registered
if ($tokenReg === true)
{
	//update the account event
	$update = $DB->prepare("UPDATE `account_data` SET `event` = UPPER(:event) WHERE `id` = :id LIMIT 1;");
	$update->bindParam(':id', $row[$columns['id']], PDO::PARAM_INT);
	$update->bindParam(':event', $event, PDO::PARAM_STR);
	$update->execute();
	unset($update);
	
	############################################################################
	## Not it's time to send the revocery mail
	$CORE->load_CoreModule('phpmailer');
	
	//setup the PHPMailer class
	$mail = new PHPMailer();
	$mail->IsMail();
	$mail->From = $config['Email'];
	$mail->FromName =  'Warcry WoW - Support';
	
	//get the message html
	$message = file_get_contents($config['RootPath'] . '/resources/mails/recovery_mail.html');
			
	//If for some reason we couldnt get the mail HTMl send blank with a key
	if (!$message)
	{
		$message = $config['BaseURL'] . '/index.php?page=password_recovery&verify=1&key=' . $key;
	}
	else
	{
		//Get the user display name
		$res = $DB->prepare("SELECT `displayname` FROM `account_data` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $row['id'], PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$drow = $res->fetch();
			$displayname = $drow['displayname'];
			unset($drow);
		}
		else
		{
			$displayname = 'Unknown';
		}
		unset($res);
		
		//replace the tags with info
		$search = array('{DISPLAY_NAME}', '{URL}');
		$replace = array($displayname, $config['BaseURL'] . '/index.php?page=password_recovery&verify=1&key=' . $key);
		$message = str_replace($search, $replace, $message);
		unset($search, $replace);
	}
	
	//By now we should have the mail message
	$mail->AddAddress($email);

	$mail->WordWrap = 50;
	$mail->IsHTML(true);
	
	$mail->Subject = "Warcry WoW Password Recovery";
	$mail->Body    = $message;
	
	//check if the message was sent
	if ($mail->Send())
	{
		$success = true;
	}
	
	//unset them variables
	unset($displayname, $message, $mail);
	
	############################################################################
	############################################################################
}
else
{
	$ERRORS->Add('Failed to send password recovery mail. Please contact the administration.');
}

//Unset some variables
unset($tokenReg, $key, $token, $event, $emailTokenLifetime, $row, $email);

//Check for errors
$ERRORS->Check('/index.php?page=password_recovery');

//handle the success
if ($success)
{
	//Setup our welcoming notification
	$NOTIFICATIONS->SetTitle('Password Recovery');
	$NOTIFICATIONS->SetHeadline('One step away!');
	$NOTIFICATIONS->SetText('We have sent you a mail containing the instructions to complete the recovery process.<br>This process may only be completed in the next 24 hours.');
	$NOTIFICATIONS->SetTextAlign('center');
	//$NOTIFICATIONS->SetAutoContinue(true);
	//$NOTIFICATIONS->SetContinueDelay(5);
	$NOTIFICATIONS->Apply();
	
	header("Location: ".$config['BaseURL']."/index.php?page=home");
}

die;