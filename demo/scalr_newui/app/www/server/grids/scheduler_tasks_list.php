<?php

	$response = array();	
	// AJAX_REQUEST;
	$context = 6;

	try
	{
		$enable_json = true;
		include("../../src/prepend.inc.php");		

		$sql = "SELECT *  FROM `scheduler_tasks` WHERE `client_id` = '".Scalr_Session::getInstance()->getClientId()."'";
		
		if ($req_query)
		{
			$filter = mysql_escape_string($req_query);
			foreach(array("id", "task_type", "target_type", "start_time_date", "end_time_date", "last_start_time", "status") as $field)
			{
				$likes[] = "$field LIKE '%{$filter}%'";
			}
			$sql .= !stristr($sql, "WHERE") ? " WHERE " : " AND (";
			$sql .= join(" OR ", $likes);
			$sql .= ")";
		}

		$response["total"] = $db->Execute($sql)->RecordCount();
		
		// limits for table 
		$start = $req_start ? (int) $req_start : 0;
		$limit = $req_limit ? (int) $req_limit : 20;
		$sql .= " LIMIT $start, $limit";
		
		$response["data"] = array();
		foreach ($db->GetAll($sql) as $row)
		{
			$farmRoleNotFound = false;
			
			switch($row['target_type'])
			{
				case SCRIPTING_TARGET::FARM:	
						try 
						{	
							$DBFarm = DBFarm::LoadByID($row['target_id']);							
							$row['target_name'] = $DBFarm->Name;
							$row['farmid'] = $row['target_id'];		
							
						} catch ( Exception  $e) 
						{		
							// farm object was not found.
							// don't add it to 	row		
							$farmRoleNotFound = true;	
						}

						break; 
							
				case SCRIPTING_TARGET::ROLE:	
						try
						{							
							$DBFarmRole = DBFarmRole::LoadByID($row['target_id']);
							$row['target_name'] = $DBFarmRole->GetRoleObject()->name;
							$row['farmid'] = $DBFarmRole->FarmID;
							$row['farm_name'] = $DBFarmRole->GetFarmObject()->Name;					
						
						} catch (Exception $e)
						{
							// role object was not found.
							// don't add it to 	row		
							$farmRoleNotFound = true;
						}			
						break;
							
				case SCRIPTING_TARGET::INSTANCE: 	
						$serverArgs = explode(":", $row['target_id']);
						
						try
						{
							$DBServer = DBServer::LoadByFarmRoleIDAndIndex($serverArgs[0], $serverArgs[1]);

							$row['target_name'] = "({$DBServer->remoteIp})";
							$DBFarmRole = $DBServer->GetFarmRoleObject();
							$row['farmid'] = $DBFarmRole->FarmID;
							$row['farm_name'] = $DBFarmRole->GetFarmObject()->Name;							
						}	
						catch(Exception $e)
						{
							// role object was not found.
							// don't add it to 	row	
							$farmRoleNotFound = true;	
						}
						break;
						
				default: break;
			}	

			// if farm or role wasn't found, but exists in schedule list - don't display 
			if($farmRoleNotFound)
				continue;			

			$task_config = unserialize($row['task_config']);
			$row['script_name'] = $db->GetOne("SELECT name FROM scripts WHERE id=?",
											array($task_config['script_id'])
										);					

			// add to `data` only if response matched the query or query string is empty				
			unset($row['task_config']);
			unset($row['client_id']);
			unset($row['target_id']);
			
			$row['task_type'] = SCHEDULE_TASK_TYPE::GetTypeByName($row['task_type']);
			$response["data"][] = $row;
		}
	}
	catch(Exception $e)
	{
		$response = array("error" => $e->getMessage(), "data" => array());
	}

	print json_encode($response);
?>