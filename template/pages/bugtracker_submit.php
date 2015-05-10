<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//Set the title
$TPL->SetTitle('Submit a Bug');
//CSS
$TPL->AddCSS('template/style/page-bugtracker-all.css');
//Print the header
$TPL->LoadHeader();

?>
<div class="content_holder">

 <div class="sub-page-title">
  <div id="title"><h1>Bug Tracker<p></p><span></span></h1></div>
 </div>
 
  	<div class="container_2" align="center">        
        
        <!-- BUG TRACKER - Submit Form -->
        
        	<?php
			if ($error = $ERRORS->DoPrint('submit_bug'))
			{
				echo $error, '<br><br>';
						
				unset($error);
			}			
			?>
        
        	<div class="report-title">
            	<h1>Bug Report</h1>
                <h4>Please select the right category for your report and include as much info as you can about the found bug. All the reports will be checked by the staff and will be aproved or closed in both cases you will recieve an answer about the report you submit.</h4>
            </div>
        	
            <div class="holder-bugtracker-form container_3 account-wide" align="left" style="padding:36px">
            
            	<form method="post" action="<?php echo $config['BaseURL']; ?>/execute.php?take=submit_bug" name="BTSubmitForm">
                
                    <div style="display:inline-block">
                        <select styled="true" name="mainCategory" onchange="return showCategories(this);">
                            <option value="0" selected="selected" disabled="disabled">Select category</option>
                            <option value="<?php echo BT_CAT_WEBSITE; ?>">Website</option>
                            <option value="<?php echo BT_CAT_WOTLK_CORE; ?>">WOTLK Core</option>
                        </select>
                    </div>
                        
                    <div class="sub-selects">
                                                
                        <!-- Categories -->
                            <div id="category-select" style="display:inline-block; margin:0 0 0 9px; display:none;">
                            </div>
                     	<!-- End.Categories -->
                       	
                        <!-- Sub Categories -->
                            <div id="subcategory-select" style="display:inline-block; margin:0 0 0 9px; display:none;">
                            </div>
                      <!-- End.Sub Categories -->
                        
                    </div>
                            
                    <br/>
                        
                    <input name="title" type="text" placeholder="Enter report title"  style="margin:15px 0 15px 0;" />
                    
                    <textarea name="text" style="display:block; float:none; width:800px; height:300px; margin:0 0 15px 0;" placeholder="Please describe the bug as much detail as possible."></textarea>
                    
                    <div class="select-priority">
                        <label class="label_radio"><div></div><input type="radio" name="prio" value="<?php echo BT_PRIORITY_LOW; ?>"/><p>Low Priority</p></label>
                        <label class="label_radio"><div></div><input type="radio" name="prio" value="<?php echo BT_PRIORITY_NORMAL; ?>" checked="checked"/><p>Normal Priority</p></label>
                        <label class="label_radio"><div></div><input type="radio" name="prio" value="<?php echo BT_PRIORITY_HIGH; ?>"/><p>Hight Priority</p></label>
                    </div>
                        
                    <input type="submit" value="Report" />
                </form>
            
            </div>
         <!-- BUG TRACKER - Submit Form . End -->

    </div>
    
</div>

<script type="text/javascript" src="<?php echo $config['BaseURL']; ?>/resources/min/?f=template/js/bug_tracker_submit.js,template/js/forms.js"></script>

<?php
if ($formData = $ERRORS->multipleError_accessFormData('submit_bug'))
{	
	echo '<script>
		var savedFormData = $.parseJSON(', json_encode(json_encode($formData)), ');
		restoreFormData(\'BTSubmitForm\', savedFormData);
	</script>';
}
unset($formData);

$TPL->LoadFooter();

?>