<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

$RealmId = $CURUSER->GetRealm();

//load the characters module
$CORE->load_ServerModule('character');
//setup the characters class
$chars = new server_Character();
//set the realm
$chars->setRealm($RealmId);

//get the cooldown
$cooldown = $CURUSER->getCooldown('unstuck');

$cooldownTime = '15 minutes';

//Set the title
$TPL->SetTitle('Unstuck');
//CSS
$TPL->AddCSS('template/style/page-unstuck.css');
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
	if ($error = $ERRORS->DoPrint('unstuck'))
	{
		echo $error, '<br><br>';
				
		unset($error);
	}			
	if ($error = $ERRORS->successPrint('unstuck'))
	{
		echo $error, '<br><br>';
				
		unset($error);
	}			
	?>
      
      <div class="container_3 account_sub_header">
         <div class="grad">
       		<div class="page-title">Unstuck</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
      	 </div>
      </div>
      
      <!-- UNSTUCK -->
      	<div class="unstuck">
      		
       		<div class="page-desc-holder">
				 The unstuck tool will only work partly if your character is online. To revive your
character our unstuck function<br/> requires your character to be offline. Teleportation 
works both ways, online and offline.

            </div>
            
          <form method="post" action="execute.php?take=unstuck">
            
            <div class="container_3 account-wide" align="center">
                            
            	<!-- Charcaters -->
	                <div class="select-charcater-s" align="right">
	                	
                        <?php
						
						if ($res = $chars->getAccountCharacters())
						{
							$selectOptions = '';
							
							//loop the characters
							while ($arr = $res->fetch())
							{
								$ClassSimple = str_replace(' ', '', strtolower($chars->getClassString($arr['class'])));
								
								echo '
			                	<!-- Charcater ', $arr['guid'], ' -->
				                <div id="character-option-', $arr['guid'], '" style="display:none;">
				                	<div class="character-holder">
				                    	<div class="s-class-icon ', $ClassSimple, '" style="background-image:url(http://wow.zamimg.com/images/wow/icons/medium/class_', $ClassSimple, '.jpg);"></div>
				                        <p>', $arr['name'], '</p><span>Level ', $arr['level'], ' ', $chars->getRaceString($arr['race']), ' ', ($arr['gender'] == 0 ? 'Male' : 'Female'), '</span>
				                    </div>
				                </div>';
								
								$selectOptions .= '<option value="'. $arr['name'] .'" getHtmlFrom="#character-option-'. $arr['guid'] .'"></option>';
								
								unset($ClassSimple);
							}
							
							echo '
		            		<div id="select-charcater-selected" style="display:none;">
								<p class="select-charcater-selected">Select character</p>
							</div>
		            		<select styled="true" id="character-select" name="character">
								<option selected="selected" disabled="disabled" getHtmlFrom="#select-charcater-selected"></option>
		                		', $selectOptions, '
		               	 	</select>';
						}
						else
						{
							echo '<p class="there-are-no-chars">There are no characters.</p>';
						}
						unset($selectOptions);
						unset($res);
						
						?>
                        
	               </div>
               <!-- Charcaters.End -->
 
 		       <!-- Cooldown Icon -->
			  		<div class="cooldown-ico">
						<div class="ust-cooldown" style="display:block;">
              			
                        <script>
							function startCooldownTimer(element, percentElement, totalCooldown, leftCooldown)
							{
								var cont = $(element);
								var contPercent = $(percentElement);
								var leftCooldown = parseInt(leftCooldown);
								var totalCooldown = parseInt(totalCooldown);
								
								var calculatePercent = function(num_amount, num_total)
								{
									var num_amount = parseInt(num_amount);
									var num_total = parseInt(num_total);
									
									var count1 = num_amount / num_total;
									var count2 = count1 * 100;
									var count = Math.round(count2);
									
									return count;
								};
								
								//update each second
								var $interval = setInterval(function()
								{
									//update the cooldown
									leftCooldown = parseInt(leftCooldown) - 1;
									
									var seconds = leftCooldown % 60;
									var minutes = Math.floor((leftCooldown / 60) % 60);
									var hours = Math.floor((leftCooldown / (60*60)) % 24);
									var days = Math.floor((leftCooldown / (24*60*60)) % 30);
									
									//update the cooldown text
									cont.html('(' + minutes + 'm and ' + seconds + 's)');
									
									//calculate the percentages
									var percent = calculatePercent(leftCooldown, totalCooldown);
									//update
									contPercent.css('height', percent + '%');
									
									//break the interval
									if (minutes == 0 && seconds == 0)
									{
										clearInterval($interval);
										cont.html('');
									}
									
								}, 1000);
							}
						</script>
                        
              			<?php
               	   
						   	if ($cooldown = $CORE->convertCooldown($cooldown))
							{
								$totalCooldown = strtotime($cooldownTime, 0);
								$leftCooldown = $cooldown['int'];
								$percentCooldown = $CORE->percent($leftCooldown, $totalCooldown);
								
							   	echo '<span id="unstuck-timer-percent" style="height:', $percentCooldown, '%"></span>
				                      <p id="unstuck-timer">(', ($cooldown['minutes'] > 0 ? $cooldown['minutes'] . 'm and ' . $cooldown['seconds'] . 's' : $cooldown['seconds'] . 's'), ')</p>
									  <script>
									  		startCooldownTimer(\'#unstuck-timer\', \'#unstuck-timer-percent\', ', $totalCooldown,', ', $leftCooldown, ');
									  </script>';
							}
							else
							{
							   	echo '<span style="height:0px"></span>
				                      <p></p>';
							}
				               
			   			?>
               
		       			</div>
			   		</div>
		       <!-- Cooldown Icon.End -->
			             
               <!-- Unstuck submit -->
               		<div class="ust-submit" align="left">
                    	<input type="submit" value="unstuck" />
                        <p>
                        Your character will be revived <br/>and teleported to its home.
                        </p>
                    </div>
               <!-- Unstuck submit.End -->
               
               <div class="clear"></div>
               
                <div class="description-small">
            		The <b>Unstuck</b> feature is a free service, but it has 10 minutes cooldown!
            	</div>
             
            </div>
            
          </form>
          
          	
            
      	</div>
      <!-- UNSTUCK.End -->
    
     </div>
	</div>
 
</div>

</div>

<?php
	unset($RealmId);
	unset($cooldownTime);
	unset($cooldown);
	unset($chars);

	$TPL->LoadFooter();

?>
