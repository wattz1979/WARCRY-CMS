<?PHP
include_once 'engine/initialize.php';

define("init_executes", true);
 
$execute = ((isset($_GET['take'])) ? $_GET['take'] : NULL);
      
$file = $config['RootPath'].'/execute_files/'.$execute.'_execute.php';  

$allowed = array(
	'buyItems',
	'login',
	'precovery',
	'precovery_finish',
	'register',
	'unstuck',
	'vote',
	'changepass',
	'changemail',
	'changedname',
	'level',
	'faction',
	'recustomization',
	'armorset',
	'screenshot',
	'teleport',
	'submit_bug',
	'item_refund',
	'post_topic',
	'post_reply',
	'edit_reply',
	'purchase_boost',
	'purchase_gold',
	'redeem_pcode',
	'set_realm',
);

if (in_array($execute, $allowed))
{
	if (file_exists($file))
	{
		require_once $file; 
	}
	else
	{
		echo 'The file does not exist.';
		die; 
	}
}
else
{
	echo 'The file is not allowed.';
	die;
}