<? 
	require("src/prepend.inc.php"); 
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
	
	$display["title"] = _("Tools&nbsp;&raquo;&nbsp;Amazon Web Services&nbsp;&raquo;&nbsp;Amazon RDS&nbsp;&raquo;&nbsp;Security groups&nbsp;&raquo;&nbsp;Add new");
	$display["add"] = true;
	$template_name = "aws_rds_sec_group_edit.tpl";
		
	// set region first 
    if (!$_POST['region'] && $_POST['step'] != 2)
    {
    	$display["title"] = _("Tools&nbsp;&raquo;&nbsp;Amazon Web Services&nbsp;&raquo;&nbsp;Amazon RDS&nbsp;&raquo;&nbsp;Security groups&nbsp;&raquo;&nbsp;Region for security group");	    					
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
	    $exists_rules = array();
	    $add_rules = array();
		foreach ((array)$post_rules as $rule)
        {
        	$group_rule = explode(":", $rule);
        	
        	if ($group_rule[0] == 'iprange')
				$add_rules[] = array('type' => 'iprange', 'iprange' => $group_rule[1]);
        	else
        		$add_rules[] = array('type' => 'user', 'user' => $group_rule[1], 'group' => $group_rule[2]);

			$new_rules_added = true;
						
			$exists_rules[md5($rule)] = true;
		}
			
		
		try
		{
			$AmazonRDSClient->CreateDBSecurityGroup($req_name, $req_description);

			if ($new_rules_added)
	        {
				foreach ($add_rules as $r)
				{
		        	// Set permissions for group
		        	if ($r['type'] == 'iprange')
			        	$AmazonRDSClient->AuthorizeDBSecurityGroupIngress($req_name, $r['iprange']);
			        else
			        	$AmazonRDSClient->AuthorizeDBSecurityGroupIngress($req_name, null, $r['group'], $r['user']);
				}
	        }
	        	        
			$okmsg = "DB security group successfully added";	        
	        UI::Redirect("aws_rds_security_groups.php");
		}
		catch(Exception $e)
		{
			$errmsg = $e->getMessage();
		}
	}
	
	require("src/append.inc.php"); 
?>