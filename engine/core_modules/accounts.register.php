<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class AccountsRegister
{	
	public function __construct()
	{
		return true;
	}

	//function to check if account exists
	static public function usernameExists($name)
	{
		global $AUTH_DB;
		
		//get the column names for table accounts
		$columns = CORE_COLUMNS::get('accounts');
		
		$res = $AUTH_DB->prepare("SELECT ".$columns['id']." FROM `".$columns['self']."` WHERE `".$columns['username']."` = :name LIMIT 1");
		$res->bindParam(':name', $name, PDO::PARAM_STR);
		$res->execute();
		
		$count = $res->rowCount();
		
		if ($count > 0)
		{
			unset($res);
			return true;
		}
		unset($res);
		
	  return false;
	}

	//validtaion function
	static public function usernameValid($string)
	{
		if ($string == '')
	  	  return false;
		  
		//Check for alphanumeric character(s)
		if (!ctype_alnum($string))
	  	  return false;

	  return true;
	}
	
	//function to check if account with email exists
	static public function emailExist($email)
	{
		global $AUTH_DB;
		
		//get the column names for table accounts
		$columns = CORE_COLUMNS::get('accounts');
		
		$res = $AUTH_DB->prepare("SELECT ".$columns['id']." FROM `".$columns['self']."` WHERE `".$columns['email']."` = :email LIMIT 1");
		$res->bindParam(':email', $email, PDO::PARAM_STR);
		$res->execute();
		
		$count = $res->rowCount();
		
		if ($count > 0)
		{
			unset($res);
			return true;
		}
		unset($res);
		
	  return false;
	}
	
	static public function displayNameValid($string)
	{
		if ($string == '')
		{
	  	  	return false;
		}
		
		//Check the string for invalid characters
		$allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!`@";

		for ($i = 0; $i < strlen($string); ++$i)
		{
	 		if (strpos($allowedchars, $string[$i]) === false)
	   	 		return false;
		}

		return true;
	}
	
	static public function displayNameExist($string)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT id, displayName FROM `account_data` WHERE `displayName` = :str LIMIT 1");
		$res->bindParam(':str', $string, PDO::PARAM_STR);
		$res->execute();
		
		$count = $res->rowCount();
		
		if ($count > 0)
		{
			unset($res);
			return true;
		}
		unset($res);
		
	  	return false;
	}
	
	static public function checkUsername($username)
	{
		if (!$username)
		{
			//if no username was defined
			return 'Please enter username.';
		}
		else if (strlen($username) > 32)
		{
			//if the username is too long
			return 'The username is too long, maximum length 32.';
		}
		else if (strlen($username) < 5)
		{
			//if the username is too short
			return 'The username is too short, minimum length 5.';
		}
		else if (!self::usernameValid($username))
		{
			//if the username is invalid
			return 'The username is not valid.';
		}
		else if (self::usernameExists($username))
		{
			//check account existatnce
			return 'The username is already in use.';
		}
		
		return false;
	}
	
	static public function checkDisplayname($displayName)
	{
		if (!$displayName)
		{
			//display name is not defined
			return 'Please enter your Display Name.';
		}
		else if (strlen($displayName) > 32)
		{
			//display name too long
			return 'The display name is too long, maximum length 32.';
		}
		else if (strlen($displayName) < 5)
		{
			//display name too short
			return 'The display name is too short, minimum length 5.';
		}
		else if (!self::displayNameValid($displayName))
		{
			//display name is not valid
			return 'The display name is not valid. Allowed characters a-z, A-Z, 0-9 and symbols <strong>!</strong>, <strong>`</strong>, <strong>@</strong>.';
		}
	  	else if (self::displayNameExist($displayName))
		{
			//dispaly name is already in use
			return 'The display name is already in use.';
		}
	
		return false;
	}
	
	static public function checkPassword($password, $password2)
	{
		if (!$password)
		{
			//no password
			return 'Please enter password.';
		}
		else if (!$password2)
		{
			//password not confirmed
			return 'Please confirm your password.';
		}
		else if ($password != $password2)
		{
			//password do not match
			return 'You\'ve failed to confirm your password.';
		}
		else if (strlen($password) > 64)
		{
			//password too long
			return 'The password is too long, maximum length 64.';
		}
		else if (strlen($password) < 6)
		{
			//password too short
			return 'The password is too short, minimum length 6.';
		}
		
		return false;
	}
	
	static public function checkEmail($email)
	{
		if (!$email)
		{
			//is email defined
			return 'Please enter your E-mail Address.';
		}
		else if (self::emailExist($email))
		{
			//if the email is already in use
			return 'Account with that E-mail Address already exist.';
		}
	
		return false;
	}
	
	static public function checkBirthdayMonth($birthdayMonth)
	{
		//validate the month
		if (!$birthdayMonth)
		{
			//if there is no month selected
			return 'Please select your birthday\'s month.';
		}
		else if ($birthdayMonth)
		{
			if (strlen($birthdayMonth) != 2)
			{
				//if the selected month is not valid
				return 'Invalid birthday\'s month selected.';
			}
		}
		
		return false;
	}

	static public function checkBirthdayDay($birthdayDay)
	{		
		//validate the day
		if (!$birthdayDay)
		{
			//if the day is not defined
			return 'Please enter your birthday\'s day.';
		}
		else if ($birthdayDay)
		{
			$dayLen = strlen($birthdayDay);
			if ($dayLen > 2 or $birthdayDay < 1 or $birthdayDay > 31)
			{
				//invalid bithday day
				return 'Invalid birthday\'s day. Please enter value from 01 to 31.';
			}
		}
		
		return false;
	}
	
	static public function checkBirthdayYear($birthdayYear)
	{
		//validate the year
		if (!$birthdayYear)
		{
			//no birthday year was defined
			return 'Please enter your birthday\'s year.';
		}
		else if ($birthdayYear)
		{
			$yearLen = strlen($birthdayYear);
			if ($yearLen != 4 or $birthdayYear > date('Y') or $birthdayYear < (date('Y') - 100))
			{
				//invalid birthday year
				return 'Invalid birthday\'s year. Please enter 4 digits value from '.(date('Y') - 100).' to '.date('Y').'.';
			}
		}
		
		return false;
	}
	
	static public function checkCountry($country)
	{
		if (!$country)
		{
			return 'Please select country.';
		}
		else if ($country)
		{
			if (strlen($country) != 2)
			{
				return 'Invalid country selected.';
			}
		}
		
		return false;
	}
	
	static public function checkSecretQuestion($question)
	{
		$Questions = new SecretQuestionData();
		
		if (!$question)
		{
			return 'Please select your secret question.';
		}
		else if (!$Questions->get($question))
		{
			return 'Invalid secret question selected.';
		}
		
		unset($Questions);
		
		return false;
	}
	
	static public function checkSecretAnswer($answer)
	{
		if (!$answer)
		{
			return 'Please enter answer to your secret qeustion.';
		}
		else if (strlen($answer) > 255)
		{
			//answer too long
			return 'The secret answer is too long, maximum length 255.';
		}
		else if (strlen($answer) < 3)
		{
			//answer too short
			return 'The secret answer is too short, minimum length 3.';
		}
		
		return false;
	}
	
	public function __destrruct()
	{
		return true;
	}
}