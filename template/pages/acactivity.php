<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//Page under construction
$TPL->UnderConstruction('Account Activity');

//Set the title
$TPL->SetTitle('Account Activity');
//CSS
$TPL->AddCSS('template/style/page-activity-all.css');
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
       		<div class="page-title">Account activity</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
      	 </div>
      </div>
      
      <!-- Account Activity -->
      	<div class="account-activity">   
      		
       		<div class="page-desc-holder">
  		Etiam mollis ullamcorper est, sed fermentum turpis congue nec. <br/>Donec laoreet lorem vitae nulla tempor ut volutpat libero tincidunt. Duis augue<br/> nisi, aliquet id faucibus sit amet, fermentum id ante.
            </div>       
            
            <div class="container_3 account-wide" align="center">
             
            		<ul class="activity-list">
                
	                	<!-- Just a row -->
		                <li>
			                <p id="r-title"><i>Teleported charcater</i> <b>Darkness</b> <i>to Icecrown</i></p>
			                <p id="r-info"></p>
                            <p id="ar-date">00.00.0000, 00:00:00</p>
		                </li>
                        
                        <!-- Just a row -->
		                <li>
			                <p id="r-title"><i>Changed display name to</i> <b>EvilSystem</b></p>
			                <p id="r-info"></p>
                            <p id="ar-date">00.00.0000, 00:00:00</p>
		                </li>
                        
                        <!-- Just a row -->
		                <li>
			                <p id="r-title"><i>Chnaged account password</i></p>
			                <p id="r-info"></p>
                            <p id="ar-date">00.00.0000, 00:00:00</p>
		                </li>
                        
                        <!-- Just a row -->
		                <li>
			                <p id="r-title"><i>Voted on</i> <b>XtreemTop100</b></p>
			                <p id="r-info"></p>
                            <p id="ar-date">00.00.0000, 00:00:00</p>
		                </li>
                        
                        <!-- Just a row -->
		                <li>
			                <p id="r-title"><i>Recruited Friend, with account name</i> <b>gosho</b></p>
			                <p id="r-info"></p>
                            <p id="ar-date">00.00.0000, 00:00:00</p>
		                </li>
                        
                        <!-- Just a row -->
		                <li>
			                <p id="r-title"><i>Submited bug report in section</i> <b>Spells</b></p>
			                <p id="r-info"></p>
                            <p id="ar-date">00.00.0000, 00:00:00</p>
		                </li>
                        
                        <!-- Just a row -->
		                <li>
			                <p id="r-title"><i>Email have been changed to</i> <b>evilsystem@duloclan.com</b></p>
			                <p id="r-info"></p>
                            <p id="ar-date">00.00.0000, 00:00:00</p>
		                </li>
                        
                        <!-- Just a row -->
		                <li>
			                <p id="r-title"><i>Uploaded new avatar</i></p>
			                <p id="r-info"></p>
                            <p id="ar-date">00.00.0000, 00:00:00</p>
		                </li>
                        
                        <!-- Just a row -->
		                <li>
			                <p id="r-title"><i>Uploaded new avatar</i></p>
			                <p id="r-info"></p>
                            <p id="ar-date">00.00.0000, 00:00:00</p>
		                </li>
                        
                        <!-- Just a row -->
		                <li>
			                <p id="r-title"><i>Teleported charcater</i> <b>Evil</b> <i>to Stormwind City</i></p>
			                <p id="r-info"></p>
                            <p id="ar-date">00.00.0000, 00:00:00</p>
		                </li>
                        
            		</ul>
             
            </div>
            
            <!-- Pagination -->
	            <div class="d-cont wide pagination-holder">
                	<ul class="pagination" id="store-pagination">
                    
						<li id="pagination-nav-first"><a href="#">First</a></li>
	                    <li id="pagination-nav-prev"><a href="#">Previous</a></li>
	                        
	                    <li id="pages"><p>|&nbsp;&nbsp;</p>0-0 of 0<p>&nbsp;&nbsp;|</p></li>
	                        
					  	<li id="pagination-nav-next"><a href="#">Next</a></li>
	                   	<li id="pagination-nav-last"><a href="#">Last</a></li>
						                    
                    </ul>
                    <div class="clear"></div>
	            </div>
            
      	</div>
      <!-- Account Activity.End -->
    
     </div>
	</div>
 
</div>
 
</div>

<?php
	$TPL->LoadFooter();
?>