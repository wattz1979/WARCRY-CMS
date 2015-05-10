<?php

include_once 'engine/initialize.php';

define('init_pages', true);
define('is_forums', true);

$pageName = (isset($_GET['page']) and !empty($_GET['page'])) ? $_GET['page'] : 'home';

//list the allowed pages
$allowed = array(
	'home',
	'forum',
	'topic',
	'post_topic',
	'post_reply',
	'edit_reply',
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
	if (!file_exists($config['RootPath'] . '/template/forums/pages/'.$pageName.'.php'))
	{
		echo 'Error: The page file is missing.';
	}
	else
	{
		//Load my forum base module
		$CORE->load_CoreModule('forums.base');
		
		//load the page
		include_once $config['RootPath'] . '/template/forums/pages/'.$pageName.'.php';
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