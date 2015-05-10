<?PHP
include_once 'engine/initialize.php';
 
$phase = ((isset($_GET['phase'])) ? (int)$_GET['phase'] : false);

$phases = array(
	//define each phase file associative to the number
	1  => 'imageUpload.php',
	2  => 'PullArticlesAjax.php',
	3  => 'PullPromoCodesAjax.php',
	4  => 'PullUsersAjax.php',
	5  => 'reportDataAjax.php',
	6  => 'save_forum_cat_order.php',
	7  => 'PullStorePurchasesAjax.php',
	8  => 'PullLogsLvlAjax.php',
	9  => 'PullLogsIggAjax.php',
	10 => 'PullLogsFactionAjax.php',
	11 => 'PullLogsCustomizeAjax.php',
	12 => 'PullLogsArmorsetsAjax.php',
	13 => 'PullPaypemtWallAjax.php',
	14 => 'PullPaypalLogsAjax.php',
	15 => 'armorsetDataAjax.php',
	16 => 'PullLogsBoostsAjax.php',
	17 => 'PullStoreItemsAjax.php',
	18 => 'StoreDeleteItem.php',
	19 => 'storeItemData.php',
);

if (isset($phases[$phase]))
{
	if (!file_exists($config['RootPath'] . '/admin/ajaxFiles/'.$phases[$phase]))
	{
		//the file dosent exist
		header('HTTP/1.0 404 not found');
		exit;
	}
	else
	{
		define('init_ajax', true);
		
		include_once $config['RootPath'] . '/admin/ajaxFiles/'.$phases[$phase];
	}
}
else
{
	//the file dosent exist
	header('HTTP/1.0 404 not found');
	exit;
}

unset($phases, $phase);

exit;