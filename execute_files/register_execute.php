<?php
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//setup new instance of multiple errors
$ERRORS->NewInstance('register');

//load the register module
$CORE->load_CoreModule('accounts.register');
$CORE->load_CoreModule('raf');
$CORE->load_CoreModule('email.reservation');
$CORE->load_CoreModule('text.captcha');

$raf = new RAF();
$captcha = new TextCaptcha();

//Define the variables
$username = isset($_POST['username']) ? $_POST['username'] : false;

$displayName = isset($_POST['displayname']) ? $_POST['displayname'] : false;

$password = isset($_POST['password']) ? $_POST['password'] : false;
$password2 = isset($_POST['password2']) ? $_POST['password2'] : false;

$email = isset($_POST['email']) ? $_POST['email'] : false;

$birthdayMonth = isset($_POST['birthday']['month']) ? $_POST['birthday']['month'] : false;
$birthdayDay = isset($_POST['birthday']['day']) ? $_POST['birthday']['day'] : false;
$birthdayYear = isset($_POST['birthday']['year']) ? $_POST['birthday']['year'] : false;

$country = isset($_POST['country']) ? $_POST['country'] : false;

$secretQuestion = isset($_POST['secretQuestion']) ? (int)$_POST['secretQuestion'] : false;
$secretAnswer = isset($_POST['secretAnswer']) ? $_POST['secretAnswer'] : false;

$rafHash = isset($_POST['raf']) ? $_POST['raf'] : false;

//missing inputs check
######################################
######## USERNAME CHECK ##############
	if ($usernameError = AccountsRegister::checkUsername($username))
	{
		$ERRORS->Add($usernameError);
	}
	
$username = trim($username);

######################################
###### DISPLAY NAME CHECK ############
	if ($displaynameError = AccountsRegister::checkDisplayname($displayName))
	{
		$ERRORS->Add($displaynameError);
	}
	
######################################
######## PASSWORD CHECK ##############
	if ($passwordError = AccountsRegister::checkPassword($password, $password2))
	{
		$ERRORS->Add($passwordError);
	}
	
$password = trim($password);

######################################
######### EMAIL CHECK ################
	if ($emailError = AccountsRegister::checkEmail($email))
	{
		$ERRORS->Add($emailError);
	}
	else
	{
		//check for reservation
		if (EmailReservations::IsReserved(array('email' => $email)) === true)
		{
			$ERRORS->Add('The e-mail address is reserved.');
		}
	}
	
$email = trim($email);

######################################
######### BIRTHDAY Check #############
	//validate the Month
	if ($birthdayMonthError = AccountsRegister::checkBirthdayMonth($birthdayMonth))
	{
		$ERRORS->Add($birthdayMonthError);
	}
	
	//validate the Day
	if ($birthdayDayError = AccountsRegister::checkBirthdayDay($birthdayDay))
	{
		$ERRORS->Add($birthdayDayError);
	}

	//validate the Year
	if ($birthdayYearError = AccountsRegister::checkBirthdayYear($birthdayYear))
	{
		$ERRORS->Add($birthdayYearError);
	}

//add zero "0" to the day if it's not aready entered
$dayLen = strlen($birthdayDay);
if (($dayLen >= 1 and $dayLen <= 2) and ($birthdayDay >= 1 and $birthdayDay <= 31))
{
	if ($dayLen == 1)
	{
		$birthdayDay = '0' . $birthdayDay;
	}
}

//merge the birthday
$birthday = $birthdayMonth . '/' . $birthdayDay . '/' . $birthdayYear;

######################################
######### Country Check ##############
	if ($countryError = AccountsRegister::checkCountry($country))
	{
		$ERRORS->Add($countryError);
	}

######################################
## Secret Question & Answer Check ####
	if ($secretQuestionError = AccountsRegister::checkSecretQuestion($secretQuestion))
	{
		$ERRORS->Add($secretQuestionError);
	}
	
	if ($secretAnswerError = AccountsRegister::checkSecretAnswer($secretAnswer))
	{
		$ERRORS->Add($secretAnswerError);
	}

$secretAnswer = trim($secretAnswer);

######################################
######### Text Captcha Check #########
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
//Check for errors
$ERRORS->Check('/index.php?page=register'.($rafHash ? '&raf='.$rafHash : ''));

##################################################
######## REGISTER SERVER ACCOUNT #################

