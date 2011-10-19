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
		
		$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
			$_SESSION['aws_region'], 
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
	
		if($req_query)
		{	
			
			//---------------------------- filter for SpotPriceHistory:
			$filter = array(); 
			$filter = explode(" ", $req_query);
	        
			$instanceTypes = array( X86_64_TYPE::C1_XLARGE,			
									X86_64_TYPE::M1_LARGE,
									X86_64_TYPE::M1_XLARGE,
									X86_64_TYPE::M2_2XLARGE,
									X86_64_TYPE::M2_4XLARGE,
									I386_TYPE::C1_MEDIUM,
									I386_TYPE::M1_SMALL
								);
			
	        
	        $i = 0;
	        $instanceTypeSet = array();
	        $requestDesription = array();	     
	         
	        foreach($filter as $item) // search instance type conjunctions
	        {
	        	
	        	for($i = 0; $i<count($instanceTypes);$i++)
	        	{ 
	        		 // serach by instance type
	        		if(strcasecmp($instanceTypes[$i],$item) == 0)
	        		{
	        			$instanceTypeSet[] = $instanceTypes[$i];
	        			continue;
	        		}
	        	}
	        		// serach by description
	        		$win   = stristr($item,"windows");
	        		$linux = stristr($item,"linux");
	        		$unix  = stristr($item,"unix");
	        		if($win)
	        		{
	        			$requestDesription[] = "Windows";
	        			continue;	
	        		}
	        		elseif(($linux) || ($unix))
	        		{
	        			$requestDesription[] = "Linux/UNIX";
	        			continue;
	        		}	        	     	
	        }	           
	        //---------------------------- end filter ------------------------			
			$describeSpotPriceType = new DescribeSpotPriceHistoryType(null,$instanceTypeSet,$requestDesription,null);
			$aws_response = $AmazonEC2Client->DescribeSpotPriceHistory($describeSpotPriceType);			
		}
        else
			$aws_response = $AmazonEC2Client->DescribeSpotPriceHistory(); // show all prices
		  
			
		// Rows	
		$rows = (array)$aws_response->spotPriceHistorySet;
		
		if ($rows["item"] instanceof stdClass)
			$rows["item"] = array($rows["item"]); // convert along subnet record to array
		
		$rowz = array();		
		
		foreach ($rows['item'] as $row)					
				$rowz[]=(array)$row;	
				
		if ($req_sort == 'price' )
		{
			$nrowz = array();
			foreach ($rowz as $row)				
				$nrowz[(string)$row['spotPrice']] = $row;			
					
			ksort($nrowz);
			
			if ($req_dir == 'ASC')
				$rowz = array_reverse($nrowz);
			else
				$rowz = $nrowz;	
		}		
		
		if ($req_sort == 'timestamp')
		{
			$nrowz = array();
			foreach ($rowz as $row)				
				$nrowz[(string)$row['timestamp']] = $row;			
					
			ksort($nrowz);
			
			if ($req_dir == 'ASC')
				$rowz = array_reverse($nrowz);
			else
				$rowz = $nrowz;	
		}	
		
		// diplay list limits
		$start = $req_start ? (int) $req_start : 0;
		$limit = $req_limit ? (int) $req_limit : 20;
		
		$response['total'] = count($rowz);	
		$rowz = (count($rowz) > $limit) ? array_slice($rowz, $start, $limit) : $rowz;
		
		// descending sorting of requested result
		$response["data"] = array();	
				
		// Rows. Create final rows array for script
		foreach ($rowz as $row)
		{ 	
			$response["data"][] = array(
					"type"				=> (string)$row['instanceType'], // have to call only like "id" for correct script work in template
					"description"		=> (string)$row['productDescription'],
					"price"			=> (string)$row['spotPrice'],					
					"timestamp"			=> (string)$row['timestamp']
					);				
		}
	 
	}
	catch(Exception $e)
	{
		$response = array("error" => $e->getMessage(), "data" => array());
	}

	print json_encode($response);
?>