<?php
	class Modules_Platforms_Ec2_Observers_Ec2 extends EventObserver
	{
		public $ObserverName = 'EC2';
		
		function __construct()
		{
			parent::__construct();
			
			$this->Crypto = Core::GetInstance("Crypto", CONFIG::$CRYPTOKEY);
		}

		/**
		 * Return new instance of AmazonEC2 object
		 *
		 * @return AmazonEC2
		 */
		private function GetAmazonEC2ClientObject(Scalr_Environment $environment, $region)
		{
	    	$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
		    	$region, 
		    	$environment->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY), 
		    	$environment->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
	    	);
			
			return $AmazonEC2Client;
		}
				
		public function OnHostUp(HostUpEvent $event)
		{
			if ($event->DBServer->platform != SERVER_PLATFORMS::EC2)
				return;
					
			try
			{
				// If we need replace old instance to new one terminate old one.
				if ($event->DBServer->replaceServerID)
				{
					Logger::getLogger(LOG_CATEGORY::FARM)->info(new FarmLogMessage($this->FarmID, "Host UP. Terminating old server: {$event->DBServer->replaceServerID})."));
					
					try {
						$oldDBServer = DBServer::LoadByID($event->DBServer->replaceServerID);
					}
					catch(Exception $e) {}

					Logger::getLogger(LOG_CATEGORY::FARM)->info(new FarmLogMessage($this->FarmID, "OLD Server found: {$oldDBServer->serverId})."));
					
					if ($oldDBServer)
						Scalr::FireEvent($oldDBServer->farmId, new BeforeHostTerminateEvent($oldDBServer));
				}
			}
			catch (Exception $e)
			{
				$this->Logger->fatal($e->getMessage());
			}			
		}
	}
?>