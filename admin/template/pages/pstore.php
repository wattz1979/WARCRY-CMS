<?PHP
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//load the characters module
$CORE->load_ServerModule('character');
//setup the characters class
$chars = new server_Character();

//print error messages
if ($error = $ERRORS->DoPrint(array('pstore_armorsets_add', 'pstore_armorsets_edit', 'pstore_armorsets_del', 'pstore_armorsets_addcat', 'pstore_armorsets_delcat')))
{
	echo $error;
	unset($error);
}
//print success messages
if ($success = $ERRORS->successPrint(array('pstore_armorsets_add', 'pstore_armorsets_edit', 'pstore_armorsets_del', 'pstore_armorsets_addcat', 'pstore_armorsets_delcat')))
{
	echo $success;
	unset($success);
}			

?>

<!-- Secondary navigation -->
<nav id="secondary">
	<ul>
		<li class="current"><a href="#maintab" onclick="changeCurrentTab('#maintab');">Armor Sets</a></li>
		<li><a href="#secondtab" onclick="changeCurrentTab('#secondtab');">Armor Set Categories</a></li>
		<li><a href="#thirdtab">Settings</a></li>
	</ul>
</nav>

<?php
//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_PSTORE))
{
	$CORE->ErrorBox('You do not have the required permissions.');
}
?>
       
<!-- The content -->
<section id="content">

<script type="text/javascript" src="template/js/forms.js"></script>

