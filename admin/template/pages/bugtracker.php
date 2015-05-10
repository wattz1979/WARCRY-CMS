<?PHP
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'browse';
$filter = isset($_GET['filter']) ? ($_GET['filter'] != '-1' ? (int)$_GET['filter'] : false) : false;

//print error messages
if ($error = $ERRORS->DoPrint(array('approve_report', 'edit_report', 'delete_report')))
{
	echo $error;
	unset($error);
}
//print success messages
if ($success = $ERRORS->successPrint(array('approve_report', 'edit_report', 'delete_report')))
{
	echo $success;
	unset($success);
}			

?>

<!-- Secondary navigation -->
<nav id="secondary">
	<ul>
		<li class="current"><a href="#maintab" onclick="changeCurrentTab('#maintab');">Browse</a></li>
	</ul>
</nav>

<?php
//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_PREV_BUGTRACKER))
{
	$CORE->ErrorBox('You do not have the required permissions.');
}
?>
    
<!-- The content -->
<section id="content">

<div class="tab" id="maintab">
	<h2>Bug Tracker</h2>
	<br />
    <div>
    
    	<script type="text/javascript">
			var filter = <?php echo $filter ? $filter : 'false'; ?>;
			
			$(document).ready(function()
			{
				if (filter !== false)
					$('#filter-select').find('option[value="'+filter+'"]').attr('selected', 'selected');
				//Hotfix for now
				if (filter == 0)
					$('#filter-select').find('option:eq(1)').attr('selected', 'selected');
				
                $('#filter-select').on('change', function()
				{
					var selected = $(this).find('option:selected');
					
					if (selected.length > 0)
					{
						if (selected.val() != '-1')
							document.location.href = "index.php?page=bugtracker&filter=" + selected.val();
					}
				});
            });
		</script>
    	
        <select name="filter" style="width: 200px;" id="filter-select">
        	<option value="-1">Select Status</option>
            <option value="<?php echo BT_STATUS_NEW; ?>">New</option>
            <option value="<?php echo BT_STATUS_OPEN; ?>">Open</option>
            <option value="<?php echo BT_STATUS_ONHOLD; ?>">On hold</option>
            <option value="<?php echo BT_STATUS_DUPLICATE; ?>">Duplicate</option>
            <option value="<?php echo BT_STATUS_INVALID; ?>">Invalid</option>
            <option value="<?php echo BT_STATUS_WONTFIX; ?>">Wont Fix</option>
            <option value="<?php echo BT_STATUS_RESOLVED; ?>">Resolved</option>
        </select>
    </div>
    <br />
    
		  <table class="datatable">
		  
		    <thead>
		      <tr>
		        <th>ID</th>
		        <th>Text</th>
                <th>Category</th>
                <th>Priority</th>
		        <th>Status</th>
                <th>Approval</th>
                <?php echo ($CURUSER->getPermissions()->isAllowed(PERMISSION_MAN_BUGTRACKER) ? '<th>Actions</th>' : ''); ?>
		      </tr>
		    </thead>
		  	
		    <tbody>
		
		    <?php
			
			//Filters
			$where = "";
			
			if ($filter !== false)
			{
				$where = "WHERE `bugtracker`.`status` = :status";
			}
			
			$res = $DB->prepare("SELECT `bugtracker`.`id`, 
										`bugtracker`.`status`, 
										`bugtracker`.`approval`, 
										`bugtracker`.`priority`, 
										`bugtracker`.`maincategory`, 
										`bugtracker`.`category`, 
										`bugtracker`.`subcategory`, 
										`bugtracker`.`title`, 
										`bugtracker`.`content`, 
										`bugtracker`.`added`, 
										`bugtracker`.`account`, 
										`account_data`.`displayName` 
								FROM `bugtracker`
								LEFT JOIN `account_data` 
								ON `bugtracker`.`account` = `account_data`.`id`
								".$where."
								ORDER BY `bugtracker`.`id` DESC;");
			if ($filter !== false)
			{
				$res->bindParam(':status', $filter, PDO::PARAM_INT);
			}
			$res->execute();
			
			if ($res->rowCount() > 0)
			{
				//get the categories
				$CategoryStore = new BTCategories();
				
				while ($arr = $res->fetch())
				{
					//Translate the status
					switch ($arr['status'])
					{
						case BT_STATUS_NEW:
							$status = 'New';
							break;
						case BT_STATUS_OPEN:
							$status = 'Open';
							break;
						case BT_STATUS_ONHOLD:
							$status = 'On hold';
							break;
						case BT_STATUS_DUPLICATE:
							$status = 'Duplicate';
							break;
						case BT_STATUS_INVALID:
							$status = 'Invalid';
							break;
						case BT_STATUS_WONTFIX:
							$status = '';
							break;
						case BT_STATUS_RESOLVED:
							$status = 'Resolved';
							break;
						default:
							$status = 'Unknown';
							break;
					}
					
					//translate the approval
					switch ($arr['approval'])
					{
						case BT_APP_STATUS_APPROVED:
							$approval = 'approved';
							break;
						case BT_APP_STATUS_DECLINED:
							$approval = 'declined';
							break;
						default:
							$approval = 'pending';
							break;
					}
					
					//translate the priority
					switch ($arr['priority'])
					{
						case BT_PRIORITY_LOW:
							$priority = 'Low';
							break;
						case BT_PRIORITY_NORMAL:
							$priority = 'Normal';
							break;
						case BT_PRIORITY_HIGH:
							$priority = 'High';
							break;
						default:
							$priority = 'Abnormal';
							break;
					}
				
					//get the main category
					$MainCategory = $CategoryStore->getMainCategory($arr['maincategory']);
	
					switch ($arr['maincategory'])
					{
						case BT_CAT_WEBSITE:
							$MainCategoryName = 'Website';
							break;
						case BT_CAT_WOTLK_CORE:
							$MainCategoryName = 'WotLK Core';
							break;
						default:
							$MainCategoryName = 'Unknown';
							break;
					}
					
					//get the category
					$Category = $MainCategory->getCategory($arr['category']);
					
					if ($Category === false)
					{
						$CategoryName = 'Unknown';
					}
					else
					{
						$CategoryName = $Category->getName();
					}
					
					$SubCategoryName = false;
					//check for sub category
					if ($Category->hasSubCategories())
					{
						$SubCategoryName = $Category->getSubCategoryName($arr['subcategory']);
					}
					
					//free memory
					unset($MainCategory, $Category);
					
					//put the category string together
					$category = $CategoryName;
					if ($SubCategoryName)
					{
						$category .= ' - '.$SubCategoryName;
					}
					
					//free memory
					unset($CategoryName, $SubCategoryName);
					
					echo '
				      <tr valign="top" data-reportid="', $arr['id'], '" class="status-', strtolower($status), '">
				        <td style="vertical-align: top !important;">', $arr['id'], '</td>
				        <td style="vertical-align: top !important; padding: 0 !important;">
						  <div class="datatable-expander" style="height: 16px; width: 400px;" title="Double click to open or close.">
						  	<p><strong>', htmlspecialchars(stripslashes($arr['title'])), '</strong><br><br>', $arr['content'], '<br><br><strong>Added:</strong> ', $arr['added'], ' <strong>by</strong> ', $arr['displayName'], ' [', $arr['account'], ']</p>
						  </div>
						</td>
						<td style="vertical-align: top !important;">', $MainCategoryName, ' - ', $category, '</td>
						<td style="vertical-align: top !important;">', $priority, '</td>
				        <td style="vertical-align: top !important;">', $status, '</td>
						<td style="vertical-align: top !important;">
							<p class="approval-status" style="margin: 0px;">', ucfirst($approval), '</p>
						</td>';
						
						//Is Allowed to manage
						if ($CURUSER->getPermissions()->isAllowed(PERMISSION_MAN_BUGTRACKER))
						{
							echo '
							<td style="vertical-align: top !important;">
								<span class="button-group">
									<a href="#" onclick="return ConstructEdit(', $arr['id'], ');" class="button icon edit">Edit</a>
									<a href="execute.php?take=delete&action=bugreport&id=', $arr['id'], '" onclick="return deletecheck(\'Are you sure you want to delete this bug report?\');" class="button icon remove danger">Remove</a>
									', ($arr['approval'] == BT_APP_STATUS_PENDING ? '
									<a href="#" onclick="return ApproveReport('.$arr['id'].');" class="button icon approve" id="approval-button">Approve</a>
									<a href="#" onclick="return DisapproveReport('.$arr['id'].');" class="button icon trash" id="disapproval-button">Disapprove</a>
									' : ''), '
								</span>
							</td>';
						}
						
						echo '
				      </tr>';
					  
					 unset($category, $priority, $status, $approval);
				}
				unset($CategoryStore, $arr);
			}
			unset($res);
			  
			?>
		    
		    </tbody>
		    
		  </table>

</div>

<script src="template/js/jquery.datatables.js" type="text/javascript"></script>
<script src="template/js/jquery-ui-1.10.0.sortable.min.js" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function(e)
{
	//Init datatables
	if ($(".datatable").length > 0)
	{
	 	var newsTable = $(".datatable").dataTable({
	      	"aoColumnDefs": [ 
	      		{ "bSortable": false, "aTargets": [ 1 ] },
				<?php echo ($CURUSER->getPermissions()->isAllowed(PERMISSION_MAN_BUGTRACKER) ? '{ "bSortable": false, "aTargets": [ 6 ] },' : ''); ?>
	    	],
			"fnDrawCallback": function()
			{
				//remove the click bind
				$('.datatable-expander').off('dblclick');
      			//Bind the click event
			  	$('.datatable-expander').on('dblclick', function(event)
				{
					if (typeof $(this).attr('expanded') == 'undefined' || $(this).attr('expanded').length == 0 || $(this).attr('expanded') == 'false')
					{
						var height = $(this).children('p').height();
						var thisHeight = $(this).height();
												
						$(this).stop(true, true).animate({ height: height }, 'fast');
						$(this).parent().parent().addClass('active-expander');
						$(this).attr('expanded', 'true');
						$(this).attr('oldHeight', thisHeight);
					}
					else
					{
						$(this).stop(true, true).animate({ height: parseInt($(this).attr('oldHeight')) }, 'fast', function()
						{
							$(this).parent().parent().removeClass('active-expander');
						});
						$(this).attr('expanded', 'false');
					}
				});
			}
		});
		//sort the table
		newsTable.fnSort( [ [0, 'desc'] ] );
	}
});

