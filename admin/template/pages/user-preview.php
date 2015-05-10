<?PHP
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$account = isset($_GET['uid']) ? (int)$_GET['uid'] : false;

?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li><a href="index.php?page=users">Users</a></li>
        <li class="current"><a href="#maintab">User Preview</a></li>
	</ul>
</nav>

<?php
//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_PREV_USERS))
{
	$CORE->ErrorBox('You do not have the required permissions.');
}
?>
          
<!-- The content -->
<section id="content">
    <div class="tab" id="maintab">
        <h2>User Management</h2>
        
        <?php
		if ($success = $ERRORS->successPrint(array('grant_permissions', 'change_rank')))
		{
			echo $success;
		}
		if ($error = $ERRORS->DoPrint(array('grant_permissions', 'change_rank')))
		{
			echo $error;
		}
		unset($error, $success);
		?>
        
        <div>
    	
			<?php
            
				//Find the user records
				$webRes = $DB->prepare("SELECT * FROM `account_data` WHERE `id` = :acc LIMIT 1;");
				$webRes->bindParam(':acc', $account, PDO::PARAM_INT);
				$webRes->execute();
				
				//Verify the user
				if ($webRes->rowCount() == 0)
				{
					echo 'Error: The user id is invalid!';
				}
				else
				{
					//fetch the webrecord
					$webRecord = $webRes->fetch();
					
					//Find the auth record
					$authRes = $AUTH_DB->prepare("SELECT * FROM `account` WHERE `id` = :acc LIMIT 1;");
					$authRes->bindParam(':acc', $account, PDO::PARAM_INT);
					$authRes->execute();
					//Fetch it
					$authRecord = $authRes->fetch();
					
					echo '
					<div class="column left">
						<h3>Web Record</h3>
						<table>';
							
							//Setup the rank
							$Rank = new UserRank($webRecord['rank']);
							//Setup the avatr
							//prepare the avatar
							if ((int)$webRecord['avatarType'] == AVATAR_TYPE_GALLERY)
							{
								$gallery = new AvatarGallery();
								$Avatar = $gallery->get((int)$webRecord['avatar']);
								unset($gallery);
							}
							else if ((int)$webRecord['avatarType'] == AVATAR_TYPE_UPLOAD)
							{
								$Avatar = new Avatar(0, $webRecord['avatar'], 0, AVATAR_TYPE_UPLOAD);
							}
							
							echo '
							<tr><td>ID</td><td>', $webRecord['id'], '</td></tr>
							<tr><td>Display Name</td><td>', $webRecord['displayName'], '</td></tr>
							<tr><td>Silver</td><td>', $webRecord['silver'], '</td></tr>
							<tr><td>Gold</td><td>', $webRecord['gold'], '</td>
							<tr><td>Birthday</td><td>', $webRecord['birthday'], '</td></tr>
							<tr><td>Gender</td><td>', $webRecord['gender'], '</td></tr>
							<tr><td>Country</td><td>', $webRecord['country'], '</td></tr>
							<tr><td style="vertical-align: top">Avatar</td><td><img src="', ($Avatar->type() == AVATAR_TYPE_GALLERY ? $config['BaseURL'] . '/resources/avatars/'.$Avatar->string() : $Avatar->string()), '" /></td></tr>
							<tr>
								<td style="vertical-align: middle">Rank</td>
								<td>', $Rank->string(), ' [', $Rank->int(), ']';
									
									//Is allowed to change users rank
									if ($CURUSER->getPermissions()->isAllowed(PERMISSION_CHANGE_USER_RANK))
									{
										$RanksData = new RankStringData();
										
										echo '
										<div id="change-rank-cont" style="float: right">
											<form method="post" action="execute.php?take=change_user_rank">
												<select name="rank" id="change-rank-select" style="display: inline-block">';
												
													foreach ($RanksData->data as $trank => $name)
													{
														echo '<option value="', $trank, '" ', ($trank == $Rank->int() ? 'selected="selected"' : ''), '>', $name, '</option>';
													}
													
												echo '
												</select>
												<input type="hidden" value="', $webRecord['id'], '" name="id" />
												<input type="button" value="Change" class="button" style="display: inline-block" onclick="this.form.submit()" />
											</form>
										</div>';
									}
									
								echo '
								</td>
							</tr>
							<tr><td>Latest IP</td><td>', $webRecord['last_ip'], '</td></tr>
							<tr><td>Latest Admin IP</td><td>', $webRecord['admin_last_ip'], '</td></tr>
							<tr><td>Registration IP</td><td>', $webRecord['reg_ip'], '</td></tr>
							<tr><td>Latest Login</td><td>', $webRecord['last_login2'], '</td></tr>
							<tr><td>Latest Admin Login</td><td>', $webRecord['admin_last_login2'], '</td></tr>
							<tr><td>Account Status</td><td>', $webRecord['status'], '</td></tr>';
					
					echo '</table>
					</div>
					<div class="column right">
						<h3>Server Record</h3>
						<table>';
							
							echo '
							<tr><td>ID</td><td>', $authRecord['id'], '</td></tr>
							<tr><td>Username</td><td>', $authRecord['username'], '</td></tr>
							<tr><td>Email</td><td>', $authRecord['email'], '</td></tr>
							<tr><td>Join Date</td><td>', $authRecord['joindate'], '</td></tr>
							<tr><td>Latest IP</td><td>', $authRecord['last_ip'], '</td></tr>
							<tr><td>Failed Login Attempts</td><td>', $authRecord['failed_logins'], '</td></tr>
							<tr><td>Locked</td><td>', $authRecord['locked'], '</td></tr>
							<tr><td>Latest Login</td><td>', $authRecord['last_login'], '</td></tr>
							<tr><td>Online Status</td><td>', ($authRecord['online'] == '0' ? 'offline' : 'online'), '</td></tr>
							<tr><td>Expansion</td><td>', $authRecord['expansion'], '</td></tr>
							<tr><td>Mute Time</td><td>', $authRecord['mutetime'], '</td></tr>
							<tr><td>Locale</td><td>', $authRecord['locale'], '</td></tr>
							<tr><td>OS</td><td>', $authRecord['os'], '</td></tr>';
							
							//Check the recruiter
							if ((int)$authRecord['recruiter'] > 0)
							{
								$authRecord['recruiter'] = (int)$authRecord['recruiter'];
								
								$recWebRes = $DB->prepare("SELECT `displayName` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
								$recWebRes->bindParam(':acc', $authRecord['recruiter'], PDO::PARAM_INT);
								$recWebRes->execute();
								
								if ($recWebRes->rowCount() > 0)
								{
									$recWebReco = $recWebRes->fetch();
									
									echo '<tr><td>Recruiter</td><td><a href="index.php?page=user-preview&uid=', $authRecord['recruiter'], '">', $recWebReco['displayName'], '</a></td></tr>';
									
									unset($recWebReco);
								}
								unset($recWebRes);
							}
										
					echo '
						</table>
					</div>
					<div class="clear"></div>';
				}
				
				//Check if we can grant permissions
				if ($CURUSER->getPermissions()->isAllowed(PERMISSION_GIVE_PERMISSIONS))
				{
					echo '
					<div>
						<h3>Admin Control Panel Permissions</h3>';
						
						//Permissions per table row
						$PermPerRow = 5;
						//Defien the manageble permissions
						$PermTable = array
						(
							array('permission' => PERMISSION_GIVE_PERMISSIONS, 	'text' => 'Grant ACP Permissions'),
							array('permission' => PERMISSION_NEWS, 				'text' => 'Manage News'),
							array('permission' => PERMISSION_ARTICLES, 			'text' => 'Manage Articles'),
							array('permission' => PERMISSION_PSTORE, 			'text' => 'Manage Premium Store'),
							array('permission' => PERMISSION_MEDIA_MOVIES, 		'text' => 'Manage Movies'),
							array('permission' => PERMISSION_MEDIA_SREENSHOTS, 	'text' => 'Manage Screenshots'),
							array('permission' => PERMISSION_FORUMS, 			'text' => 'Manage Forums'),
							array('permission' => PERMISSION_FORUM_CATS, 		'text' => 'Manage Forum Categories'),
							array('permission' => PERMISSION_LOGS, 				'text' => 'Preview Logs'),
							array('permission' => PERMISSION_PROMO_CODES, 		'text' => 'Manage Promo Codes'),
							array('permission' => PERMISSION_TICKETS, 			'text' => 'Preview In-Game Tickets'),
							array('permission' => PERMISSION_PREV_BUGTRACKER, 	'text' => 'Preview Bugtracker Reports'),
							array('permission' => PERMISSION_MAN_BUGTRACKER, 	'text' => 'Manage Bugtracker'),
							array('permission' => PERMISSION_PREV_USERS, 		'text' => 'Preview Users'),
							array('permission' => PERMISSION_CHANGE_USER_RANK, 	'text' => 'Change Users Rank'),
							array('permission' => PERMISSION_STORE,				'text' => 'Manage Store'),
						);
						//Setup a permissions object for the user
						$UserPermissions = new Permissions($webRecord['id']);
						
						echo '
						<form method="post" action="execute.php?take=grant_permissions" id="permissions_form">
							<table>
								<tr>';
							
								foreach ($PermTable as $i => $perm)
								{
									echo '
									<td>
										<input type="checkbox" name="permission_', $perm['permission'], '" id="checkbox', $i, '" ', ($UserPermissions->isAllowed($perm['permission']) ? 'checked="checked"' : ''), ' class="hiddenCheckbox">
										<label for="checkbox', $i, '" class="prettyCheckbox checkbox list">
											', $perm['text'], '
										</label>
									</td>';
									
									echo ((($i + 1) % $PermPerRow) == 0 ? '</tr><tr>' : '');
								}
								unset($i, $perm);
						
							echo '
								</tr>
								<tr>
									<td colspan="', $PermPerRow, '"><input type="button" value="Check All" class="button check" /><input type="submit" value="Apply" class="button primary submit" /><p style="float: right; padding-top: 4px; margin: 0;">(Check any of the permissions to grant access to the ACP)</p></td>
								</tr>
							</table>
							
							<input type="hidden" value="', $webRecord['id'], '" name="uid" />
						</form>';
						
						unset($UserPermissions, $PermTable, $PermPerRow);
			
					echo '
					</div>';
				}
				
         		unset($webRes, $authRes); 
			?> 
            
        </div>
    </div>

<script>
	$(document).ready(function()
	{
		$("input.check").toggle(function()
		{
			$("#permissions_form label.checkbox").each(function()
			{
				if (!$(this).hasClass("checked"))
					$(this).trigger("click");
			});
			$(this).val("uncheck all");
		},
		function()
		{
			$("#permissions_form label.checkbox").each(function()
			{
				if ($(this).hasClass("checked"))
					$(this).trigger("click");
			});
			$(this).val("check all");
		});
    });
</script>