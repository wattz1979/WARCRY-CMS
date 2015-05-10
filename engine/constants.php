<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Ranking System
define('RANK_ROOKIE', 0);
define('RANK_PARTICIPANT', 1);
define('RANK_MEMBER', 2);
define('RANK_VETERAN', 3);
define('RANK_SENIOR_MEMBER', 4);
define('RANK_ADDICT', 5);
//Staff Ranks
define('RANK_STAFF_MEMBER', 6);
define('RANK_GM', 7);
define('RANK_SENIOR_GM', 8);
define('RANK_LEAD_GM', 9);
define('RANK_CM', 10);
define('RANK_SENIOR_CM', 11);
define('RANK_LEAD_CM', 12);
define('RANK_DEV', 13);
define('RANK_LEAD_DEV', 14);
define('RANK_MANAGEMENT', 15);

//Promo Codes System
define('PCODE_USAGE_ONCE', 0);
define('PCODE_USAGE_PER_ACC', 1);

define('PCODE_REWARD_CURRENCY_S', 1);
define('PCODE_REWARD_CURRENCY_G', 2);
define('PCODE_REWARD_ITEM', 3);

//Template System
define('RESOURCE_LOAD_PRIO_HIGH', 1);
define('RESOURCE_LOAD_PRIO_LOW', 2);

//Avatar System
define('AVATAR_TYPE_GALLERY', 0);
define('AVATAR_TYPE_UPLOAD', 1);

//characters
define('FACTION_ALLIANCE', 1);
define('FACTION_HORDE', 2);

//media types
define('TYPE_SCREENSHOT', 1);
define('TYPE_WALLPAPER', 2);

//screenshop status types
define('SCREENSHOT_STATUS_PENDING', 0);
define('SCREENSHOT_STATUS_APPROVED', 1);
define('SCREENSHOT_STATUS_DENIED', 2);

//Currencies
define("CURRENCY_SILVER", 1);
define("CURRENCY_GOLD", 2);

//coin activity
define('CA_SOURCE_TYPE_NONE', 0);
define('CA_SOURCE_TYPE_PURCHASE', 1);
define('CA_SOURCE_TYPE_REWARD', 2);
define('CA_SOURCE_TYPE_DEDUCTION', 3);
define('CA_COIN_TYPE_SILVER', 1);
define('CA_COIN_TYPE_GOLD', 2);
define('CA_EXCHANGE_TYPE_PLUS', 1);
define('CA_EXCHANGE_TYPE_MINUS', 2);

//RAF System
define('RAF_LINK_PENDING', 0);
define('RAF_LINK_ACTIVE', 1);

//Social Networks
define('APP_FACEBOOK', 1);
define('APP_TWITTER', 2);
define('STATUS_POSITIVE', 1);
define('STATUS_NEGATIVE', 0);

//Changelogs
define('CHANGELOG_WEB', 1);
define('CHANGELOG_CORE', 2);
define('CHANGELOG_PERPAGE', 30);

//Transaction Logs
define('TRANSACTION_LOG_TYPE_NONE', 0);
define('TRANSACTION_LOG_TYPE_NORMAL', 1);
define('TRANSACTION_LOG_TYPE_URGENT', 2);

//Bug tracker
//Main Categories
define('BT_CAT_WEBSITE', 1);
define('BT_CAT_WOTLK_CORE', 2);
//issue approval statuses
define('BT_APP_STATUS_PENDING', 0);
define('BT_APP_STATUS_APPROVED', 1);
define('BT_APP_STATUS_DECLINED', 2);
//bug priorities
define('BT_PRIORITY_NONE', 0);
define('BT_PRIORITY_LOW', 1);
define('BT_PRIORITY_NORMAL', 2);
define('BT_PRIORITY_HIGH', 3);
//bug statuses
define('BT_STATUS_NEW', 0);
define('BT_STATUS_OPEN', 1);
define('BT_STATUS_ONHOLD', 2);
define('BT_STATUS_DUPLICATE', 3);
define('BT_STATUS_INVALID', 5);
define('BT_STATUS_WONTFIX', 6);
define('BT_STATUS_RESOLVED', 7);

//Item Refund System
define('IRS_STATUS_NONE', 0);
define('IRS_STATUS_REFUNDED', 1);
define('IRS_STATUS_ERROR', 2);

//Boosts System
define('BOOST_DURATION_10', 1);
define('BOOST_DURATION_15', 2);
define('BOOST_DURATION_30', 3);

########################################
### FORUM FLAGS SECTION ################

//Forum flags
define('WCF_FLAGS_CLASSES_LAYOUT', 1);

//Topic flags

//Post flags
define('WCF_FLAGS_STAFF_POST', 1);


