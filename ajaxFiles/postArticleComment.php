<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

header('Content-type: text/json');

if (!$CURUSER->isOnline())
{
	echo '{"error": "You must be logged in to comment."}';
	die;
}

//Get the text var
$text = ((isset($_POST['text'])) ? $_POST['text'] : false);
//Get the article id
$article = (isset($_POST['article']) ? (int)$_POST['article'] : false);

if (!$text)
{
	echo '{"error": "Please enter comment text."}';
	die;
}
if (!$article)
{
	echo '{"error": "Invalid or missing article."}';
	die;
}

//Validate the article record
$res = $DB->prepare("SELECT `comments` FROM `articles` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $article, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() == 0)
{
	echo '{"error": "Invalid or missing article."}';
	die;
}

//Fetch the article record
$row = $res->fetch();

//free mem
unset($res);

//Check if the article has comments enabled
if ($row['comments'] == '0')
{
	echo '{"error": "The comments on this article have been disabled."}';
	die;
}

//Get the time
$time = $CORE->getTime();

//Let's insert the comment
$insert = $DB->prepare("INSERT INTO `article_comments` (`text`, `added`, `author`, `article`) VALUES (:text, :added, :acc, :article);");
$insert->bindParam(':text', $text, PDO::PARAM_STR);
$insert->bindParam(':added', $time, PDO::PARAM_STR);
$insert->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
$insert->bindParam(':article', $article, PDO::PARAM_INT);
$insert->execute();

if ($insert->rowCount() > 0)
{
	echo json_encode(array(
		'id'			=> $DB->lastInsertId(),
		'text' 			=> htmlspecialchars(stripslashes($text)),
		'added' 		=> $time,
		'author' 		=> $CURUSER->get('id'),
		'author_str'	=> $CURUSER->get('displayName'),
		'article'		=> $article
	));
}
else
{
	echo '{"error": "The website failed to insert your comment."}';
}

exit;