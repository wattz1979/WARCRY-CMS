<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

if (!$CURUSER->isOnline())
{
	echo 'Error: You must be logged in.';
	die;
}

//load the character module
$CORE->load_ServerModule('character');
$CORE->load_CoreModule('item.refund.system');
$CORE->load_ServerModule('commands');
$CORE->load_CoreModule('accounts.finances');

//prepare the sendmail class						
$command = new server_Commands();

//Setup the finances class
$finance = new AccountFinances();

//Prepare the characters class
$chars = new server_Character();

//prepare multi errors
$ERRORS->NewInstance('refund_item');

$RealmId = $CURUSER->GetRealm();	
$refundId = (isset($_POST['id']) ? (int)$_POST['id'] : false);

if (!$RealmId)
{
	echo 'There is no realm assigned to your account.';
	die;
}

if (!$chars->setRealm($RealmId))
{
	echo 'The realm assigned to your account is invalid.';
	die;
}

//check if the realm is online
if ($command->CheckConnection($RealmId) !== true)
{
	echo 'The realm is currently unavailable. Please try again in few minutes.';
}

//verify the refund id
if (!$refundId)
{
	echo 'The refund id is missing.';
	die;
}

//Try getting the refund record
$row = ItemRefundSystem::GetRefundable($refundId);

unset($refundId);

if (!$row)
{
	echo 'The refund id is invalid.';
	die;
}

//verify the refund status
if ($row['status'] != IRS_STATUS_NONE)
{
	echo 'The refund record has been already refunded.';
	die;
}

//Check if the user is allowed to refund more items this week
if (ItemRefundSystem::GetRefundsDone() >= 2)
{
	echo 'You are not allowed to refund more items this week.';
	die;
}

####################################################################
## The actual unstuck script begins here
	
	//Get the character name by the guid
	$charName = $chars->getCharacterName($row['character']);
	
	//try unsticking
	$refund = $command->RefundItem($row['entry'], $charName, $RealmId);
	
	//unset the class
	unset($chars, $command, $charName);
	
	//Check if the item was destroyed
	if ($refund === false)
	{
		echo 'The website failed to refund the item. Please try again later or contact the administration.';
		die;
	}
	else if ($refund === true)
	{
		//Set the currency
		$finance->SetCurrency((int)$row['currency']);
		//Set the amount we are Giving
		$set = $finance->SetAmount((int)$row['price']);
		//Give coins to the user
		$Reward = $finance->Reward('Item Refund');

		//check if the coins ware not given
		if ($Reward !== true)
		{
			ItemRefundSystem::SetError($row['id'], 'The finance class failed to add the required amount to the user.');
			
			echo 'The website failed to update your account balance. Please contact the administration.';
			die;
		}
		
		ItemRefundSystem::RefundableSetStatus($row['id'], IRS_STATUS_REFUNDED);
		
		//register success message
		$ERRORS->registerSuccess('The item has been successfully refunded.');
		
		echo 'OK';
	}
	else
	{
		echo 'The system encoutenred the following error: ' . $refund;
		die;
	}
	
####################################################################

exit;