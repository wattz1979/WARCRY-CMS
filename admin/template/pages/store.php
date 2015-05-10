<?PHP
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$RealmID = isset($_GET['realm']) ? $_GET['realm'] : '-1';

//Save selected realm in a session
$_SESSION['ADMIN_SelectedRealmS'] = $RealmID;
//Validate realm id
if ($RealmID != '-1')
{
	$_SESSION['ADMIN_SelectedRealmS'] = isset($realms_config[$RealmID]) ? $RealmID : '-1';
}

$RealmID = $_SESSION['ADMIN_SelectedRealmS'];

//print error messages
if ($error = $ERRORS->DoPrint(array('edit_storeitem')))
{
	echo $error;
}
//print success messages
if ($success = $ERRORS->successPrint(array('edit_storeitem', 'add_storeitem')))
{
	echo $success;
}
unset($success, $error);		

?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li class="current"><a href="index.php?page=store">Item Store</a></li>
        <li><a href="index.php?page=store-add">Add new item</a></li>
	</ul>
</nav>

<?php
//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_STORE))
{
	$CORE->ErrorBox('You do not have the required permissions.');
}
?>
    
<!-- The content -->
<section id="content">

	<div class="tab" id="maintab">
        <h2>Item Store Management</h2>
      	
        <br />
        <div>
        	<?php
				//Realm selection
				if (isset($realms_config) && count($realms_config) > 0)
				{
					echo '
					<div style="width: 200px; display: inline-block; vertical-align: middle; margin-right: 10px;">
						<select name="realm" id="realm-select">
							<option value="-1">All Realms</option>';
						
							foreach ($realms_config as $id => $realmData)
							{
								echo '<option value="', $id, '" ', ($id == $RealmID ? 'selected="selected"' : ''), '>', $realmData['name'], '</option>';
							}
						
						echo '
						</select>
					</div>';
				}
			?>
            
            <script>
				$(function()
				{
					$('#realm-select').on('change', function()
					{
						window.location = 'index.php?page=store&realm=' + $(this).find('option:selected').val();
					});
				});
			</script>
            
        </div>
        <br /><br />
        
        <table class="datatable" id="datatable">
      
            <thead>
                <tr>
                    <th>Entry</th>
                    <th>Name</th>
                    <th>Item Level</th>
                    <th>Realms</th>
                    <th>Price Gold</th>
                    <th>Price Silver</th>
                    <th>Class</th>
                    <th>Subclass</th>
                    <th>Actions</th>
                </tr>
            </thead>
        
            <tbody>

            </tbody>
        
        </table>
    
    </div>

<script src="template/js/forms.js" type="text/javascript"></script>
<script src="template/js/jquery.form.js" type="text/javascript"></script>
<script src="template/js/jquery.datatables.js" type="text/javascript"></script>
<script src="template/js/jquery-ui-1.10.0.sortable.min.js" type="text/javascript"></script>

<script type="text/javascript">
var $configURL = '<?php echo $config['BaseURL']; ?>';
var $editable_onChangeTimer = null;

//apply the BBcode Editors	
$(document).ready(function(e)
{
    //Init datatables
	if ($("#datatable").length > 0)
	{
	 	var armorsetsTable = $("#datatable").dataTable(
		{
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "ajax.php?phase=17&realm=<?php echo $RealmID; ?>",
			"aoColumnDefs": [ 
				{ "bSortable": false, "aTargets": [ 8 ] },
    		]
		});
		//sort the table
		//armorsetsTable.fnSort( [ [1, 'desc'] ] );
	}
});

function DeleteItem(e, id)
{
	var TR = $(e).parent().parent().parent();
	
	var answer = confirm('Are you sure you want to delete this item?');
	
	if (!answer)
		return false;
	
	$.get('ajax.php?phase=18',
	{
		id: id
	},
	function(data)
	{
		if (data == 'OK')
		{
			TR.fadeOut('slow');
			
			new Notification('The item was successfully deleted.', 'success');
		}
		else
		{
			new Notification(data, 'error', 'urgent');
		}
	});
	
	return false;
}

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
		data: { phase: 19, id: $id },
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
					'<h2 style="margin-top:0;">Item Editing</h2>'+
						'<form method="post" action="execute.php?take=edit_storeitem&id='+data.id+'">'+
							'<input type="hidden" value="'+data.id+'" name="id" />'+
							'<section>'+
								'<label>Entry*</label>'+
								'<div><input type="text" placeholder="Required" class="required" name="entry" value="'+data.entry+'" /></div>'+
							'</section>'+
							
							'<section>'+
								'<label>Name*</label>'+
								'<div><input type="text" placeholder="Required" class="required" name="name" value="'+data.name+'" /></div>'+
							'</section>'+
							
							'<section>'+
							  	'<label>'+
									'Realms*'+
									'<small>Which realms should the item be purchasable in.</small>'+
									'<small>Use delimeter "," to list more then one realm.</small>'+
							  	'</label>'+
							  	'<div><input type="text" placeholder="Required" class="required" name="realm" value="'+data.realm+'" /></div>'+
								'<div class="clear"></div>'+
							'</section>'+
							
							'<section>'+
								'<label>'+
									'Gold Price*'+
									'<small>Set to 0 if you wish to disable this currency.</small>'+
								'</label>'+
								'<div><input type="text" placeholder="Required" class="required" name="gold" value="'+data.gold+'" /></div>'+
							'</section>'+
							
							'<section>'+
								'<label>'+
									'Silver Price*'+
									'<small>Set to 0 if you wish to disable this currency.</small>'+
								'</label>'+
								'<div><input type="text" placeholder="Required" class="required" name="silver" value="'+data.silver+'" /></div>'+
							'</section>'+
							
							'<section>'+
								'<div>'+
									'<span class="button-group" style="float:left;">'+
										'<a href="#" id="submit_btn" class="button icon edit">Submit</a>'+
										'<a href="#" onclick="return CloseEditor('+data.id+');" class="button icon remove danger">Cancel</a>'+
									'</span>'+
									'<div class="clear"></div>'+
								'</div>'+
							'</section>'+
						'</form>');
			container.append(form);
			
			//Bind the submit
			form.find('#submit_btn').bind('click', function()
			{
				$(this).parent().parent().parent().parent().submit();
				
				return false;
			});
			
			//test
			Overlay.fadeIn('fast', function()
			{
				$(this).find('select').select_skin();
			});
		}
	});
	
	return false;
}

function CloseEditor(id)
{
	$('#editor-'+id).fadeOut('fast');
}
</script>