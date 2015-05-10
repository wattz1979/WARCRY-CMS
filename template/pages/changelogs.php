<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//get the changelog number for each list (website, core etc...)
$ChangelogID = isset($_GET['changelog']) ? (int)$_GET['changelog'] : CHANGELOG_WEB;

//define changesets per pare
$PerPage = CHANGELOG_PERPAGE;

//validate the changelog id
$ValidChangelogs = array(
	CHANGELOG_WEB,
	CHANGELOG_CORE
);

//Set the title
$TPL->SetTitle('Changelogs');
//CSS
$TPL->AddCSS('template/style/page-changelogs.css');
//Print the header
$TPL->LoadHeader();

?>

<div class="content_holder">

    <div class="sub-page-title">
    	<div id="title"><h1>Changelogs<p></p><span></span></h1></div>
    </div>
 
  	<div class="container_2" align="center">
    	
            <div class="changelogs-cats">
            	<a href="<?php echo $config['BaseURL'];?>/index.php?page=changelogs&changelog=1" <?php echo ($ChangelogID == CHANGELOG_WEB ? 'class="active"' : ''); ?>>Website Changelog<p>Website related changes and updates.</p></a>
                <a href="<?php echo $config['BaseURL'];?>/index.php?page=changelogs&changelog=2" <?php echo ($ChangelogID == CHANGELOG_CORE ? 'class="active"' : ''); ?>>Core Changelog<p> WarCry Core changes and updates</p></a>
                <div class="clear"></div>
            </div>
        
            <div class="container_3 changelogs">
            	
                <?php
				if (!in_array($ChangelogID, $ValidChangelogs))
				{
					echo '<p style="padding:20px;">Invalid changelog.</p>';
				}
				else
				{
					//count the changesets
					$res = $DB->prepare("SELECT COUNT(*) FROM `changelogs` WHERE `changelog` = :changelog;");
					$res->bindParam(':changelog', $ChangelogID, PDO::PARAM_INT);
					$res->execute();
					$count = $res->fetch(PDO::FETCH_NUM);
					$count = $count[0];
					unset($res);
										
                	echo '
					<!-- Changelogs -->
						<table class="changes-list" id="changes-list">';
							
							//get the changelogs
							$res = $DB->prepare("SELECT * FROM `changelogs` WHERE `changelog` = :changelog ORDER BY `id` DESC LIMIT ".$PerPage.";");
							$res->bindParam(':changelog', $ChangelogID, PDO::PARAM_INT);
							$res->execute();
							
							if ($res->rowCount() > 0)
							{
								while ($arr = $res->fetch())
								{
									$time = $CORE->getTime(true, $arr['time']);
									$time = $time->format('j M H:i');
									
									echo '
									<tr>
										<td class="rev">Rev ', $arr['revision'], '</td>
										<td class="by">', $arr['author'], '</td>
										<td class="date">', $time, '</td>
										<td class="info">', $arr['text'], '</td>
									</tr>';
								}
								unset($res);
							}
							else
							{
								echo '<tr><td style="padding: 10px;"><strong>There are no recent changes.</strong></td></tr>';
							}
						
						echo '
						</table>';
						
						//check if we have more items to display
						if ($count > $PerPage)
						{
							//include our script
							?>
                            
							<script type="text/javascript">
								var CurPage = 1;
								var Changelog = parseInt(<?php echo $ChangelogID; ?>);
								var TotalPulled = parseInt(<?php echo $PerPage; ?>);
								var TotalChangesets = parseInt(<?php echo $count; ?>);
								
								$(document).ready(function()
								{
									$("#load-more").on("click", function()
									{
										//update the curpage
										CurPage = CurPage + 1;
										
										//pull the data
										$.ajax({
											type: "GET",
											url: "ajax.php?phase=10&page="+CurPage+"&changelog="+Changelog,
											dataType: "xml",
											success: function(data)
											{
												//get the page records count
												var list = $(data).find('list');
												var count = parseInt($(data).find('count').text());
												
												//add separator
												if (count > 0)
												{
													//create our separator
													var element = $('<tr><td colspan="4" class="separator" style="width: 910px;"><center><strong>Additional Data</strong></center></td></tr>');
													//append our separator
													$('#changes-list').append(element);
												}
												
												//loop the changesets
												list.find('changeset').each(function(i, e)
												{
                                                    var revision = $(this).find('revision').text();
													var author = $(this).find('author').text();
                                                    var time = $(this).find('time').text();
                                                    var text = $(this).find('text').text();
													
													//create our changeset
													var element = $('<tr style="display:none;"><td class="rev">Rev '+revision+'</td><td class="by">'+author+'</td><td class="date">'+time+'</td><td class="info">'+text+'</td></tr>');
													//append our changeset
													$('#changes-list').append(element);
													//queue the effects
													WarcryQueue('changelogs').add(function()
													{
														element.fadeIn('slow', function()
														{
															WarcryQueue('changelogs').goNext();
														});
													});
													//run the effects queue
													WarcryQueue('changelogs').goNext();
                                               });
												
												//update the total pulled
												TotalPulled = TotalPulled + count;
												
												//check if we have no more to pull
												if (TotalPulled == TotalChangesets)
												{
													$('.load-more').detach();
												}
											}
										});
										
										return false;
									});
								});
								
							</script>
                            
							<?php
							echo '<div class="load-more"><a href="#" id="load-more">Load more</a></div>';
						}
					
					echo'     
					<!-- Changelogs.End -->';
                }
                ?>
                
            </div>
               
    </div>
    
</div>

<?php
	//free memory
	unset($ValidChangelogs, $PerPage, $ChangelogID);

	$TPL->LoadFooter();
?>