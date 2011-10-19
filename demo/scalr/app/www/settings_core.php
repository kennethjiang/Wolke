<? 
	require("src/prepend.inc.php"); 
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
	
	$display["title"] = "Settings&nbsp;&raquo;&nbsp;General";
	if ($_POST) 
	{
		unset($_POST['Submit']);
		unset($_POST['id']);
		unset($_POST['page']);
		unset($_POST['f']);
		
		//Pass
		if ($post_pass != "******")
		{
			$db->BeginTrans();
			try 
			{				
			    // Save new password into DB
				$result = $db->Execute("REPLACE INTO config SET `value`=?, `key`=?", array($Crypto->Hash($post_pass), "admin_password"));
				
				// Failed to update
				if (!$result)
				{
					// If we cannot update at least one password, rollback all changes
					$db->RollbackTrans();
					$errmsg = "Cannot update password in database.";
					UI::Redirect("settings_core.php");
				}
				
    			// Commit all changes
    			$db->CommitTrans();				
			} 
			catch (Exception $e)
			{
				// If we cannot update at least one password, rollback all changes
				$db->RollbackTrans();
				$errmsg = "Failed to rehash passwords. ".$e->getMessage();
				UI::Redirect("settings_core.php");
			}
		}
		
		// Regular keys
		foreach($_POST as $k => $v)
		{
			if (!in_array($k, array('pass', 'admin_password', 'logger_password')))
				$db->Execute("REPLACE INTO config SET `value`=?, `key`=?", array(stripslashes($v), $k));
		}
		
		$db->CacheFlush();
		
		$okmsg = "Settings successfully updated";
		UI::Redirect("settings_core.php");
	}
	
	foreach ($db->GetAll("select * from config") as $rsk)
		$cfg[$rsk["key"]] = $rsk["value"];
	
	$display = array_merge($display, array_map('stripslashes', $cfg));
	
	require("src/append.inc.php"); 
?>