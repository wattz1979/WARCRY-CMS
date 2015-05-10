<?php
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

if (!$CURUSER->isOnline())
{
  	echo '@AjaxError@Must be logged in.';
	die;
}

//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_NEWS))
{
	echo 'You dont have the required permissions.';
	die;
}

//information
$file_path = isset($_POST['path']) ? $config['RootPath'] . $_POST['path'] : false;
$img_name = isset($_POST['imgName']) ? $_POST['imgName'] : false;
$resize = isset($_POST['resize']) ? (int)$_POST['resize'] : false;

if (!$file_path)
{
  	echo '@AjaxError@File path is missing. Please contact the administration.';
	die;
}
if (!$img_name)
{
  	echo '@AjaxError@Image name is missing. Please contact the administration.';
	die;
}

//temp src
$file_src = $file_path.'/'.$img_name;	

//replace white spaces
$file_name = str_replace(' ', '_', $img_name);
//find where the image extension begins and remove it
$file_name = substr($file_name, 0, strrpos($file_name, '.'));
//add cropped str
$file_name = $file_name . '_cropped';

$imageInfo = getimagesize($file_src);

$mime = image_type_to_mime_type($imageInfo[2]);
    
//if mime type is not allowed, return error
if(($mime != "image/jpeg") && ($mime != "image/pjpeg") && ($mime != "image/jpg") && ($mime != "image/png") && ($mime != "image/gif"))
{
	echo 'File Type not allowed.';
	die;
}

//appply the extension of the image
switch($mime)
{
	case 'image/jpeg':
		$file_name = $file_name . '.jpg';
		break;
	case 'image/pjpeg':
		$file_name = $file_name . '.jpg';
		break;
	case 'image/jpg':
		$file_name = $file_name . '.jpg';
		break;
	case 'image/png':
		$file_name = $file_name . '.png';
		break;
	case 'image/gif':
		$file_name = $file_name . '.gif';
		break;
	default:
		$file_name = $file_name . '.jpg';
		break;
}

//apply the file path
$file_src_new = $file_path . '/' . $file_name;

//Chmod the folder
//$CORE->ChmodWritable($file_path);

//we've got no error
$error = false;

$objImage = new ImageManipulation($file_src);
if ($objImage->imageok)
{
	$objImage->setJpegQuality(100);
	$objImage->setCrop($_POST['x'], $_POST['y'], $_POST['w'], $_POST['h']);
	if ($resize)
	{
		if ($imageInfo[0] > $resize or $imageInfo[0] < $resize)
		{
			$objImage->resize($resize);
		}
	}
	//$objImage->show();
  	$objImage->save($file_src_new);
	
	@unlink($file_src);
}
else
{
  	$error = '@AjaxError@Epic Fail. Please contact the administration.';
}

//check for website failure
if ($error)
{
	echo $error;
	die;
}

//Chmod the folder back to normal
//$CORE->ChmodReadonly($file_path);

echo $file_name;

exit;