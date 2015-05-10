<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$_SESSION['TermsAccepted'] = true;

echo $_SESSION['TermsReturn'];

?>