<?php
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$realm = (int)$_GET['id'];
$timeout = 0.5;

if (($status = $CACHE->get('realm_status_' . $realm)) === false)
{
	$sock = @fsockopen($realms_config[$realm]['address'], $realms_config[$realm]['port'], $errno, $errstr, $timeout);
    if ($sock)
    {
        $status = '1';
    } 
    else
    {
        $status = '0';
    }
    @fclose($sock);
    unset($sock);
	
	//Cache server status for 30 seconds
	$CACHE->store('realm_status_' . $realm, $status, "30");
}

echo $status;

exit;