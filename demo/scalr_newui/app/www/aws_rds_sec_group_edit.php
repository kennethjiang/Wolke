<? 
	require("src/prepend.inc.php"); 
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
	
	if (!$req_name)
	{
	    $errmsg = "Please select security group from list";
	    UI::Redirect("aws_rds_security_groups.php");
	}
	
	$display["title"] = _("Tools&nbsp;&raquo;&nbsp;Amazon Web Services&nbsp;&raquo;&nbsp;Amazon RDS&nbsp;&raquo;&nbsp;Security Groups&nbsp;&raquo;&nbsp;Edit group ({$req_name})");
	
	$display["group_name"] = $req_name;	
	
	$AmazonRDSClient = Scalr_Service_Cloud_Aws::newRds( 
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::ACCESS_KEY),
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::SECRET_KEY),
		$_SESSION['aws_region']
	);
		
	// Rows
	try
	{
		$response = $AmazonRDSClient->DescribeDBSecurityGroups($req_name);	
		$group = $response->DescribeDBSecurityGroupsResult->DBSecurityGroups->DBSecurityGroup;
			
		if ($group && $group instanceof SimpleXMLElement)
		{
			$rules = array();
			foreach ($group->IPRanges->IPRange as $r)
				$rules[] = $r;
							
			foreach ($group->EC2SecurityGroups->EC2SecurityGroup as $r)
				$rules[] = $r;
		}
	}
	catch(Exception $e)
	{
		$errmsg = $e->getMessage();
		UI::Redirect("sec_groups_view.php");
	}

	$group_rules = array();
		
	foreach ($rules as $rule)
	{		
		if ($rule->EC2SecurityGroupName)
		{
			$owner = (string)$rule->EC2SecurityGroupOwnerId;
			$group = (string)$rule->EC2SecurityGroupName;
			
			$r = new stdClass();
			$r->rule = "user:{$owner}:{$group}";
			$r->userId = $owner;
			$r->type = 'user';
			$r->groupname = $group;
			$r->status = (string)$rule->Status;
			$r->id = md5($r->rule);
			
			if (!$group_rules[$r->id])
			{
				$display['ug_rules'][$r->id] = $r;
				$group_rules[$r->id] = $r;
			}
		}
		elseif ($rule->CIDRIP)
		{
			$ip = (string)$rule->CIDRIP;
			
			$r = new stdClass();
			$r->rule = "iprange:{$ip}";
			$r->ip = $ip;
			$r->type = 'iprange';
			$r->status = (string)$rule->Status;
			$r->id = md5($r->rule);
			
			if (!$group_rules[$r->id])
			{
				$display['rules'][$r->id] = $r;
				$group_rules[$r->id] = $r;
			}	
		}		
	}

	if ($_POST)
	{	    		
	    $exists_rules = array();
	    $add_rules = array();
		foreach ((array)$post_rules as $rule)
        {
			if (!$group_rules[md5($rule)] && $rule)
			{
        		$group_rule = explode(":", $rule);
        		
        		if ($group_rule[0] == 'iprange')
					$add_rules[] = array('type' => 'iprange', 'iprange' => $group_rule[1]);
        		else
        			$add_rules[] = array('type' => 'user', 'user' => $group_rule[1], 'group' => $group_rule[2]);

				$new_rules_added = true;
			}
						
			$exists_rules[md5($rule)] = true;
		}
				
		$rem_rules = array();
		foreach ($group_rules as $rule_hash=>$rule)
		{
			if (!$exists_rules[$rule_hash])
			{
				if ($rule->type == 'iprange')
					$rem_rules[] = array('type' => 'iprange', 'iprange' => $rule->ip);
				else
					$rem_rules[] = array('type' => 'user', 'user' => $rule->userId, 'group' => $rule->groupname);
								
				$remove_rules = true;
			}
		}
		
		try
		{	        
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
	        
	        if ($remove_rules)
	        {
	        	foreach ($rem_rules as $r)
				{
		        	// Set permissions for group
		        	if ($r['type'] == 'iprange')
			        	$AmazonRDSClient->RevokeDBSecurityGroupIngress($req_name, $r['iprange']);
			        else
			        	$AmazonRDSClient->RevokeDBSecurityGroupIngress($req_name, null, $r['group'], $r['user']);
				}
	        }
	        
			$okmsg = "DB security group successfully updated";	        
	        UI::Redirect("aws_rds_sec_group_edit.php?name={$req_name}");
		}
		catch(Exception $e)
		{
			$errmsg = $e->getMessage();
		}
	}
	
	require("src/append.inc.php"); 
?>