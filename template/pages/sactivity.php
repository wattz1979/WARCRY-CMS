<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//Set the title
$TPL->SetTitle('Store Activity');
//Add header javascript
$TPL->AddHeaderJs($config['WoWDB_JS'], true);
//CSS
$TPL->AddCSS('template/style/page-activity-all.css');
//Print the header
$TPL->LoadHeader();

$p = (isset($_GET['p']) ? (int)$_GET['p'] : 1);

//load the pagination module
$CORE->load_CoreModule('paginationType2');

//Let's setup our pagination
$pagies = new Pagination();
$pagies->addToLink('?page='.$pageName);

$perPage = 8;
$where = "";

//count the total records
$res = $DB->prepare("SELECT COUNT(*) FROM `store_activity` WHERE `account` = :acc " . $where . ";");
$res->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
$res->execute();

$count_row = $res->fetch(PDO::FETCH_NUM);
$count = $count_row[0];
			
unset($count_row);
unset($res);

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
    
      <div class="container_3 account_sub_header">
         <div class="grad">
       		<div class="page-title">Store activity</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
      	 </div>
      </div>
      
      <!-- Store Activity -->
      	<div class="store-activity">
        
       		<div class="page-desc-holder">
				All the items you have bought from our Store will be shown at this page.
            </div>
            
            <div class="container_3 account-wide" align="center">

            	<ul class="activity-list">
                  
				<?php
		        	
					if ($count > 0)
					{
						//calculate the pages
						$pages = $pagies->calculate_pages($count, $perPage, $p);
						
						//get the activity records
						$res = $DB->prepare("SELECT * FROM `store_activity` WHERE `account` = :acc ORDER BY id DESC LIMIT ".$pages['limit'].";");
						$res->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
						$res->execute();
						
						//loop the records
						while ($arr = $res->fetch())
						{
							//get the item record from the store
							$res2 = $DB->prepare("SELECT entry, name, Quality FROM `store_items` WHERE `id` = :id LIMIT 1;");
							$res2->bindParam(':id', $arr['itemId'], PDO::PARAM_INT);
							$res2->execute();
							
							//check if we have found the item
							if ($res2->rowCount() > 0)
							{
								$item = $res2->fetch();
							}
							else
							{
								//that's the array for missing item
								$item = array('entry' => 0, 'name' => 'Unknown', 'Quality' => '0');
							}
							unset($res2);

							//format the time
							$time = $CORE->getTime(true, $arr['time']);
							$arr['time'] = $time->format('d F Y, H:i:s');
							unset($time);
							
							echo '
			                <li>
				                <p id="r-item"><a class="', strtolower($CORE->getItemQualityString($item['Quality'])), '" href="', $config['WoWDB_URL'], '/?item=', $item['entry'], '" target="_newtab" rel="item=', $item['entry'], '">[', $item['name'], ']</a></p>
				                <p id="r-date">', $arr['time'], '</p>
				                <p id="r-info">', $arr['money'], '</p>
			                </li>';
							
							unset($item);
						}
						unset($arr);
					}
					else
					{
                    	echo '<p class="there-is-nothing">There are no items.</p>';
					}
					unset($res);
					
				?>  
				                  
                </ul>
                          
            </div>
            
            <?php
			
			if ($count > 0 and $count > $perPage)
			{				
				echo '
	            <!-- Pagination -->
		            <div class="d-cont wide pagination-holder">
	                	<ul class="pagination" id="store-pagination">
	                    
							', $pages['first'], '
		                    ', $pages['previous'], '
		                        
		                    ', $pages['info'], '
		                        
		                    ', $pages['next'], '
		                    ', $pages['last'], '
							                    
	                    </ul>
	                    <div class="clear"></div>
		            </div>';
			}
			
			?>
            
      	</div>
      <!-- Store Activity.End -->
    
     </div>
	</div>
 
</div>

</div>

<?php
	unset($pagies, $pages, $p, $perPage, $where, $count);

	$TPL->LoadFooter();
?>