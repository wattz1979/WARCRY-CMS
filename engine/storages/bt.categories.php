<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class BTCategories
{
	public $data = array(
		//Website
		BT_CAT_WEBSITE => array(
			1 => array(
				'name'		=> 'Design',
				'subcats'	=> false,
			),
			2 => array(
				'name'		=> 'Functionality',
				'subcats'	=> false,
			),
		),
		//WOTLK Core
		BT_CAT_WOTLK_CORE => array(
			1 => array(
				'name'		=> 'Achievements',
				'subcats'	=> false,
			),
			2 => array(
				'name'		=> 'Spells',
				'subcats'	=> array(
					1  => 'Death Knight',
					2  => 'Druid',
					3  => 'Hunter',
					4  => 'Mage',
					5  => 'Paladin',
					6  => 'Priest',
					7  => 'Rogue',
					8  => 'Shaman',
					9  => 'Warlock',
					10 => 'Warrior',
				),
			),
			3 => array(
				'name'		=> 'Talents',
				'subcats'	=> array(
					1  => 'Death Knight',
					2  => 'Druid',
					3  => 'Hunter',
					4  => 'Mage',
					5  => 'Paladin',
					6  => 'Priest',
					7  => 'Rogue',
					8  => 'Shaman',
					9  => 'Warlock',
					10 => 'Warrior',
				),
			),
			4 => array(
				'name'		=> 'Quests',
				'subcats'	=> false,
			),
			5 => array(
				'name'		=> 'Raids/Dungeons',
				'subcats'	=> array(
					1 => 'Dungeon',
					2 => 'Dungeon Heroic',
					3 => '10Man Raid',
					4 => '10Man Raid Heroic',
					5 => '25Man Raid',
					6 => '25Man Raid Heroic',
				),
			),
			6 => array(
				'name'		=> 'Arenas/Battlegrounds',
				'subcats'	=> array(
					1  => 'Blade\'s Edge Arena',
					2  => 'Dalaran Arena',
					3  => 'Nagrand Arena',
					4  => 'Ruins of Lordaeron Arena',
					5  => 'The Ring of Valor Arena',
					6  => 'Alterac Valley BG',
					7  => 'Arathi Basin BG',
					8  => 'Eye of the Storm BG',
					9  => 'Isle of Conquest BG',
					10 => 'Strand of the Ancients BG',
					11 => 'Warsong Gulch BG',
				),
			),
			7 => array(
				'name'		=> 'Professions',
				'subcats'	=> array(
					1  => 'Alchemy',
					2  => 'Blacksmithing',
					3  => 'Enchanting',
					4  => 'Engineering',
					5  => 'Herbalism',
					6  => 'Inscription',
					7  => 'Jewelcrafting',
					8  => 'Leatherworking',
					9  => 'Mining',
					10 => 'Skinning',
					11 => 'Tailoring',
					12 => 'Archaeology',
					13 => 'Cooking',
					14 => 'First Aid',
					15 => 'Fishing',
					16 => 'Riding',
				),
			),
			8 => array(
				'name'		=> 'Open World',
				'subcats'	=> false,
			),
			9 => array(
				'name'		=> 'Miscellaneous',
				'subcats'	=> false,
			),
		),
	);

	public function __construct()
	{
		return true;
	}
	
	public function getMainCategory($key)
	{
		if (!isset($this->data[$key]))
		{
			return false;
		}
		
		$obj = new BTCategory($this->data[$key]);
		
		return $obj;
	}
	
	public function __destruct()
	{
		unset($this->data);
		return true;
	}
}

class BTCategory
{
	public $data = false;
	
	public function __construct($data)
	{
		$this->data = $data;
		return true;
	}
		
	public function getCategory($key)
	{
		if (!$key)
		{
			return false;
		}
		if (!isset($this->data[$key]))
		{
			return false;
		}
				
		$obj = new BTSubCategory($this->data[$key]);
		
		return $obj;
	}
	
	public function __destruct()
	{
		unset($this->data);
		return true;
	}
}

class BTSubCategory
{
	public $data = false;
	
	public function __construct($data)
	{
		$this->data = $data;
		return true;
	}
	
	public function getName()
	{
		return $this->data['name'];
	}
	
	public function hasSubCategories()
	{
		if (!$this->data['subcats'])
		{
			return false;
		}
		
		return true;
	}
	
	public function getSubCategoryName($key)
	{
		if (!isset($this->data['subcats'][$key]))
		{
			return false;
		}
		
		return $this->data['subcats'][$key];
	}
	
	public function __destruct()
	{
		unset($this->data);
		return true;
	}
}
