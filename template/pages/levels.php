<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

$RealmId = $CURUSER->GetRealm();

//Set the title
$TPL->SetTitle('Character Level Up');
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
	if ($error = $ERRORS->DoPrint('pStore_levels'))
	{
		echo $error, '<br><br>';
	}			
	if ($error = $ERRORS->successPrint('pStore_levels'))
	{
		echo $error, '<br><br>';
	}	
	unset($error);		
	?>     
   
            <div class="container_3 account_sub_header">
                <div class="grad">
                    <div class="page-title">Levels</div>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
                </div>
            </div>    
      
      <!-- LEVEL UP -->
      	<div class="faction-change">
      		
       		<div class="page-desc-holder">
                
            </div>
            
            <div class="container_3 account-wide" align="center">

  			<form action="execute.php?take=level" method="post">
  
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
               		<div style="display:inline-block; vertical-align:top;">
                    
               			<div id="choose-level" style="display:none;"><p class="choose-level">Choose level</p></div>
                        
	               		<!-- Level 60 / 2k Gold / 4 x 16 slot bags -->
	                    <div id="levels-option-one" style="display:none;">
                        	<div class="level-option">
                            	<b>Level 60</b> <i>(4 Gold Coins)</i>
                                <span>Level 60, 2k Gold and 4x 16 slot bags</span>
                            </div>
	                    </div>
                        
	                    <!-- Level 70 / 3k Gold / 4 x 18 slot bags -->
	                    <div id="levels-option-two" style="display:none;">
                        	<div class="level-option">
                            	<b>Level 70</b> <i>(6 Gold Coins)</i>
                                <span>Level 70, 3k Gold and 4x 18 slot bags</span>
                            </div>
	                    </div>
                        
	                    <!-- Level 80 / 4k Gold / 4 x 20 slot bags -->
	                    <div id="levels-option-three" style="display:none;">
                        	<div class="level-option">
                            	<b>Level 80</b> <i>(8 Gold Coins)</i>
                                <span>Level 80, 5k Gold and 4x 20 slot bags<span>
                            </div>
	                    </div>
                    
	               		<select styled="true" id="levels-select" name="levels">
                        	<option selected="selected" disabled="disabled" value="null" getHtmlFrom="#choose-level"></option>
	                    	<option value="1" getHtmlFrom="#levels-option-one"></option>
	                        <option value="2" getHtmlFrom="#levels-option-two"></option>
	                        <option value="3" getHtmlFrom="#levels-option-three"></option>
	                    </select>
                    </div>
               <!-- SELECT Levels.END -->
               
               <input style="top:3px; margin:0 0 0 15px;" type="submit" value="DING" />
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

$TPL->LoadFooter();

?>
