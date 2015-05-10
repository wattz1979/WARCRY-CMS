<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Facebook api
$config['FACEBOOK']['appId'] = '129961437175311';
$config['FACEBOOK']['secret'] = '';
$config['FACEBOOK']['pageID'] = '319447038092110';
$config['FACEBOOK']['pageURL'] = 'http://www.facebook.com/pages/Warcry-WoW/319447038092110';
$config['FACEBOOK']['liked_text'] = 'You liked WARCRY WoW on Facebook';

//Twitter
$config['TWITTER']['page'] = 'warcrywow';
$config['TWITTER']['following_text'] = 'You are following WARCRY WoW (@warcrywow) on Twitter';
