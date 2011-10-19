<?
	class BWScalingAlgo extends ScalingAlgo implements IScalingAlgo
	{		
		const PROPERTY_MAX_BW = 'scaling.bw.max';
		const PROPERTY_MIN_BW = 'scaling.bw.min';
		
		public function __construct()
		{
			parent::__construct();
			$this->Logger = Logger::getLogger(get_class($this));
		}
		
		public static function GetAlgoDescription()
		{
			return "BandWidth usage";
		}
		
		public static function ValidateConfiguration(array &$config, DBRole $DBRole)
		{
			$algo_name = strtolower(str_replace("ScalingAlgo", "", __CLASS__));
			
			if ($config["scaling.{$algo_name}.enabled"] == 1)
			{
				$config[self::PROPERTY_MAX_BW] = (float)$config[self::PROPERTY_MAX_BW];
				if($config[self::PROPERTY_MAX_BW] <= 0 || $config[self::PROPERTY_MAX_BW] > 200)
					throw new Exception(sprintf(_("Maximum Bandwidth for role '%s' must be a number between 1 and 200"), $DBRole->name));
					
				$config[self::PROPERTY_MIN_BW] = (int)$config[self::PROPERTY_MIN_BW];
				if($config[self::PROPERTY_MIN_BW] <= 0 || $config[self::PROPERTY_MIN_BW] > 200)
					throw new Exception(sprintf(_("Minimum Bandwidth for role '%s' must be a number between 1 and 200"), $DBRole->name));
					
				if($config[self::PROPERTY_MAX_BW] < $config[self::PROPERTY_MIN_BW])
					throw new Exception(sprintf(_("Maximum Bandwidth for role '%s' must be greather than minimum Bandwidth"), $DBRole->name));
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
			$ConfigurationForm->AppendField( new DataFormField(self::PROPERTY_MIN_BW, FORM_FIELD_TYPE::TEXT, "Minimum Bandwidth (Mbit/s)", null, null, 1));
			$ConfigurationForm->AppendField( new DataFormField(self::PROPERTY_MAX_BW, FORM_FIELD_TYPE::TEXT, "Maximum Bandwidth (Mbit/s)", null, null, 35));
			
			return $ConfigurationForm;
		}
		
		public function MakeDecision(DBFarmRole $DBFarmRole)
		{			
			//
			// Get data from BW sensor
			//
			$BWSensor = SensorFactory::NewSensor(SensorFactory::BW_SENSOR);
			$this->lastSensorValue = $BWSensor->GetValue($DBFarmRole);
			$this->Logger->info("BWScalingAlgo({$DBFarmRole->FarmID}, {$DBFarmRole->AMIID}) Sensor returned value: {$this->lastSensorValue}.");
			
			if ($this->lastSensorValue > $this->GetProperty(self::PROPERTY_MAX_BW))
			{
				if($DBFarmRole->GetRunningInstancesCount() < $DBFarmRole->GetSetting(DBFarmRole::SETTING_SCALING_MAX_INSTANCES) 
					&& $DBFarmRole->GetPendingInstancesCount() == 0)
					return ScalingAlgo::UPSCALE;
				else
					return ScalingAlgo::NOOP;
			}
			elseif ($this->lastSensorValue < $this->GetProperty(self::PROPERTY_MIN_BW))
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