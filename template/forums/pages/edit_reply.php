<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

$PostId = isset($_GET['id']) ? (int)$_GET['id'] : false;

//Validate the post
if ($PostId === false)
{
	WCF::SetupNotification('The selected reply is invalid.');
	header("Location: ".$config['BaseURL']."/forums.php");
	die;
}

$res = $DB->prepare("SELECT * FROM `wcf_posts` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $PostId, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() == 0)
{
	WCF::SetupNotification('The selected reply is invalid.');
	header("Location: ".$config['BaseURL']."/forums.php");
	die;
}

//Fetch the post record
$Post = $res->fetch();

//Free mem
unset($res);

//Verify that we have permissions to edit
//Start by checking if we own that post
if ($CURUSER->get('id') != $Post['author'])
{
	//Since we dont own the post
	//Check if we have the minimum required rank
	if ($CURUSER->getRank()->int() < $config['FORUM']['Min_Rank_Post_Edit'])
	{
		WCF::SetupNotification('You do not meet the requirements to edit this post.');
		header("Location: ".$config['BaseURL']."/forums.php");
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
				WCF::SetupNotification('You do not meet the requirements to edit this post.');
				header("Location: ".$config['BaseURL']."/forums.php");
				die;
			}
		}
	}
}

//Set the title
$TPL->SetTitle('Edit Reply');
$TPL->SetParameter('topbar', true);
//Print the header
$TPL->LoadHeader();

if ($topic = WCF::getTopicInfo($Post['topic']))
{
	if ($forum = WCF::getForumInfo($topic['forum']))
	{
		if ($catName = WCF::getCategoryName($forum['category']))
		{
			$forum['category_name'] = $catName;
		}
		else
		{
			$forum['category_name'] = 'Unknown';
		}
		unset($catName);
	}
}
	
?>

<!--<a href="#" class="important_notice"><p>Please read and accept the rules and regulations before communicating with other members!</p></a>-->

<div class="page-header-navigation">
	<a href="<?php echo $config['BaseURL'], '/forums.php'; ?>">Board Index</a>
	<?php
	if ($topic && $forum)
	{
		echo '
		<a href="', $config['BaseURL'], '/forums.php?category=', $forum['category'], '">', WCF::parseTitle($forum['category_name']), '</a>
		<a href="', $config['BaseURL'], '/forums.php?page=forum&id=', $forum['id'], '">', WCF::parseTitle($forum['name']), '</a>
		<a href="', $config['BaseURL'], '/forums.php?page=topic&id=', $topic['id'], '">', WCF::parseTitle($topic['name']), '</a>';
	}
	?>
</div>

<div class="container main-wide">
	<div class="forum-padding">
	
		<div class="forum_header">
		
			<div class="new_title">
				<p>Edit Reply</p>
				<div></div>
			</div>
			
			<?php
			if ($topic)
			{
				echo '
				<div class="topic_title">
					<h1>', WCF::parseTitle($topic['name']), '</h1>
					<h3>', $topic['added'], '</h3>
				</div>';
			}
			?>
		
		</div>
		
		<?php
		if ($error = $ERRORS->DoPrint('edit_reply', true))
		{
			echo '<div class="alerts-container">', $error, '</div>';
		}	
		unset($error);
		?>
		
		<form method="post" action="<?php echo $config['BaseURL']; ?>/execute.php?take=edit_reply" class="post_topic_reply" name="edit_reply">
		
			<label>
				<p>Reply title</p>
				<input name="title" type="text" maxlength="150" value="<?php echo WCF::parseTitle($Post['title']); ?>" />
			</label>
			
			<label>
				<p>Reply text</p>
				<?php
					echo '<textarea name="text" class="bbcode">', stripslashes($Post['text']), '</textarea>';
				?>
			</label>
			
			<input type="hidden" value="<?php echo $Post['id']; ?>" name="post" />
			
			<div>
            	<input type="submit" value="Edit Reply" />
                
				<?php
                //Should we enable staff posting
                if ($CURUSER->getRank()->int() >= RANK_STAFF_MEMBER)
                {
                    echo '
					<div style="display: inline-block; padding: 15px 0 0 15px;">
						<label class="label_check" for="staff_post">
							<div></div>
							<input type="checkbox" value="1" checked="checked" id="staff_post" name="staff_post" />
							<p style="margin: 5px 0 0 0;">Staff Post</p>
						</label>
					</div>';
                }
                ?>
			</div>
			
		</form>
	
	</div>
</div>

<script>
$(document).ready(function()
{
	$("textarea.bbcode").sceditor({
		plugins: 'bbcode',
		style: 'template/style/bbcode-default-iframe.css'
	});
	
	<?php
	if ($formData = $ERRORS->multipleError_accessFormData('edit_reply'))
	{	
		echo '
		var savedFormData = $.parseJSON(', json_encode(json_encode($formData)), ');
		restoreFormData(\'edit_reply\', savedFormData);';
	}
	unset($formData);
	?>
});
</script>

<?php

	//Add some javascripts to the loader
	$TPL->AddFooterJs('template/js/sceditor/jquery.sceditor.js');
	$TPL->AddFooterJs('template/js/sceditor/jquery.sceditor.bbcode.js');
	$TPL->AddFooterJs('template/js/forms.js');
	//Print the footer
	$TPL->LoadFooter();

?>