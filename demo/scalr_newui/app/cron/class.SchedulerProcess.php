<?php
	class SchedulerProcess implements IProcess
    {
        public $ThreadArgs;
        public $ProcessDescription = "Schedule manager";
        public $Logger;
                
    	public function __construct()
        {
        	// Get Logger instance
        	$this->Logger = Logger::getLogger(__CLASS__);
        }                
        
        public function OnStartForking()
        {
        	 // start cron, which runs scripts by it's schedule queue 
        
        	try
       	 	{
       	 		$db = Core::GetDBInstance(null, true);
       	 		
       	 		//TODO:
       	 		
       	 		// set status to "finished" for finished taskes
       	 		$info = $db->Execute("UPDATE scheduler_tasks SET `status` = ?
       	 			WHERE (end_time_date 		< CONVERT_TZ(NOW(),'SYSTEM',IFNULL(`timezone`,'SYSTEM'))) 
					OR  (last_start_time 	< CONVERT_TZ(NOW(),'SYSTEM',IFNULL(`timezone`,'SYSTEM')) 
						 AND restart_every 	= 0
						) 
					AND `status` != ? ",
        		array(TASK_STATUS::FINISHED,ENVIRONMENT_SETTINGS::TIMEZONE,TASK_STATUS::FINISHED));


       	 		// get all task from schedule table which are conformed to the conditions. 
       	 		// Request converts system time to Client's time by value of:
       	 		// 'client_settings.value' - client's timezone like "Europe/London" or others.
       	 		
       	 		$taskList = $db->GetAll("SELECT scheduler_tasks.*
						FROM scheduler_tasks
						WHERE end_time_date > CONVERT_TZ(NOW(),'SYSTEM',IFNULL(`timezone`,'SYSTEM')) AND start_time_date <= CONVERT_TZ(NOW(),'SYSTEM',IFNULL(`timezone`,'SYSTEM'))
						AND  (
								(ADDDATE(IF (last_start_time, last_start_time, start_time_date), INTERVAL restart_every MINUTE) <=  CONVERT_TZ(NOW(),'SYSTEM',IFNULL(`timezone`,'SYSTEM')))
								OR   ( last_start_time IS NULL)
							 )
						AND `status` = ?
						ORDER BY 
						IF (last_start_time, last_start_time, start_time_date) AND order_index ASC
       	 		",
        		array(TASK_STATUS::ACTIVE));

       	 		if(!$taskList)
       	 		{
       	 			$this->Logger->info(_("There is no tasks to execute in scheduler table"));
       	 			exit();
       	 		}
       	 		
       	 		// exclude one-time executing task which were executed before       	 		
        		for($i = 0; $i <count($taskList); $i++)
        		{        			
        			if($taskList[$i]['restart_every'] == 0 && $taskList[$i]['last_start_time'])        					
        				unset($taskList[$i]);
        		}
				
       	 		foreach($taskList as $task)
       	 		{
       	 			// Terminate, Lunch farm  or execute script 
       	 			$farmRoleNotFound = false;
       	 		
	       	 		switch($task['task_type'])
	       	 		{
	       	 			case SCHEDULE_TASK_TYPE::LAUNCH_FARM:
		       	 				try 
		       	 				{
			       	 				$farmId = $task['target_id'];
			       	 				$DBFarm = DBFarm::LoadByID($farmId);   // trows the exception

			       	 				if($DBFarm->Status == FARM_STATUS::TERMINATED)
			       	 			    {
									    // lunch farm
			       	 					Scalr::FireEvent($farmId, new FarmLaunchedEvent(true));
		               	 				$this->Logger->info(sprintf("Farm #{$farmId} successfully lunched"));
			       	 			    }
			       	 			    elseif($DBFarm->Status == FARM_STATUS::RUNNING)
			       	 			    {
			       	 			    	 // farm is running
				       	 				$this->Logger->info(sprintf("Farm #{$farmId} is already running"));
			       	 			    }
			       	 			    else 
			       	 			    {
			       	 			    	 // farm can't be lunched			       	 			  			       	 			    	
				       	 				$this->Logger->info(sprintf("Farm #{$farmId} can't be lunched becouse of it's status: {$DBFarm->Status}"));
			       	 			    }
		       	 				}
		       	 				catch(Exception $e)
		       	 				{
		       	 					// farm  not found
		       	 					$farmRoleNotFound  = true;		       	 					
	       	 						$this->Logger->info(sprintf("Farm #{$farmId} was not found and can't be launched"));	       	 					
		       	 				}

		       	 			    break;
	       	 			case SCHEDULE_TASK_TYPE::TERMINATE_FARM:

		       	 				try
		       	 				{
			       	 				// get config settings
			       	 				$farmId = $task['target_id'];

			       	 				$taskConfig 		= unserialize($task['task_config']);
			       	 				$keepElasticIps 	= (int)$taskConfig['keep_elastic_ips'];
			       	 				$keepEbs			= (int)$taskConfig['keep_ebs'];
			       	 				$removeZoneFromDNS	= (int)$taskConfig['deleteDNS'];

			       	 			    $DBFarm = DBFarm::LoadByID($farmId);  // trows the exception

			       	 			    if($DBFarm->Status == FARM_STATUS::RUNNING)
			       	 			    {
									    // terminate farm
									    $event = new FarmTerminatedEvent($removeZoneFromDNS, $keepElasticIps, false, $keepEbs);
									    Scalr::FireEvent($farmId, $event);

				       	 				$this->Logger->info(sprintf("Farm successfully terminated"));
			       	 			    }
			       	 			    else
			       	 			    {
				       	 				$this->Logger->info(sprintf("Farm #{$farmId} can't be terminated because of it's status"));
			       	 			    }
		       	 				}
		       	 				catch(Exception $e)
		       	 				{
		       	 					// role not found
		       	 					$farmRoleNotFound  = true;		       	 					
	       	 						$this->Logger->info(sprintf("Farm #{$farmId} was not found and can't be terminated"));	  	
		       	 				}

		       	 				break;

	       	 			case SCHEDULE_TASK_TYPE::SCRIPT_EXEC:

	       	 					// generate event name
	       	 					$event_name = 'CustomEvent-'.date("YmdHi").'-'.rand(1000,9999);       	 		
	       	 					$instances = array();	

		       	 				try
		       	 				{
			       	 				// get variables for SQL INSERT or UPDATE
			       	 				$scriptConfig 	= unserialize($task['task_config']);
			       	 				
			       	 				$scriptId 		= $scriptConfig['script_id'];
			       	 				$issync 		= $scriptConfig['issync'];
			       	 				$scriptRevision = $scriptConfig['revision'];
			       	 				$scriptTimeout  = $scriptConfig['timeout'];

			       	 				// collect only unique script args -  exclude issync, revision e.t.c.
			       	 				$keys = array('script_id', 'issync', 'revision', 'timeout');	       	 				
			       	 				foreach($scriptConfig as $paramKey => $paramValue)
			       	 				{
			       	 					if(!in_array($paramKey, $keys))
			       	 						$params[$paramKey] = $paramValue;
			       	 				}

			       	 				if(!$scriptId)
			       	 					throw new Exception(_("Script %s is not existed"),$scriptId);

			       	 				// check valiable script revision ( version )
			       	 				if (!$db->GetOne("SELECT id FROM script_revisions
	       	 							WHERE scriptid = ? AND revision = ? AND approval_state = ?",
										array($scriptId, $scriptRevision, APPROVAL_STATE::APPROVED)))
									{
										throw new Exception(_("Selected version is not approved or no longer available"));
									}

									// get executing object by target_type variable
									switch($task['target_type'])
									{

										case SCRIPTING_TARGET::FARM:

												$DBFarm = DBFarm::LoadByID($task['target_id']);	// throws exception if not found										
												$farmId 	= $DBFarm->ID;

												$farmRoleId = null;

												$servers = $db->GetAll("SELECT * FROM servers WHERE `status` IN (?,?) AND farm_id = ?",
													array(SERVER_STATUS::INIT, SERVER_STATUS::RUNNING, $farmId)
												);		
												
												break;

										case SCRIPTING_TARGET::ROLE:

												$farmRoleId = $task['target_id'];

												$DBFarmRole = DBFarmRole::LoadByID($farmRoleId); // throws exception if not found											
												$farmId = $DBFarmRole->GetFarmObject()->ID;
	
												$servers = $db->GetAll("SELECT * FROM servers WHERE `status` IN (?,?) AND farm_roleid = ?",
													array(SERVER_STATUS::INIT, SERVER_STATUS::RUNNING, $farmRoleId)
												);
												break;

										case SCRIPTING_TARGET::INSTANCE:

												$instanceArgs = explode(":",$task['target_id']);	
												$farmRoleId = $instanceArgs[0];																								

												$DBFarmRole = DBFarmRole::LoadByID($farmRoleId);	// throws exception if not found

												// target for instance looks like  "farm_roleid:index"
												// script gets farmid conformed to the roleid and index	
												$servers = $db->GetAll("SELECT * FROM servers WHERE `status` IN (?,?) AND farm_roleid = ? AND `index` = ? ",
													array(SERVER_STATUS::INIT, SERVER_STATUS::RUNNING, $farmRoleId,$instanceArgs[1])
												);	
												$farmId = $servers[0]["farm_id"];											
												break;

									} // end of  switch($task['target_type'])		

									if($servers)
									{			
										$db->Execute("INSERT INTO farm_role_scripts SET
											scriptid	= ?,
											farmid		= ?,
											farm_roleid	= ?,
											params		= ?,
											event_name	= ?,
											target		= ?,
											version		= ?,
											timeout		= ?,
											issync		= ?,
											order_index = ?,
											ismenuitem	= ?
										", array(
											$scriptId,
											$farmId,
											$farmRoleId,
											serialize($params),
											$event_name,
											$task['target_type'],
											$scriptRevision,
											$scriptTimeout,
											$issync,
											$task["order_index"],
											(isset($post_create_menu_link)) ? 1 : 0
										));

										$farm_rolescript_id = $db->Insert_ID();

				       	 				// send message to start executing task (starts script)
							       	 	foreach ($servers as $server)
										{
											$DBServer = DBServer::LoadByID($server['server_id']);
											
											$msg = new Scalr_Messaging_Msg_ExecScript($event_name);
											$msg->meta[Scalr_Messaging_MsgMeta::EVENT_ID] = "FRSID-{$farm_rolescript_id}";
											$DBServer->SendMessage($msg, true);
										}
									}
		       	 				}	
			       	 			catch (Exception $e)
			       	 			{
		       	 					// farm or role not found.
		       	 					$farmRoleNotFound  = true;
	       	 						$this->Logger->warn(sprintf("Farm, role or instances were not found, script can't be executed"));		       	 							
			       	 			}
			       	 			break; 	// end of case SCHEDULE_TASK_TYPE::SCRIPT_EXEC
	       	 				
	       	 		} // end of switch($task['task_type'])
       	 		       	 	
	       	 		if($farmRoleNotFound)
	       	 		{
	       	 			// delete task if farm or role not found.
	       	 			$db->Execute("DELETE FROM scheduler_tasks  WHERE id = ?",array($task['id']));	
		       	 		$this->Logger->warn(sprintf("Task {$task['id']} was deleted, because of the farm or role was not found"));
	       	 		}
	       	 		else
	       	 		{
		       	 		$db->Execute("UPDATE  scheduler_tasks
									SET last_start_time = CONVERT_TZ(NOW(),'SYSTEM',`timezone`)
									WHERE scheduler_tasks.id = ?
									AND `status` = ?",
								array($task['id'], TASK_STATUS::ACTIVE)
						);
						
		       	 		$this->Logger->info(sprintf("Task {$task['id']} successfully sent"));
	       	 		}
    	 		}
			}
			catch(Exception $e)
			{
				$this->Logger->warn(sprintf("Can't execute task {$task['id']}. Error message: %s",$e->getMessage()));		
			}
        }

        public function OnEndForking()
        {

        }
        public function StartThread($queue_name)
        {       	

        }
    }
