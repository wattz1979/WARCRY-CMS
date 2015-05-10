<?php
include_once 'engine/initialize.php';

//Load the most important module
$CORE->load_CoreModule('accounts.finances');
//Log this transaction
$CORE->load_CoreModule('transaction.logging');

//Setup the log class
$Logs = new TransactionLogging_Paypal();

//Setup the finances class
$finance = new AccountFinances();

// STEP 1: Read POST data
// reading posted data from directly from $_POST causes serialization 
// issues with array data in POST
// reading raw POST data from input stream instead. 
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval)
{
  	$keyval = explode ('=', $keyval);
  	if (count($keyval) == 2)
     	$myPost[$keyval[0]] = urldecode($keyval[1]);
}

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
if (function_exists('get_magic_quotes_gpc'))
{
   	$get_magic_quotes_exists = true;
} 

foreach ($myPost as $key => $value)
{        
   	if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1)
	{ 
        $value = urlencode(stripslashes($value)); 
   	}
	else
	{
        $value = urlencode($value);
   	}
   	$req .= "&$key=$value";
}

//Save the variables
$Logs->SetVariables($_POST);
//Seve the query
$Logs->SetQuery($req);

// STEP 2: Post IPN data back to paypal to validate
$ch = curl_init('https://'.$config['payments']['paypal']['url'].'/cgi-bin/webscr');
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

// In wamp like environments that do not come bundled with root authority certificates,
// please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path 
// of the certificate as shown below.
// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
if (!($res = curl_exec($ch)))
{
    // error_log("Got " . curl_error($ch) . " when processing IPN data");
    curl_close($ch);
    exit;
}
curl_close($ch);

// STEP 3: Inspect IPN validation result and act accordingly
if (strcmp($res, "VERIFIED") == 0)
{
	$Username = $_POST['custom'];
	$Amount = (int)$_POST['mc_gross'];
	$Quantity = $_POST['quantity'];
	$txn_id	= $_POST['txn_id'];
	
	/*Check payment status.*/
	if($_POST['payment_status'] != "Completed")
	{
		$Logs->append('[Error] This transaction is not completed but '.$_POST['payment_status'].' Reason: '.$Logs->ResolvePending($_POST['pending_reason']).'.', 'error');
		$Logs->save();
		exit;
	}
	
	/*Prevent txnid recycling.*/ 		
	$res = $DB->prepare("SELECT `txn_id` FROM `paypal_logs` WHERE `txn_id` = :txn AND `paypal_status` = 'Completed';");
	$res->bindParam(':txn', $txn_id, PDO::PARAM_STR);
	$res->execute();
	
	if ($res->rowCount() > 0)
	{
		$Logs->append('[Error] This transaction id is a duplicate.');
		$Logs->save();
		exit;
	}
	unset($res);
	
	/*Verify that the money ware sent to our email*/
	if ($_POST['receiver_email'] != $config['payments']['paypal']['email'])
	{
		$Logs->append('[Error] The payment receiver is not our e-mail address.');
		$Logs->save();
		exit;
	}
	
	/*log successsfull transaction*/
	$Logs->append('[Success] Successful transaction, proceeding to user updates!');
	
	//get the column names for table accounts
	$columns = CORE_COLUMNS::get('accounts');
	
	/*get the account id*/
	$res = $AUTH_DB->prepare("SELECT ".$columns['id']." FROM `".$columns['self']."` WHERE `".$columns['username']."` = :acc LIMIT 1;");
	$res->bindParam(':acc', $Username, PDO::PARAM_STR);
	$res->execute();
			
	if ($res->rowCount() == 0)
	{
		//log invalid account
		$Logs->SetLogType(TRANSACTION_LOG_TYPE_URGENT);
		$Logs->append('[Error] Invalid account, could not resolve the account id by username.');
		$Logs->save();
		exit;
	}
	else
	{
		//fetch
		$row = $res->fetch(PDO::FETCH_ASSOC);
		//save as var
		$accId = (int)$row[$columns['id']];
		//save memory
		unset($row);
		
		//Set the account id
		$finance->SetAccount($accId);
		//Set the currency to gold
		$finance->SetCurrency(CURRENCY_GOLD);
		
		/*select current gold coins*/
		$res2 = $DB->prepare("SELECT `gold` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
		$res2->bindParam(':acc', $accId, PDO::PARAM_INT);
		$res2->execute();

		if ($res2->rowCount() == 0)
		{
			$Logs->SetLogType(TRANSACTION_LOG_TYPE_URGENT);
			$Logs->append('[Error] Invalid account, could not get the gold value by account id.');
			$Logs->save();
			exit;
		}
		else
		{
			//fetch
			$row = $res2->fetch(PDO::FETCH_ASSOC);
			//var
			$gold = $row['gold'];
			//save memory
			unset($row);
			
			//We might have a formula to calculate the Coins by Amount Donated
			$AmountCoins = abs($Amount);
			
			//Set the amount we are Giving/Taking
			$finance->SetAmount($AmountCoins);
			
			//Detect if the payment is a deduction or not
			if ($Amount < 0) //THIS IS A DEDUCTION
			{
				$Logs->append('The transaction is a Deduction Type.');
				
				//Take the coins from the user
				$Deduct = $finance->Charge('Deduction of Gold Coins', CA_SOURCE_TYPE_DEDUCTION);
				
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
				unset($Deduct);
			}
			else //THIS IS A REWARD
			{
				$Logs->append('The transaction is a Reward Type.');
				
				/*add points, but compare money donated with itemcout*/
				if ($AmountCoins != $Quantity)
				{
					$Logs->SetLogType(TRANSACTION_LOG_TYPE_URGENT);
					$Logs->append('[Error] Amount of Coins calculated ('.$AmountCoins.') are not equal to the number of items ('.$Quantity.').');
					$Logs->save();
					exit;
				}
				else
				{
					//Give coins to the user
					$Reward = $finance->Reward('Purchased Gold Coins', CA_SOURCE_TYPE_PURCHASE);
					
					//check if it was updated
					if ($Reward === true)
					{		
						$Logs->append('[Success] The gold was successfully added, value: '.$gold.' was updated to: '.($gold + $AmountCoins).'.');
						$Logs->save();
					}
					else
					{
						$Logs->SetLogType(TRANSACTION_LOG_TYPE_URGENT);
						$Logs->append('[Error] Failed to apply new gold value, value: '.$gold.' was going to be updated to: '.($gold + $AmountCoins).'. Return: ' . $Reward);
						$Logs->save();
						exit;
					}
					unset($Reward);
				}
			}
		}
		unset($res2);
	}
	unset($res, $columns);
	
    // check whether the payment_status is Completed 			- CHECK
    // check that txn_id has not been previously processed 		- CHECK
    // check that receiver_email is your Primary PayPal email	- CHECK
    // check that payment_amount/payment_currency are correct	- CHECK
    // process payment
}
else if (strcmp($res, "INVALID") == 0)
{
    // log for manual investigation
	$Logs->append('[Error] Paypal did not confirm the query.');
	$Logs->save();
}

unset($finance, $Logs);

?>
