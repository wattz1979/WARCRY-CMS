<?PHP
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li class="current"><a href="index.php?page=forums">Forums</a></li>
		<li><a href="index.php?page=forum-cats">Categories</a></li>
	</ul>
</nav>

<?php
//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_FORUMS))
{
	$CORE->ErrorBox('You do not have the required permissions.');
}
?>
    
<!-- The content -->
<section id="content">

    <div class="tab" id="maintab">
    
    </div>