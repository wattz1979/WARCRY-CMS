<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->load_CoreModule('articles.base');

//Set the title
$TPL->SetTitle('Articles');
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

//Get them articles
$res = $DB->query("SELECT * FROM `articles` ORDER BY `id` DESC LIMIT 30;");
//Get the count
$Count = $res->rowCount();

?>

<!-- Main Side -->
<div class="main_side">

	<div class="articles">
    
    	<div class="header">
        	<h1><?php echo $Count; ?> Articles</h1>
            <!--<p>/</p>
            <h2><b>59</b> Comments</h2>-->
        </div>
    	
        <?php
		if ($Count > 0)
		{
			while ($arr = $res->fetch())
			{
				echo '
				<div class="article_short">
					<a class="title" href="', $config['BaseURL'], '/index.php?page=article&id=', $arr['id'], '">', Articles::parseTitle($arr['title']), '</a><br/><br/>
					<h4>', date('d M, Y', strtotime($arr['added'])), ' | ', $arr['views'], ' Views</h4>
					<p>', Articles::parseTitle($arr['short_text']), '</p>
					<a class="read_more" href="', $config['BaseURL'], '/index.php?page=article&id=', $arr['id'], '">Read More</a>
				</div>';
			}
		}
		else
		{
			echo 'There are no articles.';
		}
		?>
    	
    </div>
	    
    <div class="clear"></div>
    
</div>
<!-- Main side.End-->

<?php

unset($parser);

//include the sidebar
include $config['RootPath'] . '/template/sidebar.php';

?>

<div class="clear"></div>

</div>

<?php

$TPL->LoadFooter();

?>
