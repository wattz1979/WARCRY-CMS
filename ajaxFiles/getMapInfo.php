<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
 
$key = ((isset($_GET['key'])) ? $_GET['key'] : false);

//setup the maps data class
$MD = new MapsData();

//get the map data
$data = $MD->get($key);

//free memory
unset($MD);

//print the doc type
echo '<?xml version="1.0" encoding="UTF-8"?>';

//check if that key is valid
if (!$data)
{
	echo '<error>The map key is invalid.</error>';
}
else
{
	echo '
	<info>
		<name>', $data['name'], '</name>
		<minLevel>', $data['minLevel'], '</minLevel>
		<maxLevel>', $data['maxLevel'], '</maxLevel>
		<type>', $data['type'], '</type>
		<zone>', $data['mapId'], '</zone>
		<points count="', count($data['points']), '">';
			
			//check if we got some points
			if (count($data['points']) > 0)
			{
				foreach ($data['points'] as $point)
				{
					echo '<point styleTop="', $point['top'], '" styleLeft="', $point['left'], '" pointId="', $point['pointId'], '"></point>';
				}
			}
		
		echo '
		</points>
	</info>';
}

//set the XML header
if (!headers_sent())
{
	header ("content-type: text/xml");
}
