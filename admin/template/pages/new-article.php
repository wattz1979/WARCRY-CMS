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
		<li><a href="?page=articles">Articles</a></li>
        <li class="current"><a href="?page=new-article">New Article</a></li>
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
        <h2>Post new Article</h2>
        
        <?php
		if ($error = $ERRORS->DoPrint('addArticle'))
		{
			echo $error;
			unset($error);
		}
		?>
        
        <div class="form">
    
        	<form method="post" action="<?php echo $config['BaseURL']; ?>/admin/execute.php?take=add_article" name="add-article" id="add-article">
                <section>
                  <label for="label">
                    Headline*
                    <small>250 characters maximum.</small>
                  </label>
                
                  <div>
                    <input id="label" name="title" type="text" class="required" />
                  </div>
                </section>
                
                <section>
                  <label for="textarea_s">
                    Short Text*
                    <small>350 characters maximum.</small>
                  </label>
                  
                  <div>
                    <textarea class="required" id="textarea_s" name="short_text" rows="5"></textarea>
                  </div>
                </section>
                
                <section>
                  <label for="textarea">
                    Content*
                    <small>Only part of this contect will be displayed on the home page.</small>
                  </label>
                  
                  <div>
                    <textarea class="required bbcode" id="textarea" name="text"></textarea>
                  </div>
                </section>
                
                <section>
                	<label>
                        Comments
                        <small>Should the article have comments enabled.</small>
			     	</label>
                  
                    <div>
                        <div class="column">
                          	<input type="checkbox" value="1" id="comments" name="comments" checked="checked" />
                          	<label for="comments" class="prettyCheckbox checkbox list">
                                <span class="holderWrap" style="width: 17px; height: 17px;"></span>
                                Enable comments
                         	</label>
                        </div>
                    </div>
                </section>
                
                <input type="hidden" name="image" id="image" />
         	</form>
                
            <section>
              <label for="textarea">
                Image
                <small>Leave bank to set the default image.</small>
              </label>
              
              <div>
                    <div id="image_Loading" style="display: none;">
                        Loading...<br /><br /><br />
                    </div>
                    
                    <div id="image_PreviewSection" style="display: none; margin-bottom: 5px;">
                    </div>
                                    
                    <form id="uploadForm" method="POST" name="thumbForm" enctype="multipart/form-data">
                        <input type="hidden" name="MAX_FILE_SIZE" value="2000000">
                        <input id="label" type="file" name="file" onchange="ajaxFormSubmit()"/>
                        <input type="submit" value="submit" style="display: none;" />
                    </form>
              </div>
              
            </section>
            
            <br />
            <p>
                <input type="submit" class="button primary submit" value="Submit" onclick="submit_article();" />
            </p>

        </div>
    </div>

<script src="template/js/jquery.form.js" type="text/javascript"></script>
<script type="text/javascript" src="template/js/forms.js"></script>
<script src="template/js/sceditor/jquery.sceditor.js" type="text/javascript"></script>
<script src="template/js/sceditor/jquery.sceditor.bbcode.js" type="text/javascript"></script>

<script type="text/javascript">
	var $configURL = '<?php echo $config['BaseURL']; ?>';
	
	$(document).ready(function(e)
	{
		<?php
		if ($formData = $ERRORS->multipleError_accessFormData('addArticle'))
		{	
			echo '
			var savedFormData = $.parseJSON(', json_encode(json_encode($formData)), ');
			restoreFormData(\'add-article\', savedFormData);';
		}
		unset($formData);
		?>
		
		$("textarea.bbcode").sceditor({
			plugins: 'bbcode',
			style: 'template/css/bbcode-default-iframe.css'
		});
		
		//custom settings for validation
		$("#add-article").validate(
		{
			rules:
			{
				title:
				{
					minlength: 10,
					maxlength: 250
				},
				short_text:
				{
					minlength: 10,
					maxlength: 350
				},
		  	}
		});
	});
	
	function submit_article()
	{
		$('#add-article').submit();
	}
	
//////////////////////////////////////////////////////////////////
// Ajax Upload
//////////////////////////////////////////////////////////////////

	var ajaxOptions = 
	{
		url: 'ajax.php?phase=1',
	    beforeSubmit: function(a,f,o)
		{
			$('#image_PreviewSection', $currentTab).hide();
			$('#image_Loading', $currentTab).html('Loading...');
	     	$('#image_Loading', $currentTab).css('display', 'block');
	    },
	    success: function(data)
		{
			//check if we got errors
			var ajaxStatus = null;
					
			if (data.indexOf("@AjaxError@") > -1)
			{
				ajaxStatus = false;
			}
			else
			{
				ajaxStatus = true;
			}
				
			if (!ajaxStatus)
			{
				//we got error
	            $('#image_Loading', $currentTab).html(data);
				//unpopulate the hidden input for the product
				$("#image", $currentTab).val("");
			}
			else
			{
				//no errors
				$('#image_Loading', $currentTab).css('display', 'none');
				
				var image_src = $configURL + "/admin/tempUploads/"+ data;
							
				//update preview
				productUpdatePreview(image_src, data);
			}
	  	}
	}
		
	function ajaxFormSubmit()
	{
		$('#uploadForm', $currentTab).ajaxSubmit(ajaxOptions);	
	}

//////////////////////////////////////////////////////////////////
// A function to help me switch stuff
//////////////////////////////////////////////////////////////////

	function productUpdatePreview(imageSrc, imageName)
	{
		var previewSection = $('#image_PreviewSection', $currentTab);
		var $imageHeight;
		var $imageWidth;
			
		//hide the section in case we switched images
		previewSection.hide();
		
		function getWidthAndHeight()
		{
			$imageHeight = this.height;
			$imageWidth = this.width;
			
			proceed();
	
	    	return true;
		}
		
		var myImage = new Image();
		myImage.name = imageName;
		myImage.onload = getWidthAndHeight;
		myImage.src = imageSrc;
			
		function proceed()
		{
			//empty the container
			previewSection.html('');

			//append image
			var image = document.createElement("img");
			image.src = imageSrc;
			$(previewSection).append(image);
			
			previewSection.fadeIn("slow");
			
			//populate the hidden input for the product
			$("#image", $currentTab).val(imageName);
		}
	}
</script>