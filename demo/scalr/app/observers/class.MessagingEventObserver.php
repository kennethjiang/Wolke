<?php
	class MessagingEventObserver extends EventObserver 
	{
		public $ObserverName = 'Messaging';
		
		function __construct()
		{
			parent::__construct();
		}

		public function OnServiceConfigurationPresetChanged(ServiceConfigurationPresetChangedEvent $event)
		{
			$farmRolesPresetInfo = $this->DB->GetAll("SELECT * FROM farm_role_service_config_presets WHERE
				preset_id = ? AND behavior = ?
			", array($event->ServiceConfiguration->id, $event->ServiceConfiguration->roleBehavior));
			if (count($farmRolesPresetInfo) > 0)
			{
				$msg = new Scalr_Messaging_Msg_UpdateServiceConfiguration(
					$event->ServiceConfiguration->roleBehavior,
					$event->ResetToDefaults,
					$farmRolesPresetInfo['restart_service']
				);
				
				foreach ($farmRolesPresetInfo as $farmRole)
				{
					try
					{
						$dbFarmRole = DBFarmRole::LoadByID($farmRole['farm_roleid']);
						
						foreach ($dbFarmRole->GetServersByFilter(array('status' => SERVER_STATUS::RUNNING)) as $dbServer)
						{
							if ($dbServer->IsSupported("0.6"))
								$dbServer->SendMessage($msg);
						}
					}
					catch(Exception $e){}
				}
			}
		}
		
		public function OnRoleOptionChanged(RoleOptionChangedEvent $event) 
		{	
			switch($event->OptionName)
			{
				case 'nginx_https_vhost_template':
				case 'nginx_https_host_template':
					
					$servers = DBFarm::LoadByID($this->FarmID)->GetServersByFilter(array('status' => array(SERVER_STATUS::INIT, SERVER_STATUS::RUNNING)));
					foreach ((array)$servers as $DBServer)
					{
						if ($DBServer->GetFarmRoleObject()->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::APACHE) || $DBServer->GetFarmRoleObject()->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::NGINX))
							$DBServer->SendMessage(new Scalr_Messaging_Msg_VhostReconfigure());
					}
					
					break;
			}
		}
		
		public function OnNewMysqlMasterUp(NewMysqlMasterUpEvent $event)
		{
			$servers = DBFarm::LoadByID($this->FarmID)->GetServersByFilter(array('status' => array(SERVER_STATUS::INIT, SERVER_STATUS::RUNNING)));
			// TODO: Add scalarizr stuff
			
			foreach ((array)$servers as $DBServer)
			{
				$msg = new Scalr_Messaging_Msg_Mysql_NewMasterUp(
					$event->DBServer->GetFarmRoleObject()->GetRoleObject()->getBehaviors(),
					$event->DBServer->GetFarmRoleObject()->GetRoleObject()->name,
					$event->DBServer->localIp,
					$event->DBServer->remoteIp,
					
					/** @deprecated */
					$event->SnapURL
				);
				$farmRole = $DBServer->GetFarmRoleObject();
				$msg->replPassword = $farmRole->GetSetting(DbFarmRole::SETTING_MYSQL_REPL_PASSWORD);
				$msg->rootPassword = $farmRole->GetSetting(DbFarmRole::SETTING_MYSQL_ROOT_PASSWORD);
				if ($event->DBServer->platform == SERVER_PLATFORMS::RACKSPACE) {
					$msg->logPos = $farmRole->GetSetting(DbFarmRole::SETTING_MYSQL_LOG_POS);
					$msg->logFile = $farmRole->GetSetting(DbFarmRole::SETTING_MYSQL_LOG_FILE);
					
					$snapshot = Scalr_Model::init(Scalr_Model::STORAGE_SNAPSHOT);
					
					try {
						$snapshot->loadById($farmRole->GetSetting(DbFarmRole::SETTING_MYSQL_SCALR_SNAPSHOT_ID));
						$msg->snapshotConfig = $snapshot->getConfig();	
					} catch (Exception $e) {
						$this->Logger->error(new FarmLogMessage($event->DBServer->farmId, "Cannot get snaphotConfig for newMysqlMasterUp message: {$e->getMessage()}"));
					}
				}
			
				$DBServer->SendMessage($msg);
			}
		}
		
		public function OnHostInit(HostInitEvent $event)
		{
			$msg = new Scalr_Messaging_Msg_HostInitResponse(
				$event->DBServer->GetFarmObject()->GetSetting(DBFarm::SETTING_CRYPTO_KEY)
			);
			
			$dbServer = $event->DBServer;
			$dbFarmRole = $dbServer->GetFarmRoleObject();
			
			if (!$event->DBServer->IsSupported("0.5"))
			{
				if ($event->DBServer->platform == SERVER_PLATFORMS::EC2)
				{
					$msg->awsAccountId = $event->DBServer->GetEnvironmentObject()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCOUNT_ID);
				}
			}
			
			if ($dbFarmRole->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::MYSQL))
			{
				$isMaster = (int)$dbServer->GetProperty(SERVER_PROPERTIES::DB_MYSQL_MASTER);

				$msg->mysql = (object)array(
					"replicationMaster" => $isMaster,
					"rootPassword" => $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_ROOT_PASSWORD),
					"replPassword" => $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_REPL_PASSWORD),
					"statPassword" => $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_STAT_PASSWORD),
					"logFile" => $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_LOG_FILE),
					"logPos" => $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_LOG_POS)
				);
				
				if ($event->DBServer->IsSupported("0.7"))
				{
					if ($dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_SCALR_VOLUME_ID) && $isMaster)
					{
						try {
							$volume = Scalr_Model::init(Scalr_Model::STORAGE_VOLUME)->loadById(
								$dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_SCALR_VOLUME_ID)
							);
							
							$msg->mysql->volumeConfig = $volume->getConfig();
						} catch (Exception $e) {
						
						}
					}
					
					/*** 
					 * For Rackspace we ALWAYS need snapsjot_config for mysql
					 * ***/
					if ($dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_SCALR_SNAPSHOT_ID))
					{
						try {
							$snapshotConfig = Scalr_Model::init(Scalr_Model::STORAGE_SNAPSHOT)->loadById(
								$dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_SCALR_SNAPSHOT_ID)
							);
							
							$msg->mysql->snapshotConfig = $snapshotConfig->getConfig();
						} catch (Exception $e) {
							$this->Logger->error(new FarmLogMessage($event->DBServer->farmId, "Cannot get snaphotConfig for hostInit message: {$e->getMessage()}"));
						}
					}
					
					if (!$msg->mysql->snapshotConfig && $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_SNAPSHOT_ID))
					{
						$msg->mysql->snapshotConfig = new stdClass();
						$msg->mysql->snapshotConfig->type = $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_DATA_STORAGE_ENGINE);
						$msg->mysql->snapshotConfig->id = $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_SNAPSHOT_ID);
					}
					
					if ($isMaster && !$msg->mysql->volumeConfig)
					{
						$msg->mysql->volumeConfig = new stdClass();
						$msg->mysql->volumeConfig->type = $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_DATA_STORAGE_ENGINE);
						
						if (!$dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_MASTER_EBS_VOLUME_ID))
						{
							if ($dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_DATA_STORAGE_ENGINE) == MYSQL_STORAGE_ENGINE::EBS) {
								$msg->mysql->volumeConfig->size = $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_EBS_VOLUME_SIZE);
							}
							elseif ($dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_DATA_STORAGE_ENGINE) == MYSQL_STORAGE_ENGINE::EPH) {
								$msg->mysql->volumeConfig->snap_backend = "cf://mysql-data-bundle/scalr-{$dbFarmRole->GetFarmObject()->Hash}";
								$msg->mysql->volumeConfig->vg = 'mysql';
								$msg->mysql->volumeConfig->disk = new stdClass();
								$msg->mysql->volumeConfig->disk->type = 'loop';
								$msg->mysql->volumeConfig->disk->size = '75%root';
							}
						}
						else {
							$msg->mysql->volumeConfig->id = $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_MASTER_EBS_VOLUME_ID);
						}
					}
				}
				else {
					
					if ($isMaster)
						$msg->mysql->volumeId = $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_MASTER_EBS_VOLUME_ID);
					
					$msg->mysql->snapshotId = $dbFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_SNAPSHOT_ID);	
				}
			}			
			
			
			// Create ssh keypair for rackspace
			if ($event->DBServer->IsSupported("0.7"))
			{
				if ($event->DBServer->platform == SERVER_PLATFORMS::RACKSPACE || $event->DBServer->platform == SERVER_PLATFORMS::NIMBULA)
				{
					$sshKey = Scalr_Model::init(Scalr_Model::SSH_KEY);
					if (!$sshKey->loadGlobalByFarmId(
						$event->DBServer->farmId, 
						$event->DBServer->GetFarmRoleObject()->GetSetting(DBFarmRole::SETTING_CLOUD_LOCATION)
					)) {
						$key_name = "FARM-{$event->DBServer->farmId}";
						
						$sshKey->generateKeypair();
						
						$sshKey->farmId = $event->DBServer->farmId;
						$sshKey->clientId = $event->DBServer->clientId;
						$sshKey->envId = $event->DBServer->envId;
						$sshKey->type = Scalr_SshKey::TYPE_GLOBAL;
						$sshKey->platform = $event->DBServer->platform;
						$sshKey->cloudLocation = $event->DBServer->GetFarmRoleObject()->GetSetting(DBFarmRole::SETTING_CLOUD_LOCATION);
						$sshKey->cloudKeyName = $key_name;
						$sshKey->platform = $event->DBServer->platform;
						
						$sshKey->save();
					}
					
					$sshKeysMsg = new Scalr_Messaging_Msg_UpdateSshAuthorizedKeys(array($sshKey->getPublic()), array());
					$event->DBServer->SendMessage($sshKeysMsg);
				}
			}
			
			$event->DBServer->SendMessage($msg);
			
			$servers = DBFarm::LoadByID($this->FarmID)->GetServersByFilter(array('status' => array(SERVER_STATUS::INIT, SERVER_STATUS::RUNNING)));
			foreach ((array)$servers as $DBServer)
			{
				$msg = new Scalr_Messaging_Msg_HostInit(
					$event->DBServer->GetFarmRoleObject()->GetRoleObject()->getBehaviors(),
					$event->DBServer->GetFarmRoleObject()->GetRoleObject()->name,
					$event->DBServer->localIp,
					$event->DBServer->remoteIp
				);
				$DBServer->SendMessage($msg);
			}
		}
		
		public function OnEBSVolumeAttached(EBSVolumeAttachedEvent $event)
		{
			$servers = DBFarm::LoadByID($this->FarmID)->GetServersByFilter(array('status' => array(SERVER_STATUS::INIT, SERVER_STATUS::RUNNING)));
			foreach ((array)$servers as $DBServer)
			{
				$msg = new Scalr_Messaging_Msg_BlockDeviceAttached(
					$event->DBServer->GetFarmRoleObject()->GetRoleObject()->getBehaviors(),
					$event->DBServer->GetFarmRoleObject()->GetRoleObject()->name,
					$event->DBServer->localIp,
					$event->DBServer->remoteIp,
					$event->VolumeID,
					$event->DeviceName
				);
				
				$DBServer->SendMessage($msg);
			}
		}
		
		public function OnEBSVolumeMounted(EBSVolumeMountedEvent $event)
		{
			$servers = DBFarm::LoadByID($this->FarmID)->GetServersByFilter(array('status' => array(SERVER_STATUS::INIT, SERVER_STATUS::RUNNING)));
			foreach ((array)$servers as $DBServer)
			{
				$msg = new Scalr_Messaging_Msg_BlockDeviceMounted(
					$event->DBServer->GetFarmRoleObject()->GetRoleObject()->getBehaviors(),
					$event->DBServer->GetFarmRoleObject()->GetRoleObject()->name,
					$event->DBServer->localIp,
					$event->DBServer->remoteIp,
					$event->VolumeID,
					$event->DeviceName,
					$event->Mountpoint,
					false,
					''
				);
				
				$DBServer->SendMessage($msg);
			}
		}
		
		public function OnRebootComplete(RebootCompleteEvent $event) 
		{
			$servers = DBFarm::LoadByID($this->FarmID)->GetServersByFilter(array('status' => array(SERVER_STATUS::INIT, SERVER_STATUS::RUNNING)));
			foreach ((array)$servers as $DBServer)
			{
				$msg = new Scalr_Messaging_Msg_RebootFinish(
					$event->DBServer->GetFarmRoleObject()->GetRoleObject()->getBehaviors(),
					$event->DBServer->GetFarmRoleObject()->GetRoleObject()->name,
					$event->DBServer->localIp,
					$event->DBServer->remoteIp
				);
				$DBServer->SendMessage($msg);
			}
		}
		
		public function OnHostUp(HostUpEvent $event)
		{
			$servers = DBFarm::LoadByID($this->FarmID)->GetServersByFilter(array('status' => array(SERVER_STATUS::INIT, SERVER_STATUS::RUNNING)));
			foreach ((array)$servers as $DBServer)
			{
				$msg = new Scalr_Messaging_Msg_HostUp(
					$event->DBServer->GetFarmRoleObject()->GetRoleObject()->getBehaviors(),
					$event->DBServer->GetFarmRoleObject()->GetRoleObject()->name,
					$event->DBServer->localIp,
					$event->DBServer->remoteIp
				);
				$DBServer->SendMessage($msg);
			}
		}
		
		public function OnBeforeHostTerminate(BeforeHostTerminateEvent $event)
		{
			$servers = DBFarm::LoadByID($this->FarmID)->GetServersByFilter(array('status' => array(SERVER_STATUS::INIT, SERVER_STATUS::RUNNING)));		
			foreach ($servers as $DBServer)
			{									
				$msg = new Scalr_Messaging_Msg_BeforeHostTerminate(
					$event->DBServer->GetFarmRoleObject()->GetRoleObject()->getBehaviors(),
					$event->DBServer->GetFarmRoleObject()->GetRoleObject()->name,
					$event->DBServer->localIp,
					$event->DBServer->remoteIp
				);
				$DBServer->SendMessage($msg);
			}
		}
		
		public function OnHostDown(HostDownEvent $event)
		{
			if ($event->DBServer->IsRebooting() == 1)
				return;
			
			$dbFarm = DBFarm::LoadByID($this->FarmID);
			$servers = $dbFarm->GetServersByFilter(array('status' => array(SERVER_STATUS::RUNNING)));
			try
			{
				$DBFarmRole = $event->DBServer->GetFarmRoleObject();
				$is_synchronize = ($DBFarmRole->NewRoleID) ? true : false;
			}
			catch(Exception $e)
			{
				$is_synchronize = false;
			}

			try
			{
				$DBRole = DBRole::loadById($event->DBServer->roleId);
			}
			catch(Exception $e){}

			$first_in_role_handled = false;
			$first_in_role_server = null;
			foreach ($servers as $DBServer)
			{
				if (!($DBServer instanceof DBServer))
					continue;
				
				$isfirstinrole = '0';
				if ($event->DBServer->GetProperty(SERVER_PROPERTIES::DB_MYSQL_MASTER) && !$first_in_role_handled)
				{
					if (!$is_synchronize || $DBServer->farmRoleId != $event->DBServer->farmRoleId)
					{
						if (DBRole::loadById($DBServer->roleId)->hasBehavior(ROLE_BEHAVIORS::MYSQL))
						{
							$first_in_role_handled = true;
							$first_in_role_server = $DBServer;
							$isfirstinrole = '1';
						}
					}	
				}
				
				$msg = new Scalr_Messaging_Msg_HostDown(
					($DBRole) ? $DBRole->getBehaviors() : '*Unknown*',
					($DBRole) ? $DBRole->name : '*Unknown*',
					$event->DBServer->localIp,
					$event->DBServer->remoteIp
				);
				$msg->isFirstInRole = $isfirstinrole;
				
				$DBServer->SendMessage($msg);
			}
				
			// If EC2 master down			
			if (($event->DBServer->GetProperty(SERVER_PROPERTIES::DB_MYSQL_MASTER) || $event->DBServer->GetProperty(SERVER_PROPERTIES::DB_MYSQL_SLAVE_TO_MASTER)) &&
				$event->DBServer->IsSupported("0.5") &&
				$DBFarmRole)
			{
				$msg = new Scalr_Messaging_Msg_Mysql_PromoteToMaster(
					$DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_ROOT_PASSWORD),
					$DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_REPL_PASSWORD),
					$DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_STAT_PASSWORD)
				);
				
				if ($event->DBServer->IsSupported("0.7"))
				{
					if ($event->DBServer->platform == SERVER_PLATFORMS::EC2) {
						try {
							$volume = Scalr_Model::init(Scalr_Model::STORAGE_VOLUME)->loadById(
								$DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_SCALR_VOLUME_ID)
							);
							
							$msg->volumeConfig = $volume->getConfig();
						} catch (Exception $e) {
							$this->Logger->error(new FarmLogMessage($event->DBServer->farmId, "Cannot create volumeConfig for PromoteToMaster message: {$e->getMessage()}"));
						}
					}
				}
				elseif ($event->DBServer->platform == SERVER_PLATFORMS::EC2)
					$msg->volumeId = $DBFarmRole->GetSetting(DBFarmRole::SETTING_MYSQL_MASTER_EBS_VOLUME_ID);
				
				// Send Mysql_PromoteToMaster to the first server in the same avail zone as old master (if exists)
				// Otherwise send to first in role
				$platform = $event->DBServer->platform; 
				if ($platform == SERVER_PLATFORMS::EC2) {
					$availZone = $event->DBServer->GetProperty(EC2_SERVER_PROPERTIES::AVAIL_ZONE);
				}	
				
				foreach ($servers as $DBServer) {
					
					if ($DBServer->serverId == $event->DBServer->serverId)
						continue;
					
					if (($platform == SERVER_PLATFORMS::EC2 && $DBServer->GetProperty(EC2_SERVER_PROPERTIES::AVAIL_ZONE) == $availZone) || $platform != SERVER_PLATFORMS::EC2) {
						if (DBRole::loadById($DBServer->roleId)->hasBehavior(ROLE_BEHAVIORS::MYSQL)) {
							$DBFarmRole->SetSetting(DBFarmRole::SETTING_MYSQL_SLAVE_TO_MASTER, 1);
							$DBServer->SetProperty(SERVER_PROPERTIES::DB_MYSQL_SLAVE_TO_MASTER, 1);
							$DBServer->SendMessage($msg);
							return;
						}
					}
				}
				
				if ($first_in_role_server) {
					$DBFarmRole->SetSetting(DBFarmRole::SETTING_MYSQL_SLAVE_TO_MASTER, 1);
					$first_in_role_server->SetProperty(SERVER_PROPERTIES::DB_MYSQL_SLAVE_TO_MASTER, 1);
					$first_in_role_server->SendMessage($msg);
				}
			}
		}
	}
?>
