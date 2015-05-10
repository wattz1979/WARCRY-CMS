<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//Set the title
$TPL->SetTitle('Change Display Name');
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
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=buycoins">Buy Coins</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=vote">Vote</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=pstore">Premium Store</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=unstuck">Unstuck</a></li>
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

    <?php
	if ($error = $ERRORS->DoPrint('changedname'))
	{
		echo $error, '<br><br>';
	}			
	if ($error = $ERRORS->successPrint('changedname'))
	{
		echo $error, '<br><br>';
	}			
	unset($error);
	?>
  
      <div class="container_3 account_sub_header">
         <div class="grad">
       		<div class="page-title">Change Display Name</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
      	 </div>
      </div>
      
      <!-- Store Activity -->
      	<div class="store-activity">
        
       		<div class="page-desc-holder">
				The display name is used for your publicity, in order to keep your account secure your account login and display name must be significantly different.
            </div>
            
            <div class="container_3 account-wide" align="center">
            	
                <p style="padding: 20px;">
                <form action="<?php echo $config['BaseURL'], '/execute.php?take=changedname'; ?>" method="post">
                	
                    <div class="row row-fix">
                   	<label for="displayName">Choose your new display name:</label>
                    <input type="text" name="displayName" />
                    </div>
                    
                    	<div class="select-currency">
                        	<span>Currency:</span>
                        	<label class="label_radio"><div></div><input type="radio" name="currency" value="<?php echo CURRENCY_SILVER; ?>"/><p id="sc"><b>100</b> Silver Coins</p></label>
                            <label class="label_radio"><div></div><input type="radio" name="currency" value="<?php echo CURRENCY_GOLD; ?>" checked="checked"/><p id="gc"><b>10</b> Gold Coins</p></label>
                        </div>
                    
                    <br/>
                    <input style="left:10px;" type="submit" value="Change" /><br /><br /><br />
                    
                </form>
                </p>
                            
            </div>
                        
      	</div>
      <!-- Store Activity.End -->
        
     </div>
	</div>
 
</div>

</div>

<?php

$TPL->LoadFooter();

?>