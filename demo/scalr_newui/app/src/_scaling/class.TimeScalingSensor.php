<?
	class TimeScalingSensor implements IScalingSensor
	{
		public function __construct()
		{
			$this->Logger = Logger::getLogger("TimeScalingSensor");
		}
		
		public function GetValue(DBFarmRole $DBFarmRole)
		{
			$DBFarm = $DBFarmRole->GetFarmObject();
			
			$Client = Client::Load($DBFarm->ClientID);
    		$tz = $Client->GetSettingValue(CLIENT_SETTINGS::TIMEZONE);
    		if ($tz)
    		{
	    		$default_tz = @date_default_timezone_get();
    			@date_default_timezone_set($tz);
    		}
			
			$retval = array((int)date("Hi"), date("D"));
			
			if ($default_tz)
				@date_default_timezone_set($default_tz);
			
			return $retval;
		}
	}
?>