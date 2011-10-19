<?
	class LAScalingAlgo extends ScalingAlgo implements IScalingAlgo
	{
		const PROPERTY_MAX_LA = 'scaling.la.max';
		const PROPERTY_MIN_LA = 'scaling.la.min';
		
		public function __construct()
		{
			parent::__construct();
			$this->Logger = Logger::getLogger(get_class($this));
		}
		
		public static function GetAlgoDescription()
		{
			return "LA (Load averages)";
		}
		
		public static function ValidateConfiguration(array &$config, DBRole $DBRole)
		{
			$algo_name = strtolower(str_replace("ScalingAlgo", "", __CLASS__));
			
			if ($config["scaling.{$algo_name}.enabled"] == 1)
			{
				$config[self::PROPERTY_MAX_LA] = (float)$config[self::PROPERTY_MAX_LA];
				if($config[self::PROPERTY_MAX_LA] <= 0 || $config[self::PROPERTY_MAX_LA] > 50)
					throw new Exception(sprintf(_("Maximum LA for role '%s' must be a number between 0.1 and 50"), $DBRole->name));
					
				$config[self::PROPERTY_MIN_LA] = (float)$config[self::PROPERTY_MIN_LA];
				if($config[self::PROPERTY_MIN_LA] <= 0 || $config[self::PROPERTY_MIN_LA] > 50)
					throw new Exception(sprintf(_("Minimum LA for role '%s' must be a number between 0.1 and 50"), $DBRole->name));
					
				if($config[self::PROPERTY_MAX_LA] < $config[self::PROPERTY_MIN_LA])
					throw new Exception(sprintf(_("Maximum LA for role '%s' must be greather than minimum LA"), $DBRole->name));
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
			
			//Will set scaling.la.min and scaling.la.max
			$ConfigurationForm->AppendField( new DataFormField("scaling.la", FORM_FIELD_TYPE::MIN_MAX_SLIDER, "Load averages", null, null, "2,5"));
			
			return $ConfigurationForm;
		}
		
		public function MakeDecision(DBFarmRole $DBFarmRole)
		{
			//
			// Get data from LA sensor
			//
			$LASensor = SensorFactory::NewSensor(SensorFactory::LA_SENSOR);
			$this->lastSensorValue = $LASensor->GetValue($DBFarmRole);
			$this->Logger->info("LAScalingAlgo({$DBFarmRole->FarmID}, {$DBFarmRole->AMIID}) Sensor returned value: {$this->lastSensorValue}.");
			
			//
			if ($this->lastSensorValue > $this->GetProperty(self::PROPERTY_MAX_LA))
			{
				if($DBFarmRole->GetRunningInstancesCount() < $DBFarmRole->GetSetting(DBFarmRole::SETTING_SCALING_MAX_INSTANCES) 
					&& $DBFarmRole->GetPendingInstancesCount() == 0)
					return ScalingAlgo::UPSCALE;
				else
					return ScalingAlgo::NOOP;
			}
			elseif ($this->lastSensorValue < $this->GetProperty(self::PROPERTY_MIN_LA))
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