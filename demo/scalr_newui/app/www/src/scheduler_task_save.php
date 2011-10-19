<?php	
// functions adds new task to scheduler task table or edits 
// available tasks
    
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
    {
    	$err[] = _("Request cannot be executed from admin account");
    	throw new Exception(_("Request cannot be executed from admin account"));
    }
    
	if($req_task == 'create')
	{	
		try
		{				
			if($req_task_type == SCHEDULE_TASK_TYPE::SCRIPT_EXEC)
			{	
					switch($req_target_type)
					{
						case SCRIPTING_TARGET::FARM:

							if($req_farm_target == '')
								throw new Exception("Unknown or empty task's target parameters");
								
							$DBFarm = DBFarm::LoadByID($req_farm_target);							
							$target_id = $DBFarm->ID;												
							$target_object_clientid = $DBFarm->ClientID;
																											
							break;
								
						case SCRIPTING_TARGET::ROLE:
							
							if($req_role_target == '')
								throw new Exception("Unknown or empty task's target parameters");
								
							$DBFarmRole = DBFarmRole::LoadByID($req_role_target);
							$target_id = $DBFarmRole->ID;							
							$target_object_clientid = $DBFarmRole->GetFarmObject()->ClientID;
															
							break;
								
						case SCRIPTING_TARGET::INSTANCE:

							if($req_server_target == '')
								throw new Exception("Unknown or empty task's target parameters");
							
							$DBServer = DBServer::LoadByID($req_server_target);							
							$target_id = "{$DBServer->farmRoleId}:{$DBServer->index}";
							$target_object_clientid = $DBServer->GetFarmObject()->ClientID;
							
							break;
																									
						default: 
							throw new Exception("Unknown or empty task's target parameters");
							break;
					}		
					
			}
			elseif( $req_task_type == SCHEDULE_TASK_TYPE::TERMINATE_FARM || 
					$req_task_type == SCHEDULE_TASK_TYPE::LAUNCH_FARM)
			{
				if($req_farm_target == '')
								throw new Exception("Unknown or empty task's target parameters");
								
				$DBFarm = DBFarm::LoadByID($req_farm_target);				
				$target_object_clientid = $DBFarm->ClientID;
				
				$target_id = $DBFarm->ID;	
			}
			else	
			{	
				throw new Exception(_("Unknown or empty task type"));
			}
							
			if ((Scalr_Session::getInstance()->getClientId() && $target_object_clientid != Scalr_Session::getInstance()->getClientId()) || $target_id == 0)
				throw new Exception(_("Specified target not found, please select correct target object"));
								
		}
		catch (Exception $e)
		{			
			 $err[] = $e->getMessage();					
			throw $e;
				
		}	
	}
	if(($req_task == 'create') || ($req_task == 'edit'))
	{		
		// form error message for entering data correction
		$exception = false;		
		$Validator = new Validator();			
	
		// check entering data
		try
		{	
			if(!$Validator->IsAlphaNumeric($req_task_name))
				$err[] = _("Task name contains invalid symbols or empty");				
					
			if (!$req_startDateTime)			
				$err[] = _("Start date has incorrect date format or empty");			
			
			if (!$req_endDateTime)			
				$err[] = _("End date has incorrect date format or empty");				
			
			if (!$Validator->IsNumeric($req_restart_timeout))			
				$err[] = _("Restart timeout is not a numeric variable or empty");				
			
			if (!$Validator->IsNumeric($req_order_index))			
				$err[] = _("Priority is not a numeric variable or empty");
					
			if(!$Validator->IsNumeric($req_timeout) && $req_task_type == SCHEDULE_TASK_TYPE::SCRIPT_EXEC)		
				$err[] = _("Timeout is not a numeric variable or empty");
	
			// check correct  date  and time				
			// new task start date can't be older then current date
			if($req_task == 'create')
			{				
				if(CompareDateTime($req_startDateTime) < 0)				
					$err[] = _("Start time must be later or equal to the current date and time");							
			}
			
			if(CompareDateTime($req_endDateTime) < 1)				
					$err[] = _("End time must be later than current date and time");	
					
			if(CompareDateTime($req_startDateTime,$req_endDateTime) != -1 && $req_restart_timeout != 0) 			
				$err[] = _("End time must be later than start time");			
	
			if($err)
				throw new Exception();	
				
			// add/update database 	
			$req_config = array();
			
				
			// Rewrite	script info	
			if($req_task_type == SCHEDULE_TASK_TYPE::SCRIPT_EXEC)			
			{				
				if(!$req_scriptid || !$req_script_version)
				{
					$err[] = _("Some important script parameters were not selected");
					throw new Exception();
				}
				
				// sql script filter for correct ckeck 
				
					$script_filter_sql .= " AND ("; 
					// Show shared roles
					$script_filter_sql .= " origin='".SCRIPT_ORIGIN_TYPE::SHARED."'";
				
					// Show custom roles
					$script_filter_sql .= " OR (origin='".SCRIPT_ORIGIN_TYPE::CUSTOM."' 
							AND clientid='".Scalr_Session::getInstance()->getClientId()."')";
					
					//Show approved contributed roles
					$script_filter_sql .= " OR (origin='".SCRIPT_ORIGIN_TYPE::USER_CONTRIBUTED."' 
							AND (scripts.approval_state='".APPROVAL_STATE::APPROVED."' 
							OR clientid='".Scalr_Session::getInstance()->getClientId()."'))";
					$script_filter_sql .= ")";				
					
					// check script availble for current user
					//TODO:
				
				$Info = $db->GetRow("SELECT name FROM scripts WHERE id = ? {$script_filter_sql}", array($req_scriptid));
								
				if(!$Info)
				{			
					$err[] =_("Script {$req_scriptid} not found");
					throw new Exception();
				} 	
				
					// check correct revision of user's script
				$Info = $db->GetRow("SELECT id FROM script_revisions WHERE scriptid = ? AND revision = ?", array($req_scriptid,$req_script_version));
				
				if(!$Info)
				{
					$err[] = _("Script {$req_scriptid} revision #{$req_script_version} not found");			
					throw new Exception();
				} 
				
				unset($script_filter_sql);
				unset($Info);
			
				$req_config['script_id']= (int)$req_scriptid;				
				$req_config['revision'] = (int)$req_script_version;
				$req_config['issync'] 	= ($req_issync)? 1 : 0;
				$req_config['timeout'] 	= (int)$req_timeout;
	
				// add unique parameters for selected script version				
				foreach($req_script_args as $key => $value)
				{
					$req_config[$key] = $value;	
					
					// form an array of script args for dinamic ajax update
					$return_script_args[$key]['value'] =  $value;  				
				}
				
				$return_script_args = json_encode($return_script_args);	
			}
		
			if($req_task_type == SCHEDULE_TASK_TYPE::TERMINATE_FARM)	
			{				
				$req_config['deleteDNS'] = ($req_deleteDNS) ? 1 : 0;
				$req_config['keep_elastic_ips'] = (int)$req_keep_elastic_ips;
				$req_config['keep_ebs'] = (int)$req_keep_ebs; 	
			}	
		
			if ($req_task == 'edit')
			{			
				// update scheduler's record
				if(!$req_task_id)
					throw new Exception(_("Unknown or empty task's ID"));				

				$db->Execute("UPDATE scheduler_tasks SET
					task_name = ?,
					task_type = ?,
					start_time_date = ?,
					end_time_date = ?,
					last_start_time = ?,
					restart_every = ?,
					task_config = ?,
					order_index = ?,
					status = ?,
					env_id	= ?,
					timezone	= ?
					WHERE id = ? AND client_id = ?",
				array(
					$req_task_name,
					$req_task_type,
					$req_startDateTime,			// time 
					$req_endDateTime,
					null,
					(int)$req_restart_timeout,
					($req_config) ? serialize($req_config) : null,
					(int)$req_order_index,
					TASK_STATUS::ACTIVE,
					Scalr_Session::getInstance()->getEnvironmentId(),
					$req_timezone,
					(int)$req_task_id,						
					Scalr_Session::getInstance()->getClientId()						
					)
				);
				
				$okmsg[] =_("Task {$req_task_name} successfully updated");									
			}
		
			elseif($req_task == 'create')
			{				
					
				// add  new scheduler's record				
				$db->Execute("INSERT INTO scheduler_tasks SET
					task_name = ?,
					task_type = ?,
					target_id = ?,
					target_type =?,
					start_time_date = ?,
					end_time_date = ?,
					last_start_time = ?,
					restart_every = ?,
					task_config = ?,
					order_index = ?,
					status = ?,				
					client_id = ?,
					env_id	= ?,
					timezone	= ?",
				array($req_task_name,
					$req_task_type,
					$target_id,
					$req_target_type,
					$req_startDateTime,			// time 
					$req_endDateTime,
					null,
					(int)$req_restart_timeout,
					($req_config)? serialize($req_config):null,
					$req_order_index,
					TASK_STATUS::ACTIVE,						
					Scalr_Session::getInstance()->getClientId(),
					Scalr_Session::getInstance()->getEnvironmentId(),
					$req_timezone
					)
				);
			
				$okmsg[] =_("New task successfully added");						
			}
		}
		catch(Exception $e)
		{		
			if($e->getMessage())
				$err[] = $e->getMessage();
			throw $e;					
		}
	}
	else 	
	{	
		$err[] = _("Unknown task");					
			throw new Exception(_("Unknown task"));
	}
	
	function CompareDateTime($date1,$date2 = null)
	{	
		// compatere 2 dates 
		// if date1 later then date 2  returns 1;
		// if date2 later  then date 1  returns -1;
		// if date1 equal to date 2 returns 0;
		// if date2 is null function compateres date1 with current time (as date2 variable)

		$checkingDate1 = strtotime($date1);	
		
		if($date2)	
			$checkingDate2 = strtotime($date2); // get compareing time	
		else		
			$checkingDate2 = time(); // get current time
			
			
		if($checkingDate1 > $checkingDate2)		 // e.g. selected date later then current date
			return 1;
		elseif($checkingDate1 < $checkingDate2)  // e.g. end date is later then start date or current date is la
			return -1;
		else
			return 0;		
	}
?>