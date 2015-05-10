<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//load the coin activity handler
$CORE->load_CoreModule('coin.activity');
//load the characters handling class
$CORE->load_ServerModule('character');
//load the log class
$CORE->load_CoreModule('purchaseLog');
//load the sendmail class
$CORE->load_ServerModule('commands');
$CORE->load_CoreModule('accounts.finances');

//prepare the sendmail class						
$command = new server_Commands();

//prepare the log
$logs = new purchaseLog();

//levels data
$LevelsData = new LevelsData();

//Setup the finances class
$finance = new AccountFinances();

//prepare multi errors
$ERRORS->NewInstance('pStore_gold');
//bind the onsuccess message
$ERRORS->onSuccess('Your purchase has been successfully delivered.', '/index.php?page=purchase_gold');

$character = (isset($_POST['character']) ? $_POST['character'] : false);
$GoldAmount = (isset($_POST['amount']) ? (int)$_POST['amount'] : false);

//assume the realm is 1 (for now)
$RealmId = $CURUSER->GetRealm();

if (!$character)
{
	$ERRORS->Add("Please select a character first.");
}
if (!$GoldAmount)
{
	$ERRORS->Add("Please enter the amount of gold you would like to purchase.");
}
else
{
	//Verify the gold amount
	if ($GoldAmount < 1000)
	{
		$GoldAmount = 1000;
	}
	//check for the limit
	if ($GoldAmount > 100000)
	{
		$ERRORS->Add("The maximum that you can purchase is 100,000 gold.");
	}
}

$ERRORS->Check('/index.php?page=purchase_gold');

//Calculate the cost
//get the left overs
$leftOver = $GoldAmount % 1000;

//any left over costs +1 gold coin
if ($leftOver > 0)
{
	$GoldAmount -= $leftOver;
	$GoldAmount += 1000;
}

//calculate the price
$price = $GoldAmount / 1000;

######################################
######### CHECK FINANCES #############
$finance->SetCurrency(CURRENCY_GOLD);
$finance->SetAmount($price);

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

$ERRORS->Check('/index.php?page=purchase_gold');

####################################################################
## The actual script begins here

	//construct the characters handler
	$chars = new server_Character();

	//start logging
	$logs->add('PSTORE_GOLD', 'Starting log session. Using currency: Gold Coins, Amount of Purchase: '.$GoldAmount.', selected realm: '.$RealmId.'.', 'pending');
	
	//set the realm
	if ($chars->setRealm($RealmId))
	{
		//check if the character belongs to this account
		if ($chars->isMyCharacter(false, $character))
		{
				//send the gold
				$sentGold = $command->sendMoney($character, ($GoldAmount * 10000), 'In-Game Gold Delivery', $RealmId);

				//check if any of the actions have failed and log it
				if ($sentGold !== true)
				{
					$logs->update(false, 'The website failed to execute the send money command and returned: '.$sentGold.'.', 'error');
					$ERRORS->Add("The website failed to deliver your purchase. Please contact the administration.");
				}
				else //check if one of those actions was successful
				{
					//charge for the purchase
					$Charge = $finance->Charge("In-Game Gold", CA_SOURCE_TYPE_PURCHASE);
					
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
				}
				
				//check for fatal errors before proceeding to the complete page
				$ERRORS->Check('/index.php?page=purchase_gold');
				
				//redirect				
				$ERRORS->triggerSuccess();
		}
		else
		{
			$ERRORS->Add("The selected character does not belong to this account.");
			//log
			$logs->update(false, 'The user is trying to purchase gold for character from another account.', 'error');
		}
	}
	else
	{
		$ERRORS->Add("The website failed to load realm database. Please contact the administration for more information.");
		//log
		$logs->update(false, 'The website failed to load realm database.', 'error');
	}
	unset($logs);
	unset($chars);
	
####################################################################

$ERRORS->Check('/index.php?page=purchase_gold');

exit;