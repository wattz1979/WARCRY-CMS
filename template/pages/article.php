<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//load the pagination module
$CORE->load_CoreModule('paginationType2');
$CORE->load_CoreModule('articles.base');
$CORE->load_CoreModule('forums.parser');

$ArticleID = isset($_GET['id']) ? (int)$_GET['id'] : false;
$p = (isset($_GET['p']) ? (int)$_GET['p'] : 1);

//Make sure we have article id
if (!$ArticleID)
{
	Articles::SetupNotification('The selected article seems to be invalid.');
	header("Location: ".$config['BaseURL']."/index.php?page=articles");
	die;
}

//Try to find the article record
$res = $DB->prepare("SELECT * FROM `articles` WHERE `id` = :id LIMIT 1;");
$res->bindParam(':id', $ArticleID, PDO::PARAM_INT);
$res->execute();

//Verify the record is found
if ($res->rowCount() == 0)
{
	Articles::SetupNotification('The selected article seems to be invalid.');
	header("Location: ".$config['BaseURL']."/index.php?page=articles");
	die;
}

//Fetch the record
$row = $res->fetch();

//format the title
$row['title'] = Articles::parseTitle($row['title']);

//Register a view of the article
Articles::RegisterView($row['id']);
//Update runtime views
$row['views']++;

//Set the title
$TPL->SetTitle($row['title']);
$TPL->SetParameter('topbar', true);
//CSS
$TPL->AddCSS('template/style/page-articles.css');
//Print the header
$TPL->LoadHeader();

?>

<div class="content_holder">

<?php

if ($config['IMPORTANT_NOTICE']['ENABLE'] == true)
{
	echo '
	<div class="important_notice">
		<p>'. $config['IMPORTANT_NOTICE']['MESSAGE'] .'</p>
	</div>';
}

?>

<!-- Main Side -->
<div class="main_side">

	<div class="article_top">
    	<a id="all_articles" href="<?php echo $config['BaseURL']; ?>/index.php?page=articles">See all Articles</a>
        <?php
			//Lookup next article id
			if ($NextArticle = Articles::getNextArticle($row['id']))
			{
				echo '<a id="next_article" href="', $config['BaseURL'], '/index.php?page=article&id=', $NextArticle['id'], '">Next Article</a>';
			}
		?>
    </div>

	<div class="article">
    	<h1 id="title"><?php echo $row['title']; ?></h1>
        <h5 id="subinfo"><b><?php echo date('d M, Y', strtotime($row['added'])); ?></b><?php echo $row['views']; ?> Views &nbsp;&nbsp;&nbsp; <?php echo Articles::getCommentsCount($row['id']); ?> Comments</h5>
        <p id="post">
         	<?php
				if (($text = $CACHE->get('articles/article_' . $row['id'])) === false)
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
					$CACHE->store('articles/article_' . $row['id'], $text, "2592000");
				}
				
				//Print the text
				echo $text;
			?>
            <br/><br/>
        </p>
        
        <div class="comments">
        	
            <?php
			//Check if comments are enabled
			if ($row['comments'] == '1')
			{
				if (!$CURUSER->isOnline())
				{
					echo '
					<!-- if not logged in -->
					<div class="not_login">You must be logged in to post comment.</div>';
				}
				else
				{
					echo '
					<!-- if logged in -->
						<div class="post_comment">
							<form method="post" action="#" id="quick-comment">
								<textarea placeholder="Type in your comment..." name="text" id="textarea"></textarea>
								<input type="hidden" value="', $row['id'], '" name="article" />
								<input type="submit" value="Post comment" />
							</form>
						</div>
					 <!-- if logged in.end -->';
				}
			}
			?>
            
            <div class="comments-cont">
            	
                <?php
					//Let's setup our pagination
					$pagies = new Pagination();
					$pagies->addToLink('?page='.$pageName.'&id='.$ArticleID);

					$perPage = 10;

					//count the total records
					$res = $DB->prepare("SELECT COUNT(*) FROM `article_comments` WHERE `article` = :id;");
					$res->bindParam(':id', $ArticleID, PDO::PARAM_INT);
					$res->execute();
					
					$count_row = $res->fetch(PDO::FETCH_NUM);
					$count = $count_row[0];
								
					unset($count_row);
					unset($res);
					
					//Pull the comments
					if ($count > 0)
					{
						//calculate the pages
						$pages = $pagies->calculate_pages($count, $perPage, $p);
						
						//get the activity records
						$res = $DB->prepare("SELECT 
												`article_comments`.`id`, 
												`article_comments`.`added`, 
												`article_comments`.`author`, 
												`article_comments`.`article`, 
												`article_comments`.`text`, 
												`account_data`.`displayName` AS `author_str` 
											FROM `article_comments` 
											LEFT JOIN `account_data` ON `account_data`.`id` = `article_comments`.`author` 
											WHERE `article_comments`.`article` = :id 
											ORDER BY `article_comments`.`id` DESC 
											LIMIT ".$pages['limit'].";");
						$res->bindParam(':id', $ArticleID, PDO::PARAM_INT);
						$res->execute();
						
						//loop the records
						while ($arr = $res->fetch())
						{
							echo '
							<div class="comment_row" data-id="', $arr['id'], '">
								<div class="headline">
									<p><a href="', $config['BaseURL'], '/index.php?page=profile&uid=', $arr['author'], '">', $arr['author_str'], '</a> said:</p>
									<span id="time" data-original="', $arr['added'], '">NaN</span>
								</div>
								<p class="comment">', Articles::parseTitle($arr['text']), '</p>
							</div>';
						}
					}
					unset($arr, $res);

				?>
                
           	</div>
            
            <?php
				//Pagination
				if ($count > 0 and $count > $perPage)
				{
					echo '
					<!-- Pagination -->
					<br>
					<div class="d-cont pagination-holder">
						<ul class="pagination" id="store-pagination" style="padding-right: 10px">
						
							', $pages['first'], '
							', $pages['previous'], '
								
							', $pages['info'], '
								
							', $pages['next'], '
							', $pages['last'], '
												
						</ul>
						<div class="clear"></div>
					</div>';
				}
			?>
            
        </div>
        
    </div>
	    
    <div class="clear"></div>
    
    <script type="text/javascript">
	$(document).ready(function()
	{
		Article.ArticleID = <?php echo $ArticleID; ?>;
		Article.PerPage = <?php echo $perPage; ?>;
		Article.BindHandlers();
		Article.UpdateTimespans();
	});
	</script>
    
</div>
<!-- Main side.End-->

<?php

unset($ArticleID, $pages, $pagies, $count);

//include the sidebar
include $config['RootPath'] . '/template/sidebar.php';

?>

<div class="clear"></div>

</div>

<?php

//Load JS
$TPL->AddFooterJs('template/js/humanized.time.js');
$TPL->AddFooterJs('template/js/page.article.js');
//Print footer
$TPL->LoadFooter();

?>
