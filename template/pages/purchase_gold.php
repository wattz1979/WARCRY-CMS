<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

$RealmId = $CURUSER->GetRealm();

//Set the title
$TPL->SetTitle('Purchase In-Game Gold');
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
	if ($error = $ERRORS->DoPrint('pStore_gold'))
	{
		echo $error, '<br><br>';
	}			
	if ($error = $ERRORS->successPrint('pStore_gold'))
	{
		echo $error, '<br><br>';
	}	
	unset($error);		
	?>     
   
            <div class="container_3 account_sub_header">
                <div class="grad">
                    <div class="page-title">In-Game Gold</div>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
                </div>
            </div>    
      
      <!-- LEVEL UP -->
      	<div class="faction-change">
      		
       		<div class="page-desc-holder">
                
            </div>
            
            <div class="container_3 account-wide" align="center">

  			<form action="<?php echo $config['BaseURL']; ?>/execute.php?take=purchase_gold" method="post" id="gold-complete-form">
  
            	<!-- SELECTS (charcater and level options) -->
                <div style=" padding:20px 0 20px 0">
            
                <!-- Charcaters Select -->
   			    <div style="display:inline-block; vertical-align:top; margin:0 15px 0 0;">
                  	
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
                            <div style="display:inline-block;">
                                <select styled="true" id="character-select" name="character">
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
               <!-- Charcaters Select.End --> 
               
               	<!-- SELECT Levels -->
               		<div style="display:inline-block; vertical-align: top; top: 3px;">
               			<input type="text" maxlength="6" value="1000" name="amount" id="gold-amount" data-price="1" />
                        <div style="position: absolute; top: 0px; left: 0px; color: #6c6c6c; line-height: 34px; text-align: right; width: 300px; height: 34px; pointer-events: none;">
                        	<p style="color: #e5d6aa; font-size: 16px;">
                            	<span id="cost-amount" style="font-weight: bold;">1</span> Gold Coins
                            </p>
                        </div>
                    </div>
               	<!-- SELECT Levels.END -->
               
               <input style="top:3px; margin:0 0 0 15px;" type="submit" value="SEND" />
               </div>
               <!-- SELECTS (charcater and level options).END -->           

           </form>
                  
           </div>
            
      	</div>
      <!-- LEVEL UP.End -->
       
     </div>
	</div>
 
</div>

</div>

<?php

	//Add some javascripts to the loader
	$TPL->AddFooterJs('template/js/page.purchase.gold.js');
	//Print Footer
	$TPL->LoadFooter();

?>
