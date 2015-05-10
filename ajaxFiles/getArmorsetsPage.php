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

$page = ((isset($_GET['page'])) ? (int)$_GET['page'] : 1);
$perPage = ((isset($_GET['perPage'])) ? (int)$_GET['perPage'] : 5);
$category = (isset($_GET['category']) ? (int)$_GET['category'] : 0);
$character = (isset($_GET['character']) ? $_GET['character'] : false);
$realm = ((isset($_GET['realm'])) ? (int)$_GET['realm'] : 1);

//set the realm
$chars->setRealm($realm);

//math the offset
$offset = ($page - 1) * $perPage;

//define some defaults
$where = "";
$isFiltered = false;
                
if ($category and $category > 0)
{
	$where = "AND `category` = :filter";
	$isFiltered = true;
}

//get the armor set categories
$res = $DB->query("SELECT * FROM `armorset_categories` ORDER BY id DESC");
if ($res->rowCount() > 0)
{
	while ($arr = $res->fetch())
	{
		$categories[$arr['id']] = $arr['name'];
	}
}
unset($res);

//get the character info
if ($character and $character != '')
{
	$charClass = $chars->getCharacterData(false, $character, 'class');
	//append the conditions to the where variable
	$where .= " AND `class` IN('0', :class)";
}

//get the database records
$res = $DB->prepare("SELECT * FROM `armorsets` WHERE `realm` = '-1' ".$where." or `realm` = :realm ".$where." ORDER BY id DESC LIMIT ".$offset.",".$perPage);
$res->bindParam(':realm', $realm, PDO::PARAM_INT);
if (isset($charClass))
{
	$res->bindParam(':class', $charClass['class'], PDO::PARAM_INT);
}
if ($isFiltered)
{
	$res->bindParam(':filter', $category, PDO::PARAM_INT);
}
$res->execute();

//print the doc type
echo '<?xml version="1.0" encoding="UTF-8"?>
		<itemlist count="', $res->rowCount(), '">';

if ($res->rowCount() > 0)
{
	$i = 1;
	while ($arr = $res->fetch())
	{
		//null the array
		unset($subInfo);
		//explode the items
		$items = explode(',', $arr['items']);
		//check for set specifications
		if ($arr['tier'] != '')
		{
			$subInfo[] = $arr['tier'];
		}
		if ($arr['class'] != '' and $arr['class'] > 0)
		{
			$subInfo[] = 'Class: ' . $chars->getClassString($arr['class']);
		}
		if ($arr['type'] != '')
		{
			$subInfo[] = 'Type: ' . $arr['type'];
		}
		$subInfo[] = 'Items: '.count($items);

		echo '
		<armorset id="', $arr['id'], '">]
			<setsCount>', $res->rowCount(), '</setsCount>
			<order>', $i, '</order>
			<html>  
				<![CDATA[
				<ul class="armor-set container_3">
					<li class="set-head">
					  <div id="info">
						<p id="set-name">', $arr['name'], '</p>
						<span id="set-info">', implode(' | ', $subInfo), '</span>
					  </div>
					  <div id="price"><p>', $arr['price'], '</p> <span>gold coins</span></div>
					  <div id="arrow"></div>
					</li>
					
					<li class="armor-set-items" id="armor-set-', $arr['id'], '-items">
						', $arr['items'], '
					</li>
				</ul>
				]]>
			</html>
		</armorset>';
								
		$i++;
	}
}

echo '</itemlist>';

//set the XML header
if (!headers_sent())
{
	header ("content-type: text/xml");
}

unset($chars);