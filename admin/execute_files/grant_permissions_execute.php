<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//check for permissions
$CORE->CheckPermissionsExecute(PERMISSION_GIVE_PERMISSIONS);

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : false;

//prepare multi errors
$ERRORS->NewInstance('grant_permissions');

if (!$uid)
{
	$ERRORS->Add("The user id is missing.");
}

$ERRORS->Check('/index.php?page=user-preview&uid=' . $uid);

//Verify the user
$res = $DB->prepare("SELECT `id` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
$res->bindParam(':acc', $uid, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() == 0)
{
	$ERRORS->Add("The user seems to be invalid.");
}
else
{
	$row = $res->fetch();
}
unset($res);

$ERRORS->Check('/index.php?page=user-preview&uid=' . $uid);

####################################################################
## The actual script begins here
	
	//Check if this user already has a permissions record
	$res = $DB->prepare("SELECT * FROM `acp_permissions` WHERE `id` = :acc LIMIT 1;");
	$res->bindParam(':acc', $uid, PDO::PARAM_INT);
	$res->execute();
	
	//Insert one if the record is missing
	if ($res->rowCount() == 0)
	{
		$insert = $DB->prepare("INSERT INTO `acp_permissions` (`id`) VALUES (:account);");
		$insert->bindParam(':account', $uid, PDO::PARAM_INT);
		$insert->execute();
		
		if ($insert->rowCount() == 0)
		{
			$ERRORS->Add("The website failed to insert new permission record.");
		}
		unset($insert);
		
		$ERRORS->Check('/index.php?page=user-preview&uid=' . $uid);
	}
	unset($res);
	
	$updateset = array();
	//check which permissions should be enable or disabled
	foreach ($ACPValidPermissions as $index)
	{
		$updateset[] = "`".$index."` = '".(isset($_POST['permission_' . $index]) ? '1' : '0')."'";
	}
	
	//Let's set the permissions
	$update = $DB->prepare("UPDATE `acp_permissions` SET ".implode(',', $updateset)." WHERE `id` = :id LIMIT 1;");
	$update->bindParam(':id', $uid, PDO::PARAM_INT);
	$update->execute();
	
	if ($update->rowCount() == 0)
	{
		$ERRORS->Add("The website failed to update the user\'s permissions.");
	}
	else
	{
		//bind on success
		$ERRORS->onSuccess('The user\'s permissions have been updated.', '/index.php?page=user-preview&uid=' . $uid);
		//redirect
		$ERRORS->triggerSuccess();
	}
	unset($insert);
	
	$ERRORS->Check('/index.php?page=user-preview&uid=' . $uid);
	
####################################################################

exit;