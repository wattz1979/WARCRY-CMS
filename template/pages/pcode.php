<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

$RealmId = $CURUSER->GetRealm();

//Set the title
$TPL->SetTitle('Promotion Codes');
//CSS
$TPL->AddCSS('template/style/promorion_codes.css');
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
            if ($error = $ERRORS->DoPrint('pcode'))
            {
                echo $error, '<br><br>';
            }			
            if ($error = $ERRORS->successPrint('pcode'))
            {
                echo $error, '<br><br>';
            }			
            unset($error);
            ?>
        
            <div class="container_3 account_sub_header">
                <div class="grad">
                    <div class="page-title">Promotion Codes</div>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
                </div>
            </div>    
      
      		<!-- FACTION CHANGE -->
      		<div class="faction-change">
      		
                <div class="page-desc-holder">
                    Each promo code is unique and can be used<br/> just one time per account.
                    You may find promo codes on our social <br/>
                    network pages or in the forums.
                </div>
            
                <div class="container_3 account-wide" align="center">
                  	<div class="promotion_codes">
                    	<div class="pcode-top-cont">
                            <form id="promo-code-form" method="post" action="<?php echo $config['BaseURL']; ?>/execute.php?take=redeem_pcode" onsubmit="return false;">
                                <h1 id="enter" style="display: none;">Press ENTER to redeem your reward!</h1>
                                <h1 id="invalid" style="display: none;">Invalid or expired code !</h1>
                                <input type="text" id="code" name="code" placeholder="Enter the Code here" style="text-align: center;" />
                                <input type="hidden" name="character" id="real-char-select" />
                                <p>Promo codes consist of 12 characters (6 digits and 6 letters).</p>
                            </form>
                        
                            <!-- ITEMS -->
                            <div class="reward_container" style="display:none;">
                                <div class="arrow"></div>
                                <!---->
                                
                                <div class="reward_loading">
                                    <p>Loading...</p>
                                    <div class="circle-loading">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                </div>
                                
                                <!-- COINS Reward SILVER -->
                                <div class="coins_reward" id="reward-type-silver" style="display: none;">
                                    <h1>The reward is</h1>
                                    <h2><span id="value" style="font-weight:bold;">25</span> Silver Coins</h2>
                                </div>
                                <!-- COINS Reward . End -->
                                
                                <!-- COINS Reward GOLD -->
                                <div class="coins_reward gold" id="reward-type-gold" style="display: none;">
                                    <h1>The reward is</h1>
                                    <h2><span id="value" style="font-weight:bold;">5</span> Gold Coins</h2>
                                </div>
                                <!-- COINS Reward . End -->
                                
                                <!-- Item Reward -->
                                <div class="item_reward" id="reward-type-item" style="display: none;">
                                    <div class="item_">
                                        <div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/large/inv_misc_questionmark.jpg); background-size: 100% !important;"></div>
                                        <h2 id="subclass" style="width:70%;">None</h2>
                                        <h3 id="name">None</h3>
                                    </div>
                                    <h1>The<br/> reward is</h1>
                                </div>
                               <!-- Item Reward . End -->
                           
                            <!-- -->
                            </div>
                            <!-- ITEMS . End -->
                            
                            <div class="clear"></div>
                        </div>
                    	
                        <div class="pcode-chat-select-cont" style="display: none;">
							<?php
                            
                            //load the characters module
                            $CORE->load_ServerModule('character');
                            //setup the characters class
                            $chars = new server_Character();
                            
                            //set the realm
                            if ($chars->setRealm($RealmId))
                            {
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
                                        </div>
                                        ';
                                        
                                        $selectOptions .= '<option value="'. $arr['name'] .'" getHtmlFrom="#character-option-'. $arr['guid'] .'"></option>';
                                        
                                        unset($ClassSimple);
                                    }
                                    unset($arr);
                                    
                                    echo '
                                    <div id="select-charcater-selected" style="display:none;">
                                        <p class="select-charcater-selected">Select character</p>
                                    </div>
                                    <div style="display:inline-block; margin: 0 10px 0 4px;">
                                    <select styled="true" id="character-select">
                                        <option selected="selected" disabled="disabled" getHtmlFrom="#select-charcater-selected"></option>
                                        ', $selectOptions, '
                                    </select>
                                    </div>';
                                    unset($selectOptions);
                                }
                                else
                                {
                                    echo '<p class="there-are-no-chars">There are no characters.</p>';
                                }
                                unset($res);
                            }
                            else
                            {
                                echo '<p class="there-are-no-chars">Error: Failed to load your characters.</p>';
                            }
                            
                            unset($chars);
                            ?>
                        </div>
                        
                  	</div>
                </div>
                
            </div>
            <!-- VOTE.End -->
   
     	</div>
	</div>
 
</div>

<?php

//Load the javascript
$TPL->AddFooterJs('template/js/page.promo.codes.js');
//Print the footer
$TPL->LoadFooter();

?>