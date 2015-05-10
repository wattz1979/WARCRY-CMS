<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//check for permissions
$CORE->CheckPermissionsExecute(PERMISSION_MEDIA_MOVIES);

//prepare multi errors
$ERRORS->NewInstance('add_movie');
//bind on success
$ERRORS->onSuccess('The movie was successfully added.', '/index.php?page=media');

$title = (isset($_POST['name']) ? $_POST['name'] : false);
$youtube = (isset($_POST['youtube']) ? $_POST['youtube'] : '');
$text = (isset($_POST['text']) ? $_POST['text'] : false);
$short_text = (isset($_POST['short_text']) ? $_POST['short_text'] : false);
$image = (isset($_POST['image']) ? $_POST['image'] : false);
$movies = (isset($_POST['movies']) ? $_POST['movies'] : false);

if (!$title)
{
    $ERRORS->Add("Please enter movie title.");
}
if (!$short_text)
{
	$ERRORS->Add("Please enter movie short description.");
}
if (!$text)
{
    $ERRORS->Add("Please enter movie description.");
}
if (!$image || $image == '')
{
    $ERRORS->Add("Please upload movie thumbnail.");
}
if (!$movies || $movies == '')
{
    $ERRORS->Add("Please upload atleast 1 movie.");
}

$ERRORS->Check('/index.php?page=movie-add');

####################################################################
## The actual script begins here
	
	$uniqer = substr(md5(uniqid(rand(), 1)), 0, 5);
	//Define the temp upload folder
	$tempFolder = $config['RootPath'] . '/admin/tempUploads';
	
	//Let's start by creating a folder for the movie
	$MovieFolder = preg_replace('/[^A-Za-z0-9-_]/', '', $title) . '_' . $uniqer;
	$DirName = $MovieFolder;
	
	//append the full path
	$MovieFolder = $config['RootPath'] . '/uploads/media/movies/' . $MovieFolder;
	
	//Create the movie directory
	if (!mkdir($MovieFolder, 0755, true))
	{
		$ERRORS->Add("The website was not able to create new directory for the movie.");
	}
	
	$ImageFolder = $MovieFolder . '/thumbnails';
	//Create a folder for the images aswell
	mkdir($ImageFolder, 0755, true);
	
	//Let's start creating diferent size thumbs
	$objImage = new ImageManipulation($tempFolder . '/' . $image);
	
	//Verify the image
	if ($objImage->imageok)
	{
		$objImage->setJpegQuality(100);
		
		//Start by making the default size image, no resize
		$objImage->save($ImageFolder . '/' . $image);
		
		//Index image 401x227
		$objImage->resize(401);
		$objImage->save($ImageFolder . '/index_' . $image);
		
		//Medium image 255x145
		$objImage->resize(255);
		$objImage->save($ImageFolder . '/medium_' . $image);
		
		//Small image 200x113
		$objImage->resize(200);
		$objImage->save($ImageFolder . '/small_' . $image);
		
		//delete the temp
		@unlink($tempFolder . '/' . $image);
	}
	else
	{
		$ERRORS->Add("The uploaded thumbnail seems to be invalid.");
	}
	unset($objImage);
	
	$ERRORS->Check('/index.php?page=movie-add');
	
	//Check if we have more then one movie
	if (strstr($movies, '|'))
	{
		//split the movies into array
		$movies = explode('|', $movies);
	}
	else
	{
		//create single record array
		$movies = array($movies);
	}
	
	//Time to define our movie format variables
	$movieMP4 = '';
	$movieWEBM = '';
	$movieOGG = '';
	
	//Move the movies to their new home
	foreach ($movies as $movie)
	{
		//get the extension
		$ext = pathinfo($movie, PATHINFO_EXTENSION);
		
		//Save the movie by extension into the variables
		switch ($ext)
		{
			case 'mp4': $movieMP4 = $movie; break;
			case 'webm': $movieWEBM = $movie; break;
			case 'ogg': $movieOGG = $movie; break;
		}
		
		if (!rename($tempFolder . '/' . $movie, $MovieFolder. '/' . $movie))
		{
			$ERRORS->Add("The website failed to move the movies into new folder.");
		}
	}
	
	$ERRORS->Check('/index.php?page=movie-add');

	//insert the movie record
	$insert = $DB->prepare("INSERT INTO `movies` (`name`, `descr`, `short_text`, `added`, `account`, `dirname`, `image`, `mp4`, `webm`, `ogg`, `youtube`, `status`) VALUES (:title, :text, :short_text, :added, :acc, :dirname, :image, :mp4, :webm, :ogg, :youtube, '1');");
	$insert->bindParam(':title', $title, PDO::PARAM_STR);
	$insert->bindParam(':text', $text, PDO::PARAM_STR);
	$insert->bindParam(':short_text', $short_text, PDO::PARAM_STR);
	$insert->bindParam(':added', $CORE->getTime(), PDO::PARAM_STR);
	$insert->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
	$insert->bindParam(':dirname', $DirName, PDO::PARAM_STR);
	$insert->bindParam(':image', $image, PDO::PARAM_STR);
	$insert->bindParam(':mp4', $movieMP4, PDO::PARAM_STR);
	$insert->bindParam(':webm', $movieWEBM, PDO::PARAM_STR);
	$insert->bindParam(':ogg', $movieOGG, PDO::PARAM_STR);
	$insert->bindParam(':youtube', $youtube, PDO::PARAM_STR);
	$insert->execute();
	
	if ($insert->rowCount() < 1)
	{
		$ERRORS->Add("The website failed to insert the movie record.");
	}
	else
	{
		unset($insert);
		$ERRORS->triggerSuccess();
	}
	unset($insert);
	
####################################################################

$ERRORS->Check('/index.php?page=movie-add');

exit;