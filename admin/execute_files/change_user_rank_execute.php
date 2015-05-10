<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//check for permissions
$CORE->CheckPermissionsExecute(PERMISSION_CHANGE_USER_RANK);

//prepare multi errors
$ERRORS->NewInstance('change_rank');

$id = (isset($_POST['id']) ? (int)$_POST['id'] : false);
$rank = (isset($_POST['rank']) ? (int)$_POST['rank'] : false);

if (!$id)
{
	$ERRORS->Add("The user id is missing.");
}
if (!$rank)
{
	$ERRORS->Add("Please select any of the listed ranks.");
}

$ERRORS->Check('/index.php?page=user-preview&uid=' . $id);

//check if the user record exists
$res = $DB->prepare("SELECT `id` FROM `account_data` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $id, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() == 0)
{
	$ERRORS->Add("The user record is invalid or missing.");
}
else
{
	$row = $res->fetch();
}
unset($res);

$ERRORS->Check('/index.php?page=user-preview&uid=' . $id);

####################################################################
## The actual script begins here
	
	//bind on success
	$ERRORS->onSuccess('The user rank was successfully updated.', '/index.php?page=user-preview&uid=' . $id);
	
	//insert the news record
	$update = $DB->prepare("UPDATE `account_data` SET `rank` = :rank WHERE `id` = :id LIMIT 1;");
	$update->bindParam(':rank', $rank, PDO::PARAM_INT);
	$update->bindParam(':id', $row['id'], PDO::PARAM_INT);
	$update->execute();
	
	if ($update->rowCount() < 1)
	{
		$ERRORS->Add("The website failed to update the user's record.");
	}
	else
	{
		$ERRORS->triggerSuccess();
	}
	unset($update);
	
####################################################################

$ERRORS->Check('/index.php?page=user-preview&uid=' . $id);

exit;