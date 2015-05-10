
var Article =
{
	CommentHTML: '' + 
		'<div class="comment_row" style="display: none;">' + 
            '<div class="headline">' + 
				'<p><a href="#" id="author"></a> said:</p>' + 
				'<span id="time">Just Now</span>' + 
           	'</div>' + 
			'<p class="comment"></p>' + 
		'</div>',
	
	//Variables
	ArticleID: 0,
	PerPage: 10,
	
	//Store the last comment that should be added at the end of the queue when pulling new
	LastCommentData: null,
	
	getParameterByName: function(name)
	{
		name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
		var regexS = "[\\?&]" + name + "=([^&#]*)";
		var regex = new RegExp(regexS);
		var results = regex.exec(window.location.search);
		
		if (results == null)
			return false;
		else
			return decodeURIComponent(results[1].replace(/\+/g, " "));
	},
	
	BindHandlers: function()
	{
		var form = $('#quick-comment');
		
		//Check if we have the form
		if (form.length > 0)
		{
			//Handle form submit
			form.bind('submit', function()
			{
				$.ajaxSetup(
				{
					error: function(xhr, status, error)
					{
						console.log("An AJAX error occured: " + status + "\nError: " + error);
					},
					dataType: "json"
				});
				
				//Insert our new comment
				$.post($BaseURL + '/ajax.php?phase=24', form.serialize(), function(data)
				{
					//Check if we have successful insertion
					if (typeof data.error == 'undefined')
					{
						//remove the text from the textarea
						form.find('#textarea').val('');
						
						//Check if we're on the first page
						if (!Article.getParameterByName('p') || Article.getParameterByName('p') == '1')
						{
							//Store this comment data as last comment in the queue var
							Article.LastCommentData = data;
							//Pull any new comments, without running the queue
							Article.PullNewComments(data.id);
						}
						else
						{
							//Redirect to the first page
							window.location = $BaseURL + '/index.php?page=article&id=' + data.article + '&p=1';
						}
					}
					else
					{
						//We've got error
						$.fn.WarcryAlertBox('open', '<p>' + data.error +'</p>');
					}
				});
				
				return false;
			});
		}
	},
	
	PullNewComments: function(IgnoreComment)
	{
		//Get the last comment id
		LastCommentID = Article.GetLastCommentId();
		
		//Pull the comments Hehe
		$.get($BaseURL + '/ajax.php?phase=25',
		{
			article: Article.ArticleID,
			last_comment: LastCommentID
		},
		function(data)
		{
			//Check for errors
			if (typeof data.error == 'undefined')
			{
				//Check if we have any new comments
				if (parseInt(data.count) > 0)
				{
					//Loop the new comments
					$.each(data.comments, function(i, comment)
					{
						if (typeof IgnoreComment != 'undefined')
						{
							if (parseInt(IgnoreComment) != parseInt(comment.id))
							{
								//queue the comment
								Article.NewCommentInQueue(comment);
							}
						}
					});
				}
			}
			else
			{
				//We've got error
				$.fn.WarcryAlertBox('open', '<p>' + data.error +'</p>');
			}
			
			if (Article.LastCommentData != null)
			{
				Article.NewCommentInQueue(Article.LastCommentData);
			}
			
			//run the queue
			Article.RunCommentQueue();
		});
	},
	
	NewComment: function(data, queue)
	{
		var CommCont = $('.comments-cont');
						
		//Let's setup now comment row
		var NewComm = $(Article.CommentHTML);
		
		//Set the id
		NewComm.attr('data-id', data.id);
		
		//Set the author
		NewComm.find('#author').html(data.author_str);
		NewComm.find('#author').attr('href', $BaseURL + '/index.php?page=profile&uid=' + data.author);
		
		//Set the text
		NewComm.find('.comment').html(data.text);
		
		//Set the time
		NewComm.find('#time').html(humanized_time_span(data.added)).attr('data-original', data.added);
		
		//Update all the timespans
		Article.UpdateTimespans();
		
		//Append the new row
		CommCont.prepend(NewComm);
		
		//Fade in
		NewComm.fadeIn('fast', function()
		{
			if (typeof queue != 'undefined' && queue === true)
				WarcryQueue('ARTICLE').goNext();
		});
	},
	
	NewCommentInQueue: function(data)
	{
		WarcryQueue('ARTICLE').add(function()
		{
			Article.NewComment(data, true);
		});
	},
	
	RunCommentQueue: function()
	{
		WarcryQueue('ARTICLE').goNext();
	},
	
	GetLastCommentId: function()
	{
		return ($('.comments-cont > div:first-child').length > 0) ? parseInt($('.comments-cont > div:first-child').attr('data-id')) : 0;
	},
	
	UpdateTimespans: function()
	{
		var CommCont = $('.comments-cont');
		
		CommCont.find('.comment_row').each(function(index, element)
		{
            var original_time = $(this).find('#time').attr('data-original');
			
			if (typeof original_time != 'undefined' && original_time.length > 0)
				$(this).find('#time').html(humanized_time_span(original_time));
        });
	},
};