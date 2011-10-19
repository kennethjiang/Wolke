<?
    require("../src/prepend.inc.php");
    
    switch($_REQUEST["_cmd"])
    {            
    	case "purchaseReservedOffering":
    
	    	try
	    	{
    			$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
					$req_region,
					Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
					Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
				);
			
				$response = $AmazonEC2Client->PurchaseReservedInstancesOffering($req_offeringID);
				exit();
	    	}
	    	catch(Exception $e)
	    	{
	    		die(sprintf(_("Cannot purchase reserved instances offering: %s"), $e->getMessage()));
	    	}
    		
    		break;
    	
    	case "get_script_args":
    		
    		$scriptid = (int)$req_scriptid;
    		
    		$dbversions = $db->GetAll("SELECT * FROM script_revisions WHERE scriptid=? AND approval_state=? ORDER BY revision DESC", 
	        	array($scriptid, APPROVAL_STATE::APPROVED)
	        );
    		
    		$versions = array();
	        foreach ($dbversions as $version)
	        {
	        	$text = preg_replace('/(\\\%)/si', '$$scalr$$', $version["script"]);
	        	preg_match_all("/\%([^\%\s]+)\%/si", $text, $matches);
	        	$vars = $matches[1];
			    $data = array();
			    foreach ($vars as $var)
			    {
			    	if (!in_array($var, array_keys(CONFIG::getScriptingBuiltinVariables())))
			    		$data[$var] = ucwords(str_replace("_", " ", $var));
			    }
			    $data = json_encode($data);
	        	
	        	$versions[] = array("revision" => $version['revision'], "fields" => $data);
	        }
    		
	        print json_encode($versions);
	        exit();
	        
    		break;
    	
    	case "check_role_name":
    		
    		$role_name = $req_name;
    		$image_id = $req_ami_id;
    		
    		if (!preg_match("/^[A-Za-z0-9-]+$/", $role_name))
            	die(_("Allowed chars for role name is [A-Za-z0-9-]"));
    		
            $role_info = $db->GetRow("SELECT * FROM roles WHERE id = (SELECT role_id FROM role_images WHERE image_id=?) AND env_id=? AND origin=?",
            	array($image_id, Scalr_Session::getInstance()->getEnvironmentId(), ROLE_TYPE::CUSTOM)
            );
            if (!$role_info)
            	die("REDIRECT");
            	
            if ($role_info['name'] == $role_name)
            	die("ok");
            	
            $chk = $db->GetOne("SELECT id FROM roles WHERE name=? AND ami_id != ? AND region=?", 
            	array($role_name, $ami_id, $role_info['region'])
            );
            if (!$chk)
            	die("ok");
            else
            	die(_("Name is already used by an existing role. Please choose another name."));
            	
    		break;
    	
    	case "get_script_props":
    		
    		$script_id = (int)$req_id;
    		$version = (int)$req_version;
    		
    		$script_info = $db->GetRow("SELECT * FROM scripts WHERE id=?", 
    			array($script_id)
    		);
    		
    		$script_info['revision'] = $version;
    		$script_info['script'] = $db->GetOne("SELECT script FROM script_revisions WHERE scriptid=? AND revision=?",
    			array($script_id, $version)
    		);
    		
    		if (Scalr_Session::getInstance()->getClientId() != 0)
    		{
    			if ($script_info['origin'] != SCRIPT_ORIGIN_TYPE::SHARED && $script_info['clientid'] != Scalr_Session::getInstance()->getClientId())
    				die();
    		}
    		
    		print json_encode($script_info);
    		exit();
    		
    		break;
    	
    	case "get_role_params":
    		
    		$farmid = (int)$req_farmid;
    		
    		try
    		{
    			$DBRole = DBRole::loadById($req_role_id);
    			if (!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($DBRole->envId))
    				throw new Exception("");
    		}
    		catch(Exception $e)
    		{
    			die(_("There are no parameters for this role"));
    		}
    		
    		$params = $db->GetAll("SELECT * FROM role_parameters WHERE role_id=? AND hash NOT IN('apache_http_vhost_template','apache_https_vhost_template')", 
    			array($DBRole->id)
    		);
    		if (count($params) > 0)
    		{
    			$DataForm = new DataForm();
    			foreach ($params as $param)
    			{
					// Prepare options array 
    				if ($param['options'])
    				{
	    				$options = json_decode($param['options'], true);
	    				$fopts = array();
	    				foreach ($options as $option)
	    					$fopts[$option[0]] = $option[1];
    				}
					
    				$value = false;
    				
    				try
    				{
    					$DBFarmRole = DBFarmRole::Load($farmid, $req_role_id);
    					
    					$value = $db->GetOne("SELECT value FROM farm_role_options WHERE farm_roleid=? AND name=?",
	    					array($DBFarmRole->ID, $param['name'])
	    				);
    				}
    				catch(Exception $e){}
    				
    				// Get field value
    				
    				if ($value === false || $value === null)
    					$value = $param['defval'];
    				
    				$field = new DataFormField(
    					$param['name'],
    					$param['type'],
    					$param['name'], 
    					$param['isrequired'], 
    					$fopts, 
    					$param['defval'], 
    					$value,
    					null,
    					$param['allow_multiple_choice']
    				);
    				
    				$DataForm->AppendField($field);
    			}
    			
    			$fields = $DataForm->ListFields();
    			
    			if (count($fields) != 0)
    			{
    				$Smarty->assign(array("DataForm" => $DataForm, "elem_id"=> "role_params", "field_prefix" => "", "field_suffix" => ""));
    				print $Smarty->fetch("inc/dynamicform.tpl");
    			}
    			else
    				die(_("There are no parameters for this role"));
    		}
    		else
    			die(_("There are no parameters for this role"));
    		
    		exit();
    		
    		break;
    	
    	case "get_script_template_source":
    		
    		$id = (int)$req_scriptid;
    		$version = $req_version;
    		
			$templateinfo = $db->GetRow("SELECT * FROM scripts WHERE id=?", array($id));
    		if (Scalr_Session::getInstance()->getClientId() != 0)
    		{
    			if ($templateinfo['origin'] == SCRIPT_ORIGIN_TYPE::CUSTOM && $templateinfo['clientid'] != Scalr_Session::getInstance()->getClientId())
    				die(_('There is no source avaiable for selected script'));
    		}
    		
    		$sql = "SELECT * FROM script_revisions WHERE scriptid='{$id}'";
    		
    		if ($version == "latest")
    			$sql .= " AND revision=(SELECT MAX(revision) FROM script_revisions WHERE scriptid='{$id}' AND approval_state='".APPROVAL_STATE::APPROVED."')";
    		else
    		{
    			$version = (int)$version;
    			$sql .= " AND revision='{$version}'";
    		}
    		
    		$script = $db->GetRow($sql);
    		
    		if ($templateinfo['origin'] == SCRIPT_ORIGIN_TYPE::USER_CONTRIBUTED)
    		{
    			if ($templateinfo['clientid'] != Scalr_Session::getInstance()->getClientId() && $script['approval_state'] != APPROVAL_STATE::APPROVED)
    				die(_('There is no source avaiable for selected script'));
    		}
    		
    		if ($script)
    		{
    			print $script['script'];
    		}
    		else
    			print _('There is no source avaiable for selected script');
    			
    		exit();
    		
    		break;
    }
    
    exit();
?>