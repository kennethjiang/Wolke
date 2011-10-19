<?php
	$response = array();
	
	// AJAX_REQUEST;
	$context = 6;
	
	try
	{
		$enable_json = true;
		include("../../src/prepend.inc.php");
	
		Scalr_Session::getInstance()->getAuthToken()->hasAccessEx(Scalr_AuthToken::ACCOUNT_USER);
		
		// Load Client Object
	    $AmazonRDSClient = Scalr_Service_Cloud_Aws::newRds( 
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::ACCESS_KEY),
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::SECRET_KEY),
			$_SESSION['aws_region']
		);
	    
	    $res = $AmazonRDSClient->DescribeEvents($req_name, $req_type);
	    
	    $events = (array)$res->DescribeEventsResult->Events;
	    $response['success'] = '';
		$response['error'] = '';
	    $response['data'] = array();
		
	    if (!is_array($events['Event']))
	    	$events['Event'] = array($events['Event']);
	    		    	
	    foreach ($events['Event'] as $event)
	    {
	    	if ($event->Message)
	    	{
		    	$response['data'][] = array(
		    		'message'	=> (string)$event->Message,
		    		'time'	=> date("M j, Y H:i:s", strtotime((string)$event->Date)),
		    		'source'	=> (string)$event->SourceIdentifier,
		    		'type'		=> (string)$event->SourceType
		    	);
	    	}	
	    }
	}
	catch(Exception $e)
	{
		$response = array("error" => $e->getMessage(), "data" => array());
	}
	
	print json_encode($response);
?>