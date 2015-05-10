<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//prepare multi errors
$ERRORS->NewInstance('setrealm');

$RealmId = isset($_POST['realm']) ? (int)$_POST['realm'] : false;

if (!$RealmId)
{
	$ERRORS->Add("Please select a realm first.");
}

//Validate the relam
if (!isset($realms_config[$RealmId]))
{
	$ERRORS->Add("The selected realm is invalid.");
}

$ERRORS->Check('/index.php?page=account');

####################################################################
## The actual unstuck script begins here
	
	//bind the onsuccess message
	$ERRORS->onSuccess('<strong>' . $realms_config[$RealmId]['name'] . '</strong> was successfully set as operating realm.', '/index.php?page=account');
	
	//Set the realm
	$update = $DB->prepare("UPDATE `account_data` SET `selected_realm` = :realm WHERE `id` = :acc LIMIT 1;");
	$update->bindParam(':realm', $RealmId, PDO::PARAM_INT);
	$update->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
	$update->execute();
	
	//update the cooldown if the character was unstucked
	if ($update->rowCount() > 0)
	{
		//redirect
		$ERRORS->triggerSuccess();
	}
	else
	{
		$ERRORS->Add('The website failed to set your operating realm. Please try again later or contact the administration.');
	}
	
####################################################################

$ERRORS->Check('/index.php?page=account');

exit;