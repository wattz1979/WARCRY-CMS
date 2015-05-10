<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//Set the title
$TPL->SetTitle('Change Password');
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
	if ($error = $ERRORS->DoPrint('changepass'))
	{
		echo $error, '<br><br>';
	}			
	if ($error = $ERRORS->successPrint('changepass'))
	{
		echo $error, '<br><br>';
	}			
	unset($error);
	?>
  
      <div class="container_3 account_sub_header">
         <div class="grad">
       		<div class="page-title">Change Password</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
      	 </div>
      </div>
      
      <!-- Store Activity -->
      	<div class="store-activity">
        
       		<div class="page-desc-holder">
				Your new password will take place immediately.
            </div>
            
            <div class="container_3 account-wide" align="center">
            	
                <p style="padding: 20px;">
                <form action="<?php echo $config['BaseURL'], '/execute.php?take=changepass'; ?>" method="post">
                	
                    <div class="row">
                   	<label for="password">Password: </label>
                    <input type="password" name="password" />
                    </div>
                    
                    <div class="row">
					<label for="newPassword">New password: </label>
                    <input type="password" name="newPassword" />
                    </div>
                    
                    <div class="row">
                    <label for="newPassword2">Confirm new password: </label>
                    <input type="password" name="newPassword2" />
                    </div>

<br/>
                    <input style="left:-18px;" type="submit" value="Change" />
                    
                    <br/>
                    <br/>
                    <br/>
                    
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