<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

if (!$CURUSER->isOnline())
{
	echo 'Must be logged in.';
	die;
}

//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_FORUM_CATS))
{
	echo 'You do not have the required permissions.';
	die;
}

$id = (isset($_POST['id']) ? (int)$_POST['id'] : false);
$name = (isset($_POST['name']) ? $_POST['name'] : false);
$style = (isset($_POST['style']) ? (int)$_POST['style'] : false);

if (!$id)
{
	echo 'Category id is missing.';
	die;
}
if (!$name)
{
	echo 'Please enter category title.';
	die;
}

//check if the news record exists
$res = $DB->prepare("SELECT `id`, `name`, `flags` FROM `wcf_categories` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $id, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() == 0)
{
	echo 'The category record is missing.';
	die;
}
else
{
	$row = $res->fetch();
}
unset($res);

####################################################################
## The actual script begins here
	
	//force int
	$newFlags = $row['flags'] = intval($row['flags']);
	
	if (!$style)
	{
		//remove the classes style flags
		if ($row['flags'] & WCF_FLAGS_CLASSES_LAYOUT)
			$newFlags &= ~WCF_FLAGS_CLASSES_LAYOUT;
	}
	else
	{
		if (!($row['flags'] & WCF_FLAGS_CLASSES_LAYOUT))
			$newFlags |= WCF_FLAGS_CLASSES_LAYOUT;
	}
	
	//Check if we need an update
	if ($name == $row['name'] && $row['flags'] == $newFlags)
	{
		echo 'SKIP';
		die;
	}
	
	//insert the news record
	$update = $DB->prepare("UPDATE `wcf_categories` SET `name` = :name, `flags` = :flags WHERE `id` = :id LIMIT 1;");
	$update->bindParam(':name', $name, PDO::PARAM_STR);
	$update->bindParam(':id', $row['id'], PDO::PARAM_INT);
	$update->bindParam(':flags', $newFlags, PDO::PARAM_INT);
	$update->execute();
	
	if ($update->rowCount() < 1)
	{
		echo 'The website failed to update the category record.';
		die;
	}
	else
	{
		echo 'OK';
	}
	unset($update);
	
####################################################################

exit;