<? 

	require("src/prepend.inc.php");
	
    if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
    
	////////////////////////////////
	//// Ajax request for task save
	////////////////////////////////
	
    if ($req_action == 'SaveTask')
    {
		try
		{
    		require(dirname(__FILE__)."/src/scheduler_task_save.php");
    		
			if(!$okmsg)
			{
				$err[] = "Empty result. Error is not defined";		
					
				print json_encode(array(
		    		"result"	=> "error",
		    		"msg"		=> $err
	    		));	
			}
			else
			{
				print json_encode(array(
	    			"result"	=> "ok",
					"msg"		=> $okmsg,
					"args"		=> $return_script_args
	    		));
			}
    		
		}
		catch(Exception $e)
		{
	    	print json_encode(array(
	    		"result"	=> "error",
	    		"msg"		=> $err
    		));
		}
		
		exit();
    }
    
    ////////////
	// EDIT TASK
	////////////
    if($req_task == 'edit')
    {
    	$display["title"] = _("Edit task");
    	$display["create_form"] = false;
    	try
    	{
    		$taskId = (int)$req_id;

	    	if($taskId && $taskId > 0)
	    	{   	
	    		$taskInfo = $db->GetRow("SELECT * from scheduler_tasks WHERE id = ?",array($taskId));
	    		if(!$taskInfo)
	    			throw new Exception(_("Task #{$taskId} not found"));	
	    	}
	    	else
	    		throw new Exception(_("Task #{$taskId} not found"));

	    	if (Scalr_Session::getInstance()->getClientId() && Scalr_Session::getInstance()->getClientId() != $taskInfo['client_id'])
				UI::Redirect("scheduler.php");
				
			// display the using farm, role or instance
			$DBFarm = null;

			switch($taskInfo['target_type'])
			{
				case SCRIPTING_TARGET::FARM:

					$DBFarm = DBFarm::LoadByID($taskInfo['target_id']);
				
					$display['farminfo'] = array(
						'id' => $DBFarm->ID, 
						'name' => $DBFarm->Name, 
						'clientid' => $DBFarm->ClientID
					);
					break;

				case SCRIPTING_TARGET::ROLE: 

					$DBFarmRole = DBFarmRole::LoadByID($taskInfo['target_id']);

					$DBFarm = $DBFarmRole->GetFarmObject();

					$roleInfo = $db->GetRow("SELECT role_id, platform FROM farm_roles WHERE id = ?",array($taskInfo['target_id']));
					if($roleInfo['platform'])
						$roleInfo['name'] = $db->GetOne("SELECT name FROM roles WHERE id=?", array($roleInfo['role_id']))." ({$roleInfo['platform']})";						

					$display['farminfo']['name'] = $DBFarm->Name;
						
					$display['roleinfo']['name'] = $roleInfo['name'];
					$display['roleinfo']['farmid'] = $DBFarmRole->FarmID;


					break;
					
				case SCRIPTING_TARGET::INSTANCE:
					
					$roleServer = explode(":",$taskInfo['target_id']);
					$DBFarmRole = DBFarmRole::LoadByID($roleServer[0]);	

					$roleInfo['name'] = $db->GetOne("SELECT name FROM roles WHERE id=?", array($DBFarmRole->RoleID))." ({$DBFarmRole->Platform})";					

					$DBFarm = $DBFarmRole->GetFarmObject();
					$display['farminfo']['name'] = $DBFarm->Name;

					$display['roleinfo']['name'] = $roleInfo['name'];
					$display['roleinfo']['farmid'] = $DBFarmRole->FarmID;

					$DBServer = DBServer::LoadByFarmRoleIDAndIndex($roleServer[0],$roleServer[1]);
					$display['serverInfo']['serverId'] = $DBServer->serverId;
					$display['serverInfo']['remoteIp'] = $DBServer->remoteIp;

					break;
			}

	    }
    	catch(Exception $e)
    	{
    		$err[] = $e->getMessage();
    		UI::Redirect("/scheduler.php");	
    	}
	    	
    	$task_config = unserialize($taskInfo['task_config']);
    	
		// if task is script then get id, version(revision) and issync args
    	if($task_config['script_id'] > 0)
    	{	
			$display['issync']	 = $task_config['issync'];
			$display['timeout'] = $task_config['timeout'];	

			$display['scriptSettings'] = json_encode(array("scriptid"=>$task_config['script_id'],"revision"=>$task_config['revision']));

			$script_args = array();	
	    	// form array of script args for javascript
	    	foreach ($task_config as $key => $value)
	    	{
	    		// add only script unique argumetns
	    		if(($key == 'script_id' ) || ($key == 'issync') || ($key == 'revision')|| ($key == 'timeout'))
	    			continue;    	
	    		$script_args[$key]['value'] =  $value;
	    	}
	    	$display["script_args"] = json_encode($script_args);
    	}

    	if($taskInfo['task_type'] == SCHEDULE_TASK_TYPE::TERMINATE_FARM)
    	{
    		$display['deleteDNS'] = $task_config['deleteDNS'];
    		$display['keep_elastic_ips'] = $task_config['keep_elastic_ips'];
    		$display['keep_ebs'] = $task_config['keep_ebs'];
    	}
    	
    	if($taskInfo['task_type'] != SCHEDULE_TASK_TYPE::SCRIPT_EXEC)
    	{
    		// for correct jscript 
    		$display['scriptSettings'] =  json_encode("");
	     	// for correct javascript code send empty variable
			$display["script_args"] = json_encode("");
    	}
    	
    	$display['task_type'] = $taskInfo["task_type"];
    	$display['taskinfo'] = $taskInfo;
    	$display['timezone'] = $display['taskinfo']['timezone']; 
    }
	///////////////////
	// CREATE NEW TASK
	///////////////////
	
	elseif($req_task == 'create')
	{
		// for correct jscript 
		$display['scriptSettings'] = json_encode("");
		// for correct javascript code send empty variable
		$display["script_args"] = json_encode("");
		
		// default terminate parameters
		$display['deleteDNS'] 			= 1;
		$display['keep_elastic_ips'] 	= 1;
		$display['keep_ebs'] 			= 1; 
		
		$display["create_form"] = true;
		$display["title"] = _("Create new task");
	}
	else
	{		 // it's not a 'create' or 'edit' task
		try
		{
			throw new Exception(_("Incorrect task"));
		}
		catch (Exception $e)
		{
			$err[] = $e->getMessage();
			UI::Redirect("/scheduler.php");
		}

	}

	//////////////////////////////////
	// CONTINUE TO SHOW TASK EDIT MENU
	//////////////////////////////////
	$timezones = array();
	$timezoneAbbreviationsList = timezone_abbreviations_list();
	foreach ($timezoneAbbreviationsList as $timezoneAbbreviations) {
		foreach ($timezoneAbbreviations as $value) {
			if (preg_match( '/^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific|Australia)\//', $value['timezone_id']))
				$timezones[$value['timezone_id']] = $value['offset'];
		}
	}
	ksort($timezones);
	$display['timezones'] = array_keys($timezones);
	if (!$display['timezone'])
		$display['timezone'] = Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(ENVIRONMENT_SETTINGS::TIMEZONE);
	
	
 	$display["formData"] = json_encode(array("task_type"=>$taskInfo['task_type'],"task"=>$req_task,"task_id"=>$taskId)); 		
	$display['task'] = $req_task;
	require("src/append.inc.php"); 
 	
 	