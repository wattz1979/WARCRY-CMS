<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$CORE->loggedInOrReturn();

//Set the title
$TPL->SetTitle('Change E-mail Address');
//Print the header
$TPL->LoadHeader();

?>
<div class="content_holder">

<div class="sub-page-title">
	<div id="title"><h1>Account Panel<p></p><span></span></h1></div>
  
    <div class="quick-menu">
    	<a class="arrow" href="#"></a>
        <ul class="dropdown-qmenu">
        	<li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=store">Store</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=teleporter">Teleporter</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=buycoins">Buy Coins</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=vote">Vote</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=pstore">Premium Store</a></li>
            <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=unstuck">Unstuck</a></li>
            <!--<li id="messages-ddm">
            	<a href="<?php echo $config['BaseURL']; ?>/index.php?page=pm">
                	<b>55</b> <i>Private Messages</i>
                </a>
            </li>-->
        </ul>
    </div>
</div>
 
  	<div class="container_2 account" align="center">
     <div class="cont-image">

    <?php
	if ($error = $ERRORS->DoPrint('changemail'))
	{
		echo $error, '<br><br>';
	}			
	if ($error = $ERRORS->successPrint('changemail'))
	{
		echo $error, '<br><br>';
	}			
	unset($error);
	?>
  
      <div class="container_3 account_sub_header">
         <div class="grad">
       		<div class="page-title">Change E-mail Address</div>
       		<a href="<?php echo $config['BaseURL'], '/index.php?page=account'; ?>">Back to account</a>
      	 </div>
      </div>
      
      <!-- Store Activity -->
      	<div class="store-activity">
        
       		<div class="page-desc-holder">
				In order to change your e-mail address you have to answer your secret question.
            </div>
            
            <div class="container_3 account-wide" align="center">
            	
                <p style="padding: 20px;">
                <form action="<?php echo $config['BaseURL'], '/execute.php?take=changemail'; ?>" method="post">
                	
                    
                    <div class="row row-fix">
					<label>Select your Secret Question:</label>
                    	
                        <span style="display: inline-block; float:right;">
	                        <select name="secretQuestion" styled="true" id="select-style-2">
	           					
								<?php
								$Questions = new SecretQuestionData();
								
								foreach ($Questions->data as $key => $value)
								{
				                	echo '<option value="', $key, '">', $value, '</option>';
								}
								
								unset($Questions);		
								?>
				                
				           </select>
                       </span>
                       </div>
                    
                    <div class="row row-fix">
                    <label for="secretAnswer">Answer your Secret Question:</label>
                    <input type="text" name="secretAnswer" />
                    </div>
                    
 					<div class="row row-fix">
                    <label for="email">Enter your new E-mail Address:</label>
                    <input type="text" name="email" />
  					</div>
                    
                    <br />
                    
                    <input style="left:10px;" type="submit" value="Change" />
                    <br />
                    <br /><br />
                    
                </form>
                </p>
                            
            </div>
                        
      	</div>
      <!-- Store Activity.End -->
        
     </div>
	</div>
 
</div>

</div>

<?php

$TPL->LoadFooter();

?>