<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class EmailReservations
{
	const EXPIRE_NEVER = 'never';
	const DEFAULT_APPLICATION = 'SYSTEM';

	/**
	**  Generates random key
	**/	
	static public function generateKey()
	{
		//generate the key
		$key = uniqid(mt_rand(), true) . uniqid(mt_rand(), true);
		
		//strip dots
		return str_replace('.', '', $key);			
	}
		
	/**
	**  Reserves email address uses array as arguments and returns array with key 'result' containing bool true/false
	**  bool true - will also return 'key' containing the key used for the reserve
	**  bool false - will also return 'error' containing error message
	**/	
	static public function Reserve($args)
	{
		global $DB, $CORE;
		
		//define the default arguments
		$defaultArgs = array(
			'application'	=> self::DEFAULT_APPLICATION,
			'key'			=> self::generateKey(),
			'time'			=> $CORE->getTime(),
			'expire'		=> self::EXPIRE_NEVER,
		);
		
		//check if the arguments are array
		if (!is_array($args))
		{
			return array('result' => false, 'error' => 'The arguments must be passed as array.');
		}
		
		//check if email was passed
		if (!isset($args['email']))
		{
			return array('result' => false, 'error' => 'Please pass email to the arguments.');
		}
		
		//merge with defaults
		$args = array_merge($defaultArgs, $args);
		
		//insert new key	
		$insert_res = $DB->prepare("INSERT INTO `reserved_emails` (`email`, `application`, `key`, `time`, `expire`) VALUES (:email, :app, :key, :time, :expire);");
		$insert_res->bindParam(':email', $args['email'], PDO::PARAM_STR);
		$insert_res->bindParam(':app', $args['application'], PDO::PARAM_STR);
		$insert_res->bindParam(':key', $args['key'], PDO::PARAM_STR);
		$insert_res->bindParam(':time', $args['time'], PDO::PARAM_STR);
		$insert_res->bindParam(':expire', $args['expire'], PDO::PARAM_STR);
		$insert_res->execute();
		
		if ($insert_res->rowCount() < 1)
		{
			return array('result' => false, 'error' => 'Unable to insert the record into the database.');
		}
		unset($insert_res);
		
		//return the key used for the reserve
		return array('result' => true, 'key' => $args['key']);
	}
	
	/**
	**  Removes E-mail Reservation
	**  Returns:
	**    true - upon success
	**    string - upon error
	**/		
	static public function Unreserve($args)
	{
		global $DB;

		//define the default arguments
		$defaultArgs = array(
			'application'	=> self::DEFAULT_APPLICATION,
		);

		//check if the arguments are array
		if (!is_array($args))
		{
			return 'The arguments must be passed as array.';
		}
		//check if email was passed
		if (!isset($args['email']))
		{
			return 'Please pass email to the arguments.';
		}
		//check if key was passed
		if (!isset($args['key']))
		{
			return 'Please pass key to the arguments.';
		}

		//merge with defaults
		$args = array_merge($defaultArgs, $args);
		
		//verify the key
		$res = $DB->prepare("SELECT * FROM `reserved_emails` WHERE `email` = :email AND `application` = :app LIMIT 1;");
		$res->bindParam(':email', $args['email'], PDO::PARAM_STR);
		$res->bindParam(':app', $args['application'], PDO::PARAM_STR);
		$res->execute();
		
		//check if we have a match
		if ($res->rowCount() == 0)
		{
			//the email is not reserved so just return success
			return true;
		}
		
		//fetch the found record
		$row = $res->fetch();
		//unset the res
		unset($res);
		
		//Now we must validate the key
		if ($args['key'] === $row['key'])
		{
			//delete the record we have the key
			$delete = $DB->prepare("DELETE FROM `reserved_emails` WHERE `id` = :id LIMIT 1;");
			$delete->bindParam(':id', $row['id'], PDO::PARAM_INT);
			$delete->execute();
			
			if ($delete->rowCount() == 0)
			{
				return 'Failed to delete the record.';
			}
			else
			{
				//the email is now available
				return true;
			}
		}
		else
		{
			return 'Wrong key was passed.';
		}
	}

	/**
	**  Checks if a Email Adress is reserved
	**  Returns:
	**    true - if reserved
	**    false - if available
	**    string - upon error
	**/		
	static public function IsReserved($args)
	{
		global $DB, $CORE;

		//define the default arguments
		$defaultArgs = array(
			'application'	=> self::DEFAULT_APPLICATION,
		);

		//check if the arguments are array
		if (!is_array($args))
		{
			return 'The arguments must be passed as array.';
		}
		//check if email was passed
		if (!isset($args['email']))
		{
			return 'Please pass email to the arguments.';
		}

		//merge with defaults
		$args = array_merge($defaultArgs, $args);
		
		//verify the key
		$res = $DB->prepare("SELECT * FROM `reserved_emails` WHERE `email` = :email AND `application` = :app LIMIT 1;");
		$res->bindParam(':email', $args['email'], PDO::PARAM_STR);
		$res->bindParam(':app', $args['application'], PDO::PARAM_STR);
		$res->execute();
		
		//check if we have a match
		if ($res->rowCount() == 0)
		{
			//the email address is not reserved
			return false;
		}
		else
		{	
			//fetch the found record
			$row = $res->fetch();
			//free up some memory
			unset($res);
			
			//check expiration
			if ($row['expire'] == self::EXPIRE_NEVER)
			{
				//the email is reserved and it has no expiration time
				return true;
			}
			else
			{
				//check if it's expired
				//Convert to Time Object
				$timeObj = $CORE->getTime(true, $row['time']);
				$timeObj->add(date_interval_create_from_date_string($row['expire']));
				$expires = $timeObj->format('Y-m-d H:i:s');
				
				//now check if the time now is greater than the expiration
				if ($CORE->getTime() > $expires)
				{ 
					//The reservation has expired, delete it
					$delete = $DB->prepare("DELETE FROM `reserved_emails` WHERE `id` = :id LIMIT 1;");
					$delete->bindParam(':id', $row['id'], PDO::PARAM_INT);
					$delete->execute();
					unset($delete);
					//return email is not reserved
					return false;
				}
				else
				{
					//the reservation is still active
					return true;
				}
			}
		}
		unset($res);
	}
}