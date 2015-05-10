<?php

include_once 'engine/initialize.php';

//Load the most important module
$CORE->load_CoreModule('accounts.finances');

//Setup the finances class
$finance = new AccountFinances();

define('SECRET', ''); // YOUR SECRET KEY
define('CREDIT_TYPE_CHARGEBACK', 2);

//Whitelisted IP addresses
$ipsWhitelist = array(
);

//Get them variables
$userId 	= isset($_GET['uid']) 		? (int)$_GET['uid'] 		: NULL;
$credits	= isset($_GET['currency']) 	? (int)$_GET['currency'] 	: NULL;
$type 		= isset($_GET['type']) 		? (int)$_GET['type'] 		: NULL;
$refId 		= isset($_GET['ref']) 		? $_GET['ref'] 				: NULL;
$signature 	= isset($_GET['sig']) 		? $_GET['sig'] 				: NULL;

//Assume failured
$result = false;

//A little fuction
function calculatePingbackSignature($params, $secret)
{
	$str = '';
	foreach ($params as $k=>$v)
	{
			$str .= "$k=$v";
	}
	$str .= $secret;
	return md5($str);
}

//Check if them variables are set
if (!empty($userId) && !empty($credits) && isset($type) && !empty($refId) && !empty($signature))
{
	//Let's generate the signature
	$signatureParams = array(
			'uid' => $userId,
			'currency' => $credits,
			'type' => $type,
			'ref' => $refId
	);
    $signatureCalculated = calculatePingbackSignature($signatureParams, SECRET);
	
	//check if IP is in whitelist and if signature matches
	if (in_array($_SERVER['REMOTE_ADDR'], $ipsWhitelist) && ($signature == $signatureCalculated))
	{
		//Success
    	$result = true;
        
		//Log this transaction
		$CORE->load_CoreModule('transaction.logging');
		//Setup the log class
		$Logs = new TransactionLogging();
		//Save the variables
		$Logs->SetVariables($_GET);
		
		//Set the account id
		$finance->SetAccount($userId);
		//Set the currency to gold
		$finance->SetCurrency(CURRENCY_GOLD);
		//Check if it's deduction, Paymentwall send amount value with "-"
		if ($type == CREDIT_TYPE_CHARGEBACK)
		{
			//remove the minus
			$credits = (int)trim($credits, '-');
		}
		//Set the amount we are Giving/Taking
		$finance->SetAmount($credits);
		
		if ($type == CREDIT_TYPE_CHARGEBACK)
		{           
			// Deduct credits from user
			// This is optional, but we recommend this type of crediting to be implemented as well
			// Note that currency amount sent for chargeback is negative, e.g. -5, so be caferul about the sign
			// Don’t deduct negative number, otherwise user will get credits instead of losing them
			
			//Resolve the deduction reason by id
			switch ($_GET['reason'])
			{
				case 1:
					$reason 	= 'Chargeback';
					$reasonUser = 'Payment chargeback';
					break;
				case 2:
					$reason 	= 'Credit Card fraud Ban user';
					$reasonUser = 'Credit Card fraud';
					break;
				case 3:
					$reason 	= 'Order fraud Ban user';
					$reasonUser = 'Order fraud';
					break;
				case 4:
					$reason 	= 'Bad data entry';
					$reasonUser = 'Bad data entry';
					break;
				case 5:
					$reason 	= 'Fake / proxy user';
					$reasonUser = 'Fake / proxy user';
					break;
				case 6:
					$reason 	= 'Rejected by advertiser';
					$reasonUser = 'Rejected by advertiser';
					break;
				case 7:
					$reason 	= 'Duplicate conversions';
					$reasonUser = 'Duplicate conversions';
					break;
				case 8:
					$reason 	= 'Goodwill credit taken back';
					$reasonUser = 'Goodwill credit taken back';
					break;
				case 9:
					$reason 	= 'Cancelled order';
					$reasonUser = 'Cancelled order';
					break;
				case 10:
					$reason 	= 'Partially reversed transaction';
					$reasonUser = 'Partially reversed transaction';
					break;
				default:
					$reason 	= 'Unknown code ' . (int)$_GET['reason'];
					$reasonUser = 'Uuknown reason';
					break;
			}
			//append message to the log
			$Logs->append("The transaction is deduction type, reason: \"".$reason."\". ");
			
			//Take the coins from the user
			$Deduct = $finance->Charge('Deduction reason: ' . $reasonUser . '.', CA_SOURCE_TYPE_DEDUCTION);
			
			//Check if the deduction was successfull
			if ($Deduct === true)
			{
				//Deduction success
				$Logs->SetLogType(TRANSACTION_LOG_TYPE_NORMAL);
				//append message to the log
				$Logs->append("The deduction was successfull.");
			}
			else
			{
				//Deduction failed
				$Logs->SetLogType(TRANSACTION_LOG_TYPE_URGENT);
				//append message to the log
				$Logs->append("The deduction of coins failed, error returned: ".$Deduct.". ");
			}
			
			unset($reason, $reasonUser, $Deduct);
       	}
      	else
	   	{
			// Give credits to user

			//resolve the transaction type
			switch ($type)
			{
				case 0:
					$TransactionType = 'Credit is given.';
					$CA_SourceType 	 = CA_SOURCE_TYPE_PURCHASE;
					$CA_SourceString = 'Purchased Gold Coins';
					break;
				case 1:
					$TransactionType = 'Credit is given as a customer service.';
					$CA_SourceType 	 = CA_SOURCE_TYPE_REWARD;
					$CA_SourceString = 'Earned Gold Coins';
					break;
				default:
					$TransactionType = 'Uknown type ' . $type;
					$CA_SourceType 	 = CA_SOURCE_TYPE_NONE;
					$CA_SourceString = 'Received gold coins from unknown source';
					break;
			}
			//append message to the log
			$Logs->append("The transaction is reward type, type: \"".$TransactionType."\". ");
			
			//Give coins to the user
			$Reward = $finance->Reward($CA_SourceString, $CA_SourceType);
			
			//check if the reward was successful
			if ($Reward)
			{
				//Reward success
				$Logs->SetLogType(TRANSACTION_LOG_TYPE_NORMAL);
				//append message to the log
				$Logs->append("The rewarding was successfull. ");
			}
			else
			{
				//Reward failed
				$Logs->SetLogType(TRANSACTION_LOG_TYPE_URGENT);
				//append message to the log
				$Logs->append("The rewarding with coins failed, error returned: ".$Reward.". ");
			}
			
			unset($TransactionType, $CA_SourceType, $CA_SourceString, $Reward);
        }
		unset($finance);
		
		//save the log
		$Logs->save();
	}
}

//The request was OK
if ($result)
{
	echo 'OK';
	exit;
}
else
{
	header('HTTP/1.0 404 not found');
	exit;
}
