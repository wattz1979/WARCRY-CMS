<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->load_CoreModule('forums.parser');

$id = (isset($_GET['id']) ? (int)$_GET['id'] : false);
$error = false;

//Set the title
$TPL->SetTitle('News');
//CSS
$TPL->AddCSS('template/style/page-news.css');
//Print the header
$TPL->LoadHeader();

//if we dont have news ID find the latest one
if (!$id)
{
	$res = $DB->query("SELECT id FROM `news` ORDER BY `added` DESC LIMIT 1;");
	//if we have any news at all
	if ($res->rowCount() > 0)
	{
		$row = $res->fetch();
		//define the new id
		$id = $row['id'];
		unset($row);
	}
	else
	{
		$error = '<p class="there-is-nothing">There are no news.</p>';
	}
	unset($res);
}

//get the news record
$res = $DB->prepare("SELECT * FROM `news` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $id, PDO::PARAM_INT);
$res->execute();

//if we have any news at all
if ($res->rowCount() > 0)
{
	//fetch the record
	$row = $res->fetch();
}
else
{
	$error = '<p class="there-is-nothing">No records available.</p>';
}
unset($res);
?>
<div class="content_holder">

 <div class="sub-page-title">
  <div id="title"><h1>News<p></p><span></span></h1></div>
 </div>
 
  	<div class="container_2" align="center">
    
    	<div class="container_3 archived-news">
        
        	<!-- News Content -->
    	  		
                <?php
				//check if we got error
				
				if ($error)
				{
					echo $error;
				}
				else
				{
					//get posted by string by id
					$res = $DB->prepare("SELECT `displayName` FROM `account_data` WHERE `id` = :id LIMIT 1;");
					$res->bindParam(':id', $row['author'], PDO::PARAM_INT);
					$res->execute();
					
					if ($res->rowCount() == 0)
					{
						$author = $row['authorStr'];
					}
					else
					{
						$authorRow = $res->fetch();
						$author = $authorRow['displayName'];
					}
					unset($res);
					
					//Check the cache
					if (($text = $CACHE->get('news/news_' . $row['id'])) === false)
					{
						// create the BBCode parser
						$parser = new SBBCodeParser_Document(true, false);
						//Strip slashes
						$text = stripslashes($row['text']);
						//Parse
						$text = $parser->parse($text)->detect_links()->detect_emails()->detect_emoticons()->get_html(true);
						//fix multiple break lines
						$text = preg_replace("/<br\s*\/?>\s<br\s*\/?>\s+/", "<br/>", $text);
						
						unset($parser);
						
						//Store the parsed post in the cache for a month
						$CACHE->store('news/news_' . $row['id'], $text, "2592000");
					}
						
                	echo '
			        <div class="arnews-head" align="left">
			        	<h1>', stripslashes($row['title']), '</h1>
			            ', $CORE->convertDataTime($row['added']), ', posted by ', $author, '
			        </div>
			        
			        <div class="arnews-cont" align="left">
						', $text, '<div class="clear"></div>
			        </div>';
					
					unset($text);
				}
				
				?>
                
            <!-- News Content.End -->
            
    	</div>
        
        <!-- News Navigation -->
        
        	<?php
			
			//find newer
			$res = $DB->prepare("SELECT id FROM `news` WHERE `id` > :id ORDER BY `id` ASC LIMIT 1;");
			$res->bindParam(':id', $id, PDO::PARAM_INT);
			$res->execute();
			//fetch
			$newer = $res->fetch();
			unset($res);

			//find newer
			$res = $DB->prepare("SELECT id FROM `news` WHERE `id` < :id ORDER BY `id` DESC LIMIT 1;");
			$res->bindParam(':id', $id, PDO::PARAM_INT);
			$res->execute();
			//fetch
			$older = $res->fetch();
			unset($res);
			
			if ($newer)
			{
        		echo '<a class="newer-news-btn" href="', $config['BaseURL'], '/index.php?page=news&id=', $newer['id'], '">Newer</a>';
			}
			if ($older)
			{
            	echo '<a class="older-news-btn" href="', $config['BaseURL'], '/index.php?page=news&id=', $older['id'], '">Older</a>';
			}
			
			?>
            
            <div class="clear"></div>
        <!-- News Navigation.End -->
        
    </div>
    
</div>

</div>

<?php
	unset($parser);

	$TPL->LoadFooter();
?>