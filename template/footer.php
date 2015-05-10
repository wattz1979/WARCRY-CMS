<?php
if (!defined('init_template'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>
   <!-- BODY Content end here -->
   </div>
  </div>
 </div>
 <!-- BODY-->

 <!-- Footer -->
    <div class="footer-holder">
        <div id="footer">
        	<a class="back-to-top" href="#header"></a>
             
            <div class="left-side">
            	<a class="evil-logo" href="http://evil.duloclan.com/" target="_blank">EvilSystem Designs</a>
                <a class="dmca" href="http://www.dmca.com/Protection/Status.aspx?ID=cf56126a-7e17-426c-b7b5-aa7668c01834" title="DMCA"> <img src ="http://images.dmca.com/Badges/dmca_protected_sml_120x.png?ID=cf56126a-7e17-426c-b7b5-aa7668c01834" title="DMCA" target="_blank"><img alt="DMCA.com" />Protected by DMCA</a>
                <p>
                	All rights reserved ® <b>WARCRY</b> World Of Warcraft <br/>
					The website and its content was created for <i>Warcry WoW</i>.
                </p>
            </div>
            
            <div class="right-side">
                <ul>
                	<li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=all-wallpapers">Wallpapers</a></li>
                    <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=all-videos">Videos</a></li>
                    <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=all-screenshots">Screenshots</a></li>
                </ul>
                
                <ul>
                	<li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=references">References</a></li>
                    <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=rules">Rules</a></li>
                    <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=terms-of-use">Terms of Use</a></li>
                    <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=howto">How To</a></li>
                </ul>
                
            	<ul>
                	<li><a href="<?php echo $config['BaseURL']; ?>/index.php">Home</a></li>
                    <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=changelogs">Changelog</a></li>
                    <li><a href="<?php echo $config['BaseURL']; ?>/forums/">Forums</a></li>
                    <li><a href="<?php echo $config['BaseURL']; ?>/index.php?page=home">Armory</a></li>
                </ul>
            </div>
            
        </div>
        <div class="bot-foot-border"></div>
        <div class="bot-foot-glow"></div>
    </div>
 <!-- Footer.End -->
 </center>
 
 <div id="temp-login-form" style="display: none;">
	<div class="login-box" align="left">
		<form action="<?php echo $config['BaseURL']; ?>/execute.php?take=login" method="post">
            <input type="hidden" name="url_bl" id="js-login-box_urlbl" />
            <p>Account Name</p>
            <input type="text" name="username" autocomplete="on"> <br>
            <p>Password</p>
            <input type="password" name="password" autocomplete="on"><br>
            <div class="login-box-row">
            	<input type="submit" value="Login">
                <label class="label_check">
                    <div></div>
                    <input type="checkbox" value="1" id="rememberme" name="rememberme">
                    <p>Remember me</p>
            	</label>
            </div>
    	</form>
    	<div class="login-box-options">
     		<a href="<?php echo $config['BaseURL']; ?>/index.php?page=password_recovery">Forgot your password ?</a><br>
     		<span>Don't have an account yet ? <a href="<?php echo $config['BaseURL']; ?>/index.php?page=register">Register Now!</a></span>
    	</div>
  	</div>
 </div>
 
<?php
	//Add the default footer js include
	$TPL->AddFooterJs('template/js/footer_include.js');
	//Print the Javascript loader
	$TPL->PrintFooterJavascripts();
	//Print the footer
	$TPL->LoadFooter();
?>
</body>
</html>