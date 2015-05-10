<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Set the title
$TPL->SetTitle('Wallpapers');
//CSS
$TPL->AddCSS('template/style/page-media.css');
//Print the header
$TPL->LoadHeader();

?>
<div class="content_holder">

	 <div class="sub-page-title">
	  <div id="title"><h1>Media<p></p><span></span></h1></div>
	 </div>
	 
	 <div class="container_2" align="center" style="padding:30px 40px; width:916px;">
     
     			<div class="media-header">
					<h2>Wallpapers</h2>
                    <h3 class="items-number">(5)</h3>
                    	<div class="clear"></div>
					<div class="bline"></div>
				</div>
         <!-- All Wallpapers -->
         
         <ul class="screanshots all-wallpapers screanshots-media-page">
					<li>
						<a href="uploads/media/wallpapers/ww-one-wallpaper-1920x1080.jpg" target="_blank" class="container_frame" title="Warcry WoW Wallpaper One">
							<span class="cframe_inner" style="background-image:url(uploads/media/wallpapers/thumbs/thumb-warcry-wall1.jpg);"></span>
                            <div class="media-zoom-ico"></div>
						</a>
                        	<div class="wallpaper-info">
                            	<h2>WARCRY Wall</h2>
                                <div class="dw-res-links">
	                                <a href="uploads/media/wallpapers/ww-one-wallpaper-1024x768.jpg" target="_blank">1024x768</a>/
	                                <a href="uploads/media/wallpapers/ww-one-wallpaper-1360x768.jpg" target="_blank">1360x768</a>/
	                                <a href="uploads/media/wallpapers/ww-one-wallpaper-1600x900.jpg" target="_blank">1600x900</a>/
	                                <a href="uploads/media/wallpapers/ww-one-wallpaper-1920x1080.jpg" target="_blank">1920x1080</a>
                                </div>
                            </div>
					</li>
                    
                    <li>
						<a href="uploads/media/wallpapers/1-1920x1080.jpg" target="_blank" class="container_frame" title="Emerald Dragonshrine Wallpaper">
							<span class="cframe_inner" style="background-image:url(uploads/media/wallpapers/thumbs/1-thumb.jpg);"></span>
                            <div class="media-zoom-ico"></div>
						</a>
                        	<div class="wallpaper-info">
                            	<h2>Emerald Dragonshrine</h2>
                                <div class="dw-res-links">
	                                <a href="uploads/media/wallpapers/1-1024x768.jpg" target="_blank">1024x768</a>/
	                                <a href="uploads/media/wallpapers/1-1360x768.jpg" target="_blank">1360x768</a>/
	                                <a href="uploads/media/wallpapers/1-1600x900.jpg" target="_blank">1600x900</a>/
	                                <a href="uploads/media/wallpapers/1-1920x1080.jpg" target="_blank">1920x1080</a>
                                </div>
                            </div>
					</li>
                    
                    <li>
						<a href="uploads/media/wallpapers/2-1920x1080.jpg" target="_blank" class="container_frame" title="Lich King Wallpaper">
							<span class="cframe_inner" style="background-image:url(uploads/media/wallpapers/thumbs/2-thumb.jpg);"></span>
                            <div class="media-zoom-ico"></div>
						</a>
                        	<div class="wallpaper-info">
                            	<h2>Lich King</h2>
                                <div class="dw-res-links">
	                                <a href="uploads/media/wallpapers/2-1024x768.jpg" target="_blank">1024x768</a>/
	                                <a href="uploads/media/wallpapers/2-1360x768.jpg" target="_blank">1360x768</a>/
	                                <a href="uploads/media/wallpapers/2-1600x900.jpg" target="_blank">1600x900</a>/
	                                <a href="uploads/media/wallpapers/2-1920x1080.jpg" target="_blank">1920x1080</a>
                                </div>
                            </div>
					</li>
                    
                    <li>
						<a href="uploads/media/wallpapers/4-1920x1080.jpg" target="_blank" class="container_frame" title="Grizzlemaw Wallpaper">
							<span class="cframe_inner" style="background-image:url(uploads/media/wallpapers/thumbs/4-thumb.jpg);"></span>
                            <div class="media-zoom-ico"></div>
						</a>
                        	<div class="wallpaper-info">
                            	<h2>Grizzlemaw</h2>
                                <div class="dw-res-links">
	                                <a href="uploads/media/wallpapers/4-1024x768.jpg" target="_blank">1024x768</a>/
	                                <a href="uploads/media/wallpapers/4-1360x768.jpg" target="_blank">1360x768</a>/
	                                <a href="uploads/media/wallpapers/4-1600x900.jpg" target="_blank">1600x900</a>/
	                                <a href="uploads/media/wallpapers/4-1920x1080.jpg" target="_blank">1920x1080</a>
                                </div>
                            </div>
					</li>
                    
                    <li>
						<a href="uploads/media/wallpapers/5-1920x1080.jpg" target="_blank" class="container_frame" title="Warcry WoW Wallpaper One">
							<span class="cframe_inner" style="background-image:url(uploads/media/wallpapers/thumbs/5-thumb.jpg);"></span>
                            <div class="media-zoom-ico"></div>
						</a>
                        	<div class="wallpaper-info">
                            	<h2>Warcry WoW</h2>
                                <div class="dw-res-links">
	                                <a href="uploads/media/wallpapers/5-1024x768.jpg" target="_blank">1024x768</a>/
	                                <a href="uploads/media/wallpapers/5-1360x768.jpg" target="_blank">1360x768</a>/
	                                <a href="uploads/media/wallpapers/5-1600x900.jpg" target="_blank">1600x900</a>/
	                                <a href="uploads/media/wallpapers/5-1920x1080.jpg" target="_blank">1920x1080</a>
                                </div>
                            </div>
					</li>
					
                    <div class="clear"></div>
				</ul>
         	
        	
         <!-- All Wallpapers.End -->
	        
	 </div>
    
</div>

<?php

$TPL->LoadFooter();

?>
