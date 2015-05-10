<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//$CORE->loggedInOrReturn();

//Set the title
$TPL->SetTitle('Forums');
$TPL->SetParameter('topbar', true);
//Print the header
$TPL->LoadHeader();

$category = isset($_GET['category']) ? (int)$_GET['category'] : false;

?>
	
    <a href="<?php echo $config['BaseURL']; ?>/index.php?page=rules" class="important_notice"><p>Please read and accept the rules and regulations before communicating with other members!</p></a>
    
    <div class="page-header-navigation">
    	<a href="<?php echo $config['BaseURL'], '/forums.php'; ?>">Board Index</a>
        <!--<a href="#">Category</a>
        <a href="#">Forum</a>-->
    </div>
	
    <?php
	
	//Check if we are looking for a specific category
	if ($category)
	{
		$res = $DB->prepare("SELECT * FROM `wcf_categories` WHERE `id` = :cat ORDER BY `position` ASC;");
		$res->bindParam(':cat', $category, PDO::PARAM_INT);
		$res->execute();
	}
	else
	{
		$res = $DB->prepare("SELECT * FROM `wcf_categories` ORDER BY `position` ASC;");
		$res->execute();
	}
	
	if ($res->rowCount() > 0)
	{
		while ($category = $res->fetch())
		{
			echo '
				<div class="container main-wide">
					<div class="wide-padding">
						<h1 class="category-title"><a href="#" title="', $category['name'], '">', WCF::parseTitle($category['name']), '</a></h1>';
						
						$res2 = $DB->prepare("SELECT * FROM `wcf_forums` WHERE `category` = :id ORDER BY `position` ASC;");
						$res2->bindParam(':id', $category['id'], PDO::PARAM_INT);
						$res2->execute();

						//Check if we have any forums in this category
						if ($res2->rowCount() > 0)
						{
							//WoW Classes Layout
							if ($CORE->hasFlag((int)$category['flags'], WCF_FLAGS_CLASSES_LAYOUT))
							{
								echo '<div class="classes">';
									
									while ($forum = $res2->fetch())
									{
										$classSimple = strtolower(str_replace(' ', '', $CORE->getClassString($forum['class'])));
										
										//OMG It's a class row
										echo '
										<ul class="class_row ', $classSimple, '">
											<li class="icon"><div class="image_icon"></div></li>
											<li class="info">
												<a href="', $config['BaseURL'], '/forums.php?page=forum&id=', $forum['id'], '">
													<h1>', $CORE->getClassString($forum['class']), '</h1>
													<h2>', $forum['topics'], ' Topics</h2>
												</a>
											</li>
										</ul>';
										
										unset($classSimple);
									}
									
								echo '</div>';
							}
							//Default Layout
							else
							{
								while ($forum = $res2->fetch())
								{
									$lastTopic = ((int)$forum['lasttopic_id'] > 0) ? WCF::getTopicInfo($forum['lasttopic_id']) : false;
									
									//OMG It's a forum row
									echo '
									<ul class="forum_row">
										<li class="icon">
											<img src="template/forums/style/icons/forum_read.png" width="56" height="53" title="No unread posts" />
										</li>
										<li class="forum_title_desc">
											<a href="', $config['BaseURL'], '/forums.php?page=forum&id=', $forum['id'], '">
												<h1>', WCF::parseTitle($forum['name']), '</h1>
												<h2>', WCF::parseTitle($forum['description']), '</h2>
											</a>
										</li>
										<li class="post">
											<p>', $forum['posts'], '</p>
										</li>
										<li class="topics">
											<p>', $forum['topics'], '</p>
										</li>
										<li class="lastpost">';
											
											if ($lastTopic)
											{
												echo '
												<p class="topic_title"><a href="', $config['BaseURL'], '/forums.php?page=topic&id=', $lastTopic['id'], '">', WCF::parseTitle($lastTopic['name']), '</a></p>
												<p class="by"><a href="', $config['BaseURL'], '/index.php?page=profile&uid=', $lastTopic['author'], '">', $lastTopic['author_str'], '</a></p>
												<p class="postdate">', $lastTopic['added'], '</p>';
											}
											
										echo '
										</li>
									</ul>';
								}
							}
						}
						else
						{
							echo '<div><h2>This category is empty.</h2></div>';
						}
						unset($res2);
			
			echo '	</div>
				</div>';
		}
	}
	else
	{
		echo '<div class="container main-wide">
				<div class="wide-padding" style="padding: 40px;">
					<h2>There are no forum categories.</h2>
				</div>
			</div>';
	}
	unset($res);

$TPL->LoadFooter();

?>