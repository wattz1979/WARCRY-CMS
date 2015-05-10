<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Website Database Connection Info
$server_config['CORE'] = 'trinity';

//Realms configuration
$realms_config[1] = array(
	'name' 			=> 'TEST SERVER', 
	'descr' 		=> 'Blizzlike', 	
	'Database' 		=> array(
		'host' 		=> 'localhost', 
		'name' 		=> 'characters', 		
		'user' 		=> 'root', 
		'pass' 		=> '', 
		'encoding' 	=> 'utf8'
	), 
	'address' 		=> '127.0.0.1',
	'port' 			=> '8085',
	'soap_protocol' => 'http',
	'soap_address'  => '127.0.0.1',
	'soap_port'     => '7878',
	'soap_user'     => 'Keithus',
	'soap_pass'     => 'wattzhammer',
	'UPDATE_TIME' 	=> '10 minutes',
);