<?
	class SQSScalingSensor implements IScalingSensor
	{
		public function __construct()
		{
			$this->DB = Core::GetDBInstance();
			$this->Logger = Logger::getLogger("SQSScalingSensor");
		}
		
		public function GetValue(DBFarmRole $DBFarmRole)
		{
			$DBFarm = $DBFarmRole->GetFarmObject();
			
			$AmazonSQS = AmazonSQS::GetInstance(
				$DBFarm->GetEnvironmentObject()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY),
				$DBFarm->GetEnvironmentObject()->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY)
			);
			
			try
			{
				$res = $AmazonSQS->GetQueueAttributes($DBFarmRole->GetSetting(SQSScalingAlgo::PROPERTY_QUEUE_NAME));
				$retval = $res['ApproximateNumberOfMessages'];
			}
			catch(Exception $e)
			{
				throw new Exception(sprintf("SQSScalingSensor failed during SQS request: %s", $e->getMessage()));
			}
			
			$this->DB->Execute("REPLACE INTO sensor_data SET farm_roleid=?, sensor_name=?, sensor_value=?, dtlastupdate=?, raw_sensor_data=?",
				array($DBFarmRole->ID, get_class($this), $retval, time(), $retval)
			);
			
			return $retval;
		}
	}
?>