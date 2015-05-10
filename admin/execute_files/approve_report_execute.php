<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

if (!$CURUSER->isOnline())
{
	echo 'Please login.';
	die;
}

//define the reward in silver
$ApprovedReportReward = 4;

$id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_MAN_BUGTRACKER))
{
	echo 'You dont have the required permissions.';
	die;
}

if (!$id)
{
	echo 'Report id is missing.';
	die;
}
else
{
	//check if the news record exists
	$res = $DB->prepare("SELECT * FROM `bugtracker` WHERE `id` = :id LIMIT 1;");
	$res->bindParam(':id', $id, PDO::PARAM_INT);
	$res->execute();
	
	if ($res->rowCount() == 0)
	{
		echo 'The report record is missing.';
		die;
	}
	else
	{
		$row = $res->fetch();
	}
	unset($res);
	
	//check if the screenshot is already approved
	if ($row['approval'] != BT_APP_STATUS_PENDING)
	{
		echo 'The report must have pending approval status.';
		die;
	}
}

####################################################################
## The actual script begins here
	
	//define the approve type
	$approval = BT_APP_STATUS_APPROVED;
	
	//insert the news record
	$update = $DB->prepare("UPDATE `bugtracker` SET `approval` = :approval WHERE `id` = :id LIMIT 1;");
	$update->bindParam(':approval', $approval, PDO::PARAM_INT);
	$update->bindParam(':id', $row['id'], PDO::PARAM_INT);
	$update->execute();
	
	if ($update->rowCount() == 0)
	{
		echo 'The website failed to update the report record.';
		die;
	}
	else
	{
		//reward the user for approved screenshot
		$accUpdate = $DB->prepare("UPDATE `account_data` SET `silver` = silver + :reward WHERE `id` = :id LIMIT 1;");
		$accUpdate->bindParam(':reward', $ApprovedReportReward, PDO::PARAM_INT);
		$accUpdate->bindParam(':id', $row['account'], PDO::PARAM_INT);
		$accUpdate->execute();
		
		//check if the reward was delivered
		if ($accUpdate->rowCount() > 0)
		{
	  		//log into coin activity
	  		$ca = new CoinActivity($row['account']);
	  		$ca->set_SourceType(CA_SOURCE_TYPE_REWARD);
	  		$ca->set_SourceString('Approved Bug Report');
	  		$ca->set_CoinsType(CA_COIN_TYPE_SILVER);
	  		$ca->set_ExchangeType(CA_EXCHANGE_TYPE_PLUS);
	  		$ca->set_Amount($ApprovedReportReward);
	  		$ca->execute();
	  		unset($ca);
			
			//success
			echo 'OK';
		}
		else
		{
			echo 'The website failed to deliver the reward to the user.';
			die;
		}
		unset($accUpdate);
	}
	unset($update);
	
####################################################################

exit;