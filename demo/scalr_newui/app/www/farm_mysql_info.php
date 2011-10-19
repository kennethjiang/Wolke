<?
    require("src/prepend.inc.php"); 
    
    try
    {
    	$DBFarm = DBFarm::LoadByID($req_farmid);
    	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($DBFarm->EnvID))
    		throw new Exception("未找到云平台");
    		
    	$display['farmid'] = $DBFarm->ID;
    }
	catch(Exception $e)
	{
		UI::Redirect("/#/farms/view");	
	}

	$mysql_farm_role_id = $db->GetOne("SELECT id FROM farm_roles WHERE role_id IN (SELECT role_id FROM role_behaviors WHERE behavior=?) AND farmid=?", 
		array(ROLE_BEHAVIORS::MYSQL, $DBFarm->ID)
	);
	
	$DBFarmRole = DBFarmRole::LoadByID($mysql_farm_role_id);
	
	if ($DBFarmRole->Platform == SERVER_PLATFORMS::RDS)
		UI::Redirect("/#/farms/view");
	
	$display["title"] = "云平台 '<a href='#/farms/{$DBFarm->ID}/view'>{$DBFarm->Name}</a>'&nbsp;&raquo;&nbsp;Mysql信息";		
	
	// Storage info
	
	$storage = array(
		'type' => $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_DATA_STORAGE_ENGINE),
		'version' => $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_SCALR_VOLUME_ID) ? 2 : 1 
	);
	
	$storage['id'] = ($storage['version'] == 2) ? $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_SCALR_VOLUME_ID) : $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_MASTER_EBS_VOLUME_ID);
	
	$display['storage'] = $storage;
	
	
	
	
	if ($DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_LAST_BCP_TS))
		$display["mysql_last_backup"] = date("d M Y \a\\t H:i:s", $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_LAST_BCP_TS));
                
	if ($DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_LAST_BUNDLE_TS))
		$display["mysql_last_bundle"] = date("d M Y \a\\t H:i:s", $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_LAST_BUNDLE_TS));
	
	$mysql_servers = $DBFarm->GetMySQLInstances();
   	
   	$slave_num = 0;
   	foreach ($mysql_servers as $DBServer)
   	{
   		if ($DBServer->status != SERVER_STATUS::RUNNING)
   			continue;
   		
   		$DBFarmRole = $DBServer->GetFarmRoleObject();
   		
   		if ($DBServer->GetProperty(SERVER_PROPERTIES::DB_MYSQL_MASTER) == 1)
		{			
			$display['mysql_bundle_running'] = $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_IS_BUNDLE_RUNNING);
			$display['mysql_bundle_server_id'] = $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_BUNDLE_SERVER_ID);
			
   			if ($DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_PMA_USER))
   				$display['mysql_pma_credentials'] = true;
   			else
   			{
   				$time = $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_PMA_REQUEST_TIME);   				
   				if ($time)
   				{
   					if ($time+3600 < time())
   						$errmsg = _("系统未收到MySQL服务器的授权信息。请确认MySQL服务器正处于运行状态，并且系统可以访问到它。");
   					else
   						$display['mysql_pma_processing_access_request'] = true;
   				}
   			}
		}
   		else
   		{
   			$display['mysql_bcp_running'] = $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_IS_BCP_RUNNING);
			$display['mysql_bcp_server_id'] = $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_BCP_SERVER_ID);
   		}
		
   		try
   		{
   			$conn = &NewADOConnection("mysqli");
   			$conn->Connect($DBServer->remoteIp, CONFIG::$MYSQL_STAT_USERNAME, $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_STAT_PASSWORD), null);
   			$conn->SetFetchMode(ADODB_FETCH_ASSOC); 
   			
			if ($DBServer->GetProperty(SERVER_PROPERTIES::DB_MYSQL_MASTER) == 1)
			{
   				$r = $conn->GetRow("SHOW MASTER STATUS");
   				$MasterPosition = $r['Position'];
   				$master_ip = $DBServer->remoteIp;
   				$master_iid = $DBServer->serverId;
   				
   				if ($DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_PMA_USER))
   					$display['mysql_pma_credentials'] = true;
   				else
   				{
   					$errmsg = $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_PMA_REQUEST_ERROR);
   					if (!$errmsg)
   					{
	   					$time = $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_PMA_REQUEST_TIME);
	   					if ($time)
	   					{
	   						if ($time+3600 < time())
	   							$errmsg = _("Scalr didn't receive auth info from MySQL instance. Please check that MySQL running and Scalr has access to it.");
	   						else
	   							$display['mysql_pma_processing_access_request'] = true;
	   					}
   					}
   				}
			}
   			else
   			{
   				$r = $conn->GetRow("SHOW SLAVE STATUS");
   				
   				$SlaveNumber = ++$slave_num;
   				$SlavePosition = $r['Exec_Master_Log_Pos'];
   			}
   				
   			$display["replication_status"][$DBServer->serverId] = 
   			array(
   				"data" => $r, 
   				"MasterPosition" => $MasterPosition, 
   				"SlavePosition" => $SlavePosition,
   				"IsMaster"		=> $DBServer->GetProperty(SERVER_PROPERTIES::DB_MYSQL_MASTER),
   				"SlaveNumber"	=> $SlaveNumber
   			);
   		}
   		catch(Exception $e)
   		{
   			$display["replication_status"][$DBServer->serverId] = array(
   				"error" => ($e->msg) ? $e->msg : $e->getMessage(),
   				"IsMaster"		=> $DBServer->GetProperty(SERVER_PROPERTIES::DB_MYSQL_MASTER)
   			);
   		}
   	}
	
   	if ($_POST)
	{
		$req_farmid = (int)$req_farmid;
		
		if ($post_remove_mysql_data_bundle)
		{
			if ($post_remove_mysql_data_bundle_confirm)
			{
				
			}
			else
			{
				$Smarty->assign($display);
			    $Smarty->display("mysql_data_bundle_clear_confirm.tpl");
				exit();
			}
		}
		
		if ($post_pma_reset)
		{
			$mysql_servers = $DBFarm->GetMySQLInstances(true);
			
			if ($mysql_servers[0])
			{
				$DBServer = $mysql_servers[0];	
				$DBFarmRole = $DBServer->GetFarmRoleObject();
				
				$DBFarmRole->ClearSettings("mysql.pma");
				
				$post_pma_request_credentials = true;
			}
		}
		
		if ($post_pma_request_credentials)
		{
			$mysql_servers = $DBFarm->GetMySQLInstances(true);
			
			if ($mysql_servers[0])
			{
				$DBServer = $mysql_servers[0];	
				$DBFarmRole = $DBServer->GetFarmRoleObject();
				
				$time = $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_PMA_REQUEST_TIME); 
				
				if (!$time || $time+3600 < time())
				{
					$msg = new Scalr_Messaging_Msg_Mysql_CreatePmaUser($DBFarmRole->ID, CONFIG::$PMA_INSTANCE_IP_ADDRESS);
					
					$DBServer->SendMessage($msg);
					$DBFarmRole->SetSetting(DBFarmRole::SETTING_MYSQL_PMA_REQUEST_TIME, time());
					$DBFarmRole->SetSetting(DBFarmRole::SETTING_MYSQL_PMA_REQUEST_ERROR, "");
					
					$okmsg = _("MySQL需要访问PMA的认证信息。请稍候...");
					UI::Redirect("/farm_mysql_info.php?farmid={$req_farmid}");
				}
				else
				{
					$errmsg = _("MySQL已获得PMA的认证信息。请稍候...");
					UI::Redirect("/farm_mysql_info.php?farmid={$req_farmid}");
				}
			}
			else
			{
				$errmsg = _("目前没有正在运行的MySQL Master服务器。请稍候，直到MySQL Master服务器启动完毕。");
				UI::Redirect("/farm_mysql_info.php?farmid={$req_farmid}");
			}
		}
		
		if ($post_pma_launch)
			UI::Redirect("/externals/pma_redirect.php?farmid={$req_farmid}");
		
		if ($post_update_volumeid)
		{
			$DBFarmRole->SetSetting(DBFarmRole::SETTING_MYSQL_MASTER_EBS_VOLUME_ID, $post_mysql_master_ebs);
			
			$okmsg = _("成功更新卷ID");
	        UI::Redirect("farm_mysql_info.php?farmid={$DBFarm->ID}");
		}
		
		if ($post_run_bcp)
		{
			$mysql_servers = $DBFarm->GetMySQLInstances(false, true);
			if (!$mysql_servers) {
				$mysql_servers = $DBFarm->GetMySQLInstances(true); 
			}
			
			if (!$mysql_servers)
				$errmsg = _("目前没有正在运行的MySQL Slave服务器");
			else
			{
				$DBServer = $mysql_servers[0];
				
				$msg = new Scalr_Messaging_Msg_Mysql_CreateBackup();
				$msg->rootPassword = $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_ROOT_PASSWORD);
				$DBServer->SendMessage($msg);
				
				$DBFarmRole->SetSetting(DBFarmRole::SETTING_MYSQL_IS_BCP_RUNNING, 1);
				$DBFarmRole->SetSetting(DBFarmRole::SETTING_MYSQL_BCP_SERVER_ID, $DBServer->serverId);
					            
	            $okmsg = _("Backup request successfully sent to instance");
	            UI::Redirect("farm_mysql_info.php?farmid={$DBFarm->ID}");
			}
		}
		elseif ($post_run_bundle)
		{
			$mysql_servers = $DBFarm->GetMySQLInstances(true);
			$mysql_master = $mysql_servers[0];
			 
			if (!$mysql_master)
				$errmsg = _("目前没有正在运行的MySQL Master服务器。");
			else
			{
				$DBServer = $mysql_servers[0];
				$DBServer->SendMessage(new Scalr_Messaging_Msg_Mysql_CreateDataBundle());

				$DBFarmRole->SetSetting(DBFarmRole::SETTING_MYSQL_IS_BUNDLE_RUNNING, 1);
				$DBFarmRole->SetSetting(DBFarmRole::SETTING_MYSQL_BUNDLE_SERVER_ID, $DBServer->serverId);
				
	            $okmsg = _("MySQL数据绑定指令已成功发送到MySQL Master服务器。");
	            UI::Redirect("farm_mysql_info.php?farmid={$DBFarm->ID}");
			}
		}
	}
   	
	require_once("src/append.inc.php");
?>
