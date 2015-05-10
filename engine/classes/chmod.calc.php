<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class ChmodCalc
{
    private $dir;
    private $modes = array('owner' => 0 , 'group' => 0 , 'public' => 0);
    
    public function setOwnermodes($read,$write,$execute) 
	{
        $this->modes['owner'] = $this->setMode($read,$write,$execute);
    }
    
    public function setGroupmodes($read,$write,$execute) 
	{
        $this->modes['group'] = $this->setMode($read,$write,$execute);
    }

    public function setPublicmodes($read,$write,$execute) 
	{
        $this->modes['public'] = $this->setMode($read,$write,$execute);
    }
    
    public function getMode() 
	{
        return 0 . $this->modes['owner'] . $this->modes['group'] . $this->modes['public'];
    }
    
    private function setMode($r,$w,$e) 
	{
        $mode = 0;
        if($r) $mode+=4;
        if($w) $mode+=2;
        if($e) $mode+=1;
        return $mode;
    }
}