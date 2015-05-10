<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class MapsData
{
	private $data = array(
	
		###############################################
		## Eastern Kingdom
		###############################################

		'dun_morough' => array(
			'name' 		=> 'Dun Morogh',
			'minLevel' 	=> '1',
			'maxLevel' 	=> '10',
			'type' 	 	=> 'Alliance',
			'mapId'		=> '1',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '266', 'left' => '635', 'pointId' => 3),
				array('top'	=> '383', 'left' => '238', 'pointId' => 4),
			),
		),
		'queldanas' => array(
			'name' 		=> 'Isle of Quel\'Danas',
			'minLevel' 	=> '70',
			'maxLevel' 	=> '70',
			'type' 	 	=> 'Contested',
			'mapId'		=> '4080',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '205', 'left' => '387', 'pointId' => 2),
			),
		),
		'arathi_highlands' => array(
			'name' 		=> 'Arathi Highlands',
			'minLevel' 	=> '30',
			'maxLevel' 	=> '40',
			'type' 	 	=> 'Contested',
			'mapId'		=> '45',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '302', 'left' => '419', 'pointId' => 5),
			),
		),
		'alterac_mountains' => array(
			'name' 		=> 'Alterac Mountains',
			'minLevel' 	=> '30',
			'maxLevel' 	=> '40',
			'type' 	 	=> 'Contested',
			'mapId'		=> '18',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '322', 'left' => '365', 'pointId' => 6),
			),
		),
		'badlands' => array(
			'name' 		=> 'Badlands',
			'minLevel' 	=> '35',
			'maxLevel' 	=> '45',
			'type' 	 	=> 'Contested',
			'mapId'		=> '3',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '371', 'left' => '131', 'pointId' => 7),
				array('top'	=> '280', 'left' => '439', 'pointId' => 8),
			),
		),
		'blasted_lands' => array(
			'name' 		=> 'Blasted Lands',
			'minLevel' 	=> '45',
			'maxLevel' 	=> '55',
			'type' 	 	=> 'Contested',
			'mapId'		=> '4',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '147', 'left' => '432', 'pointId' => 9),
			),
		),
		'burning_steps' => array(
			'name' 		=> 'Burning Steppes',
			'minLevel' 	=> '50',
			'maxLevel' 	=> '58',
			'type' 	 	=> 'Contested',
			'mapId'		=> '46',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '297', 'left' => '378', 'pointId' => 10),
			),
		),
		'deadwind_pass' => array(
			'name' 		=> 'Deadwind Pass',
			'minLevel' 	=> '55',
			'maxLevel' 	=> '60',
			'type' 	 	=> 'Contested',
			'mapId'		=> '41',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '183', 'left' => '347', 'pointId' => 11),
			),
		),
		'duskwood' => array(
			'name' 		=> 'Duskwood',
			'minLevel' 	=> '18',
			'maxLevel' 	=> '30',
			'type' 	 	=> 'Contested',
			'mapId'		=> '10',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '356', 'left' => '357', 'pointId' => 12),
			),
		),
		'eastern_plaguelands' => array(
			'name' 		=> 'Eastern Plaguelands',
			'minLevel' 	=> '53',
			'maxLevel' 	=> '60',
			'type' 	 	=> 'Contested',
			'mapId'		=> '139',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '271', 'left' => '467', 'pointId' => 13),
			),
		),
		'elwynn_forest' => array(
			'name' 		=> 'Elwynn Forest',
			'minLevel' 	=> '1',
			'maxLevel' 	=> '10',
			'type' 	 	=> 'Alliance',
			'mapId'		=> '12',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '388', 'left' => '416', 'pointId' => 14),
			),
		),
		'eversong_woods' => array(
			'name' 		=> 'Eversong Woods',
			'minLevel' 	=> '1',
			'maxLevel' 	=> '10',
			'type' 	 	=> 'Horde',
			'mapId'		=> '3430',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '321', 'left' => '447', 'pointId' => 15),
			),
		),
		'ghostlands' => array(
			'name' 		=> 'Ghostlands',
			'minLevel' 	=> '10',
			'maxLevel' 	=> '20',
			'type' 	 	=> 'Horde',
			'mapId'		=> '3433',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '211', 'left' => '374', 'pointId' => 16),
			),
		),
		'hillsbrad_foothills' => array(
			'name' 		=> 'Hillsbrad Foothills',
			'minLevel' 	=> '20',
			'maxLevel' 	=> '30',
			'type' 	 	=> 'Contested',
			'mapId'		=> '267',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '296', 'left' => '653', 'pointId' => 1),
			),
		),
		'loch_modan' => array(
			'name' 		=> 'Loch Modan',
			'minLevel' 	=> '10',
			'maxLevel' 	=> '20',
			'type' 	 	=> 'Alliance',
			'mapId'		=> '38',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '201', 'left' => '248', 'pointId' => 17),
			),
		),
		'redridge_mountains' => array(
			'name' 		=> 'Redridge Mountains',
			'minLevel' 	=> '15',
			'maxLevel' 	=> '25',
			'type' 	 	=> 'Contested',
			'mapId'		=> '44',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '140', 'left' => '268', 'pointId' => 18),
			),
		),
		'searing_gourge' => array(
			'name' 		=> 'Searing Gorge',
			'minLevel' 	=> '43',
			'maxLevel' 	=> '50',
			'type' 	 	=> 'Contested',
			'mapId'		=> '51',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '329', 'left' => '260', 'pointId' => 19),
			),
		),
		'silverpine_forest' => array(
			'name' 		=> 'Silverpine Forest',
			'minLevel' 	=> '10',
			'maxLevel' 	=> '20',
			'type' 	 	=> 'Horde',
			'mapId'		=> '130',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '146', 'left' => '404', 'pointId' => 20),
			),
		),
		'stranglethorn_valley' => array(
			'name' 		=> 'Stranglethorn Vale',
			'minLevel' 	=> '30',
			'maxLevel' 	=> '45',
			'type' 	 	=> 'Contested',
			'mapId'		=> '33',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '180', 'left' => '329', 'pointId' => 21),
				array('top'	=> '392', 'left' => '214', 'pointId' => 28),
			),
		),
		'swamps_of_sorrow' => array(
			'name' 		=> 'Swamp of Sorrows',
			'minLevel' 	=> '35',
			'maxLevel' 	=> '45',
			'type' 	 	=> 'Contested',
			'mapId'		=> '8',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '253', 'left' => '191', 'pointId' => 22),
			),
		),
		'the_hinterlands' => array(
			'name' 		=> 'The Hinterlands',
			'minLevel' 	=> '45',
			'maxLevel' 	=> '50',
			'type' 	 	=> 'Contested',
			'mapId'		=> '47',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '279', 'left' => '341', 'pointId' => 23),
			),
		),
		'tirisfal_glades' => array(
			'name' 		=> 'Tirisfal Glades',
			'minLevel' 	=> '1',
			'maxLevel' 	=> '10',
			'type' 	 	=> 'Horde',
			'mapId'		=> '85',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '322', 'left' => '515', 'pointId' => 24),
			),
		),
		'western_plaguelands' => array(
			'name' 		=> 'Western Plaguelands',
			'minLevel' 	=> '51',
			'maxLevel' 	=> '58',
			'type' 	 	=> 'Contested',
			'mapId'		=> '28',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '307', 'left' => '385', 'pointId' => 25),
			),
		),
		'westfall' => array(
			'name' 		=> 'Westfall',
			'minLevel' 	=> '10',
			'maxLevel' 	=> '20',
			'type' 	 	=> 'Alliance',
			'mapId'		=> '40',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '189', 'left' => '418', 'pointId' => 26),
			),
		),
		'wetlands' => array(
			'name' 		=> 'Wetlands',
			'minLevel' 	=> '20',
			'maxLevel' 	=> '30',
			'type' 	 	=> 'Contested',
			'mapId'		=> '11',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '209', 'left' => '406', 'pointId' => 27),
			),
		),

		###############################################
		## Kalimdor
		###############################################

		'ashenvaley' => array(
			'name' 		=> 'Ashenvale',
			'minLevel' 	=> '18',
			'maxLevel' 	=> '30',
			'type' 	 	=> 'Contested',
			'mapId'		=> '331',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '386', 'left' => '545', 'pointId' => 29),
			),
		),
		'azshara' => array(
			'name' 		=> 'Azshara',
			'minLevel' 	=> '48',
			'maxLevel' 	=> '55',
			'type' 	 	=> 'Contested',
			'mapId'		=> '16',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '315', 'left' => '214', 'pointId' => 30),
			),
		),
		'azuremyst' => array(
			'name' 		=> 'Azuremyst Isle',
			'minLevel' 	=> '1',
			'maxLevel' 	=> '10',
			'type' 	 	=> 'Alliance',
			'mapId'		=> '3524',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '279', 'left' => '356', 'pointId' => 31),
			),
		),
		'barrens' => array(
			'name' 		=> 'The Barrens',
			'minLevel' 	=> '10',
			'maxLevel' 	=> '25',
			'type' 	 	=> 'Horde',
			'mapId'		=> '17',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '54', 'left' => '493', 'pointId' => 32),
			),
		),
		'bloodmysle' => array(
			'name' 		=> 'Bloodmyst Isle',
			'minLevel' 	=> '10',
			'maxLevel' 	=> '20',
			'type' 	 	=> 'Alliance',
			'mapId'		=> '3525',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '307', 'left' => '345', 'pointId' => 33),
			),
		),
		'darkshore' => array(
			'name' 		=> 'Darkshore',
			'minLevel' 	=> '10',
			'maxLevel' 	=> '20',
			'type' 	 	=> 'Alliance',
			'mapId'		=> '148',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '317', 'left' => '327', 'pointId' => 34),
			),
		),
		'desolace' => array(
			'name' 		=> 'Desolace',
			'minLevel' 	=> '30',
			'maxLevel' 	=> '40',
			'type' 	 	=> 'Contested',
			'mapId'		=> '405',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '183', 'left' => '353', 'pointId' => 35),
			),
		),
		'durotar' => array(
			'name' 		=> 'Durotar',
			'minLevel' 	=> '1',
			'maxLevel' 	=> '10',
			'type' 	 	=> 'Horde',
			'mapId'		=> '14',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '112', 'left' => '377', 'pointId' => 36),
			),
		),
		'dustwallow-marsh' => array(
			'name' 		=> 'Dustwallow Marsh',
			'minLevel' 	=> '35',
			'maxLevel' 	=> '45',
			'type' 	 	=> 'Contested',
			'mapId'		=> '15',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '247', 'left' => '442', 'pointId' => 37),
			),
		),
		'felwood' => array(
			'name' 		=> 'Felwood',
			'minLevel' 	=> '48',
			'maxLevel' 	=> '55',
			'type' 	 	=> 'Contested',
			'mapId'		=> '361',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '426', 'left' => '372', 'pointId' => 38),
			),
		),
		'feralas' => array(
			'name' 		=> 'Feralas',
			'minLevel' 	=> '40',
			'maxLevel' 	=> '50',
			'type' 	 	=> 'Contested',
			'mapId'		=> '357',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '288', 'left' => '486', 'pointId' => 39),
			),
		),
		'moonglore' => array(
			'name' 		=> 'Moonglade',
			'minLevel' 	=> '',
			'maxLevel' 	=> '',
			'type' 	 	=> 'Contested',
			'mapId'		=> '493',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '178', 'left' => '134', 'pointId' => 40),
			),
		),
		'mulgore' => array(
			'name' 		=> 'Mulgore',
			'minLevel' 	=> '1',
			'maxLevel' 	=> '10',
			'type' 	 	=> 'Horde',
			'mapId'		=> '215',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '301', 'left' => '437', 'pointId' => 41),
			),
		),
		'silithus' => array(
			'name' 		=> 'Silithus',
			'minLevel' 	=> '55',
			'maxLevel' 	=> '60',
			'type' 	 	=> 'Contested',
			'mapId'		=> '1377',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '334', 'left' => '357', 'pointId' => 42),
			),
		),
		'stonetalon' => array(
			'name' 		=> 'Stonetalon Mountains',
			'minLevel' 	=> '15',
			'maxLevel' 	=> '27',
			'type' 	 	=> 'Contested',
			'mapId'		=> '406',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '224', 'left' => '367', 'pointId' => 43),
			),
		),
		'tanaris' => array(
			'name' 		=> 'Tanaris',
			'minLevel' 	=> '40',
			'maxLevel' 	=> '50',
			'type' 	 	=> 'Contested',
			'mapId'		=> '440',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '240', 'left' => '373', 'pointId' => 44),
			),
		),
		'teldrassil' => array(
			'name' 		=> 'Teldrassil',
			'minLevel' 	=> '1',
			'maxLevel' 	=> '10',
			'type' 	 	=> 'Alliance',
			'mapId'		=> '141',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '269', 'left' => '361', 'pointId' => 45),
			),
		),
		'thousand-needles' => array(
			'name' 		=> 'Thousand Needles',
			'minLevel' 	=> '25',
			'maxLevel' 	=> '30',
			'type' 	 	=> 'Contested',
			'mapId'		=> '400',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '182', 'left' => '236', 'pointId' => 46),
			),
		),
		'ungoro' => array(
			'name' 		=> 'Un\'Goro Crater',
			'minLevel' 	=> '48',
			'maxLevel' 	=> '55',
			'type' 	 	=> 'Contested',
			'mapId'		=> '490',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '432', 'left' => '574', 'pointId' => 47),
			),
		),
		'winterspring' => array(
			'name' 		=> 'Winterspring',
			'minLevel' 	=> '53',
			'maxLevel' 	=> '60',
			'type' 	 	=> 'Contested',
			'mapId'		=> '618',
			'reqLevel' 	=> '0',
			'points' 	=> array(
				array('top'	=> '204', 'left' => '475', 'pointId' => 48),
			),
		),

		###############################################
		## Northrend
		###############################################

		'crystalsong' => array(
			'name' 		=> 'Crystalsong Forest',
			'minLevel' 	=> '74',
			'maxLevel' 	=> '76',
			'type' 	 	=> 'Contested',
			'mapId'		=> '2817',
			'reqLevel' 	=> '68',
			'points' 	=> array(
				array('top'	=> '204', 'left' => '239', 'pointId' => 49),
			),
		),
		'stormpeaks' => array(
			'name' 		=> 'The Storm Peaks',
			'minLevel' 	=> '76',
			'maxLevel' 	=> '80',
			'type' 	 	=> 'Contested',
			'mapId'		=> '67',
			'reqLevel' 	=> '68',
			'points' 	=> array(
				array('top'	=> '308', 'left' => '353', 'pointId' => 50),
			),
		),
		'zuldrak' => array(
			'name' 		=> 'Zul\'Drak',
			'minLevel' 	=> '74',
			'maxLevel' 	=> '77',
			'type' 	 	=> 'Contested',
			'mapId'		=> '66',
			'reqLevel' 	=> '68',
			'points' 	=> array(
				array('top'	=> '342', 'left' => '421', 'pointId' => 51),
			),
		),
		'grizzlyhills' => array(
			'name' 		=> 'Grizzly Hills',
			'minLevel' 	=> '73',
			'maxLevel' 	=> '75',
			'type' 	 	=> 'Contested',
			'mapId'		=> '394',
			'reqLevel' 	=> '68',
			'points' 	=> array(
				array('top'	=> '169', 'left' => '377', 'pointId' => 52),
			),
		),
		'howlingfjord' => array(
			'name' 		=> 'Howling Fjord',
			'minLevel' 	=> '68',
			'maxLevel' 	=> '72',
			'type' 	 	=> 'Contested',
			'mapId'		=> '495',
			'reqLevel' 	=> '68',
			'points' 	=> array(
				array('top'	=> '163', 'left' => '466', 'pointId' => 53),
			),
		),
		'borean-tundra' => array(
			'name' 		=> 'Borean Tundra',
			'minLevel' 	=> '68',
			'maxLevel' 	=> '72',
			'type' 	 	=> 'Contested',
			'mapId'		=> '3537',
			'reqLevel' 	=> '68',
			'points' 	=> array(
				array('top'	=> '225', 'left' => '460', 'pointId' => 54),
			),
		),
		'dragonblight' => array(
			'name' 		=> 'Dragonblight',
			'minLevel' 	=> '71',
			'maxLevel' 	=> '74',
			'type' 	 	=> 'Contested',
			'mapId'		=> '65',
			'reqLevel' 	=> '68',
			'points' 	=> array(
				array('top'	=> '206', 'left' => '489', 'pointId' => 55),
			),
		),
		'icecrown' => array(
			'name' 		=> 'Icecrown',
			'minLevel' 	=> '77',
			'maxLevel' 	=> '80',
			'type' 	 	=> 'Contested',
			'mapId'		=> '210',
			'reqLevel' 	=> '68',
			'points' 	=> array(
				array('top'	=> '280', 'left' => '486', 'pointId' => 56),
				array('top'	=> '449', 'left' => '432', 'pointId' => 57),
			),
		),
		'sholazar-basin' => array(
			'name' 		=> 'Sholazar Basin',
			'minLevel' 	=> '76',
			'maxLevel' 	=> '78',
			'type' 	 	=> 'Contested',
			'mapId'		=> '3711',
			'reqLevel' 	=> '68',
			'points' 	=> array(
				array('top'	=> '364', 'left' => '367', 'pointId' => 58),
			),
		),
		'wintersgrap' => array(
			'name' 		=> 'Wintergrasp',
			'minLevel' 	=> '77',
			'maxLevel' 	=> '80',
			'type' 	 	=> 'Contested',
			'mapId'		=> '4197',
			'reqLevel' 	=> '68',
			'points' 	=> array(
				array('top'	=> '262', 'left' => '601', 'pointId' => 59),
			),
		),
	);
	
	public function __construct()
	{
		return true;
	}
	
	public function get($key)
	{
		if (!isset($this->data[$key]))
		{
			return false;
		}
		
		return $this->data[$key];
	}
	
	public function ResolveMapByPoint($pointId)
	{
		$MapKey = false;
		//resolve to which map this point belongs
		foreach ($this->data as $key => $data)
		{
			//loop the points for the current map to check the ID
			foreach ($data['points'] as $pointData)
			{
				//check if that's the point id
				if ($pointData['pointId'] == $pointId)
				{
					$MapKey = $key;
					break(2);
				}
			}
		}
		
		return $MapKey;
	}
	
	public function __destruct()
	{
		unset($this->data);
		return true;
	}
}


