<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->load_CoreModule('forums.parser');

//Set the title
$TPL->SetTitle('Video Preview');
//CSS
$TPL->AddCSS('template/style/page-media.css');
//Print the header
$TPL->LoadHeader();

$movieId = isset($_GET['id']) ? (int)$_GET['id'] : false;

?>

<div class="content_holder">
    <div class="sub-page-title">
    	<div id="title"><h1>Media<p></p><span></span></h1></div>
    </div>
	<div class="container_2" align="center" style="padding:30px 75px; width:846px;">
     
        <div class="media-header">
            <h2>VIDEOS</h2>
            <div class="clear"></div>
            <div class="bline"></div>
        </div>
   
		<?php
			//Check if the movie ID is set and valid
            if (!$movieId)
            {
				$IdError = true;
            }
            else
            {
				$res = $DB->prepare("SELECT * FROM `movies` WHERE `id` = :id LIMIT 1;");
				$res->bindParam(':id', $movieId, PDO::PARAM_INT);
				$res->execute();
				
				if ($res->rowCount() > 0)
				{
					//fetch
					$row = $res->fetch();
					
					if (($text = $CACHE->get('media/movies/movie_' . $row['id'])) === false)
					{
						// create the BBCode parser
						$parser = new SBBCodeParser_Document(true, false);
						//Strip slashes
						$text = stripslashes($row['descr']);
						//Parse
						$text = $parser->parse($text)->detect_links()->detect_emails()->detect_emoticons()->get_html(true);
						//fix multiple break lines
						$text = preg_replace("/<br\s*\/?>\s<br\s*\/?>\s+/", "<br/>", $text);
						
						unset($parser);
						
						//Store the parsed post in the cache for a month
						$CACHE->store('media/movies/movie_' . $row['id'], $text, "2592000");
					}
								
					echo '
					<!-- VIDEO Frame -->
					<video id="movie-frame" 
						class="video-js vjs-default-skin warcry-skin" 
						controls 
						preload="auto" 
						width="846" height="476" 
						poster="', $config['BaseURL'], '/uploads/media/movies/', $row['dirname'], '/thumbnails/', $row['image'], '"
						data-setup="{}">';
						
							if ($row['mp4'] and $row['mp4'] != '')
								echo '<source src="', $config['BaseURL'], '/uploads/media/movies/', $row['dirname'], '/', $row['mp4'], '" type="video/mp4">';
							if ($row['webm'] and $row['webm'] != '')
								echo '<source src="', $config['BaseURL'], '/uploads/media/movies/', $row['dirname'], '/', $row['webm'], '" type="video/webm">';
							if ($row['ogg'] and $row['ogg'] != '')
								echo '<source src="', $config['BaseURL'], '/uploads/media/movies/', $row['dirname'], '/', $row['ogg'], '" type="video/ogg">';
								
					echo '
					</video>
					<!-- VIDEO Frame.End -->
	
					<!-- VEDEO Info -->
					<div class="open-video-info">
						<h3>', htmlspecialchars(stripslashes($row['name'])), '</h3>
						<p>', $text, '</p>
					</div>';
					
					$IdError = false;
				}
				else
				{
					//invalid movie id
					$IdError = true;
				}
				unset($row, $res);
			}
			
			//check if we have ID Error
			if ($IdError)
			{
				$ERRORS->iPrint('Unable to proceed to the requested page. Invalid movie id.', true, true);
				echo '<div align="left" style="margin: 30px 0 30px 0;"><input type="button" value="Go back" onclick="history.go(-1); return false;"></div>';
			}
			
			//Check if we have other movies [Exclude that one]
			$res = $DB->prepare("SELECT `id`, `name`, `short_text`, `youtube`, `image`, `dirname` FROM `movies` WHERE `id` != :id ORDER BY `id` DESC;");
			//exlude this movie ID if valid
			$res->bindValue(':id', ($IdError ? 0 : $movieId), PDO::PARAM_INT);
			$res->execute();
			
			if ($res->rowCount() > 0)
			{
				echo '
				<!-- Other Media Items  (Videos/Screanshots/Wallpapers) -->
				
				<script>var $RunMovieSlider = true;</script>';
				
				while ($arr = $res->fetch())
				{
					echo '
					<div id="slider">  
						
						<!-- Media item -->
						<div class="media-video-container" align="left">
							<div class="media-video-thumb container_frame">
								<div class="cframe_inner">
									<a href="index.php?page=open-video&id=', $arr['id'], '">
									<!--Video THUMB Preview-->
									<div class="image-thumb-preview" style="background-image:url(\'', $config['BaseURL'], '/uploads/media/movies/', $arr['dirname'], '/thumbnails/small_', $arr['image'], '\');"></div>
									<div class="play-button-small"></div>
									</a>
								</div>
							</div>
							<div class="video-info">
								<h3>', htmlspecialchars(stripslashes($arr['name'])), '</h3>
									<p>', htmlspecialchars(stripslashes($arr['short_text'])), '</p>
								<a href="', $arr['youtube'], '" class="youtube-link" target="_blank">Watch in YouTube</a>
							</div>
							<div class="clear"></div>
						</div>
				
					</div>';
				}
			}
		?>
         
	</div>
</div>

<script type="text/javascript">
$(document).ready(function()
{
	if (typeof $RunMovieSlider != 'undefined' && $RunMovieSlider)
	{
		$("#slider").mopSlider({
			'w':846,
			'h':150,
			'sldW':842,
			'btnW':200,
			'indi':"",
			'shuffle':0
		});
	}
});
$(window).load(function()
{
	//Posters fix for chrome and IE
	$('.vjs-poster').css('display', 'block');
});
</script>

<?php
//Add to the loader
$TPL->AddFooterJs('template/js/video.js');
$TPL->AddFooterJs('template/js/jquery-ui-1.8.16.custom.min.js');
$TPL->AddFooterJs('template/js/mopSlider-2.4.js');
//Print the header
$TPL->LoadFooter();
?>
