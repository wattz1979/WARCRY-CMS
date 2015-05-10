<?php
if (!defined('init_template'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?PHP echo $HeaderTitle; ?></title>
<meta name="google-site-verification" content="rkMFFbCodw-MTlIqBhV6rONSf-0ii1A1VYe21vqUZmg" />
<meta name="author" content="WARCRY WoW">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="title" content="<?PHP echo $HeaderTitle; ?> page.">
<meta http-equiv="Content-Language" content="en">
<meta name="Description" content="Welcome to the best free private server.">
<meta name="Keywords" content="Warcry, Warcry-WoW, Warcry WoW, WoW, World of Warcraft, Warcraft, wotlk, Wrath of the Lich King, wotlk server, Private Server, Private WoW Server, WoW Server, WoW Private Server, win a character, splash, splash page, reward, rewards">
<meta name="language" content="English">
<meta name="type" content="website">
<meta name="copyright" content="Copyright www.warcry-wow.com">
<meta name="resource-type" content="games">
<meta name="Distribution" content="Global">
<meta name="email" content="webmaster@warcry-wow.com">
<meta name="Charset" content="UTF-8">
<meta name="Rating" content="General">
<meta name="robots" content="INDEX,FOLLOW">
<meta name="Revisit-after" content="7 Days">
<meta name="DC.Creator" content="php">
<meta name="DC.Description" content="Welcome to the best free private server.">
<meta name="DC.Type" content="text"><meta name="DC.Language" content="en">
<meta name="DC.Rights" content="(c) warcry-wow.com all rights reserved.">
<link href="<?php echo $config['BaseURL']; ?>/template/style/images/favicon.ico" rel="icon" type="image/x-icon"/>

<?php
//Load diferrent CSS Groups
if (defined('is_forums'))
{
	//Forum CSS Files
	$TPL->AddCSS('template/forums/style/main.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/forums/style/form.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/forums/style/post_topic.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/bbcode-default.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/loginbox.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/shadowbox.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/technical.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/alert-box.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/IndexSlider.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/radio-checkbox.css', false, RESOURCE_LOAD_PRIO_HIGH);
}
else
{
	##########################################
	## Website CSS Files
	#
	# Common Styles
	$TPL->AddCSS('template/style/fonts.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/style.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/technical.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/select.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/forms.css', false, RESOURCE_LOAD_PRIO_HIGH);
	# Misc
	$TPL->AddCSS('template/style/home.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/video-js-new-vision.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/shadowbox.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/pages-background.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/bbcode-default.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/quick-menu.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/IndexSlider.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/account_panel.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/loginbox.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/alert-box.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddCSS('template/style/radio-checkbox.css', false, RESOURCE_LOAD_PRIO_HIGH);
}

//Load css files requested by the page
$TPL->PrintCSS();

//Global Javascript variables
echo '
<script type="text/javascript" language="javascript">
	var $BaseURL = \'', $config['BaseURL'], '\';
	var $WOWDBURL = \'', $config['WoWDB_URL'], '\';
	var $TIMEZONE = \'', $config['TimeZone'], '\';
	var $TIMEZONEOFFSET = \'', $config['TimeZoneOffset'], '\';
	var $CURUSER = { isOnline: ', ($CURUSER->isOnline() ? 'true' : 'false'), ' };
	var $LoginBox = { isLoaded: false };
	var $SocialButtons = {
		facebook: {
			appId: "', $config['FACEBOOK']['appId'],'",
			pageURL: "', $config['FACEBOOK']['pageURL'], '",
			text: "', $config['FACEBOOK']['liked_text'], '",
			status: ', ($CURUSER->isOnline() ? $CURUSER->getSocial(APP_FACEBOOK) : 'false'), ',
		},
		twitter: {
			page: "', $config['TWITTER']['page'], '",
			text: "', $config['TWITTER']['following_text'], '",
			status: ', ($CURUSER->isOnline() ? $CURUSER->getSocial(APP_TWITTER) : 'false'), ',
		},
	};
	var _gaq = _gaq || [];
	_gaq.push([\'_setAccount\', \'UA-34493960-1\']);
	_gaq.push([\'_trackPageview\']);
	(function()
	{
		var ga = document.createElement(\'script\');
		ga.type = \'text/javascript\';
		ga.async = true;
		ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
		var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>';

//Load diferrent JS Groups
if (defined('is_forums'))
{
	//Add default header javascripts for the Forums
	$TPL->AddHeaderJs('template/js/jquery-1.7.js', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddHeaderJs('template/js/custom.js', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddHeaderJs('template/forums/js/base.js', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddHeaderJs('template/js/alertbox.js', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddHeaderJs('template/js/jquery.cycle.all.js', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddHeaderJs('template/js/jquery.easing.1.3.js', false, RESOURCE_LOAD_PRIO_HIGH);
}
else
{
	//Add default header javascripts for the website
	$TPL->AddHeaderJs('template/js/jquery-1.7.js', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddHeaderJs('template/js/custom.js', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddHeaderJs('template/js/alertbox.js', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddHeaderJs('template/js/jquery.cycle.all.js', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddHeaderJs('template/js/jquery.easing.1.3.js', false, RESOURCE_LOAD_PRIO_HIGH);
	$TPL->AddHeaderJs('template/js/video.bg.js', false, RESOURCE_LOAD_PRIO_HIGH);
}

//Load js files requested by the page
$TPL->PrintHeaderJavascripts();

?>
</head>
<body>
 <center>

 <!--HEADER-->
 <div id="header" align="center">
   <div class="holder">
   	
    	<a href="./" class="logo"><p></p></a>
        
        <ul class="top-navigation">
        	<li><a id="home" href="<?php echo $config['BaseURL']; ?>/index.php?page=home"><p></p></a></li>
            <li><a id="forums" href="<?php echo $config['BaseURL']; ?>/forums.php"><p></p></a></li>
             <li id="support">
                <p></p>
                	<div class="nddm-holder" align="center">
		                <div class="navi-dropdown">
	                    	<p id="arrow"></p>
	                    	<!--<span><a href="<?php echo $config['BaseURL']; ?>/index.php?page=addons">Addons</a></span>-->
                            <span><a href="<?php echo $config['BaseURL']; ?>/index.php?page=howto">How to</a></span>
                            <span><a href="<?php echo $config['BaseURL']; ?>/forums/viewforum.php?f=52">Support</a></span>
                            <span><a href="<?php echo $config['BaseURL']; ?>/index.php?page=terms-of-use">Terms of Use</a></span>
                            <span><a href="<?php echo $config['BaseURL']; ?>/index.php?page=references">References</a></span>
                            <span><a href="<?php echo $config['BaseURL']; ?>/index.php?page=rules">Rules</a></span>
		                </div>
                    </div>
             </li>
            <li>
            	<a id="features" href="<?php echo $config['BaseURL']; ?>/index.php?page=features"><p></p></a>
                <p></p>
                	<div class="nddm-holder features" align="center">
		                <div class="navi-dropdown">
	                    	<p id="arrow"></p>
                            <span><a href="<?php echo $config['BaseURL']; ?>/index.php?page=downloads">Downloads</a></span>
                            <span><a href="<?php echo $config['BaseURL']; ?>/index.php?page=bugtracker">Bug Tracker</a></span>
                            <span><a href="<?php echo $config['BaseURL']; ?>/index.php?page=changelogs">Changelogs</a></span>
                            <span><a href="<?php echo $config['BaseURL']; ?>/index.php?page=working_content">Working Content</a></span>
		                </div>
                    </div>
            </li>
            <li>
            	<a id="media" href="<?php echo $config['BaseURL']; ?>/index.php?page=media"><p></p></a>
                <p></p>
                	<div class="nddm-holder media" align="center">
		                <div class="navi-dropdown">
	                    	<p id="arrow"></p>
                            <span><a href="<?php echo $config['BaseURL']; ?>/index.php?page=all-wallpapers">Wallpapers</a></span>
                            <span><a href="<?php echo $config['BaseURL']; ?>/index.php?page=all-videos">Videos</a></span>
                            <span><a href="<?php echo $config['BaseURL']; ?>/index.php?page=all-screanshots">Screenshots</a></span>
		                </div>
                    </div>
            </li>
            
            <li><a id="goal" href="<?php echo $config['BaseURL']; ?>/index.php?page=armory"><p></p></a></li>
        </ul>
   
   </div>
 </div>
 <!--HEADER.End-->
 
<div id="image_header" align="center">

	<?php
	//We use image slider for the forums
	if (defined('is_forums'))
	{
		echo '
		<div class="sub_head_image" align="center">
		
			<!-- TextSlider -->
			<script type="text/javascript">
				$(function() {
					$("#IndexTextFader img").css({
						opacity: 0
					});
					setTimeout(function() {
						$("#IndexTextFader").cycle({
						random: 1,
						delay: -6000
					});
					$("#IndexTextFader img").css({
						opacity: 0
					});
					}, 1000);
				}); 
			</script>
			<div class="ForumsTextFader" id="IndexTextFader">
				<img src="template/style/images/IndexSlider/boosts.png" style="opacity:0;"/>
				<img src="template/style/images/IndexSlider/icc.png" style="opacity:0;"/>
				<img src="template/style/images/IndexSlider/teleporter.png" style="opacity:0;"/>
				<img src="template/style/images/IndexSlider/voters.png" style="opacity:0;"/>
				<img src="template/style/images/IndexSlider/xprate.png" style="opacity:0;"/>
			</div>
			<!-- TextSlider.End -->';
			
			//Select random image
			$HeaderImages = array(
				'template/forums/style/header-images/lichking.jpg',
				'template/forums/style/header-images/nedruid.jpg',
				'template/forums/style/header-images/garrosh.jpg',
				'template/forums/style/header-images/sylva.jpg',
				'template/forums/style/header-images/lock.jpg'
			);
			
			echo '
			<div class="random_image" style="background-image:url(', $HeaderImages[rand(0, count($HeaderImages) - 1)], ');"></div>
		</div>';
	}
	else
	{
		if ($TPL->GetParameter('slider'))
		{
			echo '
			<!-- TextFader Cycle JQuery Plugin -->
			<script type="text/javascript">
				$(function() {
					$("#IndexTextFader img").css({
						opacity: 0
					});
					setTimeout(function() {
						$("#IndexTextFader").cycle({
						random: 1,
						delay: -6000
					});
					$("#IndexTextFader img").css({
						opacity: 0
					});
					}, 1000);
				}); 
			</script>
			
			<div id="IndexTextFader">
				<img src="template/style/images/IndexSlider/boosts.png" style="opacity:0;"/>
				<img src="template/style/images/IndexSlider/icc.png" style="opacity:0;"/>
				<img src="template/style/images/IndexSlider/teleporter.png" style="opacity:0;"/>
				<img src="template/style/images/IndexSlider/voters.png" style="opacity:0;"/>
				<img src="template/style/images/IndexSlider/xprate.png" style="opacity:0;"/>
			</div>
			
			<!-- HTML5 Movie -->
			<div class="slider" id="warcry-slider" align="center">
				<div id="html5-video"></div>
			</div>
			<script>
			$(document).ready(function()
			{
				$("#html5-video").videoBG(
				{
					mp4: "http://media.warcry-wow.com/movies/cwc.mp4",
					ogv: "http://media.warcry-wow.com/movies/cwc.ogv",
					webm: "http://media.warcry-wow.com/movies/cwc.webm",
					poster: "http://media.warcry-wow.com/movies/cwc.jpg",
					scale: true,
					loop: "loop",
					zIndex: 0
				});
			});
			</script>
			<!-- Flash Movie.End -->';
		}
		else
		{
			echo '
			<div class="sub_head_image" align="center">
				<div style="background-image:url(template/style/images/header-images/sub_image_1.jpg);"></div><!-- Random image -->
			</div>';
		}
	}
	?>
    
</div>

<!-- BODY-->
<div class="main_b_holder" align="center">

<?php
//if there is to be Top Bar
if ($TPL->GetParameter('topbar'))
{
	echo '	
	<!-- Membership Sh!t! -->
	<div class="membership-holder">
  		<div class="membership-bar">
         		 
         	<div class="search">
				<form action="', $config['BaseURL'], '/forums/search.php" method="get" id="search">
			  		<input type="text" name="keywords" maxlength="128" title="Search for keywords"><input type="submit" value="">
				</form>
			</div>';
             			
			if (!$CURUSER->isOnline())
			{
				//not logged in
				echo '
            	<!--Not logged-->
            	<div class="member-side-left">
	       			<ul class="not-logged-menu">
             			<li class="login-home"><a id="login" href="#"><p></p><span></span></a></li>
                    	<li class="register-home"><a id="register" href="', $config['BaseURL'], '/index.php?page=register"><p></p><span></span></a></li>
    	   			</ul>
             		<div class="bonus-m-links">
           				<a href="', $config['BaseURL'], '/#">Frequently Asked Questions</a>
                   		<a href="', $config['BaseURL'], '/index.php?page=howto&activate=0">Connection Guide</a>
              		</div>
            	</div>
           	 	<!--Not logged.End-->';
			}
            else
			{
				//get the last vote time
				$LastVoted = $CURUSER->getLastVoteTime();
				if ($LastVoted)
				{
					//Convert to Time Object
					$LastVotedObj = $CORE->getTime(true, $LastVoted);
					$LastVotedObj->add(date_interval_create_from_date_string($config['VOTE']['Cooldown']));
					$CooldownExpires = $LastVotedObj->format('Y-m-d H:i:s');
					
					//dont remind for voting as default
					$RemindVote = false;
					//check if the user is now able to vote so we can remind him
					if ($CORE->getTime() > $CooldownExpires)
					{ 
						$RemindVote = true;
					}
				}
				else
				{
					//we havent voted ever..
					$RemindVote = true;
				}

				//logged in
				echo '           
            	<!-- Logged In -->
     			<div class="logged_in_bar member-side-left">
       				<div class="avatar"><span></span><a href="', $config['BaseURL'], '/index.php?page=avatars" style="background-image:url(', ($CURUSER->getAvatar()->type() == AVATAR_TYPE_GALLERY ? './resources/avatars/'.$CURUSER->getAvatar()->string() : $CURUSER->getAvatar()->string()), '); background-size: 100%;"></a></div>
       
       				<div class="info">
         				<p>Welcome back, <font color="#e9ac35">', $CURUSER->get('displayName'), '</font>!</p>
         				<div class="coints">
          				<span id="gold_c"><div></div>', $CURUSER->get('gold'), ',</span>
          				<span id="silver_c"><div></div>', $CURUSER->get('silver'), '</span>
         				</div>
						
						<!-- Private Messages
                    			<div class="messages"><a href="#"><span class="icon"></span>55</a></div>
						Private Messages . End-->
						
						<!--<div class="vote-now-ico"><a href="#"><span id="icon"></span><p id="icon"></p></a></div>-->
       				</div>
       
       				<ul class="acc-menu">
         				<li><a id="acc-panel" href="', $config['BaseURL'], '/index.php?page=account"><span></span><p></p></a></li>',
						($RemindVote ? '<li class="not-voted-yet-effect"><div></div></li>' : ''),
         				'<li><a id="vote" href="', $config['BaseURL'], '/index.php?page=vote"><span></span><p></p></a></li>
         				<li><a id="buy-coins" href="', $config['BaseURL'], '/index.php?page=buycoins"><span></span><p></p></a></li>
         				<li><a id="store" href="', $config['BaseURL'], '/index.php?page=settings"><span></span><p></p></a></li>
         				<li><a id="logout" href="', $config['BaseURL'], '/logout.php"><span></span><p></p></a></li>
       				</ul>
            	</div>
            	<!-- Logged In.End -->';
			}
  		
		echo '
		</div>
	</div>
	<!-- Membership Sh!t.End -->';
}
else
{
 	echo '<div class="space-fix"></div>';
}
?>

 <div class="sec_b_holder" align="center">
  <div id="body" align="left">
   <!-- BODY Content start here -->
   