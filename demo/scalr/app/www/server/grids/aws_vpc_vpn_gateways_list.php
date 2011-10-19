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
		$aws_response = $AmazonVPCClient->DescribeVpnGateways();
		
		$rows = (array)$aws_response->vpnGatewaySet;		
			
		if ($rows["item"] instanceof stdClass)
			$rows["item"] = array($rows["item"]); // convert along  record to array
		
		$rowz = array();		
		
		foreach ($rows['item'] as $row)					
				$rowz[] = (array)$row;			

		// if VPN gateway atteched to VPC we need to process 'attachments' field		
		for($i = 0; $i<count($rowz); $i++)
		{	
			;					
			if ($rowz[$i]["attachments"]->item instanceof stdClass) // if recieve one element
			{			
				$rowz[$i]["attachments"]->item = array($rowz[$i]["attachments"]->item); // convert along  record to array
			}
			foreach ($rowz[$i]["attachments"]->item as &$item)
			{		
				$item = (array)$item; 
			}			
			$rowz[$i]["attachments"] = $rowz[$i]["attachments"]->item; // 'item' to array			
		}
	
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
				$nrowz[(string)$row['vpnGatewayId']] = $row;			
					
			ksort($nrowz);
			
			if ($req_dir == 'DESC')
				$rowz = array_reverse($nrowz);
			else
				$rowz = $nrowz;	
		}
		
		// Rows. Create final rows array for script
		$isAttached  = false; // true if one vpn has 'attached' state  
		foreach ($rowz as $row) 
		{ 	
			$dataitem = array(
				"vpn_id"			=> (string)$row['vpnGatewayId'], // have to call only like "id" for correct script work in template
				"type"				=> (string)$row['type'],
				"state"				=> (string)$row['state'],					
				"availabilityZone"	=> (string)$row['availabilityZone']
			);
			
			// if there is an attached VPN to VPC scrip will show it, else - will not show.			
			
			$isAttached = false; 
			
			for($i = 0; $i < count($row['attachments']); $i++)
			{					
				// looking for attached VPN (it's just one attached gateway by  amazon specification )
				if( ($row['attachments'][$i]['state'] === 'attached') ||
					($row['attachments'][$i]['state'] === 'attaching') ) 
				{		
					$dataitem["vpcId"] 			 = $row['attachments'][$i]['vpcId'];
					$dataitem["attachmentState"] = $row['attachments'][$i]['state'];
					$isAttached = true;			// variable is true  if attached vpn was found
				}						
			}
			
			if($isAttached == false)
			{
				$dataitem["vpcId"] = null;
				$dataitem["attachmentState"] =  null;
			}
			
			$response["data"][] = $dataitem;			
		}	
	}
	catch(Exception $e)
	{
		$response = array("error" => $e->getMessage(), "data" => array());
	}

	print json_encode($response);
?>