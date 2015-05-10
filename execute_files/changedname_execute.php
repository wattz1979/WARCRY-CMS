<?php
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//load the register module
$CORE->load_CoreModule('accounts.register');
$CORE->load_CoreModule('accounts.finances');
$CORE->load_CoreModule('purchaseLog');

//setup new instance of multiple errors
$ERRORS->NewInstance('changedname');
//bind the onsuccess message
$ERRORS->onSuccess('Your Display Name was successfuly changed.', '/index.php?page=changedname');

//Define the variables
$displayName = isset($_POST['displayName']) ? $_POST['displayName'] : false;
$currency = isset($_POST['currency']) ? (int)$_POST['currency'] : false;

//Define the cost of display name change
$PurchaseCost_Silver = 100;
$PurchaseCost_Gold = 10;

//Setup the finances class
$finance = new AccountFinances();
//prepare the log
$logs = new purchaseLog();

######################################
############### CHECKs ###############
	if (!$displayName)
	{
		//no new password
		$ERRORS->Add('Please enter your new Display Name.');
	}
	else if ($displaynameError = AccountsRegister::checkDisplayname($displayName))
	{
		$ERRORS->Add($displaynameError);
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
	
//Check for errors
$ERRORS->Check('/index.php?page=changedname');

######################################
######### CHECK FINANCES #############
$finance->SetCurrency($currency);
$finance->SetAmount(($currency == CURRENCY_GOLD ? $PurchaseCost_Gold : $PurchaseCost_Silver));

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
$ERRORS->Check('/index.php?page=changedname');

############################################################
######## UPDATE THE ACCOUNT"S DISPLAY NAME #################
	
	//start logging
	$logs->add('CHANGE_DNAME', 'Starting log session for the Change Display Name service. Using currency: '.$currency.'.', 'pending');
	
	//Apply the new display name to the account
	$update = $DB->prepare("UPDATE `account_data` SET `displayName` = :name WHERE `id` = :acc LIMIT 1;");
	$update->bindParam(':name', $displayName, PDO::PARAM_STR);
	$update->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
	$update->execute();
		
	//check if the account was affected
	if ($update->rowCount() > 0)
	{
		//update the log
		$logs->update(false, 'The user\'s display name has been successfully changed.', 'pending');
			
		//charge for the purchase
		$Charge = $finance->Charge("Display name change", CA_SOURCE_TYPE_PURCHASE);
		
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
		$ERRORS->Add('The website failed to change your Display Name. Please contact the administration.');
		//log
		$logs->update(false, 'The website failed to update the user\'s display name.', 'error');
	}

$ERRORS->Check('/index.php?page=changedname');

exit;