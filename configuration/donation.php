<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

##################################################
## PAYPAL CONFIG #################################
	
	$config['payments']['paypal']['notify_url'] = $config['BaseURL'] . '/ipn_paypal.php';
	$config['payments']['paypal']['url'] = 'www.paypal.com'; //change for sandbox testing
	$config['payments']['paypal']['email'] = 'sales@localhost';
	$config['payments']['paypal']['currecy'] = 'USD';
	$config['payments']['paypal']['currecySymbol'] = '$';