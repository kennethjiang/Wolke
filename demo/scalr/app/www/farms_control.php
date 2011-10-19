<?
    require("src/prepend.inc.php"); 
    
    if (Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN))
        $farminfo = $db->GetRow("SELECT * FROM farms WHERE id=?", array($req_farmid));
    else 
        $farminfo = $db->GetRow("SELECT * FROM farms WHERE id=? AND env_id=?", 
        	array($req_farmid, Scalr_Session::getInstance()->getEnvironmentId())
        );

    if (!$farminfo || $post_cancel)
        UI::Redirect("/#/farms/view");
                
    if ($req_action == "Terminate")
    {			        	
    	if ($req_term_step == 2 && $farminfo['status'] == FARM_STATUS::RUNNING)
	    {
	    	$_SESSION['term_post'] = $_POST;
	    	$_SESSION['issync'] = isset($_POST['cbtn_2']) ? true : false;
	    	$display["term_step"] = 2;
	    }
    	else
    	{
    		$Logger->info("Terminating farm ID {$farminfo["id"]}");
    		    		
    		if ($_SESSION['issync'])
		    {
		    	foreach ($_SESSION['term_post']['sync'] as $farm_roleid)
		    	{
		    		try
		    		{
			    		$server_id = $_SESSION['term_post']['sync_i'][$farm_roleid];
			    		
			    		$DBServer = DBServer::LoadByID($server_id);
			    		$DBFarmRole = DBFarmRole::LoadByID($farm_roleid);
			    		
			    		$ServerSnapshotCreateInfo = new ServerSnapshotCreateInfo(
			    			$DBServer, 
			    			BundleTask::GenerateRoleName($DBFarmRole, $DBServer), 
			    			SERVER_REPLACEMENT_TYPE::REPLACE_FARM, 
			    			false, 
			    			sprintf(_("Server snapshot created during farm '%s' termination at %s"),
			    				$farminfo['name'],
			    				date("M j, Y H:i:s")
			    			)
			    		);
	            		
			    		BundleTask::Create($ServerSnapshotCreateInfo);
		    		}
		    		catch(Exception $e) { 
		    			$Logger->error("Farm terminate (67): {$e->getMessage()}");
		    		}
		    	}
		    }

		    if (count($err) == 0)
		    {
			    $remove_zone_from_DNS = ($post_deleteDNS) ? 1 : 0;
		
			    $term_on_sync_fail = ($_SESSION['term_post']["untermonfail"]) ? 0 : 1;
			    
			    $event = new FarmTerminatedEvent($remove_zone_from_DNS, $post_keep_elastic_ips, $term_on_sync_fail, $post_keep_ebs);
			    Scalr::FireEvent($farminfo['id'], $event);
				
				$okmsg = _("服务器组成功关闭");
			    UI::Redirect("/#/farms/view");
		    }
    	}
    }
    
    if ($farminfo["status"] == 0)
    {
        $display["action"] = "Launch";
        $display["show_dns"] = $db->GetOne("SELECT COUNT(*) FROM dns_zones WHERE farm_id=?", $farminfo['id']);
    }
    else
    { 
		if (!$display["term_step"])
    		$display["term_step"] = 1;
    		
    	$display["action"] = "Terminate";
        $display["num"] = $db->GetOne("SELECT COUNT(*) FROM servers WHERE farm_id=?", $farminfo['id']);
        
        $display["elastic_ips"] = $db->GetOne("SELECT COUNT(*) FROM elastic_ips WHERE farmid=?", array($farminfo['id']));
        $display["ebs"] = $db->GetOne("SELECT COUNT(*) FROM ec2_ebs WHERE farm_id=?", array($farminfo['id']));
        
        //
        // Synchronize before termination
        //
        $farm_launch_time = strtotime($farminfo['dtlaunched']);        
        $outdated_farm_roles = $db->GetAll("SELECT id FROM farm_roles WHERE (UNIX_TIMESTAMP(dtlastsync) < ? OR dtlastsync IS NULL) AND farmid=?",
        	array($farm_launch_time, $farminfo['id'])
        );
        foreach ($outdated_farm_roles as $farm_role)
        {
        	$DBFarmRole = DBFarmRole::LoadByID($farm_role['id']);

        	//TODO:
        	$DBFarmRole->dtLastSync = Formater::FuzzyTimeString(strtotime($DBFarmRole->dtLastSync), false);
        	$DBFarmRole->IsBundleRunning = $db->GetOne("SELECT id FROM bundle_tasks WHERE status NOT IN ('success','failed') AND role_id=? AND farm_id IN (SELECT id FROM farms WHERE client_id=?)", array(
        		$DBFarmRole->RoleID,
        		$farminfo['clientid']
        	));

        	$DBFarmRole->RunningServers = $DBFarmRole->GetServersByFilter(array('status' => SERVER_STATUS::RUNNING));
        	
        	$display['outdated_farm_roles'][] = $DBFarmRole;
        }
        
        if (count($outdated_farm_roles) == 0)
        	$display["term_step"] = 2;
    }
    
	$display["title"] = sprintf(_("服务器组&nbsp;&raquo;&nbsp; %s"), ($display["action"]=="Terminate" ? "关闭" : $display["action"]));
	$display["new"] = ($req_new) ? "1" : "0";
	$display["iswiz"] = ($req_iswiz) ? "1" : "0";
	$display["farminfo"] = $farminfo;

	require_once("src/append.inc.php");
?>
