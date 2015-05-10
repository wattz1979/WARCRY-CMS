<?php
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$host = '46.4.69.9';
$port = 3724;
$timeout = 0.5;

if (($status = $CACHE->get('logon_status')) === false)
{
    $sock = @fsockopen($host, $port, $errno, $errstr, $timeout);
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
    $CACHE->store('logon_status', $status, "30");
}

echo $status;

exit;