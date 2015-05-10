<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

if (!($forumId = WCF::getLastViewedForum()))
{
	WCF::SetupNotification('Please make sure you are in a valid forum before posting.');
	header("Location: ".$config['BaseURL']."/forums.php");
	die;
}

//Set the title
$TPL->SetTitle('Post a new topic');
$TPL->SetParameter('topbar', true);
//Print the header
$TPL->LoadHeader();

if ($forum = WCF::getForumInfo($forumId))
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
	
?>

<!--<a href="#" class="important_notice"><p>Please read and accept the rules and regulations before communicating with other members!</p></a>-->

<div class="page-header-navigation">
	<a href="<?php echo $config['BaseURL'], '/forums.php'; ?>">Board Index</a>
	<?php
	if ($forum)
	{
		echo '
		<a href="', $config['BaseURL'], '/forums.php?category=', $forum['category'], '">', WCF::parseTitle($forum['category_name']), '</a>
		<a href="', $config['BaseURL'], '/forums.php?page=forum&id=', $forum['id'], '">', WCF::parseTitle($forum['name']), '</a>';
	}
	?>
</div>

<div class="container main-wide">
	<div class="forum-padding">
	
		<div class="forum_header">
		
			<div class="new_title">
				<p>Post New Topic</p>
				<div></div>
			</div>
			
			<?php
			if ($forum)
			{
				echo '
				<div class="forum_title">
					<h1>', WCF::parseTitle($forum['name']), '</h1>
					<h3>', WCF::parseTitle($forum['description']), '</h3>
				</div>
				<h4><b>', $forum['topics'], '</b> topics</h4>';
			}
			?>
		
		</div>
		
		<?php
		if ($error = $ERRORS->DoPrint('post_topic', true))
		{
			echo '<div class="alerts-container">', $error, '</div>';
		}	
		unset($error);
		?>
		
		<form method="post" action="<?php echo $config['BaseURL']; ?>/execute.php?take=post_topic" class="post_topic_reply" name="post_topic">
		
			<label>
				<p>Topic title</p>
				<input name="title" type="text" maxlength="150" />
			</label>
			
			<label>
				<p>Topic text</p>
				<textarea name="text" class="bbcode"></textarea>
			</label>
			
			<input type="hidden" value="<?php echo $forumId; ?>" name="forum" />
			
            <div>
            	<input type="submit" value="Post Topic" />
                
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
	if ($formData = $ERRORS->multipleError_accessFormData('post_topic'))
	{	
		echo '
		var savedFormData = $.parseJSON(', json_encode(json_encode($formData)), ');
		restoreFormData(\'post_topic\', savedFormData);';
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