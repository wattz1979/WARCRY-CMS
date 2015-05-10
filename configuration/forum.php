<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Records Display Limits
$config['FORUM']['Topics_Limit'] 	= 20;
$config['FORUM']['Posts_Limit'] 	= 10;

//Permissions
$config['FORUM']['Min_Rank_Post_Delete'] 		= RANK_STAFF_MEMBER;
$config['FORUM']['Min_Rank_Post_Edit'] 			= RANK_STAFF_MEMBER;
$config['FORUM']['Min_Rank_Post_View_Deleted']	= RANK_STAFF_MEMBER;