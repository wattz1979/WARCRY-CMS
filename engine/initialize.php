<?php

require_once 'C:\xampp\htdocs\obsidianwow\engine\core.php';

if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//setup the error handler
$ERRORS = new multipleErrors('CORE');

//Start the core
$CORE = new CORE();
	########################
	# Register Core Modules
	try
	{
		//Basic Modules
		$CORE->register_Module('news', 'CORE');
		$CORE->register_Module('pagination', 'CORE');
		$CORE->register_Module('paginationType2', 'CORE');
		$CORE->register_Module('pagination.forum', 'CORE');
		$CORE->register_Module('phpmailer', 'CORE');
		$CORE->register_Module('file.editor', 'CORE');
		$CORE->register_Module('accounts.recovery', 'CORE');
		$CORE->register_Module('accounts.register', 'CORE');
		$CORE->register_Module('purchaseLog', 'CORE');
		$CORE->register_Module('bbcodeParser', 'CORE');
		$CORE->register_Module('img.manipulation', 'CORE');
		$CORE->register_Module('coin.activity', 'CORE');
		$CORE->register_Module('raf', 'CORE');
		$CORE->register_Module('tokens', 'CORE');
		$CORE->register_Module('email.reservation', 'CORE');
		$CORE->register_Module('text.captcha', 'CORE');
		$CORE->register_Module('accounts.finances', 'CORE');
		$CORE->register_Module('transaction.logging', 'CORE');
		$CORE->register_Module('item.refund.system', 'CORE');
		$CORE->register_Module('forums.base', 'CORE');
		$CORE->register_Module('forums.parser', 'CORE');
		$CORE->register_Module('facebook.api', 'CORE');
		$CORE->register_Module('promo.codes', 'CORE');
		$CORE->register_Module('articles.base', 'CORE');
		//Server Modules
		$CORE->register_Module('columns', 'SERVER', $server_config['CORE']);
		$CORE->register_Module('account', 'SERVER', $server_config['CORE']);
		$CORE->register_Module('realm.stats', 'SERVER', $server_config['CORE']);
		$CORE->register_Module('character', 'SERVER', $server_config['CORE']);
		$CORE->register_Module('commands', 'SERVER', $server_config['CORE']);
	} 
	catch (Exception $e)
	{
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	//open database connection
	$DB = $CORE->DatabaseConnection();
	//unset the config variables
	unset($config['DatabaseName'], $config['DatabaseHost'], $config['DatabaseUser'], $config['DatabasePass'], $config['DatabaseEncoding']);
	
	//Open database connection to auth
	$AUTH_DB = $CORE->AuthDatabaseConnection();
	//unset the config variables
	unset($auth_config);

	//Load necessary server modules
	$CORE->load_ServerModule('columns');
	$CORE->load_ServerModule('account');

//starting the session class and defining it
$SESSION = new Session();
	//setting up session handlers from our PHP Class sessions
	$SESSION->register();

//setup the security class
$SECURITY = new Security();
	//Unregistring globals for security
	$SECURITY->unregisterGlobals();
	//filter the request methods
	$SECURITY->RestrictHttpMethods(array('POST', 'GET'));
	//check if the session has expired
	$SECURITY->CheckSessionLife();
		
//setup Current User class
$CURUSER = new CURUSER();

//setup the Notifications class
$NOTIFICATIONS = new Notifications();

//setup the Cache
$CACHE = new Cache(array('repo' => $config['RootPath'] . '/cache'));

//Setup the Template class
$TPL = new Template();

##############################################
## Make an User Check 

	server_Account::RememberMeCheck();
	server_Account::userCheck();

##############################################
