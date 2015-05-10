<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//check for permissions
$CORE->CheckPermissionsExecute(PERMISSION_STORE);

//prepare multi errors
$ERRORS->NewInstance('edit_storeitem');
//bind on success
$ERRORS->onSuccess('The item was successfully edited.', '/index.php?page=store');

$id = (isset($_POST['id']) ? (int)$_POST['id'] : false);

$entry = (isset($_POST['entry']) ? (int)$_POST['entry'] : false);
$name = (isset($_POST['name']) ? $_POST['name'] : false);
$realm = (isset($_POST['realm']) ? $_POST['realm'] : false);
$gold = (isset($_POST['gold']) ? (int)$_POST['gold'] : false);
$silver = (isset($_POST['silver']) ? (int)$_POST['silver'] : false);

if (!$id)
{
	$ERRORS->Add("The item id is missing.");
}
if (!$entry)
{
	$ERRORS->Add("Please enter item entry.");
}
if (!$name)
{
	$ERRORS->Add("Please enter armor set title.");
}
if (!$realm)
{
	$ERRORS->Add("Please enter item realms.");
}
if ($gold === false)
{
	$ERRORS->Add("Please enter price in gold.");
}
if ($silver === false)
{
	$ERRORS->Add("Please enter price in silver.");
}

$ERRORS->Check('/index.php?page=store');

//verify the item
$res = $DB->prepare("SELECT `id` FROM `store_items` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $id, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() == 0)
{
	$ERRORS->Add("The selected item is invalid or missing.");
}

$row = $res->fetch();

unset($res);

$ERRORS->Check('/index.php?page=store');

####################################################################
## The actual script begins here
	
	//insert the news record
	$update = $DB->prepare("UPDATE `store_items` SET `entry` = :entry, `name` = :name, `realm` = :realm, `gold` = :gold, `silver` = :silver WHERE `id` = :id LIMIT 1;");
	$update->bindParam(':name', $name, PDO::PARAM_STR);
	$update->bindParam(':entry', $entry, PDO::PARAM_INT);
	$update->bindParam(':realm', $realm, PDO::PARAM_STR);
	$update->bindParam(':gold', $gold, PDO::PARAM_INT);
	$update->bindParam(':silver', $silver, PDO::PARAM_INT);
	$update->bindParam(':id', $id, PDO::PARAM_INT);
	$update->execute();
	
	if ($update->rowCount() < 1)
	{
		$ERRORS->Add("The website failed to update the item record.");
	}
	else
	{
		unset($update);
		$ERRORS->triggerSuccess();
	}
	unset($update);
	
####################################################################

$ERRORS->Check('/index.php?page=store');

exit;