<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

if (!$CURUSER->isOnline())
{
	echo 'Invalid user!';
	die;
}

//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_STORE))
{
	echo 'You do not have the required permissions.';
	die;
}

$id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

if (!$id)
{
	echo 'Please select an item first!';
	die;
}

//Validate the item
$res = $DB->prepare("SELECT * FROM `store_items` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $id, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() == 0)
{
	echo 'The selected item is invalid or missing.';
	die;
}
unset($res);

$delete = $DB->prepare("DELETE FROM `store_items` WHERE `id` = :id LIMIT 1;");
$delete->bindParam(':id', $id, PDO::PARAM_INT);
$delete->execute();

if ($delete->rowCount() == 0)
{
	echo 'The website was unable to delete the item.';
	die;
}
unset($delete);

echo 'OK';

exit;