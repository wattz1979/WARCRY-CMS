<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

define('init_config', true);

include 'basic.php';
include 'server.php';
include 'database.php';
include 'authentication.php';
include 'donation.php';
include 'sessions.php';
include 'vote.php';
include 'captcha.php';
include 'important_notice.php';
include 'forum.php';
include 'boosts.php';
include 'social.php';