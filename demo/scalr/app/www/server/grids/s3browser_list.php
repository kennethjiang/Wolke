<?php

	$response = array();	
	// AJAX_REQUEST;
	$context = 6;
     
   
	try
	{
		$enable_json = true;
		include("../../src/prepend.inc.php");	
		
		Scalr_Session::getInstance()->getAuthToken()->hasAccessEx(Scalr_AuthToken::ACCOUNT_USER);  	
		
	    
	    // Create Amazon s3 client object
	    $AmazonS3 = new AmazonS3(
	    	Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY), 
	    	Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY)
	    );
        $AmazonCloudFront = new AmazonCloudFront(
        	Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY), 
	    	Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY)
        );

    	//Create cloundfront object
    	$distributions = $AmazonCloudFront->ListDistributions(); 	    	

		// Get list of all user buckets   
	    $buckets = $AmazonS3->ListBuckets();
	          	    
	    ksort($buckets);
	    
	    $start = $req_start ? (int) $req_start : 0;
		$limit = $req_limit ? (int) $req_limit : 20;
		
		$response['total'] = count($buckets);
		
		$buckets = (count($buckets) > $limit) ? array_slice($buckets, $start, $limit) : $buckets;
			    
	    foreach ($buckets as $bucket)
	    {
			if (!$distributions[$bucket->Name])
			{       
				if($req_query) 
				{
					if(stristr($bucket->Name, $req_query))
					{
						$response["data"][] = array(
							"name" => $bucket->Name 
						);
					}
				}  
				else
				{
	    			$response["data"][] = array(
						"name" => $bucket->Name 
					);
				}
			}
			else
			{     
				if($req_query)
				{
					if(stristr($bucket->Name, $req_query))
					{
						$response["data"][] = array(
							"name" 	=> $bucket->Name,
							"cfid"	=> $distributions[$bucket->Name]['ID'],
							"cfurl"	=> $distributions[$bucket->Name]['DomainName'],
							"cname"	=> $distributions[$bucket->Name]['CNAME'],
							"status"=> $distributions[$bucket->Name]['Status'],
							"enabled"=> $distributions[$bucket->Name]['Enabled']
						);
					}
				}
				else
				{
					$response["data"][] = array(
						"name" 	=> $bucket->Name,
						"cfid"	=> $distributions[$bucket->Name]['ID'],
						"cfurl"	=> $distributions[$bucket->Name]['DomainName'],
						"cname"	=> $distributions[$bucket->Name]['CNAME'],
						"status"=> $distributions[$bucket->Name]['Status'],
						"enabled"=> $distributions[$bucket->Name]['Enabled']
					);
				}
			}
	    }  
	}
	catch(Exception $e)
	{
		$response = array("error" => $e->getMessage(), "data" => array());
	}

	print json_encode($response);
?>