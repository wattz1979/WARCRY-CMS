<?php
if (!defined('init_template'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />

    <title>Admin Control Panel</title>

    <link rel="stylesheet" href="template/css/reset.css" />
    <link rel="stylesheet" href="template/css/visualize.css" />
    <link rel="stylesheet" href="template/css/buttons.css" />
    <link rel="stylesheet" href="template/css/checkboxes.css" />
    <link rel="stylesheet" href="template/css/inputtags.css" />
    <link rel="stylesheet" href="template/css/markitup.css" />
    <link rel="stylesheet" href="template/css/jquery.Jcrop.css" />
    <link rel="stylesheet" href="template/css/main.css" />
    <link rel="stylesheet" href="template/css/datatables.css" />
    <link rel="stylesheet" href="template/css/fileuploader.css" />
    <link rel="stylesheet" href="template/css/shadowbox.css" />
    <link rel="stylesheet" href="template/css/edit-box.css" />
    <link rel="stylesheet" href="template/css/bbcode-default.css" />
    <link rel="stylesheet" href="template/css/media.css" />

    <!--[if lt IE 9]>
    <link rel="stylesheet" href="/admincp/css/ie.css" />
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script type="text/javascript" src="<?php echo $config['WoWDB_URL']; ?>/power.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script>
		var $currentTab = null;
		
		$(function()
		{
			//set the current tab variable
			$currentTab = $('#maintab');
		});
	</script>
  </head>

  <body>

        <div id="container">
        
          <header>
          
            <!-- Logo -->
            <h1 id="logo">Admin Control Panel</h1>
          
            <!-- User info -->
            <div id="userinfo">
              <div class="intro">
                <br>Welcome <strong><?php echo $CURUSER->get('displayName'); ?></strong>!&nbsp;&nbsp;&nbsp;&nbsp;<br />
              </div>
            </div>
          
          </header>
        
          <!-- The application "window" -->
          <div id="application">
			
            <?php
			
				$MENU = array(
					0	=> array('title' => 'Dashboard', 			'page' => 'home', 					'icon' => 'dashboard', 	'permission' => false),
					1	=> array(
						'title' => 'News Management',
						'page' => 'news,news-post,news-edit',
						'icon' => 'pencil',
						'permission' => PERMISSION_NEWS
					),
					2	=> array('title' => 'Articles Management',	'page' => 'articles,new-article',	'icon' => 'pencil',		'permission' => PERMISSION_ARTICLES),
					3	=> array('title' => 'Item Store', 			'page' => 'store,store-add', 		'icon' => 'pencil', 	'permission' => PERMISSION_STORE),
					4	=> array('title' => 'Premium Store', 		'page' => 'pstore', 				'icon' => 'pencil', 	'permission' => PERMISSION_PSTORE),
					5	=> array(
						'title' => 'Media',
						'page' => 'media,movie-add,screenshots',
						'icon' => 'modal',
						'permission' => array(PERMISSION_MEDIA_MOVIES, PERMISSION_MEDIA_SREENSHOTS)
					),
					6	=> array('title' => 'Forums Management',	'page' => 'forums,forum-cats', 		'icon' => 'pencil', 	'permission' => array(PERMISSION_FORUMS, PERMISSION_FORUM_CATS)),
					7	=> array(
						'title' => 'Logs',
						'page' => 'logs,logs-fc,logs-customiz,logs-as,logs-sp,logs-igg,logs-lvl,logs-pw,logs-boost',
						'icon' => 'newspaper',
						'permission' => PERMISSION_LOGS
					),
					8	=> array('title' => 'Promo Codes', 			'page' => 'pcodes', 				'icon' => 'newspaper', 	'permission' => PERMISSION_PROMO_CODES),
					9	=> array('title' => 'Users', 				'page' => 'users,user-preview', 	'icon' => 'newspaper', 	'permission' => PERMISSION_PREV_USERS),
					10	=> array('title' => 'Bug Tracker', 			'page' => 'bugtracker', 			'icon' => 'newspaper', 	'permission' => PERMISSION_PREV_BUGTRACKER),
					11	=> array('title' => 'GM Tickets', 			'page' => 'tickets', 				'icon' => 'newspaper', 	'permission' => PERMISSION_TICKETS),
				);
            ?>
            
            <!-- Primary navigation -->
            <nav id="primary">
              <ul>
				
                <?php
					//Print the menu
					foreach ($MENU as $i => $menuItem)
					{
						$isAllowed = false;
						//Determine if we're allowed to use this button, pages...
						if (!$menuItem['permission'])
						{
							//the page does not require permissions
							$isAllowed = true;
						}
						else if (!is_array($menuItem['permission']) && $CURUSER->getPermissions()->isAllowed($menuItem['permission']))
						{
							//the page is allowed, no multiple pages
							$isAllowed = true;
						}
						else if (is_array($menuItem['permission']))
						{
							//we've got multiple permissions
							foreach ($menuItem['permission'] as $reqPermission)
							{
								if ($CURUSER->getPermissions()->isAllowed($reqPermission))
								{
									//if the user meets one of the required permissions
									$isAllowed = true;
									break;
								}
							}
						}
						
						//check if we have permissions to use the given page
						if ($isAllowed)
						{
							$isActive = false;
							
							//check for multiple pages activation
							if (strstr($menuItem['page'], ','))
							{
								$pages = explode(',', $menuItem['page']);
								
								//check if the current page
								if (in_array($pageName, $pages))
									$isActive = true;
							}
							else
							{
								$isActive = ($menuItem['page'] == $pageName) ? true : false;
							}
							
							echo '
							<li ', ($isActive ? 'class="current"' : ''), '>
							  <a href="index.php?page=', (isset($pages) ? $pages[0] : $menuItem['page']), '">
								<span class="icon ', $menuItem['icon'], '"></span>
								', $menuItem['title'], '
							  </a>
							</li>';
							
							unset($isActive, $pages);
						}
						
						unset($isAllowed);
                  	}
					unset($MENU, $i, $menuItem);
              	?>
                               
              </ul>
            
              <input type="text" id="search" placeholder="Realtime search..." />
            </nav>
          