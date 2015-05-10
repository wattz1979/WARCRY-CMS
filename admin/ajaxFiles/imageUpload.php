<?php
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

if (!$CURUSER->isOnline())
{
	echo '@AjaxError@, <br>You must be logged in.';
	die;
}

if (isset($_POST['checkFile']))
{
	$file = isset($_POST['file']) ? $_POST['file'] : false;
	
	if (file_exists($config['RootPath'] . $file))
	{
		echo 1;
	}
	else
	{
		echo 0;
	}
}
else
{
	$folder = $config['RootPath'] . '/admin/tempUploads/'; //use "/" at the end
	$maxsize = 2097152;
	$maxDemensions = 580;
	
	$error = '@AjaxError@, <br>';
	
	if (isset($_FILES["file"]))
	{
		//All file names should be lower case
		$file_title = strtolower($_FILES["file"]['name']);
		//replace white spaces
		$file_title = str_replace(' ', '_', $file_title);
		//find where the image extension begins and remove it
		$file_title = substr($file_title, 0, strrpos($file_title, '.'));
		
		//Not really uniqe - but for all practical reasons, it is
		$uniqer = substr(md5(uniqid(rand(), 1)), 0, 5);

		//Get Unique Name
		$file_name = $uniqer . "_" . $file_title;
	    
		//check the filesize
		if(filesize($_FILES["file"]['tmp_name']) > $maxsize)
		{
			$error .= 'The file you are uploading is too big, 2mb max.<br>';
		}

		if(!empty($_FILES["file"]['error']))
		{
			switch($_FILES["file"]['error'])
			{
				case '1':
					$error .= 'The uploaded file exceeds the upload_max_filesize directive in php.ini<br>';
					break;
				case '2':
					$error .= 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form<br>';
					break;
				case '3':
					$error .= 'The uploaded file was only partially uploaded<br>';
					break;
				case '4':
					$error .= 'No file was uploaded.<br>';
					break;
				case '6':
					$error .= 'Missing a temporary folder<br>';
					break;
				case '7':
					$error .= 'Failed to write file to disk<br>';
					break;
				case '8':
					$error .= 'File upload stopped by extension<br>';
					break;
				case '999':
				default:
					$error .= 'No error code avaiable<br>';
			}
		}

        list($width, $height, $type, $attr) = getimagesize($_FILES["file"]['tmp_name']);
        $mime = image_type_to_mime_type($type);
        		
		//if mime type is not allowed, return error
        if(($mime != "image/jpeg") && ($mime != "image/pjpeg") && ($mime != "image/jpg") && ($mime != "image/png") && ($mime != "image/gif"))
		{
			$error .= 'File Type not allowed.<br>';
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
		
		//Chmod the folder
		@chmod($folder, 0777);
			
		$uploadfile = $folder . $file_name;
		
		//if we got no errors
		if ($error == '@AjaxError@, <br>')
		{
			//Move the file from the stored location to the new location
			if (!move_uploaded_file($_FILES["file"]['tmp_name'], $uploadfile))
			{
				$error .= "Cannot upload the file '".$uploadfile."'"; //Show error if any.
			
				if(!file_exists($folder))
				{
					$error .= " : Folder don't exist.";
				}
				elseif(!is_writable($folder))
				{
					$error .= " : Folder not writable.";
				}
				elseif(!is_writable($uploadfile))
				{
					$error .= " : File not writable.";
				}			
			}
			else
			{
				//if ($width > $maxDemensions or $height > $maxDemensions)
				//{
				//	$objImage = new ImageManipulation($uploadfile);
 				//	if ($objImage->imageok)
				//	{
				//		$objImage->setJpegQuality(100);
  				//		$objImage->resize($maxDemensions);
				//		$file_name = 'resized_' . $file_name;
  				//		$objImage->save($folder . $file_name);
 				//	}
				//}
				//upload success
				echo $file_name;
				//null the errors
				$error = '';
			}
		}

		//Chmod the folder back to normal
		@chmod($folder, 0755);
		
		if ($error != '')
		{
			echo $error;
		}
		
	}
}