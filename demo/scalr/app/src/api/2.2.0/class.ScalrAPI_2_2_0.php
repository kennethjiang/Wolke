<?php
	
	class ScalrAPI_2_2_0 extends ScalrAPI_2_1_0
	{
		public function FarmGetDetails($FarmID)
		{
			$response = parent::FarmGetDetails($FarmID);
						
			foreach ($response->FarmRoleSet->Item as &$item)
			{
				$dbFarmRole = DBFarmRole::LoadByID($item->ID);
				
				$item->{"CloudLocation"} = $dbFarmRole->GetSetting(DBFarmRole::SETTING_CLOUD_LOCATION);
				
				if ($dbFarmRole->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::MYSQL))
				{
					$itm->{"MySQLProperties"} = new stdClass();
					$itm->{"MySQLProperties"}->{"LastBackupTime"} = $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_LAST_BCP_TS);
					$itm->{"MySQLProperties"}->{"LastBundleTime"} = $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_LAST_BUNDLE_TS);
					$itm->{"MySQLProperties"}->{"IsBackupRunning"} = $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_IS_BCP_RUNNING);
					$itm->{"MySQLProperties"}->{"IsBundleRunning"} = $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_IS_BUNDLE_RUNNING);
				}
			}
			
			return $response;
		}		
	}
?>
