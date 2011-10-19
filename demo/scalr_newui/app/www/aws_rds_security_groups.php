<? 
	require("src/prepend.inc.php"); 
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
        
	$display['load_extjs'] = true;
	                        
	$display["title"] = _("Tools&nbsp;&raquo;&nbsp;Amazon Web Services&nbsp;&raquo;&nbsp;Amazon RDS&nbsp;&raquo;&nbsp;Manage security groups");
	
	if ($_POST && $post_with_selected)
	{
		if ($post_action == 'delete')
		{
			$AmazonRDSClient = Scalr_Service_Cloud_Aws::newRds(
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY),
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY),
				$_SESSION['aws_region']
			);
				
			$i = 0;
			foreach ($post_id as $group_name)
			{
				try
				{
					$AmazonRDSClient->DeleteDBSecurityGroup($group_name);
					$i++;
				}
				catch(Exception $e)
				{
					$err[] = sprintf(_("Can't delete db security group %s: %s"), $group_name, $e->getMessage());
				}
			}

			if (!$err)
				$okmsg = sprintf(_("%s db secutity group(s) successfully removed"), $i);
				
			UI::Redirect("aws_rds_security_groups.php");
		}
	}
	
	require("src/append.inc.php"); 	
?>