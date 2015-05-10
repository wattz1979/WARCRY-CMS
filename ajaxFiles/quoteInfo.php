<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Load the forum base module
$CORE->load_CoreModule('forums.base');
//We dont have an error
$error = false;

//Set the json headers
header('Content-type: text/json');
header('Content-type: application/json');

if (!$CURUSER->isOnline())
{
	$error = 'You must be logged in!';
}

$PostId = isset($_GET['id']) ? (int)$_GET['id'] : false;

if ($PostId === false)
{
	$error = 'Invalid post id.';
}

//Validate the post
$res = $DB->prepare("SELECT * FROM `wcf_posts` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $PostId, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() == 0)
{
	$error = 'Invalid post id.';
}

//check for errors
if (!$error)
{
	//Fetch the post data
	$Post = $res->fetch();
	
	if ($author = WCF::getAuthorById($Post['author']))
	{
		$Post['author_str'] = $author;
	}
	else
	{
		$Post['author_str'] = 'Unknown';
	}
	unset($author);
	
	$data = array(
		'text' 		=> $Post['text'],
		'author'	=> $Post['author_str'],
	);
	
	echo json_encode($data);
}
else
{
	echo json_encode(array('error' => $error));
}

unset($res);

?>