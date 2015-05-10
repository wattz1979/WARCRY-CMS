<?PHP
if (!defined('init_executes'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//prepare multi errors
$ERRORS->NewInstance('teleport');

$RealmId = $CURUSER->GetRealm();

$pointId = (isset($_POST['point']) ? (int)$_POST['point'] : false);
$charName = (isset($_POST['character']) ? $_POST['character'] : false);

//bind the onsuccess message
$ERRORS->onSuccess($charName . ' has been successfully teleported.', '/index.php?page=teleporter');

//get the cooldown
$cooldown = $CURUSER->getCooldown('teleport');
$cooldownTime = '5 minutes';

if (!$charName)
{
	$ERRORS->Add("Please select a character.");
}
if (!$pointId)
{
	$ERRORS->Add("Please select a teleport location.");
}
if (!$RealmId)
{
	$ERRORS->Add("There is no realm assigned to your account.");
}
//check the cooldown
if (time() < $cooldown)
{
	$ERRORS->Add('This tool is on a cooldown, please try again later.');
}

#########################################################################################

$ERRORS->Check('/index.php?page=teleporter');

####################################################################
## The actual unstuck script begins here
	
	//load the character module
	$CORE->load_ServerModule('character');
	$CORE->load_ServerModule('commands');
	$CORE->load_CoreModule('purchaseLog');
	
	//prepare the log
	$logs = new purchaseLog();
	
	//prepare the commands class						
	$command = new server_Commands();
	//prepare the character handler
	$chars = new server_Character();
	
	//setup the maps data class
	$MD = new MapsData();
	//setup the map points data class
	$MP = new MapPoints();
	
	//start logging
	$logs->add('TELEPORTER', 'Starting log session. Teleporting player: '.$charName.', to point: '.$pointId.', selected realm: '.$RealmId.'.', 'pending');
	
	//connect to the database
	if ($chars->setRealm($RealmId))
	{
		################################################
		####   check if the character is valid    ######
		
		//get some character data
		$charData = $chars->getCharacterData(false, $charName, array('guid', 'level'));
		//find the map key by pointId
		$mapKey = $MD->ResolveMapByPoint($pointId);
		//get the map data
		$mapData = $MD->get($mapKey);
		
		if (!$chars->isMyCharacter(false, $charName))
		{
			$ERRORS->Add('The selected character does not belong to this account.');
			//update the log
			$logs->update(false, 'The selected character belongs to another account.', 'error');
		}
		else if ($mapData['reqLevel'] > $charData['level'])
		{
			$ERRORS->Add('The selected character does not meet the level requirement. The location requires a minimum of atleast ' . $charData['level'] . ' level.');
			//update the log
			$logs->update(false, 'The selected character does not meet the level requirement. The location requires a minimum of atleast ' . $charData['level'] . ' level.', 'error');
		}
		//The character seems to be valid
		else
		{
			//get the coords	
			if ($coords = $MP->get($pointId))
			{
				//if the character is Online use SOAP to teleport using commands
				if ($chars->isCharacterOnline($charData['guid']))
				{
					//try teleporting using soap
					$teleport = $command->Teleport($charName, $coords['x'], $coords['y'], $coords['z'], $coords['map'], $RealmId);
					//update the log
					$logs->update(false, 'The character is online using method SOAP.', 'pending');
				}
				else
				{
					//prepare the coords in suitable format
					$coords2 = array(
						'position_x'	=> $coords['x'],
						'position_y' 	=> $coords['y'],
						'position_z' 	=> $coords['z'],
						'map'			=> $coords['map'],
					);
					//try teleporting using PHP and SQL
					$teleport = $chars->Teleport($charData['guid'], $coords2);
					//free memory
					unset($coords2);
					//update the log
					$logs->update(false, 'The character is offline using method SQL.', 'pending');
				}
				
				//update the cooldown if the character was unstucked
				if ($teleport === true)
				{
					//set cooldown because we got no errors
					$CURUSER->setCooldown('teleport', strtotime('+'.$cooldownTime));
					//update the log
					$logs->update(false, 'The character was teleported successfully.', 'ok');
					//redirect
					$ERRORS->triggerSuccess();
				}
				else
				{
					$ERRORS->Add('The website failed to teleport your character. Please try again later or contact the administration.');
					//update the log
					$logs->update(false, 'Failed to teleport the character. Return: '.$teleport.'.', 'error');
				}
			}
			else
			{
				$ERRORS->Add('The website failed to teleport your character. Please try again later or contact the administration.');
				//update the log
				$logs->update(false, 'Failed to get coordinates for point id: '.$pointId.'.', 'error');
			}
			unset($coords);
		}
	}
	else
	{
		$ERRORS->Add('The website failed to teleport your character. Please try again later or contact the administration.');
		//update the log
		$logs->update(false, 'Failed to set Realm Id: '.$RealmId.'.', 'error');
	}
	
	unset($charData);
	unset($MD);
	unset($MP);
	unset($command);
	unset($chars);
	unset($logs);
	
####################################################################

$ERRORS->Check('/index.php?page=teleporter');

exit;