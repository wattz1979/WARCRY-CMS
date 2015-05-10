<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Shutdown
{
	//Unset the controllers
	static function UnsetControllers()
	{
		unset($GLOBALS['CORE']);
		unset($GLOBALS['ERRORS']);
		unset($GLOBALS['CURUSER']);
		unset($GLOBALS['SECURITY']);
	}

	//unset the databases
	static function UnsetDatabases()
	{
		unset($GLOBALS['AUTH_DB']);
		unset($GLOBALS['DB']);
	}

	//unset the configs
	static function UnsetConfigs()
	{
		unset($GLOBALS['config']);
		unset($GLOBALS['realms_config']);
		unset($GLOBALS['server_config']);
		unset($GLOBALS['PDO_config']);
		unset($GLOBALS['auth_config']);
	}
	
	static function Execute()
	{
		self::UnsetConfigs();
		self::UnsetDatabases();
		self::UnsetControllers();
	}
}