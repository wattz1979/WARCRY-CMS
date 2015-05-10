<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

if (!$CURUSER->isOnline())
{
	echo 'You must be logged in!';
	die;
}

$avatarId = isset($_GET['id']) ? (int)$_GET['id'] : false;

if ($avatarId === false)
{
	echo 'You must select an avatar first.';
	die;
}

$storage = new AvatarGallery();

//validate the avatar
$newAvatar = $storage->get($avatarId);

if (!$newAvatar)
{
	echo 'The selected avatar is invalid.';
	die;
}

unset($storage);

//Let's validate the ranking requirements
if ($newAvatar->rank() > $CURUSER->getRank()->int())
{
	echo 'The selected avatar requires greater user rank.';
	die;
}

$update = $DB->prepare("UPDATE `account_data` SET `avatar` = :avatar, `avatarType` = :type WHERE `id` = :account LIMIT 1;");
$update->bindParam(':account', $CURUSER->get('id'), PDO::PARAM_INT);
$update->bindParam(':avatar', $newAvatar->int(), PDO::PARAM_INT);
$update->bindParam(':type', $newAvatar->type(), PDO::PARAM_INT);
$update->execute();

if ($update->rowCount() > 0)
{
	echo 'OK';
}
else
{
	echo 'The website failed to update your avatar.';
}

?>