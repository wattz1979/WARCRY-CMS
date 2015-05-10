<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$config['SiteName'] = 'obsidianwow';

$config['RootPath'] = 'C:\xampp\htdocs\obsidianwow'; 		//(No slash at the end)
$config['BaseURL'] = 'http://localhost/obsidianwow'; 	//(No slash at the end)

//Must be unique for each website
$config['AuthCookieName'] = 'obsidian-wow';

//Minifier Settings
//StyleFolderURL rewrites the URLs for the image in the CSS files
$config['StyleFolderURL'] = 'http://localhost/obsidianwow/template/style/'; //(With slash at the end)

//E-mail Address
$config['Email'] = 'info@localhost';

//Time settings
$config['TimeZone'] = 'Europe/Berlin';
$config['TimeZoneOffset'] = '+1';

//Warcry WoW Database URL
$config['WoWDB_URL'] = 'http://195.154.209.154';	//(No slash at the end)
//Complete URL to the power.js
$config['WoWDB_JS'] = 'http://195.154.209.154/power.js';