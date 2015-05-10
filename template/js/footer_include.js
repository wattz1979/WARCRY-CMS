// JavaScript Document

//on document ready
$(document).ready(function()
{
    WarcryQueue('onload').goNext();	
	
	//transform the select forms
	$(document).find('select').each(function(index, element)
	{
        if (typeof $(element).attr('styled') != 'undefined')
		{
			if ($(element).attr('id') == 'character-select')
			{
				$(element).SelectTransform({scrollConfig: { scrollBy: 4, }});
			}
			else
			{
				$(element).SelectTransform();
			}
		}
    });
});

//do the vertical centring, for all with class vertical_center
$(function()
{
	if ($('.vertical_center').length > 0)
	{
		$('.vertical_center').each(function()
		{
			var parentHeight = $(this).parent().height();
			var height = $(this).outerHeight();
				
			$(this).css('position', 'relative');
		
			//center it
			$(this).parent().css('height', (height+15) + 'px');
			$(this).css({ top: (parentHeight/2) + 'px', marginTop: '-' + (height/2) + 'px' });
		});
	}
});

//A function to center the the swfmovie on the homepage
function CenterSWFMovie(id)
{
	$this = $('#'+id);
	
	if ($this.length > 0)
	{
		if (navigator.appName == 'Microsoft Internet Explorer')
		{
			//Internet Explorer Fix
			var $window = $(window);
			var $width = $window.width();
			var $height = $window.height();

			setInterval(function ()
			{
				//check if it's acctual resize
				if (($width != $window.width()) || ($height != $window.height())) 
				{
					//once the resize was called update the global vars
					$width = $window.width();
					$height = $window.height();
					
					//Do what you want
					if ($this.css('position') != 'absolute')
					{
						$this.css('position', 'absolute');
					}
					
					var windowWidth = $(window).width();
					var width = $this.width();
					
					//break if we reached the minimum
					if (windowWidth <= 1024)
					{
						return;
					}
				
					//center it
					$this.css({ left: (windowWidth/2) + 'px', marginLeft: '-' + (width/2) + 'px' });
				}
			}, 100);
		}
		else
		{
			//Normal way of handling it
			//bind the window resize event
			$(window).resize(function() 
			{
				console.log('Resize!');
				if ($this.css('position') != 'absolute')
				{
					$this.css('position', 'absolute');
				}
				
				var windowWidth = $(window).width();
				var width = $this.width();
				
				//break if we reached the minimum
				if (windowWidth <= 1024)
				{
					return;
				}
			
				//center it
				$this.css({ left: (windowWidth/2) + 'px', marginLeft: '-' + (width/2) + 'px' });
			});
		}
	}
}

//this decreases website preformance
//chrome aitofill solution
if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0)
{
	$(window).load(function(){
	    $('input:-webkit-autofill').each(function(){
	        var text = $(this).val();
	        var name = $(this).attr('name');
	        $(this).after(this.outerHTML).remove();
	        $('input[name=' + name + ']').val(text);
	    });
		setTimeout(function()
		{
			$('input:-webkit-autofill').each(function(){
	        	var text = $(this).val();
	        	var name = $(this).attr('name');
	       	 	$(this).after(this.outerHTML).remove();
	       	 	$('input[name=' + name + ']').val(text);
	    	});
		}, 100);
	});
}
//bind the login button handler
$(function()
{
	//if the CURUSER is not Online
	if (!$CURUSER.isOnline)
	{
		$LoginBox.closeEvent = false;
		
		$('.member-side-left .not-logged-menu > li > a#login').on('click', function()
		{			
			if (!$LoginBox.isLoaded)
			{
				//load the login box HTML
				//append new element
				$('body').append('<div id="Login-box_container" align="center"><div class="login-box-holder container_3"></div></div>');

				//set the container demensions
				var windowWidth = $(window).innerWidth();
				var windowHeight = $(window).innerHeight();
				
				$('#Login-box_container').css({ width: windowWidth + 'px', height: windowHeight + 'px' });
								
				var parentHeight = windowHeight;
				var height = $('#Login-box_container > .login-box-holder').height();
						
				//positioning vertically to center
				$('#Login-box_container > .login-box-holder').css({ top: (parentHeight/2) + 'px', marginTop: '-' + (height/2) + 'px' });
 				
				//bind some close events
				$('#Login-box_container > .login-box-holder').on('mouseenter', function()
				{
					$LoginBox.closeEvent = false;
				});
				
				//bind the close event after 1500 ms
				setTimeout(function()
				{
					$('#Login-box_container').on('click', function()
					{
						if ($LoginBox.closeEvent)
						{
							$('#Login-box_container').fadeOut('fast');
						}
					});
					//close the box on escape
					$(document).keyup(function(e)
					{
						//if escape key
						if (e.keyCode == 27) 
						{
							//if the container is visible only
							if ($('#Login-box_container').is(':visible'))
							{
								$('#Login-box_container').fadeOut('fast');
							}
						}
                    });
				}, 1500);
		
				$('#Login-box_container').stop().animate({ opacity: 1 }, "fast", function()
				{
					$('#temp-login-form > .login-box').appendTo('#Login-box_container > .login-box-holder');
					
					$LoginBox.isLoaded = true;
					$LoginBox.closeEvent = true;
					
					$('#Login-box_container > .login-box-holder').on('mouseleave', function()
					{
						$LoginBox.closeEvent = true;
					});

					//set the URL Before Login
					$('#js-login-box_urlbl').attr('value', window.location.href);								
				});
			}
			else
			{
				//the HTML is loaded, fade in
				$('#Login-box_container').stop().fadeIn('fast');
			}
						
			return false;
		});		
	}
});

//Custom Radio Buttons script
function setupLabel()
{
	if ($('.label_check input').length)
	{
		$('.label_check').each(function()
		{ 
			$(this).removeClass('c_on');
		});
		$('.label_check input:checked').each(function()
		{ 
			$(this).parent('label').addClass('c_on');
		});                
	};
	if ($('.label_radio input').length)
	{
		$('.label_radio').each(function()
		{ 
			$(this).removeClass('r_on');
		});
		$('.label_radio input:checked').each(function()
		{ 
			$(this).parent('label').addClass('r_on');
		});
	};
};
$(document).ready(function()
{
	$('body').addClass('has-js');
	$('.label_check, .label_radio').click(function()
	{
		setupLabel();
	});
	setupLabel(); 
});