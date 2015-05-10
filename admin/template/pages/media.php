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
		<li class="current"><a href="index.php?page=media">Movies</a></li>
        <li><a href="index.php?page=movie-add">New Movie</a></li>
		<li><a href="index.php?page=screenshots">Screenshots</a></li>
	</ul>
</nav>

<?php
if ($success = $ERRORS->successPrint(array('add_movie', 'delete_movie')))
{
	echo $success;
}
unset($success);
if ($error = $ERRORS->DoPrint('delete_movie'))
{
	echo $error;
}
unset($error);
		
//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_MEDIA_MOVIES))
{
	$CORE->ErrorBox('You do not have the required permissions.');
}
?>

<!-- The content -->
<section id="content">

    <div class="tab" id="maintab">
      	<h2>Movies Management</h2>
        
        <div>
            
            <?php
				//Pull them movies
				$res = $DB->query("SELECT `id`, `name`, `image`, `dirname` FROM `movies` ORDER BY `id` DESC;");
				
				if ($res->rowCount() > 0)
				{
					echo '<ul class="imagelist">';
					
					while ($arr = $res->fetch())
					{
						echo '
						<li>
							<img src="', $config['BaseURL'], '/uploads/media/movies/', $arr['dirname'], '/thumbnails/medium_', $arr['image'], '" alt="', stripslashes($arr['name']), '" style="opacity: 1;">
							<span>
								<a href="', $config['BaseURL'], '/index.php?page=open-video&id=', $arr['id'], '" target="_new" class="name ajax cboxElement">', substr(stripslashes($arr['name']), 0, 20), (strlen(stripslashes($arr['name'])) > 20 ? '...' : ''), '</a>
								<a href="#" class="edit ajax cboxElement"></a>
								<a href="execute.php?take=delete&action=movie&id='.$arr['id'].'" class="delete" onclick="return deletecheck(\'Are you sure you want to delete this movie?\');"></a>
							</span>
						</li>';
					}
					
					echo '</ul>';
				}
				else
				{
					echo '<p>There are no movies.</p>';
				}
				unset($res);
			?>
            
        </div>
        <div class="clear"></div>
        
    </div>

<script>
	$(document).ready(function()
	{
		$('.imagelist img').hover(function()
		{
			console.log('test');
			$(this).stop().animate({ opacity: '0.75'}, 'fast');
		},
		function()
		{
			$(this).stop().animate({ opacity: '1'}, 'fast');
		});
	});
</script>