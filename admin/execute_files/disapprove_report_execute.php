<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

if (!$CURUSER->isOnline())
{
	echo 'Please login.';
	die;
}

$id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_MAN_BUGTRACKER))
{
	echo 'You dont have the required permissions.';
	die;
}

if (!$id)
{
	echo 'Report id is missing.';
	die;
}
else
{
	//check if the news record exists
	$res = $DB->prepare("SELECT * FROM `bugtracker` WHERE `id` = :id LIMIT 1;");
	$res->bindParam(':id', $id, PDO::PARAM_INT);
	$res->execute();
	
	if ($res->rowCount() == 0)
	{
		echo 'The report record is missing.';
		die;
	}
	else
	{
		$row = $res->fetch();
	}
	unset($res);
	
	//check if the screenshot is already approved
	if ($row['approval'] != BT_APP_STATUS_PENDING)
	{
		echo 'The report must have pending approval status.';
		die;
	}
}

####################################################################
## The actual script begins here
	
	//define the approve type
	$approval = BT_APP_STATUS_DECLINED;
	
	//insert the news record
	$update = $DB->prepare("UPDATE `bugtracker` SET `approval` = :approval WHERE `id` = :id LIMIT 1;");
	$update->bindParam(':approval', $approval, PDO::PARAM_INT);
	$update->bindParam(':id', $row['id'], PDO::PARAM_INT);
	$update->execute();
	
	if ($update->rowCount() == 0)
	{
		echo 'The website failed to update the report record.';
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