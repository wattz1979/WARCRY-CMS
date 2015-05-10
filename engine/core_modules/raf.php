<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class RAF
{
	public function __construct()
	{
		return true;
	}
	
	public function CreateLink($account, $recruiter)
	{
		global $DB, $CORE;
		
		$status = RAF_LINK_PENDING;
		$date = $CORE->getTime();
				
		$insert = $DB->prepare("INSERT INTO `raf_links` (`account`, `recruiter`, `date`, `status`) VALUES (:acc, :rec, :date, :status);");
		$insert->bindParam(':acc', $account, PDO::PARAM_INT);
		$insert->bindParam(':rec', $recruiter, PDO::PARAM_INT);
		$insert->bindParam(':date', $date, PDO::PARAM_STR);
		$insert->bindParam(':status', $status, PDO::PARAM_INT);
		$insert->execute();
		
		if ($insert->rowCount() > 0)
		{
			unset($insert);
			return true;
		}
		else
		{
			unset($insert);
			return false;
		}
	}
	
	public function FindHash($hash)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT * FROM `raf_hash` WHERE `hash` = :hash LIMIT 1;");
		$res->bindParam(':hash', $hash, PDO::PARAM_STR);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$row = $res->fetch();
			unset($res);
			return $row;
		}
		else
		{
			unset($res);
			return false;
		}
	}
	
	public function GetCuruserHash()
	{
		global $CURUSER, $DB;
		
		$res = $DB->prepare("SELECT * FROM `raf_hash` WHERE `account` = :acc LIMIT 1;");
		$res->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$row = $res->fetch();
			unset($res);
			return $row['hash'];
		}
		else
		{
			unset($res);
			return $this->GenerateHash();
		}
	}
	
	private function GenerateHash()
	{
		global $CURUSER, $DB;
		
		$hash = sha1($CURUSER->get('id') . '-' . time());
		
		$insert = $DB->prepare("INSERT INTO `raf_hash` (`account`, `hash`) VALUES (:acc, :hash);");
		$insert->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
		$insert->bindParam(':hash', $hash, PDO::PARAM_STR);
		$insert->execute();
		
		if ($insert->rowCount() > 0)
		{
			unset($insert);
			return $hash;
		}
		else
		{
			unset($insert);
			return false;
		}
	}
	
	public function GetPendingLinks($acc)
	{
		global $DB;
		
		$status = RAF_LINK_PENDING;
		
		$res = $DB->prepare("SELECT * FROM `raf_links` WHERE `recruiter` = :acc AND `status` = :status ORDER BY id DESC;");
		$res->bindParam(':acc', $acc, PDO::PARAM_INT);
		$res->bindParam(':status', $status, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			return $res;
		}
		else
		{
			unset($res);
			return false;
		}
	}

	public function GetActiveLinks($acc)
	{
		global $DB;
		
		$status = RAF_LINK_ACTIVE;
		
		$res = $DB->prepare("SELECT * FROM `raf_links` WHERE `recruiter` = :acc AND `status` = :status ORDER BY id DESC;");
		$res->bindParam(':acc', $acc, PDO::PARAM_INT);
		$res->bindParam(':status', $status, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			return $res;
		}
		else
		{
			unset($res);
			return false;
		}
	}

	public function GetReferralsCount($acc)
	{
		global $DB;
		
		$status = RAF_LINK_ACTIVE;
		
		$res = $DB->prepare("SELECT COUNT(*) FROM `raf_links` WHERE `recruiter` = :acc AND `status` = :status;");
		$res->bindParam(':acc', $acc, PDO::PARAM_INT);
		$res->bindParam(':status', $status, PDO::PARAM_INT);
		$res->execute();	
		$count_row = $res->fetch(PDO::FETCH_NUM);
		$count = $count_row[0];
		unset($count_row);
		unset($res);
		
		return $count;
	}

	public function __destrruct()
	{
		return true;
	}
}