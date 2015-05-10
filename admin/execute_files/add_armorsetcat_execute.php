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
$ERRORS->NewInstance('pstore_armorsets_addcat');
//bind on success
$ERRORS->onSuccess('The category was successfully added.', '/index.php?page=pstore&switchTab=2');

$name = (isset($_POST['name']) ? $_POST['name'] : false);

if (!$name)
{
	$ERRORS->Add("Please enter category title.");
}

$ERRORS->Check('/index.php?page=pstore&switchTab=2');

####################################################################
## The actual script begins here
	
	//insert the news record
	$insert = $DB->prepare("INSERT INTO `armorset_categories` (`name`) VALUES (:title);");
	$insert->bindParam(':title', $name, PDO::PARAM_STR);
	$insert->execute();
	
	if ($insert->rowCount() < 1)
	{
		$ERRORS->Add("The website failed to insert the category record.");
	}
	else
	{
		unset($insert);
		$ERRORS->triggerSuccess();
	}
	unset($insert);
	
####################################################################

$ERRORS->Check('/index.php?page=pstore&switchTab=2');

exit;