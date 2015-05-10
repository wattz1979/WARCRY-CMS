<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

$action = (isset($_GET['action']) ? $_GET['action'] : false);
$id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

if (!$action)
{
	echo 'Please define action.';
}
else if ($action == 'news') //action news
{
	//check for permissions
	$CORE->CheckPermissionsExecute(PERMISSION_NEWS);
	
	//prepare multi errors
	$ERRORS->NewInstance('deleteNews');
	//bind on success
	$ERRORS->onSuccess('The news ware successfully deleted.', '/index.php?page=news');
	
	if (!$id)
	{
		$ERRORS->Add("The news id is missing.");
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

	$ERRORS->Check('/index.php?page=news');
	
	####################################################################
	## The actual script begins here
		
		//delete the news image
		$folder = $config['RootPath'] . '/uploads/news/icons';
		//Chmod the folder
		//$CORE->ChmodWritable($folder);
		//delete the image if it's not default
		if (file_exists($folder . '/'. $row['image']) and $row['image'] != 'default.jpg')
		{
			unlink($folder . '/'. $row['image']);
		}
		//Chmod the folder back to normal
		//$CORE->ChmodReadonly($folder);
		
		$delete = $DB->prepare('DELETE FROM `news` WHERE `id` = :id LIMIT 1;');
		$delete->bindParam(':id', $id, PDO::PARAM_INT);
		$delete->execute();
		
		if ($delete->rowCount() < 1)
		{
			$ERRORS->Add("The website failed to delete the news record.");
		}
		else
		{
			unset($delete);
			$ERRORS->triggerSuccess();
		}
		unset($delete);
		
	####################################################################
	
	$ERRORS->Check('/index.php?page=news');
}
else if ($action == 'article')
{
	//check for permissions
	$CORE->CheckPermissionsExecute(PERMISSION_ARTICLES);

	//prepare multi errors
	$ERRORS->NewInstance('del_article');
	//bind on success
	$ERRORS->onSuccess('The article was successfully deleted.', '/index.php?page=articles');
	
	if (!$id)
	{
		$ERRORS->Add("The news id is missing.");
	}
	//check if the news record exists
	$res = $DB->prepare("SELECT id, image FROM `articles` WHERE `id` = :id LIMIT 1;");
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

	$ERRORS->Check('/index.php?page=articles');
	
	####################################################################
	## The actual script begins here
		
		//delete the article image
		$folder = $config['RootPath'] . '/uploads/articles';
		//Chmod the folder
		//$CORE->ChmodWritable($folder);
		//delete the image if it's not default
		if (file_exists($folder . '/'. $row['image']) and $row['image'] != '')
		{
			unlink($folder . '/'. $row['image']);
		}
		//Chmod the folder back to normal
		//$CORE->ChmodReadonly($folder);
		
		$delete = $DB->prepare('DELETE FROM `articles` WHERE `id` = :id LIMIT 1;');
		$delete->bindParam(':id', $id, PDO::PARAM_INT);
		$delete->execute();
		
		if ($delete->rowCount() < 1)
		{
			$ERRORS->Add("The website failed to delete the article record.");
		}
		else
		{
			unset($delete);
			
			//Delete the comments aswell
			$del = $DB->prepare("DELETE FROM `article_comments` WHERE `article` = :id;");
			$del->bindParam(':id', $id, PDO::PARAM_INT);
			$del->execute();
			
			//redirect
			$ERRORS->triggerSuccess();
		}
		unset($delete);
		
	####################################################################
	
	$ERRORS->Check('/index.php?page=articles');
	
}
else if ($action == 'movie')
{
	//check for permissions
	$CORE->CheckPermissionsExecute(PERMISSION_MEDIA_MOVIES);
	
	//prepare multi errors
	$ERRORS->NewInstance('delete_movie');
	//bind on success
	$ERRORS->onSuccess('The movie was successfully deleted.', '/index.php?page=media');
	
	if (!$id)
	{
		$ERRORS->Add("The movie id is missing.");
	}
	
	//check if the news record exists
	$res = $DB->prepare("SELECT id, dirname FROM `movies` WHERE `id` = :id LIMIT 1;");
	$res->bindParam(':id', $id, PDO::PARAM_INT);
	$res->execute();
	
	if ($res->rowCount() == 0)
	{
		$ERRORS->Add("The movie record is missing.");
	}
	else
	{
		$row = $res->fetch();
	}
	unset($res);
	
	$ERRORS->Check('/index.php?page=media');
	
	####################################################################
	## The actual script begins here
	
		//delete the whole movie folder
		$folder = $config['RootPath'] . '/uploads/media/movies/' . $row['dirname'];
		
		//Delete the record
		$delete = $DB->prepare('DELETE FROM `movies` WHERE `id` = :id LIMIT 1;');
		$delete->bindParam(':id', $id, PDO::PARAM_INT);
		$delete->execute();
		
		if ($delete->rowCount() < 1)
		{
			$ERRORS->Add("The website failed to delete the movie record.");
		}
		else
		{
			unset($delete);
			
			//delete the folder
			$CORE->RecursiveRemoveDirectory($folder);
			
			//redirect
			$ERRORS->triggerSuccess();
		}
		unset($delete);
	
	####################################################################
	
	$ERRORS->Check('/index.php?page=media');
}
else if ($action == 'armorsets_category')
{
	//check for permissions
	$CORE->CheckPermissionsExecute(PERMISSION_PSTORE);

	//prepare multi errors
	$ERRORS->NewInstance('pstore_armorsets_delcat');
	//bind on success
	$ERRORS->onSuccess('The category was successfully deleted.', '/index.php?page=pstore&switchTab=2');
	
	if (!$id)
	{
		$ERRORS->Add("The category id is missing.");
	}

	$ERRORS->Check('/index.php?page=pstore&switchTab=2');
	
	####################################################################
	## The actual script begins here
		
		$delete = $DB->prepare('DELETE FROM `armorset_categories` WHERE `id` = :id LIMIT 1;');
		$delete->bindParam(':id', $id, PDO::PARAM_INT);
		$delete->execute();
		
		if ($delete->rowCount() < 1)
		{
			$ERRORS->Add("The website failed to delete the category record.");
		}
		else
		{
			unset($delete);
			$ERRORS->triggerSuccess();
		}
		unset($delete);
		
	####################################################################
	
	$ERRORS->Check('/index.php?page=pstore&switchTab=2');
}
else if ($action == 'pcode')
{
	//check for permissions
	$CORE->CheckPermissionsExecute(PERMISSION_PROMO_CODES);

	//prepare multi errors
	$ERRORS->NewInstance('pcode_delete');
	//bind on success
	$ERRORS->onSuccess('The code was successfully deleted.', '/index.php?page=pcodes');
	
	if (!$id)
	{
		$ERRORS->Add("The promo code id is missing.");
	}

	$ERRORS->Check('/index.php?page=pcodes');
	
	####################################################################
	## The actual script begins here
		
		$delete = $DB->prepare('DELETE FROM `promo_codes` WHERE `id` = :id LIMIT 1;');
		$delete->bindParam(':id', $id, PDO::PARAM_INT);
		$delete->execute();
		
		if ($delete->rowCount() < 1)
		{
			$ERRORS->Add("The website failed to delete the promo code.");
		}
		else
		{
			unset($delete);
			$ERRORS->triggerSuccess();
		}
		unset($delete);
		
	####################################################################
	
	$ERRORS->Check('/index.php?page=pcodes');
}
else if ($action == 'forum_category')
{
	//check for permissions
	$CORE->CheckPermissionsExecute(PERMISSION_FORUM_CATS);
	
	//prepare multi errors
	$ERRORS->NewInstance('forums_delcat');
	//bind on success
	$ERRORS->onSuccess('The category was successfully deleted.', '/index.php?page=forum-cats');
	
	if (!$id)
	{
		$ERRORS->Add("The category id is missing.");
	}

	$ERRORS->Check('/index.php?page=forum-cats');
	
	####################################################################
	## The actual script begins here
		
		$delete = $DB->prepare('DELETE FROM `wcf_categories` WHERE `id` = :id LIMIT 1;');
		$delete->bindParam(':id', $id, PDO::PARAM_INT);
		$delete->execute();
		
		if ($delete->rowCount() < 1)
		{
			$ERRORS->Add("The website failed to delete the category record.");
		}
		else
		{
			unset($delete);
			$ERRORS->triggerSuccess();
		}
		unset($delete);
		
	####################################################################
	
	$ERRORS->Check('/index.php?page=forum-cats');
}
else if ($action == 'armorset')
{
	//check for permissions
	$CORE->CheckPermissionsExecute(PERMISSION_PSTORE);
	
	//prepare multi errors
	$ERRORS->NewInstance('pstore_armorsets_del');
	//bind on success
	$ERRORS->onSuccess('The armor set was successfully deleted.', '/index.php?page=pstore');
	
	if (!$id)
	{
		$ERRORS->Add("The armor set id is missing.");
	}

	$ERRORS->Check('/index.php?page=pstore');
	
	####################################################################
	## The actual script begins here
		
		$delete = $DB->prepare('DELETE FROM `armorsets` WHERE `id` = :id LIMIT 1;');
		$delete->bindParam(':id', $id, PDO::PARAM_INT);
		$delete->execute();
		
		if ($delete->rowCount() < 1)
		{
			$ERRORS->Add("The website failed to delete the armor set record.");
		}
		else
		{
			unset($delete);
			$ERRORS->triggerSuccess();
		}
		unset($delete);
		
	####################################################################
	
	$ERRORS->Check('/index.php?page=pstore');
}
else if ($action == 'bugreport')
{
	//check for permissions
	$CORE->CheckPermissionsExecute(PERMISSION_MAN_BUGTRACKER);
	
	//prepare multi errors
	$ERRORS->NewInstance('delete_report');
	//bind on success
	$ERRORS->onSuccess('The report was successfully deleted.', '/index.php?page=bugtracker');

	if (!$id)
	{
		$ERRORS->Add("The report id is missing.");
	}
	
	$ERRORS->Check('/index.php?page=bugtracker');
	
	####################################################################
	## The actual script begins here
		
		$delete = $DB->prepare('DELETE FROM `bugtracker` WHERE `id` = :id LIMIT 1;');
		$delete->bindParam(':id', $id, PDO::PARAM_INT);
		$delete->execute();
		
		if ($delete->rowCount() < 1)
		{
			$ERRORS->Add("The website failed to delete the report record.");
		}
		else
		{
			unset($delete);
			$ERRORS->triggerSuccess();
		}
		unset($delete);
		
	####################################################################
	
	$ERRORS->Check('/index.php?page=bugtracker');	
}
else if ($action == 'screenshot') //action news
{
	//check for permissions
	$CORE->CheckPermissionsExecute(PERMISSION_MEDIA_SREENSHOTS);
	
	//prepare multi errors
	$ERRORS->NewInstance('deleteScreenshot');
	//bind on success
	$ERRORS->onSuccess('The screenshot was successfully deleted.', '/index.php?page=screenshots');
	
	if (!$id)
	{
		$ERRORS->Add("The screenshot id is missing.");
	}
	//check if the news record exists
	$res = $DB->prepare("SELECT id, image FROM `images` WHERE `id` = :id LIMIT 1;");
	$res->bindParam(':id', $id, PDO::PARAM_INT);
	$res->execute();
	
	if ($res->rowCount() == 0)
	{
		$ERRORS->Add("The screenshot record is missing.");
	}
	else
	{
		$row = $res->fetch();
	}
	unset($res);

	$ERRORS->Check('/index.php?page=screenshots');
	
	####################################################################
	## The actual script begins here
		
		//delete the news image
		$folder = $config['RootPath'] . '/uploads/media/screenshots';
		//Chmod the folder
		//$CORE->ChmodWritable($folder);
		//delete the image if it's not default
		if (file_exists($folder . '/'. $row['image']))
		{
			unlink($folder . '/'. $row['image']);
		}
		//Chmod the folder back to normal
		//$CORE->ChmodReadonly($folder);
		
		$delete = $DB->prepare('DELETE FROM `images` WHERE `id` = :id LIMIT 1;');
		$delete->bindParam(':id', $id, PDO::PARAM_INT);
		$delete->execute();
		
		if ($delete->rowCount() < 1)
		{
			$ERRORS->Add("The website failed to delete the screenshot record.");
		}
		else
		{
			unset($delete);
			$ERRORS->triggerSuccess();
		}
		unset($delete);
		
	####################################################################
	
	$ERRORS->Check('/index.php?page=screenshots');
}
else
{
	echo 'Invalid Action.';
}

exit;