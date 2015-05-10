<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class RankStringData
{
	public $data = array(
		RANK_ROOKIE 		=> 'Rookie',
		RANK_PARTICIPANT	=> 'Participant',
		RANK_MEMBER			=> 'Member',
		RANK_VETERAN		=> 'Veteran',
		RANK_SENIOR_MEMBER	=> 'Senior Member',
		RANK_ADDICT			=> 'Addict',
		RANK_STAFF_MEMBER	=> 'Staff Member',
		//Staff
		RANK_GM				=> 'Game Master',
		RANK_SENIOR_GM		=> 'Senior Game Master',
		RANK_LEAD_GM		=> 'Lead Game Master',
		RANK_CM				=> 'Community Manager',
		RANK_SENIOR_CM		=> 'Senior Community Manager',
		RANK_LEAD_CM		=> 'Lead Community Manager',
		RANK_DEV			=> 'Developer',
		RANK_LEAD_DEV		=> 'Lead Developer',
		RANK_MANAGEMENT		=> 'Management',
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
