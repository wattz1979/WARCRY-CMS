<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class TextCaptcha
{
	private $config;
	
	public function __construct()
	{
		global $config;
		
		$this->config = $config['CAPTCHA'];
		
		return true;
	}

	public function CreateInstance()
	{
		//We will return an array
		$return = array();
		
		// load captcha using web service
		$url = 'http://api.textcaptcha.com/' . $this->config['api_key'];
		
		$OurXML = false;
		
		try
		{
		  $xml = @new SimpleXMLElement($url,null,true);
		} 
		catch (Exception $e) 
		{
			//unable to get qeustion from the TextCaptcha service use one of the stored
			$xml = $this->GetStoredInstance();
			
			//check if we ware able to get one of the stored
			if (!$xml)
			{
				// if there is a problem, use static fallback..
				$fallback = 
				'<captcha>'.
				  '<question>Is ice hot or cold?</question>'.
				  '<answer>'.md5('cold').'</answer>'.
				'</captcha>';
				
				$xml = new SimpleXMLElement($fallback);
				
				//mem
				unset($fallback);
			}
			
			//define that this is our XML
			$OurXML = true;			
		}
		
		if (!$OurXML)
		{
			$this->StoreCaptcha($xml);
		}
			
		// display question as part of form
		$return['question'] = (string)$xml->question;
		  
		//store answers in session
		$ans = array();
		foreach ($xml->answer as $hash)
		{
			//salt the hash
			$salted = md5($hash . $this->config['salt']);
			//store
		  	$ans[] = (string)$salted;
		}
		$_SESSION['CAPTCHA']['Answers'] = $ans;
		
		//free up memory
		unset($ans, $hash, $salted, $xml, $OurXML, $url);
		
		//Generate response field name and save it
		$ReponseFieldName = md5(uniqid() . $this->config['salt'] . time());
		
		$return['ResponseFieldName'] = $ReponseFieldName;
		$_SESSION['CAPTCHA']['ResponseFieldName'] = $ReponseFieldName;
		
		unset($ReponseFieldName);
		
		//return the question
		return $return;
	}
	
	public function CheckAnswer($answer)
	{
		$data = isset($_SESSION['CAPTCHA']) ? $_SESSION['CAPTCHA'] : false;
		
		$return = false;
		
		//check if the captcha data is set
		if ($data)
		{
			//hash the answer
			$hash = md5($answer);
			$salted = md5($hash . $this->config['salt']);
						
			if (isset($_SESSION['CAPTCHA']['Answers']) and is_array($_SESSION['CAPTCHA']['Answers']))
			{
				if (in_array($salted, $_SESSION['CAPTCHA']['Answers']))
				{
					$return = true;
				}
			}
		}
		
		unset($_SESSION['CAPTCHA']['Answers'], $data, $hash, $salted);
		
		return $return;
	}
	
	public function Kill()
	{
		unset($_SESSION['CAPTCHA']);
	}
	
	public function GetResponseFieldName()
	{
		if (isset($_SESSION['CAPTCHA']['ResponseFieldName']))
		{
			return $_SESSION['CAPTCHA']['ResponseFieldName'];
		}
		
		return false;
	}
	
	private function StoreCaptcha($xml)
	{
		global $DB;
		
		//parse the question
		$question = (string)$xml->question;
		
		$checkValue = md5($question);
		//check if we already have that question
		$res = $DB->prepare("SELECT `id` FROM `text_captcha` WHERE `questionHash` = :hash LIMIT 1;");
		$res->bindParam(':hash', $checkValue, PDO::PARAM_STR);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			//already have that one
			return false;
		}
		
		unset($res);
		
		//parse the answers and put em in a string with \n as separator
		$answers = "";
		foreach ($xml->answer as $hash)
		{
			//store
		  	$answers .= (string)$hash . "\n";
		}
		
		//save to the DB
		$insert = $DB->prepare("INSERT INTO `text_captcha` (`questionHash`, `question`, `answers`) VALUES (:hash, :question, :answers);");
		$insert->bindParam(':hash', $checkValue, PDO::PARAM_STR);
		$insert->bindParam(':question', $question, PDO::PARAM_STR);
		$insert->bindParam(':answers', $answers, PDO::PARAM_STR);
		$insert->execute();
		
		//free memory
		unset($insert, $checkValue, $question, $answers, $hash, $xml);
		
		return true;
	}
	
	private function GetStoredInstance()
	{
		global $DB;
		
		//get random offset
		$offset_result = $DB->query("SELECT FLOOR(RAND() * COUNT(*)) AS `offset` FROM `text_captcha`;");
		$offset_row = $offset_result->fetch();
		$offset = $offset_row['offset'];
		//free up some memory
		unset($offset_row, $offset_result);
		
		$res = $DB->query("SELECT `question`, `answers` FROM `text_captcha` LIMIT ".$offset.", 1;");
		
		//check if we have a record
		if ($res->rowCount() > 0)
		{
			$row = $res->fetch();
			unset($res);
			
			//get the answers from the string
			$answers = explode("\n", $row['answers']);
			
			//construct the XML
			$fallback = 
		  	'<captcha>'.
			  	'<question>'.$row['question'].'</question>';
			  	//put in the answers
				foreach ($answers as $hash)
				{
			  		$fallback .= '<answer>'.$hash.'</answer>';
				}
				
			$fallback .= 
			'</captcha>';
			
		  	$xml = new SimpleXMLElement($fallback);
			
			//free memory
			unset($row, $answers, $fallback, $hash);
			
			//return the XML
			return $xml;
		}
		unset($res);
		
		//we dont have any questions stored	
		return false;
	}
	
	public function __destrruct()
	{
		unset($this->config);
		return true;
	}
}