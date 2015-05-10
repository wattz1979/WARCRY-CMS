<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

$RealmId = $CURUSER->GetRealm();

$RealmSource = 'old';

$filter = (isset($_GET['filter']) ? (int)$_GET['filter'] : 0);

//define items per page
$perPage = 5;

//Set the title
$TPL->SetTitle('Armor Sets');
//Add header javascript
$TPL->AddHeaderJs($config['WoWDB_JS'], true);
//CSS
$TPL->AddCSS('template/style/page-armor-sets.css');
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
			if ($error = $ERRORS->DoPrint('pStore_armorsets'))
			{
				echo $error, '<br><br>';
			}			
			if ($error = $ERRORS->successPrint('pStore_armorsets'))
			{
				echo $error, '<br><br>';
			}			
			unset($error);
			?>     
   
            <div class="container_3 account_sub_header">
                <div class="grad">
                    <div class="page-title">Armor Sets</div>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
                </div>
            </div>    
              
          	<!-- ARMOR SETS -->
          	<div class="armor-sets">
                
           		<!-- Top Bar (Charcater & Gear filter) -->
                <div class="container_3 account-wide" align="center" style="margin:40px 0 0 0;">
               		<div style="padding:10px 0 10px 0;" align="left">
                    	<!-- Charcaters -->
                    	<div style="display:block; padding:0 20px 0 10px; float:left;">
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
                                    <div style="display:inline-block; float: left; margin: 16px 18px 16px 18px; vertical-align: middle;">
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
                   
                    	<div style="float:right; padding:2px 12px 0 0; margin: 16px 18px 16px 18px;">
                            <select styled="true" id="armors-filter-select" name="filter">
                                <option selected="selected" disabled="disabled">Apply Filter</option>
                                <option value="0">None</option>
                                <?php
                                
                                $res = $DB->query("SELECT id, name FROM `armorset_categories` ORDER BY name ASC;");
                                //check if we have any cats at all
                                if ($res->rowCount() > 0)
                                {
                                    while ($arr = $res->fetch())
                                    {
                                        echo '<option value="', $arr['id'], '" ', ($filter == $arr['id'] ? 'selected="selected"' : ''), '>', $arr['name'], '</option>';
                                    }
									unset($arr);
                                }
                                unset($res);
								
                                ?>
                            </select>
                    	</div>
                   
                   <div class="clear"></div>
                                  
                  </div>
                </div>
                <!-- Top Bar (Charcater & Gear filter) -->
                
                <div class="pagination-container account-wide" align="right">
                    <ul class="pagination" id="armorsets-pagination" style="margin-top: 20px; display: none;">
                            
                        <li id="pagination-nav-first"><a href="#">First</a></li>
                        <li id="pagination-nav-prev"><a href="#">Previous</a></li>
                            
                        <li id="pages">0-0 of 0<p>&nbsp;&nbsp;|</p></li>
                            
                        <li id="pagination-nav-next"><a href="#">Next</a></li>
                        <li id="pagination-nav-last"><a href="#">Last</a></li>
                                            
                    </ul>
                </div>
    
                <script type="text/javascript" src="<?php echo $config['BaseURL']; ?>/resources/min/?f=template/js/armorsets.js"></script>
                
                <script>
                    $(document).ready(function()
                    {
						$('#armorsets_loading').LoadingBar();
						
                        //Setup the store class
                        $('#armorsets-container').WarcryArmorsets(
                        {
                            currentPage: 0, 
                            totalPages: 0, 
                            perPage: <?php echo $perPage; ?>, 
                            totalRecords: 0,
							filter:
							{
								category: <?php echo ($filter ? $filter : 0); ?>,
								character: ''
							},
                            realm: <?php echo $RealmId; ?>,
                            source: '<?php echo $RealmSource; ?>',
                        });
                    });
                </script> 
             	
                <!-- MESSAGE before the items -->
                <div class="armor-sets-page-msg" id="armorsets-starting-message">
                    <strong>Please select a charcater to continue.</strong><br /> Selecting a character will sort the armor sets by your character's class,<br/>
                    you can also apply a filter which will help you find your desired armor set faster.
                </div>
                
                <div class="displayed-armor-sets">
    
                    <div id="armorsets_loading" align="center" style="width: 100%; display: none;"></div>

                    <!-- this is the sets container -->
                    <div id="armorsets-container" style="padding:0; margin:0;">
                    	<!-- ARMORSETS ARE HERE -->
                    </div>
                    <!-- sets container ends -->
    
                </div>
                
                <div class="armor-set-prepurchase-info" align="left" style="display: none;">
                
	                <br/>
	                <p id="armorsets-info-title">
	                    <b>Please select a armor set.</b>
	                </p>
	                <br/>
                    
	                <p id="armorsets-info-text">
	                    Your new armor set will be delivered via the in-game mail system.
	                </p>
	                <br/><br/>
	                
	                <form action="execute.php?take=armorset" method="post" id="armorset-purchase-form">
	                    <input type="hidden" name="character" id="selected-character" />
	                    <input type="hidden" name="armorset" id="selected-armorset" />
	                    <input type="submit" value="Purchase" />
	                </form>
                
                </div>
                
                <div class="clear"></div>
                
            </div>
          <!-- ARMOR SETS.End -->
    
     </div>
	</div>
 
</div>
 
</div>

<?php

$TPL->LoadFooter();

?>