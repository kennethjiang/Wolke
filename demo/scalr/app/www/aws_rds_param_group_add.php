<?php
	require("src/prepend.inc.php"); 
		
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
	
	$display["title"] = _("Tools&nbsp;&raquo;&nbsp;Amazon Web Services&nbsp;&raquo;&nbsp;Amazon RDS&nbsp;&raquo;&nbsp;Add  new group");
		
	//
	// set region first
	// 
    if (!$_POST['region'] && $_POST['step'] != 2)
    {
		$display["title"] = _("Tools&nbsp;&raquo;&nbsp;Amazon Web Services&nbsp;&raquo;&nbsp;Amazon RDS&nbsp;&raquo;&nbsp;Region for parameters group");	    					
		$Smarty->assign($display);
		$Smarty->display("region_information_step.tpl");
		exit();
    }
    else  // if region was set
    	$display['region'] = $req_region;
    	
	$AmazonRDSClient = Scalr_Service_Cloud_Aws::newRds(
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY),
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY),
		$req_region
	);
	
	if ($_POST && $_POST['step'] == 2)
	{	
		try
		{	
			//
			// Creates DB parameter group
			//		
			if (!$req_newGroupName)
			{
				$errmsg = "Please add parameter group name";
				UI::Redirect("aws_rds_param_group_add.php");
			}
			if (!$req_engine)
			{
				$errmsg = "Please select parameter group engine";
				UI::Redirect("aws_rds_param_group_add.php");
			}
			if (!$req_description)
			{
				$errmsg = "Please add description";
				UI::Redirect("aws_rds_param_group_add.php");
			}
			
			$res = $AmazonRDSClient->CreateDBParameterGroup($req_newGroupName,$req_description,$req_engine);
			$okmsg = "DB parameter group successfully created";	
			UI::Redirect("aws_rds_param_group_edit.php?name={$req_newGroupName}");
		}
		catch(Exception $e)
		{
			
			$err[] = sprintf(_("Can't create db parameter group %s: %s"), $req_newGroupName, $e->getMessage());
		}
	}	
			
	require("src/append.inc.php"); 
?>