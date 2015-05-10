<?php

include_once 'engine/initialize.php';

define('init_pages', true);

$pageName = (isset($_GET['page']) and !empty($_GET['page'])) ? $_GET['page'] : 'home';

//list the allowed pages
$allowed = array(
	'home',
	'armory',
	'login',
	'loginb',
	'register',
	'account',
	'store',
	'vote',
	'buycoins',
	'news',
	'unstuck',
	'cactivity',
	'sactivity',
	'acactivity',
	'store_complete',
	'changepass',
	'changemail',
	'changedname',
	'teleporter',
	'settings',
	'factionchange',
	'recustomization',
	'levels',
	'itemsets',
	'addons',
	'working_content',
	'howto',
	'media',
	'open-video',
	'upload-screanshot',
	'all-wallpapers',
	'all-screenshots',
	'all-videos',
	'recruit-a-friend',
	'bugtracker',
	'bugtracker_submit',
	'bugtracker-search',
	'changelogs',
	'terms-before-register',
	'notification',
	'purchase-gcoins',
	'password_recovery',
	'terms-of-use',
	'bugtracker-search-results',
	'features',
	'downloads',
	'pm',
	'pm-conversation',
	'pm-send',
	'realm-details',
	'items_refund',
	'references',
	'rules',
	'avatars',
	'articles',
	'article',
	'boosts',
	'profile',
	'article=1',
	'article=2',
	'article=3',
	'purchase_gold',
	'pcode',
	'buy-gcoins',
);

//Check for notifications
if ($pageName != 'notification' and $NOTIFICATIONS->Check())
{
	//append the current page URL to the first notification for return
	if ($NOTIFICATIONS->AppendUrlToFirst())
	{
		//Go to the notifications page
		$NOTIFICATIONS->Launch();
	}
}

//Moving this to the page itself
//$CORE->LoadHeader();

if (in_array($pageName, $allowed))
{
	if (!file_exists($config['RootPath'] . '/template/pages/'.$pageName.'.php'))
	{
		echo 'Error: The page file is missing.';
	}
	else
	{	
		include_once $config['RootPath'] . '/template/pages/'.$pageName.'.php';
	}
}
else
{
	echo 'Error: Page not allowed.';
}

//Moving this to the page itself
//$CORE->LoadFooter();

//free up some memory
unset($allowed);
unset($pageName);

//Run a complete shutdown
Shutdown::Execute();

exit;