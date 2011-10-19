<?php
	
	class ScalrAPI_2_1_0 extends ScalrAPI_2_0_0
	{
		public function FarmsList()
		{
			$response = parent::FarmsList();
			
			foreach ($response->FarmSet->Item as &$item)
				unset($item->Region);
	
			return $response;
		}
		
		public function FarmGetDetails($FarmID)
		{
			$response = parent::FarmGetDetails($FarmID);
						
			foreach ($response->FarmRoleSet->Item as &$item)
				$item->{"CloudLocation"} = DBFarmRole::LoadByID($item->ID)->CloudLocation;
			
			return $response;
		}
		
		public function ApacheVhostCreate($DomainName, $FarmID, $FarmRoleID, $DocumentRootDir, $EnableSSL, $SSLPrivateKey = null, $SSLCertificate = null)
		{
			$validator = new Validator();		
			
			if(!$validator->IsDomain($DomainName))
				$err[] = _("Domain name is incorrect");
				
			$DBFarm = DBFarm::LoadByID($FarmID);
			if ($DBFarm->EnvID != $this->Environment->id)
				throw new Exception(sprintf("Farm #%s not found", $FarmID));

			$DBFarmRole = DBFarmRole::LoadByID($FarmRoleID);
			if ($DBFarm->ID != $DBFarmRole->FarmID)
				throw new Exception(sprintf("FarmRole #%s not found on Farm #%s", $FarmRoleID, $FarmID));

			if(!$DocumentRootDir)
				throw new Exception(_("DocumentRootDir required"));
			
			$options = serialize(array(
				"document_root" 	=> trim($DocumentRootDir),
				"logs_dir"			=> "/var/log",
				"server_admin"		=> $this->Client->Email	
			));
				
			$httpConfigTemplateSSL = @file_get_contents(dirname(__FILE__)."/../../templates/services/apache/ssl.vhost.tpl");
			$httpConfigTemplate = @file_get_contents(dirname(__FILE__)."/../../templates/services/apache/nonssl.vhost.tpl");
			
			$vhost = DBApacheVhost::create($DomainName,
				(int)$FarmID,
				(int)$FarmRoleID,
				(int)$this->Client->ID,
				false,
				$httpConfigTemplate,
				$httpConfigTemplateSSL,
				$options
			);
			
			$vhost->envId = (int)$this->Environment->id;
			
			if($EnableSSL)
				$vhost->enableSSL(base64_decode($SSLCertificate), base64_decode($SSLPrivateKey), "");
			else
				$vhost->disableSSL();


			$vhost->save();
			
			$response = $this->CreateInitialResponse();
			$response->Result = 1;
			
			return $response;
		}
		
		public function ApacheVhostsList()
		{
			$response = $this->CreateInitialResponse();
			$response->ApacheVhostSet = new stdClass();
			$response->ApacheVhostSet->Item = array();
			
			$rows = $this->DB->Execute("SELECT * FROM apache_vhosts WHERE client_id=?", array($this->Client->ID));
			while ($row = $rows->FetchRow())
			{
				$itm = new stdClass();
				$itm->{"Name"} = $row['name'];
				$itm->{"FarmID"} = $row['farm_id'];
				$itm->{"FarmRoleID"} = $row['farm_roleid'];
				$itm->{"IsSSLEnabled"} = $row['is_ssl_enabled'];
				$itm->{"LastModifiedAt"} = $row['last_modified'];
				
				$response->ApacheVhostSet->Item[] = $itm; 	
		    }
		    
		    return $response;
		}
	}
?>