<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Load the Tokens Module
$CORE->load_CoreModule('promo.codes');

header('Content-type: text/json');

$code = ((isset($_GET['code'])) ? $_GET['code'] : false);

if (!$code)
{
	echo '{"error": "The promo code is missing."}';
	die;
}

//Setup new promo code
$PCode = new PromoCode($code);

//Verify promo code
if ($PCode->Verify())
{
	echo json_encode($PCode->getInfo());
	exit;
}

//If we're here then something is wrong
echo '{"error": "', $PCode->getLastError(), '"}';

unset($PCode);

exit;