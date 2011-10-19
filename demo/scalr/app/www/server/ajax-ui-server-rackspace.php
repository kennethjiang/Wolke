<?
    require("../src/prepend.inc.php");
        
    class RackSpaceAjaxUIServer
    {
    	private $environment;
    	
    	public function __construct()
    	{
    		$this->DB = Core::GetDBInstance();
    		$this->Logger = Logger::getLogger(__CLASS__);
    		
    		$this->environment = Scalr_Session::getInstance()->getEnvironment();
    	}
    	
    	function GetFlavorsList($cloudLocation)
    	{
    		$cs = Scalr_Service_Cloud_Rackspace::newRackspaceCS(
				$this->environment->getPlatformConfigValue(Modules_Platforms_Rackspace::USERNAME, true, $cloudLocation),
				$this->environment->getPlatformConfigValue(Modules_Platforms_Rackspace::API_KEY, true, $cloudLocation),
				$cloudLocation
			);			
			
			$retval = array();
			foreach ($cs->listFlavors(true)->flavors as $flavor) {
				$retval[] = array(
					$flavor->id, 
					sprintf('RAM: %s MB Disk: %s GB', $flavor->ram, $flavor->disk)	
				);
			}
		    
		    return $retval;
    	}
    }
    
    // Run
    try
    {
    	$AjaxUIServer = new RackSpaceAjaxUIServer();
    	
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