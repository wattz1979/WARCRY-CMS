<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();
$CORE->UnderConstruction('Private Messages');

//Set the title
$TPL->SetTitle('Private Messages');
//CSS
$TPL->AddCSS('template/style/message-system.css');
//Print the header
$TPL->LoadHeader();

?>
<div class="content_holder">

<div class="sub-page-title">
	<div id="title"><h1>Account Panel<p></p><span></span></h1></div>
  	<style> .quick-menu:hover .dropdown-qmenu {height:212px !important;}</style>
    <div class="quick-menu">
    	<a class="arrow" href="#"></a>
        <ul class="dropdown-qmenu">
        	<li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=store">Store</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=teleporter">Teleporter</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=buycoins">Buy Coins</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=vote">Vote</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=pstore">Premium Store</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=unstuck">Unstuck</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=settings">Settings & Options</a></li>
            <li id="messages-ddm">
            	<a href="<?php echo $config['BaseURL']; ?>/index.php?page=pm">
                	<b>55</b> <i>Private Messages</i>
                </a>
            </li>
        </ul>
    </div>
</div>
 
  	<div class="container_2 account" align="center">
    
   	  <div style="height:75px;"></div>
      <div class="container_3 account_sub_header">
         <div class="grad">
       		<div class="page-title">Private Messages</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
      	 </div>
      </div>
      
      <!-- Private Messages -->
      	<div class="private-messages">
        
       		<!-- PM Menu -->
            	<div class="pm-menu">
                    <div class="menu">
                    	<a href="#" id="inbox" class="active" name="Inbox">Inbox</a>
                        <a href="#" id="sent-items" name="Sent Items">Sent Items</a>
                        <a href="#" id="write-letters" name="Write Letters"><p>Write Letters</p><div id="ico"></div></a>
                    </div>
                    <div class="pm-top-info">
                    	<h1>55 Letters</h1>
                        <h2><i>(34 Inbox , 21 Sent items)</i></h2>
                    </div>
                </div>
                <div class="clear"></div>
            <!-- PM Menu . End-->

            <div class="container_3 account-wide pm-container" align="center">
            
            	<!-- Message Row -->
                	<ul class="message-row">
                    	<li class="checkbox"><label class="label_check"><div></div><input type="checkbox"/></label></li>
                        <li class="msg-title"><a href="#">Curabitur fermentum blandit velit</a></li>
                        <li class="pmu-holder">
                        	<div class="sent-by"><p>Sent by</p></div>
                            <div class="pm-user-profile">
                            	<div class="pm-up-avatar" style="background-image: url(http://i.imgur.com/mFSpI.png);"></div>
                                <div class="pm-up-info">
                                	<p><font color="#aa0000">EvilSystem</font></p>
                                    <span>(Management)</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                <!-- Message Row . End-->
                
                <!-- Message Row -->
                	<ul class="message-row">
                    	<li class="checkbox"><label class="label_check"><div></div><input type="checkbox"/></label></li>
                        <li class="msg-title"><a href="#">Curabitur fermentum blandit velit</a></li>
                        <li class="pmu-holder">
                        	<div class="sent-by"><p>Sent by</p></div>
                            <div class="pm-user-profile">
                            	<div class="pm-up-avatar" style="background-image: url(http://i.imgur.com/mFSpI.png);"></div>
                                <div class="pm-up-info">
                                	<p><font color="#aa0000">EvilSystem</font></p>
                                    <span>(Management)</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                <!-- Message Row . End-->
                
                <!-- Message Row -->
                	<ul class="message-row important">
                    	<li class="checkbox"><label class="label_check"><div></div><input type="checkbox"/></label></li>
                        <li class="msg-title"><a href="#">Curabitur fermentum blandit velit</a></li>
                        <li class="pmu-holder">
                        	<div class="sent-by"><p>Sent by</p></div>
                            <div class="pm-user-profile">
                            	<div class="pm-up-avatar" style="background-image: url(http://i.imgur.com/mFSpI.png);"></div>
                                <div class="pm-up-info">
                                	<p><font color="#aa0000">EvilSystem</font></p>
                                    <span>(Management)</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                <!-- Message Row . End-->
                
                <!-- Message Row -->
                	<ul class="message-row">
                    	<li class="checkbox"><label class="label_check"><div></div><input type="checkbox"/></label></li>
                        <li class="msg-title"><a href="#">Curabitur fermentum blandit velit</a></li>
                        <li class="pmu-holder">
                        	<div class="sent-by"><p>Sent by</p></div>
                            <div class="pm-user-profile">
                            	<div class="pm-up-avatar" style="background-image: url(http://i.imgur.com/mFSpI.png);"></div>
                                <div class="pm-up-info">
                                	<p><font color="#aa0000">EvilSystem</font></p>
                                    <span>(Management)</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                <!-- Message Row . End-->
                
                 <!-- Message Row -->
                	<ul class="message-row">
                    	<li class="checkbox"><label class="label_check"><div></div><input type="checkbox"/></label></li>
                        <li class="msg-title"><a href="#">Curabitur fermentum blandit velit</a></li>
                        <li class="pmu-holder">
                        	<div class="sent-by"><p>Sent by</p></div>
                            <div class="pm-user-profile">
                            	<div class="pm-up-avatar" style="background-image: url(http://i.imgur.com/mFSpI.png);"></div>
                                <div class="pm-up-info">
                                	<p><font color="#aa0000">EvilSystem</font></p>
                                    <span>(Management)</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                <!-- Message Row . End-->
                          
            </div>
            
            	<!-- Actions -->
                    <div class="actions">
                    	<ul class="left">
                        	<li><a href="#">Select All</a></li>
                            <li><a href="#">Display all Messages</a></li>
                        </ul>
                        <ul class="right">
                        	<li><a href="#">Remove All</a></li>
                            <li><a href="#">Remove Selected</a></li>
                        </ul>
                    </div>
                    <div class="clear"></div>
                <!-- Actions . End -->
            
            
            <div style="padding:0 0 70px 0;"></div>
            
      	</div>
      <!-- Private Messages . End -->
     
	</div>
 
</div>

</div>

<?php

$TPL->LoadFooter();

?>
