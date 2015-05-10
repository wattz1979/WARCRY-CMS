<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

if (!$CURUSER->isOnline())
{
	echo 'You must be logged in!';
	die;
}

//Load the forum base module
$CORE->load_CoreModule('forums.base');

$PostId = isset($_GET['id']) ? (int)$_GET['id'] : false;

if ($PostId === false)
{
	echo 'No post selected.';
	die;
}

//Validate the post
$res = $DB->prepare("SELECT * FROM `wcf_posts` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $PostId, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() == 0)
{
	echo 'The selected post is invalid.';
	die;
}

//Fetch the post data
$Post = $res->fetch();

unset($res);

//Verify that we have permissions to delete
//Start by checking if we own that post
if ($CURUSER->get('id') != $Post['author'])
{
	//Since we dont own the post
	//Check if we have the minimum required rank
	if ($CURUSER->getRank()->int() < $config['FORUM']['Min_Rank_Post_Delete'])
	{
		echo 'You do not meet the requirements to delete this post.';
		die;
	}
	else
	{
		//We have the minimum required rank
		//now check if the authoer is lower rank
		//If the author is not resolved we assume he is lower rank
		if ($userInfo = WCF::getAuthorInfo($Post['author']))
		{
			//Get the poster rank
			$userRank = new UserRank($userInfo['rank']);
			
			//The author has equal or geater rank, we cant delete his post
			if ($CURUSER->getRank()->int() <= $userRank->int())
			{
				echo 'You do not meet the requirements to delete this post.';
				die;
			}
		}
	}
}

//Posts dont actually get delete, but only disabled os we gonna do a little update
$update = $DB->prepare("UPDATE `wcf_posts` SET `deleted_by` = :user, `deleted_time` = :time WHERE `id` = :post LIMIT 1;");
$update->bindParam(':user', $CURUSER->get('id'), PDO::PARAM_INT);
$update->bindParam(':time', $CORE->getTime(), PDO::PARAM_STR);
$update->bindParam(':post', $PostId, PDO::PARAM_INT);
$update->execute();

if ($update->rowCount() > 0)
{
	//Update the topic with the post id
	$update = $DB->prepare("UPDATE `wcf_topics` SET `posts` = `posts` - 1 WHERE `id` = :topic LIMIT 1;");
	$update->bindParam(':topic', $Post['topic'], PDO::PARAM_INT);
	$update->execute();
	
	//Find the forum of this topic
	$res = $DB->prepare("SELECT `forum` FROM `wcf_topics` WHERE `id` = :topic LIMIT 1;");
	$res->bindParam(':topic', $Post['topic'], PDO::PARAM_INT);
	$res->execute();
	
	if ($res->rowCount() > 0)
	{
		$topic = $res->fetch();
		
		//Update the forum
		$update = $DB->prepare("UPDATE `wcf_forums` SET `posts` = `posts` - 1 WHERE `id` = :forum LIMIT 1;");
		$update->bindParam(':forum', $topic['forum'], PDO::PARAM_INT);
		$update->execute();
	}
}
else
{
	echo 'The website failed to delete the post. Please contact the administration.';
	die;
}

echo 'OK';

?>