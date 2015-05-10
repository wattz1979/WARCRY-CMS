<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

$CORE->load_CoreModule('raf');
$raf = new RAF();

//Set the title
$TPL->SetTitle('Account Panel');
//Print the header
$TPL->LoadHeader();

?>
<div class="content_holder">

    <div class="sub-page-title">
    	<div id="title">
        	<h1>Account Panel<p></p><span></span></h1>
        </div>
        <?php
		if (isset($realms_config) && count($realms_config) > 1)
		{
			echo '
			<div style="float: right; margin-top: 22px; margin-right: 10px;">
				<form action="execute.php?take=set_realm" method="post">
					<select name="realm" styled="true" onchange="this.form.submit()">
						<option value="0">Select operating realm</option>';
						
						foreach ($realms_config as $id => $realm)
						{
							echo '<option value="', $id, '">', $realm['name'], '</option>';
						}
						unset($id, $realm);
						
					echo '
					</select>
				</form>
			</div>';
		}
		?>
    </div>
 
  	<div class="container_2 account" align="center">
     	<div class="cont-image">
        	
            <!-- Errors -->
            <?php
			if ($error = $ERRORS->DoPrint('setrealm'))
			{
				echo $error, '<br><br>';
			}			
			if ($success = $ERRORS->successPrint('setrealm'))
			{
				echo $success, '<br><br>';
			}	
			unset($error, $success);	
			?>
            
    		<!-- Main Account info -->
        	<div class="container_3 account_light_cont account_info_cont" align="left">
         		<div class="account_info" align="left">
         	
            		<?php
					echo '
					<ul class="account_avatar">
						<li id="avatar"><span style="background:url(', ($CURUSER->getAvatar()->type() == AVATAR_TYPE_GALLERY ? './resources/avatars/'.$CURUSER->getAvatar()->string() : $CURUSER->getAvatar()->string()), ') no-repeat; background-size: 100%;"></span><p></p></li>
						<li id="change_avatar"><a href="', $config['BaseURL'],'/index.php?page=avatars">Change your Avatar</a></li>
					</ul>
			
					<ul class="account_info_main">
						<li id="displayname"><span>Display name:</span><p>', $CURUSER->get('displayName'), '</p></li>
						<li id="rank"><span>Rank:</span><p>', $CURUSER->getRank()->string(), '</p></li>
						<li><span>Username:</span><p>', $CURUSER->get('username'), '</p></li>
						<li><span>Email:</span><p>', $CURUSER->get('email'), '</p></li>
						<li id="gcoins"><span>Gold Coins:</span><div></div><p>', $CURUSER->get('gold'), '</p></li>
						<li id="scoins"><span>Silver Coins:</span><div></div><p>', $CURUSER->get('silver'), '</p></li>
					</ul>
					
					<ul class="account_info_second">
						<li><span>Referred members:</span><p><a href="', $config['BaseURL'], '/index.php?page=recruit-a-friend">', $raf->GetReferralsCount($CURUSER->get('id')),'</a></p></li>
						<br/>
						<li><span>Last login:</span><p>', $CURUSER->get('last_login'), '</p></li>
						<li><span>Last IP Address:</span><p>', $CURUSER->get('last_ip'), '</p></li>
						<br/>
						<li><span>Registration date:</span><p>', $CURUSER->get('joindate'), '</p></li>
						<br/>
						<li><span>Operating realm:</span><p>', $realms_config[$CURUSER->GetRealm()]['name'], '</p></li>
					</ul>';
					?>
                
            		<div class="clear"></div>
         		</div>
        	</div>
        	<!-- Main Account info.End -->
        
        	<!-- Main Account menu -->
        	<ul id="accoun_panel_menu">
        
        		<li id="boost"><a href="<?php echo $config['BaseURL'], '/index.php?page=boosts'; ?>"><p></p></a></li>
        
                <li>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=store'; ?>">
                     <div id="icon" style="background-image:url(http://wow.zamimg.com/images/wow/icons/large/inv_misc_bag_10_black.jpg);"></div>
                     <span>
                      <p>Store</p>
                      Spend your coins here.
                     </span>
                    </a>
                </li>
            
                <li>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=vote'; ?>">
                     <div id="icon" style="background-image:url(http://wow.zamimg.com/images/wow/icons/large/inv_crate_04.jpg);"></div>
                     <span>
                      <p>Vote</p>
                      Vote for us and earn silver coins.
                     </span>
                    </a>
                </li>
            
                <li>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=buycoins'; ?>">
                     <div id="icon" style="background-image: url(./template/style/images/misc/coins_icon.jpg);"></div>
                     <span>
                      <p>Buy Coins</p>
                      Purchase gold coins.
                     </span>
                    </a>
                </li>
            
                <li>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=teleporter'; ?>">
                     <div id="icon" style="background-image:url(http://wow.zamimg.com/images/wow/icons/large/inv_misc_rune_05.jpg);"></div>
                     <span>
                      <p>Teleporter</p>
                      Teleport your characters.
                     </span>
                    </a>
                </li>
                
                <li>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=unstuck'; ?>">
                     <div id="icon" style="background-image:url(http://wow.zamimg.com/images/wow/icons/large/inv_misc_rune_01.jpg);"></div>
                     <span>
                      <p>Unstuck</p>
                      Unstuck and revive your charcaters.
                     </span>
                    </a>
                </li>           
                
                <li>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=itemsets'; ?>">
                     <div id="icon" style="background-image:url(http://wow.zamimg.com/images/wow/icons/large/inv_chest_robe_raidpriest_k_01.jpg);"></div>
                     <span>
                      <p>Armor Sets</p>
                      Leveling and end-game item sets.
                     </span>
                    </a>
                </li>
            
                <li>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=levels'; ?>">
                     <div id="icon" style="background-image:url(http://wow.zamimg.com/images/wow/icons/large/spell_holy_divineprovidence.jpg);"></div>
                     <span>
                      <p>Level up</p>
                      Level up your character instantly.
                     </span>
                    </a>
                </li>
                
                <li>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=purchase_gold'; ?>">
                     <div id="icon" style="background-image:url(http://wow.zamimg.com/images/wow/icons/large/inv_misc_coin_02.jpg);"></div>
                     <span>
                      <p>In-Game Gold</p>
                      Purchase gold for your character.
                     </span>
                    </a>
                </li>
                
                <li>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=factionchange'; ?>">
                     <div id="icon" style="background-image:url(./template/style/images/misc/faction_change_icon.jpg);"></div>
                     <span>
                      <p>Faction Change</p>
                      Change your charcater faction.
                     </span>
                    </a>
                </li>
            
                <li>
                    <a href="<?php echo $config['BaseURL'], '/index.php?page=recustomization'; ?>">
                     <div id="icon" style="background-image:url(http://wow.zamimg.com/images/wow/icons/large/race_human_male.jpg);"></div>
                     <span>
                      <p>Re-Customization</p>
                      Change your character look.
                     </span>
                    </a>
                </li>
            
            </ul>
            <!-- Main Account menu.End -->
        
        	<!-- Quick account menu -->
	        <ul class="quick_acc_menu">
            	<li class="special"><a href="<?php echo $config['BaseURL'], '/index.php?page=pcode';?>">Promotion Codes<p></p><span></span></a></a>
            	<li><a href="<?php echo $config['BaseURL'], '/index.php?page=recruit-a-friend';?>">Recruit a Friend<p></p><span></span></a></a>
            	<li><a href="<?php echo $config['BaseURL'], '/index.php?page=items_refund'; ?>">Refund Items<p></p><span></span></a></li>
	        	<li><a href="<?php echo $config['BaseURL'], '/index.php?page=changepass'; ?>">Change password<p></p><span></span></a></li>
	            <li><a href="<?php echo $config['BaseURL'], '/index.php?page=changemail'; ?>">Change email<p></p><span></span></a></li>
	            <li><a href="<?php echo $config['BaseURL'], '/index.php?page=changedname'; ?>">Change display name<p></p><span></span></a></li>
                <li><a href="<?php echo $config['BaseURL'], '/index.php?page=acactivity'; ?>">Account activity<p></p><span></span></a></li>
	            <li><a href="<?php echo $config['BaseURL'], '/index.php?page=sactivity'; ?>">Store activity<p></p><span></span></a></li>
	            <li><a href="<?php echo $config['BaseURL'], '/index.php?page=cactivity'; ?>">Coins activity<p></p><span></span></a></li>
	        </ul>
	    	<!-- Quick account menu.End -->
        
        	<div class="clear"></div>
        
     	</div>
	</div>
 
</div>

<?php
	unset($raf);

	$TPL->LoadFooter();
?>