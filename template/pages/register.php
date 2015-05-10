<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//if the user is already logged in return him to index
if ($CURUSER->isOnline())
{
   header("Refresh: 0; url=".$config['BaseURL']."/index.php");
   exit();
}

//get raf hash
$rafHash = isset($_GET['raf']) ? $_GET['raf'] : false;

//Before loading any HTML i wanna check if we need to redirect to the Terms page before register
//check if the Terms of Usage have been accpeted
if (!isset($_SESSION['TermsAccepted']) or $_SESSION['TermsAccepted'] != true)
{
	//save the page query
	$_SESSION['TermsReturn'] = $config['BaseURL'] . "/index.php?page=register" . ($rafHash ? '&raf='.$rafHash : '');
	//redirect
	header("Location: ".$config['BaseURL']."/index.php?page=terms-before-register");
	die;
}

//Set the title
$TPL->SetTitle('Register new Account');
//Print the header
$TPL->LoadHeader();

?>

 <div class="sub-page-title">
  <div id="title"><h1>Register<p></p><span></span></h1></div>
 </div>
 
 
 <div class="container_2" align="center">
 
<div class="error-holder">
   <?php
	if ($error = $ERRORS->DoPrint('register'))
	{
		echo $error, '<br><br>';
				
		unset($error);
	}			
	?>
</div>

   <div class="container_3" align="center">
   
     <!-- FORMS -->
      <form action="execute.php?take=register" method="post" name="registrationForm">
      	
        <?php
			//RAF system input
			if ($rafHash)
				echo '<input type="hidden" name="raf" value="', $rafHash, '" />';
		?>
        
        <div class="row">
          <label for="register-username">Account Name</label>
          <input type="text" name="username" id="register-username" tabindex="1" />
        </div>
        
        <div class="row">
          <label for="register-displayName">Display Name</label>
          <input type="text" name="displayname" id="register-displayName" tabindex="2" />
        </div>
        
        	<div class="seperator"></div>
        
        <div class="row">
          <label for="register-password">Password</label>
          <input type="password" name="password" id="register-password" tabindex="3" />
        </div>
        
        <div class="row">
          <label for="register-password2">Repeat Password</label>
          <input type="password" name="password2" id="register-password2" tabindex="4" />
        </div>
        
       		<div class="seperator"></div>
        
        <div class="row">
          <label for="register-email">Email Address</label>
          <input type="text" name="email" id="register-email" tabindex="5" />
        </div>        
        
        	<div class="seperator"></div>
        
        <div class="row">
        	<label>Birthday</label>
      		<input type="text" name="birthday[year]" placeholder="Year" tabindex="7" />
			<input type="text" name="birthday[day]" placeholder="Day" tabindex="6" />
            
            <select name="birthday[month]" styled="true" id="register-select-birthday-month">
            	<option disabled="disabled">Month</option>
                
				<?php	
				//months options
				$months = array(
					'01' => 'January',
					'02' => 'February',
					'03' => 'March',
					'04' => 'April',
					'05' => 'May',
					'06' => 'June',
					'07' => 'July',
					'08' => 'August',
					'09' => 'September',
					'10' => 'October',
					'11' => 'November',
					'12' => 'December',
				);

				//print the months
				foreach ($months as $number => $name)
				{
                	echo '<option value="', $number, '">', $name, '</option>';
				}
				unset($months);
				?>
                		
          	</select>
        </div>
        
       	<div class="seperator"></div>
        
        <div class="row">
        	<label>Country</label>
            <select name="country" class="country-select" style="min-width: 350px !important;" styled="true" id="select-style-1">
             	<option disabled="disabled">Select Country</option>
           		
				<?php
				$Countries = new CountriesData();
				
				foreach ($Countries->data as $key => $value)
				{
                	echo '<option value="', $key, '">', $value, '</option>';
				}
				
				unset($Countries);	
				?>
                
           </select>
           <script type="text/javascript">
            //Select the country automatically
            $(function()
            {
                $.get('http://api.hostip.info/country.php', function(country)
                {
                    //verify the country
                    if (typeof country == 'string' && country.length >= 2 && country.length <= 3)
                    {
						var option = $('.country-select').find('option[value="' + country + '"]');
						
						$('.country-select').SelectTransform('quickSelect', {option: option, index: option.index()});
                    }
                });
            });
            </script>
        </div>
        
        <div class="seperator"></div>

        <div class="row">
        	<label>Secret Question</label>
            <select name="secretQuestion" style="width: 350px !important;" styled="true" id="select-style-1">
             	<option disabled="disabled">Select Question</option>
           		
				<?php
				$Questions = new SecretQuestionData();
				
				foreach ($Questions->data as $key => $value)
				{
                	echo '<option value="', $key, '">', $value, '</option>';
				}
				
				unset($Questions);		
				?>
                
           </select>
        </div>
        
        <div class="row">
          <label for="register-secretAnswer">Secret Answer</label>
          <input type="text" name="secretAnswer" id="register-secretAnswer" tabindex="8" />
        </div>
        
        <?php
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
		?>
       
        <div class="row" align="right">
       		<input type="submit" value="complete" tabindex="10">
        </div>
     
      </form>
     <!-- FORMS.End -->
   
   </div>
 
 
 </div>

<!--<script type="text/javascript">
	$(document).ready(function()
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
    });
</script>-->

<?php
	if ($formData = $ERRORS->multipleError_accessFormData('register'))
	{	
		echo '
		<script>
			$(document).ready(function()
			{
				var savedFormData = $.parseJSON(', json_encode(json_encode($formData)), ');
				restoreFormData(\'registrationForm\', savedFormData);
			});
		</script>';
	}
	unset($formData);

	//Add some javascripts to the loader
	$TPL->AddFooterJs('template/js/alertbox.js');
	$TPL->AddFooterJs('template/js/forms.js');
	//Print the footer
	$TPL->LoadFooter();
?>