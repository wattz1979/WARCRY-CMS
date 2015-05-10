<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$RealmId = $CURUSER->GetRealm();

$perPage = ((isset($_GET['perPage'])) ? (int)$_GET['perPage'] : 6);
$search = (isset($_GET['search']) ? $_GET['search'] : '');
$quality = (isset($_GET['quality']) ? $_GET['quality'] : '-1');

//define some defaults
$isSearch = false;
$isQuality = false;
$where = "WHERE `realm` LIKE CONCAT('%', :realm, '%')";

//if we have a search
if ($search != '')
{
	$isSearch = true;
							
	//and quality selected
	if ($quality != '-1' and $quality != '')
	{
		$isQuality = true;
								
		$where = "WHERE `name` LIKE CONCAT('%', :search, '%') AND `Quality` = :quality AND `realm` LIKE CONCAT('%', :realm, '%')";
	}
	else
	{
		$where = "WHERE `name` LIKE CONCAT('%', :search, '%') AND `realm` LIKE CONCAT('%', :realm, '%')";
	}
}
else if ($quality != '-1' and $quality != '')
{
	$isQuality = true;
							
	$where = "WHERE `Quality` = :quality AND `realm` LIKE CONCAT('%', :realm, '%')";
}

//count the items
$count_res = $DB->prepare("SELECT COUNT(*) FROM `store_items` ".$where);
$count_res->bindParam(':realm', $RealmId, PDO::PARAM_INT);
if ($isSearch)
{
	$count_res->bindParam(':search', $search, PDO::PARAM_STR);
}
if ($isQuality)
{
	$count_res->bindParam(':quality', $quality, PDO::PARAM_INT);
}
$count_res->execute();

$count_row = $count_res->fetch(PDO::FETCH_NUM);				
$count = $count_row[0];
									
unset($count_row);
unset($count_res);

$totalPages = ceil($count / $perPage);

//print the doc type
echo '<?xml version="1.0" encoding="UTF-8"?>
		<info>
			<totalPages>', $totalPages, '</totalPages>
			<totalRecords>', $count, '</totalRecords>
		</info>';

//set the XML header
if (!headers_sent())
{
	header ("content-type: text/xml");
}