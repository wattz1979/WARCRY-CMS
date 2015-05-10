<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class BoostsData
{
	public $data = array(
		1 => array(
			'name' 			=> 'Path of Glory',
			'description' 	=> 'You characters will recieve 20% more experience from killing monsters and completing quests.',
			'icon'			=> 'http://wow.zamimg.com/images/wow/icons/large/achievement_bg_killflagcarriers_grabflag_capit.jpg',
		),
		2 => array(
			'name' 			=> 'Champion\'s Calling',
			'description' 	=> 'Your charcaters will recieve 20% more honor from killing players or playing battlegrounds.',
			'icon'			=> 'http://wow.zamimg.com/images/wow/icons/large/achievement_featsofstrength_gladiator_10.jpg',
		),
		3 => array(
			'name' 			=> 'Crafter\'s Dedication',
			'description' 	=> 'While using crafting or gathering profession skills you have a chance to gain extra skill points.',
			'icon'			=> 'http://wow.zamimg.com/images/wow/icons/large/trade_engineering.jpg',
		),
		4 => array(
			'name' 			=> 'Adventurer\'s Switfness',
			'description' 	=> 'Increase your characters movement speed by 20% while running or riding a mount.',
			'icon'			=> 'http://wow.zamimg.com/images/wow/icons/large/spell_fire_burningspeed.jpg',
		),
		5 => array(
			'name' 			=> 'Raider\'s Resolve',
			'description' 	=> 'Your charcaters become faster while dead and gives you discount on repair costs.',
			'icon'			=> 'http://wow.zamimg.com/images/wow/icons/large/spell_holy_guardianspirit.jpg',
		)
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
	
	public function __destruct()
	{
		unset($this->data);
	}
}
