<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class PromoCode
{
	private $token 		= false;
	private $data  		= false;
	private $errors		= array();
	private $account 	= false;
	private $realm		= 1;
	private $character  = false;
	
	public function __construct($code)
	{
		$this->token = $this->parseCode($code);
	}
	
	public function setAccount($acc)
	{
		$this->account = (int)$acc;
		
		return $this;
	}
	
	public function setRealm($RealmID)
	{
		$this->realm = (int)$RealmID;
	}
	
	public function setCharacter($name)
	{
		$this->character = $name;
	}
	
	public function Verify()
	{
		global $DB, $CURUSER;
		
		if (!$this->token)
		{
			$this->errors[] = 'There is no code to verify.';
			return false;
		}
		
		//Check if we have a CURUSER or Account set
		if (!$this->account && !$CURUSER->isOnline())
		{
			$this->errors[] = 'No account is presented.';
			return false;
		}
		else if (!$this->account)
		{
			//use the curuser acc
			$this->account = $CURUSER->get('id');
		}
			
		//lookup the token
		$res = $DB->prepare("SELECT * FROM `promo_codes` WHERE `token` = :token LIMIT 1;");
		$res->bindParam(':token', $this->token, PDO::PARAM_STR);
		$res->execute();
		
		if ($res->rowCount() == 0)
		{
			$this->errors[] = 'The Code is invalid or does not exist.';
			return false;
		}
		
		//Fetch the code record
		$this->data = $res->fetch();
		
		//Check for Per Account Usage
		if ((int)$this->data['usage'] == PCODE_USAGE_PER_ACC)
		{
			//We must verify that this account has not yet used this code
			$usageRes = $DB->prepare("SELECT * FROM `promo_codes_usage` WHERE `token` = :token AND `account` = :acc LIMIT 1;");
			$usageRes->bindParam(':token', $this->token, PDO::PARAM_STR);
			$usageRes->bindParam(':acc', $this->account, PDO::PARAM_INT);
			$usageRes->execute();
			
			if ($usageRes->rowCount() > 0)
			{
				$this->errors[] = 'The current user has already used this code.';
				return false;
			}
			unset($usageRes);
		}
		
		//Verify the reward type
		$Rewards = array(PCODE_REWARD_CURRENCY_S, PCODE_REWARD_CURRENCY_G, PCODE_REWARD_ITEM);
		
		if (!in_array((int)$this->data['reward_type'], $Rewards))
		{
			$this->errors[] = 'The code seems to have invalid reward type.';
			return false;
		}
		
		//the code is free for use
		return true;
	}
	
	public function ProcessReward()
	{
		global $DB;
		
		//check if we have the data
		if (!$this->data)
		{
			$this->errors[] = 'The Code data is missing, have you verified the code?';
			return false;
		}
		
		//Handle diferrent reward types
		switch ((int)$this->data['reward_type'])
		{
			case PCODE_REWARD_CURRENCY_S:
				$Reward = $this->ProcessCurrencyReward(CURRENCY_SILVER, (int)$this->data['reward_value']);
				break;
			case PCODE_REWARD_CURRENCY_G:
				$Reward = $this->ProcessCurrencyReward(CURRENCY_GOLD, (int)$this->data['reward_value']);
				break;
			case PCODE_REWARD_ITEM:
				$Reward = $this->ProcessItemReward((int)$this->data['reward_value']);
				break;
			default:
				$Reward = false;
				break;
		}
		
		//Check if the reward was processed
		if (!$Reward)
		{
			$this->errors[] = $this->getLastError();
			return false;
		}
		
		//Handle PER ACCOUNT codes save
		if ((int)$this->data['usage'] == PCODE_USAGE_PER_ACC)
		{
			$insert = $DB->prepare("INSERT INTO `promo_codes_usage` (`token`, `account`) VALUES (:token, :acc);");
			$insert->bindParam(':token', $this->token, PDO::PARAM_STR);
			$insert->bindParam(':acc', $this->account, PDO::PARAM_INT);
			$insert->execute();
		}
		
		//Handle single usage code
		if ((int)$this->data['usage'] != PCODE_USAGE_PER_ACC)
		{
			//The code record should be destroyed
			$delete = $DB->prepare("DELETE FROM `promo_codes` WHERE `token` = :token LIMIT 1;");
			$delete->bindParam(':token', $this->token, PDO::PARAM_STR);
			$delete->execute();
		}
		
		return true;
	}
	
	private function ProcessCurrencyReward($currency, $amount)
	{
		global $CORE;
		
		//Verify the currency
		$Currencies = array(CURRENCY_SILVER, CURRENCY_GOLD);
		
		if (!in_array($currency, $Currencies))
		{
			$this->errors[] = 'The code seems to have invalid reward type.';
			return false;
		}
		
		//Load the Finances module if not loaded
		if (!$CORE->isLoaded_CoreModule('accounts.finances'))
			$CORE->load_CoreModule('accounts.finances');
		
		//Setup the finances class
		$finance = new AccountFinances();
		
		//Add the currency to the user
		//Set the account id
		$finance->SetAccount($this->account);
		//Set the currency to gold
		$finance->SetCurrency($currency);
		//Set the amount we are Giving
		$finance->SetAmount($amount);
		
		//Give coins to the user
		$Reward = $finance->Reward('Promotion Code', CA_SOURCE_TYPE_REWARD);
		
		//check if it was updated
		if ($Reward !== true)
		{
			$this->errors[] = 'The website was unable to deliver your reward due to reason: ' . $Reward;
			return false;
		}
		
		unset($finance);
		
		return true;
	}
	
	private function ProcessItemReward($entry)
	{
		global $CORE;
		
		//Make sure we have a selected realm
		//It's set to realm 1 by default but
		//it might be needed later on
		if ($this->realm === false)
		{
			$this->errors[] = 'The realm id is missing.';
			return false;
		}
		
		//Make sure a character is selected
		if (!$this->character)
		{
			$this->errors[] = 'This code requires a character to be selected.';
			return false;
		}
		
		//load the sendmail class
		if (!$CORE->isLoaded_ServerModule('commands'))
			$CORE->load_ServerModule('commands');
		
		//prepare the sendmail class						
		$command = new server_Commands();
		
		//check if the realm is online
		if ($command->CheckConnection($this->realm) !== true)
		{
			$this->errors[] = 'The realm is currently unavailable. Please try again in few minutes.';
			return false;
		}
		
		//load the characters handling class
		if (!$CORE->isLoaded_ServerModule('character'))
			$CORE->load_ServerModule('character');
		
		//construct the characters handler
		$chars = new server_Character();
		
		if (!$chars->setRealm($this->realm))
		{
			$this->errors[] = 'The website failed to load realm database. Please contact the administration for more information.';
			return false;
		}
		
		//check if the character belongs to this account
		if (!$chars->isMyCharacter(false, $this->character, (int)$this->account))
		{
			$this->errors[] = 'The selected character does not belong to this account.';
			return false;
		}
		
		//Send the item
		$sentMail = $command->sendItems($this->character, $entry, 'Promotion Code Reward', $this->realm);
		
		//make sure the mail was sent
		if ($sentMail !== true)
		{
			$this->errors[] = 'The website was unable to deliver your reward due to reason: '.$sentMail;
			return false;
		}
		
		unset($chars, $command);
		
		return true;
	}
	
	public function getInfo()
	{
		return $this->data;
	}
	
	public function getLastError()
	{
		if (count($this->errors) > 0)
		{
			return $this->errors[count($this->errors) - 1];
		}
		
		return false;
	}
	
	private function parseCode($code)
	{
		return str_replace('-', '', $code);
	}
	
	public function __destrruct()
	{
		unset($this->data);
	}
}

