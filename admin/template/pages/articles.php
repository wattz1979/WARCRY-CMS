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
		<li class="current"><a href="?page=articles">Articles</a></li>
        <li><a href="?page=new-article">New Article</a></li>
	</ul>
</nav>
    
<?php
//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_ARTICLES))
{
	$CORE->ErrorBox('You do not have the required permissions.');
}
?>
      
<!-- The content -->
<section id="content">
    <div class="tab" id="maintab">
        <h2>Articles Management</h2>
        
        <?php
		if ($success = $ERRORS->successPrint(array('addArticle', 'editArticle')))
		{
			echo $success;
		}
		unset($success);
		?>
        
        <div>
    
            <table id="datatable" class="datatable">
          
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Short Text</th>
                        <th>Views</th>
                        <th>Added</th>
                        <th>Author</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="sortable">
            
                </tbody>
            
            </table>
        
        </div>
    </div>

<script src="template/js/jquery.form.js" type="text/javascript"></script>
<script src="template/js/jquery.datatables.js" type="text/javascript"></script>

<script type="text/javascript">
	$(document).ready(function(e)
	{
		$('#datatable').dataTable(
		{
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "ajax.php?phase=2",
			"aoColumnDefs": [ 
				{ "bSortable": false, "aTargets": [ 2 ] },
				{ "bSortable": false, "aTargets": [ 6 ] }
			]
		});
	});
</script>