<?php
	$response = array();
	
	// AJAX_REQUEST;
	$context = 6;
	
	try
	{
		$enable_json = true;
		include("../../src/prepend.inc.php");
	
		Scalr_Session::getInstance()->getAuthToken()->hasAccessEx(Scalr_AuthToken::ACCOUNT_USER);
		
		$AmazonELBClient = Scalr_Service_Cloud_Aws::newElb(
			$_SESSION['aws_region'], 
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY), 
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY)
		);
		
		// Rows
		$aws_response = $AmazonELBClient->DescribeLoadBalancers();

		$lb = (array)$aws_response->DescribeLoadBalancersResult->LoadBalancerDescriptions;
		
		$rowz = $lb['member'];
				
		if (!is_array($rowz))
			$rowz = array($rowz);
			
		foreach ($rowz as $pk=>$pv)
		{
			if (!((string)$pv->DNSName))
				continue;
			
			$roleid = $db->GetOne("SELECT farm_roleid FROM farm_role_settings WHERE name=? AND value=?", 
				array(DBFarmRole::SETTING_BALANCING_HOSTNAME, (string)$pv->DNSName)
			);
			
			if ($roleid)
			{
				try
				{
					$DBFarmRole = DBFarmRole::LoadByID($roleid);
					$farmid = $DBFarmRole->FarmID;
					$farm_name = $db->GetOne("SELECT name FROM farms WHERE id=?", array($DBFarmRole->FarmID));
					$role_name = $DBFarmRole->GetRoleObject()->name;
				}
				catch(Exception $e)
				{
					$farmid = false;
					$farm_name = false;
					$role_name = false;		
				}
			}
			else
			{
				$farmid = false;
				$farm_name = false;
				$role_name = false;
			}
			
			$rowz1[] = array(
				"name"		=> (string)$pv->LoadBalancerName,
				"dtcreated"	=> date("d M d H:i:s", strtotime($pv->CreatedTime)),
				"dnsname"	=> (string)$pv->DNSName,
				"farmid"	=> $farmid,
				"farm_name"	=> $farm_name,
				"role_name"	=> $role_name
			);
		}
		
		$response["total"] = count($rowz1);
		
		$start = $req_start ? (int) $req_start : 0;
		$limit = $req_limit ? (int) $req_limit : 20;

		if ($response['total'] && $start > $response['total'])
			$start = floor($response['total'] / $limit) * $limit;

		$rowz = (count($rowz1) > $limit) ? array_slice($rowz1, $start, $limit) : $rowz1;
		
		$response["data"] = array();
		
		
		// Rows
		foreach ($rowz as $r)
		{
		    $response["data"][] = $r;
		}
	}
	catch(Exception $e)
	{
		$response = array("error" => $e->getMessage(), "data" => array());
	}
	
	print json_encode($response);
?>