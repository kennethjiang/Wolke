<?php
	$response = array();
	
	// AJAX_REQUEST;
	$context = 6;
	
	try
	{
		$enable_json = true;
		include("../../src/prepend.inc.php");
	
		if (isset($req_show_all))
		{
			if ($req_show_all == 'true')
				$_SESSION['sg_show_all'] = true;
			else
				$_SESSION['sg_show_all'] = false;
		}
		
		$ls = PlatformFactory::NewPlatform($req_platform)->getLocations();		
		if (!$req_location || !$ls[$req_location])
			$location = array_shift(array_keys($ls));
		else
			$location = $req_location;
		
		switch($req_platform)
		{
			case SERVER_PLATFORMS::EC2:
				
				$platformClient = Scalr_Service_Cloud_Aws::newEc2(
					$location,
					Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
					Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
				);
				
				break;
				
			case SERVER_PLATFORMS::EUCALYPTUS:
				
				$platformClient = Scalr_Service_Cloud_Eucalyptus::newCloud(
					Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Eucalyptus::SECRET_KEY, true, $location),
					Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Eucalyptus::ACCESS_KEY, true, $location),
					Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Eucalyptus::EC2_URL, true, $location)
				);
				
				break;
		}
		
	
		// Rows
		$aws_response = $platformClient->DescribeSecurityGroups();
		
		$rows = $aws_response->securityGroupInfo->item;
		foreach ($rows as $row)
		{
			if ($req_query)
			{
				if (!stristr($row->groupName, $req_query))
					continue;
			}
			
			// Show only scalr security groups
			if (stristr($row->groupName, CONFIG::$SECGROUP_PREFIX) || stristr($row->groupName, "scalr-role.") || $_SESSION['sg_show_all'])
				$rowz[] = $row;
		}
		
		if ($rowz instanceof stdClass)
			$rowz = array($rowz);
			
		$start = $req_start ? (int) $req_start : 0;
		$limit = $req_limit ? (int) $req_limit : 20;
		
		$response['total'] = count($rowz);
		
		$rowz = (count($rowz) > $limit) ? array_slice($rowz, $start, $limit) : $rowz;
		
		$response["data"] = array();
		
		if ($req_sort)
		{
			$nrowz = array();
			foreach ($rowz as $row)
			{
				$nrowz[$row->groupName] = $row;
			}
			
			ksort($nrowz);
			
			if ($req_dir == 'DESC')
				$rowz = array_reverse($nrowz);
			else
				$rowz = $nrowz;
		}
		
		// Rows
		foreach ($rowz as $row)
		{
		    $response["data"][] = array(
		    	"name"			=> $row->groupName,
		    	"description"	=> $row->groupDescription,
		    	"id"			=> $row->groupName
		    );
		}
	}
	catch(Exception $e)
	{
		$response = array("error" => $e->getMessage(), "data" => array());
	}
	
	print json_encode($response);
?>