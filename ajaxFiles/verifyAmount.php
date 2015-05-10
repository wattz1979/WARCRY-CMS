<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
 
$silver = ((isset($_GET['silver'])) ? (int)$_GET['silver'] : 0);
$gold = ((isset($_GET['gold'])) ? (int)$_GET['gold'] : 0);
$realm = ((isset($_GET['realm'])) ? (int)$_GET['realm'] : false);

//check if the curuser is online
if (!$CURUSER->isOnline())
{
	echo 'You must be logged in.';
	exit;
}

//check if the realm value was passed
if (!$realm)
{
	echo 'Website error: Cannot determine if the realm is online.';
	exit;
}
else
{
	$sock = @fsockopen($realms_config[$realm]['address'], $realms_config[$realm]['port'], $ERROR_NO, $ERROR_STR, 0.5);
	if($sock)
	{
		@fclose($sock);
	} 
	else
	{
		echo 'The realm is currently unavailable. Please try again in few minutes.';
		exit;
	}
}

//now check the amounts
if ($silver > 0 and $gold > 0)
{
	if ($CURUSER->get('silver') >= $silver and $CURUSER->get('gold') >= $gold)
	{
		echo 'OK';
	}
	else
	{
		$text = 'Not enough money.';
		
		//check if the silver is short
		if ($CURUSER->get('silver') < $silver)
		{
			$silverNeeded = $silver - $CURUSER->get('silver');
		}
		//check the gold
		if ($CURUSER->get('gold') < $gold)
		{
			$goldNeeded = $gold - $CURUSER->get('gold');
		}
		//assamble the message
		if (isset($silverNeeded) and isset($goldNeeded))
		{
			$text .= ' You are '. $silverNeeded .' silver and '. $goldNeeded .' gold short.';
		}
		else
		{
			if (isset($silverNeeded))
			{
				$text .= ' You are '. $silverNeeded .' silver short.';
			}
			else
			{
				$text .= ' You are '. $goldNeeded .' gold short.';
			}
		}
		
		//print
		echo $text;
	}
}
else if ($silver == 0 and $gold > 0)
{
	//check the gold
	if ($CURUSER->get('gold') >= $gold)
	{
		echo 'OK';
	}
	else
	{
		$text = 'Not enough money.';
		$text .= ' You are '. ($gold - $CURUSER->get('gold')) .' gold short.';

		//print
		echo $text;
	}
}
else if ($silver > 0 and $gold == 0)
{
	//check the gold
	if ($CURUSER->get('silver') >= $silver)
	{
		echo 'OK';
	}
	else
	{
		$text = 'Not enough money.';
		$text .= ' You are '. ($silver - $CURUSER->get('silver')) .' silver short.';

		//print
		echo $text;
	}
}
else
{
	echo 'Error: The script has noting to do...';
}

exit;