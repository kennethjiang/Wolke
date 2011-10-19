<? 
	require("src/prepend.inc.php"); 
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
		
	// select autosnapshot type by params
	
	if ($req_volumeId)
	{
		$objectId =  $req_volumeId;
		$object_type = AUTOSNAPSHOT_TYPE::EBSSnap;
	}
	elseif ($req_array_id)
	{
		$objectId =  $req_array_id;
		$object_type = AUTOSNAPSHOT_TYPE::EBSArraySnap;
	}
	elseif ($req_name)
	{
		$objectId =  $req_name;
		$object_type = AUTOSNAPSHOT_TYPE::RDSSnap;
	}
	
	if ($post_cancel)
	{
       switch($object_type)
		{
			case AUTOSNAPSHOT_TYPE::EBSSnap: 		UI::Redirect("/#/tools/aws/ec2/ebs/volumes/view"); 			break;
			case AUTOSNAPSHOT_TYPE::RDSSnap:		UI::Redirect("/#/tools/aws/rds/instances");	break;
			default: break;				
		}
	}
	
	switch($object_type)
	{
		case AUTOSNAPSHOT_TYPE::EBSSnap: 
			{
				// checks correctness of  EBS instance volume
				try
				{					
					$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
						$req_region,
						Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
						Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
					);
					
					$AmazonEC2Client->DescribeVolumes($objectId);
				}
				catch(Exception $e)
				{
					$errmsg = $e->getMessage();
					UI::Redirect("/#/tools/aws/ec2/ebs/volumes/view");
				}	
							
				$display["volumeId"] = $objectId;
				$display["title"] = _("Auto-snapshots settings for EC2 EBS volume '{$objectId}'");	
				break;	
			}
		case AUTOSNAPSHOT_TYPE::RDSSnap: 
			{
				// checks correctness of  RDS instance name
				try
				{
					$AmazonRDSClient = Scalr_Service_Cloud_Aws::newRds(
						Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::ACCESS_KEY),
						Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::SECRET_KEY),
						$_SESSION['aws_region']
					);
					
					$AmazonRDSClient->DescribeDBInstances($req_name);
						
					$req_region = $_SESSION['aws_region']; 
				}
				catch(Exception $e)
				{
					$errmsg = $e->getMessage();
					UI::Redirect("/#/tools/aws/rds/instances");
				}
				
				$display["title"] = _("Auto-snapshots settings for RDS instance '{$objectId}'");
				break;		
			}
			
	}
	// look for existing settings in DB by current objectid and type
	$info = $db->GetRow("SELECT * FROM autosnap_settings WHERE 
		objectid = ? AND 
		object_type = ? AND 
		env_id = ?", 
	array(
		$objectId,
		$object_type,
	 	Scalr_Session::getInstance()->getEnvironmentId()
	));
		
	$redirect = false;
	
	if ($_POST)
	{	
		// if we change settings...
		if ($post_enable == 1)
		{	
			if (!$err)
			{
				if (!$info)
				{			
					// add new settings record		
					$db->Execute("INSERT INTO autosnap_settings SET
						clientid 	= ?,						
						period		= ?,
						rotate		= ?,
						region		= ?,
						objectid	= ?,
						object_type	= ?,
						env_id		= ?
					", array(
						Scalr_Session::getInstance()->getClientId(),						
						$post_period,
						$post_rotate,
						$req_region,
						$objectId,						
						$object_type,
						Scalr_Session::getInstance()->getEnvironmentId()
					));
					
					$okmsg = sprintf(_("Auto-snapshots successfully enabled for %s"), $objectId);
				}
				else
				{		
					// or update old settings record			
					$db->Execute("UPDATE autosnap_settings SET
						period		= ?,
						rotate		= ?
						WHERE clientid = ? AND objectid = ? AND object_type = ?
					", array(
						$post_period,
						$post_rotate,
						Scalr_Session::getInstance()->getClientId(),
						$objectId,
						$object_type
					));
								
					$okmsg = sprintf(_("Auto-snapshot settings successfully updated for %s"), $objectId);
				}
				
				// redirect by type to the  previos page
				$redirect = true;
			}
		}
		else
		{			
			// if we don't want to continue  using settings for this instance (or volume)
			$db->Execute("DELETE FROM autosnap_settings WHERE 
				objectid	= ? AND 
				object_type	= ? AND 
				env_id		= ?",
			 array(
			 	$objectId,
			 	$object_type,
			 	Scalr_Session::getInstance()->getEnvironmentId()
			 ));			
				
			$okmsg = sprintf(_("Auto-snapshots successfully disabled"));
			
			$redirect = true;
		}
		
		if($redirect)	
			switch($object_type)
				{
					case AUTOSNAPSHOT_TYPE::EBSSnap: 		UI::Redirect("/#/tools/aws/ec2/ebs/volumes/view"); 			break;
					case AUTOSNAPSHOT_TYPE::RDSSnap:		UI::Redirect("/#/tools/aws/rds/instances");	break;	
					default: break;				
				}
	}
	
	$display['auto_snap'] = $info;
	$display['visible'] = ($info) ? "" : "none";
	$display["region"] = $req_region;
	        
	require("src/append.inc.php"); 
?>