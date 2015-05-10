<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class purchaseLog
{
	private $lastLogId = NULL;
	
	public function __construct()
	{
		return true;
	}
	
	public function add($source, $message, $status)
	{
		global $DB, $CORE, $CURUSER;
		
		$account = $CURUSER->get('id');
		$time = $CORE->getTime();
		
		$insert = $DB->prepare("INSERT INTO `purchase_log` (`account`, `source`, `text`, `time`, `status`) VALUES (:account, :source, :text, :time, :status);");
		$insert->bindParam(':account', $account, PDO::PARAM_INT);
		$insert->bindParam(':source', $source, PDO::PARAM_STR);
		$insert->bindParam(':text', $message, PDO::PARAM_STR);
		$insert->bindParam(':time', $time, PDO::PARAM_STR);
		$insert->bindParam(':status', $status, PDO::PARAM_STR);
		$insert->execute();
		
		//check if the record was inserted
		if ($insert->rowCount() > 0)
		{
			//update the last log id var
			$this->lastLogId = $DB->lastInsertId();
			
			unset($insert);
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function update($logId, $message, $status = false)
	{
		global $DB;
		
		if (!$logId)
		{
			$logId = $this->lastLogId;
		}
		
		$update = $DB->prepare("UPDATE `purchase_log` SET `text` = CONCAT(`text`, ' | Update: ', :text) ".($status ? ", `status` = :status" : "")." WHERE `id` = :logId LIMIT 1;");
		$update->bindParam(':text', $message, PDO::PARAM_STR);
		if ($status)
		{
			$update->bindParam(':status', $status, PDO::PARAM_STR);
		}
		$update->bindParam(':logId', $logId, PDO::PARAM_INT);
		$update->execute();
		
		//check if the record was inserted
		if ($update->rowCount() > 0)
		{
			unset($update);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function __destrruct()
	{
		return true;
	}
}