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
$ERRORS->NewInstance('pStore_recustomization');
//bind the onsuccess message
$ERRORS->onSuccess('Successfull character re-customization.', '/index.php?page=recustomization');

$character = (isset($_POST['character']) ? $_POST['character'] : false);

//assume the realm is 1 (for now)
$RealmId = $CURUSER->GetRealm();

//define how much a faction change is going to cost
$recustomizationPrice = 5;

if (!$character)
{
	$ERRORS->Add("Please select a character first.");
}

//Set the currency and price
$finance->SetCurrency(CURRENCY_GOLD);
$finance->SetAmount($recustomizationPrice);

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
$ERRORS->Check('/index.php?page=recustomization');

####################################################################
## The actual script begins here

	//construct the characters handler
	$chars = new server_Character();

	//start logging
	$logs->add('PSTORE_CUSTOMIZE', 'Starting log session, initial value of the required currency: '.$accountGold.' Gold, selected realm: '.$RealmId.'.', 'pending');
	
	//set the realm
	if ($chars->setRealm($RealmId))
	{
		//check if the character belongs to this account
		if ($chars->isMyCharacter(false, $character))
		{
			//recustomize the character
			$recustomization = $command->Customize($character, $RealmId);
			
			//check if the command was successfull
			if ($recustomization === true)
			{
				//charge for the purchase
				$Charge = $finance->Charge("Character Recustomization", CA_SOURCE_TYPE_NONE);
				
				if ($Charge === true)
				{
					//update the log
					$logs->update(false, 'The recustomization command has been executed and the user has been successfully charged for his purchase.', 'ok');
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
				$logs->update(false, 'Soap failed to execute the recustomization command.', 'error');
			}
				
			//check for fatal errors before proceeding to the complete page
			$ERRORS->Check('/index.php?page=recustomization');
			
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

$ERRORS->Check('/index.php?page=recustomization');

exit;