<?php

	require("src/prepend.inc.php"); 
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}	
	
	$display['load_extjs'] = true;	
	$display["title"] = _("Tools&nbsp;&raquo;&nbsp;Amazon Web Services&nbsp;&raquo;&nbsp;Amazon EC2&nbsp;&raquo;&nbsp;Datafeed&nbsp;&raquo;&nbsp;Create new datafeed");

	$AmazonS3 = new AmazonS3(
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY),
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY)
	);
	
	// Get list of all user buckets for datafeed creation
    $buckets = $AmazonS3->ListBuckets();
   	$display["buckets"] = array();
    foreach ($buckets as $bucket)
    {		
    	array_push($display["buckets"], (string)$bucket->Name);
    }	   

	if($_POST)
	{			
		try
		{	
			$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
				$_SESSION['aws_region'],
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
			);
			
			$AmazonEC2Client->CreateSpotDatafeedSubscription($req_buckets);
			
		}
		catch(Exception $e)
		{		
			//if datafeed is existed, then new datafeed creation just changes bucket's id for that datafeed
			$err[] =  $e->getMessage(); // Cannot create datafeed 
			UI::Redirect("aws_ec2_datafeed_add.php");
		}		
		
		$okmsg = sprintf(_("Datafeed  successfully created"));
		
		UI::Redirect("aws_ec2_datafeed_view.php");
	}
	
	require("src/append.inc.php"); 	

?>