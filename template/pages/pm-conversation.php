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
		
        <!-- MESSAGE ROW - Conversation -->
            <div class="container_3 account-wide pm-container conversation" align="center">
            	<!-- Header Row -->
                	<ul class="message-row">
                        <li class="msg-title"><p>Curabitur fermentum blandit velit</p></li>
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
                <!-- Header . End-->
                
                <!-- TEXT Container -->
                	<div class="message-text">
                    
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ullamcorper est nec tellus rhoncus cursus. Morbi sit amet metus ipsum,
                    a interdum turpis. Vestibulum vestibulum tincidunt diam, ultrices vulputate massa fringilla vitae. Proin eu nisl nulla, in ornare urna.
                    Aenean ut nisi lacus, sit amet sodales leo. Integer eleifend tortor eu elit iaculis ac blandit enim luctus. Aliquam erat volutpat. 
                    <br/><br/>Curabitur fermentum blandit velit, pretium laoreet arcu eleifend vel. Morbi blandit nisl sed eros condimentum gravida. Maecenas 
                    bibendum metus non arcu ultricies et luctus diam rutrum. Suspendisse luctus accumsan elit, eget sollicitudin metus luctus id. Vestibulum 
                    ut sapien pellentesque est sodales ultrices accumsan ac nisi. Nunc magna metus, fringilla at malesuada a, congue eget quam. Vestibulum 
                    luctus neque in dolor molestie suscipit. Nulla a nibh eget libero congue commodo sed in turpis. Cum sociis natoque penatibus et 
                    magnis dis parturient montes, nascetur ridiculus mus. Mauris eleifend, quam vitae porttitor vulputate, odio libero interdum eros, 
                    et interdum neque risus vel sem. <br/><br/>
                    Proin turpis nisl, vestibulum at luctus et, luctus condimentum mi. Aliquam eu augue at risus porta ullamcorper. Suspendisse nec dolor diam, 
                    vitae fringilla neque. <br/><br/>Integer cursus aliquet ipsum non dignissim. 
                    
                	</div>
                <!-- TEXT Container -->
            </div>
		<!-- MESSAGE ROW - Conversation . End -->
        
        <!-- MESSAGE ROW - Conversation -->
            <div class="container_3 account-wide pm-container conv-answer" align="center">
            	<!-- Header Row -->
                	<ul class="message-row">
                        <li class="answer">
                        	<p>Answer</p> <span>(Sent by me)</span>
                        </li>
                    </ul>
                <!-- Header . End-->
                
                <!-- TEXT Container -->
                	<div class="message-text">
                    
                    Nunc magna metus, fringilla at malesuada a, congue eget quam. Vestibulum 
                    luctus neque in dolor molestie suscipit. Nulla a nibh eget libero congue commodo sed in turpis. Cum sociis natoque penatibus et 
                    magnis dis parturient montes, nascetur ridiculus mus. <br/><br/>Integer cursus aliquet ipsum non dignissim. 
                    
                	</div>
                <!-- TEXT Container -->
            </div>
		<!-- MESSAGE ROW - Conversation . End -->
            
            <div class="pmc-reply">
            	<a class="reply-btn" href="#">Reply</a>
            </div>
            
            
            <div style="padding:0 0 70px 0;"></div>
      	</div>
      <!-- Private Messages . End -->
     
	</div>
 
</div>

</div>

<?php

$TPL->LoadFooter();

?>
