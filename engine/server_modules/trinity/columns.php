<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class CORE_COLUMNS
{
	static private $tables = array(
		'accounts' 		=> array(
							'self'					=>	'account',
							'id'					=> 	'id',
							'username'    			=> 	'username',   		
							'shapasshash'			=>  'sha_pass_hash',
							'lastip'				=>  'last_ip',
							'lastlogin'				=>  'last_login',
							'flags'					=>  'expansion',
							'email'					=>  'email',
							'joindate'				=> 	'joindate',
							'recruiter'				=> 	'recruiter',
							'sessionkey'			=> 	'sessionkey',
							'v'						=>	'v',
							's'						=>	's',
						),
		'characters' 	=> array(
							'self'					=> 'characters',
							'account'				=> 'account',
							'guid'					=> 'guid',
							'name'					=> 'name',
							'honorPoints'			=> 'totalHonorPoints',
							'killsLifeTime'			=> 'totalKills',
							'online'				=> 'online',
							'level'					=> 'level',
							'class'					=> 'class',
							'race'					=> 'race',
							'gender'				=> 'gender',
							'gold'					=> 'money',
						),
		'guild'			=> array(
							'self'					=> 'guild',
							'guildid'				=> 'guildid',
							'name'					=> 'name',
							'leaderguid'			=> 'leaderguid',
							'EmblemStyle'			=> 'EmblemStyle',
							'EmblemColor'			=> 'EmblemColor',
							'BorderStyle'			=> 'BorderStyle',
							'BorderColor'			=> 'BorderColor',
							'BackgroundColor'		=> 'BackgroundColor',
							'info'					=> 'info',
							'createdate'			=> 'createdate',
							'xp'					=> 'xp',
							'level'					=> 'level',
						),
		'guild_member'  => array(
							'self'					=> 'guild_member',
							'guildid'				=> 'guildid',
							'guid'					=> 'guid',
							'rank'					=> 'rank',
							'achievementPoints'		=> 'achievementPoints',
						),
	);
	
	static public function get($table)
	{
		if (isset(self::$tables[$table]))
		{
			return self::$tables[$table];
		}
		else
		{
			return false;
		}
	}
}

$core_columns = array(
		//
		// GM premisions translation (all that are used by this cms)
		//
		"az"						=> "4",
		"a"							=> "3",
		"gm_normalplayer"			=> "0", //gm premission for normal player
		//
		// Expansion data in sql
		//
		"expansion_vanilla"			=> "0",
		"expansion_tbc"				=> "1",
		"expansion_wotlk"			=> "2",
		"expansion_cata"			=> "0",
		"register_expansion"		=> "2", //The expansion all users will be registerd with
		//
		// Accounts Table
		//
		"accounts" 					=> "account",	 	//table 'accounts'
			"accid"					=> "id",
			"username"    			=> "username",   		
			"password" 				=> "",	 	//leave blank if doesnt exits
														//if exists  website will try update raw pass here 
			"encrypted_password"	=> "sha_pass_hash",
			"lastip"				=> "last_ip",
			"lastlogin"				=> "last_login",
			"flags"					=> "expansion",
			"email"					=> "email", //for gimmepass.php
		//
		// Account Access Table
		//
		"account_access"            => "account_access",
			"accessid"				=> "id",
			"gm"					=> "gmlevel",
		//
		// Account Ban Table
		//
		"account_banned"            => "account_banned",
			"bannedid"				=> "id",
			"banned_untill"			=> "unbandate",
		    "banreason"             => "banreason",
		    "banactive"             => "active",
		//
		// IP Ban Table
		//
		"ip_banned"                 => "ip_banned",
			"banned_untill"			=> "unbandate",
		    "banreason"             => "banreason",
		//
		// Characters Table
		//
		"characters"				=> "characters",  //table 'characters'
			"characters_acct"		=> "account",
			"characters_guid"		=> "guid",
			"characters_name"		=> "name",
			"characters_honorPoints"=> "totalHonorPoints",
			"characters_killsLifeTime"=>"totalKills",
			"characters_online"		=> "online",
			"characters_level"		=> "level",
			"characters_class"		=> "class",
			"characters_race"		=> "race",
			"characters_gender"		=> "gender",
			"characters_gold"		=> "money",
		//
		// Item Table
		//
		"items"						=> "item_template",
			"items_name1"			=> "name",
			"items_quality"			=> "Quality",
			"items_entry"			=> "entry",
		//
		// Tickets
		//
		"gm_tickets"				=> "gm_tickets",
			"gm_tickets_guid"		=> "guid",
			"gm_tickets_playerGuid"	=> "playerGuid",
			"gm_tickets_timestamp" 	=> "timestamp",
			"gm_tickets_message" 	=> "message",
		
);

//races
//1 Human
//3 Dwarf
//4 Nightelf
//7 Gnome
//11 Draenei
	
//2 Orc
//5 Undead
//6 Taoren
//8 Troll
//10 Bloodelf
