<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

$RealmId = $CURUSER->GetRealm();

//check if we have the session set
if (!isset($_SESSION['StoreItemReturn']))
{
	header("Location: ".$config['BaseURL']);
	die;
}

//Set the title
$TPL->SetTitle('Store');
//Add header javascript
$TPL->AddHeaderJs($config['WoWDB_JS'], true);
//CSS
$TPL->AddCSS('template/style/page-store-complete.css');
//Print the header
$TPL->LoadHeader();

?>
<div class="content_holder">

 <div class="sub-page-title">
  <div id="title"><h1>Account Panel<p></p><span></span></h1></div>
 </div>
 
  	<div class="container_2 account" align="center">
     <div class="cont-image">
    
      <div class="container_3 account_sub_header">
         <div class="grad">
       		<div class="page-title">Store</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
      	 </div>
      </div>
      
      <!-- Store Complete purchase -->
      	<div class="store-complete">
            
            <div class="container_3 account-wide" align="center">
            
	            <div class="top-info">
                	<p>Purchase Complete</p>
                    <span>The items below ware sent to <b><?php echo $_SESSION['StoreItemReturnChar']; ?></b>.</span>
	            </div>
                
                <!-- Item List -->
	                <ul class="items-list">

                        <?php
											
						foreach ($_SESSION['StoreItemReturn'] as $id => $data)
						{
							$res = $DB->prepare("SELECT entry, name, Quality FROM `store_items` WHERE `id` = :id LIMIT 1;");
							$res->bindParam(':id', $data['id'], PDO::PARAM_INT);
							$res->execute();
							
							if ($res->rowCount() > 0)
							{
								$row = $res->fetch();
								
								if ($data['error'] == '')
								{
									//successfully sent item
									echo '
	                        		<li>
		                    			<p title="Item successfully sent." class="status success-i"><em></em></p>
		                        		<p class="item-info">
	                            			<a href="', $config['WoWDB_URL'], '/?item=', $row['entry'], '" target="_newtab" rel="item=', $row['entry'], '" class="item-ico ', strtolower($CORE->getItemQualityString($row['Quality'])), '" style="background-image:url(http://wow.zamimg.com/images/wow/icons/large/', strtolower($data['icon']), '.jpg);"></a>
	                                		<b>', $row['name'], '</b>
	                            		</p>
		                    		</li>';
								}
								else
								{
									//failed item
									echo '
	                        		<li>
		                    			<p title="', $data['error'],'" class="status fail-i"><em></em></p>
		                        		<p class="item-info">
	                            			<a href="', $config['WoWDB_URL'], '/?item=', $row['entry'], '" rel="item=', $row['entry'], '" class="item-ico ', strtolower($CORE->getItemQualityString($row['Quality'])), '" style="background-image:url(http://wow.zamimg.com/images/wow/icons/large/', strtolower($data['icon']), '.jpg);"></a>
	                                		<b>', $row['name'], '</b>
	                            		</p>
		                    		</li>';
								}
								unset($row);
							}
							else
							{
								echo '
                        		<li>
	                    			<p title="The item does not exist in the store." class="status fail-i"><em></em></p>
	                        		<p class="item-info">
                            			<a href="#" class="item-ico poor" style="background-image:url(http://wow.zamimg.com/images/wow/icons/large/inv_misc_questionmark.jpg);"></a>
                                		<b>Unknown</b>
                            		</p>
	                    		</li>';
							}
							unset($res);
						}
						
						//unset the sessions
						unset($_SESSION['StoreItemReturn']);
						unset($_SESSION['StoreItemReturnChar']);
						
						?>
                                                
	                </ul>
                <!-- Item List -->
                
                	<div class="description">
                    Items marked with red cross have not been sent. <br/>
                    To understand why move your mouse over the red cross, <br/>the reason should be displayed as tooltip.
                    </div>
             
	        </div>
            
      	</div>
      <!-- Account Activity.End -->
        
     </div>
	</div>
 
</div>

</div>

<?php

$TPL->LoadFooter();

?>
