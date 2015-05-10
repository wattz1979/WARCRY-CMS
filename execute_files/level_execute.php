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
$ERRORS->NewInstance('pStore_levels');
//bind the onsuccess message
$ERRORS->onSuccess('Your purchase has been successfully delivered.', '/index.php?page=levels');

$level = (isset($_POST['levels']) ? (int)$_POST['levels'] : false);
$character = (isset($_POST['character']) ? $_POST['character'] : false);

//assume the realm is 1 (for now)
$RealmId = $CURUSER->GetRealm();

if (!$character)
{
	$ERRORS->Add("Please select a character first.");
}
if (!$level)
{
	$ERRORS->Add("Please select your desired level.");
}
else if (!$LevelsData->get($level))
{
	$ERRORS->Add("There was a problem with your level selection, if the problem persists please contact the administration.");
}

//overright the variable with the actual data
$level = $LevelsData->get($level);
unset($LevelsData);

######################################
######### CHECK FINANCES #############
$finance->SetCurrency($level['priceCurrency']);
$finance->SetAmount($level['price']);

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

$ERRORS->Check('/index.php?page=levels');

####################################################################
## The actual script begins here

	//construct the characters handler
	$chars = new server_Character();

	//start logging
	$logs->add('PSTORE_LEVEL', 'Starting log session. Leveling character: '.$character.' to level '.$level['level'].'. Using currency: '.$level['priceCurrency'].', price value: '.$level['price'].', selected realm: '.$RealmId.'.', 'pending');
	
	//set the realm
	if ($chars->setRealm($RealmId))
	{
		//check if the character belongs to this account
		if ($chars->isMyCharacter(false, $character))
		{
			//get the character name
			$charData = $chars->getCharacterData(false, $character, 'level');
			
			//check if the character is already higher level
			if ($charData['level'] < $level['level'])
			{
				//level the character
				$levelUp = $command->levelTo($character, $level['level'], $RealmId);
				//send the gold
				$sentGold = $command->sendMoney($character, $level['money'], 'Premium Store Delivery', $RealmId);
				//make the bags string
				$bagsString = "";
				for ($i = 0; $i < $level['bags']; $i++) { $bagsString .= $level['bagsId'] . " "; }
				//send the bags
				$sentBags = $command->sendItems($character, $bagsString, 'Premium Store Delivery', $RealmId);
				
				//check if any of the actions have failed and log it
				if ($levelUp !== true)
				{
					$logs->update(false, 'The website failed to execute the level command and returned: '.$levelUp.'.', 'error');
				}
				if ($sentGold !== true)
				{
					$logs->update(false, 'The website failed to execute the send money command and returned: '.$sentGold.'.', 'error');
				}
				if ($sentBags !== true)
				{
					$logs->update(false, 'The website failed to execute the send items command and returned: '.$sentBags.'.', 'error');
				}
				
				//check if one of those actions was successful
				if ($levelUp === true or $sentGold === true or $sentBags === true)
				{
					//charge for the purchase
					$Charge = $finance->Charge("Level Up", CA_SOURCE_TYPE_PURCHASE);
					
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
				else
				{
					$ERRORS->Add("The website failed to deliver your purchase. Please contact the administration.");
					//update the log
					$logs->update(false, 'Soap failed to execute any of the level up commands.', 'error');
				}
				
				//check for fatal errors before proceeding to the complete page
				$ERRORS->Check('/index.php?page=levels');
				
				//redirect				
				$ERRORS->triggerSuccess();
			}
			else
			{
				$ERRORS->Add("The selected character is already higher level.");
				//log
				$logs->update(false, 'The user\'s character is already higher level.', 'error');
			}
		}
		else
		{
			$ERRORS->Add("The selected character does not belong to this account.");
			//log
			$logs->update(false, 'The user is trying to level character from another account.', 'error');
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

$ERRORS->Check('/index.php?page=levels');

exit;