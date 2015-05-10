<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

if (!$CURUSER->isOnline())
{
	echo 'You must be logged in.';
	die;
}

//define the reward in silver
$ApprovedScreenshotReward = 2;

$id = (isset($_POST['id']) ? (int)$_POST['id'] : false);

//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_MEDIA_SREENSHOTS))
{
	echo 'You dont have the required permissions.';
	die;
}

if (!$id)
{
	echo 'Screenshot id is missing.';
	die;
}

//check if the news record exists
$res = $DB->prepare("SELECT * FROM `images` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $id, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() == 0)
{
	echo 'The screenshot record is missing.';
	die;
}
else
{
	$row = $res->fetch();
}
unset($res);

//check if the screenshot is already approved
if ($row['status'] == SCREENSHOT_STATUS_APPROVED)
{
	echo 'This screenshot is already approved.';
	die;
}

####################################################################
## The actual script begins here
	
	//define the approve type
	$status = SCREENSHOT_STATUS_APPROVED;
	
	//insert the news record
	$update = $DB->prepare("UPDATE `images` SET `status` = :status WHERE `id` = :id LIMIT 1;");
	$update->bindParam(':status', $status, PDO::PARAM_INT);
	$update->bindParam(':id', $row['id'], PDO::PARAM_INT);
	$update->execute();
	
	if ($update->rowCount() == 0)
	{
		echo 'The website failed to update the screenshot record.';
		die;
	}
	else
	{
		//reward the user for approved screenshot
		$accUpdate = $DB->prepare("UPDATE `account_data` SET `silver` = silver + :reward WHERE `id` = :id LIMIT 1;");
		$accUpdate->bindParam(':reward', $ApprovedScreenshotReward, PDO::PARAM_INT);
		$accUpdate->bindParam(':id', $row['account'], PDO::PARAM_INT);
		$accUpdate->execute();
		
		//check if the reward was delivered
		if ($accUpdate->rowCount() > 0)
		{
	  		//log into coin activity
	  		$ca = new CoinActivity($row['account']);
	  		$ca->set_SourceType(CA_SOURCE_TYPE_REWARD);
	  		$ca->set_SourceString('Approved Screenshot');
	  		$ca->set_CoinsType(CA_COIN_TYPE_SILVER);
	  		$ca->set_ExchangeType(CA_EXCHANGE_TYPE_PLUS);
	  		$ca->set_Amount($ApprovedScreenshotReward);
	  		$ca->execute();
	  		unset($ca);
			
			//success
			echo 'OK';
		}
		else
		{
			echo 'The website failed to deliver the reward to the user.';
		}
		unset($accUpdate);
	}
	unset($update);
	
####################################################################

exit;