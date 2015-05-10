<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

if (!$CURUSER->isOnline())
{
	echo 'You must be logged in.';
	die;
}

//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_PSTORE))
{
	echo 'You do not have the required permissions.';
	die;
}

$id = (isset($_POST['id']) ? (int)$_POST['id'] : false);
$name = (isset($_POST['name']) ? $_POST['name'] : false);

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
$res = $DB->prepare("SELECT id, name FROM `armorset_categories` WHERE `id` = :id LIMIT 1;");
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
	
	//insert the news record
	$update = $DB->prepare("UPDATE `armorset_categories` SET `name` = :name WHERE `id` = :id LIMIT 1;");
	$update->bindParam(':name', $name, PDO::PARAM_STR);
	$update->bindParam(':id', $row['id'], PDO::PARAM_INT);
	$update->execute();
	
	if ($update->rowCount() < 1)
	{
		echo 'The website failed to update the category record.';
		die;
	}
	else
	{
		unset($insert);
		echo 'OK';
	}
	unset($insert);
	
####################################################################

exit;