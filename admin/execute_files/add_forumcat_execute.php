<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//check for permissions
$CORE->CheckPermissionsExecute(PERMISSION_FORUM_CATS);

//prepare multi errors
$ERRORS->NewInstance('forums_addcat');
//bind on success
$ERRORS->onSuccess('The category was successfully added.', '/index.php?page=forum-cats');

$name = (isset($_POST['name']) ? $_POST['name'] : false);
$style = (isset($_POST['style']) ? (int)$_POST['style'] : false);

if (!$name)
{
	$ERRORS->Add("Please enter category title.");
}

$ERRORS->Check('/index.php?page=forum-cats');

####################################################################
## The actual script begins here
	
	//Determine the position we have to place this cat
	$res = $DB->prepare("SELECT `position` FROM `wcf_categories` ORDER BY `position` DESC LIMIT 1;");
	$res->execute();
	
	if ($res->rowCount() > 0)
	{
		$row = $res->fetch();
		
		$position = $row['position'] + 1;
		
		unset($row);
	}
	else
	{
		$position = 0;
	}
	unset($res);
	
	$flags = 0;
	
	if ($style)
	{
		$flags |= WCF_FLAGS_CLASSES_LAYOUT;
	}
	
	//insert the news record
	$insert = $DB->prepare("INSERT INTO `wcf_categories` (`name`, `flags`, `position`) VALUES (:title, :flags, :pos);");
	$insert->bindParam(':title', $name, PDO::PARAM_STR);
	$insert->bindParam(':flags', $flags, PDO::PARAM_INT);
	$insert->bindParam(':pos', $position, PDO::PARAM_INT);
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

$ERRORS->Check('/index.php?page=forum-cats');

exit;