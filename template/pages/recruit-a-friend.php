<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//load the characters module
$CORE->load_CoreModule('raf');
//setup the raf class
$raf = new RAF();

//Set the title
$TPL->SetTitle('Recruit a Friend');
//CSS
$TPL->AddCSS('template/style/page-recruit-a-friend.css');
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
	if ($error = $ERRORS->DoPrint('raf'))
	{
		echo $error, '<br><br>';
	}			
	if ($error = $ERRORS->successPrint('raf'))
	{
		echo $error, '<br><br>';
	}			
	unset($error);
	?>
   
      <div class="container_3 account_sub_header">
         <div class="grad">
       		<div class="page-title">Recruit a Friend</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
      	 </div>
      </div>
      
      <!-- VOTE -->
      	<div class="vote-page">
      		
       		<div class="page-desc-holder">
				By recruiting friends you will benefit greatly, but before that your friends must become <br/>
                eligible for the program.To become eligible your referrals must have atleast one charcater level 60 or level 80 <br/>
                for Death Knight. <br/>
                For each five votes by your friend, you will receive 1 Silver coin.<br/>
                If your friend  purchases 50 Gold coins, you will receive 5 Gold coins as reward.<br/>
            </div>
            
            <div class="container_3 account-wide" align="center">
             
             	<br/><br/>
                
             	<!-- RECRUIT Link -->
                <div class="recruit-link-holder">
                	<h2>Your referal link</h2>
                	<div class="recruit-link">
						<?php
                            echo '<div class="email-link">
								<input type="text" disabled="disabled" value="', $config['BaseURL'], '/index.php?page=register&raf=', $raf->GetCuruserHash(), '" id="raf-hash" />
							</div>';
                        ?>
                        <a href="javascript: void(0);" id="raf-hash-btn"></a>
                	</div>
                </div>
                
                <!-- RECRUITED -->
                
                <div class="recruited">
                	    
                        <!-- ACTIVE Referals -->              
                		<ul class="active-recruited-members">
                        
                        	<li class="arm-title"><h2>Active referrals</h2></li>
							<li class="tab-header"><h3>Display Name</h3><h4>Registration Date</h4><p>Completion Date</p><span>Status</span></li>
                            
                            <?php
							if ($res = $raf->GetActiveLinks($CURUSER->get('id')))
							{
								while ($arr = $res->fetch())
								{
									//get the account info
									$res2 = $DB->prepare("SELECT `displayName` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
									$res2->bindParam(':acc', $arr['account'], PDO::PARAM_INT);
									$res2->execute();
									
									if ($res2->rowCount() > 0)
									{
										$row = $res2->fetch();
										$displayName = $row['displayName'];
										unset($row);
									}
									else
									{
										$displayName = 'Unknown';
									}
									unset($res2);
									
									echo '<li><h3>', $displayName, '</h3><h4>', $arr['date'], '</h4><p>', $arr['cDate'], '</p><span>Active</span></li>'; //<li><h3>Sino5</h3><p>00:00:00, 00.00.0000</p><span>Active</span></li>
									
									unset($displayName);
								}
								unset($arr);
							}
							else
							{
								echo '<li><strong><p class="raf-no-records">There are no records.</p></strong></li>';
							}
							unset($res);
							?>

                    	</ul>
                        
                        <!-- PENDING Referals -->              
                		<ul class="active-recruited-members pending-ref">
                        
                        	<li class="arm-title"><h2>Pending referrals</h2></li>
							<li class="tab-header"><h3>Display Name</h3><h4>Registration Date</h4><p>Character level</p></li>
                            
                            <?php
							if ($res = $raf->GetPendingLinks($CURUSER->get('id')))
							{
								while ($arr = $res->fetch())
								{
									//status text
									$statusText = $arr['statusText'] != '' ? $arr['statusText'] : 'Waiting for update';
									//get the account info
									$res2 = $DB->prepare("SELECT `displayName` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
									$res2->bindParam(':acc', $arr['account'], PDO::PARAM_INT);
									$res2->execute();
									
									if ($res2->rowCount() > 0)
									{
										$row = $res2->fetch();
										$displayName = $row['displayName'];
										unset($row);
									}
									else
									{
										$displayName = 'Unknown';
									}
									unset($res2);
									
                            		echo '<li><h3>', $displayName, '</h3><h4>', $arr['date'], '</h4><p>', $statusText, '</li>'; //<b>Rasputin</b>  Druid  Level 23 </p>
									
									unset($displayName);
								}
								unset($arr);
							}
							else
							{
								echo '<li><strong><p class="raf-no-records">There are no records.</p></strong></li>';
							}
							unset($res);
							?>
                            
                    	</ul>
                        
                        <div class="referal-pending-info">
                        	
                        </div>
                                        
                </div>
             
            </div>
            
      	</div>
      <!-- VOTE.End -->
        
     </div>
	</div>
 
</div>
 
</div>

<script type="text/javascript" src="template/js/ZeroClipboard.js"></script>
<script type="text/javascript">
	$(function()
	{
		//Create a new clipboard client
		var clip = new ZeroClipboard.Client();

		//Glue the clipboard client to the last td in each row
		clip.glue($('#raf-hash-btn')[0]);

		//Grab the text from the parent row of the icon
		var txt = $('#raf-hash').val();
		clip.setText(txt);

		//Add mouseover event
		clip.addEventListener('mouseover', function()
		{
			$('#raf-hash-btn').addClass('mouseover');
		});
		//add mouseout event
		clip.addEventListener('mouseout', function()
		{
			$('#raf-hash-btn').removeClass('mouseover');
		});
	});                               
</script>

<?php
	unset($raf);

	$TPL->LoadFooter();
?>