function ConstructEdit(id)
{
	//Check if it's already constructed
	if ($('#editor-'+id).length > 0)
	{
		$('#editor-'+id).fadeIn('fast');
		//break
		return false;
	}
	
	var $id = id;
	
	//Pull the data for this report
	$.ajax({
		type: "GET",
		url: "ajax.php",
		data: { phase: 5, id: $id },
		dataType: 'json',
		cache: false,
		error: function(jqXHR, textStatus, errorThrown)
		{
			console.log(textStatus);
		},
		success: function(data)
		{
		   var $data = data;
		   
		   	//start by constructing overlay
			var Overlay = $('<div class="edit-overlay" id="editor-'+id+'"></div>');
			$('body').append(Overlay);
			Overlay.css({ width: $(window).innerWidth(), height: $(window).innerHeight() });
			
			//create the form container
			var container = $('<div class="edit-container" style="margin-top: 200px;"></div>');
			Overlay.append(container);
			
			//make it draggable
			container.draggable();
			
			//create the form
			var form = $(
					'<h2 style="margin-top:0;">Report Editing</h2>'+
						'<form method="post" action="execute.php?take=edit_bugreport&id='+id+'">'+
							'<input type="hidden" value="'+id+'" name="id" />'+
							'<section>'+
								'<label>Title*</label>'+
								'<div><input type="text" placeholder="Required" class="required" name="title" value="'+$data.title+'" /></div>'+
							'</section>'+
							'<section>'+
								'<label>Content*</label>'+
								'<div><textarea name="content" style="height: 80px;">'+$data.content+'</textarea></div>'+
							'</section>'+
							'<section>'+
								'<label>Priority*</label>'+
								'<div><select name="priority" style="width: 200px; padding: 3px;">'+
									'<option value="1" '+($data.priority == "1" ? 'selected="selected"' : '')+'>Low</option>'+
									'<option value="2" '+($data.priority == "2" ? 'selected="selected"' : '')+'>Normal</option>'+
									'<option value="3" '+($data.priority == "3" ? 'selected="selected"' : '')+'>High</option>'+
								'</select></div>'+
							'</section>'+
							'<section>'+
								'<label>Status*</label>'+
								'<div><select name="status" style="width: 200px; padding: 3px;">'+
									'<option value="0" '+($data.status == "0" ? 'selected="selected"' : '')+'>New</option>'+
									'<option value="1" '+($data.status == "1" ? 'selected="selected"' : '')+'>Open</option>'+
									'<option value="2" '+($data.status == "2" ? 'selected="selected"' : '')+'>On Hold</option>'+
									'<option value="3" '+($data.status == "3" ? 'selected="selected"' : '')+'>Duplicate</option>'+
									'<option value="5" '+($data.status == "5" ? 'selected="selected"' : '')+'>Invalid</option>'+
									'<option value="6" '+($data.status == "6" ? 'selected="selected"' : '')+'>Wontfix</option>'+
									'<option value="7" '+($data.status == "7" ? 'selected="selected"' : '')+'>Resolved</option>'+
								'</select></div>'+
							'</section>'+
							'<section>'+
								'<div>'+
									'<span class="button-group" style="float:left;">'+
										'<a href="#" onclick="return SubmitForm(this);" class="button icon edit">Submit</a>'+
										'<a href="#" onclick="return CloseEditor('+id+');" class="button icon remove danger">Cancel</a>'+
									'</span>'+
									'<div class="clear"></div>'+
								'</div>'+
							'</section>'+
						'</form>');
			container.append(form);
			
			//test
			Overlay.fadeIn('fast');
		}
	});
	
	return false;
}

