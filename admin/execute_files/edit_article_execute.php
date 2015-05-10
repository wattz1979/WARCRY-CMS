<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//check for permissions
$CORE->CheckPermissionsExecute(PERMISSION_ARTICLES);

//prepare multi errors
$ERRORS->NewInstance('editArticle');
//bind on success
$ERRORS->onSuccess('The article was successfully edited.', '/index.php?page=articles');

$id = (isset($_POST['id']) ? (int)$_POST['id'] : false);

$title = (isset($_POST['title']) ? $_POST['title'] : false);
$shortText = (isset($_POST['short_text']) ? $_POST['short_text'] : false);
$text = (isset($_POST['text']) ? $_POST['text'] : false);
$image = (isset($_POST['image']) ? $_POST['image'] : false);
$comments = (isset($_POST['comments']) ? '1' : '0');

if (!$id)
{
	$ERRORS->Add("The article id is missing.");
}
if (!$title)
{
	$ERRORS->Add("Please enter article headline.");
}
if (!$shortText)
{
	$ERRORS->Add("Please enter article short text.");
}
if (!$text)
{
	$ERRORS->Add("Please enter article content.");
}

//check if the news record exists
$res = $DB->prepare("SELECT id, image FROM `articles` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $id, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() == 0)
{
	$ERRORS->Add("The article record is missing.");
}
else
{
	$row = $res->fetch();
}
unset($res);

$ERRORS->Check('/index.php?page=edit-article&id='.$id.'');

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
		$moveFolder = $config['RootPath'] . '/uploads/articles';
		
		//Chmod the folder
		//$CORE->ChmodWritable($moveFolder);
		//move the thumb image, if fail set default
		if (!rename($tempFolder. '/' .$image, $moveFolder. '/' .$image))
		{
			$image = '';
		}
		//Chmod the folder back to normal
		//$CORE->ChmodReadonly($moveFolder);
	}
	
	//insert the news record
	$update = $DB->prepare("UPDATE `articles` SET `title` = :title, `short_text` = :short, `text` = :text, `image` = :image, `comments` = :comments WHERE `id` = :id LIMIT 1;");
	$update->bindParam(':title', $title, PDO::PARAM_STR);
	$update->bindParam(':short', $shortText, PDO::PARAM_STR);
	$update->bindParam(':text', $text, PDO::PARAM_STR);
	$update->bindParam(':image', $image, PDO::PARAM_STR);
	$update->bindParam(':comments', $comments, PDO::PARAM_INT);
	$update->bindParam(':id', $row['id'], PDO::PARAM_INT);
	$update->execute();
	
	if ($update->rowCount() < 1)
	{
		$ERRORS->Add("The website failed to update the article record.");
	}
	else
	{
		//We've got to clear the cache
		$CACHE->clear('articles/article_' . $id);
		
		unset($update);
		$ERRORS->triggerSuccess();
	}
	unset($update);
	
####################################################################

$ERRORS->Check('/index.php?page=edit-article&id='.$id.'');

exit;