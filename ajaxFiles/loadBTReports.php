<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : false;
$perPage = isset($_GET['perpage']) ? (int)$_GET['perpage'] : false;

//calculate the offset
$offsetStart = ($page - 1) * $perPage;

if ($page and $perPage)
{
	if ($CURUSER->isOnline())
	{
		$res = $DB->prepare("SELECT * FROM `bugtracker` WHERE `account` = :acc ORDER BY id DESC LIMIT ".$offsetStart.",".$perPage);
		$res->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_STR);
		$res->execute();
	
		//get the count
		$count = $res->rowCount();
				
		//save the count to the data
		$data['count'] = $count;
		
		if ($count > 0)
		{
			//get the categories
			$CategoryStore = new BTCategories();
			
			//setup empty array
			$data['issues'] = array();
			
			//loop the issues found
			while ($arr = $res->fetch())
			{
				//Translate the status
				switch ($arr['status'])
				{
					case BT_STATUS_NEW:
						$status = 'New';
						break;
					case BT_STATUS_OPEN:
						$status = 'Open';
						break;
					case BT_STATUS_ONHOLD:
						$status = 'On hold';
						break;
					case BT_STATUS_DUPLICATE:
						$status = 'Duplicate';
						break;
					case BT_STATUS_INVALID:
						$status = 'Invalid';
						break;
					case BT_STATUS_WONTFIX:
						$status = '';
						break;
					case BT_STATUS_RESOLVED:
						$status = 'Resolved';
						break;
					default:
						$status = 'Unknown';
						break;
				}

				//translate the approval
				switch ($arr['approval'])
				{
					case BT_APP_STATUS_APPROVED:
						$approval = 'approved';
						break;
					case BT_APP_STATUS_DECLINED:
						$approval = 'declined';
						break;
					default:
						$approval = 'pending';
						break;
				}
				
				//translate the priority
				switch ($arr['priority'])
				{
					case BT_PRIORITY_LOW:
						$priority = 'Low';
						break;
					case BT_PRIORITY_NORMAL:
						$priority = 'Normal';
						break;
					case BT_PRIORITY_HIGH:
						$priority = 'High';
						break;
					default:
						$priority = 'Abnormal';
						break;
				}
				
				//get the main category
				$MainCategory = $CategoryStore->getMainCategory($arr['maincategory']);

				switch ($arr['maincategory'])
				{
					case BT_CAT_WEBSITE:
						$MainCategoryName = 'Website';
						break;
					case BT_CAT_WOTLK_CORE:
						$MainCategoryName = 'WotLK Core';
						break;
					default:
						$MainCategoryName = 'Unknown';
						break;
				}
				
				//get the category
				$Category = $MainCategory->getCategory($arr['category']);
				
				if ($Category === false)
				{
					$CategoryName = 'Unknown';
				}
				else
				{
					$CategoryName = $Category->getName();
				}
				
				$SubCategoryName = false;
				//check for sub category
				if ($Category->hasSubCategories())
				{
					$SubCategoryName = $Category->getSubCategoryName($arr['subcategory']);
				}
				
				//free memory
				unset($MainCategory, $Category);
				
				//put the category string together
				$category = $CategoryName;
				if ($SubCategoryName)
				{
					$category .= ' - '.$SubCategoryName;
				}
				
				//free memory
				unset($CategoryName, $SubCategoryName);
				
				//save the issue data
				$data['issues'][] = array(
					'title' 		=> htmlspecialchars(stripslashes($arr['title'])),
					'approval'		=> $approval,
					'status'		=> $status,
					'priority'		=> $priority,
					'category'		=> $category,
					'maincategory'	=> $MainCategoryName,
				);
			}
			unset($arr, $status, $category, $approval, $priority, $MainCategoryName);
		}
		unset($count, $res, $CategoryStore);
	}
	else
	{
		$data = array(
			'error' => 'The user is not logged in.',
		);
	}
}
else
{
	$data = array(
		'error' => 'Missing variables.',
	);
}

$encoded = json_encode($data);
unset($data);
echo $encoded;

exit;