<div class="tab" id="maintab">

        <div class="column left twothird">
          <h2>Armor Sets Management</h2>
          
		  <table class="datatable" id="datatable_armorsets">
		  
		    <thead>
		      <tr>
		        <th>Title</th>
                <th>Items</th>
		        <th>Realm</th>
		        <th>Category</th>
                <th>Price</th>
		        <th>Actions</th>
		      </tr>
		    </thead>
		  	
		    <tbody>
		
		    <?php
			//get the armor set categories
			$res = $DB->query("SELECT * FROM `armorset_categories` ORDER BY id DESC");
			if ($res->rowCount() > 0)
			{
				while ($arr = $res->fetch())
				{
					$categories[$arr['id']] = $arr['name'];
				}
			}
			unset($res);
			
			$res = $DB->query("SELECT * FROM `armorsets` ORDER BY id DESC");
			
			if ($res->rowCount() > 0)
			{
				while ($arr = $res->fetch())
				{
					//null the array
					unset($subInfo);
					//check for set specifications
					if ($arr['tier'] != '')
					{
						$subInfo[] = $arr['tier'];
					}
					if ($arr['class'] != '' and $arr['class'] > 0)
					{
						$subInfo[] = 'Class: ' . $chars->getClassString($arr['class']);
					}
					if ($arr['type'] != '')
					{
						$subInfo[] = 'Type: ' . $arr['type'];
					}
					//explode the items
					$items = explode(',', $arr['items']);
					//get the category name
					if (isset($categories[$arr['category']]))
					{
						$cat = $categories[$arr['category']];
					}
					else
					{
						$cat = 'Unknown';
					}
					//get the realm name
					if ($arr['realm'] == '-1')
					{
						$realmStr = 'All Realms';
					}
					else
					{
						if (isset($realms_config[$arr['realm']]))
						{
							$realmStr = $realms_config[$arr['realm']]['name'];
						}
						else
						{
							$realmStr = 'Unknown';
						}
					}
					
					echo '
				      <tr data-id="', $arr['id'], '">
				        <td>', $arr['name'], (isset($subInfo) ? '<p class="subInfo">' . implode(' | ', $subInfo) . '</p>' : ''), '</td>
				        <td class="armorset-items">';
							
							//loop the items
							foreach ($items as $entry)
							{
								echo '
								<a href="', $config['WoWDB_URL'], '/?item=', $entry, '" rel="item=', $entry, '" onclick="return false;" id="armorset-', $arr['id'], '-item-', $entry, '"></a>
								<script>
									$(function()
									{
										load_ArmorSetItem(', $entry, ', \'#armorset-', $arr['id'], '-item-', $entry, '\');
									});
								</script>';
							}
							
						echo '
						</td>
				        <td>', $realmStr, '</td>
						<td>', $cat, '</td>
				        <td>', $arr['price'], '</td>
				        <td>
				          <span class="button-group">
				            <a href="#" onclick="return ConstructEdit(', $arr['id'], ');" class="button icon edit">Edit</a>
				            <a href="execute.php?take=delete&action=armorset&id=', $arr['id'], '" onclick="return deletecheck(\'Are you sure you want to delete this armor set?\');" class="button icon remove danger">Remove</a>
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
        <div class="column right third">
        	<h2>Add new Armor Set</h2>
            
	        <form action="execute.php?take=add_armorset" method="post" id="add_armorset_form">
	            <section>
	              <label>
                  	Title*
                  </label>
	              <div>
	                <input type="text" placeholder="Required" class="required" name="name" />
	              </div>
	            </section>
	            
                <section>
	              <label>
                  	Realm
	              	<small>Which realm should the set be purchasable in.</small>
                  </label>
	              <div>
	                <select name="realm">
                    	<option value="-1">All Realms</option>
                    	<?php
						if (isset($realms_config))
						{
							foreach ($realms_config as $id => $data)
							{
								echo '<option value="', $id, '">', $data['name'], '</option>';
							}
						}
						?>
                    </select>
	              </div>
	            </section>
                
  				<section>
	              <label>
                  	Category*
                  </label>
	              <div>
	                <select name="category">
                    	
                    	<?php
						
						$res = $DB->query("SELECT id, name FROM `armorset_categories` ORDER BY name ASC;");
						//check if we have any cats at all
						if ($res->rowCount() > 0)
						{
							echo '<option>Select Category</option>';
							
							while ($arr = $res->fetch())
							{
								echo '<option value="', $arr['id'], '">', $arr['name'], '</option>';
							}
						}
						else
						{
							echo '<option>Please add new category</option>';
						}
						
						?>
                        
                    </select>
	              </div>
	            </section>
                              
                <section>
	              <label>
                  	Price*
	              	<small>The price is in Gold Coins.</small>
                  </label>
	              <div>
	                <input type="text" placeholder="Required" class="required small" name="price" />
	              </div>
	            </section>
                
	            <section>
	              <label>
                  	Tier/Season
	              	<small>Example: "Tier 9", "Season 11" etc.</small>
                  </label>
	              <div>
	                <input type="text" name="tier"/>
	              </div>
	            </section>
	            
                <section>
			      <label>
			        Required Class
			      </label>
			      
			      <div>
			        <select name="class">
			          <option value="0">None</option>
			          <option value="1">Warrior</option>
			          <option value="2">Paladin</option>
                      <option value="3">Hunter</option>
                      <option value="4">Rogue</option>
                      <option value="5">Priest</option>
                      <option value="6">Death Knight</option>
                      <option value="7">Shaman</option>
                      <option value="8">Mage</option>
                      <option value="9">Warlock</option>
                      <option value="11">Druid</option>
			        </select>
			      </div>
			    </section>
                
                <section>
	              <label>
                  	Type
	              	<small>Example: "Elemental", "Arms" etc.</small>
                  </label>
	              <div>
	                <input type="text" name="type" />
	              </div>
	            </section>
                
                <section>
	              <label>
                  	Items*
	              	<small>Click on the text and enter item id.</small>
                  </label>
	              <div>
	                <input type="text" name="items" class="small" id="setItems" />
	              </div>
	            </section>
                
	            <section>
	              <input type="submit" class="button primary big" value="Submit" />
	            </section>
			</form>
        
        </div>
        
        <div class="clear"></div>
        
  	<?php
  
 	$action = isset($_GET['action']) ? $_GET['action'] : 'browse';
  
  	if ($action == 'edit')
  	{
  	}
  
  	?>

</div>

<div class="tab" id="secondtab">
		        
	  	<div class="column left twothird">
          
          <h2>Armor Set Categories</h2>
          
		  <table id="datatable2" class="datatable datatable_editable">
		  
		    <thead>
		      <tr>
		        <th>ID</th>
		        <th>Category Name</th>
		        <th>Actions</th>
		      </tr>
		    </thead>
		  	
		    <tbody>
		
		    <?php
			
			$res = $DB->query("SELECT * FROM `armorset_categories` ORDER BY id DESC");
			
			if ($res->rowCount() > 0)
			{
				while ($arr = $res->fetch())
				{
					echo '
				      <tr>
				        <td>', $arr['id'], '</td>
				        <td>
							<input type="text" disabled="disabled" value="', $arr['name'], '" id="cat-editable-', $arr['id'], '" isOpen="false">
							<div id="cat-editable-', $arr['id'], '-infobox" class="datatable_editable-infobox">Errpr</div>
						</td>
				        <td>
				          <span class="button-group">
				            <a href="javascript: void(0);" onclick="return editableDatatable(this, \'cat-editable-', $arr['id'], '\', ', $arr['id'], ');" class="button icon edit">Edit</a>
				            <a href="execute.php?take=delete&action=armorsets_category&id=', $arr['id'], '" onclick="return deletecheck(\'Are you sure you want to delete this category?\');" class="button icon remove danger">Remove</a>
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
        
        <div class="column right third">
        
			<h2>Add New Category</h2>
              
              <form action="execute.php?take=add_armorsetcat" method="post">
			    <section>
			      <label>Title*</label>
			      
			      <div>
			        <input type="text" placeholder="Required" class="required" name="name" />
			      </div>
			    </section>
			    
			    <section>
			      <input type="submit" class="button primary big" value="Submit" />
			    </section>
			  </form>      	
        
        </div>
        
        <div class="clear"></div>
	
</div>
              
<div class="tab" id="thirdtab">
	<h2>News Settings</h2>

</div>

<?php
unset($chars);
?>

<script src="template/js/jquery.form.js" type="text/javascript"></script>
<script src="template/js/jquery.datatables.js" type="text/javascript"></script>
<script src="template/js/jquery.inputtags.js" type="text/javascript"></script>
<script src="template/js/jquery-ui-1.10.0.sortable.min.js" type="text/javascript"></script>

<script type="text/javascript">
var $configURL = '<?php echo $config['BaseURL']; ?>';
var $editable_onChangeTimer = null;

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
		data: { phase: 15, id: $id },
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
					'<h2 style="margin-top:0;">Armorset Editing</h2>'+
						'<form method="post" action="execute.php?take=edit_armorset&id='+data.id+'">'+
							'<input type="hidden" value="'+data.id+'" name="id" />'+
							'<section>'+
								'<label>Title*</label>'+
								'<div><input type="text" placeholder="Required" class="required" name="name" value="'+data.name+'" /></div>'+
							'</section>'+
							
							'<section>'+
							  	'<label>'+
									'Realm'+
									'<small>Which realm should the set be purchasable in.</small>'+
							  	'</label>'+
							  	'<div>'+
									'<select name="realm">'+
										'<option value="-1">All Realms</option>'+
										<?php
										if (isset($realms_config))
										{
											foreach ($realms_config as $id => $data)
											{
												echo '\'<option value="', $id, '">', $data['name'], '</option>\'+';
											}
										}
										?>
									'</select>'+
							  	'</div>'+
							'</section>'+
							
							'<section>'+
							  	'<label>'+
									'Category*'+
							  	'</label>'+
							  	'<div>'+
									'<select name="category">'+
									
									<?php
									
									$res = $DB->query("SELECT id, name FROM `armorset_categories` ORDER BY name ASC;");
									//check if we have any cats at all
									if ($res->rowCount() > 0)
									{
										echo '\'<option>Select Category</option>\'+';
										
										while ($arr = $res->fetch())
										{
											echo '\'<option value="', $arr['id'], '">', $arr['name'], '</option>\'+';
										}
									}
									else
									{
										echo '\'<option>Please add new category</option>\'+';
									}
									
									?>
									
									'</select>'+
							  	'</div>'+
							'</section>'+
										  
							'<section>'+
							  	'<label>'+
									'Price*'+
									'<small>The price is in Gold Coins.</small>'+
							  	'</label>'+
							  	'<div>'+
									'<input type="text" placeholder="Required" class="required small" name="price" value="'+data.price+'" />'+
							  	'</div>'+
							'</section>'+
							
							'<section>'+
							  	'<label>'+
									'Tier/Season'+
									'<small>Example: "Tier 9", "Season 11" etc.</small>'+
							  	'</label>'+
							  	'<div>'+
									'<input type="text" name="tier" value="'+data.tier+'" />'+
							  	'</div>'+
							'</section>'+
							
							'<section>'+
							  	'<label>'+
									'Required Class'+
							  	'</label>'+
							  
							  	'<div>'+
									'<select name="class">'+
										'<option value="0">None</option>'+
										'<option value="1">Warrior</option>'+
										'<option value="2">Paladin</option>'+
										'<option value="3">Hunter</option>'+
										'<option value="4">Rogue</option>'+
										'<option value="5">Priest</option>'+
										'<option value="6">Death Knight</option>'+
										'<option value="7">Shaman</option>'+
										'<option value="8">Mage</option>'+
										'<option value="9">Warlock</option>'+
										'<option value="11">Druid</option>'+
									'</select>'+
							  	'</div>'+
							'</section>'+
							
							'<section>'+
							  	'<label>'+
									'Type'+
									'<small>Example: "Elemental", "Arms" etc.</small>'+
							  	'</label>'+
							  	'<div>'+
									'<input type="text" name="type" value="'+data.type+'" />'+
							  	'</div>'+
							'</section>'+
							
							'<section>'+
							  	'<label>'+
									'Items*'+
									'<small>Click on the text and enter item id.</small>'+
							  	'</label>'+
							  	'<div>'+
									'<input type="text" name="items" class="small" id="setItems_'+data.id+'" />'+
							  	'</div>'+
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
			
			//Manage selects
			form.find('select').each(function(i, e)
			{
				var selectName = $(this).attr('name');
				
				//manage diferent selects
				switch (selectName)
				{
					case 'realm':
					{
						$(this).children('option').each(function()
						{
                            if ($(this).val() == data.realm)
							{
								$(this).attr('selected', 'selected');
							}
                        });
						break;
					}
					case 'category':
					{
						$(this).children('option').each(function()
						{
                            if ($(this).val() == data.category)
							{
								$(this).attr('selected', 'selected');
							}
                        });
						break;
					}
					case 'class':
					{
						$(this).children('option').each(function()
						{
                            if ($(this).val() == data.class)
							{
								$(this).attr('selected', 'selected');
							}
                        });
						break;
					}
				}
			});
			
			//setup the item list for the Add Armor Set
			$('#setItems_' + data.id).tagsInput(
			{
				wowheadItems: true,
				defaultText: 'add a item',
			});
			
			//Add the current items
			var items = data.items.toString().split(",");
			
			$.each(items, function(ind, val)
			{
				$('#setItems_' + data.id).addTag(val + "",
				{
					wowheadItems: true,
					focus: true,
					unique: true
				});
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

//apply the BBcode Editors	
$(document).ready(function(e)
{
    //Init datatables
	if ($("#datatable_armorsets").length > 0)
	{
	 	var armorsetsTable = $("#datatable_armorsets").dataTable(
		{
			"bAutoWidth": false,
			"aoColumnDefs": [ 
				{ "sWidth": "220px", "aTargets": [ 1 ] },
      			{ "sWidth": "80px", "aTargets": [ 2 ] },
				{ "sWidth": "50px", "aTargets": [ 4 ] },
				{ "sWidth": "139px", "aTargets": [ 5 ] },
				{ "bSortable": false, "aTargets": [ 5 ] },
				{ "bSortable": false, "aTargets": [ 1 ] }
    		]
		});
		//sort the table
		//armorsetsTable.fnSort( [ [1, 'desc'] ] );
	}
	if ($("#datatable2").length > 0)
	{
	 	var newsTable = $("#datatable2").dataTable(
		{
			"bFilter": false,
			"aoColumnDefs": [ 
	      		{ "bSortable": false, "aTargets": [ 2 ] }
	    	]
		});
		//sort the table
		newsTable.fnSort( [ [1, 'desc'] ] );
	}
	
	//setup the item list for the Add Armor Set
	$('#setItems').tagsInput(
	{
		wowheadItems: true,
		defaultText: 'add a item',
	});
	
	//custom settings for validation
	$("#add_armorset_form").validate(
	{
		rules:
	  	{
			name:
			{
				required: true,
				maxlength: 250
			},
	    	price:
			{
	      		required: true,
	      		number: true
	    	}
	  	}
	});
});

function editableDatatable(btn, id, record)
{
	var input = $('#' + id);
	
	if (input.attr('isOpen') == 'false')
	{
		//activate the btn
		$(btn).addClass('active');
		//activate the input
		input.removeAttr('disabled');
		//set the isOpen attr
		input.attr('isOpen', 'true');
		//bind the onchange handler
		input.on('keyup', function()
		{
			clearTimeout($editable_onChangeTimer);
			//check if the input has any text at all
			if ($(this).val() != '')
			{
				$editable_onChangeTimer = setTimeout(function()
				{
					save_CategoryData(id, record);
				}, 1000);
			}
		});
	}
	else
	{
		//deactivate the btn
		$(btn).removeClass('active');
		//deactivate the input
		input.attr('disabled', 'disabled');
		//set the isOpen attr
		input.attr('isOpen', 'false');
		//unbind the handler
		input.off('keyup');
	}
	
	return false;
}

function save_CategoryData(id, record)
{
	var input = $('#' + id);
	var infobox = $('#' + id + '-infobox');
	
	$.post(
		$configURL + "/admin/execute.php?take=edit_armorsetcat", 
		{ 
			id: record,
			name: input.val() 
		}, 
		function(data)
		{
     		//check for errors
			if (data != 'OK')
			{
				infobox.html(data);
				infobox.fadeIn('fast');
			}
			else
			{
				//check if we need to hide the infobox
				if (infobox.css('display') != 'none')
				{
					infobox.fadeOut('fast');
				}
			}
   		}
	);
}

function load_ArmorSetItem(entry, elementId)
{
	var link = $(elementId);
	
	//check if we have the data of this item
	if (typeof $('body').data('item-'+entry) == 'object')
	{
		var quality = $('body').data('item-'+entry).quality;
		var icon = $('body').data('item-'+entry).icon;
		
		//append the item
		link.addClass(quality.toLowerCase());
		link.css('background', 'url(http://wow.zamimg.com/images/wow/icons/large/' + icon.toLowerCase() + '.jpg);');
	}
	else
	{
		//prepare the ajax error handlers
		$.ajaxSetup({
			error:function(x,e)
			{
				console.log('Parser Error.');
			},
			dataType: "json",
		});
		//get the icon
		$.get($configURL + "/ajax.php?phase=1",
		{
			entry: entry
		},
		function(data)
		{
			var quality = data.quality_str;
			var icon = data.icon;
			
			//save the data, and use it the next time we request it
			$('body').data('item-'+entry, {quality: quality, icon: icon});
			
			//append the item
			link.addClass(quality.toLowerCase());
			link.attr('style', 'background: url(http://wow.zamimg.com/images/wow/icons/large/' + icon.toLowerCase() + '.jpg);');
		});
	}
}
</script>