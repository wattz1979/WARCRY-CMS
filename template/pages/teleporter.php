<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

$RealmId = $CURUSER->GetRealm();

//get the cooldown
$cooldown = $CURUSER->getCooldown('teleport');
$cooldownTime = '5 minutes';

//Set the title
$TPL->SetTitle('Teleporter');
//Load the css
$TPL->AddCSS('template/style/page-teleporter.css');
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
    
      <div class="container_3 account_sub_header">
         <div class="grad">
       		<div class="page-title">Teleporter</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
      	 </div>
      </div>
      
      <!-- Account Activity -->
      	<div class="teleporter">
      		
       		<div class="page-desc-holder">
  				The Teleporter can be used while you are in-game and does not cost<br/> anything. This tool has 5 minutes cooldown.
            </div>
            
            <?php
			if ($error = $ERRORS->DoPrint('teleport'))
			{
				echo '<br>', $error, '<br><br>';
			}			
			if ($error = $ERRORS->successPrint('teleport'))
			{
				echo '<br>', $error, '<br><br>';
			}			
			unset($error);
			?>

            <div class="container_3 account-wide padding-tele" align="center">            		
            	
                <div class="map-holder" align="center">
                	
                    <script>
						function startCooldownTimer(element, totalCooldown, leftCooldown)
						{
							var cont = $(element);
							var leftCooldown = parseInt(leftCooldown);
							var totalCooldown = parseInt(totalCooldown);
							
							//update each second
							var $interval = setInterval(function()
							{
								//update the cooldown
								leftCooldown = parseInt(leftCooldown) - 1;
								
								var seconds = leftCooldown % 60;
								var minutes = Math.floor((leftCooldown / 60) % 60);
								var hours = Math.floor((leftCooldown / (60*60)) % 24);
								var days = Math.floor((leftCooldown / (24*60*60)) % 30);
								
								if (seconds < 10)
								{
									seconds = '0' + seconds;
								}
								
								//update the cooldown text
								cont.html((minutes > 0 ? minutes + '<span>:</span>' + seconds : seconds));
															
								//break the interval
								if (minutes == 0 && seconds == 0)
								{
									clearInterval($interval);
									$('.cooldown-window').css('display', 'none');
									DrawCanvases();
								}
								
							}, 1000);
						}
					</script>
                    
                    <?php
               	   
					if ($cooldown = $CORE->convertCooldown($cooldown))
					{
						$totalCooldown = strtotime($cooldownTime, 0);
						$leftCooldown = $cooldown['int'];
						
						echo '
						<div class="cooldown-window">
							<div id="text-holder">
								<span>The teleporter is on cooldown!</span>
								<h5 id="cooldown-timer">
									', ($cooldown['minutes'] > 0 ? $cooldown['minutes'] . '<span>:</span>' . ($cooldown['seconds'] < 10 ? '0' . $cooldown['seconds'] : $cooldown['seconds']) : $cooldown['seconds']), '
								</h5>
								<p>Please wait untill the cooldown expires then you will be able to use the teleporter again!</p>
							</div>
                    	</div>';
						
						echo '
						<!-- Visual cooldown vars -->
						<script>
							$OnCooldown = true;
							//run the cooldown timer
							startCooldownTimer(\'#cooldown-timer\', ', $totalCooldown,', ', $leftCooldown, ');
						</script>';
					}
					else
					{
						echo '
						<!-- Visual cooldown vars -->
						<script>
							$OnCooldown = false;
						</script>';
					}
						   
					?>
                
                	<div class="tele-back-btn" style="display:none;"><a href="javascript: void(0);" id="tp-back"></a></div>
                    
                    	<!-- STEP TWO -->
                    	<div class="step-two" id="tp-mainmap-container">
                            <a href="#" class="kalimdor" id="tp-btn-kalimdor"></a>
                            <a href="#" class="eastern-kingdoms" id="tp-btn-eastern-kingdoms"></a>
                            <a href="#" class="northrend" id="tp-btn-northrend"></a>
                        </div>
                        <!-- -->
                       
                        <!-- Northrend -->
                        <div class="open-map-holder" id="tp-northrend-container" mapname="northrend" style="display:none;">
                            <div class="map-title" align="left"><p>Northrend</p><span>234 teleport places</span></div>
                        	<div class="open-northrend">
                                <div class="canv-holder">
                                    <canvas id="crystalsong" width="72" height="41"></canvas>
                                    <canvas id="stormpeaks" width="140" height="125"></canvas>
                                    <canvas id="zuldrak" width="117" height="102"></canvas>
                                    <canvas id="grizzlyhills" width="107" height="84"></canvas>
                                    <canvas id="howlingfjord" width="103" height="122"></canvas>
                                    <canvas id="borean-tundra" width="135" height="84"></canvas>
                                    <canvas id="dragonblight" width="129" height="75"></canvas>
                                    <canvas id="icecrown" width="135" height="114"></canvas>
                                    <canvas id="sholazar-basin" width="87" height="70"></canvas>
                                    <canvas id="wintersgrap" width="87" height="70"></canvas>
                                </div>
                                <div class="opan-territory-holder" style="width:320px; height:320px; top:45%; left:50%; margin:-145px 0 0 -150px; display:none;">                              
                                    <div class="territory-name-info" style="display:none;">
                                        <h1 id="name"></h1>
                                        <p id="level"></p>
                                        <p id="places"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- -->
                        
                        <!-- Kalimdor -->
                        <div class="open-map-holder" id="tp-kalimdor-container" mapname="kalimdor" style="display:none;">
                            <div class="map-title" align="left"><p>Kalimdor</p><span>234 teleport places</span></div>
                        	<div class="open-kalimdor">
                                <div class="canv-holder">
                                    <canvas id="ashenvaley" width="121" height="73"></canvas>
                                    <canvas id="azshara" width="88" height="67"></canvas>
                                    <canvas id="azuremyst" width="72" height="51"></canvas>
                                    <canvas id="barrens" width="69" height="161"></canvas>
                                    <canvas id="bloodmysle" width="45" height="37"></canvas>
                                    <canvas id="darkshore" width="74" height="99"></canvas>
                                    <canvas id="desolace" width="62" height="69"></canvas>
                                    <canvas id="durotar" width="36" height="78"></canvas>
                                    <canvas id="dustwallow-marsh" width="58" height="68"></canvas>
                                    <canvas id="felwood" width="46" height="85"></canvas>
                                    <canvas id="feralas" width="105" height="98"></canvas>
                                    <canvas id="moonglore" width="33" height="35"></canvas>
                                    <canvas id="mulgore" width="50" height="81"></canvas>
                                    <canvas id="silithus" width="86" height="111"></canvas>
                                    <canvas id="stonetalon" width="91" height="79"></canvas>
                                    <canvas id="tanaris" width="83" height="95"></canvas>
                                    <canvas id="teldrassil" width="66" height="52"></canvas>
                                    <canvas id="thousand-needles" width="95" height="63"></canvas>
                                    <canvas id="ungoro" width="59" height="90"></canvas>
                                    <canvas id="winterspring" width="85" height="111"></canvas>
                                </div>
                                <div class="opan-territory-holder" style="width:320px; height:320px; top:45%; left:50%; margin:-145px 0 0 -145px; display:none;">                              
                                    <div class="territory-name-info" style="display:none;">
                                        <h1 id="name"></h1>
                                        <p id="level"></p>
                                        <p id="places"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- -->
                        
                        <!-- Eastern Kingdoms -->
                        <div class="open-map-holder" id="tp-eastern-kingdoms-container" mapname="eastern-kingdoms" style="display:none;">
                        	<div class="map-title" align="left"><p>Eastern Kingdoms </p><span>234 teleport places</span></div>
                        	<div class="open-eastern-kingdoms">
                                <div class="canv-holder">
                                    <canvas id="queldanas" width="26" height="33"></canvas>
                                    <canvas id="alterac_mountains" width="56" height="37"></canvas>
                                    <canvas id="arathi_highlands" width="64" height="43"></canvas>
                                    <canvas id="badlands" width="65" height="27"></canvas>
                                    <canvas id="blasted_lands" width="40" height="53"></canvas>
                                    <canvas id="burning_steps" width="71" height="32"></canvas>
                                    <canvas id="deadwind_pass" width="20" height="26"></canvas>
                                    <canvas id="dun_morough" width="108" height="76"></canvas>
                                    <canvas id="duskwood" width="49" height="33"></canvas>
                                    <canvas id="eastern_plaguelands" width="92" height="62"></canvas>
                                    <canvas id="elwynn_forest" width="72" height="47"></canvas>
                                    <canvas id="eversong_woods" width="64" height="60"></canvas>
                                    <canvas id="ghostlands" width="76" height="81"></canvas>
                                    <canvas id="hillsbrad_foothills" width="65" height="42"></canvas>
                                    <canvas id="loch_modan" width="50" height="39"></canvas>
                                    <canvas id="redridge_mountains" width="76" height="35"></canvas>
                                    <canvas id="searing_gourge" width="41" height="30"></canvas>
                                    <canvas id="silverpine_forest" width="40" height="60"></canvas>
                                    <canvas id="stranglethorn_valley" width="71" height="78"></canvas>
                                    <canvas id="swamps_of_sorrow" width="46" height="28"></canvas>
                                    <canvas id="the_hinterlands" width="99" height="49"></canvas>
                                    <canvas id="tirisfal_glades" width="95" height="49"></canvas>
                                    <canvas id="western_plaguelands" width="37" height="56"></canvas>
                                    <canvas id="westfall" width="40" height="47"></canvas>
                                    <canvas id="wetlands" width="81" height="48"></canvas>
                                </div>
                                <div class="opan-territory-holder" style="width:320px; height:320px; top:45%; left:0; margin:-145px 0 0 0; display:none;">                              
                                    <div class="territory-name-info" style="display:none;">
                                        <h1 id="name"></h1>
                                        <p id="level"></p>
                                        <p id="places"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- -->
 						
                        <!-- Opened Territory -->
                        <div class="open-territory" style="display:none;">                              
                           <div class="map-title" align="left"><p id="name"></p><span id="level"></span></div>
                        </div>
                        
                        <!-- Overlay --
                        <div class="open-territory-overlay" style="display:none;"></div>
                        -->
                        
                        <!-- Loading Bar -->
                        <div class="TP_loading_cont" align="center" style="display: none;">
                            <div id="TP_loading" align="center" style="width: 100%;"></div>
                            <script>
                                $(document).ready(function()
                                {
                                    $('#TP_loading').LoadingBar();
                                });
                            </script>
                       	</div>
                </div>
                
                <!-- Character Select Form -->
                <div class="complete-tele-form active-tele-form" style="display:none; margin:6px 0 0 0;" align="left">
                                        
                    <form action="<?php echo $config['BaseURL']; ?>/execute.php?take=teleport" method="post" onsubmit="return OnTeleportSubmit(this);">
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
								<div style="display:inline-block; margin: 0 10px 0 4px;">
								<select styled="true" id="character-select" name="character" onchange="return OnCharacterSelect(this);">
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
                        
                        <input type="submit" value="teleport"/>
                    </form>
                    
                </div>
                <!-- Character Select Form.End -->
            </div>
            
      	</div>
      <!-- Account Activity.End -->
        
     </div>
	</div>
 
</div>
 
</div>

<?php

//Add to the loader
$TPL->AddFooterJs('template/js/jquery.easyTooltip.js');
$TPL->AddFooterJs('template/js/teleporter.js');
$TPL->AddFooterJs('template/js/northrend.js');
$TPL->AddFooterJs('template/js/kalimdor.js');
$TPL->AddFooterJs('template/js/eastern-kingdoms.js');
//Print the footer
$TPL->LoadFooter();

?>