//some default variables
$expansion = 2;
$recruiter = 0;

	//resolve the RAF acc ID
	if ($rafHash)
	{
		if ($rafRow = $raf->FindHash($rafHash))
		{
			$recruiter = $rafRow['account'];
		}
	}
	
	//register
  	if ($accountId = server_Account::register($username, $password, $email, $expansion, $recruiter))
  	{
		//unset the terms variable
		unset($_SESSION['TermsAccepted']);
		
		//Get visitor's IP Address
		$ip = $SECURITY->getip();
		$thetime = $CORE->getTime();
		$regStatus = 'active';
	  	
		//hash the secret answer
		$aHash = sha1($secretQuestion . ':' . strtolower($secretAnswer));

		//insert web record
		$insert = $DB->prepare("REPLACE INTO `account_data` (`id`, `displayName`, `birthday`, `country`, `secretQuestion`, `secretAnswer`, `last_ip`, `reg_ip`, `last_login`, `last_login2`, `status`) VALUES (:accid, :displayName, :birthday, :country, :secretQuestion, :secretAnswer, :lastip, :regip, '0000-00-00 00:00:00', :lastlogin2, :status);");
		$insert->bindParam(':accid', $accountId, PDO::PARAM_INT);
		$insert->bindparam(':displayName', $displayName, PDO::PARAM_STR);
		$insert->bindParam(':birthday', $birthday, PDO::PARAM_STR);
		$insert->bindParam(':country', $country, PDO::PARAM_STR);
		$insert->bindParam(':secretQuestion', $secretQuestion, PDO::PARAM_INT);
		$insert->bindParam(':secretAnswer', $aHash, PDO::PARAM_STR);
		$insert->bindParam(':lastip', $ip, PDO::PARAM_STR);
		$insert->bindParam(':regip', $ip, PDO::PARAM_STR);
		$insert->bindParam(':lastlogin2', $thetime, PDO::PARAM_STR);
		$insert->bindParam(':status', $regStatus, PDO::PARAM_STR);
		$insert->execute();
		
		######################################
		############## RAF ###################
		//make a new raf link record because
		//we dont wanna query out auth databse 
		//too much with the website
		if ($rafHash)
		{
			if ($rafRow)
			{
				$raf->CreateLink($accountId, $recruiter);
			}
		}
		
		######################################
		############ MAILING #################
		$CORE->load_CoreModule('phpmailer');
		
		//setup the PHPMailer class
		$mail = new PHPMailer();
		$mail->IsMail();
		$mail->From = $config['Email'];
		$mail->FromName =  'Warcry WoW - Info';
		$mail->AddAddress($email);
		
		//get the message html
		$message = file_get_contents($config['RootPath'] . '/resources/mails/register_mail.html');
				
		//break if the function failed to laod HTML
		if ($message)
		{				
			//replace the tags with info
			$search = array('{USERNAME}', '{DISPLAYNAME}', '{PASSWORD}');
			$replace = array($username, $displayName, $password);
			$message = str_replace($search, $replace, $message);
			
			$mail->WordWrap = 50;
			$mail->IsHTML(true);
			
			$mail->Subject = "Warcry WoW Registration";
			$mail->Body    = $message;
			//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";

	  		$mail->Send();
		}

		######################################
		############# LOGIN ##################
		$shapasshash = server_Account::makeHash($username, $password);
		$CURUSER->setLoggedIn($accountId, $shapasshash);
		
		//unset
		unset($raf);
		
		//Setup our welcoming notification
		$NOTIFICATIONS->SetTitle('Notification');
		$NOTIFICATIONS->SetHeadline('Congratulation!');
		$NOTIFICATIONS->SetText('Welcome and thank you for joining the Warcry community.<br>Your Warcry account has been automatically activated.<br>Please enjoy.');
		$NOTIFICATIONS->SetTextAlign('center');
		//$NOTIFICATIONS->SetAutoContinue(true);
		//$NOTIFICATIONS->SetContinueDelay(5);
		$NOTIFICATIONS->Apply();
		
		######################################
		########## Redirect ##################
		header("Location: ".$config['BaseURL']."/index.php?page=home");
  	}
	else
	{
		$ERRORS->Add('Website Failure, it seems the website is not functioning at the moment. If this problem persists please contact the administration.');
	}

//unset
unset($raf);

$ERRORS->Check('/index.php?page=register'.($rafHash ? '&raf='.$rafHash : ''));

exit;