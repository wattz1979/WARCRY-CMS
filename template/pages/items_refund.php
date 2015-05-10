<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//load the characters handling class
$CORE->load_ServerModule('character');
$CORE->load_CoreModule('item.refund.system');

//assume the realm is 1 (for now)
$RealmId = $CURUSER->GetRealm();

//construct the characters handler
$chars = new server_Character();
$chars->setRealm($RealmId);

//Set the title
$TPL->SetTitle('Item Refunding');
//Add header javascript
$TPL->AddHeaderJs($config['WoWDB_JS'], true);
//CSS
$TPL->AddCSS('template/style/page-refund.css');
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
            if ($success = $ERRORS->successPrint('refund_item'))
            {
                echo $success, '<br><br>';
            }			
            unset($success);
			?>
            
            <div class="container_3 account_sub_header">
                <div class="grad">
                    <div class="page-title">Refund</div>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
                </div>
            </div>
      
      		<!-- VOTE -->
      		<div class="vote-page">
      		
                <div class="page-desc-holder">
                    Refunding an item purchased from our store gives you back the full amount you paid.
                    <br/><br/>
                    The system requires your character to be online and the item must be in your character's bags.<br/>
                    You are allowed to use the Refund System 2 times a week.
                </div>
            
				<?php
					//Array storage for character data (less queries)
					$characterData = array();
					
                    //Get the refundables
                    $res = ItemRefundSystem::GetRefundables();
                ?>
            	
                <div class="container_3 account-wide" align="center">
                    <div class="items">
                    
                    <?php
					if ($res)
					{
						while ($arr = $res->fetch())
						{
							$GUID = $arr['character'];
							
							//Get the character data for this refund
							if (!isset($characterData[$GUID]))
							{
								$columns = array('name', 'class', 'level', 'race', 'gender');
								
								$characterData[$GUID] = $chars->getCharacterData($GUID, false, $columns);
								
								unset($columns);
							}
							
                    		echo '
							<ul class="item-row">
								<li class="item-icon">
									<a href="', $config['WoWDB_URL'], '/?item=', $arr['entry'], '" target="_newtab" rel="item=', $arr['entry'], '">
										<img style="background-image:url(http://wow.zamimg.com/images/wow/icons/large/inv_misc_questionmark.jpg)"/>
									</a>
								</li>
								<li class="item-info"><h2>Loading</h2><h5>', $arr['price'], ' ', ($arr['currency'] == CA_COIN_TYPE_SILVER ? 'Silver' : 'Gold'), ' Coins</h5></li>
								<li class="refund-btn"><a href="#" class="refund-btn" onclick="return RefundItem(', $arr['id'], ');">Refund</a></li>';
								
								if ($characterData[$GUID])
								{
									$ClassSimple = str_replace(' ', '', strtolower($chars->getClassString($characterData[$GUID]['class'])));
									
									echo '
									<li class="character">
										<div class="character-holder">
											<div class="s-class-icon ', $ClassSimple, '" style="background-image:url(http://wow.zamimg.com/images/wow/icons/medium/class_', $ClassSimple, '.jpg);"></div>
											<p>', $characterData[$GUID]['name'], '</p><span>Level ', $characterData[$GUID]['level'], ' ', $chars->getRaceString($characterData[$GUID]['race']), ' ', ($characterData[$GUID]['gender'] == 0 ? 'Male' : 'Female'), '</span>
										</div>
									</li>';
									
									unset($ClassSimple);
								}
								else
								{
									//Character not found
									echo '
									<li class="character">
										<div class="character-holder">
											<div class="s-class-icon" style="background-image:url(http://wow.zamimg.com/images/wow/icons/large/inv_misc_questionmark.jpg);"></div>
											<p>Unknown</p><span>Character not found</span>
										</div>
									</li>';
								}
								
								echo '
								<li class="sent-to"><p>Sent to</p></li>
							</ul>';
						}
						unset($arr, $GUID);
					}
					else
					{
						echo '<p style="font-size: 16px; padding-top: 10px;">You don\'t have any refundable items for this week.</p>';
					}
					?>
                           
                    </div>
                </div>
            
            	<?php
					unset($characterData, $res);
                ?>
                
      		</div>
      		<!-- VOTE.End -->
    
		</div>
	</div>
 
</div>

</div>

<?php
	
	unset($chars, $RealmId);
	
	//Add some javascripts to the loader
	$TPL->AddFooterJs('template/js/page.items.refund.js');
	//Print the footer
	$TPL->LoadFooter();

?>
