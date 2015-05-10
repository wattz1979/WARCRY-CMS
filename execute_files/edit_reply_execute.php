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
$ERRORS->NewInstance('edit_reply');

//Define the variables
$PostId = isset($_POST['post']) ? (int)$_POST['post'] : false;
$title = isset($_POST['title']) ? $_POST['title'] : false;
$text = isset($_POST['text']) ? $_POST['text'] : false;
$staffPost = isset($_POST['staff_post']) ? true : false;

######################################
############### CHECKS ###############

	if (!$PostId)
	{
		//We have no forum id
		$ERRORS->Add('An unexpected error occurred, missing reply id.');
	}
	else if (!WCF::verifyPostId($PostId))
	{
		//Veirfy the forum
		$ERRORS->Add('An unexpected error occurred, the selected post is invalid.');
	}
	
	if (!$title)
	{
		$ERRORS->Add('Please enter reply title.');
	}
	else if (strlen($title) > 150)
	{
		$ERRORS->Add('The reply title is too long, maximum 150 characters.');
	}
	
	if (!$text)
	{
		$ERRORS->Add('Please enter reply text.');
	}
	
//Check for errors
$ERRORS->Check('/forums.php?page=edit_reply&id=' . $PostId);

##################################################
######## REGISTER SERVER ACCOUNT #################

//We need to pull the post flags
$res = $DB->prepare("SELECT `id`, `flags`, `topic` FROM `wcf_posts` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $PostId, PDO::PARAM_INT);
$res->execute();

$Post = $res->fetch();

//Post Flags
$postFlags = (int)$Post['flags'];

//Should we enable staff post
if ($staffPost && $CURUSER->getRank()->int() >= RANK_STAFF_MEMBER)
{
	if (!$CORE->hasFlag($postFlags, WCF_FLAGS_STAFF_POST))
		$CORE->setFlag($postFlags, WCF_FLAGS_STAFF_POST);
}
else
{
	$CORE->removeFlag($postFlags, WCF_FLAGS_STAFF_POST);
}

//Update the topic with the post id
$update = $DB->prepare("UPDATE `wcf_posts` SET `title` = :title, `text` = :text, `flags` = :flags, `lastedit_by` = :editor, `lastedit_time` = :time WHERE `id` = :post LIMIT 1;");
$update->bindParam(':title', $title, PDO::PARAM_STR);
$update->bindParam(':text', $text, PDO::PARAM_STR);
$update->bindParam(':flags', $postFlags, PDO::PARAM_INT);
$update->bindParam(':post', $PostId, PDO::PARAM_INT);
$update->bindParam(':editor', $CURUSER->get('id'), PDO::PARAM_INT);
$update->bindParam(':time', $time, PDO::PARAM_STR);
$update->execute();

if ($update->rowCount() > 0)
{
	//We've got to clear the cache
	$CACHE->clear('forums/posts/post_' . $PostId);
	//Get the post page
	$page = WCF::calculatePostPage($PostId);
	//bind the onsuccess message
	$ERRORS->onSuccess('Success.', '/forums.php?page=topic&id=' . $Post['topic'] . '&p='.$page.'#post-' . $PostId);
	//Trigger it
	$ERRORS->triggerSuccess();
}
else
{
	$ERRORS->Add('The website failed to update you\'re reply. Please contact the administration.');
}

//Check for errors
$ERRORS->Check('/forums.php?page=edit_reply&id=' . $PostId);

exit;