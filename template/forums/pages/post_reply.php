<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

$quote = isset($_GET['quote']) ? (int)$_GET['quote'] : false;

if (!($topicId = WCF::getLastViewedTopic()))
{
	WCF::SetupNotification('Please make sure you are in a valid topic before posting.');
	header("Location: ".$config['BaseURL']."/forums.php");
	die;
}

//Set the title
$TPL->SetTitle('Reply to Topic');
$TPL->SetParameter('topbar', true);
//Print the header
$TPL->LoadHeader();

if ($topic = WCF::getTopicInfo($topicId))
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
				<p>Topic Reply</p>
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
		if ($error = $ERRORS->DoPrint('post_reply', true))
		{
			echo '<div class="alerts-container">', $error, '</div>';
		}	
		unset($error);
		?>
		
		<form method="post" action="<?php echo $config['BaseURL']; ?>/execute.php?take=post_reply" class="post_topic_reply" name="post_reply">
		
			<label>
				<p>Reply title</p>
				<input name="title" type="text" maxlength="150" value="<?php echo ($topic ? 'Re: ' . WCF::parseTitle($topic['name']) : ''); ?>" />
			</label>
			
			<label>
				<p>Reply text</p>
				<?php
					echo '<textarea name="text" class="bbcode">';
						
						//Check if we're quoting somebody
						if ($quote)
						{
							//Try getting info about the post
							if ($QuoteInfo = WCF::getQuoteInfo($quote))
							{
								echo '[quote=', $QuoteInfo['author'], ']', $QuoteInfo['text'], '[/quote]', "\n\r";
							}
						}
						
					echo '</textarea>';
				?>
			</label>
			
			<input type="hidden" value="<?php echo $topicId; ?>" name="topic" />
			
			<div>
            	<input type="submit" value="Post Reply" />
                
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
	if ($formData = $ERRORS->multipleError_accessFormData('post_reply'))
	{	
		echo '
		var savedFormData = $.parseJSON(', json_encode(json_encode($formData)), ');
		restoreFormData(\'post_reply\', savedFormData);';
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