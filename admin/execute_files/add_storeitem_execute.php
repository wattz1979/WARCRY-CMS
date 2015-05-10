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
$ERRORS->NewInstance('add_storeitem');
//bind on success
$ERRORS->onSuccess('The item was successfully added.', '/index.php?page=store');

$entry = (isset($_POST['entry']) ? (int)$_POST['entry'] : false);
$name = (isset($_POST['name']) ? $_POST['name'] : false);
$quality = (isset($_POST['quality']) ? (int)$_POST['quality'] : false);
$realm = (isset($_POST['realm']) ? $_POST['realm'] : false);
$gold = (isset($_POST['gold']) ? (int)$_POST['gold'] : false);
$silver = (isset($_POST['silver']) ? (int)$_POST['silver'] : false);
$class = (isset($_POST['class']) ? (int)$_POST['class'] : false);
$subclass = (isset($_POST['subclass']) ? (int)$_POST['subclass'] : false);
$itemlevel = (isset($_POST['itemlevel']) ? (int)$_POST['itemlevel'] : false);
$type = (isset($_POST['invtype']) ? (int)$_POST['invtype'] : false);

if (!$entry)
{
	$ERRORS->Add("Please enter item entry.");
}
if (!$name)
{
	$ERRORS->Add("Please enter item name.");
}
if ($quality === false)
{
	$ERRORS->Add("Please select select item quality.");
}
if (!$realm)
{
	$ERRORS->Add("Please enter realms in which the item will be purchasable.");
}
if ($gold === false)
{
	$ERRORS->Add("Please enter item price in gold.");
}
if ($silver === false)
{
	$ERRORS->Add("Please enter item price in silver.");
}

$ERRORS->Check('/index.php?page=store-add');

####################################################################
## The actual script begins here
	
	//insert the news record
	$insert = $DB->prepare("INSERT INTO `store_items` (`entry`, `realm`, `name`, `gold`, `silver`, `class`, `subclass`, `ItemLevel`, `Quality`, `InventoryType`) VALUES (:entry, :realms, :name, :gold, :silver, :class, :subclass, :itemlevel, :quality, :type);");
	$insert->bindParam(':entry', $entry, PDO::PARAM_INT);
	$insert->bindParam(':realms', $realm, PDO::PARAM_STR);
	$insert->bindParam(':name', $name, PDO::PARAM_STR);
	$insert->bindParam(':gold', $gold, PDO::PARAM_INT);
	$insert->bindParam(':silver', $silver, PDO::PARAM_INT);
	$insert->bindParam(':class', $class, PDO::PARAM_INT);
	$insert->bindParam(':subclass', $subclass, PDO::PARAM_INT);
	$insert->bindParam(':itemlevel', $itemlevel, PDO::PARAM_INT);
	$insert->bindParam(':quality', $quality, PDO::PARAM_INT);
	$insert->execute();
	
	if ($insert->rowCount() < 1)
	{
		$ERRORS->Add("The website failed to insert the store item record.");
	}
	else
	{
		unset($insert);
		$ERRORS->triggerSuccess();
	}
	unset($insert);
	
####################################################################

$ERRORS->Check('/index.php?page=store-add');

exit;