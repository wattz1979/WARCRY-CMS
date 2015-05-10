<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Set template parameters
$TPL->SetParameters(array(
	'title'		=> 'Home',
	'slider'	=> true,
	'topbar'	=> true
));
//Print the header
$TPL->LoadHeader();

?>

<div class="content_holder">

<?php

if ($config['IMPORTANT_NOTICE']['ENABLE'] == true)
{
	echo '
	<div class="important_notice">
		<p>'. $config['IMPORTANT_NOTICE']['MESSAGE'] .'</p>
	</div>
	';
}

?>

<!-- Main Side -->
<div class="main_side">
 
<!-- Index News -->
<div class="index_news">
   
   	<div class="welcome_to_warcry">
    	<h1>Welcome to Warcry WoW</h1>
        <p>
        Warcry is a quality warcraft server utilizing talented developers and resources to ensure the best blizz-like experience around. Our team is a dedicated team that professionally creates and maintain everyday features for the players. Register today!
        </p>
    </div>
   	
	<div class="news_container">
    	
        <ul class="header">
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=news">Archived News</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=changelogs">Changelogs</a></li>
        </ul>
        <div class="clear"></div>
        
        <div class="active_latest_news">
        
			<?php
            //Get latest news
            $res = $DB->prepare("SELECT * FROM `news` ORDER BY `id` DESC LIMIT 1;");
            $res->execute();
            
			if ($res->rowCount() > 0)
			{
				$row = $res->fetch();
				
				echo '
				<div class="news_thumb_image"><img src="./uploads/news/thumbs/', $row['image'], '" /></div>
				<div class="news_content">
					<h1>', stripslashes($row['title']), '</h1>
					<h4>By <a href="#">', $row['authorStr'], '</a>, ', $CORE->convertDataTime($row['added']), '</h4>
					<p>', stripslashes($row['shortText']), '</p>
					<a class="readn_ln" href="?page=news&id=', $row['id'], '">Read More</a>
				</div>
				<div class="clear"></div>';
			}
			unset($res, $row);
			?>
            
        </div>
        
        <ul class="older_news">
        
        <?php
			$res = $DB->prepare("SELECT * FROM `news` ORDER BY `id` DESC LIMIT 1, 3;");
			$res->execute();
			
			while ($arr = $res->fetch())
			{
				echo '
				<li>
					<h2><a href="?page=news&id=', $arr['id'], '">', stripslashes($arr['title']), '</a></h2>
					<h4>by <a href="#">', $arr['authorStr'], '</a>, ', $CORE->convertDataTime($arr['added']), '</h4>
					<div class="line_sep"></div>
					<a class="rm" href="?page=news&id=', $arr['id'], '">Read More</a>
					<div class="hover_effect"></div>
				</li>';
			}
			unset($arr, $row);
		?>
        
        </ul>
    
	</div>
    
