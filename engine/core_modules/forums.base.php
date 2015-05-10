<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class WCF
{
	static public function setLastViewedForum($id)
	{
		$_SESSION['FORUM']['LastViewedForum'] = (int)$id;
	}
	
	static public function setLastViewedTopic($id)
	{
		$_SESSION['FORUM']['LastViewedTopic'] = (int)$id;
	}
	
	static public function getLastViewedForum()
	{
		if (isset($_SESSION['FORUM']['LastViewedForum']))
		{
			return $_SESSION['FORUM']['LastViewedForum'];
		}
		
		return false;
	}
	
	static public function getLastViewedTopic()
	{
		if (isset($_SESSION['FORUM']['LastViewedTopic']))
		{
			return $_SESSION['FORUM']['LastViewedTopic'];
		}
		
		return false;
	}
	
	static public function parseTitle($str)
	{
		return htmlspecialchars(stripslashes($str));
	}
	
	###########################################
	###### POST FUNCTIONS #####################
	
	static public function verifyPostId($id)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT `id` FROM `wcf_posts` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
		
		return ($res->rowCount() > 0) ? true : false;
	}
	
	static public function getPostsCount($topic, $IncludeDeleted = false)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT COUNT(*) FROM `wcf_posts` WHERE `topic` = :topic ".($IncludeDeleted ? '' : "AND `deleted_by` = '0'").";");
		$res->bindParam(':topic', $topic, PDO::PARAM_INT);
		$res->execute();
		
		$count_row = $res->fetch(PDO::FETCH_NUM);
		
		return $count_row[0];
	}
	
	static public function getPostInfo($post)
	{
		global $DB, $config;
		
		$res = $DB->prepare("SELECT `id`, `added`, `author`, `topic` FROM `wcf_posts` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $post, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$row = $res->fetch();
			
			//Get the author string
			if ($author = self::getAuthorById($row['author']))
			{
				$row['author_str'] = $author;
			}
			else
			{
				$row['author_str'] = 'Unknown';
			}
			unset($author);
			
			//format the time
			$row['added'] = date('D M j, Y, h:i a', strtotime($row['added']));
			
			//Calculate last page
			$row['page_number'] = self::calculatePostPage($post);

			return $row;
		}
		
		return false;
	}
	
	static public function getQuoteInfo($post)
	{
		global $DB, $config;
		
		$res = $DB->prepare("SELECT `author`, `text` FROM `wcf_posts` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $post, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$row = $res->fetch();
			
			//Get the author string
			if ($author = self::getAuthorById($row['author']))
			{
				$row['author'] = $author;
			}
			else
			{
				$row['author'] = 'Unknown';
			}
			unset($author);

			return $row;
		}
		
		return false;
	}
	
	public function calculatePostPage($post)
	{
		global $DB, $config, $CURUSER;
		
		//Detect if we need to include deleted posts
		$IncludeDeleted = (($CURUSER->isOnline() && $CURUSER->getRank()->int() >= $config['FORUM']['Min_Rank_Post_View_Deleted']) ? true : false);
		
		$res = $DB->prepare("SELECT COUNT(*) AS count FROM `wcf_posts` WHERE `topic` = (SELECT `topic` FROM `wcf_posts` WHERE `id` = :post LIMIT 1) AND `id` <= :post ".(!$IncludeDeleted ? " AND `deleted_by` = '0'" : '')." ORDER BY `id` ASC;");
		$res->bindParam(':post', $post, PDO::PARAM_INT);
		$res->execute();
		
		//fetch
		$row = $res->fetch();
		
		//re-variable
		$position = $row['count'];
		
		//free mem
		unset($res, $row);
		
		return ($position > $config['FORUM']['Posts_Limit'] ? ceil($position / $config['FORUM']['Posts_Limit']) : 0);
	}
	
	###########################################
	###### TOPIC FUNCTIONS ####################
	
	static public function verifyTopicId($id)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT `id` FROM `wcf_topics` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
		
		return ($res->rowCount() > 0) ? true : false;
	}
	
	static public function getTopicsCount($forum)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT COUNT(*) FROM `wcf_topics` WHERE `forum` = :forum;");
		$res->bindParam(':forum', $forum, PDO::PARAM_INT);
		$res->execute();
		
		$count_row = $res->fetch(PDO::FETCH_NUM);
		
		return $count_row[0];
	}
	
	static public function getTopicInfo($id)
	{
		global $DB, $config;
		
		$res = $DB->prepare("SELECT `id`, `forum`, `name`, `added`, `author` FROM `wcf_topics` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$row = $res->fetch();
			
			//Get the author string
			if ($author = self::getAuthorById($row['author']))
			{
				$row['author_str'] = $author;
			}
			else
			{
				$row['author_str'] = 'Unknown';
			}
			unset($author);
			
			//format the time
			$row['added'] = date('D M j, Y, h:i a', strtotime($row['added']));
			
			return $row;
		}
		
		return false;
	}
	
	static public function getTopicLastPost($topic)
	{
		global $DB, $config;
		
		$res = $DB->prepare("SELECT `id`, `added`, `author` FROM `wcf_posts` WHERE `topic` = :id AND `deleted_by` = '0' ORDER BY `added` DESC LIMIT 1;");
		$res->bindParam(':id', $topic, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$row = $res->fetch();
			
			//Get the author string
			if ($author = self::getAuthorById($row['author']))
			{
				$row['author_str'] = $author;
			}
			else
			{
				$row['author_str'] = 'Unknown';
			}
			unset($author);
			
			//format the time
			$row['added'] = date('D M j, Y, h:i a', strtotime($row['added']));
			
			//Post page number
			$row['page_number'] = self::calculatePostPage($row['id']);
			
			return $row;
		}
		
		return false;
	}
	
	###########################################
	###### FORUM FUNCTIONS ####################
	
	static public function getAuthorById($id)
	{
		global $DB;
		
		$author_res = $DB->prepare("SELECT `displayName` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
		$author_res->bindParam(':acc', $id, PDO::PARAM_INT);
		$author_res->execute();
		
		if ($author_res->rowCount() > 0)
		{
			$author_row = $author_res->fetch();
			
			return $author_row['displayName'];
		}
		
		return false;
	}
	
	static public function verifyForumId($id)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT `id` FROM `wcf_forums` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
		
		return ($res->rowCount() > 0) ? true : false;
	}
	
	static public function getForumInfo($id)
	{
		global $DB;
		
		//Find the parent forum
		$forum_res = $DB->prepare("SELECT `id`, `name`, `description`, `topics`, `category` FROM `wcf_forums` WHERE `id` = :id LIMIT 1;");
		$forum_res->bindParam(':id', $id, PDO::PARAM_INT);
		$forum_res->execute();
		
		if ($forum_res->rowCount() > 0)
		{
			return $forum_res->fetch();
		}
		
		return false;
	}
	
	static public function getForumLastTopic($forum)
	{
		global $DB, $config;
		
		$res = $DB->prepare("SELECT `id`, `name`, `added`, `author` FROM `wcf_topics` WHERE `forum` = :id ORDER BY `added` DESC LIMIT 1;");
		$res->bindParam(':id', $forum, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$row = $res->fetch();
			
			//Get the author string
			if ($author = self::getAuthorById($row['author']))
			{
				$row['author_str'] = $author;
			}
			else
			{
				$row['author_str'] = 'Unknown';
			}
			unset($author);
			
			//format the time
			$row['added'] = date('D M j, Y, h:i a', strtotime($row['added']));
			
			return $row;
		}
		
		return false;
	}
	
	###########################################
	###### MISC FUNCTIONS #####################
	
	static public function getAuthorInfo($id)
	{
		global $DB;
		
		$author_res = $DB->prepare("SELECT `displayName`, `rank`, `avatar`, `avatarType` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
		$author_res->bindParam(':acc', $id, PDO::PARAM_INT);
		$author_res->execute();
		
		if ($author_res->rowCount() > 0)
		{
			return $author_res->fetch();
		}
		
		return false;
	}
	
	static public function getCategoryName($id)
	{
		global $DB;
		
		//Find the category name
		$res = $DB->prepare("SELECT `name` FROM `wcf_categories` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$catRow = $res->fetch();
			
			return $catRow['name'];
		}
		
		return false;
	}
	
	static public function SetupNotification($text)
	{
		global $NOTIFICATIONS;
		
		//Setup our notification
		$NOTIFICATIONS->SetTitle('Alert!');
		$NOTIFICATIONS->SetHeadline('An error occured!');
		$NOTIFICATIONS->SetText($text);
		$NOTIFICATIONS->SetTextAlign('center');
		$NOTIFICATIONS->SetAutoContinue(true);
		$NOTIFICATIONS->SetContinueDelay(4);
		$NOTIFICATIONS->Apply();
	}
}