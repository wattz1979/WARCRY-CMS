<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//check for permissions
$CORE->CheckPermissionsExecute(PERMISSION_PSTORE);

//prepare multi errors
$ERRORS->NewInstance('pstore_armorsets_add');
//bind on success
$ERRORS->onSuccess('The armor set was successfully added.', '/index.php?page=pstore');

$name = (isset($_POST['name']) ? $_POST['name'] : false);
$realm = (isset($_POST['realm']) ? $_POST['realm'] : false);
$category = (isset($_POST['category']) ? (int)$_POST['category'] : false);
$price = (isset($_POST['price']) ? (int)$_POST['price'] : false);
$class = (isset($_POST['class']) ? (int)$_POST['class'] : false);
$type = (isset($_POST['type']) ? $_POST['type'] : false);
$tier = (isset($_POST['tier']) ? $_POST['tier'] : false);
$items = (isset($_POST['items']) ? $_POST['items'] : false);

if (!$name)
{
	$ERRORS->Add("Please enter armor set title.");
}
if (!$category or $category == 0)
{
	$ERRORS->Add("Please select armor set category.");
}
if (!$price)
{
	$ERRORS->Add("Please price for the armor set.");
}
if (!$items or $items == '')
{
	$ERRORS->Add("Please place at least one item for the armor set.");
}

$ERRORS->Check('/index.php?page=pstore');

####################################################################
## The actual script begins here
	
	//insert the news record
	$insert = $DB->prepare("INSERT INTO `armorsets` (`name`, `realm`, `category`, `price`, `tier`, `class`, `type`, `items`) VALUES (:title, :realm, :cat, :price, :tier, :class, :type, :items);");
	$insert->bindParam(':title', $name, PDO::PARAM_STR);
	$insert->bindParam(':realm', $realm, PDO::PARAM_STR);
	$insert->bindParam(':cat', $category, PDO::PARAM_INT);
	$insert->bindParam(':price', $price, PDO::PARAM_INT);
	$insert->bindParam(':tier', $tier, PDO::PARAM_STR);
	$insert->bindParam(':class', $class, PDO::PARAM_INT);
	$insert->bindParam(':type', $type, PDO::PARAM_STR);
	$insert->bindParam(':items', $items, PDO::PARAM_STR);
	$insert->execute();
	
	if ($insert->rowCount() < 1)
	{
		$ERRORS->Add("The website failed to insert the armor set record.");
	}
	else
	{
		unset($insert);
		$ERRORS->triggerSuccess();
	}
	unset($insert);
	
####################################################################

$ERRORS->Check('/index.php?page=pstore');

exit;