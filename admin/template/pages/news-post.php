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
		<li><a href="index.php?page=news">News</a></li>
		<li class="current"><a href="index.php?page=news-post">Post</a></li>
		<li><a href="#thirdtab">Settings</a></li>
	</ul>
</nav>

<?php
//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_NEWS))
{
	$CORE->ErrorBox('You do not have the required permissions.');
}
?>
  
<!-- The content -->
<section id="content">

	<div class="tab" id="maintab">
		<h2>Post News</h2>

		<?php
        if ($error = $ERRORS->DoPrint('addNews'))
        {
            echo $error;
            unset($error);
        }
        ?>
        
        <div class="form">
    
            <form method="post" action="<?php echo $config['BaseURL']; ?>/admin/execute.php?take=addNews" id="newsForm" name="addNewsForm">
            
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
                    <label for="textarea">
                        Short Text*
                        <small>500 characters maximum.</small>
                    </label>
                          
                    <div>
                        <textarea class="required" rows="8" id="textarea" name="shortText"></textarea>
                    </div>
                </section>
                
                <section>
                  <label for="textarea2">
                    Content*
                  </label>
                  
                  <div>
                    <textarea class="required bbcode" id="textarea2" name="text"></textarea>
                  </div>
                </section>
                
                <input type="hidden" name="image" id="newsImage" />
            
            </form>
	
            <section>
              <label for="textarea">
                Icon
                <small>Leave bank to set the default image.</small>
              </label>
              
              <div>
                    <div id="image_Loading" style="display: none;">
                        Loading...<br /><br /><br />
                    </div>
                    
                    <div id="image_PreviewSection" style="display: none; margin-bottom: 5px;">
                    </div>
        
                    <form id="croppingForm" method="POST" onsubmit="return false;" action="<?php echo $config['BaseURL']; ?>/admin/execute.php?take=cropImage" name="cropForm">
                        
                        <input type="hidden" name="path" value="/admin/tempUploads" />
                        <input type="hidden" id="jCrop-imageName" name="imgName" value="" />
                        <input type="hidden" name="resize" value="229" />
                        
                        <input type="hidden" id="x" name="x" />
                        <input type="hidden" id="y" name="y" />
                        <input type="hidden" id="w" name="w" />
                        <input type="hidden" id="h" name="h" />
                        
                        <div id="image_CroppingSection" style="display: none; margin-bottom: 5px;">
                            <div id="uploadedImagePreview" style="width: 229px !important; display: inline-block;"></div>
                            <div id="CropResult" style="display: inline-block; max-width: 210px; padding-left: 5px; vertical-align: top;">
                                <button class="button primary" onclick="cropFormSubmit();">Crop</button>
                            </div>
                        </div>
                        
                    </form>
                                    
                    <form id="uploadForm" method="POST" name="thumbForm" enctype="multipart/form-data">
                        <input type="hidden" name="MAX_FILE_SIZE" value="2000000">
                        <input id="label" type="file" name="file" onchange="ajaxFormSubmit()"/>
                        <input type="submit" value="submit" style="display: none;" />
                    </form>
              </div>
            </section>
     
       		<div class="clear"></div>
   
		</div>
       
        <br />  
        <p>
            <input type="button" class="button primary submit" value="Submit" onclick="return submitNews();"/>
        </p>

	</div>

<script type="text/javascript" src="template/js/forms.js"></script>
<script src="template/js/jquery.color.js" type="text/javascript"></script>
<script src="template/js/jquery.Jcrop.min.js" type="text/javascript"></script>
<script src="template/js/jquery.form.js" type="text/javascript"></script>
<script src="template/js/sceditor/jquery.sceditor.js" type="text/javascript"></script>
<script src="template/js/sceditor/jquery.sceditor.bbcode.js" type="text/javascript"></script>

