<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//Set the title
$TPL->SetTitle('Screenshot Upload');
//CSS
$TPL->AddCSS('template/style/page-media.css');
//Print the header
$TPL->LoadHeader();

?>
<div class="content_holder">

 <div class="sub-page-title">
  <div id="title"><h1>Account Panel<p></p><span></span></h1></div>
 </div>
 
  	<div class="container_2 account" align="center">
     <div class="cont-image">    
      <!-- Upload Screanshot -->
 
	     <?php
		if ($error = $ERRORS->DoPrint('screenshots'))
		{
			echo $error, '<br><br>';
		}			
		if ($error = $ERRORS->successPrint('screenshots'))
		{
			echo $error, '<br><br>';
		}			
		unset($error);
		?>
      
		<div class="container_3 account_sub_header">
			<div class="grad">
				<div class="page-title">Upload Screenshot</div>
				<a href="<?php echo $config['BaseURL'], '/index.php?page=media'; ?>">Back to media</a>
			</div>
		</div>
      
		<div class="page-desc-holder">
            Upload your screenshots made on our realms.<br/>
            After aproval from the staff you will receive 1 Silver Coint for each screenshot submitted.<br/>
            Please try to upload funny and unique screenshots.
		</div>
      
		<div class="container_3 account-wide" align="center">
        	<div class="upload-screanshot">
	        	<form method="post" action="<?php echo $config['BaseURL']; ?>/execute.php?take=screenshot" enctype="multipart/form-data">
                	<div class="row">
                    	<label for="screanshot-name">Screenshot Title:</label>
                		<input type="text" id="screanshot-name" name="title" />
                    </div>
                
                	<div class="row">
                        <label for="screanshot-file">Select file:</label>
                        <input type="file" id="screanshot-file" name="file" />
	                </div>
                
                	<div class="row textarea-row">
	                    <label for="screanshot-descr">Description:</label>
	                    <textarea id="screanshot-descr" spellcheck="false" name="descr"></textarea>
	                    <div class="clear"></div>
                	</div>
                	<br/>
                	<input type="submit" value="Submit" style="left:-20px;" />
                </form>
            </div>
		</div>
      
      <!-- Upload Screanshot.End -->
     </div>
	</div>
 
</div>

<script>
$(document).ready(function()
{
	$('#screanshot-file').customFileInput();
});
</script>

<?php
	//Add some javascripts to the loader
	$TPL->AddFooterJs('template/js/jQuery.fileinput.js');
	//Print Header
	$TPL->LoadFooter();
?>