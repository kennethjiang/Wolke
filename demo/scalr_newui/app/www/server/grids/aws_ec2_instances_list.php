<?php
	$response = array();
	// AJAX_REQUEST;
	$context = 6;

	try
	{
		$enable_json = true;
		include("../../src/prepend.inc.php");		
		
		$req_show_all = true;	//
		
		if (isset($req_show_all))
		{
			if ($req_show_all == 'true')
				$_SESSION['sg_show_all'] = true;
			else
				$_SESSION['sg_show_all'] = false;
		}
		
		$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
			$_SESSION['aws_region'], 
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		$aws_response = $AmazonEC2Client->DescribeInstances(); // show all instances
				
		// Rows	
		$rows = (array)$aws_response->reservationSet;		
			
		if ($rows["item"] instanceof stdClass)
			$rows["item"] = array($rows["item"]); // convert along subnet record to array
		
		$rowz = array();		
		
		foreach ($rows['item'] as $row)	  // as don't need additional inforamation, we just get only usefull one.
		{				
			$temp = (array)$row->instancesSet;
			$rowz[]= (array)$temp['item'];      
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
				$nrowz[(string)$row['createTime']] = $row;			
					
			ksort($nrowz);
			
			if ($req_dir == 'DESC')
				$rowz = array_reverse($nrowz);
			else
				$rowz = $nrowz;	
		}
		
		// Rows. Create final rows array for script
		foreach ($rowz as $row)
		{ 	
					
			if($row['instanceLifecycle'] == 'spot')
			$response["data"][] = array(
					"iid"				=> (string)$row['instanceId'], // have to call only like "id" for correct script work in template
					"imageId"			=> (string)$row['imageId'],
					"instanceState"		=> (string)$row['instanceState']->name,					
					"dnsName"			=> (string)$row['dnsName'],
					"keyName"			=> (string)$row['keyName'],
					"instanceType"	 	=> (string)$row['instanceType'],			
					"availabilityZone"	=> (string)$row['placement']->availabilityZone,					
					"monState"			=> (string)$row['monitoring']->state,
					"instanceLifecycle" => (string)$row['instanceLifecycle'],
					"launchTime" 		=> (string)$row['launchTime']									
					);	
			
		} 		

	}
	catch(Exception $e)
	{
		$response = array("error" => $e->getMessage(), "data" => array());
	}

	print json_encode($response);
?>