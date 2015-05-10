<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

###############################################
################### CAPTCHA ###################
$CORE->load_CoreModule('text.captcha');
//setup
$captcha = new TextCaptcha();
//request instance
$Instance = $captcha->CreateInstance();

$return = array(
	'question'	 		=> $Instance['question'], 
	'ResponseFieldName' => $Instance['ResponseFieldName'],
);
			
//free up memory
unset($Instance, $captcha);

echo json_encode($return);

exit;