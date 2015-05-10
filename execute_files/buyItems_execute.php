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
//load the refund system
$CORE->load_CoreModule('item.refund.system');

//prepare the sendmail class						
$command = new server_Commands();

//prepare the log
$logs = new purchaseLog();

//prepare multi errors
$ERRORS->NewInstance('store');
	
$items = (isset($_POST['items']) ? $_POST['items'] : false);
$charName = (isset($_POST['character']) ? $_POST['character'] : false);

$RealmId = $CURUSER->GetRealm();

//check if the realm is online
if ($command->CheckConnection($RealmId) !== true)
{
	$ERRORS->Add("The realm is currently unavailable. Please try again in few minutes.");
}
if (!$charName)
{
	$ERRORS->Add("Please select a character first.");
}
if (!$items)
{
	$ERRORS->Add("There ware no items to send.");
}

$ERRORS->Check('/index.php?page=store');

####################################################################
## The actual script begins here

	//construct the characters handler
	$chars = new server_Character();
	
	//get the account gold and silver
	$accountGold = (int)$CURUSER->get('gold');
	$accountSilver = (int)$CURUSER->get('silver');

	//start logging
	$logs->add('STORE', 'Starting log session, initial values: '.$accountSilver.' silver, '.$accountGold.' gold. To character: '.$charName.' in realm: '.$RealmId.'.', 'pending');
	
	//set the realm
	if ($chars->setRealm($RealmId))
	{
		//check if the character belongs to this account
		if ($chars->isMyCharacter(false, $charName))
		{
			//create error array
			$itemErrors = array();
			//create negative items string
			$itemsString = false;
			
			$charData = $chars->getCharacterData(false, $charName, 'guid');
			//Get the character GUID
			$characterGUID = $charData['guid'];
			
			unset($charData);
			
			//loop the item list
			foreach ($items as $index => $data)
			{
				//set the default hasMoney to false
				$hasMoney = false;
				
				list($id, $currency, $icon) = explode(',', $data);
				
				//save the currency of this item in case of error
				$itemErrors[$index]['id'] = $id;
				$itemErrors[$index]['currency'] = $currency;
				$itemErrors[$index]['icon'] = $icon;
				$itemErrors[$index]['error'] = '';
				
				//get the store items records
				$res = $DB->prepare("SELECT id, entry, silver, gold FROM `store_items` WHERE `id` = :item AND `realm` LIKE CONCAT('%', :realm, '%') LIMIT 1;");
				$res->bindParam(':item', $id, PDO::PARAM_INT);
				$res->bindParam(':realm', $RealmId, PDO::PARAM_INT);
				$res->execute();
								
				//check if we have found the item
				if ($res->rowCount() > 0)
				{
					//fetch the record
					$row = $res->fetch();
					
					//now check if the account has the money needed
					if ($currency == 'gold' or $currency == 'silver')
					{
						//if the currency is silver
						if ($currency == 'silver')
						{
							//Check if the item is purchasable with silver
							if ((int)$row['silver'] > 0)
							{
								if ($accountSilver >= (int)$row['silver'])
								{
									//define that the account has the money
									$hasMoney = true;
									//remove the money from the account
									$accountSilver = $accountSilver - (int)$row['silver'];
								}
								else
								{
									//save the item error
									$itemErrors[$index]['error'] = 'You do not have enough silver to buy this item.';
									//log
									$logs->update(false, 'The user does not have enough silver to complete the purchase of item (id: '.$id.').', 'error');
								}
							}
							else
							{
								//save the item error
								$itemErrors[$index]['error'] = 'This item cannot be purchased with silver.';
								//log
								$logs->update(false, 'The user is trying to purchase item (id: '.$id.') with the wrong currency.', 'error');
							}
							$moneyString = $row['silver'] . ' Silver';
						}
						else
						{
							//Check if the item is purchasable with silver
							if ((int)$row['gold'] > 0)
							{
								if ($accountGold >= (int)$row['gold'])
								{
									//define that the account has the money
									$hasMoney = true;
									//remove the money from the account
									$accountGold = $accountGold - (int)$row['gold'];
								}
								else
								{
									//save the item error
									$itemErrors[$index]['error'] = 'You do not have enough gold to buy this item.';
									//log
									$logs->update(false, 'The user does not have enough gold to complete the purchase of item (id: '.$id.').', 'error');
								}
							}
							else
							{
								//save the item error
								$itemErrors[$index]['error'] = 'This item cannot be purchased with gold.';
								//log
								$logs->update(false, 'The user is trying to purchase item (id: '.$id.') with the wrong currency.', 'error');
							}
							$moneyString = $row['gold'] . ' Gold';
						}
						
						//if the character has the money send the item
						if ($hasMoney)
						{
							$theTime = $CORE->getTime();
							
							$currencyType = $currency == 'silver' ? CA_COIN_TYPE_SILVER : CA_COIN_TYPE_GOLD;
							$price = $currency == 'silver' ? $row['silver'] : $row['gold'];
							
							//insert in the store_activity table
							$insert = $DB->prepare("INSERT INTO `store_activity` (`account`, `source`, `text`, `time`, `itemId`, `money`) VALUES (:acc, 'STORE', 'Purchase', :time, :itemId, :money);");
							$insert->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
							$insert->bindParam(':time', $theTime, PDO::PARAM_STR);
							$insert->bindParam(':itemId', $id, PDO::PARAM_INT);
							$insert->bindParam(':money', $moneyString, PDO::PARAM_STR);
							$insert->execute();
							unset($insert);
							
							//log into coin activity
							$ca = new CoinActivity();
							$ca->set_SourceType(CA_SOURCE_TYPE_NONE);
							$ca->set_SourceString('Item Purchase');
							$ca->set_CoinsType($currencyType);
							$ca->set_ExchangeType(CA_EXCHANGE_TYPE_MINUS);
							$ca->set_Amount($price);
							$ca->execute();
							unset($ca);
							
							$currencyType = $currency == 'silver' ? CURRENCY_SILVER : CURRENCY_GOLD;
							
							//Add the item as refundable
							$refundable = ItemRefundSystem::AddRefundable($row['entry'], $price, $currencyType, $characterGUID);
							
							//update the log
							$logs->update(false, 'The user has enough money, proceeding to item (id: '.$id.') sending.' . ($refundable ? ' Successfully added the item as refundable.' : ''));
							
							//sending... append the item entry to the items string for the SOAP command
							if (!$itemsString)
							{
								$itemsString = $row['entry'];
							}
							else
							{
								$itemsString .= ' ' . $row['entry'];
							}
							
							unset($currencyType, $price, $theTime);
						}
					}
					else
					{
						//save the item error
						$itemErrors[$index]['error'] = 'The selected currency for this item is invalid.';
						//log
						$logs->update(false, 'The user is using invalid currency for this purchase of item (id: '.$id.').', 'error');
					}
				}
				else
				{
					//save the item error
					$itemErrors[$index]['error'] = 'The item does not exist in the store.';
					//log
					$logs->update(false, 'The user is trying to purchase invalid item (id: '.$id.') that do not exist in the store.', 'error');
				}
				unset($res);
			}
			//end of the item loop
			
			//if we have any items to send
			if ($itemsString)
			{
				//Count the items in this string
				$qItems = explode(' ', $itemsString);
				
				//update the log
				$logs->update(false, 'Total items in the cart: ' . count($qItems));
						
				//send the items
				$sentMail = $command->sendItems($charName, $itemsString, 'Store Item Delivery', $RealmId);
				
				//make sure the mail was sent
				if ($sentMail === true)
				{
					//update the account money
					$update = $DB->prepare("UPDATE `account_data` SET `silver` = :silver, `gold` = :gold WHERE `id` = :account LIMIT 1;");
					$update->bindParam(':silver', $accountSilver, PDO::PARAM_INT);
					$update->bindParam(':gold', $accountGold, PDO::PARAM_INT);
					$update->bindParam(':account', $CURUSER->get('id'), PDO::PARAM_INT);	
					$update->execute();
					
					if ($update->rowCount() > 0)
					{
						//update the log
						$logs->update(false, 'The mail was sent and the user has been successfully charged for his purchase. New values: '.$accountSilver.' silver, '.$accountGold.' gold.', 'ok');
					}
					else
					{
						//update the log
						$logs->update(false, 'The user was not charged for his purchase, website failed to update. Values that should have been applied: '.$accountSilver.' silver, '.$accountGold.' gold.', 'error');
					}					
				}
				else
				{
					$ERRORS->Add("The website failed to deliver your purchase. Please contact the administration.");
					//update the log
					$logs->update(false, 'Soap failed to send the items, soap return: '.$sentMail, 'error');
				}
			}
			
			unset($characterGUID);
			
			//check for fatal errors before proceeding to the complete page
			$ERRORS->Check('/index.php?page=store');
			
			//save the item array on a session
			$_SESSION['StoreItemReturn'] = $itemErrors;
			$_SESSION['StoreItemReturnChar'] = $charName;
			
			//redirect				
			header("Location: ".$config['BaseURL']."/index.php?page=store_complete");
		}
		else
		{
			$ERRORS->Add("The selected character does not belong to this account.");
			//log
			$logs->update(false, 'The user is trying to purchase an item for another account.', 'error');
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

$ERRORS->Check('/index.php?page=store');

exit;