<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class SecretQuestionData
{
	public $data = array(
		'1' => 'Your city of birth?',
		'2' => 'Mother’s city of birth? ',
		'3' => 'Father’s city of birth?',
		'4' => 'Model of your first car?',
		'5' => 'Best friend in high school?',
		'6' => 'First elementary school I attended?',
		'7' => 'What was your first WoW character name?',
		'8' => 'Name of your first pet?',
	);

	public function __construct()
	{
		return true;
	}
	
	public function get($key)
	{
		if (!isset($this->data[$key]))
		{
			return false;
		}
		
		return $this->data[$key];
	}
	
	public function __destruct()
	{
		unset($this->data);
		return true;
	}
}
