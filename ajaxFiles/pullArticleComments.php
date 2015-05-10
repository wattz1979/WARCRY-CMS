<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

header('Content-type: text/json');

$perPage = 10;

//Get the article id
$article = (isset($_GET['article']) ? (int)$_GET['article'] : false);
$lastComment = (isset($_GET['last_comment']) ? (int)$_GET['last_comment'] : 0);

if (!$article)
{
	echo '{"error": "Invalid or missing article."}';
	die;
}

//Make new Array
$data = array(
	'count'		=> 0,
	'comments'	=> array()
);

//Pull the records since the last id
$res = $DB->prepare("SELECT
						`article_comments`.`id`, 
						`article_comments`.`added`, 
						`article_comments`.`author`, 
						`article_comments`.`article`, 
						`article_comments`.`text`, 
						`account_data`.`displayName` AS `author_str` 
					FROM `article_comments` 
					LEFT JOIN `account_data` ON `account_data`.`id` = `article_comments`.`author` 
					WHERE `article_comments`.`article` = :article AND `article_comments`.`id` > :last ORDER BY `article_comments`.`id` ASC LIMIT :limit;");
$res->bindParam(':article', $article, PDO::PARAM_INT);
$res->bindParam(':last', $lastComment, PDO::PARAM_INT);
$res->bindParam(':limit', $perPage, PDO::PARAM_INT);
$res->execute();

//save the count
$data['count'] = $res->rowCount();

//save the comments
while ($arr = $res->fetch())
{
	$data['comments'][] = $arr;
}
unset($arr, $res);

echo json_encode($data);

exit;