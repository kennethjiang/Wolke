<?
    require("../src/prepend.inc.php");
        
    class EC2AjaxUIServer
    {
    	private $environment;
    	
    	public function __construct()
    	{
    		$this->DB = Core::GetDBInstance();
    		$this->Logger = Logger::getLogger(__CLASS__);
    		
    		$this->environment = Scalr_Session::getInstance()->getEnvironment();
    	}
    	
    	function GetAvailZonesList($Region)
    	{
	    	$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
    			$Region,
    			$this->environment->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
				$this->environment->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
    		);
    		
    		// Get Avail zones
		    $avail_zones_resp = $AmazonEC2Client->DescribeAvailabilityZones();
		    if ($avail_zones_resp->availabilityZoneInfo->item instanceOf stdClass)
		    	$avail_zones_resp->availabilityZoneInfo->item = array($avail_zones_resp->availabilityZoneInfo->item);
		    
		    $retval = array();
		    foreach ($avail_zones_resp->availabilityZoneInfo->item as $zone)
		    {
		    	if (stristr($zone->zoneState,'available'))
		    		array_push($retval, array('id' => (string)$zone->zoneName, 'name' => (string)$zone->zoneName));
		    }
		    
		    return $retval;
    	}
    	
    	function GetSnapshotsList($Region)
    	{
    		$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
    			$Region,
    			$this->environment->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
				$this->environment->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
    		);
    		
    		$response = $AmazonEC2Client->DescribeSnapshots();
	    	$rowz = $response->snapshotSet->item;
				
			if ($rowz instanceof stdClass)
				$rowz = array($rowz);

			$retval = array();
				
			foreach ($rowz as $pk=>$pv)
			{		
				if ($pv->ownerId != Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCOUNT_ID))
					continue;
				
				if ($pv->status == 'completed')
					$retval[] = array(
						"snapid" 	=> (string)$pv->snapshotId,
						"createdat"	=> date("M j, Y H:i:s", strtotime((string)$pv->startTime)),
						"size"		=> (string)$pv->volumeSize
					);
			}
			
			return $retval;
    	}
    }
    
    // Run
    try
    {
    	$AjaxUIServer = new EC2AjaxUIServer();
    	
    	$Reflect = new ReflectionClass($AjaxUIServer);
    	if (!$Reflect->hasMethod($req_action))
    		throw new Exception(sprintf("Unknown action: %s", $req_action));
    		
    	$ReflectMethod = $Reflect->getMethod($req_action);
    		
    	$args = array();
    	foreach ($ReflectMethod->getParameters() as $param)
    	{
    		if (!$param->isArray())
    			$args[$param->name] = $_REQUEST[$param->name];
    		else
    			$args[$param->name] = json_decode($_REQUEST[$param->name]);
    	}	
    	
    	$result = $ReflectMethod->invokeArgs($AjaxUIServer, $args);
    }
    catch(Exception $e)
    {
    	$error = $e->getMessage();
    }
    
    $response = array("error" => $error, "success" => ($error == '') ? true : false, "data" => $result);
	print json_encode($response);
	exit();
?>