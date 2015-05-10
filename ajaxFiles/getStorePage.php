<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$RealmId = $CURUSER->GetRealm();

$page = ((isset($_GET['page'])) ? (int)$_GET['page'] : 1);
$perPage = ((isset($_GET['perPage'])) ? (int)$_GET['perPage'] : 6);
$search = (isset($_GET['search']) ? $_GET['search'] : '');
$quality = (isset($_GET['quality']) ? $_GET['quality'] : '-1');

//math the offset
$offset = ($page - 1) * $perPage;

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

//get the database records
$res = $DB->prepare("SELECT id, entry, realm, gold, silver FROM `store_items` ".$where." ORDER BY entry DESC LIMIT ".$offset.",".$perPage);
$res->bindParam(':realm', $RealmId, PDO::PARAM_INT);
if ($isSearch)
{
	$res->bindParam(':search', $search, PDO::PARAM_STR);
}
if ($isQuality)
{
	$res->bindParam(':quality', $quality, PDO::PARAM_INT);
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
		echo '
		<item id="', $arr['id'], '">
			<entry>', $arr['entry'], '</entry>
			<realm>', $arr['realm'], '</realm>
			<gold>', $arr['gold'], '</gold>
			<silver>', $arr['silver'], '</silver>
			<order>', $i, '</order>
			<html>  
				<![CDATA[
				<li id="store-item-', $i, '">
					<div id="item-cont">
						<div class="item-ico"><a href="', $config['WoWDB_URL'], '/?item=', $arr['entry'], '" rel="item=', $arr['entry'], '" id="icon"></a></div>
						<div class="item-info">
			           		<p></p>
			           		<span id="info"></span>
			         		<div class="item-price-coins"> ';
							
							$separator = '';
							$issetGold = false;
							$issetSilver = false;
							$goldStr = '';
							$silverStr = '';
							
							if ($arr['gold'] != '' and $arr['gold'] != 0)
							{
								$goldStr = '<div class="g-coin"></div><span id="store-price-gold">' . $arr['gold'] . '</span>';
								$issetGold = true;
							}
							
							if ($arr['silver'] != '' and $arr['silver'] != 0)
							{
								$silverStr = '<div class="s-coin"></div><span id="store-price-silver">' . $arr['silver'] . '</span>';
								$issetSilver = true;
							}

							if ($issetGold and $issetSilver)
							{
								$separator = '<span id="separator">|</span>';
							}
							
							echo $silverStr . $separator . $goldStr;
						
						echo '
							</div>
			       		</div>
			 		</div>
			   	</li>
				]]>
			</html>
		</item>';
								
		$i++;
	}
}

echo '</itemlist>';

//set the XML header
if (!headers_sent())
{
	header ("content-type: text/xml");
}