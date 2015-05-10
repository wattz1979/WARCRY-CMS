// JavaScript Document

(function($)
{
  	var $AlertBox = {};
	
	var methods =
	{
		getCaller : function()
		{
			return (typeof $(this) != 'undefined') ? $AlertBox.CalledBy : null;
		},
		
		open : function(html, buttons)
		{
			if (typeof $(this) != 'undefined')
			{
				$AlertBox.CalledBy = $(this);
			}
			else
			{
				$AlertBox.CalledBy = null;
			}
				
			//append new element
			$('body').append('<div id="Alert-box_container" align="center"><div class="alert-box-holder">'+ html +'</div></div>');
			
			//Check if we have buttons
			if (typeof buttons == 'object')
			{
				$('.alert-box-holder').append('<div class="alert-box-buttons"></div>');
				
				//loop the buttons
				for (var key in buttons)
				{
					var button = buttons[key];
					
					//create the button
					var newButton = $('<a href="#">' + button.text + '</a>');
					
					//bund click function
					if (typeof button.onclick == 'string')
					{
						if (button.onclick == 'close')
							$(newButton).click(function(){ $.fn.WarcryAlertBox('close'); return false; });
					}
					else if (typeof button.onclick == 'function')
					{
						$(newButton).click(button.onclick);
					}
					
					//append the button
					$('.alert-box-buttons').append(newButton);
				}
			}
			
			//set the container demensions
			var windowWidth = $(window).innerWidth();
			var windowHeight = $(window).innerHeight();
				
			$('#Alert-box_container').css({ width: windowWidth + 'px', height: windowHeight + 'px' });
								
			var parentHeight = windowHeight;
			var height = $('#Alert-box_container > .alert-box-holder').height();
						
			//positioning vertically to center
			$('#Alert-box_container > .alert-box-holder').css({ top: (parentHeight/2) + 'px', marginTop: '-' + (height/2) + 'px' });
 				 				
			$('#Alert-box_container').stop().animate({ opacity: 1 }, 'fast');

			$AlertBox.closeEvent = true;
														
			//bind some close events
			$('#Alert-box_container > .alert-box-holder').on('mouseenter', function()
			{
				$AlertBox.closeEvent = false;
			});
			$('#Alert-box_container > .alert-box-holder').on('mouseleave', function()
			{
				$AlertBox.closeEvent = true;
			});
			$('#Alert-box_container').on('click', function()
			{
				if ($AlertBox.closeEvent)
				{
					$.fn.WarcryAlertBox('close');
				}
			});
			//close the box on escape
			$(document).keyup(function(e)
			{
				//if escape key
				if (e.keyCode == 27) 
				{
					if ($('#Alert-box_container').length > 0)
					{
						//if the container is visible only
						if ($('#Alert-box_container').is(':visible'))
						{
							$.fn.WarcryAlertBox('close');
						}
					}
				}
			});
		},
		
		close : function()
		{
			$('#Alert-box_container').fadeOut('fast', function()
			{
				$('#Alert-box_container').detach();
			});
		},
	}
	
  	$.fn.WarcryAlertBox = function(method)
  	{
  		if (methods[method])
		{
     		return methods[method].apply(this, Array.prototype.slice.call( arguments, 1 ));
    	}
		else
		{
      		$.error( 'Method ' +  method + ' does not exist on jQuery.WarcryAlertBox');
    	}    
  	};

})(jQuery);