<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//Set the title
$TPL->SetTitle('Gold Coins');
//Load page css
$TPL->AddCSS('template/style/buy-coins.css');
//Print the header
$TPL->LoadHeader();

?>
<div class="content_holder">

<div class="sub-page-title">
	<div id="title"><h1>Account Panel<p></p><span></span></h1></div>
  
    <div class="quick-menu">
    	<a class="arrow" href="#"></a>
        <ul class="dropdown-qmenu">
        	<li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=store">Store</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=teleporter">Teleporter</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=vote">Vote</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=pstore">Premium Store</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=unstuck">Unstuck</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=settings">Settings & Options</a></li>
            <!--<li id="messages-ddm">
            	<a href="<?php echo $config['BaseURL']; ?>/index.php?page=pm">
                	<b>55</b> <i>Private Messages</i>
                </a>
            </li>-->
        </ul>
    </div>
</div>
 
  	<div class="container_2 account" align="center">
     <div class="cont-image">
    
      <div class="container_3 account_sub_header">
         <div class="grad">
       		<div class="page-title">Gold Coins</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
      	 </div>
      </div>
      
      <!-- Buy Coins -->
      
        	<div class="page-desc-holder">
            	<b>You may select a method to purchase coins via an option below.</b><br/><br/>
            </div>
            
            <div class="container_3 account-wide" align="center">
            
            <div class="buy-coins">
            	
                <ul class="payment_methods">
                	<li id="paypal"><a href="<?php echo $config['BaseURL']; ?>/index.php?page=buy-gcoins"><img src="./template/style/images/misc/paypal.png" /></a></li>
                    <li>OR</li>
                    <li id="paymentwall"><a href="<?php echo $config['BaseURL']; ?>/index.php?page=purchase-gcoins"> <img src="./template/style/images/misc/paymentwall.png" /></a></li>
                </ul>
                
                <p>Please take a moment before purchasing to view our <a href="<?php echo $config['BaseURL']; ?>/index.php?page=terms-of-use">Terms of Use</a> and <a href="<?php echo $config['BaseURL']; ?>/index.php?page=rules">Rules & Regulations</a></p>
                         
            </div>
              
            </div>
      
      <!-- Buy Coins.End -->
    
     </div>
	</div>
 
</div>
 
</div>

<?php

$TPL->LoadFooter();

?>
