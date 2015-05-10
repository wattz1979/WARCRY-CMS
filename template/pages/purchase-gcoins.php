<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//Set the title
$TPL->SetTitle('Purchase Gold Coins - Paymentwall');
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
            <div class="sub-active-page">Paymentwall</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=buycoins'; ?>">Back</a>
      	 </div>
      </div>
      
      <!-- Purchase Gold Coins -->
      	<div class="faction-change">
      		
       		<div class="page-desc-holder">
                
            </div>
            
            <div class="container_3 account-wide" align="center" style="min-height:377px;">
				<iframe style="min-height:377px;" src="http://wallapi.com/api/ps/?key=7f9c84553141a0ed80cd8d7b0c5c6bda&uid=<?php echo $CURUSER->get('id'); ?>&widget=p1_2" width="843" frameborder="0"></iframe>
           </div>
            
      	</div>
      <!-- Purchase Gold Coins.End -->
       
     </div>
	</div>
 
</div>

</div>

<?php
	$TPL->LoadFooter();
?>