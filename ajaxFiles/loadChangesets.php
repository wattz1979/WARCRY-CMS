<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
 
$page = ((isset($_GET['page'])) ? (int)$_GET['page'] : 1);
$ChangelogID = isset($_GET['changelog']) ? (int)$_GET['changelog'] : CHANGELOG_WEB;

$perPage = CHANGELOG_PERPAGE;

//math the offset
$offset = ($page - 1) * $perPage;

//get the database records
$res = $DB->prepare("SELECT * FROM `changelogs` WHERE `changelog` = :changelog ORDER BY `id` DESC LIMIT ".$offset.",".$perPage);
$res->bindParam(':changelog', $ChangelogID, PDO::PARAM_INT);
$res->execute();

//print the doc type
echo '<?xml version="1.0" encoding="UTF-8"?>
		<data>
			<count><![CDATA[', $res->rowCount(), ']]></count>
			<list>';

				if ($res->rowCount() > 0)
				{
					while ($arr = $res->fetch())
					{
						$time = $CORE->getTime(true, $arr['time']);
						$time = $time->format('j M H:i');
						
						echo '
						<changeset>
							<id><![CDATA[', $arr['id'], ']]></id>
							<revision><![CDATA[', $arr['revision'], ']]></revision>
							<author><![CDATA[', $arr['author'], ']]></author>
							<time><![CDATA[', $time, ']]></time>
							<text><![CDATA[', ($arr['text'] == '' ? 'No comment' : $arr['text']), ']]></text>
						</changeset>';
					}
				}
				else
				{
					echo 'NO_RESULTS';
				}

			echo '
			</list>
		</data>';

//set the XML header
if (!headers_sent())
{
	header("content-type: text/xml");
}