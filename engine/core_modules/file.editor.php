<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class FileEditor
{
	var $myfile;
	var $file_array;
	
	public function __construct($file){
				
		try
        {
			$this->check_file($file);
		}
		catch(Exception $e)
        {
			echo 'Message: ' .$e->getMessage();
			die;
        }
		
		$this->myfile = $file;
		
	}
	
	private function check_file($file){
		
		if(!file_exists($file)){
			throw new Exception("FileEditor wasnt able to load file.<br><br> at file: ".__FILE__." line: ".__LINE__);
		}
		
		if(!is_writable($file)){
			if(!chmod($file, 0777)){
				throw new Exception("FileEditor wasnt able to set file read/write file permissions.");
			}
		}
		
    }
	
	private function _lines2array(){
		
		$handle = @fopen($this->myfile, "r");
		
        if ($handle) {
			
            while (($buffer = fgets($handle)) !== false) {
                $this->file_array[] = $buffer;
            }
		
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
		
        fclose($handle);
        }
		
    }
	
	public function prepare_array(){
		$this->_lines2array();
	}
	
	public function array_print(){
		
		echo '<pre>';
		print_r($this->file_array);
		echo '</pre>';
		
	}
	
	public function get_line($line){
		
		if (isset($this->file_array[$line])){
		    $return = $this->file_array[$line];
		} else {
			$return = false;
		}
	 
	 return $return;	
	}
	
	public function search($str, $sens = true){
								
		foreach ($this->file_array as $key => $value){
			
			if ($sens){
						
			    if (strlen(strstr($value,$str))>0){
				    $return = $key;
				    break;
			    }
				
			} else {
				
				if (strlen(stristr($value,$str))>0){
				    $return = $key;
				    break;
			    }

			}
			
		}
		
		if(!isset($return)){
			$return = false;
		}
		
	 return $return;
	}
	
	public function str_replace_at_line($find, $replace, $line, $sens = true){
		
		if (isset($this->file_array[$line])){
			$stack = $this->file_array[$line];
		} else {
			$stack = false;
		}
		
		if ($stack){
		    if ($sens){
			    $this->file_array[$line] = str_ireplace($find, $replace, $stack);
		    } else {
			    $this->file_array[$line] = str_replace($find, $replace, $stack);
		    }
		}
		
	}
	
	public function change_line($line, $str){
		
		if (isset($this->file_array[$line])){
			$this->file_array[$line] = $str . "\n";
			$return = true;
		} else {
			$return = false;
		}
	 
	 return $return;	
	}
		
	public function push_line_after($line, $str){
		
		$i = 0;
		foreach ($this->file_array as $key => $value){
			
			$this->file_array[$i] = $value;
						
			if ($key == $line){
				
				$i = $i + 1;
				
				$this->file_array[$i] = $str . "\n";
				
			}
			
		 $i++;	
		}
		
	}
	
	public function delete_line($line){
		
		unset($this->file_array[$line]);
		
		$i = 0;
		foreach($this->file_array as $key => $value){
			
			unset($this->file_array[$key]);
			
			$this->file_array[$i] = $value;
			
		 $i++;
		}
		
	}
	public function append($str){
	    
		if (!is_array($this->file_array))
		  $this->file_array = array();
		
		$str = $str . "\n";
		
		//returns the new number
		$push = array_push($this->file_array, $str);
		
	  return $push;
	}
	
	public function write(){
		
		$file_string = '';
		
		foreach ($this->file_array as $key => $value){
						
			$file_string .= $value;
			
		}
        
		$write = @file_put_contents($this->myfile, $file_string);
	    
		@chmod($this->myfile, 0644);
		
		unset($file_string);
		
	 return $write;	
	}

	public function __destruct(){
		unset($this->myfile);
	    unset($this->file_array);
	}
	
	//This function is special for array configs
	public function changeConfig($key, $value){
		
		//Let's check if your content starts with array, because in that case we dont need ' ' around  the value
		//Get the starting of the whole string, 0 to 6 exactly 7 characters lenght " array "
		$arr_str = substr ($value, 0, 7);
		
		//check exactly for the word array
		if (preg_match("/\barray\b/", $arr_str))
		  $is_array = true;
		else
		  $is_array = false;
				
		//Search for the line with our config
		$line = $this->search($key, false);
		
		//make new string for the line
		//if the our value start with array dont include ' '
		if ($is_array)
		{
		  	$new_string = $key . " = ".$value.";";
		}
		else
		{
		  	$new_string = $key . " = '".$value."';";
		}
		
		//If we have the line
		if($line)
  		  $return = $this->change_line($line, $new_string);
		else //else append new line
  		  $return = $this->append($new_string);
      
	  //Returns true if our edit is successfull, returns the number of the line if append was executed, else if the change_line faild returns false (this is rare)
	  return $return;
	}
	
}