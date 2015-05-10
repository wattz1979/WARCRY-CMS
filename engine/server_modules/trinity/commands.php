<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class server_Commands
{
	public function __construct()
	{
		return true;
	}

	public function CheckConnection($realmid)
	{
		global $CORE;
		
		//try to send the items
		$soapMsg = $CORE->ExecuteSoapCommand('.help', $realmid);
				
		//check if the mail was sent
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}
		
	public function sendItems($charName, $items, $subject, $realmid)
	{
		global $CORE;
		
		//try to send the items
		$soapMsg = $CORE->ExecuteSoapCommand('.send items '.$charName.' "'.$subject.'" "Thank you for your contribution and loyalty towards Warcry WoW. Here is your reward that you deserve. Have a nice day here at Warcry WoW and Don\'t forget to vote! Regards,
		Warcry WoW" '.$items, $realmid);
				
		//check if the mail was sent
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}
	
	public function sendMoney($charName, $money, $subject, $realmid)
	{
		global $CORE;
		
		//try to send the money
		$soapMsg = $CORE->ExecuteSoapCommand('.send money '.$charName.' "'.$subject.'" "Thank you for your contribution and loyalty towards Warcry WoW. Here is your reward that you deserve. Have a nice day here at Warcry WoW and Don\'t forget to vote! Regards,
		Warcry WoW" '.$money, $realmid);
				
		//check if the mail was sent
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}

	public function levelTo($charName, $level, $realmid)
	{
		global $CORE;
		
		$soapMsg = $CORE->ExecuteSoapCommand('.character level '.$charName.' '.$level, $realmid);
		
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}
	
	public function FactionChange($charName, $realmid)
	{
		global $CORE;
		
		$soapMsg = $CORE->ExecuteSoapCommand('.character changefaction '.$charName, $realmid);
		
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}
	
	public function RaceChange($charName, $realmid)
	{
		global $CORE;
		
		$soapMsg = $CORE->ExecuteSoapCommand('.character changerace '.$charName, $realmid);
		
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}
	
	public function Customize($charName, $realmid)
	{
		global $CORE;
		
		$soapMsg = $CORE->ExecuteSoapCommand('.character customize '.$charName, $realmid);
		
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}

	public function Revive($charName, $realmid)
	{
		global $CORE;
		
		$soapMsg = $CORE->ExecuteSoapCommand('.revive '.$charName, $realmid);
		
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}
	
	public function Teleport($charName, $x, $y, $z, $mapId, $realmid)
	{
		global $CORE;
		
		$soapMsg = $CORE->ExecuteSoapCommand('.pteleport '.$charName.' '.$x.' '.$y.' '.$z.' '.$mapId, $realmid);
		
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}
	
	public function RefundItem($entry, $charName, $realmid)
	{
		global $CORE;
		
		$soapMsg = $CORE->ExecuteSoapCommand('.refunditem '.$charName.' '.$entry, $realmid);
		
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}
	
	public function __destruct()
	{
		return true;
	}
}
