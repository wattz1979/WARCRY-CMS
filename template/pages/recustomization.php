<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

$RealmId = $CURUSER->GetRealm();

//Set the title
$TPL->SetTitle('Character Recustomization');
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
		if ($error = $ERRORS->DoPrint('pStore_recustomization'))
		{
			echo $error, '<br><br>';
		}			
		if ($error = $ERRORS->successPrint('pStore_recustomization'))
		{
			echo $error, '<br><br>';
		}			
		unset($error);
		?>     
 
            <div class="container_3 account_sub_header">
                <div class="grad">
                    <div class="page-title">Re-Customization</div>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
                </div>
            </div>    
      
      <!-- FACTION CHANGE -->
      	<div class="faction-change">
      		
       		<div class="page-desc-holder">
                Character re-customization will allow you to change the looks of<br/>
				your character. (character rename included)<br/>
				This will cost you <font color="#aa893b"><b>5 gold</b></font> coins.
            </div>
            
            <div class="container_3 account-wide" align="center">
              <div style="padding:30px 0 30px 0;">
              	
                <form action="execute.php?take=recustomization" method="post">
                              
                <!-- Charcaters -->
                <div style="display:inline-block; padding:0 20px 0 0;">
                
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
               <!-- Charcaters.End -->
               
               <input type="submit" value="Change" />
               
               </form>
               
              </div>
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