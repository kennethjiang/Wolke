<?php
	class DNSEventObserver extends EventObserver
	{
		public $ObserverName = 'DNS';
		
		function __construct()
		{
			parent::__construct();
		}
	
		public function OnRebootComplete(RebootCompleteEvent $event)
		{
			
		}
		
		public function OnNewMysqlMasterUp(NewMysqlMasterUpEvent $event)
		{			
			$this->updateZoneServerRecords($event->DBServer->serverId, $event->DBServer->farmId, true);
		}
	
		/**
		 * Public IP address for instance changed
		 *
		 * @param array $instanceinfo
		 * @param string $new_ip_address
		 */
		public function OnIPAddressChanged(IPAddressChangedEvent $event)
		{
			$this->updateZoneServerRecords($event->DBServer->serverId, $event->DBServer->farmId);
		}
		
		/**
		 * Farm launched
		 *
		 * @param bool $mark_instances_as_active
		 */
		public function OnFarmLaunched(FarmLaunchedEvent $event)
		{
			$zones = DBDNSZone::loadByFarmId($event->GetFarmID());
			if (count($zones) == 0)
				return;
				
			foreach ($zones as $zone)
			{
				if ($zone->status == DNS_ZONE_STATUS::INACTIVE)
				{
					$zone->status = DNS_ZONE_STATUS::PENDING_CREATE;
					$zone->save();
				}
			}
		}
		/**
		 * Farm terminated
		 *
		 * @param bool $remove_zone_from_DNS
		 * @param bool $keep_elastic_ips
		 */
		public function OnFarmTerminated(FarmTerminatedEvent $event)
		{
			if (!$event->RemoveZoneFromDNS)
				return;
				
			$zones = DBDNSZone::loadByFarmId($event->GetFarmID());
			if (count($zones) == 0)
				return;
				
			foreach ($zones as $zone)
			{
				if ($zone->status != DNS_ZONE_STATUS::PENDING_DELETE)
				{
					$zone->status = DNS_ZONE_STATUS::INACTIVE;
					$zone->save();
				}
				
				/*
				$this->DB->Execute("DELETE FROM dns_zone_records WHERE server_id != '' AND zone_id=?", 
					array($zone->id)
				);
				*/
			}
		}
		
		/**
		 * Instance sent hostUp event
		 *
		 * @param array $instanceinfo
		 */
		public function OnHostUp(HostUpEvent $event)
		{
			$update_all = $event->DBServer->GetFarmRoleObject()->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::MYSQL) ? true : false;
			$this->updateZoneServerRecords($event->DBServer->serverId, $event->DBServer->farmId, $update_all);
		}
		
		public function OnBeforeHostTerminate(BeforeHostTerminateEvent $event)
		{
			$update_all = false;
			try {
				$update_all = $event->DBServer->GetFarmRoleObject()->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::MYSQL) ? true : false;
			}
			catch(Exception $e){}
			
			$this->updateZoneServerRecords($event->DBServer->serverId, $event->DBServer->farmId, $update_all);
		}
		
		/**
		 * Instance terminated
		 *
		 * @param array $instanceinfo
		 */
		public function OnHostDown(HostDownEvent $event)
		{
			$update_all = false;
			try {
				$update_all = $event->DBServer->GetFarmRoleObject()->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::MYSQL) ? true : false;
			}
			catch(Exception $e){}
			
			$this->updateZoneServerRecords($event->DBServer->serverId, $event->DBServer->farmId, $update_all, true);
		}
		
		private function updateZoneServerRecords($server_id, $farm_id, $reset_all_system_records = false, $skip_status_check = false)
		{
			$zones = DBDNSZone::loadByFarmId($farm_id);
			foreach ($zones as $DBDNSZone)
			{
				if (!$skip_status_check && ($DBDNSZone->status == DNS_ZONE_STATUS::PENDING_DELETE || $DBDNSZone->status == DNS_ZONE_STATUS::INACTIVE))
					continue;
				
				if (!$reset_all_system_records)
				{
					$DBDNSZone->updateSystemRecords($server_id);
					$DBDNSZone->save();
				}
				else
				{
					$DBDNSZone->save(true);
				}
			}
		}
	}
?>