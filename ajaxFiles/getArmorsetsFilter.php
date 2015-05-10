<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//load the characters module
$CORE->load_ServerModule('character');
//setup the characters class
$chars = new server_Character();

$perPage = (isset($_GET['perPage']) ? (int)$_GET['perPage'] : 6);
$category = (isset($_GET['category']) ? (int)$_GET['category'] : 0);
$character = (isset($_GET['character']) ? $_GET['character'] : false);
$realm = (isset($_GET['realm']) ? (int)$_GET['realm'] : 1);

//set the realm
$chars->setRealm($realm);

//define some defaults
$where = "";
$isFiltered = false;

if ($category and $category > 0)
{
	$where = "AND `category` = :filter";
	$isFiltered = true;
}

//get the character info
if ($character and $character != '')
{
	$charClass = $chars->getCharacterData(false, $character, 'class');
	//append the conditions to the where variable
	$where .= " AND `class` IN('0', :class)";
}

//count the items
$count_res = $DB->prepare("SELECT COUNT(*) FROM `armorsets` WHERE `realm` = '-1' ".$where." or `realm` = :realm ".$where);
$count_res->bindParam(':realm', $realm, PDO::PARAM_INT);
if (isset($charClass))
{
	$count_res->bindParam(':class', $charClass['class'], PDO::PARAM_INT);
}
if ($isFiltered)
{
	$count_res->bindParam(':filter', $category, PDO::PARAM_INT);
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

unset($chars);