<?php
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//load the register module
$CORE->load_CoreModule('accounts.finances');
$CORE->load_CoreModule('purchaseLog');

//setup new instance of multiple errors
$ERRORS->NewInstance('purchase_boost');
//bind the onsuccess message
$ERRORS->onSuccess('Your Boosts have been successfuly applied, please re-log.', '/index.php?page=boosts');

//Define the variables
$RealmId = $CURUSER->GetRealm();
$BoostId = isset($_POST['boost']) ? (int)$_POST['boost'] : false;
$currency = isset($_POST['currency']) ? (int)$_POST['currency'] : false;
$DurationId = isset($_POST['duration']) ? (int)$_POST['duration'] : false;

//Setup the finances class
$finance = new AccountFinances();
//prepare the log
$logs = new purchaseLog();
//The boosts storage
$BoostsStorage = new BoostsData();

######################################
############### CHECKs ###############
	if (!$BoostId)
	{
		//no boost selected
		$ERRORS->Add('Please select boost first.');
	}
	else if (!($BoostDetails = $BoostsStorage->get($BoostId)))
	{
		//Verify the boost id
		$ERRORS->Add('The selected boost is invalid.');
	}
	
	if (!$currency)
	{
		//no currency is selected
		$ERRORS->Add('Please select a currency for the purchase.');
	}
	else if (!$finance->IsValidCurrency($currency))
	{
		//invalid currency
		$ERRORS->Add('Error, invalid currency selected.');
	}
	
	if (!$DurationId)
	{
		$ERRORS->Add('Please select boost duration.');
	}
	else if (!in_array($DurationId, array(BOOST_DURATION_10, BOOST_DURATION_15, BOOST_DURATION_30)))
	{
		$ERRORS->Add('The selected boost duration is invalid.');
	}
	
//Check for errors
$ERRORS->Check('/index.php?page=changedname');

######################################
######### CHECK FINANCES #############
$finance->SetCurrency($currency);
$finance->SetAmount($config['BOOSTS']['PRICEING'][$DurationId][$currency]);

//check if the user has enough balance
if ($BalanceError = $finance->CheckBalance())
{
	if (is_array($BalanceError))
	{
		//insufficient amount
		foreach ($BalanceError as $currency)
		{
			$ERRORS->Add("You do not have enough " . ucfirst($currency) . " Coins.");
		}
	}
	else
	{
		//technical error
		$ERRORS->Add('Error, the website failed to verify your account balance.');
	}
}
unset($BalanceError);

//Check for errors
$ERRORS->Check('/index.php?page=boosts');

############################################################
######## UPDATE THE ACCOUNT"S DISPLAY NAME #################
	
	//Get the time
	$time = $CORE->getTime(true);
	
	//start logging
	$logs->add('BOOSTS', 'Starting log session for the Boost Purchase service. Using currency: '.$currency.' and duration: '.$DurationId.', selected realm: '.$RealmId.'.', 'pending');
	
	//set the realm
	if ($RealmDB = $CORE->RealmDatabaseConnection($RealmId))
	{
		# Check if the boost is already active
		$res = $RealmDB->prepare("SELECT * FROM `player_boosts` WHERE `account_Id` = :acc AND `boosts` = :boost LIMIT 1;");
		$res->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
		$res->bindParam(':boost', $BoostId, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			if ($time->getTimestamp() > (int)$arr['unsetdate'])
			{
				//already expired, remove from the database
				$delete = $RealmDB->prepare("DELETE FROM `player_boosts` WHERE `account_Id` = :acc AND `boosts` = :boost LIMIT 1;");
				$delete->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
				$delete->bindParam(':boost', $BoostId, PDO::PARAM_INT);
				$delete->execute();
			}
			else
			{
				$ERRORS->Add('The selected boost is already active, please wait untill it has expired.');
				//update the log
				$logs->update(false, 'The selected boost is already active.', 'error');
			}
		}
		unset($res);
		
		//Check for errors
		$ERRORS->Check('/index.php?page=boosts');
		
		//Calculate the expire time
		$DurationStrings = array(
			BOOST_DURATION_10 => '10 days',
			BOOST_DURATION_15 => '15 days',
			BOOST_DURATION_30 => '30 days'
		);
		
		$Expires = $time->getTimestamp() + strtotime($DurationStrings[$DurationId], 0);
		
		//Give the boost
		$insert = $RealmDB->prepare("INSERT INTO `player_boosts` (`account_Id`, `boosts`, `setdate`, `unsetdate`, `active`) VALUES (:acc, :boost, :setdate, :expire, '1');");
		$insert->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
		$insert->bindParam(':boost', $BoostId, PDO::PARAM_INT);
		$insert->bindParam(':setdate', $time->getTimestamp(), PDO::PARAM_INT);
		$insert->bindParam(':expire', $Expires, PDO::PARAM_INT);
		$insert->execute();
		
		//check if the account was affected
		if ($insert->rowCount() > 0)
		{
			//update the log
			$logs->update(false, 'The boost has been insert with expire time: '.$Expires.' ['.$DurationStrings[$DurationId].'].', 'pending');
				
			//charge for the purchase
			$Charge = $finance->Charge("Purchased Boost", CA_SOURCE_TYPE_PURCHASE);
			
			if ($Charge === true)
			{
				//update the log
				$logs->update(false, 'The user has been charged for his purchase.', 'ok');
			}
			else
			{
				//update the log
				$logs->update(false, 'The user was not charged for his purchase, website failed to update.', 'error');
			}
			unset($Charge);
			
			//free up some memory
			unset($finance);
			
			######################################
			########## Redirect ##################	
			$ERRORS->triggerSuccess();
		}
		else
		{
			$ERRORS->Add('The website failed to set your boost. Please contact the administration.');
			//log
			$logs->update(false, 'The website failed to insert the boost record.', 'error');
		}
		unset($insert, $DurationStrings);
	}
	else
	{
		$ERRORS->Add("The website failed to connect to the server. Please contact the adminsitration.");
	}
	unset($RealmDB);

$ERRORS->Check('/index.php?page=boosts');

exit;