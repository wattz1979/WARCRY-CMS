<?PHP
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'browse';

?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li><a href="index.php?page=logs">Paypal Logs</a></li>
        <li><a href="index.php?page=logs-pw">Paymentwall Logs</a></li>
        <li><a href="index.php?page=logs-sp">Store Purchase Logs</a></li>
		<li class="current"><a href="index.php?page=logs-as">Armor Sets Purchase Logs</a></li>
		<li><a href="index.php?page=logs-lvl">P.Store Level Purchase Logs</a></li>
		<li><a href="index.php?page=logs-fc">P.Store Faction Change Logs</a></li>
        <li><a href="index.php?page=logs-customiz">P.Store Re-customization Logs</a></li>
        <li><a href="index.php?page=logs-igg">P.Store In-Game Gold Logs</a></li>
        <li><a href="index.php?page=logs-boost">Boosts Purchase Logs</a></li>
	</ul>
</nav>

<?php
//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_LOGS))
{
	$CORE->ErrorBox('You do not have the required permissions.');
}
?>

<!-- The content -->
<section id="content">

    <div class="tab" id="maintab">
		<h2>Armor Sets Purchase Logs</h2>
    
		  <table class="datatable">
		  
		    <thead>
		      <tr>
		        <th>ID</th>
		        <th>Text</th>
		        <th>Account</th>
		        <th>Datetime</th>
		        <th>Status</th>
		      </tr>
		    </thead>
		  	
		    <tbody>
		
		    <?php
			
			$res = $DB->query("SELECT * FROM `purchase_log` WHERE `source` = 'PSTORE_ARMORSETS' ORDER BY id DESC");
			
			?>
		    
		    </tbody>
		    
		  </table>
          
	</div>

<script src="template/js/jquery.datatables.js" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function(e)
{	
	//Init datatables
	if ($(".datatable").length > 0)
	{
	 	var newsTable = $(".datatable").dataTable(
		{
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "ajax.php?phase=12",
	      	"aoColumnDefs": [ 
	      		{ "bSortable": false, "aTargets": [ 1 ] }
	    	] 
		});
		//sort the table
		newsTable.fnSort( [ [0, 'desc'] ] );
	}
});
function Toggle(btn)
{
	var parent = $(btn).parent().parent();
		
	if (typeof parent.attr('expanded') == 'undefined' || parent.attr('expanded').length == 0 || parent.attr('expanded') == 'false')
	{
		var height = parent.children('p').height();
		var thisHeight = parent.height();
								
		parent.stop(true, true).animate({ height: height }, 'fast');
		parent.parent().parent().addClass('active-expander');
		parent.attr('expanded', 'true');
		parent.attr('oldHeight', thisHeight);
		$(btn).html('Close');
	}
	else
	{
		parent.stop(true, true).animate({ height: parseInt(parent.attr('oldHeight')) }, 'fast', function()
		{
			parent.parent().parent().removeClass('active-expander');
			$(btn).html('Open');
		});
		parent.attr('expanded', 'false');
	}
	
	return false;
}
</script>