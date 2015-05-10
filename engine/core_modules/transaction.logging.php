<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class TransactionLogging
{
	private $userId 	= NULL;
	private $credits	= NULL;
	private $type 		= NULL;
	private $refId 		= NULL;
	private $query		= NULL;
	private $text		= '';
	private $logType 	= TRANSACTION_LOG_TYPE_NONE;
	
	public function __construct()
	{
	}
	
	public function SetVariables($array)
	{
		//Get the important variables
		$this->userId 		= isset($array['uid']) 		? (int)$array['uid'] 	 	: '';
		$this->credits 		= isset($array['currency']) ? (int)$array['currency'] 	: '';
		$this->type 		= isset($array['type']) 	? (int)$array['type'] 	 	: '';
		$this->refId 		= isset($array['ref']) 		? $array['ref'] 			: '';
		
		//Save the requrest query
		$query = '';
		foreach ($array as $key => $value)
		{
			$query .= $key . ':' . $value . ',';
		}
		//remove the last ,
		rtrim($query, ',');
		
		$this->query = $query;
		
		unset($query, $key, $value, $array);
	}
	
	public function SetLogType($type)
	{
		$this->logType = $type;
	}
	
	public function append($text)
	{
		$this->text .= $text;
	}
	
	public function save()
	{
	  	global $CORE, $DB;
		
		$insert = $DB->prepare("INSERT INTO `paymentwall_logs` (`account`, `TransactionAmount`, `TransactionType`, `TransactionRefId`, `TransactionQuery`, `text`, `type`) VALUES (:account, :tAmount, :tType, :tRefId, :tQuery, :text, :type);");
		$insert->bindParam(':account', $this->userId, PDO::PARAM_INT);
		$insert->bindParam(':tAmount', $this->credits, PDO::PARAM_INT);
		$insert->bindParam(':tType', $this->type, PDO::PARAM_INT);
		$insert->bindParam(':tRefId', $this->refId, PDO::PARAM_STR);
		$insert->bindParam(':tQuery', $this->query, PDO::PARAM_STR);
		$insert->bindParam(':text', $this->text, PDO::PARAM_STR);
		$insert->bindParam(':type', $this->logType, PDO::PARAM_INT);
		$insert->execute();
		unset($insert);
		
		return true;
	}
	
	public function __destruct()
	{
		unset($this->userId, $this->credits, $this->type, $this->refId, $this->query, $this->text);
	}
}

class TransactionLogging_Paypal
{
	private $username   	= NULL;
	private $product_id 	= NULL;
	private $txn_id			= NULL;
	private $txn_type		= NULL;
	private $amount 		= NULL;
	private $payer_email 	= NULL;
	private $receiver_email	= NULL;
	private $status 		= NULL;
	private $query			= NULL;
	private $text			= '';
	private $logType 		= TRANSACTION_LOG_TYPE_NONE;
	
	public function __construct()
	{
	}
	
	public function SetVariables($array)
	{
		//Get the important variables
		$this->username   		= isset($array['custom'])			? $array['custom']			: '';
		$this->product_id 		= isset($array['item_number'])		? $array['item_number']		: '';
		$this->txn_id			= isset($array['txn_id'])			? $array['txn_id']			: '';
		$this->txn_type			= isset($array['txn_type'])			? $array['txn_type']		: '';
		$this->amount 			= isset($array['mc_gross'])			? $array['mc_gross']		: '';
		$this->payer_email 		= isset($array['payer_email'])		? $array['payer_email']		: '';
		$this->receiver_email	= isset($array['receiver_email'])	? $array['receiver_email']	: '';
		$this->status 			= isset($array['payment_status'])	? $array['payment_status']	: '';

		unset($array);
	}
	
	public function SetQuery($str)
	{
		$this->query = $str;
	}
	
	public function SetLogType($type)
	{
		$this->logType = $type;
	}
	
	public function append($text)
	{
		$this->text .= $text;
	}
	
	public function save()
	{
	  	global $CORE, $DB;
		
		//Get the time
		$time = $CORE->getTime();
		
		//Insert the record
		$insert = $DB->prepare("INSERT INTO `paypal_logs` 	(`account`, `txn_id`, 	`txn_type`, `amount`, 	`payer_email`, 	`receiver_email`, 	`time`, `paypal_status`, `query_string`, `type`, `text`) 
								VALUES 						(:login, 	:txn, 		:txn_type, 	:amount, 	:payer, 		:receiver,			:time, 	:status, 		 :query, 		 :type,	 :text);");
		$insert->bindParam(':login', $this->username, PDO::PARAM_STR);
		$insert->bindParam(':txn', $this->txn_id, PDO::PARAM_STR);
		$insert->bindParam(':txn_type', $this->txn_type, PDO::PARAM_STR);
		$insert->bindParam(':amount', $this->amount, PDO::PARAM_STR);
		$insert->bindParam(':payer', $this->payer_email, PDO::PARAM_STR);
		$insert->bindParam(':receiver', $this->receiver_email, PDO::PARAM_STR);
		$insert->bindParam(':time', $time, PDO::PARAM_STR);
		$insert->bindParam(':status', $this->status, PDO::PARAM_STR);
		$insert->bindParam(':query', $this->query, PDO::PARAM_STR);
		$insert->bindParam(':text', $this->text, PDO::PARAM_STR);
		$insert->bindParam(':type', $this->logType, PDO::PARAM_INT);
		$insert->execute();
	
		return true;
	}
	
	public function ResolvePending($status)
	{
		switch($status)
		{
			case 'address':
				return 'The payment is pending because your customer did not include a confirmedshipping address and your Payment Receiving Preferences is set yoallow you to manually accept or deny each of these payments. To changeyour preference, go to the Preferences sectionof your Profile.';
			case 'authorization':
				return 'You set the payment action to Authorization and have not yet capturedfunds.';
			case 'echeck':
				return 'The paymentis pending because it was made by an eCheck that has not yet cleared.';
			case 'intl':
				return 'The payment is pending because you hold a non-U.S. account and donot have a withdrawal mechanism. You must manually accept or deny thispayment from your AccountOverview.';
			case 'multi_currency':
				return 'You do not have a balance in the currency sent, and you do not have your Payment ReceivingPreferences set to automatically convert and accept this payment. You must manually accept or deny this payment.';		
			case 'order':
				return 'You set the payment action to Order and have not yet captured funds.';
			case 'paymentreview':
				return 'The payment is pending while it is being reviewed by PayPal for risk.';
			case 'unilateral':
				return 'The payment is pending because it was made to an email address that is not yet registered or confirmed.';
			case 'upgrade':
				return 'The paymentis pending because it was made via credit card and you must upgrade your account to Business or Premier status in order to receive the funds. upgrade can also mean that you have reached the monthly limit for transactionson your account.';
			case 'verify':
				return 'The payment is pending because you are not yet verified. You must verify your account before you can accept this payment.';
			case 'other':
				return 'The paymentis pending for a reason other than those listed above. For moreinformation, contact PayPal Customer Service.';
			default:
				return 'Unknown reason "'.$status.'".';
		}
	}
	
	public function __destruct()
	{
	}
}