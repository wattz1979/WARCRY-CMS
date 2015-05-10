<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class AccountFinances
{	
	private $account;
	private $currency = false;
	private $amounts = array();
	
	//translate the currencies for the database
	private $CurrencyTranslations = array(
		CURRENCY_SILVER => 'silver',
		CURRENCY_GOLD 	=> 'gold'
	);
	
	public function __construct()
	{
		global $CURUSER;
		
		$this->account = $CURUSER->get('id');
	}
	
	public function SetAccount($id)
	{
		if (is_int($id))
		{
			$this->account = $id;
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function IsValidCurrency($currency)
	{
		$Valid = array(CURRENCY_SILVER, CURRENCY_GOLD);
		
		if (in_array($currency, $Valid))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function SetCurrency($currency)
	{
		if ($this->IsValidCurrency($currency))
		{
			$this->currency = $currency;
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function SetAmount($amount)
	{
		//check if we have currency set
		if ($this->currency)
		{
			//check for digits only
			if (is_int($amount))
			{
				//check if we have the currency already set in the amounts storage
				if (isset($this->amounts[$this->currency]))
				{
					//add to the already existant amount
					$this->amounts[$this->currency] = $this->amounts[$this->currency] + $amount;
				}
				else
				{
					$this->amounts[$this->currency] = $amount;
				}
				
				return true;
			}
			else
			{
				return false;
			}
		}
		
		return false;
	}
	
	/* 
	This function will return the following:
		- false on success 
		- string on technical error
		- array on insufficient amount (contains the keys of the insufficient currencies)
	*/
	public function CheckBalance()
	{
		global $CURUSER, $DB;
		
		//let's start by getting the account's balance
		$balance = array();
		$MustPullBalance = false;
		
		//First of all check if we have an user online
		if ($CURUSER->isOnline())
		{
			//check if the selected account is the curuser
			if ($this->account == $CURUSER->get('id'))
			{
				foreach ($this->CurrencyTranslations as $id => $key)
				{
					$balance[$id] = $CURUSER->get($key);
				}
				unset($id, $key);
			}
			else
			{
				$MustPullBalance = true;
			}
		}
		else
		{
			$MustPullBalance = true;
		}
		
		//define the available currencies
		$AvailableCurrencies = array();
		foreach ($this->CurrencyTranslations as $id => $key)
		{
			array_push($AvailableCurrencies, '`' . $key . '`');
		}
		unset($id, $key);
		
		//check if the user is remote and we must pull the balance from the DB
		if ($MustPullBalance)
		{
			$res = $DB->prepare("SELECT ".implode(',', $AvailableCurrencies)." FROM `account_data` WHERE `id` = :account LIMIT 1;");
			$res->bindParam(':account', $this->account, PDO::PARAM_INT);
			$res->execute();
			//check if the balance was pulled
			if ($res->rowCount() > 0)
			{
				$row = $res->fetch();
				foreach ($this->CurrencyTranslations as $id => $key)
				{
					$balance[$id] = $row[$key];
				}
				unset($row, $id, $key);
			}
			else
			{
				//we have failed to pull the remote account balance
				return 'Error, unable to get the account\'s balance';
			}
			unset($res);
		}
		unset($MustPullBalance);
		
		//We must have the account balance by now, let's proceed by checking if the balance meets our needs
		//first check if the amounts array has any amount in it
		if (count($this->amounts) > 0)
		{
			$insufficient = array();
			//loop trough the amounts we need checked
			foreach ($this->amounts as $id => $amount)
			{
				//check if the balance is lower the amount
				if ($balance[$id] < $amount)
				{
					//mark this currency as insufficient
					array_push($insufficient, $this->CurrencyTranslations[$id]);
				}
			}
			unset($id, $amount);
			
			//check if we need to return the insufficients
			if (count($insufficient) > 0)
			{
				return $insufficient;
			}
		}
		else
		{
			return 'Error, there is no amount to compare with...';
		}
		
		unset($AvailableCurrencies, $balance, $insufficient);
		
		return false;
	}

	/* 
	This function will return the following:
		- bool true on success
		- string on technical error
	*/
	
	public function Charge($CA_SourceString, $CA_SourceType = CA_SOURCE_TYPE_NONE)
	{
		global $CURUSER, $DB, $CORE;
		
		//check if we have amouts to charge
		if (count($this->amounts) == 0)
		{
			return 'Error, we have no amounts to charge...';
		}
		
		//check if the coins activity modules is loaded
		if (!$CORE->isLoaded_CoreModule('coin.activity'))
		{
			$CORE->load_CoreModule('coin.activity');
		}
		
		//we have to construct the query
		$updateset = array();
		//loop trough the amounts we need to update
		foreach ($this->amounts as $id => $amount)
		{
			$updateset[] = "`".$this->CurrencyTranslations[$id]."` = ".$this->CurrencyTranslations[$id]." - ".$amount;
		}
		
		//update the account money
		$update = $DB->prepare("UPDATE `account_data` SET ".implode(',', $updateset)." WHERE `id` = :account LIMIT 1;");
		$update->bindParam(':account', $this->account, PDO::PARAM_INT);	
		$update->execute();
		
		//assume that the query failed
		$Return = 'The website failed to execute the amount update query.';
		
		if ($update->rowCount() > 0)
		{
			//Log for each currency
			foreach ($this->amounts as $id => $amount)
			{
				//resolve the coins type
				switch ($id)
				{
					case CURRENCY_SILVER:
						$CoinsType = CA_COIN_TYPE_SILVER;
						break;
					case CURRENCY_GOLD:
						$CoinsType = CA_COIN_TYPE_GOLD;
						break;
					default:
						$CoinsType = CA_COIN_TYPE_SILVER;
						break;
				}
				
				//log into coin activity
				$ca = new CoinActivity($this->account);
				$ca->set_SourceType($CA_SourceType);
				$ca->set_SourceString($CA_SourceString);
				$ca->set_CoinsType($CoinsType);
				$ca->set_ExchangeType(CA_EXCHANGE_TYPE_MINUS);
				$ca->set_Amount($amount);
				$ca->execute();
				unset($ca);
				
				$Return = true;
			}
			unset($id, $amount, $CoinsType);
		}
		unset($update, $updateset);
		
		return $Return;
	}
	
	/* 
	This function will return the following:
		- bool true on success
		- string on technical error
	*/
	
	public function Reward($CA_SourceString, $CA_SourceType = CA_SOURCE_TYPE_NONE)
	{
		global $CURUSER, $DB, $CORE;
		
		//check if we have amouts to charge
		if (count($this->amounts) == 0)
		{
			return 'Error, we have no amounts to reward...';
		}
		
		//check if the coins activity modules is loaded
		if (!$CORE->isLoaded_CoreModule('coin.activity'))
		{
			$CORE->load_CoreModule('coin.activity');
		}
		
		//we have to construct the query
		$updateset = array();
		//loop trough the amounts we need to update
		foreach ($this->amounts as $id => $amount)
		{
			$updateset[] = "`".$this->CurrencyTranslations[$id]."` = ".$this->CurrencyTranslations[$id]." + ".$amount;
		}
		
		//update the account money
		$update = $DB->prepare("UPDATE `account_data` SET ".implode(',', $updateset)." WHERE `id` = :account LIMIT 1;");
		$update->bindParam(':account', $this->account, PDO::PARAM_INT);	
		$update->execute();
		
		//assume that the query failed
		$Return = 'The website failed to execute the amount update query.';
		
		if ($update->rowCount() > 0)
		{
			//Log for each currency
			foreach ($this->amounts as $id => $amount)
			{
				//resolve the coins type
				switch ($id)
				{
					case CURRENCY_SILVER:
						$CoinsType = CA_COIN_TYPE_SILVER;
						break;
					case CURRENCY_GOLD:
						$CoinsType = CA_COIN_TYPE_GOLD;
						break;
					default:
						$CoinsType = CA_COIN_TYPE_SILVER;
						break;
				}
				
				//log into coin activity
				$ca = new CoinActivity($this->account);
				$ca->set_SourceType($CA_SourceType);
				$ca->set_SourceString($CA_SourceString);
				$ca->set_CoinsType($CoinsType);
				$ca->set_ExchangeType(CA_EXCHANGE_TYPE_PLUS);
				$ca->set_Amount($amount);
				$ca->execute();
				unset($ca);
				
				$Return = true;
			}
			unset($id, $amount, $CoinsType);
		}
		unset($update, $updateset);
		
		return $Return;
	}
	
	public function __destrruct()
	{
		unset($this->account, $this->currency, $this->amounts, $this->CurrencyTranslations);
	}
}