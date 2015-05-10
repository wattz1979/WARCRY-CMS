<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Articles
{
	static public function parseTitle($str)
	{
		return htmlspecialchars(stripslashes($str));
	}
	
	static public function getNextArticle($id)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT `id` FROM `articles` WHERE `id` < :id ORDER BY `id` DESC;");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
		
		return $res->rowCount() > 0 ? $res->fetch() : false;
	}
	
	static public function RegisterView($id)
	{
		global $DB;
		
		$update = $DB->prepare("UPDATE `articles` SET `views` = views + 1 WHERE `id` = :id LIMIT 1;");
		$update->bindParam(':id', $id, PDO::PARAM_INT);
		$update->execute();
		
		return $update->rowCount() > 0 ? true : false;
	}
	
	static public function getCommentsCount($article)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT COUNT(*) FROM `article_comments` WHERE `article` = :article;");
		$res->bindParam(':article', $article, PDO::PARAM_INT);
		$res->execute();
		
		$count_row = $res->fetch(PDO::FETCH_NUM);
		
		return $count_row[0];
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