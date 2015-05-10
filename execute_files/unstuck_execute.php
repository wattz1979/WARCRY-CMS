<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//load the character module
$CORE->load_ServerModule('character');

$chars = new server_Character();

//prepare multi errors
$ERRORS->NewInstance('unstuck');

$RealmId = $CURUSER->GetRealm();

$charName = (isset($_POST['character']) ? $_POST['character'] : false);

//bind the onsuccess message
$ERRORS->onSuccess($charName . ' has been successfully unstucked.', '/index.php?page=unstuck');

$cooldown = $CURUSER->getCooldown('unstuck');
$cooldownTime = '15 minutes';

if (!$charName)
{
	$ERRORS->Add("Please select a character.");
}
if (!$RealmId)
{
	$ERRORS->Add("There is no realm assigned to your account.");
}
if (!$chars->setRealm($RealmId))
{
	$ERRORS->Add('The realm assigned to your account is invalid.');
}
//check if this character belongs to this account
if (!$chars->isMyCharacter(false, $charName))
{
	$ERRORS->Add('The selected character does not belong to this account.');
}
//check the cooldown
if (time() < $cooldown)
{
	$ERRORS->Add('This tool is on cooldown, please try again later.');
}

$ERRORS->Check('/index.php?page=unstuck');

####################################################################
## The actual unstuck script begins here
	
	//try unsticking
	$unstuck = $chars->Unstuck(false, $charName);
	
	//unset the class
	unset($chars);
	
	//update the cooldown if the character was unstucked
	if ($unstuck)
	{
		//set cooldown because we got no errors
		$CURUSER->setCooldown('unstuck', strtotime('+'.$cooldownTime));
		//redirect
		$ERRORS->triggerSuccess();
	}
	else
	{
		$ERRORS->Add('The website failed to unstuck your character. Please try again later or contact the administration.');
	}
	
####################################################################

$ERRORS->Check('/index.php?page=unstuck');

exit;