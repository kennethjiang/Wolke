<? 
	require("src/prepend.inc.php"); 
	
	$display["title"] = _("Tools&nbsp;&raquo;&nbsp;Amazon Web Services&nbsp;&raquo;&nbsp;Amazon RDS&nbsp;&raquo;&nbsp;Modify DB Instance");
		
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
	
	$AmazonRDSClient = Scalr_Service_Cloud_Aws::newRds( 
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::ACCESS_KEY),
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::SECRET_KEY),
		$_SESSION['aws_region']
	);
	
	if (!$req_name)
	{
		UI::Redirect("/#/tools/aws/rds/instances");
	}
	
	try
	{
		$instance = $AmazonRDSClient->DescribeDBInstances($req_name);
		$instance = $instance->DescribeDBInstancesResult->DBInstances->DBInstance;
	}
	catch(Exception $e)
	{
		$errmsg = "AWS error: {$e->getMessage()}";
		UI::Redirect("/#/tools/aws/rds/instances");
	}
	
	
	$sg = (array)$instance->DBSecurityGroups;
	$sec_groups = array();
	if (is_array($sg['DBSecurityGroup']))
	{
		foreach ($sg['DBSecurityGroup'] as $g)
			$sec_groups[(string)$g->DBSecurityGroupName] = (array)$g;
			
	}
	else
		$sec_groups = array((string)$sg['DBSecurityGroup']->DBSecurityGroupName => (array)$sg['DBSecurityGroup']);
		
	$pg = (array)$instance->DBParameterGroups;
	$param_groups = array();
	if (is_array($pg['DBParameterGroup']))
	{
		foreach ($pg['DBParameterGroup'] as $g)
			$param_groups[(string)$g->DBParameterGroupName] = (array)$g;
			
	}
	else
		$param_groups = array((string)$pg['DBParameterGroup']->DBParameterGroupName => (array)$pg['DBParameterGroup']);
		
	$display['sec_groups'] = array_keys($sec_groups);
	
	if ($_POST)
	{		
		$_POST['PreferredMaintenanceWindow'] = "{$_POST['pmw1']['ddd']}:{$_POST['pmw1']['hh']}:{$_POST['pmw1']['mm']}-{$_POST['pmw2']['ddd']}:{$_POST['pmw2']['hh']}:{$_POST['pmw2']['mm']}";
		$_POST['PreferredBackupWindow'] = "{$_POST['pbw1']['hh']}:{$_POST['pbw1']['mm']}-{$_POST['pbw2']['hh']}:{$_POST['pbw2']['mm']}";

		try
		{				
			if ($instance->DBInstanceStatus == 'storage-full')
			{
				$AmazonRDSClient->ModifyDBInstance(
					$req_name,
					null,
					null,
					null,
					null,
					$_POST['AllocatedStorage'],
					null,
					null,
					null,
					null,
					null
				);
			}
			else
			{
				$AmazonRDSClient->ModifyDBInstance(
					$req_name,
					$_POST['DBParameterGroupName'] == 'default.mysql5.1' ? null : $_POST['DBParameterGroupName'],
					$_POST['DBSecurityGroups'],
					$_POST['PreferredMaintenanceWindow'],
					$_POST['MasterUserPassword'] ? $_POST['MasterUserPassword'] : null,
					$_POST['AllocatedStorage'],
					$_POST['DBInstanceClass'],
					$_POST['ApplyImmediately'],
					$_POST['BackupRetentionPeriod'],
					$_POST['PreferredBackupWindow'],
					$_POST['MultiAZ']?1:0
				);
			}
		}
		catch(Exception $e)
		{
			$err[] = $e->getMessage();
		}
		
		if (count($err) == 0)
		{
			$okmsg = _("DB instance successfully updated");
			UI::Redirect("/#/tools/aws/rds/instances");
		}
	}
	
	//
	// Load DB parameter groups
	//
	$DBParameterGroups = $AmazonRDSClient->DescribeDBParameterGroups();
	$groups = (array)$DBParameterGroups->DescribeDBParameterGroupsResult->DBParameterGroups;
	$groups = $groups['DBParameterGroup'];	
	if ($groups)
	{
		if (!is_array($groups))
			$groups = array($groups);
			
		foreach ((array)$groups as $group)
			$display['DBParameterGroups'][] = (array)$group;
	}
	
	//
	// Load DB security groups
	//
	$DescribeDBSecurityGroups = $AmazonRDSClient->DescribeDBSecurityGroups();
	$sgroups = (array)$DescribeDBSecurityGroups->DescribeDBSecurityGroupsResult->DBSecurityGroups;
	$sgroups = $sgroups['DBSecurityGroup'];
	if ($sgroups)
	{
		if (!is_array($sgroups))
			$sgroups = array($sgroups);
			
		foreach ((array)$sgroups as $sgroup)
			$display['DBSecurityGroups'][] = $sgroup;
	}
	
	$display['instance'] = $instance;
	$display['DBParameterGroupName'] = (string)$instance->DBParameterGroups->DBParameterGroup->DBParameterGroupName;
	
	require("src/append.inc.php"); 
?>