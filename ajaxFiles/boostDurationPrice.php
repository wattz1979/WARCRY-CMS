<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$DurationId = ((isset($_GET['id'])) ? (int)$_GET['id'] : false);

if (!$DurationId)
{
	echo '{"error": "The duration id is missing."}';
	die;
}

header('Content-type: text/json');
header('Content-type: application/json');

echo json_encode($config['BOOSTS']['PRICEING'][$DurationId]);