<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//check for permissions
$CORE->CheckPermissionsExecute(PERMISSION_MAN_BUGTRACKER);

//prepare multi errors
$ERRORS->NewInstance('edit_report');
//bind on success
$ERRORS->onSuccess('The report was successfully updated.', '/index.php?page=bugtracker');

$id = (isset($_POST['id']) ? (int)$_POST['id'] : false);
$title = (isset($_POST['title']) ? $_POST['title'] : false);
$content = (isset($_POST['content']) ? $_POST['content'] : false);
$priority = (isset($_POST['priority']) ? (int)$_POST['priority'] : false);
$status = (isset($_POST['status']) ? (int)$_POST['status'] : false);

if (!$id)
{
	$ERRORS->Add("Report id is missing.");
}
if (!$title)
{
	$ERRORS->Add("Please enter report title.");
}
if (!$content)
{
	$ERRORS->Add("Please enter report content.");
}

$ERRORS->Check('/index.php?page=bugtracker');

//check if the news record exists
$res = $DB->prepare("SELECT id, title, content, priority, status FROM `bugtracker` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $id, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() == 0)
{
	$ERRORS->Add("The report record is missing.");
}
else
{
	$row = $res->fetch();
}
unset($res);

$ERRORS->Check('/index.php?page=bugtracker');

####################################################################
## The actual script begins here
	
	//insert the news record
	$update = $DB->prepare("UPDATE `bugtracker` SET `title` = :title, `content` = :content, `priority` = :priority, `status` = :status WHERE `id` = :id LIMIT 1;");
	$update->bindParam(':title', $title, PDO::PARAM_STR);
	$update->bindParam(':content', $content, PDO::PARAM_STR);
	$update->bindParam(':priority', $priority, PDO::PARAM_INT);
	$update->bindParam(':status', $status, PDO::PARAM_INT);
	$update->bindParam(':id', $row['id'], PDO::PARAM_INT);
	$update->execute();
	
	if ($update->rowCount() < 1)
	{
		$ERRORS->Add("The website failed to update the report.");
	}
	else
	{
		unset($insert);
		$ERRORS->triggerSuccess();
	}
	unset($insert);
	
	$ERRORS->Check('/index.php?page=bugtracker');
	
####################################################################

exit;