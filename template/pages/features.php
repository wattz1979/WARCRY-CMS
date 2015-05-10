<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Set the title
$TPL->SetTitle('Features');
//Print the header
$TPL->LoadHeader();

?>
<div class="content_holder">

 <div class="sub-page-title">
  <div id="title"><h1>Features<p></p><span></span></h1></div>
 </div>
 
  	<div class="container_2 features" align="center">
    
           
        <!-- Container Title -->
        <h1 class="features-row-title">Warcry Official Addons</h1>
        <!-- Container Title . End -->
    
    	<div class="container_3 archived-news w-addons" align="left">
        
        	<!-- ADDON ROW -->
            	<div class="w-addon-row">
                	<img src="template/style/images/media/warcry-xp-changer.jpg" width="268" height="163" alt="Warcry XP Rate Changer Addon"/>
                    <div class="addon-info">
                    	<h1>XP Rates Changer</h1>
                        <p>
                        We've implanted a brand new feature which allows you to set your own rates ranging from 1, like blizzards own realms up to 15 times the regular rates. As default we've set the rates at 10 but feel free to change it to your own liking.
                        </p>
                        <div class="war-links"><a class="download" href="<?php echo $config['BaseURL']; ?>/resources/addons/Warcry-XP-Rate-Changer.zip" title="Download Warcry WoW XP Rate Changer" target="_self">Download</a></div>
                    </div>
                    <div class="clear"></div>
                </div>
            <!-- ADDON ROW . End-->
        	
    	</div>
        
        <!-- Container Title -->
        
        <h1 class="features-row-title" style="margin:80px 0 20px 70px;">Website Features</h1>
        <!-- Container Title . End -->
        	
            <div class="feature-row">
            	<img src="template/style/images/media/raf.jpg" width="90" height="90" alt="Warcry Recruit a Friend" />
            	<div class="info">
                	<h1>Recruit your Friends</h1>
                    <h2>
                    Recruiting friends has many benefits! Your friends, however, must become eligable for the program.
                    To become eligible, your referrals must have at least one level 60 character or a level 80 for the Death Knight class.
                    </h2>
                </div>
                <div class="clear"></div>
            </div>
            
            <div class="feature-row">
            	<img src="template/style/images/media/bug-tracker.jpg" width="90" height="90" alt="Warcry Bug Tracker" />
            	<div class="info">
                	<h1>Bug Tracker</h1>
                    <h2>
                    You can report any non working content in-game or on our website or forum.  If we approve your bug report you will receive 4 Silver Coins. Please try to include as much information in the report as you can, set the right priority of your report and the right category.
                    </h2>
                </div>
                <div class="clear"></div>
            </div>
            
            <div class="feature-row">
            	<img src="template/style/images/media/teleporter.jpg" width="90" height="90" alt="Warcry Teleporter" />
            	<div class="info">
                	<h1>Unique Teleporter</h1>
                    <h2>
                   The Teleporter is a free tool that can be used while you are in-game and does not require you to log out. The teleporter has a 5 minute cooldown.
                    </h2>
                </div>
                <div class="clear"></div>
            </div>
            
            <div class="feature-row">
            	<img src="template/style/images/media/store.jpg" width="90" height="90" alt="Warcry Items Store" />
            	<div class="info">
                	<h1>Items Store</h1>
                    <h2>
                   In reward of your voting and support of our community you will be able to spend your Silver or Gold Coins on our store. 
                   You can purchase all items bellow item level 248 with Silver Coins or Gold Coins. 
                   You can also purchase more than one item at a time. (See how to use the Store here !)
                    </h2>
                </div>
                <div class="clear"></div>
            </div>
            
            <div class="feature-row">
            	<img src="template/style/images/media/changelogs.jpg" width="90" height="90" alt="Warcry Bug Tracker" />
            	<div class="info">
                	<h1>Changelogs</h1>
                    <h2>
					The changelog page enables you to view our developers current projects.. 
					Please keep in mind that not all changes are currently applied to the game or the website, but will be on the next server restart.
                    </h2>
                </div>
                <div class="clear"></div>
            </div>            
            
            <br/><br/><br/><br/>
            
            <div class="wotlk-realm-banner">
            	<a href="index.php?page=working_content" title="WOTLK Working Content" target="_self">
                	<h1>Working Content</h1>
                    <h2>Wrath of the Lich King (3.3.5a) - Working Content</h2>
            	</a>
            </div>
            <br/><br/>
            
        
        
        <!-- FEATURES BG --> <div class="features-bg-dark"></div><div class="features-bg"></div>
        
    </div>
    
</div>

<?php
	$TPL->LoadFooter();
?>