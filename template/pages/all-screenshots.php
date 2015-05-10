<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Set the title
$TPL->SetTitle('Screenshots');
//CSS
$TPL->AddCSS('template/style/page-media.css');
//Print the header
$TPL->LoadHeader();

$p = (isset($_GET['p']) ? (int)$_GET['p'] : 1);

//load the pagination module
$CORE->load_CoreModule('paginationType2');

//Let's setup our pagination
$pagies = new Pagination();
$pagies->addToLink('?page='.$pageName);

$perPage = 16;
$where = "";

$type = TYPE_SCREENSHOT;
$status = SCREENSHOT_STATUS_APPROVED;

//count the total records
$res = $DB->prepare("SELECT COUNT(*) FROM `images` WHERE `type` = :type AND `status` = :status " . $where . ";");
$res->bindParam(':type', $type, PDO::PARAM_INT);
$res->bindParam(':status', $status, PDO::PARAM_INT);
$res->execute();

$count_row = $res->fetch(PDO::FETCH_NUM);
$count = $count_row[0];
			
unset($count_row);
unset($res);

?>

<div class="content_holder">

	 <div class="sub-page-title">
	  <div id="title"><h1>Media<p></p><span></span></h1></div>
	 </div>
	 
	 <div class="container_2" align="center" style="padding:30px 40px; width:916px;">
     
     			<div class="media-header">
					<h2>Screenshots</h2>
                    <h3 class="items-number">(<?php echo $count; ?>)</h3>
                    	<div class="clear"></div>
					<div class="bline"></div>
				</div>
     	
         	<!-- All Screanshots -->
         	<ul class="screanshots all-screanshots screanshots-media-page-two">

				<?php
		        	
				if ($count > 0)
				{
					//calculate the pages
					$pages = $pagies->calculate_pages($count, $perPage, $p);
					
					$res = $DB->prepare("SELECT * FROM `images` WHERE `type` = :type AND `status` = :status ORDER BY id DESC LIMIT ".$pages['limit'].";");
					$res->bindParam(':type', $type, PDO::PARAM_INT);
					$res->bindParam(':status', $status, PDO::PARAM_INT);
					$res->execute();

					//loop the records
					while ($arr = $res->fetch())
					{
						//find the uploaders display name
						$res2 = $DB->prepare("SELECT `displayName` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
						$res2->bindParam(':acc', $arr['account'], PDO::PARAM_INT);
						$res2->execute();
						
						if ($res2->rowCount() > 0)
						{
							$row = $res2->fetch();
							$account = $row['displayName'];
							unset($row);
						}
						else
						{
							$account = 'Unknown';
						}
						unset($res2);
						
            			echo '
						<li>
							<a href="', $config['BaseURL'], '/uploads/media/screenshots/', $arr['image'],'" class="container_frame" rel="shadowbox" title="', $arr['name'], '{|}', $arr['descr'], '">
								<span class="cframe_inner" style="background-image:url(', $config['BaseURL'], '/uploads/media/screenshots/thumbs/', $arr['image'],'); background-size: 100%; background-repeat: no-repeat;"></span>
								<div class="media-zoom-ico"></div>
							</a>
							<div class="screanshot-title-info">', $arr['name'], '<p>By ', $account, '</p></div>
						</li>';
						
						unset($account);
					}
					unset($arr);
				}
				else
				{
					echo '<p class="there-is-nothing">There are no items.</p>';
				}
				unset($res);
                            
				?>            
					
                <div class="clear"></div>
            </ul>
            <div class="clear"></div>
        	
         	<!-- All Screanshots.End -->

            <?php
			
			if ($count > 0 and $count > $perPage)
			{
				echo '
	            <!-- Pagination -->
		            <div class="d-cont pagination-holder pagination-media">
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
			unset($pagies, $pages);
			
			?>
	        
	 </div>
    
</div>

<?php

unset($count, $status, $type, $where, $perPage, $p);

//Add some javascripts to the loader
$TPL->AddFooterJs('template/js/shadowbox.js');
$TPL->AddFooterJs('template/js/init.custom.shadowbox.js');
//Print footer
$TPL->LoadFooter();

?>
