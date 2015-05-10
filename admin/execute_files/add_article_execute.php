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
$ERRORS->NewInstance('addArticle');
//bind on success
$ERRORS->onSuccess('The article was successfully added.', '/index.php?page=articles');

$title = (isset($_POST['title']) ? $_POST['title'] : false);
$shortText = (isset($_POST['short_text']) ? $_POST['short_text'] : false);
$text = (isset($_POST['text']) ? $_POST['text'] : false);
$comments = (isset($_POST['comments']) ? 1 : 0);
$image = (isset($_POST['image']) ? $_POST['image'] : '');

if (!$title)
{
	$ERRORS->Add("Please enter article headline.");
}
else if (strlen($title) > 250)
{
	$ERRORS->Add("The article headline is too long, 250 characters max.");
}
if (!$text)
{
	$ERRORS->Add("Please enter article content.");
}

$ERRORS->Check('/index.php?page=new-article');

####################################################################
## The actual script begins here
	
	//check if we got icon uploaded
	if ($image && $image != '')
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
	
	//Get the time
	$time = $CORE->getTime();
	
	//insert the news record
	$insert = $DB->prepare("INSERT INTO `articles` (`title`, `short_text`, `text`, `comments`, `added`, `author`, `image`) VALUES (:title, :short_text, :text, :comments, :time, :acc, :image);");
	$insert->bindParam(':title', $title, PDO::PARAM_STR);
	$insert->bindParam(':short_text', $shortText, PDO::PARAM_STR);
	$insert->bindParam(':text', $text, PDO::PARAM_STR);
	$insert->bindParam(':comments', $comments, PDO::PARAM_INT);
	$insert->bindParam(':time', $time, PDO::PARAM_STR);
	$insert->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
	$insert->bindParam(':image', $image, PDO::PARAM_STR);
	$insert->execute();
	
	if ($insert->rowCount() < 1)
	{
		$ERRORS->Add("The website failed to insert the article record.");
	}
	else
	{
		unset($insert);
		$ERRORS->triggerSuccess();
	}
	unset($insert);
	
####################################################################

$ERRORS->Check('/index.php?page=new-article');

exit;