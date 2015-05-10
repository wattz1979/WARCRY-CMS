<?php
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Load the forum base module
$CORE->load_CoreModule('forums.base');

$CORE->loggedInOrReturn();

//setup new instance of multiple errors
$ERRORS->NewInstance('post_reply');

//Define the variables
$topicId = isset($_POST['topic']) ? (int)$_POST['topic'] : false;
$title = isset($_POST['title']) ? $_POST['title'] : false;
$text = isset($_POST['text']) ? $_POST['text'] : false;
$staffPost = isset($_POST['staff_post']) ? true : false;

######################################
############### CHECKS ###############

	if (!$topicId)
	{
		//We have no forum id
		$ERRORS->Add('An unexpected error occurred, missing topic id.');
	}
	else if (!WCF::verifyTopicId($topicId))
	{
		//Veirfy the forum
		$ERRORS->Add('An unexpected error occurred, the selected topic is invalid.');
	}

	if ($title && strlen($title) > 150)
	{
		$ERRORS->Add('The reply title is too long, maximum 150 characters.');
	}
	
	if (!$text)
	{
		$ERRORS->Add('Please enter text for the reply.');
	}
	
//Check for errors
$ERRORS->Check('/forums.php?page=post_reply');

##################################################
######## REGISTER SERVER ACCOUNT #################
	
	//Resolve the forum name and id
	$res = $DB->prepare("SELECT `name`, `forum` FROM `wcf_topics` WHERE `id` = :id LIMIT 1;");
	$res->bindParam(':id', $topicId, PDO::PARAM_INT);
	$res->execute();
	
	if ($res->rowCount() > 0)
	{
		$topic = $res->fetch();
		
		$forumId = $topic['forum'];
		
		//Post title if missing
		if (!$title)
		{
			$title = 'Re: ' . $topic['name'];
		}
		
		//Post Flags
		$postFlags = 0;
		
		//Should we enable staff post
		if ($staffPost && $CURUSER->getRank()->int() >= RANK_STAFF_MEMBER)
		{
			$CORE->setFlag($postFlags, WCF_FLAGS_STAFF_POST);
		}
		
		//Insert the first post
		$insert2 = $DB->prepare("INSERT INTO `wcf_posts` (`topic`, `title`, `text`, `added`, `author`, `flags`) VALUES (:topic, :title, :text, :time, :author, :flags);");
		$insert2->bindParam(':topic', $topicId, PDO::PARAM_INT);
		$insert2->bindParam(':title', $title, PDO::PARAM_STR);
		$insert2->bindParam(':text', $text, PDO::PARAM_STR);
		$insert2->bindParam(':time', $time, PDO::PARAM_STR);
		$insert2->bindParam(':author', $CURUSER->get('id'), PDO::PARAM_INT);
		$insert2->bindParam(':flags', $postFlags, PDO::PARAM_INT);
		$insert2->execute();
		
		if ($insert2->rowCount() > 0)
		{
			$postId = $DB->lastInsertId();
			
			//Update the topic with the post id
			$update = $DB->prepare("UPDATE `wcf_topics` SET `posts` = `posts` + 1, `lastpost_id` = :post, `lastpost_time` = :time WHERE `id` = :topic LIMIT 1;");
			$update->bindParam(':topic', $topicId, PDO::PARAM_INT);
			$update->bindParam(':post', $postId, PDO::PARAM_INT);
			$update->bindParam(':time', $time, PDO::PARAM_STR);
			$update->execute();
			
			//Update the forum
			$update = $DB->prepare("UPDATE `wcf_forums` SET `posts` = `posts` + 1 WHERE `id` = :forum LIMIT 1;");
			$update->bindParam(':forum', $forumId, PDO::PARAM_INT);
			$update->execute();
			
			######################################
			########## Redirect ##################
			$PostPage = WCF::calculatePostPage($postId);
			
			//bind the onsuccess message
			$ERRORS->onSuccess('Success.', '/forums.php?page=topic&id=' . $topicId . '&p='.$PostPage.'#post-' . $postId);
			//Trigger it
			$ERRORS->triggerSuccess();
		}
		else
		{
			$ERRORS->Add('The topic you are replying on might have been removed. Cannot continue.');
		}
	}
	else
	{
		$ERRORS->Add('The website failed to insert your post. Please contact the administration.');
	}

$ERRORS->Check('/forums.php?page=post_reply');

exit;