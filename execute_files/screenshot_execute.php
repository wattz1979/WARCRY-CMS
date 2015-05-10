<?php
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

################################################
########## EXIF plugin required ################
################################################

$CORE->loggedInOrReturn();
$CORE->load_CoreModule('img.manipulation');
//prepare multi errors
$ERRORS->NewInstance('screenshots');
//bind the onsuccess message
$ERRORS->onSuccess('The screenshot was successfully uploaded.', '/index.php?page=upload-screanshot');

//information
$file_path = $config['RootPath'] . '/uploads/media/screenshots';
$thumb_folder = '/thumbs';
//thumb size
$thumb_width = 200;
$thumb_height = 114;
//post data
$title = isset($_POST['title']) ? $_POST['title'] : false;
$descr = isset($_POST['descr']) ? $_POST['descr'] : false;

//allowed types
$allowedTypes = array('image/jpeg', 'image/pjpeg', 'image/jpg', 'image/png', 'image/gif');

//do we have an title
if (!$title)
{
	$ERRORS->Add("Please fill in the title field.");
}
//check if we've got any image at all
if (!isset($_FILES['file']))
{
	$ERRORS->Add("Please select an screenshot to upload.");
}
//do we have description
if (!$descr)
{
	$ERRORS->Add("Please fill in the description field.");
}
	
//temp file
$tempFile = $_FILES['file']['tmp_name'];	

//get the image type
$imageType = exif_imagetype($tempFile);
//get the mime type
$mime = image_type_to_mime_type($imageType);

//if mime type is not allowed, return error
if (!in_array($mime, $allowedTypes))
{
	$ERRORS->Add("File Type not allowed.");
}
else
//check the $_FILES file type aswell
if (!in_array($_FILES['file']['type'], $allowedTypes))
{
	$ERRORS->Add("File Type not allowed.");
}

//check for errors
$ERRORS->Check('/index.php?page=upload-screanshot');

#################################################
#### FILE NAME HANDLING ########################
	
	//get some info about the file name
	$fileInfo = pathinfo($_FILES['file']['name']);
	//get the image name
	$imageName = $fileInfo['filename'];
	//replace white spaces
	$imageName = str_replace(' ', '_', $imageName);
	//replace any PHP extension
	$imageName = str_replace(array('php', 'php3', 'php4', 'php5', 'phtml'), '', $imageName);
	//add timestamp to the image name
	$imageName = $imageName . '_' . time();

	//appply the extension of the image
	switch($mime)
	{
		case 'image/jpeg':
			$imageName .= '.jpg';
			break;
		case 'image/pjpeg':
			$imageName .= '.jpg';
			break;
		case 'image/jpg':
			$imageName .= '.jpg';
			break;
		case 'image/png':
			$imageName .= '.png';
			break;
		case 'image/gif':
			$imageName .= '.gif';
			break;
		default:
			$imageName .= '.jpg';
			break;
	}
	
//apply the file path
$file_src_new = $file_path . '/' . $imageName;
//thumb
$file_src_new_thumb = $file_path . $thumb_folder . '/' . $imageName;

//Chmod the folder
//$CORE->ChmodWritable($file_path);
//$CORE->ChmodWritable($file_path . $thumb_folder);

//handle the upload
if (move_uploaded_file($tempFile, $file_src_new))
{
	//try deleting the temp file
    @unlink($tempFile);
}
else
{
	$ERRORS->Add("The website failed to upload your screenshot. If this problem presists please contact the administration.");
}

//resample the image
$objImage = new ImageManipulation($file_src_new);

if ($objImage->imageok)
{
	//resample the image
	$objImage->setJpegQuality(100);
  	$objImage->save($file_src_new);
	//make a thumb
	$objImage->resizeProper($thumb_width, $thumb_height);
  	$objImage->save($file_src_new_thumb);
	
	//get the time
	$time = $CORE->getTime();
	$type = TYPE_SCREENSHOT;
	$status = SCREENSHOT_STATUS_PENDING;
	
	//insert into the database
	$insert = $DB->prepare("INSERT INTO `images` (`name`, `descr`, `added`, `account`, `image`, `type`, `status`) VALUES (:name, :descr, :added, :account, :image, :type, :status);");
	$insert->bindParam(':name', $title, PDO::PARAM_STR);
	$insert->bindParam(':descr', $descr, PDO::PARAM_STR);
	$insert->bindParam(':added', $time, PDO::PARAM_STR);
	$insert->bindParam(':account', $CURUSER->get('id'), PDO::PARAM_INT);
	$insert->bindParam(':image', $imageName, PDO::PARAM_STR);
	$insert->bindParam(':type', $type, PDO::PARAM_INT);
	$insert->bindParam(':status', $status, PDO::PARAM_INT);
	$insert->execute();
	
	if ($insert->rowCount() == 0)
	{
		$ERRORS->Add("The website failed to save your screenshot. If this problem presists please contact the administration.");
	}
	
	unset($insert);
	unset($objImage);
}
else
{
	$ERRORS->Add("The image file is invalid. If this problem presists please contact the administration.");
}

//Chmod the folder back to normal
//$CORE->ChmodReadonly($file_path);
//$CORE->ChmodReadonly($file_path . $thumb_folder);

//check for errors
$ERRORS->Check('/index.php?page=upload-screanshot');

//screenshot upload successfull
$ERRORS->triggerSuccess();

exit;