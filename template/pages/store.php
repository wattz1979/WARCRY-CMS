<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

$RealmId = $CURUSER->GetRealm();
$RealmSource = 'old';

$search = (isset($_GET['search']) ? $_GET['search'] : '');
$quality = (isset($_GET['quality']) ? $_GET['quality'] : '-1');

//define items per page
$perPage = 6;

//Set the title
$TPL->SetTitle('Store');
//Add header javascript
$TPL->AddHeaderJs($config['WoWDB_JS'], true);
//CSS
$TPL->AddCSS('template/style/page-store.css');
//Print the header
$TPL->LoadHeader();
?>

<div class="content_holder">

<div class="sub-page-title">
	<div id="title"><h1>Account Panel<p></p><span></span></h1></div>
  
    <div class="quick-menu">
    	<a class="arrow" href="#"></a>
        <ul class="dropdown-qmenu">
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
	if ($error = $ERRORS->DoPrint('store'))
	{
		echo $error, '<br><br>';
				
		unset($error);
	}			
	?>
        
      <div class="container_3 account_sub_header">
         <div class="grad">
       		<div class="page-title">Store</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
      	 </div>
      </div>
      
      
      <div class="store_notice">
      		<h1>We have decided to limit the item levels of what you can buy. Currently, the highest item level you can obtain is <b>226</b>. This will be increased over time.</h1>
      </div>
      
      
      <!-- Store -->
      
      	<!-- SEARCH Bar -->
      	<div class="container_3 account-wide" id="search-bar">
        	<form method="get" onsubmit="return false;" id="store-search-form">
            	<input id="store-search-input" name="search" type="text" placeholder="Search" value="<?php echo ($search != '' ? $search : ''); ?>"/>
                <select id="store-quality-select" name="quality" styled="true">
                <?php
                echo '
	                 <option value="-1" 							', ($quality == '-1' ? 'selected="selected"' : ''), '>Any item quality</option>
	                 <option value="0" style="color: #9D9D9D;" 		', ($quality == '0' ? 'selected="selected"' : ''), '>Poor</option>
	                 <option value="1" style="color: white;" 		', ($quality == '1' ? 'selected="selected"' : ''), '>Common</option>
	                 <option value="2" style="color: #1EFF00;" 		', ($quality == '2' ? 'selected="selected"' : ''), '>Uncommon</option>
	                 <option value="3" style="color: #0070DD;" 		', ($quality == '3' ? 'selected="selected"' : ''), '>Rare</option>
	                 <option value="4" style="color: #A335EE;" 		', ($quality == '4' ? 'selected="selected"' : ''), '>Epic</option>
	                 <option value="5" style="color: #FF8000;" 		', ($quality == '5' ? 'selected="selected"' : ''), '>Legendary</option>
	                 <!--<option value="6" style="color: #E5CC80;" 	', ($quality == '6' ? 'selected="selected"' : ''), '>Artifact</option>-->
	                 <option value="7" style="color: #E5CC80;" 		', ($quality == '7' ? 'selected="selected"' : ''), '>Bind to Account</option>';
				?>
	            </select>
                <input type="submit" value="Search" />
            </form>
        </div>
        
        <!-- Search Results -->
        	
            <div class="search-results">
            
            	<div class="search-results-head">
                	<div class="title">Search results</div>
                    <ul class="pagination" id="store-pagination" style="display: none;">
                    
					<li id="pagination-nav-first"><a href="#">First</a></li>
                    <li id="pagination-nav-prev"><a href="#">Previous</a></li>
                        
                    <li id="pages">0-0 of 0<p>&nbsp;&nbsp;|</p></li>
                        
				  	<li id="pagination-nav-next"><a href="#">Next</a></li>
                   	<li id="pagination-nav-last"><a href="#">Last</a></li>
						                    
                    </ul>
                </div>
                
                <div class="items_results" align="left">
                
                    <div id="store_loading" align="center" style="width: 100%;"></div>

 					<script type="text/javascript" src="<?php echo $config['BaseURL']; ?>/resources/min/?f=template/js/store.js"></script>
                   
                    <script>
						$(document).ready(function()
						{
							$('#store_loading').LoadingBar();
							
							//Setup the store class
							$('#store-item-container').WarcryStore(
							{
								currentPage: 0, 
								totalPages: 0, 
								perPage: <?php echo $perPage; ?>, 
								totalRecords: 0,
								realm: <?php echo $RealmId; ?>,
								source: '<?php echo $RealmSource; ?>',
							});
						});
					</script>
                    
                	<ul id="store-item-container">
                        
                        <?php
						//define some defaults
						$isSearch = false;
						$isQuality = false;
						$where = "";

						//if we have a search
						if ($search != '')
						{
							$isSearch = true;
							
							//and quality selected
							if ($quality != '-1' and $quality != '')
							{
								$isQuality = true;
								
								$where = "WHERE `name` LIKE CONCAT('%', :search, '%') AND `Quality` = :quality";
							}
							else
							{
								$where = "WHERE `name` LIKE CONCAT('%', :search, '%')";
							}
						}
						else if ($quality != '-1' and $quality != '')
						{
							$isQuality = true;
							
							$where = "WHERE `Quality` = :quality";
						}

																					
						//count the items
						$count_res = $DB->prepare("SELECT COUNT(*) FROM `store_items` ".$where);
						if ($isSearch)
						{
							$count_res->bindParam(':search', $search, PDO::PARAM_STR);
						}
						if ($isQuality)
						{
							$count_res->bindParam(':quality', $quality, PDO::PARAM_INT);
						}
						$count_res->execute();
						$count_row = $count_res->fetch(PDO::FETCH_NUM);
						
						$count = $count_row[0];
									
						unset($count_row);
						unset($count_res);
											
						if ($count > 0)
						{
							//setup some pagination suff
							$currentPage = 1;
							$totalPages = ceil($count / $perPage);

							//setup the store javascript class
							echo '	<script>
									$(document).ready(function()
									{
										$(\'#store-item-container\').WarcryStore(\'changeConfig\',
										{
											currentPage: ', $currentPage, ', 
											totalPages: ', $totalPages, ', 
											totalRecords: ', $count, ', 
											filter: 
											{
												search: \'', $search, '\', 
												quality: \'', $quality, '\' 
											}
										});
										$(\'#store-item-container\').WarcryStore(\'loadPage\', \'first\');
									});
							  		</script>';
						}
						else
						{
							echo '<p class="noresults">There are no items.</p>
							<script>
								$(function()
								{
									WarcryQueue(\'STORE\').add(function()
									{
										$(\'#store_loading\').LoadingBar(\'state4\', function()
										{
											WarcryQueue(\'STORE\').goNext();
										});
									});
									
									WarcryQueue(\'STORE\').add(function()
									{
										$(\'#store_loading\').fadeOut(\'slow\', function()
										{
											//fade in the item list
											$(\'#store-item-container\').fadeIn(\'slow\');
										});
									});
									
									WarcryQueue(\'STORE\').goNext();
								});
							</script>';
						}
						
						?>

                    </ul>
                </div>
 
  			<!-- Search Results -->
            	
                <div class="items-currency-list">
                
                	<div class="desc-head" align="left">
                		<div class="title">Shopping Cart</div>
                		<div class="desc">(You can pay with different coins for each item. Click on the price to select the coins you want to pay with.)</div>
                	</div>
                    
                    <div class="list-coins" id="store-cart-top-menu">
                    	<div class="g-coin"></div>
                        <div class="s-coin"></div>
                        <div class="empty-cart"><a id="store-empty-cart-btn" href="#" style="display:none;">Empty Cart</a></div>
                    </div>
                    <div class="container_3 account-wide" id="store-shopping-cart">
                        
                        <p>The cart is empty.</p>
                                        
                    </div>
                    <div class="list-coins" id="store-total-amount" align="right" style="display: none;">
                    	Total: 
                        <div class="s-coin"></div>
                        <div id="store-total-silver">10</div> 
                        <div class="g-coin"></div>
                        <div id="store-total-gold">20</div>
                    </div>
                
                </div>
 
            </div>
        
        <!-- Search Results -->
        
        <!-- Select Character & Complete -->
            	
            <div class="items-currency-list">
                
                	<div class="desc-head" align="left">
                		<div class="title">Select character</div>
                	</div>
                    
                    <div id="store-complete-form-container" class="container_3 account-wide" style="height:71px;">
                    
                    <form method="post" action="<?php echo $config['BaseURL']; ?>/execute.php?take=buyItems" id="store-complete-form"> 
                    		                    
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
								<div style="display:inline-block; float: left; margin: 16px 18px 0 18px;">
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

	                    <input type="submit" value="Purchase" />
                        
                    </form>
                    
                    </div>
               
            </div>
        
        <!-- Select Character & Complete.End -->
      
      <!-- Store.End -->
        
     </div>
	</div>
 
 </div>

</div>

<?php

$TPL->LoadFooter();

?>
