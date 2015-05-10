<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//Set the title
$TPL->SetTitle('Vote for us');
//CSS
$TPL->AddCSS('template/style/page-vote.css');
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
    
    <?php
	if ($error = $ERRORS->DoPrint('vote'))
	{
		echo $error, '<br><br>';
	}			
	if ($error = $ERRORS->successPrint('vote'))
	{
		echo $error, '<br><br>';
	}			
	unset($error);
	?>
   
      <div class="container_3 account_sub_header">
         <div class="grad">
       		<div class="page-title">Vote</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
      	 </div>
      </div>
      
      <!-- VOTE -->
      	<div class="vote-page">
      		
       		<div class="page-desc-holder">
				With every vote you will recieve <font color="#808080"><b>2 silver</b></font> coins. <br/>
				You can spend your coins for amazing stuff on our website.
            </div>
            
            <div class="container_3 account-wide" align="center">
             
            		<ul class="vote-sites-cont">
                    	
                        <?php
							
							$VoteSites = new VoteSitesData();
							
							foreach ($VoteSites->data as $id => $data)
							{								
								$cooldown = $CURUSER->getCooldown('votingsite'.$id);
								
								//if the site is availible for voting
								if (time() > $cooldown)
								{
									echo '
		                            <li>
		                        	  <a href="', $config['BaseURL'], '/execute.php?take=vote&site=', $id, '" onclick="window.open(\'', $data['url'], '\', \'_newtab\'); return true;">
		                            	<div class="vote-site-image" style="background-image:url(\'', $data['img'], '\')"></div>
		                                <p>You can vote now!</p>
		                              </a>
		                        	</li>';
								}
								else
								{
									//convert the cooldown to minutes and stuff
									$cooldownArr = $CORE->convertCooldown($cooldown);
									
									echo '
		                            <li class="not-active">
		                        	  <a href="', $data['url'], '">
		                            	<div class="vote-site-image" style="background-image:url(\'', $data['img'], '\')"></div>
		                                <p>';
										
										if ($cooldownArr['hours'] > 0)
										{
											echo $cooldownArr['hours'], ' hours until vote!';
										}
										else if ($cooldownArr['minutes'] > 0)
										{
											echo $cooldownArr['minutes'], ' minutes until vote!';
										}
										else if ($cooldownArr['seconds'] > 0)
										{
											echo $cooldownArr['seconds'], ' seconds until vote!';
										}
										
										echo ' 
										</p>
		                              </a>
		                        	</li>';
									
									unset($cooldownArr);
								}
								unset($cooldown);
							}
							
							unset($VoteSites, $data, $id);
						?>
                                                    
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
