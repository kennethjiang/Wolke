<?
	class LAScalingSensor implements IScalingSensor
	{
		public function __construct()
		{
			$this->DB = Core::GetDBInstance();
			$this->SNMP = new SNMP();
			$this->Logger = Logger::getLogger("LAScalingSensor");
		}
		
		public function GetValue(DBFarmRole $DBFarmRole)
		{
			$servers = $DBFarmRole->GetServersByFilter(array('status' => SERVER_STATUS::RUNNING));
			$DBFarm = $DBFarmRole->GetFarmObject();
			
			$roleLA = 0;
			
			if (count($servers) == 0)
				return 0;
			
			foreach ($servers as $DBServer)
			{
				$port = $DBServer->GetProperty(SERVER_PROPERTIES::SZR_SNMP_PORT);
				if (!$port)
					$port = 161;
				
				$this->SNMP->Connect($DBServer->remoteIp, $port, $DBFarm->Hash, null, null, true);
            	$res = $this->SNMP->Get(".1.3.6.1.4.1.2021.10.1.3.3");
            	
            	$la = (float)$res;
				$this->Logger->info(sprintf("LA (15 min average) on '%s' = %s", $DBServer->serverId, $la));
                                    
                $roleLA += $la;
			}
			
			$retval = round($roleLA/count($servers), 2);
			
			$this->DB->Execute("REPLACE INTO sensor_data SET farm_roleid=?, sensor_name=?, sensor_value=?, dtlastupdate=?, raw_sensor_data=?",
				array($DBFarmRole->ID, get_class($this), $retval, time(), $roleLA)
			);
			
			return $retval;
		}
	}
?>