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

$id = (isset($_POST['id']) ? (int)$_POST['id'] : false);

//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_MEDIA_SREENSHOTS))
{
	echo 'You dont have the required permissions.';
	die;
}

if (!$id)
{
	echo 'Screenshot id is missing.';
	die;
}

//check if the news record exists
$res = $DB->prepare("SELECT * FROM `images` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $id, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() == 0)
{
	echo 'The screenshot record is missing.';
	die;
}
else
{
	$row = $res->fetch();
}
unset($res);

//check if the screenshot is already approved
if ($row['status'] == SCREENSHOT_STATUS_DENIED)
{
	echo 'This screenshot is already denied.';
	die;
}

####################################################################
## The actual script begins here
	
	//define the approve type
	$status = SCREENSHOT_STATUS_DENIED;
	
	//insert the news record
	$update = $DB->prepare("UPDATE `images` SET `status` = :status WHERE `id` = :id LIMIT 1;");
	$update->bindParam(':status', $status, PDO::PARAM_INT);
	$update->bindParam(':id', $row['id'], PDO::PARAM_INT);
	$update->execute();
	
	if ($update->rowCount() == 0)
	{
		echo 'The website failed to update the screenshot record.';
		die;
	}
	else
	{
		//success
		echo 'OK';
	}
	unset($update);
	
####################################################################

exit;