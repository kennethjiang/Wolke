<?
	require_once('src/prepend.inc.php');
    $display['load_extjs'] = false;	    
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
		
	if (!$req_lb || !$req_iid)
		UI::Redirect("/#/dashboard");
		
	$display['title'] = 'AWS Load balancer > Instance health state';
	
	$AmazonELBClient = Scalr_Service_Cloud_Aws::newElb(
		$_SESSION['aws_region'], 
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY), 
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY)
	);

	if ($_POST)
	{
		if ($post_cbtn_2)
		{
			try
			{
				$res = $AmazonELBClient->DeregisterInstancesFromLoadBalancer($req_lb, array($req_iid));
				if ($res)
				{
					$okmsg = _("Instance successfully deregistered from the load balancer");
					UI::Redirect("aws_elb_details.php?name={$req_lb}");
				}
			}
			catch(Exception $e)
			{
				$errmsg = $e->getMessage();
			}
		}
	}
	
	$info = $AmazonELBClient->DescribeInstanceHealth($req_lb, array($req_iid));
	
	$display['info'] = $info->DescribeInstanceHealthResult->InstanceStates->member; 

	$display['name'] = htmlspecialchars($req_lb);
	
	require_once ("src/append.inc.php");
?>