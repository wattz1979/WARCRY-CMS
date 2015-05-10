<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//Set the title
$TPL->SetTitle('Coin Activity');
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
$res = $DB->prepare("SELECT COUNT(*) FROM `coin_activity` WHERE `account` = :acc " . $where . ";");
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
       		<div class="page-title">Coins activity</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
      	 </div>
      </div>
      
      <!-- Coins Activity -->
      	<div class="coins-activity">
        
      		
       		<div class="page-desc-holder">
		Donec non sem diam. Donec id sapien et quam vestibulum congue a sed lectus. <br/>Pellentesque feugiat tempus mauris, id porttitor sapien interdum non.
            </div>
            
            
            <div class="container_3 account-wide" align="center">
             
             	<ul class="activity-list">

				<?php
		        	
					if ($count > 0)
					{
						//calculate the pages
						$pages = $pagies->calculate_pages($count, $perPage, $p);
						
						//get the activity records
						$res = $DB->prepare("SELECT * FROM `coin_activity` WHERE `account` = :acc ORDER BY id DESC LIMIT ".$pages['limit'].";");
						$res->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
						$res->execute();
						
						//loop the records
						while ($arr = $res->fetch())
						{
							//check the source type
							switch ($arr['sourceType'])
							{
								case CA_SOURCE_TYPE_PURCHASE:
									$sourceType = '<i>Purchased</i> ';
									break;
								case CA_SOURCE_TYPE_REWARD:
									$sourceType = '<i>Reward</i> ';
									break;
								case CA_SOURCE_TYPE_DEDUCTION:
									$sourceType = '<i>Deducted</i> ';
									break;
								case CA_SOURCE_TYPE_NONE:
								default:
									$sourceType = '';
									break;
							}
							
							//check the coins type
							switch ($arr['coinsType'])
							{
								case CA_COIN_TYPE_SILVER:
									$coinType = 'Silver coins';
									break;
								case CA_COIN_TYPE_GOLD:
									$coinType = 'Gold coins';
									break;
								default:
									$coinType = 'Unknown coins';
									break;
							}
							
							//check the exchange type
							switch ($arr['exchangeType'])
							{
								case CA_EXCHANGE_TYPE_MINUS:
									$exchangeType = '- ';
									break;
								case CA_EXCHANGE_TYPE_PLUS:
								default:
									$exchangeType = '';
									break;
							}
							
							//format the time
							$time = $CORE->getTime(true, $arr['time']);
							$arr['time'] = $time->format('d F Y, H:i:s');
							unset($time);
							
							echo '
			                <li>
				                <p id="r-title2">', $sourceType, $exchangeType, '<b>', $arr['amount'], ' ', $coinType, '</b></p>
				                <p id="r-date2">', $arr['time'], '</p>
				                <p id="r-info2">', $arr['source'], '</p>
			                </li>';
							
							unset($sourceType, $exchangeType, $coinType);
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
      <!-- Coins Activity.End -->
    
     </div>
	</div>
 
</div>

</div>

<?php
	unset($pagies, $pages, $perPage, $where, $count, $p);

	$TPL->LoadFooter();
?>