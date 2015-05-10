<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Notifications
{
	private $notifications;
	private $title;
	private $headline;
	private $text;
	private $textAlign = 'left';
	private $autoContinue = false;
	private $continueDelay  = 5;
	
	public function __construct()
	{
		$this->notifications = isset($_SESSION['notifications']) ? $_SESSION['notifications'] : false;
	}
	
	public function SetTitle($title)
	{
		$this->title = ($title == '' ? 'Notification' : $title);
	}
	
	public function SetHeadline($text)
	{
		$this->headline = $text;
	}
	
	public function SetText($text)
	{
		$this->text = $text;
	}
	
	public function SetTextAlign($align)
	{
		$this->textAlign = $align;
	}
	
	public function SetAutoContinue($method)
	{
		$this->autoContinue = $method;
	}
	
	public function SetContinueDelay($seconds)
	{
		$this->continueDelay = (int)$seconds;
	}
	
	public function Apply()
	{
		//check if we dont have notifications so we can convert to array
		if (!$this->notifications)
		{
			$this->notifications = array();
		}
		
		$this->notifications[] = array(
			'title'			=> $this->title,
			'headline'		=> $this->headline,
			'text'			=> $this->text,
			'textAlign'		=> $this->textAlign,
			'autoContinue'	=> $this->autoContinue,
			'delay'			=> $this->continueDelay,
		);
		
		//save to the session
		$_SESSION['notifications'] = $this->notifications;
	}
	
	public function Check()
	{
		if ($this->notifications)
		{
			if (is_array($this->notifications) and count($this->notifications) > 0)
			{
				return true;
			}
		}
		
		return false;
	}
	
	public function AppendUrlToFirst($url = false)
	{
		global $CORE;
		
		if (!$url)
		{
			$url = $CORE->getPageURL();
		}
		
		if (is_array($this->notifications) and count($this->notifications) > 0)
		{
			if ($CORE->ValidateURLBeforeLogin($url))
			{
				$this->notifications[0]['return'] = $url;
			
				//save to the session
				$_SESSION['notifications'] = $this->notifications;
			
				return true;
			}
			else
			{
				//The url is invalid
				return false;
			}
		}
		
		return false;
	}
	
	public function Launch()
	{
		global $config;
		
		$_SESSION['AvailableNotification'] = true;
		
		header("Location: ".$config['BaseURL']."/index.php?page=notification");
		die;
	}
	
	public function GetFirst($destroy = true)
	{
		if (is_array($this->notifications) and count($this->notifications) > 0)
		{
			$return = $this->notifications[0];
			
			if ($destroy)
			{
				//remove the notification
				array_splice($this->notifications, 0, 1);
				
				//save to the session
				$_SESSION['notifications'] = $this->notifications;
			}
			
			return $return;
		}
		
		return false;
	}
	
	public function __destrruct()
	{
		unset($this->notifications, $this->title, $this->headline, $this->text);
	}
}