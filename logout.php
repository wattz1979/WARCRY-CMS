<?PHP
include_once 'engine/initialize.php';

if (!$CURUSER->isOnline())
{
   header("Refresh: 0; url=".$config['BaseURL']."/index.php");
   exit();
}

//logout the user
$CURUSER->logout();
$CORE->removeCookie('rmm');

header("Location: ".$config['BaseURL']."/");