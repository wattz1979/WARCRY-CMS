<?PHP
include_once 'engine/initialize.php';
 
$phase = ((isset($_GET['phase'])) ? (int)$_GET['phase'] : false);

$phases = array(
	//define each phase file associative to the number
	1  => 'getItem.php',
	2  => 'getStorePage.php',
	3  => 'getStoreFilter.php',
	4  => 'verifyAmount.php',
	5  => 'getArmorsetsPage.php',
	6  => 'getArmorsetsFilter.php',
	7  => 'getMapInfo.php',
	8  => 'verifyPoint.php',
	9  => 'updateSocialStatus.php',
	10 => 'loadChangesets.php',
	11 => 'acceptTerms.php',
	13 => 'getBTCategoryData.php',
	14 => 'refreshCaptcha.php',
	15 => 'loadBTReports.php',
	16 => 'setAvatar.php',
	17 => 'deletePost.php',
	18 => 'quoteInfo.php',
	19 => 'serverStatus.php',
	20 => 'logonStatus.php',
	21 => 'boostDurationPrice.php',
	22 => 'getSocialCounter.php',
	23 => 'lookupPcode.php',
	24 => 'postArticleComment.php',
	25 => 'pullArticleComments.php',
);

if (isset($phases[$phase]))
{
	if (!file_exists($config['RootPath'] . '/ajaxFiles/'.$phases[$phase]))
	{
		//the file dosent exist
		header('HTTP/1.0 404 not found');
		exit;
	}
	else
	{
		define('init_ajax', true);
		
		include_once $config['RootPath'] . '/ajaxFiles/'.$phases[$phase];
	}
}
else
{
	//the file dosent exist
	header('HTTP/1.0 404 not found');
	exit;
}

unset($phases, $phase);

//call the shutdown
Shutdown::Execute();

exit;