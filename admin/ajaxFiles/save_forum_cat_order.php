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
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_FORUM_CATS))
{
	echo 'You do not have the required permissions.';
	die;
}

$order = (isset($_POST['order']) ? $_POST['order'] : false);

if (!$order)
{
	echo 'The order list is missing.';
	die;
}

foreach ($order as $position => $id)
{
	//insert the news record
	$update = $DB->prepare("UPDATE `wcf_categories` SET `position` = :pos WHERE `id` = :id LIMIT 1;");
	$update->bindParam(':pos', $position, PDO::PARAM_INT);
	$update->bindParam(':id', $id, PDO::PARAM_INT);
	$update->execute();
	unset($update);
}

echo 'OK';

exit;