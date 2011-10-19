<?
    require("src/prepend.inc.php"); 
        
    if (Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN))
        $farminfo = $db->GetRow("SELECT * FROM farms WHERE id=?", array($req_farmid));
    else 
        $farminfo = $db->GetRow("SELECT * FROM farms WHERE id=? AND env_id=?", 
        	array($req_farmid, Scalr_Session::getInstance()->getEnvironmentId())
        );

    if (!$farminfo)
        UI::Redirect("/#/farms/view");
        
    if ($farminfo["status"] != FARM_STATUS::RUNNING)
    {
    	$errmsg = _("You cannot view statistics for terminated farm");
    	UI::Redirect("/#/farms/view");
    }
	
    if ($req_role && $get_watcher)
    {
    	$display["title"] = _("Farm&nbsp;&raquo;&nbsp;Extended statistics");
		$display["farminfo"] = $farminfo;
		
		$Reflect = new ReflectionClass("GRAPH_TYPE");
	    $types = $Reflect->getConstants();
	    
	    $display['farmid'] = $farminfo['id'];
	    $display['watcher'] = $get_watcher;
	    $display['role_name'] = $get_role;
	    $display['mon_version'] = 2;
	    
	    $template_name = 'farm_extended_stats.tpl';
	    
	    require_once("src/append.inc.php");
	    exit();
    }
    elseif ($req_role && $req_server_index)
    {
    	$display['farmid'] = $farminfo['id'];
    	
    	try
    	{
    		$DBServer = DBServer::LoadByFarmRoleIDAndIndex($req_role, $req_server_index);
    		if (!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($DBServer->envId))
    			throw new Exception("No such server");
    	}
    	catch(Exception $e)
    	{
    		UI::Redirect("/#/farms_view");
    	}
    	
    	$display["title"] = _("Farm&nbsp;&raquo;&nbsp;Statistics for server: {$DBServer->serverId} ({$DBServer->remoteIp})");

    	$display["roles"] = array();
    	
    	array_push($display["roles"], array("t"	=> "instance", "name" => "Server {$DBServer->serverId} ({$DBServer->remoteIp})", "id" => "INSTANCE_{$DBServer->farmRoleId}_{$DBServer->index}"));
    	
    	$watchers = array("MEMSNMP", "CPUSNMP", "NETSNMP", "LASNMP");
		foreach ($display["roles"] as &$role)
		{
			foreach ($watchers as $watcher)
			{
				$role["images"][$watcher]['params'] = array(
					"farmid"	=> $req_farmid, 
					"role_name" => "Server {$DBServer->serverId} ({$DBServer->remoteIp})",
					"role_id"	=> $role['id'],
					"watcher"	=> $watcher,
					"type"		=> 'daily',
					"farmid"	=> $farminfo['id']
				);
				
				$role["images"][$watcher]['hash'] = md5(implode("", $role["images"][$watcher]['params']));
			}
			
			$display["tabs_list"][$role["id"]] = _("Server {$DBServer->serverId} ({$DBServer->remoteIp})");
		}
    	
		$display['selected_tab'] = "INSTANCE_{$DBServer->farmRoleId}_{$DBServer->index}";
		
	    $display['mon_version'] = 2;
	    
	    $template_name = 'monitoring.tpl';
	    
	    require_once("src/append.inc.php");
	    exit();
    }
    else
    {
		$display["title"] = _("Farm&nbsp;&raquo;&nbsp;Statistics");
		$display["farminfo"] = $farminfo;
		$display["farmid"] = $farminfo['id'];
		
	
		$display["roles"] = $db->GetAll("SELECT farm_roles.*, roles.name FROM farm_roles 
			INNER JOIN roles ON roles.id = farm_roles.role_id 
			WHERE farmid=?", array($farminfo['id'])
		);
			
		array_push($display["roles"], array("name" => "FARM", "id" => "FARM"));
	
		$display["roles"] = array_reverse($display["roles"]);
		
		
		$watchers = array("MEMSNMP", "CPUSNMP", "NETSNMP", "LASNMP");
		foreach ($display["roles"] as &$role)
		{
			if ($role['id'] == $req_role)
				$selected_role = $role['id'];
			
			foreach ($watchers as $watcher)
			{
				$role["images"][$watcher]['params'] = array(
					"farmid"	=> $req_farmid, 
					"role_name" => $role['name'],
					"role_id"	=> $role['id'],
					"watcher"	=> $watcher,
					"type"		=> 'daily',
					"farmid"	=> $farminfo['id']
				);
				
				$role["images"][$watcher]['hash'] = md5(implode("", $role["images"][$watcher]['params']));
			}
			
			if ($role["id"] == "FARM")
				$display["tabs_list"][$role["id"]] = _("Entire farm");
			else
				$display["tabs_list"][$role["id"]] = $role["name"];
		}
	
		/**
	     * Tabs
	     */
		if (!$req_role)
			$display["selected_tab"] = "FARM";
		else
			$display["selected_tab"] = $selected_role;
    }
	
	require_once("src/append.inc.php");
?>