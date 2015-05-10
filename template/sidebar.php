<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

?>

<!-- Sidebar -->
<div class="sidebar">
	
    <!-- Banners -->
    	<div class="banners">
        	<a href="<?php echo $config['BaseURL']; ?>/forums.php?page=forum&id=15" id="support"><p></p></a>
            <a href="<?php echo $config['BaseURL']; ?>/index.php?page=downloads#launcher" id="launcher_dw"><p></p></a>
        </div>
    <!-- Banners . End -->

    <!-- REALMLIST -->
        <div class="realmlist container">
            <div class="light_normal">
            	set realmlist <font color="#6a5e4f">logon.warcry-wow.com</font>
            </div>
        </div>
    <!-- REALMLIST.End -->

  
  	<div class="index-status-container">

		<?php
        if (isset($realms_config))
        {
            foreach ($realms_config as $id => $data)
            {
                echo '<!-- REALM -->
                    <div class="realm_st">
                        <a href="index.php?page=realm-details&id=', $id, '">
                            <div class="realmst_head">
                                <div class="realm_name">
                                    <span id="realm-status-', $id, '">
                                        <script>
                                            $(function()
                                            {
                                                WarcryQueue(\'onload\').add(function()
                                                {
                                                    updateRealmStatus(', $id, ');
                                                });
                                            });
                                        </script>
                                    </span>
                                    ', $data['name'], '
                                </div>
                                <p class="realm-desc">', $data['descr'], '</p>
                            </div>
                        </a>
                    </div>
                <!-- REALM.End -->';
                
                unset($count, $stats);
            }
            unset($id, $data);
        }
        ?>
    	
        <script>
			//Update the teamspeak server status
			$(function()
			{
				WarcryQueue('onload').add(function()
				{
					updateTeamspeakStatus();
				});
			});
		</script>
        
    	<div class="ts-status">
        	<h3>TEAMSPEAK is <p class="status" id="teeamspeak-status"><font color="#313F09">Online</font></p></h3>
            <a href="http://www.teamspeak.com/?page=downloads" target="_blank" id="download-ts">Download TS3 Client</a>
            <a href="<?php echo $config['BaseURL']; ?>/index.php?page=howto&activate=1" id="download-htc">How to Connect</a>
        </div>
        
       	<div class="logon-status">
        	<div id="logon-status">
            	<script>
				$(function()
				{
					WarcryQueue('onload').add(function()
					{
						updateLogonStatus();
					});
				});
				</script>
            	<h3>LOGON Status: <br /><p class="status" id="logon-status2">Unknown</p></h3>
                <!--<p>2 days 2 hours 52 min Uptime</p>-->
            </div>
            <div id="server-time">
            	<script>
					ServerTimeCloack();
				</script>
            	<span>Server Time</span>
                <p id="server-time-cloack">00:00:00</p>
            </div>
        </div>
    </div>

    <div class="spotlight">
    	
        <?php
		
		if (!$CORE->isLoaded_CoreModule('articles.base'))
			$CORE->load_CoreModule('articles.base');
		
		$ArticlesLimit = 10;
		
		//Get the articles
		$res = $DB->prepare("SELECT `id`, `title`, `short_text`, `views`, `added`, `image` FROM `articles` ORDER BY `id` DESC LIMIT :limit;");
		$res->bindParam(':limit', $ArticlesLimit, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			echo '
			<div class="sub_header">
				<h1>Spotlight</h1>
			</div>
    
			<div class="blueberry">
				<ul class="slides">';
				
				$First = true;
				while ($arr = $res->fetch())
				{
					echo '
					<li', ($First ? ' style="position: relative"' : ' style="display: none"'), '>
						', ($arr['image'] != '' ? '<img src="./uploads/articles/'.$arr['image'].'" />' : ''), '
						<h1><a href="', $config['BaseURL'], '/index.php?page=article&id=', $arr['id'], '">', $arr['title'], '</a></h1>
						<h4>', date('d M, Y', strtotime($arr['added'])), ' | ', $arr['views'], ' Views | ', Articles::getCommentsCount($arr['id']), ' Comments</h4>
						<p>', htmlspecialchars(stripslashes($arr['short_text'])), '</p>
					</li>';
					
					if ($First)
						$First = false;
				}
				unset($arr, $First);
				
				echo '
				</ul>
				
				<!-- Optional, see options below -->
				<ul class="pager">';
					
					//Set the buttons for the slides
					for ($i = 0; $i < $res->rowCount(); $i++)
						echo '<li><a href="#"><span></span></a></li>';
				
				echo '
				</ul>
				<!-- Optional, see options below -->
				
			</div>';
			
			//Initialize Blueberry only if we have more then one article
			if ($res->rowCount() > 1)
			{
				echo '
				<script>
					$(window).load(function()
					{
						$(\'.blueberry\').blueberry();
					});
				</script>';
			}
		}
		unset($res, $ArticlesLimit);
		
		?>
        
	</div>
    
</div>

<?php
$TPL->AddFooterJs('template/js/jquery.blueberry.js');
?>

<!-- Sidebar.End -->