<?
	require_once('src/prepend.inc.php');
    $display['load_extjs'] = false;	    
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
		
	if (!$req_name)
		UI::Redirect("/#/dashboard");
		
	$display['title'] = 'Elastic Load Balancer details';
	
	$AmazonELBClient = Scalr_Service_Cloud_Aws::newElb(
		$_SESSION['aws_region'], 
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY), 
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY)
	);

	if ($_POST['action'] == 'associate_sp')
	{
		try {
			$AmazonELBClient->SetLoadBalancerPoliciesOfListener($req_name, $req_sp_p, $req_p);
			
			$okmsg = _("Stickiness policies successfully associated with listener");
			UI::Redirect("/aws_elb_details.php?name={$req_name}");
			
		}catch (Exception $e){
			$err[] = $e->getMessage();
		}
	}
	elseif ($_POST['action'] == 'create_sp')
	{
		try {
			if ($post_sp_type == 'AppCookieStickinessPolicies')
				$AmazonELBClient->CreateAppCookieStickinessPolicy($req_name, $req_sp_name, $req_sp_cname);
			else
				$AmazonELBClient->CreateLBCookieStickinessPolicy($req_name, $req_sp_name, $req_sp_cname);

			$okmsg = _("Stickiness policy successfully created");
			UI::Redirect("/aws_elb_details.php?name={$req_name}");
			
		} catch(Exception $e) {
			$err[] = $e->getMessage();
		}
	}
	
	$info = $AmazonELBClient->DescribeLoadBalancers(array($req_name));
	$elb = $info->DescribeLoadBalancersResult->LoadBalancerDescriptions->member;
	
	if (!is_array($elb->Policies->AppCookieStickinessPolicies->member))
		$elb->Policies->AppCookieStickinessPolicies->member = array($elb->Policies->AppCookieStickinessPolicies->member);
		
	if (!is_array($elb->Policies->LBCookieStickinessPolicies->member))
		$elb->Policies->LBCookieStickinessPolicies->member = array($elb->Policies->LBCookieStickinessPolicies->member);
	
	if (!is_array($elb->Instances->member))
		$elb->Instances->member = array($elb->Instances->member);
		
	if (!is_array($elb->AvailabilityZones->member))
		$elb->AvailabilityZones->member = array($elb->AvailabilityZones->member);
		
	if (!is_array($elb->Listeners->member))
		$elb->AvailabilityZones->member = array($elb->AvailabilityZones->member);
	
	$display['elb'] = $elb;
	
	require_once ("src/append.inc.php");
?>