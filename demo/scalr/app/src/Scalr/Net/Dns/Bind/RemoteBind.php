<?
	class Scalr_Net_Dns_Bind_RemoteBind
	{
		/**
		 * 
		 * @var Scalr_Net_Dns_Bind_Transport
		 */
		private $transport;
		private $logger;
		
		private $namedConf = false;
		private $zonesConfig = array();
		
		function __construct()
		{
			$this->logger = Logger::getLogger(__CLASS__);
		}
		
		function setTransport(Scalr_Net_Dns_Bind_Transport $transport)
		{
			$this->transport = $transport;
			
			$this->listZones();
		}
		
		function listZones()
		{
			if (count($this->zonesConfig) == 0)
			{
				$contents = $this->transport->getNamedConf();
				preg_match_all("/\/\/(.*?)-BEGIN(.*?)\/\/\\1-END/sm", $contents, $matches);
				foreach ($matches[1] as $index=>$domain_name)
					$this->zonesConfig[$domain_name] = $matches[0][$index];
			}
			
//			if (count($this->zonesConfig) == 0)
//				throw new Exception("Zones config is empty");
			
			return array_keys($this->zonesConfig); 
		}
		
		function addZoneToNamedConf(DBDNSZone $DBDDNSZone)
		{
			$this->zonesConfig[$DBDDNSZone->zoneName] = $DBDDNSZone->getContents(true);
		}
		
		function removeZoneFromNamedConf(DBDNSZone $DBDDNSZone)
		{
			unset($this->zonesConfig[$DBDDNSZone->zoneName]);
		}
		
		function addZoneDbFile(DBDNSZone $DBDDNSZone)
		{
			$this->transport->uploadZoneDbFile($DBDDNSZone->zoneName, $DBDDNSZone->getContents());
		}
		
		function removeZoneDbFile(DBDNSZone $DBDDNSZone)
		{
			$this->transport->removeZoneDbFile($DBDDNSZone->zoneName);
		}
		
		function saveNamedConf()
		{
			$this->transport->setNamedConf(implode("\n", $this->zonesConfig));
			$this->reloadBind();
		}
		
		function reloadBind()
		{
			$this->transport->rndcReload();
		}
	}
?>
