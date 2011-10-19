<?php
	$response = array();

	// AJAX_REQUEST;
	$context = 6;
	
	try
	{
		$enable_json = true;
		include("../../src/prepend.inc.php");
	
		$req_show_all = true;
		
		if (isset($req_show_all))
		{
			if ($req_show_all == 'true')
				$_SESSION['sg_show_all'] = true;
			else
				$_SESSION['sg_show_all'] = false;
		}
		
		$AmazonRDSClient = Scalr_Service_Cloud_Aws::newRds( 
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::ACCESS_KEY),
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::SECRET_KEY),
			$_SESSION['aws_region']
		);
		
		// Rows		
		$aws_response = $AmazonRDSClient->DescribeDBSecurityGroups();		

		$rows = $aws_response->DescribeDBSecurityGroupsResult->DBSecurityGroups->DBSecurityGroup;

		foreach ($rows as $row)
		{
			if ($req_query)
			{
				if (!stristr($row->DBSecurityGroupName, $req_query))
					continue;
			}
			
			// Show only scalr security groups
			if (stristr($row->DBSecurityGroupName, CONFIG::$SECGROUP_PREFIX) || $_SESSION['sg_show_all'])
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
				$nrowz[(string)$row->DBSecurityGroupName] = $row;
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
		    	"name"			=> (string)$row->DBSecurityGroupName,
		    	"description"	=> (string)$row->DBSecurityGroupDescription,
		    	"id"			=> (string)$row->DBSecurityGroupName
		    );
		}
	}
	catch(Exception $e)
	{
		$response = array("error" => $e->getMessage(), "data" => array());
	}
	
	print json_encode($response);
?>