<?php

include_once 'engine/initialize.php';

define('init_pages', true);

$pageName = (isset($_GET['page']) and !empty($_GET['page'])) ? $_GET['page'] : 'home';

//list the allowed pages
$allowed = array(
	'home',
	'bugtracker',
	'news',
	'news-post',
	'news-edit',
	'media',
	'movie-add',
	'screenshots',
	'logs',
	'logs-pw',
	'logs-sp',
	'logs-as',
	'logs-lvl',
	'logs-fc',
	'logs-customiz',
	'logs-igg',
	'logs-boost',
	'pstore',
	'tickets',
	'forums',
	'forum-cats',
	'users',
	'user-preview',
	'pcodes',
	'articles',
	'new-article',
	'edit-article',
	'store',
	'store-add',
);

ob_start();

$CORE->loggedInOrReturn();

$CORE->LoadHeader();

if (in_array($pageName, $allowed))
{
	if (!file_exists($config['RootPath'] . '/admin/template/pages/'.$pageName.'.php'))
	{
		ob_end_clean();
		echo 'Error: The page file is missing.';
		die;
	}
	else
	{
		include_once $config['RootPath'] . '/admin/template/pages/'.$pageName.'.php';
	}
}
else
{
	ob_end_clean();
	echo 'Error: Page not allowed.';
	die;
}

$CORE->LoadFooter();

exit;