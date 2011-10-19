<? 
	require("src/prepend.inc.php"); 
	$display['load_extjs'] = true;
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}

	$display["title"] = _("Tasks scheduler");
	
	if (isset($post_cancel))
		UI::Redirect("scheduler.php");

	if ($req_action)
	{
		$Validator = new Validator();
		
		if (!is_array($req_id))
			$req_id = array($req_id);
		
		foreach ($req_id as $task_id)
		{
			if(!$Validator->IsNumeric($task_id))
				continue;
			
			switch($req_action)
			{
				case "delete":
					
					$db->Execute("DELETE FROM scheduler_tasks WHERE id = ? AND client_id = ?",
						array($task_id, Scalr_Session::getInstance()->getClientId())
					);
					$okmsg = _("Selected task(s) successfully removed");
					
					break;
					
				case "activate":
					
					$db->Execute("UPDATE scheduler_tasks SET `status` = ? WHERE id = ? AND `status` = ? AND client_id = ?",
						array(TASK_STATUS::ACTIVE, $task_id, TASK_STATUS::SUSPENDED, Scalr_Session::getInstance()->getClientId())
					);
					$okmsg = _("Selected task(s) successfully activated");
					
					break;
					
				case "suspend":
					
					$db->Execute("UPDATE scheduler_tasks SET `status` = ? WHERE id = ? AND `status` = ? AND client_id = ?",
						array(TASK_STATUS::SUSPENDED, $task_id, TASK_STATUS::ACTIVE, Scalr_Session::getInstance()->getClientId())
					);
					$okmsg = _("Selected task(s) successfully suspended");
					
					break;
			}
		}	
		
		UI::Redirect("scheduler.php");
	}
	
	require("src/append.inc.php");
?>