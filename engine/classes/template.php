<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Template
{
	private $config;
	
	/* 
		Css Resources to be loaded with the header
		Two arrays containing diferent priority of load
	*/
	private $CssFiles = array(
		RESOURCE_LOAD_PRIO_HIGH => array(),
		RESOURCE_LOAD_PRIO_LOW	=> array()
	);
	
	/* 
		Javascript Resources to be loaded with the header
		Two arrays containing diferent priority of load
	*/
	private $HeaderJsFiles = array(
		RESOURCE_LOAD_PRIO_HIGH => array(),
		RESOURCE_LOAD_PRIO_LOW	=> array()
	);
	
	/* 
		Javascript Resources to be loaded with the footer
		Two arrays containing diferent priority of load
	*/
	private $FooterJsFiles = array(
		RESOURCE_LOAD_PRIO_HIGH => array(),
		RESOURCE_LOAD_PRIO_LOW	=> array()
	);
	
	/*
		Default Template Parameters
	*/
	private $Parameters = array(
		'title'				=> '',
		'slider'			=> false,
		'topbar'			=> false
	);
	
	public function __construct()
	{
		global $config;
		
		$this->config = $config;
	}
	
	public function SetParameters($parameters)
	{
		if (is_array($parameters) && !empty($parameters))
		{
			foreach ($parameters as $key => $param)
			{
				$this->Parameters[$key] = $param;
			}
			
			return true;
		}
		
		return false;
	}
	
	public function GetParameters()
	{
		return $this->Parameters;
	}
	
	public function SetParameter($key, $param)
	{
		$this->Parameters[$key] = $param;
	}
	
	public function GetParameter($key)
	{
		if (isset($this->Parameters[$key]))
			return $this->Parameters[$key];
		
		return false;
	}
	
	/*
		A simple function to set page title
	*/
	public function SetTitle($title)
	{
		$this->Parameters['title'] = $title;
	}
	
	public function AddCss($file, $remote = false, $priority = RESOURCE_LOAD_PRIO_LOW)
	{
		$this->CssFiles[$priority][] = array('file' => $file, 'remote' => $remote);
	}
	
	public function AddHeaderJs($file, $remote = false, $priority = RESOURCE_LOAD_PRIO_LOW)
	{
		$this->HeaderJsFiles[$priority][] = array('file' => $file, 'remote' => $remote);
	}
	
	public function AddFooterJs($file, $remote = false, $priority = RESOURCE_LOAD_PRIO_LOW)
	{
		$this->FooterJsFiles[$priority][] = array('file' => $file, 'remote' => $remote);
	}
	
	public function PrintCSS()
	{
		if (!empty($this->CssFiles[RESOURCE_LOAD_PRIO_HIGH]) || !empty($this->CssFiles[RESOURCE_LOAD_PRIO_LOW]))
		{
			//merge to load under single resource
			$string = '';

			//Let's print the CSS Files
			//Note there are remote loads
			//Note there are two levels of priority
			
			//Starting with high priority
			foreach ($this->CssFiles[RESOURCE_LOAD_PRIO_HIGH] as $css)
			{
				//handle remote load
				if ($css['remote'])
				{
					//print right away
					echo '<link rel="stylesheet" href="', $css['file'], '" />';
				}
				else
				{
					$string .= $css['file'].',';
				}
			}
			unset($css);
			
			//Now low priority
			foreach ($this->CssFiles[RESOURCE_LOAD_PRIO_LOW] as $css)
			{
				//handle remote load
				if ($css['remote'])
				{
					//print right away
					echo '<link rel="stylesheet" href="', $css['file'], '" />';
				}
				else
				{
					$string .= $css['file'].',';
				}
			}
			unset($css);
			
			if ($string != '')
			{
				//remove the last ","
				$string = substr($string, 0, strlen($string) - 1);
			
				echo '<link rel="stylesheet" href="', $this->config['BaseURL'], '/resources/min/?f=', $string, '" />';
			}
	
			unset($string);
		}
	}
	
	public function PrintJavascripts($array)
	{
		if (is_array($array) && (!empty($array[RESOURCE_LOAD_PRIO_HIGH]) || !empty($array[RESOURCE_LOAD_PRIO_LOW])))
		{
			//merge to load under single resource
			$string = '';

			//Let's print the Js Files
			//Note there are remote loads
			//Note there are two levels of priority
			
			//Starting with high priority
			foreach ($array[RESOURCE_LOAD_PRIO_HIGH] as $js)
			{
				//handle remote load
				if ($js['remote'])
				{
					//print right away
					echo '<script type="text/javascript" src="', $js['file'], '"></script>';
				}
				else
				{
					$string .= $js['file'].',';
				}
			}
			unset($js);
			
			//Now low priority
			foreach ($array[RESOURCE_LOAD_PRIO_LOW] as $js)
			{
				//handle remote load
				if ($js['remote'])
				{
					//print right away
					echo '<script type="text/javascript" src="', $js['file'], '"></script>';
				}
				else
				{
					$string .= $js['file'].',';
				}
			}
			unset($js);
			
			if ($string != '')
			{
				//remove the last ","
				$string = substr($string, 0, strlen($string) - 1);
			
				echo '<script type="text/javascript" src="', $this->config['BaseURL'], '/resources/min/?f=', $string, '"></script>';
			}
	
			unset($string);
		}
	}
	
	public function PrintHeaderJavascripts()
	{
		$this->PrintJavascripts($this->HeaderJsFiles);
	}
	
	public function PrintFooterJavascripts()
	{
		$this->PrintJavascripts($this->FooterJsFiles);
	}
	
	public function LoadHeader()
	{
		global $CORE, $CURUSER, $DB, $pageName, $TPL;

		$HeaderTitle = $this->config['SiteName'] . ($this->Parameters['title'] != '' ? ' - ' . $this->Parameters['title'] : '');
				
		$config = $this->config;
		
		define('init_template', true);
	
		require_once $this->config['RootPath'] . '/template/header.php';
	}
	
	public function LoadFooter()
	{
		global $config, $TPL;
		
		require_once $this->config['RootPath'] . '/template/footer.php';
	}
	
	public function UnderConstruction($title)
	{
		global $config, $TPL;
		
		$this->LoadHeader('Under Construction');
		
		$PAGE_TITLE = $title;
		
		include $config['RootPath'] . '/template/pages/construction.php';
		
		$this->LoadFooter();
		exit;
	}
	
	public function BufferFlush()
	{
		echo "\n\n<!-- Deal with browser-related buffering by sending some incompressible strings -->\n\n";

    	while (ob_get_level())
        	ob_end_flush();

    	if(ob_get_length())
		{
			@ob_flush();
			@flush();
			@ob_end_flush();
    	}
    	@ob_start();
	}
	
	public function __destrruct()
	{
		unset($this->config, $this->CssFiles, $this->HeaderJsFiles, $this->FooterJsFiles, $this->Parameters);
	}
}