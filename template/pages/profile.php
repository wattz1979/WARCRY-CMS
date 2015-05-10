<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->UnderConstruction('User Profiler');

//Set the title
$TPL->SetTitle('User Profile');
//Print the header
$TPL->LoadHeader();

?>

<div class="content_holder">

 <div class="sub-page-title">
  <div id="title"><h1>User's Profile<p></p><span></span></h1></div>
 </div>
 
  	<div class="container_2" align="center">
    
    	<div class="container_3 archived-news under-construction" align="left">
        	<!-- UNDER CONSTRUCTION -->  
            	
                <div class="holder">
                    <h5>oops...<span></span></h5>
                    <h4>Looks like this page is under construction!<span></span></h4>
               	</div>      	

            <!-- TERMS OF USE.End -->
    	</div>
        
    </div>
    
</div>

<?php
	$TPL->LoadFooter();
?>