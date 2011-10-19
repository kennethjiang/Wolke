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
	
		switch($req_ownerFilter)
		{
			case 	'amazon': 	$owner = array('amazon'); 				break;
			case	'all': 		$owner = null; 							break;
			case	'my': 		$owner = array(Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCOUNT_ID)); 	break;
		}
		
		// describe amis
		$aws_response = $AmazonEC2Client->DescribeImages(new DescribeImagesType(null,null,$owner));

		// Rows	
		$rows = (array)$aws_response->imagesSet;
		
		if ($rows["item"] instanceof stdClass)
			$rows["item"] = array($rows["item"]); // convert along subnet record to array
		
		$rowz = array();

		foreach ($rows['item'] as $row)	
		{			
			if(strpos((string)$row->imageId,"mi")) // select only amies ("mi" to return 1, not 0 as ami)
			{
				if($req_query)
				{
					 // convert element to string and look for an filter parameter	
					$str = implode(" ",(array)$row);
					
					if(strpos($str,$req_query))
						$rowz[]=(array)$row;
				}
				else
					// no filtration 
					$rowz[]=(array)$row;
			}
		}

		// display list limits
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
				$nrowz[(string)$row['spotPrice']] = $row;

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
				"imageId"			=> (string)$row['imageId'], // have to call only like "id" for correct script work in template
				"imageState"		=> (string)$row['imageState'],
				"imageOwnerId"		=> (string)$row['imageOwnerId'],
				"architecture"		=> (string)$row['architecture'],
				"imageType"			=> (string)$row['imageType'],
				"rootDeviceType"	=> (string)$row['rootDeviceType']
			);				
		} 
		
		$response["types_i386"]   = array(I386_TYPE::M1_SMALL,I386_TYPE::C1_MEDIUM);
		$response["types_x86_64"] = array(X86_64_TYPE::M1_LARGE, X86_64_TYPE::M1_XLARGE, X86_64_TYPE::C1_XLARGE,X86_64_TYPE::M2_2XLARGE,X86_64_TYPE::M2_4XLARGE );

	}
	catch(Exception $e)
	{
		$response = array("error" => $e->getMessage(), "data" => array());
	}

	print json_encode($response);
?>