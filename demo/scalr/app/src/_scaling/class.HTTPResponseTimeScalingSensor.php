<?
	class HTTPResponseTimeScalingSensor implements IScalingSensor
	{
		public function __construct()
		{
			$this->DB = Core::GetDBInstance();
			$this->Logger = Logger::getLogger("HTTPResponseTimeScalingSensor");
		}
		
		public function GetValue(DBFarmRole $DBFarmRole)
		{
			$start_time = microtime(true);
			
			// REQUEST
			$HttpRequest = new HttpRequest();
			
			$HttpRequest->setOptions(array(
				"redirect" => 10, 
				"useragent" => "Scalr (http://scalr.net) HTTPResponseTimeScalingSensor"
			));
			$HttpRequest->setUrl($DBFarmRole->GetSetting(HTTPResponseTimeScalingAlgo::PROPERTY_URL));
			$HttpRequest->setMethod(constant("HTTP_METH_GET"));
			try 
            {
                $HttpRequest->send();
                //$info = $HttpRequest->getResponseInfo();
            }
            catch (Exception $e)
            {
            	if ($e->innerException)
            		$message = $e->innerException->getMessage();
            	else
            		$message = $e->getMessage();  
            	
            	throw new Exception("HTTPResponseTime scaling sensor cannot get value: {$message}");
            }
			
			$retval = round(microtime(true) - $start_time, 2);
			
						
			$this->DB->Execute("REPLACE INTO sensor_data SET farm_roleid=?, sensor_name=?, sensor_value=?, dtlastupdate=?, raw_sensor_data=?",
				array($DBFarmRole->ID, get_class($this), $retval, time(), $retval)
			);
			
			return $retval;
		}
	}
?>