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
		<li><a href="index.php?page=media">Movies</a></li>
        <li class="current"><a href="index.php?page=movie-add">New Movie</a></li>
		<li><a href="index.php?page=screenshots">Screenshots</a></li>
	</ul>
</nav>

<?php
//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_MEDIA_MOVIES))
{
	$CORE->ErrorBox('You do not have the required permissions.');
}
?>
  
<!-- The content -->
<section id="content">

	<div class="tab" id="maintab">
		<h2>Add a Movie</h2>

		<?php
        if ($error = $ERRORS->DoPrint('add_movie'))
        {
            echo $error;
        }
		unset($error);
        ?>
        
        <div class="form">
    
            <form method="post" action="<?php echo $config['BaseURL']; ?>/admin/execute.php?take=add_movie" id="movieForm" name="addMovieForm">
            
                <section>
                  <label for="label">
                    Title*
                    <small>250 characters maximum.</small>
                  </label>
                
                  <div>
                    <input id="label" name="name" type="text" class="required" />
                  </div>
                </section>
                
                <section>
                  <label for="label">
                    Youtube Link
                    <small>The URL pointing to the video on Youtube.</small>
                  </label>
                
                  <div>
                    <input id="label" name="youtube" type="text" class="required" />
                  </div>
                </section>
                
                <section>
                  	<label for="shorttext">
                    	Short Description*
                    	<small>115 characters maximum.</small>
                  	</label>
                  
                  	<div>
                    	<textarea rows="3" id="shorttext" name="short_text"></textarea>
                  	</div>
                </section>
                
                <section>
                  <label for="textarea">
                    Description*
                  </label>
                  
                  <div>
                    <textarea class="required bbcode" id="textarea" name="text"></textarea>
                  </div>
                </section>
                
                <input type="hidden" name="image" id="movieImage" />
            	<input type="hidden" name="movies" id="movieList" />
            </form>
	
            <section>
              <label for="textarea">
                Video Thumbnail*
                <small>Recommended size: 846x479.</small>
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
     		
            <section>
              	<label for="textarea">
                	Movie Files*
                	<small>Required atleast 1 movie.</small>
                    <small>Supported formats: MP4, WEBM, OGG.</small>
                    <small>If the uploader doesn't start uploading instantly,<br /> don't panic, it's loading the files into the memory.</small>
              	</label>
              
              	<div>
                    <div id="uploader">
                        <p>You browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
                    </div>
              	</div>
            </section>

       		<div class="clear"></div>
   
		</div>
       
        <br />  
        <p>
            <input type="button" class="button primary submit" value="Submit" onclick="return submitMovie();"/>
        </p>

	</div>

<script type="text/javascript" src="template/js/forms.js"></script>
<script src="template/js/jquery.color.js" type="text/javascript"></script>
<script src="template/js/jquery.form.js" type="text/javascript"></script>
<script src="template/js/sceditor/jquery.sceditor.js" type="text/javascript"></script>
<script src="template/js/sceditor/jquery.sceditor.bbcode.js" type="text/javascript"></script>

<style type="text/css">@import url(template/js/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css);</style>
<!-- Third party script for BrowserPlus runtime (Google Gears included in Gears runtime now) -->
<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
<!-- Load plupload and all it's runtimes and finally the jQuery queue widget -->
<script type="text/javascript" src="template/js/plupload/plupload.full.js"></script>
<script type="text/javascript" src="template/js/plupload/jquery.plupload.queue/jquery.plupload.queue.js"></script>

<script type="text/javascript">
	var $configURL = '<?php echo $config['BaseURL']; ?>';
	
	//apply the BBcode Editors	
	$(document).ready(function(e)
	{
		<?php
		if ($formData = $ERRORS->multipleError_accessFormData('add_movie'))
		{	
			echo '
			var savedFormData = $.parseJSON(', json_encode(json_encode($formData)), ');
			restoreFormData(\'addMovieForm\', savedFormData);';
		}
		unset($formData);
		?>
		
		//Have to clear the movie list on load, because we might have had an error and the old uploads will be applied
		$('#movieList').val('');
		
		$("textarea.bbcode").sceditor({
			plugins: 'bbcode',
			style: 'template/css/bbcode-default-iframe.css'
		});
		
		//custom settings for validation
		$("#movieForm").validate(
		{
			rules:
			{
				name:
				{
					minlength: 1,
					maxlength: 250
				},
				short_text:
				{
					minlength: 10,
					maxlength: 115
				},
				youtube:
				{
					minlength: 0,
					maxlength: 150
				},
			}
		});
		
		$("#uploader").pluploadQueue(
		{
			// General settings
			runtimes : 'gears,silverlight,flash,browserplus,html5',
			url : 'execute.php?take=chuckedUpload',
			max_file_size : '1GB',
			chunk_size : '1024KB',
			unique_names : true,

			// Specify what files to browse for
			filters : [
				{title : "Movie files", extensions : "mp4,webm,ogg"},
			],

			// Flash settings
			flash_swf_url : 'template/js/plupload/plupload.flash.swf',

			// Silverlight settings
			silverlight_xap_url : 'template/js/plupload/plupload.silverlight.xap'
		});
		
		var PlUploader = $("#uploader").pluploadQueue();
		
		//catch the uploaded files
		PlUploader.bind('FileUploaded', function(uploader, file, response)
		{
			//parse the response
			response = jQuery.parseJSON(response.response);
			
			//If the file has no error
			if (typeof response.error == 'undefined')
			{
				var list = $('#movieList').val();
				
				//create this movie record
				var movie = file.target_name;
				
				//append this movie to the movie list
				//Check if this is the first movie
				if (typeof list == 'undefined' || list.length == 0 || list == '')
				{
					$('#movieList').val(movie);
				}
				else
				{
					$('#movieList').val(list + '|' + movie);
				}
			}
		});
	});
	
	//A function to submit the news
	function submitMovie()
	{
		$('#movieForm').submit();
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
				$("#movieImage", $currentTab).val("");
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
			image.style.width = '300px';
			$(previewSection).append(image);
		
			previewSection.fadeIn("slow");
			
			//populate the hidden input for the product
			$("#movieImage", $currentTab).val(imageName);
		}
	}
</script>