<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//Set the title
$TPL->SetTitle('Purchase Gold Coins - PayPal');
//Load page css
$TPL->AddCSS('template/style/page-buy-gcoins.css');
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
       		<div class="page-title"><p>Gold Coins</p></div>
            <div class="sub-active-page">PayPal</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=buycoins'; ?>">Back</a>
      	 </div>
      </div>
      
      <br/><br/>
      
      <!-- Buy Coins -->
                  
            <div class="container_3 account-wide" align="center">
            
            <div class="buy-coins">
            
              	<!--<div id="option-paypal-html" style="display:none;">
                	<p class="option-paypal"></p>
                </div>
              	<div id="option-moneybooks-html" style="display:none;">
                	<p class="option-moneybooks"></p>
                </div>
                <div id="option-onebip-html" style="display:none;">
                	<p class="option-onebip"></p>
                </div>-->
            
            	<!--<div class="container-left payment-method" align="left">
                	<div class="title">Select payment method</div>
	                <form action="#" method="get">
                    	<select styled="true" onchange="return changePaymentMethod(this);" id="payment-method-select">
		               	 	<option class="paypal" getHtmlFrom="#option-paypal-html" value="paypal"></option>
		                	<option class="moneybooks" disabled="disabled" getHtmlFrom="#option-moneybooks-html" value="moneybooks"></option>
                            <option class="onebip" getHtmlFrom="#option-onebip-html" value="onebip"></option>
	                	</select>
                    </form>
                </div>
                -->
                
                <div class="container-left payment-method" align="left">
                	<img src="./template/style/images/misc/paypal.png" style="margin:32px 0 0 35px" />
                </div>
                
                <!------------------------------------------------------------------->
                <!-- PAYMENT FORMS -------------------------------------------------->
                
                	  <form action="https://<?php echo $config['payments']['paypal']['url']; ?>/cgi-bin/webscr" method="post" target="paypal" id="paypal-form">     
					      <input type="hidden" name="cmd" value="_xclick">        
					      <input type="hidden" name="business" value="<?php echo $config['payments']['paypal']['email']; ?>">        
						  <input type="hidden" name="item_name" value="Warcry Gold Coins">        
						  <input type="hidden" name="item_number" id="paypal-product-id" value="<?php echo $CURUSER->get('id'); ?>WCC10">        
						  <input type="hidden" name="amount" value="1.00">            
						  <input type="hidden" name="quantity" value="10" />
						  <input type="hidden" name="currency_code" value="<?php echo $config['payments']['paypal']['currecy']; ?>">     
						  <input type="hidden" name="notify_url" value="<?php echo $config['payments']['paypal']['notify_url']; ?>" />
                          <input type="hidden" name="custom" value="<?php echo $CURUSER->get('username'); ?>" />
					  </form>
                      
                <!------------------------------------------------------------------->
                <!------------------------------------------------------------------->
                
                <!-- If paypal or where can be selected any number -->
                <div class="container-left coins-number" align="center">
                	<ul>
                    	<li id="onemore-a"><a href="javascript: void(0);" id="payment-increase-coins"></a></li>
                        <li><input id="selected-coins-input" type="text" value="10"/></li>
                        <li id="oneless-a"><a href="javascript: void(0);" id="payment-decrease-coins"></a></li>
                    </ul>
                </div>
                
                <div class="container-left purchase" align="left">
                	
                    <div class="d-cont coin-money-price">
                    
                    	<p>You will be charged <br/>via PayPal</p> 
                        <div>$<span id="payment-infoPane-price">10</span></div>
                        
                        
                    </div>
                    <p>You will be charged in USD. Please remember all refunds have to be approved.</p>
                    
                    <input type="submit" value="purchase" onclick="return submitPaymentForm();" />
                    
                </div>
                
                <!-- If paypal or where can be selected any number -->
                
                
                <div class="clear"></div>
              
            </div>
              
            </div>
      
      <!-- Buy Coins.End -->
    
     </div>
	</div>
 
</div>
 
</div>

<?php
	$TPL->AddFooterJs('template/js/page.buy.gcoins.js');
	$TPL->LoadFooter();
?>