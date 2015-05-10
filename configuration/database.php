<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Default PDO Error handler
$PDO_config['errorHandler'] = PDO::ERRMODE_WARNING;
//Default PDO Fetch Mode
$PDO_config['fetch'] = PDO::FETCH_ASSOC;

//Website Database Connection Info
$config['DatabaseHost'] = 'localhost';
$config['DatabaseUser'] = 'root';
$config['DatabasePass'] = '';
$config['DatabaseName'] = 'warcry';
$config['DatabaseEncoding'] = 'utf8';
