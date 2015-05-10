<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class ItemRefundSystem
{
	static public function AddRefundable($entry, $price, $currency, $character, $account = false)
	{
		global $CORE, $DB, $CURUSER;
		
		if (!$account)
			$account = $CURUSER->get('id');
		
		$time = $CORE->getTime();
		$status = IRS_STATUS_NONE;
		
		$insert = $DB->prepare("INSERT INTO `refundable_items` (`entry`, `price`, `currency`, `character`, `account`, `time`, `status`) VALUES (:entry, :price, :currency, :character, :account, :time, :status);");
		$insert->bindParam(':entry', $entry, PDO::PARAM_INT);
		$insert->bindParam(':price', $price, PDO::PARAM_INT);
		$insert->bindParam(':currency', $currency, PDO::PARAM_INT);
		$insert->bindParam(':character', $character, PDO::PARAM_INT);
		$insert->bindParam(':account', $account, PDO::PARAM_INT);
		$insert->bindParam(':time', $time, PDO::PARAM_STR);
		$insert->bindParam(':status', $status, PDO::PARAM_INT);
		$insert->execute();
		
		unset($time, $status);
		
		if ($insert->rowCount() == 0)
		{
			return false;
		}
		
		unset($insert);
		
		return true;
	}
	
	static public function GetRefundables($account = false)
	{
		global $CORE, $DB, $CURUSER;
		
		if (!$account)
			$account = $CURUSER->get('id');
		
		$status = IRS_STATUS_NONE;
		
		//Get the week start and end days
		list($start, $end) = $CORE->getWeekStartEnd();
		
		//Get the refundables for this week
		$res = $DB->prepare("SELECT * FROM `refundable_items` WHERE `account` = :account AND `time` BETWEEN :start AND :end AND `status` = :status ORDER BY `time` DESC;");
		$res->bindParam(':account', $account, PDO::PARAM_INT);
		$res->bindParam(':start', $start, PDO::PARAM_STR);
		$res->bindParam(':end', $end, PDO::PARAM_STR);
		$res->bindParam(':status', $status, PDO::PARAM_INT);
		$res->execute();
		
		unset($start, $end, $status);
		
		if ($res->rowCount() == 0)
		{
			return false;
		}
		
		return $res;
	}
	
	static public function GetRefundsDone($account = false)
	{
		global $CORE, $DB, $CURUSER;
		
		if (!$account)
			$account = $CURUSER->get('id');
		
		$status = IRS_STATUS_REFUNDED;
		
		//Get the week start and end days
		list($start, $end) = $CORE->getWeekStartEnd();
		
		//Get the refundables for this week
		$res = $DB->prepare("SELECT * FROM `refundable_items` WHERE `account` = :account AND `time` BETWEEN :start AND :end AND `status` = :status ORDER BY `time` DESC;");
		$res->bindParam(':account', $account, PDO::PARAM_INT);
		$res->bindParam(':start', $start, PDO::PARAM_STR);
		$res->bindParam(':end', $end, PDO::PARAM_STR);
		$res->bindParam(':status', $status, PDO::PARAM_INT);
		$res->execute();
		
		unset($start, $end, $status);
		
		return $res->rowCount();
	}
	
	static public function RefundableSetStatus($id, $status)
	{
		global $DB, $CORE;
		
		$update = $DB->prepare("UPDATE `refundable_items` SET `status` = :status ".(($status == IRS_STATUS_REFUNDED) ? ", `timeRefunded` = :time" : "")." WHERE `id` = :id LIMIT 1;");
		$update->bindParam(':status', $status, PDO::PARAM_INT);
		$update->bindParam(':id', $id, PDO::PARAM_INT);
		if ($status == IRS_STATUS_REFUNDED)
		{
			$update->bindParam(':time', $CORE->getTime(), PDO::PARAM_STR);
		}
		$update->execute();
		
		if ($update->rowCount() == 0)
		{
			return false;
		}
		
		unset($update);
		
		return true;
	}
	
	static public function GetRefundable($id)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT * FROM `refundable_items` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() == 0)
		{
			return false;
		}
		
		$row = $res->fetch();
		unset($res);
		
		return $row;
	}
	
	static public function SetError($id, $error)
	{
		global $DB;
		
		$status = IRS_STATUS_ERROR;
		
		$update = $DB->prepare("UPDATE `refundable_items` SET `status` = :status, `error` = :error WHERE `id` = :id LIMIT 1;");
		$update->bindParam(':status', $status, PDO::PARAM_INT);
		$update->bindParam(':id', $id, PDO::PARAM_INT);
		$update->bindParam(':error', $error, PDO::PARAM_STR);
		$update->execute();
		
		if ($update->rowCount() == 0)
		{
			return false;
		}
		
		unset($update);
		
		return true;
	}
}