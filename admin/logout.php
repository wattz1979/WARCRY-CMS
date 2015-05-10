<?PHP
include_once 'engine/initialize.php';

if (!$CURUSER->isOnline())
{
   header("Refresh: 0; url=".$config['BaseURL']."/admin/index.php");
   exit;
}

//logout the user
$CURUSER->logout();

header("Location: ".$config['BaseURL']."/");