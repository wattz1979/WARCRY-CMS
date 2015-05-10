<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

require_once $config['RootPath'] . '/engine/phpmailer/class.phpmailer.php';

?>