function CloseEditor(id)
{
	$('#editor-'+id).fadeOut('fast');
}

function SubmitForm(btn)
{
	$(btn).parent().parent().parent().parent().submit();
}

function ApproveReport(id)
{
	var $id = id;
	
	//Run the ajax
	$.ajax({
		type: "GET",
		url: "execute.php",
		data: { take: 'approve_report', id: $id },
		cache: false,
		error: function(jqXHR, textStatus, errorThrown)
		{
			console.log(textStatus);
		},
		success: function(data)
		{
		   var $data = data;
		   
		   //check if it was successful
		   if ($data == 'OK')
		   {
			   //update the text
			   var tr = $('.datatable').find("[data-reportid='" + id + "']");
			   tr.find('.approval-status').html('Approved');
			   //hide the approval buttons
			   tr.find('#approval-button').hide();
			   tr.find('#disapproval-button').hide();
			   //alert success
			   new Notification('The report has been successfully update.', 'success');
		   }
		   else
		   {
			   new Notification($data, 'error', 'urgent');
		   }
		}
	});
	
	return false;
}

function DisapproveReport(id)
{
	var $id = id;
	
	//Run the ajax
	$.ajax({
		type: "GET",
		url: "execute.php",
		data: { take: 'disapprove_report', id: $id },
		cache: false,
		error: function(jqXHR, textStatus, errorThrown)
		{
			console.log(textStatus);
		},
		success: function(data)
		{
		   var $data = data;
		   
		   //check if it was successful
		   if ($data == 'OK')
		   {
			   //update the text
			   var tr = $('.datatable').find("[data-reportid='" + id + "']");
			   tr.find('.approval-status').html('Declined');
			   //hide the approval buttons
			   tr.find('#approval-button').hide();
			   tr.find('#disapproval-button').hide();
			   //alert success
			   new Notification('The report has been successfully update.', 'success');
		   }
		   else
		   {
			   new Notification($data, 'error', 'urgent');
		   }
		}
	});
	
	return false;
}
</script>














