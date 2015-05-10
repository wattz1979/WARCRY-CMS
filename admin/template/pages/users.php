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
		<li class="current"><a href="#maintab" onclick="changeCurrentTab('#maintab');">Users</a></li>
	</ul>
</nav>

<?php
//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_PREV_USERS))
{
	$CORE->ErrorBox('You do not have the required permissions.');
}
?>
        
<!-- The content -->
<section id="content">
    <div class="tab" id="maintab">
        <h2>User Management</h2>
        
        <p>TODO: Search Channel Switch</p>
        
        <div>
    
            <table id="datatable" class="datatable">
          
                <thead>
                    <tr>
                        <th>Acc ID</th>
                        <th>User [Display|Account]</th>
                        <th>Rank</th>
                        <th>GM Level</th>
                        <th>Email</th>
                        <th>Register IP</th>
                        <th>Register Date</th>
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
			"sAjaxSource": "ajax.php?phase=4",
			"aoColumnDefs": [ 
				{ "bSortable": false, "aTargets": [ 3 ] },
				{ "bSortable": false, "aTargets": [ 4 ] },
				{ "bSortable": false, "aTargets": [ 6 ] }
			]
		});
	});
</script>