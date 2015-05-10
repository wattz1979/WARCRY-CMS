<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//load the pagination module
$CORE->load_CoreModule('pagination.forum');

//$CORE->loggedInOrReturn();

$forumId = isset($_GET['id']) ? (int)$_GET['id'] : false;
$p = isset($_GET['p']) ? (int)$_GET['p'] : 1;

//Let's setup our pagination
$pagies = new Pagination();
$pagies->addToLink('?page='.$pageName.'&id='.$forumId);

$perPage = $config['FORUM']['Topics_Limit'];

//make sure we have the forum id
if (!$forumId)
{
	WCF::SetupNotification('Please make sure you have selected a valid forum.');
	header("Location: ".$config['BaseURL']."/forums.php");
	die;
}

$res = $DB->prepare("SELECT * FROM `wcf_forums` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $forumId, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() == 0)
{
	WCF::SetupNotification('The selected forum does not exist or was deleted.');
	header("Location: ".$config['BaseURL']."/forums.php");
	die;
}

//save the last viewd forum
WCF::setLastViewedForum($forumId);

//Fetch the forum record
$row = $res->fetch();

//Set the title
$TPL->SetTitle(WCF::parseTitle($row['name']));
$TPL->SetParameter('topbar', true);
//Print the header
$TPL->LoadHeader();

if ($catName = WCF::getCategoryName($row['category']))
{
	$row['category_name'] = $catName;
}
else
{
	$row['category_name'] = 'Unknown';
}
unset($catName);

//count the total topics
$count = WCF::getTopicsCount($row['id']);

//Get the topics on this page
if ($count > 0)
{
	//calculate the pages
	$pages = $pagies->calculate_pages($count, $perPage, $p);

	$topics_res = $DB->prepare("SELECT * FROM `wcf_topics` WHERE `forum` = :forum ORDER BY `lastpost_time` DESC LIMIT ".$pages['limit'].";");
	$topics_res->bindParam(':forum', $row['id'], PDO::PARAM_INT);
	$topics_res->execute();
	
	$countOnPage = $topics_res->rowCount();
}
else
{
	$countOnPage = 0;
}

?>

<!--<a href="#" class="important_notice"><p>Please read and accept the rules and regulations before communicating with other members!</p></a>-->

<div class="page-header-navigation">
	<a href="<?php echo $config['BaseURL'], '/forums.php'; ?>">Board Index</a>
	<a href="<?php echo $config['BaseURL'], '/forums.php?category=', $row['category']; ?>"><?php echo WCF::parseTitle($row['category_name']); ?></a>
	<a href="<?php echo $config['BaseURL'], '/forums.php?page=forum&id=', $row['id']; ?>"><?php echo WCF::parseTitle($row['name']); ?></a>
</div>

<div class="container main-wide">
	<div class="forum-padding">
		
		<!-- Forum Header -->
		<div class="forum_header">
			<div class="forum_title">
				<h1><?php echo WCF::parseTitle($row['name']); ?></h1>
				<h3><?php echo WCF::parseTitle($row['description']); ?></h3>
			</div>
			<h4><b><?php echo $count; ?></b> topics</h4>
		</div>
		<!-- Forum Header.End -->
		
		<?php
		
		if ($CURUSER->isOnline() || ($countOnPage > 5 && $count > $perPage))
		{
			echo '
			<!-- Actions -->
			<div class="actions_c">';
			
				if ($CURUSER->isOnline())
					echo '<a href="', $config['BaseURL'], '/forums.php?page=post_topic" class="forum_btn_large">Post New Topic</a>';

				if ($countOnPage > 5 && $count > $perPage)
				{
					echo '
					<ul class="pagination">
						', $pages['previous'], '
						', $pages['pages'], '
						', $pages['next'], '
					</ul>';
				}
			
			echo '	
			</div>
			<!-- Actions.End -->';
		}
		
		echo '
		<!--<ul class="topic_header">
			<li class="topic">Topic</li>
			<li class="lastpost">Last post</li>
		</ul>-->';
		
		if ($countOnPage > 0)
		{
			//loop the records
			while ($arr = $topics_res->fetch())
			{
				if ($author = WCF::getAuthorById($arr['author']))
				{
					$arr['author_str'] = $author;
				}
				else
				{
					$arr['author_str'] = 'Unknown';
				}
				unset($author);
				
				//format the time
				$arr['added'] = date('D M j, Y, h:i a', strtotime($arr['added']));
				
				//Get the last post
				$lastPost = WCF::getTopicLastPost($arr['id']);
				
				echo '
				<ul class="topic_row">
					<li class="icon">
						<img src="template/forums/style/icons/topic_unread_mine.png" width="55px" height="39px"/>
					</li>
					<li class="topic_title_by_date">
						<h1><a href="', $config['BaseURL'], '/forums.php?page=topic&id=', $arr['id'], '">', WCF::parseTitle($arr['name']), '</a></h1>
						<p>Created by <a href="#">', $arr['author_str'], '</a>, ', $arr['added'], '</p>
					</li>
					<li class="lastpost">';
					
						if ($lastPost)
						{
							echo '
							<h4>by <a href="', $config['BaseURL'], '/index.php?page=profile&uid=', $lastPost['author'], '">', $lastPost['author_str'], '</a></h4>
							<h5>', $lastPost['added'], '</h5>
							<a href="', $config['BaseURL'], '/forums.php?page=topic&id=', $arr['id'], '&p=', $lastPost['page_number'], '#post-', $lastPost['id'], '" class="go_to_lastpost" title="Go to last post"><p>Go to last post</p></a>';
						}
						
					echo '
					</li>
				</ul>';
			}
			unset($topics_res, $arr, $lastPost);
		}
		else
		{
			echo '<h2>There are no topics.</h2>';
		}
		
		if (($CURUSER->isOnline() && $countOnPage > 5) || $count > $perPage)
		{
			echo '
			<!-- Actions -->
			<div class="actions_c bottom">';
			
				//this button should show only when we have more than 5 posts on this apge
				if ($CURUSER->isOnline() && $countOnPage > 5)
					echo '<a href="', $config['BaseURL'], '/forums.php?page=post_topic" class="forum_btn_large">Post New Topic</a>';
				
				//those should show only if we have more than once page
				if ($count > $perPage)
				{
					echo '
					<ul class="pagination">
						', $pages['previous'], '
						', $pages['pages'], '
						', $pages['next'], '
					</ul>';
				}
			
			echo '	
			</div>
			<!-- Actions.End -->';
		}
		?>

	</div>
</div>

<?php

$TPL->LoadFooter();

?>