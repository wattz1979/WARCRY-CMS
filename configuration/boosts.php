<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Boosts Price per Duration
$config['BOOSTS']['PRICEING'] = array(
	BOOST_DURATION_10 => array(CURRENCY_SILVER => 100, CURRENCY_GOLD => 10),
	BOOST_DURATION_15 => array(CURRENCY_SILVER => 140, CURRENCY_GOLD => 14),
	BOOST_DURATION_30 => array(CURRENCY_SILVER => 270, CURRENCY_GOLD => 27)
);