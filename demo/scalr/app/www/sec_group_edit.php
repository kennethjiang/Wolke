<? 
	require("src/prepend.inc.php"); 
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
	
	if ($req_farm_roleid)
	{
		$DBFarmRole = DBFarmRole::LoadByID($req_farm_roleid);
		
		if ($DBFarmRole->GetSetting(DBFarmRole::SETTING_AWS_SECURITY_GROUP))
			$req_name = $DBFarmRole->GetSetting(DBFarmRole::SETTING_AWS_SECURITY_GROUP); 
		else
			$req_name = CONFIG::$SECGROUP_PREFIX.$DBFarmRole->GetRoleObject()->name;
	}
	   
	if (!$req_name)
	{
	    $errmsg = "Please select security group from list";
	    UI::Redirect("sec_groups_view.php");
	}
	
	
	$display["title"] = "Security group&nbsp;&raquo;&nbsp;Edit group '{$req_name}'";
	$display["group_name"] = $req_name;
	$display["platform"] = $req_platform;
	$display["location"] = $req_location;
	
	switch($req_platform)
	{
		case SERVER_PLATFORMS::EC2:
			
			$platformClient = Scalr_Service_Cloud_Aws::newEc2(
				$req_location,
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
			);
			
			$account_id = Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCOUNT_ID);
			
			break;
			
		case SERVER_PLATFORMS::EUCALYPTUS:
			
			$platformClient = Scalr_Service_Cloud_Eucalyptus::newCloud(
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Eucalyptus::SECRET_KEY, true, $req_location),
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Eucalyptus::ACCESS_KEY, true, $req_location),
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Eucalyptus::EC2_URL, true, $req_location)
			);
			
			$account_id = Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Eucalyptus::ACCOUNT_ID, true, $req_location);
			
			break;
	}
	
	// Rows
	try
	{
		if ($req_platform == SERVER_PLATFORMS::EC2)
			$response = $platformClient->DescribeSecurityGroups($req_name);
		else
			$response = $platformClient->describeSecurityGroups(array($req_name));
		
		$group = $response->securityGroupInfo->item;
		if (!($group instanceof stdClass))
			$group = $group[0];
		
		if ($group && $group instanceof stdClass)
		{	
			$rules = $group->ipPermissions->item;
			
			if ($rules instanceof stdClass)
				$rules = array($rules);
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
		if ($rule->groups->item  && !is_array($rule->groups->item))
			$rule->groups->item = array($rule->groups->item);
		
		if (count($rule->groups->item) > 0)
		{
			foreach ($rule->groups->item as &$group)
			{
				if ($group)
				{
					$r = clone $rule;
					$r->ip = '0.0.0.0/0';
					$r->rule = "user:{$group->userId}:{$group->groupName}:0.0.0.0/0";
					$r->userId = $group->userId;
					$r->type = 'user';
					$r->groupname = $group->groupName;
					$r->id = md5($r->rule);
					
					if (!$group_rules[$r->id])
					{
						$display['ug_rules'][$r->id] = $r;
						$group_rules[$r->id] = $r;
					}
				}
			}
		}
		elseif (count($rule->ipRanges->item) > 1)
		{
			foreach ($rule->ipRanges->item as &$ipRange)
			{
				if ($ipRange)
				{
					$r = clone $rule;
					
					$r->ip = $ipRange->cidrIp;
					$r->rule = "{$r->ipProtocol}:{$r->fromPort}:{$r->toPort}:{$ipRange->cidrIp}";
					
					$r->id = md5($r->rule);
					
					if (!$display['rules'][$r->id])
					{
						$display['rules'][$r->id] = $r;
						$group_rules[$r->id] = $r;
					}
				}
			}	
		}
		else
		{
			$rule->ip = $rule->ipRanges->item->cidrIp;
			$rule->rule = "{$rule->ipProtocol}:{$rule->fromPort}:{$rule->toPort}:{$rule->ip}";
			
			$rule->id = md5($rule->rule);
			
			$display['rules'][$rule->id] = $rule;
			$group_rules[$rule->id] = $rule;
		}
		
		
	}
	
	if ($_POST)
	{	    		
		$addRulesSet = array();
		$exists_rules = array();
		foreach ((array)$post_rules as $rule)
        {
			if (!$group_rules[md5($rule)] && $rule)
			{
        		$group_rule = explode(":", $rule);
        		
        		if ($group_rule[0] != 'user')
        		{
					$addRulesSet[] = array(
						'IpProtocol'	=> $group_rule[0],
						'FromPort'		=> $group_rule[1],
						'ToPort'		=> $group_rule[2],
						'CidrIp'		=> $group_rule[3]
					);
        		}
        		else
        		{
        			$addRulesSet[] = array(
						'IpProtocol'	=> 'tcp',
						'FromPort'		=> 1,
						'ToPort'		=> 65535,
						'GroupName'		=> $group_rule[2],
        				'UserId'		=> $group_rule[1]
					);
					
					$addRulesSet[] = array(
						'IpProtocol'	=> 'udp',
						'FromPort'		=> 1,
						'ToPort'		=> 65535,
						'GroupName'		=> $group_rule[2],
        				'UserId'		=> $group_rule[1]
					);
					
					$addRulesSet[] = array(
						'IpProtocol'	=> 'icmp',
						'FromPort'		=> -1,
						'ToPort'		=> -1,
						'GroupName'		=> $group_rule[2],
        				'UserId'		=> $group_rule[1]
					);
        		}

				$new_rules_added = true;
			}
						
			$exists_rules[md5($rule)] = true;
		}
		
		$remRulesSet = array();
		foreach ($group_rules as $rule_hash=>$rule)
		{
			if (!$exists_rules[$rule_hash])
			{
				if ($rule->type != 'user')
				{
					$remRulesSet[] = array(
						'IpProtocol'	=> $rule->ipProtocol,
						'FromPort'		=> $rule->fromPort,
						'ToPort'		=> $rule->toPort,
						'CidrIp'		=> $rule->ip
					);
				}
				else
				{
					$remRulesSet[] = array(
						'IpProtocol'	=> 'tcp',
						'FromPort'		=> 1,
						'ToPort'		=> 65535,
						'GroupName'		=> $rule->groupname,
        				'UserId'		=> $rule->userId
					);
					
					$remRulesSet[] = array(
						'IpProtocol'	=> 'udp',
						'FromPort'		=> 1,
						'ToPort'		=> 65535,
						'GroupName'		=> $rule->groupname,
        				'UserId'		=> $rule->userId
					);
					
					$remRulesSet[] = array(
						'IpProtocol'	=> 'icmp',
						'FromPort'		=> -1,
						'ToPort'		=> -1,
						'GroupName'		=> $rule->groupname,
        				'UserId'		=> $rule->userId
					);
				}
								
				$remove_rules = true;
			}
		}
		
		try {
			if ($new_rules_added)
			{
				if ($req_platform == SERVER_PLATFORMS::EUCALYPTUS)
				{
					foreach ($addRulesSet as $rule)
						$platformClient->authorizeSecurityGroupIngress(
							$req_name, 
							$rule['IpProtocol'], 
							$rule['FromPort'], 
							$rule['ToPort'], 
							$rule['CidrIp'], 
							$rule['GroupName'], 
							$rule['UserId']
						);
				}
				else
				{
					$IpPermissionSet = new IpPermissionSetType();
					foreach ($addRulesSet as $rule) {
						if ($rule['GroupName'])
							$IpPermissionSet->AddItem(
								$rule['IpProtocol'], 
								$rule['FromPort'], 
								$rule['ToPort'], 
								array('userId' => $rule['UserId'], 'groupName' => $rule['GroupName']), 
								null
							);
						else
							$IpPermissionSet->AddItem(
								$rule['IpProtocol'], 
								$rule['FromPort'], 
								$rule['ToPort'], 
								null, 
								array($rule['CidrIp'])
							);
					}
					
					$platformClient->AuthorizeSecurityGroupIngress($account_id, $req_name, $IpPermissionSet);
				}
			}
			
			if ($remove_rules)
			{
				if ($req_platform == SERVER_PLATFORMS::EUCALYPTUS)
				{
					foreach ($remRulesSet as $rule)
						$platformClient->revokeSecurityGroupIngress(
							$req_name, 
							$rule['IpProtocol'], 
							$rule['FromPort'], 
							$rule['ToPort'], 
							$rule['CidrIp'], 
							$rule['GroupName'], 
							$rule['UserId']
						);
				}
				else
				{
					$IpPermissionSet = new IpPermissionSetType();
					foreach ($remRulesSet as $rule) {
						
						if ($rule['GroupName'])
							$IpPermissionSet->AddItem(
								$rule['IpProtocol'], 
								$rule['FromPort'], 
								$rule['ToPort'], 
								array('userId' => $rule['UserId'], 'groupName' => $rule['GroupName']), 
								null
							);
						else
							$IpPermissionSet->AddItem(
								$rule['IpProtocol'], 
								$rule['FromPort'], 
								$rule['ToPort'], 
								null, 
								array($rule['CidrIp'])
							);
					}
					
					$platformClient->RevokeSecurityGroupIngress($account_id, $req_name, $IpPermissionSet);
				}
			}
		}
		catch(Exception $e)
		{
			$errmsg = $e->getMessage();
		}
			
		if (!$errmsg)
		{
			$okmsg = _("Security group successfully updated");
			UI::Redirect("/sec_group_edit.php?name={$req_name}&platform={$req_platform}&location={$req_location}");
		}
	}
	
	require("src/append.inc.php"); 
?>