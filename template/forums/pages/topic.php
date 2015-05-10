<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->load_CoreModule('forums.parser');
$CORE->load_CoreModule('pagination.forum');

//$CORE->loggedInOrReturn();

$topicId = isset($_GET['id']) ? (int)$_GET['id'] : false;
$p = isset($_GET['p']) ? (int)$_GET['p'] : 1;

//Let's setup our pagination
$pagies = new Pagination();
$pagies->addToLink('?page='.$pageName.'&id='.$topicId);

$perPage = $config['FORUM']['Posts_Limit'];

//make sure we have the forum id
if (!$topicId)
{
	WCF::SetupNotification('Please make sure you have selected a valid topic.');
	header("Location: ".$config['BaseURL']."/forums.php");
	die;
}
	
$res = $DB->prepare("SELECT * FROM `wcf_topics` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $topicId, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() == 0)
{
	WCF::SetupNotification('The selected topic does not exist or was deleted.');
	header("Location: ".$config['BaseURL']."/forums.php");
	die;
}

//save the last viewd topic
WCF::setLastViewedTopic($topicId);

//Fetch the post record
$row = $res->fetch();
		
//format the time
$row['added'] = date('D M j, Y, h:i A', strtotime($row['added']));

//Set the title
$TPL->SetTitle(WCF::parseTitle($row['name']));
$TPL->SetParameter('topbar', true);
//Add CSS to the loader
$TPL->AddCss('template/style/select.css');
//Print the header
$TPL->LoadHeader();

if ($forumRow = WCF::getForumInfo($row['forum']))
{
	if ($catName = WCF::getCategoryName($forumRow['category']))
	{
		$forumRow['category_name'] = $catName;
	}
	else
	{
		$forumRow['category_name'] = 'Unknown';
	}
}
else
{
	$forumRow['id'] = 0;
	$forumRow['name'] = 'Unknown';
	$forumRow['category'] = 0;
	$forumRow['category_name'] = 'Unknown';
}

//Staff memebers should be able to see deleted posts
$IncludeDeleted = (($CURUSER->isOnline() && $CURUSER->getRank()->int() >= $config['FORUM']['Min_Rank_Post_View_Deleted']) ? true : false);

//count the total topics
$count = WCF::getPostsCount($row['id'], $IncludeDeleted);

//Get the topics on this page
if ($count > 0)
{
	//calculate the pages
	$pages = $pagies->calculate_pages($count, $perPage, $p);
	
	$posts_res = $DB->prepare("SELECT * FROM `wcf_posts` WHERE `topic` = :topic ".($IncludeDeleted ? '' : "AND `deleted_by` = '0'")." ORDER BY `id` ASC LIMIT ".$pages['limit'].";");
	$posts_res->bindParam(':topic', $row['id'], PDO::PARAM_INT);
	$posts_res->execute();
	
	$countOnPage = $posts_res->rowCount();
}
else
{
	$countOnPage = 0;
}

?>

<!--<a href="#" class="important_notice"><p>Please read and accept the rules and regulations before communicating with other members!</p></a>-->

<div class="page-header-navigation">
	<a href="<?php echo $config['BaseURL'], '/forums.php'; ?>">Board Index</a>
	<a href="<?php echo $config['BaseURL'], '/forums.php?category=', $forumRow['category']; ?>"><?php echo WCF::parseTitle($forumRow['category_name']); ?></a>
	<a href="<?php echo $config['BaseURL'], '/forums.php?page=forum&id=', $forumRow['id']; ?>"><?php echo WCF::parseTitle($forumRow['name']); ?></a>
	<a href="<?php echo $config['BaseURL'], '/forums.php?page=topic&id=', $row['id']; ?>"><?php echo WCF::parseTitle($row['name']); ?></a>
</div>

<div class="container main-wide">
	<div class="forum-padding">
		
		<!-- Forum Header -->
		<div class="topic_header">
			<div class="topic_title">
				<h1><?php echo WCF::parseTitle($row['name']); ?></h1>
				<h3><?php echo $row['added']; ?></h3>
			</div>
			<h4><b><?php echo $count; ?></b> posts</h4>
		</div>
		<!-- Forum Header.End -->

		<?php
		if (($countOnPage > 2 && $count > $perPage) || $CURUSER->isOnline())
		{
			echo '
			<!-- Actions -->
			<div class="actions_c">';

				if ($CURUSER->isOnline())
				{
					echo '<a href="', $config['BaseURL'], '/forums.php?page=post_reply" class="forum_btn_large">Post Reply</a>';
				}
				
				if ($countOnPage > 2 && $count > $perPage)
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
		
		if ($countOnPage > 0)
		{
			//loop the records
			while ($arr = $posts_res->fetch())
			{
				if (($text = $CACHE->get('forums/posts/post_' . $arr['id'])) === false)
				{
					// create the BBCode parser
					$parser = new SBBCodeParser_Document(true, false);
					//Strip slashes
					$text = stripslashes($arr['text']);
					//Parse
					$text = $parser->parse($text)->detect_links()->detect_emails()->detect_emoticons()->get_html(true);
					//fix multiple break lines
					$text = preg_replace("/<br\s*\/?>\s<br\s*\/?>\s+/", "<br/>", $text);
					
					unset($parser);
					
					//Store the parsed post in the cache for a month
					$CACHE->store('forums/posts/post_' . $arr['id'], $text, "2592000");
				}
				
				if ($userInfo = WCF::getAuthorInfo($arr['author']))
				{
					$userRank = new UserRank($userInfo['rank']);
					$arr['author_str'] = $userInfo['displayName'];
					
					//prepare the avatar
					if ((int)$userInfo['avatarType'] == AVATAR_TYPE_GALLERY)
					{
						$gallery = new AvatarGallery();
						$Avatar = $gallery->get((int)$userInfo['avatar']);
						unset($gallery);
					}
					else if ((int)$userInfo['avatarType'] == AVATAR_TYPE_UPLOAD)
					{
						$Avatar = new Avatar(0, $userInfo['avatar'], 0, AVATAR_TYPE_UPLOAD);
					}
				}
				else
				{
					$userRank = new UserRank(0);
					$arr['author_str'] = 'Unknown';
					$arr['author_rank'] = 'Unknown';
					$gallery = new AvatarGallery();
					$Avatar = $gallery->get(0);
					unset($gallery);
				}
				
				//format the time
				$arr['added'] = date('D M j, Y, h:i A', strtotime($arr['added']));
				
				//Is staff post
				$staffPost = $CORE->hasFlag((int)$arr['flags'], WCF_FLAGS_STAFF_POST);
				//Is deleted
				$deletedPost = ((int)$arr['deleted_by'] > 0 ? true : false);
				
				//Resolve the deletion author
				if ($deletedPost)
				{
					$userInfo = WCF::getAuthorInfo($arr['deleted_by']);
					$arr['deleted_by_str'] = $userInfo['displayName'];
					unset($userInfo);
					$arr['deleted_time'] = date('D M j, Y, h:i A', strtotime($arr['deleted_time']));
				}
				
				echo '
				<!-- Topic Post -->
				<div class="topic_post', ($staffPost ? ' admin_post' : ''), ($deletedPost ? ' deleted_post' : ''), '" id="post-', $arr['id'], '">';
					
					if ($staffPost)
					{
						echo '<!-- Admin Warcry WoW post -->
						<div class="admin_post_logo_wc"></div>';
					}
					
					echo '
					<div class="left_side">
					
						<div class="user_avatar">';
							
							//handle avatars
							if ($Avatar->type() == AVATAR_TYPE_GALLERY)
							{
								echo '<span style="background:url(./resources/avatars/', $Avatar->string(), ') no-repeat; background-size: 100%;">';
							}
							else
							{
								echo '<span style="background:url(', $Avatar->string(), ') no-repeat; background-size: 100%;">';
							}
						
						echo '
						</div>
						
						<div class="user_info">
							<div class="usr_and_pr">
								<a href="', $config['BaseURL'], '/index.php?page=profile&uid=', $arr['author'], '" class="username">', $arr['author_str'], '</a>
								
								<div class="drop_down_profile">
									<span class="profile">Profile</span>
									<a href="" class="arrow"></a>
									<div class="drop_down_container">
										<h1>', $arr['author_str'], '</h1>
										<h3>', $userRank->string(), '</h3>
										<ul class="user_menu">
											<li><a href="', $config['BaseURL'], '/index.php?page=profile&uid=', $arr['author'], '">Profile</a></li>
											<li><a href="#">View Posts</a></li>
											<li><a href="#">Ignore</a></li>
										</ul>
									</div>
								</div>
								
							</div>
							
							<h3>', $userRank->string(), '</h3>
						</div>
					
					</div>
					<div class="right_side">
						<div class="post_container">
						', ($deletedPost ? '<p style="color: red;">This post has been deleted by '.$arr['deleted_by_str'].' on '.$arr['deleted_time'].'.</p><br>' : ''), '
						', $text, '
						</div>
						<ul class="post_controls">
							<li class="post_date">', $arr['added'], '</li>';
							
							//Check if we can edit the post
							if ($CURUSER->isOnline() && !$deletedPost && ($CURUSER->get('id') == $arr['author'] || ($CURUSER->getRank()->int() >= $config['FORUM']['Min_Rank_Post_Edit'] && $CURUSER->getRank()->int() > $userRank->int())))
								echo '<li><a class="edit" href="', $config['BaseURL'], '/forums.php?page=edit_reply&id=', $arr['id'], '" title="Edit">Edit</a></li>';
							
							//Check if we can delete the post
							if ($CURUSER->isOnline() && !$deletedPost && ($CURUSER->get('id') == $arr['author'] || ($CURUSER->getRank()->int() >= $config['FORUM']['Min_Rank_Post_Delete'] && $CURUSER->getRank()->int() > $userRank->int())))
								echo '<li><a class="delete post-delete-button" data-post-id="', $arr['id'], '" href="', $config['BaseURL'], '" title="Delete">Delete</a></li>';
							
							//Staff is not reportable
							if (!$staffPost)
								echo '<!--<li><a class="report" href="', $config['BaseURL'], '" title="Report">Report</a></li>-->';
								
							echo '<!--<li><a class="warn" href="', $config['BaseURL'], '" title="Warn">Warn</a></li>-->';
							
							//Can quote only if online and the post is not deleted
							if ($CURUSER->isOnline() && !$deletedPost)
								echo '<li><a class="quote post-quote-button" data-post-id="', $arr['id'], '" href="', $config['BaseURL'], '" title="Quote">Quote</a></li>';
						
						echo '
						</ul>
					</div>
					<div class="clear"></div>
				</div>
				<!-- Topic Post.End -->';
			}
			unset($topics_res, $arr);
		}
		
		//those should show only if we have more than once page
		if ($count > $perPage)
		{
			echo '
			<!-- Actions -->
			<div class="actions_c bottom">
				<div>
					<select name="action" styled="true">
						<option value="edit">Edit the Topic</option>
					</select>
				</div>
				<ul class="pagination">
					', $pages['previous'], '
					', $pages['pages'], '
					', $pages['next'], '
				</ul>
			</div>
			<!-- Actions.End -->
			<br /><br /><br />';
		}
		
		//Quick Reply if online
		if ($CURUSER->isOnline())
		{
			echo '
			<div class="quick_reply topic_post">
				<form method="post" action="', $config['BaseURL'], '/execute.php?take=post_reply">
					<h2>Quick Reply</h2>
					<textarea id="quick_reply_textarea" name="text"></textarea>
					<input type="hidden" name="topic" value="', $topicId, '" />
					', (($CURUSER->getRank()->int() >= RANK_STAFF_MEMBER) ? '<input type="hidden" value="1" name="staff_post" />' : ''), '
					<input type="submit" value="Post">
					<a href="', $config['BaseURL'], '/forums.php?page=post_reply" class="forum_btn_large advanced_post" id="go-advanced-post">Advanced post</a>
				</form>
			</div>';
		}
		?>

	</div>
</div>

<?php

$TPL->LoadFooter();

?>