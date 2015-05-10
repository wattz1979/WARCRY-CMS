<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class CoinActivity
{
	private $Account = false;
	private $SourceType = CA_SOURCE_TYPE_NONE;
	private $SourceString = '';
	private $CoinsType = CA_COIN_TYPE_SILVER;
	private $ExchangeType = CA_EXCHANGE_TYPE_PLUS;
	private $Amount;
	
	public function __construct($acc = false)
	{
		$this->Account = $acc;
	}
	
	/*
	  Setting the source type
	  - None
	  - Purchase
	  - Reward
	*/
	public function set_SourceType($type)
	{
		$this->SourceType = $type;
	}
	
	/*
	  Setting the source as string
	  - example: via Paypal
	*/
	public function set_SourceString($str)
	{
		$this->SourceString = $str;
	}
	
	/*
	  Setting the coins type
	  - Silver
	  - Gold
	*/
	public function set_CoinsType($type)
	{
		$this->CoinsType = $type;
	}
	
	/*
	  Setting the exchange type
	  - Plus
	  - Minus
	*/
	public function set_ExchangeType($type)
	{
		$this->ExchangeType = $type;
	}
	
	/*
	  Setting the amount (int value)
	  - example: 200
	*/
	public function set_Amount($amount)
	{
		$this->Amount = $amount;
	}
	
	public function execute()
	{
		global $DB, $CURUSER, $CORE;
		
		//check if we have set account ID
		if (!$this->Account)
		{
			//get the CURUSER acc id
			$accId = $CURUSER->get('id');
		}
		else
		{
			$accId = $this->Account;
		}
		//get the time
		$time = $CORE->getTime();
		
		$insert = $DB->prepare("INSERT INTO `coin_activity` (`account`, `source`, `sourceType`, `coinsType`, `exchangeType`, `amount`, `time`) VALUES (:acc, :source, :sourceType, :coinsType, :exchangeType, :amount, :time);");
		$insert->bindParam(':acc', $accId, PDO::PARAM_INT);
		$insert->bindParam(':source', $this->SourceString, PDO::PARAM_STR);
		$insert->bindParam(':sourceType', $this->SourceType, PDO::PARAM_INT);
		$insert->bindParam(':coinsType', $this->CoinsType, PDO::PARAM_INT);
		$insert->bindParam(':exchangeType', $this->ExchangeType, PDO::PARAM_INT);
		$insert->bindParam(':amount', $this->Amount, PDO::PARAM_INT);
		$insert->bindParam(':time', $time, PDO::PARAM_STR);
		$insert->execute();
		
		if ($insert->rowCount() > 0)
		{
			//success
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function __destrruct()
	{
	}
}