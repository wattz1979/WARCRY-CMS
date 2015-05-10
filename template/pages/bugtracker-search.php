<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Set the title
$TPL->SetTitle('Bug Tracker - Search');
//CSS
$TPL->AddCSS('template/style/page-bugtracker-all.css');
//Print the header
$TPL->LoadHeader();

$searchString = isset($_GET['q']) ? $_GET['q'] : false;
//Trim the search string
$searchString = trim($searchString);
//reduce white spaces
$searchString = preg_replace('!\s+!', ' ', $searchString);

$count = 0;
//Try searching for that string
if ($searchString)
{
	$res = $DB->prepare("SELECT * FROM `bugtracker` WHERE `title` LIKE CONCAT('%', :title, '%') ORDER BY id DESC LIMIT 25;");
	$res->bindParam(':title', $searchString, PDO::PARAM_STR);
	$res->execute();
	
	$count = $res->rowCount();
}

?>
<div class="content_holder">

 <div class="sub-page-title">
  <div id="title"><h1>Bug Tracker<p></p><span></span></h1></div>
 </div>
 
  	<div class="container_2 bug-search-results" align="center">        
        <!-- BUG TRACKER - Search results -->
        
        	<h4>Search for "<span><?php echo strip_tags($searchString); ?></span>"</h4>
            
        	<div class="container_3 bug-report-search-results" style="width:843px; padding-top:14px; padding-bottom:10px;">
        		
                <?php
					//check if we have found something
					if ($count > 0)
					{
						//loop the issues
						while ($arr = $res->fetch())
						{
							
							//Find the user displayname
							$res2 = $DB->prepare("SELECT `displayName` FROM `account_data` WHERE `id` = :id LIMIT 1;");
							$res2->bindParam(':id', $arr['account'], PDO::PARAM_INT);
							$res2->execute();
							
							if ($res2->rowCount() > 0)
							{
								$row = $res2->fetch();
								$user = $row['displayName'];
								unset($row);
							}
							else
							{
								$user = 'Unknown';
							}
							unset($res2);
							
							$time = $CORE->getTime(true, $arr['added']);
							$time = $time->format("d.m.Y | h:i A");							
							
							//translate the status
							switch ($arr['status'])
							{
								case BT_STATUS_NEW:
									$status = 'New';
									break;
								case BT_STATUS_OPEN:
									$status = 'Open';
									break;
								case BT_STATUS_ONHOLD:
									$status = 'On hold';
									break;
								case BT_STATUS_DUPLICATE:
									$status = 'Duplicate';
									break;
								case BT_STATUS_INVALID:
									$status = 'Invalid';
									break;
								case BT_STATUS_WONTFIX:
									$status = '';
									break;
								case BT_STATUS_RESOLVED:
									$status = 'Resolved';
									break;
								default:
									$status = 'Unknown';
									break;
							}
							
							//translate the approval
							switch ($arr['approval'])
							{
								case BT_APP_STATUS_APPROVED:
									$approval = 'approved';
									break;
								case BT_APP_STATUS_DECLINED:
									$approval = 'declined';
									break;
								default:
									$approval = 'pending';
									break;
							}
							
							echo '
							<ul class="bug-report-row">
								<li class="title">', htmlspecialchars(stripslashes($arr['title'])), '</li>
								<li class="by">by <b>', $user, '</b></li>
								<li class="date">', $time, '</li>
								<li class="status ', $approval, '"><b>', $status, '</b></li>
							</ul>';
						}
						unset($time, $status);
					}
					else
					{
						echo '<p class="there-is-nothing" align="center" style="font-size:14px;">No report ware found.</p>';
					}
                ?>
                                
            </div>
        <!-- BUG TRACKER - Search results . End -->
    </div>
    
</div>

<?php

unset($res, $count, $searchString);

$TPL->LoadFooter();

?>