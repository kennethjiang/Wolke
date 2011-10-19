<? 
	require("src/prepend.inc.php"); 
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
        
	$display['load_extjs'] = true;
	
	if (!in_array($req_platform, array(SERVER_PLATFORMS::EC2, SERVER_PLATFORMS::EUCALYPTUS)))
		UI::Redirect("/#/dashboard");
		
	if (!Scalr_Session::getInstance()->getEnvironment()->isPlatformEnabled($req_platform))
	{
		$errmsg = sprintf(_("%s platform is not enabled for current environment"), ucfirst($req_platform));
		UI::Redirect("/#/dashboard");
	}
	
	$locations = PlatformFactory::NewPlatform($req_platform)->getLocations();
	$display['locations'] = array();
	foreach ($locations as $k => $v)
		$display['locations'][] = array($k, $v);
	
	$display['locations'] = json_encode($display['locations']);
	if (!$req_location || !$locations[$req_location])
		$display['location'] = array_shift(array_keys($locations));
	else
		$display['location'] = $req_location;
	
	switch($req_platform)
	{
		case SERVER_PLATFORMS::EC2:
			
			$platformClient = Scalr_Service_Cloud_Aws::newEc2(
				$display['location'],
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
			);
			
			break;
			
		case SERVER_PLATFORMS::EUCALYPTUS:
			
			$platformClient = Scalr_Service_Cloud_Eucalyptus::newCloud(
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Eucalyptus::SECRET_KEY, true, $display['location']),
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Eucalyptus::ACCESS_KEY, true, $display['location']),
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Eucalyptus::EC2_URL, true, $display['location'])
			);
			
			break;
	}
                        
	$display["title"] = "Roles&nbsp;&raquo;&nbsp;Security groups";
	
	if ($_POST && $post_with_selected)
	{
		if ($post_action == 'delete')
		{
			$i = 0;
			foreach ($post_id as $group_name)
			{
				try
				{
					$platformClient->DeleteSecurityGroup($group_name);
					$i++;
				}
				catch(Exception $e)
				{
					$err[] = sprintf(_("Cannot delete group %s: %s"), $group_name, $e->getMessage());
				}
			}
			
			if ($i > 0)
				$okmsg = sprintf(_("%s security group(s) successfully removed"), $i);
				
			UI::Redirect("sec_groups_view.php");
		}
	}
	
	$display['platform'] = $req_platform;
	
	require("src/append.inc.php"); 	
?>