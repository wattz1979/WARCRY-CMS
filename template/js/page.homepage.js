//constants
var APP_FACEBOOK = 1;
var APP_TWITTER = 2;
var STATUS_POSITIVE = 1;
var STATUS_NEGATIVE = 0;

function ReplaceFacebook()
{
	//detach the whole thing first
	$('.facebook').find('.button-container').find('.fb-like').detach();
	//append the new link/button
	$('.facebook').find('.button-container').append('<a href="'+$SocialButtons.facebook.pageURL+'" class="fb-like fb-active-hotfix" target="_blank" title="'+$SocialButtons.facebook.text+'">Liked</a>');
}

function ReplaceTwitter()
{
	//detach the whole thing first
	$('.twitter').find('.button-container').find('.twitter-follow-button').detach();
	//append the new link/button
	$('.twitter').find('.button-container').append('<a href="https://twitter.com/'+$SocialButtons.twitter.page+'" class="twitter-follow-button twitter-active-hotfix" target="_blank" title="'+$SocialButtons.twitter.text+'">Follow</a>');
}

function UpdateFacebookCounter()
{
	WarcryQueue('onload').add(function()
	{
		//We have the do not load parameter
		if ($('#facebook-likes-counter').hasClass('do-not-load'))
		{
			WarcryQueue('onload').goNext();
			return;
		}
			
		$.ajax(
		{
			url: $BaseURL + "/ajax.php?phase=22&sid=1",
			cache: false,
			dataType: 'json',
			success: function(data)
			{
				$('#facebook-likes-counter').html(data.likes);
				WarcryQueue('onload').goNext();
			}
		});
	});
}
function UpdateTwitterCounter()
{
	WarcryQueue('onload').add(function()
	{
		//We have the do not load parameter
		if ($('#twitter-follows-counter').hasClass('do-not-load'))
		{
			WarcryQueue('onload').goNext();
			return;
		}
		
		$.ajax(
		{
			url: $BaseURL + "/ajax.php?phase=22&sid=2",
			cache: false,
			dataType: 'json',
			success: function(data)
			{
				$('#twitter-follows-counter').html(data.followers_count);
				WarcryQueue('onload').goNext();
			}
		});
	});
}
function UpdateSocial(app, status)
{
	var $app = app;
	var $status = status;
	
	//do the cha cha
	$.ajax({
		url: $BaseURL + "/ajax.php?phase=9", 
		data: 
		{
			app: $app,
			status: $status,
		},
		type: 'post',
		error: function(XMLHttpRequest, textStatus, errorThrown)
		{
			if(XMLHttpRequest.status == 408)
			{
				UpdateSocial($app, $status);
			}
		}
	});	
}

$(document).ready(function()
{   
	// ############################################################### //
	// ####################### FACEBOOK ############################## //
	
	//Determine if we need to load it
	if ($SocialButtons.facebook.status == STATUS_NEGATIVE)
	{
		// Load the SDK's source Asynchronously
		// Note that the debug version is being actively developed and might 
		// contain some type checks that are overly strict. 
		// Please report such bugs using the bugs tool.
		(function(d, debug)
		{
			var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
		 	if (d.getElementById(id)) {return;}
		 	js = d.createElement('script'); js.id = id; js.async = true;
		 	js.src = "//connect.facebook.net/en_US/all" + (debug ? "/debug" : "") + ".js";
		 	ref.parentNode.insertBefore(js, ref);
		}(document, /*debug*/ false));
		
		//bind the click shit
		window.fbAsyncInit = function()
		{
			// init the FB JS SDK
			FB.init({
			  appId      : $SocialButtons.facebook.appId, // App ID from the App Dashboard
			  //status     : true, // check the login status upon init?
			  //cookie     : true, // set sessions cookies to allow your server to access the session?
			  xfbml      : true  // parse XFBML tags on this page?
			});

			//catch like event
			FB.Event.subscribe('edge.create', function(href, widget)
			{
				var Likes = parseInt($('#facebook-likes-counter').html());
				//update
				$('#facebook-likes-counter').html(parseInt(Likes + 1));
				//update visual
				$('#facebook-icon').addClass('active');
				//replace the button
				ReplaceFacebook();
				//storing like so we can update the visual of users
				if ($CURUSER.isOnline)
				{
					UpdateSocial(APP_FACEBOOK, STATUS_POSITIVE);
				}
			});
		};
	}
	
	// ############################################################### //
	// ######################## TWITTER ############################## //
	
	//Determine if we need to load it
	if ($SocialButtons.twitter.status == STATUS_NEGATIVE)
	{
		window.twttr = (function (d,s,id) 
		{
			var t, js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id))
				return; 
			js=d.createElement(s);
			js.id=id;
			js.src="//platform.twitter.com/widgets.js";
			js.async = true;
			fjs.parentNode.insertBefore(js, fjs);
			
			return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });
		}
		(document, "script", "twitter-wjs"));
	  	
		//Twitter Events
		twttr.ready(function(twttr)
		{
			//Twitter Events
			twttr.events.bind('follow', function(event)
			{
				var Followers = parseInt($('#twitter-follows-counter').html());
				//update
				$('#twitter-follows-counter').html(parseInt(Followers + 1));
				//update visual
				$('#twitter-icon').addClass('active');
				//replace the button
				ReplaceTwitter();
				//storing like so we can update the visual of users
				if ($CURUSER.isOnline)
				{
					UpdateSocial(APP_TWITTER, STATUS_POSITIVE);
				}
			});
		});
	}
	
	//faceb00k get the likes count
	UpdateFacebookCounter();
	//Twitter followers update
	UpdateTwitterCounter();
});
