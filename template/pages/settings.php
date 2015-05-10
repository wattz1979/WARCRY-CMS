<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//Set the title
$TPL->SetTitle('Account Settings');
//CSS
$TPL->AddCSS('template/style/page-account-settings.css');
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
    
   
      <div class="container_3 account_sub_header">
         <div class="grad">
       		<div class="page-title">Account Settings</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
      	 </div>
      </div>
      
      <!-- VOTE -->
      	<div class="vote-page">
      		
       		
            
            <div class="container_3 account-wide" align="center">
             
            		<ul class="account-settings">
                    	<li>
                        	<a href="<?php echo $config['BaseURL'], '/index.php?page=changepass'; ?>">
                        	Change password
                            <p>Keep your account secure.</p>
                            </a>
                        </li>
                        <li>
                        	<a href="<?php echo $config['BaseURL'], '/index.php?page=changemail'; ?>">
                            Change email
                            <p>Change you account email.</p>
                            </a>
                        </li>
                        <li>
                        	<a href="<?php echo $config['BaseURL'], '/index.php?page=changedname'; ?>">
                            Change display name
                            <p>Change your account name. This service costs coins.</p>
                           	</a>
                        </li>
                        <li>
                        	<a href="#">
                            Support ticket
                            <p>If you have a problem submit a ticket.</p>
                            </a>
                        </li>
                    </ul>
             
            </div>
            
            
      	</div>
      <!-- VOTE.End -->
        
    
     </div>
	</div>
 
 
</div>
 

</div>

<?php
	$TPL->LoadFooter();
?>