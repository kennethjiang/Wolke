<?
	class UsageStatsPollerProcess implements IProcess
    {
        public $ThreadArgs;
        public $ProcessDescription = "Farm usage stats poller";
        public $Logger;
        
    	public function __construct()
        {
        	// Get Logger instance
        	$this->Logger = Logger::getLogger(__CLASS__);
        }
        
        public function OnStartForking()
        {
            $db = Core::GetDBInstance();
            
            $this->Logger->info("Fetching running farms...");
            
            $this->ThreadArgs = $db->GetAll("SELECT farms.id as id FROM farms 
            	INNER JOIN clients ON clients.id = farms.clientid WHERE clients.isactive='1' AND farms.status=?",
            	array(FARM_STATUS::RUNNING)
            );
                        
            $this->Logger->info("Found ".count($this->ThreadArgs)." farms.");
        }
        
        public function OnEndForking()
        {

        }
        
        public function StartThread($farminfo)
        {
            // Reconfigure observers;
        	Scalr::ReconfigureObservers();
        	
        	$db = Core::GetDBInstance();
            $snmpClient = new Scalr_Net_Snmp_Client();
            
            $DBFarm = DBFarm::LoadByID($farminfo['id']);
            
            define("SUB_TRANSACTIONID", posix_getpid());
            define("LOGGER_FARMID", $farminfo["id"]);
            
            $this->Logger->info("[".SUB_TRANSACTIONID."] Begin polling usage stats for farm (ID: {$DBFarm->ID}, Name: {$DBFarm->Name})");

            foreach ($DBFarm->GetFarmRoles() as $DBFarmRole)
            {
                $this->Logger->info("[FarmID: {$DBFarm->ID}] Begin check '{$DBFarmRole->GetRoleObject()->name}' role servers. Platform: {$DBFarmRole->Platform}");                
                foreach ($DBFarmRole->GetServersByFilter() as $DBServer)
                {                	
                	if ($DBServer->status == SERVER_STATUS::PENDING_TERMINATE)
                        continue;
                    	                
                    if (!$DBServer->IsRebooting())
                    {
						if (!$DBServer->remoteIp)
							continue;
                    	
						$port = $DBServer->GetProperty(SERVER_PROPERTIES::SZR_SNMP_PORT);
						if (!$port)
							$port = 161;
							
                    	$snmpClient->connect($DBServer->remoteIp, $port, $DBFarm->Hash, null, null, true);
                        $res = $snmpClient->get(".1.3.6.1.4.1.2021.10.1.3.3");
                        if ($res)
                        {                                	
							preg_match_all("/[0-9]+/si", $snmpClient->get(".1.3.6.1.2.1.2.2.1.10.2"), $matches);
							$bw_in = $matches[0][0];
						                        
							preg_match_all("/[0-9]+/si", $snmpClient->get(".1.3.6.1.2.1.2.2.1.16.2"), $matches);
							$bw_out = $matches[0][0];
						            
							$c_bw_in = (int)$DBServer->GetProperty(SERVER_PROPERTIES::STATISTICS_BW_IN);
							$c_bw_out = (int)$DBServer->GetProperty(SERVER_PROPERTIES::STATISTICS_BW_OUT);
							
				            if ($bw_in > $c_bw_in && ($bw_in-(int)$c_bw_in) > 0)
				            	$bw_in_used[] = round(((int)$bw_in-(int)$c_bw_in)/1024, 2);
				            else
				            	$bw_in_used[] = $bw_in/1024;
						            	
				            if ($bw_out > $c_bw_out && ($bw_out-(int)$c_bw_out) > 0)
				            	$bw_out_used[] = round(((int)$bw_out-(int)$c_bw_out)/1024, 2);
				            else
				            	$bw_out_used[] = $bw_out/1024;

				            	
				            $DBServer->SetProperties(array(
				            	SERVER_PROPERTIES::STATISTICS_BW_IN 	=> $bw_in,
				            	SERVER_PROPERTIES::STATISTICS_BW_OUT 	=> $bw_out
				            ));
						}
					}           
                } //for each items
            }
            
            //
            // Update statistics
            //
			$this->Logger->debug("Updating statistics for farm.");
                
			$current_stat = $db->GetRow("SELECT * FROM farm_stats WHERE farmid=? AND month=? AND year=?",
				array($DBFarm->ID, date("m"), date("Y"))
			);

			/*
			/*
			foreach ($items as $item)
			{
				$launch_time = strtotime($item->launchTime);
				$uptime = time() - $launch_time;
                    
				$last_uptime = $db->GetOne("SELECT uptime FROM farm_instances WHERE instance_id=?", array($item->instanceId));
				$uptime_delta = $uptime-$last_uptime;
                    
				$stat_uptime[$item->instanceType] += $uptime_delta;
				
				$db->Execute("UPDATE farm_instances SET uptime=? WHERE instance_id=?",
					array($uptime, $item->instanceId)
				);
			}
			*/
                                
			if (!$current_stat)
			{
				$db->Execute("INSERT INTO farm_stats SET farmid=?, month=?, year=?",
					array($farminfo['id'], date("m"), date("Y"))
				);
			}
			
			$data = array(
                (int)array_sum((array)$bw_in_used),
                (int)array_sum((array)$bw_out_used),
                (int)$stat_uptime['m1.small'],
                (int)$stat_uptime['m1.large'],
                (int)$stat_uptime['m1.xlarge'],
                (int)$stat_uptime['c1.medium'],
                (int)$stat_uptime['c1.xlarge'],
                
                time(),
                $farminfo['id'],
                date("m"),
                date("Y")
			);
			
			$db->Execute("UPDATE farm_stats SET 
                bw_in		= bw_in+?, 
                bw_out		= bw_out+?, 
                m1_small	= m1_small+?,
                m1_large	= m1_large+?,
                m1_xlarge	= m1_xlarge+?,
                c1_medium	= c1_medium+?,
                c1_xlarge	= c1_xlarge+?,
                dtlastupdate = ?
                WHERE farmid = ? AND month = ? AND year = ?
			", $data);                
                
 			//
			//Statistics update - end
			// 
        }
    }
?>