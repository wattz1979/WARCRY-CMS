<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//load the characters module
$CORE->load_ServerModule('character');

//Get the user selected realm
$RealmId = $CURUSER->GetRealm();

$pointId = ((isset($_GET['point'])) ? (int)$_GET['point'] : false);
$charName = ((isset($_GET['character'])) ? $_GET['character'] : false);

//print the doc type
echo '<?xml version="1.0" encoding="UTF-8"?>';

//check
if (!$pointId)
{
	echo '<error>Invalid point id.</error>';
}
if (!$charName)
{
	echo '<error>Invalid character.</error>';
}

//setup the maps data class
$MD = new MapsData();
//find the map key by pointId
$mapKey = $MD->ResolveMapByPoint($pointId);
//get the map data
$mapData = $MD->get($mapKey);
//free memory
unset($MD);

//setup the characters class
$chars = new server_Character();
//set the realm
$chars->setRealm($RealmId);
//get the character level
$level = $chars->getCharacterData(false, $charName, 'level');
unset($chars);

//return the collected data
echo '
<info>
	<reqLevel>', $mapData['reqLevel'], '</reqLevel>
	<charLevel>', $level['level'], '</charLevel>
</info>';

//set the XML header
if (!headers_sent())
{
	header ("content-type: text/xml");
}
