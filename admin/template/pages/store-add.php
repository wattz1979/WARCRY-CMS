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
		<li><a href="index.php?page=store">Item Store</a></li>
        <li class="current"><a href="index.php?page=store-add">Add new item</a></li>
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
		<h2>Add Store Item</h2>

		<?php
        if ($error = $ERRORS->DoPrint('add_storeitem'))
        {
            echo $error;
            unset($error);
        }
        ?>
        
        <div class="form">
    
            <form method="post" action="<?php echo $config['BaseURL']; ?>/admin/execute.php?take=add_storeitem" name="addItemForm" id="add-item">
            
                <section>
                  	<label for="label">
                    	Item Entry*
                    	<small>The entry from item_template.</small>
                  	</label>
                  	<div>
                    	<input id="label" name="entry" type="text" class="required item-entry" />
                        <div style="">
                        	<input type="button" class="button primary submit" value="Item Autofill" onclick="return ItemAutofill();" />
                        </div>
                  	</div>
                </section>
                
                <section>
                  	<label for="label2">
                    	Item Name*
                  	</label>
                  	<div>
                    	<input id="label2" name="name" type="text" class="required item-name" />
                  	</div>
                </section>
                
                <section>
                  	<label for="label3">
                    	Item Quality*
                  	</label>
                  	<div>
                    	<select name="quality" id="label3" class="item-quality">
                            <option value="0" selected="selected">Poor</option>
                            <option value="1">Common</option>
                            <option value="2">Uncommon</option>
                            <option value="3">Rare</option>
                            <option value="4">Epic</option>
                            <option value="5">Legendary</option>
                            <option value="6">Artifact</option>
                            <option value="7">Bind to Account</option>
                        </select>
                  	</div>
                </section>
                
                <section>
                  	<label for="label4">
                    	Realms*
                    	<small>Use delimeter "," to list more then one realm.</small>
                  	</label>
                  	<div>
                    	<input id="label4" name="realm" type="text" class="required" />
                  	</div>
                </section>
                
                <section>
                  	<label for="label5">
                    	Gold Price*
                    	<small>Set to 0 if you wish to disable this currency.</small>
                  	</label>
                  	<div>
                    	<input id="label5" name="gold" type="text" class="required" />
                  	</div>
                </section>
                
                <section>
                  	<label for="label6">
                    	Silver Price*
                    	<small>Set to 0 if you wish to disable this currency.</small>
                  	</label>
                  	<div>
                    	<input id="label6" name="silver" type="text" class="required" />
                  	</div>
                </section>
                
                <section>
                  	<label for="label7">
                    	Item Class*
                    	<small>The item class from item_template.</small>
                  	</label>
                  	<div>
                    	<select name="class" id="label7" class="item-class" onchange="return ClassChanges(this);">
                        	<?php
							foreach ($itemclasses as $i => $data)
							{
								echo '<option value="', $data['id'], '">', $data['name'], ' [', $data['id'], ']</option>';
							}
							unset($i, $data);
							?>
                        </select>
                  	</div>
                </section>
                
                <section>
                  	<label>
                    	Item Subclass*
                    	<small>The item subclass from item_template.</small>
                  	</label>
                  	<div>
                    	<?php
						foreach ($itemclasses as $i => $data)
						{
							echo '<select ', ($data['id'] == '0' ? 'name="subclass" class="subclass-visible"' : 'style="display: none"'), ' id="subclass-of-class-', $data['id'], '">';
								
								//print them subclasses of the current class
								foreach ($itemsubclasses as $i2 => $sdata)
								{
									if ($sdata['class'] == $data['id'])
										echo '<option value="', $sdata['subclass'], '">', $sdata['name'], ' [', $sdata['subclass'], ']</option>';
								}
								unset($i2, $sdata);
								
							echo '</select>';
						}
						unset($i, $data);
						?>
                  	</div>
                    <div class="clear"></div>
                </section>
                
                 <section>
                  	<label for="label8">
                    	Item Level
                    	<small>Used mainly for filtering.</small>
                  	</label>
                  	<div>
                    	<input id="label8" name="itemlevel" type="text" class="required item-itemlevel" />
                  	</div>
                </section>
                
                <section>
                  	<label for="label9">
                    	Item Inventory Type
                    	<small>Cold use some extra data.</small>
                  	</label>
                  	<div>
                    	<input id="label9" name="invtype" type="text" class="required item-inv-type" value="0" />
                  	</div>
                </section>
                
            	<br />  
                <p>
                    <input type="button" class="button primary submit" value="Submit" onclick="this.form.submit()" />
                </p>
            
            </form>

       		<div class="clear"></div>
   
		</div>

	</div>