<script type="text/javascript">
	var $configURL = '<?php echo $config['BaseURL']; ?>';
	
	//apply the BBcode Editors	
	$(document).ready(function(e)
	{
		<?php
		if ($formData = $ERRORS->multipleError_accessFormData('addNews'))
		{	
			echo '
			var savedFormData = $.parseJSON(', json_encode(json_encode($formData)), ');
			restoreFormData(\'addNewsForm\', savedFormData);';
		}
		unset($formData);
		?>
		
		$("textarea.bbcode").sceditor({
			plugins: 'bbcode',
			style: 'template/css/bbcode-default-iframe.css'
		});
		
		//custom settings for validation
		$("#newsForm").validate(
		{
			rules:
			{
				shortText:
				{
					minlength: 1,
					maxlength: 500
				},
			}
		});
	});
	
	//A function to submit the news
	function submitNews()
	{
		$('#newsForm').submit();
	}
	
	//////////////////////////////////////////////////////////////////
	// CROPPING & RESIZING
	//////////////////////////////////////////////////////////////////

	// Create variables (in this scope) to hold the API and image size
	var $jcrop_api, $boundx, $boundy;
	var $maxWidth = 229;
	
	var $jcrop_update = function(c)
	{	
		$('#x', $currentTab).val(c.x);
	    $('#y', $currentTab).val(c.y);
	    $('#w', $currentTab).val(c.w);
	    $('#h', $currentTab).val(c.h);
	}
	
	var $jcrop_clearCoords = function()
	{
		$('#croppingForm input', $currentTab).val('');
	}
	
	var $jcrop_options =
	{
		onChange: $jcrop_update,
		onSelect: $jcrop_update,
		onRelease: $jcrop_clearCoords,
		allowResize: true,
		allowSelect: false,
		setSelect: [229, 148, 0, 0],
		trueSize: [],
		aspectRatio: 1.555
	}
	
	var $jcrop_arg2 = function()
	{
		// Use the API to get the real image size
		var bounds = this.getBounds();
		$boundx = bounds[0];
		$boundy = bounds[1];
		// Store the API in the jcrop_api variable
		$jcrop_api = this;
	}

	function cropFormSubmit()
	{
		var cropForm = $("#croppingForm", $currentTab);
	    var cropppingSection = $('#image_CroppingSection', $currentTab);
			
		cropppingSection.hide();
		
		$('#image_Loading', $currentTab).html('Loading...');
	    $('#image_Loading', $currentTab).css('display', 'block');
		
		cropForm.ajaxSubmit(
		{
			url: 'execute.php?take=cropImage',
	    	success: function(data)
			{
				//hide the loading
				$('#image_Loading', $currentTab).fadeOut('fast');
	
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
					$('#image_Loading', $currentTab).fadeIn('fast');
					//unpopulate the hidden input for the product
					$("#newsImage", $currentTab).val("");
				}
				else
				{			
					var image_src = $configURL + "/admin/tempUploads/"+ data;		
					//update preview
					productUpdatePreview(image_src, data);
				}
	  		}
		});	
		
		return false;		
	}
	
	//////////////////////////////////////////////////////////////////
	// Ajax Upload
	//////////////////////////////////////////////////////////////////

	var ajaxOptions = 
	{
		url: 'ajax.php?phase=1',
	    beforeSubmit: function(a,f,o)
		{
	   	 	$('#image_CroppingSection', $currentTab).hide();
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
				$("#newsImage", $currentTab).val("");
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
	    var cropppingSection = $('#image_CroppingSection', $currentTab);
		var previewSection = $('#image_PreviewSection', $currentTab);
		var $imageHeight;
		var $imageWidth;
			
		//hide the section in case we switched images
		cropppingSection.hide();
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
			if ($imageWidth > 229 || $imageHeight > 148)
			{
				//open the cropping section
				var previewContainer = cropppingSection.find('#uploadedImagePreview');
				
				previewContainer.fadeIn();
				
				//empty the container
				previewContainer.html('');
				
				//append image
				var image = document.createElement("img");
				image.src = imageSrc;
				
				$(image).css({ maxWidth: $maxWidth, });
				
				previewContainer.append(image);
					
				cropppingSection.fadeIn("slow");
				
				//set image name input
				$("#jCrop-imageName", $currentTab).val(imageName);
				
				$jcrop_options.trueSize = [$imageWidth, $imageHeight];
				//start the crop
				$(image).Jcrop($jcrop_options , $jcrop_arg2);
			}
			else
			{
				//empty the container
				previewSection.html('');
	
				//append image
				var image = document.createElement("img");
				image.src = imageSrc;
				$(previewSection).append(image);
			
				previewSection.fadeIn("slow");
				
				//populate the hidden input for the product
				$("#newsImage", $currentTab).val(imageName);
			}
		}
	}
</script>