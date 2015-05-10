<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//check for permissions
$CORE->CheckPermissionsExecute(PERMISSION_NEWS);

//prepare multi errors
$ERRORS->NewInstance('editNews');
//bind on success
$ERRORS->onSuccess('The news ware successfully edited.', '/index.php?page=news');

$id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

$title = (isset($_POST['title']) ? $_POST['title'] : false);
$shortText = (isset($_POST['shortText']) ? $_POST['shortText'] : false);
$text = (isset($_POST['text']) ? $_POST['text'] : false);
$image = (isset($_POST['image']) ? $_POST['image'] : false);

if (!$id)
{
	$ERRORS->Add("The news id is missing.");
}
if (!$title)
{
	$ERRORS->Add("Please enter news headline.");
}
if (!$shortText)
{
	$ERRORS->Add("Please enter news short text.");
}
if (!$text)
{
	$ERRORS->Add("Please enter news content.");
}

//check if the news record exists
$res = $DB->prepare("SELECT id, image FROM `news` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $id, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() == 0)
{
	$ERRORS->Add("The news record is missing.");
}
else
{
	$row = $res->fetch();
}
unset($res);

$ERRORS->Check('/index.php?page=news-edit&id='.$id);

####################################################################
## The actual script begins here
	
	//check if we got icon uploaded
	if (!$image or $image == '')
	{
		$image = $row['image'];
	}
	else if ($row['image'] != $image)
	{
		//try moving the icon
		$tempFolder = $config['RootPath'] . '/admin/tempUploads';
		$moveFolder = $config['RootPath'] . '/uploads/news/thumbs';
		
		//Chmod the folder
		//$CORE->ChmodWritable($moveFolder);
		//move the thumb image, if fail set default
		if (!rename($tempFolder. '/' .$image, $moveFolder. '/' .$image))
		{
			$image = 'default.png';
		}
		//Chmod the folder back to normal
		//$CORE->ChmodReadonly($moveFolder);
	}
	
	//insert the news record
	$update = $DB->prepare("UPDATE `news` SET `title` = :title, `shortText` = :short, `text` = :text, `image` = :image WHERE `id` = :id LIMIT 1;");
	$update->bindParam(':title', $title, PDO::PARAM_STR);
	$update->bindParam(':short', $shortText, PDO::PARAM_STR);
	$update->bindParam(':text', $text, PDO::PARAM_STR);
	$update->bindParam(':image', $image, PDO::PARAM_STR);
	$update->bindParam(':id', $row['id'], PDO::PARAM_INT);
	$update->execute();
	
	if ($update->rowCount() < 1)
	{
		$ERRORS->Add("The website failed to update the news record.");
	}
	else
	{
		//We've got to clear the cache
		$CACHE->clear('news/news_' . $row['id']);
		
		unset($insert);
		$ERRORS->triggerSuccess();
	}
	unset($insert);
	
####################################################################

$ERRORS->Check('/index.php?page=news-edit&id='.$id);

exit;