<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//check for Verification
$verify = isset($_GET['verify']) ? ($_GET['verify'] == 1 ? true : false) : false;
$key = isset($_GET['key']) ? $_GET['key'] : false;

//Set the title
$TPL->SetTitle('Password Recovery');
//Print the header
$TPL->LoadHeader();

?>
<div class="content_holder">

    <div class="sub-page-title">
    	<div id="title"><h1>Password Recovery<p></p><span></span></h1></div>
    </div>
 
  	<div class="container_2" align="center">
    	<div class="cont-image" style="background: none;">

			<?php
            if ($error = $ERRORS->DoPrint('password_recovery'))
            {
                echo $error, '<br>';
            }			
            unset($error);
            ?>
            
            <!-- Store Activity -->
            <div class="store-activity">
            	
                <?php
				
				//switch between step one and step two
				if (!$verify)
                {
					echo '
                    <div class="page-desc-holder">
                        In order to recover your account password you have to enter the E-Mail address associated with your account, <br />
                        and then follow the instructions we\'ve sent you on the given E-Mail address.
                    </div>
                    
                    <div class="container_3 account-wide" align="center">
                        
						<br><br><br>
                        <div style="padding: 20px; width: 500px; display: block;">
                        <form action="', $config['BaseURL'], '/execute.php?take=precovery" method="post">
    
                            <div class="row" style="width: auto !important;">
                                <label for="email">Enter your E-mail Address:</label>
                                <input type="text" name="email" />
                            </div>';
								
								/*
                                ###############################################
                                ################### CAPTCHA ###################
                                $CORE->load_CoreModule('text.captcha');
                                //setup
                                $captcha = new TextCaptcha();
                                //request instance
                                $Instance = $captcha->CreateInstance();
                                
                                //display the question
								echo '
								<div class="human-test">
									<h3>Human Test</h3>
									<a id="newq" href="javascript: void(0);">New Question</a>
									<div id="question-hodlder">
										<div id="captcha-question">', $Instance['question'], '</div>
										<input type="text" name="', $Instance['ResponseFieldName'],'" id="', $Instance['ResponseFieldName'],'" placeholder="Type the answer here" tabindex="9" />
									</div>
								</div>';
                                            
                                //free up memory
                                unset($Instance, $captcha);
								*/
								
							echo '
                            <br/>
                            <div class="row" align="center">
                                <div>
                                    <input type="submit" value="Continue" />
                                </div>
                            </div>
                            <br /><br />
                            
                        </form>
                        </div>
                                    
                    </div>';
				}
				else
				{
					//check if the key is set
					if ($key)
					{
						//load the Tokens module
						$CORE->load_CoreModule('tokens');
						//construct
						$token = new Tokens();
						//Set the application string so the token is only valid for this app
						$token->setApplication('PRECOVER');
						
						//set and validate the token
						$TokenValidation = $token->set_decodedToken($key);
						//make sure the token checks out
						if ($TokenValidation === true)
						{
							//the token is valid, save the token for the execute
							$_SESSION['P_Recovery_Token'] = $key;
							
							echo '
							<div class="page-desc-holder">
								This is the final step of the password recovery process,
								all you have to do is to enter your new password.
							</div>
								
							<div class="container_3 account-wide" align="center">
								
								<p style="padding: 20px;">
								
									<form action="', $config['BaseURL'], '/execute.php?take=precovery_finish" method="post">
    
										<div class="row row-fix">
											<label for="email">New password:</label>
											<input type="password" name="password" />
										</div>
										
										<div class="row row-fix">
											<label for="email">Confirm password:</label>
											<input type="password" name="password2" />
										</div>
										
										<br/>
										<div class="row row-fix" align="left">
											<div style="padding-left:240px;">
												<input type="submit" value="Continue" />
											</div>
										</div>
										<br /><br />
										
									</form>
							
								</p>
							</div>';
						}
						else
						{
							//Setup our notification
							$NOTIFICATIONS->SetTitle('Notification');
							$NOTIFICATIONS->SetHeadline('Error!');
							$NOTIFICATIONS->SetText('Invalid security token.<br>Please open your your e-mail and follow the instruction we have sent you.');
							$NOTIFICATIONS->SetTextAlign('center');
							//$NOTIFICATIONS->SetAutoContinue(true);
							//$NOTIFICATIONS->SetContinueDelay(5);
							$NOTIFICATIONS->Apply();
							
							echo '<meta http-equiv="refresh" content="0;URL=\'', $config['BaseURL'], '/index.php?page=password_recovery\'">';
						}
						unset($token, $TokenValidation);
					}
					else
					{
						echo '<meta http-equiv="refresh" content="0;URL=\'', $config['BaseURL'], '/index.php?page=password_recovery\'">';
					}
				}
				unset($verify, $key);
				
				?>
                
            </div>
     		<!-- Store Activity.End -->
        
     	</div>
	</div>
 
</div>

</div>

<script type="text/javascript">
	$(document).ready(function()
	{
		if ($('#newq').length > 0)
		{
			$('#newq').click(function()
			{
				var $this = $(this);
				
				$.ajax({
					type: "GET",
					url: $BaseURL + "/ajax.php?phase=14",
					dataType: 'json',
					cache: false,
					error: function(jqXHR, textStatus, errorThrown)
					{
						console.log(textStatus);
					},
					success: function(data)
					{
					   var cont = $this.parent();
					   //update the question
					   $('#captcha-question').html(data.question);
					   //update the response field
					   var responseFiled = cont.find('input[type="text"]');
					   responseFiled.attr('id', data.ResponseFieldName);
					   responseFiled.attr('name', data.ResponseFieldName);
					}
				});
			});
		}
    });
</script>

<?php
	$TPL->LoadFooter();
?>