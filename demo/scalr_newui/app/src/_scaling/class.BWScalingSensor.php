<?
	class BWScalingSensor implements IScalingSensor
	{
		public function __construct()
		{
			$this->DB = Core::GetDBInstance();
			$this->SNMP = new SNMP();
			$this->Logger = Logger::getLogger("BWScalingSensor");
		}
		
		public function GetValue(DBFarmRole $DBFarmRole)
		{
			$servers = $DBFarmRole->GetServersByFilter(array('status' => SERVER_STATUS::RUNNING));
			$DBFarm = $DBFarmRole->GetFarmObject();
			
			$roleBW = 0;
			
			if (count($servers) == 0)
				return 0;
			
			$prev_sensor_data = $this->DB->GetRow("SELECT raw_sensor_data, dtlastupdate FROM sensor_data WHERE farm_roleid=? AND sensor_name=?",
				array($DBFarmRole->ID, get_class($this))
			);
				
			foreach ($servers as $DBServer)
			{
				$port = $DBServer->GetProperty(SERVER_PROPERTIES::SZR_SNMP_PORT);
				if (!$port)
					$port = 161;
				
				$this->SNMP->Connect($DBServer->remoteIp, $port, $DBFarm->Hash, null, null, true);
            	preg_match_all("/[0-9]+/si", $this->SNMP->Get(".1.3.6.1.2.1.2.2.1.16.2"), $matches);
				$bw_out = (int)$matches[0][0];
            	
            	$bw = round($bw_out/1024/1024, 2);
				$this->Logger->info(sprintf("Bandwidth usage (out) for instance '%s' = %s MB", $DBServer->serverId, $bw));
                                    
                $roleBW += $bw;
			}
			
			$roleBW = round($roleBW/count($servers), 2);
			
			if ($prev_sensor_data)
			{
				$time = (time()-$prev_sensor_data['dtlastupdate']);
				$bandwidth_usage = ($roleBW - $prev_sensor_data['raw_sensor_data'])*8;
				
				$bandwidth_channel_usage = $bandwidth_usage/$time; // in Mbits/sec
				$retval = round($bandwidth_channel_usage, 2);
			}
			else
				$retval = 0;
			
			$this->DB->Execute("REPLACE INTO sensor_data SET farm_roleid=?, sensor_name=?, sensor_value=?, dtlastupdate=?, raw_sensor_data=?",
				array($DBFarmRole->ID, get_class($this), $retval, time(), $roleBW)
			);
			
			return $retval;
		}
	}
?>