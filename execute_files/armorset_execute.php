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

//prepare the sendmail class						
$command = new server_Commands();

//prepare the log
$logs = new purchaseLog();

//prepare multi errors
$ERRORS->NewInstance('pStore_armorsets');
//bind the onsuccess message
$ERRORS->onSuccess('The armor set was successfully sent.', '/index.php?page=itemsets');

$character = (isset($_POST['character']) ? $_POST['character'] : false);
$armorset = (isset($_POST['armorset']) ? (int)$_POST['armorset'] : false);

//Get the user selected realm
$RealmId = $CURUSER->GetRealm();

//get the account gold
$accountGold = (int)$CURUSER->get('gold');

if (!$character)
{
	$ERRORS->Add("Please select a character first.");
}

//find the armorset record
$res = $DB->prepare("SELECT id, price, realm, items FROM `armorsets` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $armorset, PDO::PARAM_INT);
$res->execute();

//check if we have found the record
if ($res->rowCount() == 0)
{
	$ERRORS->Add("The armor set record is missing.");
}
else
{
	//fetch the armorset record
	$row = $res->fetch();
}
unset($res);

//check if the itemset realm is the current selected one
if ($row['realm'] != '-1' and $RealmId != $row['realm'])
{
	$ERRORS->Add("The selected armor set is for another realm.");
}
//check if the user has the money
if ($accountGold < $row['price'])
{
	$ERRORS->Add("You do not have enough Gold Coins.");
}

$ERRORS->Check('/index.php?page=itemsets');

####################################################################
## The actual script begins here

	//construct the characters handler
	$chars = new server_Character();

	//start logging
	$logs->add('PSTORE_ARMORSETS', 'Starting log session, initial value of the required currency: '.$accountGold.' Gold, selected realm: '.$RealmId.'.', 'pending');
	
	//set the realm
	if ($chars->setRealm($RealmId))
	{
		//check if the character belongs to this account
		if ($chars->isMyCharacter(false, $character))
		{
			//prepare the items string
			$itemsString = str_replace(',', ' ', $row['items']);
			//level the character
			$sendItems = $command->sendItems($character, $itemsString, 'Armor Set Delivery', $RealmId);
			
			//check if the command was successfull
			if ($sendItems === true)
			{
				//calc the new currency amount
				$accountGold = $accountGold - $row['price'];
				
				//update the account money
				$update = $DB->prepare("UPDATE `account_data` SET `gold` = :amount WHERE `id` = :account LIMIT 1;");
				$update->bindParam(':amount', $accountGold, PDO::PARAM_INT);
				$update->bindParam(':account', $CURUSER->get('id'), PDO::PARAM_INT);	
				$update->execute();
				
				if ($update->rowCount() > 0)
				{
					//update the log
					$logs->update(false, 'The send items command has been executed and the user has been successfully charged for his purchase. New value of required currency: '.$accountGold.' Gold.', 'ok');
					//log into coin activity
					$ca = new CoinActivity();
					$ca->set_SourceType(CA_SOURCE_TYPE_NONE);
					$ca->set_SourceString('Armorset Purchase');
					$ca->set_CoinsType(CA_COIN_TYPE_GOLD);
					$ca->set_ExchangeType(CA_EXCHANGE_TYPE_MINUS);
					$ca->set_Amount($row['price']);
					$ca->execute();
					unset($ca);
				}
				else
				{
					//update the log
					$logs->update(false, 'The user was not charged for his purchase, website failed to update. Values that should have been applied: '.$accountGold.' Gold.', 'error');
				}					
			}
			else
			{
				$ERRORS->Add("The website failed to complete your order. Please contact the administration.");
				//update the log
				$logs->update(false, 'Soap failed to execute the send items command.', 'error');
			}
				
			//check for fatal errors before proceeding to the complete page
			$ERRORS->Check('/index.php?page=itemsets');
			
			//redirect				
			$ERRORS->triggerSuccess();
		}
		else
		{
			$ERRORS->Add("The selected character does not belong to this account.");
			//log
			$logs->update(false, 'The user is trying to purchase armor set for another account.', 'error');
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

$ERRORS->Check('/index.php?page=itemsets');

exit;