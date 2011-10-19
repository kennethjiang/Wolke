<?php
	
	require("src/prepend.inc.php"); 
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
	
	$display['load_extjs'] = true;
	$display["createDatefeed"] = false;	 // field for smarty template to show "create" message OR datafeed info
	$display["title"] = _("Tools&nbsp;&raquo;&nbsp;Amazon Web Services&nbsp;&raquo;&nbsp;Amazon EC2&nbsp;&raquo;&nbsp;Datafeed");
	try
	{	
		$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
			$_SESSION['aws_region'],
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		$aws_response = $AmazonEC2Client->DescribeSpotDatafeedSubscription();
				
		$rowz = (array)$aws_response->spotDatafeedSubscription;
							
		$display["bucket"]	= (string)$rowz['bucket'];
		$display["state"]	= (string)$rowz['state'];					
		$display["prefix"]	= (string)$rowz['prefix'];
		
	}
	catch(Exception $e)
	{		
		// if datafeed is not existed scalr shows invitation to create new one 
		$display["createDatefeed"] = true;		
	}
	
	if($req_cbtn_2 == 'Create new datafeed')
	{		
		UI::Redirect("aws_ec2_datafeed_add.php");
	}
	
	if ($req_cbtn_2 == 'Delete datafeed')
	{ 			
		try
		{				
			$AmazonEC2Client->DeleteSpotDatafeedSubscription();						
		}
		catch(Exception $e)
		{
			$err[] =  $e->getMessage(); // Can't delete Datafeed
			UI::Redirect("aws_ec2_datafeed_view.php");
		}	
					
		$okmsg = sprintf(_("Datafeed  successfully removed"));
		
		UI::Redirect("aws_ec2_datafeed_view.php");
		
	}
	
	require("src/append.inc.php"); 	

?>
