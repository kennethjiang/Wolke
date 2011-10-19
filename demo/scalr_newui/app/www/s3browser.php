<? 
	require("src/prepend.inc.php"); 
	$display['load_extjs'] = true;
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("index.php");
	}

	$display["title"] = _("Amazon S3 and CloudFront manager");
  
	if ($req_action)
	{

	    // Create Amazon s3 client object
	    $AmazonS3 = new AmazonS3(
	    	Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY), 
	    	Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY)
	    );
        $AmazonCloudFront = new AmazonCloudFront(
        	Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY), 
	    	Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY)
        );
    
	    if ($req_action == 'delete_backet')
	    {
    		try
    		{       
	    		$res = $AmazonS3->DeleteBucket($req_name);
	    		  
	    		if ($res)
	    		{
	    			$okmsg = _("Bucket successfully removed");
	    			UI::Redirect("s3browser.php");
	    		}
	    		else
	    			$errmsg = _("Cannot delete specified bucket");
    		}
    		catch(Exception $e)
    		{
    			$errmsg = sprintf(_("Cannot delete specified bucket. %s"), $e->getMessage());
    		}
	    }
	    
	    if ($req_action == 'disable_dist' || $req_action == 'enable_dist')
	    {
    		try
    		{
	    		$info = $AmazonCloudFront->GetDistributionConfig($req_id);
	    		
	    		$DistributionConfig = new DistributionConfig();
				$DistributionConfig->CallerReference 	= $info['CallerReference'];
				$DistributionConfig->CNAME 				= $info['CNAME'];
				$DistributionConfig->Comment 			= $info['Comment'];
				$DistributionConfig->Enabled 			= ($req_action == 'disable_dist') ? false : true;
				$DistributionConfig->Origin 			= $info['Origin'];
				
				$E_TAG = $AmazonCloudFront->SetDistributionConfig($req_id, $DistributionConfig, $info['Etag']);
    		}
    		catch(Exception $e)
    		{
    			$errmsg = $e->getMessage();
    		}
			
    		if (!$errmsg)
    		{
    			$okmsg = sprintf(_("Distribution successfully updated"));
    			UI::Redirect("s3browser.php");
    		}
	    }
	        
	    if ($req_action == 'delete_dist')
	    {  	    	
    		try
    		{                  
    			$result = $AmazonCloudFront->DeleteDistribution($req_id);
    			
    			$info = $db->GetRow("SELECT * FROM distributions WHERE cfid=?", array($req_id));
    			
    			if ($info)
    			{
	    			$db->Execute("DELETE FROM distributions WHERE cfid=?", array($req_id));
	    			
	    			// Remove CNAME from DNS zone
	    			$zoneinfo = $db->GetRow("SELECT * FROM zones WHERE zone=? AND clientid=?",
	    				array($info['zone'], Scalr_Session::getInstance()->getClientId())
	    			);
	    			
	    			if ($zoneinfo)
	    			{
	    				$db->Execute("DELETE FROM records WHERE 
	    					zoneid	= ? AND
	    					rtype	= ? AND
	    					rvalue	= ? AND
	    					rkey	= ?
	    				", array($zoneinfo['id'], 'CNAME', "{$info['cname']}", "{$info['cfurl']}."));
	    				
	    				$db->Execute("UPDATE zones SET isobsoleted='1' WHERE id=?", array($zoneinfo['id']));
	    			}
    			}
    			
    			$okmsg = _("Distribution successfully removed");
    			UI::Redirect("s3browser.php");
    		}
    		catch(Exception $e)
    		{
    			$errmsg = sprintf(_("Cannot remove distribution: %s"), $e->getMessage());
    		}
	    }
	    
	    if ($req_action == 'create_dist')
	    {    	
    		if (isset($post_cancel))
    			UI::Redirect("s3browser.php");
			
    		if ($post_confirm)
    		{ 	    			
    			if($post_remotedomainname) // if customer uses self created domain name
    			{
    				$post_zone = $post_remotedomainname;
    				$post_domainname = '';
    			}
    			
	    		$DistributionConfig = new DistributionConfig();	    	
	    		if($post_remotedomainname)
	    			$DistributionConfig->CNAME = "{$post_zone}";
	    		else
	    			$DistributionConfig->CNAME = "{$post_domainname}.{$post_zone}";
	    			 	
	    		$DistributionConfig->Comment 			= $post_comment;
	    		$DistributionConfig->Enabled 			= true;
	    		$DistributionConfig->CallerReference 	= date("YmdHis");
	    		$DistributionConfig->Origin 			= "{$req_bucket_name}.s3.amazonaws.com";
		    
	    		try
	    		{ 	    		   			
	    			$result = $AmazonCloudFront->CreateDistribution($DistributionConfig);
	    		   			
	    			$db->Execute("INSERT INTO distributions SET
	    				cfid	= ?,
	    				cfurl	= ?,
	    				cname	= ?,
	    				zone	= ?,
	    				bucket	= ?,
	    				clientid= ?
	    			", 
	    				array($result['ID'], 
	    				$result['DomainName'], 
	    				$post_domainname, 
	    				$post_zone, 
	    				$req_bucket_name, 
	    				Scalr_Session::getInstance()->getClientId()
	    				)
	    			);
	    			
	    			// Add CNAME to zone
	    			$zoneinfo = $db->GetRow("SELECT * FROM zones WHERE zone=? AND clientid=?", 
	    				array(
	    				$post_zone,
	    				Scalr_Session::getInstance()->getClientId()
	    			));
	    			
	    			if ($zoneinfo && $post_domainname)
	    			{
	    				$db->Execute("INSERT INTO records SET 
	    					zoneid	= ?,
	    					rtype	= ?,
	    					ttl		= ?,
	    					rkey	= ?,
	    					rvalue	= ?,
	    					issystem= ?
	    				", array($zoneinfo['id'], 'CNAME', 14400, "{$post_domainname}", "{$result['DomainName']}.", 0));
	    				
	    				$db->Execute("UPDATE zones SET isobsoleted='1' WHERE id=?", array($zoneinfo['id']));
	    			}   
		    
	    			$okmsg = _("Distribution successfully created");
	    			UI::Redirect("s3browser.php");
	    		}
	    		catch(Exception $e)
	    		{
	    			$errmsg = sprintf(_("Cannot create new distribution: %s"), $e->getMessage());
	    		}
    		}
    		
    		if (!$post_confirm || $errmsg)
    		{
    			$display['errmsg'] = $errmsg;
    			$display['bucket_name'] = $req_name;
    			$display['zones'] = $db->GetAll("SELECT * FROM dns_zones WHERE status!=? AND env_id=?", 
    				array(DNS_ZONE_STATUS::PENDING_DELETE,
    				Scalr_Session::getInstance()->getEnvironmentId())
    			);
    			
    			$Smarty->assign($display);
    			$Smarty->display("create_distribution.tpl");
    			exit();
    		}
	    }
	}
	
	require("src/append.inc.php");
?>