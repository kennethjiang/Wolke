<?php
	$response = array();
	
	// AJAX_REQUEST;
	$context = 6;
	
	try
	{
		$enable_json = true;
		include("../../src/prepend.inc.php");
	
		Scalr_Session::getInstance()->getAuthToken()->hasAccessEx(Scalr_AuthToken::ACCOUNT_USER);
		
		$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
			$_SESSION['aws_region'], 
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY), 
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		// Rows
		$aws_response = $AmazonEC2Client->DescribeAddresses();
		$rowz = $aws_response->addressesSet->item;
		$rowz1 = array();

		if ($rowz instanceof stdClass)
			$rowz = array($rowz);
		
		foreach ($rowz as $pk=>$pv)
		{
			$item = array(
				'ipaddress' => $pv->publicIp,
		    	'instance_id' => $pv->instanceId
				/*
		    	'farmid' => $r->farmId, 
		    	'farm_name' => $r->farmName, 
		    	'role_name' => ($r->dbInstance && $r->dbInstance['role_name']) ? $r->dbInstance['role_name'] : $r->dbInfo['role_name'],
		    	'indb' => ($r->dbInfo) ? true : false
		    	*/
			);
			
			$info = $db->GetRow("SELECT * FROM elastic_ips WHERE ipaddress=?", array($pv->publicIp));
			if ($info)
			{
				$item['farm_id'] = $info['farmid'];
				$item['farm_roleid'] = $info['farm_roleid'];
				$item['server_id'] = $info['server_id'];
				$item['indb'] = true;
				$item['server_index'] = $info['instance_index'];
				
				//WORKAROUND: EIPS not imported correclty from 1.2 to 2.0
				if (!$item['server_id'] && $info['state'] == 1)
				{
					try
					{
						
						$DBServer = DBServer::LoadByPropertyValue(EC2_SERVER_PROPERTIES::INSTANCE_ID, $item['instance_id']);
						$item['server_id'] = $DBServer->serverId;
					}
					catch(Exception $e){}
				}
				
				if ($item['farm_roleid'])
				{
					try
					{
						$DBFarmRole = DBFarmRole::LoadByID($item['farm_roleid']);
						$item['role_name'] = $DBFarmRole->GetRoleObject()->name;
						$item['farm_name'] = $DBFarmRole->GetFarmObject()->Name;
					}
					catch(Exception $e){}
				}
			}
			else
			{
				try
				{
					$DBServer = DBServer::LoadByPropertyValue(EC2_SERVER_PROPERTIES::INSTANCE_ID, $pv->instanceId);
					$item['server_id'] = $DBServer->serverId;
					$item['farm_id'] = $DBServer->farmId;
				}
				catch(Exception $e){}
			}
			
			$doadd = true;
			
			// Filter by farm id
			if ($req_farmid)
			{
				if ($item['farm_id'] != $req_farmid)
					$doadd = false;
			}
			
			if ($doadd)
				$rowz1[] = $item;
		}
		
		$response["total"] = count($rowz1);
		
		$start = $req_start ? (int) $req_start : 0;
		$limit = $req_limit ? (int) $req_limit : 20;

		if ($response['total'] && $start > $response['total'])
			$start = floor($response['total'] / $limit) * $limit;

		$rowz = (count($rowz1) > $limit) ? array_slice($rowz1, $start, $limit) : $rowz1;
		
		$response["data"] = $rowz;
	}
	catch(Exception $e)
	{
		$response = array("error" => $e->getMessage(), "data" => array());
	}
	print json_encode($response);
?>