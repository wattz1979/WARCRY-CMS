<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Session
{		
	public function __construct()
	{		
		// set default sessions save handler
		ini_set('session.save_handler', 'files'); 
	}

	protected function _start()
	{
	  global $config;
	  
		session_name($config['AuthCookieName'].'_hash');

		session_start();
				
		return true;
	}

	public function register()
	{

		//Start the session if needed
		if(!isset($_SESSION))
		{
		  $this->_start();
		}

	}
	
    public function _open($save_path, $session_name)
	{	
	  return true;
    }

    public function _close() {
        return true;
    }

    public function _read($id)
	{
      return true; 
    }

    public function _write($id, $data)
	{
      return true; 
    }

    public function _destroy($id)
	{
	  return true;
    }

    public function _clean($max)
	{
   	  return true;
    }			
}