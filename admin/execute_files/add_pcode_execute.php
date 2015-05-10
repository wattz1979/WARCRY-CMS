<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//check for permissions
$CORE->CheckPermissionsExecute(PERMISSION_PROMO_CODES);

//prepare multi errors
$ERRORS->NewInstance('pcode_add');

$format = (isset($_POST['format']) ? $_POST['format'] : 'XXXX-XXXX-XXXX');
$usage = (isset($_POST['usage']) ? (int)$_POST['usage'] : 0);
$reward_type = (isset($_POST['reward_type']) ? (int)$_POST['reward_type'] : false);
$reward_value = (isset($_POST['reward_value']) ? (int)$_POST['reward_value'] : false);

if ($reward_type === false || $reward_type == 0)
{
	$ERRORS->Add("Please select code reward type.");
}
else
{
	//Validate the reward type
	$types = array(PCODE_REWARD_CURRENCY_S, PCODE_REWARD_CURRENCY_G, PCODE_REWARD_ITEM);
	
	if (!in_array($reward_type, $types))
	{
		$ERRORS->Add('The selected reward type is invalid.');
	}
}

if ($reward_value === false || $reward_value == 0)
{
	$ERRORS->Add("Please enter code reward value.");
}


$ERRORS->Check('/index.php?page=pcodes');

####################################################################
## The actual script begins here

//Load the Tokens Module
$CORE->load_CoreModule('promo.codes');

//Setup new Promo Code Generator
$PCodeGen = new PromoCodeGen();

//Setup the reward
$PCodeGen->setRewardType($reward_type)->setRewardValue($reward_value);
//Register the key and format it
if ($key = $PCodeGen->setUsage($usage)->format($format)->Generate()->get())
{
	//bind on success
	$ERRORS->onSuccess('The promo code "'.$key.'" was successfully added.', '/index.php?page=pcodes');
	$ERRORS->triggerSuccess();
}
else
{
	$ERRORS->Add($PCodeGen->getLastError());
}

####################################################################

$ERRORS->Check('/index.php?page=pcodes');

exit;