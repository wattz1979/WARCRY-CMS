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
		<li class="current"><a href="index.php?page=news">News</a></li>
		<li><a href="index.php?page=news-post">Post</a></li>
		<li><a href="#thirdtab">Settings</a></li>
	</ul>
</nav>

<?php
//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_NEWS))
{
	$CORE->ErrorBox('You do not have the required permissions.');
}
?>
  
<!-- The content -->
<section id="content">

    <div class="tab" id="maintab">
        <h2>News Management</h2>

		<?php
		if ($error = $ERRORS->DoPrint('deleteNews'))
		{
			echo $error;
			unset($error);
		}			
		if ($success = $ERRORS->successPrint('deleteNews'))
		{
			echo $success;
			unset($success);
		}			
		if ($success = $ERRORS->successPrint('addNews'))
		{
			echo $success;
			unset($success);
		}			
		if ($success = $ERRORS->successPrint('editNews'))
		{
			echo $success;
			unset($success);
		}			

		?>
		  
        <table class="datatable">
        
            <thead>
              <tr>
                <th>Headline</th>
                <th>Posted</th>
                <th>Posted by</th>
                <th>Actions</th>
              </tr>
            </thead>
            
            <tbody>
            
            <?php
            
            $res = $DB->query("SELECT * FROM `news` ORDER BY id DESC");
            
            if ($res->rowCount() > 0)
            {
                while ($arr = $res->fetch())
                {
                    echo '
                      <tr>
                        <td>', $arr['title'], '</td>
                        <td>', $arr['added'], '</td>
                        <td>', $arr['authorStr'], '</td>
                        <td>
                          <span class="button-group">
                            <a href="index.php?page=news-edit&id=', $arr['id'], '" class="button icon edit">Edit</a>
                            <a href="execute.php?take=delete&action=news&id=', $arr['id'], '" onclick="return deletecheck(\'Are you sure you want to delete those news?\');" class="button icon remove danger">Remove</a>
                          </span>
                        </td>
                      </tr>';
                }
            }
            unset($res);
              
            ?>
            
            </tbody>
        
        </table>

	</div>

<script src="template/js/jquery.color.js" type="text/javascript"></script>
<script src="template/js/jquery.datatables.js" type="text/javascript"></script>

<script type="text/javascript">
	var $configURL = '<?php echo $config['BaseURL']; ?>';

	//apply the BBcode Editors	
	$(document).ready(function(e)
	{
		//Init datatables
		if ($(".datatable").length > 0)
		{
			var newsTable = $(".datatable").dataTable({
				"aoColumnDefs": [ 
					{ "bSortable": false, "aTargets": [ 3 ] }
				] 
			});
			//sort the table
			newsTable.fnSort( [ [1, 'desc'] ] );
		}
	});
</script>