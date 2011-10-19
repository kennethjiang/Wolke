<? 
	require("src/prepend.inc.php"); 
	
	$display["title"] = _("Tools&nbsp;&raquo;&nbsp;Amazon Web Services&nbsp;&raquo;&nbsp;Amazon RDS&nbsp;&raquo;&nbsp;Launch new DB Instance");
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}	
	
	//
	// set region first
	// the $req_snapshot is used to hide the "Select region" menu during restoring instance from snapshot proccess
	//  
	
    if (!$_POST['region'] && $_POST['step'] != 2 && !$req_snapshot)
    {
    	$display["title"] = _("Tools&nbsp;&raquo;&nbsp;Amazon Web Services&nbsp;&raquo;&nbsp;Amazon RDS&nbsp;&raquo;&nbsp;Region for new DB Instance");	    					
    	$Smarty->assign($display);
		$Smarty->display("region_information_step.tpl");
		exit();
    }	 
    else  // if region was set
    {   	
    	if(!$req_region) // used to continue restoring from snapshot with snapshot's region parameters
    		$req_region = $_SESSION['aws_region'];

    	$display['region'] = $req_region;
    }
    	
    $AmazonRDSClient = Scalr_Service_Cloud_Aws::newRds( 
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::ACCESS_KEY),
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::SECRET_KEY),
		$_SESSION['aws_region']
	);
 		
	if ($_POST && $_POST['step'] == 2)
	{			
		$_POST['PreferredMaintenanceWindow'] = "{$_POST['pmw1']['ddd']}:{$_POST['pmw1']['hh']}:{$_POST['pmw1']['mm']}-{$_POST['pmw2']['ddd']}:{$_POST['pmw2']['hh']}:{$_POST['pmw2']['mm']}";
		$_POST['PreferredBackupWindow'] = "{$_POST['pbw1']['hh']}:{$_POST['pbw1']['mm']}-{$_POST['pbw2']['hh']}:{$_POST['pbw2']['mm']}";

		try
		{		
			if (!$post_snapshot)
			{	
				//
				// Creates new instance 
				//			
				$AmazonRDSClient->CreateDBInstance(
					$_POST['DBInstanceIdentifier'],
					$_POST['AllocatedStorage'],
					$_POST['DBInstanceClass'],
					$_POST['Engine'],				
					$_POST['MasterUsername'],
					$_POST['MasterUserPassword'],					
					$_POST['Port'],
					$_POST['DBName'],
					$_POST['DBParameterGroup'],
					$_POST['DBSecurityGroups'],
					$_POST['AvailabilityZone'] ? $_POST['AvailabilityZone'] : null,
					$_POST['PreferredMaintenanceWindow'],
					$_POST['BackupRetentionPeriod'],
					$_POST['PreferredBackupWindow'],
					$_POST['MultiAZ']
				);
			}
			else
			{
				// 
				// Restores instance
				//
				$AmazonRDSClient->RestoreDBInstanceFromDBSnapshot(
					$_POST['snapshot'], 
					$_POST['DBInstanceIdentifier'],
					$_POST['DBInstanceClass'],
					$_POST['Port'],
					$_POST['AvailabilityZone'] ? $_POST['AvailabilityZone'] : null,
					$_POST['MultiAZ'] 
				);
			}
		}
		catch(Exception $e)
		{			
			$err[] = $e->getMessage();
			$display['POST'] = $_POST;
		}
	
		if (count($err) == 0)
		{			
			$okmsg = _("DB instance successfully launched");
			UI::Redirect("/#/tools/aws/rds/instances");
		}
	}	
	
	if ($req_snapshot)
		$display['snapshot'] = $req_snapshot; 
	
	if (!$req_snapshot)
	{
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
				$display['DBParameterGroups'][] = $group;
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
	}
	
	//
	// Load avail zones
	//
 
	$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
		$_SESSION['aws_region'], 
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
	);	
	
    // Get Avail zones
    $avail_zones_resp = $AmazonEC2Client->DescribeAvailabilityZones();
    $display["avail_zones"] = array();
    
    // Random assign zone
    array_push($display["avail_zones"], "");
    
    foreach ($avail_zones_resp->availabilityZoneInfo->item as $zone)
    {
    	if (stristr($zone->zoneState,'available'))
    		array_push($display["avail_zones"], (string)$zone->zoneName);
    }
	
	require("src/append.inc.php"); 
?>