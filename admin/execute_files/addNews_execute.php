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
$ERRORS->NewInstance('addNews');
//bind on success
$ERRORS->onSuccess('The news ware successfully posted.', '/index.php?page=news');

$title = (isset($_POST['title']) ? $_POST['title'] : false);
$shortText = (isset($_POST['shortText']) ? $_POST['shortText'] : false);
$text = (isset($_POST['text']) ? $_POST['text'] : false);
$image = (isset($_POST['image']) ? $_POST['image'] : false);

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

$ERRORS->Check('/index.php?page=news-post');

####################################################################
## The actual script begins here
	
	//check if we got icon uploaded
	if (!$image or $image == '')
	{
		$image = 'default.png';
	}
	else
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
	$insert = $DB->prepare("INSERT INTO `news` (`title`, `shortText`, `text`, `image`, `added`, `author`, `authorStr`) VALUES (:title, :short, :text, :image, :added, :author, :authorStr);");
	$insert->bindParam(':title', $title, PDO::PARAM_STR);
	$insert->bindParam(':short', $shortText, PDO::PARAM_STR);
	$insert->bindParam(':text', $text, PDO::PARAM_STR);
	$insert->bindParam(':image', $image, PDO::PARAM_STR);
	$insert->bindParam(':added', $CORE->getTime(), PDO::PARAM_STR);
	$insert->bindParam(':author', $CURUSER->get('id'), PDO::PARAM_INT);
	$insert->bindParam(':authorStr', $CURUSER->get('displayName'), PDO::PARAM_STR);
	$insert->execute();
	
	if ($insert->rowCount() < 1)
	{
		$ERRORS->Add("The website failed to insert the news record.");
	}
	else
	{
		unset($insert);
		$ERRORS->triggerSuccess();
	}
	unset($insert);
	
####################################################################

$ERRORS->Check('/index.php?page=news-post');

exit;