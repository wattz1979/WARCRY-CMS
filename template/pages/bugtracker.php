<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Set the title
$TPL->SetTitle('Bug Tracker');
//CSS
$TPL->AddCSS('template/style/page-bugtracker-all.css');
//Print the header
$TPL->LoadHeader();

$statusApproved = BT_APP_STATUS_APPROVED;
//define changesets per pare
$PerPage = 25;

//count the user's reports that are not approved
$res = $DB->prepare("SELECT COUNT(*) FROM `bugtracker` WHERE `approval` != :status;");
$res->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$res->execute();
$count = $res->fetch(PDO::FETCH_NUM);
$countNotApproved = $count[0];
unset($res, $count);
//count the user's reports that are approved
$res = $DB->prepare("SELECT COUNT(*) FROM `bugtracker` WHERE `approval` = :status;");
$res->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$res->execute();
$count = $res->fetch(PDO::FETCH_NUM);
$countApproved = $count[0];
unset($res, $count);

//total
$total = $countNotApproved + $countApproved;

?>
<div class="content_holder">

 <div class="sub-page-title">
  <div id="title"><h1>Bug Tracker<p></p><span></span></h1></div>
 </div>
 
  	<div class="container_2" align="center">
    
    		<!-- Bug Report Search -->
                <div class="bugs-search-bar container_3">
                	<form method="get" action="<?php echo $config['BaseURL']; ?>/index.php">
                    	<input type="text" placeholder="Search..." name="q" />
                            <select styled="styled" id="search-category" name="mainCategory">
                                <option value="0" disabled="disabled">Select Category</option>
                                <option value="<?php echo BT_CAT_WEBSITE; ?>">Website</option>
                                <option value="<?php echo BT_CAT_WOTLK_CORE; ?>" selected="selected">WotLK Core</option>
                            </select>
                        	<input type="hidden" name="search" value="1" />
                            <input type="hidden" name="page" value="bugtracker-search" />
                        <input type="submit" value="Search" />
                    </form>
                </div>
           	<!-- Bug Report Search.End -->
            
    
    	<!-- BUG TRACKER - Main Page -->
            <div class="holder-bugtracker">
            
                <div class="bug-reports-holder reports">
                    <h1><?php echo $total; ?></h1>
                    <h3>Submited reports</h3>
                </div>
                
                <div class="bug-reports-holder confirmed">
                    <h1><?php echo $countApproved; ?></h1>
                    <h3>Approved Reports</h3>
                </div>
                
                <?php
					//unset them counts we no longer need em
					unset($total, $countNotApproved, $countApproved);
				?>
                
                <a href="<?php echo $config['BaseURL']; ?>/index.php?page=bugtracker_submit" class="submit-bug-report">
                    <div class="plus-ico">
                        <div id="partone"></div><div id="parttwo"></div>
                    </div>
                    <h1>Submit Report</h1>
                </a>
                
                    <div class="clear"></div>
                    
                    <?php
						//check if we have current user
						if ($CURUSER->isOnline())
						{
							//count the user's reports that are not approved
							$res = $DB->prepare("SELECT COUNT(*) FROM `bugtracker` WHERE `account` = :acc AND `approval` != :status;");
							$res->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
							$res->bindParam(':status', $statusApproved, PDO::PARAM_INT);
							$res->execute();
							$count = $res->fetch(PDO::FETCH_NUM);
							$countNotApproved = $count[0];
							unset($res, $count);
							//count the user's reports that are approved
							$res = $DB->prepare("SELECT COUNT(*) FROM `bugtracker` WHERE `account` = :acc AND `approval` = :status;");
							$res->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
							$res->bindParam(':status', $statusApproved, PDO::PARAM_INT);
							$res->execute();
							$count = $res->fetch(PDO::FETCH_NUM);
							$countApproved = $count[0];
							unset($res, $count);
							
							//total count
							$count = $countApproved + $countNotApproved;
							
                    		echo '
							<div class="bugs-submited-by-me">
								You have submited <b>', $count, '</b> Bug Reports <span>(', $countApproved, ' of them are aproved)</span> ', ($count > 0 ? '<a id="see-all-reports" href="#">See All</a>' : ''), '
							</div>';
							
							//if we have report bind them script for load more
							if ($count > 0)
							{
								?>
                                <script type="text/javascript">
									var CurPage = 0;
									var TotalPulled = 0;
									var TotalReports = parseInt(<?php echo $count; ?>);
									var PerPage = parseInt(<?php echo $PerPage; ?>);
									var ListOpened = false;
									
									//bind the starting event
									$('#see-all-reports').bind('click', function()
									{
										if (!ListOpened)
										{
											$('#see-all-reports').html('Hide All');
											//fade in the container
											$('.all-reports-by-me').fadeIn('fast');
											//load the page
											if (CurPage == 0)
											{
												LoadPage(1);
											}
											//define
											ListOpened = true;
										}
										else
										{
											$('#see-all-reports').html('See All');
											//fade out the container
											$('.all-reports-by-me').fadeOut('fast');
											//define
											ListOpened = false;
										}
										
										return false;
									});
									
									function LoadPage(page)
									{
										CurPage = page;
										
										//pull the data
										$.ajax({
											type: "GET",
											url: "ajax.php?phase=15&page="+CurPage+"&perpage="+PerPage,
											dataType: 'json',
											cache: false,
											error: function(jqXHR, textStatus, errorThrown)
											{
												$('#report-container').append('<li style="text-align: center;" id="loading"><a class="closed"><p class="title" style="width: auto;">An error occured!</p></a></li>');
												console.log(textStatus);
											},
											success: function(data)
											{
												//get the count
												var count = parseInt(data.count);
												//update the total pulled
												TotalPulled = TotalPulled + count;
												
												//check if we have to remove the load more button
												if (TotalPulled >= TotalReports)
												{
													//check if we have the load more button
													if ($('#report-container').find('#load-more').length > 0)
													{
														WarcryQueue('bt_reports').add(function()
														{
															$('#report-container').find('#load-more').fadeOut('fast', function()
															{
																$('#report-container').find('#load-more').detach();
																//run the queue
																WarcryQueue('bt_reports').goNext();
															});
														});
													}
												}
												
												//loop them issues
												$.each(data.issues, function(key, value)
												{
													WarcryQueue('bt_reports').add(function()
													{
														//append the new issue
														var newIssue = $(
														'<li style="display: none;">'+
															'<a class="'+value.approval+'" href="#">'+
																'<p class="title">'+value.title+'</p>'+
																'<p class="main-cat">'+value.maincategory+'</p>'+
																'<p class="sub-cat">'+value.category+'</p>'+
																'<p class="prio">'+value.priority+' Priority</p>'+
																'<p class="status">'+value.status+'</p>'+
															'</a>'+
														'</li>');
														//append
														$('#report-container').append(newIssue);
														//fade in
														newIssue.fadeIn('fast', function()
														{
															WarcryQueue('bt_reports').goNext();
														});
													});
												});
												//check if we have more to pull
												if (TotalPulled < TotalReports)
												{
													//check if we already have the load more button
													if ($('#report-container').find('#load-more').length == 0)
													{
														WarcryQueue('bt_reports').add(function()
														{
															//add load more button
															var loadmore = $('<li style="text-align: center; display: none;" id="load-more"><a class="closed" href="#"><p class="title" style="width: auto;">Load More</p></a></li>');
															$('#report-container').append(loadmore);
															//bind the click event
															$(loadmore).bind('click', function()
															{
																LoadPage(CurPage + 1);
																
																return false;
															});
															loadmore.fadeIn('fast', function()
															{
																WarcryQueue('bt_reports').goNext();
															});
														});
													}
												}
												
												//run the queue
												WarcryQueue('bt_reports').goNext();
											}
										});
									}
								</script>
                                <?php
							}
						}
					?>
                    
                    <!-- ALL REPORTS BY THIS USER - Will be displayed only if the user click on "SEE ALL" link! -->
                    	<div class="all-reports-by-me" style="display: none;">
                        	<ul class="reports" id="report-container">                                
                            </ul>
                        </div>
                    
                     <!--ALL REPORTS BY THIS USER . End -->
                    
                
                <div class="bug-tracker-info">
                
                     <h3><font color="#c7962c">Bug Tracker Guidelines</font></h3>
                     <br/>
                     <b><font color="#79736a">We highly appreciate your efforts to report any problems you may discover on our site or ingame. 
                     In order to process and resolve all reported bugs, we ask you to follow the guidelines below.</font></b> <br/><br/>
    				<font color="#656059">
                    - Please search before submitting anything to our bug tracker. It's possible someone else has already reported the bug in question. <br/>
                    - Use proper titles. E.g. the name of the quest, NPC or Item you may have problems with. <br/>
                    - What is wrong? E.g. What happens and what is supposed to happen. <br/>
                    - Add anything else you think might be useful for us to know. <br/><br/>
                    </font>

					<i><font color="#79736a">Please follow these guidelines and you'll make us work much easier. In return, we'll reward you with Silver Coins for each approved report.</font></i>
                </div>
                
            </div>
    	<!-- BUG TRACKER - Main Page . End -->
        
    </div>
    
</div>

<?php

$TPL->LoadFooter();

?>