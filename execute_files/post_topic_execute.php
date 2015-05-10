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
$ERRORS->NewInstance('post_topic');

//Define the variables
$forumId = isset($_POST['forum']) ? (int)$_POST['forum'] : false;
$title = isset($_POST['title']) ? $_POST['title'] : false;
$text = isset($_POST['text']) ? $_POST['text'] : false;
$staffPost = isset($_POST['staff_post']) ? true : false;

######################################
############### CHECKS ###############

	if (!$forumId)
	{
		//We have no forum id
		$ERRORS->Add('An unexpected error occurred, missing forum id.');
	}
	else if (!WCF::verifyForumId($forumId))
	{
		//Veirfy the forum
		$ERRORS->Add('An unexpected error occurred, the selected forum is invalid.');
	}
	
	if (!$title)
	{
		$ERRORS->Add('Please enter topic title.');
	}
	else if (strlen($title) > 150)
	{
		$ERRORS->Add('The topic title is too long, maximum 150 characters.');
	}
	
	if (!$text)
	{
		$ERRORS->Add('Please enter text for the topic.');
	}
	
//Check for errors
$ERRORS->Check('/forums.php?page=post_topic');

##################################################
######## REGISTER SERVER ACCOUNT #################
	
	//get the time
	$time = $CORE->getTime();
	//Topic Flags
	$flags = 0;
	
	//Insert the topic record
	$insert = $DB->prepare("INSERT INTO `wcf_topics` (`forum`, `name`, `added`, `author`, `flags`) VALUES (:forum, :name, :time, :author, :flags);");
	$insert->bindParam(':forum', $forumId, PDO::PARAM_INT);
	$insert->bindParam(':name', $title, PDO::PARAM_STR);
	$insert->bindParam(':time', $time, PDO::PARAM_STR);
	$insert->bindParam(':author', $CURUSER->get('id'), PDO::PARAM_INT);
	$insert->bindParam(':flags', $flags, PDO::PARAM_INT);
	$insert->execute();
		
	//check if the topic was inserted
	if ($insert->rowCount() > 0)
	{
		$topicId = $DB->lastInsertId();
		
		//prepare the post title
		$postTitle = 'Re: ' . $title;
		
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
		$insert2->bindParam(':title', $postTitle, PDO::PARAM_STR);
		$insert2->bindParam(':text', $text, PDO::PARAM_STR);
		$insert2->bindParam(':time', $time, PDO::PARAM_STR);
		$insert2->bindParam(':author', $CURUSER->get('id'), PDO::PARAM_INT);
		$insert2->bindParam(':flags', $postFlags, PDO::PARAM_INT);
		$insert2->execute();
		
		if ($insert2->rowCount() > 0)
		{
			$postId = $DB->lastInsertId();
			
			//Update the topic with the post id
			$update = $DB->prepare("UPDATE `wcf_topics` SET `posts` = 1, `lastpost_id` = :post, `lastpost_time` = :time WHERE `id` = :topic LIMIT 1;");
			$update->bindParam(':topic', $topicId, PDO::PARAM_INT);
			$update->bindParam(':post', $postId, PDO::PARAM_INT);
			$update->bindParam(':time', $time, PDO::PARAM_STR);
			$update->execute();
			
			//Update the forum
			$update = $DB->prepare("UPDATE `wcf_forums` SET `topics` = `topics` + 1, `posts` = `posts` + 1, `lasttopic_id` = :topic WHERE `id` = :forum LIMIT 1;");
			$update->bindParam(':topic', $topicId, PDO::PARAM_INT);
			$update->bindParam(':forum', $forumId, PDO::PARAM_INT);
			$update->execute();
			
			######################################
			########## Redirect ##################
			//bind the onsuccess message
			$ERRORS->onSuccess('Success.', '/forums.php?page=topic&id=' . $topicId);
			//Trigger it
			$ERRORS->triggerSuccess();
		}
		else
		{
			$ERRORS->Add('The website failed to insert part of your topic. Please contact the administration.');
		}
	}
	else
	{
		$ERRORS->Add('The website failed to insert your topic. Please contact the administration.');
	}

$ERRORS->Check('/forums.php?page=post_topic');

exit;