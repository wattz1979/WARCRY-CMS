<?php

require_once 'core.php';

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
		$CORE->register_Module('pagination', 'CORE');
		$CORE->register_Module('paginationType2', 'CORE');
		$CORE->register_Module('phpmailer', 'CORE');
		$CORE->register_Module('file.editor', 'CORE');
		$CORE->register_Module('promo.codes', 'CORE');
		//Server Modules
		$CORE->register_Module('columns', 'SERVER', $server_config['CORE']);
		$CORE->register_Module('account', 'SERVER', $server_config['CORE']);
		$CORE->register_Module('realm.stats', 'SERVER', $server_config['CORE']);
		$CORE->register_Module('character', 'SERVER', $server_config['CORE']);
		$CORE->register_Module('sendmail', 'SERVER', $server_config['CORE']);
	} 
	catch (Exception $e)
	{
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	//open database connection
	$DB = $CORE->DatabaseConnection();
	
	//Open database connection to auth
	$AUTH_DB = $CORE->AuthDatabaseConnection();

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

//setup the Cache
$CACHE = new Cache(array('repo' => $config['RootPath'] . '/cache'));

##############################################
## Make an User Check 
	
	server_Account::userCheck(true);

##############################################
