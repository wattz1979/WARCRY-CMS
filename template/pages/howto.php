<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Set the title
$TPL->SetTitle('How To');
//CSS
$TPL->AddCSS('template/style/page-support-all.css');
//Print the header
$TPL->LoadHeader();

?>
<div class="content_holder">

 <div class="sub-page-title">
  <div id="title"><h1>How to<p></p><span></span></h1></div>
 </div>
 
  	<div class="container_2" align="center">
    
    	<div class="container_3 archived-news" align="left">
        	<!-- How To -->
            
            	<div class="how-to-top-info">
                	Please select one of the articles.
                </div>
              	
                <div id="accordion">
                
	            	<ul class="howto-row">
	                	<li class="howto-row-title">How to Connect</li>
	                    <li class="howto-row-content">
	                    	<p>1. Download World of Warcraft client if you don't have it.<br/>&nbsp;&nbsp;&nbsp;&nbsp;<i>(<a href="#">World of Warcrfat WOTLK Torrent</a>)</i></p>
	                    	<p>2. Create a new account <a href="<?php echo $config['BaseURL']; ?>/index.php?page=register">(here)</a>.</p>
	                        <p>3. Go to "<span>../World of Warcraft/Data/enUS/realmlist.wtf</span>" or "<span>../World of Warcraft/Data/enGb/realmlist.wtf</span>" file.</p>
	                        	<div class="realmlist-file-image"></div>
	                        <p>4. Open <span>realmlist.wtf</span>  with Notepad or any text editor.</p>
	                        <p>5. Replace the text inside with "<span>set realmlist logon.warcry-wow.com</span>" then save.</p>
	                        <p>6. Start the game using the WoW.exe in your World of Warcraft Folder, login and play! </p>
	                    </li>
	                </ul>
                    
                    <ul class="howto-row">
	                	<li class="howto-row-title">TEAMSPEAK : How to Connect</li>
	                    <li class="howto-row-content">
	                    	<p><b>This basic tutorial is to show how someone can put in their information and log in to their selected server.</b></p>
							<br/><br/>
							<p><b>1.</b> Double-click your TeamSpeak 3 Icon on your Desktop to open up the Teamspeak 3 client.</p>
                            <p><b>2.</b> Once the Teamspeak 3 Client is open on the top left of the Teamspeak 3 client click on "Connections" and then click "Connect"(Ctrl+S) it is the first option on the drop down box.</p>
                            <br/>
                            <img src="http://img571.imageshack.us/img571/7500/85f12796987941a08ac0876.png" style=" border-radius:8px;" />
                            <br/><br/>
                            
                           <p><b>3.</b> After you go to "Connections" and the "Connect" a small control panel asking for a Address(Server address/IP), Your Nickname (Can be anything you want, but better use your website Display Name.), Password (There is no password for our TS).</p>
                           <br/>
                           <img src="http://img577.imageshack.us/img577/4554/f6f377355d564ea1a2f8f02.png" style=" border-radius:8px;" />
                           <br/><br/><br/>
                           
                           <p><b>4.</b> Then fill in all the information that was given bellow. For your Username put in what ever you want (But better use your website Display Name). <br/><br/> IP: <b>188.138.121.10:7</b><br/>Password: <b>There is no password!</b></p>
                           <br/>
                           <img src="http://img3.imageshack.us/img3/8775/890446e875634faebfc7196.png" style=" border-radius:8px;" />
                           <br/><br/>
                           
                           <p><b>5.</b> You can hit the "Connect" button at the bottom of the panel now to connect to your server, or if you are already connected to a server and don't want to disconnect from it, click the "In New Tab" button to open another Teamspeak 3 server connection in a new tab so you can access both servers at once.
                           </p>
                           
                            
	                    </li>
	                </ul>
	                
	                <ul class="howto-row">
	                	<li class="howto-row-title">How to keep your Account Safe</li>
	                    <li class="howto-row-content" style="display:none">
	                    	<p><b>1. <font color="#ad8c47">Naming Policy</font></b></p>
		                        <p id="sub-content">
		                        The most frequent and common mistake which players are doing is using the same account name for all the 
		                        servers they play (most of the time, account name matches e-mail adress as well). You should NOT do that. 
		                        Simply because it's making your account extremely vulnerable against hackers and "corrupted" staff team members.
		                        <br/><br/>
		                        
		                        <b>Tip:</b> If you really have problems remembering your account names, you may simply add a few numbers to your original account name.<br/><br/>
								<b>Tip:</b> do not use simple account names like "johny" "michael" "leetpro" etc. Use an abstract word or a 
		                        variation of severalwords. Account must consist of both letters and numbers.<br/><br/>
		                        <b>Tip:</b> Make a text file, where you can save all your private information about accounts. 
		                        This way you won't forget your personal info.(I do that myself) But make sure you have a copy if this text 
		                        file saved somewhere and make sure you're always protected by antivirus.
		                        </p>
	                            
	                    	<p><b>2. <font color="#ad8c47">Setting Password</font></b></p>
		                        <p id="sub-content">
		                        Same as account name. Account passwords must be different for all accounts you use. 
	                            This will provide a 100% safety to your account information. Account password should consist of both letters and numbers.
		                        <br/><br/>
	                            <i>Good account password: you0ar3n0tpr3pared</i>
	                            <br/><br/>
	                            
		                        <b>Tip:</b> Change your password at least 1 time per 2-3 months.<br/><br/>
	                            <b>Tip:</b> Use traditional Latin alphabet, using letters such as "ö" may incorrectly store your password in the database.
	                            <br/><br/>
		                        </p>
	                            
	                    	<p><b>4. <font color="#ad8c47">Email</font></b></p>
		                        <p id="sub-content">
		                        Setting your email (a valid one) is extremely important as THIS WILL BE YOUR ONLY WAY TO RETRIEVE ACCOUNT PASSWORD IF YOU FORGET IT.
	                            <br/><br/>
	                            Make sure your email is active and accepts messages from @warcry-wow.com.
		                        </p>
	                            
	                        <p><b>4. <font color="#ad8c47">Sharing account information</font></b></p>
		                        <p id="sub-content">
		                       	There are a lot of people who share the same account. This is a very bad idea. This completely compromizes accounts security and here are a few reasons why:
	                            <br/><br/>
	                            1. 
	                            You may be sure that the guy you gave your account info is a relayable person. - WRONG. 
	                            NEVER EVER trust anyone, especially those so-called "friends" you've found on the web are not friends at all. 
	                            You've never seen them, you've never heard them - how can you trust it?
	                            <br/><br/>
	                            2. Trying to trade your account is another stupid idea. First of all, if you think you can earn some money using Warcry-WoW 
	                            server you need to think again. The person who are you trying with can simply screenshot conversation which will 
	                            lead to your account permament suspenion. Trading accounts on Warcry-WoW  is general not even possible because 
	                            you cannot change account e-mail -> cannot change password of "newly" traded account. Use common sense and never sell/trade your account.
		                        </p>
	                    	
	                    </li>
	                </ul>
                    
                    <ul class="howto-row">
	                	<li class="howto-row-title">How To Earn Coins</li>
	                    <li class="howto-row-content how-coins">
                        	<h2 id="gold"><p></p>Gold Coins<span></span></h2>
	                    	<ul class="methods-rows">
                                <li>
                                    <a href="<?php echo $config['BaseURL']; ?>/index.php?page=purchase-gcoins">PURCHASE GOLD COINS</a> - You can purchase Gold Coins via PayPal, Credit Card, Bank Transactions, SMS or by Phone. 1 Gold Coin is 1 USD.
                                </li>
                                <li>
                                    <a href="<?php echo $config['BaseURL']; ?>/index.php?page=earn-gcoins">EARN GOLD COINS</a> - You can earn Gold Coins by completing offers (e.g. surveys, downloads, videos, sign ups and more).
                                </li>
                                <li>
                                <a href="<?php echo $config['BaseURL']; ?>/index.php?page=recruit-a-friend">RECRUIT A FRIEND</a> - Get one of your friends to play on Warcry and level up a character to 60 or 80 for Death Knights and get 5 Gold Coins if your friend purchases 50 Gold Coins.
                                </li>
                            </ul>
                            
                            <h2 id="silver"><p></p>Silver Coins<span></span></h2>
                
                            <ul class="methods-rows">
                                <li>
                                    <a href="<?php echo $config['BaseURL']; ?>/index.php?page=vote">VOTE FOR US</a> - Voting helps us grow. As a reward you'll get 2 Silver Coins for each site you vote on every 12 hours.
                                </li>
                                
                                <li>
                                    <a href="<?php echo $config['BaseURL']; ?>/index.php?page=recruit-a-friend">RECRUIT YOUR FRIENDS</a> - Get one of your friends to play on Warcry and level up a character to 60 or 80 for Death Knights and get 1 Silver Coin if your friend votes for us.
                                </li>
                                
                                <li>
                                    <a href="<?php echo $config['BaseURL']; ?>/index.php?page=upload-screanshot">SUBMIT SCREENSHOTS</a> -  Capture your favorite moments on Warcry and send them to us! You'll be given 1 Silver Coin for each screenshot that appears on our site.
                                </li>
                                
                                <li class="not-allowed">
                                    <a href="<?php echo $config['BaseURL']; ?>/index.php?page=bugtracker">BUG TRACKER</a> - Is one of your spells not working or found some other bug? Report them and get 4 Silver Coins for each aproved report.
                                </li>
                                
                                <li>
                                    <a href="#">LIKE US ON FACEBOOK</a> - Help us spread Warcry and like us on Facebook. We'll give you 5 Silver Coins if you do!
                                </li>
                                 
                            </ul>
	                    </li>
	                </ul>
	            	
                </div>
            
            <!-- How To.End -->
    	</div>
        
    </div>
    
</div>

<script>
	$(document).ready(function()
	{
		$("#accordion").accordion({ header: '.howto-row-title', autoHeight: false, active: false });
		
		<?php
		//do we need to activate one of the guides?
		$activate = isset($_GET['activate']) ? (int)$_GET['activate'] : false;
		
		if ($activate !== false)
		{
			echo '$("#accordion").accordion("activate", ', $activate, ');';
		}
		
		unset($activate);
		?>
	});
</script>

<?php

//Add to the loader
$TPL->AddFooterJs('template/js/jquery-ui-1.8.16.custom.min.js');
//Print the header
$TPL->LoadFooter();

?>