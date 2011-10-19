<?
	class RAMScalingSensor implements IScalingSensor
	{
		public function __construct()
		{
			$this->DB = Core::GetDBInstance();
			$this->SNMP = new SNMP();
			$this->Logger = Logger::getLogger("RAMScalingSensor");
		}
		
		public function GetValue(DBFarmRole $DBFarmRole)
		{
			$servers = $DBFarmRole->GetServersByFilter(array('status' => SERVER_STATUS::RUNNING));
			$DBFarm = $DBFarmRole->GetFarmObject();
			
			$roleRAM = 0;
			
			if (count($servers) == 0)
				return 0;
							
			foreach ($servers as $DBServer)
			{
				$port = $DBServer->GetProperty(SERVER_PROPERTIES::SZR_SNMP_PORT);
				if (!$port)
					$port = 161;
				
				$this->SNMP->Connect($DBServer->remoteIp, $port, $DBFarm->Hash, null, null, true);
            	preg_match_all("/[0-9]+/si", $this->SNMP->Get(".1.3.6.1.4.1.2021.4.11.0"), $matches); // Free (Mem+Swap)
            	//preg_match_all("/[0-9]+/si", $this->SNMP->Get(".1.3.6.1.4.1.2021.4.6.0"), $matches); // Only MEM
				$free_ram = (int)$matches[0][0];
            	
            	$ram = round($free_ram/1024, 2);
				$this->Logger->info(sprintf("Free RAM on instance '%s' = %s MB", $DBServer->serverId, $ram));
                                    
                $roleRAM += $ram;
			}
			
			$retval = round($roleRAM/count($servers), 2);
			
			$this->DB->Execute("REPLACE INTO sensor_data SET farm_roleid=?, sensor_name=?, sensor_value=?, dtlastupdate=?, raw_sensor_data=?",
				array($DBFarmRole->ID, get_class($this), $retval, time(), $roleRAM)
			);
			
			return $retval;
		}
	}
?>