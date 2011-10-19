<?php
	require("src/prepend.inc.php"); 
		
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER, Scalr_AuthToken::MODULE_VHOSTS))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}	
	   	
	$display["title"] = _("Apache vhosts view");	

	if($req_action)
	{
		$Validator = new Validator();
		
		if (!is_array($req_id))
			$req_id = array($req_id);
		
		foreach ($req_id as $vhost_id)
		{
			if(!$Validator->IsNumeric($vhost_id))
				continue;
							
			if($req_action == "delete")
			{
				$dbFarmId = $db->GetOne("SELECT farm_id FROM apache_vhosts WHERE id = ? AND env_id = ?",
					array($vhost_id, Scalr_Session::getInstance()->getEnvironmentId())
				);
				
				if ($dbFarmId)
				{
					$db->Execute("DELETE FROM apache_vhosts WHERE id = ? AND env_id = ?",
						array($vhost_id, Scalr_Session::getInstance()->getEnvironmentId())
					);
					
					$dbFarm = DBFarm::LoadByID($dbFarmId);
					
					$servers = $dbFarm->GetServersByFilter(array('status' => array(SERVER_STATUS::INIT, SERVER_STATUS::RUNNING)));
					foreach ($servers as $DBServer)
					{
						if ($DBServer->GetFarmRoleObject()->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::NGINX) || 
							$DBServer->GetFarmRoleObject()->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::APACHE))
							$DBServer->SendMessage(new Scalr_Messaging_Msg_VhostReconfigure());
					}
					
					$okmsg = _("Selected virtual host(s) successfully removed");
				}
			}
		}
	}
	
	if ($req_farm_id)
	{
		$farm_id = (int)$req_farm_id;
		$display["grid_query_string"] = "&farm_id={$farm_id}";
	}
	
	require("src/append.inc.php"); 