<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//prepare multi errors
$ERRORS->NewInstance('submit_bug');
//bind the onsuccess message
$ERRORS->onSuccess('Your bug report has been successfully submitted.', '/index.php?page=bugtracker');

$title = (isset($_POST['title']) ? $_POST['title'] : false);
$text = (isset($_POST['text']) ? $_POST['text'] : false);

$priority = (isset($_POST['prio']) ? (int)$_POST['prio'] : 1);

$mainCategory = (isset($_POST['mainCategory']) ? (int)$_POST['mainCategory'] : false);
$category = (isset($_POST['category']) ? (int)$_POST['category'] : false);
$subcategory = (isset($_POST['subcategory']) ? (int)$_POST['subcategory'] : false);

//define the valid categories
$validMainCategories = array(BT_CAT_WEBSITE, BT_CAT_WOTLK_CORE);

if (!$title)
{
	$ERRORS->Add("Please enter report title.");
}
else if (strlen($title) > 250)
{
	$ERRORS->Add("The title is too long. 250 characters maximum.");	
}
else if (str_word_count($title) < 2)
{
	$ERRORS->Add("The title is too short. 2 words minimum.");	
}
if (!$text)
{
	$ERRORS->Add("Please describe the bug as much detail as possible.");
}
if (!$category)
{
	$ERRORS->Add("Please select a category.");
}
else
//validate the category
if (!in_array($mainCategory, $validMainCategories))
{
	$ERRORS->Add("Please select valid category.");
}

$ERRORS->Check('/index.php?page=bugtracker_submit');

####################################################################
## The actual unstuck script begins here
	
	$CategoryStore = new BTCategories();
	$CategoryData = $CategoryStore->getMainCategory($mainCategory)->getCategory($category);
	//free memory
	unset($CategoryStore);
		
	//Do more checks
	if ($CategoryData === false)
	{
		$ERRORS->Add("Please select valid sub-category.");
	}
	else if ($CategoryData->hasSubCategories() and !$subcategory)
	{
		$ERRORS->Add("Please select specifics.");
	}
	else if ($subcategory)
	{
		//try getting the sub-category name
		if (!$SubCategoryName = $CategoryData->getSubCategoryName($subcategory))
		{
			$ERRORS->Add("Please select valid specifics.");
		}
		unset($SubCategoryName);
	}
	//free some memory
	unset($CategoryData);
	
	//check for errors
	$ERRORS->Check('/index.php?page=bugtracker_submit');
	
	//approval status
	$approval = BT_APP_STATUS_PENDING;
	$status = BT_STATUS_NEW;
	
	//Insert our issue link
	$insert = $DB->prepare("INSERT INTO `bugtracker` (`account`, `title`, `content`, `maincategory`, `category`, `subcategory`, `added`, `status`, `priority`, `approval`) VALUES (:acc, :title, :content, :maincat, :cat, :subcat, :added, :status, :priority, :approval);");
	$insert->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
	$insert->bindParam(':title', $title, PDO::PARAM_STR);
	$insert->bindParam(':content', $text, PDO::PARAM_STR);
	$insert->bindParam(':maincat', $mainCategory, PDO::PARAM_INT);
	$insert->bindParam(':cat', $category, PDO::PARAM_INT);
	$insert->bindParam(':subcat', $subcategory, PDO::PARAM_INT);
	$insert->bindParam(':added', $CORE->getTime(), PDO::PARAM_STR);
	$insert->bindParam(':status', $status, PDO::PARAM_INT);
	$insert->bindParam(':priority', $priority, PDO::PARAM_INT);
	$insert->bindParam(':approval', $approval, PDO::PARAM_INT);
	$insert->execute();
	unset($insert);
	
	$ERRORS->triggerSuccess();
	
####################################################################

$ERRORS->Check('/index.php?page=bugtracker_submit');

exit;