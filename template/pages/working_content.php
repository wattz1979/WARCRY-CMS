<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Set the title
$TPL->SetTitle('Working Content');
//CSS
$TPL->AddCSS('template/style/page-support-all.css');
//Print the header
$TPL->LoadHeader();

?>
<div class="content_holder">

 <div class="sub-page-title">
  <div id="title"><h1>Working Content<p></p><span></span></h1></div>
 </div>
 
  	<div class="container_2" align="center">
    
    	<div class="container_3" style="width:843px; padding:0; margin:30px 0 0 0;">
        	<!-- Working Content -->
             <div class="working-content" align="left">
             
	             	<div class="top-patch-info">
	                	<h2><p></p>Wrath of the Lich King <br/><i>Client version 3.3.5a (12340)</i></h2>
	                    <h5></h5>
	                </div>
                    
                  	
                    <!-- CLASSES -->
                    <div class="content-row">
                    	<h3>CLASSES</h3>
                        <div style="float:left; padding:0 35px 0 0;">
                    	<p><font color="#c41f3b">Death Knight</font> working 97% as intended</p>
						<p><font color="#ff7d0a">Druid</font> working 95% as intended</p>
						<p><font color="#abd473">Hunter</font> working 98% as intended</p>
						<p><font color="#69ccf0">Mage</font> working 98% as intended</p>
						<p><font color="#f58ca0">Paladin</font> working 96% as intended</p>
                        </div>
                        <div style="float:left">
						<p><font color="#ffffff">Priest</font> working 96% as intended</p>
						<p><font color="#fff569">Rogue</font> working 97% as intended</p>
						<p><font color="#0070de">Shaman</font> working 99% as intended</p>
						<p><font color="#948290">Warlock</font> working 97% as intended</p>
						<p><font color="#c79c61">Warrior</font> working 99% as intended</p>
                        </div>
                        <div class="clear"></div>
                    </div>
                    
                    <!-- BATTLEGROUNDS AND ARENAS -->
                    <div class="content-row">
                    	<h3>BATTLEGROUNDS AND ARENAS</h3>
                        <p><b style=" display:block; padding:0 0 10px 0;"><font color="#a69e93">Random Battlegrounds are enabled</font></b></p>
                        <div style="float:left; padding:0 35px 0 0;">
							<p><b><font color="#a69e93">Warsong Gulch</font></b> <font color="#5a6c33">is working</font></p>
	                        <p><b><font color="#a69e93">Eye of the Storm</font></b> <font color="#5a6c33">is working</font></p>
							<p><b><font color="#a69e93">Arathi Basin</font></b> <font color="#5a6c33">is working</font></p>
							<p><b><font color="#a69e93">Alterac Valley</font></b> <font color="#5a6c33">is working</font></p>
							<p><b><font color="#a69e93">Strand of the Ancients</font></b> <font color="#5a6c33">is working</font></p>
							<p><b><font color="#a69e93">Isle of Conquest</font></b> <font color="#5a6c33">is working</font></p>
                        <p><b><font color="#af3322">Wintergrasp is disabled</font></b></p>
                        </div>
                        <div style="float:left">
	                        <p><b><font color="#a69e93">Dalaran Arena</font></b> <font color="#5a6c33">is working</font></p>
							<p><b><font color="#a69e93">Blade's Edge Arena</font></b> <font color="#5a6c33">is working</font></p>
							<p><b><font color="#a69e93">Nagrand Arena</font></b> <font color="#5a6c33">is working</font></p>
							<p><b><font color="#a69e93">Ruins of Lordaeron</font></b> <font color="#5a6c33">is working</font></p>
							<p><b><font color="#a69e93">The Ring of Valor</font></b> <font color="#5a6c33">is working</font></p>
                        </div>
                        <div class="clear"></div>
                    </div>
                    
                    <!-- DUNGEONS AND RAIDS -->
                    <div class="content-row">
                    	<h3>DUNGEONS AND RAIDS</h3>
                        
                        <p><b style=" display:block; padding:0 0 10px 0;"><font color="#a69e93">Dungeon Finder is enabled</font></b></p>
                        <div style="float:left; width:50%;">
                        	<p><b><font color="#a69e93">Ruby Sanctum</font></b> <font color="#5a6c33">is available</font><i>(Fully scripted)</i></p>
                            <p>
                            	<b><font color="#a69e93">Icecrown</font></b> <font color="#5a6c33">is available</font>
                                <ul class="numbers-list">
                                	    <li><b>Lord Marrowgar</b> is fully scripted</li>
									    <li><b>Lady Deathwhisper</b> is fully scripted</li>
									    <li><font color="#af3322">Gunship Battle  is under development</font></li>
									    <li><b>Deathbringer Saurfang</b> is fully scripted</li>
									    <li><b>Festergut</b> is fully scripted</li>
									    <li><b>Rotface</b> is fully scripted</li>
									    <li><b>Professor Putricide</b> is fully scripted</li>
									    <li><b>Blood Prince Council encounter</b>  is fully scripted</li>
									    <li><b>Blood-Queen Lana'thel</b> is fully scripted</li>
									    <li><b>Valithria Dreamwalker</b> is fully scripted</li>
									    <li><b>Sindragosa</b> is fully scripted</li>
									    <li><b>The Lich King</b> is fully scripted</li>
                                </ul>
                            </p>
                            <p><b><font color="#a69e93">Vault of Archavon</font></b> <font color="#5a6c33">is available</font><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">Trial of the Grand Crusader</font></b> <font color="#5a6c33">is available</font><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">Onyxia's Lair</font></b> <font color="#5a6c33">is available</font><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">Ruby Sanctum</font></b> <font color="#5a6c33">is available</font><i>(Fully scripted)</i></p>
                            <p>
                            	<b><font color="#a69e93">Ulduar</font></b> <font color="#5a6c33">is available</font>
                                <ul class="numbers-list">
                                	    <li><b>Flame Leviathan</b> is fully scripted</li>
									    <li><b>Ignis the Furnace Master</b> is fully scripted</li>
									    <li><b>Razorscale</b> is fully scripted</li>
									    <li><b>XT-002 Deconstructor</b> is fully scripted</li>
									    <li><b>Assembly of Iron</b> is fully scripted</li>
									    <li><b>Kologarn</b> is fully scripted</li>
									    <li><b>Auriaya</b>  is fully scripted</li>
									    <li><b>Freya</b> is fully scripted</li>
									    <li><b>Hodir</b> is fully scripted</li>
                                        <li><font color="#af3322">Mimiron is under development</font></li>
									    <li><b>Thorim</b> is fully scripted</li>
                                        <li><b>General Vezax</b> is fully scripted</li>
                                        <li><b>Yogg-Saron</b> is fully scripted</li>
                                        <li><font color="#af3322">Algalon the Observer is under development</font></li>
                                </ul>
                            </p>
                            <p><b><font color="#a69e93">Eye of Eternity</font></b> <font color="#5a6c33">is available</font><i>(Fully scripted)</i></p>
                        </div>
                        <div style="float:left; width:40%; margin: 0 0 0 10% ;">
                        	<p><b><font color="#a69e93">Halls of Reflection</font></b><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">Pit of Saron</font></b><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">Forge of Souls</font></b><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">Trial of the Champion</font></b><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">Ahn'kahet: The Old Kingdom</font></b><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">Gundrak</font></b><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">The Violet Hold</font></b><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">Utgarde Pinnacle</font></b><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">Culling of Stratholme</font></b><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">The Oculus</font></b><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">The Nexus</font></b><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">Halls of Lightning</font></b><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">Halls of Stone</font></b><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">Drak'Tharon Keep</font></b><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">Azjol-Nerub</font></b><i>(Fully scripted)</i></p>
                            <p><b><font color="#a69e93">Utgarde Keep</font></b><i>(Fully scripted)</i></p>
                        </div>
                        <div class="clear"></div>
                        
                    </div>
            
         	 </div>
            <!-- Working Content.End -->
    	</div>
               
        
    </div>
    
</div>

<?php
	$TPL->LoadFooter();
?>