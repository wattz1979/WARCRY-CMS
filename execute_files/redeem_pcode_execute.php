<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//Load the Tokens Module
$CORE->load_CoreModule('promo.codes');

//prepare multi errors
$ERRORS->NewInstance('pcode');

$RealmID = $CURUSER->GetRealm();

//Get the code
$code = ((isset($_POST['code'])) ? $_POST['code'] : false);
//Get the character name if passed
$charName = ((isset($_POST['character'])) ? $_POST['character'] : false);

if (!$code)
{
	$ERRORS->Add("Please enter promo code.");
}

$ERRORS->Check('/index.php?page=pcode');

//Setup new promo code
$PCode = new PromoCode($code);

//set the account
$PCode->setAccount($CURUSER->get('id'));
//set the realm in case of item reward
$PCode->setRealm($RealmID);
//set character if online
$PCode->setCharacter($charName);

//Verify promo code
if ($PCode->Verify())
{
	//Reward the user
	if ($PCode->ProcessReward())
	{
		//bind the onsuccess message
		$ERRORS->onSuccess('The promotion code was successfully redeemed.', '/index.php?page=pcode');
		$ERRORS->triggerSuccess();
		exit;
	}
	else
	{
		$ERRORS->Add($PCode->getLastError());
	}
}
else
{
	$ERRORS->Add($PCode->getLastError());
}

$ERRORS->Check('/index.php?page=pcode');

exit;