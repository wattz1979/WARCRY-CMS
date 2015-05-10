<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$category = isset($_GET['category']) ? (int)$_GET['category'] : false;

if ($category)
{
	$data = new BTCategories();
	
	$catData = $data->getMainCategory($category)->data;
	
	unset($data);
	
	$encoded = json_encode($catData);
	
	unset($catData);
	
	echo $encoded;
}
else
{
	header('HTTP/1.0 404 not found');
	exit;
}

exit;