<?
	require_once('src/prepend.inc.php');
    $display['load_extjs'] = true;	    
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
	
	if ($req_farmid)
		$display['grid_query_string'] .= "&farmid={$req_farmid}";
	
	if ($req_action == 'remove')
	{
		try
		{
			$roleid = $db->GetOne("SELECT farm_roleid FROM farm_role_settings WHERE name=? AND value=?",
			array(
				DBFarmRole::SETTING_BALANCING_NAME,
				$req_name
			));
									
			$AmazonELBClient = Scalr_Service_Cloud_Aws::newElb(
				$_SESSION['aws_region'], 
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY), 
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY)
			);
	
			$AmazonELBClient->DeleteLoadBalancer($req_name);
			
			if ($roleid)
			{
				$DBFarmRole = DBFarmRole::LoadByID($roleid);
				$DBFarmRole->SetSetting(DBFarmRole::SETTING_BALANCING_USE_ELB, 0);
				$DBFarmRole->SetSetting(DBFarmRole::SETTING_BALANCING_HOSTNAME, "");
				$DBFarmRole->SetSetting(DBFarmRole::SETTING_BALANCING_NAME, "");
			}
			
			$okmsg = _("Load balancer successfully removed");
			UI::Redirect('/aws_elb.php');
		}
		catch(Exception $e)
		{
			$errmsg = sprintf(_('Cannot remove load balancer: %s'), $e->getMessage());
		}
	}
		
	$display['title'] = 'Elastic Load Balancers';
		
	require_once ("src/append.inc.php");
?>