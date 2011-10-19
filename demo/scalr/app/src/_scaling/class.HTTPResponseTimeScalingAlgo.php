<?
	class HTTPResponseTimeScalingAlgo extends ScalingAlgo implements IScalingAlgo
	{
		const PROPERTY_MIN_RTIME = 'scaling.httpresponsetime.min';
		const PROPERTY_MAX_RTIME = 'scaling.httpresponsetime.max';
		const PROPERTY_URL 		 = 'scaling.httpresponsetime.url';
		
		public function __construct()
		{
			parent::__construct();
			$this->Logger = Logger::getLogger(get_class($this));
		}
		
		public static function GetAlgoDescription()
		{
			return "HTTP URL Response time";
		}
		
		public static function ValidateConfiguration(array &$config, DBRole $DBRole)
		{
			$algo_name = strtolower(str_replace("ScalingAlgo", "", __CLASS__));
			
			if ($config["scaling.{$algo_name}.enabled"] == 1)
			{
				$config[self::PROPERTY_MIN_RTIME] = (int)$config[self::PROPERTY_MIN_RTIME];
				if($config[self::PROPERTY_MIN_RTIME] < 1)
					throw new Exception(sprintf(_("Minimum response time (HTTP response time scaling) for role '%s' must be a number greather than 0"), $DBRole->name));
					
				$config[self::PROPERTY_MAX_RTIME] = (int)$config[self::PROPERTY_MAX_RTIME];
				if($config[self::PROPERTY_MAX_RTIME] < 2)
					throw new Exception(sprintf(_("Maximum response time (HTTP response time scaling) for role '%s' must be a number greather than 2"), $DBRole->name));
					
				if($config[self::PROPERTY_MAX_RTIME] < $config[self::PROPERTY_MIN_RTIME])
					throw new Exception(sprintf(_("Maximum response time (HTTP response time scaling) for role '%s' must be greather than minimum response time"), $DBRole->name));
					
				if (!$config[self::PROPERTY_URL] || $config[self::PROPERTY_URL] == '0')
					throw new Exception(sprintf(_("URL for role '%s' required"), $DBRole->name));
			}
				
			return true;
		}
		
		/**
		 * Must return a DataForm object that will be used to draw a configuration form for this scalign algo.
		 * @return DataForm object
		 */
		public static function GetConfigurationForm(Scalr_Environment $environment)
		{
			$ConfigurationForm = new DataForm();
			
			$ConfigurationForm->AppendField( new DataFormField(self::PROPERTY_MAX_RTIME, FORM_FIELD_TYPE::TEXT, "Maximum response time", null, null, 30));
			$ConfigurationForm->AppendField( new DataFormField(self::PROPERTY_MIN_RTIME, FORM_FIELD_TYPE::TEXT, "Minimum response time", null, null, 10));
			$ConfigurationForm->AppendField( new DataFormField(self::PROPERTY_URL, FORM_FIELD_TYPE::TEXT, "URL (with http(s)://)", null, null, "http://site.com"));
			
			return $ConfigurationForm;
		}
		
		public function MakeDecision(DBFarmRole $DBFarmRole)
		{			
			//
			// Get data from SQS sensor
			//
			try
			{
				$Sensor = SensorFactory::NewSensor(SensorFactory::HTTP_RESPONSE_TIME_SENSOR);
				$this->lastSensorValue = $Sensor->GetValue($DBFarmRole);
				$this->Logger->info("HTTPResponseTimeScalingSensor({$DBFarmRole->FarmID}, {$DBFarmRole->AMIID}) Sensor returned value: {$this->lastSensorValue}.");
			}
			catch(Exception $e)
			{
				Logger::getLogger(LOG_CATEGORY::FARM)->warn(new FarmLogMessage($DBFarmRole->FarmID, $e->getMessage()));
				return ScalingAlgo::NOOP;
			}
			
			if ($this->lastSensorValue > $this->GetProperty(self::PROPERTY_MAX_RTIME))
			{
				if($DBFarmRole->GetRunningInstancesCount() < $DBFarmRole->GetSetting(DBFarmRole::SETTING_SCALING_MAX_INSTANCES) 
					&& $DBFarmRole->GetPendingInstancesCount() == 0)
					return ScalingAlgo::UPSCALE;
				else
					return ScalingAlgo::NOOP;
			}
			elseif ($this->lastSensorValue < $this->GetProperty(self::PROPERTY_MIN_RTIME))
			{
				if ($DBFarmRole->GetRunningInstancesCount() > $DBFarmRole->GetSetting(DBFarmRole::SETTING_SCALING_MIN_INSTANCES))
					return ScalingAlgo::DOWNSCALE;
				else
					return ScalingAlgo::NOOP;
			}
			else
				return ScalingAlgo::NOOP;
		}
	}
?>