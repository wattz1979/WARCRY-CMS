<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//check if we've got return
if (isset($_GET['return']))
{
	$return = rawurldecode($_GET['return']);
	$_SESSION['url_bl'] = $return;
}

//if the user is already logged in return him to index
if ($CURUSER->isOnline())
{
   header("Refresh: 0; url=".$config['BaseURL']."/index.php");
   exit();
}

//Set the title
$TPL->SetTitle('Sign In');
//Print the header
$TPL->LoadHeader();

?>

 <div class="sub-page-title">
  <div id="title"><h1>Login<p></p><span></span></h1></div>
 </div>
 
 <div class="container_2" align="center">
  <div class="vertical_center" align="center">
  
<div class="error-holder">
	<?php
	if ($error = $ERRORS->DoPrint('login'))
	{
		echo $error, '<br><br>';
				
		unset($error);
	}		
	?>
</div>
   
   <div class="container_3" align="center">
   
     <!-- FORMS -->
      <form action="<?php echo $config['BaseURL']; ?>/execute.php?take=login" method="post" name="loginForm">
      
        <div class="row">
          <label>Account Name</label>
          <input type="text" name="username">
        </div>
        
        <div class="row">
          <label>Password</label>
          <input type="password" name="password">
        </div>
        	<br/>        
        <div class="row" align="right">
        	<label class="label_check">
            	<div></div>
                <input type="checkbox" value="1" id="rememberme" name="rememberme">
                <p>Remember me</p>
            </label>
        	<input type="submit" value="log in">
        </div>
     
      </form>
      
     <div class="login-box-options login-page">
     <a href="<?php echo $config['BaseURL']; ?>/index.php?page=password_recovery">Forgot your password ?</a><br>
     <span>Don't have an account yet ? <a href="<?php echo $config['BaseURL']; ?>/index.php?page=register">Register Now!</a></span>
    </div>
    
    <!-- FORMS.End -->
   
   </div>
   
  </div>
 </div>
 
<?php

$TPL->LoadFooter();

?>
