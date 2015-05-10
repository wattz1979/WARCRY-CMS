<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$RealmId = $CURUSER->GetRealm();
$entry = ((isset($_GET['entry'])) ? (int)$_GET['entry'] : false);

function PullData($entry)
{
	global $CORE;
	
	$response = $CORE->getRemotePage(array(
		'host'	=> 'db.warcry-wow.com',
		'port'	=> 80,
		'page'	=> '/ajax.php?item=' . $entry . '&json'
	));
	
	return $response['body'];
}

if (!($data = $CACHE->get('world/items/item_store_data_' . $RealmId . '_' . $entry)))
{
    $data = PullData($entry);
	
    //Cache server status for 30 seconds
    $CACHE->store('world/items/item_store_data_' . $RealmId . '_' . $entry, $data, strtotime('+1 month', 0));
}

header('Content-Type: application/json');
echo $data;