<script src="template/js/forms.js" type="text/javascript"></script>
<script src="template/js/jquery.form.js" type="text/javascript"></script>

<script type="text/javascript">
	var $configURL = '<?php echo $config['BaseURL']; ?>';
	
	//apply the BBcode Editors	
	$(document).ready(function(e)
	{
		<?php
		if ($formData = $ERRORS->multipleError_accessFormData('add_storeitem'))
		{	
			echo '
			var savedFormData = $.parseJSON(', json_encode(json_encode($formData)), ');
			restoreFormData(\'addItemForm\', savedFormData);';
		}
		unset($formData);
		?>
	});
	
	function ItemAutofill()
	{
		var entry = $('.item-entry').val();
		
		if (entry.length == 0)
			return false;
		
		//prepare the ajax error handlers
		$.ajaxSetup({
			error:function(x,e)
			{
				console.log('Ajax Error.');
				console.log(x);
			},
			dataType: "json",
		});
					
		$.get("<?php echo $config['BaseURL']; ?>/ajax.php?phase=1",
		{
			entry: entry
		},
		function(data)
		{
			if (data == null)
			{
				new Notification('The item entry seems to be invalid.', 'error', 'urgent');
				return;
			}
			
			var name = data.name;
			var quality = data.quality;
			var icon = data.icon;
			var subclass = data.subclass_str;
			var inventorySlot = data.InventoryType;
			
			//fill in the data
			$('.item-name').val(name);
			$('.item-quality').find('option:selected').attr('selected', null);
			$('.item-quality').find('option[value="'+quality+'"]').attr('selected', 'selected');
			$('.item-quality').trigger('change');
			
			$('.item-class').find('option:selected').attr('selected', null);
			$('.item-class').find('option[value="'+data.class+'"]').attr('selected', 'selected');
			$('.item-class').trigger('change');
			
			$('#subclass-of-class-' + data.class).find('option:selected').attr('selected', null);
			$('#subclass-of-class-' + data.class).find('option[value="'+data.subclass+'"]').attr('selected', 'selected');
			$('#subclass-of-class-' + data.class).trigger('change');
			
			$('.item-inv-type').val(inventorySlot);
			
			if (typeof data.ItemLevel != 'undefined')
			{
				$('.item-itemlevel').val(data.ItemLevel);
			}
		});
	}
	
	function ClassChanges(e)
	{
		var selected = $(e).find('option:selected');
		var theclass = selected.val();
		
		//hide the currenly visible subclass select
		$('.subclass-visible').attr('name', null);
		$('.subclass-visible').parent().fadeOut(500);
		$('.subclass-visible').removeClass('subclass-visible');
		
		//Show the new one
		$('#subclass-of-class-' + theclass).attr('name', 'subclass');
		$('#subclass-of-class-' + theclass).css('display', 'block');
		$('#subclass-of-class-' + theclass).addClass('subclass-visible');
		$('#subclass-of-class-' + theclass).parent().css('width', 'auto');
		$('#subclass-of-class-' + theclass).parent().find('.cmf-skinned-text').css('width', 'auto');
		$('#subclass-of-class-' + theclass).parent().delay(500).fadeIn('fast');
	}
</script>