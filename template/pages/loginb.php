<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//check if we just had the login
if (!isset($_SESSION['JustLoggedIn']))
{
   header("Refresh: 0; url=".$config['BaseURL']."/index.php");
   exit();
}

$url = false;
//check if we have URL the user wanted to access before we ask to login
if (isset($_SESSION['url_bl']))
{
	//check if it is valid URL
	if($CORE->ValidateURLBeforeLogin($_SESSION['url_bl']))
	{
		$url = trim($_SESSION['url_bl']);
	}
	unset($_SESSION['url_bl']);
}

//default url
if (!$url)
{
	$url = $config['BaseURL'] . '/index.php';
}

//Set the title
$TPL->SetTitle('Sign In');
//Print the header
$TPL->LoadHeader();

?>

 <div class="sub-page-title">
  <div id="title"><h1>Login<p></p><span></span></h1></div>
 </div>
 
 <div class="container_2" align="center">
  <div class="vertical_center" align="center">
     
   <div class="container_3" align="center">
   		
        <div class="login-success">
            <h1>Login Successful</h1>
            <p>Please wait...</p>
        </div>
   
   </div>
   
  </div>
 </div>
 
<?php

	//Load the footer
	$TPL->LoadFooter();
	
	//Flush the page to the buffer
	$TPL->BufferFlush();
		
	//check for referral activations
	//load the characters module
	$CORE->load_CoreModule('raf');
	//load the characters handling class
	$CORE->load_ServerModule('character');
	//setup the raf class
	$raf = new RAF();
	//construct the characters handler
	$chars = new server_Character();
	
	/* The new way of activating RAF Links */
	
	//check if we have recruiter
	if ($CURUSER->get('recruiter') > 0)
	{
		//find the record
		$res = $DB->prepare("SELECT * FROM `raf_links` WHERE `account` = :acc AND `recruiter` = :rec LIMIT 1;");
		$res->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
		$res->bindParam(':rec', $CURUSER->get('recruiter'), PDO::PARAM_INT);
		$res->execute();
		
		//check if we have the link
		if ($res->rowCount() > 0)
		{
			//fetch
			$row = $res->fetch();
			
			//check if the link status is pending
			if ($row['status'] == RAF_LINK_PENDING)
			{
				//check for activation
				//cooldowns
				$cooldown = $CURUSER->getCooldown('RAF_REF_UP');
				$cooldownTime = '15 minutes';
		
				//check the cooldown, we dont want users to spamm our databases
				if (!$cooldown or time() > $cooldown)
				{
					//define that we have not found a character yet
					$found = false;
					//define that we have not met the requirements for the status change
					$requirementsMet = false;
					//find the hightest level character in all the realms
					//loop the realms
					foreach ($realms_config as $RealmId => $RealmData)
					{
						//set the realm id
						if ($chars->setRealm($RealmId))
						{
							//now find it
							if ($charRow = $chars->FindHightestLevelCharacter($row['account']))
							{
								$found = true;
								//check if the character meets the requirements
								if ($charRow['class'] == 6)
								{
									//if the character is DK
									if ($charRow['level'] >= 80)
									{
										//the character meets the requirements
										$requirementsMet = true;
									}
								}
								else
								{
									//any other class than DK
									if ($charRow['level'] >= 60)
									{
										//the character meets the requirements
										$requirementsMet = true;
									}
								}
								//if the character meet's the requirements
								if ($requirementsMet)
								{
									//update the status and statusText
									$statusText = '<b>'.$charRow['name'].'</b> '.$chars->getClassString($charRow['class']).' Level '.$charRow['level'].'</p>';
									$status = RAF_LINK_ACTIVE;
									//query
									$update = $DB->prepare("UPDATE `raf_links` SET `statusText` = :text, `status` = :status, `cDate` = :time WHERE `id` = :id LIMIT 1;");
									$update->bindParam(':id', $row['id'], PDO::PARAM_INT);
									$update->bindParam(':text', $statusText, PDO::PARAM_STR);
									$update->bindParam(':status', $status, PDO::PARAM_INT);
									$update->bindParam(':time', $CORE->getTime(), PDO::PARAM_STR);
									$update->execute();
									unset($update);
									//the link is active save that info to the CURUSER class
									$CURUSER->setRecruiterLinkState(RAF_LINK_ACTIVE);
									//break the realm loop for this referral
									break 1;
								}
								unset($requirementsMet);
							}
							unset($charRow);
						}
					} //end of the realms loop
					//if we had no characters for this referral update the status text
					if (!$found)
					{
						$statusText = 'No character was found';
						$update = $DB->prepare("UPDATE `raf_links` SET `statusText` = :text WHERE `id` = :id LIMIT 1;");
						$update->bindParam(':id', $row['id'], PDO::PARAM_INT);
						$update->bindParam(':text', $statusText, PDO::PARAM_STR);
						$update->execute();
						unset($update);
					}
					unset($found);
					//set a cooldown on this update
					$CURUSER->setCooldown('RAF_REF_UP', strtotime('+'.$cooldownTime));
				} //here ends the IF Cooldown
				unset($cooldown, $cooldownTime);
			}
			else if ($row['status'] == RAF_LINK_ACTIVE)
			{
				//the link is active save that info to the CURUSER class
				$CURUSER->setRecruiterLinkState(RAF_LINK_ACTIVE);
			}
			unset($row);
		} //IF the record is found ends here
		unset($res);
	}
	
	unset($chars);
	unset($raf);
	
	####################################################################
	############ Find the account last vote date time ##################
	
	$res = $DB->prepare("SELECT * FROM `vote_data` WHERE `account` = :acc ORDER BY timestamp DESC LIMIT 1;");
	$res->bindParam(':acc', $CURUSER->get('id'), PDO::PARAM_INT);
	$res->execute();
	
	if ($res->rowCount() > 0)
	{
		$row = $res->fetch();
		$CURUSER->setLastVoteTime($row['timestamp']);
		unset($row);
	}
	unset($res);
	
	//unset the page pass
	unset($_SESSION['JustLoggedIn']);
	//redirect to the correct page
	echo '<meta http-equiv="refresh" content="1;URL=\'', $url, '\'">';
?>