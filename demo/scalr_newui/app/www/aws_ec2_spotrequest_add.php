<?php

	require("src/prepend.inc.php"); 
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
	
	$display["title"] = _("Tools&nbsp;&raquo;&nbsp;Amazon Web Services&nbsp;&raquo;&nbsp;Amazon EC2&nbsp;&raquo;&nbsp;Spot requests&nbsp;&raquo;&nbsp;Configure spot request");			

	
	// Get Avail zones
	$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
		$_SESSION['aws_region'],
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
	);
			
    $avail_zones_resp = $AmazonEC2Client->DescribeAvailabilityZones();
    $display["avail_zones"] = array();       	
		
    // Random assign zone
    array_push($display["avail_zones"], "");
    
    foreach ($avail_zones_resp->availabilityZoneInfo->item as $zone)
    {
    	if (stristr($zone->zoneState,'available'))
    		array_push($display["avail_zones"], (string)$zone->zoneName);
    }    			
  
  	$display['amiId'] = $req_id;
  	
  	// depending on the ami's architecture we select it's type:
    if($req_arch == 'x86_64')  
    {
    	$r = new ReflectionClass("X86_64_TYPE");
   		$display['instance_type'] = array_values($r->getConstants());
    }    	
    elseif($req_arch == 'i386')
    {
    	$r = new ReflectionClass("I386_TYPE");
   		$display['instance_type'] = array_values($r->getConstants());
    }   
   	else
   	{
   		$errmsg = "AMI architecture type is empty. Please select correct AMI";
   		unset($r);
		UI::Redirect("aws_ec2_amis_view.php");
   	}
   	unset($r);
   	
   	if (!$req_id)
	{
		$errmsg = "Please select the ami first";
		UI::Redirect("aws_ec2_amis_view.php");
	}	
	
	if($_POST)
	{

		try
		{							
			if(!$req_spotPrice)
			{
				$errmsg = "Please enter correct max price";
				UI::Redirect("aws_ec2_spotrequest_add.php");
			}
			
			if (!$req_aws_instance_type)
			{
				$errmsg = "Please select instance type";
				UI::Redirect("aws_ec2_amis_view.php");
			}
			if(!$req_count)
			{
				$errmsg = "Please enter number of instances to request";
				UI::Redirect("aws_ec2_spotrequest_add.php");
			}
			
			$launchSpecification = new LaunchSpecificationType($req_id,null,null,null,null,$req_aws_instance_type,null);
				
			$launchGroup 	= null;			// amazon doesnt' have full documentation and support for this functions

			
			//validFrom date format: "Y-m-d\TH:i:s.\Z";	
			$requestSpotInstance = new RequestSpotInstancesType($req_spotPrice,$req_count,$req_isPersistanType,
													$req_validFrom,$req_validUntil,$launchGroup,
													$availabilityZoneGroup,$launchSpecification);			
			
			$res = $AmazonEC2Client->RequestSpotInstances($requestSpotInstance);

			$okmsg = "Spot request created successfully.";	
			UI::Redirect("aws_ec2_spotrequest_view.php");
		}
		catch(Exception $e)
		{
			$err[] = $e->getMessage(); //Cannot create spot request for this ami 
			UI::Redirect("aws_ec2_amis_view.php");
		}	
		
	}	
	
	require("src/append.inc.php"); 

?>