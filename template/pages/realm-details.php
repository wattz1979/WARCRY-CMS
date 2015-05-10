<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$RealmId = isset($_GET['id']) ? (int)$_GET['id'] : $CURUSER->GetRealm();

//load Realm Stats module
$CORE->load_ServerModule('realm.stats');

$stats = new server_RealmStats();
$stats->setRealm($RealmId);
$stats->prepareUptimeRow();

//get the characters online count
$count = $stats->getOnline();

//Set the title
$TPL->SetTitle('Fury Realm Details');
//CSS
$TPL->AddCSS('template/style/realm_details.css');
//Print the header
$TPL->LoadHeader();

?>
<div class="content_holder">

  	<div class="container_2 realm-details" align="center">
    
    	<!-- REALM TOP INFO -->
        	<div class="realm_main_info">
            	<h1>
                	Fury
                	<div class="overlay_1"></div>
                    <div class="overlay_2"></div>
               	</h1>
                <h2>
                	Wrath of the Lich King
                    <div class="overlay_1"></div>
                    <div class="overlay_2"></div>
                    <div>
                    	Blizzlike Content, High Rate Realm, XP Rate Changer
                        <div class="overlay_1"></div>
                    </div>
                </h2>
                <img id="expansion" alt="Wrath of the Lich King" name="Wrath of the Lich King Logo" src="./template/style/images/misc/realm_expansion_wotlk.png" />
            </div>
        <!-- REALM TOP INFO.End -->
        
        <!-- REALM Status bar -->
        	<div class="realm_staus_info">
            	
                <div class="realm_status">
                	<script type="text/javascript">
						//ajax update status
						$(document).ready(function()
						{
							var $this = $('#realm-status');
							var $realm = <?php echo $RealmId; ?>;
							
							$.get(
								"ajax.php?phase=19&id=1", 
								{ 
									id: $realm,
								},
								function(data){
									if (data == '1'){
										$this.addClass('online');
										$this.find('#status-text').html('Online');
									}else{
										$this.addClass('offline');
										$this.find('#status-text').html('Offline');
									}
								}
							);	
						});
                    </script>
                    <h1 class="status" id="realm-status">
                        <span id="status-text">Unknown</span>
                        <div class="overlay_1"></div>
                    </h1>
                    <h2>
                        <?php echo $stats->getUptime(); ?> Uptime
                        <div class="overlay_1"></div>
                    </h2>
                </div>
                
                <div class="realm_online_players">
                	<h1>
                    	<?php echo $count['total']; ?><p>Players</p>
                        <div class="overlay_1"></div>
                    </h1>
                    <h2>
                    	<?php echo $count['alliance']; ?> Alliance and <?php echo $count['horde']; ?> Horde
                        <div class="overlay_1"></div>
                   	</h2>
                </div>
                
            </div>
        <!-- REALM Status bar.End -->
        
        <!-- REALM Info -->
        	<div class="realm_info">
            	<span>
                	<h1>
                    	Realm Information
                        <a href="index.php?page=features">Features</a>
                        <a href="index.php?page=working_content">Working Content</a>
                    </h1>
                    <h2>
                    Experience Warth of the Lich King in a much faster pased  environment with our High Rate Realm, Rage! Do you rather level a bit slower? No problem! We've developed our own <a href="index.php?page=featured-addons">AddOn</a> to help you change your Experience Rates. You can choose from 1x the regular XP rate all the way up to 15 times.
                    <br/><br/>
                    Rage is in constant development and issues are being resolved and working content is added all the time. Visit our Working Content page for a full overview of all the content available. 
                    </h2>
                </span>
            </div>
        <!-- REALM Info.End -->
        
        <?php
		//Start of IF DETAILS
		if ($details = $stats->GetRealmDetails())
		{
			//Collective total
			$details->total = (int)$details->alliance + (int)$details->horde;
			?>
        
            <!-- REALM STATISTICS -->
                <div class="realm_statistics">
                    
                    <!-- Faction Balance -->
                    <div class="statistic_holder" style="margin:0 0 0 5px;">
                        <h1 class="head_info">
                            <p>Faction Balance</p>
                            <span>A quick overview of the current balance between the Horde and Alliance.</span>
                            
                            <?php
							//Calculate percentage
							$AlliancePercent = $CORE->percent((int)$details->alliance, $details->total);
							$HordePercent = $CORE->percent((int)$details->horde, $details->total);
							
							echo '
								<div class="alliance_horde_statistics">
									<div class="faction_bars_case">
										
										<div class="alliance_bar faction_bar" style="height:', $AlliancePercent + 20, '%"><h1>', (int)$details->alliance, '</h1><h2>characters</h2><h3>', $AlliancePercent, '% Alliance</h3><div class="grad"></div></div>
										<div class="horde_bar faction_bar" style="height:', $HordePercent + 20, '%"><h1>', (int)$details->horde, '</h1><h2>characters</h2><h3>', $HordePercent, '% Horde</h3><div class="grad"></div></div>
									</div>
									<div class="all_characters">
										<h1>', $details->total, ' Characters</h1>
									</div>
								</div>';
								
							unset($AlliancePercent, $HordePercent);
                            ?>
                            
                        </h1>
                    
                    </div>
                    
                        <!-- Seperator --><div class="stats-seperator"></div>
                    
                    <!-- Race Balance -->
                    <div class="statistic_holder">
                        <h1 class="head_info">
                            <p>Race Balance</p>
                            <span>A quick overview of the current race balance on a per race basis.</span>
                        </h1>
                        
                        <div class="race_class_stats">
                            
                            <?php
								//Calculate percentage
								$bloodelfPct = $CORE->percent((int)$details->bloodelfs, $details->total);
								$draeneiPct = $CORE->percent((int)$details->draeneis, $details->total);
								$dwarfPct = $CORE->percent((int)$details->dwarfs, $details->total);
								$gnomePct = $CORE->percent((int)$details->gnomes, $details->total);
								$humanPct = $CORE->percent((int)$details->humans, $details->total);
								$nightelfPct = $CORE->percent((int)$details->nightelfs, $details->total);
								$orcPct = $CORE->percent((int)$details->orcs, $details->total);
								$taurenPct = $CORE->percent((int)$details->taurens, $details->total);
								$trollPct = $CORE->percent((int)$details->trolls, $details->total);
								$undeadPct = $CORE->percent((int)$details->undeads, $details->total);
							
								echo '
								<!-- Blood Elfs -->
								<div class="bar_row">
									<div class="scale" style="width:', $bloodelfPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/race_bloodelf_male.jpg);"></div>
										<span>', $bloodelfPct, '%</span>
									</div>
									<h1>', (int)$details->bloodelfs, ' <span>Blood Elfs</span></h1>
								</div>
								
								<!-- Draeneis -->
								<div class="bar_row">
									<div class="scale" style="width:', $draeneiPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/race_draenei_male.jpg);"></div>
										<span>', $draeneiPct, '%</span>
									</div>
									<h1>', (int)$details->draeneis, ' <span>Draeneis</span></h1>
								</div>
								
								<!-- Dwarfs -->
								<div class="bar_row">
									<div class="scale" style="width:', $dwarfPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/race_dwarf_male.jpg);"></div>
										<span>', $dwarfPct, '%</span>
									</div>
									<h1>', (int)$details->dwarfs, ' <span>Dwarfs</span></h1>
								</div>
								
								<!-- Gnomes -->
								<div class="bar_row">
									<div class="scale" style="width:', $gnomePct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/race_gnome_male.jpg);"></div>
										<span>', $gnomePct, '%</span>
									</div>
									<h1>', (int)$details->gnomes, ' <span>Gnomes</span></h1>
								</div>
								
								<!-- Humans -->
								<div class="bar_row">
									<div class="scale" style="width:', $humanPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/race_human_male.jpg);"></div>
										<span>', $humanPct, '%</span>
									</div>
									<h1>', (int)$details->humans, ' <span>Humans</span></h1>
								</div>
								
								<!-- Night Elfs -->
								<div class="bar_row">
									<div class="scale" style="width:', $nightelfPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/race_nightelf_male.jpg);"></div>
										<span>', $nightelfPct, '%</span>
									</div>
									<h1>', (int)$details->nightelfs, ' <span>Night Elfs</span></h1>
								</div>
								
								<!-- Orcs -->
								<div class="bar_row">
									<div class="scale" style="width:', $orcPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/race_orc_male.jpg);"></div>
										<span>', $orcPct, '%</span>
									</div>
									<h1>', (int)$details->orcs, ' <span>Orcs</span></h1>
								</div>
								
								<!-- Taurens -->
								<div class="bar_row">
									<div class="scale" style="width:', $taurenPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/race_tauren_male.jpg);"></div>
										<span>', $taurenPct, '%</span>
									</div>
									<h1>', (int)$details->taurens, ' <span>Taurens</span></h1>
								</div>
								
								<!-- Trolls -->
								<div class="bar_row">
									<div class="scale" style="width:', $trollPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/race_troll_male.jpg);"></div>
										<span>', $trollPct, '%</span>
									</div>
									<h1>', (int)$details->trolls, ' <span>Trolls</span></h1>
								</div>
								
								<!-- Undeads -->
								<div class="bar_row">
									<div class="scale" style="width:', $undeadPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/race_scourge_male.jpg);"></div>
										<span>', $undeadPct, '%</span>
									</div>
									<h1>', (int)$details->undeads, ' <span>Undeads</span></h1>
								</div>';
								
								unset($bloodelfPct, $draeneiPct, $dwarfPct, $gnomePct, $humanPct, $nightelfPct, $orcPct, $taurenPct, $trollPct, $undeadPct);
							?>
                            
                        </div>
                    
                    </div>
                    
                        <!-- Seperator --><div class="stats-seperator"></div>
                    
                    <!-- Class Balance -->
                    <div class="statistic_holder">
                        <h1 class="head_info">
                            <p>Class Balance</p>
                            <span>A quick overview of the current class balance on a per class basis.</span>
                        </h1>
                        
                        <div class="race_class_stats">
                            
                            <?php
								//Calculate percentage
								$deathknightPct = $CORE->percent((int)$details->deathknights, $details->total);
								$druidPct = $CORE->percent((int)$details->druids, $details->total);
								$hunterPct = $CORE->percent((int)$details->hunters, $details->total);
								$magePct = $CORE->percent((int)$details->mages, $details->total);
								$paladinPct = $CORE->percent((int)$details->paladins, $details->total);
								$priestPct = $CORE->percent((int)$details->priests, $details->total);
								$roguePct = $CORE->percent((int)$details->rogues, $details->total);
								$shamanPct = $CORE->percent((int)$details->shamans, $details->total);
								$warlockPct = $CORE->percent((int)$details->warlocks, $details->total);
								$warriorPct = $CORE->percent((int)$details->warriors, $details->total);
                           
								echo '
								<!-- Death Knights -->
								<div class="bar_row">
									<div class="scale dk" style="width:', $deathknightPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/class_deathknight.jpg);"></div>
										<span>', $deathknightPct, '%</span>
									</div>
									<h1>', (int)$details->deathknights, ' <span>Death Knights</span></h1>
								</div>
								
								<!-- Druids -->
								<div class="bar_row">
									<div class="scale dr" style="width:', $druidPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/class_druid.jpg);"></div>
										<span>', $druidPct, '%</span>
									</div>
									<h1>', (int)$details->druids, ' <span>Druids</span></h1>
								</div>
								
								<!-- Hunters -->
								<div class="bar_row">
									<div class="scale ht" style="width:', $hunterPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/class_hunter.jpg);"></div>
										<span>', $hunterPct, '%</span>
									</div>
									<h1>', (int)$details->hunters, ' <span>Hunters</span></h1>
								</div>
								
								<!-- Mages -->
								<div class="bar_row">
									<div class="scale mg" style="width:', $magePct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/class_mage.jpg);"></div>
										<span>', $magePct, '%</span>
									</div>
									<h1>', (int)$details->mages, ' <span>Mages</span></h1>
								</div>
								
								<!-- Paladins -->
								<div class="bar_row">
									<div class="scale pl" style="width:', $paladinPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/class_paladin.jpg);"></div>
										<span>', $paladinPct, '%</span>
									</div>
									<h1>', (int)$details->paladins, ' <span>Paladins</span></h1>
								</div>
								
								<!-- Priests -->
								<div class="bar_row">
									<div class="scale pr" style="width:', $priestPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/class_priest.jpg);"></div>
										<span>', $priestPct, '%</span>
									</div>
									<h1>', (int)$details->priests, ' <span>Priests</span></h1>
								</div>
								
								<!-- Rogues -->
								<div class="bar_row">
									<div class="scale rg" style="width:', $roguePct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/class_rogue.jpg);"></div>
										<span>', $roguePct, '%</span>
									</div>
									<h1>', (int)$details->rogues, ' <span>Rogues</span></h1>
								</div>
								
								<!-- Shamans -->
								<div class="bar_row">
									<div class="scale sh" style="width:', $shamanPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/class_shaman.jpg);"></div>
										<span>', $shamanPct, '%</span>
									</div>
									<h1>', (int)$details->shamans, ' <span>Shamans</span></h1>
								</div>
								
								<!-- Warlocks -->
								<div class="bar_row">
									<div class="scale wl" style="width:', $warlockPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/class_warlock.jpg);"></div>
										<span>', $warlockPct, '%</span>
									</div>
									<h1>', (int)$details->warlocks, ' <span>Warlocks</span></h1>
								</div>
								
								<!-- Warriors -->
								<div class="bar_row">
									<div class="scale wr" style="width:', $warriorPct + 20, '%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/class_warrior.jpg);"></div>
										<span>', $warriorPct, '%</span>
									</div>
									<h1>', (int)$details->warriors, ' <span>Warriors</span></h1>
								</div>';
								
								unset($deathknightPct, $druidPct, $hunterPct, $magePct, $paladinPct, $priestPct, $roguePct, $shamanPct, $warlockPct, $warriorPct);
							?>
                            
                        </div>
                    
                    </div>
                    
                            <div class="clear"></div>
                    
                    <!-- Some info -->
                    <div class="statistics_note">
                        <h3>Statistics displayed on this page do not include characters below level 10 and are automatically updated once a day.</h3>
                    </div>
                    <br/><br/>
                    
                    
                </div>
            <!-- REALM STATISTICS -->
        
        <?php
		}
		//End of IF DETAILS
		?>
        
    </div>
    
</div>

<?php
	$TPL->LoadFooter();
?>