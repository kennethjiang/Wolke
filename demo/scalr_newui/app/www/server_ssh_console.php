<? 
	require("src/prepend.inc.php"); 
		
	$DBServer = DBServer::LoadByID($req_server_id);
	$DBFarm = $DBServer->GetFarmObject();
	
	if (Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($DBServer->envId))
	{
		if ($DBServer->remoteIp)
		{
			$dbRole = DBRole::loadById($DBServer->roleId);
			
			$ssh_port = $dbRole->getProperty(DBRole::PROPERTY_SSH_PORT);
			if (!$ssh_port)
				$ssh_port = 22;
			
			try
			{
				$sshKey = Scalr_Model::init(Scalr_Model::SSH_KEY)->loadGlobalByFarmId(
					$DBServer->farmId,
					$DBServer->GetFarmRoleObject()->GetSetting(DBFarmRole::SETTING_CLOUD_LOCATION)
				);
			}
			catch(Exception $e)
			{
				UI::Redirect("/server_view.php");
			}
				
			$Smarty->assign(
				array(
					"DBServer" => $DBServer, 
					"DBFarm"	=> $DBServer->GetFarmObject(),
					"DBRole"	=> $DBServer->GetFarmRoleObject()->GetRoleObject(),
					"host" => $DBServer->remoteIp, 
					"port" => $ssh_port, 
					"key" => base64_encode($sshKey->getPrivate())
				)
			);
			$Smarty->display("ssh_applet.tpl");
			exit();
		}
		else
			$errmsg = _("Server not initialized yet");
	}

	UI::Redirect("/server_view.php");
	
	require("src/append.inc.php"); 
	
?>