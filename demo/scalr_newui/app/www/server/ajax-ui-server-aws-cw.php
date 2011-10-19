<?
    require("../src/prepend.inc.php");
        
    class CWAjaxUIServer
    {
    	public function __construct()
    	{
    		$this->DB = Core::GetDBInstance();
    		$this->Logger = Logger::getLogger(__CLASS__);
    		
    		$environment = Scalr_Session::getInstance()->getEnvironment();
    		$this->AmazonCloudWatch = AmazonCloudWatch::GetInstance(
    			$environment->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY),
    			$environment->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY)
    		);
    	}
    	
    	function GetMetric($MetricName, $StartTime, $EndTime, $Type, $Period, $NameSpace, $DType, $DValue, $DateFormat)
    	{
    		$res = $this->AmazonCloudWatch->GetMetricStatistics(
				$MetricName, 
				strtotime($StartTime), 
				strtotime($EndTime), 
				array($Type), 
				null, 
				$Period, 
				$NameSpace,
				array($DType => $DValue)
			);
			
			$retval = array();
			ksort($res);
	    	foreach ($res as $time => $val)
				$retval[] = array('time' => date($DateFormat, $time), 'value' => (float)round($val[$Type], 2));
			
			return $retval;
    	}
    }
    
    // Run
    try
    {
    	$AjaxUIServer = new CWAjaxUIServer();
    	
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
    
    $response = array("error" => $error, "data" => $result);
	print json_encode($response);
	exit();
?>