class PromoCodeGen
{
	private $token 			= false;
	private $keyAlgorythm	= 'SSSSSSdddddd';
	private $usage			= PCODE_USAGE_ONCE;
	private $format 		= false;
	private $rewardType 	= PCODE_REWARD_CURRENCY_S;
	private $rewardVal  	= 0;
	private $errors			= array();
	
	public function __construct()
	{
	}
	
	public function setUsage($usage)
	{
		$this->usage = $usage;
		
		return $this;
	}
	
	public function setRewardType($type)
	{
		$this->rewardType = $type;
		
		return $this;
	}
	
	public function setRewardValue($value)
	{
		$this->rewardVal = $value;
		
		return $this;
	}
	
	public function format($format)
	{
		$this->format = $format;
		
		return $this;
	}
	
	public function setAlgorythm($str)
	{
		$this->keyAlgorythm = $str;
	}
	
	public function Generate()
	{
		global $DB;
		
		//Generate a token
		$this->token = $this->generateKeyByAlgorythm();
		
		//Verify the uniqueness of the key
		if ($this->Exists())
		{
			//simply generate a new one
			return $this->Generate();
		}
		
		//prepare the format
		$format = ($this->format) ? $this->format : 'NONE';
		
		//save it to the database
		$insert = $DB->prepare("INSERT INTO `promo_codes` (`token`, `usage`, `reward_type`, `reward_value`, `format`) VALUES (:token, :usage, :reward, :value, :format);");
		$insert->bindParam(':token', $this->token, PDO::PARAM_STR);
		$insert->bindParam(':usage', $this->usage, PDO::PARAM_INT);
		$insert->bindParam(':reward', $this->rewardType, PDO::PARAM_INT);
		$insert->bindParam(':value', $this->rewardVal, PDO::PARAM_INT);
		$insert->bindParam(':format', $format, PDO::PARAM_STR);
		$insert->execute();
		
		//Check for errors
		if ($insert->rowCount() == 0)
		{
			$this->errors[] = 'The website failed to register the Promo Code.';
		}
		
		return $this;
	}
	
