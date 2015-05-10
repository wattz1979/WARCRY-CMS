<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//Predefine the realm id
$RealmId = $CURUSER->GetRealm();

//Set the title
$TPL->SetTitle('Boosts');
//Print the header
$TPL->LoadHeader();

$Boosts = new BoostsData();

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
                <li id="messages-ddm">
                    <a href="<?php echo $config['BaseURL']; ?>/index.php?page=pm">
                        <b>55</b> <i>Private Messages</i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
 
  	<div class="container_2 account" align="center">
     	<div class="cont-image">
			
            <?php
			if ($error = $ERRORS->DoPrint('purchase_boost'))
			{
				echo $error, '<br><br>';
			}			
			if ($error = $ERRORS->successPrint('purchase_boost'))
			{
				echo $error, '<br><br>';
			}			
			unset($error);
			?>
            
            <div class="container_3 account_sub_header">
                <div class="grad">
                    <div class="page-title">Boosts</div>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
                </div>
            </div>
          
            <div class="page-desc-holder">
                Boost auras applied to your account and are active on all <br/>of your charcaters.
                Some of the auras does not apply when you are in Battleground, Arena,<br/> Dungeon or Instance.
            </div>
          	
            <?php
				$ActiveBoosts = array();
				
				//Find the active boosts for this account/realm
				if ($RealmDb = $CORE->RealmDatabaseConnection($RealmId))
				{
					//locate the records for this account if any
					$res = $RealmDb->prepare("SELECT * FROM `player_boosts` WHERE `account_Id` = :acc AND `active` = '1' ORDER BY `unsetdate` ASC;");
					$res->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
					$res->execute();
					
					if ($res->rowCount() > 0)
					{
						while ($arr = $res->fetch())
						{
							//verify that this boost is really active
							$time = $CORE->getTime(true);
							
							if ($time->getTimestamp() > (int)$arr['unsetdate'])
							{
								//already expired
								continue;
							}
							unset($time);
							
							//push to the active boosts
							$ActiveBoosts[] = $arr;
						}
						unset($arr);
					}
					unset($res);
				}
				unset($RealmDb);
			?>
            
            <!-- Boosts -->  
            <div class="container_3 account-wide" align="center">
                <div class="boosts_page">
                
                    <!-- Purchase Aura -->
                    <div class="purchase_boost">
                        
                        <div class="top_info">
                            Please select the boost you need, then select the period of time you want this aura to be active and then select the currency you want to pay with. 
                            You cant purchase boost that is already active on your account.
                        </div>
                        
                        <ul class="select_boost">
                            
                            <?php
							
							//Loop through our boosts
							foreach ($Boosts->data as $BoostId => $BoostData)
							{
								$isActive = false;
								foreach ($ActiveBoosts as $key => $bb)
								{
									if ((int)$bb['boosts'] == $BoostId)
									{
										$isActive = true;
										break;
									}
								}
								
								echo '
								<li ', ($isActive ? 'class="disabled"' : ''), '>
									<a href="#" data-boost-id="', $BoostId, '">
										<div class="icon" style="background-image:url(', $BoostData['icon'], ');"></div>
										<div class="info">
											<h2>', $BoostData['name'], '</h2>
											<h3>', $BoostData['description'], '</h3>
										</div>
										<p>This boost is already active!</p>
									</a>
								</li>';
							}
							
							?>
                            
                            <div class="clear"></div>
                        </ul>

                        <form method="post" action="<?php echo $config['BaseURL']; ?>/execute.php?take=purchase_boost" id="boosts-complete-form">
                            <div class="select-currency select-period" id="select-duration" align="right">
                                <span>Select boost duration</span>
                                <label class="label_radio"><div></div><input type="radio" name="duration" value="<?php echo BOOST_DURATION_10; ?>" checked="checked" /><p class="dr"><b>10</b> Days</p></label>
                                <label class="label_radio"><div></div><input type="radio" name="duration" value="<?php echo BOOST_DURATION_15; ?>" /><p class="dr"><b>15</b> Days</p></label>
                                <label class="label_radio"><div></div><input type="radio" name="duration" value="<?php echo BOOST_DURATION_30; ?>" /><p class="dr"><b>30</b> Days</p></label>
                            </div>

                            <input type="submit" value="Purchase" class="purchase_btn" />
                            
                            <div class="select-currency" id="select-currency">
                                <span>Currency:</span>
                                <label class="label_radio">
                                	<div></div>
                                    <input type="radio" name="currency" value="<?php echo CURRENCY_SILVER; ?>" data-price-value="<?php echo $config['BOOSTS']['PRICEING'][BOOST_DURATION_10][CURRENCY_SILVER]; ?>" />
                                    <p id="sc"><b id="price"><?php echo $config['BOOSTS']['PRICEING'][BOOST_DURATION_10][CURRENCY_SILVER]; ?></b> Silver Coins</p>
                                </label>
                                <label class="label_radio">
                                	<div></div>
                                    <input type="radio" name="currency" value="<?php echo CURRENCY_GOLD; ?>" checked="checked" data-price-value="<?php echo $config['BOOSTS']['PRICEING'][BOOST_DURATION_10][CURRENCY_GOLD]; ?>" />
                                    <p id="gc"><b id="price"><?php echo $config['BOOSTS']['PRICEING'][BOOST_DURATION_10][CURRENCY_GOLD]; ?></b> Gold Coins</p>
                                </label>
                            </div>
                        
                            <input type="hidden" name="boost" value="0" id="selected-boost-id" />
                        </form>

                        <div class="clear"></div>
                        
                    </div>
                    <!-- Purchase Aura.End -->
                        
                    <div class="active_boosts">
                        <h1>Active boosts</h1>
                        <ul class="active_boosts">
                        	<?php
							//Loop through the active boosts
							foreach ($ActiveBoosts as $key => $BoostRecord)
							{
								//Get the boost details
								$BoostDetails = $Boosts->get((int)$BoostRecord['boosts']);
								//Get the time left in single measure
								$timeLeft = $CORE->singleMeasureTimeLeft((int)$BoostRecord['unsetdate']);
								
								echo '
								<li>
									<div class="icon" style="background-image:url(', $BoostDetails['icon'], ');"></div>
									<p>', $timeLeft, ' left</p>
								</li>';
								
								unset($timeLeft, $BoostDetails);
							}
							unset($key, $BoostRecord, $ActiveBoosts);
							?>
                        </ul>
                    </div>
                    <div class="clear"></div>
                                 
                </div>
            </div>
            <!-- Boosts.End -->
    
        </div>
	</div>

</div>

<?php
	unset($Boosts);
	
	//Add some javascripts to the loader
	$TPL->AddFooterJs('template/js/page.boosts.js');
	//Print the footer
	$TPL->LoadFooter();
?>