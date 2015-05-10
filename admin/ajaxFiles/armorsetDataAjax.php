<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

if (!$CURUSER->isOnline())
{
	echo json_encode(array('error' => 'You must be logged in.'));
	die;
}

//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_PSTORE))
{
	echo 'You dont have the required permissions.';
	die;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : false;

if ($id)
{
	$res = $DB->prepare("SELECT * FROM `armorsets` WHERE `id` = :id LIMIT 1;");
	$res->bindParam(':id', $id, PDO::PARAM_INT);
	$res->execute();
	
	if ($res->rowCount() > 0)
	{
		$data = $res->fetch();
	}
	else
	{
		$data = array('error' => 'No record was found.');
	}
	unset($res);
}
else
{
	$data = array('error' => 'Missing id.');
}

echo json_encode($data);

exit;