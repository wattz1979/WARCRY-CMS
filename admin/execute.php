<?PHP
include_once 'engine/initialize.php';

define("init_executes", true);
 
$execute = ((isset($_GET['take'])) ? $_GET['take'] : NULL);
      
$file = $config['RootPath'].'/admin/execute_files/'.$execute.'_execute.php';  

$allowed = array(
	'login',
	'cropImage',
	'addNews',
	'editNews',
	'delete',
	'add_armorsetcat',
	'edit_armorsetcat',
	'add_forumcat',
	'edit_forumcat',
	'add_armorset',
	'edit_armorset',
	'approve_screenshot',
	'deny_screenshot',
	'approve_report',
	'disapprove_report',
	'edit_bugreport',
	'add_pcode',
	'add_article',
	'edit_article',
	'grant_permissions',
	'chuckedUpload',
	'add_movie',
	'edit_storeitem',
	'add_storeitem',
	'change_user_rank',
);

if (in_array($execute, $allowed))
{
	if (file_exists($file))
	{
		require_once $file;
	}
	else
	{
		header('HTTP/1.0 404 not found');
		exit;
	}
}
else
{
	header('HTTP/1.0 404 not found');
	exit;
}