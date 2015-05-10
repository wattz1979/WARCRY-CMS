// JavaScript Document

$(document).ready(function()
{
    //Bind Post Delete Buttons
	$('.post-delete-button').on('click', function()
	{
		$(this).WarcryAlertBox('open', '<p>Are you sure you want to delete this post?</p>',
		{
			0: { 
				text: 'Yes', onclick: function(event)
				{
					var PostId = parseInt($.fn.WarcryAlertBox('getCaller').attr('data-post-id'));
					
					$.get($BaseURL + '/ajax.php?phase=17', 
					{ 
						id: PostId,
					},
					function(data)
					{
						if (data == 'OK')
						{
							$('#post-' + PostId).fadeOut('slow', function(){ $(this).detach(); });
						}
						else
						{
							$.fn.WarcryAlertBox('open', '<p>Error: '+data+'</p>');
						}
					});	
					
					//Close the box
					$.fn.WarcryAlertBox('close');
					
					return false;
				}
			},
			1: { text: 'No', onclick: 'close' }
		});
		
		return false;
	});
	
	//Bind Post Quote Buttons
	$('.post-quote-button').on('click', function()
	{
		var PostId = $(this).attr('data-post-id');
		
		//Pull info about the post
		$.get($BaseURL + '/ajax.php?phase=18', 
		{ 
			id: PostId,
		},
		function(data)
		{
			//Check for error
			if (typeof data.error == 'undefined')
			{
				var PostText = data.text;
				var PostAuthor = data.author;
				var QuoteText = '[quote='+PostAuthor+']'+PostText+'[/quote]' + "\n";
				
				//Focus the text area
				$('#quick_reply_textarea').focus();
				//Append the text
				$('#quick_reply_textarea').html(QuoteText);
				//Update the advanced button href
				$('#go-advanced-post').attr('href', $('#go-advanced-post').attr('href') + '&quote=' + PostId);
			}
			else
			{
				$.fn.WarcryAlertBox('open', '<p>Error: '+data.error+'</p>');
			}
		});	

		return false;
	});
});