	/**
	**  Generates a key by defined pattern
	**  - Usable markers (s, d)
	**/
	
	private function generateKeyByAlgorythm()
	{
		//randomize the pattern
		$pattern = str_shuffle($this->keyAlgorythm);
		
		//split into markers
		$markers = str_split($pattern);
		
		$key = '';
		//let's put up our key
		foreach ($markers as $marker)
		{
			switch ($marker)
			{
				case 'd':
					$key .= substr(str_shuffle(str_repeat("0123456789", 1)), 0, 1);
					break;
				case 's':
					$key .= substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 1)), 0, 1);
					break;
				case 'S':
					$key .= strtoupper(substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 1)), 0, 1));
					break;
			}
		}
		
		//save our key
		$this->token = $key;
		
		unset($pattern, $markers, $marker, $key);
		
		return $this->token;
	}
	
	public function Exists($token = false)
	{
		global $DB;
		
		if (!$token)
		{
			$token = $this->token;
		}
		
		//lookup the token
		$res = $DB->prepare("SELECT `id` FROM `promo_codes` WHERE `token` = :token LIMIT 1;");
		$res->bindParam(':token', $token, PDO::PARAM_STR);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			return true;
		}
		unset($res);
		
		return false;
	}
	
	public function get()
	{
		//Check for errors
		if (count($this->errors) > 0)
			return false;
		
		if ($this->format)
		{
			//split into markers
			$markers = str_split($this->format);
			$keyChar = str_split($this->token);
			
			$reduce = 0;
			$key = '';
			//let's put up our key
			foreach ($markers as $index => $marker)
			{
				if (strtolower($marker) == 'x')
				{
					$key .= $keyChar[$index - $reduce];
				}
				else
				{
					$key .= $markers[$index];
					$reduce++;
				}
			}
			unset($markers, $keyChar, $index, $marker, $reduce);
			
			return $key;
		}
		
		return $this->token;
	}
	
	public function getLastError()
	{
		if (count($this->errors) > 0)
		{
			return $this->errors[count($this->errors) - 1];
		}
		
		return false;
	}
	
	public function __destrruct()
	{
	}
}