</div>
<!-- Index News.End -->

	<!-- SOCIAL Media -->
	<div class="social-media container">
        <div class="media-buttons-holder"><!-- Media buttons holder -->
        		
                <?php
					####################################################
					####### FACEBOOK 
					
					$FB_BTN_STATE = '';
					//check if there is user and if he liked us on FB
					if ($CURUSER->isOnline())
					{
						//get the status of FB
						if ($CURUSER->getSocial(APP_FACEBOOK) == STATUS_POSITIVE)
						{
							$FB_BTN_STATE = 'active';
						}
					}
					
					####################################################
					####### TWITTER
					$TWT_BTN_STATE = '';
					//check if there is user and if he liked us on twitter
					if ($CURUSER->isOnline())
					{
						//get the status of twitter
						if ($CURUSER->getSocial(APP_TWITTER) == STATUS_POSITIVE)
						{
							$TWT_BTN_STATE = 'active';
						}
					}
				?>
        
		         <!-- Facebook -->
		         <div class="media-wrapp">
                    <div class="media-button-holder">
                    
	                 	<!-- New Media Button look -->
	                 	<div class="facebook media-new-design" id="facebook-button">
                        	<div class="button-container">
	                    		<div class="new-design-left-part"><p class="icon <?php echo $FB_BTN_STATE; ?>" id="facebook-icon"></p><span>Like</span></div>
                                <?php
									//manage active/inactive state of Facebook button
									if ($FB_BTN_STATE == '')
									{
                            			echo '<div class="fb-like" data-href="', $config['FACEBOOK']['pageURL'], '" data-send="false" data-width="500" data-show-faces="false"></div>';
									}
									else
									{
										echo '<a href="', $config['FACEBOOK']['pageURL'], '" class="fb-like fb-active-hotfix" target="_blank" title="', $config['FACEBOOK']['liked_text'], '">Liked</a>';
									}
								?>
                            </div>
	                        <div class="new-design-count-cont">
                            	<p class="arrow"></p>
                                <?php
                                if ($json = $CACHE->get('facebook_likes'))
								{
									$data = json_decode($json, true);
									echo '<span id="facebook-likes-counter" class="do-not-load">', $data['likes'], '</span>';
									unset($data);
								}
								else
								{
									echo '<span id="facebook-likes-counter">0</span>';
								}
								unset($json)
								?>
                           	</div>
	                    </div>
                        <!-- New Media Button look.End -->
                        
                    </div>
		         </div>
		            
		         <!-- TWITTER -->
		         <div class="media-wrapp">
                    <div class="media-button-holder">
                    
	                 	<!-- New Media Button look -->
	                 	<div class="twitter media-new-design" id="twitter-button">
                        	<div class="button-container">
	                    		<div class="new-design-left-part"><p class="icon <?php echo $TWT_BTN_STATE; ?>" id="twitter-icon"></p><span>Follow</span></div>
                                 <?php
									//manage active/inactive state of Twitter button
									if ($TWT_BTN_STATE == '')
									{
                        				echo '<a href="https://twitter.com/', $config['TWITTER']['page'], '" class="twitter-follow-button">Follow</a>';
									}
									else
									{
                        				echo '<a href="https://twitter.com/', $config['TWITTER']['page'], '" class="twitter-follow-button twitter-active-hotfix" target="_blank" title="', $config['TWITTER']['following_text'], '">Follow</a>';
									}
								?>
                            </div>
	                        <div class="new-design-count-cont">
                            	<p class="arrow"></p>
                                <?php
                                if ($json = $CACHE->get('twitter_stats'))
								{
									$data = json_decode($json, true);
									echo '<span id="twitter-follows-counter" class="do-not-load">', $data['followers_count'], '</span>';
									unset($data);
								}
								else
								{
									echo '<span id="twitter-follows-counter">0</span>';
								}
								unset($json)
								?>
                            </div>
	                    </div>
                        <!-- New Media Button look.End -->
                        
                    </div>
		         </div>
                 
                 <?php
				 	unset($FB_BTN_STATE, $TWT_BTN_STATE);
				 ?>
                 
                 <!-- YOUTUBE -->
		         <div class="media-wrapp">
                    <div class="media-button-holder">
                    
	                 	<!-- New Media Button look -->
	                 	<div class="youtube media-new-design">
	                    	<a href="http://www.youtube.com/user/WarcryWoW1" target="_blank"><div class="new-design-left-part"><p class="icon"></p><span>YouTube</span></div></a>
	                    </div>
                        <!-- New Media Button look.End -->
                        
                    </div>
		         </div>
         
         </div><!-- Media buttons holder.END -->
        <div class="gradient"></div>
	</div>
 	<!-- SOCIAL Media.End -->

    <!-- MEDIA -->
    
    	<div class="home_media">
        
        	<div class="new_trailer">
            	<div class="sub_header">
                	<h1>New Video</h1>
                    <a href="<?php echo $config['BaseURL']; ?>/index.php?page=all-videos">All Videos</a>
                    <div class="clear"></div>
                </div>
                <div class="new_video_thumb">
                <?php
						//Define the chosen movie
						$ChooseMovieId = false;
						
						//Check if we have a chosen movie
						if (!$ChooseMovieId)
						{
							$res = $DB->query("SELECT `id` FROM `movies` ORDER BY `id` DESC LIMIT 1;");
							
							if ($res->rowCount() > 0)
							{
								$row = $res->fetch();
								//set chosen
								$ChooseMovieId = $row['id'];
								
								unset($row);
							}
							unset($res);
						}
						
						//get the chosen movie
						$res = $DB->prepare("SELECT `id`, `name`, `short_text`, `youtube`, `image`, `dirname` FROM `movies` WHERE `id` = :id LIMIT 1;");
						$res->bindParam(':id', $ChooseMovieId, PDO::PARAM_INT);
						$res->execute();
						
						if ($res->rowCount() > 0)
						{
							$row = $res->fetch();
								
							echo '
							<a title="', $row['name'], '" href="index.php?page=open-video&id=', $row['id'], '">
								<!--Video THUMB Preview-->
								<div class="image-thumb-preview" style="background-image:url(\'', $config['BaseURL'], '/uploads/media/movies/', $row['dirname'], '/thumbnails/index_', $row['image'], '\');"></div>
								<div class="play-button-small"></div>
							</a>';
						}
						unset($ChooseMovieId, $res);
					?>
                    </div>
                    
                    <div class="sub_header sreenshots">
                        <h1>New Screenshots</h1>
                        <a href="<?php echo $config['BaseURL']; ?>/index.php?page=all-screenshots">All Screeshots</a>
                        <div class="clear"></div>
                	</div>
                    
                    <?php
                //Random Screenshots
                $type = TYPE_SCREENSHOT;
                $status = SCREENSHOT_STATUS_APPROVED;
				
				$res = $DB->prepare("SELECT * FROM `images` WHERE `type` = :type AND `status` = :status ORDER BY RAND() LIMIT 3;");
				$res->bindParam(':type', $type, PDO::PARAM_INT);
				$res->bindParam(':status', $status, PDO::PARAM_INT);
				$res->execute();
				
				echo '
					<!-- Screenshots -->
					<ul class="screanshots home_scr">';
					
					//loop the records
					while ($arr = $res->fetch())
					{
						echo '
							<li>
								<a href="', $config['BaseURL'], '/uploads/media/screenshots/', $arr['image'],'" class="container_frame" rel="shadowbox" title="', $arr['name'], '{|}', $arr['descr'], '">
									<span class="cframe_inner" style="background-image:url(', $config['BaseURL'], '/uploads/media/screenshots/thumbs/', $arr['image'],'); background-repeat: no-repeat; background-size:121%;"></span>
								</a>
							</li>';
					}
					unset($res);
				
				echo '<div class="clear"></div></ul>';
				
				?>
                
            </div>
        </div>
    
    <!-- MEDIA.End -->
    
    <!-- TOP VOTERS -->
        <div class="top_voters">
            <div class="sub_header">
                <h1>Top Voters</h1>
                <h2>! Reset Every Month</h2>
            </div>
            
            <div class="cont_container">
            
            	<ul class="top_voters_list">
                <?php
				
				$year = date('Y');
				$month = date('n');
				
				$res = $DB->prepare("SELECT `account`, `counter` FROM `votecounter` WHERE `year` = :year AND `month` = :month ORDER BY `counter` DESC LIMIT 5;");
				$res->bindParam(':year', $year, PDO::PARAM_INT);
				$res->bindParam(':month', $month, PDO::PARAM_INT);
				$res->execute();
				
				$x = 1;
				
				while ($arr = $res->fetch())
				{
					$accid = $arr['account'];
					
					$res2 = $DB->prepare("SELECT `displayName` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
					$res2->bindParam(':acc', $accid, PDO::PARAM_INT);
					$res2->execute();
					
					$arr2 = $res2->fetch();
					
					echo '
						<li>
							<p>', $x ,'</p>
							<a href="', $config['BaseURL'], '/index.php?page=profile&uid=', $accid, '">', $arr2['displayName'] ,'</a>
							<span>', $arr['counter'] ,' <i>Votes</i></span>
						</li>';
					$x++;
					unset($res2, $arr2, $accid);
				}
				unset($res, $x);
				
				?>
                <div class="gift_box">
                	<div class="gift_image"></div>
                    <h2>
                    Monthly rewards will be given to the Top Voters. 
                    25 silver coins for the Top 5 and an additional 5 gold coins for top 3.
                    </h2>
                </div>
            
            </div>
            
        </div>
    <!-- TOP VOTERS.End -->
    
    <div class="clear"></div>
    
</div>
<!-- Main side.End-->

<?php

//include the sidebar
include $config['RootPath'] . '/template/sidebar.php';

?>

<div class="clear"></div>

</div>

<!-- Include Social APIs -->
<div id="fb-root"></div>

<?php
	//Add some javascripts to the loader
	$TPL->AddFooterJs('template/js/page.homepage.js');
	$TPL->AddFooterJs('template/js/shadowbox.js');
	$TPL->AddFooterJs('template/js/init.custom.shadowbox.js');
	//print the footer
	$TPL->LoadFooter();
?>
