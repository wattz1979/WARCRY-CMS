<?PHP
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$RealmID = isset($_GET['realm']) ? (int)$_GET['realm'] : false;
$iclosed = isset($_GET['iclosed']) ? (int)$_GET['iclosed'] : 0;

//Save selected realm in a session
if ($RealmID !== false)
{
	$_SESSION['ADMIN_SelectedRealm'] = isset($realms_config[$RealmID]) ? $RealmID : 1;
}

$RealmID = isset($_SESSION['ADMIN_SelectedRealm']) ? (int)$_SESSION['ADMIN_SelectedRealm'] : 1;

?>

<!-- Secondary navigation -->
<nav id="secondary">
	<ul>
		<li class="current"><a href="#maintab" onclick="changeCurrentTab('#maintab');">Browse</a></li>
	</ul>
</nav>

<?php
//Try the realm
if (!($REALM_DB = $CORE->RealmDatabaseConnection($RealmID)))
{
	$CORE->ErrorBox('Unable to connect to the realm database.');
}
//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_TICKETS))
{
	$CORE->ErrorBox('You do not have the required permissions.');
}
?>

<!-- The content -->
<section id="content">

<div class="tab" id="maintab">
	<h2>GM Tickets</h2>
    	
        <br />
        <div>
        	<?php
				//Realm selection
				if (isset($realms_config) && count($realms_config) > 1)
				{
					echo '
					<div style="width: 200px; display: inline-block; vertical-align: middle; margin-right: 10px;">
						<select name="realm" id="realm-select">';
						
						foreach ($realms_config as $id => $realmData)
						{
							echo '<option value="', $id, '" ', ($id == $RealmID ? 'selected="selected"' : ''), '>', $realmData['name'], '</option>';
						}
						
						echo '
						</select>
					</div>';
				}
				
				//Closed inclusion
           		echo ($iclosed == 1) ? 
				'<a href="'.$config['BaseURL'].'/admin/index.php?page=tickets&iclosed=0">Exclude Closed</a>' : 
				'<a href="'.$config['BaseURL'].'/admin/index.php?page=tickets&iclosed=1">Include Closed</a>';
			?>
            
            <script>
				$(function()
				{
					$('#realm-select').on('change', function()
					{
						window.location = 'index.php?page=tickets&realm=' + $(this).find('option:selected').val();
					});
				});
			</script>
            
        </div>
        <br /><br />
        
		  <table class="datatable">
		  
		    <thead>
		      <tr>
		        <th>ID</th>
		        <th>Text</th>
                <th>Ticket By</th>
                <th>Status</th>
		        <th>Comment</th>
                <th>Views</th>
		      </tr>
		    </thead>
		  	
		    <tbody>
		
		    <?php
			
			if ($iclosed == 1)
			{
				$where = "";
			}
			else
			{
				$where = "WHERE `gm_tickets`.`closedBy` = '0'";
			}
				
			$res = $REALM_DB->prepare("SELECT `gm_tickets`.`ticketId`, 
										`gm_tickets`.`message`, 
										`gm_tickets`.`guid`, 
										`gm_tickets`.`name`, 
										`gm_tickets`.`createTime`, 
										`gm_tickets`.`closedBy`, 
										`gm_tickets`.`assignedTo`, 
										`gm_tickets`.`comment`, 
										`gm_tickets`.`viewed` 
								FROM `gm_tickets`
								".$where."
								ORDER BY `gm_tickets`.`createTime` DESC;");
			$res->execute();
			
			if ($res->rowCount() > 0)
			{
				while ($arr = $res->fetch())
				{
					if ($arr['closedBy'] == 0)
					{
						$status = 'Open';
						//Check if the user is online
						$res2 = $REALM_DB->prepare("SELECT `online` FROM `characters` WHERE `guid` = :guid LIMIT 1;");
						$res2->bindParam(':guid', $arr['guid'], PDO::PARAM_INT);
						$res2->execute();
						
						if ($res2->rowCount() > 0)
						{
							$char = $res2->fetch();
							
							if ($char['online'] == 0)
							{
								$online = '[offline]';
							}
							else
							{
								$online = '[online]';
							}
							unset($char);
						}
						unset($res2);
					}
					else
					{
						$status = 'Closed';
					}
						
					echo '
				    <tr valign="top">
				    	<td style="vertical-align: top !important;">', $arr['ticketId'], '</td>
				        <td style="vertical-align: top !important; padding: 0 !important;">
						  	<div class="datatable-expander" style="height: 16px; position: relative;">
						  		<p style="max-width: 700px;"><strong>', $arr['message'], '</strong></p>
								<span style="position: absolute; top: 1px; right: 0px;">
									<a href="#" onclick="return Toggle(this);">Open</a>
								</span>
						  	</div>
						</td>
						<td style="vertical-align: top !important;">', $arr['name'], ' ', (isset($online) ? $online : ''), '</td>
						<td style="vertical-align: top !important;">', $status, '</td>
				        <td style="vertical-align: top !important;">', $arr['comment'], '</td>
						<td style="vertical-align: top !important;">', $arr['viewed'], '</td>
				   	</tr>';
				}
				unset($arr);
			}
			unset($res);
			  
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
	 	var newsTable = $(".datatable").dataTable({
	      	"aoColumnDefs": [ 
	      		{ "bSortable": false, "aTargets": [ 1 ] }
	    	],
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

<?php
	unset($REALM_DB);
?>