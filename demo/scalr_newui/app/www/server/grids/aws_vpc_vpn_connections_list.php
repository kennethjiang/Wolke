<?php
	$response = array();

	// AJAX_REQUEST;
	$context = 6;
	
	try
	{
		$enable_json = true;
		include("../../src/prepend.inc.php");		
		
		$req_show_all = true;	// �� ���� �������� $req_show_all?
		
		if (isset($req_show_all))
		{
			if ($req_show_all == 'true')
				$_SESSION['sg_show_all'] = true;
			else
				$_SESSION['sg_show_all'] = false;
		}
		
		$AmazonVPCClient = Scalr_Service_Cloud_Aws::newVpc(
			$_SESSION['aws_region'], 
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);	
		
		// Rows	
		$aws_response = $AmazonVPCClient->DescribeVpnConnections();
		$rows = (array)$aws_response->vpnConnectionSet;				
			
		if ($rows["item"] instanceof stdClass)
			$rows["item"] = array($rows["item"]); // convert along  record to array
		
		$rowz = array();		
		
		foreach ($rows['item'] as $row)					
				$rowz[]=(array)$row;	

		
		// diplay list limits
		$start = $req_start ? (int) $req_start : 0;
		$limit = $req_limit ? (int) $req_limit : 20;
		
		$response['total'] = count($rowz);	
		$rowz = (count($rowz) > $limit) ? array_slice($rowz, $start, $limit) : $rowz;
		
		// descending sorting of requested result
		$response["data"] = array();	
		
		if ($req_sort)
		{
			$nrowz = array();
			foreach ($rowz as $row)				
				$nrowz[(string)$row['vpnConnectionId']] = $row;			
					
			ksort($nrowz);
			
			if ($req_dir == 'DESC')
				$rowz = array_reverse($nrowz);
			else
				$rowz = $nrowz;	
		}		
		
		// Rows. Create final rows array for script
		foreach ($rowz as $row)
		{ 	
			$response["data"][] = array(
					"conn_id"			=> (string)$row['vpnConnectionId'], // have to call only like "id" for correct script work in template
					"type"				=> (string)$row['type'],
					"state"				=> (string)$row['state'],
					"vpnGatewayId"		=> (string)$row['vpnGatewayId'],										
					"customerGatewayId"	=> (string)$row['customerGatewayId']
					);				
		}	
	
	}
	catch(Exception $e)
	{
		$response = array("error" => $e->getMessage(), "data" => array());
	}

	print json_encode($response);
?>