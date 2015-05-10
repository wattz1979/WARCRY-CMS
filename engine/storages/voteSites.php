<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class VoteSitesData
{
	public $data = array(
		1 => array('name' => 'XtremeTop100', 	'url' => 'http://www.xtremetop100.com/in.php?site=1132331157', 		'img' => 'http://www.xtremeTop100.com/votenew.jpg'),
		2 => array('name' => 'TOPG', 			'url' => 'http://topg.org/World-Of-Warcraft/in-354373', 			'img' => 'http://topg.org/topg.gif'),
		3 => array('name' => 'Top100Arena', 	'url' => 'http://www.top100arena.com/in.asp?id=78675 ', 			'img' => 'http://www.top100arena.com/hit.asp?id=78675&c=WoW&t=2'),
		4 => array('name' => 'OpenWoW', 		'url' => 'http://www.openwow.com/?vote=2302', 						'img' => 'http://cdn.openwow.com/toplist/vote_small.jpg'),
		5 => array('name' => 'GameSites200', 	'url' => 'http://www.gamesites200.com/wowprivate/in.php?id=10780', 	'img' => 'http://www.gamesites200.com/wowprivate/vote.gif'),
		6 => array('name' => 'WoWStatus', 		'url' => 'http://www.wowstatus.net/in.php?server=776723',			'img' => 'http://www.wowstatus.net/includes/images/vote.gif'),
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
		return true;
	}
}
