<?
    require("../src/prepend.inc.php");
        
    class EucaAjaxUIServer
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
	    	$client = Scalr_Service_Cloud_Eucalyptus::newCloud(
				$this->environment->getPlatformConfigValue(Modules_Platforms_Eucalyptus::SECRET_KEY, true, $Region),
				$this->environment->getPlatformConfigValue(Modules_Platforms_Eucalyptus::ACCESS_KEY, true, $Region),
				$this->environment->getPlatformConfigValue(Modules_Platforms_Eucalyptus::EC2_URL, true, $Region)
			);
    		
    		// Get Avail zones
		    $avail_zones_resp = $client->describeAvailabilityZones();
		    $retval = array();
		    
		    foreach ($avail_zones_resp->availabilityZoneInfo->item as $zone)
		    	array_push($retval, array('id' => (string)$zone->zoneName, 'name' => (string)$zone->zoneName));
		    
		    return $retval;
    	}
    }
    
    // Run
    try
    {
    	$AjaxUIServer = new EucaAjaxUIServer();
    	
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