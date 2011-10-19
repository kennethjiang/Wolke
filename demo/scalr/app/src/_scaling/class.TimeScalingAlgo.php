<?
	class TimeScalingAlgo extends ScalingAlgo implements IScalingAlgo
	{		
		const PROPERTY_TIME_PERIODS = 'scaling.time.periods';
		const PROPERTY_NEED_INSTANCES_IN_CURRENT_PERIOD = 'scaling.time.instances_count';
		
		public function __construct()
		{
			parent::__construct();
			$this->Logger = Logger::getLogger(get_class($this));
		}
		
		public static function GetAlgoDescription()
		{
			return "Time and day of week";
		}
		
		public static function ValidateConfiguration(array &$config, DBRole $DBRole)
		{
			//$algo_name = strtolower(str_replace("ScalingAlgo", "", __CLASS__));
			//				
			return true;	
		}
		
		/**
		 * Must return a DataForm object that will be used to draw a configuration form for this scalign algo.
		 * @return DataForm object
		 */
		public static function GetConfigurationForm(Scalr_Environment $environment)
		{
			$ConfigurationForm = new DataForm();
			$ConfigurationForm->AppendField( new DataFormField('scaling.time', FORM_FIELD_TYPE::DATE_TIME_TABLE, ""));
			
			return $ConfigurationForm;
		}
		
		public function MakeDecision(DBFarmRole $DBFarmRole)
		{			
			$DB = Core::GetDBInstance();
			//
			// Get data from BW sensor
			//
			$TimeSensor = SensorFactory::NewSensor(SensorFactory::TIME_SENSOR);
			$this->lastSensorValue = $TimeSensor->GetValue($DBFarmRole);
			$this->Logger->info("TimeScalingAlgo({$DBFarmRole->FarmID}, {$DBFarmRole->AMIID}) Sensor returned value: ".serialize($this->lastSensorValue).".");
			
			$scaling_period = $DB->GetRow("SELECT * FROM farm_role_scaling_times WHERE
				'{$this->lastSensorValue[0]}' >= start_time AND
				'{$this->lastSensorValue[0]}' <= end_time-10 AND
				INSTR(days_of_week, '{$this->lastSensorValue[1]}') != 0 AND
				farm_roleid = '{$DBFarmRole->ID}'
			");
			
			if ($scaling_period)
			{
				$this->Logger->info("TimeScalingAlgo({$DBFarmRole->FarmID}, {$DBFarmRole->AMIID}) Found scaling period. Total {$scaling_period['instances_count']} instances should be running.");
				$num_instances = $scaling_period['instances_count'];

				$DBFarmRole->SetSetting(self::PROPERTY_NEED_INSTANCES_IN_CURRENT_PERIOD, $num_instances);
				if (($DBFarmRole->GetRunningInstancesCount()+$DBFarmRole->GetPendingInstancesCount()) < $num_instances)
					return ScalingAlgo::UPSCALE;
				else
					return ScalingAlgo::STOP_SCALING;
			}
			else
			{
				$DBFarmRole->SetSetting(self::PROPERTY_NEED_INSTANCES_IN_CURRENT_PERIOD, "");
				if ($DBFarmRole->GetRunningInstancesCount() > $DBFarmRole->GetSetting(DBFarmRole::SETTING_SCALING_MIN_INSTANCES))
					return ScalingAlgo::DOWNSCALE;
				else
					return ScalingAlgo::NOOP;
			}
		}
	}
?>