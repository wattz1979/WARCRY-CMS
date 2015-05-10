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
//Setup the finances class
$finance = new AccountFinances();

//prepare the log
$logs = new purchaseLog();

//prepare multi errors
$ERRORS->NewInstance('pStore_faction');
//bind the onsuccess message
$ERRORS->onSuccess('Successfull character faction change.', '/index.php?page=factionchange');

$character = (isset($_POST['character']) ? $_POST['character'] : false);

$RealmId = $CURUSER->GetRealm();

//define how much a faction change is going to cost
$factionChangePrice = 5;

if (!$character)
{
	$ERRORS->Add("Please select a character first.");
}

//Set the currency and price
$finance->SetCurrency(CURRENCY_GOLD);
$finance->SetAmount($factionChangePrice);

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

$ERRORS->Check('/index.php?page=factionchange');

####################################################################
## The actual script begins here

	//construct the characters handler
	$chars = new server_Character();

	//start logging
	$logs->add('PSTORE_FACTION', 'Starting log session, initial value of the required currency: '.$accountGold.' Gold, selected realm: '.$RealmId.'.', 'pending');
	
	//set the realm
	if ($chars->setRealm($RealmId))
	{
		//check if the character belongs to this account
		if ($chars->isMyCharacter(false, $character))
		{
			//level the character
			$FactionChange = $command->FactionChange($character, $RealmId);
			
			//check if the command was successfull
			if ($FactionChange === true)
			{
				//charge for the purchase
				$Charge = $finance->Charge("Faction Change", CA_SOURCE_TYPE_NONE);
				
				if ($Charge === true)
				{
					//update the log
					$logs->update(false, 'The faction change command has been executed and the user has been successfully charged for his purchase.', 'ok');
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
			else
			{
				$ERRORS->Add("The website failed to complete your order. Please contact the administration.");
				//update the log
				$logs->update(false, 'Soap failed to execute the faction change command.', 'error');
			}
				
			//check for fatal errors before proceeding to the complete page
			$ERRORS->Check('/index.php?page=factionchange');
			
			//redirect				
			$ERRORS->triggerSuccess();
		}
		else
		{
			$ERRORS->Add("The selected character does not belong to this account.");
			//log
			$logs->update(false, 'The user is trying to purchase faction change for another account.', 'error');
		}
	}
	else
	{
		$ERRORS->Add("The website failed to load realm database. Please contact the administration for more information.");
		//log
		$logs->update(false, 'The website failed to load realm database.', 'error');
	}
	
	unset($chars);
	
####################################################################

$ERRORS->Check('/index.php?page=factionchange');

exit;