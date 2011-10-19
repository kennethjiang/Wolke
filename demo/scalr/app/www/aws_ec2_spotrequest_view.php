<?php
	
	require("src/prepend.inc.php"); 
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
		
	$display['load_extjs'] = true;		
	$display["title"] = _("Tools&nbsp;&raquo;&nbsp;Amazon Web Services&nbsp;&raquo;&nbsp;Amazon EC2&nbsp;&raquo;&nbsp;Spot requests&nbsp;&raquo;&nbsp;View spot instances requests");
	
	if ($_POST && $post_with_selected)
	{ 
		if ($post_action == 'delete')
		{			
			$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
				$_SESSION['aws_region'],
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
			);
									
			try
			{				
				$AmazonEC2Client->CancelSpotInstanceRequests( new CancelSpotInstanceRequestsType($post_spotInstanceRequestId));	
				$i = count($post_spotInstanceRequestId);			
			}
			catch(Exception $e)
			{
				$err[] =  $e->getMessage(); // Cannot cancel spot request
				UI::Redirect("aws_ec2_spotrequest_view.php");
			}			
			
			if ($i > 0)
				$okmsg = sprintf(_("%s Selected spot request(s) successfully canceled"), $i);
			
			UI::Redirect("aws_ec2_spotrequest_view.php");
		}
	}
	require("src/append.inc.php"); 	

?>
