<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Set the title
$TPL->SetTitle('Addons');
//CSS
$TPL->AddCSS('template/style/page-support-all.css');
//Print the header
$TPL->LoadHeader();

?>
<div class="content_holder">

 <div class="sub-page-title">
  <div id="title"><h1>Addons<p></p><span></span></h1></div>
 </div>
 
  	<div class="container_2" align="center">
    
    	<div class="container_3 archived-news">
        	<!-- Addons -->
            
            <div class="addons-info-top">
	            <h3>Avalible 3.3.5a Addons</h3>
	            <p>
                	Because of constant internal changes in the WoW software most addon`s 
                    come in many different versions and usualy also run only under that version with the respective WoW version.
                    The addons on this page are already tested in version 3.3.5 and you could find it here and in most popular Addon portals.<br/>
                    <i>
                    ( <a href="http://www.curse.com/" target="_blank">www.curse.com</a>, 
                    <a href="http://www.wowinterface.com/addons.php" target="_blank">www.wowinterface.com</a>, 
                    <a href="http://www.wowace.com/" target="_blank">www.wowace.com</a> )
                    </i>
                </p>
            </div>
            
            
            	<div class="addon-row" align="left">
                  <h4>Addon name goeshere</h4>
                  <p>
                  	Orem ipsum dolor sit amet, consectetur adipiscing elit. In convallis tristique justo, a fringilla purus tempor a. Praesent libero erat, hendrerit sed auctor posuere, blandit at leo.
                  </p>
                  <div id="addon-navigation">
                  	<a class="download" href="#">Download</a>
                    	&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                    <a class="source" href="#">This addon in Curse</a>
                  </div>
                </div>
                
                <div class="addon-row" align="left">
                  <h4>Addon name goeshere</h4>
                  <p>
                  	Orem ipsum dolor sit amet, consectetur adipiscing elit. In convallis tristique justo, a fringilla purus tempor a. Praesent libero erat, hendrerit sed auctor posuere, blandit at leo.
                  </p>
                  <div id="addon-navigation">
                  	<a class="download" href="#">Download</a>
                    	&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                    <a class="source" href="#">This addon in Curse</a>
                  </div>
                </div>
                
                <div class="addon-row" align="left">
                  <h4>Addon name goeshere</h4>
                  <p>
                  	Orem ipsum dolor sit amet, consectetur adipiscing elit. In convallis tristique justo, a fringilla purus tempor a. Praesent libero erat, hendrerit sed auctor posuere, blandit at leo.
                  </p>
                  <div id="addon-navigation">
                  	<a class="download" href="#">Download</a>
                    	&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                    <a class="source" href="#">This addon in Curse</a>
                  </div>
                </div>
                
                <div class="addon-row" align="left">
                  <h4>Addon name goeshere</h4>
                  <p>
                  	Orem ipsum dolor sit amet, consectetur adipiscing elit. In convallis tristique justo, a fringilla purus tempor a. Praesent libero erat, hendrerit sed auctor posuere, blandit at leo.
                  </p>
                  <div id="addon-navigation">
                  	<a class="download" href="#">Download</a>
                    	&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                    <a class="source" href="#">This addon in Curse</a>
                  </div>
                </div>
    	  		
                
            <!-- Addons.End -->
    	</div>
               
        
    </div>
    
</div>

<?php
	$TPL->LoadFooter();
?>